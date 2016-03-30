<?php
define('DS', DIRECTORY_SEPARATOR);
define('EXT', '.php');
define('ALGORITHM_LIB', realpath(dirname( __FILE__ )).DS.'algorithms');
define('MAX_IO_RETRY', 50);
define('DEFAULT_ALGORITHM', 'XXTEA-128');
define('PLAINTEXT_CHUNK_SIZE', 4096);
define('HEADER_IDENTIFIER_LEN', 9);
define('HEADER_VERSION_LEN', 10);
define('HEADER_OA_VERSION_LEN', 10);
define('HEADER_OA_VERSION_LEN_NEW', 20);
define('HEADER_ALGORITHM_NAME_LEN', 20);
define('HEADER_ALGORITHM_VER_LEN', 10);
define('HEADER_IDENTIFIER','TDENCRYPT');
define('HEADER_HEAD_LEN', HEADER_IDENTIFIER_LEN + HEADER_VERSION_LEN + 4 + 4 + 4 + 4);
define('HEADER_HEAD','a'.HEADER_IDENTIFIER_LEN.'header_identifier/a'.HEADER_VERSION_LEN.'header_version/Lheader_size/Loriginal_size/Ldata_size/Lext_size');
define('HEADER_EXT','a*ext_info');

global $head_version_array;
$head_version_array = array(
    "1.0.120424" => "a".HEADER_ALGORITHM_NAME_LEN."algorithm_name/a".HEADER_ALGORITHM_VER_LEN."algorithm_ver/a".HEADER_OA_VERSION_LEN."oa_version/C1whole/Lchunk_size/a*key",
    "1.1.130509" => "a".HEADER_ALGORITHM_NAME_LEN."algorithm_name/a".HEADER_ALGORITHM_VER_LEN."algorithm_ver/a".HEADER_OA_VERSION_LEN_NEW."oa_version/C1whole/Lchunk_size/a*key"
);

/**
 * TEncryptor - An encrpyt and decrypt string class
 * @author lx
 * @copyright Copyright (c) 2012 Tongda Tech, Inc.
 * @version v1.0
 */

class TEncryptor
{
    /**
     * Support algorithms.
     * @var array
     */
    private $_available_algorithms = array();

    /**
     * To be used algorithm.
     * @var string
     */
    private $_cipher = '';

    /**
     * An encrypt object
     * @var mixed
     */
    public $encryptor = null;

    /**
     * key
     * @var string
     */
    private $_key = "";

    /**
     * Encrypted key to store in file.
     * @var string
     */
    private $_cipher_key = "";

    /**
     * Error code.
     * @var int
     */
    private $_err_no = 0;

    /**
     * Length of to be encrypted content.
     * @var int
     */
    private $_size = 0;

    /**
     * Version of the header in use.
     * @var string
     */
    private $_header_ver = "1.1.130509";

    /**
     * Available algorithms in mcrypt lib.
     * @var array
     */
    private $_mcrypt_algorithms = array('DES-64', 'RC2-128', 'RC2-256', 'BLOWFISH-128', 'BLOWFISH-256');

    /**
     * RSA public key to protect the key for encrypting file.
     * @var string
     */
    private $_attach_encrypt_pkey = "";

    /**
     * The source file path(to be encrypted or dncrypted).
     * @var string
     */
    private $_file_src = "";

    /**
     * The handle of source file.
     * @var mixed
     */
    private $_handle_src = null;

    /**
     * Constructor
     * @param string $cipher To be used algorithm.
     */
    public function __construct($file_src)
    {
        if(trim($file_src) == "")
        {
            $this->_setError(-100);
            return false;
        }

        if(!file_exists($file_src) || !is_file($file_src))
        {
            $this->_setError(-101);
            return false;
        }


        $this->_file_src = $file_src;
        //获取加密公钥
        $params = SysParams::model()->getParams('attach_encrypt_pkey');//("ATTACH_ENCRYPT_PKEY");
        $attachEncryptPkey = unserialize($params["attach_encrypt_pkey"]);
        $this->_attach_encrypt_pkey = $attachEncryptPkey["pkey_1"];

    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->closeFile();
    }


    /**
     * Close the source file.
     * @return void
     */
    public function closeFile()
    {
        if(is_resource($this->_handle_src))
        {
            fclose($this->_handle_src);
        }
    }

    /**
     * Get the list of available algorithms.
     * @return array
     */
    public static function getAlgorithms()
    {
        $ini_file = ALGORITHM_LIB.DS."algorithms.ini";
        if(!file_exists($ini_file))
            return false;
        $available_algorithms = parse_ini_file($ini_file,true);
        return $available_algorithms;
    }

    /**
     * Generate key
     * @param string $cipher To be used algorithm.
     * @return string
     */
    public static function genKey( $length = 128 ) {

        $source = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        $key = '';
        for ( $i = 0; $i < $length; $i++ )
        {
            $key .= $source[ mt_rand(0, strlen($source) - 1) ];
        }
        return $key;
    }


    /**
     * Generate the encrypted key in header.
     * @param string $string.
     * @param string $operation.
     * @param int $expiry.
     * @return string
     */
    private function _authcode($string, $operation = '', $expiry = 0)
    {
        if($this->_attach_encrypt_pkey == "")
            return $string;

        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;

        // 密匙
        $key = md5($this->_attach_encrypt_pkey);

        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // substr($result, 0, 10) == 0 验证数据有效性
            // substr($result, 0, 10) - time() > 0 验证数据有效性
            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }

    /**
     * Get the encrypted chunk size from plaintext.
     * @return int
     */
    private function _getChunkSize()
    {
        $i = 0;
        $data = null;
        while( $i++ * 16 < PLAINTEXT_CHUNK_SIZE )
        {
            $data .= md5( mt_rand(), true );
        }
        $encrypted_data = $this->encryptor->encrypt($data);
        return strlen($encrypted_data);
    }


    /**
     * Initialization the cipher object.
     * @return void
     */
    private function _initCipher()
    {
        $this->_available_algorithms = self::getAlgorithms();
        if(!array_key_exists($this->_cipher, $this->_available_algorithms))
        {
            $this->_setError(-102);
            $this->_cipher = DEFAULT_ALGORITHM;
        }

        if(in_array($this->_cipher, $this->_mcrypt_algorithms))
        {
            $class = "AlgorithmStandard";
        }
        else if(substr(strtolower($this->_cipher), 0, 5) == "xxtea")
        {
            $class = "AlgorithmXXTEA";
        }
        $class_file = ALGORITHM_LIB . DS . $class . EXT;
        if(file_exists($class_file))
            require_once ($class_file);
        $this->encryptor = @ new $class($this->_cipher);
    }

    /**
     * Encrypt the file.
     * @param string $file_des
     * @param string $cipher
     * @param bool $whole
     * @param bool $overwrite
     * @return bool
     */
    public function encryptFile($file_des, $cipher = DEFAULT_ALGORITHM, $whole = false, $overwrite = true)
    {
        global $MYOA_ATTACH_ENCRYPT_DEFAULT_LEN;

        //检测源文件是否已加密
        $header_src = $this->getHeaderHead();
        if($header_head["header_identifier"] == HEADER_IDENTIFIER)
            return;

        $this->_cipher = strtoupper($cipher);
        if(!$whole)
        {
            $this->_size = $MYOA_ATTACH_ENCRYPT_DEFAULT_LEN * 1024;
        }

        $this->_initCipher();

        if(!is_object($this->encryptor))
        {
            $this->_setError(-103);
            return false;
        }

        $this->_key = self::genKey();
        $this->encryptor->setKey($this->_key);
        $this->_cipher_key = $this->_authcode($this->_key);

        $file_size = sprintf("%u",filesize($this->_file_src));
        $size = ($this->_size == 0 || $this->_size > $file_size) ? $file_size : $this->_size;

        if(!is_resource($this->_handle_src))
        {
            $this->_handle_src = fopen($this->_file_src, "rb");
        }
        $handle_des = fopen($file_des, "wb");

        $header = $this->_packHeader();
        $pos_des_begin = strlen($header);

        rewind($this->_handle_src);
        fseek($handle_des, $pos_des_begin);
        for($pos_src=0, $pos_des = $pos_des_begin; $pos_src < $size; $pos_src += $chunk)
        {
            $chunk = ($size - $pos_src > PLAINTEXT_CHUNK_SIZE) ? PLAINTEXT_CHUNK_SIZE : $size - $pos_src;
            $data = $this->_fread($this->_handle_src, $chunk);
            if(strlen($data) != $chunk)
            {
                $this->_setError(-200);
                $this->_writeLog("encrypt");
                return false;
            }
            $encrypt_data = $this->encryptor->encrypt($data);
            //echo strlen($data)." ".strlen($encrypt_data)."<br>";
            if(!$this->_fwrite($handle_des, $encrypt_data, strlen($encrypt_data)))
            {
                fclose($handle_des);
                @ unlink($file_des);
                $this->_setError(-201);
                $this->_writeLog("encrypt");
                return false;
            }
            $pos_des += strlen($encrypt_data);
        }

        //部分加密方式
        if($file_size > $size)
        {
            while (!feof($this->_handle_src))
            {
                $surplus_data = $this->_fread($this->_handle_src, PLAINTEXT_CHUNK_SIZE);
                if(!$this->_fwrite($handle_des, $surplus_data, strlen($surplus_data)))
                {
                    fclose($handle_des);
                    @ unlink($file_des);
                    $this->_setError(-201);
                    $this->_writeLog("encrypt");
                    return false;
                }
            }
        }

        //写入头信息
        rewind($handle_des);
        $this->_fwrite($handle_des, $header, strlen($header));
        //写入加密数据大小
        fseek($handle_des, HEADER_HEAD_LEN - 4 -4);
        $this->_fwrite($handle_des, pack("L", sprintf("%u", $pos_des-$pos_des_begin)), 4);
        fclose($handle_des);

        if($this->_err_no != 0)
        {
            $this->_writeLog("encrypt");
        }

        return true;
    }

    /**
     * Decrypt the file.
     * @param string $file_des if it's empty, directly echo the content.
     * @return bool
     */
    public function decryptFile($file_des="")
    {
        if(!is_resource($this->_handle_src))
        {
            $this->_handle_src = fopen($this->_file_src, "rb");
        }

        $header = $this->_unpackHeader();
        //var_dump($header);exit;

        $this->_cipher = strtoupper($header["algorithm_name"]);
        $this->_initCipher();
        if(!is_object($this->encryptor))
        {
            $this->_setError(-103);
            return false;
        }
        $header_size = HEADER_HEAD_LEN + $header["header_size"];
        $file_size = sprintf("%u", filesize($this->_file_src));
        $size = $header["whole"] == 1 ? $file_size : ($header["data_size"] + $header_size);
        if($header)
        {
            $key = $this->_authcode($header["key"], "DECODE");
            $this->encryptor->setKey($key);

            if($file_des!="")
            {
                $handle_des = fopen($file_des, "wb");
            }

            $chunk = $header["chunk_size"];
            //fseek($this->_handle_src, $header_size);
            for($pos_src = $header_size, $pos_des=0; $pos_src < $size; $pos_src += $chunk)
            {
                $chunk = ($size - $pos_src > $chunk) ? $chunk : $size - $pos_src;
                $data = $this->_fread($this->_handle_src, $chunk);
                if(strlen($data) != $chunk)
                {
                    $this->_setError(-200);
                    $this->_writeLog("dncrypt");
                    return false;
                }
                $decrypt_data = $this->encryptor->decrypt($data);
                if(is_resource($handle_des))
                {
                    if(!$this->_fwrite($handle_des, $decrypt_data, strlen($decrypt_data)))
                    {
                        fclose($handle_des);
                        @ unlink($file_des);
                        $this->_setError(-201);
                        $this->_writeLog("dncrypt");
                        return false;
                    }
                }
                else
                {
                    echo $decrypt_data;
                }
                $pos_des += strlen($decrypt_data);
            }
            //部分加密方式
            if($file_size > $size)
            {
                while (!feof($this->_handle_src))
                {
                    $surplus_data = fread($this->_handle_src, PLAINTEXT_CHUNK_SIZE);
                    if(is_resource($handle_des))
                    {
                        if(!$this->_fwrite($handle_des, $surplus_data, strlen($surplus_data)))
                        {
                            fclose($handle_des);
                            @ unlink($file_des);
                            $this->_setError(-201);
                            $this->_writeLog("dncrypt");
                            return false;
                        }
                    }
                    else
                    {
                        echo $surplus_data;
                    }
                }
            }
            if(is_resource($handle_des))
            {
                fclose($handle_des);
            }
        }

        if($this->_err_no != 0)
        {
            $this->_writeLog("decrypt");
        }

        return true;
    }

    /**
     * Generate header of the encrypted file.
     * @return string
     */
    private function _packHeader()
    {
        global $VERSION_INFO;

        //算法
        $header .= pack("a".HEADER_ALGORITHM_NAME_LEN, $this->_cipher);
        //算法版本
        $version = $this->encryptor->getVersion();
        $header .= pack("a".HEADER_ALGORITHM_VER_LEN, $version);
        //OA版本
        $header .= pack("a".HEADER_OA_VERSION_LEN_NEW, $VERSION_INFO);
        //加密方式（部分加密、完整加密）
        $whole = 0;
        if($this->_size == 0)
            $whole = 1;
        $header .= pack("C1", $whole);
        //密文块长度
        $chunk_size = $this->_getChunkSize();
        $header .= pack("L", $chunk_size);
        //密钥
        $header .= pack("a".strlen($this->_cipher_key), $this->_cipher_key);

        //文件扩展信息
        $ext_info = array();
        if(is_image($this->_file_src))
        {
            $ext_info["image_size"] = @ getimagesize($this->_file_src);
        }
        $ext_str = serialize($ext_info);
        $header = pack("a".strlen($ext_str), $ext_str) . $header;

        //头主体长度
        $header_body_size = strlen($header);
        //文件扩展信息长度
        $header = pack("L", strlen($ext_str)) . $header;
        //加密数据大小（占位）
        $header = pack("L", 0).$header;
        //原始文件大小
        $header = pack("L", sprintf("%u", filesize($this->_file_src))).$header;
        //头长度
        $header = pack("L", $header_body_size).$header;
        //头版本
        $header = pack("a".HEADER_VERSION_LEN, $this->_header_ver).$header;
        //标识符
        $header = pack("a".HEADER_IDENTIFIER_LEN, HEADER_IDENTIFIER).$header;

        return $header;

    }

    /**
     * Get the front part of header in the encrypted file.
     * @return array
     */
    public function getHeaderHead()
    {
        $header_head = array();
        if(!file_exists($this->_file_src))
            return $header_head;

        //判断源文件长度
        $file_size = filesize($this->_file_src);
        if($file_size <= HEADER_HEAD_LEN)
            return $header_head;

        if(!is_resource($this->_handle_src))
        {
            $this->_handle_src = fopen($this->_file_src, "rb");
        }

        $data = $this->_fread($this->_handle_src, HEADER_HEAD_LEN);
        $header_head = @ unpack(HEADER_HEAD,$data);

        if($header_head["header_identifier"] == HEADER_IDENTIFIER)
        {
            if($header_head["ext_size"] > 0 && $header_head["ext_size"]< PLAINTEXT_CHUNK_SIZE)
            {
                $data = $this->_fread($this->_handle_src, $header_head["ext_size"]);
                $ext_info = @ unpack(HEADER_EXT, $data);
                if($ext_info["ext_info"]!="")
                {
                    $header_head["ext_info"] = unserialize($ext_info["ext_info"]);
                }
            }
            return $header_head;
        }
        return array();
    }

    /**
     * Unpack and get the whole header in the encrypted file.
     * @return array
     */
    private function _unpackHeader()
    {
        global $head_version_array;
        $header = array();
        rewind($this->_handle_src);
        $header_head = $this->getHeaderHead();

        //检测文件头
        if($header_head["header_identifier"] == HEADER_IDENTIFIER)
        {
            fseek($this->_handle_src, HEADER_HEAD_LEN + $header_head["ext_size"]);
            $data = fread($this->_handle_src, $header_head["header_size"]-$header_head["ext_size"]);
            $header_version = $header_head["header_version"];

            if(array_key_exists($header_version, $head_version_array))
            {
                $header_body = unpack($head_version_array[$header_version], $data);
                $header = array_merge($header_head, $header_body);
            }
        }
        return  $header;
    }

    /**
     * Set the error code.
     * @param err_no
     */
    private function _setError($err_no)
    {
        $this->_err_no = $err_no;
    }

    /**
     * Get the error message by error code.
     * @return string
     */
    public function getErrorMsg()
    {
        if($this->_err_no >= 0)
            return;

        $rtMsg = "";
        switch($this->_err_no)
        {
            case -100:
                $rtMsg = _("初始化失败，源文件地址不能为空。");
                break;
            case -101:
                $rtMsg = _("初始化失败，源文件不存在。");
                break;
            case -102:
                $rtMsg = _("未知的加密算法。");
                break;
            case -103:
                $rtMsg = _("初始化算法失败。");
                break;
            case -200:
                $rtMsg = _("读取文件操作失败。");
                break;
            case -201:
                $rtMsg = _("写入文件操作失败。");
                break;
        }

        return $rtMsg;
    }

    private function _writeLog($methord)
    {
        global $ROOT_PATH;
        $log_file = "tdcrypt.err";
        $php_log_file = ini_get("error_log");
        if($php_log_file)
        {
            $log_path = pathinfo($php_log_file, PATHINFO_DIRNAME);
        }
        else
        {
            $log_path = realpath($ROOT_PATH."../")."/logs";
        }

        if(!is_dir($log_path))
        {
            mkdir($log_path, 0700);
        }
        $log_file = $log_path."/".$log_file;
        $handle = fopen($log_file, "ab");
        $log_msg = '['.date("Y-m-d H:i:s").'] ['.$methord.'] '.$this->getErrorMsg().' '.$this->_file_src."\r\n";
        fwrite($handle, $log_msg);
        fclose($handle);
    }

    private function _fread($handle, $len)
    {
        $ret = "";
        $ret_len = 0;
        $retry = 0;
        while($ret_len < $len && !feof($handle) && $retry < MAX_IO_RETRY)
        {
            $data = fread($handle, $len - $ret_len);
            $ret .= $data;
            $ret_len += strlen($data);
            $retry++;
        }

        return $ret;
    }

    private function _fwrite($handle, $data, $len)
    {
        $ret_len = 0;
        $retry = 0;
        while($ret_len < $len && $retry < MAX_IO_RETRY)
        {
            $data = substr($data, $ret_len, $len-$ret_len);
            $data_len = fwrite($handle, $data, $len - $ret_len);
            $ret_len += $data_len;
            $retry++;
        }

        return ($ret_len === $len);
    }
}
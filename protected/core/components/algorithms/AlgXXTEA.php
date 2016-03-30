<?php

Yii::import('core.components.algorithms.IAlgorithm');
/**
 * xxtea加解密算法封装类
 * @author lx
 * @copyright Copyright (c) 2012 Tongda Tech, Inc.
 * @version v1.0
 */
class AlgXXTEA implements IAlgorithm
{

    /**
     * Version
     *
     * @var string
     * @access private
     */
    private $_ver = "1.0.120424";
    
    /**
     * Key
     *
     * @var string
     * @access private
     */
    private $_key = "";

    /**
     * key size of the cipher
     *
     * @var int
     * @access private
     */    
    private $_key_size = null;
        
    /**
     * Constructor
     *
     * @param string $key
     * @access public
     */
    public function __construct($cipher)
    {
        
    }

    /**
     * Set the key.
     * @param string $key
     */      
    function setKey($key)
    {
        $this->_key = $key;
    }
    
    
    /**
     * Encrypt the data.
     * @param string $data
     * @return string
     */
    public function encrypt($data)
    {
        if ($data == "") {
            return "";
        }
        
        if (extension_loaded('tdcrypt')) {
            $encrypted_data = td_encrypt($data, $this->_key);
            if($encrypted_data)
            {
                return $encrypted_data;
            }
            else
            {
                return $data;
            }
        }
        
        $v = $this->_str2long($data, true);
        $k = $this->_str2long($this->_key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;
     
        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        
        $sum = 0;
        while (0 < $q--) {
            $sum = $this->_int32($sum + $delta);
            $e = $sum >> 2 & 3;
            for ($p = 0; $p < $n; $p++) {
                $y = $v[$p + 1];
                $mx = $this->_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $z = $v[$p] = $this->_int32($v[$p] + $mx);
            }
            $y = $v[0];
            $mx = $this->_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$n] = $this->_int32($v[$n] + $mx);
        }
        return $this->_long2str($v, false);
    }
    
    
    /**
     * Decrypt the data.
     * @param string $data
     * @return string
     */
    public function decrypt($data)
    {
        if ($data == "") {
            return "";
        }

        if (extension_loaded('tdcrypt')) {
            $decrypted_data =  td_decrypt($data, $this->_key);
            if($decrypted_data)
            {
                return $decrypted_data;
            }
            else
            {
                return $data;
            }
        }
        
        $v = $this->_str2long($data, false);
        $k = $this->_str2long($this->_key, false);
        if (count($k) < 4) {
            for ($i = count($k); $i < 4; $i++) {
                $k[$i] = 0;
            }
        }
        $n = count($v) - 1;
     
        $z = $v[$n];
        $y = $v[0];
        $delta = 0x9E3779B9;
        $q = floor(6 + 52 / ($n + 1));
        $sum = $this->_int32($q * $delta);
        while ($sum != 0) {
            $e = $sum >> 2 & 3;
            for ($p = $n; $p > 0; $p--) {
                $z = $v[$p - 1];
                $mx = $this->_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
                $y = $v[$p] = $this->_int32($v[$p] - $mx);
            }
            $z = $v[$n];
            $mx = $this->_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ $this->_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[0] = $this->_int32($v[0] - $mx);
            $sum = $this->_int32($sum - $delta);
        }
        return $this->_long2str($v, true);
    }

    /**
     * Get the version of algorithm.
     * @return string
     */
    public function getVersion()
    {
        return $this->_ver;
    }
    

    private function _long2str($v, $w) {
        $len = count($v);
        $n = ($len - 1) << 2;
        if ($w) {
            $m = $v[$len - 1];
            if (($m < $n - 3) || ($m > $n)) return false;
            $n = $m;
        }
        $s = array();
        for ($i = 0; $i < $len; $i++) {
            $s[$i] = pack("V", $v[$i]);
        }
        if ($w) {
            return substr(join('', $s), 0, $n);
        }
        else {
            return join('', $s);
        }
    }
     
    private function _str2long($s, $w) {
        $v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
        $v = array_values($v);
        if ($w) {
            $v[count($v)] = strlen($s);
        }
        return $v;
    }
     
    private function _int32($n) {
        while ($n >= 2147483648) $n -= 4294967296;
        while ($n <= -2147483649) $n += 4294967296;
        return (int)$n;
    }
}
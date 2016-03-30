<?php
/**
 * TSocket class file.
 *
 * @author lx <lx@tongda2000.com>
 */

/**
 * socket通讯类
 * 
 * @property array $config 默认配置
 * @property resource $socket 套接字
 * @property int $errorNO 错误号
 * @property int $errorMsg 错误信息
 */
class TSocket{
    
	protected $config = array(
		'host'			=> null,
		'protocol'		=> 'udp',
		'port'			=> null,
		'timeout'		=> 5
	);
    protected $socket = null;
	protected $errorNO = null;
    protected $errorMsg = '';
    
    public function __construct($host, $port, $protocol = null, $timeout = null) {
        if(!extension_loaded("sockets")){
            throw new CException("请打开socket扩展 ");
        }
        socket_clear_error();
        if(trim($host) == '' || !is_numeric($port)) {
            throw new CException("参数错误 ");
        }
        $this->config['host'] = $host;
        $this->config['port'] = $port;
        if(isset($protocol)) {
            $this->config['protocol'] = strtolower($protocol);
        }
        if(isset($timeout)) {
            $this->config['timeout'] = intval($timeout);
        }
        $this->create();      
    }

    /**
     * 
     * @return boolean
     */
    protected function create(){
        $protocol = getprotobyname($this->config['protocol']);
        if($protocol == false) {
            throw new CException('无效的通讯协议');
        }
        switch($this->config['protocol']) {
            case 'tcp':
                $type = SOCK_STREAM;
                break;
            case 'udp':
                $type = SOCK_DGRAM;
                break;
            case 'icmp':
                $type = SOCK_RAW;
                break;
            default:
                throw new CException('不支持的通讯类型');
        }
        !$this->socket && $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    /**
     * 向套接字写数据并等待返回
     * @param type $data
     * @param type $readLength
     * @param type $readType
     * @return boolean | string
     */
    public function write($data, $readLength = 2048, $readType = PHP_BINARY_READ){
        !$this->socket&&$this->create();
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array("sec" => $this->config['timeout'], "usec" => 0));
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec" => $this->config['timeout'], "usec" => 0));
        if(false == @socket_connect($this->socket, $this->config['host'], $this->config['port'])) {
            return false;
        }
        $data = $this->filter($data);
        $result = @socket_write($this->socket, $data, strlen($data));
        if(!intval($result)){
            return false;
        }
        $response = '';
        while($read = @socket_read($this->socket, $readLength, $readType)) {
           $response .= $read;
           if(strlen($read) < $readLength) {
              break;
           }
        }
        if($read == false) {
            return false;
        } else {
            return $response;
        }
    }
    
    /**
     * 向套接字发送消息
     * @param type $data
     * @return boolean
     */
    public function sendTo($data, $flags = 0) {
        $data = $this->filter($data);
        return @socket_sendto($this->socket, $data, strlen($data), $flags, $this->config['host'], $this->config['port']);     
    }

    /**
     * 处理通讯数据
     * @param string $contents
     * @return string
     */
    protected function filter($data){
        return $data;
    }

    /**
     * 获取错误号
     * @return int
     */ 
    public function getErrorNO(){
        return socket_last_error();
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getErrorMsg(){
        $errorNO = socket_last_error();
        if($errorNO >= 10050 && $errorNO <= 10065)
           return '服务未启动或设置不正确';
        else {
           return TUtil::iconv(socket_strerror($errorNO), isset(Yii::app()->params['server_charset']) ? Yii::app()->params['server_charset'] : 'gbk', Yii::app()->charset);
        }
    }
    
    /**
     * 返回配置信息
     * @return array
     */
    public function getConfig(){
        return $this->config;
    }

    /**
     * 关闭链接
     */
    protected function close(){
        $this->socket&&socket_close($this->socket);
        unset($this->socket);
    }

    public function __destruct(){
        $this->close();
    }
}


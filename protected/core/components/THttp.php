<?php
/**
 * HTTP Request
 *
 * @author F.L
 */
class THttp extends CApplicationComponent{

    private $_ip = '';
    private $_host = '';
    private $_url = '';
    private $_port = '';
    private $_errno = '';
    private $_errstr = '';
    private $_timeout = 15;
    private $_fp = null;
    private $_formdata = array();
    private $_filedata = array();
    
    public $options = array();

    public function init(){
        parent::init();
        
        if(!$this->options['host']){
            $this->options['host'] = $_SERVER['HTTP_HOST'];
        }
        
        if(!empty($this->options)){
            $this->setConfig($this->options);
        }
    }
    
    // 设置连接参数
    private function setConfig($config) {
        $this->_ip = isset($config['ip']) ? $config['ip'] : '';
        $this->_host = isset($config['host']) ? $config['host'] : '';
        $this->_url = isset($config['url']) ? $config['url'] : '';
        $this->_port = isset($config['port']) ? $config['port'] : '';
        $this->_errno = isset($config['errno']) ? $config['errno'] : '';
        $this->_errstr = isset($config['errstr']) ? $config['errstr'] : '';
        $this->_timeout = isset($confg['timeout']) ? $confg['timeout'] : 15;

        // 如没有设置ip，则用host代替
        if ($this->_ip == '') {
            $this->_ip = $this->_host;
        }
    }

    // 设置表单数据
    private function setFormData($formdata = array()) {
        $this->_formdata = $formdata;
    }

    // 设置文件数据
    private function setFileData($filedata = array()) {
        $this->_filedata = $filedata;
    }

    // 发送数据
    private function send($type = 'get') {

        $type = strtolower($type);

        // 检查发送类型
        if (!in_array($type, array('get', 'post', 'multipart'))) {
            return false;
        }

        // 检查连接
        if ($this->connect()) {

            switch ($type) {
                case 'get':
                    $out = $this->sendGet();
                    break;

                case 'post':
                    $out = $this->sendPost();
                    break;

                case 'multipart':
                    $out = $this->sendMultipart();
                    break;
            }

            // 空数据
            if (!$out) {
                return false;
            }

            // 发送数据
            fputs($this->_fp, $out);

            // 读取返回数据
            $response = '';

            while ($row = fread($this->_fp, 4096)) {
                $response .= $row;
            }

            // 断开连接
            $this->disconnect();

            $pos = strpos($response, "\r\n\r\n");
            $response = substr($response, $pos + 4);

            
            
            return $response;
        } else {
            return false;
        }
    }

    // 创建连接
    private function connect() {
        $this->_fp = fsockopen($this->_ip, $this->_port, $this->_errno, $this->_errstr, $this->_timeout);
        if (!$this->_fp) {
            return false;
        }
        return true;
    }

    // 断开连接
    private function disconnect() {
        if ($this->_fp != null) {
            fclose($this->_fp);
            $this->_fp = null;
        }
    }

    // get 方式,处理发送的数据,不会处理文件数据
    private function sendGet() {

        // 处理url
        if ($this->_formdata) {
            $url = $this->_url . '?' . http_build_query($this->_formdata);
        } else {
            $url = $this->_url;
        }

        $out = "GET " . $url . " HTTP/1.1\r\n";
        $out .= "Host: " . $this->_host . "\r\n";
        $out .= "Connection: close\r\n\r\n";

        return $out;
    }

    // post 方式,处理发送的数据
    private function sendPost() {

        // 检查是否空数据
        if (!$this->_formdata && !$this->_filedata) {
            return false;
        }

        // form data
        $data = $this->_formdata ? $this->_formdata : array();

        // file data
        if ($this->_filedata) {
            foreach ($this->_filedata as $filedata) {
                if (file_exists($filedata['path'])) {
                    $data[$filedata['name']] = file_get_contents($filedata['path']);
                }
            }
        }

        if (!$data) {
            return false;
        }

        $data = http_build_query($data);

        $out = "POST " . $this->_url . " HTTP/1.1\r\n";
        $out .= "Host: " . $this->_host . "\r\n";
        $out .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out .= "Content-Length: " . strlen($data) . "\r\n";
        $out .= "Connection: close\r\n\r\n";
        $out .= $data;

        return $out;
    }

    // multipart 方式,处理发送的数据,发送文件推荐使用此方式
    private function sendMultipart() {

        // 检查是否空数据
        if (!$this->_formdata && !$this->_filedata) {
            return false;
        }

        // 设置分割标识
        srand((double) microtime() * 1000000);
        $boundary = '---------------------------' . substr(md5(rand(0, 32000)), 0, 10);

        $data = '--' . $boundary . "\r\n";

        // form data
        $formdata = '';

        foreach ($this->_formdata as $key => $val) {
            $formdata .= "Content-Disposition: form-data; name=\"" . $key . "\"\r\n";
            $formdata .= "Content-Type: text/plain\r\n\r\n";
            if (is_array($val)) {
                $formdata .= json_encode($val) . "\r\n"; // 数组使用json encode后方便处理
            } else {
                $formdata .= rawurlencode($val) . "\r\n";
            }
            $formdata .= '--' . $boundary . "\r\n";
        }

        // file data
        $filedata = '';

        foreach ($this->_filedata as $val) {
            if (file_exists($val['path'])) {
                $filedata .= "Content-Disposition: form-data; name=\"" . $val['name'] . "\"; filename=\"" . $val['filename'] . "\"\r\n";
                $filedata .= "Content-Type: " . mime_content_type($val['path']) . "\r\n\r\n";
                $filedata .= implode('', file($val['path'])) . "\r\n";
                $filedata .= '--' . $boundary . "\r\n";
            }
        }

        if (!$formdata && !$filedata) {
            return false;
        }

        $data .= $formdata . $filedata . "--\r\n\r\n";

        $out = "POST " . $this->_url . " HTTP/1.1\r\n";
        $out .= "Host: " . $this->_host . "\r\n";
        $out .= "Content-Type: multipart/form-data; boundary=" . $boundary . "\r\n";
        $out .= "Content-Length: " . strlen($data) . "\r\n";
        $out .= "Connection: close\r\n\r\n";
        $out .= $data;

        return $out;
    }
    
    public function post($url, $formData = array(), $fileData = array()){
        $type = 'post';
        if(!$url){
            return false;
        }
        $urlinfo = parse_url($url);
        $config = array_merge($this->options, array(
            'ip' => $urlinfo['host'],
            'port' => $urlinfo['port'] ? $urlinfo['port'] : '80',
            'url' => $url,
        ));
        $this->setConfig($config);
        if(!empty($formData)){
            $this->setFormData($formData);
        }
        if(!empty($fileData)){
            $this->setFileData($fileData);
            $type = 'multipart';
        }
        return $this->send($type);
    }
    
    public function get($url, $formData = array()){
        $type = 'get';
        if(!$url){
            return false;
        }
        $urlinfo = parse_url($url);
        $config = array_merge($this->options, array(
            'ip' => $urlinfo['host'],
            'port' => $urlinfo['port'] ? $urlinfo['port'] : '80',
            'url' => $url,
        ));
        $this->setConfig($config);
        if(!empty($formData)){
            $this->setFormData($formData);
        }
        return $this->send($type);
    }

}

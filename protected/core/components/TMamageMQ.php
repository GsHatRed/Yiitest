<?php

/**
 * Description of TMamageMQ
 *
 * @author jcl <jcl@tongda2000.com>
 */
class TMamageMQ {
    //put your code here
    private $curl;

    public function __construct() {
        $this->curl = new TCurl();
    }

    /**
     * @param string $url
     * @param string $username
     * @param string $password
     * @param array $headers
     * @throws CHttpException
     * @return boolean
     */
    public function getManageData($url, $username, $password, $headers = array()) {
        if (!$url || !$username) {
            return false;
        }
        $this->curl->setOption(CURLOPT_USERPWD, $username . ':' . $password);
        if ($headers) {
            $this->curl->setHeaders($headers);
        }
        $get = $this->curl->get($url);
        $data = json_decode($get, true);
        if (array_key_exists("error", $data) && $data['error'] == 'not_authorised') {
            throw new CHttpException(404, '用户未被授权！');
            //exit('用户未被授权！');
        }
        return $data;
    }

    //curl put方法提交数据
    public function setPutData($url, $username, $password, $data, $params = array(), $headers = array()) {
        if (!$url || !$username) {
            return false;
        }
        $this->curl->setOption(CURLOPT_USERPWD, $username . ':' . $password);
        if ($headers) {
            $this->curl->setHeaders($headers);
        }
        $return = $this->curl->put($url, $data, $params);
        return $return;
    }

    //delete
    public function deleteData($url, $username, $password, $headers = array()) {
        if (!$url || !$username) {
            return false;
        }
        $this->curl->setOption(CURLOPT_USERPWD, $username . ':' . $password);
        if ($headers) {
            $this->curl->setHeaders($headers);
        }

        $return = $this->curl->delete($url);
        return $return;
    }


}
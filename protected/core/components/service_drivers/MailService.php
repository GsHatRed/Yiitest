<?php

/**
 * MailService class file.
 *
 * @author lx <lx@tongda2000.com>
 */

class MailService extends AbstractService{
    const CMD_RECEIVE = 0;
    const CMD_SEND = 1;
    const CMD_RECEIVE_BIG = 2;
    const CMD_RETRY = 3;
    const CMD_TEST = 5;
    
    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port; 
        $this->init();        
    }
    
    /**
     * 
     * @param array $cmd 指令
     * <pre>
     * array(
     *      'action'=>MailService::CMD_TEST, 
     *      'data' => 123,
     *      'priority' => 0
     * ) 
     * </pre>
     * @return type
     */
    public function exec($cmd) {
        $action = $cmd['action'];
        $data = $cmd['data'];
        if(!isset($data) || !isset($action) || !in_array($action, (new ReflectionClass($this))->getConstants())) {
            return false;
        }
        $priority = $cmd['priority'];
        $priority = isset($priority) ? intval($priority) : 0;
        $reqData = $action.$priority.$data;
        $reqData = sprintf("%04d", strlen($reqData)+4).$reqData;
        return $this->socket->write($reqData);
    }
}

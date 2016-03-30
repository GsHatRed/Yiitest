<?php

/**
 * ImService class file.
 *
 * @author lx <lx@tongda2000.com>
 */

class ImService extends AbstractService{
    const CMD_SEND = 'C^m^n^';

    const CMD_GROUP_ADD = 'S^d^0^';
    const CMD_GROUP_UPDATE = 'S^d^1^';
    const CMD_GROUP_DELETE = 'S^d^2^';
    
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
     *      'action'=>ImService::CMD_SEND,
     *      'data' => 123,
     * ) 
     * </pre>
     * @return type
     */
    public function exec($cmd) {
        $data = $cmd['action'].$cmd['data'];
        if(!isset($data) || !isset($cmd['action']) || !in_array($cmd['action'], (new ReflectionClass($this))->getConstants())) {
            return false;
        }
        return $this->socket->write($data);
    }
}

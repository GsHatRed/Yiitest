<?php

/**
 * AbstractService class file.
 *
 * @author lx <lx@tongda2000.com>
 */

abstract Class AbstractService{
    protected $socket;
    protected $host;
    protected $port;
    protected $protocol = 'udp';
    protected $timeout = 5;

    public function init() {
        $this->socket = new TSocket($this->host, $this->port, $this->protocol, $this->timeout);
    }
    public function getSocket() {
        return $this->socket;
    }
    abstract public function exec($cmd);
}


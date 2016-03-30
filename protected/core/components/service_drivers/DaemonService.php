<?php

/**
 * TaskService class file.
 *
 * @author zmm <zmm@tongda2000.com>
 */
class DaemonService extends AbstractService {

    const CMD_REG = 'REG';
    const CMD_REG_INFO = 'GET_REG_INFO';
    const CMD_EXEC_TASK = 'EXEC_HTTP_TASK';
//    const CMD_RELOAD_TASK = 'RELOAD_ALL_TASK';
    const CMD_DBBACKUP = 'DB_BACKUP';

    public function __construct($host, $port) {
        $this->host = $host;
        $this->port = $port;
        $this->init();
    }

    public function exec($cmd) {
        if (trim($cmd) == '' || !in_array(explode(' ', $cmd)[0], (new ReflectionClass($this))->getConstants())) {
            file_put_contents("2.txt", "11111");
            return false;
        }
        
        $ret = $this->socket->write($cmd);
        if (strtoupper(bin2hex(substr($ret, 0, 3))) == "EFBBBF") {
            $ret = substr($ret, 3);
        } else {
            $ret = TUtil::iconv($ret, "gbk", "utf8");
        }
          file_put_contents("4.txt", $ret);
        return $ret;
    }

}

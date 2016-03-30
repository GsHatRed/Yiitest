<?php

/**
 * TService class file.
 *
 * @author lx <lx@tongda2000.com>
 */
Yii::import('core.components.service_drivers.*');

class TService {

    const SERVICE_TASK = 'task';
    const SERVICE_IM = 'im';
    const SERVICE_MAIL = 'mail';
    const SERVICE_DAEMON = 'daemon';

    public static function factory($serviceName) {
        $serviceName = strtolower($serviceName);
        if (array_key_exists($serviceName, array(self::SERVICE_TASK, self::SERVICE_IM, self::SERVICE_MAIL, self::SERVICE_DAEMON))) {
         //   file_put_contents("1.txt", $serviceName);
            throw new CException(Yii::t('core', '不支持的服务名称'));
        }
        $params = SysParams::getParams(array($serviceName . '_host', $serviceName . '_port'));
        $className = ucfirst($serviceName) . 'Service';
         
        return new $className($params[$serviceName . '_host'], $params[$serviceName . '_port']);
    }

}

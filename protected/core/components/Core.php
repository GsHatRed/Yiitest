<?php

/**
 * Core class file.
 *
 */

Yii::import('application.core.models.DataLog');

/**
 * 通达核心组件
 *
 * 设置core别名，提供日志方法
 * @property string $version 产品版本号
 *
 */
class Core extends CApplicationComponent {

    const OS_WINDOWS = 'windows';
    const OS_LINUX = 'linux';
    const OS_MAC = 'darwin';
    const OS_UNIX = 'unix';
    const OS_UNKNOWN = '';
    const CHARSET_WINDOWS = 'gbk';
    const CHARSET_UNIX = 'utf-8';

    /**
     * @var string 资源路径
     */
    protected $_assetsUrl;

    public function init() {

        if (Yii::getPathOfAlias('core') === false)
            Yii::setPathOfAlias('core', realpath(dirname(__FILE__) . '/..'));
        Yii::app()->setLanguage(strtolower(Yii::app()->getLanguage()));
        parent::init();
    }

    /**
     * 获取产品版本号
     * @return string
     */
    public function getVersion() {
        return '16.3.31';
    }

    /**
     * 获取产品名称
     * @return string
     */
    public function getProductName() {
        return 'GShatred';
    }

    /**
     * 获取发布的资源的URL路径.
     * @return string
     */
    public function getAssetsUrl() {
        if (isset($this->_assetsUrl))
            return $this->_assetsUrl;
        else {
            $assetsPath = Yii::getPathOfAlias('application.core.assets');
            $assetsUrl = Yii::app()->assetManager->publish($assetsPath);
            return $this->_assetsUrl = $assetsUrl;
        }
    }

    /**
     * 获取静态文件路径
     * @return string
     */
    public function getStaticUrl() {
        if (!empty($params['static_server_http'])) {
            return $params['static_server_http'];
        } else {
            return Yii::app()->baseUrl . '/static';
        }
    }

    /**
     * 获取主题资源路径
     * @return string
     */
    public function getThemeAssetsUrl() {
        $path = Yii::getPathOfAlias('application.modules.theme.modules.' . Yii::app()->user->theme . '.assets');
        return Yii::app()->assetManager->getPublishedUrl($path);
    }

    /**
     * 记录系统日志
     * 
     * @param string $message
     * @param int $logType
     * @param string $level
     * @param string $category
     * @return boolean
     */
    public function log($message, $logType = 0, $level = SysLog::LEVEL_INFO, $category = '', $user = null) {
        if (trim($message) == '')
            return false;

        $model = new SysLog();
        $model->attributes = array(
            'user_id' => is_null($user) ? Yii::app()->user->id : $user->id,
            'user_name' => is_null($user) ? Yii::app()->user->user_name : $user->user_name,
            'category' => $category == '' ? TUtil::getCurrentRoute() : $category,
            'log_type' => $logType,
            'level' => $level,
            'message' => $message,
            'log_ip' => $this->getRealIp(),
        );
        if ($model->save())
            return true;
        else
            return false;
    }

    /**
     * 记录应用日志
     * 
     * @param string $model
     * @param int $pk
     * @param string $logType
     * @param string|array $extraData
     * @return boolean
     */
    public function appLog($model, $pk, $logType, $extraData = '', $user = '') {
        $log = new DataLog();
        $log->attributes = array(
            'user_id' => $user !== '' ? 0 : Yii::app()->user->id,
            'user_name' => $user !== '' ? $user : Yii::app()->user->user_name,
            'model' => $model,
            'pk' => $pk,
            'log_type' => $logType,
            'extra_data' => is_array($extraData) ? serialize($extraData) : $extraData,
        );
        if ($log->save())
            return true;
        else
            return false;
    }

    /**
     * 记录任务操作日志
     * 
     * @param string $model
     * @param int $pk
     * @param string $logType
     * @param string|array $extraData
     * @return boolean
     */
    public function taskLog($processId, $taskId, $logType, $extraData = '', $user = '') {
        $log = new WfRtLog();
        $log->attributes = array(
            'process_id' => $processId,
            'task_id' => $taskId,
            'log_type' => $logType,
            'create_user' => $user !== '' ? 0 : Yii::app()->user->id,
            'create_time' => time(),
            'content' => $extraData,
        );
        if ($log->save())
            return true;
        else
            return false;
    }

    /**
     * 获取真实IP
     * 
     * @return string|boolean
     */
    public function getRealIp() {
        foreach (
        [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ] as $var) {
            if (array_key_exists($var, $_SERVER)) {
                foreach (explode(',', $_SERVER[$var]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false)
                        return $ip;
                }
            }
        }
        return false;
    }


}

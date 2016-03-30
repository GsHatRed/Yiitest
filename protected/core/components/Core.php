<?php

/**
 * Core class file.
 *
 * @author lx <lx@tongda2000.com>
 */
Yii::import('application.modules.sms.models.*');
Yii::import('application.modules.notification.models.*');
Yii::import('application.modules.email.models.*');
Yii::import('application.core.models.DataLog');
Yii::import('application.workflow.models.WfRtLog');

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
    //短信方式sms_type存储数据;(0:短信猫；1:短信网关；2:mas信息机)
    const SMS_TYPE_MODEM = 0;
    const SMS_TYPE_GETWAY = 1;
    const SMS_TYPE_MAS = 2;

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
        return '14.04';
    }

    /**
     * 获取产品名称
     * @return string
     */
    public function getProductName() {
        return 'Office Anywhere 政务版';
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

    /**
     * 发送系统消息提醒
     * 
     * <pre>
     * $params = array(
     *     'remindType' => 'notify',
     *     'content' => '关于xxx考试有关事项的公告',
     *     'remind_url' => '/notify/default/view/id/24/isopen/1',
     *     'to_id' => '4,5',
     * );
     * <pre>
     * @param array $params
     * @return boolean
     */
    public function sendNotification($params = array()) {
        $content = new NotificationContent();
        $content->from_id = isset($params['from_id']) ? $params['from_id'] : Yii::app()->user->id;
        $content->notification_type = $params['remindType'];
        $content->content = $params['content'];
        $content->send_time = $params['send_time'] ? $params['send_time'] : time();
        $content->remind_url = $params['remind_url'];
        $content->people_type = $params['people_type'] ? $params['people_type'] : 0;
        if (is_array($params['to_id']) && !empty($params['to_id'])) {
            $content->save();
            $params['to_id'] = array_unique($params['to_id']);
            foreach ($params['to_id'] as $id) {
                if ($id) {
                    $notification = new Notification();
                    $notification->to_id = $id;
                    $notification->people_type = $params['to_people_type'] ? $params['to_people_type'] : 0;
                    $notification->remind_flag = 0;
                    $notification->delete_flag = 0;
                    $notification->content_id = $content->id;
                    $notification->remind_time = time();
                    $notification->save();
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 发送系统消息提醒
     * 
     * <pre>
     * $params = array(
     *     'remindType' => 'notify',
     *     'content' => '关于xxx考试有关事项的公告',
     *     'remind_url' => '/notify/default/view/id/24/isopen/1',
     *     'to_id' => '4,5',
     * );
     * <pre>
     * @param array $params
     * @return boolean
     */
    public function sendEmail($params = array()) {
        $content = new EmailContent();
        $content->create_user = isset($params['from_id']) ? $params['from_id'] : Yii::app()->user->id;
        $content->subject = $params['subject'];
        $content->content = $params['content'];
        $content->send_time = time();
        $content->create_time = time();
        $content->to_uid = $params['to_uid'];
        $content->cc_uid = isset($params['cc_uid']) ? $params['cc_uid'] : '';
        $content->bcc_uid = isset($params['bcc_uid']) ? $params['bcc_uid'] : '';
        $content->email_status = isset($params['email_status']) ? $params['email_status'] : 1;
        $content->webmail_inside_id = 0;
        $content->important = 0;
        $content->webmail_inside_address = '';
        $content->to_webmail = '';
        $content->compress_content = '';
        Yii::app()->tokenizer->setText(strip_tags($params['content']));
        $content->keyword = Yii::app()->tokenizer->getTopWords();
        $toArray = ($params['to_uid'] != "") ? explode(",", trim($params['to_uid'], ",")) : array();
        if (is_array($toArray) && !empty($toArray)) {
            $content->save();
            $toArray = array_unique($toArray);
            foreach ($toArray as $id) {
                if ($id) {
                    $model = new Email;
                    $model->from_mail = '';
                    $model->to_uid = $id;
                    $model->read_receipt = 0;
                    $model->content_id = $content->id;
                    $model->create_time = $content->send_time;
                    $model->subject = $content->subject;
                    $model->from_uid = Yii::app()->user->id;
                    $model->save();
                }
            }
            return true;
        }
        return false;
    }

    /**
     * 发送短信提醒
     * <pre>
     * $params = array(
     *     'remindType' => 'notify',
     *     'content' => '关于xxx考试有关事项的公告',
     *     'to_id' => array(4, 5),
     *     'mobile' => array("xxx", "xxx"),
     * );
     * <pre>
     * @param array $params
     * @return boolean
     */
    public function sendSMS($params = array()) {
        //add by jcl 2014-06-10 外部收文短信提醒，task不能获取user_id
        $from = isset($params['from_id']) ? $params['from_id'] : Yii::app()->user->id;
        if (!$params['content']) {
            return false;
        }
        if (is_string($params['to_id'])) {
            $params['to_id'] = TUtil::texplode($params['to_id']);
        }
        if (empty($params['to_id']) && empty($params['mobile'])) {
            return false;
        }

        Yii::import('sms.models.Sms');
        $arrMobiles = array();
        //检查当前用户提醒权限
        $bRemindPriv = SmsPriv::checkRemindPriv($from);
        // 从系统用户获取手机号
        if ($bRemindPriv === true && !empty($params['to_id'])) {
            $params['to_id'] = array_unique($params['to_id']);
            if ($params['to_people_type'] == 0) {
                $users = User::getUserInfoById($params['to_id'], true, false);
                //过滤无被提醒权限的用户
                $arrReceiveUser = SmsPriv::filterRemindUsers($params['to_id']);

                foreach ($params['to_id'] as $id) {
                    //手机短信
                    if (in_array($id, $arrReceiveUser) && (!isset($users[$id]['is_enabled']) || $users[$id]['is_enabled'])) {
                        if ($users[$id]['mobile']) {
                            $arrMobiles[] = $users[$id]['mobile'];
                        }
                    }
                }
            } else if (($params['to_people_type'] == 1) || ($params['to_people_type'] == 2)) {
                Yii::import("edustudent.models.*");
                $criteria = new CDbCriteria;
                $criteria->select = TUtil::qc('mobile_phone');
                $criteria->addCondition(TUtil::qc("id"), $params['to_id']);
                if ($params['to_people_type'] == 1) {
                    //给学生发送短信
                    $models = EduStudents::model()->findAll($criteria);
                    foreach ($models as $model) {
                        if ($model->mobile_phone != "") {
                            $arrMobiles[] = $model->mobile_phone;
                        }
                    }
                } else {
                    //给家长发送短信
                    $models = EduParents::model()->findAll($criteria);
                    foreach ($models as $model) {
                        if ($model->mobile_phone != "") {
                            $arrMobiles[] = $model->mobile_phone;
                        }
                    }
                }
            }
        }

        // 合并手机号
        if (!empty($params['mobile'])) {
            $arrMobiles = array_merge($arrMobiles, $params['mobile']);
        }
        $arrMobiles = array_filter($arrMobiles);
        $arrMobiles = array_unique($arrMobiles);

        if (!empty($arrMobiles)) {
            $sendFlag = 0;

            if (substr(SysParams::getParams(array("sms_type"), TRUE), 0, 1) == self::SMS_TYPE_GETWAY) {
                Yii::import('core.components.sms_drivers.*');
                $SmsObj = new SMSGetway();
                $ret = $SmsObj->send($arrMobiles, $params['content'], time());
                if ($ret > 0) {
                    $sendFlag = 1;
                } else {
                    $this->log('发送失败，原因' + $SmsObj->getErrMsg($ret), SysLog::TYPE_SMS, SysLog::LEVEL_ERROR);
                }
            }
            if (substr(SysParams::getParams(array("sms_type"), TRUE), 0, 1) == self::SMS_TYPE_MAS) {
                Yii::import('core.components.sms_drivers.*');
                $SmsObj = new SMSMas();
                $ret = $SmsObj->send($arrMobiles, $params['content']);
                if ($ret == true) {
                    $sendFlag = 1;
                } else {
                    $this->log('发送失败，原因' + $SmsObj->getErrMsg(), SysLog::TYPE_SMS, SysLog::LEVEL_ERROR);
                }
            }
            if ($params['to_people_type'] != 0) {
                $sms_type = Sms::$sms_type['EXTERNAL_PERSONNEl'];
            } else {
                $sms_type = Sms::$sms_type['INTERNAL_PERSONNEl'];
            }
            foreach ($arrMobiles as $mobile) {
                $sms = new Sms();
                $sms->unsetAttributes();
                $sms->sms_type = $sms_type;
                $sms->setIsNewRecord(true);
                $sms->attributes = array(
                    //'from_id' => Yii::app()->user->id,
                    'from_id' => $from,
                    'phone' => $mobile,
                    'content' => TUtil::substr($params['content'], 0, 200),
                    'send_time' => $params['send_time'] ? $params['send_time'] : time(),
                    'send_flag' => $sendFlag,
                    'send_num' => 1,
                );
                $sms->save();
            }
            return true;
        }
        return false;
    }

    /**
     * 移动端推送
     * @param array|string $to
     * @param string $type
     * @param string $content
     */
    public function mobilePush($to, $type, $content) {
        if (SysParams::getParams('enable_mobile_push')) {
            try {
                $imService = TService::factory(TService::SERVICE_IM);
                //PC在线人员不推送
                if (!is_array($to)) {
                    $to = explode(',', rtrim($to, ','));
                }
                if (!SysParams::getParams('enable_mobile_push_online')) {
                    $onlineUsers = UserOnline::model()->findAll(array(
                        'select' => 'id',
                        'condition' => 'id in(:id) and CLIENT!=5 and CLIENT!=6',
                        'params' => array(':id' => implode(',', $to))));
                    if ($onlineUsers != null) {
                        $onlineUsers = TUtil::ar2array($onlineUsers);
                        $to = array_diff($to, $onlineUsers);
                    }
                }
                $to = implode(',', $to) . ',';
                $imService->exec(array(
                    'action' => ImService::CMD_SEND,
                    'data' => $to . '^' . $type . '^' . $content)
                );
            } catch (Exception $exc) {
                $this->log($exc->getMessage(), SysLog::TYPE_MOBILE_PUSH, SysLog::LEVEL_ERROR);
            }
        }
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
    public function edulog($message, $user = null, $people_type = 0, $logType = 0, $level = SysLog::LEVEL_INFO, $category = '') {
        if (trim($message) == '')
            return false;
        $version = TUtil::getSysVersion();
        if (!in_array($version, array(1, 5))) {
            Yii::import('application.core.models.EduSysLog');
            $model = new EduSysLog();
            $model->attributes = array(
                'user_id' => is_null($user) ? Yii::app()->user->id : $user->id,
                'user_name' => is_null($user) ? Yii::app()->user->user_name : $user->name,
                'category' => $category == '' ? TUtil::getCurrentRoute() : $category,
                'log_type' => $logType,
                'level' => $level,
                'message' => $message,
                'log_ip' => $this->getRealIp(),
                'people_type' => $people_type,
                'log_time' => time(),
            );
            if ($model->save())
                return true;
            else
                return false;
        }
    }

}

<?php
/**
 * RemindBehavior class file.
 *
 * @author fl<fl@tongda2000.com>
 */

Yii::import('application.modules.notification.models.*');

class RemindBehavior extends CActiveRecordBehavior {
       
    /**
     * @var string 提醒类型
     */
    public $remindType = '';    
    
    /**
     * @var string 消息提醒
     */
    private $notification;
    
    /**
     * @var string 短信提醒 
     */
    private $sms;
        
    /**
     * 判断是否发送提醒
     */
    private function check($params){
        // 判断内容
        if(empty($params['content']) 
                || empty($params['to_id']) 
                || empty($params['remind_url'])) {
            return false;
        }
        $this->remindType = $_POST['TRemind']['type'];
        $config = SysRemind::model()->getConfig($this->remindType);
        //判断是否启用
        if(!$config['using_flag']) {
            return false;
        }

        $this->notification = $_POST['TRemind']['notification'];
        $this->sms = $_POST['TRemind']['sms'];
        
        return true;
    }
    
   /**
     * @param array 提醒参数
     * 返回值：
     * <pre>
     * array(
     *     'content'=>'text',//提醒内容
     *     'to_id'=>array(),//发送用户id
     *     'remind_url'=>'text',//打开url链接
     * );
     * <pre>
     */
    public function remind($params) {

        if(!$this->check($params)) {
            return false;
        }
        if($this->notification) {
            Yii::app()->core->sendNotification(
                    array(
                        'content' => $params['content'],
                        'remind_url' => $params['remind_url'],
                        'to_id' => $params['to_id'],
                        'remindType' => $this->remindType,
                        'send_time' =>$params['send_time'] ? $params['send_time'] : time(),
                        'people_type' =>$params['people_type'] ? $params['people_type'] : 0,
                        'to_people_type' =>$params['to_people_type'] ? $params['to_people_type'] : 0,
            ));
            //移动端消息推送
            Yii::app()->core->mobilePush($params['to_id'], $this->remindType ,$params['content']);
        }
        if($this->sms){
            Yii::app()->core->sendSMS(
                    array(
                        'content' => $params['content'],
                        'to_id' => $params['to_id'],
                        'remindType' => $this->remindType,
                        'to_people_type' =>$params['to_people_type'] ? $params['to_people_type'] : 0,
            ));
        }
        return true;
    } 
    /** 修改提醒状态
     * @param array 提醒参数
     * 返回值：
     * <pre>
     * array(
     *     'to_id'=>'',//发送用户id
     *     'remind_url'=>'text',//打开url链接
     *     'people_type'=>'0', //人员类型ID
     * );
     * <pre>
     */
    public function updateRemind($params) {
        $url = Yii::app()->controller->module->getId()
                . '/' . Yii::app()->controller->getId()
                . '/' . Yii::app()->controller->getAction()->getId();

        if (in_array($this->owner->tableName(), array("news", "notify", "vote", "collect"))) {
            if (isset($_GET["action"])) {
                unset($_GET["action"]);
            }
        }
        $remindUrl = $params["remind_url"] ? $params["remind_url"] : Yii::app()->createUrl($url, $_GET);
        $toId = $params["to_id"] ? $params["to_id"] : Yii::app()->user->id;
        $peopleType = $params["people_type"] ? $params["people_type"] : 0;
        $notificationContentModel = NotificationContent::model()->find(TUtil::qc('remind_url') . "=:remind_url and " . TUtil::qc('people_type') . "=:people_type", array(":remind_url" => $remindUrl, ":people_type" => $peopleType));
        if ($notificationContentModel != null) {
            $notificationModel = Notification::model()->find(TUtil::qc('content_id') . "=:content_id and " . TUtil::qc('to_id') . "=:to_id and " . TUtil::qc('remind_flag') . "!=2", array(":content_id" => $notificationContentModel->id, ":to_id" => $toId));
            if ($notificationModel != null) {
                $notificationModel->remind_flag = 2;
                $notificationModel->save(false);
            }
        }
    }
}


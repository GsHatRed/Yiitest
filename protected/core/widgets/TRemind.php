<?php
/**
 * Description of TRemind
 *
 * @author FL
 */
Yii::import('sms.models.SmsPriv');
Yii::import('core.widgets.TWidget');

class TRemind extends TWidget {
    
    /**
     * 提醒类型
     * @var string 
     */
    public $type;
    
    /**
     * 提醒启用设置
     * @var array
     */
    public $config;
    
    public function init() {
        parent::init();
        if(!$this->type){
            $this->type = Yii::app()->controller->module->id;
        }
        $this->config = SysRemind::model()->getConfig($this->type);
    }
    
    public function run() {
        if(!isset($this->type))
            return;
        
        echo CHtml::hiddenField('TRemind[type]', $this->type,array('id'=>$this->getId()."_TRemind_type"));
        if($this->config['using_flag']) {
            $this->renderNotification();
            if(SmsPriv::checkRemindPriv(Yii::app()->user->id)){
                $this->renderSms();
            }
        }
    }
    
    /**
     * 消息提醒
     */
    public function renderNotification() {
        echo CHtml::openTag('label', array('class'=>'checkbox inline'))."\n";
        echo CHtml::checkBox('TRemind[notification]', $this->config['notification_remind'],  array('id'=>$this->getId()."_TRemind_notification"))."\n";
        echo CHtml::label('发送应用提醒消息', $this->getId()."_TRemind_notification");
        echo CHtml::closeTag('label')."\n";
    }
    
    /**
     * 手机短信
     */
    public function renderSms() {
        echo CHtml::openTag('label', array('class'=>'checkbox inline'))."\n";
        echo CHtml::checkBox('TRemind[sms]', $this->config['sms_remind'], array('id'=>$this->getId()."_TRemind_sms"))."\n";
        echo CHtml::label('发送手机短信提醒', $this->getId()."_TRemind_sms");
        echo CHtml::closeTag('label')."\n";
    }
    
}

?>

<?php
/**
 * 应用操作日志类文件
 * 
 * @author lx <lx@tongda2000.com>
 */
Yii::import("core.models.DataLog");

class DataLogBehavior extends CActiveRecordBehavior {

    const LOG_TYPE_VIEW   = 'view';
    const LOG_TYPE_CREATE = 'create';
    const LOG_TYPE_UPDATE = 'update';
    const LOG_TYPE_DELETE = 'delete';
    /**
     * 应用模型主键名称
     * @var type 
     */
    public $pk = 'id';
    
    /**
     * 是否允许自动保存
     */
    public $autoSave = false;

    /**
     * 添加日志记录
     * @param string $logType
     * @param string|array $extraData
     * @return type
     */
    public function log($logType = '', $extraData = ''){
        $log = new DataLog();
        $log->attributes = array(
            'user_id' => isset(Yii::app()->user->id)?Yii::app()->user->id:(isset(Yii::app()->session['stu_uid']) ? Yii::app()->session['stu_uid'] : Yii::app()->session['id']),
            'user_name' => isset(Yii::app()->user->user_name)?Yii::app()->user->user_name:(isset(Yii::app()->session['par_name']) ? Yii::app()->session['par_name'] : Yii::app()->session['stu_name']),
            'model' => $this->owner->tableName(),
            'pk' => $this->owner->{$this->pk},
            'log_type' => $logType == '' ? Yii::app()->controller->action->getId() : $logType,
            'log_time' => time(),
            'extra_data' => is_array($extraData) ? serialize($extraData) : $extraData
        );
        return $log->save();
    }
    
    /**
     * 保存事件处理
     * @param type $event
     */
    public function afterSave($event) {
        if($this->autoSave) {
            if($this->owner->isNewRecord) {
                $this->log(self::LOG_TYPE_CREATE);
            } else {
                $this->log(self::LOG_TYPE_UPDATE);
            }
        }
    }

    /**
     * 删除事件处理
     * @param type $event
     */
    public function afterDelete($event) {
        parent::afterDelete($event);
        DataLog::model()->deleteAll(TUtil::qc('pk')."=:pk and ".TUtil::qc('model')."=:model",array(":pk"=>$this->owner->attributes[$this->pk],":model"=>$this->owner->tableName()));
    }
    
    /**
     * 获取日志
     * 
     * @return array
     */
    public function getLogs($logType = null, $group = false){
        $logModel = new DataLog;
        if($this->owner->attributes[$this->pk])
            $logModel->pk = $this->owner->attributes[$this->pk];
        $logModel->model = $this->owner->tableName();
        if(isset($logType)) {
            $logModel->log_type = $logType;
        }
        return $logModel->findAll($logModel->searchCondition($group));
    }
    
    /**
     * 搜索日志
     * 
     * @return CActiveDataProvider
     */
    public function searchLogs($logType = null){
        $logModel = new DataLog;
        if($this->owner->attributes[$this->pk])
            $logModel->pk = $this->owner->attributes[$this->pk];
        $logModel->model = $this->owner->tableName();
        if(isset($logType)) {
            $logModel->log_type = $logType;
        }
        return $logModel->search();
    }
    
    /**
     * 获取关联日志的条件比较器
     * @param type $criteria
     * @param type $logType
     * @param type $logStartTime
     * @param type $logEndTime
     * @param type $userId
     */
    public function getLogCriteria($criteria = null, $logType = null, $logStartTime = 0, $logEndTime = 0, $userId = '') {
        
    }
    /**
     * 是否存在日志
     * @param $logType
     */
    public function hasLog(){
        if($this->owner->attributes[$this->pk]) {
            $pk = $this->owner->attributes[$this->pk];
        }
        $model = $this->owner->tableName();
        $logModel = DataLog::model()->find(TUtil::qc("pk").'=:pk and '.TUtil::qc("model").'=:model and '.TUtil::qc("user_id").'=:user_id', array(':pk'=>$pk,':model'=>$model,':user_id'=>Yii::app()->user->id));
        return $logModel;
    }
}

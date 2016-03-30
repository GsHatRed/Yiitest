<?php
/**
 * 流程转交Modal
 *
 * @author FL
 */

Yii::import('core.widgets.TWidget');

class TTurnModal extends TWidget {
    
    public $id;
    
    public $type;
    
    public $model;
    
    public $taskId;
    
    public $taskPk;
    
    public $nextTasks;
    
    public $defaultTurn;
    
    public $defaultUser;
    
    public $formIds;
    
    public $wfUrl;
    
    public $turnUrl;
    
    public $endTaskUrl;
    
    public $remindUrl;
    
    public $checkConditionUrl;
    
    public function init() {
        parent::init();
        
        $this->id = 'turnModal';
    }
    
    public function run() {
        $this->render('core.views.workflow.turnmodal');
    }
    
    
}

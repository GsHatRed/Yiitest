<?php

Yii::import('core.widgets.TWidget');

class TFreeTurnModal extends TWidget {

    public $id;
    public $type;
    public $wfUrl;
    public $model;
    public $taskId;
    public $taskPk;
    public $formIds;
    public $turnUrl;
    public $remindUrl;
    public $endTaskUrl;

    public function init() {
        parent::init();

        $this->id = 'turnModal';
    }

    public function run() {
        $this->render('core.views.workflow.freeturnmodal');
    }

}

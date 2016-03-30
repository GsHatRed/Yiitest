<?php

Yii::import('core.widgets.TWidget');

class TRollBackModal extends TWidget {

    public $id;
    public $type;
    public $model;
    public $prevTasks;
    public $formIds;
    public $rollBackUrl;

    public function init() {
        parent::init();

        $this->id = 'rollBcakModal';

        if (!$this->type) {
            $this->type = 'list';
        }
    }

    public function run() {
        $this->render('core.views.workflow.rollbackmodal');
    }

}

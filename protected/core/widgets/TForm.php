<?php

    Yii::import('application.modules.form.models.*');
    Yii::import('core.widgets.TWidget');
    /*
     * To change this template, choose Tools | Templates
     * and open the template in the editor.
     */

    class TForm extends TWidget {

        public $scriptFile = 'form.js';

        /**
         * document
         */
        public $model;

        /**
         * 表单ID
         */
        public $formID;

        /**
         * 表单数据ID
         */
        public $dataID;

        /**
         * 动作
         */
        public $action;
        
         /**
         * 模块
         */
        public $module = "document";

        /**
         * 表单类型
         * AIP
         * HTML
         */
        public $formType;

        /**
         * 自定义表单数据保存地址
         */
        public $formSaveUrl;

        /**
         * 版式文件参数
         */
        public $aipParams = array();
        
        /**
         * 委托人
         */
        public $delegateUser;

        /**
         * 流程ID
         */
        private $flowID;

        /**
         * 步骤号
         */
        private $taskNum;
        
        /**
         * 任务pk
         */
        public $taskPk;
        
        /**
         * 获取编号地址
         */
        private $getNumUrl;
        /**
         * 修改编号地址
         */
        private $modifyNumUrl;
        /**
         * 系统代码项
         */
        private $sysCodeUrl;
        /**
         * 列表数据获取地址
         */
        private $showListDataUrl;
        
        /**
         * 加载套红模版地址
         */
        private  $autoDocHeaderUrl;

        /**
         * 获取宏控件值
        */
        private $macroUrl;

        private $formArray = array();

        /**
         * 版式文件菜单显示
         */
        private $aipBtnPriv = array();
        /**
         * 表单打印
         */
        private $formBtn = array();
        /**
         * 可写字段
         */
        private $accessWriteField = array();
        /**
         * 必填字段
         */
        private $requiredField = array();

        /**
         * 主表单映射数据
         * @var array
         */
        public $mapData = array();
        public $pdf417url;
        public function init() {
            $this->getNumUrl = Yii::app()->createUrl('document/default/autoNumber');
            $this->modifyNumUrl = Yii::app()->createUrl('document/default/modify');
            $this->sysCodeUrl = Yii::app()->createUrl('document/default/item'); 
            $this->showListDataUrl = Yii::app()->createUrl('sys/datasrc/dataPicker');
            $this->autoDocHeaderUrl = Yii::app()->createUrl('sys/templateOffice/loadDoc', array('id' => '_docId','type' => 'auto'));
            $this->macroUrl = Yii::app()->createUrl('portal/ajax/macro');
            if ($this->model instanceof Document ||  $this->model instanceof SVTask || $this->model instanceof Collect || $this->model instanceof WfRtApplication) {
                $formDataId = $this->model->getMapInfo($this->formID);
                $this->pdf417url = Yii::app()->createUrl('document/default/getPdf417',array('id'=>$this->model->id));
                if ($formDataId !== null) {
                    $this->dataID = $formDataId;
                }
                $processId = $this->model->getProcessId();
            }
            if ($this->formID && $this->formType == Form::TYPE_HTML) {
                if($this->action === 'update') {
                    if ($this->model instanceof Document) {
                        $this->flowID = $this->model->type->flow_id;
                        $this->formBtn = $this->model->type->getFormBtn($this->model->getTaskId($this->taskPk));
                    }
                    if ($this->model instanceof WfRtApplication) {
                        $this->flowID = $this->model->application->workflow_id;
                        $this->formBtn = array('print');//@todo
                    }
                    if ($this->model instanceof SVTask) {
                        $sysApplication =  SysApplication::model()->find("model='sv_task'");
                        $this->flowID = $this->model->getWorkflowId() ? $this->model->getWorkflowId() :  ($sysApplication ? $sysApplication->workflow_id:"");
                        $this->accessWriteField = $sysApplication ? $sysApplication->getWritableField($this->taskPk) : array();
                        $requiredField = $sysApplication ? $sysApplication->getRequiredField($this->taskPk) : array();
                        if($this->model->getFormId()){
                            if($sysApplication!=null && $sysApplication->form_id!=$this->model->getFormId()){
                                $this->module = "meeting";
                                $requiredField = array();
                            }
                        }
                    }else if ($this->model instanceof Collect) {
                        $sysApplication =  SysApplication::model()->find("model='collect'");
                        $this->flowID = $this->model->getWorkflowId() ? $this->model->getWorkflowId() :  ($sysApplication ? $sysApplication->workflow_id:"");
                        $this->accessWriteField = $sysApplication ? $sysApplication->getWritableField($this->taskPk) : array();
                        $requiredField = $sysApplication ? $sysApplication->getRequiredField($this->taskPk) : array();
                        if($this->model->getFormId()){
                            if($sysApplication!=null && $sysApplication->form_id!=$this->model->getFormId()){
                                $this->module = "meeting";
                                $requiredField = array();
                            }
                        }
                 
                    }else{
                        $this->accessWriteField = $this->model->getWritableField($this->taskPk);
                        $requiredField = $this->model->getRequiredField($this->taskPk);
                    }
                    if(!empty($this->accessWriteField) && isset($this->accessWriteField[$this->formID])) {
                        if(!empty($requiredField) && isset($requiredField[$this->formID])) {
                            $this->requiredField = array_intersect($requiredField[$this->formID], $this->accessWriteField[$this->formID]);
                        }
                    }
                   $this->formArray = TFormUtil::formUpdateView($this->formID, $this->dataID, $this->accessWriteField, $this->flowID,$this->requiredField, $this->delegateUser,$processId,$this->module,$this->taskPk);
                   $this->requiredField = json_encode(FormHtmlField::getFieldType($this->formID, $this->requiredField));
             
                } else {
                    $this->formBtn = array('print');
                }
                if (isset($this->action)) {
                        Yii::app()->clientScript->scriptMap = array(
                                'bootstrap-yii.css' => false,
                        );                        
                    if ($this->action == 'view') {
                        $this->formArray = TFormUtil::formView($this->formID, FormHtml::getPrintModel($this->formID));
                    } else if ($this->action == 'print' || $this->action == 'detail') {
                        $this->formArray = TFormUtil::formPrint($this->formID, $this->dataID,$processId,$this->action);//@todo
                    }
                }
            }
            if ($this->formType == Form::TYPE_AIP) {
                $aipFormPriv = $this->model->type->getAIPFormPriv($this->model->getTaskId($this->taskPk));
                $this->aipBtnPriv[$aipFormPriv] = $this->model->type->getAIPFormBtn($this->model->getTaskId($this->taskPk));
            }
            
            $this->registerScript();
            
            parent::init();
        }

        public function run() {
            if($this->action != 'view') {
                $this->renderButton();
            }
            if (!$this->formID) {
                $this->originalForm();
            } else if ($this->formType == Form::TYPE_HTML) {
                if ($this->formArray['form']) {
                    echo '<form enctype="multipart/form-data" class="form-horizontal" id="'.$this->formID.'-form" action="" method="post">';
                    echo "<style>
                        form * {
                            font-family:宋体;
                        }
                        form > h1,h2,h3,h4,h5,h6{
                            font-weight:normal;
                        }
                        select,input[type=text]{
                            margin-bottom:0px;
                        }
                        .countersign input[readonly]{
                            margin-bottom:10px;
                        }
                        .listview  td > p{
                            text-align:right;
                        }
                        .listview  td{
                            text-align:center!important;
                            white-space:normal!important;
                        }
                        </style>";
                    echo $this->formArray['form'];
                    if ($this->formArray['websign'] == true) {
                        $this->widget('core.widgets.TWebsign');
                    }
                    if(Yii::app()->controller->getAction()->getId()!="download"){
                      $this->widget('core.widgets.TSelectorModal', array('type' => 'parase'));
                      $this->widget('core.widgets.TSelectorModal', array('type' => 'opinion'));
                    }
                } else {
                    $this->widget('core.widgets.TMessageBox', array(
                        'icon' => 'icon-marker-2',
                        'type' => 'warning',
                        'title' => '温馨提示',
                        'content' => '表单内容为空，请完善！'
                    ));
                }
                echo '</form>';
            } else {
                $this->aipForm();
            }
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->formID, implode("\n", $this->getRegisterScripts()));
            parent::run();
        }

        public function renderButton() {
            if (!$this->formID || $this->formType == Form::TYPE_HTML) {
                $formBtn = $this->formBtn;
                if($this->model) {
                    $rtProcess = WfRtProcess::model()->findByPk($this->model->getProcessId());
                }
                echo CHtml::openTag('div',array('style'=>'position:fixed;right:20px;top:5px;','id'=>'form_btn'));
                $this->widget('bootstrap.widgets.TbButton', array(
                    'label' => '导出',
                    'type' => 'danger',
                    'url' => Yii::app()->createUrl('form/html/download', array('formID' => $this->formID, 'dataID' => $this->dataID)),
                    'visible'=> ((!empty($formBtn) && in_array('export',$formBtn))) ? true : false,
                ));
                $this->widget('bootstrap.widgets.TbButton', array(
                    'label' => '打印',
                    'type' => 'danger',
                    'url' => Yii::app()->createUrl('form/html/print', array('formID' => $this->formID, 'dataID' => $this->dataID)),
                    'htmlOptions' => array(
                        'style' => 'margin-left:5px;',
                        'target' => '_blank'
                    ),
                    'visible' => ((!empty($formBtn) && in_array('print',$formBtn)) || ($rtProcess->status==0 && $rtProcess->end_time!=0)) ? true : false
                ));
                $this->widget('bootstrap.widgets.TbButton', array(
                    'label' => '保存',
                    'type' => 'danger',
                    'htmlOptions' => array(
                        'style' => 'margin-left:5px;',
                        'onclick' => 'formUtils.formSave(true)'
                    ),
                    'visible' => (isset($this->action) && $this->action == 'update') ? true : false
                ));
                echo CHtml::closeTag('div');
            }
        }

        public function getRegisterScripts() {
            $accessWriteField  = json_encode($this->accessWriteField[$this->formID]);
            $js[] = "
                if(typeof(formUtils) == 'object') {
                    formUtils.formSaveUrl = '{$this->formSaveUrl}';
                    formUtils.getNumUrl = '{$this->getNumUrl}';
                    formUtils.modifyNumUrl = '{$this->modifyNumUrl}';
                    formUtils.showListDataUrl = '{$this->showListDataUrl}';
                    formUtils.autoDocHeaderUrl = '{$this->autoDocHeaderUrl}';
                    formUtils.macroUrl = '{$this->macroUrl}';
                    formUtils.sysCodeUrl = '{$this->sysCodeUrl}';
                    formUtils.action = '{$this->action}';
                    formUtils.taskPk = '{$this->taskPk}';    
                    formUtils.module = '{$this->module}';
                    formUtils.requiredField = {$this->requiredField};
                    formUtils.accessWriteField = {$accessWriteField};
                    formUtils.init();
                }
            ";
            return $js;
        }
        
        public function registerScript(){
            $cs = Yii::app()->getClientScript();
            if($this->formType == Form::TYPE_HTML){
                $save = 'formUtils.formSave(false, Operation.setStatus);';
            } elseif($this->formType == Form::TYPE_AIP) {
                $save = 'aip_FORM.Save("silent", Operation.setStatus);';
            }
            $js = <<<EOD
var Operation = {
    status : 0,
    setStatus : function(status){
        Operation.status = status;
    },
    getStatus : function(){
        return this.status;
    },
    save : function(){
        this.status = 0;
        {$save}
    }
}       
EOD;
            $cs->registerScript('operation', $js, CClientScript::POS_HEAD);
        }

        public function originalForm() {
            $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
                'id' => 'document-detail-form',
                'type' => 'horizontal',
                'action' => Yii::app()->createUrl('document/default/update', array('id' => $this->model->id)),
                    ));
            echo $form->errorSummary($this->model);

            echo $form->textFieldRow($this->model, 'doc_title');

            echo $form->textFieldRow($this->model, 'begin_dept');

            echo $form->textFieldRow($this->model, 'doc_num');

            echo $form->textFieldRow($this->model, 'user_doc_num');

            echo $form->dropDownListRow($this->model, 'secret_level', SysCode::getCodeList('ARCH_SECRET'));

            echo $form->dropDownListRow($this->model, 'secret_year', SysCode::getCodeList('ARCH_SAVE_DATE'));

            echo $form->dropDownListRow($this->model, 'hurry_level', SysCode::getCodeList('ARCH_URGENCY'));

            echo $form->textAreaRow($this->model, 'remark', array('rows' => 6, 'cols' => 50));

            echo $form->textFieldRow($this->model, 'create_time', array('value' => TDateTimeUtil::format($this->model->create_time, 'medium', 'medium'), 'readonly' => true));
            $this->endWidget();
        }

        public function aipForm() {
            echo '<style>
                .color-red{
                    color: #d84a38;
                }
                .color-blue{
                    color: #4b8cf7;
                }
            </style>';
            echo '<div id="toolbars">';
            $aipObj = "aip_{$this->aipParams['id']}";
            $mapData = CJSON::encode($this->mapData);
            $pdf417url = CJSON::encode($this->pdf417url);
            $edit = array_key_exists('edit', $this->aipBtnPriv);
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'label' => '全屏',
                'icon' => 'icon-screen-4',
                'htmlOptions' => array(
                    'style' => 'margin-top: 3px;',
                    'onclick' => "{$aipObj}.obj.ShowFullScreen = 1;"
                ),
            ));
//            $this->widget('bootstrap.widgets.TbButtonGroup', array(
//                'buttonType' => 'button',
//                'toggle' => 'radio',
//                'buttons' => array(
//                    array('label' => '1:1', 'icon' => 'icon-scale-up', 'htmlOptions' => array('onclick' => "{$aipObj}.obj.SetPageMode(1,100)")),
//                    array('label' => '页宽', 'icon' => 'icon-width', 'htmlOptions' => array('onclick' => "{$aipObj}.obj.SetPageMode(2,100)")),
//                    array('label' => '页高', 'icon' => 'icon-height', 'htmlOptions' => array('onclick' => "{$aipObj}.obj.SetPageMode(4,3)")),
//                    array('label' => '翻页', 'icon' => 'icon-book', 'htmlOptions' => array('onclick' => "{$aipObj}.obj.SetPageMode(32,1)")),
//                ),
//                'htmlOptions' => array(
//                    'style' => 'margin: 3px 0 0 3px;'
//                ),
//            ));
//            $this->widget('bootstrap.widgets.TbButtonGroup', array(
//                'buttonType' => 'button',
//                'toggle' => 'radio',
//                'buttons' => array(
//                    array('label' => '放大', 'icon' => 'icon-zoom-in', 'htmlOptions' => array('onclick' => "{$aipObj}.Zoom(5);")),
//                    array('label' => '缩小', 'icon' => 'icon-zoom-out', 'htmlOptions' => array('onclick' => "{$aipObj}.Zoom(-5);")),
//                ),
//                'htmlOptions' => array(
//                    'style' => 'margin-left:3px;margin-top: 3px;'
//                ),
//            ));
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'label' => '橡皮擦',
                'icon' => 'icon-undo',
                'htmlOptions' => array(
                    'id' => 'erase',
                    'style' => 'margin-left:3px;margin-top: 3px;',
                ),
                'visible' => $edit && in_array('write',$this->aipBtnPriv['edit'])
            ));
            $this->widget('bootstrap.widgets.TbButtonGroup', array(
                'buttonType' => 'button',
                'toggle' => 'radio',
                'buttons' => array(
                    array('label' => '细', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => "{$aipObj}.SetPenWidth(2);", 'title' => '笔宽：细', 'rel' => 'tooltip', 'data-placement' => 'right')),
                    array('label' => '中', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => "{$aipObj}.SetPenWidth(3);", 'title' => '笔宽：中', 'rel' => 'tooltip', 'data-placement' => 'right')),
                    array('label' => '粗', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => "{$aipObj}.SetPenWidth(6);", 'title' => '笔宽：粗', 'rel' => 'tooltip', 'data-placement' => 'right')),
//                    array('label' => '压感', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => 'aip.obj.Pressurelevel=0;', 'title' => '压感', 'rel' => 'tooltip', 'data-placement' => 'right')),
                ),
                'htmlOptions' => array(
                    'style' => 'margin-left:3px;margin-top: 3px;'
                ),
            ));
            $this->widget('bootstrap.widgets.TbButtonGroup', array(
                'buttonType' => 'button',
                'toggle' => 'radio',
                'buttons' => array(
                    array('label' => '红', 'icon' => 'icon-square color-red', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => "{$aipObj}.SetPenColor(255);", 'title' => '笔色：红', 'rel' => 'tooltip', 'data-placement' => 'right')),
                    array('label' => '黑', 'icon' => 'icon-square', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => "{$aipObj}.SetPenColor(0);", 'title' => '笔色：黑', 'rel' => 'tooltip', 'data-placement' => 'right')),
                    array('label' => '蓝', 'icon' => 'icon-square color-blue', 'visible' => $edit && in_array('write',$this->aipBtnPriv['edit']), 'htmlOptions' => array('onclick' => "{$aipObj}.SetPenColor(16711680);", 'title' => '笔色：蓝', 'rel' => 'tooltip', 'data-placement' => 'right')),
                ),
                'htmlOptions' => array(
                    'style' => 'margin-left:3px;margin-top: 3px;'
                ),
            ));
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'danger',
                'label' => '盖章',
                'icon' => 'icon-keyhole',
                'htmlOptions' => array(
                    'style' => 'margin-left:3px;margin-top: 3px;',
                    'onclick' => "{$aipObj}.ActAddSeal();"
                ),
                'visible' => $edit && in_array('seal',$this->aipBtnPriv['edit'])
            ));
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'danger',
                'label' => '生成二维码',
                'icon' => 'icon-barcode',
                'htmlOptions' => array(
                    'style' => 'margin-left:3px;margin-top: 3px;',
                    'onclick' => "{$aipObj}.ActPdf417code();"
                ),
                'visible' => $edit && in_array('seal',$this->aipBtnPriv['edit'])//@todo
            ));
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'danger',
                'label' => '开始手写',
                'icon' => 'icon-pen',
                'htmlOptions' => array(
                    'id' => 'handWrite',
                    'style' => 'margin-left:3px;margin-top: 3px;width:140px;',
                ),
                'visible' => $edit && in_array('write',$this->aipBtnPriv['edit'])
            ));
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'danger',
                'label' => '保存',
                'htmlOptions' => array(
                    'style' => 'margin-left:3px;margin-top: 3px;float:right;',
                    'onclick' => "{$aipObj}.Save();"
                ),
                'visible' => $edit
            ));
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'primary',
                'label' => '打印',
                'htmlOptions' => array(
                    'style' => 'margin-left:3px;margin-top: 3px;float:right;',
                    'onclick' => "{$aipObj}.obj.PrintDoc(1,1);"
                ),
                'visible' => ($edit && in_array('print',$this->aipBtnPriv['edit'])) || (!$edit && in_array('print',$this->aipBtnPriv['read']))
            ));
            echo '</div>';
            $this->widget('core.widgets.TAip', array('params' => $this->aipParams));
            $jsInit = "
                $(window).resize(function(){
                    var winWidth = $(window).width();
                    var winHeight = $(window).height();
                    var objectId = {$aipObj}.cfg.prefix+{$aipObj}.cfg.id;
//                   {$aipObj}.SetSize(winWidth, winHeight - $('#toolbar').outerHeight());
//                    $('#'+ objectId).parent().width(winWidth);
                    $('#'+ objectId).parent().height(winHeight - $('#toolbar').outerHeight() - 45);
                    $('#'+ objectId).parent().css({\"margin-top\":\"3px\"});
                });
                $('#erase').on('click',function(e){
                    if($('#handWrite').hasClass('btn-primary')){
                        {$aipObj}.obj.CurrAction = 0;
                        $('#handWrite').removeClass('btn-primary').addClass('btn-danger');
                        $('#handWrite').html('<i class=\"icon-pen\"></i> 开始手写');
                    }
                    {$aipObj}.ActErase();
                });
                $('#handWrite').on('click',function(){
                    if($(this).hasClass('btn-danger')){
                        {$aipObj}.ActHandWrite();
                        $(this).removeClass('btn-danger').addClass('btn-primary');
                        $(this).html('<i class=\"icon-pen\"></i> 手写中...');
                    } else {
                        {$aipObj}.obj.CurrAction = 0;
                        $(this).removeClass('btn-primary').addClass('btn-danger');
                        $(this).html('<i class=\"icon-pen\"></i> 开始手写');
                    }
                });
                {$aipObj}.mapData = {$mapData}
                {$aipObj}.pdf417url = {$pdf417url}
            ";
            Yii::app()->getClientScript()->registerCoreScript('cookie');
            Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->formID, $jsInit, CClientScript::POS_END);
        }

    }

?>

<?php

/**
 * TFlowDesigner class file.
 *
 * @author lx <lx@tongda2000.com>
 */
Yii::import('core.widgets.TWidget');

class TFlowDesigner extends TWidget {

    public $coreScriptFile = array('jquery', 'jquery.ui');
    public $scriptFile = array(
        '//raphael-min.js',
        '//bootstrap/bootstrap-contextmenu.js',
        '//bootstrap/bootstrap-tour.min.js',
        'designer.js',
    );
    public $cssFile = array('workflow.designer.css');
    public $editable = true;

    /**
     *
     * @var array 直接加载数据
     */
    public $data = array();

    /**
     *
     * @var array 运行中数据
     */
    public $runData = array();
    public $loadUrl = '';
    public $saveUrl = '';
    public $delayLoad = false;

    /**
     *
     * @var int 流程id
     */
    public $id = NULL;
    private $_config;
    private $_view;

    public function init() {
        if($this->editable) {
            $this->cssFile = array('workflow.designer.css');
            $this->_view = 'core.views.workflow.designer';
        } else {
            $this->cssFile = array('workflow.view.css');
            $this->_view = 'core.views.workflow.view';
        }
            
        parent::init();
        if (empty($this->id) && empty($this->data)) {
            throw new CException('配置错误');
        }

        $this->_config = array(
            'editable' => $this->editable,
            'data' => $this->data,
            'id' => $this->id,
            'delayLoad' => $this->delayLoad,
            'showAll'=>Yii::app()->params['show_all_node'],
            'loadUrl' => Yii::app()->createUrl('/portal/ajax/WfDfData', array('id' => $this->id)),
            'saveUrl' => Yii::app()->createUrl('/workflow/designer/save'),
            'runData' => $this->runData,
        );
    }

    public function run() {
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('cookie');
        $config = CJSON::encode($this->_config);
        $JS = <<<EOT
$('.diagram').FlowDesigner($config);
EOT;
        Yii::app()->clientScript->registerScript('designer', $JS);
        $this->render($this->_view);
    }

}

<?php

/**
 * TSelectorModal class file.
 *
 * @author fl <fl@tongda2000.com>
 */
Yii::import('core.widgets.TWidget');

/**
 * 选择器弹出层
 */
class TSelectorModal extends TWidget {

    public $type;
    private $modal_id;

    /**
     * 是否为单选用户
     * 默认 false
     */
    public $single = false;
    private $header;
    private $view_name;
    private $placeholder;
    public $attributes;
    public $controllerId;
    public $showOtherOrg = false;

    public function init() {
        $this->controllerId = $this->getController()->id;
        if (in_array(strtolower($this->type), array('user'))) {
//            $this->showOtherOrg = User::isInterfaceUser() && User::isIndependentUser() && !in_array($this->controllerId, Yii::app()->params['filter_models']) ? true : false;
            $this->showOtherOrg = User::isInterfaceUser() && Yii::app()->user->independent && !in_array($this->controllerId, Yii::app()->params['filter_models']) ? true : false;
        }
        switch (strtolower($this->type)) {
            case 'otherorg':
                $this->modal_id = 'selectotherorg';
                $this->header = '选择外部组织机构';
                $this->view_name = 'core.views.selector.otherorg';
                $this->scriptFile = 'selector/otherorg.js';
                break;
            case 'user':
                $this->modal_id = $this->single == false ? 'selectuser' : 'selectsingleuser';
                $this->header = '选择人员';
                $this->placeholder = '按名字或者拼音缩写搜索...';
                $this->view_name = $this->single == false ? 'core.views.selector.user' : 'core.views.selector.singleuser';
                $this->scriptFile = $this->single == false ? 'selector/user.js' : 'selector/singleuser.js';
                break;
            case 'wfuser':
                $this->modal_id = 'selectwfuser';
                $this->header = '选择人员';
                $this->placeholder = '按名字或者拼音缩写搜索...';
                $this->view_name = 'core.views.selector.wfuser';
                $this->scriptFile = 'selector/wfuser.js';
                break;
            case 'org':
                $this->modal_id = $this->single == false ? 'selectorg' : 'selectsingleorg';
                $this->header = '选择部门';
                $this->placeholder = '搜索部门';
                $this->view_name = $this->single == false ? 'core.views.selector.org' : 'core.views.selector.singleorg';
                $this->scriptFile = $this->single == false ? 'selector/org.js' : 'selector/singleorg.js';
                break;
            case 'role':
                $this->modal_id = 'selectrole';
                $this->header = '选择角色';
                $this->placeholder = '搜索角色';
                $this->view_name = 'core.views.selector.role';
                $this->scriptFile = 'selector/role.js';
                break;
            case 'table':
                $this->modal_id = 'selecttable';
                $this->header = '选择表';
                $this->placeholder = '搜索表名';
                $this->view_name = 'core.views.selector.table';
                $this->scriptFile = 'selector/table.js';
                break;
            case 'form':
                $this->modal_id = 'selectform';
                $this->header = '选择表单';
                $this->placeholder = '搜索表单';
                $this->view_name = 'core.views.selector.form';
                $this->scriptFile = 'selector/form.js';
                break;
            case 'wfdf':
                $this->modal_id = 'selectwfdf';
                $this->header = '选择流程';
                $this->placeholder = '搜索流程';
                $this->view_name = 'core.views.selector.wfdf';
                $this->scriptFile = 'selector/wfdf.js';
                break;
            case 'doctype':
                $this->modal_id = 'selectdoctype';
                $this->header = '选择公文类型';
                $this->placeholder = '搜索公文类型';
                $this->view_name = 'core.views.selector.doctype';
                $this->scriptFile = 'selector/doctype.js';
                break;
            case 'address':
                $this->modal_id = 'selectaddress';
                $this->header = '选择通讯录人员';
                $this->placeholder = '搜索通讯录人员';
                $this->view_name = 'core.views.selector.address';
                $this->scriptFile = 'selector/address.js';
                break;
            case 'parase':
                $this->modal_id = 'selectparase';
                $this->header = '选择常用语';
                $this->placeholder = '搜索常用语';
                $this->view_name = 'core.views.selector.parase';
                $this->scriptFile = 'selector/parase.js';
                break;
            case 'opinion':
                $this->modal_id = 'selectopinion';
                $this->header = '选择流程意见';
                $this->placeholder = '搜索流程意见';
                $this->view_name = 'core.views.selector.opinion';
                $this->scriptFile = 'selector/opinion.js';
                break;
            default:
                break;
        }
        parent::init();
    }

    public function run() {
        if (!empty($this->modal_id)) {
            $this->beginWidget('bootstrap.widgets.TbModal', array('id' => $this->modal_id, 'htmlOptions' => array('data-height' => 260)));
            $this->renderHeader();
            $this->renderContent();
            $this->renderFooter();
            $this->endWidget();
        }
    }

    public function renderHeader() {
        echo CHtml::openTag('div', array('class' => 'modal-header'));
        echo CHtml::openTag('h4', array('style' => 'height:20px;'));
        echo $this->header;
        echo CHtml::textField('', '', array('class' => 'pull-right search-query', 'placeholder' => $this->placeholder));
        echo CHtml::closeTag('h4');
        echo CHtml::closeTag('div');
    }

    public function renderContent() {
        echo CHtml::openTag('div', array('class' => 'modal-body', 'style' => 'padding:5px 10px;'));
        if ($this->view_name) {
            $this->render($this->view_name, array('id' => $this->modal_id, 'controllerId' => $this->controllerId, 'showOtherOrg' => $this->showOtherOrg));
        }
        echo CHtml::closeTag('div');
    }

    public function renderFooter() {
        echo CHtml::openTag('div', array('class' => 'modal-footer'));
        echo CHtml::tag('div', array('id' => 'selected_labels', 'style' => 'width:90%;'), '');
        $this->widget('bootstrap.widgets.TbButton', array(
            'type' => 'primary',
            'label' => '确定',
            'url' => '##',
        ));
        echo CHtml::closeTag('div');
    }

}

?>

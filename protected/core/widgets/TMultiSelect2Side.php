<?php
/**
 * 两侧形式的多选框
 *
 * @author fl <fl@tongda2000.com>
 */
class TMultiSelect2Side extends TWidget {

    public $tableHtmlOptions = array('class'=>'table table-bordered', 'style'=>'text-align: center;');

    public $btnTdHeaderHtmlOptions = array('style'=>'width:30px;');

    public $sortBtnTdHeaderContent = '排序';

    public $selectHeaderHtmlOptions = array('style'=>'text-align: center;');

    public $moveBtnTdHeaderContent = '选择';

    public $btnTdHtmlOptions = array('style'=>'vertical-align:middle;');

    public $selectTdHtmlOptions = array('style'=>'text-align: center;padding: 0;');

    public $selectHeaderTitle = '显示以下桌面模块';

    public $noselectHeaderTitle = '备选桌面模块';

    public $selectName;

    public $noselectName;

    public $data = array('select'=>array(), 'noselect'=>array());

    public $buttons = array();

    public $selects = array();

    public $scriptFile ='jquery.multiselect.js';

    public function init(){
        parent::init();

        $id = $this->getId();

        $this->tableHtmlOptions['id'] = $this->id;

        $this->buttons = array(
            'up' => array(
                'buttonType'=>'button',
                'type'=>'primary',
                'size'=>'small',
                'icon'=>'icon-arrow-up',
                'htmlOptions'=>array('id'=>$id.'_btn_up'),
            ),
            'down' => array(
                'buttonType'=>'button',
                'type'=>'primary',
                'size'=>'small',
                'icon'=>'icon-arrow-down',
                'htmlOptions'=>array('id'=>$id.'_btn_down'),
            ),
            'left' => array(
                'buttonType'=>'button',
                'type'=>'primary',
                'size'=>'small',
                'icon'=>'icon-arrow-left',
                'htmlOptions'=>array('id'=>$id.'_btn_moveleft'),
            ),
            'right' => array(
                'buttonType'=>'button',
                'type'=>'primary',
                'size'=>'small',
                'icon'=>'icon-arrow-right-2',
                'htmlOptions'=>array('id'=>$id.'_btn_moveright'),
            ),
            'selectall' => array(
                'buttonType'=>'button',
                'type'=>'info',
                'size'=>'small',
                'label'=>'全选',
                'htmlOptions'=>array('id'=>$id.'_btn_select_all', 'style'=>'margin-bottom: 10px;')
            ),
            'noselectall' => array(
                'buttonType'=>'button',
                'type'=>'info',
                'size'=>'small',
                'label'=>'全选',
                'htmlOptions'=>array('id'=>$id.'_btn_noselect_all','style'=>'margin-bottom: 10px;')
            ),
        );

        if(!$this->selectName){
            $this->selectName = $id.'_select';
        }

        if(!$this->noselectName){
            $this->noselectName = $id.'_noselect';
        }

        $this->selects = array(
            'select' => array(
                'name'=>$this->selectName.'[]',
                'select'=>'',
                'data'=>$this->data['select'],
                'htmlOptions'=>array('id'=>$id.'_select', 'style'=>'width:100%;height: 240px;', 'multiple'=>'multiple'),
            ),
            'noselect' => array(
                'name'=>$this->noselectName.'[]',
                'select'=>'',
                'data'=>$this->data['noselect'],
                'htmlOptions'=>array('id'=>$id.'_noselect', 'style'=>'width:100%;height: 240px;', 'multiple'=>'multiple'),
            ),
        );
    }

    public function run() {

        $this->registerScript();

        echo CHtml::openTag('table', $this->tableHtmlOptions);

        $this->renderThead();

        $this->renderTbody();

        echo CHtml::closeTag('table');

    }

    protected function renderThead(){
        echo CHtml::openTag('thead');
        echo CHtml::openTag('tr');
        echo CHtml::tag('td', $this->btnTdHeaderHtmlOptions, $this->sortBtnTdHeaderContent);
        echo CHtml::tag('td', $this->selectHeaderHtmlOptions, $this->selectHeaderTitle);
        echo CHtml::tag('td', $this->btnTdHeaderHtmlOptions, $this->moveBtnTdHeaderContent);
        echo CHtml::tag('td', $this->selectHeaderHtmlOptions, $this->noselectHeaderTitle);
        echo CHtml::closeTag('tr');
        echo CHtml::closeTag('thead');
    }

    protected function renderTbody(){
        echo CHtml::openTag('tbody');
        echo CHtml::openTag('tr');
        $this->renderSortTd();
        $this->renderSelectTd();
        $this->renderMoveTd();
        $this->renderNoSelectTd();
        echo CHtml::closeTag('tr');
        echo CHtml::closeTag('tbody');
    }

    protected function renderSortTd(){
        echo CHtml::openTag('td', $this->btnTdHtmlOptions);
        $this->widget('bootstrap.widgets.TbButton', $this->buttons['up']);
        echo CHtml::tag('p', array('style'=>'height:10px;'), '');
        $this->widget('bootstrap.widgets.TbButton', $this->buttons['down']);
        echo CHtml::closeTag('td');
    }

    protected function renderMoveTd(){
        echo CHtml::openTag('td', $this->btnTdHtmlOptions);
        $this->widget('bootstrap.widgets.TbButton', $this->buttons['left']);
        echo CHtml::tag('p', array('style'=>'height:10px;'), '');
        $this->widget('bootstrap.widgets.TbButton', $this->buttons['right']);
        echo CHtml::closeTag('td');
    }

    protected function renderSelectTd(){
        echo CHtml::openTag('td', $this->selectTdHtmlOptions);
        echo CHtml::dropDownList(
                $this->selects['select']['name'],
                $this->selects['select']['select'],
                $this->selects['select']['data'],
                $this->selects['select']['htmlOptions']
                );
        $this->widget('bootstrap.widgets.TbButton', $this->buttons['selectall']);
        echo CHtml::closeTag('td');
    }

    protected function renderNoSelectTd(){
        echo CHtml::openTag('td', $this->selectTdHtmlOptions);
        echo CHtml::dropDownList(
                $this->selects['noselect']['name'],
                $this->selects['noselect']['select'],
                $this->selects['noselect']['data'],
                $this->selects['noselect']['htmlOptions']
                );
        $this->widget('bootstrap.widgets.TbButton', $this->buttons['noselectall']);
        echo CHtml::closeTag('td');

    }

    public function registerScript(){
        Yii::app()->clientScript->registerScript($this->id, '
            $("#'.$this->id.'").MultiSelect();
        ');
    }

}

?>

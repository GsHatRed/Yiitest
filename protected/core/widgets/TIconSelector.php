<?php

/**
 * TIconSelector class file.
 *
 * @author FL
 */
class TIconSelector extends CInputWidget {

    /**
     * 模态图片选择窗口URL
     * @var string 
     */
    public $modalDialogUrl = '';
    
    public function init() {
        if($this->modalDialogUrl == '') {
            $this->modalDialogUrl = Yii::app()->createUrl('/portal/widget/icon');
        }
    }

    public function run() {

        list($name, $id) = $this->resolveNameID();

        $this->registerClientScript($id);

        $this->htmlOptions['id'] = $id;

        // 是否存在Model
        if ($this->hasModel()) {
            echo CHtml::activehiddenField($this->model, $this->attribute, $this->htmlOptions);
        } else {
            echo CHtml::hiddenField($name, $this->value, $this->htmlOptions);
        }
        $content = CHtml::tag('i', array('class' => isset($this->attribute) ? $this->model->{$this->attribute} : $this->value), '');
        echo CHtml::tag('span', array('id' => $id . '_show', 'style' =>(isset($this->attribute) && $this->model->{$this->attribute}!="") ? 'font-size:25px;vertical-align: middle;display: inline-block;margin-right:6px' : 'font-size:25px;vertical-align: middle;display: inline-block;'), $content);

        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => '选择图片',
            'id' => $id . '_btn',
        ));
    }

    /**
     * 注册JS
     */
    protected function registerClientScript($id) {

        Yii::app()->clientScript->registerScript(__CLASS__ . $this->getId(), '
function updateIcon(className,classId){ $("#"+classId).val(className);$("#"+classId+"_show").find("i").attr("class", className);}
$("#' . $id . '_btn").on("click",function(){
    if(window.iconTimer) clearInterval(window.iconTimer);
    window.returnValue = "";
    var classId = "'.$id.'";
    window.returnValue = TUtil.openUrl("' . $this->modalDialogUrl . '", "modal", "", 550, 350);
    if(window.returnValue) {
        updateIcon(returnValue,classId);
    } else {
        window.iconTimer = setInterval(function(){if(window.returnValue) {updateIcon(returnValue,classId);clearInterval(window.iconTimer);}},500);
    }
});            
');
    }

}

?>

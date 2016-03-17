<?php

/**
 * TbFileSelector class file.
 * @author lx
 * @copyright  Copyright &copy; Tongda Tec 2013
 * @package bootstrap.widgets
 */

/**
 *
 * 示例用法：
 * $this->
 */
class TbFileSelector extends CInputWidget {

    /**
     *
     * @var TbActiveForm 表单对象
     */
    public $form;
    public $selectorType = NULL;

    /**
     *
     * @var array 参数
     */
    public $options = array();

    /**
     * @var array the HTML attributes for the widget container.
     */
    public $htmlOptions = array();

    /**
     * 是否允许从文件柜选中
     * @var bool
     */
    public $enableFileFolder = true;

    /**
     * 是否允许插入图片
     * @var bool
     */
    public $enableInsertPicture = false;

    /**
     * 模态图片选择窗口URL
     * @var string 
     */
    private $_fileFolderUrl = '/portal/widget/fileselector';

    /**
     * Initializes the widget.
     */
    public function init() {
        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();

        if (isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'file-selector ' . $this->htmlOptions['class'];
        } else {
            $this->htmlOptions['class'] = 'file-selector';
        }
        if (isset($this->htmlOptions['enableFileFolder'])) {
            $this->enableFileFolder = $this->htmlOptions['enableFileFolder'];
        }

        if (isset($this->htmlOptions['enableInsertPicture'])) {
            $this->enableInsertPicture = $this->htmlOptions['enableInsertPicture'];
        }

        $optionsDefault = array(
            'list' => '#' . $this->id . '-container',
            'STRING' => array(
                'remove' => '<i class="icon-remove" rel="tooltip" title="去掉该文件"></i>',
                'selected' => '文件：$file',
                'denied' => '禁止上传扩展名为$ext的文件',
                'duplicate' => '您已经选择了这个文件，请勿重复选择：$file',
            )
        );

        $this->options = isset($this->options) ? array_merge($optionsDefault, $this->options) : $optionsDefault;

        if (!isset($this->selectorType)) {
            $this->selectorType = 'link';
        }
    }

    /**
     * Runs the widget.
     */
    public function run() {
        Yii::import("sys.models.SysModules");
        $module = Yii::app()->controller->module->id;
        $sysModule = SysModules::model()->find(TUtil::qc('code') . "=:code", array(":code" => $module));
        if ($sysModule != null && ($sysModule->enable_attachment == 0)) {
            return;
        }
        $id = $this->id . '-file';
        $containerId = $this->id . '-container';
        $label = '从本地选择';
        if ($this->hasModel()) {
            $name = CHtml::activeName($this->model, $this->attribute) . '[]';
            $attributes = array('name' => $name, 'id' => $id, 'hideFocus' => true);
            if (isset($this->htmlOptions['accept'])) {
                $attributes['accept'] = $this->htmlOptions['accept'];
            }
            if (isset($this->htmlOptions['maxlength'])) {
                $attributes['maxlength'] = $this->htmlOptions['maxlength'];
            }
            if (isset($this->htmlOptions['multiple'])) {
                $attributes['multiple'] = $this->htmlOptions['multiple'];
            }
            if ($this->form) {
                $label .= $this->form->fileField($this->model, $this->attribute, $attributes);
            } else {
                $label .= CHtml::activeFileField($this->model, $this->attribute, $attributes);
            }
        }

        $this->controller->widget('bootstrap.widgets.TbButton', array(
            'type' => $this->selectorType,
            'label' => $label,
            //'size' => 'small',
            'icon' => 'storage',
            'encodeLabel' => false,
            'htmlOptions' => $this->htmlOptions,
        ));
        if ($this->enableFileFolder == true) {
            echo "&nbsp;&nbsp;&nbsp;";
            $this->controller->widget('bootstrap.widgets.TbButton', array(
                'type' => $this->selectorType,
                'label' => '从文件柜选择',
                //'size' => 'small',
                'buttonType' => 'button',
                'icon' => 'folder',
                'id' => $id . '_btn',
            ));
        }
        if ($this->enableInsertPicture == true) {
            echo "&nbsp;&nbsp;&nbsp;";
            $this->widget('core.widgets.TPictureSelector', array(
                'model' => $this->model,
                'attribute' => $this->attribute . "_picture",
            ));
        }
        if ($this->enableFileFolder == true) {
          echo "<div id='SelFileDiv'></div>";
        }
        echo CHtml::tag('div', array('id' => $containerId), '', true);
        if ($this->enableFileFolder == true) {
            echo CHtml::hiddenField('Sel_ATTACHMENT_ID', '', array('id' => 'Sel_ATTACHMENT_ID'));
            echo CHtml::hiddenField('Sel_ATTACHMENT_NAME', '', array('id' => 'Sel_ATTACHMENT_NAME'));
            $this->registerClientScript($id);
        }

        Yii::app()->bootstrap->registerAssetCss('bootstrap-file-selector.css');
        $cs = Yii::app()->getClientScript();
        $cs->registerCoreScript('multifile');
        $cs->registerScript(__CLASS__ . '#' . $this->id, "\n$('#{$id}').MultiFile(" . CJavaScript::encode($this->options) . ")");
    }

    /**
     * 注册JS
     */
    protected function registerClientScript($id) {
        $fileFolderurl = $this->_fileFolderUrl;
        $array = array('length'=>'pk');
        $max = 0;
        $accept = "";
        if (isset($this->htmlOptions['accept']) && $this->htmlOptions['accept'] != "") {
            $array["accept"] = $this->htmlOptions['accept'];
            $accept = $array["accept"];
        }
        if (isset($this->htmlOptions['maxlength']) || isset($this->options['max'])) {
            $array["max"] = $this->htmlOptions['maxlength'] ? $this->htmlOptions['maxlength'] : $this->options['max'];
            $max = intval($array["max"]);
        }
        $url = !empty($array) ? Yii::app()->controller->createUrl($fileFolderurl, $array) : Yii::app()->controller->createUrl($fileFolderurl);
        Yii::app()->clientScript->registerScript(__CLASS__ . $this->getId(), '
 var  containerId = "'.($this->id . '-container').'";
$("#' . $id . '_btn' . '").click(function(){
        var url = "'.$url.'";
        var  max = '.$max.';
        var fileLength = $("#"+containerId+" .MultiFile-remove").length;
        if(fileLength>=max && max!=0){
           return false;
        }
        var myleft=(screen.availWidth-780)/2;
        var mytop=100
        var mywidth=780;
        var myheight=500;
        window.open(url.replace("pk",fileLength),"从文件柜中获取文件","height="+myheight+",width="+mywidth+",status=1,toolbar=no,menubar=no,location=no,scrollbars=yes,top="+mytop+",left="+myleft+",resizable=yes");
        return false;
    });  
    $(".MultiFile-remove").live("click",function(){
        $(this).parent().remove();
        var FileId = $(this).attr("data-value");
        var NameArray = new Array();
        var IdArray = new Array();
        var ParentWindow=window.opener;
        if($("#Sel_ATTACHMENT_NAME")&&$("#Sel_ATTACHMENT_NAME").val()!="")
            NameArray=$("#Sel_ATTACHMENT_NAME").val().split("*");
       if($("#Sel_ATTACHMENT_ID")&&$("#Sel_ATTACHMENT_ID").val()!="")
            IdArray=$("#Sel_ATTACHMENT_ID").val().split("*");
        var NameValue="";
        var IdValue="";
        var DivInnerHTML="";
        var flag=0;
        for(j=0;j<NameArray.length;j++)
        {
            if(NameArray[j]=="")
                continue;
            if(IdArray[j]==FileId)
            {
                flag=1;
                continue;
            }
            NameValue+=NameArray[j]+"*";
            IdValue+=IdArray[j]+"*";
            DivInnerHTML+="<div class=\"MultiFile-label\"><a class=\"MultiFile-remove\" href=\"javascript:;\" data-value="+IdArray[j]+"><i class=\"icon-remove\" rel=\"tooltip\" title=\"去掉该文件\"></i></a> <span class=\"MultiFile-title\">"+NameArray[j]+"</span></div>";
        }
   
        if(flag==1)
        {
            $("#Sel_ATTACHMENT_NAME").val(NameValue);
            $("#Sel_ATTACHMENT_ID").val(IdValue);
            $("#SelFileDiv").html(DivInnerHTML);
        }
    });
');
    }

}

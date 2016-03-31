<?php

/**
 * TNav class file.
 *
 */
Yii::import('core.widgets.TWidget');

class TNav extends TWidget {

    /**
     * 标题
     * @var string
     */
    public $title;

    /**
     * 标题图标
     * @var string
     */
    public $headIcon;

    /**
     * 导航链接
     * @var array
     * <pre>
     *     array(
     *         array('label'=>'日志', 'url'=>'#'),
     *         array('label'=>'新建日志'),
     *         ...
     *     )
     * </pre>
     */
    public $links = array();

    /**
     * Nav属性
     * @var array
     */
    public $htmlOptions = array();

    /**
     * 内容HTML属性
     */
    public $contentHtmlOptions = array();

    /**
     * @var boolean 可编辑标题
     */
    public $editTitle = false;

    /**
     * @var Model 可编辑对象
     */
    public $model;
    
    /**
     * @var string 自定义标题提交action
     */
    public $action = 'update';
    /**
     * @var array 可编辑参数
     */
    public $editOptions = array();

    public function init() {
        parent::init();
        if (!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->getId();

        if (isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] = 'td-nav' . $this->htmlOptions['class'];
        else
            $this->htmlOptions['class'] = 'td-nav';

        if ($this->editTitle) {
            if (!isset($this->model)) {
                throw new CException('Model不能为空!');
            }
        }

        $this->renderHeader();
        $this->renderContentBegin();
    }

    public function run() {

        if ($this->editTitle) {
            $this->registerScript();
        }

        $this->renderContentEnd();
        $this->renderFooter();
    }

    public function renderHeader() {
        echo CHtml::openTag('div', $this->htmlOptions) . "\n";
        echo '<table class="td-nav-table"><tr>' . "\n";
        $this->renderTitle();
        $this->renderLinks();
    }

    /**
     * 显示标题
     */
    public function renderTitle() {
        if (isset($this->title)) {
            echo '<td>' . "\n";
            if ($this->headIcon)
                $this->title = '<i class="' . $this->headIcon . '"></i>' . $this->title;
            $this->title = '<span class="td-nav-title">'
                    . $this->title . '</span>';
            echo $this->title . "\n";

            if ($this->editTitle) {
                $titleForm = $this->controller->beginWidget('bootstrap.widgets.TbActiveForm', array(
                    'id' => 'titleForm',
                    'type' => 'inline',
                    'action' => $this->controller->createUrl($this->action, array('id' => $this->model->id)),
                    'htmlOptions' => array('style' => 'margin:0px;'),
                ));
                echo $titleForm->textFieldRow($this->model, $this->editOptions['titleField'], array('style' => 'width:100%;display:none;'));
                $this->controller->endWidget();
            }

            echo '</td>' . "\n";
        }
    }

    /**
     * 显示链接
     */
    public function renderLinks() {
        if (empty($this->links))
            return;

        if (!empty($this->links) && is_array($this->links)) {
            echo '<td>' . "\n";
            $i = 0;
            $n = count($this->links);
            foreach ($this->links as $link) {
                $options = $link;
                $options['type'] = 'link';
                $options['htmlOptions']['style'] = 'padding-left:0;padding-right:0;';
                if (!isset($options['url']))
                    $options['htmlOptions']['style'] .= ' text-decoration:none;color:#000;cursor:default;';
                $this->controller->widget('bootstrap.widgets.TbButton', $options);
                if ($i < $n - 1)
                    echo '<span style="display:inline-block;vertical-align: middle;color:#000;">&nbsp;/&nbsp;</span>';
                $i++;
            }
            echo '</td>' . "\n";
//            $pinJs = "\n$('#'{$this->getId()}).pin()";
//            Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl.'/js/jquery/jquery.pin.js')
//                    ->registerScript(__CLASS__.$this->getId() , $pinJs);
        }
    }

    public function renderContentBegin() {
        echo '<td>' . "\n";
        echo CHtml::openTag('div', $this->contentHtmlOptions);
    }

    public function renderContentEnd() {
        echo CHtml::closeTag('div');
        echo '</td>' . "\n";
        echo '</tr>' . "\n";
        echo '</table>' . "\n";
    }

    public function renderFooter() {
        echo CHtml::closeTag('div') . "\n";
    }

    public function registerScript() {

        $name = CHtml::activeName($this->model, $this->editOptions['titleField']);
        $id = CHtml::getIdByName($name);

        $cs = Yii::app()->getClientScript();

        $cs->registerScript($this->id, '
            $(".td-nav .td-nav-title").hide();
            $("#"+"' . $id . '").show();
            $("#"+"' . $id . '")[0].focus();
            $(document).on("dblclick", ".td-nav .td-nav-title", function(){
                $(this).hide();
                $("#"+"' . $id . '").show();
                $("#"+"' . $id . '")[0].focus();
            });

    $(document).on("focusout", "#"+"' . $id . '", function(){
        var $form = $("#titleForm");
        $.ajax({
            url : $form.attr("action"),
            type : "post",
            data : $form.serialize(),
            success : function(data){
                if(data === "OK"){
                    //alert("保存成功!");
                }
            }
        });
        $(this).hide();
        $(".td-nav .td-nav-title").text($(this).val()).show();
    });

    $(document).on("keydown", "#"+"' . $id . '", function(event){
        if(event.keyCode === 13){
            return false;
        }
        return true;
    });
');
    }

}

?>

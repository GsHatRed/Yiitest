<?php
/**
 * THelper class file.
 * 
 * @author hf
 */

Yii::import('core.widgets.TWidget');
Yii::import('help.models.Help');

/**
 * 帮助信息显示组件
 */
class THelper extends TWidget {

    /**
     * $id 帮助id
     * 支持string array
     */
    public $id;
    public $_iconId = 'help_icon';
    public $_iconClass = 'icon-question';

    /**
     * 帮助条目信息
     */
    public $items;

    /**
     * 帮助图标属性
     */
    public $htmlOptions = array();

    /**
     * 详情css文件
     */
    public $cssFile = array('markdown/docs.css');
    private $view_name = 'core.views.markdown.view';

    public function init() {
        if (!$this->id)
            return '';
        if (is_numeric($this->id)) {
            $this->id = array($this->id);
        } else if (is_string($this->id)) {
            $this->id = explode(',', trim($this->id, ','));
        }
        if (is_array($this->id) && !empty($this->id)) {
            $this->items = Help::getItems($this->id);
        }
        if (!$this->htmlOptions['id'])
            $this->htmlOptions['id'] = $this->_iconId;
        else
            $this->_iconId = $this->htmlOptions['id'];
        if (!$this->htmlOptions['class'])
            $this->htmlOptions['class'] = $this->_iconClass;
        if (!$this->htmlOptions['style'])
            $this->htmlOptions['style'] = 'margin-left:6px;cursor:pointer;';
        if (!$this->htmlOptions['rel'])
            $this->htmlOptions['rel'] = 'tooltip';
        if (!$this->htmlOptions['data-original-title'])
            $this->htmlOptions['data-original-title'] = '帮助';
        parent::init();
    }

    public function run() {
        $this->renderIcon();
        $this->beginWidget('bootstrap.widgets.TbModal', array('id' => $this->_iconId . '_modal', 'options' => array("backdrop" => "static"), 'htmlOptions' => array('style' => 'width:1000px;left:35%;')));
        $this->renderHeader();
        $this->renderContent();
        $this->renderFooter();
        $this->endWidget();
        //注册JS
        $cs = Yii::app()->getClientScript();
        $cs->registerScript(__CLASS__ . '#' . $this->_iconId, implode("\n", $this->getRegisterScripts()));
    }

    public function renderIcon() {
        echo CHtml::tag('i', $this->htmlOptions, '');
    }

    public function renderHeader() {
        echo CHtml::openTag('div', array('class' => 'modal-header'));
        echo CHtml::tag('a', array('class' => 'close', 'data-dismiss' => 'modal'), '×');
        echo CHtml::openTag('h4', array('style' => 'height:20px;'));
        echo '系统帮助文档';
        echo CHtml::closeTag('h4');
        echo CHtml::closeTag('div');
    }

    public function renderContent() {
        echo CHtml::openTag('div', array('class' => 'modal-body', 'style' => 'overflow:hidden;padding-top: 0;padding-bottom: 0;'));
        if ($this->view_name) {
            $this->render($this->view_name, array('items' => $this->items, 'id' => $this->_iconId));
        }
        echo CHtml::closeTag('div');
    }

    public function renderFooter() {
        echo CHtml::openTag('div', array('class' => 'modal-footer'));
        $this->widget('bootstrap.widgets.TbButton', array(
            'type' => 'primary',
            'label' => '关闭',
            'htmlOptions' => array(
                'data-dismiss' => 'modal'
            ),
        ));
        echo CHtml::closeTag('div');
    }

    /**
     * 注册JS
     */
    protected function getRegisterScripts() {
        $li = "#" . $this->_iconId . "_items li";
        $icon = '#' . $this->_iconId;
        $model = $icon . '_modal';
        $content = $icon . '_content';
        $js[] = "jQuery('$li').live('click', function(){
                        var helpId = $(this).attr('keydata');
                        $('$li').removeClass('active');
                        $(this).addClass('active');
                        $('$content'  +' .jspPane > div').hide();
                        $('$content' + '_' + helpId).show();
                    })";
        $js[] = "$('$icon').live('click',function(){
                        $('$model').modal('show');
                        $('$content').niceScroll({cursorcolor:'#ccc'});
                    })";
        return $js;
    }

}

?>

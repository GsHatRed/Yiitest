<?php

/**
 * 提示框.
 *
 * @author zmm
 */
Yii::import('core.widgets.TWidget');

/**
 * TMessageBox组件包装类.
 *
 * <pre>
 * $this->widget('core.widgets.TMessageBox', array(
 *   'icon' => 'icon-marker-2',
 *   'type' => 'warning',
 *   'title' => '友情提示',
 *   'content' => '通过自定义字段，用户可对软件的部份功能进行定制，自定义字段模块可以到“系统模块管理”模块添加。',
 *   'htmlOptions' => array(
 *      'id'=>'box',
 *   )
 *  ));
 * </pre>
 *
 */
class TMessageBox extends TWidget {
    // Box types.

    const TYPE_PRIMARY = 'primary';
    const TYPE_SUCCESS = 'success';
    const TYPE_INFO = 'info';
    const TYPE_WARNING = 'warning';
    const TYPE_DANGER = 'danger';

    /**
     * @var array HTML属性选项
     */
    public $htmlOptions = array();

    /**
     * @var string 提示小图标
     */
    public $icon;

    /**
     * @var string 提示背景
     */
    public $type;

    /**
     * @var string 提示标题
     */
    public $title;

    /**
     * @var string 提示内容
     */
    public $content;

    /**
     * @var string 底部提示
     */
    public $footer;
    
    /**
     * @var string 默认样式
     */
    public $defaultCss = 'td-messagebox';

    /**
     * ### .init()
     *
     * Initializes the widget.
     */
    public function init() {

        $classes = array();
        
        $classes[] = $this->defaultCss;
        $classes[] = 'panel';
        
        $validTypes = array(self::TYPE_PRIMARY, self::TYPE_SUCCESS, self::TYPE_INFO, self::TYPE_WARNING, self::TYPE_DANGER);

        if (!isset($this->type)) {
            $this->type = self::TYPE_INFO;
        }
        if (isset($this->type) && in_array($this->type, $validTypes))
            $classes[] = 'panel-' . $this->type;
        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (isset($this->htmlOptions['class']))
                $this->htmlOptions['class'] .= ' ' . $classes;
            else
                $this->htmlOptions['class'] = $classes;
        }

        if (!isset($this->title)) {
            $this->title = '温馨提示';
        }
        if (!isset($this->footer)) {
            $this->footer = '';
        }
        if (isset($this->icon)) {
            if (strpos($this->icon, 'icon') === false)
                $this->icon = 'icon-' . implode(' icon-', explode(' ', $this->icon));
            $this->title = '<i class="' . $this->icon . '"></i> ' . $this->title;
        }else {
            $this->title = '<i class="icon-info"></i> ' . $this->title;
        }
        if ($this->htmlOptions['id'] == null)
            $this->htmlOptions['id'] = $this->getId();
        parent::init();
    }

    /**
     * ### .run()
     *
     * Runs the widget.
     */
    public function run() {


        echo CHtml::openTag('div', $this->htmlOptions);
        $this->renderHeader();
        $this->renderContent();
        if ($this->footer != '') {
            $this->renderFooter();
        }
        echo CHtml::closeTag('div');
    }

    public function renderHeader() {
        echo CHtml::openTag('div', array('class' => 'panel-heading'));
        echo CHtml::openTag('h3', array('class' => 'panel-title'));
        echo $this->title;
        echo CHtml::closeTag('h3');
        echo CHtml::closeTag('div');
    }

    public function renderContent() {
        echo CHtml::openTag('div', array('class' => 'panel-body'));
        echo $this->content;
        echo CHtml::closeTag('div');
    }

    public function renderFooter() {
        echo CHtml::openTag('div', array('class' => 'panel-footer'));
        echo $this->footer;
        echo CHtml::closeTag('div');
    }

}

?>
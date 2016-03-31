<?php

/**
 * 视图选择按钮
 *
 */
Yii::import('core.widgets.TWidget');

class TToolkits extends TWidget {
    /**
     *
     * @var array 要显示的的toolkits 
     */
    public $include = array();
    
    /**
     *
     * @var array 排除显示的toolkits
     */
    public $exclude = array();
    
    public $htmlOptions = array();
    
    public $placement = 'right';
    
    public $enablePopover = true;
    
    public function init() {
        $this->htmlOptions['class'] = 'td-toolkits '.$this->htmlOptions['class'];
        Yii::app()->clientScript->registerScript('','jQuery("[rel=popover]").popover({"trigger":"hover"})');
    }
    
    public function run() {
        echo CHtml::openTag('ul', $this->htmlOptions);
        $toolkits = Toolkits::model()->findAll(TUtil::qc('is_active').'=1');
        if ($toolkits !== NULL) {
            foreach ($toolkits as $toolkit) {
                echo CHtml::tag('li', array(), CHtml::link(
                        CHtml::tag("span", array("class" => $toolkit->icon), '') . $toolkit->name,
                        Yii::app()->createUrl('/portal/resources/toolkit', array('fileName' => $toolkit->file_name)), 
                        array(
                            'data-title' => $toolkit->name,
                            'data-content' => is_resource($toolkit->description) ? stream_get_contents($toolkit->description):$toolkit->description,//oracle 下CLOB兼容处理
                            'data-placement' => $this->placement,
                            'rel' => $this->enablePopover ? 'popover' : '',
                            'data-html'=>true,
                        )
                ));
            }
        }
        echo CHtml::closeTag('ul');
    }
}
?>

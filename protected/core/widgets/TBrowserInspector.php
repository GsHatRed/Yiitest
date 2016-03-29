<?php

/**
 * 浏览器检查组件
 *
 * @author lx <lx@tongda2000.com>
 */
Yii::import('core.widgets.TWidget');

/**
 * 
 */
class TBrowserInspector extends TWidget {

    /**
     *
     * @var array 检查条件
     */
    public $condition = array('msie'=>array('6.0','7.0'));  //检查ie6、ie7
    public $cssFile = array('browser.css');
    public $browsers = array('ie'=>'IE9/IE10', 'chrome'=>'Chrome', 'firefox'=>'Firefox', 'safari'=>'Safari', 'opera'=>'Opera');
    public $title = '您的浏览器已经过时';
    public $description = '建议您更换下列现代浏览器，以获得更好的显示效果！';

    public function init(){
        parent::init();
        
        $this->htmlOptions = array_merge($this->htmlOptions, array('data-width' => 600,'data-height'=>200));
    }
    
    public function run() {
        if(empty($this->condition))
            return;
        
        $this->beginWidget('bootstrap.widgets.TbModal', array(
            'id' => $this->id,
            'htmlOptions' => $this->htmlOptions,
                )
        );
        $attachments = array();
        $toolkits = Toolkits::model()->findAll();
        foreach($toolkits as $tool){
            if(array_key_exists($tool->sys_name, $this->browsers)){
                $attachments[$tool->sys_name] = Yii::app()->createUrl('/portal/resources/toolkit', array('fileName' => $tool->file_name));
            }
        }
        $browserList = '';
        foreach($this->browsers as $key => $val){
            if(array_key_exists($key, $attachments)){
                $browserList .= CHtml::tag('li', array('class'=>$key), CHtml::link(
                        $val,
                        $attachments[$key]
                ));
            } else {
                $browserList .= '<li class="'.$key.'">'.$val.'</li>';
            }
        }
        echo <<<EOD
                <div class="modal-header">
                    <h4>$this->title</h4> 
                </div>
                <div class="modal-body">
                    <p style="font-size:14px;">$this->description</p>
                    <ul id="browser-list">
                        $browserList
                    </ul>
                </div>
                <div class="modal-footer" style="text-align: center;">
EOD;
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'button',
            'type' => 'danger',
            'label' => '我知道了',
            'htmlOptions' => array("data-dismiss" => "modal"),
        ));
        echo '</div>';
        $this->endWidget();
        
        $jsConditionArray = array();
        $patterns = '';
        foreach ($this->condition as $browser=>$version) {
            $condition = "(\$.browser.{$browser} && (";
            foreach ((array)$version as $versionStr) {
                $condition .= "\$.browser.version == '{$versionStr}' || ";
            }
            $condition .= " 1==0 ))";
            $jsConditionArray[] = $condition;
        }
        $jsCondition = implode('||', $jsConditionArray);
        $js = <<<EOD
            //浏览器版本检测
            if ({$jsCondition}) {
                \$('#{$this->id}').modal('show');
            }
EOD;
        Yii::app()->clientScript->registerScript(__CLASS__ . '#' . $this->id, $js);
    }

}

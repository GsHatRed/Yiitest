<?php
/**
 * TTagCloud class file.
 *
 */

Yii::import('core.widgets.TWidget');
/**
 * 标签云
 */
class TTagCloud extends TWidget {
    
    public $data;
    
    public $linkOptions;
    
    public $width=400;
    
    public $height=300;
    
    protected $htmlTags;


    public function init() {
        
        $this->scriptFile = '//swfobject.js';
        
        if(!is_array($this->data)){
            throw new CException('数据错误!');
        }
        
        $this->htmlTags = '';
        foreach($this->data as $item){
            $this->linkOptions['tag'] = $item;
            $this->htmlTags .= CHtml::link($item, $this->linkOptions, array('style'=>'font-size:'.rand(10, 14).'px;'));
        }
        
        parent::init();
    }
    
    public function run() {
        $id = $this->id;
        $objUrl = Yii::app()->core->staticUrl."/activex/tagcloud.swf";
        echo CHtml::tag('div', array('id'=>$id), '');
        echo '<script type="text/javascript">
            var rnumber = Math.floor(Math.random()*9999999);
            var cloud = new SWFObject("'.$objUrl.'?r="+rnumber, "tagcloudflash", "'.$this->width.'", "'.$this->height.'", "9", "#000");
            cloud.addParam("wmode", "transparent");
            cloud.addParam("allowScriptAccess", "always");
            cloud.addVariable("tspeed", "100");
            cloud.addVariable("mode", "tags");
            cloud.addVariable("tagcloud", \'<tags>'.urlencode($this->htmlTags).'</tags>\');
            cloud.write("'.$id.'");
            </script>';
    }

}

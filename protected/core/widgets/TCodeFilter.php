<?php

/**
 * TCodeFilter class file.
 *
 */
Yii::import('core.widgets.TWidget');

class TCodeFilter extends TWidget {
    public $type = null;
    public $name = null;
    public $scriptFile = 'code_filter.js';
    public $events = array();
    public $default = array('All'=>'全部类型');
    public $defaultOptions = array('All'=>array('data-icon'=>'icon-category'));
    
    private $_curPath;

    private function getCodeFilterOptions() {
        $ret = array();
        if($this->type=="NOTIFICATION") {
            $codeItem = array();
            $codeData = SysRemind::model()->findAll();
           foreach ($codeData as $codeItem) {    
               $ret[$codeItem['code']]['data-url'] = Yii::app()->createUrl($this->_curPath, array($this->name => $codeItem['code']));  
           }
        } else{          
            $codeData = SysCode::getCodeData($this->type);
          foreach ($codeData as $codeItem) {
            if($codeItem['code_icon'] != '')
                $ret[$codeItem['id']]['data-icon'] = $codeItem['code_icon'];        
                $ret[$codeItem['id']]['data-url'] = Yii::app()->createUrl($this->_curPath, array($this->name => $codeItem['id']));             
        }
        }
        if(is_array($this->defaultOptions) && sizeof($this->defaultOptions) > 0)
            $ret = CMap::mergeArray($this->defaultOptions, $ret);
        return $ret; 
    }
    public function init() {
        if($this->type == null)
            throw new CException('系统代码类型必须设置');
        if($this->name == null)
            throw new CException('名称必须设置');
        
        $this->_curPath = Yii::app()->controller->module->getId()
                        . '/' . Yii::app()->controller->getId() 
                        . '/' . Yii::app()->controller->getAction()->getId();
        
        if(!isset($this->defaultOptions['All']['data-url']))
            $this->defaultOptions['All']['data-url'] = Yii::app()->createUrl($this->_curPath);
        
        if(!isset($this->events['change']))
            $this->events['change'] = 'js:CodeFilterUtil.changeUrl';
        if(!isset($this->htmlOptions['style']))
            $this->htmlOptions['style'] = 'width:120px;';
        
        parent::init();
    }
   
    
    public function run() {
        $this->type=="NOTIFICATION" ? $codeData = SysRemind::model()->getAllCode() : $codeData = SysCode::getCodeList($this->type);
        $this->widget('bootstrap.widgets.TbSelect2', array(
            'data' => CMap::mergeArray($this->default, $codeData),
            'name' =>$this->name,
            'val' => $_GET[$this->name],
            'options' => array(
                'minimumResultsForSearch' => -1,
                'formatResult' => 'js:CodeFilterUtil.formatIcon',
                'formatSelection' => 'js:CodeFilterUtil.formatIcon',
                'escapeMarkup' => 'js:CodeFilterUtil.escapeMarkup'
            ),
            'events' => $this->events,
            'htmlOptions' => array(
                'name' => 'change-type',
                'style' => 'width:120px;',
                'options' => $this->getCodeFilterOptions(),
            )
        ));
    }

}

?>

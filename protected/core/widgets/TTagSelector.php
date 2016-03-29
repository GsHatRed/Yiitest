<?php
/**
 * TTagSelector class file.
 * 
 * @author fl <fl@tongda2000.com>
 */

/**
 * 标签选择组件
 */
Yii::import('ext.bootstrap.widgets.TbSelect2');

class TTagSelector extends TbSelect2 {
    
    public $asDropDownList = false;
    
    public $attribute = 'tags';
    
    public $tokenSeparators = array(',', ' ');
    
    public $width = '200px';
    
    public $placeholder;
    
    public function init(){
        parent::init();
        
        $this->options = array(
            'tags' => TagCollect::model()->getTags($this->model->tableName()),
            'width' => $this->width,
            'tokenSeparators' => $this->tokenSeparators,
        );
        
        if($this->placeholder){
            $this->options['placeholder'] = $this->placeholder;
        }
    }
    
}
?>

<?php
/**
 * TComponentFactory class file.
 *
 * @author lx <lx@tongda2000.com>
 */

class TComponentFactory extends CApplicationComponent {
    
    //组件实例
    protected $_component = NULL;
    
    //驱动路径
    public $driverPath = '';
    
    //接口类名称
    public $interfaceClass = '';
    
    //驱动类名称
    public $driverClass = '';
    
    //可配参数
    public $options = array();
    
    //初始化
    public function init() {
        parent::init();
        
        $this->options['class'] = $this->driverPath.'.'.$this->driverClass;
        if(class_exists($this->driverClass)) {
            if(!($this->_component instanceof $this->driverClass)){
                $this->_component = Yii::createComponent($this->options);
            }
        } else {
            throw new CException("[$this->driverClass]该组件尚未实现！");
        }
        
    }
    
    public function __call($name, $arguments) {
        if($this->_component !== NULL && in_array($name, get_class_methods($this->interfaceClass))) {
            return call_user_func_array(array($this->_component, $name), $arguments);
        }                
    }
    
    public function component() {
        if ($this->_component instanceof $this->driverClass) {
            return $this->_component;
        } else {
            throw new CException('组建尚未初始化');
        }
    }
    
}

?>

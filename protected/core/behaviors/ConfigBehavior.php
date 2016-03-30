<?php
/**
 * 配置行为类
 *
 */
class ConfigBehavior extends CBehavior {
    private $count = 0;
    public function events() {
        return array_merge(parent::events(), array(
            'onBeginRequest' => 'beginRequest',
        ));
    }

    public function beginRequest() {
        //设置用户自定义组件
        if(file_exists($this->owner->getBasePath().'/config/config.php')){
            $config = require($this->owner->getBasePath().'/config/config.php');
            if(!empty($config['components'])) {
                $this->owner->setComponents($config['components']);
            }
            if(!empty($config['params'])) {
                $this->owner->setParams($config['params']);
            }
            if(isset($config['defaultController'])){
                $this->owner->defaultController = $config['defaultController'];
            }
        }
        //设置模块
        $modules = array_merge($this->owner->modules, $this->getModules());
        $this->owner->setModules($modules);
    }

    private function getModules() {
        $modules = Yii::app()->cache->get('C_MODULES');
        if ($modules == false) {
            $modules = $this->cacheModules();
        }
        return $modules;
    }

    private function cacheModules() {
        $modules = $this->collectModulesRecursive();
        Yii::app()->cache->set('C_MODULES', $modules);
        return $modules;
    }

    private function collectModulesRecursive($path = NULL) {
        $modules = array();
        if (!isset($path)) {
            $path = $this->owner->basePath ;
        }
        
        foreach (scandir($path) as $moduleName) {
            $this->count++;
            if ($moduleName == "." || $moduleName == "..") {
                continue;
            }
            $configFile = $path . '/' . $moduleName . '/config.php';
            $module = array();
            if (file_exists($configFile)) {
                $module = array_merge($module, require($configFile));
            }

            if (is_dir($path . '/' . $moduleName )) {
                $module['modules'] = $this->collectModulesRecursive($path . '/' . $moduleName );
            }
            $modules[$moduleName] = $module;
        }
        return $modules;
    }

}



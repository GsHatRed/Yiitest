<?php

/**
 * 组件基类
 */

class TWidget extends CWidget {

    /**
     * @var array 核心组件
     */
    public $coreScriptFile = array('jquery');

    /**
     * @var mixed 组件脚本文件
     */
    public $scriptFile;

    /**
     * @var mixed 组件脚本文件
     */
    public $cssFile;

    /**
     * @var array HTML属性选项
     */
    public $htmlOptions = array();

    public function init() {
        $this->registerWidgetScripts();
    }

    /**
     * 注册组件需要的js、css脚本
     */
    public function registerWidgetScripts() {
        //注册组件css
        $cs = Yii::app()->getClientScript();
        if (is_string($this->cssFile)) {
            $cs->registerCssFile(strpos($this->cssFile, '//') === 0 ?
                            Yii::app()->core->staticUrl . '/css/' . substr($this->cssFile, 2) : Yii::app()->core->getAssetsUrl() . '/css/' . $this->cssFile);
        } elseif (is_array($this->cssFile)) {
            foreach ($this->cssFile as $cssFile) {
                $cs->registerCssFile(strpos($cssFile, '//') === 0 ?
                                Yii::app()->core->staticUrl . '/css/' . substr($cssFile, 2) : Yii::app()->core->getAssetsUrl() . '/css/' . $cssFile);
            }
        }

        //注册核心js
        if (is_array($this->coreScriptFile)) {
            foreach ($this->coreScriptFile as $coreScriptFile) {
                $cs->registerCoreScript($coreScriptFile);
            }
        }

        //注册组件js
        if (is_string($this->scriptFile)) {
            $cs->registerScriptFile(strpos($this->scriptFile, '//') === 0 ?
                            Yii::app()->core->staticUrl . '/js/' . substr($this->scriptFile, 2) : Yii::app()->core->getAssetsUrl() . '/js/' . $this->scriptFile);
        } elseif (is_array($this->scriptFile)) {
            foreach ($this->scriptFile as $scriptFile) {
                $cs->registerScriptFile(strpos($scriptFile, '//') === 0 ?
                                Yii::app()->core->staticUrl . '/js/' . substr($scriptFile, 2) : Yii::app()->core->getAssetsUrl() . '/js/' . $scriptFile);
            }
        }
    }

}

?>

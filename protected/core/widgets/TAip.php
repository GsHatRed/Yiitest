<?php

Yii::import('core.widgets.TWidget');

/**
 * AIP控件封装类
 *
 */
class TAip extends TWidget {
    const AIP_TRIAL_NAME = 'HWPostil_trial.cab';
    const AIP_TRIAL_VERSION = '3.1.2.2';
    const AIP_TRIAL_CLSID = 'clsid:FF3FE7A0-0578-4FEE-A54E-FB21B277D567';
    const AIP_FORMAL_NAME = 'HWPostil.cab';
    const AIP_FORMAL_VERSION = '3.1.1.8';
    const AIP_FORMAL_CLSID = 'clsid:FF1FE7A0-0578-4FEE-A34E-FB21B277D561';
    public $scriptFile = 'aip.js';
    public $coreScriptFile = array('cookie');

    /**
     *
     * @var string 容器
     */
    public $containerId = '';

    /**
     *
     * @var string 自定义加载完成事件
     */
    public $onCtrlReady = "";

    /**
     *
     * @var string 自定义打开完毕事件
     */
    public $onDocOpened = "";

    /**
     *
     * @var array 参数
     */
    public $params = array();
    
    /**
     *
     * @var type AIP控件名称
     */
    private $_aipName = '';

    public function init() {
        if(Yii::app()->storage->isFileExists(Yii::getPathOfAlias('webroot').Yii::app()->core->staticUrl . "/activex/".self::AIP_FORMAL_NAME)) {
            $this->params['codebase'] = Yii::app()->core->staticUrl . "/activex/".self::AIP_FORMAL_NAME;
            $this->params['version'] = self::AIP_FORMAL_VERSION;
            $this->params['clsid'] = self::AIP_FORMAL_CLSID;
        } else {
            $this->params['codebase'] = Yii::app()->core->staticUrl . "/activex/".self::AIP_TRIAL_NAME;
            $this->params['version'] = self::AIP_TRIAL_VERSION;
            $this->params['clsid'] = self::AIP_TRIAL_CLSID;
        }
        
        if(isset(Yii::app()->params['aip']['params']) && is_array(Yii::app()->params['aip']['params'])){
            $this->params = array_merge(Yii::app()->params['aip']['params'], $this->params);
        }
            
        if (!isset($this->params['id'])) {
            $this->params['id'] = 0;
        }
        if (!isset($this->params['prefix'])) {
            $this->params['prefix'] = "AIP_";
        }
        $this->_aipName = $this->params['prefix'] . $this->params['id'];

        if ($this->onCtrlReady != "") {
            $this->onCtrlReady = CJavaScript::encode($this->onCtrlReady);
        }
        if ($this->onDocOpened != "") {
            $this->onDocOpened = CJavaScript::encode($this->onDocOpened);
        }

        parent::init();
    }

    public function run() {
        if (!$this->containerId) {
            $this->containerId = $this->getId();
            echo CHtml::tag('div', array('id' => $this->containerId), '');
        }
        $js = "aip_{$this->params['id']} = new AIP('{$this->containerId}',".CJSON::encode($this->params).");";
        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $this->id, $js, CClientScript::POS_END);

        echo <<<EOD
            <script language="javascript" for="{$this->_aipName}" event="NotifyCtrlReady">
                aip_{$this->params['id']}.OnCtrlReady();
                {$this->onCtrlReady}
            </script>
            <script language=javascript for="{$this->_aipName}" event=NotifyDocOpened>
                aip_{$this->params['id']}.OnDocOpened();
                {$this->onDocOpened}
            </script>
EOD;
    }

    public function getObj() {
        return 'aip_'.$this->params['id'];
    }


}

?>
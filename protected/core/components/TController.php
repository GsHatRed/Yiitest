<?php

class TController extends CController {
    public $layout = '//layouts/page';
    public $title;
    private $_pageTitle;
    public $layoutParams = array();
    public $widgets = array();
    public $accessCheck = true;
    public $noAccessCheckActions = array();
    //禁用YiiDebugToolbar的actions
    public $disableYiiDebugToolbarActions = array();
    
    //调试模式下ajax返回干净的json
    public function beforeAction($action) {
        parent::beforeAction($action);
        if(YII_DEBUG && (Yii::app()->request->isAjaxRequest || in_array($action->getId(), $this->disableYiiDebugToolbarActions))) {
            foreach (Yii::app()->log->routes as $route) { 
                if ($route instanceof YiiDebugToolbarRoute) {
                    $route->enabled = false;
                    break;
                }                 
            }
        }
        return true;
    }

    public function filters() {
        return array(
            array('sys.modules.auth.filters.AuthFilter', 'noAccessCheck' => !$this->accessCheck, 'noAccessCheckActions'=>$this->noAccessCheckActions),
            array('ext.bootstrap.filters.BootstrapFilter')
        );
    }

    public function init() {
        parent::init();
        //登陆的语言cookie
        $multiLanguage = SysParams::getParams('multi_language');
        if(intval($multiLanguage) == 1) {
            $cookie=  Yii::app()->request->getCookies();
            $language=isset($cookie['language']->value) ? $cookie['language']->value : Yii::app()->language;
            Yii::app()->setLanguage($language);
        }
    }
    /**
     * @return string the page title. Defaults to the controller name and the action name.
     */
    public function getPageTitle() {
        if($this->_pageTitle) {
            return $this->_pageTitle;
        } else {
            $browserTitle = SysParams::getParams('browser_title') ;
            return $this->_pageTitle = ($browserTitle ? $browserTitle : Yii::app()->name) . (isset($this->title) ? '-'.$this->title : '');
        }
    }    
    /**
     * @param string $value the page title.
     */
    public function setPageTitle($value) {
            $this->_pageTitle=$value;
    }
}
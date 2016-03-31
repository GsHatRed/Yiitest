<?php

/**
 * 视图选择按钮
 *
 */
Yii::import('core.widgets.TWidget');

class TViewSwitch extends TWidget {

    const VIEW_GRID = 'grid';
    const VIEW_KANBAN = 'kanban';
    const VIEW_SUMMARY = 'summary';
    const VIEW_DETAIL = 'detail';
    const ICON_UNKNOWN = 'icon-question';
    const ICON_GRID = 'icon-menu-3';
    const ICON_KANBAN = 'icon-grid-5';
    const ICON_SUMMARY = 'icon-list';
    const LABEL_GRID = '列表视图';
    const LABEL_KANBAN = '看板视图';
    const LABEL_SUMMARY = '摘要视图';
    const LABEL_UNKNOWN = '未知视图';

    /**
     * 按钮列表HTML属性
     * @var array 
     */
    public $htmlOptions;

    /**
     * 视图配置，例如：
     * array(
     *      'grid'=>array('label'=>'列表视图','url'=>'xxxxx'),
     *      'kanban'=>array('url'=>'xxxx')
     * )
     * @var string
     */
    public $views;

    /**
     * 当前视图按钮ID
     * @var string
     * 
     * options: kanban, list
     */
    public $activeView;

    /**
     * 是否显示为下拉
     * @var bool 
     */
    public $dropdown = false;

    private function _getViewLabel($viewName) {
        switch ($viewName) {
            case 'grid':
                return self::LABEL_GRID;
            case 'kanban':
                return self::LABEL_KANBAN;
            case 'summary':
                return self::LABEL_SUMMARY;
            default:
                return self::LABEL_UNKNOWN;
        }
    }

    private function _getViewIcon($viewName) {
        switch ($viewName) {
            case 'grid':
                return self::ICON_GRID;
            case 'kanban':
                return self::ICON_KANBAN;
            case 'summary':
                return self::ICON_SUMMARY;
            default:
                return self::ICON_UNKNOWN;
        }
    }

    /**
     * init
     */
    public function init() {
        if (!is_array($this->views))
            throw new CException('请设置视图类型');

        foreach ($this->views as $key => &$view) {
            $allowViews = array(self::VIEW_GRID, self::VIEW_KANBAN, self::VIEW_SUMMARY, self::VIEW_DETAIL);
            if (is_string($view)) {
                $view = array('name' => $view);
            }
            if (!in_array($view['name'], $allowViews)) {
                unset($view);
                continue;
            }
            if ($view['label'] == '')
                $view['label'] = $this->_getViewLabel($view['name']);
            if ($view['icon'] == '')
                $view['icon'] = $this->_getViewIcon($view['name']);
            if ($view['url'] == '') {
                $view['url'] = Yii::app()->createUrl(TUtil::getCurrentRoute(), array('viewType' => $view['name']));
            }
        }
    }
    
    public function run() {
        if($this->dropdown == true) {
            $this->renderDropdown ();
        } else {
            $this->renderRadio();
        }
    }
    
    public function renderRadio() {
        $buttons = array();
        foreach ($this->views as $view) {
            $buttons[] = array(
                'active' => $view['name'] == $this->activeView,
                'icon' => $view['icon'],
                'url' => $view['url'],
                'htmlOptions' => array(
                    'rel' => 'tooltip',
                    'data-original-title' => $view['title'],
                    'data-placement' => 'bottom',
                )
            );
        }
        $this->widget('bootstrap.widgets.TbButtonGroup', array(
            'toggle' => 'radio',
            'buttons' => $buttons,
            'htmlOptions' => $this->htmlOptions,
        ));
    }

    public function renderDropdown() {        
        $items = array();
        foreach ($this->views as $view) {
            if ($view['name'] == $this->activeView) {
                continue;
            }
            $items[] = array(
                'label' => $view['label'],
                'icon' => $view['icon'],
                'url' => $view['url'],
            );
        }

        $buttons = array(
            array(
                'icon' => $this->_getViewIcon($this->activeView),
                'label' => $this->_getViewLabel($this->activeView),
                'items' => $items
            )
        );
        $this->widget('bootstrap.widgets.TbButtonGroup', array(
            'buttons' => $buttons,
            'htmlOptions' => $this->htmlOptions,
        ));
    }

}
?>


<?php

/**
 * 看板组件类
 * 
 * @author zmm <zmm@tongda2000.com>
 */
Yii::import('zii.widgets.CListView');

class TbKanbanListView extends CListView {

    /**
     * @var string 主标签 
     */
    public $tagName = 'div';

    /**
     * @var CActiveDataProvider  
     */
    public $dataProvider;

    /**
     * @var array html属性
     */
    public $htmlOptions = array();

    /**
     * @var 看板元素
     */
    public $card;

    /**
     * @var string 空内容提示 
     */
    public $emptyText;

    /**
     * @var string 成功执行AJAX更新请求后调用JS方法
     */
    public $afterAjaxUpdate;

    /**
     * @var string 更新选择器
     */
    public $updateSelector = '{page}';

    /**
     * @var boolean 使用翻页功能 
     */
    public $enablePagination = true;

    /**
     * @var boolean 使用翻页功能 
     */
    public $enableToolbar = true;

    /**
     * @var array 翻页组件
     */
    public $pager = array('class' => 'bootstrap.widgets.TbPager');

    /**
     * @var array 工具栏组件
     */
    public $toolbar = array();

    /**
     * @var string 页面内容模版
     */
    public $template = "{toolbar}\n{kanban}\n{pager}";

    /**
     * @var string Loading图标 
     */
    public $loadingCssClass = 'kanban-view-loading';

    /**
     * @var string Pager样式 
     */
    public $pagerCssClass = 'pagination';

    /**
     * @var string Toolbar样式 
     */
    public $toolbarCssClass = 'toolbar';

    /**
     * 动态更新
     * @var string  
     */
    public $ajaxUpdate;

    /**
     * @var boolean  是否启用checkbox选择框
     */
    public $checkbox = false;

    /**
     * ### .init()
     *
     * Initializes the widget.
     */
    public function init() {
        if ($this->dataProvider === null)
            throw new CException(Yii::t('zii', 'The property "dataProvider" cannot be empty.'));
        if ($this->itemView === null)
            throw new CException(Yii::t('zii', 'The property "itemView" cannot be empty.'));
        $this->dataProvider->getData();
        $this->htmlOptions['id'] = $this->getId();
        if (!isset($this->htmlOptions['class']))
            $this->htmlOptions['class'] = 'kanban';
        $afterAjaxUpdate = "js:function() {
			jQuery('.popover').remove();
			jQuery('{$popover}').popover();
			jQuery('.tooltip').remove();
			jQuery('{$tooltip}').tooltip();
		}";
        if (is_string($this->pagerCssClass) && ($this->pagerCssClass == "td-pager")) {
            $this->pagerCssClass = 'pagination td-pager';
            $this->pager['class'] = 'core.widgets.TPager';
        }

        if (!isset($this->afterAjaxUpdate))
            $this->afterAjaxUpdate = $afterAjaxUpdate;
        $this->itemsCssClass .= ' clearfix';
    }

    public function renderView($n) {

        echo CHtml::openTag($this->itemsTagName, array('class' => $this->itemsCssClass)) . "\n";
        $dataProvider = $this->dataProvider->getData();
        $owner = $this->getOwner();
        $viewFile = $owner->getViewFile($this->itemView);
        $data = $this->viewData;
        $data['index'] = $n;
        $data['data'] = $dataProvider[$n];
        $data['widget'] = $this;
        $owner->renderFile($viewFile, $data);
        echo CHtml::closeTag($this->itemsTagName);
    }

    public function run() {

        $this->registerCss();
        $this->registerClientScript();

        echo CHtml::openTag($this->tagName, $this->htmlOptions) . "\n";

        $this->renderContent();
        $this->renderKeys();

        echo CHtml::closeTag($this->tagName) . "\n";
    }

    /**
     * 渲染看板内容
     */
    public function renderContent() {
        ob_start();
        echo preg_replace_callback("/{(\w+)}/", array($this, 'renderSection'), $this->template);
        ob_end_flush();
    }

    /**
     * 渲染内容片段
     * @param string $matches
     * @return string
     */
    protected function renderSection($matches) {
        $method = 'render' . $matches[1];
        if (method_exists($this, $method)) {
            $this->$method();
            $html = ob_get_contents();
            ob_clean();
            return $html;
        } else
            return $matches[0];
    }

    /**
     * Renders the pager.
     */
    public function renderPager() {
        if (!$this->enablePagination)
            return;

        $pager = array();
        $class = 'CLinkPager';
        if (is_string($this->pager))
            $class = $this->pager;
        elseif (is_array($this->pager)) {
            $pager = $this->pager;
            if (isset($pager['class'])) {
                $class = $pager['class'];
                unset($pager['class']);
            }
        }
        $pager['pages'] = $this->dataProvider->getPagination();

        if ($pager['pages']->getPageCount() > 1) {
            echo '<div class="' . $this->pagerCssClass . '">';
            $this->widget($class, $pager);
            echo '</div>';
        } else
            $this->widget($class, $pager);
    }

    public function renderKanban() {
        $data = $this->dataProvider->getData();
        $n = count($data);
        echo "<ul class=\"kanban-ul\">\n";

        if ($n > 0) {
            for ($i = 0; $i < $n; ++$i)
                $this->renderCard($i);
        } else {
            $this->renderEmptyText();
        }
        echo "</ul>\n";
        echo "<div class=\"clearfix\"></div>\n";
    }

    /**
     * Renders the toolbar.
     */
    public function renderToolbar() {
        $toolbar = array();
        if(!is_array($this->toolbar)) {
            return;
        }else{
            $toolbar = $this->toolbar;
        }
        $TbButtonGroup = 'bootstrap.widgets.TbButtonGroup';
        $TbButton = 'bootstrap.widgets.TbButton';
        $count = count($toolbar);
        if ($count > 0) {
        echo '<div class="' . $this->toolbarCssClass . '">';
        foreach ($this->toolbar as $item) {
            if($item['visible'] !== false){
                unset($item['visible']);
                if(!isset($item['class'])) {
                    if (isset($item['buttons'])) {
                        $this->widget($TbButtonGroup, $item);
                    } else {
                        $this->widget($TbButton, $item);
                    }
                } else {
                    $class = $item['class'];
                    unset($item['class']);
                    $this->widget($class, $item);
                }
            }
        }
        echo '</div>';
        }
    }

    public function renderCard($n) {
        echo CHtml::openTag('li') . "\n";
        $this->renderView($n);
        echo "</li>\n";
    }

    public function renderKeys() {
        echo CHtml::openTag('div', array(
            'class' => 'keys',
            'style' => 'display:none',
            'title' => Yii::app()->getRequest()->getUrl(),
        ));

        foreach ($this->dataProvider->getKeys() as $key)
            echo "<span>" . CHtml::encode($key) . "</span>";
        echo "</div>\n";
    }

//
    public function registerClientScript() {
        $id = $this->getId();

        if ($this->ajaxUpdate === false)
            $ajaxUpdate = false;
        else
            $ajaxUpdate = array_unique(preg_split('/\s*,\s*/', $this->ajaxUpdate . ',' . $id, -1, PREG_SPLIT_NO_EMPTY));

        $options = array(
            'ajaxUpdate' => $ajaxUpdate,
            'pagerClass' => $this->pagerCssClass,
            'updateSelector' => $this->updateSelector,
            'loadingClass' => $this->loadingCssClass,
            'checkbox' => $this->checkbox,
        );
        if ($this->enablePagination)
            $options['pageVar'] = $this->dataProvider->getPagination()->pageVar;

        $options = CJavaScript::encode($options);
        $cs = Yii::app()->getClientScript();
        Yii::app()->bootstrap->registerAssetCss('jquery.kanbanview.css');
        Yii::app()->bootstrap->registerAssetJs('jquery.kanbanlistview.js');
        $cs->registerScript(__CLASS__ . $this->id, "jQuery('#$id').kanbanView($options)");
    }

    public function renderEmptyText() {
        $emptyText = $this->emptyText === null ? Yii::t('zii', 'No results found.') : $this->emptyText;
        $this->widget('core.widgets.TMessageBox', array(
            'icon' => 'icon-bubble-6',
            'type' => 'success',
            'title' => '温馨提示',
            'content' => $emptyText,
        ));
    }

    public function registerCss() {
         Yii::app()->bootstrap->registerAssetCss('jquery.kanbanview.css');
    }

}

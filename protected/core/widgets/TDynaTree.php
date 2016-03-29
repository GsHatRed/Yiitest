<?php
Yii::import('core.widgets.TWidget');
/**
 * TDynaTree class file.
 *
 * @author Applee
 */
class TDynatree extends TWidget {

    public $scriptFile = array('jquery.dynatree.min.js', 'jquery.dynatree.helper.js');
    public $cssFile = array('jquery.dynatree.css');
    
    public $treeId;

    /**
     * @var array 请求数据url
     */
    public $jsonUrl = '';

    /**
     * @var array 初始化数据
     */
    public $children = 'null';
    public $imagePath = '';

    /**
     * @var integer 最小展开级次
     */
    public $minExpandLevel = 2;
    public $persist = 'false';
    public $checkbox = 'false';

    /**
     * @var array 选中模式
     */
    public $selectMode = 3;
    public $events = array('onPostInit', 'onLazyRead', 'onActivate', 'onBlur', 'onDblClick', 'onSelect', 'onSelect', 'onRender', 'onCreate');

    /**
     * @var array 附加时间处理函数
     */
    public $eventHandler = array();
    public $htmlOptions = array();

    public function init() {
        parent::init();
        if (!isset($this->treeId))
            $this->treeId = $this->getId();

        echo CHtml::tag('div', array('id' => $this->treeId), '');
        
        if ($this->imagePath) {
            
        }
        else
            $this->imagePath = Yii::app()->td->getAssetsUrl().'/img/dynatree/org/';

        foreach ($this->events as $event) {
            if (!isset($this->eventHandler[$event])) {
                $this->eventHandler[$event] = 'null';
            }
        }
    }

    public function run() {

        $JS = <<<EOT
jQuery('#{$this->treeId}').dynatree({
    minExpandLevel: {$this->minExpandLevel}, // 1: root node is not collapsible
    imagePath: "{$this->imagePath}", // Path to a folder containing icons. Defaults to 'skin/' subdirectory.
    children: {$this->children}, // Init tree structure from this object array.
    initId: null, // Init tree structure from a <ul> element with this ID.
    initAjax: {url:"{$this->jsonUrl}"}, // Ajax options used to initialize the tree strucuture.
    autoFocus: true, // Set focus to first child, when expanding or lazy-loading.
    keyboard: true, // Support keyboard navigation.
    persist: {$this->persist}, // Persist expand-status to a cookie
    autoCollapse: false, // Automatically collapse all siblings, when a node is expanded.
    clickFolderMode: 3, // 1:activate, 2:expand, 3:activate and expand
    activeVisible: true, // Make sure, active nodes are visible (expanded).
    checkbox: false, // Show checkboxes.
    selectMode: {$this->selectMode}, // 1:single, 2:multi, 3:multi-hier
    fx: { height: "toggle", duration: 200 }, // Animations, e.g. null or { height: "toggle", duration: 200 }
    noLink: false, // Use <span> instead of <a> tags for all nodes
    onPostInit: function(isReloading, isError){
        if(typeof(tree_loaded) == 'function')
            tree_loaded(this, isReloading, isError);
    },
    onLazyRead: function(node){
        if(typeof(this.options.customOptions) == "object" && typeof(this.options.customOptions.LazyReadAPI) == "function")
        {
            node.addChild(this.options.customOptions.LazyReadAPI({"node":node, "flag":this.options.customOptions.paras.flag}));
            node.setLazyNodeStatus(DTNodeStatus_Ok);
        }
        else if(node.data.json)
        {
            node.appendAjax({
                url: node.data.json
            });
        }
        else
        {
            node.setLazyNodeStatus(DTNodeStatus_Ok);
        }
    },
    onActivate: function(node){
        if(typeof(tree_click) == 'function')
            tree_click(node);
        node = null;
    },
    onBlur: function(node) {
       node.deactivate();
       node = null;
    },
    onDblClick: function(node){
       if(typeof(tree_dblclick) == 'function')
          tree_dblclick(node);
       node = null;
    },
    onSelect: function(select, node){
       if(typeof(tree_select) == 'function')
          tree_select(select, node);
       node = null;
    },
    onRender: function(node, nodeSpan){
       if(typeof(tree_render) == 'function')
          tree_render(node, nodeSpan);
       node = null;
       nodeSpan = null;
    },
    onCreate: function(node, nodeSpan){
       if(typeof(tree_create) == 'function')
          tree_create(node, nodeSpan);
       node = null;
       nodeSpan = null;
    },
    strings: {
       loading: '加载中请稍后',
       loadError: '载入失败'
    }
 });
EOT;

        Yii::app()->getClientScript()
                ->registerCoreScript('jquery.ui')
                ->registerCoreScript('jquery.cookie')
                ->registerScript(__CLASS__.'#'. $this->id, $JS);
    }

}

?>

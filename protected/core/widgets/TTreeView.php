<?php
/**
 * TTreeView class file.
 *
 * @copyright (c) 2013, Tongda Tech (http://www.tongda2000.com)
 * @version $id$
 */
Yii::import('core.widgets.TWidget');

/**
 *
 * 基于zTree的包装类
 * @link http://www.ztree.me/v3/api.php
 *
 * 组件使用方法:
 * <pre>
 * $this->widget('core.widgets.TTreeView',array(
 * 		'treeNodeNameKey'=>'name',
 * 		'treeNodeKey'=>'id',
 * 		'treeNodeParentKey'=>'pId',
 * 		'options'=>array(
 *          'view' => array(
 *              'expandSpeed'=>'slow',
 *              'showLine'=>false,
 *          )
 *      ),
 * 		'data'=>array(
 * 			array('id'=>1, 'pId'=>0, 'name'=>'目录1'),
 * 			array('id'=>2, 'pId'=>1, 'name'=>'目录2'),
 * 			array('id'=>3, 'pId'=>1, 'name'=>'目录3'),
 * 			array('id'=>4, 'pId'=>1, 'name'=>'目录4'),
 * 			array('id'=>5, 'pId'=>2, 'name'=>'目录5'),
 * 			array('id'=>6, 'pId'=>3, 'name'=>'目录6')
 * 		)
 * ));
 * </pre>
 *
 * 定义数据的三种方式：
 * 1、设置model属性后(model类名或者model对象)：
 * 		数据获得方式则为$model->model()->findAll($this->criteria)
 * 		例如：
 * 			1、array(
 * 				'model'=>'tree'
 * 			)
 * 			2、array(
 * 				'model'=>$model, //此处为一个model对象(需要是CModel的子类)
 * 			)
 * 2、设置data属性
 * 		数据可以为数组，或者model的数据集(数组形式)
 * 		例如：
 * 			1、array(
 * 				'data'=>array(
 * 					array('id'=>1, 'pId'=>0, 'name'=>'目录1'),
 * 					array('id'=>2, 'pId'=>1, 'name'=>'目录2'),
 * 					array('id'=>3, 'pId'=>1, 'name'=>'目录3'),
 * 					array('id'=>4, 'pId'=>1, 'name'=>'目录4'),
 * 					array('id'=>5, 'pId'=>2, 'name'=>'目录5'),
 * 					array('id'=>6, 'pId'=>3, 'name'=>'目录6')
 * 				)
 * 			)
 *
 * 			2、array(
 * 				'data'=>tree::model()->findAll()
 * 			)
 * 3、设置dataUrl属性
 *      $this->widget('core.widgets.zTree',array(
 *          'dataUrl' => $this->createUrl('/site/test'),
 *      );
 *      url返回json格式数据，具体请参考treeNode格式
 *
 */
class TTreeView extends TWidget {

    public $scriptFile = array('jquery.ztree.all-3.5.min.js','jquery.ztree.exhide-3.5.min.js');
    public $cssFile = array('zTreeStyle.css');

    public $cssFileUser = null;

	public $treeId = null;
    /**
     * 数据
     *
     * @var array|string
     */
    public $data;

    /**
     * 异步加载数据的url
     *
     * @var string
     */
    public $asyncUrl;

    public $ayncParams;

    /**
     * zTree数据的model名称
     * 设置此项，则为$model::model()->findAll($this->criteria)
     * @var string|object
     */
    public $model;

    /**
     * 查询条件
     * 设置model属性后生效
     * @var mixed
     */
    public $criteria;

    /**
     * 树形节点列名键名
     * 默认为name
     * @var string
     */
    public $treeNodeNameKey = 'name';

    /**
     * 树形节点ID键名
     *
     * @var string
     */
    public $treeNodeKey = 'id';

    /**
     * 树形节点父ID键名
     *
     * @var string
     */
    public $treeNodeParentKey = 'pId';

    /**
     * @var array
     */
    public $options = array();

    /**
     * @var array
     */
    public $callback = array();

    public $expandAll = false;

    /**
     * 默认选中
     * @var integer NodeID
     */
    public $selectNode;
    
    /**
     *
     * @var array 右键菜单
     */
    public $contextMenu = array();
    
    public $contextMenuId = null;


    /**
     * 初始化
     * @see CJuiWidget::init()
     */
    public function init() {
        if( isset($this->cssFileUser) && is_array($this->cssFileUser))
        {
            foreach($this->cssFileUser as $cssFile)
            {
                $this->cssFile[] = $cssFile;
            }
        }

        if (!isset($this->treeId))
            $this->treeId = $this->getId();

        $this->options['treeId'] = $this->treeId;
       	$this->htmlOptions['id'] = $this->treeId;


        $this->htmlOptions['class'] = 'ztree ' . $this->htmlOptions['class'];

        if ($this->asyncUrl != null) {
            $this->options['async'] = array(
                'enable' => true,
                'url' => $this->asyncUrl,
                'type' => 'post',
                'autoParam' => 'js:["id", "name"]',
            );
            if(isset($this->ayncParams)){
                $this->options['async'] = array_merge($this->options['async'], $this->ayncParams);
            }
            $this->options['async']['autoParam'] = new CJavaScriptExpression($this->options['async']['autoParam']);
            if($this->options['async']['otherParam'])
                $this->options['async']['otherParam'] = new CJavaScriptExpression($this->options['async']['otherParam']);
        }
        if ($this->model || $this->data) {
            $this->options['data']['simpleData']['enable'] = true;

            if ($this->treeNodeKey !== null) {
                $this->options['data']['simpleData']['treeNodeKey'] = $this->treeNodeKey;
            }
            if ($this->treeNodeParentKey !== null) {
                $this->options['data']['simpleData']['treeNodeParentKey'] = $this->treeNodeParentKey;
            }
        }
        //右键菜单
        if(sizeof($this->contextMenu) > 0) {
            if(!isset($this->contextMenuId)) {
                $this->contextMenuId = $this->treeId.'_contextmenu';
            }
            $userRightClick = '""';
            if(isset($this->callback['onRightClick'])) {
                $userRightClick = new CJavaScriptExpression($this->callback['onRightClick']);
            }elseif(isset($this->options['callback']['onRightClick'])){
                $userRightClick = new CJavaScriptExpression($this->options['callback']['onRightClick']);
            }
            $this->callback['onRightClick'] = 'js:function(event, treeId, treeNode){if(treeNode && typeof(treeNode.noclick) == "undefined"){TTreeUtil.contextMenu("'.$this->contextMenuId.'", '.$userRightClick.', event, treeId, treeNode);}}';
        }
        if(!empty($this->callback)){
            if(is_array($this->options['callback'])){
                $this->options['callback'] = array_merge($this->options['callback'], $this->callback);
            }else{
                $this->options['callback'] = $this->callback;
            }
        }
        parent::init();
    }

    public function run() {
        //树形容器
        echo CHtml::tag('ul', $this->htmlOptions, '');
        //右键菜单
        if(sizeof($this->contextMenu) > 0) {
            echo CHtml::openTag('div', array('id'=>$this->contextMenuId, 'style'=>'position:absolute;'));
            $this->widget('bootstrap.widgets.TbMenu', array(
                'items'=>$this->contextMenu,
                'htmlOptions'=>array('class'=>'dropdown-menu'),
            ));
            echo CHtml::closeTag('div');
        }
        //注册JS
        $cs = Yii::app()->getClientScript();
        $cs->registerScript(__CLASS__ . '#' . $this->treeId, implode("\n", $this->getRegisterScripts()));
    }

    /**
     * 注册JS列表
     *
     */
    protected function getRegisterScripts() {
        $js = array();
        $data = $this->getData();
        $options = CJavaScript::encode($this->options);
        if($data)
            $js[] = "zTree_{$this->treeId} = jQuery.fn.zTree.init(jQuery('#{$this->treeId}'),{$options}, {$data});\r\n";
        else
            $js[] = "zTree_{$this->treeId} = jQuery.fn.zTree.init(jQuery('#{$this->treeId}'),{$options});\r\n";
        if($this->expandAll)
            $js[] = "zTree_{$this->treeId}.expandAll(true);";
        if(isset($this->selectNode)) {
            $js[] = "zTree_{$this->treeId}.selectNode(zTree_{$this->treeId}.getNodeByParam('id', '{$this->selectNode}'))";
        }
        if(isset($this->contextMenuId)) {
            $js[] = '$(document).on("click", function(event){$("#'.$this->contextMenuId.'").css({"visibility" : "hidden"});});';
        }
        return $js;
    }

    /**
     * 获得数据
     *
     */
    protected function getData() {
        if ($this->model !== null) {
            $model = is_object($this->model) ? $this->model : new $this->model;
            if ($model instanceof CModel) {
                $data = $model->findAll($this->criteria);
            }
        } else {
            $data = $this->data;
        }

        if (is_array($data)) {
            if (current($data) instanceof CModel) {
                $arr = array();
                foreach ($data as $key => $value) {
                    $arr[$key] = $value->getAttributes();
                    $arr[$key]['name'] = $arr[$key][$this->treeNodeNameKey];
                    $arr[$key]['id'] = $arr[$key][$this->treeNodeKey];
                    $arr[$key]['pId'] = $arr[$key][$this->treeNodeParentKey];
                }
                $data = $arr;
            }
            $data = CJavaScript::encode($data);
        }

        return $data;
    }

}
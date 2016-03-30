<?php
/**
 * 查询过滤类
 *
 * @author FL
 */

/** 
 * 
 * $this->widget('core.widgets.TSearchFilter', array(
 *     'rows'=>array(
 *         array(            
 *             'header'=>'状态',
 *             'name'=>'status',
 *             'items'=>array(
 *                array(
 *                  'label'=>'未接受',
 *                  'value'=>'unread',
 *                  'active'=>true,  
 *                ),
 *                array(
 *                  'label'=>'办理中',
 *                  'value'=>'read',
 *                ),
 *             ),
 *         )
 *     ),
 * ));
 * </pre>
 */
Yii::import('core.widgets.TWidget');
class TSearchFilter extends TWidget {
    
    /**
     * @var string 标题
     */
    public $title;
    
    /**
     * @var array 组件属性
     */
    public $htmlOptions = array();
    
    /**
     * @var boolean 是否显示查询条件
     */
    private $_show;
    
    /**
     * @var boolean 默认显示
     */
    public $defaultShow = true;
    
    /**
     * @var boolean 是否显示已选条件
     */
    public $showSelected = false;
    
    /**
     * @var string 表格样式类型 
     */
    public $tableType = 'bordered';
    
    /**
     * @var array 表格HTML属性
     */
    public $tableHtmlOptions = array();
    
    /**
     * @var array 筛选条件数组
     */
    public $rows = array();
    
    /**
     * @var array 已选中条件
     */
    private $_selectedCondition = array();
    
    /**
     *
     * @var array 路径参数
     */
    public $urlParams = array();
    
    
    
    public function init() {
        parent::init();
        
        if(!isset($this->htmlOptions['id']))
            $this->htmlOptions['id'] = $this->id;
        
        $classes = array('table');
        if(is_string($this->tableType))
            $this->tableType = explode (' ', $this->tableType);
        foreach($this->tableType as $type){
            $classes[] = 'table-'.$type; 
        }
        if(isset($this->tableHtmlOptions['class'])){
            $this->tableHtmlOptions['class'] .= implode(' ', $classes);
        } else {
            $this->tableHtmlOptions['class'] = implode(' ', $classes);
        }
        
        if(isset($this->tableHtmlOptions['style'])){
            $this->tableHtmlOptions['style'] .= ';margin-bottom: 6px;';
        } else {
            $this->tableHtmlOptions['style'] = 'margin-bottom: 6px;';
        }
        
        $this->registerCss();
        $this->registerJs();
        
        if(!$this->urlParams)
            $this->urlParams = array();
        foreach($this->rows as &$row){
            foreach($row['items'] as $item){
                if($item['active'] == true){
                    $this->_selectedCondition[] = $item;
                    $row['selected'] = true;
                    
                    $this->urlParams[$row['name']] = $item['value'];
                }
            }
        }
        
        if(empty($this->_selectedCondition)){
            $this->_show = $this->defaultShow;
        } else {
            $this->_show = true;
        }
    }
    
    public function run() {
        echo CHtml::openTag('div', $this->htmlOptions)."\n";
        $this->renderTable();
        echo CHtml::closeTag('div')."\n";
    }
    
    public function renderTable(){
        echo CHtml::openTag('table', $this->tableHtmlOptions)."\n";
        $this->renderTableHead();
        $this->renderTableBody();
        echo CHtml::closeTag('table')."\n";
    }
    
    public function renderTableHead(){
        echo '<thead>'."\n";
        echo '<tr>'."\n";
        echo '<th style="width:60px;">'.$this->title.'</th>'."\n";
        echo '<th  style="width:100%;border-left:none;">'."\n";
        if($this->_show)
            echo '<div class="pull-right" id="expandBtn" style="cursor: pointer">展开<i class="icon-arrow-down"></i></div>'."\n";
        else
            echo '<div class="pull-right" id="expandBtn" style="cursor: pointer">收起<i class="icon-arrow-up"></i></div>'."\n";
        echo '</th>'."\n";
        echo '</tr>'."\n";
        echo '</thead>'."\n";
    }
    
    public function renderTableBody(){
        if($this->_show)
            echo '<tbody>'."\n";
        else
            echo '<tbody style="display:none;">'."\n";
        $this->renderSelected();
        $this->renderCondition();
        echo '</tbody>'."\n";
    }
    
    public function renderSelected(){
        if($this->showSelected && !empty($this->_selectedCondition)){
            echo '<tr><td style="text-align:right"><b>已选条件</b></td><td style="border-left:none;">'."\n";
            foreach($this->_selectedCondition as $item){
                echo '<span class="label label-info">'.$item['label'].'</span>'."\n";
            }
            echo '</td></tr>'."\n";
        }
    }
    
    public function renderCondition(){
        echo '<tr>'."\n";
        foreach($this->rows as $row){
            echo '<td style="text-align:right"><b>'.$row['header'].'</b></td>'."\n";
            echo '<td style="border-left:none;">'."\n";
            if($row['selected']){
                $params = $this->urlParams;
                unset($params[$row['name']]);
                echo '<a  title="全部" href="'.Yii::app()->createUrl($this->owner->route, $params).'">全部</a>'."\n";
            } else {
                echo '<span class="label label-info">全部</span>'."\n";
            }
            foreach((array)$row['items'] as $item){
                if($item['active']){
                    echo '<span class="label label-info">'.$item['label'].'</span>'."\n";
                } else {
                    $params = $this->urlParams;
                    $params[$row['name']] = $item['value'];
                    echo '<a title="'.$item['name'].'" href="'.Yii::app()->createUrl($this->owner->route, $params).'">'.$item['label'].'</a>'."\n";
                }
            }
            echo '</td></tr>';
        }
        echo '</tr>'."\n";
    }
    
    public function registerCss(){
        $cs = Yii::app()->getClientScript();
        $cs->registerCss($this->id, "#{$this->id} span, #{$this->id} a{margin-right: 10px;}");
    }
    
    public function registerJs(){
        $cs = Yii::app()->getClientScript();
        $js = <<<EOD
        $('#expandBtn').click(function(){
            if($(this).find('i').hasClass('icon-arrow-down')){
                $(this).html('收起<i class="icon-arrow-up"></i>');
                $('#{$this->id}').find('tbody').hide();
            }else{
                $(this).html('展开<i class="icon-arrow-down"></i>');
                $('#{$this->id}').find('tbody').show();
            }
        });
EOD;
        $cs->registerScript($this->id, $js);
    }
    
}

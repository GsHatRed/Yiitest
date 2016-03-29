<?php

/**
 * TFlotGraph class file.
 *
 */
Yii::import('core.widgets.TWidget');

/**
 * flotcharts组件包装类.
 *
 * @link http://www.flotcharts.org/
 *
 * 用法如下:
 * @link http://www.yiiframework.com/extension/flot
 * //example view for flot graphs
 * <pre>
 * // 在同一条线图
 * $this->widget('core.widgets.TFlotCharts', array(
 *       'data'=>array(
 *           array(
 *               'label'=> 'line',               //图例（表示图中各个类别的具体含义）
 *               'data'=>array(
 *                   array(1,1),
 *                   array(2,7),
 *                   array(3,12),
 *                   array(4,32),
 *                   array(5,62),
 *                   array(6,89),
 *               ),                             //data数组里面的每一个数组代表一个坐标，数组的第一个元素表示横坐标，第二个元素表示纵坐标
 *               'lines'=>array('show'=>true),  //折线图，此时的折线图下面没有颜色，加上'fill'=>true才有
 *               'points'=>array('show'=>true), //设置每一个坐标是否带空心的圆点
 *           ),
 *           array(
 *               'label'=> 'bars',             //图例（表示图中各个类别的具体含义）
 *               'data'=>array(
 *                   array(1,12),
 *                   array(2,16),
 *                   array(3,89),
 *                   array(4,44),
 *                   array(5,38),
 *               ),
 *               'bars'=>array('show'=>true),  //矩阵图
 *           ),
 *       ),
 *       'options'=>array(
 *               'legend'=>array(
 *                    'position'=>'nw',          //设置图例的位置，默认是显示在右边，设置成这个值后显示在左边
 *                    'show'=>true,              //设置图例是否显示，默认是显示
 *                    'margin'=>50,              //设置图例的外边距
 *                    'backgroundOpacity'=> 0.5  //设置图例的透明度，默认背景为白色
 *                   ),
 *       ),
 *    'htmlOptions'=>array(                      //设置其他属性
 *        'style'=>'width:800px;height:300px;',
 *    ),
 * ));
 * </pre>
 *
 */
class TFlotCharts extends TWidget {

    //默认颜色
    private $_defaultColors = array('#88bbc8', '#ed7a53', '#9FC569', '#bbdce3', '#9a3b1b', '#5a8022', '#2c7282');
    
    public $scriptFile = array('flot/excanvas.min.js', 'flot/jquery.flot.min.js', 'flot/jquery.flot.pie.min.js','flot/jquery.flot.canvas.min.js','flot/jquery.flot.categories.min.js');
    public $data = array();
    public $options = array();
    public $htmlOptions = array();
    


    /**
     * @var string 容器tag标签，默认div.
     */
    public $tagName = 'div';
    protected $_assetsUrl;

    public function init() {
        if ($this->htmlOptions['id'] == null)
            $this->htmlOptions['id'] = $this->getId();
           parent::init(); 
        if(!isset($this->options['colors']))
            $this->options['colors'] = $this->_defaultColors;
        
        if(!isset($this->options['tooltip']))
            $this->options['tooltip'] = true;
    }

    public function run() {
        echo CHtml::tag($this->tagName, $this->htmlOptions ,'');
        $flotoptions = !empty($this->options) ? CJavaScript::encode($this->options) : '';
        $flotdata = CJavaScript::encode($this->data);
        $id = $this->htmlOptions['id'];
        $js = "\n$.plot('#{$id}',$flotdata,$flotoptions);";
        Yii::app()->getClientScript()->registerScript(__CLASS__ . '#' . $id, $js);
    }


}
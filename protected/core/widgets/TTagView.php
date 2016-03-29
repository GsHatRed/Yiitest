<?php
/**
 * TTagView class file.
 * 
 * @author fl <fl@tongda2000.com>
 */

Yii::import('core.widgets.TWidget');

/**
 * 标签显示组件
 * 
 * 支持label和link两种显示方式
 */
class TTagView extends TWidget {
    
    const TYPE_LABEL = 'label';
    const TYPE_LINK = 'link';

    /**
     * 显示类型
     * label:标签类型
     * link:文本链接类型
     * @var string 
     */
    public $type='label';
    
    /**
     * tag数据
     * @var string ','号分隔字符 
     */
    public $data;
    
    /**
     * 链接参数
     * 参考 CHTML::normalizeUrl 
     * @var array  
     */
    public $linkOptions;
    
    /**
     * @var array 重要的标签
     */
    public $important;
    
    /**
     * @var string Class类型
     */
    public $labelClass = 'label';
    
    public $delimiter = '、';
    
    protected $tagArray;


    public function init() {
        parent::init();
        
        $this->tagArray = TUtil::texplode($this->data);
    }
    
    public function run(){
        if($this->type == self::TYPE_LABEL)
            $this->renderLabel ();
        elseif($this->type == self::TYPE_LINK)
            $this->renderLink();
        else
            throw new CException('错误的标签类型!');
    }
    
    protected function renderLabel(){
        foreach($this->tagArray as $tag){
            if($this->linkOptions){
                $this->linkOptions['tag'] = $tag;
                $class = $this->labelClass;
                if(!empty($this->important) && in_array($tag, $this->important))
                    $class .= ' label-important';
                echo CHtml::tag('span', array('class'=>$class, 'style'=>"margin-left:10px;"), 
                    CHtml::link($tag, $this->linkOptions, array('style'=>'color:#fff;'))
                );
            } else {
                echo CHtml::tag('span', array('class'=>'label', 'style'=>"margin-left:10px;"), $tag);
            }
        }
    }
    
    protected function renderLink(){
        $i=0;
        $max = count($this->tagArray);
        foreach($this->tagArray as $tag){
            $this->linkOptions['tag'] = $tag;
            echo CHtml::link($tag, $this->linkOptions, array('style'=>'padding:0;', 'rel' => 'tooltip', 'data-original-title' => $tag, 'data-placement' => 'bottom'));
            if($i < $max -1)
                echo $this->delimiter;
            $i++;
        }
    }
    
}
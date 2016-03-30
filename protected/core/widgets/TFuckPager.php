<?php

/**
 * TPager class file.
 *
 * @author GS
 *
 * TPager组件包装类.
 *
 * <pre>
 *   $this->widget('bootstrap.widgets.TbGroupGridView', array(
 *        'pagerCssClass' => 'fuck-pager',
 *         ......
 *     ));
 * </pre>
 *     注意：只要加上pagerCssClass属性和相应的属性值为fuck-pager就能够调用
 *
 * Bootstrap pager.
 * @see <http://twitter.github.com/bootstrap/components.html#pagination>
 */
class TFuckPager extends CLinkPager {
    // Pager alignments.

    const ALIGNMENT_CENTER = 'centered';
    const ALIGNMENT_RIGHT = 'right';

    /**
     * @var string the pager alignment. 
     * Valid values are 'centered' and 'right'.
     */
    public $alignment;

    /**
     * @var string the text shown before page buttons.
     * Defaults to an empty string, meaning that no header will be displayed.
     */
    public $header = '';

    /**
     * @var string the URL of the CSS file used by this pager.
     * Defaults to false, meaning that no CSS will be included.
     */
    public $cssFile = false;

    /**
     * @var boolean whether to display the first and last items.
     */
    public $displayFirstAndLast = false;

    /**
     * ### .init()
     *
     * Initializes the pager by setting some default property values.
     */
    public function init() {
        $this->registerScript();
        $this->registerCss();
        $classes = array();
        $validAlignments = array(self::ALIGNMENT_CENTER, self::ALIGNMENT_RIGHT);

        if (in_array($this->alignment, $validAlignments))
            $classes[] = 'pagination-' . $this->alignment;

        if (!empty($classes)) {
            $classes = implode(' ', $classes);
            if (isset($this->htmlOptions['class']))
                $this->htmlOptions['class'] = ' ' . $classes;
            else
                $this->htmlOptions['class'] = $classes;
        }
        if($this->nextPageLabel===null)
            $this->nextPageLabel=Yii::t('yii','&gt;');
        if($this->prevPageLabel===null)
            $this->prevPageLabel=Yii::t('yii','&lt;');
        if($this->firstPageLabel===null)
            $this->firstPageLabel=Yii::t('yii','&lt;&lt;');
        if($this->lastPageLabel===null)
            $this->lastPageLabel=Yii::t('yii','&gt;&gt;');
        if($this->header===null)
            $this->header=Yii::t('yii','Go to page: ');

        parent::init();
    }
    // public function run()
    // {
    //     $this->registerClientScript();
    //     $buttons=$this->createPageButtons();
    //     if(empty($buttons))
    //         return;
    //     echo $this->header;
    //     echo CHtml::tag('ul',$this->htmlOptions,implode("\n",$buttons));
    //     echo $this->footer;
    // }
    /**
     * ### .createPageButtons()
     *
     * Creates the page buttons.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons() {
        $pageCount = $this->getPageCount();
        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        if($pageCount>1){        
            echo "<div  style='float:left;display:inline-block;margin-right:8px;height:30px;line-height:30px;'>第".($currentPage+1)."页</div>";
            echo "<div  style='float:left;display:inline-block;margin-right:8px;height:30px;line-height:30px;'>每页10条</div>";
            echo "<div  style='float:left;display:inline-block;margin-right:8px;height:30px;line-height:30px;'>共".$this->itemCount."条</div>";
        }
        if ($pageCount <= 1)
            return array();
        list($beginPage,$endPage)=$this->getPageRange();
        $buttons = array();

        // first page
        $buttons[]=$this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,$currentPage<=0,false);

        // prev page
        if(($page=$currentPage-1)<0)
            $page=0;
        $buttons[]=$this->createPageButton($this->prevPageLabel,$page,'fuckPre',$currentPage<=0,false);

        // internal pages
        for($i=$beginPage;$i<=$endPage;++$i)
            $buttons[]=$this->createPageButton($i+1,$i,$this->internalPageCssClass,false,$i==$currentPage);

        // next page
        if(($page=$currentPage+1)>=$pageCount-1)
            $page=$pageCount-1;
        $buttons[]=$this->createPageButton($this->nextPageLabel,$page,'fuckNext',$currentPage>=$pageCount-1,false);

        // last page
        $buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);


        return $buttons;
    }
    protected function getPageRange()
    {
        $currentPage=$this->getCurrentPage();
        $pageCount=$this->getPageCount();

        $beginPage=max(0, $currentPage-(int)($this->maxButtonCount/2));
        if(($endPage=$beginPage+$this->maxButtonCount-1)>=$pageCount)
        {
            $endPage=$pageCount-1;
            $beginPage=max(0,$endPage-$this->maxButtonCount+1);
        }
        return array($beginPage,$endPage);
    }
    /**
     * ### .createPageButton()
     *
     * Creates a page button.
     * You may override this method to customize the page buttons.
     *
     * @param string $label the text label for the button
     * @param integer $page the page number
     * @param string $class the CSS class for the page button. This could be 'page', 'first', 'last', 'next' or 'previous'.
     * @param boolean $hidden whether this page button is visible
     * @param boolean $selected whether this page button is selected
     * @return string the generated button
     */
    protected function createPageButton($label,$page,$class,$hidden,$selected)
    {
        if($hidden || $selected)
            $class.=' '.($hidden ? $this->hiddenPageCssClass : $this->selectedPageCssClass);
        return '<li class="'.$class.'">'.CHtml::link($label,$this->createPageUrl($page)).'</li>';
    }
    protected function createPageButton2($label, $page, $class, $hidden, $selected) {
        $class == "next" ? $pager = "next" : $pager = "previous";
        if ($hidden || $selected)
            $class .= ' ' . ($hidden ? 'disabled' : 'active');

        return $pager == "previous" ? 
                CHtml::tag('li', array('class' => $class), CHtml::link(CHtml::tag('span', array('class' => 'icon-arrow-left-4'), $label), $this->createPageUrl($page), array('title' => '上一页', 'style'=>'vertical-align:middle'))) : 
                CHtml::tag('li', array('class' => $class), CHtml::link(CHtml::tag('span', array('class' => 'icon-arrow-right-5'), $label), $this->createPageUrl($page), array('title' => '下一页', 'style'=>'vertical-align:middle')));
    }

    /**
     * 注册CSs
     */
    protected function registerCss() {
       
        Yii::app()->clientScript->registerCssFile(Yii::app()->core->getAssetsUrl() . '/css/pager.css');
    }

    /**
     * 注册JS
     */
    protected function registerScript() {

    }

}

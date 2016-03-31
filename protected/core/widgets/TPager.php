<?php

/**
 * TPager class file.
 *
 *
 * TPager组件包装类.
 *
 * <pre>
 *   $this->widget('bootstrap.widgets.TbGroupGridView', array(
 *        'pagerCssClass' => 'td-pager',
 *         ......
 *     ));
 * </pre>
 *     注意：只要加上pagerCssClass属性和相应的属性值为td-pager就能够调用
 *
 * Bootstrap pager.
 * @see <http://twitter.github.com/bootstrap/components.html#pagination>
 */
class TPager extends CLinkPager {
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
        if ($this->nextPageLabel === null)
            $this->nextPageLabel = '';

        if ($this->prevPageLabel === null)
            $this->prevPageLabel = '';

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

        parent::init();
    }

    /**
     * ### .createPageButtons()
     *
     * Creates the page buttons.
     * @return array a list of page buttons (in HTML code).
     */
    protected function createPageButtons() {
        $pageCount = $this->getPageCount();
        if($pageCount>1)
            echo "<div  style='float:left;display:inline-block;margin-right:8px;height:30px;line-height:30px;'>共".$this->itemCount."条</div>";
        if ($pageCount <= 1)
            return array();

        $currentPage = $this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons = array();

        // internal pages
        for ($i = $beginPage; $i <= ($pageCount - 1); ++$i) {
            $class = ($i == $currentPage ? 'active' : 'disabled');
            $pages[] = array('label' => ($i + 1) . "/" . $pageCount, 'url' => $this->createPageUrl($i), 'linkOptions' => array('class' => $class));
        };

        $this->widget('bootstrap.widgets.TbButtonGroup', array(
            'size' => 'small',
            'buttons' => array(
                array('label' => ($currentPage + 1) . "/" . $pageCount, 'items' => $pages)
                )));
        // first page
        if ($this->displayFirstAndLast)
            $buttons[] = $this->createPageButton($this->firstPageLabel, 0, 'first', $currentPage <= 0, false);

        // prev page
        if (($page = $currentPage - 1) < 0)
            $page = 0;

        $buttons[] = $this->createPageButton($this->prevPageLabel, $page, 'previous', $currentPage <= 0, false);




        // next page
        if (($page = $currentPage + 1) >= $pageCount - 1)
            $page = $pageCount - 1;

        $buttons[] = $this->createPageButton($this->nextPageLabel, $page, 'next', $currentPage >= ($pageCount - 1), false);

        // last page
        if ($this->displayFirstAndLast)
            $buttons[] = $this->createPageButton($this->lastPageLabel, $pageCount - 1, 'last', $currentPage >= ($pageCount - 1), false);


        return $buttons;
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
    protected function createPageButton($label, $page, $class, $hidden, $selected) {
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
        $cs = Yii::app()->clientScript;
        $cs->registerScript(__CLASS__ . $this->getId(), '
            $(".td-pager .dropdown-toggle").live("mouseenter",function(){
            if($(".td-pager .dropdown-menu").height()>=200){
             $(".td-pager .dropdown-menu").height(200);
             $(".td-pager .dropdown-menu").niceScroll({cursorcolor:"#ccc",horizrailenabled:false});
            };
       $(this).toggle(
    function(){
        $(".td-pager .dropdown-menu").show();
    }, 
    function(){
          $(".td-pager .dropdown-menu").hide();
    });
    });
                
    ');
    }

}

<?php

/**
 * zTreeDropdown class file.
 *
 * 基于ztree的下拉菜单
 */
Yii::import('core.widgets.TTreeView');

/**
 *
 * zTree下拉菜单
 *
 * 使用方法:
 * <pre>
 * $this->widget('path.ztree.TTreeDropdown',array(
 * 		'treeNodeNameKey'=>'name',
 * 		'treeNodeKey'=>'id',
 * 		'treeNodeParentKey'=>'pId',
 *      'showLink'=>true,
 *      'linkText'=>'选择',
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
 * 	提醒：
 * 		1、width属性不填的话，背景层宽度与textId宽度一样
 *      2、默认展开所有结点
 *
 *
 */
class TTreeDropdown extends TTreeView {

    public $cssFileUser = array('td.treeview.default.css');

    public $form = null;
    public $formModel;
    public $formAttribute;
    /**
     * 是否显示选择链接
     * @var bool
     */
    public $showLink;

    /**
     * 选择链接文字
     * @var string
     */
    public $linkText = '选择';

    /**
     * 默认值
     * @var string
     */
    public $defaultText = '';

    /**
     * 是否自动展开所有结点
     * @var bool
     */
    public $expandAll = true;

    /**
     * tree容器高度
     * @var int
     */
    public $height = 100;

    /**
     * tree容器宽度
     * @var int
     */
    public $width;

    private $_textId;
    private $_hiddenId;
    private $_linkId = null;


    /**
     * 初始化
     * @see TTreeView::init()
     */
    public function init() {
        parent::init();
        if($this->formModel instanceof CModel && $this->formAttribute!==null && $this->form!==null) {
            $this->_textId = $this->formAttribute . '_text';
            $this->_hiddenId = get_class($this->formModel).'_'.$this->formAttribute;
            $this->_linkId = $this->formAttribute . '_link';
            echo CHtml::textField($this->_textId, $this->defaultText, array('id'=>$this->_textId));
            echo $this->form->hiddenField($this->formModel, $this->formAttribute, array('value'=>$this->selectNode));
        } else {
            $this->_textId = $this->treeId . '_text';
            $this->_hiddenId = $this->treeId . '_hidden';
            $this->_linkId = $this->treeId . '_link';
            echo CHtml::textField($this->_textId, $this->defaultText, array('id'=>$this->_textId));
            echo CHtml::hiddenField($this->_hiddenId, $this->selectNode, array('id'=>$this->_hiddenId));
        }

        //显示选择链接
        if($this->showLink)
        {
            echo CHtml::link($this->linkText, '#', array('id'=>$this->_linkId));
        }


        if(!$this->options['callback']['onClick']) {
            $this->options['callback']['onClick'] = "js:function(event, treeId, treeNode) {
						if (treeNode) {
							$('#{$this->_textId}').attr('value', treeNode.name);
                            $('#{$this->_hiddenId}').attr('value', treeNode.id);
							$('#{$this->treeId}').fadeOut('fast');
						}
					}";
        }
        $this->htmlOptions['class'] .= ' ztreeDropDown';

    }

    /**
     * 注册JS
     * @see zTree::getRegisterScripts()
     */
    protected function getRegisterScripts() {
        $js = parent::getRegisterScripts();

        $trigger = "#{$this->_textId}".($this->showLink ? $this->_linkId : "");
        $js[] = "jQuery('$trigger').live('click', function(){
                    if($('#{$this->treeId}:visible').length > 0)
                        return;
//                    \$('#{$this->treeId}').parents('div.modal-body').each(function(){
//                        if(\$(this).css('position') == 'relative') {
//                            relativeDom.push(this);
//                            \$(this).css('position', 'static');
//                        }
//                    });

					var inputObj = \$('#{$this->_textId}');
					var inputOffset = inputObj.position();
					\$('#{$this->treeId}').css({left:inputOffset.left + 'px', top:inputOffset.top + inputObj.outerHeight() + 'px'}).slideDown('fast').fadeIn(\"fast\");
		})";
        if ($this->width === null) {
            $js[] = "\$('#{$this->treeId}').width(\$('#{$this->_textId}').width());";
        }
        if ($this->height !== null) {
            $js[] = "\$('#{$this->treeId}').height(".$this->height.");";
        }
        $js[] = '$("body").bind("mousedown", function(event){
				if (!(event.target.id == "'.$this->_textId.'" || event.target.id == "' . $this->treeId . '" || $(event.target).parents("#' . $this->treeId . '").length>0))
				{
//                    if(relativeDom.length > 0) {
//                        $.each(relativeDom ,function(i, obj){
//                            $(obj).css({"position" :"relative"});
//                        });
//                    }
					$("#' . $this->treeId . '").fadeOut("fast");
				}
			});';
        $js[] = "var relativeDom = [];";
        return $js;
    }

}
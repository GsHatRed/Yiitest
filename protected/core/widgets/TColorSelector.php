<?php
/**
 * TColorSelector class file.
 *
 * @author FL
 */
class TColorSelector extends CInputWidget {
    
    /**
     * 色块样式数量
     * @var type 
     */
    public $maxColor=18;
    
    public function run(){

		list($name, $id) = $this->resolveNameID();

		$this->registerClientScript($id);

		$this->htmlOptions['id'] = $id;

		// 是否存在Model
		if ($this->hasModel())
		{
            echo CHtml::activehiddenField($this->model, $this->attribute, $this->htmlOptions);
		}
		else
			echo CHtml::hiddenField($name, $this->value, $this->htmlOptions);
        
        for($i=1; $i<=$this->maxColor; $i++) {
            echo '<a class="pick_color" href="#" node-data="'.$i.'"><span class="color_style_'.$i.'"></span></a>';
        }
    }
    
    /**
     * 注册JS
     */
    protected function registerClientScript($id) {
        
        $cs = Yii::app()->clientScript;
        
        $cs->registerCssFile(Yii::app()->core->getAssetsUrl().'/css/portal.css');
        
        $cs->registerScript(__CLASS__ . $this->getId(), '
$(".pick_color").toggle(
    function(){
        $(".pick_color").removeClass("active");
        $(this).addClass("active");
        $("#'.$id.'").val($(this).attr("node-data"));
    }, 
    function(){
        $(this).removeClass("active");
        $("#'.$id.'").val();
    }
);            
');
    }
    
    
}

?>

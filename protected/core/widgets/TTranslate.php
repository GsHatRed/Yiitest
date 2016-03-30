<?php
Yii::import('core.widgets.TWidget');
class TTranslate extends TWidget{
    
    public $model;
    
    public $attribute;
    
    public $modalId;
    
    public function init(){
        $this->modalId=  $this->id.'Modal'.uniqid();
        $this->showLangModal();
        Yii::app()->clientScript->registerScript($this->modalId.'translateModal','
            jQuery("#'.$this->modalId.'button").live("click",function(){
                $("#'.$this->modalId.'").modal("show");
                    $("#'.$this->modalId.'ModalBody input").each(function(){
                        $(this).keyup(function(){
                            $(this).attr("value",$(this).val());
                        });
                    });
            });
            jQuery("#'.$this->modalId.'save").delegate(this,"click",function(){
                var allInput=$("#'.$this->modalId.'ModalBody").find("input");
                var total=allInput.length;
                var isNull=1;
                $.each(allInput,function(i,value){
                    if(value.value==""){
                        isNull=isNull+1;
                    }else
                        $(this).html($(this).val());
                });
                if(1!=isNull){
                    $("#'.$this->modalId.'message").html("请把所有语言翻译完成！");
                    return false;
                }else{
                    var form=$("<form id=\''.$this->modalId.'form\'></form>");
                   form1=$("#'.$this->modalId.'ModalBody").html();
                    form=form.append(form1);
                    var data=$(form).serializeArray();
                    $.post("'.Yii::app()->createUrl('/portal/ajax/trans').'",{data:data},function(result){
                        var str=result.split("|");
                        if(str[0]=="ok"){
                            $("#'.$this->modalId.'message").css("color","green");
                        }
                        $("#'.$this->modalId.'message").html(str[1]);
                    });
                    $("#'.$this->modalId.'").modal("hide");
                }
            });
        ');
    }
 
    public function run(){
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType'=>'button',
                'label'=>'翻译',
                'htmlOptions'=>array('id'=>$this->modalId.'button','style'=>'margin-left:10px;'),
            ));
    }
    
    public function showLangModal(){
        $language=Yii::app()->params['language'];
        $this->beginWidget('bootstrap.widgets.TbModal', array('id' => $this->modalId, 'htmlOptions' => array('style' => 'margin-top:-200px;','backdrop'=>'static')));
        echo "<div class=\"modal-header\">
                <h4 style=\"width:30%;display: inline-block;\">翻译</h4> 
                <span id='".$this->modalId."message' style=\"margin-left:30px;color:red;\"></span>
            </div>";
        echo "<div id='".$this->modalId."ModalBody' class=\"modal-body\" style=\"text-align:center\">";
            $tableName=$this->model->tableName();
            $attribute=$this->attribute;
            echo CHtml::hiddenField('tableName',$tableName);
            echo CHtml::hiddenField('attribute',$attribute);
            $pk=$this->model->primaryKey;
            echo CHtml::activeHiddenField($this->model, $this->model->pk,array('name'=>'pk'));
            $result=Translation::model()->find('model=:tableName and pk=:pk and attribute=:attribute',array(':tableName'=>$tableName,':pk'=>$pk,':attribute'=>$attribute));
            $data=  json_decode($result->data);
            foreach($language as $key =>$value){
                if(strtolower($key)=='zh_cn')
                    continue;
                echo "<div><span style='width:80px !important;display: inline-block;'>".$value.'</span>'.CHtml::textField($key,$data->$key)."</div>";
            }
        echo "</div>";
        echo "<div class=\"modal-footer\" style=\"text-align: center;\">";
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'type' => 'info',
                'label' => '保存',
                'htmlOptions' => array(
                    'id' => $this->modalId.'save',
                ),
            ));
            echo "&nbsp;";
            $this->widget('bootstrap.widgets.TbButton', array(
                'buttonType' => 'button',
                'label' => '取消',
                'htmlOptions' => array("data-dismiss" => "modal",'id'=>$this->modalId.'back'),
            ));
        echo "</div>";
        $this->endWidget();
    }
}
?>

<?php

/**
 * TPictureSelector class file.
 *
 */
class TPictureSelector extends CInputWidget {

    /**
     * 模态图片选择窗口URL
     * @var string 
     */
    public function run() {

        $this->registerCss($id);
        $this->uploadPictureModal();
        $this->registerClientScript($id);
        $this->htmlOptions['id'] = $id;
        $this->widget('bootstrap.widgets.TbButton', array(
            'label' => '批量插入图片',
            'type' => 'link',
            'icon' => 'icon-image',
            'id' => $id . '_btn',
        ));
    }

    /**
     * 注册JS
     */
    protected function registerClientScript($id) {

        Yii::app()->clientScript->registerScript(__CLASS__ . $this->getId(), '
$("#' . $id . '_btn' . '").live("click",function(){
    $("#upload-picture-modal").show();
    $("#upload_opt").val("insert");
    $("<div class=\"modal-backdrop fade in upload-picture-backdrop\" style=\"z-index: 999;\"></div>").appendTo("body");
    checkShow();
});    
function checkShow(){
   if($("#upload-picture-modal .files li").length>0){
        $("#upload-picture-modal .fileupload-buttonbar .fileinput-button").addClass("move-right");
        $("#upload-picture-modal .batchInsertPicture").show();
        $("#upload-picture-modal .btn-add").addClass("hide-button");
        $("#upload-picture-modal .message-info").hide();
    }else{
        $("#upload-picture-modal .fileupload-buttonbar .fileinput-button").removeClass("move-right");
        $("#upload-picture-modal .batchInsertPicture").hide();
        $("#upload-picture-modal .message-info").show();
        $("#upload-picture-modal .btn-add").removeClass("hide-button");
    }
}
 $("#upload-picture-modal").find(".close").click(function(){
    $(this).parents("#upload-picture-modal").hide();
    $("#upload_opt").val("upload");
    $(".upload-picture-backdrop").remove();
 });
 $("#upload-picture-modal li").find(".btn-delete-pic").live("click",function(){
    $(this).parents("li").remove();
    checkShow();
 });
$("#upload-picture-modal .return-message").live("mouseenter",function(){
    $(this).find(".delete-pic").show(); 
    $(this).find(".btn-insert").show()
});
$("#upload-picture-modal .btn-insert").live("click",function(){
    var self = $(this);
    var src = self.attr("data-url");
    var editorId = self.attr("data-ckeditor");
    TCKEditorHelper.insertImage(editorId, src);
    return false;
});
$("#upload-picture-modal .batchInsertPicture").live("click",function(event){
    $("#upload-picture-modal .btn-insert").each(function(){
        var self = $(this);
        var src = self.attr("data-url");
        var editorId = self.attr("data-ckeditor");
        TCKEditorHelper.insertImage(editorId, src);
    });
    return false;
});
$("#upload-picture-modal .return-message").live("mouseleave",function(){
    $(this).find(".delete-pic").hide(); 
    $(this).find(".btn-insert").hide()
});
$("#upload-picture-modal").find(".closeModal").live("click",function(){
    $(this).parents("#upload-picture-modal").hide();
    $("#upload_opt").val("upload");
    $(".upload-picture-backdrop").remove();
 });
');
    }

    /**
     * 
     * @todo 图片批量上传层
     */
    private function uploadPictureModal() {
        echo CHtml::openTag('div', array('id' => "upload-picture-modal", 'style' => 'display:none;'));
        echo CHtml::openTag('div', array('class' => 'modal-header'));
        echo CHtml::tag('a', array('class' => 'close'), ×);
        echo CHtml::tag('h4', array('style' => 'height:20px;width:30%;display: inline-block;'), "图片批量插入");
        echo CHtml::closeTag('div');
        echo CHtml::openTag('div', array('class' => 'modal-body'));
        echo CHtml::openTag('div', array('class' => 'drop-box'));
        $this->renderForm();
        echo CHtml::closeTag('div');
        echo CHtml::closeTag('div');
        echo CHtml::openTag('div', array('class' => 'modal-footer'));
        echo "&nbsp;&nbsp;";
        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'buttom',
            'label' => '批量插入图片',
            'type' => 'info',
            'htmlOptions' => array("class" => "pull-left batchInsertPicture", "style" => "display:none;"),
        ));

        $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType' => 'buttom',
            'label' => '关闭',
            'htmlOptions' => array("class" => "pull-right closeModal")
        ));
        echo CHtml::closeTag('div');
        echo CHtml::closeTag('div');
    }

    private function renderForm() {
        $this->widget('bootstrap.widgets.TbFileUpload', array(
            'model' => $this->model,
            'attribute' => $this->attribute,
            'uploadView' => 'bootstrap.views.fileupload.uploadpic',
            'downloadView' => 'bootstrap.views.fileupload.insertpic',
            'formView' => 'bootstrap.views.fileupload.uploadform',
            'multiple' => true,
            'options' => array(
                'fileInput' => 'js:$("input[name=\''.CHtml::resolveName($this->model, $this->attribute).'\']")',
                'add' => 'js:function(e,data){
                 $("#upload-picture-modal .fileupload-buttonbar .fileinput-button").addClass("move-right");
                 $("#upload-picture-modal .batchInsertPicture").show();
                 $("#upload-picture-modal .btn-add").addClass("hide-button");
                 $("#upload-picture-modal .message-info").hide();
                 data.submit();
                }',
                'success'=> 'js:function(data,status){
                 /*若是邮件模块，则返回生成草稿的id*/
                 if(data[0].ckeditor=="EmailContent_content"){
                       $("#hidden_id").val(data[0].draft_id);
                  }
                }',
            )
        ));
    }

    protected function registerCss($id) {
        $css = <<<EOD
           #upload-picture-modal {
            position:fixed;
            right:4px;
            bottom:4px;
            min-width:700px !important;
            background-color: #ffffff;
            border: 1px solid #999;
            -webkit-border-radius: 6px;
            -moz-border-radius: 6px;
            border-radius: 6px;
            outline: none;
            -webkit-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
            -moz-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
            box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
            -webkit-background-clip: padding-box;
            -moz-background-clip: padding-box;
            z-index:1000;
            background-clip: padding-box;
        }
        #upload-picture-modal  .drop-box{
             overflow-x:hidden;
             width:680px;
             height: 256px;
             text-align: center;
             border: 2px dashed #DCDCDC;
             border-radius: 5px;
             padding-top:10px;
        }
        #upload-picture-modal  .template-insert {
             height:232px;
             position: relative;
             border:none;
             float: left;
         }
        #upload-picture-modal .template-insert:hover img{
             filter:alpha(opacity=30); 
             -moz-opacity:0.3; 
             opacity:0.3;
        }
        #upload-picture-modal .template-insert .return-message {
            width: 200px;
            height:220px;
            position: relative;
            float: left;
            padding:10px !important;
            border: none;
        }
        #upload-picture-modal .template-insert .start{
             position: absolute;
             bottom:50px;
             left:60px;
        }
        #upload-picture-modal .template-insert .return-message img{
            position:absolute;
            top:0px;
            left:0px;
            height: 200px;
            width:200px;
            border:1px solid #ccc;
        }
        #upload-picture-modal  .template-insert .preview {
            width: 200px;
            height: 200px;
            position: absolute;
            top:0;
            left: 0;
        }
        #upload-picture-modal  .template-insert .progress-bar{
            position: absolute;
            top:90px;
            left:0;
        }
        #upload-picture-modal  .template-insert .preview canvas{
            width: 200px;
            height: 200px;
        }
        #upload-picture-modal .template-insert .return-message .btn-insert{
            position:absolute;
            top:90px;
            left:40px;
        }
        #upload-picture-modal .template-insert .return-message .btn-delete-pic{
            position:absolute;
            top:90px;
            left:130px;
        } 
        #upload-picture-modal .template-upload .start{ 
            width: 80px !important;
        }
        #upload-picture-modal .move-bottom{ 
            position: absolute;
            bottom:-50px;
            left:10px;
            z-index:1200;
        }
        #upload-picture-modal .move-right{ 
            position: absolute;
            bottom:-50px;
            right:80px;
            z-index:1200;
        }
        #upload-picture-modal .hide-button{
            padding: 0 !important;
            height:1px  !important;
        }
EOD;
        Yii::app()->clientScript->registerCss(__CLASS__ . '#' . $id, $css);
    }

}

?>
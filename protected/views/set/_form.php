<script>


    if (typeof FileReader == 'undefined')
    {
        function photos(event) {
            var src = event.target.value;
            var pathLength = src.length;
            var additionName = src.substring(pathLength - 3, pathLength);

            if (additionName == "jpg" || additionName == "png" || additionName == "gif" || additionName == "JPG" || additionName == "PNG" || additionName == "GIF")
            {
                var img = '<img src="' + src + '" style="width:80px;height:80px;" />';
                $("#avatar-img").empty().append(img);
            }
            else
            {
                var img = "<font color=red>请选择格式为jpg,png或gif的图片!否则无法预览及上传</font>";
                $("#avatar-img").empty().append(img);
            }
        }
    }
    else
    {
        function photos(e) {
            for (var i = 0; i < e.target.files.length; i++)
            {

                var file = e.target.files.item(i);
                if (!(/^image\/.*$/i.test(file.type)) || (file.type == "image/bmp"))
                {
                    var img = "<font color=red>请选择格式为jpg,png或gif的图片!否则无法预览及上传</font>";
                    $("#avatar-img").empty().append(img);
                    continue;
                }
                if (navigator.userAgent.indexOf("MSIE") != -1) {
                    var imgDiv = document.createElement("img");
                    $("#avatar-img").empty();
                    imgDiv.appendTo("#avatar-img");
                    imgDiv.style.width = "80px";
                    imgDiv.style.height = "80px";
                    imgDiv.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod = scale)";
                    imgDiv.filters.item("DXImageTransform.Microsoft.AlphaImageLoader").src = $(this).val();
                } else {
                    //实例化FileReader API
                    var freader = new FileReader();
                    freader.readAsDataURL(file);
                    freader.onload = function(e)
                    {
                        var img = '<img src="' + e.target.result + '" style="width:80px;height:80px;"/>';
                        $("#avatar-img").empty().append(img);
                    }
                }
            }
        }
    }
</script>
<?php
Yii::app()->clientScript->registerCss($this->id, '
    #edu-students-form{position:relative;}
    #edu-students-form #tow-columns .control-group {display: inline-block;}
    #edu-students-form #tow-columns .control-label {width:60px;}
    #edu-students-form #tow-columns .controls {margin-left:80px;}
    #one-column{margin-left:170px;}
    #avatar {
        position: absolute;
        border: 1px solid #ccc;
        border-radius: 2px;
        -webkit-border-radius: 2px;
        -moz-border-radius: 2px;
        -webkit-box-shadow: 0 0 8px rgba(0,0,0,0.3);
        -moz-box-shadow: 0 0 8px rgba(0,0,0,0.3);
        box-shadow: 0 0 8px rgba(0,0,0,0.3);
        width: 80px;
        margin-top:-146px;
        margin-left:120px;
    }
      #avatar-op{
        cursor:pointer !important;
        width:100%;
        text-align: center;
        color: #fff;
        height: 20px;
        line-height: 20px;
        background-color: #ccc;    
    }
    #avatar-op:hover{
        opacity: 0.8;
    }
    #avatar-op .edit{
        width:80px;
        color:#fff;
        width:80px;
        display:block;
        position:relative;
        text-align: center;
        text-decoration: none;
    }
    #avatar-op .file{
        position:absolute;
        left:0;
        height: 20px;
        overflow: hidden;
        width:80px;
        opacity: 0;
        filter:alpha(opacity=0);
        cursor:pointer !important;

    }
    #avatar-op .bar {
        background:#000; 
        opacity: 0.5;
    }
');
?>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'profile-form',
    'enableAjaxValidation'=>true,
)); ?>

<div class="search-form">
    <?php echo $form->errorSummary($model); ?>
    <?php echo $form->errorSummary($profile_model); ?>
    <div id='one-column'>
        <?php echo $form->textField($model, 'username'); ?>

    </div>
    

    <div id="tow-columns">
        <div>
            <?php echo $form->labelEx($profile_model,'sex'); ?>
            <?php echo $form->dropDownList($profile_model, 'sex', array(0 => '女',1 => '男')); ?>
            <?php echo $form->error($profile_model,'sex'); ?>
        </div>
        <div>
            <?php echo $form->numberField($profile_model, 'mobile', array('size' => 11, 'maxlength' => 11)); ?>
        </div>

        <div>
            <?php echo $form->numberField($profile_model, 'qq', array('size' => 20, 'maxlength' => 20)); ?>
            <?php echo $form->textField($profile_model, 'name', array('size' => 30, 'maxlength' => 30)); ?>
        </div>

        <div>
            <?php echo $form->emailField($model, 'email', array('size' => 50, 'maxlength' => 50)); ?>
        </div>
        <div>
            <?php echo $form->textArea($profile_model, 'profiles', array('size' => 60, 'maxlength' => 255)); ?>

        </div>
    <div class="row buttons">
        <?php echo CHtml::submitButton('修改'); ?>
    </div>
    </div>




<?php $this->endWidget(); ?>

</div><!-- form -->
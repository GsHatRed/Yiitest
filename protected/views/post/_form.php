 <?php 
//$this->widget('ext.ckeditor.CKEditorWidget',array(
//  "model"=>$model, # 数据模型
//  "attribute"=>'content', # 数据模型中的字段
//  "defaultValue"=>"Test Text", # 默认值 "config" => array( 
//  //"height"=>"400px", 
//  //"width"=>"100%", 
//  //"toolbar"=>"Full",#工具条全部显示， 
//  "config" => array(
//       "height"=>"400px",
//       "width"=>"100%",
//       "toolbar"=>"Basic",
//       ),
//  //"filebrowserBrowseUrl"=>'/ckfinder/ckfinder.php' #这里很关键，设置这个后，打开上传功能和浏览服务器功能 
//  ) 
// //  Optional address settings if you did not copy ckeditor on application root 
// //  "ckEditor"=>Yii::app()->basePath."/ckeditor/ckeditor.php", 
// // Path to ckeditor.php 
// //  "ckBasePath"=>Yii::app()->baseUrl."/ckeditor/", 
// //  Realtive Path to the Editor (from Web-Root) 
//   ); 
 ?>
<script type="text/javascript" src="<?=Yii::app()->request->baseUrl?>/static/ckeditor/ckeditor.js"></script>
<div class="form">
            
<?php $form=$this->beginWidget('CActiveForm'); ?>

	<?php echo CHtml::errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>80,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'content'); ?>
		<?php echo CHtml::activeTextArea($model,'content',array('rows'=>10, 'cols'=>70)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'tags'); ?>
		<?php $this->widget('CAutoComplete', array(
			'model'=>$model,
			'attribute'=>'tags',
			'url'=>array('suggestTags'),
			'multiple'=>true,
			'htmlOptions'=>array('size'=>50),
		)); ?>
		<p class="hint">不同的標籤請用空格或逗號隔開.</p>
		<?php echo $form->error($model,'tags'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->dropDownList($model,'status',Lookup::items('PostStatus')); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
<script type="text/javascript">
    CKEDITOR.replace( 'Post_content' );
</script>
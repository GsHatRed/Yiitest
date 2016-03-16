<?php
$this->pageTitle=Yii::app()->name . ' - 註冊';
$this->breadcrumbs=array(
	'註冊',
);
?>

<h1>註冊</h1>

<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'regist-form',
	'enableAjaxValidation'=>true,
)); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'username'); ?>
		<?php echo $form->textField($model,'username'); ?>
		<?php echo $form->error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'password'); ?>
		<?php echo $form->passwordField($model,'password'); ?>
		<?php echo $form->error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'email'); ?>
		<?php echo $form->textField($model,'email'); ?>
		<?php echo $form->error($model,'email'); ?>
	</div>

	<div class="row submit">
		<?php echo CHtml::submitButton('註冊'); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->

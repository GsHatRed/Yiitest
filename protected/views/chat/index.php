<p>想說點什麼呢?</p>
<script type="text/javascript" src="<?=Yii::app()->request->baseUrl?>/static/ckeditor/ckeditor.js"></script>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'chat-form',
    'enableAjaxValidation'=>true,
)); ?>
	<div class="row">
		<?php echo CHtml::activeTextArea($model,'content',array('rows'=>10, 'cols'=>70)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div>
	<div class="row buttons">
        <?php echo CHtml::submitButton('發言'); ?>
    </div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
    CKEDITOR.replace( 'Chat_content' );
</script>
<?php
$this->widget('bootstrap.widgets.TbGridView', array(
    'dataProvider' => $dataProvider,
    'columns' =>  array(
        array('name'=>'user_id','value'=>'User::getNameById($data->user_id)'),
        array('name'=>'content','type'=>'raw','value'=>'$data->content')
        )
));
?>
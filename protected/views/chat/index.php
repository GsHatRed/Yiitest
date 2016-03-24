<p>想說點什麼呢?</p>
<script type="text/javascript" src="<?=Yii::app()->request->baseUrl?>/static/ckeditor/ckeditor.js"></script>
<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'chat-form',
    'enableAjaxValidation'=>true,
)); ?>
	<div class="row">
		<?php echo CHtml::activeTextArea($model,'content',array('rows'=>10, 'cols'=>70)); ?>
		<?php echo $form->error($model,'content'); ?>
	</div><br />
	<div class="row buttons">
        <?php 
            if(Yii::app()->user->isGuest){
                echo CHtml::link('請先登錄',Yii::app()->createURL('/site/login'));
            }else{
                echo CHtml::submitButton('發言'); 
            }
        ?>
    </div>
<?php $this->endWidget(); ?>
<script type="text/javascript">
    CKEDITOR.replace( 'Chat_content' );
</script>
<?php
// $this->widget('bootstrap.widgets.TbGridView', array(
//     'dataProvider' => $dataProvider,
//     'columns' =>  array(
//         array('name'=>'user_id','value'=>'User::getNameById($data->user_id)'),
//         array('name'=>'content','type'=>'raw','value'=>'$data->content'),
//         array('name'=>'date','value'=>'date("Y-m-d",$data->date)')
//         )
// ));
?>
<?php foreach($dataProvider->getData() as $key => $data):?>
    <?php if($data->parent_id==0): ?>
    <?php $float = $key%2==1?'right':'left'; ?>
    <div class="chat" style="float:<?=$float?>">
        <span class="name"><?=User::getNameById($data->user_id)?>:</span>
        <span class="content"><?=$data->content?></span>
        <span class="time"><?=date("Y-m-d H:i:s",$data->date)?></span>
    </div>
<?php endif;endforeach;?>
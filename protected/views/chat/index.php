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
                $this->widget('bootstrap.widgets.TbButton', array(
                    'buttonType' => 'button',
                    'type' => 'danger',
                    'label' => '發言',
                    'htmlOptions' => array(
                        'onclick' => 'js: $("#chat-form").submit()',
                    ),
                ));
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
$this->widget('bootstrap.widgets.TbListView', array(
    'dataProvider' => $dataProvider,
    'itemView' => '_chat',
    'template' => '{items}{pager}',
    'id' => 'chat',
    'htmlOptions' => array('style' => 'padding-top:0px;')
));
?>
<?php //foreach($dataProvider->getData() as $key => $data):?>
    <?php //if($data->parent_id==0): ?>
    <?php //$float = $key%2==1?'right':'left'; ?>
    <!-- <div class="chat" style="float:<?//=$float?>">
        <span class="name"><?//=User::getNameById($data->user_id)?>:</span>
        <span class="content"><?//=$data->content?></span>
        <span class="time"><?//=date("Y-m-d H:i:s",$data->date)?></span>
        <span class='praise right' title='喜欢' id="<?//=$data->id?>"><img href="" alt='赞'><?//=$data->praise?></span>
        <span class='reply right' title='回复'><img href="" alt='回复'></span>
    </div> -->
<?php //endif;endforeach;?>
<script>
    $(document).ready(function(){
        $('.praise').bind('click',function(){
            var el = $(this);
            $.post("<?=Yii::app()->createURL('/chat/praise')?>",{'id':el.attr('id')},function(result){
                if(result=='ok'){
                    $.notify({type: 'success', message: {text: '点赞成功！', icon: 'icon-checkmark'}}).show();
                    el.html(Number(el.html())+1);
                }else{
                    $.notify({type: 'error', message: {text: result, icon: 'icon-close'}}).show();
                }
            });
        })
    })
</script>
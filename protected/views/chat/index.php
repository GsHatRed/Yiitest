<script type="text/javascript" src="<?=Yii::app()->request->baseUrl?>/static/ckeditor/ckeditor.js"></script>

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
<p>想說點什麼呢?</p>
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
                    el.html(Number(el.html())-1);
                }
            });
            return false;
        })

        $('.reply').bind('click',function(){
            var el = $(this);
            var user_name = el.parent('.chat').children('a').children('.name').contents().filter(function()
                {
                    return this.nodeType==3;//文本节点
                }).text();
            $('#reply_input').remove();
            $('.hint').remove();
            $('#chat-form').append('<input id="reply_input" name="pid" value="'+el.attr('id')+'" type="hidden" />');
            var hint = $('<span class="hint">回复'+user_name+'<a class="cancle_hint" href="javascript:void(0)">取消</a></span>');
            $('#chat-form .buttons').append(hint);
            hint.bind('click',cancle);
            return false;
        })

        $('.delete').bind('click',function(){
            var el = $(this);
            $.post("<?=Yii::app()->createURL('/chat/delete')?>",{'id':el.attr('id')},function(result){
                if(result=='ok'){
                    $.notify({type: 'success', message: {text: '删除成功！', icon: 'icon-checkmark'}}).show();
                    $('.tooltip').remove();el.parent('div').remove();
                }else{
                    $.notify({type: 'error', message: {text: result, icon: 'icon-close'}}).show();
                }
            });
            return false;
        })
        function cancle(){
            $('#reply_input').remove();
            $('.hint').remove();
            return false;
        }

    })
</script>
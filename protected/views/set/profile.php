<?php $this->renderPartial('_focus',array(
            'ffocus' => $ffocus,
            'tfocus' => $tfocus,
            'model' => $model
        )); ?>
<span>
    本站第<?=$model->id?>位用戶。
    在線時長:<?=floor($model->profile->online_time/60)?>小時<?=$model->profile->online_time%60?>分鐘。
</span>
<?php
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => '修改',
        'type' => "danger",
        'url' => $this->createUrl("update"),
        'visible' => $visible,
        'htmlOptions' => array('style' => 'float:right;margin-left:5px'),
    ));
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => '關注',
        'type' => "info",
        'visible' => !$isFocus,
        'url' => 'javascript:void(0);',
        'htmlOptions' => array('style' => 'float:right;','class' => 'focus'),
    ));
    $this->widget('bootstrap.widgets.TbButton', array(
        'label' => '已關注',
        'type' => "info",
        'visible' => $isFocus,
        'url' => 'javascript:void(0);',
        'htmlOptions' => array('style' => 'float:right;','class' => 'cancel'),
    ));
    $this->widget('bootstrap.widgets.TbDetailView', array(
        'data' => $model,
        'attributes' =>  array(
            //'id',
            'username',
            array('name' => '頭像', 'value' => CHtml::image(Profile::avatarByUserId($model->id)), 'type' => 'raw'),

            array('name' => '暱稱', 'value' => $model->profile->name),
            'email',
            array('name' => '手機號', 'value' => $model->profile->mobile),
            array('name' => 'QQ', 'value' => $model->profile->qq),
            
            array('name' => '簡介', 'value' => $model->profile->profiles),
                ) 
    ));
?>
<?php $this->renderPartial('_article',array(
            'article'=>$article,
        )); ?>

<script>
$(function(){
    var id = <?=$model->id?>;
    var cancel = function(){
        var el = $(this);
        $.post("<?=Yii::app()->createURL('/set/focus')?>",{'id':id,'type':1},function(result){
            if(result=='ok'){
                $.notify({type: 'success', message: {text: '取消關注', icon: 'icon-checkmark'}}).show();
                var buttons = $('<a style="float:right;" class="btn btn-info focus" id="yw0" href="javascript:void(0);">關注</a>');
                el.after(buttons);
                buttons.bind('click',focus);
                el.remove();
            }else{
                $.notify({type: 'error', message: {text: result, icon: 'icon-close'}}).show();
            }
        });
    }
    var focus = function(){
        var el = $(this);
        $.post("<?=Yii::app()->createURL('/set/focus')?>",{'id':id,'type':0},function(result){
            if(result=='ok'){
                $.notify({type: 'success', message: {text: '關注成功', icon: 'icon-checkmark'}}).show();
                var buttons = $("<a style='float:right;' class='btn btn-info focus' id='yw0' href='javascript:void(0);'>已關注</a>");
                el.after(buttons);
                buttons.bind('click',cancel);
                el.remove();
            }else{
                $.notify({type: 'error', message: {text: result, icon: 'icon-close'}}).show();
            }
        });
    }
    $('.focus').bind('click',focus);
    $('.cancel').bind('click',cancel);
    
})
</script>
<span>
    您是本站第<?=$model->id?>位用戶。
    在線時長:<?=floor($model->profile->online_time/60)?>小時<?=$model->profile->online_time%60?>分鐘。
</span>
<?php
$this->widget('bootstrap.widgets.TbButton', array(
        'label' => '修改',
        'size' => 'larger',
        'url' => $this->createUrl("update"),
    ));
$this->widget('bootstrap.widgets.TbDetailView', array(
    'data' => $model,
    'attributes' =>  array(
        //'id',
        'username',
        array('name' => '頭像', 'value' => CHtml::image(Profile::avatarHelper($model->profile->avatar)), 'type' => 'raw'),

        array('name' => '暱稱', 'value' => $model->profile->name),
        'email',
        array('name' => '手機號', 'value' => $model->profile->mobile),
        array('name' => 'QQ', 'value' => $model->profile->qq),
        
        array('name' => '簡介', 'value' => $model->profile->profiles),
            ) 
));
?>
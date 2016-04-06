<?php $this->renderPartial('_focus',array(
            'ffocus' => $ffocus,
            'tfocus' => $tfocus,
            'model' => $user
        )); ?>
<?php
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
?>
<div class="fans-body">
	<div class="page-header">
	    <h1><?=User::getNameById($user->id)?> 的<?=$type?> <small>(<?=Focus::$typefocus($user->id)?>)</small></h1>
	</div>
    <ul class="media-list">
    	<?php $this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$model,
			'itemView' => $typefocus=="countFfocus" ? '_list' : '_listfans',
			'template'=>"{items}\n{pager}",
		)); ?>
    </ul>
</div>

<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . "/static/js/focus.js",2);?>
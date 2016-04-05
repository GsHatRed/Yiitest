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
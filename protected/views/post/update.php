<?php
$this->breadcrumbs=array(
	$model->title=>$model->url,
	'更新',
);
?>

<h1>更新 <i><?php echo CHtml::encode($model->title); ?></i></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
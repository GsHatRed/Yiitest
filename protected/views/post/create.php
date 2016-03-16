<?php
$this->breadcrumbs=array(
	'創建文章',
);
?>
<h1>創建文章</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php if(!empty($_GET['tag'])): ?>
<h1>文章標籤:<i><?php echo CHtml::encode($_GET['tag']); ?></i></h1>
<?php endif; ?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_tagview',
	'template'=>"{items}\n{pager}",
)); ?>

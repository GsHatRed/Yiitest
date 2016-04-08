<?php $this->beginContent('/layouts/main'); ?>
	<div class="span-6 last">
		<div id="sidebar">
			
			<?php $this->widget('TagList', array(
				'maxTags'=>Yii::app()->params['tagCloudCount'],
			)); ?>

		</div><!-- sidebar -->
	</div>
	<div class="span-18">
		<div id="content">
			<?php echo $content; ?>
		</div><!-- content -->
	</div>

<?php $this->endContent(); ?>
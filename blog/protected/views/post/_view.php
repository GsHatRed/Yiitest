<div class="post">
	<div class="title">
		<?php echo CHtml::link(CHtml::encode($data->title), $data->url); ?>
	</div>
	<div class="author">
		發佈人:<?php echo $data->author->username . ' 發佈時間:' . date('F j, Y',$data->create_time); ?>
	</div>
	<div class="content">
		<?php
			$this->beginWidget('CMarkdown', array('purifyOutput'=>true));
			echo $data->content;
			$this->endWidget();
		?>
	</div>
	<div class="nav">
		<b>標籤:</b>
		<?php echo implode(', ', $data->tagLinks); ?>
		<br/>
		<?php echo CHtml::link('查看', $data->url); ?> |
		<?php echo CHtml::link("评论 ({$data->commentCount})",$data->url.'#comments'); ?> |
		最后更新于 <?php echo date('M-d, Y',$data->update_time); ?>
	</div>
</div>

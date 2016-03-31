<ul>
	<?php if(!empty($this->getRecentComments())):foreach($this->getRecentComments() as $comment): ?>
	<li><?php echo $comment->authorLink; ?> on
		<?php echo CHtml::link(CHtml::encode($comment->post->title), $comment->getUrl()); ?>
	</li>
	<?php endforeach;else: ?>
	<li>暂无评论</li>
	<?php endif;?>
</ul>
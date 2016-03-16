<ul>
	<li><?php echo CHtml::link('创建新博客',array('post/create')); ?></li>
	<li><?php echo CHtml::link('管理文章',array('post/admin')); ?></li>
	<li><?php echo CHtml::link('管理评论',array('comment/index')) . ' (' . Comment::model()->pendingCommentCount . ')'; ?></li>
	<li><?php echo CHtml::link('退出',array('site/logout')); ?></li>
</ul>
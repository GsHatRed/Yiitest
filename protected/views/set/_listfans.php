<li class="media" data-key="<?=$data->id?>">
	<a class="media-left" href="<?=Yii::app()->createUrl('/set/info',array('id'=>$data->f_user_id))?>" rel="author" data-original-title="" title="">
		<img src="<?=Profile::avatarByUserId($data->f_user_id)?>" alt="">
	</a>
	<div class="media-body">
		<h2 class="media-heading">
			<a href="<?=Yii::app()->createUrl('/set/info',array('id'=>$data->f_user_id))?>" rel="author" data-original-title="" title=""><?=User::getNameById($data->f_user_id)?></a>
		</h2>
		<div class="media-action">
			<span><?=date('Y-m-d H:i:s',$data->create_time)?></span>
			<span class="pull-right">
				<a class="follow" href="<?=$data->f_user->url?>">取消关注</a> | 
				<a href="<?=$data->f_user->url?>">访问</a>
			</span>
		</div>
	</div>
</li>
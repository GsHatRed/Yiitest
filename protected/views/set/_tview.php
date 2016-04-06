<li>
	<a href="<?=$data->f_user->url?>" rhref="<?=Yii::app()->createUrl('/set/info',array('id'=>$data->t_user_id))?>" rel="author" data-original-title="" title="">
		<img src="<?=Profile::avatarByUserId($data->t_user_id)?>" alt="">
    </a>
</li>
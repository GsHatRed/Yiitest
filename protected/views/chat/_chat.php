<?php $float = $index%2==1?'right':'left';?>
<div class="chat" style="float:<?=$float?>">
	<a href="<?=$data->user->url?>" rhref="<?=Yii::app()->createUrl('/set/info',array('id'=>$data->user->id))?>" rel="author" data-original-title="" title="">
	    <span class="name">
	    	<img src="<?=Profile::avatarByUserId($data->user_id)?>"/><?=User::getNameById($data->user_id)?>
	    </span>
	</a>
	<?=(empty($data->parent_id) ? '' : '回复 '.Chat::getUserById($data->parent_id))?>:
    <span class="content"><?=$data->content?></span>
    <span class="time"><?=date("Y-m-d H:i:s",$data->date)?></span>
    <a class='delete right icon-remove' href='javascript:void(0)' data-toggle="tooltip" data-original-title='删除' id="<?=$data->id?>">删除</a>
    <a class='praise right icon-thumbs-up-2' href='javascript:void(0)' data-toggle="tooltip" data-original-title='喜欢' id="<?=$data->id?>"><?=$data->chatCount?></a>
    <a class='reply right icon-pencil' href='javascript:void(0)' data-toggle="tooltip" data-original-title='回复' id="<?=$data->id?>">回复</a>
</div>
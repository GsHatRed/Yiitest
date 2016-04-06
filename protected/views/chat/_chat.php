<?php $float = $index%2==1?'right':'left';?>
<div class="chat" style="float:<?=$float?>">
    <span class="name"><img src="<?=Profile::avatarByUserId($data->user_id)?>"/><?=User::getNameById($data->user_id)?>:</span>
    <span class="content"><?=$data->content?></span>
    <span class="time"><?=date("Y-m-d H:i:s",$data->date)?></span>
    <span class='praise right icon-thumbs-up-2' data-toggle="tooltip" data-original-title='喜欢' id="<?=$data->id?>"><?=$data->praise?></span>
    <span class='reply right' data-toggle="tooltip" data-original-title='回复'><img href="" alt='回复'></span>
</div>
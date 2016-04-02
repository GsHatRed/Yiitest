<div class="panel-avatar">
<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title">关注 
        	<span class="badge">2</span>
        	<span class="pull-right"><a href="#">全部关注</a></span>
        </h2>
    </div>
    <div class="panel-body">
        <ul class="avatar-list">
        	<?php $this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$tfocus,
				'itemView'=>'_tview',
				'template'=>"{items}\n{pager}",
			)); ?>
        </ul>
    </div>
</div>

<div class="panel panel-default low">
    <div class="panel-heading">
        <h2 class="panel-title">粉丝 
        	<span class="badge">2</span>
        	<span class="pull-right"><a href="#">全部粉丝</a></span>
        </h2>
    </div>
    <div class="panel-body">
        <ul class="avatar-list">
        	<?php $this->widget('zii.widgets.CListView', array(
				'dataProvider'=>$ffocus,
				'itemView'=>'_fview',
				'template'=>"{items}\n{pager}",
			)); ?>
        </ul>
    </div>
</div>
</div>
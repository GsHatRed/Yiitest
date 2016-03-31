<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="en" />

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/static/css/screen.css" media="screen, projection" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/static/css/print.css" media="print" />
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/static/css/ie.css" media="screen, projection" />
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/static/css/main.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/static/css/form.css" />
	<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . "/static/js/util.js");?>
	<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
		<div id="mainmenu">
		<?php 
			$this->widget('zii.widgets.CMenu',array(
				'items'=>array(
					array('label'=>'主頁', 'url'=>array('post/index')),
					array('label'=>'標籤', 'url'=>array('post/tags')),
					array('label'=>'聊起來', 'url'=>array('chat/index')),
					array('label'=>'關於', 'url'=>array('site/page', 'view'=>'about')),
					array('label'=>'聯繫我們', 'url'=>array('site/contact')),
					array('label'=>'個人主頁', 'url'=>array('set/profile'), 'visible'=>!Yii::app()->user->isGuest),
					array('label'=>'登陸', 'url'=>array('site/login'), 'visible'=>Yii::app()->user->isGuest),
					array('label'=>'註冊', 'url'=>array('site/regist'), 'visible'=>Yii::app()->user->isGuest),
					array('label'=>'退出 ('.User::getNameById(Yii::app()->user->id).')', 'url'=>array('site/logout'), 'visible'=>!Yii::app()->user->isGuest)
				),
			)); 
		?>
		</div><!-- mainmenu -->
	</div><!-- header -->

	<?php $this->widget('zii.widgets.CBreadcrumbs', array(
		'links'=>$this->breadcrumbs,
	)); ?><!-- breadcrumbs -->
	<div class="panel panel-info online">
		<div class="panel-heading">在线<br><em><?=User::getOnlineNum()?></em>人</div>
		<div class="panel-body">
		    <ul class="online-scroll list-unstyled ps-container ps-theme-default ps-active-y" style="height: 443px;" data-ps-id="137d9bf9-205a-ad95-5cec-a529472e5734">
				<?php foreach( User::getOnlineUser() as $key => $data ): ?>
				<li>
					<a href="<?=$data->url?>" rel="author" data-original-title="" title="">
					<img src="<?=Profile::avatarByUserId($data->id)?>" alt="gotokfc">
					</a>
				</li>
				<div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px;">
					<div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div>
				</div>
				<div class="ps-scrollbar-y-rail" style="top: 0px; height: 443px; right: 3px;">
					<div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 70px;"></div>
				</div>
				<?php endforeach;?>
			</ul>
		</div>
		<div class="panel-footer">共<br><em><?=User::getTotal()?></em>人</div>
	</div>
		<?php echo $content; ?>

	<div id="footer">
		<?php echo Yii::app()->params['copyrightInfo']; ?><br/>
		All Rights Reserved.<br/>
		<?php echo Yii::powered(); ?>
		<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/static/css/panel.css" />
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
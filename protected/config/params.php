<?php

// this contains the application parameters that can be maintained via GUI
return array(
	// this is displayed in the header section
	'title'=>'Gs熊爸爸',
	// this is used in error pages
	'adminEmail'=>'306655621@qq.com',
	// number of posts displayed per page
	'postsPerPage'=>10,
	// maximum number of comments that can be displayed in recent comments portlet
	'recentCommentCount'=>10,
	// maximum number of tags that can be displayed in tag cloud portlet
	'tagCloudCount'=>20,
	// whether post comments need to be approved before published
	'commentNeedApproval'=>true,
	//密码加密
    'password_encrypt_salt' => 'GS',
	// the copyright information displayed in the footer section
	'copyrightInfo'=>'Copyright &copy; 2016.1.13 by Gs熊爸爸.',
	'color' => array(
		'0' => 'black',
		'1' => 'red',
		'2' => 'yellow',
		'3' => 'blue'
		),
	'avatarUrl' => './../upload/avatar/',
	'avatarView' => '/blog/upload/avatar/',
);

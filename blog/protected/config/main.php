<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'have a try',
	'charset' => 'UTF-8',
    'language'=>'zh_CN',
    'defaultController' => 'blog',
	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.validator.*',
	),

	'defaultController'=>'post',

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// 'db'=>array(
		// 	'connectionString' => 'sqlite:protected/data/blog.db',
		// 	'tablePrefix' => 'tbl_',
		// ),
		// uncomment the following to use a MySQL database
		'db' => array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=test;port=3306',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'tablePrefix' => 'tbl_',
        ),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		
		'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '<controller:\w+>/<id:\d+>/*' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>/*' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/*' => '<controller>/<action>',
            ),
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);
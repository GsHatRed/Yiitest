<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Gself Blog',
	'charset' => 'UTF-8',
    'language'=>'zh_CN',
	// preloading 'log' component
	'preload'=>array('core','bootstrap','log'),
	//'theme'=>'bootstrap',
	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.validator.*',
		'application.core.components.*',
        'application.core.utils.*',
	),
	'behaviors' => array('application.core.behaviors.ConfigBehavior'),
	'defaultController'=>'post/index',
	'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'GShatred',
            'ipFilters'=>array('127.0.0.1','::1'),
            // 'generatorPaths'=>array(
            //     'ext.bootstrap.gii',
            // ),
        ),
    ),
	// application components
	'components'=>array(
		'core' => array(
            'class' => 'application.core.components.Core',
        ),
        'assetManager' => array(
            'class' => 'application.core.components.TAssetManager'
        ),
        'request'=>array(
            'class' => 'application.core.components.THttpRequest',
        ),
        'storage' => array(
            'class' => 'core.components.TStorage',
        ),
        'image' => array(
            'class' => 'core.components.TImage',
        ),
        'tokenizer' => array(
            'class' => 'core.components.TTokenizer'
        ),
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
		'bootstrap'=>array(  
                'class'=>'ext.bootstrap.components.Bootstrap',  
        ),  
		'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
            	'gii'=>'gii',
	            'gii/<controller:\w+>'=>'gii/<controller>',
	            'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
                '<controller:\w+>/<id:\d+>/*' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>/*' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/*' => '<controller>/<action>',
            ),
        ),
		// 'log'=>array(
		// 	'class'=>'CLogRouter',
		// 	'routes'=>array(
		// 		array(
		// 			'class'=>'CFileLogRoute',
		// 			'levels'=>'error, warning',
		// 		),
				
		// 	),
		// ),
		'cache' => array(
            'class'=>'CMemCache',  
            'servers'=>array(  
                array(  
                    'host'=>'127.0.0.1',  
                    'port'=>11211,  
                    'weight'=>100,
                    'timeout' => 3,
                ),  
            ),
        ),
		'log' => array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
                    'ipFilters'=>array('127.0.0.1'),
                ),
            ),
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);
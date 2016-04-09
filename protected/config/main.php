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
        'application.modules.srbac.controllers.SBaseController',  
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
        'srbac' => array(  
	       'userclass'=>'User', //default: User  
           'userid'=>'id', //default: userid  
           'username'=>'username', //default:username  
           'delimeter'=>'@', //default:-  
           'debug'=>true, //default :false  
           'pageSize'=>10, // default : 15  
           'superUser' =>'Authority', //default: Authorizer  
           'css'=>'srbac.css', //default: srbac.css  
           'layout'=> 'application.views.layouts.main', //default: application.views.layouts.main,  
           //must be an existing alias  
           'notAuthorizedView'=> 'srbac.views.authitem.unauthorized', // default:  
           //srbac.views.authitem.unauthorized, must be an existing alias  
           'alwaysAllowed'=>array( //default: array()  
           'SiteLogin','SiteLogout','SiteIndex','SiteAdmin',  
           'SiteError', 'SiteContact'),  
           'userActions'=>array('Show','View','List'), //default: array()  
           'listBoxNumberOfLines' => 15, //default : 10 'imagesPath' => 'srbac.images', // default: srbac.images 'imagesPack'=>'noia', //default: noia 'iconText'=>true, // default : false 'header'=>'srbac.views.authitem.header', //default : srbac.views.authitem.header,  
           //must be an existing alias 'footer'=>'srbac.views.authitem.footer', //default: srbac.views.authitem.footer,  
           //must be an existing alias 'showHeader'=>true, // default: false 'showFooter'=>true, // default: false  
           'alwaysAllowedPath'=>'srbac.components', 
	    )
    ),
	// application components
	'components'=>array(
		'core' => array(
            'class' => 'application.core.components.Core',
        ),
        'image' => array(
            'class' => 'core.components.TImage',
        ),
        'thumb'=>array(
			'class'=>'core.components.CThumb',
		),
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'class' => 'application.modules.srbac.components.AuthWebUser',
      'loginUrl' => array('/yii/site/login'),
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
      'authManager' => array(  
          //'class'=>'srbac.components.SDbAuthManager',  
         // The database component used  
         'connectionID'=>'db',  
         // The itemTable name (default:authitem)  
         'itemTable'=>'authitem',  
         // The assignmentTable name (default:authassignment)  
         'assignmentTable'=>'authassignment',  
         // The itemChildTable name (default:authitemchild)  
         'itemChildTable'=>'authitemchild',  
         'class'=>'CDbAuthManager',  
          'defaultRoles'=>array('authenticated', 'guest'),  
          'behaviors' => array(
              'auth' => array(
                  'class' => 'application.modules.srbac.components.AuthBehavior',
                  'admins' => array('admin'),
              ),
          ),
	    ),  
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'bootstrap'=>array(  
                'class'=>'ext.bootstrap.components.Bootstrap',  
        ),  
		'urlManager' => array(
			//'class' => 'ext.urlmanager.UrlManager',
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
            	'gii'=>'gii',
	            'gii/<controller:\w+>'=>'gii/<controller>',
	            'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
	            'srbac'=>'srbac',
	            'srbac/<controller:\w+>'=>'srbac/<controller>',
	            'srbac/<controller:\w+>/<action:\w+>'=>'srbac/<controller>/<action>',
                '<controller:\w+>/<id:\d+>/*' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>/*' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/*' => '<controller>/<action>',
                // REST patterns
		        array('api/list', 'pattern'=>'api/<model:\w+>', 'verb'=>'GET'),
		        array('api/view', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'GET'),
		        array('api/update', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'PUT'),
		        array('api/delete', 'pattern'=>'api/<model:\w+>/<id:\d+>', 'verb'=>'DELETE'),
		        array('api/create', 'pattern'=>'api/<model:\w+>', 'verb'=>'POST'),
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
          //       array(  
		        //     'class' => 'CFileLogRoute',  
		        //     'levels' => 'error, warning',  
		        //     'categories'=> '*',  
		        //     'logFile'=> 'error.log',  
		        // ), 
            ),
        ),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>require(dirname(__FILE__).'/params.php'),
);
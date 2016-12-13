<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => '测试',
    'defaultController' => 'site',
    // preloading 'log' component
    'preload' => array('log'),
    // application default language.
    'language' => 'zh_cn',
    //'language'=>'en_us',
    // config to be defined at runtime.
    'behaviors' => array('ApplicationConfigBehavior'),
    // autoloading model and component classes
    'import' => array(
        'application.models.*',
        'application.models.user.*',
        'application.models.base.*',
        'application.components.*',
    ),
    'modules' => array(
        'fileupload',
        'mobiledoctor',
        'mobile',
        'translate', //manages translation message.
        'admin', //admin module.
        'weixinpub',
        'pay',
        /** user module * */
        /*   'user' => array(
          'tableUsers' => 'tbl_users',
          'tableProfiles' => 'tbl_profiles',
          'tableProfileFields' => 'tbl_profiles_fields',
          ),
         * 
         */
        // uncomment the following to enable the Gii tool
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'password',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
        ),
    ),
    // application components
    'components' => array(
        'mobileDetect' => array(
            'class' => 'ext.MobileDetect.MobileDetect'
        ),
        'image' => array(
            'class' => 'application.extensions.image.CImageComponent',
            // GD or ImageMagick
            'driver' => 'GD',
        ),
        'mail' => array(
            'class' => 'ext.mail.YiiMail',
            'transportType' => 'smtp',
            'transportOptions' => array(
                'host' => 'smtp.ym.163.com',
                'username' => 'noreply@mingyihz.com',
                'password' => '91466636',
                'port' => '994',
                'encryption' => 'ssl',
            // 'encryption' => 'tls',
            ),
            'viewPath' => 'application.views.mail',
            'logging' => true,
            'dryRun' => false
        ),
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true
        ),
        // User module
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'caseSensitive' => false,
            'showScriptName' => false,
            'rules' => array(
                array('api/list', 'pattern' => 'api/<model:\w+>', 'verb' => 'GET'),
                //   array('api/view', 'pattern' => 'api/<model:\w+>/<id:\d+>', 'verb' => 'GET'),
                array('api/view', 'pattern' => 'api/<model:\w+>/<id:\d+>', 'verb' => 'GET'),
                array('api/update', 'pattern' => 'api/<model:\w+>/<id:\d+>', 'verb' => 'PUT'),
                array('api/delete', 'pattern' => 'api/<model:\w+>/<id:\d+>', 'verb' => 'DELETE'),
                array('api/create', 'pattern' => 'api/<model:\w+>', 'verb' => 'POST'),
                'event/view/<page:\w+>' => 'event/view',
                '<view:(aboutus|faq|terms|media|rules|joinus|news|bigevents|zhitongche|refundAgreement|honors|help|mygy|mygyexpert)>' => 'site/page',
                '<controller:\w+>/<action:index>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        // 本地数据库
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=mytest',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'schemaCachingDuration' => 3600    // 开启表结构缓存（schema caching）提高性能
        ),
        'errorHandler' => array(
        // use 'site/error' action to display errors
        // 'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'application.log',
                    'levels' => 'error, warning',
                ),
                array(
                    'class' => 'CFileLogRoute',
                    'logFile' => 'trace.log',
                    'levels' => 'trace',
                ),
            ),
        ),
    ),
    'params' => array(
        // this is used in contact page
        'baseUrl' => '/mytest',
    ),
    'theme' => 'v1',
);

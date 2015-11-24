RBAC Manager for Yii 2
=========

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist zc/yii2-rbac "*"
php composer.phar require "yiisoft/yii2-jui": "~2.0@dev" //can't be installed via composer.json requiremtns because of DependencyResolver issue
```

or add

```json
"zc/yii2-rbac": "*"
```

to the require section of your composer.json.

Usage
------------
Once the extension is installed, simply modify your application configuration as follows:

```php
return [
    //....
    'modules' => [
        'rbac' => [
            'class' => 'zc\rbac\Module',
            //Some controller property maybe need to change.
            'controllerMap' => [
                'assignment' => [
                    'class' => 'zc\rbac\controllers\AssignmentController',
                    'userClassName' => 'path\to\models\User',
                ]
            ]
        ],
    ],

  'components' => [
        ....
        'user' => [
                    'identityClass' => 'common\models\User',//'app\models\AdminUser',用户模型
                    'enableAutoLogin' => true,
                    'loginUrl' => ['user/login'],//登录地址
                ],
         'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['guest', 'user'],
            'cache' => 'yii\caching\FileCache',
            //'itemTable' => 'AuthItem',
            //'itemChildTable' => 'AuthItemChild',
            //'assignmentTable' => 'AuthAssignment',
            //'ruleTable' => 'AuthRule',
        ],
    ]
    'as access' => [
        'class' => 'zc\rbac\components\AccessControl',
        'allowActions' => [
            '/',
            'home/*',
            'backend/reflushmenu',
            'home/captcha',
            'home/error',
            'user/logout',
            'user/login',
            //'some-controller/some-action',
            // The actions listed here will be allowed to everyone including guests.
            // So, 'admin/*' should not appear here in the production, of course.
            // But in the earlier stages of your development, you may probably want to
            // add a lot of actions here until you finally completed setting up rbac,
            // otherwise you may not even take a first step.
        ],
        'rules'        => [
            [
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'actions' => ['logout'],
                'allow' => true,
                'roles' => ['@'],
            ],
            [
                'actions' => ['error'],
                'allow'   => true,
            ],
            [
                'actions' => ['login'],
                'allow'   => true,
                'roles'   => ['?'],
            ],
        ],
    ],
];
```
you need execute rbac init migration by the following command:
```
php yii migrate/up --migrationPath=@zc/rbac/migrations
```

You can then access Auth manager through the following URL:
```
http://localhost/path/to/index.php?r=rbac/
http://localhost/path/to/index.php?r=rbac/route
http://localhost/path/to/index.php?r=rbac/permission
http://localhost/path/to/index.php?r=rbac/role
http://localhost/path/to/index.php?r=rbac/assignment
```

For applying rules add to your controller following code:
```php
use zc\rbac\components\AccessControl;

class ExampleController extends Controller 
{

/**
 * 如果在配置文件里面配置了，这里就可以不用了
 * Returns a list of behaviors that this component should behave as.
 */
public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'permissionNameIsRoute => false,// 此处很重要，默认为false，不使用路由做权限资源名，将路由写在descrption里
            ],
            'verbs' => [
                ...
            ],
        ];
    }
  // Your actionss
}
```

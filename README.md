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
        .....
        'admin' => [
            'class' => 'app\modules\admin\Module',
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
            ]
        ],
    ],
  'components' => [
        ....
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
];
```
you need execute rbac init migration by the following command:
```
php yii migrate/up --migrationPath=@zc/rbac/migrations
```

You can then access Auth manager through the following URL:
```
http://localhost/path/to/index.php?r=admin/rbac/
http://localhost/path/to/index.php?r=admin/rbac/route
http://localhost/path/to/index.php?r=admin/rbac/permission
http://localhost/path/to/index.php?r=admin/rbac/role
http://localhost/path/to/index.php?r=admin/rbac/assignment
```

For applying rules add to your controller following code:
```php
use zc\rbac\components\AccessControl;

class ExampleController extends Controller 
{

/**
 * Returns a list of behaviors that this component should behave as.
 */
public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
            ],
            'verbs' => [
                ...
            ],
        ];
    }
  // Your actionss
}
```

<?php

namespace zc\rbac\components;

use Yii;
use yii\base\InlineAction;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;
use zc\rbac\models\search\AuthItemSearch;

/**
 * Class AccessControl
 * @package zc\rbac\components
 */
class AccessControl extends \yii\filters\AccessControl
{

    /**
     * @var array
     */
    public $params = [];
    /**
     * 权限资源名是否是路由
     * 默认权限资源名不为路由，将路由写在description里
     * @var bool
     */
    public $permissionNameIsRoute = false;
    /**
     * @var array List of action that not need to check access.
     * 默认允许访问的路由
     */
    public $allowActions = [];
    /**
     * This method is invoked right before an action is to be executed (after all possible filters.)
     * You may override this method to do last-minute preparation for the action.
     * @param InlineAction $action the action to be executed.
     * @return boolean whether the action should continue to be executed.
     */
    public function beforeAction($action)
    {
        if(parent::beforeAction($action)) {
            //if ($this->permissionNameIsRoute) {//根据权限路由检查
                $actionId = $action->getUniqueId();
                if(in_array('/'.$actionId,$this->allowActions)){
                    return true;
                }
                $user = Yii::$app->getUser();
                $params = isset($this->params[$action->id]) ? $this->params[$action->id] : [];
                if ($user->can('/'.$actionId, $params)) {
                    return true;
                }
                $controller = $action->controller;
                do {
                    if(in_array('/'.ltrim($controller->getUniqueId() . '/*', '/'),$this->allowActions)){
                        return true;
                    }
                    if ($user->can('/'.ltrim($controller->getUniqueId() . '/*', '/'))) {
                        return true;
                    }
                    $controller = $controller->module;
                } while ($controller !== null);
            //} else {//根据人为设置的权限descriptioon检查
                $searchModel = new AuthItemSearch(['type' => Item::TYPE_PERMISSION]);
                $searchModel->search([]);
                $permissionsArray = ArrayHelper::getColumn($searchModel->items, 'description');

                $actionId = $action->getUniqueId();
                if(in_array($actionId,$this->allowActions)){
                    return true;
                }
                $user = Yii::$app->getUser();
                $params = isset($this->params[$action->id]) ? $this->params[$action->id] : [];
                if ($permissionStr = array_search($actionId, $permissionsArray)) {
                    if ($user->can($permissionStr, $params)) {
                        return true;
                    }
                }
                $controller = $action->controller;
                do {
                    if(in_array(ltrim($controller->getUniqueId() . '/*', '/'),$this->allowActions)){
                        return true;
                    }
                    if ($permissionStr = array_search(ltrim($controller->getUniqueId() . '/*', '/'), $permissionsArray)) {
                        if ($user->can($permissionStr)) {
                            return true;
                        }
                    }
                    $controller = $controller->module;
                } while ($controller !== null);

            //}
            if (isset($this->denyCallback)) {
                call_user_func($this->denyCallback, null, $action);
            } else {
                $this->denyAccess($user);
            }
        }
        return false ;
    }


    /**
     * Returns a value indicating whether the filer is active for the given action.
     * @param InlineAction $action the action being filtered
     * @return boolean whether the filer is active for the given action.
     */
    protected function isActive($action)
    {
        $uniqueId = $action->getUniqueId();
        if ($uniqueId === Yii::$app->getErrorHandler()->errorAction) {
            return false;
        } else if (Yii::$app->user->isGuest && Yii::$app->user->loginUrl[0] == $uniqueId) {
            return false;
        }
        return parent::isActive($action);
    }

}

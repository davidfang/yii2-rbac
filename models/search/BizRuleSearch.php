<?php

namespace zc\rbac\models\search;

use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use zc\rbac\models\BizRuleModel;

/**
 * Class BizRuleSearch
 * @package zc\rbac\models\search
 */
class BizRuleSearch extends Model
{
    /**
     * @var string name of the rule
     */
    public $name;

    /**
     * Returns the validation rules for attributes.
     *
     * Validation rules are used by [[validate()]] to check if attribute values are valid.
     * Child classes may override this method to declare different validation rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['name'], 'safe']
        ];
    }

    /**
     * Search
     *
     * @param array $params
     * @return \yii\data\ActiveDataProvider|\yii\data\ArrayDataProvider
     */
    public function search($params,$authManagerStr)
    {
        /* @var \yii\rbac\Manager $authManager */
        $authManager = Yii::$app->$authManagerStr;
        $models = [];
        $included = !($this->load($params) && $this->validate() && trim($this->name) !== '');
        foreach ($authManager->getRules() as $name => $item) {
            if ($included || stripos($item->name, $this->name) !== false) {
                $bizRule =  new BizRuleModel($item);
                $bizRule->authManagerStr = $authManagerStr;
                $models[$name] = $bizRule;
            }
        }
        return new ArrayDataProvider([
            'allModels' => $models,
        ]);
    }
}
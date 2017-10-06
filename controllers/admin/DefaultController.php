<?php

namespace aminkt\userAccounting\controllers\admin;

use aminkt\userAccounting\UserAccounting;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Default controller for the `userAccounting` module
 */
class DefaultController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $transactionModel = UserAccounting::getInstance()->transactionModel;
        $formatter = \Yii::$app->formatter;
        $locale = $formatter->locale;
        $formatter->locale = 'en-US';
        $first = $formatter->asDate('today', 'yyyy-MM-dd');
        $dataProvider = new ActiveDataProvider([
            'query' => $transactionModel::find()->where(['between', 'time', $first, new Expression("NOW()")])
        ]);
        $formatter->locale = $locale;
        return $this->render('/default/index', [
            'dataProvider' => $dataProvider
        ]);
    }
}

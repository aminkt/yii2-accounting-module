<?php

namespace aminkt\userAccounting\controllers\admin;

use aminkt\userAccounting\UserAccounting;
use yii\data\ActiveDataProvider;
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
        $dataProvider = new ActiveDataProvider([
            'query' => $transactionModel::find()->where([
                'YEAR(time)' => 'YEAR(NOW())',
                'MONTH(time)' => 'MONTH(NOW())',
                'DAY(time)' => 'DAY(NOW())'
            ])
        ]);
        return $this->render('/default/index', [
            'dataProvider' => $dataProvider
        ]);
    }
}

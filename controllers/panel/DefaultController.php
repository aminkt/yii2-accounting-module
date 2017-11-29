<?php

namespace aminkt\userAccounting\controllers\panel;


use aminkt\userAccounting\UserAccounting;
use common\widgets\alert\Alert;
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
                'userId'=>\Yii::$app->getUser()->getId()
            ])
        ]);
        return $this->render('/default/index', [
            'dataProvider'=>$dataProvider
        ]);
    }
}

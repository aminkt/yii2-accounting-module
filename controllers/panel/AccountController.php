<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 8/26/17
 * Time: 8:36 PM
 */

namespace aminkt\userAccounting\controllers\panel;


use aminkt\userAccounting\models\Account;
use aminkt\widgets\alert\Alert;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;

class AccountController extends Controller
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
        $dataProvider = new ActiveDataProvider([
            'query' => Account::find()->where([
                'userId' => \Yii::$app->getUser()->getId()
            ])->andWhere(['!=', 'status', Account::STATUS_REMOVED])
        ]);
        return $this->render('/panel/account/index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Renders the create view for the module
     * @return string
     */
    public function actionCreate()
    {
        $model = new Account();
        $model->owner = \Yii::$app->getUser()->getIdentity()->name . ' ' . \Yii::$app->getUser()->getIdentity()->family;

        if ($model->load(\Yii::$app->getRequest()->post())) {
            $model->status = $model::STATUS_WAITING;
            $model->userId = \Yii::$app->getUser()->getId();
            $model->owner = \Yii::$app->getUser()->getIdentity()->name . ' ' . \Yii::$app->getUser()->getIdentity()->family;
            if ($model->save()) {
                Alert::success('حساب شما با موفقیت ثبت شد', 'حساب شما بعد از تائید قابل استفاده میباشد.');
            } else {
                $errors = $model->errors;
                \Yii::error($errors);
                Alert::error('اطلاعات ذخیره نشد.', 'متاسفانه در ثبت اطلاعات مشکلی وجود دارد.');
            }
        }
        return $this->render('/panel/account/create', [
            'model' => $model,
        ]);
    }
}
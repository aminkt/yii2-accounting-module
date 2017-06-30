<?php

namespace aminkt\userAccounting\controllers\panel;


use aminkt\userAccounting\models\Account;
use aminkt\userAccounting\models\PayRequest;
use aminkt\userAccounting\models\PayRequestForm;
use aminkt\userAccounting\models\Transaction;
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
        $dataProvider = new ActiveDataProvider([
            'query'=>Transaction::find()->where([
                'userId'=>\Yii::$app->getUser()->getId()
            ])
        ]);
        return $this->render('/default/index', [
            'dataProvider'=>$dataProvider
        ]);
    }


    /**
     * Pay request from user to settle account.
     */
    public function actionPayRequest(){
        $model = new PayRequestForm();

        $dataProvider = new ActiveDataProvider([
            'query'=>PayRequest::find()->andWhere([
                'userId'=>\Yii::$app->getUser()->getId(),
            ])
        ]);

        if($model->load(\Yii::$app->getRequest()->post())){
            if($model->regPayRequest()){

            }else{

            }
        }

        return $this->render('/default/pay-request', [
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ]);
    }


    /**
     * Bank account management page
     */
    public function actionAccounts()
    {
        $model = new Account();
        $model->owner = \Yii::$app->getUser()->getIdentity()->name.' '.\Yii::$app->getUser()->getIdentity()->family;
        $dataProvider = new ActiveDataProvider([
            'query'=>Account::find()->andWhere(['userId'=>\Yii::$app->getUser()->getId()])
        ]);

        if($model->load(\Yii::$app->getRequest()->post())){
            $model->status = $model::STATUS_WAITING;
            $model->amountPaid = 0;
            $model->userId = \Yii::$app->getUser()->getId();
            $model->owner = \Yii::$app->getUser()->getIdentity()->name.' '.\Yii::$app->getUser()->getIdentity()->family;
            if($model->save()){
                Alert::success('حساب شما با موفقیت ثبت شد', 'حساب شما بعد از تائید قابل استفاده میباشد.');
                return $this->redirect(['accounts']);
            }else{
                $errors = $model->errors;
                \Yii::error($errors);
                Alert::error('اطلاعات ذخیره نشد.', 'متاسفانه در ثبت اطلاعات مشکلی وجود دارد.');
            }
        }
        return $this->render('/default/accounts', [
            'model'=>$model,
            'dataProvider'=>$dataProvider,
        ]);
    }
}

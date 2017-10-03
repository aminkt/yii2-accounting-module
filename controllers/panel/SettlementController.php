<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/6/17
 * Time: 1:21 PM
 */

namespace aminkt\userAccounting\controllers\panel;


use aminkt\userAccounting\models\Account;
use aminkt\userAccounting\models\Purse;
use aminkt\userAccounting\models\Settlement;
use aminkt\userAccounting\models\SettlementRequestForm;
use common\widgets\alert\Alert;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class SettlementController extends Controller
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
     * Create and list Settlement model.
     *
     * @param string $action
     *
     * @return mixed
     */
    public function actionIndex($action = "list")
    {
        $settlementRequestForm = new SettlementRequestForm();
        $userId = \Yii::$app->getUser()->getIdentity()->getId();
        $accounts = ArrayHelper::map(Account::findByUserId($userId), 'id', 'bankName');
        $purses = ArrayHelper::map(Purse::findByUserId($userId), 'id', 'name');
        $dataProvider = new ActiveDataProvider([
            'query' => Settlement::find()->where([
                'userId' => $userId
            ])
        ]);
        if ($settlementRequestForm->load(Yii::$app->request->post())) {
            $account = Account::findOne($settlementRequestForm->account);
            $purse = Purse::findOne($settlementRequestForm->purse);
            if ($account && $purse && $account->userId == $userId && $purse->userId == $userId) {
                $settlementRequestForm->account = $account->id;
                $settlementRequestForm->purse = $purse->id;
                if ($settlementRequestForm->regPayRequest()) {
                    $action = "list";
                    Alert::success('درخواست تسویه حساب برای شما با موفقیت ثبت شد', 'درخواست تسویه حساب برای شما منتظر تائید است.');
                } else {
                    $errors = $settlementRequestForm->errors;
                    \Yii::error($errors);
                    Alert::error('اطلاعات ذخیره نشد.', 'متاسفانه در ثبت اطلاعات مشکلی وجود دارد.');
                }
            }
        }
        return $this->render('/panel/settlement/index', [
            'settlementRequestForm' => $settlementRequestForm,
            'accounts' => $accounts,
            'purses' => $purses,
            'dataProvider' => $dataProvider,
            'action' => $action
        ]);
    }
}
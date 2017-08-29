<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 8/26/17
 * Time: 8:36 PM
 */

namespace aminkt\userAccounting\controllers\admin;


use aminkt\userAccounting\models\Account;
use common\widgets\alert\Alert;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use Yii;

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
            'query' => Account::find()
        ]);
        return $this->render('/admin/account/index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Updates an existing Account model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Account::SCENARIO_UPDATE);
        $updatedModel = $model;
        if ($updatedModel->load(Yii::$app->request->post())) {
            $updatedModel->userId = $model->userId;;
            $updatedModel->amountPaid = $model->amountPaid;
            if (Account::updateAccount($model, $updatedModel))
                Alert::success('حساب با موفقیت ویرایش شد.', ' ');
            else
                Alert::error('خطایی در ویرایش اطلاعات وجود دارد!', 'لطفا دوباره تلاش کنید.');
        }
        return $this->render('/admin/account/update', [
            'model' => $updatedModel,
        ]);
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Account the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Account::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('صفحه‌ای یافت نشد.');
        }
    }
}
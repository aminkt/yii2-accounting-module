<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/17/17
 * Time: 10:47 PM
 */

namespace aminkt\userAccounting\controllers\admin;


use aminkt\userAccounting\exceptions\InvalidArgumentException;
use aminkt\userAccounting\exceptions\RuntimeException;
use aminkt\userAccounting\models\Purse;
use aminkt\userAccounting\models\Settlement;
use aminkt\widgets\alert\Alert;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class PurseController extends Controller
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
     * Updates an existing Settlement model.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->accountId == 0)
                $model->accountId = null;
            if ($model->save()) {
                Alert::success('حساب با موفقیت ویرایش شد.', ' ');
            } else {
                Alert::error('خطایی در ویرایش اطلاعات وجود دارد!', 'لطفا دوباره تلاش کنید.');
            }
        }
        return $this->render('/admin/purse/update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Purse the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Purse::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('صفحه‌ای یافت نشد.');
        }
    }

    public function actionSettlement($id)
    {
        $model = $this->findModel($id);
        $amount = $model->getAmount();
        try {
            $settlement = Settlement::createSettlementRequest(
                $amount,
                $model,
                $model->accountId,
                'تسویه کیف پول'
            );
            Alert::success("درخواست تسویه ایجاد شد.",
                'مبلغ: ' . $settlement->amount . ' تومان <br>' .
                'حساب بانکی: ' . $model->account->bankName
            );
        } catch (InvalidArgumentException $exception) {
            Alert::error("در تسویه کیف پول مشکلی به وجود امده است.", 'موجودی کیف پول کمتر یا بیشتر از مقدار مجاز است.');
            Yii::error($exception, self::className());
        } catch (RuntimeException $exception) {
            Alert::error("در تسویه کیف پول مشکلی به وجود امده است.", $exception->getMessage());
            Yii::error($exception, self::className());
        }
        return $this->actionIndex();
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Purse::find()
        ]);
        return $this->render('/admin/purse/index', [
            'dataProvider' => $dataProvider
        ]);
    }
}


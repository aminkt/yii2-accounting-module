<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/17/17
 * Time: 10:47 PM
 */

namespace aminkt\userAccounting\controllers\admin;


use aminkt\userAccounting\models\Settlement;
use common\widgets\alert\Alert;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use Yii;

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
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Settlement::find()
        ]);
        return $this->render('/admin/settlement/index', [
            'dataProvider' => $dataProvider
        ]);
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
            if ($model->save()) {
                Alert::success('حساب با موفقیت ویرایش شد.', ' ');
            } else {
                Alert::error('خطایی در ویرایش اطلاعات وجود دارد!', 'لطفا دوباره تلاش کنید.');
            }
        }
        return $this->render('/admin/settlement/update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Account model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Settlement the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Settlement::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('صفحه‌ای یافت نشد.');
        }
    }
}


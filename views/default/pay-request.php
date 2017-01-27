<?php


/* @var $this yii\web\View */
/* @var $model \userAccounting\models\PayRequestForm */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'درخواست های تسویه حساب';
$this->params['description']='لیست درخواست های تسویه حساب.';
$this->params['breadcrumbs'][] = ['label' => 'امور مالی', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-8">
        <!-- BEGIN Portlet PORTLET-->
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-list font-yellow-casablanca"></i>
                    <span class="caption-subject bold font-yellow-casablanca uppercase"> لیست درخواست ها </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                echo \yii\grid\GridView::widget([
                    'dataProvider'=>$dataProvider,
                    'columns'=>[
                        'accountName',
                        'amount',
                        'bankTrackingCode',
                        [
                            'attribute'=>'payTime',
                            'format'=>'raw',
                            'value'=>function($model){
                                /** @var $model \userAccounting\models\PayRequest */
                                $formatter = Yii::$app->formatter;
                                return $formatter->asDatetime($model->payTime, 'dd MMMM YY ساعت hh:mm');
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                /** @var $model \userAccounting\models\PayRequest */
                                if ($model->status == $model::STATUS_WAITING)
                                    return 'در انتظار تائید';
                                elseif ($model->status == $model::STATUS_CONFIRMED)
                                    return 'تائید شده';
                                elseif ($model->status == $model::STATUS_REJECTED)
                                    return 'عدم احراز صلاحیت';

                                return null;
                            }
                        ],
                        [
                            'attribute'=>'createTime',
                            'value'=>function($model){
                                /** @var $model \userAccounting\models\PayRequest */
                                $formatter = Yii::$app->formatter;
                                return $formatter->asDatetime($model->createTime, 'dd MMMM YY ساعت hh:mm');
                            }
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
        <!-- END Portlet PORTLET-->
    </div>
    <div class="col-md-4">
        <!-- BEGIN Portlet PORTLET-->
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-plus font-yellow-casablanca"></i>
                    <span class="caption-subject bold font-yellow-casablanca uppercase"> ثبت درخواست جدید </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body">
                    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

                    <?= $form->field($model, 'amount')->textInput() ?>

                    <?php
                    $accounts = \userAccounting\models\Account::find()->where([
                        'userId'=>Yii::$app->getUser()->getId(),
                        'status'=>\userAccounting\models\Account::STATUS_CONFIRMED
                    ])->asArray()->all();
                    $items = \yii\helpers\ArrayHelper::map($accounts, 'id', 'bankName');
                    echo $form->field($model, 'account')->dropDownList($items);
                    ?>

                    <?= \yii\bootstrap\Html::submitButton('ثبت درخواست', ['class'=>'btn btn-primary']) ?>

                    <?php \yii\bootstrap\ActiveForm::end(); ?>
            </div>
        </div>
        <!-- END Portlet PORTLET-->
    </div>
</div>
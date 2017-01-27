<?php


/* @var $this yii\web\View */
/* @var $model \userAccounting\models\Account */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'حساب های بانکی';
$this->params['description']='لیست حساب های بانکی.';
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
                    <span class="caption-subject bold font-yellow-casablanca uppercase"> لیست حساب های شما </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                echo \yii\grid\GridView::widget([
                    'dataProvider'=>$dataProvider,
                    'columns'=>[
                        'bankName',
                        'cardNumber',
                        'accountNumber',
                        'shaba',
                        'amountPaid',
                        [
                            'attribute' => 'status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                /** @var $model \userAccounting\models\Account */
                                if ($model->status == $model::STATUS_WAITING)
                                    return 'در انتظار تائید';
                                elseif ($model->status == $model::STATUS_CONFIRMED)
                                    return 'تائید شده';
                                elseif ($model->status == $model::STATUS_BLOCKED)
                                    return 'مسدود شده';
                                elseif ($model->status == $model::STATUS_DEACTIVATE)
                                    return 'غیرفعال';
                                elseif ($model->status == $model::STATUS_REJECTED)
                                    return 'عدم احراز صلاحیت';

                                return null;
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
                    <span class="caption-subject bold font-yellow-casablanca uppercase"> ثبت حساب جدید </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body">
                    <?php $form = \yii\bootstrap\ActiveForm::begin(); ?>

                    <?= $form->field($model, 'bankName')->textInput() ?>
                    <?= $form->field($model, 'cardNumber')->textInput(['style'=>'direction:ltr']) ?>
                    <?= $form->field($model, 'accountNumber')->textInput(['style'=>'direction:ltr']) ?>
                    <?= $form->field($model, 'shaba')->textInput(['style'=>'direction:ltr']) ?>
                    <?= $form->field($model, 'owner')->textInput(['disabled'=>true]) ?>

                    <?= \yii\bootstrap\Html::submitButton('ثبت حساب', ['class'=>'btn btn-primary']) ?>

                    <?php \yii\bootstrap\ActiveForm::end(); ?>
            </div>
        </div>
        <!-- END Portlet PORTLET-->
    </div>
</div>
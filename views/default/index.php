<?php


/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'لیست تراکنش ها';
$this->params['description']='لیست تراکنش های انجام شده';
$this->params['breadcrumbs'][] = ['label' => 'امور مالی', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <!-- BEGIN Portlet PORTLET-->
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-list font-yellow-casablanca"></i>
                    <span class="caption-subject bold font-yellow-casablanca uppercase"> لیست تراکنش های حساب </span>
                    <span class="caption-helper"></span>
                </div>
            </div>
            <div class="portlet-body">
                <?php
                echo \yii\grid\GridView::widget([
                    'dataProvider'=>$dataProvider,
                    'rowOptions'=>function($model, $key, $index, $grid){
                        if($model->amount<0)
                            return [
                                'class'=>'danger'
                            ];
                    },
                    'columns'=>[
                        'amount',
                        'description:text',
                        [
                            'attribute' => 'type',
                            'format' => 'raw',
                            'value' => function ($model) {
                                /** @var $model \userAccounting\models\Transaction */
                                if ($model->type == $model::TYPE_PAY_REQUEST)
                                    return 'درخواست تسویه';
                                elseif ($model->type == $model::TYPE_GIFT)
                                    return 'هدیه';
                                elseif ($model->type == $model::TYPE_BUY)
                                    return 'خرید';
                                elseif ($model->type == $model::TYPE_CHARGE_ACCOUNT)
                                    return 'شارژ حساب کاربری';
                                elseif ($model->type == $model::TYPE_REJECT_PAY_REQUEST)
                                    return 'رد درخواست تسویه';
                                elseif ($model->type == $model::TYPE_SALE)
                                    return 'فروش';
                                elseif ($model->type == $model::TYPE_UNKNOWN)
                                    return 'نا مشخص';

                                return null;
                            }
                        ],
                        [
                            'attribute'=>'time',
                            'value'=>function($model){
                                /** @var $model \userAccounting\models\Transaction */
                                $formatter = Yii::$app->formatter;
                                return $formatter->asDatetime($model->time, 'dd MMMM YY ساعت hh:mm');
                            }
                        ],
                    ]
                ]);
                ?>
            </div>
        </div>
        <!-- END Portlet PORTLET-->
    </div>
</div>
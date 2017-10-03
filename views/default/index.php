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
                                /** @var $model \aminkt\userAccounting\interfaces\TransactionInterface */
                                if ($model->getType() == $model::TYPE_SETTLEMENT_REQUEST_WITHDRAW)
                                    return 'درخواست تسویه';
                                elseif ($model->getType() == $model::TYPE_SETTLEMENT_REQUEST_REJECTED)
                                    return 'رد درخواست تسویه';
                                elseif ($model->getType() == $model::TYPE_GIFT)
                                    return 'هدیه';
                                elseif ($model->getType() == $model::TYPE_NORMAL)
                                    return 'عادی';

                                return null;
                            }
                        ],
                        [
                            'attribute'=>'time',
                            'value'=>function($model){
                                /** @var $model \aminkt\userAccounting\models\Transaction */
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
<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/18/17
 * Time: 3:55 PM
 */

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'لیست کیف های پول کاربران';
$this->params['des'] = 'از این بخش میتوانید کیف های پول کاربران را مدیریت کنید';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="portlet light portlet-fit ">
    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-blue-madison bold uppercase"><?= Html::encode($this->title) ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="table-toolbar">
            <div class="row">
                <div class="col-md-6">
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'name',
                    [
                        'attribute' => 'user',
                        'format' => 'raw',
                        'label' => 'کابر',
                        'value' => function ($model) {
                            /** @var $model \aminkt\userAccounting\models\Account */
                            return $model->user->getFullName() . ' (' . $model->user->getId() . ')<br>' . $model->user->getMobile();
                        }
                    ],
                    [
                        'attribute' => 'accountId',
                        'format' => 'raw',
                        'value' => function ($model) {
                            /* @var $model aminkt\userAccounting\models\Purse */
                            return $model->account ? $model->account->bankName . ' (' . $model->accountId . ')' : 'تنظیم نشده';
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'وضعیت',
                        'value' => function ($model) {
                            /* @var $model aminkt\userAccounting\models\Purse */
                            switch ($model->status) {
                                case $model::STATUS_CONFIRMED:
                                    return 'تائید شده';
                                    break;
                                case $model::STATUS_BLOCKED:
                                    return 'مسدود شده';
                                    break;
                                case $model::STATUS_WAITING:
                                    return 'در انتظار نائید';
                                    break;
                                case $model::STATUS_REMOVED:
                                    return 'حذف شده';
                                    break;
                            }
                            return null;
                        }
                    ],
                    [
                        'attribute' => 'autoSettlement',
                        'label' => 'تسویه خودکار',
                        'value' => function ($model) {
                            /* @var $model aminkt\userAccounting\models\Purse */
                            return $model->autoSettlement ? 'فعال' : 'غیر فعال';
                        }
                    ],
                    [
                        'label' => 'موجودی',
                        'value' => function ($model) {
                            /* @var $model aminkt\userAccounting\models\Purse */
                            return $model->getAmount() . ' تومان';
                        }
                    ],

                    'createTime:datetime',
                    'updateTime:datetime',
                    [
                        'format' => 'raw',
                        'value' => function ($model) {
                            $buttons = Html::a('<i class="fa fa-pencil"></i>' . ' ویرایش', \yii\helpers\Url::toRoute(['purse/update', 'id' => $model->id]), ['class' => 'btn green']);
                            $buttons2 = Html::a('<i class="fa fa-credit-card"></i>' . ' درخواست تسویه', \yii\helpers\Url::toRoute(['purse/settlement', 'id' => $model->id]), ['class' => 'btn yellow']);
                            return $buttons . $buttons2;
                        },
                    ],
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if ($model->status == \aminkt\userAccounting\models\Purse::STATUS_WAITING)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == \aminkt\userAccounting\models\Purse::STATUS_CONFIRMED)
                        return [
                            'class' => 'success'
                        ];
                    elseif ($model->status == \aminkt\userAccounting\models\Purse::STATUS_REMOVED)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == \aminkt\userAccounting\models\Purse::STATUS_BLOCKED)
                        return [
                            'class' => 'danger'
                        ];

                    return null;
                },
            ]); ?>
        </div>

    </div>
</div>

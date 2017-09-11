<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/6/17
 * Time: 1:36 PM
 */
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
use \aminkt\userAccounting\models\Settlement;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
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
                    <div class="btn-group">
                        <?= Html::a('ایجاد حساب <i class="fa fa-plus"></i>', ['account/create'], ['class' => 'btn green']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'account',
                        'format' => 'raw',
                        'value' => function ($model) {
                            /* @var $model aminkt\userAccounting\models\Settlement */
                            return $model->account->accountNumber;
                        }
                    ],
                    [
                        'attribute' => 'purse',
                        'format' => 'raw',
                        'value' => function ($model) {
                            /* @var $model aminkt\userAccounting\models\Settlement */
                            return $model->purse->name;
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->status == Settlement::STATUS_WAITING)
                                return 'در انتظار تائید';
                            elseif ($model->status == Settlement::STATUS_CONFIRMED)
                                return 'تائید شده';
                            elseif ($model->status == Settlement::STATUS_REMOVED)
                                return 'حذف شده';
                            elseif ($model->status == Settlement::STATUS_BLOCKED)
                                return 'مسدود شده';
                            elseif ($model->status == Settlement::STATUS_REJECTTED)
                                return 'عدم احراز صلاحیت';
                            return null;
                        }
                    ],
                    'settlementType',
                    'description',
                    'operatorNote',
                    'settlementTime',
                    'createTime',
                    'updateTime',
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if ($model->status == Settlement::STATUS_WAITING)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == Settlement::STATUS_CONFIRMED)
                        return [
                            'class' => 'success'
                        ];
                    elseif ($model->status == Settlement::STATUS_REMOVED)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == Settlement::STATUS_BLOCKED)
                        return [
                            'class' => 'danger'
                        ];
                    elseif ($model->status == Settlement::STATUS_REJECTTED)
                        return [
                            'class' => 'danger'
                        ];

                    return null;
                },
            ]); ?>
            <?php Pjax::end(); ?>
        </div>

    </div>
</div>


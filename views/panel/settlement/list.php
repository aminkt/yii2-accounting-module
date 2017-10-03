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
    <div class="portlet-body">
        <div class="table-responsive">
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
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
                        'value' => 'statusLabel',
                    ],
                    [
                        'attribute' => 'settlementType',
                        'value' => 'settlementTypeLabel',
                    ],
                    'amount',
                    'settlementTime:datetime',
                    'createTime:datetime',
                    'updateTime:datetime',
                    'operatorNote:ntext',
                    'description:ntext',
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
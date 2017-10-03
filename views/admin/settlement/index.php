<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/18/17
 * Time: 3:55 PM
 */

use \aminkt\userAccounting\models\Settlement;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \aminkt\userAccounting\models\Settlement */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'درخواست های تسویه حساب کاربران';
$this->params['des'] = 'از این بخش میتوانید درخواست های تسویه حساب کاربران را مدیریت کنید';
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
                    [
                        'format' => 'raw',
                        'value' => function ($model) {
                            $buttons = Html::a('<i class="fa fa-pencil"></i>' . ' ویرایش', \yii\helpers\Url::toRoute(['settlement/update', 'id' => $model->id]), ['class' => 'btn green btn-block']);
                            return $buttons;
                        },
                    ],
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

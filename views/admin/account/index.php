<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 8/26/17
 * Time: 8:36 PM
 */

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \aminkt\userAccounting\models\Account */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'لیست حساب های کاربران';
$this->params['des'] = 'از این بخش میتوانید حساب های خود را مدیریت کنید';
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
                    [
                        'attribute' => 'user',
                        'format' => 'raw',
                        'label' => 'کابر',
                        'value' => function ($model) {
                            /** @var $model \aminkt\userAccounting\models\Account */
                            return $model->user->getFullName() . ' (' . $model->user->getId() . ')<br>' . $model->user->getMobile();
                        }
                    ],
                    'bankName',
                    'cardNumber',
                    'accountNumber',
                    'shaba',
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            /** @var $model \aminkt\userAccounting\models\Account */
                            if ($model->status == $model::STATUS_WAITING)
                                return 'در انتظار تائید';
                            elseif ($model->status == $model::STATUS_CONFIRMED)
                                return 'تائید شده';
                            elseif ($model->status == $model::STATUS_BLOCKED)
                                return 'مسدود شده';
                            elseif ($model->status == $model::STATUS_REMOVED)
                                return 'حذف شده';

                            return null;
                        }
                    ],
                    [
                        'format' => 'raw',
                        'value' => function ($model) {
                            $buttons = Html::a('<i class="fa fa-pencil"></i>' . ' ویرایش', \yii\helpers\Url::toRoute(['account/update', 'id' => $model->id]), ['class' => 'btn green btn-block']);
                            return $buttons;
                        },
                    ],
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    /** @var $model \aminkt\userAccounting\models\Account */
                    if ($model->status == $model::STATUS_WAITING)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == $model::STATUS_BLOCKED)
                        return [
                            'class' => 'danger'
                        ];
                    elseif ($model->status == $model::STATUS_REMOVED)
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

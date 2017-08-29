<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 8/26/17
 * Time: 8:36 PM
 */

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\Account */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'لیست حساب بانکی';
$this->params['des'] = 'از این بخش میتوانید حساب های خود را مدیریت کنید';
$this->params['breadcrumbs'][] = ['label' => 'امورمالی', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'حساب بانکی', 'url' => ['index']];
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
                ],
                'rowOptions' => function ($model, $key, $index, $grid) {
                    if ($model->status == $model::STATUS_WAITING)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == $model::STATUS_CONFIRMED)
                        return [
                            'class' => 'success'
                        ];
                    elseif ($model->status == $model::STATUS_BLOCKED)
                        return [
                            'class' => 'warning'
                        ];
                    elseif ($model->status == $model::STATUS_DEACTIVATE)
                        return [
                            'class' => 'danger'
                        ];
                    elseif ($model->status == $model::STATUS_REJECTED)
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

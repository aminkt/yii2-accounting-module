<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/18/17
 * Time: 5:36 PM
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\Settlement */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'form-horizontal form-bordered',
    ]
]); ?>
<div class="form-body" style="    display: block;
    float: right;
    width: 50%;">

    <?= $form->field($model, 'bankTrackingCode', [
        'template' => '{label}<div class="col-md-9">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

    <?= $form->field($model, 'status', [
        'template' => '{label}<div class="col-md-9">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->dropDownList(
        [
            $model::STATUS_CONFIRMED => 'تائید شده',
            $model::STATUS_REJECTTED => 'رد درخواست',
            $model::STATUS_BLOCKED => 'مسدود شده',
        ]
    ) ?>

    <?= $form->field($model, 'operatorNote', [
        'template' => '{label}<div class="col-md-9">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textarea(['maxlength' => true, 'class' => 'form-control']) ?>

</div>
<div style="    display: block;
    float: left;
    width: 40%; padding-left: 10px;"><h3>مشخصات حساب</h3>
    <?php
    echo \yii\widgets\DetailView::widget([
        'model' => $model->account,
        'attributes' => [
            [
                'label' => 'مبلغ',
                'value' => $model->amount . ' تومان',
            ],
            [
                'label' => 'وضعیت',
                'value' => $model->getStatusLabel(),
            ],
            [
                'label' => 'شکل تسویه',
                'value' => $model->settlementType == $model::TYPE_CART_TO_CART ? 'کارت به کارت' : 'شبا',
            ],
            [
                'label' => 'توضیحات',
                'value' => $model->description,
                'format' => 'ntext'
            ],
            'bankName',
            'owner',
            [
                'attribute' => 'shaba',
                'contentOptions' => ['style' => 'direction:ltr; text-align:right'],
            ],
            [
                'attribute' => 'cardNumber',
                'contentOptions' => ['style' => 'direction:ltr; text-align:right'],
            ],
            [
                'attribute' => 'accountNumber',
                'contentOptions' => ['style' => 'direction:ltr; text-align:right'],
            ],
            'updateTime:datetime',
            'createTime:datetime',
        ],
    ])
    ?></div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton('<i class="fa fa-check"></i> ویرایش درخواست تسویه حساب', ['class' => 'btn green']) ?>
            <?= Html::button('<i class="fa fa-remove"></i> عدم ویرایش', ['class' => 'btn grey-salsa']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

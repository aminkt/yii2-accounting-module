<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\Purse */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'form-horizontal form-bordered',
    ]
]); ?>
<div class="form-body">
    <?= $form->field($model, 'name', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>

    <?php
    $accountLists = \yii\helpers\ArrayHelper::map(
        \aminkt\userAccounting\models\Account::find()->where(['userId' => $model->getUserId()])->asArray()->all(),
        'id',
        'bankName'
    );
    $accountLists = array_merge([0 => 'هیچ کدام'], $accountLists)
    ?>
    <?= $form->field($model, 'accountId', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->dropDownList(
        $accountLists
    ) ?>

    <?= $form->field($model, 'autoSettlement', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->dropDownList([
        $model::AUTO_SETTLEMENT_OFF => 'غیرفعال',
        $model::AUTO_SETTLEMENT_ON => 'فعال'
    ]) ?>

    <?= $form->field($model, 'status', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->dropDownList([
        $model::STATUS_WAITING => 'در انتظار تائید',
        $model::STATUS_CONFIRMED => 'تائید شده',
        $model::STATUS_BLOCKED => 'مسدود شده',
        $model::STATUS_REMOVED => 'حذف شده',
    ]) ?>

    <?= $form->field($model, 'description', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textarea(['maxlength' => true, 'class' => 'form-control']) ?>

    <?= $form->field($model, 'operatorNote', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textarea(['maxlength' => true, 'class' => 'form-control']) ?>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton('<i class="fa fa-check"></i> ویرایش درخواست تسویه حساب', ['class' => 'btn green']) ?>
            <?= Html::button('<i class="fa fa-remove"></i> عدم ویرایش', ['class' => 'btn grey-salsa']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

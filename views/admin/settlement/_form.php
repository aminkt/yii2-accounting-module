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
<div class="form-body">
    <?= $form->field($model, 'accountName', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'amount', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'bankTrackingCode', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'settlementType', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->dropDownList(
        $model::getSettlementTypeList()
    ) ?>
    <?= $form->field($model, 'status', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->dropDownList(
        $model::getStatusList()
    ) ?>
    <?= $form->field($model, 'operatorNote', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textarea(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'description', [
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

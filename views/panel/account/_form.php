<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 8/27/17
 * Time: 3:19 PM
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\Account */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'options' => [
        'class' => 'form-horizontal form-bordered',
    ]
]); ?>
<div class="form-body">
    <?= $form->field($model, 'owner', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control', 'readonly' => true, 'disabled' => true]) ?>
    <?= $form->field($model, 'bankName', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'accountNumber', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'cardNumber', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
    <?= $form->field($model, 'shaba', [
        'template' => '{label}<div class="col-md-4">{input}<span class="help-block">{hint}{error}</span></div>',
        'labelOptions' => ['class' => 'control-label col-md-3']
    ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
</div>
<div class="form-actions">
    <div class="row">
        <div class="col-md-offset-3 col-md-9">
            <?= Html::submitButton('<i class="fa fa-check"></i> ثبت حساب', ['class' => 'btn green']) ?>
            <?= Html::a('<i class="fa fa-remove"></i> لغو', ['/userAccounting/account/index'], ['class' => 'btn grey-salsa']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

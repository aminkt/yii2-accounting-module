<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/6/17
 * Time: 1:37 PM
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use aminkt\userAccounting\models\Settlement;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\SettlementRequestForm */
/* @var $form yii\widgets\ActiveForm */
/* @var $accounts aminkt\userAccounting\models\Account[] */
/* @var $purses aminkt\userAccounting\models\Purse[] */
?>

<div class="row">
    <div class="cols-xs-12 cols-sm-12 col-md-8">
        <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'amount', [
            'template' => '{label}{input}<span class="help-block">{hint}{error}</span>',
            'labelOptions' => ['class' => 'control-label']
        ])->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
        <?= $form->field($model, 'type', [
            'template' => '{label}{input}',
            'labelOptions' => ['class' => 'control-label']
        ])->dropDownList([
            Settlement::TYPE_CART_TO_CART => 'کارت به کارت',
            Settlement::TYPE_SHABA => 'شماره شبا',
        ]) ?>
        <?= $form->field($model, 'account')->dropDownList($accounts) ?>
        <?= $form->field($model, 'purse')->dropDownList($purses) ?>
        <?= $form->field($model, 'description')->textarea(); ?>
        <?= Html::submitButton('<i class="fa fa-check"></i> درخواست تسویه', ['class' => 'btn green']) ?>
        <?= Html::button('<i class="fa fa-remove"></i> عدم درخواست', ['class' => 'btn grey-salsa']) ?>
        <?php $form = ActiveForm::end(); ?>

    </div>
</div>
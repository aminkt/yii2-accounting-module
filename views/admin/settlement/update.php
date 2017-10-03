<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/18/17
 * Time: 5:29 PM
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\Settlement */
$this->title = 'ویرایش درخواست تسویه حساب بانکی کاربر';
$this->params['des'] = 'از این بخش میتوانید درخواست های تسویه حساب بانکی کاربر را ویرایش کنید';

$this->params['breadcrumbs'][] = ['label' => 'امورمالی', 'url' => ['/userAccounting']];
$this->params['breadcrumbs'][] = ['label' => 'حساب بانکی', 'url' => ['/userAccounting/settlement/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="portlet light form-fit ">
    <div class="portlet-title tabbable-line">
        <div class="caption caption-md">
            <i class="icon-globe theme-font hide"></i>
            <span class="caption-subject font-blue-madison bold uppercase"><?= Html::encode($this->title) ?></span>
        </div>
    </div>
    <div class="portlet-body form">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>

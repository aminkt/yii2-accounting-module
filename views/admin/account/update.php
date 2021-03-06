<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 8/27/17
 * Time: 2:49 PM
 */

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model aminkt\userAccounting\models\Account */
$this->title = 'ویرایش حساب بانکی کاربر';
$this->params['des'] = 'از این بخش میتوانید حساب های بانکی کاربر را ویرایش کنید';

$this->params['breadcrumbs'][] = ['label' => 'امورمالی', 'url' => ['/userAccounting']];
$this->params['breadcrumbs'][] = ['label' => 'حساب بانکی', 'url' => ['/userAccounting/account/index']];
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

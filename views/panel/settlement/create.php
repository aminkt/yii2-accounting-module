<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/6/17
 * Time: 1:37 PM
 */
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $settlementRequestForm aminkt\userAccounting\models\SettlementRequestForm */
/* @var $accounts aminkt\userAccounting\models\Account[] */
/* @var $purses aminkt\userAccounting\models\Purse[] */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'درخواست تسویه حساب';
$this->params['des'] = 'از این بخش میتوانید درخواست تسویه حساب خود را مدیریت کنید';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="profile-content">
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="icon-globe theme-font hide"></i>
                        <span class="caption-subject font-blue-madison bold uppercase"><?= Html::encode($this->title) ?></span>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a aria-expanded="true" href="#tab_1_1" data-toggle="tab">درخواست تسویه حساب</a>
                        </li>
                        <li class="">
                            <a aria-expanded="false" href="#tab_1_2" data-toggle="tab">در خواست های تسویه قبلی</a>
                        </li>
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_1_1">
                            <?= Yii::$app->controller->renderPartial('/panel/settlement/_form', [
                                'model' => $settlementRequestForm,
                                'accounts' => $accounts,
                                'purses' => $purses
                            ]); ?>
                        </div>
                        <div class="tab-pane" id="tab_1_2">
                            <?= Yii::$app->controller->renderPartial('/panel/settlement/index', [
                                'dataProvider' => $dataProvider
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

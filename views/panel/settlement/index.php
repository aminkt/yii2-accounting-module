<?php
/**
 * Created by PhpStorm.
 * User: winkers
 * Date: 9/6/17
 * Time: 1:37 PM
 */
use yii\helpers\Html;

function setActiveTab($name, $tab)
{
    if ($name == $tab)
        return "active";
    return "";
}

/* @var $this yii\web\View */
/* @var $settlementRequestForm aminkt\userAccounting\models\SettlementRequestForm */
/* @var $accounts aminkt\userAccounting\models\Account[] */
/* @var $purses aminkt\userAccounting\models\Purse[] */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $action string */

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
                        <li class="<?= setActiveTab('create', $action) ?>">
                            <a aria-expanded="false" href="#tab_1_1" data-toggle="tab">درخواست تسویه حساب</a>
                        </li>
                        <li class="<?= setActiveTab('list', $action) ?>">
                            <a aria-expanded="true" href="#tab_1_2" data-toggle="tab">در خواست های تسویه قبلی</a>
                        </li>
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane <?= setActiveTab('create', $action) ?>" id="tab_1_1">
                            <?= Yii::$app->controller->renderPartial('/panel/settlement/_form', [
                                'model' => $settlementRequestForm,
                                'accounts' => $accounts,
                                'purses' => $purses
                            ]); ?>
                        </div>
                        <div class="tab-pane <?= setActiveTab('list', $action) ?>" id="tab_1_2">
                            <?= Yii::$app->controller->renderPartial('/panel/settlement/list', [
                                'dataProvider' => $dataProvider
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$createUrl = \yii\helpers\Url::to(['settlement/index', 'action' => 'create']);
$listUrl = \yii\helpers\Url::to(['settlement/index', 'action' => 'list']);
$js = <<<JS
    $(".nav.nav-tabs a").click(function (e) {
        var link = this.href.slice(-7);
        if (link === "tab_1_1") {
            window.location.replace("$createUrl");
        } else if (link === "tab_1_2") {
            window.location.replace("$listUrl");
        }
    })
JS;
$this->registerJs($js);
?>




<?php

namespace aminkt\userAccounting;
use userAccounting\controllers\panel\DefaultController;

/**
 * userAccounting module definition class
 */
class UserAccounting extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'userAccounting\controllers\panel';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::configure($this, require( __DIR__.DIRECTORY_SEPARATOR.'config.php'));
        $this->controllerMap = [
            'account'=>DefaultController::className(),
        ];
    }
}

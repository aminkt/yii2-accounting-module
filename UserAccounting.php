<?php

namespace aminkt\userAccounting;

/**
 * userAccounting module definition class
 */
class UserAccounting extends \yii\base\Module
{
    const ADMIN_CONTROLLER_NAMESPACE = 'aminkt\userAccounting\controllers\admin';
    const PANEL_CONTROLLER_NAMESPACE = 'aminkt\userAccounting\controllers\panel';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'aminkt\userAccounting\controllers\panel';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::configure($this, require( __DIR__.DIRECTORY_SEPARATOR.'config.php'));
    }
}

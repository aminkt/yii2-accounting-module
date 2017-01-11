<?php

namespace userAccounting\controllers\panel;

use yii\web\Controller;

/**
 * Default controller for the `userAccounting` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('/default/index');
    }
}

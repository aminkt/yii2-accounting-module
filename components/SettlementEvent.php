<?php

namespace aminkt\userAccounting\components;

use yii\base\Event;

class SettlementEvent extends Event
{
    /** @var null|\aminkt\userAccounting\models\Settlement $settlement */
    public $settlement = null;

    /**
     * Return settlement model.
     *
     * @return null|\aminkt\userAccounting\models\Settlement
     */
    public function getSettlement()
    {
        return $this->settlement;
    }

    /**
     * Set settlement model.
     *
     * @param \aminkt\userAccounting\models\Settlement $settlement
     *
     * @return $this
     */
    public function setSettlement($settlement)
    {
        $this->settlement = $settlement;
        return $this;
    }


}
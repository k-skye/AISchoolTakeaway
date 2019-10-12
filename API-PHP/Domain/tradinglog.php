<?php
namespace App\Domain;

use App\Model\tradinglog as ModelTradinglog;

class tradinglog {

     public function getOnesAllTradLog($deliverID,$offset,$limit) {
        $model = new ModelTradinglog();
        return $model->getOnesAllTradLog($deliverID,$offset,$limit);
    }

}

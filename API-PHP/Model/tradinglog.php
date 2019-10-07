<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class tradinglog extends NotORM {

    public function getOneLogsNotDone($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->where('done = ?', 0)
            ->fetchAll();
    }

    public function getOneLogsMonthDone($deliverID,$date) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->where("date >= '".$date."'")
            ->fetchAll();
    }

}

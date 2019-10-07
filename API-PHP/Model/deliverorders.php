<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class deliverorders extends NotORM {

    public function getOneOrderCountByID($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->count();
    }

    public function getOneOrderCountCanComment($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->where('canComment = ?', 1)
            ->count();
    }

}

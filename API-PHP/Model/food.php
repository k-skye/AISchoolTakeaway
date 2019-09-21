<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class food extends NotORM {

    public function getCate($restID) {
        return $this->getORM()
            ->select('category')
            ->where('restID = ?', $restID)
            ->fetchAll();
    }

    public function getFoods($cate) {
        return $this->getORM()
            ->where('category = ?', $cate)
            ->fetchAll();
    }

    public function getFoodsByRestID($restID) {
        return $this->getORM()
            ->where('restID = ?', $restID)
            ->fetchAll();
    }
}

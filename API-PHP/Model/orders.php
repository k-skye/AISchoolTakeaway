<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class orders extends NotORM {

    public function getOnesAllOrders($id,$offset,$limit) {
        return $this->getORM()
            ->where('id >= ?', $offset)
            ->where('userID = ?', $id)
            ->limit($limit)
            ->fetchAll();
    }
}

<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class help extends NotORM {

    public function getDeliverAllHelp() {
        return $this->getORM()
        ->where('inwhere', 1)
        ->fetchAll();
    }

    public function getUserAllHelp() {
        return $this->getORM()
        ->where('inwhere', 0)
        ->fetchAll();
    }

}

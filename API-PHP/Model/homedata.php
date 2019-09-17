<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class homedata extends NotORM {

    public function getHeadAdImg() {
        return $this->getORM()
            ->where('id', 1)
            ->fetch('data');
    }
    
}

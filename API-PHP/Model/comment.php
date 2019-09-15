<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class comment extends NotORM {

    public function getSomeComment($restID,$offset,$limit) {
        return $this->getORM()
            ->where('restID = ?', $restID)
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

}

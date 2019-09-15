<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class restaurant extends NotORM {

    public function getSomeRest($offset,$limit) {
        return $this->getORM()
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }
    
    public function getOneRest($id) {
        return $this->getORM()
            ->where('id = ?', $id)
            ->fetchOne();
    }
}

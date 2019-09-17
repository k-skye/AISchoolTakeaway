<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class restaurant extends NotORM {

    public function getRestsInNormal($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->order('stars')
            ->order('salesNum DESC')
            ->fetchAll();
    }

    public function getRestsInSale($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->order('salesNum DESC')
            ->fetchAll();
    }

    public function getRestsInStars($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->order('stars DESC')
            ->fetchAll();
    }
    
    public function getOneRest($id) {
        return $this->getORM()
            ->where('id = ?', $id)
            ->fetchOne();
    }
}

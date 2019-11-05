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

    public function getFoods($cate,$restID) {
        return $this->getORM()
            ->where('category = ?', $cate)
            ->where('restID = ?', $restID)
            ->fetchAll();
    }

    public function getFoodsByRestID($restID) {
        return $this->getORM()
            ->where('restID = ?', $restID)
            ->fetchAll();
    }

    public function searchFoodName($text) {
        $text = '%'.$text.'%';
        return $this->getORM()
        ->where('name LIKE ?', $text)
        ->fetchAll();
    }

    public function getOneFoodByID($foodID) {
        return $this->getORM()
            ->where('id = ?', $foodID)
            ->fetchAll();
    }

    public function updateSalesNum($ID,$salesNum) {
        $data = array('salesNum' => $salesNum);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    }
}

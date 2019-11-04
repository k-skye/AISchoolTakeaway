<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class deliverorders extends NotORM {

    public function getOneOrderCountByID($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->count();
    }

    public function getOneOrderByID($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->fetchOne();
    }

    public function getOneByID($ID) {
        return $this->getORM()
            ->where('ID = ?', $ID)
            ->fetchOne();
    }

    public function getOneOrderByorderID($orderID) {
        return $this->getORM()
            ->where('orderID = ?', $orderID)
            ->fetchOne();
    }

    public function getOneOrderCountCanComment($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->where('canComment = ?', 1)
            ->count();
    }

    public function addOneOrder($orderID,$deliverID,$createTime) {
        $data = array('orderID' => $orderID,'deliverID' => $deliverID,'createTime' => $createTime);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function getAllOrder($deliverID,$offset,$limit) {
        return $this->getORM()
        ->where('id >= ?', $offset)
        ->where('deliverID = ?', $deliverID)
        ->where('delivedTime IS NULL')//未送达
        ->limit($limit)
        ->order('getFoodTime DESC')
        ->fetchAll();
    } 
    
    public function updateGetFoodTime($ID,$time) {
        $data = array('getFoodTime' => $time);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    } 

    public function updatehasComment($ID) {
        $data = array('hasComment' => 1);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    } 

    public function updatedelivedTime($ID,$time) {
        $data = array('delivedTime' => $time);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    } 

    public function getOneUserAllOrderFinish($deliverID,$offset,$limit) {
        return $this->getORM()
        ->where('id >= ?', $offset)
        ->where('deliverID = ?', $deliverID)
        ->limit($limit)
        ->fetchAll();
    }

    public function getAllOrderCountCanComment($deliverID,$offset,$limit) {
        return $this->getORM()
        ->where('id >= ?', $offset)
        ->where('deliverID = ?', $deliverID)
        ->where('canComment = ?', 1)
        ->limit($limit)
        ->order('hasComment')
        ->fetchAll();
    }

    public function updateCanComment($ID) {
        $data = array('canComment' => 1);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    } 
}

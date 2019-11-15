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

    public function getAllOrder($deliverID,$page) {
        return $this->getORM()
        ->where('deliverID = ?', $deliverID)
        ->where('delivedTime IS NULL')//未送达
        ->order('getFoodTime DESC')
        ->page($page, 5)
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

    public function getOneUserAllOrderFinish($deliverID,$page) {
        return $this->getORM()
        ->where('deliverID = ?', $deliverID)
        ->page($page, 5)
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

    public function updateCanComment($orderID) {
        $data = array('canComment' => 1);
        return $this->getORM()
        ->where('orderID', $orderID)
        ->update($data);
    } 
}

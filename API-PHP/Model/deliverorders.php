<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class deliverorders extends NotORM {

    public function getOneOrderCountByID($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->count();
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

}

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

    public function insertOneOrder($userID,$foodArrID,$remark,$restID,$totalPrice,$payPrice,$addrID,$discountID,$createTime) {
        $data = array('userID' => $userID,'foods' => $foodArrID,'remark' => $remark,'discountID' => $discountID,'restID' => $restID,'totalPrice' => $totalPrice,'payPrice' => $payPrice,'addressID' => $addrID,'createTime' => $createTime, 'status' => 0);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }
}

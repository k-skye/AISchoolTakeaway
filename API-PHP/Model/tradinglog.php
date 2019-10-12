<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class tradinglog extends NotORM {

    public function getOneLogsNotDone($deliverID) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->where('done = ?', 0)
            ->fetchAll();
    }

    public function getOneLogsMonthDone($deliverID,$date) {
        return $this->getORM()
            ->where('deliverID = ?', $deliverID)
            ->where("date >= '".$date."'")
            ->fetchAll();
    }

    public function getOnesAllTradLog($deliverID,$offset,$limit) {
        return $this->getORM()
        ->where('id >= ?', $offset)
        ->where('deliverID = ?', $deliverID)
        ->limit($limit)
        ->order('date DESC')
        ->fetchAll();
    }

    public function addOneTradLogWithDeliver($deliverID,$money,$date,$deliverOrderID) {
        $data = array('type' => '配送费','deliverID' => $deliverID,'money' => $money,'date' => $date,'deliverorderID' => $deliverOrderID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

}

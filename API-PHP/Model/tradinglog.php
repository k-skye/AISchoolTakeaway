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

    public function getOneLogsByWechatID($wechatID) {
        return $this->getORM()
            ->where('orderWechatID = ?', $wechatID)
            ->where('done = ?', 1)
            ->fetchOne();
    }

    public function getOneLogsCompensateByWechatID($wechatID) {
        return $this->getORM()
            ->where('orderWechatID = ?', $wechatID)
            ->fetchOne();
    }

    public function updateDone($ID) {
        $data = array('done' => 1);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    }
    
    public function updateCompensate($id,$orderNo) {
        $data = array('orderWechatID' => $orderNo);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function updateCompensatePay($orderNo,$payPrice,$payTime) {
        $data = array('money' => $payPrice,'date' => $payTime);
        return $this->getORM()
        ->where('orderWechatID', $orderNo)
        ->update($data);
    }

    public function getAllLogsNotDone() {
        return $this->getORM()
            ->where('type LIKE ?', '%费%')
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

    public function addOneTradLogWithExpressDeliver($deliverID,$money,$date,$deliverOrderID) {
        $data = array('type' => '快递费','deliverID' => $deliverID,'money' => $money,'date' => $date,'deliverorderID' => $deliverOrderID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function addOneCompensate($orderID,$deliverID) {
        $data = array('type' => '快递尾款','orderID' => $orderID,'deliverID' => $deliverID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function addOneCashTradLog($deliverID,$orderWechatID,$money,$date) {
        //'done' => '1',  微信没有权限前暂时解决方案
        $data = array('type' => '提现','deliverID' => $deliverID,'orderWechatID' => $orderWechatID,'money' => $money,'date' => $date);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

}

<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class orders extends NotORM {

    public function getOnesAllOrders($userID,$page) {
        return $this->getORM()
            ->where('userID = ?', $userID)
            ->order('createTime DESC')
            ->page($page,5)
            ->fetchAll();
    }

    public function getOnesOneOrder($ID) {
        return $this->getORM()
            ->where('id = ?', $ID)
            ->fetchOne();
    }

    public function getOnesOneOrderByWechatNo($ID) {
        return $this->getORM()
            ->where('orderNo = ?', $ID)
            ->fetchOne();
    }

    public function getAllOrdersOnNeedDelive($page) {
        return $this->getORM()
            ->order('fastMoney DESC')
            ->where('status = 1')
            ->page($page, 5)
            ->fetchAll();
    }

    public function insertOneOrder($userID,$foodArrID,$remark,$restID,$totalPrice,$payPrice,$addrID,$discountID,$createTime,$shouldDeliveTime,$deliveFee,$upstairs) {
        $data = array('userID' => $userID,'foods' => $foodArrID,'remark' => $remark,'discountID' => $discountID,'restID' => $restID,'totalPrice' => $totalPrice,'payPrice' => $payPrice,'addressID' => $addrID,'createTime' => $createTime, 'status' => 0, 'shouldDeliveTime' => $shouldDeliveTime, 'deliveFee' => $deliveFee, 'upstairs' => $upstairs);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }
    
    public function insertOneExpressOrder($userID,$expressAddr,$remark,$expressCode,$totalPrice,$payPrice,$addrID,$createTime,$shouldDeliveTime,$deliveFee,$weight,$goodType,$isNeedFast,$fastMoney,$discountID) {
        $data = array('userID' => $userID,'expressAddr' => $expressAddr,'remark' => $remark,'expressCode' => $expressCode,'totalPrice' => $totalPrice,'payPrice' => $payPrice,'addressID' => $addrID,'createTime' => $createTime, 'status' => 0, 'type' => 1, 'shouldDeliveTime' => $shouldDeliveTime, 'deliveFee' => $deliveFee, 'weight' => $weight, 'goodType' => $goodType, 'isNeedFast' => $isNeedFast, 'fastMoney' => $fastMoney,'discountID' => $discountID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function updateOrderNo($id,$orderNo) {
        $data = array('orderNo' => $orderNo);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function updateOrderPay($orderNo,$payPrice,$payTime) {
        $data = array('payTime' => $payTime,'payPrice' => $payPrice,'status' => 1);
        return $this->getORM()
        ->where('status = 0')
        ->where('orderNo', $orderNo)
        ->update($data);
    }

    public function updateOrderExpressNoFastMoney($id,$totalPrice,$payPrice,$deliveFee) {
        $data = array('totalPrice' => $totalPrice,'payPrice' => $payPrice,'deliveFee' => $deliveFee);
        return $this->getORM()
        ->where('status = 4')
        ->where('id', $id)
        ->update($data);
    }

    public function updateOrderOtherPay($id,$totalPrice,$payPrice,$deliveFee) {
        $data = array('totalPrice' => $totalPrice,'payPrice' => $payPrice,'deliveFee' => $deliveFee, 'isNeedFast' => 2);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function updateOrderCompensate($id,$totalPrice,$payPrice,$deliveFee) {
        $data = array('totalPrice' => $totalPrice,'payPrice' => $payPrice,'deliveFee' => $deliveFee);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function updateStatus($id,$statusNum) {
        $data = array('status' => $statusNum);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function updateStatusOnJieDan($id,$statusNum) {
        $data = array('status' => $statusNum);
        return $this->getORM()
        ->where('status = 1')
        ->where('id', $id)
        ->update($data);
    }

    public function cancelOrder($id) {
        $data = array('status' => 8);
        return $this->getORM()
        ->where('status = 1')
        ->where('id', $id)
        ->update($data);
    }

    public function errorOrder($id) {
        $data = array('status' => -1);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function updateComplaint($id) {
        $data = array('hasComplaint' => 1);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function userCommentOrder($id,$commentID) {
        $data = array('status' => 5,'hasComment' => 1,'commentID' => $commentID);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function getNeedCancelOrder($theTime) {
        $query = 'createTime >= '.$theTime;
        return $this->getORM()
        ->where($query)
        ->fetchAll();
    }
    
}

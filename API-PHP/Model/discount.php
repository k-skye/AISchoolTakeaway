<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class discount extends NotORM {

    public function getOnesAllcountsTrue($userID) {
        return $this->getORM()
            ->where('userID = ?', $userID)
            ->where('available', 1)
            ->fetchAll();
    }

    public function getOnesAllcountsFalse($userID) {
        return $this->getORM()
            ->where('userID = ?', $userID)
            ->where('available', 0)
            ->fetchAll();
    }

    public function changeToFalseAndReason($id,$reason) {
        $data = array('available' => 0, 'reason' => $reason);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function changeToTrue($id) {
        $data = array('available' => 1, 'reason' => '');
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function getOnesDiscounts($id) {
        return $this->getORM()
        ->where('id', $id)
        ->fetchOne();
    }
}

<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class address extends NotORM {

    public function getOneByAddrById($addrId) {
        return $this->getORM()
        ->where('id', $addrId)
        ->fetchOne();
    }

    public function getAllByAddrByUserId($userID) {
        return $this->getORM()
        ->where('userid = ?', $userID)
        ->fetchAll();
    }

    public function addAddr($userid,$dormitory,$roomNum,$gender,$name,$phone) {
        $data = array('userid' => $userid, 'dormitory' => $dormitory, 'roomNum' => $roomNum, 'gender' => $gender, 'name' => $name, 'phone' => $phone);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function changeAddr($id,$dormitory,$roomNum,$gender,$name,$phone) {
        $data = array('dormitory' => $dormitory, 'roomNum' => $roomNum, 'gender' => $gender, 'name' => $name, 'phone' => $phone);
        return $this->getORM()
        ->where('id', $id)
        ->update($data);
    }

    public function removeAddr($id) {
        return $this->getORM()
        ->where('id', $id)
        ->delete();
    }
}

<?php
namespace App\Domain;

use App\Model\address as ModelAddress;

class address {

    public function getOneAddr($addrID) {
        $model = new ModelAddress();
        return $model->getOneByAddrById($addrID);
    }

    public function getUserAddrs($userID) {
        $model = new ModelAddress();
        return $model->getAllByAddrByUserId($userID);
    }

    public function addAddr($userid,$dormitory,$roomNum,$gender,$name,$phone) {
        $model = new ModelAddress();
        $res = $model->addAddr($userid,$dormitory,$roomNum,$gender,$name,$phone);
        if ($res) {
            return 0;
        }else {
            return -1;
        }
    }

    public function changeAddr($id,$dormitory,$roomNum,$gender,$name,$phone) {
        $model = new ModelAddress();
        $res = $model->changeAddr($id,$dormitory,$roomNum,$gender,$name,$phone);
        if ($res == 0 || $res) {//数据已更新或无变化
            return 0;
        }else {
            return -1;//更新异常
        }
    }

    public function removeAddr($id) {
        $model = new ModelAddress();
        $res = $model->removeAddr($id);
        if ($res) {
            return 0;
        }else {
            return -1;
        }
    }
}

<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class users extends NotORM {

    public function getAllUsers() {
        return $this->getORM()
            ->fetchAll();
    }

    public function changeUserInfo($phoneNo,$stuID,$openid) {
        $data = array('phoneNo' => $phoneNo, 'stuID' => $stuID);
        return $this->getORM()
        ->where('openid', $openid)
        ->update($data);
    }

    public function changeUserInfoOnMainAddrIDByUserId($userID,$mainAddrID) {
        $data = array('mainAddressID' => $mainAddrID);
        return $this->getORM()
        ->where('id', $userID)
        ->update($data);
    }

    public function getOneUserByPhone($phoneNo) {
        return $this->getORM()
        ->where('phoneNo', $phoneNo)
        ->fetchOne();
    }

    public function getOneUserByUserID($userID) {
        return $this->getORM()
        ->where('id', $userID)
        ->fetchOne();
    }

    public function getOneUserByOpenid($openid) {
        return $this->getORM()
        ->where('openid', $openid)
        ->fetchOne();
    }

    public function createOpenid($openid) {
        $data = array('openid' => $openid);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function saveWechatUserInfo($openid,$nickname,$city,$headimgurl) {
        $data = array('name' => $nickname, 'city' => $city, 'avatar' => $headimgurl);
        return $this->getORM()
        ->where('openid', $openid)
        ->update($data);
    }
}

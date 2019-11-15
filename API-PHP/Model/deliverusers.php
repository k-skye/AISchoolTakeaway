<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class deliverusers extends NotORM {

    public function getAllUsers() {
        return $this->getORM()
            ->where('cardImg IS NOT NULL')
            ->fetchAll();
    }

    public function changeUserInfo($phoneNo,$stuID,$openid,$realName,$sex,$cardImg) {
        $data = array('phoneNo' => $phoneNo, 'stuID' => $stuID, 'realName' => $realName, 'sex' => $sex, 'cardImg' => $cardImg);
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

    public function changeUserInfoOnChooseByUserId($userID,$chooseAddr,$chooseRest,$chooseNear,$chooseType,$chooseExpress) {
        $data = array('chooseAddr' => $chooseAddr,'chooseRest' => $chooseRest,'chooseNear' => $chooseNear,'chooseType' => $chooseType,'chooseExpress' => $chooseExpress);
        return $this->getORM()
        ->where('id', $userID)
        ->update($data);
    }

    public function changeUserInfoOnSendMessage($userID,$sendMessage) {
        $data = array('sendMessage' => $sendMessage);
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

    public function updateCashTimestamp($ID,$time) {
        $data = array('lastCashTimeStamp' => $time);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    }

    public function updateNounAndIncome($noun,$income,$deliverID) {
        $data = array('noun' => $noun,'income' => $income);
        return $this->getORM()
        ->where('id', $deliverID)
        ->update($data);
    }

    public function updateNounToZero($ID) {
        $data = array('noun' => 0,'income' => 0);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    } 
}

<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class feedback extends NotORM {
    //inwhere 0是用户我的界面建议 1是配送员我的界面建议  2订单详情页面投诉伙伴按钮

    public function addOneFeedBackByDeliver($deliverID,$content) {
        $data = array('inwhere' => 1,'content' => $content,'deliverID' => $deliverID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function addOneFeedBackByUser($userID,$content) {
        $data = array('inwhere' => 0,'content' => $content,'userID' => $userID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function addOneComplaintByUser($userID,$content,$deliverID) {
        $data = array('inwhere' => 2,'content' => $content,'userID' => $userID,'deliverID' => $deliverID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }
}

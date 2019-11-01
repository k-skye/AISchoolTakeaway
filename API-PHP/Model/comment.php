<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class comment extends NotORM {

    public function getSomeComment($restID,$offset,$limit) {
        return $this->getORM()
            ->where('restID = ?', $restID)
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

    public function getOneComment($ID) {
        return $this->getORM()
            ->where('id', $ID)
            ->fetchOne();
    }

    public function updateCommentDeliveReply($ID,$text) {
        $data = array('deliverReply' => $text);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    } 

    public function addOneComment($text,$restID,$images,$stars,$userID,$time) {
        $data = array('content' => $text,'restID' => $restID,'images' => $images,'stars' => $stars,'userID' => $userID,'time' => $time);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function getOnesComment($userID,$offset,$limit) {
        return $this->getORM()
            ->where('userID = ?', $userID)
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

}

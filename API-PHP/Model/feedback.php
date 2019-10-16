<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class feedback extends NotORM {

    public function addOneFeedBackByDeliver($deliverID,$content) {
        $data = array('inwhere' => 1,'content' => $content,'deliverID' => $deliverID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }
}

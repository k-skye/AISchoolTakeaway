<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class phonecode extends NotORM {

    public function saveCode($phoneNo,$randomCode) {
        //INSERT INTO `phonecode` (`id`, `phoneNo`, `code`, `time`) VALUES (NULL, '13123464111', '737122', CURRENT_TIMESTAMP);
        //INSERT INTO `phonecode` ( `phoneNo`, `code`) VALUES ( '13123464123', '737322')
        $data = array('phoneNo' => $phoneNo, 'code' => $randomCode);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function getCode($codeID) {
        return $this->getORM()
        ->where('id', $codeID)
        ->fetch('code');
    }
}

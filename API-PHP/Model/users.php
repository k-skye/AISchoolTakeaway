<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class users extends NotORM {

    public function getAllUsers() {
        return $this->getORM()
            ->fetchAll();
    }

    public function createUsers($name,$phoneNo,$avatar,$stuID) {
        $data = array('name' => $name, 'phoneNo' => $phoneNo, 'avatar' => $avatar, 'stuID' => $stuID);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }

    public function getOneUser($phoneNo) {
        return $this->getORM()
        ->where('phoneNo', $phoneNo)
        ->fetchOne();
    }
}

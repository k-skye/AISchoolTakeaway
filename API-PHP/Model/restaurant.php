<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class restaurant extends NotORM {

    public function getRestsInNormal($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->order('stars')
            ->order('salesNum DESC')
            ->fetchAll();
    }

    public function getRestsInSale($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->order('salesNum DESC')
            ->fetchAll();
    }

    public function getRestsInStars($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->order('stars DESC')
            ->fetchAll();
    }
    
    public function getOneRest($id) {
        return $this->getORM()
            ->where('id = ?', $id)
            ->fetchOne();
    }

    public function getRestsAdmin($page,$limit,$rateQuery,$rate,$name,$statusQuery,$status,$sortQuery) {
        return $this->getORM()
            ->where($rateQuery, $rate)
            ->where('name LIKE ?', "%".$name."%")
            ->where($statusQuery, $status)
            ->order($sortQuery)
            ->page($page, $limit)
            ->fetchAll();
    }

    public function addRestsAdmin($name,$roomNum,$location,$logo) {
        $data = array('name' => $name,'roomNum' => $roomNum,'location' => $location,'logo' => $logo);
        $orm = $this->getORM();
        $orm->insert($data);

        // 返回新增的ID（注意，这里不能使用连贯操作，因为要保持同一个ORM实例）
        return $orm->insert_id();
    }
}

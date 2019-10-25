<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class restaurant extends NotORM {

    public function getRestsInNormal($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->order('stars and salesNum DESC')
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

    public function getRestsInNormalWtihRoomNum($offset,$limit,$roomNum) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('roomNum', $roomNum)
            ->order('stars and salesNum DESC')
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

    public function getRestsInSale($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->order('salesNum DESC')
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

    public function getRestsInSaleWithRoomNum($offset,$limit,$roomNum) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('roomNum', $roomNum)
            ->order('salesNum DESC')
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

    public function getRestsInStars($offset,$limit) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->order('stars DESC')
            ->where('id >= ?', $offset)
            ->limit($limit)
            ->fetchAll();
    }

    public function getRestsInStarsWithRoomNum($offset,$limit,$roomNum) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('roomNum', $roomNum)
            ->order('stars DESC')
            ->where('id >= ?', $offset)
            ->limit($limit)
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

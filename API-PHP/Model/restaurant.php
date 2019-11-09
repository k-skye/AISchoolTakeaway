<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;

class restaurant extends NotORM {

    public function getRestsInNormal($page) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->order('salesNum DESC,stars DESC')
            ->page($page, 5)
            ->fetchAll();
    }

    public function getRestsInNormalWtihRoomNum($page,$roomNum) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('roomNum', $roomNum)
            ->order('salesNum DESC,stars DESC')
            ->page($page, 5)
            ->fetchAll();
    }

    public function getRestsInSale($page) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->order('salesNum DESC')
            ->page($page, 5)
            ->fetchAll();
    }

    public function getRestsInSaleWithRoomNum($page,$roomNum) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('roomNum', $roomNum)
            ->order('salesNum DESC')
            ->page($page, 5)
            ->fetchAll();
    }

    public function getRestsInStars($page) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->order('stars DESC')
            ->page($page, 5)
            ->fetchAll();
    }

    public function getRestsInStarsWithRoomNum($page,$roomNum) {
        return $this->getORM()
            //默认排序筛选条件-评价和销量一起排序
            ->where('roomNum', $roomNum)
            ->order('stars DESC')
            ->page($page, 5)
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

    public function updateSalesNum($ID,$salesNum) {
        $data = array('salesNum' => $salesNum);
        return $this->getORM()
        ->where('id', $ID)
        ->update($data);
    }
}

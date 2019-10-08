<?php
namespace App\Domain;

use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;
use App\Model\discount as ModelDiscount;
use App\Model\address as ModelAddress;

class orders {

    public function getOnesAllOrders($userID,$offset,$limit) {
        $model = new ModelOders();
        $arr = $model->getOnesAllOrders($userID,$offset,$limit);
        $i = 0;
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        foreach ($arr as $value){
            $restID = $arr[$i]['restID'];
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['restName'] = $restRes['name'];
            $arr[$i]['restLogo'] = $restRes['logo'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        return $arr;
    }

    public function getAllOrdersOnNeedDelive($offset,$limit) {
        $model = new ModelOders();
        $arr = $model->getAllOrdersOnNeedDelive($offset,$limit);
        $i = 0;
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        foreach ($arr as $value){
            $restID = $arr[$i]['restID'];
            $addrID = $arr[$i]['addressID'];
            //处理时间
            $date=date_create($arr[$i]['shouldDeliveTime']);
            $output=date_format($date,"H:i");
            $t = time();
            $createTime = (int)date('d',$t) + 1;
            $thatTime = (int)date_format($date,"d");
            if ($thatTime == $createTime){
            	$arr[$i]['shouldDeliveTime'] = '明天 '.$output;
            }else {
                $arr[$i]['shouldDeliveTime'] = $output;
            }
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['restName'] = $restRes['name'];
            $arr[$i]['restLogo'] = $restRes['logo'];
            $arr[$i]['restNum'] = $restRes['roomNum'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['dormitory'] = $addrRes['dormitory'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        return $arr;
    }

    public function insertOneOrder($userID,$foodArrID,$remark,$restID,$totalPrice,$payPrice,$addrID,$discountID) {
        $model = new ModelOders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        $res = $model->insertOneOrder($userID,$foodArrID,$remark,$restID,$totalPrice,$payPrice,$addrID,$discountID,$createTime);
        if ($res) {
            //让红包报废
            $modelDisc = new ModelDiscount();
            $ress = $modelDisc->changeToFalseAndReason($discountID,"已使用");
            if ($ress == 0 || $ress) {//数据已更新或无变化
                return $res;
            }else{
                return -2;
            }
        }else {
            return -1;
        }
    }
    
    public function updateOrderNo($id,$orderNo) {
        $model = new ModelOders();
        return $model->updateOrderNo($id,$orderNo);
    }

    public function updateOrderPay($orderNo,$payPrice,$payTime) {
        $model = new ModelOders();
        return $model->updateOrderPay($orderNo,$payPrice,$payTime);
    }
}

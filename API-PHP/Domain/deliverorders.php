<?php
namespace App\Domain;

use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;
use App\Model\address as ModelAddress;

class deliverorders {

    public function addOneOrder($orderID,$deliverID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为2-接单状态
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,2);
        $rres = $model->addOneOrder($orderID,$deliverID,$createTime);
        if ($rres && $res) {
            return $rres;
        }else {
            return -1;
        }
    }

    public function getAllOrder($deliverID,$offset,$limit) {
        $modelOrder = new ModelDeliverorders();
        $arr =  $modelOrder->getAllOrder($deliverID,$offset,$limit);
        $model = new ModelOders();
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        foreach ($arr as $value){
            $value['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $value['order']['restID'];
            $addrID = $value['order']['addressID'];
            //处理时间
            $date=date_create($value['order']['shouldDeliveTime']);
            $output=date_format($date,"H:i");
            $t = time();
            $createTime = (int)date('d',$t) + 1;
            $thatTime = (int)date_format($date,"d");
            if ($thatTime == $createTime){
            	$value['order']['shouldDeliveTime'] = '明天 '.$output;
            }else {
                $value['order']['shouldDeliveTime'] = $output;
            }
            $restRes = $modelRest->getOneRest($restID);
            $value['order']['restName'] = $restRes['name'];
            $value['order']['restLogo'] = $restRes['logo'];
            $value['order']['restNum'] = $restRes['roomNum'];
            $value['order']['location'] = $restRes['location'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $value['addr'] = $addrRes;
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $value['food'] = $foodsRes;
        }
        return $arr;
    }

    public function changToGetFood($orderID,$ID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为3-配送状态
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,3);
        $rres = $model->updateGetFoodTime($ID,$createTime);
        if ($rres && $res) {
            return $rres;
        }else {
            return -1;
        }
    }

    public function changToFinishDelive($orderID,$ID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为4-已送达
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,4);
        $rres = $model->updatedelivedTime($ID,$createTime);
        if ($rres && $res) {
            return $rres;
        }else {
            return -1;
        }
    }

}

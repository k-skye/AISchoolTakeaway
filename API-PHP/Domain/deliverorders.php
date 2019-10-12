<?php
namespace App\Domain;

use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;
use App\Model\address as ModelAddress;
use App\Model\comment as ModelComment;
use App\Model\tradinglog as ModelTradinglog;

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
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $arr[$i]['order']['restID'];
            $addrID = $arr[$i]['order']['addressID'];
            //处理时间
            $date=date_create($arr[$i]['order']['shouldDeliveTime']);
            $output=date_format($date,"H:i");
            $t = time();
            $createTime = (int)date('d',$t) + 1;
            $thatTime = (int)date_format($date,"d");
            if ($thatTime == $createTime){
            	$arr[$i]['order']['shouldDeliveTime'] = '明天 '.$output;
            }else {
                $arr[$i]['order']['shouldDeliveTime'] = $output;
            }
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['order']['restName'] = $restRes['name'];
            $arr[$i]['order']['restLogo'] = $restRes['logo'];
            $arr[$i]['order']['restNum'] = $restRes['roomNum'];
            $arr[$i]['order']['location'] = $restRes['location'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['addr'] = $addrRes;
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        return $arr;
    }

    public function getOneUserAllOrderFinish($deliverID,$offset,$limit) {
        $modelOrder = new ModelDeliverorders();
        $arr =  $modelOrder->getOneUserAllOrderFinish($deliverID,$offset,$limit);
        $model = new ModelOders();
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $arr[$i]['order']['restID'];
            $addrID = $arr[$i]['order']['addressID'];
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['order']['restName'] = $restRes['name'];
            $arr[$i]['order']['restLogo'] = $restRes['logo'];
            $arr[$i]['order']['restNum'] = $restRes['roomNum'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['dormitory'] = $addrRes['dormitory'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        return $arr;
    }

    public function getAllOrderCountCanComment($deliverID,$offset,$limit) {
        $modelOrder = new ModelDeliverorders();
        $arr =  $modelOrder->getAllOrderCountCanComment($deliverID,$offset,$limit);
        $model = new ModelOders();
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $modelComm = new ModelComment();
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $arr[$i]['order']['restID'];
            $addrID = $arr[$i]['order']['addressID'];
            $commentID = $arr[$i]['order']['commentID'];
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['order']['restName'] = $restRes['name'];
            $arr[$i]['order']['restLogo'] = $restRes['logo'];
            $arr[$i]['order']['restNum'] = $restRes['roomNum'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['dormitory'] = $addrRes['dormitory'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $commentRes = $modelComm->getOneComment($commentID);
            $arr[$i]['comment'] = $commentRes;
            $i++;
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

    public function changToFinishDelive($orderID,$ID,$deliverID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为4-已送达
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,4);
        $orderInfo = $modelOrder->getOnesOneOrder($orderID);
        $money = $orderInfo['deliveFee'];
        $modelTradLog = new ModelTradinglog();
        $rres = $modelTradLog->addOneTradLogWithDeliver($deliverID,$money,$createTime,$ID);
        $rrres = $model->updatedelivedTime($ID,$createTime);
        if ($rrres && $res && $rres) {
            return $rrres;
        }else {
            return -1;
        }
    }

}

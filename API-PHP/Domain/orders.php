<?php
namespace App\Domain;

use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;
use App\Model\discount as ModelDiscount;
use App\Model\address as ModelAddress;
use App\Model\deliverusers as ModelUsers;

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

    public function getAllOrdersOnNeedDelive($deliverID,$offset,$limit) {
        $model = new ModelOders();
        $arr = $model->getAllOrdersOnNeedDelive($offset,$limit);
        $i = 0;
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $modelUser = new ModelUsers();
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
            $arr[$i]['sex'] = $addrRes['gender'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        $userInfo = $modelUser->getOneUserByUserID($deliverID);
        $needSex = $userInfo['sex'];
        //去掉性别不一致的
        $iisex = 0;
        $sex = 0;
        foreach ($arr as $value){
            $sex = $value['sex'];
             //把性别不相等的去掉
             if ($sex != $needSex) {
                unset($arr[$iisex]);
             }
             $iisex++;
         }
         $arr = array_values($arr);//重建索引
        if ($userInfo['chooseAddr'] == 0 && $userInfo['chooseRest'] ==0) {//默认
            return $arr;
        }else{
            if ($userInfo['chooseAddr'] > 0){
                $needNum = $userInfo['chooseAddr'];
                if ($userInfo['chooseNear'] == 1) {//包括附近宿舍
                if ($needNum <= 6) {
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于6的都去掉
                         if ($dormitoryNum > 6) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                     
                }else if ($needNum <= 10){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于10和小于7的都去掉
                         if ($dormitoryNum > 10 || $dormitoryNum < 7) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 15){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于15和小于11的都去掉
                         if ($dormitoryNum > 15 || $dormitoryNum < 11) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 17){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于17和小于16的都去掉
                         if ($dormitoryNum > 17 || $dormitoryNum < 16) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 20){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于20和小于18的都去掉
                         if ($dormitoryNum > 20 || $dormitoryNum < 18) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 22){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于22和小于20的都去掉
                         if ($dormitoryNum > 22 || $dormitoryNum < 20) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
                }else if ($userInfo['chooseNear'] == 0) {
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把不等于的去掉
                         if ($dormitoryNum != $needNum) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
                $arr = array_values($arr);//重建索引
            }
            if ($userInfo['chooseRest'] > 0) {
                $needNum = $userInfo['chooseRest'];
                if ($needNum == 1) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等1\2的去掉
                         if ($restNum != 1 || $restNum !=2) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 2) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等2的去掉
                         if ($restNum != 3) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 3) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等3的去掉
                         if ($restNum != 4) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 4) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等4的去掉
                         if ($restNum != 5) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
                $arr = array_values($arr);//重建索引
            }
            return $arr;
        }
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

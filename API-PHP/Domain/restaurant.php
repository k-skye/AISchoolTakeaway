<?php
namespace App\Domain;

use App\Model\restaurant as ModelRestaurant;

class restaurant {

    public function getRestsByRule($page,$condition) {
        $model = new ModelRestaurant();
        switch ($condition) {
            case 'normal':
                $arr = $model->getRestsInNormal($page);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
            case 'sale':
                $arr = $model->getRestsInSale($page);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
            case 'stars':
                $arr = $model->getRestsInStars($page);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
            default:
                $arr = $model->getRestsInNormal($page);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
        }
    }

    public function getOneRest($id) {
        $model = new ModelRestaurant();
        $res = $model->getOneRest($id);
        $res['deliveryFee'] = '1.5';
        $res['deliveryTime'] = '30';
        return $res;
    }

    public function getRestsByRuleWithRoomNum($page,$condition,$roomNum) {
        $model = new ModelRestaurant();
        switch ($condition) {
            case 'normal':
                $arr = $model->getRestsInNormalWtihRoomNum($page,$roomNum);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
            case 'sale':
                $arr = $model->getRestsInSaleWithRoomNum($page,$roomNum);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
            case 'stars':
                $arr = $model->getRestsInStarsWithRoomNum($page,$roomNum);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
            default:
                $arr = $model->getRestsInNormalWtihRoomNum($page,$roomNum);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '1.5';
                    $i++;
                }
                return $arr;
                break;
        }
/*         $i = 0;
        foreach ($arr as $value) {
            if ($value['roomNum'] != $roomNum) {
                //去掉不是这个roomNum条件下的
                unset($arr[$i]);
            }
            $i++;
        }
        $arr = array_values($arr);//重建索引 */
    }

    public function getRestsAdmin($page,$limit,$rate,$name,$status,$sort) {
        $model = new ModelRestaurant();
        //SELECT * FROM `restaurant` WHERE `name` LIKE '%牛肉%' AND `status` != '3' AND `stars` <= 5
        //可选参数
        $rateQuery = "";
        if ($rate == "") {
            $rateQuery = "stars NOT LIKE ?";
            $rate = "6";
        }else{
            $rateQuery = "stars LIKE ?";
            $rate = "%".$rate."%";
        }
        $statusQuery = "";
        if ($status == "") {
            $statusQuery = "status NOT LIKE ?";
            $status = "3";
        }else{
            $statusQuery = "status LIKE ?";
            $status = "%".$status."%";
        }
        $sortQuery = "";
        if ($sort == "-id") {
            $sortQuery = "id DESC";
        }else{
            $sortQuery = "id";
        }
        $res = $model->getRestsAdmin($page,$limit,$rateQuery,$rate,$name,$statusQuery,$status,$sortQuery);
        $total = count($res);
        return array('items' => $res,'total' => $total);
    }

    public function addRestsAdmin($name,$roomNum,$location,$logo) {
        $model = new ModelRestaurant();
        return $model->addRestsAdmin($name,$roomNum,$location,$logo);
    }
}

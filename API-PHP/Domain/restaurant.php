<?php
namespace App\Domain;

use App\Model\restaurant as ModelRestaurant;

class restaurant {

    public function getRestsByRule($offset,$limit,$condition) {
        $model = new ModelRestaurant();
        switch ($condition) {
            case 'normal':
                $arr = $model->getRestsInNormal($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '0.01';
                    $i++;
                }
                return $arr;
                break;
            case 'sale':
                $arr = $model->getRestsInSale($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '0.01';
                    $i++;
                }
                return $arr;
                break;
            case 'stars':
                $arr = $model->getRestsInStars($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '0.01';
                    $i++;
                }
                return $arr;
                break;
            default:
                $arr = $model->getRestsInNormal($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '0.01';
                    $i++;
                }
                return $arr;
                break;
        }
    }

    public function getOneRest($id) {
        $model = new ModelRestaurant();
        $res = $model->getOneRest($id);
        $res['deliveryFee'] = '0.01';
        $res['deliveryTime'] = '30';
        return $res;
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

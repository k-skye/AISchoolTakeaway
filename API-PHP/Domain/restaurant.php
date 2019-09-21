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
                    $arr[$i]['deliveryFee'] = '11';
                    $i++;
                }
                return $arr;
                break;
            case 'sale':
                $arr = $model->getRestsInSale($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '11';
                    $i++;
                }
                return $arr;
                break;
            case 'stars':
                $arr = $model->getRestsInStars($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '11';
                    $i++;
                }
                return $arr;
                break;
            default:
                $arr = $model->getRestsInNormal($offset,$limit);
                $i = 0;
                foreach ($arr as $value){
                    //添加配送费一起返回
                    $arr[$i]['deliveryFee'] = '11';
                    $i++;
                }
                return $arr;
                break;
        }
    }

    public function getOneRest($id) {
        $model = new ModelRestaurant();
        $res = $model->getOneRest($id);
        $res['deliveryFee'] = '11';
        $res['deliveryTime'] = '30';
        return $res;
    }
}

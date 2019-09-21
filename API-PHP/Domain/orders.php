<?php
namespace App\Domain;

use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;

class orders {

    public function getOnesAllOrders($id,$offset,$limit) {
        $model = new ModelOders();
                $arr = $model->getOnesAllOrders($id,$offset,$limit);
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
}

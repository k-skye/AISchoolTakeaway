<?php
namespace App\Domain;

use App\Model\homedata as ModelHomeData;
use App\Model\food as ModelFood;
use App\Model\restaurant as ModelRestaurant;

class homedata {

    public function getHeadAdImg() {
        $model = new ModelHomeData();
        return $model->getHeadAdImg();
    }

    public function getSearchByRule($text) {
        $modelFood = new ModelFood();
        $foodInfo = $modelFood->searchFoodName($text);
        $restArr = array();
        foreach ($foodInfo as $value) {
            array_push($restArr,$value['restID']);
        }
        $restArr = array_unique($restArr);
        //获取到所有restID数组之后全部拿来查询一遍
        $modelRest = new ModelRestaurant();
        $okArr = array();
        foreach ($restArr as $value) {
            $restInfo = $modelRest->getOneRest($value);
            array_push($okArr,$restInfo);
        }
        return $okArr;
    }
}

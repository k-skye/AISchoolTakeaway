<?php
namespace App\Domain;

use App\Model\food as ModelFood;

class food {

    public function getFoods($restID) {
        //先查店铺的分类，再用每一个分类去获取这个分类下所有食物
        $arr = array(array('cate' => '' , 'food' => array()));
        $model = new ModelFood();
        $arrCate = $model->getCate($restID);
        $i = 0;
        foreach ($arrCate as $value){
            $arr[$i]['cate'] = $value['category'];
            $i++;
        }
        $i = 0;
        foreach ($arrCate as $value){
            $arrFood = $model->getFoods($value);
            $arr[$i]['food'] = $arrFood;
            $i++;
        }
        return $arr;
    }

}

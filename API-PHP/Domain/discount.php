<?php
namespace App\Domain;

use App\Model\discount as ModelDiscount;

class discount {

    public function getOnesAllcounts($userID) {
        $model = new ModelDiscount();
        $countTrue = $model->getOnesAllcountsTrue($userID);
        $countFalse = $model->getOnesAllcountsFalse($userID);
        $arr = array();
        if (empty($countTrue) && empty($countFalse)) {
            return -1;
        }else{
            $arr[0] = $countFalse;
            $arr[1] = $countTrue;
            return $arr;
        }
    }

}

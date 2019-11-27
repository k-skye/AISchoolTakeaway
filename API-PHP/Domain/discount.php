<?php
namespace App\Domain;

use App\Model\discount as ModelDiscount;

class discount {

    public function getOnesAllcounts($userID,$type) {
        $model = new ModelDiscount();
        $countTrue = $model->getOnesAllcountsTrue($userID);
        $countFalse = $model->getOnesAllcountsFalse($userID);
        $arr = array();
        if (empty($countTrue) && empty($countFalse)) {
            return -1;
        }else{
            //先分出过期的红包
            $i = 0;
                foreach ($countTrue as $value) {
                    $currentTime = (int)$value['endAt'];
                    if ($currentTime < (time())) {
                        //从true去掉，加到false里
                        unset($countTrue[$i]);
                        $value['reason'] = '已过期';
                        $value['available'] = 0;
                        array_push($countFalse,$value);
                    }
                    $i++;
                }
            $countTrue = array_values($countTrue);//重建索引
            
            $needTypeNum = (int)$type;
            if ($needTypeNum == 0) {
                //  全部红包都要，不需要筛选
            }else {
                $i = 0;
                foreach ($countTrue as $value) {
                    $currentType = (int)$value['type'];
                    if ($currentType != $needTypeNum) {
                        //从true去掉，加到false里
                        unset($countTrue[$i]);
                        $value['reason'] = '不符合购买此类型商品';
                        $value['available'] = 0;
                        array_push($countFalse,$value);
                    }
                    $i++;
                }
                $countTrue = array_values($countTrue);//重建索引
            }
            $arr[0] = $countFalse;
            $arr[1] = $countTrue;
            return $arr;
        }
    }

    public function getOnesDiscounts($id) {
        $model = new ModelDiscount();
        return $model->getOnesDiscounts($id);
    }

}

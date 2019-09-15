<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\food as DomainFood;

/**
 * 商品接口
 * @author: kang <knskye@gmail.com> 2019-08-22
 */


class Food extends Api {
    public function getRules() {
        return array(
            'getFoods' => array(
                'restID'  => array('name' => 'restID', 'require' => true, 'desc' => '店铺id'),
            )
        );
    }
    /**
     * 拿食物
     * @desc 测试一下
     */
    public function getFoods() {
        $domain = new DomainFood();
        return $domain->getFoods($this->restID);
    }
} 

<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\restaurant as DomainRestaurant;

/**
 * 店铺接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class Restaurant extends Api {
    public function getRules() {
        return array(
            'getSomeRest' => array(
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
            'getOneRest' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '店铺id'),
            ),
        );
    }
    /**
     * 拿一些餐厅信息
     * @desc 测试一下
     */
    public function getSomeRest() {
        $domain = new DomainRestaurant();
        return $domain->getSomeRest($this->offset,$this->limit);
    }
    /**
     * 拿某个餐厅信息
     * @desc 测试一下
     */
    public function getOneRest() {
        $domain = new DomainRestaurant();
        return $domain->getOneRest($this->id);
    }
} 

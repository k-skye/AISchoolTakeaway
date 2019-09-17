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
            'getRestsByRule' => array(
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
                'condition'  => array('name' => 'condition', 'require' => true, 'desc' => '筛选规则')
            ),
            'getOneRest' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '店铺id'),
            ),
        );
    }
    /**
     * 拿指定规则排序的店铺信息
     * @desc 测试一下
     */
    public function getRestsByRule() {
        $domain = new DomainRestaurant();
        return $domain->getRestsByRule($this->offset,$this->limit,$this->condition);
    }
    /**
     * 拿某个店铺信息
     * @desc 测试一下
     */
    public function getOneRest() {
        $domain = new DomainRestaurant();
        return $domain->getOneRest($this->id);
    }
} 

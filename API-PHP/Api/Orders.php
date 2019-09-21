<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\orders as DomainOders;

/**
 * 订单接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class Orders extends Api {
    public function getRules() {
        return array(
            'getOnesAllOrders' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '用户id'),
            ),
        );
    }
    /**
     * 拿一个用户的所有订单
     * @desc 测试一下
     */
    public function getOnesAllOrders() {
        $domain = new DomainOders();
        return $domain->getOnesAllOrders($this->id,1,1);
    }
} 

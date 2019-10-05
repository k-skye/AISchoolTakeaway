<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\orders as DomainOders;
use PhalApi\Exception\InternalServerErrorException;

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
            'createOneOrder' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'foodArrID'  => array('name' => 'foodArrID', 'require' => true, 'desc' => '购买的商品数组'),
                'remark'  => array('name' => 'remark', 'require' => true, 'desc' => '订单备注'),
                'restID'  => array('name' => 'restID', 'require' => true, 'desc' => '餐厅id'),
                'totalPrice'  => array('name' => 'totalPrice', 'require' => true, 'desc' => '原价'),
                'payPrice'  => array('name' => 'payPrice', 'require' => true, 'desc' => '支付价格'),
                'addrID'  => array('name' => 'addrID', 'require' => true, 'desc' => '地址id'),
                'discountID'  => array('name' => 'discountID', 'require' => true, 'desc' => '红包id'),
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
    /**
     * 创建订单
     * @desc 测试一下
     */
    public function createOneOrder() {
        $domain = new DomainOders();
        $res = $domain->insertOneOrder($this->userID,$this->foodArrID,$this->remark,$this->restID,$this->totalPrice,$this->payPrice,$this->addrID,$this->discountID);
        switch ($res) {
            case '-1':
                throw new InternalServerErrorException("新增订单失败", 12);
                break;
            case '-2':
                throw new InternalServerErrorException("变更红包状态失败", 13);
                break;
            default:
            return 'ok';
                break;
        }
    }
} 

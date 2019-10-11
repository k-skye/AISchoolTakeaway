<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\deliverorders as DomainDeliverorders;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 配送订单接口
 * @author: kang <knskye@gmail.com> 2019-10-11
 */


class Deliverorders extends Api {
    public function getRules() {
        return array(
            'createOneOrder' => array(
                'orderID'  => array('name' => 'orderID', 'require' => true, 'desc' => '订单id'),
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
            ),
        );
    }
    /**
     * 创建配送订单
     * @desc 测试一下
     */
    public function createOneOrder() {
        $domain = new DomainDeliverorders();
        $res = $domain->addOneOrder($this->orderID,$this->deliverID);
        if ($res > 0 ) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("新增配送订单失败", 16);
        }
    }
} 

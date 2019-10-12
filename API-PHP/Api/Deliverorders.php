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
            'getAllOrder' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
            'changToGetFood' => array(
                'orderID'  => array('name' => 'orderID', 'require' => true, 'desc' => '订单id'),
                'ID'  => array('name' => 'ID', 'require' => true, 'desc' => '配送订单id'),
            ),
            'changToFinishDelive' => array(
                'orderID'  => array('name' => 'orderID', 'require' => true, 'desc' => '订单id'),
                'ID'  => array('name' => 'ID', 'require' => true, 'desc' => '配送订单id'),
                'deliverID' => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
            ),
            'getOneUserAllOrderFinish' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
            'getAllOrderCountCanComment' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
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
    /**
     * 拿所有待配送订单
     * @desc 测试一下
     */
    public function getAllOrder() {
        $domain = new DomainDeliverorders();
        return $domain->getAllOrder($this->deliverID,$this->offset,$this->limit);
    }
    /**
     * 拿一个伙伴的所有订单
     * @desc 用在我的，所有订单界面
     */
    public function getOneUserAllOrderFinish() {
        $domain = new DomainDeliverorders();
        return $domain->getOneUserAllOrderFinish($this->deliverID,$this->offset,$this->limit);
    }
    /**
     * 拿一个伙伴的所有可评价订单
     * @desc 测试一下
     */
    public function getAllOrderCountCanComment() {
        $domain = new DomainDeliverorders();
        return $domain->getAllOrderCountCanComment($this->deliverID,$this->offset,$this->limit);
    }
    /**
     * 已取到商品
     * @desc 测试一下
     */
    public function changToGetFood() {
        $domain = new DomainDeliverorders();
        $res = $domain->changToGetFood($this->orderID,$this->ID);
        if ($res > 0 ) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("修改配送订单已取餐失败", 17);
        }
    }
    /**
     * 已送达
     * @desc 测试一下
     */
    public function changToFinishDelive() {
        $domain = new DomainDeliverorders();
        $res = $domain->changToFinishDelive($this->orderID,$this->ID,$this->deliverID);
        if ($res > 0 ) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("修改配送已送达订单失败", 18);
        }
    }
} 

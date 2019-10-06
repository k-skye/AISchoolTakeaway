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
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
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
                'openID'  => array('name' => 'openID', 'require' => true, 'desc' => 'openid'),
            ),
            'handlePay' => array(
                'payPrice'  => array('name' => 'payPrice', 'require' => true, 'desc' => '实际支付金额'),
                'orderNo'  => array('name' => 'orderNo', 'require' => true, 'desc' => '微信商户订单号'),
                'payTime'  => array('name' => 'payTime', 'require' => true, 'desc' => '支付时间')
            ),
        );
    }
    /**
     * 拿一个用户的所有订单
     * @desc 测试一下
     */
    public function getOnesAllOrders() {
        $domain = new DomainOders();
        return $domain->getOnesAllOrders($this->userID,$this->offset,$this->limit);
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
                $curl = new \PhalApi\CUrl();
                $t = time();
                $createTime = date('YmdHis'.$res,$t);
                $orderNo = $createTime.$res;
                $rres = $domain->updateOrderNo($res,$orderNo);
                if ($rres) {
                    $url = "https://takeawayapi.pykky.com/pay/jsapi.php?orderNo=".$orderNo."&payPrice=".$this->payPrice."&ordername=华广饭堂外卖&notifyUrl=https://takeawayapi.pykky.com/pay/notifyOrder.php&openID=".$this->openID;
                    $rs = $curl->get($url, 10000);
                    return $rs;
                }else{
                    throw new InternalServerErrorException("添加商户订单号失败状态失败", 14);
                }
                break;
        }
    }

    /**
     * 支付回调处理
     * @desc 测试一下
     */
    public function handlePay() {
        $domain = new DomainOders();
        $t = $this->payTime;
        $time = strtotime($t);
        $okTime = date('Y-m-d H:i:s',$time);
        $res = $domain->updateOrderPay($this->orderNo,$this->payPrice,$okTime);
        if ($res) {
            return 0;
        }else{
            return -1;
        }
    }
} 

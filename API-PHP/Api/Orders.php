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
                'page'  => array('name' => 'page', 'require' => true, 'desc' => '第几页'),
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
                'shouldDeliveTime'  => array('name' => 'shouldDeliveTime', 'require' => true, 'desc' => '配送时间'),
                'deliveFee'  => array('name' => 'deliveFee', 'require' => true, 'desc' => '配送费'),
                'upstairs'  => array('name' => 'upstairs', 'require' => true, 'desc' => '是否上楼'),
            ),
            'handlePay' => array(
                'payPrice'  => array('name' => 'payPrice', 'require' => true, 'desc' => '实际支付金额'),
                'orderNo'  => array('name' => 'orderNo', 'require' => true, 'desc' => '微信商户订单号'),
                'payTime'  => array('name' => 'payTime', 'require' => true, 'desc' => '支付时间')
            ),
            'getAllNeedDeliveOrders' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'page'  => array('name' => 'page', 'require' => true, 'desc' => '页数'),
            ),
            'cancelOrder' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '订单id'),
            ),
            'createOneExpressOrder' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'expressAddr'  => array('name' => 'expressAddr', 'require' => true, 'desc' => '取件地址'),
                'remark'  => array('name' => 'remark', 'require' => true, 'desc' => '订单备注'),
                'expressCode'  => array('name' => 'expressCode', 'require' => true, 'desc' => '取件码'),
                'totalPrice'  => array('name' => 'totalPrice', 'require' => true, 'desc' => '原价'),
                'payPrice'  => array('name' => 'payPrice', 'require' => true, 'desc' => '支付价格'),
                'addrID'  => array('name' => 'addrID', 'require' => true, 'desc' => '地址id'),
                'openID'  => array('name' => 'openID', 'require' => true, 'desc' => 'openid'),
                'shouldDeliveTime'  => array('name' => 'shouldDeliveTime', 'require' => true, 'desc' => '配送时间'),
                'deliveFee'  => array('name' => 'deliveFee', 'require' => true, 'desc' => '配送费'),
                'weight'  => array('name' => 'weight', 'require' => true, 'desc' => '重量'),
                'goodType'  => array('name' => 'goodType', 'require' => true, 'desc' => '快递类型'),
                'isNeedFast'  => array('name' => 'isNeedFast', 'require' => true, 'desc' => '需要加急'),
                'fastMoney'  => array('name' => 'fastMoney', 'require' => true, 'desc' => '加急红包金额'),
                'discountID'  => array('name' => 'discountID', 'require' => true, 'desc' => '红包id'),
            ),
            'handleExpressPay' => array(
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
        return $domain->getOnesAllOrders($this->userID,$this->page);
    }
    /**
     * 拿所有订单待接单的
     * @desc 测试一下
     */
    public function getAllNeedDeliveOrders() {
        $domain = new DomainOders();
        return $domain->getAllOrdersOnNeedDelive($this->deliverID,$this->page);
    }
    /**
     * 创建订单
     * @desc 测试一下
     */
    public function createOneOrder() {
        $domain = new DomainOders();
        $res = $domain->insertOneOrder($this->userID,$this->foodArrID,$this->remark,$this->restID,$this->totalPrice,$this->payPrice,$this->addrID,$this->discountID,$this->shouldDeliveTime,$this->deliveFee,$this->upstairs);
        switch ($res) {
            case (-1):
                throw new InternalServerErrorException("新增订单失败", 12);
                break;
            case (-2):
                throw new InternalServerErrorException("变更红包状态失败", 13);
                break;
            default:
                $curl = new \PhalApi\CUrl(3);//失败重试3次
                $t = time();
                $createTime = date('YmdHis'.$res,$t);
                $orderNo = $createTime.$res;
                $rres = $domain->updateOrderNo($res,$orderNo);
                if ($rres) {
                    $url = "https://takeawayapi.pykky.com/pay/jsapi.php?orderNo=".$orderNo."&payPrice=".$this->payPrice."&ordername=华广饭堂外卖&notifyUrl=https://takeawayapi.pykky.com/pay/notifyOrder.php&openID=".$this->openID;
                    $rs = $curl->get($url, 5000);
                    return $rs;
                }else{
                    throw new InternalServerErrorException("添加商户订单号失败", 14);
                }
                break;
        }
    }
    /**
     * 创建快递订单
     * @desc 测试一下
     */
    public function createOneExpressOrder() {
        $domain = new DomainOders();
        $res = $domain->insertOneExpressOrder($this->userID,$this->expressAddr,$this->remark,$this->expressCode,$this->totalPrice,$this->payPrice,$this->addrID,$this->shouldDeliveTime,$this->deliveFee,$this->weight,$this->goodType,$this->isNeedFast,$this->fastMoney,$this->discountID);
        switch ($res) {
            case (-1):
                throw new InternalServerErrorException("新增订单失败", 33);
                break;
            default:
                $curl = new \PhalApi\CUrl(3);//失败重试3次
                $t = time();
                $createTime = date('YmdHis'.$res,$t);
                $orderNo = $createTime.$res;
                $rres = $domain->updateOrderNo($res,$orderNo);
                if ($rres) {
                    $url = "https://takeawayapi.pykky.com/pay/jsapi.php?orderNo=".$orderNo."&payPrice=".$this->payPrice."&ordername=华广快递代拿&notifyUrl=https://takeawayapi.pykky.com/pay/notifyExpressOrder.php&openID=".$this->openID;
                    $rs = $curl->get($url, 5000);
                    return $rs;
                }else{
                    throw new InternalServerErrorException("快递代拿添加商户订单号失败", 34);
                }
                break;
        }
    }

    /**
     * 美食跑腿支付回调处理
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
    /**
     * 快递代拿支付回调处理
     * @desc 测试一下
     */
    public function handleExpressPay() {
        $domain = new DomainOders();
        $t = $this->payTime;
        $time = strtotime($t);
        $okTime = date('Y-m-d H:i:s',$time);
        $res = $domain->updateExpressOrderPay($this->orderNo,$this->payPrice,$okTime);
        if ($res) {
            return 0;
        }else{
            return -1;
        }
    }
    /**
     * 在伙伴接单前取消订单
     * @desc 测试一下
     */
    public function cancelOrder() {
        $domain = new DomainOders();
        $res = $domain->cancelOrder($this->id,0);
        if ($res) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("服务器取消订单失败", 27);
        }
    }
    /**
     * 自动30分钟定时退款
     * @desc 测试一下
     */
    public function autoCancelOrder() {
        $domain = new DomainOders();
        //取n时间前的订单
        $theTime = strtotime("-48 hour");
        $needCancelOrderArr = $domain->getNeedCancelOrder($theTime);
        $res = "";
        foreach ($needCancelOrderArr as $value) {
            //对每一个订单，请求退款接口
            //支付时间
            $paytimeUnixTime = (int)date(strtotime($value['payTime']));
            $createtimeUnixTime = (int)date(strtotime($value['createTime']));
            //预约单
            $shouldDelivetimeUnixTime = (int)date(strtotime($value['shouldDeliveTime']));
            $nowtimeUnix = (int)strtotime("now");
            if ((($nowtimeUnix-$paytimeUnixTime)>1800) && (((int)$value['status'])==1) && (($shouldDelivetimeUnixTime-$nowtimeUnix)<1800)){
                $res = $res.$value['id'].'-'.$domain->cancelOrder($value['id'],0).';';
                //测试$res = $value['id'];
            }
            if ((($nowtimeUnix-$createtimeUnixTime)>1800) && (((int)$value['status'])==0)){
                $res = $res.$value['id'].'-'.$domain->cancelOrder($value['id'],1).';';
            }
        }
        if (!empty($res)) {
            //return 'ok';
            return $res;
        }else{
            throw new InternalServerErrorException("自动取消订单失败", 30);
        }
    }
} 

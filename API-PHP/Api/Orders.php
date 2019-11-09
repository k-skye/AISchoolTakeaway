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
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
            'cancelOrder' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '订单id'),
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
     * 拿所有订单待接单的
     * @desc 测试一下
     */
    public function getAllNeedDeliveOrders() {
        $domain = new DomainOders();
        return $domain->getAllOrdersOnNeedDelive($this->deliverID,$this->offset,$this->limit);
    }
    /**
     * 创建订单
     * @desc 测试一下
     */
    public function createOneOrder() {
        $domain = new DomainOders();
        $res = $domain->insertOneOrder($this->userID,$this->foodArrID,$this->remark,$this->restID,$this->totalPrice,$this->payPrice,$this->addrID,$this->discountID,$this->shouldDeliveTime,$this->deliveFee,$this->upstairs);
        switch ($res) {
            case '-1':
                throw new InternalServerErrorException("新增订单失败", 12);
                break;
            case '-2':
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
    /**
     * 在伙伴接单前取消订单
     * @desc 测试一下
     */
    public function cancelOrder() {
        $domain = new DomainOders();
        $res = $domain->cancelOrder($this->id);
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
        //取60分钟前的订单
        $theTime = strtotime("-48 hour");
        $needCancelOrderArr = $domain->getNeedCancelOrder($theTime);
        $res = "";
        foreach ($needCancelOrderArr as $value) {
            //对每一个订单，请求退款接口
            //支付时间
            $paytimeUnixTime = (int)date(strtotime($value['payTime']));
            //预约单
            $shouldDelivetimeUnixTime = (int)date(strtotime($value['shouldDeliveTime']));
            $nowtimeUnix = (int)strtotime("now");
            if ((($nowtimeUnix-$paytimeUnixTime)>1800) && (((int)$value['status'])==1) && (($shouldDelivetimeUnixTime-$nowtimeUnix)<3600)){
                $res += $value['id'].$domain->cancelOrder($value['id']).';';
                //测试$res = $value['id'];
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

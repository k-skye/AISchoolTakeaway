<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\tradinglog as DomainTradinglog;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Exception\BadRequestException;

/**
 * 账单接口
 * @author: kang <knskye@gmail.com> 2019-08-22
 */


class TradingLog extends Api {
    public function getRules() {
        return array(
            'getOnesAllTradLog' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
            'cashOneUser' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
            ),
            'createOneCompensate' => array(
                'orderID'  => array('name' => 'orderID', 'require' => true, 'desc' => '订单id'),
                'openID'  => array('name' => 'openID', 'require' => true, 'desc' => 'openid')
            ),
            'handleCompensatePay' => array(
                'payPrice'  => array('name' => 'payPrice', 'require' => true, 'desc' => '实际支付金额'),
                'orderNo'  => array('name' => 'orderNo', 'require' => true, 'desc' => '微信商户订单号'),
                'payTime'  => array('name' => 'payTime', 'require' => true, 'desc' => '支付时间')
            ),
        );
    }
    /**
     * 拿某人所有账单明细
     * @desc 测试一下
     */
    public function getOnesAllTradLog() {
        $domain = new DomainTradinglog();
        return $domain->getOnesAllTradLog($this->deliverID,$this->offset,$this->limit);
    }
    /**
     * 跑腿支付回调处理
     * @desc 测试一下
     */
    public function handleCompensatePay() {
        $domain = new DomainTradinglog();
        $t = $this->payTime;
        $time = strtotime($t);
        $okTime = date('Y-m-d H:i:s',$time);
        $res = $domain->updateCompensatePay($this->orderNo,$this->payPrice,$okTime);
        if ($res) {
            return 0;
        }else{
            return -1;
        }
    }
    /**
     * 创建支付尾款记录和订单
     * @desc 测试一下
     */
    public function createOneCompensate() {
        $domain = new DomainTradinglog();
        $res = $domain->createOneCompensate($this->orderID);
        if ($res) {
            $curl = new \PhalApi\CUrl(3);//失败重试3次
                $t = time();
                $createTime = date('YmdHis'.$res,$t);
                $orderNo = $createTime.$res;
                $rres = $domain->updateCompensate($res,$orderNo);
                if ($rres) {
                    $url = "https://takeawayapi.pykky.com/pay/jsapi.php?orderNo=".$orderNo."&payPrice=3.0&ordername=华广快递代拿尾款&notifyUrl=https://takeawayapi.pykky.com/pay/notifyCompensateOrder.php&openID=".$this->openID;
                    $rs = $curl->get($url, 5000);
                    return $rs;
                }else{
                    throw new InternalServerErrorException("快递代拿添加尾款订单号失败", 36);
                }
        }else {
            throw new InternalServerErrorException("新增支付尾款记录失败", 35);
        }
    }
    /**
     * 提现到微信零钱
     * @desc 测试一下
     */
    public function cashOneUser() {
        $domain = new DomainTradinglog();
        $res = $domain->addCashTradLog($this->deliverID);
        switch ($res) {
            case 0:
                return 'ok';
                break;
            case -2:
                throw new InternalServerErrorException("记录提现账单失败", 21);
                break;
            case -3:
                throw new InternalServerErrorException("变更余额失败", 22);
                break;
            case -4:
                throw new InternalServerErrorException("记录上次提现时间失败", 23);
                break;
            case -10:
                throw new BadRequestException("满1元才可提现", 3);
                break;
            case -11:
                throw new BadRequestException("离上次提现未满24小时", 4);
                break;
            default:
                throw new InternalServerErrorException($res, 24);
                break;
        }
    }

    /**
     * 自动指定时间更新配送余额
     * @desc 测试一下
     */
    public function autoDoneLog() {
        $domain = new DomainTradinglog();
        $res = $domain->autoDoneLog();
        if ($res != -1) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("自动更新配送余额失败", 31);
        }
    }
} 

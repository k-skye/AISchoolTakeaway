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
} 

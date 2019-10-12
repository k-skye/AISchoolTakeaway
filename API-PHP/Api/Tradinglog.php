<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\tradinglog as DomainTradinglog;
use PhalApi\Exception\InternalServerErrorException;

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
} 

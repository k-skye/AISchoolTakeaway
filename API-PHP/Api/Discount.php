<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\discount as DomainDiscount;

/**
 * 红包接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class Discount extends Api {
    public function getRules() {
        return array(
            'getOnesAllcounts' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
            ),
        );
    }
    /**
     * 拿一个用户的所有红包，包括可用和不可用
     * @desc 测试一下
     */
    public function getOnesAllcounts() {
        $domain = new DomainDiscount();
        $res = $domain->getOnesAllcounts($this->userID);
        if ($res == -1) {
            return 0;//这个用户没有红包
        }else{
            return $res;
        }
    }
} 

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
                'type'  => array('name' => 'type', 'require' => false, 'desc' => '业务类型'),
            ),
            'getOnesDiscounts' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '红包id'),
            ),
        );
    }
    /**
     * 拿一个用户的所有红包，包括可用和不可用
     * @desc 测试一下
     */
    public function getOnesAllcounts() {
        $domain = new DomainDiscount();
        if (empty($this->type)) {
            $this->type = 0;
        }
        $res = $domain->getOnesAllcounts($this->userID,$this->type);
        if ($res == -1) {
            return 0;//这个用户没有红包
        }else{
            return $res;
        }
    }
    /**
     * 用红包id拿某个红包信息
     * @desc 测试一下
     */
    public function getOnesDiscounts() {
        $domain = new DomainDiscount();
        return $domain->getOnesDiscounts($this->id);
    }
    
} 

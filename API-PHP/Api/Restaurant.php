<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\restaurant as DomainRestaurant;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 店铺接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class Restaurant extends Api {
    public function getRules() {
        return array(
            'getRestsByRule' => array(
                'page'  => array('name' => 'page', 'require' => true, 'desc' => '第几页'),
                'condition'  => array('name' => 'condition', 'require' => false, 'desc' => '筛选规则')
            ),
            'getOneRest' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '店铺id'),
            ),
            'getRestsAdmin' => array(
                'page'  => array('name' => 'page', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
                'rate'  => array('name' => 'rate', 'require' => false, 'desc' => '评分'),
                'name'  => array('name' => 'name', 'require' => false, 'desc' => '店铺名'),
                'status'  => array('name' => 'status', 'require' => false, 'desc' => '状态'),
                'sort'  => array('name' => 'sort', 'require' => true, 'desc' => '按id正逆排序'),
            ),
            'addRestsAdmin' => array(
                'name'  => array('name' => 'name', 'require' => true, 'desc' => '店铺名'),
                'roomNum'  => array('name' => 'roomNum', 'require' => true, 'desc' => '第几饭堂'),
                'location'  => array('name' => 'location', 'require' => true, 'desc' => '具体位置'),
                'logo'  => array('name' => 'logo', 'require' => true, 'desc' => '图片地址')
            ),
            'getRestsByRuleWithRoomNum' => array(
                'page'  => array('name' => 'page', 'require' => true, 'desc' => '第几页'),
                'condition'  => array('name' => 'condition', 'require' => false, 'desc' => '筛选规则'),
                'roomNum'  => array('name' => 'roomNum', 'require' => true, 'desc' => '餐厅编号'),
            ),
        );
    }
    /**
     * 拿指定规则排序的店铺信息
     * @desc 测试一下
     */
    public function getRestsByRule() {
        $domain = new DomainRestaurant();
        return $domain->getRestsByRule($this->page,$this->condition);
    }
    /**
     * 拿某个店铺信息
     * @desc 测试一下
     */
    public function getOneRest() {
        $domain = new DomainRestaurant();
        return $domain->getOneRest($this->id);
    }
    /**
     * 店铺列表
     * @desc 测试一下
     */
    public function getRestsAdmin() {
        $domain = new DomainRestaurant();
        if (empty($this->rate)) {
            $this->rate = "";
        }
        if (empty($this->name)) {
            $this->name = "";
        }
        if (empty($this->status)) {
            $this->status = "";
        }
        return $domain->getRestsAdmin($this->page,$this->limit,$this->rate,$this->name,$this->status,$this->sort);
    }
    /**
     * 添加店铺
     * @desc 测试一下
     */
    public function addRestsAdmin() {
        $domain = new DomainRestaurant();
        $res = $domain->addRestsAdmin($this->name,$this->roomNum,$this->location,$this->logo);
        if ($res > 0) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("添加店铺状态失败", 15);
        }
    }
    /**
     * 拿指定规则排序的店铺信息在某一个餐厅
     * @desc 测试一下
     */
    public function getRestsByRuleWithRoomNum() {
        $domain = new DomainRestaurant();
        return $domain->getRestsByRuleWithRoomNum($this->page,$this->condition,$this->roomNum);
    }
} 

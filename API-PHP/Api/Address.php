<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\address as DomainAddress;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 地址接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class Address extends Api {
    public function getRules() {
        return array(
            'getOneAddr' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '地址id'),
            ),
            'getUserAddrs' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
            ),
            'addAddr' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'dormitory'  => array('name' => 'dormitory', 'require' => true, 'desc' => '宿舍'),
                'roomNum'  => array('name' => 'roomNum', 'require' => true, 'desc' => '房间'),
                'gender'  => array('name' => 'gender', 'require' => true, 'desc' => '性别'),
                'name'  => array('name' => 'name', 'require' => true, 'desc' => '名字'),
                'phone'  => array('name' => 'phone', 'require' => true, 'desc' => '手机号')
            ),
            'changeAddr' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '地址id'),
                'dormitory'  => array('name' => 'dormitory', 'require' => true, 'desc' => '宿舍'),
                'roomNum'  => array('name' => 'roomNum', 'require' => true, 'desc' => '房间'),
                'gender'  => array('name' => 'gender', 'require' => true, 'desc' => '性别'),
                'name'  => array('name' => 'name', 'require' => true, 'desc' => '名字'),
                'phone'  => array('name' => 'phone', 'require' => true, 'desc' => '手机号')
            ),
            'removeAddr' => array(
                'id'  => array('name' => 'id', 'require' => true, 'desc' => '地址id')
            )
        );
    }
    /**
     * 拿一个地址
     * @desc 测试一下
     */
    public function getOneAddr() {
        $domain = new DomainAddress();
        return $domain->getOneAddr($this->id);
    }
    /**
     * 拿一个用户的所有地址
     * @desc 测试一下
     */
    public function getUserAddrs() {
        $domain = new DomainAddress();
        return $domain->getUserAddrs($this->userID);
    }
    /**
     * 添加新地址
     * @desc 测试一下
     */
    public function addAddr() {
        $domain = new DomainAddress();
        $res = $domain->addAddr($this->userID,$this->dormitory,$this->roomNum,$this->gender,$this->name,$this->phone);
        switch ($res) {
            case '-1':
                throw new InternalServerErrorException('添加地址失败', 3);
                break;
            case '-2':
                throw new InternalServerErrorException('修改默认收货地址id失败', 11);
                break;
            default:
                return $res;
                break;
        }
    }
    /**
     * 更新地址
     * @desc 测试一下
     */
    public function changeAddr() {
        $domain = new DomainAddress();
        $res = $domain->changeAddr($this->id,$this->dormitory,$this->roomNum,$this->gender,$this->name,$this->phone);
        if ($res == 0) {
            return $res;
        }else {
            throw new InternalServerErrorException('更新地址失败', 4);
        }
    }
    /**
     * 删除地址
     * @desc 测试一下
     */
    public function removeAddr() {
        $domain = new DomainAddress();
        $res = $domain->removeAddr($this->id);
        if ($res == 0) {
            return $res;
        }else {
            throw new InternalServerErrorException('删除地址失败', 5);
        }
    }
} 

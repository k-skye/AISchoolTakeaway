<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\users as DomainUsers;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Exception\BadRequestException;

/**
 * 用户接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class Users extends Api {
    public function getRules() {
        return array(
            'userLogin' => array(
                'firstLogin'  => array('name' => 'firstLogin', 'require' => true, 'desc' => '是否第一次登陆'),
                'phoneNo'  => array('name' => 'phoneNo', 'require' => true, 'desc' => '手机号码'),
                'loginCode'  => array('name' => 'loginCode', 'require' => true, 'desc' => '验证码'),
                'codeID'  => array('name' => 'codeID', 'require' => true, 'desc' => '验证码id'),
                'name'  => array('name' => 'name', 'desc' => '学生名字'),
                'avatar'  => array('name' => 'avatar', 'desc' => '头像'),
                'stuID'  => array('name' => 'stuID', 'desc' => '学号')
            ),
            'sendMessage' => array(
                'phoneNo'  => array('name' => 'phoneNo', 'require' => true, 'desc' => '手机号码'),
            )
        );
    }

    /**
     * 发送短信
     * @desc 利用阿里云短信API模版
     */
    public function sendMessage() {
        $domain = new DomainUsers();
        $codeID = $domain->sendMessage($this->phoneNo);//返回新增id
        if ($codeID > 0) {
            $firstLogin = $domain->checkFirst($this->phoneNo);//检测是否已存在，第一次注册登陆
            if ($firstLogin == -1) {
                return array(
                    'firstLogin' => '1',
                    'codeID' => $codeID
                );
            }else {
                return array(
                    'firstLogin' => '0',
                    'codeID' => $codeID
                );
            }
        }else {
            throw new InternalServerErrorException("新增验证码错误", 1);
        }
    }
    /**
     * 用户登陆
     * @desc 提交验证码验证并登陆
     */
    public function userLogin() {
        $domain = new DomainUsers();
        $res = $domain->userLogin($this->firstLogin,$this->loginCode,$this->codeID,$this->name,$this->phoneNo,$this->avatar,$this->stuID);
        switch ($res) {
            case '0':
                return array(
                    'name' => $this->name
                );
                break;
            case '-1':
                throw new InternalServerErrorException('登陆错误', 2);
                break;    
            case '-2':
                throw new BadRequestException('验证码错误', 1);
                break;  
            default:
                return array(
                    'name' => $res
                );
                break;
        }
    }
    /**
     * 拿所有用户信息
     * @desc 测试一下
     */
    public function getAllUsers() {
        $domain = new DomainUsers();
        return $domain->getAllUsers();
    }
} 

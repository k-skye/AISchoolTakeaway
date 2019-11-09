<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\deliverusers as DomainUsers;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Exception\BadRequestException;

/**
 * 伙伴接口
 * @author: kang <knskye@gmail.com> 2019-08-21
 */


class DeliverUsers extends Api {
    public function getRules() {
        return array(
            'userReg' => array(
                'phoneNo'  => array('name' => 'phoneNo', 'require' => true, 'desc' => '手机号码'),
                'loginCode'  => array('name' => 'loginCode', 'require' => true, 'desc' => '验证码'),
                'codeID'  => array('name' => 'codeID', 'require' => true, 'desc' => '验证码id'),
                'stuID'  => array('name' => 'stuID', 'require' => true, 'desc' => '学号'),
                'openid'  => array('name' => 'openid', 'require' => true, 'desc' => 'openid'),
                'realName'  => array('name' => 'realName', 'require' => true, 'desc' => '真实姓名'),
                'sex'  => array('name' => 'sex', 'require' => true, 'desc' => '性别'),
                'cardImg'  => array('name' => 'cardImg', 'require' => true, 'desc' => '学生证图片url'),
            ),
            'sendMessage' => array(
                'phoneNo'  => array('name' => 'phoneNo', 'require' => true, 'desc' => '手机号码'),
            ),
            'getOpenid' => array(
                'code'  => array('name' => 'code', 'require' => true, 'desc' => '微信CODE'),
            ),
            'getUserInfo' => array(
                'openid'  => array('name' => 'openid', 'require' => true, 'desc' => 'openid'),
            ),
            'getInfoInWechat' => array(
                'code'  => array('name' => 'code', 'require' => true, 'desc' => '微信CODE'),
            ),
            'changeUserInfoOnChooseByUserId' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '配送员id'),
                'chooseAddr'  => array('name' => 'chooseAddr', 'require' => true, 'desc' => '选择宿舍'),
                'chooseRest'  => array('name' => 'chooseRest', 'require' => true, 'desc' => '选择饭堂'),
                'chooseNear'  => array('name' => 'chooseNear', 'require' => true, 'desc' => '是否附近宿舍'),
            ),
            'changeUserInfoOnSendMessage' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '配送员id'),
                'sendMessage'  => array('name' => 'sendMessage', 'require' => true, 'desc' => '是否开启提醒'),
            ),
        );
    }

    /**
     * 管理员登陆
     * @desc 测试一下
     */
    public function loginAdmin() {
        return array("token" => "admin-token");
    }

    /**
     * 管理员登出
     * @desc 测试一下
     */
    public function logout() {
        return "success";
    }

    /**
     * 更改筛选
     * @desc 测试一下
     */
    public function changeUserInfoOnChooseByUserId() {
        $domain = new DomainUsers();
        $res = $domain->changeUserInfoOnChooseByUserId($this->userID,$this->chooseAddr,$this->chooseRest,$this->chooseNear);
        if ($res > 0) {
            return 'ok';
        }else {
            throw new InternalServerErrorException("修改筛选错误", 20);
        }
    }

    /**
     * 开关微信提醒
     * @desc 测试一下
     */
    public function changeUserInfoOnSendMessage() {
        $domain = new DomainUsers();
        $res = $domain->changeUserInfoOnSendMessage($this->userID,$this->sendMessage);
        if ($res > 0) {
            return 'ok';
        }else {
            throw new InternalServerErrorException("修改开关提醒错误", 32);
        }
    }

    /**
     * 发送短信
     * @desc 利用阿里云短信API模版
     */
    public function sendMessage() {
        $domain = new DomainUsers();
        $check = $domain->checkPhoneExist($this->phoneNo);
        if ($check == -1) {
            throw new BadRequestException('手机号已存在', 2);
        }
        $codeID = $domain->sendMessage($this->phoneNo);//返回新增id
        if ($codeID > 0) {
            return array(
                'codeID' => $codeID
            );
        }else {
            throw new InternalServerErrorException("新增验证码错误", 1);
        }
    }
    /**
     * 用户注册
     * @desc 提交验证码验证并注册
     */
    public function userReg() {
        $domain = new DomainUsers();
        $res = $domain->userReg($this->loginCode,$this->codeID,$this->phoneNo,$this->stuID,$this->openid,$this->realName,$this->sex,$this->cardImg);
        switch ($res) {
            case '0':
                return $res;
                break;
            case '-1':
                throw new InternalServerErrorException('更新数据和注册失败', 2);
                break;    
            case '-2':
                throw new BadRequestException('验证码错误', 1);
                break;  
            default:
                return $res;
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
    /**
     * 微信登陆回调获取openid
     * @desc 测试一下
     */
    public function getOpenid() {
        //用拿到的code请求微信服务器拿openid
        $curl = new \PhalApi\CUrl();
        $appid = 'wx3df92dead7bcd174';
        $appsecret = 'd6bade00fdeec6e09500d74a9d3fb15b';
        $rs = $curl->get('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$this->code.'&grant_type=authorization_code', 3000);
        $rs = json_decode($rs);
        if (property_exists($rs,'openid')) {
            $domain = new DomainUsers();
            $res = $domain->checkFirstByOpenid($rs->openid);
            if ($res == -1) {
                throw new InternalServerErrorException('创建openid失败', 7);
            }else {
                //跳转回去前端处理
                switch ($res) {
                    case '1':
                        $to = "location:http://takeawaydeliver.pykky.com/?firstlogin=1&openid=".$rs->openid;
                        header($to);
                        break;
                    
                    case '2':
                        $to = "location:http://takeawaydeliver.pykky.com/?firstlogin=1&openid=".$rs->openid;
                        header($to);
                        break;

                    default:
                        $to = "location:http://takeawaydeliver.pykky.com/?firstlogin=0&openid=".$rs->openid;
                        header($to);
                        break;
                }
            }
        }else {
            throw new InternalServerErrorException($rs->errmsg, 6);
        }
    }
    /**
     * 获取一个用户详细信息
     * @desc 测试一下
     */
    public function getUserInfo() {
        $domain = new DomainUsers();
        return $domain->getOneUserInfo($this->openid);
    }
    /**
     * 注册前微信登陆获取详细个人信息
     * @desc 测试一下
     */
    public function getInfoInWechat() {
        //用拿到的code请求微信服务器拿openid
        $curl = new \PhalApi\CUrl();
        $appid = 'wx3df92dead7bcd174';
        $appsecret = 'd6bade00fdeec6e09500d74a9d3fb15b';
        $rs = $curl->get('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$appsecret.'&code='.$this->code.'&grant_type=authorization_code', 3000);
        $rs = json_decode($rs);
        if (property_exists($rs,'openid') && property_exists($rs,'access_token')) {
            $openid = $rs->openid;
            $at = $rs->access_token;
            $rrs = $curl->get('https://api.weixin.qq.com/sns/userinfo?access_token='.$at.'&openid='.$openid.'&lang=zh_CN', 3000);
            $rrs = json_decode($rrs);
            if (property_exists($rrs,'nickname')) {
                $domain = new DomainUsers();
                //去除emoji-bug
                $okname = $rrs->nickname;
                $okname = preg_replace_callback(
                    '/./u',
                    function (array $match) {
                        return strlen($match[0]) >= 4 ? '' : $match[0];
                    },
                    $okname);
                $res = $domain->saveWechatUserInfo($rrs->openid,$okname,$rrs->city,$rrs->headimgurl);
                if ($res == -1) {
                    throw new InternalServerErrorException('更新用户信息失败', 10);
                }else {
                    //跳转回去前端处理
                    $to = "location:http://takeawaydeliver.pykky.com/register?isregister=1&openid=".$openid;
                    header($to);
                }
            }else {
                throw new InternalServerErrorException($rrs->errmsg, 9);
            }
        }else {
            throw new InternalServerErrorException($rs->errmsg, 8);
        }
    }
} 

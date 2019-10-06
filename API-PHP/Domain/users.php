<?php
namespace App\Domain;

use App\Model\users as ModelUsers;
use App\Model\phonecode as ModelPhoneCode;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;
use App\Model\address as ModelAddress;

class users {

    public function getAllUsers() {
        $model = new ModelUsers();
        return $model->getAllUsers();
    }

    public function sendMessage($phoneNo) {
        //生成6位随机数
        $randomCode = mt_rand(100000,999999);
        $errors = '';
        $res = '';
        AlibabaCloud::accessKeyClient('LTAI4FtNRprzcJ7ofnrKqt1u', 'BQro1eMV1iCOuHeKWTvBF3xRySJuMV')
                        ->regionId('cn-hangzhou')
                        ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                          ->product('Dysmsapi')
                          // ->scheme('https') // https | http
                          ->version('2017-05-25')
                          ->action('SendSms')
                          ->method('POST')
                          ->host('dysmsapi.aliyuncs.com')
                          ->options([
                                        'query' => [
                                          'RegionId' => "cn-hangzhou",
                                          'PhoneNumbers' => "$phoneNo",
                                          'SignName' => "华广",
                                          'TemplateCode' => "SMS_173474547",
                                          'TemplateParam' => "{\"code\":\"$randomCode\"}",
                                        ],
                                    ])
                          ->request();
            // print_r($result->toArray());
            $res = $result->toArray();
        } catch (ClientException $e) {
            $errors = $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            $errors = $e->getErrorMessage() . PHP_EOL;
        }
        //保存随机数
        $modelPhoneCode = new ModelPhoneCode();
        $codeID = $modelPhoneCode->saveCode($phoneNo,$randomCode);
        if ($codeID > 0) {
            return $codeID;
        }else {
            return -1;
        }
    }

    public function checkFirstByOpenid($openid) {
        //检测是否存在这个openid，存在就返回openid对应用户信息
        $modelUser = new ModelUsers();
        $res = $modelUser->getOneUserByOpenid($openid);
        if (empty($res)) {
            //不存在就插入新的用户，把openid存进去
            $rs = $modelUser->createOpenid($openid);
            if ($rs > 0) {
                //插入成功
                return 1;
            }else {
                return -1;
            }
        }else {
            //已存在了，判断有没有信息在里面，有的话返回firstlogin1不然就是0
            if (empty($res['phoneNo'])) {
                return 2;
            }else{
                return 3;
            }
        }
    }

    public function checkPhoneExist($phoneNo) {
        //检测是否存在这个openid，存在就返回openid对应用户信息
        $modelUser = new ModelUsers();
        $res = $modelUser->getOneUserByPhone($phoneNo);
        if (!empty($res)) {
            return -1;
        }
    }

    public function getOneUserInfo($openid) {
        //检测是否存在这个openid，存在就返回openid对应用户信息
        $modelUser = new ModelUsers();
        $res = $modelUser->getOneUserByOpenid($openid);
        if (empty($res['phoneNo'])) {
            $res['firstlogin'] = 1;
        }else{
            $res['firstlogin'] = 0;
        }
        return $res;
    }

    public function saveWechatUserInfo($openid,$nickname,$city,$headimgurl) {
        $modelUser = new ModelUsers();
        $res = $modelUser->saveWechatUserInfo($openid,$nickname,$city,$headimgurl);
        if ($res == 0 || $res) {//数据已更新或无变化
            return 0;
        }else {
            return -1;//更新异常
        }
    }
    
    public function userReg($loginCode,$codeID,$phoneNo,$stuID,$openid) {
        //检测code是否正确
        $modelPhoneCode = new ModelPhoneCode();
        $modelUser = new ModelUsers();
        $randomCode = $modelPhoneCode->getCode($codeID);
        if ($randomCode == $loginCode) {
            //正确
                $res = $modelUser->changeUserInfo($phoneNo,$stuID,$openid);
                if ($res == 0 || $res) {//数据已更新或无变化
                    return 0;
                }else {
                    return -1;//更新异常
                }
        }else{
            //错误
            return -2;
        }
    }
}

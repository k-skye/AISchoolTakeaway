<?php
namespace App\Domain;

use App\Model\users as ModelUsers;
use App\Model\phonecode as ModelPhoneCode;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

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
        $model = new ModelPhoneCode();
        $codeID = $model->saveCode($phoneNo,$randomCode);
        if ($codeID > 0) {
            return $codeID;
        }else {
            return -1;
        }
    }

    public function checkFirst($phoneNo) {
        //检测是否首次登陆
        $modelUser = new ModelUsers();
        $res = $modelUser->getOneUser($phoneNo);
        if (empty($res)) {
            return -1;
        }else {
            return 0;
        }
    }
    
    public function userLogin($firstLogin,$loginCode,$codeID,$name,$phoneNo,$avatar,$stuID) {
        //检测code是否正确
        $model = new ModelPhoneCode();
        $modelUser = new ModelUsers();
        $randomCode = $model->getCode($codeID);
        if ($randomCode == $loginCode) {
            //正确
            if ($firstLogin == 1) {
                //去users表创建新用户
                $res = $modelUser->createUsers($name,$phoneNo,$avatar,$stuID);
                if ($res > 0) {
                    return 0;
                }else {
                    return -1;
                }
            }else{
                //查找已存在的，返回学生名字
                $res = $modelUser->getOneUser($phoneNo);
                if (empty($res)) {
                    return -1;
                }else {
                    return $res['name'];
                }
            }
        }else{
            //错误
            return -2;
        }
    }
}

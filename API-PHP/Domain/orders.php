<?php
namespace App\Domain;

use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;
use App\Model\discount as ModelDiscount;
use App\Model\address as ModelAddress;
use App\Model\deliverusers as ModelDeliverUser;
use App\Model\deliverorders as ModelDeliverorders;
use App\Model\users as ModelUsers;

class WeixinPush
{
    protected $appid;
    protected $secret;
    protected $accessToken;
    function  __construct($appid, $secret)
    {
        if ($appid && $secret) {
            $this->appid = $appid;
            $this->secret = $secret;
            $this->accessToken = $this->getToken($appid, $secret);
        }
        
    }
    /**
     * 发送post请求
     * @param string $url
     * @param string $param
     * @return bool|mixed
     */
    function http_request($url, $data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }
    /**
     * 发送get请求
     * @param string $url
     * @return bool|mixed
     */
    function request_get($url = '')
    {
        if (empty($url)) {
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    /**
     * 获取token
     * @param $appid
     * @param $appsecret
     * @return mixed
     */
    protected function getToken($appid, $appsecret)
    {
		$token_access_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $appsecret;
		$res = file_get_contents($token_access_url);    //获取文件内容或获取网络请求的内容
		//echo $res;
		$result = json_decode($res, true);   //接受一个 JSON 格式的字符串并且把它转换为 PHP 变量
		$access_token = $result['access_token'];
		return $access_token;
    }
    /**
     * 发送自定义的模板消息
     * @param $touser
     * @param $template_id
     * @param $url
     * @param $data
     * @param string $topcolor
     * @return bool
     */
    public function doSend($touser, $template_id, $url,$sdata, $topcolor = '#7B68EE')
    {   
        $template = array(
            'touser' => $touser,
            'template_id' => $template_id,
            'url' => $url,
            'topcolor' => $topcolor,
            'data' => $sdata
        );
        $json_template = json_encode($template);
        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $this->accessToken;
        $dataRes = $this->http_request($url, urldecode($json_template));
        return true;
/*         if (((int)$dataRes['errcode']) == 0) {
            return true;
        } else {
            return false;
        } */
    }
}

class orders {

    public function getOnesAllOrders($userID,$page) {
        $model = new ModelOders();
        $arr = $model->getOnesAllOrders($userID,$page);
        $i = 0;
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelUser = new ModelDeliverUser();
        $modelDeliverOrder = new ModelDeliverorders();
        foreach ($arr as $value){
            $restID = $arr[$i]['restID'];
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['restName'] = $restRes['name'];
            $arr[$i]['restLogo'] = $restRes['logo'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            if((int)$arr[$i]['status'] >= 2){
                //去deliverorder表查配送信息
                $deliverOrder = $modelDeliverOrder->getOneOrderByorderID($arr[$i]['id']);
                $deliID = $deliverOrder['deliverID'];
                $arr[$i]['deliverID'] = $deliID;
                $arr[$i]['delivedTime'] = $deliverOrder['delivedTime'];
                $deliverInfo = $modelUser->getOneUserByUserID($deliID);
                $arr[$i]['deliverName'] = (mb_substr($deliverInfo['realName'], 0, 1)).'同学';
                $arr[$i]['deliverPhone'] = $deliverInfo['phoneNo'];
            }else {
                $arr[$i]['deliverID'] = '';
            }
            $i++;
        }
        return $arr;
    }

    public function getAllOrdersOnNeedDelive($deliverID,$page) {
        $model = new ModelOders();
        $arr = $model->getAllOrdersOnNeedDelive($page);
        $i = 0;
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $modelUser = new ModelDeliverUser();
        foreach ($arr as $value){
            $restID = $arr[$i]['restID'];
            $addrID = $arr[$i]['addressID'];
            //处理时间
            $date=date_create($arr[$i]['shouldDeliveTime']);
            $output=date_format($date,"H:i");
            $t = time();
            $createTime = (int)date('d',$t) + 1;
            $thatTime = (int)date_format($date,"d");
            if ($thatTime == $createTime){
            	$arr[$i]['shouldDeliveTime'] = '明天 '.$output;
            }else {
                $arr[$i]['shouldDeliveTime'] = $output;
            }
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['restName'] = $restRes['name'];
            $arr[$i]['restLogo'] = $restRes['logo'];
            $arr[$i]['restNum'] = $restRes['roomNum'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['dormitory'] = $addrRes['dormitory'];
            $arr[$i]['sex'] = $addrRes['gender'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        $userInfo = $modelUser->getOneUserByUserID($deliverID);
        $needSex = $userInfo['sex'];
        //去掉性别不一致的
        $iisex = 0;
        $sex = 0;
        foreach ($arr as $value){
            $sex = $value['sex'];
            //当要送上楼的时候
            if (((int)$value['upstairs']) != 0) {
                //把性别不相等的去掉
                if ($sex != $needSex) {
                    unset($arr[$iisex]);
                }
            }
             $iisex++;
         }
         $arr = array_values($arr);//重建索引
        if ($userInfo['chooseAddr'] == 0 && $userInfo['chooseRest'] ==0 && $userInfo['chooseType'] ==0 && $userInfo['chooseExpress'] ==0) {//默认
            return $arr;
        }else{
            if ($userInfo['chooseAddr'] > 0){
                $needNum = $userInfo['chooseAddr'];
                if ($userInfo['chooseNear'] == 1) {//包括附近宿舍
                if ($needNum <= 6) {
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于6的都去掉
                         if ($dormitoryNum > 6) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                     
                }else if ($needNum <= 10){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于10和小于7的都去掉
                         if ($dormitoryNum > 10 || $dormitoryNum < 7) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 15){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于15和小于11的都去掉
                         if ($dormitoryNum > 15 || $dormitoryNum < 11) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 17){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于17和小于16的都去掉
                         if ($dormitoryNum > 17 || $dormitoryNum < 16) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 20){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于20和小于18的都去掉
                         if ($dormitoryNum > 20 || $dormitoryNum < 18) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum <= 22){
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把大于22和小于20的都去掉
                         if ($dormitoryNum > 22 || $dormitoryNum < 20) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
                }else if ($userInfo['chooseNear'] == 0) {
                    $ii = 0;
                    $dormitoryNum = 0;
                    foreach ($arr as $value){
                    if(preg_match('/\d+/',$value['dormitory'],$arrMath)){
                        $dormitoryNum = $arrMath[0];
                     }//这次循环拿宿舍楼的数字
                         //把不等于的去掉
                         if ($dormitoryNum != $needNum) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
                $arr = array_values($arr);//重建索引
            }
            if ($userInfo['chooseRest'] > 0) {
                $needNum = $userInfo['chooseRest'];
                if ($needNum == 1) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等1\2的去掉
                         if ($restNum != 1 || $restNum !=2) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 2) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等2的去掉
                         if ($restNum != 3) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 3) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等3的去掉
                         if ($restNum != 4) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 4) {
                    $ii = 0;
                    $restNum = 0;
                    foreach ($arr as $value){
                        $restNum = (int)$value['restNum'];
                         //把不等4的去掉
                         if ($restNum != 5) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
                $arr = array_values($arr);//重建索引
            }
            //去掉快递点不一样的
            
            if ($userInfo['chooseExpress'] > 0) {
                $needNum = (int)$userInfo['chooseExpress'];//配送员需要的快递点
                if ($needNum == 1) {
                    $ii = 0;
                    foreach ($arr as $value){
                        $expressAddr = $value['expressAddr'];
                         //把不等的去掉
                         if ($expressAddr != 'C3') {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 2) {
                    $ii = 0;
                    foreach ($arr as $value){
                        $expressAddr = $value['expressAddr'];
                         //把不等的去掉
                         if ($expressAddr != 'C4') {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 3) {
                    $ii = 0;
                    foreach ($arr as $value){
                        $expressAddr = $value['expressAddr'];
                         //把不等的去掉
                         if ($expressAddr != '商业街京东派') {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
            }
            $arr = array_values($arr);//重建索引
            //去掉订单类型不一样的
            if ($userInfo['chooseType'] > 0) {
                $needNum = (int)$userInfo['chooseType'];//配送员需要的类型
                if ($needNum == 1) {
                    $ii = 0;
                    foreach ($arr as $value){
                        $typeNum = (int)$value['type'];
                         //把不等的去掉
                         if ($typeNum != 0) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }else if ($needNum == 2) {
                    $ii = 0;
                    foreach ($arr as $value){
                        $typeNum = (int)$value['type'];
                         //把不等的去掉
                         if ($typeNum != 1) {
                            unset($arr[$ii]);
                         }
                         $ii++;
                     }
                }
            }
            $arr = array_values($arr);//重建索引
            return $arr;
        }
    }

    public function insertOneOrder($userID,$foodArrID,$remark,$restID,$totalPrice,$payPrice,$addrID,$discountID,$shouldDeliveTime,$deliveFee,$upstairs) {
        $model = new ModelOders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        $res = $model->insertOneOrder($userID,$foodArrID,$remark,$restID,$totalPrice,$payPrice,$addrID,$discountID,$createTime,$shouldDeliveTime,$deliveFee,$upstairs);
        if ($res) {
            //让红包报废
            $modelDisc = new ModelDiscount();
            $ress = $modelDisc->changeToFalseAndReason($discountID,"已使用");
            if ($ress == 0 || $ress) {//数据已更新或无变化
                return $res;
            }else{
                return -2;
            }
        }else {
            return -1;
        }
    }

    public function insertOneExpressOrder($userID,$expressAddr,$remark,$expressCode,$totalPrice,$payPrice,$addrID,$shouldDeliveTime,$deliveFee,$weight,$goodType,$isNeedFast,$fastMoney,$discountID) {
        $model = new ModelOders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        $res = $model->insertOneExpressOrder($userID,$expressAddr,$remark,$expressCode,$totalPrice,$payPrice,$addrID,$createTime,$shouldDeliveTime,$deliveFee,$weight,$goodType,$isNeedFast,$fastMoney,$discountID);
        if ($res) {
            //让红包报废
            $modelDisc = new ModelDiscount();
            $ress = $modelDisc->changeToFalseAndReason($discountID,"已使用");
            if ($ress == 0 || $ress) {//数据已更新或无变化
                return $res;
            }else{
                return -2;
            }
        }else {
            return -1;
        }
    }

    public function updateExpressOrderPay($orderNo,$payPrice,$payTime) {
        $model = new ModelOders();
        $res = $model->updateOrderPay($orderNo,$payPrice,$payTime);
        if ($res) {
            //开始推送微信消息给所有配送员
        $orderInfo = $model->getOnesOneOrderByWechatNo($orderNo);
        $weight = $orderInfo['weight'];
        $addrID = $orderInfo['addressID'];
        $express = $orderInfo['expressAddr'];
        $isNeedFast = $orderInfo['isNeedFast'];
        //$fastMoney = $orderInfo['fastMoney'];
        $deliveTime = $orderInfo['shouldDeliveTime'];
        $deliverFee = (float)$orderInfo['deliveFee'];
        //找地址
        $modelAddr = new ModelAddress();
        $addrRes = $modelAddr->getOneByAddrById($addrID);
        $dormitory = $addrRes['dormitory'];
        $needSex = (int)$addrRes['gender'];
        
        $typeStr = '小件(约0～3瓶中型怡宝重)';
        switch ($weight) {
            case '1':
                $typeStr = '中件(约1瓶大型怡宝重)';
                break;
            case '2':
                $typeStr = '大件(约1箱牛奶重)';
                break;
            case '3':
                $typeStr = '特大件(约2箱牛奶重)';
                break;
            case '4':
                $typeStr = '其他件(>2箱牛奶重或者体积大)';
                break;
            default:
                break;
        }
        $title = '可得配送费';
        if (((int)$isNeedFast) == 1) {
            $typeStr = $typeStr.' 且 加急';//修复bug订单类型为0 2019.11.16
            $title = '可得配送费和加急红包共';
            //$deliverFee += (float)$fastMoney;重复bug已修复 2019.11.16
        }
        $addr = $express.' - '.$dormitory;
        $expressNum = 0;//用于判断unset和原下标无关
        switch ($express) {
            case 'C3':
                $expressNum = 1;
                break;
            case 'C4':
                $expressNum = 2;
                break;
            case '商业街京东派':
                $expressNum = 3;
                break;
            default:
                break;
        }
        $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
        $url='http://takeawaydeliver.pykky.com/';
        $first='您有 '.$express.' 新订单可接';
        $remark='若不想接收此消息可在配送端关闭提醒或更改筛选规则';
        //测试用
        //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
        $modid='2ufY9x3kvO8gLJsTzc7IgSPPipBbu-MkBEfXIvjkFZ4';
        $data = array(
            'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
            'tradeDateTime'=>array('value'=>urlencode($deliveTime.'（预计送达）'),'color'=>'#743A3A'),
            'orderType'=>array('value'=>urlencode($typeStr),'color'=>"#743A3A"),
            'customerInfo'=>array('value'=>urlencode($addr),'color'=>"#0000FF"),
            'orderItemName'=>array('value'=>urlencode($title),'color'=>"#000000"),
            'orderItemData'=>array('value'=>urlencode($deliverFee.' 元'),'color'=>"#0000FF"),
            'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
        );
        //所有配送员
        $modelAllUser = new ModelDeliverUser();
        $allDeliverArr = $modelAllUser->getAllUsers();
        
        $iisex = 0;
        $iiRest = 0;
        $iimessage = 0;
        foreach ($allDeliverArr as $value){

            //去掉性别不一致的
            $sex = (int)$value['sex'];
                //把性别不相等的去掉
                if ($sex != $needSex) {
                    unset($allDeliverArr[$iisex]);
                }
            $iisex++;

            $sendMessage = (int)$value['sendMessage'];
            if ($sendMessage == 0) {
                //设置了不想接消息
                unset($allDeliverArr[$iimessage]);
            }
            $iimessage++;
            
            //筛选
            $chooseAddr = (int)$value['chooseAddr'];
            $chooseNear = (int)$value['chooseNear'];
            $chooseType = (int)$value['chooseType'];
            $chooseExpress = (int)$value['chooseExpress'];

            if ($chooseAddr == 0 && $chooseExpress ==0 && $chooseType==0) {//默认
                //
            }else{
                if ($chooseAddr > 0){
                    $needNum = $chooseAddr;
                    if ($chooseNear == 1) {//包括附近宿舍
                    if ($needNum <= 6) {
                        
                        $dormitoryNum = 0;
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于6的都去掉
                             if ($dormitoryNum > 6) {
                                unset($allDeliverArr[$iiRest]);
                             }
                            
                         
                         
                    }else if ($needNum <= 10){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于10和小于7的都去掉
                             if ($dormitoryNum > 10 || $dormitoryNum < 7) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }else if ($needNum <= 15){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于15和小于11的都去掉
                             if ($dormitoryNum > 15 || $dormitoryNum < 11) {
                                unset($allDeliverArr[$iiRest]);
                             }
                            
                         
                    }else if ($needNum <= 17){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于17和小于16的都去掉
                             if ($dormitoryNum > 17 || $dormitoryNum < 16) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }else if ($needNum <= 20){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于20和小于18的都去掉
                             if ($dormitoryNum > 20 || $dormitoryNum < 18) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }else if ($needNum <= 22){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于22和小于20的都去掉
                             if ($dormitoryNum > 22 || $dormitoryNum < 20) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }
                    }else if ($chooseNear == 0) {
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把不等于的去掉
                             if ($dormitoryNum != $needNum) {
                                unset($allDeliverArr[$iiRest]);
                             }
                            
                         
                    }
                }
                //去掉快递点不一样的
                if ($chooseExpress > 0) {
                    $needNum = $chooseExpress;//配送员需要的快递点  expressNum下单的快递点
                    if ($needNum != $expressNum) {
                        unset($allDeliverArr[$iiRest]);
                    }
                }
                //去掉订单类型不一样的
                if ($chooseType > 0) {
                    $needNum = $chooseType;//配送员需要的类型
                    if ($needNum != 2) {
                        unset($allDeliverArr[$iiRest]);
                    }
                }
            }
            $iiRest++;
         }
        
        $allDeliverArr = array_values($allDeliverArr);//重建索引
        //发送
        foreach ($allDeliverArr as $value) {
                if ((!empty($value['cardImg']))){

                    //测试用
                    //if ($value['openid'] == 'oAMY6uLSkezkVmGf2M27o07Xfthg') {
                        $openid = $value['openid'];
                        $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');
                    //}
    
                }
        }


        //开始给用户发已支付/待接单消息
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);

        $url='';
        $first='订单已发布，等待有空的小伙伴接单';
        $remark='因为是跑腿任务，不能保证您的订单一定会有小伙伴接单噢～若30分钟后仍未被接单会自动退款';
        //测试用
        //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
        $modid='0YWKECWoWvrVijLuDm45mX1yxIzXkLigaZbdtCCa7Ts';
        $data = array(
            'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
            'keyword1'=>array('value'=>urlencode('快递代拿'),'color'=>'#0000FF'),
            'keyword2'=>array('value'=>urlencode((((float)$payPrice)/100).' 元'),'color'=>"#0000FF"),
            'keyword3'=>array('value'=>urlencode($createTime),'color'=>"#743A3A"),
            'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
        );
        //发送
        $userid = $orderInfo['userID'];
        $modelTureUser = new ModelUsers();
        $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
        $openid = $trueUserInfo['openid'];
        $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

        return $res;
        }else{
            return -1;
        }
    }
    
    public function updateOrderNo($id,$orderNo) {
        $model = new ModelOders();
        return $model->updateOrderNo($id,$orderNo);
    }

    public function updateOrderPay($orderNo,$payPrice,$payTime) {
        $model = new ModelOders();
        $res = $model->updateOrderPay($orderNo,$payPrice,$payTime);
        if ($res) {
            //开始推送微信消息给所有配送员
        $orderInfo = $model->getOnesOneOrderByWechatNo($orderNo);
        $upstairs = $orderInfo['upstairs'];
        $addrID = $orderInfo['addressID'];
        $restID = $orderInfo['restID'];
        $deliveTime = $orderInfo['shouldDeliveTime'];
        $deliverFee = $orderInfo['deliveFee'];
        $foodArr = $orderInfo['foods'];
        //找地址
        $modelAddr = new ModelAddress();
        $addrRes = $modelAddr->getOneByAddrById($addrID);
        $dormitory = $addrRes['dormitory'];
        $needSex = (int)$addrRes['gender'];
        //找店铺
        $modelRest = new ModelRestaurant();
        $restRes = $modelRest->getOneRest($restID);
        $restNum = (int)$restRes['roomNum'];
        $needUpstair = '送楼下';
        if (((int)$upstairs) == 1){
            $needUpstair = '送上门(1-3楼)';
        }else if (((int)$upstairs) == 2) {
            $needUpstair = '送上门(4-6楼)';
        }
        $addr = $restNum.'饭 - '.$dormitory;
        
        $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
        $url='http://takeawaydeliver.pykky.com/';
        $first='您有 '.$restNum.'饭 新跑腿订单可接';
        $remark='若不想接收此消息可在配送端关闭提醒或更改筛选规则';
        //测试用
        //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
        $modid='2ufY9x3kvO8gLJsTzc7IgSPPipBbu-MkBEfXIvjkFZ4';
        $data = array(
            'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
            'tradeDateTime'=>array('value'=>urlencode($deliveTime.'（预计送达）'),'color'=>'#743A3A'),
            'orderType'=>array('value'=>urlencode($needUpstair),'color'=>"#0000FF"),
            'customerInfo'=>array('value'=>urlencode($addr),'color'=>"#0000FF"),
            'orderItemName'=>array('value'=>urlencode('可得配送费'),'color'=>"#000000"),
            'orderItemData'=>array('value'=>urlencode($deliverFee.' 元'),'color'=>"#0000FF"),
            'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
        );
        //所有配送员
        $modelAllUser = new ModelDeliverUser();
        $allDeliverArr = $modelAllUser->getAllUsers();
        
        $iisex = 0;
        $iiRest = 0;
        $iimessage = 0;
        foreach ($allDeliverArr as $value){

            //去掉性别不一致的
            $sex = (int)$value['sex'];
            //当要送上楼的时候
            if (((int)$upstairs) != 0) {
                //把性别不相等的去掉
                if ($sex != $needSex) {
                    unset($allDeliverArr[$iisex]);
                }
            }
            $iisex++;

            $sendMessage = (int)$value['sendMessage'];
            if ($sendMessage == 0) {
                //设置了不想接消息
                unset($allDeliverArr[$iimessage]);
            }
            $iimessage++;
            
            //筛选
            $chooseAddr = (int)$value['chooseAddr'];
            $chooseRest = (int)$value['chooseRest'];
            $chooseNear = (int)$value['chooseNear'];
            $chooseType = (int)$value['chooseType'];

            if ($chooseAddr == 0 && $chooseRest ==0 && $chooseType==0) {//默认
                //
            }else{
                if ($chooseAddr > 0){
                    $needNum = $chooseAddr;
                    if ($chooseNear == 1) {//包括附近宿舍
                    if ($needNum <= 6) {
                        
                        $dormitoryNum = 0;
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于6的都去掉
                             if ($dormitoryNum > 6) {
                                unset($allDeliverArr[$iiRest]);
                             }
                            
                         
                         
                    }else if ($needNum <= 10){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于10和小于7的都去掉
                             if ($dormitoryNum > 10 || $dormitoryNum < 7) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }else if ($needNum <= 15){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于15和小于11的都去掉
                             if ($dormitoryNum > 15 || $dormitoryNum < 11) {
                                unset($allDeliverArr[$iiRest]);
                             }
                            
                         
                    }else if ($needNum <= 17){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于17和小于16的都去掉
                             if ($dormitoryNum > 17 || $dormitoryNum < 16) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }else if ($needNum <= 20){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于20和小于18的都去掉
                             if ($dormitoryNum > 20 || $dormitoryNum < 18) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }else if ($needNum <= 22){
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把大于22和小于20的都去掉
                             if ($dormitoryNum > 22 || $dormitoryNum < 20) {
                                unset($allDeliverArr[$iiRest]);
                             }
                             
                         
                    }
                    }else if ($chooseNear == 0) {
                        
                        $dormitoryNum = 0;
                        
                        if(preg_match('/\d+/',$dormitory,$arrMath)){
                            $dormitoryNum = $arrMath[0];
                         }//这次循环拿宿舍楼的数字
                             //把不等于的去掉
                             if ($dormitoryNum != $needNum) {
                                unset($allDeliverArr[$iiRest]);
                             }
                            
                         
                    }
                }
                if ($chooseRest > 0) {
                    $needNum = $chooseRest;//配送员需要的饭堂  restNum下单的饭堂
                    if ($needNum != $restNum) {
                        unset($allDeliverArr[$iiRest]);
                    }
                }
                if ($chooseType > 0) {
                    $needNum = $chooseType;//配送员需要的类型
                    if ($needNum != 1) {
                        unset($allDeliverArr[$iiRest]);
                    }
                }
            }
            $iiRest++;
         }
        
        $allDeliverArr = array_values($allDeliverArr);//重建索引
        //发送
        foreach ($allDeliverArr as $value) {
                if ((!empty($value['cardImg'])) && $value['openid'] != 'oAMY6uDvgBa5GmRVLsRSeMavgDu8'){

                    //测试用
                    //if ($value['openid'] == 'oAMY6uLSkezkVmGf2M27o07Xfthg') {
                        $openid = $value['openid'];
                        $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');
                    //}
    
                }
        }


        //开始给用户发已支付/待接单消息
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);

        $url='';
        $first='订单已发布，等待有空的小伙伴接单';
        $remark='因为是跑腿任务，不能保证您的订单一定会有小伙伴接单噢～若30分钟后仍未被接单会自动退款';
        //测试用
        //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
        $modid='0YWKECWoWvrVijLuDm45mX1yxIzXkLigaZbdtCCa7Ts';
        $data = array(
            'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
            'keyword1'=>array('value'=>urlencode('美食跑腿'),'color'=>'#0000FF'),
            'keyword2'=>array('value'=>urlencode((((float)$payPrice)/100).' 元'),'color'=>"#0000FF"),
            'keyword3'=>array('value'=>urlencode($createTime),'color'=>"#743A3A"),
            'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
        );
        //发送
        $userid = $orderInfo['userID'];
        $modelTureUser = new ModelUsers();
        $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
        $openid = $trueUserInfo['openid'];
        $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

        //店铺数和商品数销量增加
        $foodArr = json_decode($foodArr);
        $modelFood = new ModelFood();
        $foodres = null;
        foreach ($foodArr as $value) {
            //对每一个商品
            //在food数据库里找当前数，再update++
            $foodInfo = $modelFood->getOneFoodByID($value);
            $salesNum = (int)$foodInfo['salesNum'];
            $salesNum++;
            $foodres = $modelFood->updateSalesNum($value,$salesNum);
        }
        //对店铺
        $salesNumRest = (int)$restRes['salesNum'];
        $salesNumRest++;
        $restres = $modelRest->updateSalesNum($restID,$salesNumRest);


        return $res;
        }else{
            return -1;
        }
    }

    public function cancelOrder($id,$isNotPay) {
        $model = new ModelOders();
        if ($isNotPay==1) {
            //没支付 直接关闭订单
            return $model->updateStatus($id,8);
        }
        $orderDetail = $model->getOnesOneOrder($id);
        $orderNo = $orderDetail['orderNo'];
        $discountID = (int)$orderDetail['discountID'];
        //拿金额
        $totalPrice = ((int)($orderDetail['payPrice']))/100; 
        $refundPrice = $totalPrice;
        $curl = new \PhalApi\CUrl();
        $url = "https://takeawayapi.pykky.com/pay/refund.php?orderNo=".$orderNo."&totalPrice=".$totalPrice."&refundPrice=".$refundPrice;
        $rs = $curl->get($url, 10000);
        //return $rs;
        if ($rs == 'refund success') {
            $res = $model->cancelOrder($id);
            //红包恢复
            if ($discountID != -1) {
                $modelDisc = new ModelDiscount();
                $ress = $modelDisc->changeToTrue($discountID);
            }
        }
        
        if ($res) {
            //发退款消息
            $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret

            $url='';
            $first='您的订单已被关闭';
            $remark='平台前期伙伴可能不充足，希望您能谅解，如有疑问可联系客服：17889465893';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='3LIHebvXA-lvn0pZHt9vPH7a5a2Ezc9ggO3NeQhfa94';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'keyword1'=>array('value'=>urlencode('原路退回'),'color'=>'#0000FF'),
                'keyword2'=>array('value'=>urlencode($refundPrice.' 元'),'color'=>"#0000FF"),
                'keyword3'=>array('value'=>urlencode('具体以微信支付通知为准'),'color'=>"#743A3A"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $userid = $orderDetail['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');


            return $res;
        }else{
            //把订单状态修改为异常状态
            $rres = $model->errorOrder($id);
            if ($rres) {
                return -1;
            }else {
                return -2;
            }
        }
    }

    public function getNeedCancelOrder($theTime) {
        $model = new ModelOders();
        return $model->getNeedCancelOrder($theTime);
    }
}

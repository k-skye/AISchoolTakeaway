<?php
namespace App\Domain;

use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;
use App\Model\food as ModelFood;
use App\Model\address as ModelAddress;
use App\Model\comment as ModelComment;
use App\Model\tradinglog as ModelTradinglog;
use App\Model\deliverusers as ModelDeliverUser;
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


class deliverorders {

    public function addOneOrder($orderID,$deliverID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为2-接单状态
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatusOnJieDan($orderID,2);
        if ($res) {
            //
        }else {
            return -2;
        }
        $rres = $model->addOneOrder($orderID,$deliverID,$createTime);
        if ($rres) {
            //开始推送给用户已接单
            $t = time();
            $createTime = date('Y-m-d H:i:s',$t);
            //拿配送员信息
            $modelDeliverUser = new ModelDeliverUser();
            $DeliverUserInfo = $modelDeliverUser->getOneUserByUserID($deliverID);
            $firstName = mb_substr($DeliverUserInfo['realName'], 0, 1);
            $phoneNo = $DeliverUserInfo['phoneNo'];

            $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
            $url='https://takeaway.pykky.com/order';
            $first='订单已被接单，小伙伴正在前往店铺';
            $remark='';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='Tbr9oI-fefuJplg6WOfn0oeHT9HAlKNPy-NICycgiwg';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'keyword1'=>array('value'=>urlencode($firstName.'同学'),'color'=>'#0000FF'),
                'keyword2'=>array('value'=>urlencode($phoneNo),'color'=>"#0000FF"),
                'keyword3'=>array('value'=>urlencode($createTime),'color'=>"#743A3A"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $orderInfo = $modelOrder->getOnesOneOrder($orderID);
            $userid = $orderInfo['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

            return $rres;
        }else {
            return -1;
        }
    }

    public function getAllOrder($deliverID,$page) {
        $modelOrder = new ModelDeliverorders();
        $arr =  $modelOrder->getAllOrder($deliverID,$page);
        $model = new ModelOders();
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $arr[$i]['order']['restID'];
            $addrID = $arr[$i]['order']['addressID'];
            //处理时间
            $date=date_create($arr[$i]['order']['shouldDeliveTime']);
            $output=date_format($date,"H:i");
            $t = time();
            $createTime = (int)date('d',$t) + 1;
            $thatTime = (int)date_format($date,"d");
            if ($thatTime == $createTime){
            	$arr[$i]['order']['shouldDeliveTime'] = '明天 '.$output;
            }else { 
                $arr[$i]['order']['shouldDeliveTime'] = $output;
            }
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['order']['restName'] = $restRes['name'];
            $arr[$i]['order']['restLogo'] = $restRes['logo'];
            $arr[$i]['order']['restNum'] = $restRes['roomNum'];
            $arr[$i]['order']['location'] = $restRes['location'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['addr'] = $addrRes;
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        return $arr;
    }

    public function getOneUserAllOrderFinish($deliverID,$page) {
        $modelOrder = new ModelDeliverorders();
        $arr =  $modelOrder->getOneUserAllOrderFinish($deliverID,$page);
        $model = new ModelOders();
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $arr[$i]['order']['restID'];
            $addrID = $arr[$i]['order']['addressID'];
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['order']['restName'] = $restRes['name'];
            $arr[$i]['order']['restLogo'] = $restRes['logo'];
            $arr[$i]['order']['restNum'] = $restRes['roomNum'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['dormitory'] = $addrRes['dormitory'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $i++;
        }
        return $arr;
    }

    public function getAllOrderCountCanComment($deliverID,$offset,$limit) {
        $modelOrder = new ModelDeliverorders();
        $arr =  $modelOrder->getAllOrderCountCanComment($deliverID,$offset,$limit);
        $model = new ModelOders();
        $modelRest = new ModelRestaurant();
        $modelFood = new ModelFood();
        $modelAddr = new ModelAddress();
        $modelComm = new ModelComment();
        $i = 0;
        foreach ($arr as $value){
            $arr[$i]['order'] = $model->getOnesOneOrder($value['orderID']);
            $restID = $arr[$i]['order']['restID'];
            $addrID = $arr[$i]['order']['addressID'];
            $commentID = $arr[$i]['order']['commentID'];
            $restRes = $modelRest->getOneRest($restID);
            $arr[$i]['order']['restName'] = $restRes['name'];
            $arr[$i]['order']['restLogo'] = $restRes['logo'];
            $arr[$i]['order']['restNum'] = $restRes['roomNum'];
            $addrRes = $modelAddr->getOneByAddrById($addrID);
            $arr[$i]['dormitory'] = $addrRes['dormitory'];
            $foodsRes = $modelFood->getFoodsByRestID($restID);
            $arr[$i]['food'] = $foodsRes;
            $commentRes = $modelComm->getOneComment($commentID);
            $arr[$i]['comment'] = $commentRes;
            $i++;
        }
        return $arr;
    }

    public function changToGetFood($orderID,$ID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为3-配送状态
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,3);
        $rres = $model->updateGetFoodTime($ID,$createTime);
        if ($rres && $res) {
            //开始推送给用户已接单
            $t = time();
            $createTime = date('Y-m-d H:i:s',$t);
            //拿配送员信息
            $modelDeliverUser = new ModelDeliverUser();
            $DeliverOrderInfo = $model->getOneByID($ID);
            $DeliverUserInfo = $modelDeliverUser->getOneUserByUserID($DeliverOrderInfo['deliverID']);
            $firstName = mb_substr($DeliverUserInfo['realName'], 0, 1);
            $phoneNo = $DeliverUserInfo['phoneNo'];

            $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
            $url='https://takeaway.pykky.com/order';
            $first='小伙伴已取到商品，正在飞速前往您的宿舍';
            $remark='';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='8wvvEwBuVFQBo9Yu_6SzWQsPjIZIgnBKjBWt2ZItrTk';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'keyword1'=>array('value'=>urlencode($createTime),'color'=>"#743A3A"),
                'keyword2'=>array('value'=>urlencode($firstName.'同学'),'color'=>'#0000FF'),
                'keyword3'=>array('value'=>urlencode($phoneNo),'color'=>"#0000FF"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $orderInfo = $modelOrder->getOnesOneOrder($orderID);
            $userid = $orderInfo['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

            return $rres;
        }else {
            return -1;
        }
    }

    public function changToCompensate($orderID) {
        $model = new ModelDeliverorders();
        //修改原订单状态为9-商品不符
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,9);
        if ($res) {
            //开始推送给用户已接单
            $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
            $url='https://takeaway.pykky.com/order';
            $first='您有 3 元快递尾款需要支付';
            $remark='伙伴反馈实际拿到的快递重量与您下单所填写的不符，需要您支付尾款后继续配送';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='aqa1jV_h-Ok9hkSdy6fR6_sVaRMQuXuQ4uyj7xDtg2g';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'keyword1'=>array('value'=>urlencode('3 元'),'color'=>"#0000FF"),
                'keyword2'=>array('value'=>urlencode('17889465893'),'color'=>"#743A3A"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $orderInfo = $modelOrder->getOnesOneOrder($orderID);
            $userid = $orderInfo['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

            return $res;
        }else {
            return -1;
        }
    }

    public function changToFinishDelive($orderID,$ID,$deliverID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为4-已送达
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,4);
        $orderInfo = $modelOrder->getOnesOneOrder($orderID);
        $money = $orderInfo['deliveFee'];
        $okmoney = (float)$orderInfo['payPrice'];
        $okmoney = ($okmoney/100);
        $modelTradLog = new ModelTradinglog();
        $rres = $modelTradLog->addOneTradLogWithDeliver($deliverID,$money,$createTime,$ID);
        $rrres = $model->updatedelivedTime($ID,$createTime);
        if ($rrres && $res && $rres) {

            $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
            $url='https://takeaway.pykky.com/order';
            $first='伙伴报告已送达至您手中，给他/她和店铺一个评价吧～';
            $remark='如有疑问可联系客服：17889465893';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='KABBNyp3tEWy4MpcL5GCTzjwhX1vpPHpnmlaqaz58tE';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'keyword1'=>array('value'=>urlencode('美食跑腿'),'color'=>"#0000FF"),
                'keyword2'=>array('value'=>urlencode($okmoney.' 元'),'color'=>'#0000FF'),
                'keyword3'=>array('value'=>urlencode($createTime),'color'=>"#743A3A"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $userid = $orderInfo['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');


            return $rrres;
        }else {
            return -1;
        }
    }

    public function changToFinishExpressDelive($orderID,$ID,$deliverID) {
        $model = new ModelDeliverorders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        //修改原订单状态为4-已送达
        $modelOrder = new ModelOders();
        $res = $modelOrder->updateStatus($orderID,4);
        $orderInfo = $modelOrder->getOnesOneOrder($orderID);
        $money = $orderInfo['deliveFee'];
        $okmoney = (int)$orderInfo['payPrice'];
        $okmoney = ($okmoney/100);
        $modelTradLog = new ModelTradinglog();
        $rres = $modelTradLog->addOneTradLogWithExpressDeliver($deliverID,$money,$createTime,$ID);
        $rrres = $model->updatedelivedTime($ID,$createTime);

        $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
        //超时送达退款红包
        //对比shouldDeliveTime和现在时间
        $shouldtimeUnixTime = (int)date(strtotime($orderInfo['shouldDeliveTime']));
        $nowtimeUnix = (int)strtotime("now");
        if ((($nowtimeUnix-$shouldtimeUnixTime)>300) && $rrres && $res && $rres && (((int)$orderInfo['isNeedFast'])==1)) {
            //拿金额
            $orderNo = $orderInfo['orderNo'];
            $totalPrice = $okmoney; 
            $refundPrice = (float)$orderInfo['fastMoney'];
            $curl = new \PhalApi\CUrl();
            $url = "https://takeawayapi.pykky.com/pay/refund.php?orderNo=".$orderNo."&totalPrice=".$totalPrice."&refundPrice=".$refundPrice;
            $rs = $curl->get($url, 10000);
            //return $rs;
            if ($rs == 'refund success') {
                $payPrice = (int)$orderInfo['payPrice'];
                $finalMoney = $okmoney-$refundPrice;
                $payPrice = $payPrice-($refundPrice*100);
                $resp = $modelOrder->updateOrderOtherPay($orderID,$finalMoney,$payPrice,$finalMoney);
                if ($resp) {
                       //发退款消息
                       $url='';
                       $first='伙伴未在指定时间内送达，您的加急红包已退回';
                       $remark='如有疑问可联系客服：17889465893';
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
                   
                       $userid = $orderInfo['userID'];
                       $modelTureUser = new ModelUsers();
                       $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
                       $openid = $trueUserInfo['openid'];
                       $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');
                }else {
                    return -3;
                }
            }else {
                return -2;
            }
        }
        if ($rrres && $res && $rres) {
            
            $url='https://takeaway.pykky.com/order';
            $first='伙伴报告已送达至您手中啦～';
            $remark='如有疑问可联系客服：17889465893';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='KABBNyp3tEWy4MpcL5GCTzjwhX1vpPHpnmlaqaz58tE';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'keyword1'=>array('value'=>urlencode('快递代拿'),'color'=>"#0000FF"),
                'keyword2'=>array('value'=>urlencode($okmoney.' 元'),'color'=>'#0000FF'),
                'keyword3'=>array('value'=>urlencode($createTime),'color'=>"#743A3A"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $userid = $orderInfo['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

            return $rrres;
        }else {
            return -1;
        }
    }

}

<?php
namespace App\Domain;

use App\Model\tradinglog as ModelTradinglog;
use App\Model\deliverusers as ModelDeliverUsers;
use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;
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


class tradinglog {

    public function getOnesAllTradLog($deliverID,$offset,$limit) {
        $model = new ModelTradinglog();
        return $model->getOnesAllTradLog($deliverID,$offset,$limit);
    }

    public function createOneCompensate($orderID) {
        $model = new ModelTradinglog();
        //通过orderid查deliverorders
        $modelDeliveOrder = new ModelDeliverorders();
        $deliveOrderInfo = $modelDeliveOrder->getOneOrderByorderID($orderID);
        $deliverID = $deliveOrderInfo['deliverID'];
        return $model->addOneCompensate($orderID,$deliverID);
    }

    public function updateCompensate($id,$orderNo) {
        $model = new ModelTradinglog();
        return $model->updateCompensate($id,$orderNo);
    }

    public function updateCompensatePay($orderNo,$payPrice,$payTime) {
        $model = new ModelTradinglog();
        $res = $model->updateCompensatePay($orderNo,(((float)$payPrice)/100),$payTime);
        if ($res) {
            //拿orderid
            $logInfo = $model->getOneLogsCompensateByWechatID($orderNo);
            $orderID = $logInfo['orderID'];
            $id = $logInfo['id'];
            
            //把钱加到订单的金额中
            $modelOrder = new ModelOders();
            $orderInfo = $modelOrder->getOnesOneOrder($orderID);
            $totalPrice = (float)$orderInfo['totalPrice'];
            $OldpayPrice = (float)$orderInfo['payPrice'];
            $deliveFee = (float)$orderInfo['deliveFee'];
            $okPayPrice = $OldpayPrice + $payPrice;
            $okTotalPrice = $totalPrice + ($payPrice/100) ;
            $okDeliveFee = $deliveFee + ($payPrice/100);
            $rrres = $modelOrder->updateOrderCompensate($orderID,$okTotalPrice,$okPayPrice,$okDeliveFee);

            //修改order的status为配送中
            $rres = $modelOrder->updateStatus($orderID,3);
            $rrrres = $model->updateDone($id);

            if ($rres && $rrres & $rrrres) {
                //提醒用户
                //开始给用户发已支付/待接单消息
                $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
                $t = time();
                $createTime = date('Y-m-d H:i:s',$t);

                $url='';
                $first='已支付尾款，伙伴将继续为您派送';
                $remark='无';
                //测试用
                //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
                $modid='0YWKECWoWvrVijLuDm45mX1yxIzXkLigaZbdtCCa7Ts';
                $data = array(
                    'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                    'keyword1'=>array('value'=>urlencode('快递代拿尾款'),'color'=>'#0000FF'),
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
            }else {
                return -2;
            }
        }else{
            return -1;
        }
        
    }

    public function autoDoneLog() {
        $model = new ModelTradinglog();
        $notDoneLogArr = $model->getAllLogsNotDone();
        $rrres = "";
        $modelDeliveOrder = new ModelDeliverorders();
        $modelOrder = new ModelOders();
        $modelDeliverUser = new ModelDeliverUser();
        foreach ($notDoneLogArr as $value) {
            //对每一个订单，请求退款接口
            //支付时间
            $paytimeUnixTime = (int)date(strtotime($value['date']));
            $nowtimeUnix = (int)strtotime("now");
            if ((($nowtimeUnix-$paytimeUnixTime)>7200)){//2小时
                //通过配送订单id查order订单
                $deliveOrderInfo = $modelDeliveOrder->getOneByID($value['deliverorderID']);
                //查order
                $OrderInfo = $modelOrder->getOnesOneOrder($deliveOrderInfo['orderID']);
                //拿总原价 加到deliverUser余额和配送费里
                $deliveUserInfo = $modelDeliverUser->getOneUserByUserID($deliveOrderInfo['deliverID']);
                $totalprice = ((float)$OrderInfo['totalPrice']);
                $deliverFee = (float)$OrderInfo['deliveFee'];
                //现在的余额和配送费
                $noun = (float)$deliveUserInfo['noun'];
                $income = (float)$deliveUserInfo['income'];
                //计算
                $noun+=$totalprice;
                $income+=$deliverFee;
                //更新余额和配送费
                $res = $modelDeliverUser->updateNounAndIncome($noun,$income,$deliveOrderInfo['deliverID']);
                //更新Done状态为1
                $rres = null;
                if ($res) {
                    $rres = $model->updateDone($value['id']);
                }
                if ($res && $rres) {
                    $rrres = "ok";
                }
            }
        }
        if (!empty($rrres)) {
            //return 'ok';
            return 1;
        }else{
            return -1;
        }
    }

    public function addCashTradLog($deliverID) {
        $modelUser = new ModelDeliverUsers();
        $userInfo = $modelUser->getOneUserByUserID($deliverID);
        $openID = $userInfo['openid'];
        $personName = $userInfo['realName'];
        $noun = 0.0;
        $income = 0.0;
        $fee = 0.0;
        $OKmoney = 0.0;
        $noun = round(($userInfo['noun']),2);
        //如果没有满1元
        if ($noun < 1) {
            return -10;
        }
        $income = round(($userInfo['income']),2);
        $fee = round(($income*0.3),2);
        $OKmoney = round(($noun - $fee),2);
        //生成商家订单号
        $t = time();
        //不够24小时
        $lastTime = $userInfo['lastCashTimeStamp'];
        if (!empty($lastTime)) {
            /* $lastTime = date('d',$lastTime);
            $today = date('d',$t); */
            $lastTime = (int)$lastTime;
            $today = $t;
/*             if($lastTime<28){
				if (($lastTime+1)!=$today) {
                    return -11;
            	}
            } */
            if (($today-$lastTime)<86400) {
                return -11;
            }
        }
        $createTime = date('Y-m-d H:i:s',$t);
        $NoTime = date('YmdHis',$t);
        $randomNo = mt_rand(1000,9999);
        $orderNo = $NoTime.$randomNo.$NoTime;//32位随机字母或数字
        //先记录申请提现时间
        $rrrs = $modelUser->updateCashTimestamp($deliverID,$t);
        if ($rrrs <= 0) {
            return -4;
        }
        //开始付款
        /* $curl = new \PhalApi\CUrl();
        $url = "https://takeawayapi.pykky.com/pay/transfers.php?orderNo=".$orderNo."&payPrice=".$OKmoney."&personName=".$personName."&openID=".$openID;
        $rs = $curl->get($url, 10000); 
        等开通企业付款权限才行
        */
        $rs= 'success';
        if ($rs = 'success') {
            //付款成功，记录到账单里
            $model = new ModelTradinglog();
            $res =  $model->addOneCashTradLog($deliverID,$orderNo,-$OKmoney,$createTime);
            if ($res > 0) {
                //记录账单成功，清空余额和月收入
                $rres = $modelUser->updateNounToZero($deliverID);
                if ($rres > 0) {
                    //清空余额成功

                    //发消息给配送员
                    $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
                    $url='';
                    $first='您的提现申请已提交';
                    $remark='平台 本月 暂不支持直接转账到您账户，所以暂时需要您加一下客服进行提现：17889465893';
                    //测试用
                    //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
                    $modid='sT48tAXBWOBg7PlwyTbeRyS-wZ7XZFTY5R6YCnDaG8M';
                    $data = array(
                        'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                        'keyword1'=>array('value'=>urlencode($personName),'color'=>'#0000FF'),
                        'keyword2'=>array('value'=>urlencode($createTime),'color'=>"#0000FF"),
                        'keyword3'=>array('value'=>urlencode($OKmoney.' 元'),'color'=>"#743A3A"),
                        'keyword4'=>array('value'=>urlencode('微信零钱'),'color'=>"#743A3A"),
                        'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
                    );
                    //发送
                    $openid = $openID;
                    $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

                    return 0;
                }else{
                    return -3;
                }
            }else {
                return -2;
            }
        }else {
            return $rs;
        }
    }

}

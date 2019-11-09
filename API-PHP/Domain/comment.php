<?php
namespace App\Domain;

use App\Model\comment as ModelComment;
use App\Model\users as ModelUsers;
use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;

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

class comment {

    public function getSomeComment($restID,$offset,$limit) {
        $model = new ModelComment();
        $arr = $model->getSomeComment($restID,$offset,$limit);
        $i = 0;
        $modelUsers = new ModelUsers();
        foreach ($arr as $value){
            $arr[$i]['images'] = json_decode($value['images']);
            //添加名字和头像一起返回
            $userID = $arr[$i]['userID'];
            $arr[$i]['userName'] = $modelUsers->getOneUserByUserID($userID)['name'];
            $arr[$i]['userAvatar'] = $modelUsers->getOneUserByUserID($userID)['avatar'];
            $i++;
        }
        return $arr;
    }

    public function CommentDeliveReply($ID,$text,$deliveOrderID) {
        $model = new ModelComment();
        $modelDeliveOrder = new ModelDeliverorders();
        $res = $modelDeliveOrder->updatehasComment($deliveOrderID);
        if ($res > 0) {
            $rres = $model->updateCommentDeliveReply($ID,$text);

            //发消息给用户已回复评价
            $weixin = new WeixinPush("wx3df92dead7bcd174","d6bade00fdeec6e09500d74a9d3fb15b");//传入appid和appsecret
            $url='';
            $first='伙伴已经回复了您的评论噢～';
            $remark='感谢您选择AI未来校园平台，期待下次光临！';
            //测试用
            //$remark='这是AI未来校园的测试消息，若给您带来不便请谅解！';
            $modid='JuAOPEXIzLJXy5m3Q_5qGC4EqfnbBmlJu9Vhs-tYH3Y';
            $data = array(
                'first'=>array('value'=>urlencode($first),'color'=>"#743A3A"),
                'Content1'=>array('value'=>urlencode('评论内容：'.$text),'color'=>'#000000'),
                'Good'=>array('value'=>urlencode('美食跑腿'),'color'=>"#000000"),
                'remark'=>array('value'=>urlencode($remark),'color'=>'#000000'),
            );
            //发送
            $deliverOrderInfo = $modelDeliveOrder->getOneByID($deliveOrderID);
            $orderID = $deliverOrderInfo['orderID'];
            $modelOrder = new ModelOders();
            $orderInfo = $modelOrder->getOnesOneOrder($orderID);
            $userid = $orderInfo['userID'];
            $modelTureUser = new ModelUsers();
            $trueUserInfo = $modelTureUser->getOneUserByUserID($userid);
            $openid = $trueUserInfo['openid'];
            $weixin->doSend($openid, $modid, $url, $data, $topcolor = '#7B68EE');

            return $rres;
        }else{
            return -1;
        }
    }

    public function CommentByUser($userID,$text,$restID,$orderID,$images,$stars) {
        $model = new ModelComment();
        $modelOrder = new ModelOders();
        $t = time();
        $createTime = date('Y-m-d H:i:s',$t);
        $res = $model->addOneComment($text,$restID,$images,$stars,$userID,$createTime);
        $rres = $modelOrder->userCommentOrder($orderID,$res);
        $modelDeliveOrder = new ModelDeliverorders();
        $rrres = $modelDeliveOrder->updateCanComment($orderID);
        if ($res && $rres && $rrres) {
            return $res;
        }else{
            return -1;
        }
    }

    public function getOnesComment($userID,$offset,$limit) {
        $model = new ModelComment();
        $modelRest = new ModelRestaurant();
        $res = $model->getOnesComment($userID,$offset,$limit);
        $i = 0;
        foreach ($res as $value) {
            $restInfo = $modelRest->getOneRest($value['restID']);
            $res[$i]['restName'] = $restInfo['name'];
            $res[$i]['restLogo'] = $restInfo['logo'];
            $i++;
        }
        return $res;
    }

}

<?php
namespace App\Domain;

use App\Model\tradinglog as ModelTradinglog;
use App\Model\deliverusers as ModelUsers;

class tradinglog {

     public function getOnesAllTradLog($deliverID,$offset,$limit) {
        $model = new ModelTradinglog();
        return $model->getOnesAllTradLog($deliverID,$offset,$limit);
    }

    public function addCashTradLog($deliverID) {
        $modelUser = new ModelUsers();
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
        $fee = round(($income*0.2),2);
        $OKmoney = round(($noun - $fee),2);
        //生成商家订单号
        $t = time();
        //不够24小时
        $lastTime = $userInfo['lastCashTimeStamp'];
        if (!empty($lastTime)) {
            $lastTime = date('d',$lastTime);
            $today = date('d',$t);
            if (($lastTime+1)!=$today) {
                return -11;
            }else if ($lastTime != 30 && $today != 1) {
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
                //记录账单成功，清空余额
                $rres = $modelUser->updateNounToZero($deliverID);
                if ($rres > 0) {
                    //清空余额成功
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

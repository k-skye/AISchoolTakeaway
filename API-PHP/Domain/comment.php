<?php
namespace App\Domain;

use App\Model\comment as ModelComment;
use App\Model\users as ModelUsers;
use App\Model\deliverorders as ModelDeliverorders;
use App\Model\orders as ModelOders;
use App\Model\restaurant as ModelRestaurant;

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
            return $model->updateCommentDeliveReply($ID,$text);
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
        $rres = $modelOrder->userCommentOrder($orderID);
        if ($res && $rres) {
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

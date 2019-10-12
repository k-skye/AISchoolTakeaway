<?php
namespace App\Domain;

use App\Model\comment as ModelComment;
use App\Model\users as ModelUsers;
use App\Model\deliverorders as ModelDeliverorders;

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

}

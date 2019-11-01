<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\comment as DomainComment;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 评论接口
 * @author: kang <knskye@gmail.com> 2019-08-22
 */


class Comment extends Api {
    public function getRules() {
        return array(
            'getSomeComment' => array(
                'restID'  => array('name' => 'restID', 'require' => true, 'desc' => '店铺id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
            'CommentDeliveReply' => array(
                'ID'  => array('name' => 'ID', 'require' => true, 'desc' => '评论id'),
                'text'  => array('name' => 'text', 'require' => true, 'desc' => '回复内容'),
                'deliveOrderID'  => array('name' => 'deliveOrderID', 'require' => true, 'desc' => '配送订单id'),
            ),
            'CommentByUser' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户ID'),
                'text'  => array('name' => 'text', 'require' => true, 'desc' => '回复内容'),
                'restID'  => array('name' => 'restID', 'require' => true, 'desc' => '店铺id'),
                'orderID'  => array('name' => 'orderID', 'require' => true, 'desc' => '订单id'),
                'images'  => array('name' => 'images', 'require' => true, 'desc' => '图片'),
                'stars'  => array('name' => 'stars', 'require' => true, 'desc' => '评分'),
            ),
            'getOnesComment' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'offset'  => array('name' => 'offset', 'require' => true, 'desc' => '忽略前几项'),
                'limit'  => array('name' => 'limit', 'require' => true, 'desc' => '限制只获取多少行'),
            ),
        );
    }
    /**
     * 拿一些评论
     * @desc 测试一下
     */
    public function getSomeComment() {
        $domain = new DomainComment();
        return $domain->getSomeComment($this->restID,$this->offset,$this->limit);
    }
    /**
     * 配送员回复评论
     * @desc 测试一下
     */
    public function CommentDeliveReply() {
        $domain = new DomainComment();
        $res = $domain->CommentDeliveReply($this->ID,$this->text,$this->deliveOrderID);
        if ($res > 0) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("修改配送订单已取餐失败", 19);
        }
    }
    /**
     * 用户评价
     * @desc 测试一下
     */
    public function CommentByUser() {
        $domain = new DomainComment();
        $res = $domain->CommentByUser($this->userID,$this->text,$this->restID,$this->orderID,$this->images,$this->stars);
        if ($res > 0) {
            return 'ok';
        }else{
            throw new InternalServerErrorException("用户评论失败", 29);
        }
    }
    /**
     * 拿用户的所有评论
     * @desc 测试一下
     */
    public function getOnesComment() {
        $domain = new DomainComment();
        return $domain->getOnesComment($this->userID,$this->offset,$this->limit);
    }
} 

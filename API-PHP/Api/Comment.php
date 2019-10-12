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
            )
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
} 

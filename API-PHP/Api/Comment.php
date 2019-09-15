<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\comment as DomainComment;

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
} 

<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\feedback as DomainFeedback;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 反馈接口
 * @author: kang <knskye@gmail.com> 2019-08-22
 */


class Feedback extends Api {
    public function getRules() {
        return array(
            'addOneFeedBackByDeliver' => array(
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'content'  => array('name' => 'content', 'require' => true, 'desc' => '内容'),
            ),
        );
    }
    /**
     * 添加配送员反馈
     * @desc 测试一下
     */
    public function addOneFeedBackByDeliver() {
        $domain = new DomainFeedback();
        $res = $domain->addOneFeedBackByDeliver($this->deliverID,$this->content);
        if ($res > 0) {
            return 'ok';
        }else{
            throw new InternalServerErrorException('添加反馈数据失败', 525);
        }
    }
} 

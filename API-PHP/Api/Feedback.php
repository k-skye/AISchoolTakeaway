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
            'addOneFeedBackByUser' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'content'  => array('name' => 'content', 'require' => true, 'desc' => '内容'),
            ),
            'addOneComplaintByUser' => array(
                'userID'  => array('name' => 'userID', 'require' => true, 'desc' => '用户id'),
                'content'  => array('name' => 'content', 'require' => true, 'desc' => '内容'),
                'deliverID'  => array('name' => 'deliverID', 'require' => true, 'desc' => '配送员id'),
                'orderID'  => array('name' => 'orderID', 'require' => true, 'desc' => '订单id'),
            )
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
    /**
     * 添加用户反馈
     * @desc 测试一下
     */
    public function addOneFeedBackByUser() {
        $domain = new DomainFeedback();
        $res = $domain->addOneFeedBackByUser($this->userID,$this->content);
        if ($res > 0) {
            return 'ok';
        }else{
            throw new InternalServerErrorException('添加反馈数据失败', 526);
        }
    }
    /**
     * 添加用户投诉伙伴
     * @desc 测试一下
     */
    public function addOneComplaintByUser() {
        $domain = new DomainFeedback();
        $res = $domain->addOneComplaintByUser($this->userID,$this->content,$this->deliverID,$this->orderID);
        if ($res > 0) {
            return 'ok';
        }else{
            throw new InternalServerErrorException('添加用户投诉数据失败', 528);
        }
    }
} 

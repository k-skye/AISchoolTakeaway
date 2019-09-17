<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\homedata as DomainHomeData;

/**
 * 首页数据展示接口
 * @author: kang <knskye@gmail.com> 2019-09-17
 */


class HomeData extends Api {
    /**
     * 拿头部轮播广告数据
     * @desc 测试一下
     */
    public function getHeadAdImg() {
        $domain = new DomainHomeData();
        return $domain->getHeadAdImg();
    }
} 

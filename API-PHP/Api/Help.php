<?php
namespace App\Api;
use PhalApi\Api;
use App\Domain\help as DomainHelp;
use PhalApi\Exception\InternalServerErrorException;

/**
 * 帮助接口
 * @author: kang <knskye@gmail.com> 2019-08-22
 */


class Help extends Api {
    /**
     * 拿配送端所有帮助
     * @desc 测试一下
     */
    public function getDeliverAllHelp() {
        $domain = new DomainHelp();
        return $domain->getDeliverAllHelp();
    }

    /**
     * 拿用户端所有帮助
     * @desc 测试一下
     */
    public function getUserAllHelp() {
        $domain = new DomainHelp();
        return $domain->getUserAllHelp();
    }
} 

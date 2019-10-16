<?php
namespace App\Api;

use PhalApi\Api;

/**
 * 上传接口
 * 
 * 测试页面： http://localhost/upload.html
 *
 * @author dogstar 20170611
 */

class UploadImg extends Api {

    public function getRules() {
        return array(
            'deliverCard' => array(
                'file' => array(
                    'name' => 'file',        // 客户端上传的文件字段
                    'type' => 'file', 
                    'require' => true, 
                    'max' => 2097152,        // 最大允许上传2M = 2 * 1024 * 1024, 
                    'range' => array('image/jpeg', 'image/png'),  // 允许的文件格式
                    'ext' => 'jpeg,jpg,png', // 允许的文件扩展名 
                    'desc' => '待上传的图片文件',
                ),
            ),
        );
    }   

    /**
     * 配送员学生证图片文件上传
     * @desc 只能上传单个图片文件
     * @return int code 操作状态码，0成功，1失败
     * @return url string 成功上传时返回的图片URL
     */
    public function deliverCard() {
        $rs = array('code' => 0, 'url' => '');
        
        $tmpName = $this->file['tmp_name'];
        $name = md5($this->file['name'] . $_SERVER['REQUEST_TIME']);
        $ext = strrchr($this->file['name'], '.');
        $uploadFolder = '/home/api-php/public/uploads/deliverCard/';
        $imgPath = $uploadFolder.$name.$ext;
        
        if (move_uploaded_file($tmpName, $imgPath)) {
            $rs['code'] = 1;
            $rs['url'] = sprintf('https://takeawayapi.pykky.com/uploads/deliverCard/%s%s',$name,$ext);
        }
        return $rs;
    }
}

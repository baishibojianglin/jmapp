<?php

namespace app\admin\controller;

use think\Controller;
use think\Request;

/**
 * 后台文件上传控制器类
 * Class Upload
 * @package app\admin\controller
 */
class Upload extends Base
{
    /**
     * 上传图片
     * @param string $name 字段名称
     * @param string $dir 上传的子目录
     * @return \think\response\Json
     */
    public function upload($name, $dir)
    {
        $fileInfo = upload($name, $dir);
        if ($fileInfo) {
            return show(config('code.success'), '上传成功', $fileInfo);
        } else {
            return show(config('code.error'), $fileInfo, [], 404);
        }
    }
}

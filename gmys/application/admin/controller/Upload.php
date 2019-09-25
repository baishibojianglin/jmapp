<?php

namespace app\admin\controller;

use think\Controller;
use think\Image;
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
        // 获取上传信息
        $fileInfo = upload($name, $dir);
        if ($fileInfo) {
            return show(config('code.success'), '上传成功', $fileInfo);
        } else {
            return show(config('code.error'), $fileInfo, [], 404);
        }
    }

    /**
     * 上传图片并生成缩略图
     * @param string $name
     * @param string $dir
     * @param int $width
     * @param int $height
     * @return \think\response\Json
     */
    public function uploadThumb($name = 'file', $dir = '', $width = 200, $height = 200)
    {
        // 获取上传信息
        $fileInfo = upload($name, $dir);
        if ($fileInfo) {
            // 生成缩略图
            imageThumb($fileInfo, $width, $height);

            return show(config('code.success'), '上传成功', $fileInfo);
        } else {
            return show(config('code.error'), $fileInfo, [], 404);
        }
    }
}

<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

/**
 * 分页模板：基于paginate()的分页模板，在视图层.html页面使用如{:pagination($data)}
 * @param $obj
 * @return string
 */
function pagination($obj){
    if(!$obj) {
        return '';
    }

    $params = request()->param();
    return $obj->appends($params)->render();
}

/**
 * 通用化API接口数据输出
 * @param int $status 业务状态码
 * @param string $message 信息提示
 * @param array $data 数据
 * @param int $httpCode http状态码
 * @return \think\response\Json
 */
function show($status, $message, $data = [], $httpCode = 200){
    $data = [
        'status' => $status,
        'message' => $message,
        'data' => $data,
    ];
    return json($data, $httpCode);
}

/**
 * 文件上传
 * @param string $name 字段名称
 * @param string $dir 上传的子目录
 * @return string
 */
function upload($name = 'file', $dir = '')
{
    // 获取表单上传文件
    $file = \think\Request::instance()->file($name);

    // 把图片上传到指定的目录
    if ($file) {
        $info = $file->move('upload' . DS . $dir); // './public/upload/$dir' //$file->move(ROOT_PATH . 'public' . DS . 'upload' . DS . $dir)

        if ($info) {
            // 成功上传后 获取上传信息
            return $info->getPathname();
        } else {
            // 上传失败删除文件
            @unlink($info->getPathname()); //提示 Permission denied

            // 上传失败获取错误信息
            return $file->getError();
        }
    }
}

/**
 * 生成缩略图
 * @param string $fileInfo 上传信息
 * @param int $width
 * @param int $height
 */
function imageThumb($fileInfo = '', $width = 150, $height = 150)
{
    // 使用open方法打开图像文件进行相关操作
    $image = \think\Image::open($fileInfo);
    // 直接获取当前请求中的文件上传对象
    //$image = \think\Image::open(request()->file($name = 'image'));

    // 删除原图（好像下面生成缩略图并保存的步骤也会删除原图）
    //unlink($fileInfo);

    // 按照原图的比例生成一个最大为 $width*$height 的缩略图并保存为 $fileInfo
    $image->thumb($width, $height)->save($fileInfo);
}

/**
 * 让 array_column() 函数兼容低版本PHP
 * 其中 array_column(array,column_key,index_key) 函数（PHP版本：5.5+）返回输入数组中某个单一列的值。
 * @param $input
 * @param $columnKey
 * @param null $indexKey
 * @return array
 */
function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
        $indexKeyIsNull            = (is_null($indexKey))?true :false;
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
        $result                         = array();
        foreach((array)$input as $key=>$row){
            if($columnKeyIsNumber){
                $tmp= array_slice($row, $columnKey, 1);
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
            }else{
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
            }
            if(!$indexKeyIsNull){
                if($indexKeyIsNumber){
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key))?current($key):null;
                    $key = is_null($key)?0:$key;
                }else{
                    $key = isset($row[$indexKey])?$row[$indexKey]:0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
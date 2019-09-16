<?php

namespace app\common\lib\exception;

use think\exception\Handle;

/**
 * 自定义异常处理ApiHandleException类
 * Class ApiHandleException
 * @package app\common\lib\exception
 */
class ApiHandleException extends Handle
{
    /**
     * http状态码
     * @var int
     */
    public $httpCode = 500;

    /**
     * （自定义）Render an exception into an HTTP response.
     *
     * @param \Exception $e
     * @return \think\response\Json
     */
    public function render(\Exception $e)
    {
        // 当应用调试模式开启时，调用系统默认异常处理handle类\think\exception\Handle，便以服务端工程师查看异常
        if(config('app_debug') == true){
            return parent::render($e);
        }

        // 获取自定义http状态码
        if($e instanceof ApiException){
            $this->httpCode = $e->httpCode;
        }

        return show(0, $e->getMessage(), [], $this->httpCode);
    }
}
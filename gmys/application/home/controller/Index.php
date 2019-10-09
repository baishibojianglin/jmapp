<?php

namespace app\home\controller;

/**
 * Class Index
 * @package app\home\controller
 */
class Index extends Base
{
    /**
     * 首页
     * @return \think\response\View
     */
    public function index()
    {
        // 案例
        $caseList = model('Article')->where('status', 4)->limit(4)->select();

        return view('', ['caseList' => $caseList]);
    }
}

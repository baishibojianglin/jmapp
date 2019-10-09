<?php

namespace app\home\controller;

/**
 * Class About
 * @package app\home\controller
 */
class About extends Base
{
    /**
     * 关于
     * @return \think\response\View
     */
    public function about()
    {
        // 获取团队成员列表
        $memberList = model('Member')->where(['status' => config('code.status_enable'), 'is_delete' => config('code.not_delete')])->limit(6)->select();
        $memberList = json_decode(json_encode($memberList), true);

        return view('', ['memberList' => $memberList]);
    }
}
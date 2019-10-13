<?php
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {
        //halt(session(config('admin.session_admin'), '', config('admin.session_admin_scope')));
        return $this->fetch();
    }

    public function console()
    {
        // php获取今日开始时间戳和结束时间戳
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;

        $memberCount = model('Member')->count(); // 团队成员数
        $memberCountToday = model('Member')->where('create_time', 'between time', [$beginToday, $endToday])->count(); // 今日新增团队成员数
        $articleCount = model('Article')->count(); // 设计作品数
        $articleCountToday = model('Article')->where('create_time', 'between time', [$beginToday, $endToday])->count(); // 今日新增设计作品数
        $newsCount = model('News')->count(); // 新闻动态数
        $newsCountToday = model('News')->where('create_time', 'between time', [$beginToday, $endToday])->count(); // 今日新增新闻动态数

        return $this->fetch('', [
            'memberCount' => $memberCount,
            'memberCountToday' => $memberCountToday,
            'articleCount' => $articleCount,
            'articleCountToday' => $articleCountToday,
            'newsCount' => $newsCount,
            'newsCountToday' => $newsCountToday,
        ]);
    }
}

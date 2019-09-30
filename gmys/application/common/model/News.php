<?php

namespace app\common\model;

use think\Model;

/**
 * 新闻模型类
 * Class News
 * @package app\common\model
 */
class News extends Base
{
    /**
     * 获取新闻列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getNews($map = [], $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['article_id' => 'asc'];

        $result = $this->field('content', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 根据条件获取新闻列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getNewsByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['article_id' => 'asc'];

        $result = $this->where($map)
            ->limit($from, $size)
            ->order($order)
            ->select();
        //echo $this->getLastSql();
        return $result;
    }
}

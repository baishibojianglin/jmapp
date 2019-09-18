<?php

namespace app\common\model;

use think\Model;

/**
 * 团队成员模型类
 * Class Member
 * @package app\common\model
 */
class Member extends Base
{
    /**
     * 获取团队成员列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getMember($map = [], $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['member_id' => 'asc'];

        $result = $this->field('abstract,description', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 根据条件获取团队成员列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getMemberByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['member_id' => 'asc'];

        $result = $this->where($map)
            ->limit($from, $size)
            ->order($order)
            ->select();
        //echo $this->getLastSql();
        return $result;
    }
}

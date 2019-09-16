<?php

namespace app\common\model;

use think\Model;

/**
 * 管理员模型类
 * Class Admin
 * @package app\common\model
 */
class Admin extends Base
{
    /**
     * 获取管理员列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAdmin($map = [], $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['admin_id' => 'asc'];

        $result = $this->field('password', true) // 字段排除
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }

    /**
     * 根据条件获取管理员列表数据
     * @param array $map
     * @param int $from
     * @param int $size
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getAdminByCondition($map = [], $from = 0, $size = 5)
    {
        if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }

        $order = ['admin_id' => 'asc'];

        $result = $this->where($map)
            ->limit($from, $size)
            ->order($order)
            ->select();
        //echo $this->getLastSql();
        return $result;
    }
}

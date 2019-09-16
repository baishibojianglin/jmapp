<?php

namespace app\common\model;

use think\Model;

/**
 * Auth权限认证用户组模型类
 * Class AuthGroup
 * @package app\common\model
 */
class AuthGroup extends Base
{
    /**
     * 获取Auth用户组列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAuthGroup($map = [], $size = 5)
    {
        /*if(!isset($map['is_delete'])) {
            $map['is_delete'] = ['neq', config('code.is_delete')];
        }*/

        $order = ['id' => 'asc'];

        $result = $this->field(true)
            ->where($map)
            ->order($order)
            ->paginate($size);
        return $result;
    }
}

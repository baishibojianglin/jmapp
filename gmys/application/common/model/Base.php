<?php

namespace app\common\model;

use think\Model;

/**
 * 基础模型类库
 * Class Base
 * @package app\common\model
 */
class Base extends Model
{
    protected $autoWriteTimestamp = true;

    /**
     * 新增
     * @param $data
     * @param string $id 返回主键id
     * @return mixed
     */
    public function add($data, $id = 'id')
    {
        if(!is_array($data)){
            exception('传递数据不合法');
        }
        $this->allowField(true)->save($data); // allowField(true) 过滤非数据表字段的数据

        return $this->$id;
    }

    /**
     * 软删除指定资源（自定义软删除，不同于tp5软删除traits\model\SoftDelete）
     * @param $id_key
     * @param $id_value
     * @return false|int
     */
    public function softDelete($id_key, $id_value)
    {
        return $this->save(['is_delete' => config('code.is_delete')], [$id_key => $id_value]);
    }
}

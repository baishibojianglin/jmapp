<?php

namespace app\common\model;

use think\Model;

/**
 * Auth权限认证规则模型类
 * Class AuthRule
 * @package app\common\model
 */
class AuthRule extends Base
{
    /**
     * 获取Auth规则列表数据（基于paginate()自动化分页）
     * @param array $map
     * @param int $size
     * @return \think\Paginator
     */
    public function getAuthRule($map = [], $size = 5)
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

    /**
     * 获取Auth规则列表树
     * @param array $map
     * @return mixed
     */
    public function getAuthRuleTree($map = [])
    {
        $result = $this->where($map)->select();

        /*// 处理数据
        foreach ($result as $key => $value) {
            if ($value['level'] != 0) {
                // level 用于定义 title 前面的空位符的长度
                $result[$key]['title'] = str_repeat(' ', $value['level'] * 5). '|——' . $value['title']; // str_repeat(string,repeat) 函数把字符串重复指定的次数
            }
        }*/

        return $this->sort($result);
    }

    /**
     * 无限级分类递归排序
     * @param $data
     * @param int $pid
     * @param int $level
     * @return array
     */
    public function sort($data, $pid = 0, $level = 0)
    {
        // 定义静态数组
        static $arr = array();

        foreach ($data as $key => $value) {
            // 找出第一个顶级元素（Auth规则）
            if ($value['pid'] == $pid) {
                // 定义level字段（该字段可以不写入数据表中）
                $value['level'] = $level;

                // 定义所有上级id（包含当前id）
                $value['parent_ids'] = $this->getParentId($value['id']);

                // 定义所有下级id（不包含当前id）
                $value['child_ids'] = $this->getChildId($value['id']);

                // 数据放入静态数组中
                $arr[] = $value;

                // 递归：找出当前元素的子元素（当前Auth规则的id即为子元素的pid）
                $this->sort($data, $value['id'], $level + 1);
            }
        }

        return $arr;
    }

    /**
     * 获取下级id（不包含当前id）
     * @param $id
     * @return array
     */
    public function getChildId($id)
    {
        $data_list = $this->select();
        return $this->_getChildId($data_list, $id, true);
    }

    /**
     * 递归获取下级id（不包含当前id）
     * @param $data
     * @param $id
     * @param bool $clear 清空静态数组标识
     * @return array
     */
    private function _getChildId($data, $id, $clear = false)
    {
        // 定义静态数组
        static $arr = array();
        // 清空静态数组
        if ($clear) {
            $arr = array();
        }

        foreach ($data as $key => $value) {
            if ($value['pid'] == $id) {
                // 当前 id 即 $value['id'] 数据放入静态数组中
                $arr[] = $value['id'];

                // 递归：找出当前元素的子元素（当前Auth规则的id即为子元素的pid）
                $this->_getChildId($data, $value['id'], false);
            }
        }

        // 排序
        asort($arr);

        // 返回存放下级id的数组
        return $arr;
    }

    /**
     * 获取上级id（包含当前id）
     * @param $id
     * @return array
     */
    public function getParentId($id)
    {
        $data_list = $this->select();
        return $this->_getParentId($data_list, $id, true);
    }

    /**
     * 递归获取上级id（包含当前id）
     * @param $data
     * @param $id
     * @param bool $clear 清空静态数组标识
     * @return array
     */
    private function _getParentId($data, $id, $clear = false)
    {
        // 定义静态数组
        static $arr = array();
        // 清空静态数组
        if ($clear) {
            $arr = array();
        }

        foreach ($data as $key => $value) {
            if ($value['id'] == $id) {
                // 当前 id 即 $value['id'] 数据放入静态数组中
                $arr[] = $value['id'];

                // 递归：找出当前元素的父元素（当前Auth规则的pid即为父元素的id）
                $this->_getParentId($data, $value['pid'], false);
            }
        }

        // 排序
        asort($arr);

        // 返回存放id和上级id的数组
        return $arr;
    }
}

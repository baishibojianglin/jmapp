<?php
/**
 * Created by PhpStorm.
 * User: 問安冬日
 * Date: 2019/8/2
 * Time: 12:23
 */

namespace app\common\validate;

use think\Validate;

class AuthGroup extends Validate
{
    protected $rule = [
        'title' => 'require|unique:auth_group|max:100',
        //'status' => 'require',
    ];

    protected $message = [
        'title.require' => '用户组中文名称不能为空',
        'title.unique' => '用户组已存在', // 唯一性
        'title.max' => '用户组中文名称长度不能超过100',
        //'status.require' => '状态不能为空',
    ];

    protected $scene = [
        // 验证update场景
        'update' => [
            'title' => 'require|max:100',
            'title.unique' => 'unique:auth_group, title^id', // 忽略唯一(unique)类型字段title对自身数据的唯一性验证
            //'status'
        ],
    ];
}
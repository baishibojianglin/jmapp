<?php

namespace app\common\validate;

use think\Validate;

class Admin extends Validate
{
    protected $rule = [
        'admin_name' => 'require|unique:admin|max:20',
        'password' => 'require|max:20',
    ];

    protected $message = [
        'admin_name.require' => '管理员名称不能为空',
        'admin_name.unique' => '管理员已存在', // 唯一性
        'admin_name.max'     => '管理员名称长度不能超过20',
        'password.require' => '密码不能为空',
        'password.max'     => '密码长度不能超过20',
        'password.confirm' => '密码和确认密码不一致',
    ];

    protected $scene = [
        'login' => ['admin_name' => 'require|max:20', 'admin_name.unique' => 'unique:admin, admin_name^admin_id', 'password'], // 忽略唯一(unique)类型字段admin_name对自身数据的唯一性验证
        'update' => ['admin_name' => 'require|max:20', 'admin_name.unique' => 'unique:admin, admin_name^admin_id'],
        'update_password' => ['password' => 'require|confirm|max:20'], // 更新密码场景
    ];
}
<?php
/**
 * Created by PhpStorm.
 * User: 問安冬日
 * Date: 2019/8/3
 * Time: 18:24
 */

namespace app\common\validate;

use think\Validate;

class AuthRule extends Validate
{
    protected $rule = [
        'name' => 'require|unique:auth_rule|max:80',
        'title' => 'require|unique:auth_rule|max:20',
        'type' => 'require',
        'module' => 'require|max:20',
        'pid' => 'require',
        //'status' => 'require',
    ];

    protected $message = [
        'name.require' => '规则标识不能为空',
        'name.unique' => '规则已存在', // 唯一性
        'name.max' => '规则标识长度不能超过80',
        'title.require' => '规则中文名称不能为空',
        'title.unique' => '规则中文名称已存在', // 唯一性
        'title.max' => '规则中文名称长度不能超过20',
        'type.require' => '规则类型不能为空',
        'module.require' => '规则所属模块不能为空',
        'module.max' => '规则所属模块长度不能超过20',
        'pid.require' => '上级id不能为空',
        //'status.require' => '状态不能为空',
    ];

    protected $scene = [
        // 验证update场景
        'update' => [
            'name'=> 'require|max:80',
            'name.unique' => 'unique:auth_rule, name^id', // 忽略唯一(unique)类型字段name对自身数据的唯一性验证
            'title' => 'require|max:20',
            'title.unique' => 'unique:auth_rule, title^id', // 忽略唯一(unique)类型字段title对自身数据的唯一性验证
            'type',
            //'module',
            'pid',
            //'status'
        ],
    ];
}
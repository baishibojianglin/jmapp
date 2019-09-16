<?php

// 状态码配置
return [
    // 用户及其他状态
    'status' => [
        0 => '停用',
        1 => '启用',
        2 => '待审核',
    ],

    'status_disable' => 0, // 关闭/停用
    'status_enable' => 1, // 开启/启用
    'status_pending' => 2, // 待审核

    // 是否删除
    'not_delete' => 0, // 未删除
    'is_delete' => 1, // 删除

    // API接口状态码
    'error' => 0,
    'success' => 1,
];
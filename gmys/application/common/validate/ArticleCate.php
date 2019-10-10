<?php

namespace app\common\validate;

use think\Validate;

class ArticleCate extends Validate
{
    protected $rule = [
        'cate_name|类别名称' => 'require|unique:article_cate|max:20', // 控制器中需要传入主键，忽略唯一(unique)类型字段cate_name对自身数据的唯一性验证，但不影响不同主键之间的cate_name唯一性验证
    ];
}
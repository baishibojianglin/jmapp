<?php

namespace app\common\lib;


/**
 * IAuth相关
 * Class IAuth
 * @package app\common\lib
 */
class IAuth
{
    /**
     * 密码加密
     * @param string $data
     * @return string
     */
    public static function encrypt($data)
    {
        return md5(config('app.password_pre_salt') . $data);
    }
}
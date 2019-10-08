<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\Controller;

/**
 * 后台登录控制器类
 * Class Login
 * @package app\admin\controller
 */
class Login extends Base
{
    /**
     * 初始化
     */
    public function _initialize()
    {
        // 重写并覆盖Base类的_initialize()方法

        // 初始化参数
        $this->module= request()->module(); // 模块
        $this->session_admin = session(config('admin.session_admin'), '', config('admin.session_admin_scope')); // 管理员session值
    }

    /**
     * 登录页
     * @return \think\response\Json|void
     */
    public function index()
    {
        // 如果后台用户已经登录，需要跳转到后台首页
        $isLogin = $this->isLogin(); // isLogin() 方法调用当前 admin/Login 控制器的 $this->session_admin 参数
        if($isLogin) {
            return $this->redirect('Index/index');
        } else {
            //return show(config('code.error'), '未登录', ['url' => $this->module . '/Login/index'], 401);
            return $this->fetch();
        }
    }

    /**
     * 登录验证
     * @return \think\response\Json
     * @throws ApiException
     */
    public function check()
    {
        if (request()->isPost()) {
            // 传入的参数
            $data = input('post.');

            // validate校验账号、密码是否合法
            $validate = validate('Admin');
            if (!$validate->check(
                $data,
                $rule = []
                /*[ // 重新定义验证规则
                    'admin_name' => 'require|max:20',
                    'password' => 'require|max:20',
                ]*/,
                $scene = 'login'
            )) {
                return show(config('code.error'), $validate->getError(), [], 403); //$this->error($validate->getError());
            }

            // 先判断账号，再判断密码
            try {
                $admin_data = model('Admin')->get(['admin_name' => $data['admin_name']]);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500);
            }
            // 判断账号是否存在或正常
            if (!$admin_data || ($admin_data->status != config('code.status_enable'))) {
                return show(config('code.error'), '管理员不存在', [], 404);
            }
            // 判断密码是否正确
            if (IAuth::encrypt($data['password']) != $admin_data['password']) {
                return show(config('code.error'), '密码错误', [], 404);
            }
            //halt($admin_data);

            // 判断验证码
            if (!captcha_check($data['captcha'])) {
                return show(config('code.error'), '验证码错误', [], 400);
            }

            // 更新数据库：登录时间、登录IP
            $update_data = [
                'last_login_time' => time(),
                'last_login_ip' => request()->ip(),
            ];
            try {
                model('Admin')->save($update_data, ['admin_id' => $admin_data->admin_id]);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500);
            }

            // 存session
            session(config('admin.session_admin'), $admin_data, config('admin.session_admin_scope'));
            return show(config('code.success'), '登录成功', ['url' => config('app.I_SERVER_NAME') . $this->module . '/Index/index']); //$this->success('登录成功', 'Index/index');
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 注销
     * 1.清空session
     * 2.跳转到登录页
     */
    public function logout()
    {
        // 清空session
        session(null, config('admin.session_admin_scope'));

        // 跳转
        $this->redirect('Login/index');
    }
}

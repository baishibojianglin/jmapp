<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台Auth权限认证规则控制器类
 * Class AuthRule
 * @package app\admin\controller
 */
class AuthRule extends Base
{
    /**
     * 显示Auth规则资源列表
     * @return \think\response\Json
     * @throws ApiException
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 获取Auth规则资源列表
     */
    public function getAuthRule()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['name'])) {
                $map['name'] = ['like', '%' . trim($param['name']) . '%'];
            }
            if (!empty($param['title'])) {
                $map['title'] = ['like', '%' . trim($param['title']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取Auth规则列表树
            $data = $this->_authRuleTree($map);

            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 显示创建Auth规则资源表单页
     * @return \think\response\Json
     * @throws ApiException
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view();
            return $this->authRuleTree();
        }
    }

    /**
     * 保存新建的Auth规则资源
     * @param Request $request
     * @return \think\response\Json
     * @throws ApiException
     */
    public function save(Request $request)
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的数据
            $data = input('post.');

            // validate验证
            $validate = validate('AuthRule');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 处理数据
            // 状态status
            $data['status'] = isset($data['status']) ? $data['status'] : config('code.status_enable');
            // 级别level
            $plevel = db('auth_rule')->field('level')->find($data['pid']);
            if ($plevel) {
                $data['level'] = $plevel['level'] + 1;
            } else {
                $data['level'] = 0;
            }

            // 新增
            // 捕获异常
            try {
                $id = model('AuthRule')->add($data, 'id'); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                // 每次新增、更新或删除Auth规则时，必须删除指定session值（指定作用域的指定值）
                session('_auth_list_' . $this->session_admin->admin_id . '2', null, config('admin.session_admin_scope'));

                return show(config('code.success'), 'id = ' . $id . '的Auth规则新增成功', [], 201);
            } else {
                return show(config('code.error'), '用户新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的Auth规则资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('AuthRule')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            }
        }
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            $data = model('AuthRule')->find($id);
            return $this->fetch('', ['data' => $data]);
            //return $this->authRuleTree();
        }
    }

    /**
     * 保存更新的Auth规则资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 判断为PUT请求
        //if (request()->isPut()) {
        if (request()->isPost()) {
            // 传入的数据
            $param = input('param.');

            // validate验证
            $validate = validate('AuthRule');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['name'])) {
                $data['name'] = $param['name'];
            }
            if (!empty($param['title'])) {
                $data['title'] = $param['title'];
            }
            if (isset($param['type'])) {
                $data['type'] = input('param.type', null, 'intval');
            }
            if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
                $data['status'] = input('param.status', null, 'intval');
            }
            if (!empty($param['module'])) {
                $data['module'] = $param['module'];
            }
            if (isset($param['pid'])) {
                $data['pid'] = $param['pid'];

                // 处理数据：级别level
                $plevel = db('auth_rule')->field('level')->find($param['pid']);
                if ($plevel) {
                    $data['level'] = $plevel['level'] + 1;
                } else {
                    $data['level'] = 0;
                }
            }
            if (!empty($param['icon'])) {
                $data['icon'] = $param['icon'];
            }
            if (isset($param['sort'])) {
                $data['sort'] = $param['sort'];
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('AuthRule')->save($data, ['id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if (false === $result) {
                return show(config('code.error'), '更新失败', [], 403);
            } else {
                // 每次新增、更新或删除Auth规则时，必须删除指定session值（指定作用域的指定值）
                session('_auth_list_' . $this->session_admin->admin_id . '2', null, config('admin.session_admin_scope'));

                return show(config('code.success'), '更新成功', [], 201);
            }
        }
    }

    /**
     * 删除指定Auth规则资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     * @throws \think\Exception
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 显示指定的管理员
            try {
                $data = model('AuthRule')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 真删除
            $result = model('AuthRule')->destroy($id);
            if (!$result) {
                return show(config('code.error'), '删除失败', ['url' => config('app.SERVER_NAME') . $this->module . '/auth_rule/index']);
            } else {
                // 每次新增、更新或删除Auth规则时，必须删除指定session值（指定作用域的指定值）
                session('_auth_list_' . $this->session_admin->admin_id . '2', null, config('admin.session_admin_scope'));

                return show(config('code.success'), '删除成功', ['url' => config('app.SERVER_NAME') . $this->module . '/auth_rule/index']);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 获取Auth规则列表树（返回给页面的json数据）
     * @return \think\response\Json
     * @throws ApiException
     */
    public function authRuleTree()
    {
        $data = $this->_authRuleTree();
        return show(config('code.success'), 'ok', $data);
    }

    // +----------------------------------------------------------------------
    // | 定义方法的公共部分
    // +----------------------------------------------------------------------
    /**
     * 获取处理数据后的Auth规则列表树
     * @param $map
     * @return mixed
     * @throws ApiException
     */
    private function _authRuleTree($map = [])
    {
        // 获取Auth规则列表树，用于页面下拉框列表、权限配置
        // 捕获异常
        try {
            $data = model('AuthRule')->getAuthRuleTree($map);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        if ($data) {
            // 处理数据
            $status = config('code.status');
            foreach ($data as $key => $value) {
                if ($value['level'] != 0) {
                    // level 用于定义 title 前面的空位符的长度
                    $data[$key]['title'] = str_repeat('&nbsp;', $value['level'] * 5). '└─ ' . $value['title']; // str_repeat(string,repeat) 函数把字符串重复指定的次数
                }

                // 定义状态信息status_msg
                $data[$key]['status_msg'] = $status[$value['status']];
                // 规则类型
                $data[$key]['type'] = $value['type'] == 2 ? 'menu' : 'url';
            }
        }

        return $data;
    }
}

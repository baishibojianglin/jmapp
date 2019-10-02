<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use app\common\lib\IAuth;
use think\auth\Auth;
use think\Controller;

/**
 * 后台管理员控制器类
 * Class Admin
 * @package app\admin\controller
 */
class Admin extends Base
{
    /**
     * 数据表主键id字段
     * @var string
     */
    public $admin_id = 'admin_id';

    /**
     * 显示管理员资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取管理员资源列表
     * @return \think\response\Json
     */
    public function getAdmin()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['admin_name'])) {
                $map['admin_name'] = ['like', '%' . trim($param['admin_name']) . '%'];
            }
            if (!empty($param['create_time'])) {
                $map['create_time'] = $param['create_time'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Admin')->getAdmin($map, $this->size);

            // 处理数据
            $status = config('code.status');
            $auth = new Auth();
            foreach ($data as $key => $value) {
                // 定义状态信息status_msg
                $data[$key]['status_msg'] = $status[$value['status']];
                $data[$key]['last_login_time'] = date('Y-m-d H:i:s', $value['last_login_time']);

                // 获取管理员所属Auth用户组
                $authGroup = $auth->getGroups($value['admin_id']);
                if ($authGroup) { // 避免其他未设置用户组的管理员造成的错误
                    $data[$key]['group_id'] = $authGroup[0]['group_id'];
                    $data[$key]['group_title'] = $authGroup[0]['title'];
                };
            }

            return show(config('code.success'), 'ok', $data);
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 新增管理员
     * @return mixed|\think\response\Json
     * @throws ApiException
     */
    public function add()
    {
        // 判断为POST请求
        if (request()->isPost()) {
            // 传入的参数
            $data = input('post.'); //halt(input('post.'));

            // 验证不需要validate验证的其他参数
            if (!isset($data['group_id'])) { // 所属用户组id
                return show(config('code.error'), '用户组不能为空', [], 412);
            }

            // validate验证
            $validate = validate('Admin');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403); //$this->error($validate->getError());
            }

            // 处理参数
            $data['password'] = IAuth::encrypt($data['password']); //md5(config('app.password_pre_salt') . $data['password']);
            $data['status'] = isset($data['status']) ? $data['status'] : config('code.status_disable'); // 可以注释掉

            // 捕获异常
            try {
                $id = model('Admin')->add($data, $this->admin_id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error')); //$this->error($e->getMessage());
            }
            // 判断是否新增成功：获取id
            if ($id) {
                // 添加管理员到Auth用户组
                $authGroupAccessData = [];
                $authGroupAccessData['uid'] = $id;
                $authGroupAccessData['group_id'] = $data['group_id'];
                if ($authGroupAccessData['uid'] && $authGroupAccessData['group_id']) {
                    db('auth_group_access')->insert($authGroupAccessData);
                }

                return show(config('code.success'), 'admin_id = ' . $id . '的管理员新增成功', ['url' => config('app.SERVER_NAME') . $this->module . '/admin/index']); //$this->success('admin_id = ' . $id . '的管理员新增成功');
            } else {
                return show(config('code.error'), '管理员新增失败', [], 403); //$this->error('err');
            }
        } else {
            $authGroupList = model('AuthGroup')->select();
            return $this->fetch('', ['authGroupList' => $authGroupList]);
        }
    }

    /**
     * 显示指定的管理员
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Admin')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 定义status_msg
                $status = config('code.status');
                $data['status_msg'] = $status[$data['status']];

                return show(config('code.success'), 'ok', $data);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
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
            $authGroupList = model('AuthGroup')->select();
            return $this->fetch('', ['authGroupList' => $authGroupList]);
        }
    }

    /**
     * 保存更新的管理员
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update($id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // 验证不需要validate验证的其他参数
            if (!isset($param['group_id'])) { // 所属用户组id
                return show(config('code.error'), '用户组不能为空', [], 412);
            }

            // validate验证
            $validate = validate('Admin');
            if (!$validate->check($param, [], 'update')) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['admin_name'])) {
                $data['admin_name'] = $param['admin_name'];
            }
            if (!empty($param['password'])) {
                $data['password'] = IAuth::encrypt($param['password']);
            }
            if (isset($param['group_id'])) {
                if ($param['group_id'] != $param['old_group_id'] && $id == $this->session_admin['admin_id']) {
                    return show(config('code.error'), '管理员已登录，禁止更新用户组', [], 400);
                }
                $data['status'] = input('param.status', null, 'intval');
            }
            if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
                if ($param['status'] != $this->session_admin['status'] && $id == $this->session_admin['admin_id']) {
                    return show(config('code.error'), '管理员已登录，禁止更新状态', [], 400);
                }
                $data['status'] = input('param.status', null, 'intval');
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Admin')->save($data, ['admin_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                // 更新管理员的Auth用户组
                if ($id && $param['group_id']) {
                    db('auth_group_access')->where(['uid' => $id])->update(['group_id' => $param['group_id']]);
                }

                return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 保存更新的管理员密码
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function updateAdminPassword($id)
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 传入的数据
            $param = input('param.');

            // validate验证
            $validate = validate('Admin');
            if (!$validate->check($param, [], 'update_password')) { // 更新密码场景
                return show(config('code.error'), $validate->getError(), [], 403);
            }

            // 判断数据是否存在
            $data = [];
            if (!empty($param['password'])) {
                $data['password'] = IAuth::encrypt($param['password']);
            }

            if (empty($data)) {
                return show(config('code.error'), '数据不合法', [], 404);
            }

            // 更新
            try {
                $result = model('Admin')->save($data, ['admin_id' => $id]); // 更新
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            if ($result) {
                return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
            } else {
                return show(config('code.error'), '更新失败', [], 403);
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }

    /**
     * 删除指定管理员
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 显示指定的管理员
            try {
                $data = model('Admin')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['admin_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('Admin')->softDelete($this->admin_id, $id);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '软删除失败', ['url' => 'parent']);
                } else {
                    // 删除管理员的Auth用户组
                    db('auth_group_access')->where(['uid' => $id])->delete();

                    return show(config('code.success'), '软删除成功', ['url' => 'delete']); //['url' => config('app.SERVER_NAME') . $this->module . '/admin/index']
                }
            }

            // 真删除
            if ($data['is_delete'] == config('code.is_delete')) {
                $result = model('Admin')->destroy($id);
                if (!$result) {
                    return show(config('code.error'), '删除失败', ['url' => 'parent']);
                } else {
                    return show(config('code.success'), '删除成功', ['url' => 'delete']);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}

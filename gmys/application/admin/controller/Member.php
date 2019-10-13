<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台团队成员控制器类
 * Class Member
 * @package app\admin\controller
 */
class Member extends Base
{
    /**
     * 显示团队成员资源列表
     * @return \think\response\Json
     */
    public function index()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 获取团队成员资源列表
     * @return \think\response\Json
     */
    public function getMember()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['member_name'])) {
                $map['m.member_name'] = ['like', '%' . trim($param['member_name']) . '%'];
            }
            if (!empty($param['level_name'])) {
                // 获取文章类别 level_id
                $memberLevel = db('member_level')->field('level_id')->where('level_name', 'like', '%' . trim($param['level_name']) . '%')->select();
                $level_ids = [];
                foreach ($memberLevel as $key => $value) {
                    $level_ids[] = $value['level_id'];
                }
                $map['m.level_id'] = ['in', $level_ids]; // [NOT] IN 查询
            }
            if (isset($param['is_delete'])) {
                $map['m.is_delete'] = $param['is_delete'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Member')->getMember($map, $this->size);
            $status = config('code.status');
            foreach ($data as $key => $value) {
                // 处理数据
                $data[$key]['status_msg'] = $status[$value['status']]; // 定义status_msg
            }

            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 显示创建团队成员资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view('', ['memberLevelList' => MemberLevel::memberLevelList()]);
        }
    }

    /**
     * 保存新建的团队成员资源
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
            /*$validate = validate('Member');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // TODO：处理数据

            // 新增
            // 捕获异常
            try {
                $id = model('Member')->add($data, 'member_id'); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '团队成员新增成功', ['url' => config('app.I_SERVER_NAME') . $this->module . '/member/index'], 201);
            } else {
                return show(config('code.error'), '团队成员新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的团队成员资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Member')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
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
            return view('', ['memberLevelList' => MemberLevel::memberLevelList()]);
        }
    }

    /**
     * 保存更新的团队成员资源
     * @param Request $request
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function update(Request $request, $id)
    {
        // 传入的数据
        $param = input('param.');

        // validate验证
        /*$validate = validate('Member');
        if (!$validate->check($param, [], '')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }*/

        // 判断数据是否存在
        $data = [];
        if (!empty($param['member_name'])) {
            $data['member_name'] = trim($param['member_name']);
        }
        if (isset($param['level_id'])) {
            $data['level_id'] = $param['level_id'];
        }
        if (!empty($param['abstract'])) {
            $data['abstract'] = trim($param['abstract']);
        }
        if (!empty($param['phone'])) {
            $data['phone'] = trim($param['phone']);
        }
        if (!empty($param['advantage'])) {
            $data['advantage'] = trim($param['advantage']);
        }
        if (!empty($param['production'])) {
            $data['production'] = trim($param['production']);
        }
        if (!empty($param['avatar'])) {
            $data['avatar'] = trim($param['avatar']);

            // 获取更新成功前的团队成员头像avatar
            $member = model('Member')->field('avatar')->find($id);
        }
        if (!empty($param['description'])) {
            $data['description'] = $param['description'];
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = model('Member')->save($data, ['member_id' => $id]); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', [], 403);
        } else {
            // 删除更新成功前的团队成员头像avatar文件
            if (!empty($param['avatar']) && trim($param['avatar']) != $member['avatar']) {
                // 删除文件
                unlink(ROOT_PATH . 'public' . DS . $member['avatar']);
            }

            return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
        }
    }

    /**
     * 删除指定团队成员资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 判断为DELETE请求
        if (request()->isDelete()) {
            // 显示指定的团队成员
            try {
                $data = model('Member')->find($id);
                //return show(config('code.success'), 'ok', $data);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            // 判断数据是否存在
            if ($data['member_id'] != $id) {
                return show(config('code.error'), '数据不存在');
            }

            // 判断删除条件：用户状态
            if (config('code.status_enable') == $data['status']) {
                return show(config('code.error'), '删除失败：团队成员已启用', ['url' => 'deleteFalse']);
            }

            // 软删除
            if ($data['is_delete'] != config('code.is_delete')) {
                // 捕获异常
                try {
                    $result = model('Member')->softDelete('member_id', $id);
                } catch (\Exception $e) {
                    throw new ApiException($e->getMessage(), 500, config('code.error'));
                }

                if (!$result) {
                    return show(config('code.error'), '移除失败', ['url' => 'parent']);
                } else {
                    return show(config('code.success'), '移除成功', ['url' => 'delete']);
                }
            }

            // 真删除
            if ($data['is_delete'] == config('code.is_delete')) {
                $result = model('Member')->destroy($id);
                if (!$result) {
                    return show(config('code.error'), '删除失败', ['url' => 'parent']);
                } else {
                    // 删除文件
                    @unlink(ROOT_PATH . 'public' . DS . $data['avatar']);

                    return show(config('code.success'), '删除成功', ['url' => 'delete']);
                }
            }
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}

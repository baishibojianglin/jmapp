<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台团队成员等级控制器类
 * Class MemberLevel
 * @package app\admin\controller
 */
class MemberLevel extends Base
{
    /**
     * 显示团队成员等级资源列表
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
     * 获取团队成员等级资源列表
     * @return \think\response\Json
     */
    public function getMemberLevel()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            /*if (!empty($param['level_name'])) {
                $map['level_name'] = ['like', '%' . trime($param['level_name']) . '%'];
            }*/

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = db('member_level')->where($map)->paginate($this->size);

            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 获取团队成员等级资源列表（静态方法）
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function memberLevelList()
    {
        $data = db('member_level')->field('level_id, level_name')->select();
        return $data;
    }

    /**
     * 显示创建团队成员等级资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view();
        }
    }

    /**
     * 保存新建的团队成员等级资源
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
            /*$validate = validate('MemberLevel');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // TODO：处理数据

            // 新增
            // 捕获异常
            try {
                $id = db('member_level')->insert($data); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '团队成员等级新增成功', ['url' => 'parent'], 201); //['url' => config('app.I_SERVER_NAME') . $this->module . '/member_level/index']
            } else {
                return show(config('code.error'), '团队成员等级新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的团队成员等级资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = db('MemberLevel')->find($id);
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
            return view();
        }
    }

    /**
     * 保存更新的团队成员等级资源
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
        /*$validate = validate('MemberLevel');
        if (!$validate->check($param, [], '')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }*/

        // 判断数据是否存在
        $data = [];
        if (!empty($param['level_name'])) {
            $data['level_name'] = trim($param['level_name']);
        }
        if (!empty($param['describe'])) {
            $data['describe'] = $param['describe'];
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = db('MemberLevel')->where(['level_id' => $id])->update($data); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', [], 403);
        } else {
            return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
        }
    }

    /**
     * 删除指定团队成员等级资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 显示指定的团队成员等级资源
        try {
            $data = db('MemberLevel')->find($id);
            //return show(config('code.success'), 'ok', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['level_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 查询是否已有团队成员配置该等级
        try {
            $memberList = db('member')->where('level_id', $id)->select();
            //return show(config('code.success'), 'ok', count($memberList));
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 真删除
        if (count($memberList) == 0) {
            $result = db('MemberLevel')->delete($id);
            if (!$result) {
                return show(config('code.error'), '删除失败', ['url' => 'parent']);
            } else {
                return show(config('code.success'), '删除成功', ['url' => 'delete']);
            }
        } else {
            return show(config('code.error'), '删除失败：已有团队成员配置该等级', ['url' => 'deleteFalse']);
        }
    }
}

<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台文章类别控制器类
 * Class ArticleCate
 * @package app\admin\controller
 */
class ArticleCate extends Base
{
    /**
     * 显示文章类别资源列表
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
     * 获取文章类别资源列表
     * @return \think\response\Json
     */
    public function getArticleCate()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['cate_name'])) {
                $map['cate_name|cate_alias'] = ['like', '%' . trim($param['cate_name']) . '%']; // 在多个字段之间用|分割表示OR查询，用&分割表示AND查询，如：Db::table('think_user')->where('name|title','like','thinkphp%')->where('create_time&update_time','>',0)->find();
            }
            if (!empty($param['parent_id'])) {
                $map['parent_id'] = ['like', '%' . trim($param['parent_id']) . '%'];
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取文章类别列表树
            $data = $this->_articleCateTree($map);
            $count = model('ArticleCate')->count();

            return show(config('code.success'), 'OK', $data = ['data' => $data, 'count' => $count]);
        }
    }

    /**
     * 获取处理数据后的文章类别列表树
     * @param $map
     * @return mixed
     * @throws ApiException
     */
    public static function _articleCateTree($map = [])
    {
        // 获取文章类别列表树，用于页面下拉框列表
        // 捕获异常
        try {
            $data = model('ArticleCate')->getArticleCateTree($map);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        if ($data) {
            // 处理数据
            foreach ($data as $key => $value) {
                // 是否导航显示
                $data[$key]['show_in_nav_msg'] = $value['show_in_nav'] == 1 ? '显示' : '不显示';

                if ($value['level'] != 0) {
                    // level 用于定义 title 前面的空位符的长度
                    $data[$key]['cate_name'] = str_repeat('&nbsp;', $value['level'] * 5). '└─ ' . $value['cate_name']; // str_repeat(string,repeat) 函数把字符串重复指定的次数
                }
            }
        }

        return $data;
    }

    /**
     * 显示创建文章类别资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return view('', ['articleCateTree' => $this->_articleCateTree()]);
        }
    }

    /**
     * 保存新建的文章类别资源
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
            /*$validate = validate('ArticleCate');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // TODO：处理数据

            // 新增
            // 捕获异常
            try {
                $id = model('ArticleCate')->add($data, 'cate_id'); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '文章类别新增成功', ['url' => config('app.SERVER_NAME') . $this->module . '/article_cate/index'], 201);
            } else {
                return show(config('code.error'), '文章类别新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的文章类别资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('ArticleCate')->find($id);
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
            return view('', ['articleCateTree' => $this->_articleCateTree()]);
        }
    }

    /**
     * 保存更新的文章类别资源
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
        /*$validate = validate('ArticleCate');
        if (!$validate->check($param, [], '')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }*/

        // 判断数据是否存在
        $data = [];
        if (!empty($param['cate_name'])) {
            $data['cate_name'] = trim($param['cate_name']);
        }
        if (!empty($param['cate_alias'])) {
            $data['cate_alias'] = trim($param['cate_alias']);
        }
        if (isset($param['parent_id'])) {
            $data['parent_id'] = $param['parent_id'];
        }
        if (!empty($param['keywords'])) {
            $data['keywords'] = trim($param['keywords']);
        }
        if (isset($param['show_in_nav'])) {
            $data['show_in_nav'] = $param['show_in_nav'];
        }
        if (!empty($param['cate_description'])) {
            $data['cate_description'] = trim($param['cate_description']);
        }
        if (isset($param['sort'])) {
            $data['sort'] = $param['sort'];
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = model('ArticleCate')->save($data, ['cate_id' => $id]); // 更新
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
     * 删除指定文章类别资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 显示指定的店鋪比赛场次模板
        try {
            $data = model('ArticleCate')->find($id);
            //return show(config('code.success'), 'ok', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['cate_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 真删除
        if ($data['status'] == config('code.status_disable') && empty($data['rules'])) {
            $result = model('ArticleCate')->destroy($id);
            if (!$result) {
                return show(config('code.error'), '删除失败');
            } else {
                return show(config('code.success'), '删除成功');
            }
        } else {
            return show(config('code.error'), '删除失败：用户组启用或用户组规则不为空');
        }
    }
}

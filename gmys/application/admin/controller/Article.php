<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台文章控制器类
 * Class Article
 * @package app\admin\controller
 */
class Article extends Base
{
    private $status = [0 => '草稿', 1 => '通过', 2 => '待审核', 3 => '驳回', 4 => '发布', 5 => '下架']; // 文章状态：0草稿，1通过，2待审核，3驳回，4发布，5下架 //config('code.status')

    /**
     * 显示文章资源列表
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
     * 获取文章资源列表
     * @return \think\response\Json
     */
    public function getArticle()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 传入的数据
            $param = input('param.');
            $query = http_build_query($param); // 生成 URL-encode 之后的请求字符串 //halt($query);

            // 查询条件
            $map = [];
            if (!empty($param['article_title'])) {
                $map['title'] = ['like', '%' . trim($param['article_title']) . '%'];
            }
            if (isset($param['cate_name'])) {
                // 获取文章类别 cate_id
                $articleCate = db('article_cate')->field('cate_id')->where('cate_name', 'like', '%' . trim($param['cate_name']) . '%')->select();
                $cate_ids = [];
                foreach ($articleCate as $key => $value) {
                    $cate_ids[] = $value['cate_id'];
                }
                $map['cate_id'] = ['in', $cate_ids]; // [NOT] IN 查询
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('Article')->getArticle($map, $this->size);
            foreach ($data as $key => $value) {
                // 处理数据
                // 文章类别
                $articleCate = db('article_cate')->field('cate_name')->where('cate_id', $value['cate_id'])->find();
                $data[$key]['cate_name'] = $articleCate['cate_name'];
                if ($value['cate_id'] == 0) {$data[$key]['cate_name'] = '其他';}

                // 定义status_msg
                $data[$key]['status_msg'] = $this->status[$value['status']];
            }
            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 显示创建文章资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 定义顶级类别分组说明
            $cateType = config('code.cate_type'); // 文章类别分组
            $cateTypeDescription = []; // 顶级类别分组说明
            foreach ($cateType as $key => $value) {
                $cateTypeDescription[] = $key . ' ' . $value;
            }
            $cateTypeDescription = implode('，', $cateTypeDescription);

            // 获取设计师
            $member = model('Member')->field('member_id, member_name')->select();

            return view('', ['articleCateTree' => ArticleCate::_articleCateTree(), 'cateTypeDescription' => $cateTypeDescription, 'member' => $member]);
        }
    }

    /**
     * 保存新建的文章资源
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
            /*$validate = validate('Article');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // TODO：处理数据

            // 新增
            // 捕获异常
            try {
                $id = model('Article')->add($data, 'article_id'); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '文章新增成功', ['url' => config('app.SERVER_NAME') . $this->module . '/article/index'], 201);
            } else {
                return show(config('code.error'), '文章新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的文章资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('Article')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $data['status_msg'] = $this->status[$data['status']];

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
            // 定义顶级类别分组说明
            $cateType = config('code.cate_type'); // 文章类别分组
            $cateTypeDescription = []; // 顶级类别分组说明
            foreach ($cateType as $key => $value) {
                $cateTypeDescription[] = $key . ' ' . $value;
            }
            $cateTypeDescription = implode('，', $cateTypeDescription);

            // 获取设计师
            $member = model('Member')->field('member_id, member_name')->select();

            return view('', ['articleCateTree' => ArticleCate::_articleCateTree(), 'cateTypeDescription' => $cateTypeDescription, 'member' => $member]);
        }
    }

    /**
     * 保存更新的文章资源
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
        /*$validate = validate('Article');
        if (!$validate->check($param, [], '')) {
            return show(config('code.error'), $validate->getError(), [], 403);
        }*/

        // 判断数据是否存在
        $data = [];
        if (!empty($param['title'])) {
            $data['title'] = trim($param['title']);
        }
        if (!empty($param['author'])) {
            $data['author'] = trim($param['author']);
        }
        if (isset($param['cate_id'])) {
            $data['cate_id'] = $param['cate_id'];
        }
        if (!empty($param['keywords'])) {
            $data['keywords'] = trim($param['keywords']);
        }
        if (!empty($param['article_abstract'])) {
            $data['article_abstract'] = trim($param['article_abstract']);
        }
        if (!empty($param['house_type'])) {
            $data['house_type'] = trim($param['house_type']);
        }
        if (!empty($param['area'])) {
            $data['area'] = trim($param['area']);
        }
        if (!empty($param['price'])) {
            $data['price'] = trim($param['price']);
        }
        if (isset($param['designer_id'])) {
            $data['designer_id'] = trim($param['designer_id']);
        }
        if (!empty($param['phone'])) {
            $data['phone'] = trim($param['phone']);
        }
        if (!empty($param['thumb'])) {
            $data['thumb'] = trim($param['thumb']);

            // 获取更新成功前的文章缩略图thumb
            $article = model('Article')->field('thumb')->find($id);
        }
        if (!empty($param['content'])) {
            $data['content'] = $param['content'];
        }
        if (isset($param['status'])) { // 不能用 !empty() ，否则 status = 0 时也判断为空
            $data['status'] = input('param.status', null, 'intval');
        }

        if (empty($data)) {
            return show(config('code.error'), '数据不合法', [], 404);
        }

        // 更新
        try {
            $result = model('Article')->save($data, ['article_id' => $id]); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', [], 403);
        } else {

            // 当为更新状态时，直接刷新当前页面
            if (array_key_exists('status', $param) && count($param) == 2) { // 传入2个参数，其中一个是 status
                return $this->redirect('article/index');
            }

            // 删除更新成功前的文章缩略图thumb文件
            if (!empty($param['thumb'] && trim($param['thumb']) != $article['thumb'])) {
                // 删除文件
                unlink(ROOT_PATH . 'public' . DS . $article['thumb']);
            }

            return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
        }
    }

    /**
     * 删除指定文章资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 显示指定的店鋪比赛场次模板
        try {
            $data = model('Article')->find($id);
            //return show(config('code.success'), 'ok', $data);
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }

        // 判断数据是否存在
        if ($data['article_id'] != $id) {
            return show(config('code.error'), '数据不存在');
        }

        // 真删除
        if ($data['status'] == config('code.status_disable') && empty($data['rules'])) {
            $result = model('Article')->destroy($id);
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

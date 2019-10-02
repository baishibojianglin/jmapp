<?php

namespace app\admin\controller;

use app\common\lib\exception\ApiException;
use think\Controller;
use think\Request;

/**
 * 后台新闻控制器类
 * Class News
 * @package app\admin\controller
 */
class News extends Base
{
    private $articleStatus = []; // 文章或新闻状态
    private $newsCateType = []; // 新闻类别分组

    /**
     * 初始化
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->articleStatus = config('code.article_status');
        $this->newsCateType = config('code.news_cate_type');
    }

    /**
     * 显示新闻资源列表
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
     * 获取新闻资源列表
     * @return \think\response\Json
     */
    public function getNews()
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
                // 获取新闻类别 cate_id
                $newsCate = db('news_cate')->field('cate_id')->where('cate_name', 'like', '%' . trim($param['cate_name']) . '%')->select();
                $cate_ids = [];
                foreach ($newsCate as $key => $value) {
                    $cate_ids[] = $value['cate_id'];
                }
                $map['cate_id'] = ['in', $cate_ids]; // [NOT] IN 查询
            }

            // 获取分页page、size
            $this->getPageAndSize($param);

            // 获取分页列表数据 模式一：基于paginate()自动化分页
            $data = model('News')->getNews($map, $this->size);
            foreach ($data as $key => $value) {
                // 处理数据
                // 新闻类别
                $newsCate = db('news_cate')->field('cate_name')->where('cate_id', $value['cate_id'])->find();
                $data[$key]['cate_name'] = $newsCate['cate_name'];
                if ($value['cate_id'] == 0) {$data[$key]['cate_name'] = '其他';}

                // 定义status_msg
                $data[$key]['status_msg'] = $this->articleStatus[$value['status']];
            }
            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 显示创建新闻资源表单页.
     *
     * @return \think\Response
     */
    public function create()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            // 定义顶级类别分组说明
            $cateTypeDescription = []; // 顶级类别分组说明
            foreach ($this->newsCateType as $key => $value) {
                $cateTypeDescription[] = $key . ' ' . $value;
            }
            $cateTypeDescription = implode('，', $cateTypeDescription);

            return view('', ['newsCateTree' => NewsCate::_newsCateTree(), 'cateTypeDescription' => $cateTypeDescription]);
        }
    }

    /**
     * 保存新建的新闻资源
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
            /*$validate = validate('News');
            if (!$validate->check($data)) {
                return show(config('code.error'), $validate->getError(), [], 403);
            }*/

            // TODO：处理数据

            // 新增
            // 捕获异常
            try {
                $id = model('News')->add($data, 'article_id'); // 新增
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }
            // 判断是否新增成功：获取id
            if ($id) {
                return show(config('code.success'), '新闻新增成功', ['url' => config('app.SERVER_NAME') . $this->module . '/news/index'], 201);
            } else {
                return show(config('code.error'), '新闻新增失败', [], 403);
            }
        }
    }

    /**
     * 显示指定的新闻资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function read($id)
    {
        // 判断为GET请求
        if (request()->isGet()) {
            try {
                $data = model('News')->find($id);
            } catch (\Exception $e) {
                throw new ApiException($e->getMessage(), 500, config('code.error'));
            }

            if ($data) {
                // 处理数据
                // 定义status_msg
                $data['status_msg'] = $this->articleStatus[$data['status']];

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
            $cateTypeDescription = []; // 顶级类别分组说明
            foreach ($this->newsCateType as $key => $value) {
                $cateTypeDescription[] = $key . ' ' . $value;
            }
            $cateTypeDescription = implode('，', $cateTypeDescription);

            return view('', ['newsCateTree' => NewsCate::_newsCateTree(), 'cateTypeDescription' => $cateTypeDescription]);
        }
    }

    /**
     * 保存更新的新闻资源
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
        /*$validate = validate('News');
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
        if (!empty($param['thumb'])) {
            $data['thumb'] = trim($param['thumb']);

            // 获取更新成功前的新闻缩略图thumb
            $news = model('News')->field('thumb')->find($id);
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
            $result = model('News')->save($data, ['article_id' => $id]); // 更新
        } catch (\Exception $e) {
            throw new ApiException($e->getMessage(), 500, config('code.error'));
        }
        if (false === $result) {
            return show(config('code.error'), '更新失败', [], 403);
        } else {

            // 当为更新状态时，直接刷新当前页面
            if (array_key_exists('status', $param) && count($param) == 2) { // 传入2个参数，其中一个是 status
                return $this->redirect('news/index');
            }

            // 删除更新成功前的新闻缩略图thumb文件
            if (!empty($param['thumb']) && trim($param['thumb']) != $news['thumb']) {
                // 删除文件
                unlink(ROOT_PATH . 'public' . DS . $news['thumb']);
            }

            return show(config('code.success'), '更新成功', ['url' => 'parent'], 201);
        }
    }

    /**
     * 删除指定新闻资源
     * @param int $id
     * @return \think\response\Json
     * @throws ApiException
     */
    public function delete($id)
    {
        // 显示指定的店鋪比赛场次模板
        try {
            $data = model('News')->find($id);
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
            $result = model('News')->destroy($id);
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

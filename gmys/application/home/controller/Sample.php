<?php

namespace app\home\controller;
use app\common\lib\exception\ApiException;

/**
 * Class Sample
 * @package app\home\controller
 */
class Sample extends Base
{
    /**
     * 案例列表
     * @return \think\response\View
     */
    public function index()
    {
        // 获取案例列表
        $caseList = $this->getArticle();
        $caseList = json_decode(json_encode($caseList), true);

        return view('case/case', ['caseList' => $caseList]);
    }

    /**
     * 获取案例列表
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
            $map = ['status' => 4, 'is_delete' => config('code.not_delete')];
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

            return $data;
        }
    }

    /**
     * 案例详情
     * @return \think\response\View
     */
    public function sampleDetail($id)
    {
        // 显示指定的新闻资源
        $caseData = $this->read($id);
        $caseData = json_decode(json_encode($caseData), true);
        // 发布时间
        $caseData['publish_time'] = date('Y-m-d', $caseData['publish_time']);

        // 更新浏览量views
        @model('Article')->where('article_id', $id)->setInc('views');

        return view('case/case_detail', ['caseData' => $caseData]);
    }

    /**
     * 显示指定的案例资源
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
                return $data;
            }
        }
    }
}
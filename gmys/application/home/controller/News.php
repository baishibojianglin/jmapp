<?php

namespace app\home\controller;

use app\common\lib\exception\ApiException;

/**
 * Class News
 * @package app\home\controller
 */
class News extends Base
{
    /**
     * 新闻列表
     * @return \think\response\View
     */
    public function news()
    {
        // 获取新闻列表
        $newsList = $this->getNews();
        $newsList = json_decode(json_encode($newsList), true);

        return view('', ['newsList' => $newsList]);
    }

    /**
     * 获取新闻列表
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
            $map = ['status' => 4, 'is_delete' => config('code.not_delete')];
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
                // 发布时间
                $value['publish_time'] = date('Y-m-d', $value['publish_time']);
            }

            return $data;
        }
    }

    /**
     * 新闻详情
     * @return \think\response\View
     */
    public function newsDetail($id)
    {
        // 显示指定的新闻资源
        $newsData = $this->read($id);
        $newsData = json_decode(json_encode($newsData), true);
        // 发布时间
        $newsData['publish_time'] = date('Y-m-d', $newsData['publish_time']);

        // 更新浏览量views
        @model('News')->where('article_id', $id)->setInc('views');

        return view('', ['newsData' => $newsData]);
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
                return $data;
            }
        }
    }
}
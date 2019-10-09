<?php

namespace app\home\controller;

use think\Controller;

/**
 * Class Base
 * @package app\home\controller
 */
class Base extends Controller
{
    /**
     * page 当前页
     * @var string
     */
    public $page = '';

    /**
     * size 每页条数
     * @var string
     */
    public $size = '';

    /**
     * 每页从第几条开始
     * @var int
     */
    public $from = 0;

    /**
     * 初始化
     */
    public function _initialize()
    {
        // 获取网站信息
        $this->getSiteInfo();
    }

    /**
     * 获取网站信息
     */
    public function getSiteInfo()
    {
        $siteInfo = model('Config')->where('type', 'site_info')->cache(true, 10)->select();

        // 二维数组转一维数组 ['name' => 'value']，即以数据表 `name`字段 的值为键名，以 `value`字段 的值为值
        $data = i_array_column($siteInfo, 'value', 'name');

        $this->assign('siteInfo', $data);
    }

    /**
     * 获取分页page、size、from
     * @param $params
     */
    public function getPageAndSize($params)
    {
        $this->page = !empty($params['page']) ? $params['page'] : 1;
        $this->size = !empty($params['size']) ? $params['size'] : config('paginate.list_rows');
        $this->from = ($this->page - 1) * $this->size; // 'limit from,size'
    }
}

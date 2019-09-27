<?php

namespace app\admin\controller;

/**
 * 网站系统配置控制器类
 * Class Config
 * @package app\admin\controller
 */
class SystemConfig extends Base
{
    /**
     * 显示网站信息配置表单页
     * @return mixed
     */
    public function siteInfo()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            return $this->fetch();
        }
    }

    /**
     * 获取网站信息
     * @return \think\response\Json
     */
    public function getSiteInfo()
    {
        // 判断为GET请求
        if (request()->isGet()) {
            $siteInfo = model('Config')->where('type', 'site_info')->cache(true, 10)->select();

            // 二维数组转一维数组 ['name' => 'value']，即以数据表 `name`字段 的值为键名，以 `value`字段 的值为值
            $data = array_column($siteInfo, 'value', 'name'); // array_column(array,column_key,index_key) 返回输入数组中某个单一列的值。

            return show(config('code.success'), 'OK', $data);
        }
    }

    /**
     * 保存更新或新增的网站信息资源
     * @return \think\response\Json
     */
    public function update()
    {
        // 判断为PUT请求
        if (request()->isPut()) {
            // 获取更新前的网站信息
            $siteInfo = model('Config')->where('type', 'site_info')->select();
            $data = array_column($siteInfo, 'value', 'name');
            // 获取更新成功前的网站logo
            $siteInfoData = model('Config')->field('value')->where(['name' => 'site_logo'])->find();

            // 传入的参数
            $param = input('param.');

            // 更新数据方式1 foreach遍历更新：批量更新仅能根据主键值进行更新，其它情况请使用foreach遍历更新
            // 遍历传入的参数 $param，获取参数的键名 $key（`name`字段的值） 和值 $value（`value`字段的值）
            foreach ($param as $key => $value) {

                // 判断传入的参数 $param 的值 $param[$key] 即 $value 不为空时，执行更新或新增
                if (!empty($value)) {

                    // 遍历更新前的网站信息 $data，获取键名 $k（`name`字段的值） 和值 $v（`value`字段的值）
                    $k_arr = [];
                    foreach ($data as $k => $v) {
                        $k_arr[] = $k;
                        // 当传入的参数值与更新前的`value`字段的值不同时，执行更新
                        if ($key == $k && $value != $v) {
                            // 更新
                            $result[$key] = model('Config')->allowField(true)->save(['value' => $value], ['name' => $key, 'type' => 'site_info']);
                            if (false == $result[$key]) {
                                return show(config('code.error'), $key . '更新失败', [], 403);
                            }

                            // 删除更新成功前的网站logo文件
                            if ($key == 'site_logo' && trim($value) != $siteInfoData['value']) {
                                // 删除文件
                                unlink(ROOT_PATH . 'public' . DS . $siteInfoData['value']);
                            }
                        }
                    }

                    // 如果传入的参数的键名 $key（`name`字段的值）在数据库不存在时，执行新增
                    if (!in_array($key, $k_arr)) {
                        model('Config')->allowField(true)->insert(['name' => $key, 'value' => $value, 'type' => 'site_info']);
                    }
                }
            }

            // 更新数据方式2 批量更新数据：根据复合主键 PRIMARY KEY (`id`, `name`) （需将 `id` 和 `name` 均设置成主键）（执行失败）
            /*$list = [
                ['id' => 2, 'name' => 'record_no', 'value' => $param['record_no']],
                ['id' => 3, 'name' => 'site_name', 'value' => $param['site_name']],
                ['id' => 5, 'name' => 'site_title', 'value' => $param['site_title']],
                ['id' => 6, 'name' => 'site_description', 'value' => $param['site_description']],
                ['id' => 7, 'name' => 'site_keyword', 'value' => $param['site_keyword']],
                ['id' => 8, 'name' => 'contact', 'value' => $param['contact']],
                ['id' => 9, 'name' => 'phone', 'value' => $param['phone']],
                ['id' => 10, 'name' => 'email', 'value' => $param['email']],
                ['id' => 11, 'name' => 'address', 'value' => $param['address']],
                ['id' => 12, 'name' => 'qq', 'value' => $param['qq']],
            ];
            model('Config')->saveAll($list);*/

            return show(config('code.success'), '更新成功', ['url' => config('app.SERVER_NAME') . $this->module . '/system_config/siteInfo'], 201);
        } else {
            return show(config('code.error'), '请求不合法', [], 400);
        }
    }
}
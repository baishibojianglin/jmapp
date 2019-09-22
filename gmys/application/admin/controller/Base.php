<?php

namespace app\admin\controller;

use think\auth\Auth;
use think\Controller;
use think\Db;
use think\Session;

/**
 * 后台基础类库
 * Class Base
 * @package app\admin\controller
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
     * 模块
     * @var string
     */
    public $module = 'admin';

    /**
     * 当前登录管理员session值
     * @var mixed
     */
    public $session_admin;

    /**
     * 初始化
     */
    public function _initialize()
    {
        // 初始化参数
        $this->module = request()->module(); // 模块
        $this->session_admin = session(config('admin.session_admin'), '', config('admin.session_admin_scope')); // 管理员session值

        // 判断是否登录
        $isLogin = $this->isLogin();
        if (!$isLogin) {
            $this->redirect('Login/index');
        }

        // Auth权限认证：对节点进行认证
        //$this->checkAuth();

        // 获得权限菜单，模板变量赋值
        $menus = $this->getAuthRuleMenus($this->session_admin->admin_id);
        foreach($menus as $key => $value) {
            $menus[$key] = json_decode($value, true);
        }
        $this->assign('menus', $menus);
        //dump($menus);
    }

    /**
     * 判断是否登录
     * @return bool
     */
    public function isLogin()
    {
        /*// 获取session
        $admin_data = session(config('admin.session_admin'), '', config('admin.session_admin_scope'));*/
        if ($this->session_admin && $this->session_admin->admin_id) {
            $this->assign('session_admin', $this->session_admin); // 模板变量赋值
            return true;
        }
        return false;
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

    /**
     * Auth权限认证：对节点进行认证
     */
    public function checkAuth()
    {
        $auth = new Auth(); // 实例化Auth权限认证类
        $controller = request()->controller(); // 控制器
        $action = request()->action(); // 方法
        $name = $this->module . '/' . $controller . '/' . $action; // 规则唯一标识
        $notCheckName = [ // 不需认证的规则
            $this->module . '/' .'Index/index',
            $this->module . '/' .'Login/logout',
        ];
        // 超级管理员拥有所有权限
        if (!in_array($this->session_admin->admin_id, $this->getSuperAdmin())) {
            // 不需认证的规则
            if (!in_array($name, $notCheckName)) {
                // 检查权限
                if (!$auth->check($name, $this->session_admin->admin_id, $type = 2)) {
                    $this->error('无权访问'); //return show(config('code.error'), '无权访问', [], 401);
                }
            }
        }
    }

    /**
     * 获得权限菜单（参考： /vendor/5ini99/think-auth/src/Auth.php 类 getAuthList($uid, $type) 方法）
     * @param $uid
     * @param int $type
     * @return array|mixed
     */
    public function getAuthRuleMenus($uid, $type =2)
    {
        $auth = new Auth(); // 实例化Auth权限认证类

        static $_authList = []; //保存用户验证通过的权限列表
        $t = implode(',', (array)$type);
        if (isset($_authList[$uid . $t])) {
            return $_authList[$uid . $t];
        }
        if (2 == $type && Session::has('_auth_list_' . $uid . $t, config('admin.session_admin_scope'))) {
            return Session::get('_auth_list_' . $uid . $t, config('admin.session_admin_scope'));
        }
        //读取用户所属用户组
        $groups = $auth->getGroups($uid);
        $ids = []; //保存用户所属用户组设置的所有权限规则id
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid . $t] = [];
            return [];
        }

        $map = array(
            //'id' => ['in', $ids],
            'type' => $type,
            'status' => 1,
        );
        // 判断是否为超级管理员：不是超级管理员时查询用户组对应的规则，否则查询所有规则
        if (!in_array($this->session_admin->admin_id, $this->getSuperAdmin())) {
            $map['id'] = ['in', $ids];
        }

        //读取用户组所有权限规则
        $rules = Db::name('auth_rule')->where($map)->field('condition,name,title,id,pid,level,icon')->order('sort')->select();
        //循环规则，判断结果。
        $authList = []; //
        foreach ($rules as $rule) {
            if (!empty($rule['condition'])) {
                //根据condition进行验证
                $user = $this->getUserInfo($uid); //获取用户信息,一维数组
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $rule['condition']);
                //dump($command); //debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = json_encode([
                        'id' => $rule['id'],
                        'name' => $rule['name'], //strtolower($rule['name'])
                        'title' => $rule['title'],
                        'pid' => $rule['pid'],
                        'level' => $rule['level'],
                        'icon' => $rule['icon'],
                    ]);
                }
            } else {
                //只要存在就记录
                $authList[] = json_encode([
                    'id' => $rule['id'],
                    'name' => $rule['name'], //strtolower($rule['name'])
                    'title' => $rule['title'],
                    'pid' => $rule['pid'],
                    'level' => $rule['level'],
                    'icon' => $rule['icon'],
                ]);
            }
        }
        $_authList[$uid . $t] = $authList;
        if (2 == $type) {
            //规则列表结果保存到session
            Session::set('_auth_list_' . $uid . $t, $authList, config('admin.session_admin_scope'));
        }

        return array_unique($authList);
    }

    /**
     * 获得用户资料,根据自己的情况读取数据库（参考： /vendor/5ini99/think-auth/src/Auth.php 类 getUserInfo($uid) 方法）
     * @param $uid
     * @return mixed
     */
    protected function getUserInfo($uid)
    {
        static $userinfo = [];

        $user = Db::name('admin');
        // 获取用户表主键
        $_pk = is_string($user->getPk()) ? $user->getPk() : 'uid';
        if (!isset($userinfo[$uid])) {
            $userinfo[$uid] = $user->where($_pk, $uid)->find();
        }

        return $userinfo[$uid];
    }

    /**
     * 获取超级管理员
     * @return array
     */
    public function getSuperAdmin()
    {
        $userGroupIds = Db::name('auth_group_access')->where('group_id = 1')->select();
        $superAdmin = [];
        foreach ($userGroupIds as $key => $value) {
            $superAdmin[] = $value['uid'];
        }
        return $superAdmin;
    }


    /**
     * ajax 修改指定表数据字段  一般修改状态，比如：是否推荐、是否开启 等 图标切换的
     * table,id_name,id_value,field,value
     * @return \think\response\Json
     * @throws \think\Exception
     */
    public function changeTableVal(){
        $table = input('table'); // 表名
        $id_name = input('id_name'); // 表主键id名
        $id_value = input('id_value'); // 表主键id值
        $field  = input('field'); // 修改哪个字段
        $value  = input('value'); // 修改字段值
        $result = db($table)->where("$id_name = $id_value")->update(array($field => $value)); // 根据条件保存修改的数据
        if($result){
            return show(config('code.success'), '更新成功', [], 201);
        }else{
            return show(config('code.error'), '更新失败', [], 403);
        }
    }
}

<?php
namespace app\admin\controller;

class Index extends Base
{
    public function index()
    {
        //halt(session(config('admin.session_admin'), '', config('admin.session_admin_scope')));
        return $this->fetch();
    }

    public function console()
    {
        return $this->fetch();
    }
}

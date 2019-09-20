<?php
namespace app\index\controller;
use think\Db;

class Index
{
    public function index()
    {
       $articlelist=Db::name('article')->order('artitle_id desc')->select();     
       return json($articlelist);
    }
    
}

<?php
namespace app\index\controller;
use think\Db;

class Index
{
    public function index()
    {
       $articlelist=Db::name('article')->select();     
       return json($articlelist);
    }
}

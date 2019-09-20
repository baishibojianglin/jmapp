<?php
namespace app\index\controller;
use think\Db;

class Index
{   
	//装饰风格列表
    public function index()
    {
       $articlelist=Db::name('article')->where('status',4)->field('article_id,thumb,title,house_type,area,price,phone')->order('article_id desc')->select();     
       return json($articlelist);
    }
    
}



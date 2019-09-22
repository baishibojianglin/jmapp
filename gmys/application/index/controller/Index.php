<?php
namespace app\index\controller;
use think\Db;

class Index
{   
	//装饰风格文章列表
    public function index()
    {
       $articlelist=Db::name('article')->where('status',4)->field('article_id,thumb,title,house_type,area,price,phone')->order('article_id desc')->select();     
       return json($articlelist);
    }

    //装饰风格文章详情
    public function detail()
    {
       $match['article_id']=$_POST['id'];
       $content=Db::name('article')->where($match)->find();     
       return json($content);
    }   
    
}



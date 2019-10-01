<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;


class Index extends Controller
{   

	 //装饰风格文章列表
    public function index(){

       $articlelist=Db::name('article')->where('status',4)->field('article_id,thumb,title,house_type,area,price,phone')->order('article_id desc')->select();     
       return json($articlelist);
    }

    //装饰风格文章详情
    public function detail()
    {
       //查文章内容
       $match_article['article_id']=$_POST['id'];
       $content=Db::name('article')->alias('a')->join('member b','b.member_id=a.designer_id')->where($match_article)->find();
       return json($content);
       
    }  


    //设计师团队
    public function designer()
    {
       $match_design['status']=1;
       $designlist=Db::name('member')->where($match_design)->select();
       return json($designlist);     
    } 




    
}



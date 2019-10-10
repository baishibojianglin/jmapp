<?php

namespace app\index\controller;

use think\Controller;
use think\Db;
use think\Request;


class Index extends Controller
{   

    //装饰风格文章列表
    public function index(){
       $match['a.cate_id'] = $_GET['typeid'];
       $match['b.cate_type'] = 1; // 类别分组：1微信公众号菜单
       $match['a.status'] = 4; // 文章状态：4发布
       $articlelist=Db::name('article')->alias('a')->join('article_cate b','b.cate_id=a.cate_id')->where($match)->field('article_id,thumb,title,house_type,area,price,phone')->order('article_id desc')->select();
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
       $designlist=Db::name('member')->alias('a')->join('member_level b','b.level_id=a.level_id')->where($match_design)->select();
       return json($designlist);     
    } 

    //设计师详情
    public function designerdetail()
    {
       $list=$this->request->param();
       $match_design['member_id']=$list['id'];
       $designlist=Db::name('member')->alias('a')->join('member_level b','b.level_id=a.level_id')->where($match_design)->find();
       return json($designlist);   
    } 


    //关于我们
    public function about()
    {
       $list=Db::name('config')->where('id',14)->find();
       return json($list);   
    }

}
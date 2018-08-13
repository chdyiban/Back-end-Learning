<?php
namespace app\index\controller;
use app\index\model\Message;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
        $list= Message::all();
        $this->assign('list',$list);
        return $this->fetch();
		// $this->assign('list',$list);
       // return $list;
    }
    public function add()
    {
        return $this->fetch();
    }
    public function addarticle()
    {
        $message=new Message();  
        $message->name=input('post.name');
        $message->article=input('post.article');
        if($message->save()){
            return $this->success('留言成功！','index');
        }else{
            return $this->success('留言失败');
        }
    }

}

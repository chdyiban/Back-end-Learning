<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\User;
use app\admin\model\Message;
class Index extends Controller
{
    public function index()
    {
        $list= Message::all();
        $this->assign('list',$list);
        return $this->fetch();
    }
    public function login()
    {
        return $this-> fetch();
    }
    public function check(){
        $data=input('post.');
        $user =new User();
        $result =$user ->where('email',$data['name'])->find();
        if($result){
            if($user->where('password',$data['password'])->find()){
               session('email',$data['name']);
                $this->success('登陆成功','index');
            }else{
                $this->error('密码错误');
            }
        }else{
            $this->error('用户名不存在');
        }
        dump($data);
    }
    // public function addarticle()
    // {
    //     $message=new MessageModel();  
    //     $message->name  = $_POST['name'];
    //     $message->article= $_POST['content'];
    //     $message->save();
    //     return fetch('index');
    // }

}
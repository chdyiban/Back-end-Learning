<?php
namespace app\index\controller;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
		return $this->fetch();
        
    }
    public function add()
    {
        return $this->fetch();
    }
    public function login()
    {
        if(request()->isAjax()){
            $data=[
                'username'=>input('post.username'),
                'password'=>input('post.password')
            ]
        }
        return $this->fetch();
    }
}
<?php
namespace app\index\controller;
use app\index\model\Message as MessageModel;
use think\Controller;
class Index extends Controller
{
    public function index()
    {
        return $this-> fetch();
    }
    public function add()
    {
        return $this -> fetch();
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

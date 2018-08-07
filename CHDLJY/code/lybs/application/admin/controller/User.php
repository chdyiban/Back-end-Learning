<?php
namespace app\admin\controller;
use think\Controller;
class User extends Controller
{
    public function index()
    {
        return $this-> fetch();
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
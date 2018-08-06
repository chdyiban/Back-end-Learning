<?php
namespace app\index\controller;
use app\index\model\User as UserModel;

class User
{
    public function add()
    {
        $user=new UserModel();
        $user->username='asdfasd';
        $user->password='123456';
        $user->nickname='xk';
        $user->save();
       return "fadfdsa";
        
    }
}
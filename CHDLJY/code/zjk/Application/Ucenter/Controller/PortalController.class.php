<?php
/**
 * 放置用户登陆注册
 */
namespace Ucenter\Controller;


use Common\Model\FollowModel;
use Think\Controller;
use User\Api\UserApi;

require_once APP_PATH . 'User/Conf/config.php';

class PortalController extends Controller
{
    public function loginSuccess(){
        echo $_GET['login'].'<br/>';
        echo $_GET['id']; 

        //在这里写登录成功的方法
        
    }

}
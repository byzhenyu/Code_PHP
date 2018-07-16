<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller 
{
    /**
     * 登录密码验证
     */
    public function login_password() {
    	$password = '12345678';
    	$chk = checkLoginPassword($password);
    	echo $chk; //-1密码长度不足，1弱 2中 3高
    }

    /**
     * 支付密码验证
     */
    public function pay_password() {
    	$password = '123456';
    	$chk = checkPayPassword($password);
    	echo $chk; //
    }
    
}
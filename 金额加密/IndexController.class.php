<?php

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller {
    public function index() {
       $money      = 19.96;
       $user_id    = 10;
       $Aes_string = '02154B81D3AD53CEF07384FF52090B4FC0721459A6223E57D1CC5E5B7B5797F3';
       $code = Ase_Encrypt($money,$user_id);   //加密
       $code = Ase_Decrypt($Aes_string);//解密
       echo $code;
    }
}
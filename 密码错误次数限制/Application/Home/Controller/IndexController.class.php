<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 控制器
 * create by wangzhiliang 
 */
class IndexController extends Controller {

    public function index(){
        $user_id = 1;
        $count = 5;
        $continued_time = 60;
        $res = errorCount(2, $user_id, $count, $continued_time);
        p($res);
    }
}

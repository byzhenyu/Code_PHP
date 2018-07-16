<?php

// 常量定义
const VERSION    = '1.0.0.0';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

//用于测试打印数组数据
function p($arr){
	header('content-type:text/html;charset=utf-8');
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

/**
  * 用于API调式时输出LOG文件
  * @param mixed $value 要打印的数年据
  * @param string $file 文件要保存的完整路径, 含文件名, 默认在当前控制器目录下同名.log
  * @return null 无返回值
  * by zhaojiping liuniukeji.com
  */
function LL($value='', $file=''){
    if ($file == '') {
        $file = './Application/'. MODULE_NAME .'/Controller/'. CONTROLLER_NAME .'Controller.class.log';
    }
    error_log(print_r($value,1),3, $file);
}

/**
 * 返回JSON通一格式
 * by zhaojiping liuniukeji.com
 */
function V($status=-1, $info='', $data=''){
    if ($status == -1) {
        exit('参数调用错误');
    }
    return array('status'=>$status, 'info'=>$info, 'data'=>$data);
}

/**
 * 返回JSON通一格式
* @param int $type  1：第一次验证  2；后续验证
 * @param int $user_id  用户id
 * @param int $count  错误次数
 * @param int $continued_time  持续时间，时间戳
 */
function errorCount($type, $user_id, $count, $continued_time){

    $PasswordErrorCount = M('PasswordErrorCount');
    $time = time();
    $where['user_id'] = $user_id;
    $info = $PasswordErrorCount->where($where)->find();
    if ($type == 1) {
        if ($info) {   // 存在已经密码错误的记录
            if ($info['count'] == $count && $info['over_time'] > $time) {
                return array('status'=>0, 'info'=>'密码错误次数达到上限，请过段时间在登录');
            } else {
                return array('status'=>1, 'info'=>'错误次数解封，可进行后续操作');
            }
        } else {
            return array('status'=>1, 'info'=>'没有查询到密码错误提示，可继续进行下一步');
        }
    } elseif ($type == 2) {
        if ($info) {  // 存在已经密码错误的记录
            // 判断错误次数
            if ($info['count'] == $count && $info['over_time'] < $time) {
                // 重新设置
                $setdata = array(
                    'count' => 1,
                    'add_time' => $time,
                    'over_time' => $time + $continued_time,
                );
                $PasswordErrorCount->where($where)->save($setdata);
                return array('status'=>0, 'info'=>'密码错误次数重新开始');
            } elseif ($info['count'] == $count && $info['over_time'] > $time) {
                return array('status'=>0, 'info'=>'密码错误次数达到上限，请过段时间在登录');
            } else {
                // 更新错误密码次数
                $PasswordErrorCount->where($where)->setInc('count', 1);
                return array('status'=>0, 'info'=>'密码错误次数+1');
            }
        } else {  // 不存在已经密码错误的记录
            $data = array(
                'user_id' => $user_id,
                'count' => 1,
                'add_time' => $time,
                'over_time' => $time + $continued_time,
            );
            $PasswordErrorCount->add($data);
            return array('status'=>0, 'info'=>'密码错误，开始记录错误次数');
        }
    }
}
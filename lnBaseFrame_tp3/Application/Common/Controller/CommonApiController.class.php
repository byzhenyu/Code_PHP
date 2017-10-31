<?php
namespace Common\Controller;
/**
 * 用户登录后, 需要继承的基类
 * create by zhaojiping <QQ: 17620286>
 */
class CommonApiController extends CommonController {

    protected function _initialize(){
        $code = $_POST['code'];
        if ($code == '') {
            $this->ajaxReturn(V('0', '非法访问'));
        }
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
        if (C('APP_DATA_ENCODE') === true) {
            // 解密
            $aes = new \Common\Tools\Aes();
            $code = $aes->aes128cbcHexDecrypt($code);
    		if ($code == '') {
            	$this->ajaxReturn(V('0', '非法访问!'));
            }
        }
        $params = json_decode($code, true);
        // 重新赋值
        $_POST = null;
        foreach ($params as $key => $value) {
            // $_GET[$key] = $value;
            $_POST[$key] = $value;
            if ($key == 'p') {
                $_GET['p'] = $value;
            }
        }
        $token = I('post.token', '');
        // 判断token值是否正确并返回用户信息
        $uid = $this->checkTokenAndGetUid($token);
        if ($uid > 0) {
        	define('UID', $uid);
        } else {
        	$this->ajaxReturn(V('0', '用户不合法, 无权访问'));
        }
    }

    protected function apiReturn($status, $message='', $data=''){
        if ($status != 0 && $status != 1) {
            exit('参数调用错误 status');
        }

        if ($data != '' && C('APP_DATA_ENCODE') == true) {
            $data = json_encode($data); // 数组转为json字符串
            $aes = new \Common\Tools\Aes();
            $data = $aes->aes128cbcEncrypt($data); // 加密
        }

        if (is_null($data) || empty($data)) $data = array();
                $this->ajaxReturn(V($status, $message, $data));

    }

    private function checkTokenAndGetUid($token){
    	$where['token'] = $token;
    	$where['disabled'] = 0;
    	$where['status'] = 0;
    	$where['isUser'] = 0;
    	$id = M('Member')->where($where)->getField('id');
    	return $id;
    }

}

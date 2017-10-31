<?php
namespace Admin\Controller;
/**
 *  后台首页控制器
 */
class PublicController extends \Think\Controller {

    /**
     * 后台用户登录显示
     */
    public function login(){
        // 123456的加密758e001c4fd2b221540ef0a36a133f02d93a5def7511da3d0f2d171d9c344e91
        if(is_login()){
            $this->redirect('Index/index');
        }else{
            /* 读取数据库中的配置 */
            $config	=	S('DB_CONFIG_DATA');
            if(!$config){
                $config = api('Config/lists');
                S('DB_CONFIG_DATA',$config);
            }
            C($config); //添加配置

            // 随机加密字符串
            $security_code = randNumber();
            session('security_code', $security_code);
            $this->security_code = $security_code;
            
            $this->display();
        }
        
    }

    /**
     * 后台用户登录
     */
    public function doLogin(){
        //$this->ajaxReturn(V(1, '登录成功'));
        $verify = I('verify', '');
        /* 检测验证码 TODO: */
       if(!check_verify($verify)){
            $this->ajaxReturn(V(0, '验证码输入错误'));
        }  

        $username = I('username', '');
        $where['username'] = array('eq', $username);
        $disabled = M('Member')->where($where)->getfield('disabled');
        unset($where);
        if ($disabled == 1) {
            $this->ajaxReturn(V(0, '您的账号已被禁用！'));
        }
        $password = I('password', '');
        // 获取security_code, 重新组织密码
        $pre_code = substr($password, 0, 2);
        $end_code = substr($password, -2);
        $password = substr($password, 2, -2);
        $security_code = $pre_code . $end_code;
        if ($security_code != session('security_code')) {
            //记录日志
            D('LoginHistory')->addMemberLoginLog(1, $username, 0, 1, '非法登录');
            $this->ajaxReturn(V(0, '非法操作, 您的IP已记录'));
        }

        $member = D('Common/Member');
        $data = $member->create(I('post.'), 4);
        if (!$data) {
            //记录日志
            D('LoginHistory')->addMemberLoginLog(1, $username, 0, 1, $member->getError());
            $this->ajaxReturn(V(0, $member->getError()));
        }
        $loginInfo = $member->login($username, $password, 'admin', session('security_code'));
        if( $loginInfo['status'] == 1 ){ //登录成功
            /* 存入session */
            $this->autoSession($loginInfo['data']);
            //记录日志
            D('LoginHistory')->addMemberLoginLog(1, '', $loginInfo['data']['id'], 0, '登录成功');
            $this->ajaxReturn(V(1, '登录成功'));
        } else {
            //记录日志
            D('LoginHistory')->addMemberLoginLog(1, $username, 0, 1, $loginInfo['info']);
            $this->ajaxReturn(V(0, $loginInfo['info']));
        }
    }

    /* 退出登录 */
    public function logout(){
        session(null);
        $this->redirect('login');
    }

    public function verify(){
        $Verify = new \Think\Verify(array(
            'length' => 4,
            'useNoise' => FALSE,
            'imageH' =>40,
            'imageW' => 100,
            'fontSize'=>14
        ));
        $Verify->entry(1);
    }
    
    /**
     * 自动登录用户
     * @param  integer $user 用户信息数组
     */
    private function autoSession($user){
    	/* 记录登录SESSION和COOKIES */
    	$auth = array(
			'id'              => $user['id'],
			'uid'             => $user['id'],
			'username'        => $user['username'],
			'real_name'       => $user['real_name'],
			'phone'           => $user['phone'],
			'photo_path'       => $user['photo_path'],
			'last_login_time' => $user['last_login_time']
    	   );
    	session('admin_auth', $auth);
    }


}

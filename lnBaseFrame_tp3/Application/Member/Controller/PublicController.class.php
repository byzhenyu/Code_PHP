<?php

namespace Member\Controller;

/**
 * @author zhaojiping <QQ: 17620286>
 */
class PublicController extends \Think\Controller {

    protected function _initialize(){
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置
    }

    /**
     * 后台用户登录显示
     */
    public function login(){
        // 123456的加密758e001c4fd2b221540ef0a36a133f02d93a5def7511da3d0f2d171d9c344e91
        if(user_is_login()){
            $this->redirect('Home/Index/index');
        }else{
            /* 读取数据库中的配置 */
            $config =   S('DB_CONFIG_DATA');
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
        $member = D('Common/Member');
        $data = $member->create(I('post.'), 4);
        $username = I('post.username', '');
        if (!$data) {
            D('Basic/LoginHistory')->addMemberLoginLog(0, $username, 0, 1, $member->getError().'(PC)');
            $this->ajaxReturn(V(0, $member->getError()));
        }

        $where['username'] = array('eq', $username);
        $disabled = M('Member')->where($where)->getfield('disabled');
        unset($where);
        if ($disabled == 1) {
            $this->ajaxReturn(V(0, '您的账号已被禁用！'));
        }
        $password = I('post.password', '');

        // 获取security_code, 重新组织密码
        $pre_code = substr($password, 0, 2);
        $end_code = substr($password, -2);
        $security_code = $pre_code . $end_code;
        if ($security_code != session('security_code')) {
            //记录日志
            D('Basic/LoginHistory')->addMemberLoginLog(0, $username, 0, 1, '非法登录'.'(PC)');
            $this->ajaxReturn(V(0, '非法操作, 您的IP已记录'));
        }

        $password = substr($password, 2, -2);
        $loginInfo = $member->login($username, $password, 'user');
        if( $loginInfo['status'] == 1 ){ //登录成功
            /* 存入session */
            $this->autoSession($loginInfo['data']);
            //记录日志
            D('Basic/LoginHistory')->addMemberLoginLog(0, '', $loginInfo['data']['id'], 0, '登录成功'.'(PC)');
            $this->ajaxReturn(V(1, '登录成功'));
        } else {
            //记录日志
            D('Basic/LoginHistory')->addMemberLoginLog(0, $username, 0, 1, $loginInfo['info'].'(PC)');
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
    	session('user_auth', $auth);
    }

    //密码找回功能
    public function getForgetPassword(){
        $mobile = I('get.mobile');
        if(!isMobile($mobile)){
            $this->error('请输入有效的手机号码','',true);
            exit();
        }
        $code= I('get.mobilecode','');
        if(empty($code)){
            $this->error('验证码不能为空！');
            exit();
        }
        $financial_code= I('get.financial_code','');
        if(empty($financial_code)){
            $this->error('财务编号不能为空！');
            exit();
        }
        $m_count = M('Member')->where(array('phone'=>$mobile,'financial_code'=>$financial_code))->count();
        if($m_count == 0){
            $this->error('抱歉，该用户的财务编号不正确！','',true);
        }

        $re_code = $this->checkPhoneVerify($code,$mobile);
        $re_code = true;
        if($re_code== false){
            $info = array('info'=>'手机验证码输入错误','status'=>0,'url'=>'');
            $this->ajaxReturn($info);
        }else{
            $this->success('验证成功',U('Public/resetPassword'),true);
        }
    }

    /**
     * 验证 短信验证是否正确
     * @param unknown_type $code
     * @param unknown_type $mobile
     */
    public function checkPhoneVerify($code,$mobile){
        $where['code'] = $code;
        $where['mobile'] = $mobile;
        $where['type'] = 0;
        $where['status'] = 2;
        $where['end_time'] = array('EGT', NOW_TIME);
        $shortMessage = M('MobileSendCode');
        $count = $shortMessage->where($where)->count();
        if ($count <= 0) {
            return false;
        }else{
            return true;
        }
    }

    //找回密码验证该手机号是否存在
    public function findMobile(){
        $model = M('MobileSendCode');
        $mobile= I ('get.mobile');
        session('check_mobile',$mobile);
        if (empty($mobile) || !isMobile($mobile)){
            $this->error('请输入有效的手机号码','',true);
            exit;
        }
        $userInfo = M('Member')->field('phone,status,disabled,post_status')->where(array('phone'=>$mobile))->find();
        if($userInfo['status']==1){
            $this->error('抱歉，该手机号已被删除，无法找回！','',true);
        }
        if($userInfo['disabled']==1){
            $this->error('抱歉，该手机号已被禁用，无法找回！','',true);
        }
        if($userInfo['post_status']>2){
            $this->error('抱歉，该手机号用户岗位状态不合法，无法找回！','',true);
        }
        $where['phone'] = $mobile;
        //验证手机号码是否已经验证
        if(!$userInfo['phone']){
            $this->error('抱歉,该手机号不存在','',true);
        }

        $where['create_time'] = array('EGT', NOW_TIME - 59);
        $where['status'] = 2;
        // 查询是不是已经发送过了
        $count = $model->where($where)->count();
        if ($count > 0 ) {
            $this->error('验证码1分钟内不能重复发送','',true);
        }else{
            $today = strtotime(date('Y-m-d'));
            $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
            $dwhere['create_time'] = array('between',array($today,$tomorrow));
            $count = $model->where($dwhere)->count();
            /*if($count>5){
                $info = array('info'=>'您的手机号今天已经发了5条短信了，再发就要被封号了','status'=>1,'url'=>'');
                $this->ajaxReturn($info);
                exit;
            }*/
            $code = $this->randCodes(4,1);
            $sms = new \Common\Tools\SmsMeilian();
            $result = $sms->sendSMS($mobile, $code);
            if ($result === true) {
                $msg = '验证码已发送, 2个小时内有效';
                $data['status'] = 0;
                $data['send_response_msg'] = '发送成功';
                $data['code'] = $code;
                $data['mobile'] = $mobile;
                $data['create_time'] = NOW_TIME;
                $data['type'] = 0;
                $data['valid_time'] = NOW_TIME + 2 * 3600; // 验证码有效期
                $model->data($data)->add();
            } else {
                $msg =  '验证码发送失败';
                $data['status'] = 1;
                $data['send_response_msg'] = '发送失败';
            }
            if ($result === true) {
                $this->success($msg,'',true);
            }else{
                $this->error($msg,'',true);
            }
        }
    }

    /**
     +----------------------------------------------------------
     * 生成随机字符串
     +----------------------------------------------------------
     * @param int       $length  要生成的随机字符串长度
     * @param string    $type    随机码类型：0，数字+大小写字母；1，数字；2，小写字母；3，大写字母；4，特殊字符；-1，数字+大小写字母+特殊字符
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    function randCodes($length = 5, $type = 0) {
        $arr = array(1 => "0123456789", 2 => "abcdefghijklmnopqrstuvwxyz", 3 => "ABCDEFGHIJKLMNOPQRSTUVWXYZ", 4 => "~@#$%^&*(){}[]|");
        if ($type == 0) {
            array_pop($arr);
            $string = implode("", $arr);
        } elseif ($type == "-1") {
            $string = implode("", $arr);
        } else {
            $string = $arr[$type];
        }
        $count = strlen($string) - 1;
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $string[rand(0, $count)];
        }
        return $code;
    }

    //找回密码跳转重设页面
    public function resetPassword(){
        $realmobile=session('check_mobile');
        if(empty($realmobile)){
            redirect(U('Public/forget_password'));
            exit();
        }
        $this->display('Public/password_reset');
    }

    public function forget_password(){
        $this->display('forget_password');
    }

    //设置新密码
    public function modifyPassword(){
        $realmobile=session('check_mobile');
        if(empty($realmobile)){
            redirect(U('Public/forget_password'));
            exit();
        }
        $password= I ('post.password');
        $cpassword = I('post.cpassword');
        if($password != $cpassword){
            $this->error('两次密码不一致','',true);
        }
        if(strlen($password) >41 || strlen($password) < 36){
            $this->error('密码长度必须在36-41位','',true);
        }
        $newpassword=md5($password);
        $result= M('Member')->where(array('phone' => $realmobile))->setField('password', $newpassword);
       if ($result !== false) {
            //$datainfo=M ('MobileSendCode')->where(array('mobile'=>$realmobile,'type'=>0))->delete();
            session('check_mobile','');
            $this->success('密码修改成功,正在跳转至登录页!',U('Public/login'),true);
        }else{
            $this->error('修改失败','',true);
        }
    }


}

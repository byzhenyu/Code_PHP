<?php
namespace Member\Controller;

/**
 * @author zhaojiping <QQ: 17620286>
 */
class PublicApiController extends \Think\Controller {

    protected function _initialize(){

        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

    }

    protected function apiReturn($status, $message='', $data=''){
        if ($status != 0 && $status != 1) {
            exit('参数调用错误 status');
        }

        if ($data != '' && C('APP_DATA_ENCODE') === true) {
            $data = json_encode($data); // 数组转为json字符串
            $aes = new \Common\Tools\Aes();
            $data = $aes->aes128cbcEncrypt($data); // 加密
        }

        if (is_null($data)) $data='';

        $this->ajaxReturn(V($status, $message, $data));
    }

    private function _decode(){
        $code = $_POST['code'];

        if ($code == '') {
            $this->ajaxReturn(V('0', '非法访问'));
        }

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
        }
    }

    /**
     * 用户登录
     */
    public function doLogin(){ 
        $this->_decode();

        //$this->apiReturn(1, 'test', array('username'=>'test', 'password'=>'5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'machine_code'=>'123456'));
        $machine_code = I('post.machine_code', '');
        if ($machine_code == '') {
            $this->apiReturn(0, '参数错误, 请传递机器码!');
        }

        $member = D('Common/Member');
        $data = $member->create(I('post.'), 4);
        $username = I('post.username', '');
        if (!$data) {
            D('Basic/LoginHistory')->addMemberLoginLog(0, $username, 0, 1, $member->getError().'(手机)');
            $this->apiReturn(0, $member->getError());
        }

        $password = I('post.password', '');

        $loginInfo = $member->login($username, $password, 'user');
        if( $loginInfo['status'] == 1 ){ //登录成功
            // 判断机器码是否一致, 开发阶段不判断机器码
            // 1.首先第一次登录记录机器码
            /*if (empty($loginInfo['data']['machine_code'])) {
                 M('Member')->where('id='.$loginInfo['data']['id'])->data(array('machine_code'=>$machine_code))->save();
                 $loginInfo['data']['machine_code'] = $machine_code;
            }
            if ($machine_code != $loginInfo['data']['machine_code']) {
                $this->apiReturn(0, '此手机禁止登录, 请与管理员联系');
            }*/
            // 生成token值并保存, 开发阶段不加token值
            // 此处临时需要固定死的token
            $loginInfo['data']['token'] = M('Member')->where('id='.$loginInfo['data']['id'])->getField('token');
            if (empty($loginInfo['data']['token'])) {
                $loginInfo['data']['token'] = $this->_createTokenAndSave($loginInfo['data']);
            }
            //$loginInfo['data']['token'] = $this->_createTokenAndSave($loginInfo['data']);
            unset($loginInfo['data']['password']);
            unset($loginInfo['data']['isAdmin']);
            unset($loginInfo['data']['isUser']);
            unset($loginInfo['data']['machine_code']);
            // 返回用户职称
            $loginInfo['data']['is_manager'] = (int)!is_member($loginInfo['data']['position_id']);

            if ($loginInfo['data']['birthday'] > 0) {
                $loginInfo['data']['birthday'] = time_format($loginInfo['data']['birthday'], 'Y-m-d');
            }
            $loginInfo['data']['photo_path_thumb_120'] = thumb($loginInfo['data']['photo_path']);
            $loginInfo['data']['photo_path_thumb_220'] = thumb($loginInfo['data']['photo_path'], 220, 220);
            // 返回部门经纬度
            $member = D('Basic/Member')->getMemberInfoById($loginInfo['data']['id'], 'area_id');
            $area = D('Basic/Area')->getAreaInfoById($member['area_id']);
            $loginInfo['data']['longitude']  = $area['longitude'];    // 经度
            $loginInfo['data']['latitude']  = $area['latitude'];      // 纬度
            // 返回Banner
            $loginInfo['data']['bannerList'] = $this->_bannerList();
            // 返回nav
            $loginInfo['data']['navList'] = $this->_navList();
            // 返回行动轨迹接口调用时间
            $loginInfo['data']['action_track_time'] = C('ACTION_TRACK_TIME');
            $attendance = C('ATTENDANCE_TIME_SETTING');
            $attendance = explode('-', $attendance);
            // 返回上班时间
            $loginInfo['data']['businessTime'] = $attendance[0];
            // 返回下班时间
            $loginInfo['data']['closeTime'] = $attendance[1]; 
            //记录日志
            D('Basic/LoginHistory')->addMemberLoginLog(0, '', $loginInfo['data']['id'], 0, '登录成功'.'(手机)');
            $this->apiReturn(1, '登录成功', $loginInfo['data'], false);
        } else {
            //记录日志
            D('Basic/LoginHistory')->addMemberLoginLog(0, $username, 0, 1, $loginInfo['info'].'(手机)');
            $this->apiReturn(0, $loginInfo['info']);
        }
    }

    /**
     * 生成token值, 并保存到数据库
     * @param array userInfo 用户信息
     * @return string token值
     */
    private function _createTokenAndSave($userInfo){
        $token = randNumber(18); // 18位纯数字
        $where['id'] = $userInfo['id'];
        $data['token'] = $token;
        M('Member')->where($where)->data($data)->save();
        return $token;
    }

    // 返回app首页轮播图数据
    private function _bannerList(){
        $list = M('Banner')
                    ->where(array('type' => 1, 'status' => 0, 'disabled' => 0))
                    ->field('id, img, url, open_type, article_id')
                    ->order('sort asc')
                    ->select();
        foreach ($list as $key => $value) {
            $list[$key]['img'] = thumb($value['img'], 1200, 600);
        }
        return $list;
    }

    // 返回app导航栏数据
    private function _navList(){
        $list = M('Nav')
                    ->where(array('type' => 0, 'status' => 0, 'disabled' => 0))
                    ->field('id, title, img, url')
                    ->order('sort asc')
                    ->select();
        return $list;
    }

    // 返回app启动页和登录背景图片
    public function startPicture(){
        $where['status'] = 0;
        $where['disabled'] = 0;
        $where['is_default'] = 0;
        $banner = M('Banner');
        // 启动页
        $startPicture = $banner->where($where)->where(array('type' => 0))->getField('img');
        // 登录背景图
        $loginBgPicture = $banner->where($where)->where(array('type' => 2))->getField('img');
        $data = array(
                'startPicture' => $startPicture,
                'loginBgPicture' => $loginBgPicture
            );

        $this->apiReturn(1, '返回信息', $data);
    }

    /**
     * 获取验证码
     */
    public function getVerify(){
        $mobile = I('mobile');
        if (empty($mobile) || !isMobile($mobile)){
            $this->apiReturn(0, '请输入有效的手机号码');
        }
        $memberInfo = M('Member')->where(array('phone' => $mobile))->field('id')->find();
        if (empty($memberInfo)) {
            $this->apiReturn(0, '不存在该用户！');
        }
        // 验证今天是否提交申请
        if ($this->_isAutoType($mobile)) {
            $this->apiReturn(0, '本手机号提交的申请正在审核中，请勿重复提交');
        }
        $model = M('MobileSendCode');
        $where['mobile'] = $mobile;
        $where['create_time'] = array('EGT', NOW_TIME - 59);
        // 查询是不是已经发送过了
        $count = $model->where($where)->count();
        if ($count > 0 ) {
            $this->apiReturn(0, '验证码1分钟内不能重复发送');
        }else{
            $today = strtotime(date('Y-m-d'));
            $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
            $dwhere['mobile'] = $mobile;
            $dwhere['create_time'] = array('between',array($today,$tomorrow));
            $count = $model->where($dwhere)->count();
            if($count>5){
                $this->apiReturn(0, '您的手机号今天已经发了5条短信了，再发就要被封号了');
            }
            $code = randNumber(4);
            $sms = new \Common\Tools\SmsMeilian();
            $result = $sms->sendSMS($mobile, '鲁南制药信息部提示您：您本次验证码是:'.$code.',请勿转告他人！');
            if ($result == true) {
                $msg = '验证码以发送到您的手机，有效时间2个小时！';
                $data['status'] = 2;
                $apiStatus = 1;
            } else {
                $msg = '验证码以发送失败！';
                $data['status'] = 1;
                $apiStatus = 0;
            }
            $data['mobile'] = $mobile;
            $data['type'] = 1;
            $data['code'] = $code;
            $data['create_time'] = time();
            $data['valid_time'] = time() + 2 * 3600;       // 验证码有效期
            $data['send_response_msg'] = $msg;
            $model->data($data)->add();
            $this->apiReturn($apiStatus, $msg);
        }
    }


    // 更换登录信息（存在即更换手机号和设备，只更新手机号，只更新设备）
    public function changeLoginInfo(){
        if (IS_POST) {
            $model = M('Member');
            $machine_code = I('machine_code');
            $type = I('type', 0, 'intval');
            $mobile = I('mobile');
            if (empty($machine_code)) {
                $this->apiReturn(0, '机器码参数错误！');
            }
            if ($type == 0) {
                $this->apiReturn(0, 'type参数错误!');
            }
            $code = I('code', '', 'htmlspecialchars');
            if ($code == '') {
                $this->apiReturn(0, '验证码错误！');
            }
            if (empty($mobile) || !isMobile($mobile)){
                $this->apiReturn(0, '请输入有效的手机号码');
            }
            // 验证验证码
            $re_code = $this->checkPhoneVerify($code,$mobile);
            if (!$re_code) {
                $this->apiReturn(0, '验证码错误！');
            }
            $memberInfo = $model->where(array('phone' => $mobile))->field('id, machine_code')->find();
            if (empty($memberInfo)) {
                $this->apiReturn(0, '不存在该用户！');
            }
            // 更换手机号（存在只更新手机号，同时更新手机号和设备）
            if ($type == 1) {
                $newMobile = I('newMobile');
                if (empty($newMobile) || !isMobile($newMobile)){
                    $this->apiReturn(0, '请输入有效的手机号码');
                }
                if ($newMobile == $mobile){
                    $this->apiReturn(0, '两次输入的手机号一样');
                }
                $result = $this->_saveChangInfo($mobile, $newMobile, $machine_code, $type);
            }
            // 更换设备
            if ($type == 2) {
                if ($machine_code == $memberInfo['machine_code']) {
                    $this->apiReturn(0, '使用设备一样，没有更换。');
                }
                $result = $this->_saveChangInfo($mobile, '', $machine_code, $type);
            }
            if ($result) {
                $this->apiReturn(1, '申请已经提交，等待管理员审核');
            } else{
                $this->apiReturn(0, '更换申请失败，请联系管理员');
            }
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
        $where['status'] = 2;
        $where['valid_time'] = array('EGT', time());
        $shortMessage = M('MobileSendCode');
        // 查询最新验证对应的主键id
        $id = $shortMessage->where(array('mobile' => $mobile))->max('id');
        $where['id'] = $id;
        if ($shortMessage->where($where)->find()) {
            return true;
        } else{
            return false;
        }
    }

    /**
     * 保存信息
     * @param unknown_type $mobile
     * @param unknown_type $newMobile
     * @param srting $machine_code
     * @param int $type
     */
    private function _saveChangInfo($mobile, $newMobile=0, $machine_code, $type){
        $data = array(
            'mobile'       => $mobile,
            'new_mobile'   => $newMobile,
            'machine_code' => $machine_code,
            'add_time'     => time(),
            'type'         => $type,
        );
        $result = M('ChangeMobileApply')->add($data);
        if ($result) {
            // 更新到总的申请记录表
            $member_id = M('Member')->where(array('phone' => $mobile))->getField('id');
            $apply_result = D('Common/ApplyHistory')->addApply('ChangeMobileApply', $result, $member_id);
            if ($apply_result['status'] == 0) {
                return false;
            }
            return true;
        } else{
            return false;
        }
    }

    /**
     * 验证 提交是否已经提交并且处理了
     * @param unknown_type $mobile
     */
    private function _isAutoType($mobile){
        $where['mobile'] = $mobile;
        $where['auto_type'] = 0;
        if(M('ChangeMobileApply')->where($where)->find()){
            return true;
        } else {
            return false;
        }
    }

    //找回密码验证 发送短信
    public function sendSms(){
        $this->_decode();
        $model = M('MobileSendCode');
        $mobile= I ('mobile');
        if (empty($mobile) || !isMobile($mobile)){
            $this->error('请输入有效的手机号码','',true);
            exit;
        }
        $userInfo = M('Member')->field('phone,status,disabled,post_status')->where(array('phone'=>$mobile))->find();
        if($userInfo['status']==1){
            $this->apiReturn(0, '该手机号已被删除，无法找回！', '');
        }
        if($userInfo['disabled']==1){
            $this->apiReturn(0, '该手机号已被禁用，无法找回！', '');
        }
        if($userInfo['post_status']>2){
            $this->apiReturn(0, '该手机号用户岗位状态不合法，无法找回！', '');
        }
        $where['phone'] = $mobile;
        //验证手机号码是否已经验证
        if(!$userInfo['phone']){
            $this->apiReturn(0, '该手机号不存在', '');
        }

        $where['create_time'] = array('EGT', NOW_TIME - 59);
        $where['status'] = 2;
        // 查询是不是已经发送过了
        $count = $model->where($where)->count();
        if ($count > 0 ) {
            $this->apiReturn(0, '验证码1分钟内不能重复发送', '');
        }else{
            $today = strtotime(date('Y-m-d'));
            $tomorrow = strtotime(date('Y-m-d',strtotime('+1 day')));
            $dwhere['create_time'] = array('between',array($today,$tomorrow));
            $count = $model->where($dwhere)->count();
            if($count>5){
                $info = array('info'=>'您的手机号今天已经发了5条短信了，再发就要被封号了','status'=>1,'url'=>'');
                $this->ajaxReturn($info);
                exit;
            }
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
                $this->apiReturn(1, $msg, '');
            }else{
                $this->apiReturn(0, $msg, '');
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
    private function randCodes($length = 5, $type = 0) {
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

    //密码找回功能--提交手机验证码和财务编号
    public function validateInfoByChangePassword(){
        $this->_decode();
        $mobile = I('mobile');
        if(!isMobile($mobile)){
            $this->apiReturn(0, '请输入有效的手机号码', '');
        }
        $code= I('mobilecode','');
        if(empty($code)){
            $this->apiReturn(0, '验证码不能为空！', '');
        }
        $financial_code= I('financial_code','');
        if(empty($financial_code)){
            $this->apiReturn(0, '财务编号不能为空！', '');
        }
        $m_count = M('Member')->where(array('phone'=>$mobile,'financial_code'=>$financial_code))->count();
        if($m_count == 0){
            $this->apiReturn(0, '该用户的财务编号不正确！', '');
        }

        //$re_code = $this->checkMissPhoneVerify($code,$mobile);
        $re_code = true;
        if($re_code== false){
            $this->apiReturn(0, '手机验证码输入错误', '');
        } else {
            $this->apiReturn(1, '验证通过', '');
        }
    }
    /**
     * 验证 短信验证是否正确
     * @param unknown_type $code
     * @param unknown_type $mobile
     */
    private function checkMissPhoneVerify($code,$mobile){
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

    //设置新密码
    public function resetPassword(){
        $this->_decode();
        $password= I ('password');
        $cpassword = I('cpassword');
        $mobile = I('mobile');
        $mobilecode = I('mobilecode');
        //$re_code = $this->checkMissPhoneVerify($mobilecode,$mobile);
        $re_code = true;
        if($re_code== false){
            $this->apiReturn(0, '手机验证码输入错误', '');
        }
        if($password != $cpassword){
            $this->apiReturn(0, '两次密码不一致', '');
        }
        if(strlen($password) >41 || strlen($password) < 36){
            $this->apiReturn(0, '密码长度必须在36-41位', '');
        }
        $newpassword=md5($password);
        $result= M('Member')->where(array('phone' => $mobile))->setField('password', $newpassword);
       if ($result !== false) {
            //$datainfo=M ('MobileSendCode')->where(array('mobile'=>$mobile,'code'=>$mobilecode))->delete();
            $this->apiReturn(1, '密码修改成功', '');
        }else{
            $this->apiReturn(1, '修改失败', '');
        }
    }


    
}

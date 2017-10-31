<?php

namespace Member\Controller;

/**
 * @author songwei <QQ: 837686983>
 */
class MemberController extends \Common\Controller\CommonWebController {

    public function index(){
        $member_info = D('Member/Member')->getMemberInfo(UID);
        $this->assign('info',$member_info);
        $this->display();
    }

    //修改个人信息页面信息展示
    public function editMember(){
        $member_info = D('Member/Member')->getMemberInfo();

        

        $this->assign('info',$member_info);
        $this->display("editMember");
    }

    public function saveMemberInfo(){
        $info = I('post.');
        $member = D('Member/Member');
        $birthday = time();
        $info['birthday'] = strtotime($info['birthday']);
        if($info['birthday']>=$birthday){
            $this->ajaxReturn(V(0,'生日不合法'));
        }

        if($info){
            $member->where(array('id'=>UID))->save($info);
            $this->ajaxReturn(V(1,"修改成功"));
        }else{
            $this->ajaxReturn(V(0,$member->getError()));
        }

    }


    // 展示修改密码页面
    public function modifyPassword(){
    	$this->display('modifyPassword');
    }

    // 修改登录密码

    public function doModifyLoginPassword(){
        // 对接收数据进行验证
        $login_password = I('post.login_password','');
        $new_login_password = I('post.new_login_password','');
        $rules = array(
                array('login_password', '36,40', '登录密码长度长度有误', 1, 'length', 4),
                array('new_login_password', '36,40', '新登录密码长度不合法', 2, 'length', 4)
            );

        $data['login_password'] = $login_password;
        $data['new_login_password'] = $new_login_password;
        $Member = M("Member");
        $res = $Member->validate($rules)->create($data, 4);
        if ($res !==false) {
            $result = D('Member/Member')->modifyLoginPassword($login_password,$new_login_password);
            if($result==0){
                $this->ajaxReturn(V('0','原密码输入不正确'));
            }
            if($result==1){
                $this->ajaxReturn(V('1','密码修改成功'));
            }
        }else{
            $this->ajaxReturn(V('0',$Member->getError()));
        }

    }


    // 修改验证密码
    public function doModifyPayPassword(){

        // 对接收数据进行验证
        $pay_password = I('post.pay_password','');
        $new_pay_password = I('post.new_pay_password','');

        $rules = array(
            array('pay_password', '36,40', '验证密码长度长度有误', 1, 'length', 4),
            array('new_pay_password', '36,40', '新验证密码长度不合法', 2, 'length', 4),
        );

        $data['pay_password'] = $pay_password;
        $data['new_pay_password'] = $new_pay_password;

        $Member = M("Member");
        $res = $Member->validate($rules)->create($data, 4);

        if($res!==false){
            $result = D('Member/Member')->modifyPayPassword($pay_password,$new_pay_password);

            if($result==0){
                $this->ajaxReturn(V('0','原密码输入不正确'));
            }
            if($result==1){
                $this->ajaxReturn(V('1','密码修改成功'));
            }
        }else{
            $this->ajaxReturn(V('0',$Member->getError()));
        }

    }

    public function monthly(){
        $this->display();
    }

   // 删除图片
    public function delFile(){
      $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
       $this->_uploadImg();  //调用父类的方法
    }
}
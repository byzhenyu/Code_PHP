<?php

namespace Member\Controller;

/**
 * @author songwei <QQ: 837686983>
 */
class MemberApiController extends \Common\Controller\CommonApiController {

    public function getMemberInfo(){
        $memberInfo = D('Member/Member')->getMemberInfo();
        $this->apiReturn(1,'用户基本信息',$memberInfo);
    }

    public function updataMemberInfo(){
        $updataInfo = D('Member/Member')->updataMemberInfo();
        if ($updataInfo['status'] == 1) {
            $memberInfo = D('Member/Member')->getMemberInfo();
            $this->apiReturn(1,'修改用户基本信息成功',$memberInfo);
        }else{
            $this->apiReturn(0, $updataInfo['info']);
        }
    }

    public function modifyLoginPassword(){
        $password = I('password','');//这里是从api端传过来的原密码，字段名称为password
        $new_password = I('new_password','');//这是从api端传过来的新密码，字段名称为new_password
        $rules = array(
            array('password','36,40','登录密码长度长度有误','1','length',4),
            array('new_password','36,40','新密码长度长度有误','1','length',4),
        );
        $data['password'] = $password;
        $data['new_password'] = $new_password;
        $res_info = M('Member')->validate($rules)->create($data,4);
        if($res_info){
            $res = D('Member/Member')->modifyLoginPassword($password,$new_password);
            if($res==0){
                $this->apiReturn(0,'原密码输入错误');
            }else{
                $this->apiReturn(1,'登录密码修改成功');
            }
        }else{
            $this->apiReturn(0,M('Member')->getError());
        }

    }

    public function modifyPayPassword(){
        $password = I('post.password','');//这里是从api端传过来的原密码，字段名称为password
        $new_password = I('post.new_password','');//这是从api端传过来的新密码，字段名称为new_password
        $rules = array(
            array('password','36,40','登录密码长度长度有误','1','length',4),
            array('new_password','36,40','新密码长度长度有误','1','length',4),
        );
        $data['password'] = $password;
        $data['new_password'] = $new_password;
        $res_info = M('Member')->validate($rules)->create($data,4);
        if($res_info){
            $res = D('Member/Member')->modifyPayPassword($password,$new_password);
            if($res==0){
                $this->apiReturn(0,'原密码输入错误');
            }else{
                $this->apiReturn(1,'验证密码修改成功');
            }
        }else{
            $this->apiReturn(0,M('Member')->getError());
        }

    }

    // 用户通迅录(好友列表)
    public function friendList(){
        $field = array('id','real_name','username','department_id','position_id','area_id','email','phone','sex','birthday','photo_path', 'emchat_username');
        // 获取用户的基本信息
        $memberInfo = D('Member/Member')->getMemberInfo();

        // 获取好友
        $where['isService'] = 1; // 不是客服
        if (is_member($memberInfo['position_id']) == true) { // 如果是业务员, 只显示自己同部门的同事
            $where['department_id'] = $memberInfo['department_id'];
        } else { // 如果是领导, 显示所有子部门(包含自己部门)的同事
            $where['department_id'] = array('in', getChildIds('UserDepartment', $memberInfo['department_id']));
        }

        $where['isUser'] = 0;
        $memberList = D('Member/Member')->getMemberList($where, $field);
        $colleagues = array(); // 同事列表
        $subManagers = array(); // 提取下级部门管理层
        foreach ($memberList as $key => $value) {
            $memberList[$key]['is_service'] = 0;
            if ($value['id'] == UID) { // 去掉自己
                continue;
            }
            if (is_member($value['position_id']) == true) {
                $colleagues[] = $memberList[$key];
            } else {
                $subManagers[] = $memberList[$key];
            }
        }
        // 提取上级管理层
        $departmentParentIds = getParentIds('UserDepartment', $memberInfo['department_id']);
        $positionParentIds = getParentIds('Position', $memberInfo['position_id']);
        array_shift($positionParentIds);//排除本职务
        unset($where);
        $where['department_id'] = array('in', $departmentParentIds);
        $where['position_id'] = array('in', $positionParentIds);
        $where['isUser'] = 0;
        $parentManagers = D('Member/Member')->getMemberList($where, $field);
        foreach ($parentManagers as $key => $value) {
            $parentManagers[$key]['is_service'] = 0;
        }
        if (is_member($memberInfo['position_id']) == true) { // 如果是业务员 排除掉直属的第一领导
            array_shift($subManagers);
        }
        if ($subManagers && $parentManagers) {
            $data['managers'] = array_merge($subManagers, $parentManagers);
        } else {
            if ($subManagers) {
                $data['managers'] = $subManagers;
            } else if ($parentManagers) {
                $data['managers'] = $parentManagers;
            } else {
                $data['managers'] = array();
            }
        }

        // 获取客服列表
        unset($where);
        $where['isService'] = 0;
        $serviceList = D('Member/Member')->getMemberList($where, $field);
        foreach ($serviceList as $key => $value) {
            $serviceList[$key]['is_service'] = 1;
        }
        $data['colleagues'] = $colleagues;
        $data['services'] = $serviceList;

        //按照用户名首字母及逆行排序
        $sort = array(  
            'direction' => 'SORT_ASC', //排序顺序标志 SORT_DESC 降序；SORT_ASC 升序  
            'field'     => 'username',       //排序字段  
        );  
        $arrSort = array();  
        foreach($data['colleagues'] AS $uniqid => $row){  
            foreach($row AS $key=>$value){  
                $arrSort[$key][$uniqid] = $value;  
            }  
        }
        if($sort['direction']){  
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data['colleagues']);
        }

        $arrSort = array(); 
        foreach($data['services'] AS $uniqid => $row){  
            foreach($row AS $key=>$value){  
                $arrSort[$key][$uniqid] = $value;  
            }  
        }
        if($sort['direction']){  
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data['services']);
        }

        $arrSort = array(); 
        foreach($data['managers'] AS $uniqid => $row){  
            foreach($row AS $key=>$value){  
                $arrSort[$key][$uniqid] = $value;  
            }  
        }
        if($sort['direction']){  
            array_multisort($arrSort[$sort['field']], constant($sort['direction']), $data['managers']);
        }

        $this->apiReturn(1, '通讯录', $data);
    }

    /**
     * 根据环信号获取用户信息
     */
    public function getMemberInfoByEmchatId() {
        $emchat_username = I('emchat_username', '');
        if ($emchat_username == '') {
            $this->apiReturn(0, '参数异常', '');
        }
        $memberInfo = D('Member/Member')->getMemberInfoByEmchatId($emchat_username);
        $this->apiReturn(1, '用户详细信息', $memberInfo);
    }

    /**
     * 用户头像修改功能
     * @author liuyang <[594353482]>
     */
    public function appUploadImg() {
        $img = app_upload_img('photo', '');
        if ($img === 0) {
            $this->apiReturn(0, '上传头像失败');
        } else if ($img === -1){
            $this->apiReturn(0, '上传头像失败');
        } else {
            //保存头像
            $result = D('Member/Member')->saveHeadPortrait($img);
        }
        //返回头型数据
        $data['original_path'] = $img;
        $data['small_path'] = thumb($img, 120, 120);
        $data['medium_path'] = thumb($img, 240, 240);
        $this->apiReturn($result['status'], $result['info'], $data);
    }

}
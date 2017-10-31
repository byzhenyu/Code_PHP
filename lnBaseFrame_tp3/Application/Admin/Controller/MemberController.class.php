<?php
namespace Admin\Controller;

/**
 *  后台用户控制器
 * @author zhaojiping <QQ: 17620286>
 *
 */
class MemberController extends AdminCommonController {

    /**
     * 管理员列表
     */
    public function index(){
        $map['status'] = 0;
        $map['isAdmin|isService'] = 0;
        $role_id = I('role_id', 0, 'intval');
        if ($role_id > 0) {
            $map['member_role.role_id'] = array('eq', $role_id);
        }
        $post_status = I('post_status', 0, 'intval');
        if ($post_status > 0) {
            $map['post_status'] = array('eq', $post_status);
        }
        $aMemberModel = D('Admin/Member');
        $result = $aMemberModel->getMemberByPage($map);
        // 查询所有的角色
        $roleList = D('Role')->where('status='. 0)->field('id, name')->select();
        $this->assign('roleList', $roleList);
        $this->assign('role_id', I('role_id', ''));
        $this->assign('post_status', I('post_status', ''));
       	//设置分页变量
       	$this->assign('list', $result['list']);
       	$this->assign('page', $result['page']);
        $this->display();
    }

    //批量上传用户数据(excel格式)
    public function addMoreMember(){
        if(IS_POST){
            $excelController = A('Excel');
            $excelController->uploadExcel();
        }
        $this->display('add_more_member');
    }

    // 上传附件
    public function uploadField(){
        $this->_uploadField();  //调用父类的方法
    }

    public function searchByPinyin(){
        $where['disabled'] = 0;
        $where['status'] = 0;
        $where['isUser'] = 0;
        getDataByKeywords('Member', 'real_name',$where);
    }

    /**
     * 前台用户列表
     */
    public function userList(){
        $map['status'] = 0;
        $map['isUser'] = 0;
        $role_id = I('role_id', 0, 'intval');
        if ($role_id > 0) {
            $map['member_role.role_id'] = array('eq', $role_id);
        }
        $post_status = I('post_status', 0, 'intval');
        if ($post_status > 0) {
            $map['post_status'] = array('eq', $post_status);
        }
        $aMemberModel = D('Admin/Member');
        $result = $aMemberModel->getMemberByPage($map);

        // 查询所有的角色
        $roleList = D('Role')->where('status='. 0)->field('id, name')->select();
        $this->assign('roleList', $roleList);
        $this->assign('role_id', I('role_id', ''));
        $this->assign('post_status', I('post_status', ''));
        //设置分页变量
        $this->assign('list', $result['list']);
        $this->assign('page', $result['page']);
        $this->display('index');
    }

    /**
     * 回收站用户列表
     */
    public function recycleList(){
        $map['status'] = 1;
        $result = D('Member')->getMemberByPage($map);

        //设置分页变量
        $this->assign('list', $result['list']);
        $this->assign('page', $result['page']);
        $this->display('recycleList');
    }

    /**
     * create by yuanyulin
     * 添加或修改用户信息
     */
    public function edit(){
        $id = I('id', 0, 'intval');//接收到的用户id
        if (IS_POST) {
            //需要插入member表的数据
            $member = I('post.member','');
            $member['birthday'] = strtotime($member['birthday']);
//            var_dump($member);die;
            //需要插入member_role表的数据
            $memberRole = I('post.memberRole','');
            //实例化模型
            $memberModel = D('Member');
            $memberRoleModel = D('MemberRole');

            //用户分组数据
            $member_group_id = I('member_group_id','');

            if ($id > 0) {//更新
                if ($memberModel->create($member, 2) !== false) {
                    if ($memberModel->where('id='. $id)->save() === false) $this->ajaxReturn(V(0, $memberModel->getError()));
                } else {
                    $this->ajaxReturn(V(0, $memberModel->getError()));
                }
            } else {//插入
                if ($memberModel->create($member, 1) !== false) {
                    //校验用户分组
                    if ($member_group_id == '') {
                        $this->ajaxReturn(V(0, '请选择用户分组'));
                    }
                    $id = $memberModel->add();//添加的时候获取添加完成功能后的id
                    if ($id === false) $this->ajaxReturn(V(0, $memberModel->getError()));
                } else {
                    $this->ajaxReturn(V(0, $memberModel->getError()));
                }
            }
            // 插入用户角色表
            $result = $memberRoleModel->memberRoleStatus($memberRole, $id);
            if ($result['status'] == 0) {
                $this->ajaxReturn(V(0, $result['info']));
            }
            //插入用户分组数据
            D('Admin/AuthGroup')->updateUserGroup($id, $member_group_id);

            $this->ajaxReturn(V(1, '保存成功'));
        } else {
            $UserDepartmentModel = D('UserDepartment');
            $AreaModel = D('Area');
            if ($id > 0) {
                $info = M('Member')->field(true)->find($id);
            } else {
                //处理默认不可登陆后台
                $info['isAdmin'] = 1;
            }
            // 查询所有的角色
            $roleList = D('Role')->where('status='. 0)->field('id, name')->select();
            // 查询用户拥有的权限
            $memberRoleData = M('MemberRole')->where('member_id='. $id)->field('role_id')->select();
            $memberRoleInfo = i_array_column($memberRoleData, 'role_id');
            //查询用户分组数据以及用户所属的分组
            $member_group = M('AuthGroupAccess')->field(true)->where('uid='. $id)->find();
            $info['member_group_id'] = $member_group['group_id'];
            $authGroupList = D('Admin/AuthGroup')->getAuthGroupList();
            $this->assign('authGroupList', $authGroupList);
            $this->assign('info', $info);
            
            $this->assign('roleList', $roleList);
            $memberRoleInfo = json_encode($memberRoleInfo);
            $this->assign('memberRoleInfo', $memberRoleInfo);
            $this->display();
        }
    }

    /**
     * 修改用户个人信息
     */
    public function adminSetting(){
        if (IS_POST) {
            $rules = array(
                array('real_name', '1,30', '真实姓名长度有误', 1, 'length', 2),
                array('password', '36,41', '登录密码长度不合法', 2, 'length', 2), // 不为空时验证
                array('email', 'isEmail', '邮箱地址不合法', 2, 'function', 2), // 不为空时验证
                array('sex', array(0,1,2), '非法数据, 性别字段', 1, 'in'),
                array('birthday', 'validateDate', '用户生日不是一个合法的日期', 2, 'function', 2),
                array('photo_path', '0,255', '用户头像长度有误', 1, 'length', 2),
                array('remark', '0,150', '用户签名长度过长', 1, 'length', 2),
            );
            $member = M("Member");
            $data = $member->validate($rules)->create(I('post.'), 2);
            if (!$data){
                $this->ajaxReturn(V(0, $member->getError()));
            } else {
                if ($data['password'] == '') {
                    unset($data['password']);
                } else {
                    $data['password'] = md5($data['password']);
                }
                if(empty($data['birthday'])){
                    unset($data['birthday']);
                }else{
                    $data['birthday'] = strtotime($data['birthday']);
                }
                $saveData['real_name']  = $data['real_name'];
                $saveData['password']   = $data['password'];
                $saveData['email']      = $data['email'];
                $saveData['sex']        = $data['sex'];
                $saveData['birthday']   = $data['birthday'];
                $saveData['photo_path'] = $data['photo_path'];
                $saveData['remark']     = $data['remark'];

                $member->where('id='. UID)->data($saveData)->save();
                $this->ajaxReturn(V(1,'保存成功! 设置的内容要在重新登录后才会生效'));
            }

        } else {
            $member = M('Member')->field('id,real_name,email,sex,birthday,photo_path,remark')->where(array('id' => UID))->find();
            $this->info = $member;
            $this->display('adminSetting');
        }
    }

    // 放入回收站
    public function recycle(){
        $id = I('id', 0);
        if ($id == 1) {
            $this->ajaxReturn(V(0, '超级管理员不能删除'));
        } else {
            $ids = explode(',', $id);
            if (in_array(1, $ids)) {
                $this->ajaxReturn(V(0, '超级管理员不能删除'));
            }
            $this->_recycle('Member');  //调用父类的方法
        }

    }

    // 从回收站还原
    public function restore(){
        $this->_restore('Member');  //调用父类的方法
    }

    // 改变可用状态
    public function changeDisabled(){
        $id = I('id', 0);
        if ($id == 1 ) {
            $this->ajaxReturn(V(0, '超级管理员不能禁用'));
        } else {
            $this->_changeDisabled('Member');  //调用父类的方法
        }

    }

    // 删除图片
    public function delFile(){
        $this->_delFile();  //调用父类的方法
    }

    // 上传图片
    public function uploadImg(){
        $this->_uploadImg();  //调用父类的方法
    }

    /**
     * 跳转至选择人员的页面(多选)
     * @author liuyang 594353482@qq.com
    */
    public function toUserSelect(){
        //获取查询需要的数据
        $positionTree = D('Position')->getPositionTree();
        $userDepartmentTree = D('UserDepartment')->getUserDepartmentTree();
        $userRoleList = D('Role')->getAllRoleList();

        //设置查询需要的数据
        $this->assign('positionTree', $positionTree);
        $this->assign('userDepartmentTree', $userDepartmentTree);
        $this->assign('userRoleList', $userRoleList);

        $this->display('userSelect');
    }

    /**
     * 选择人员弹窗确认用户数据
     * 根据用户选择的按钮确认用户选择的人员数据
     * @author liuyang 594353482@qq.com
     */
    public function ajaxUserDataByButtonType(){
        $map['status'] = 0;
        $map['member.isUser'] = array('eq', 0);
        $buttonType = I('get.buttonType', 0, 'intval');
        if ($buttonType==1) {
            //获取查询条件
            $department_id = I('get.department_id', 0, 'intval');
            $position_id = I('get.position_id', 0, 'intval');
            $role_id = I('get.role_id', 0, 'intval');
            if ($department_id!=null && $department_id!=0) {
                $map['member.department_id'] = array('in', $department_id.'');
            }
            if ($position_id!=null && $position_id!=0) {
                $map['member.position_id'] = array('in', $position_id.'');
            }
            if ($role_id!=null && $role_id!=0) {
                $map['member_role.role_id'] = array('in', $role_id.'');
            }
            //获取列表展示的用户数据
            $result = D('Member')->getMemberListNoPage($map, 'id,real_name');
        } else {
            $ids = I('get.ids', '');
            $map['member.id'] = array('in', $ids);
            $result = D('Member')->getMemberListNoPage($map, 'id,real_name');
        }
        $this->ajaxReturn($result);
    }

    /**
     * 根据部门获取用户数据
     * @author liuyang 594353482@qq.com
     */
    public function getUsersByDeptId(){
        $ids = I('ids','');
        $where['department_id'] = array('in' ,$ids);
        $where['status'] = 0;
        $field = 'id,real_name';
        $order = 'department_id desc,reg_time desc, id desc';
        $list = D('Member')->getMemberList($where ,$field ,$order);
        $this->ajaxReturn($list);
    }

    /**
     * 根据职务获取用户数据
     * @author liuyang 594353482@qq.com
     */
    public function getUsersByPostId(){
        $ids = I('ids','');
        $where['position_id'] = array('in' ,$ids);
        $where['status'] = 0;
        $field = 'id,real_name';
        $order = 'position_id desc,reg_time desc, id desc';
        $list = D('Member')->getMemberList($where ,$field ,$order);
        $this->ajaxReturn($list);
    }
    /**
     * ajax获取选择人员
     */
    public function ajaxMemberSelectData(){
        $map['status'] = 0;
        //获取查询条件
        $department_id = I('post.department_id', 0, 'intval');
        $position_id = I('post.position_id', 0, 'intval');
        $role_id = I('post.role_id', 0, 'intval');
        $keywords = I('post.keywords','');
        $page = trim(I('page', 1, 'intval'));
        if ($department_id!=null && $department_id!=0)
            $map['member.department_id'] = array('in', $department_id.'');
        if ($position_id!=null && $position_id!=0)
            $map['member.position_id'] = array('in', $position_id.'');
        if ($role_id!=null && $role_id!=0)
            $map['member_role.role_id'] = array('in', $role_id.'');
        //获取列表展示的用户数据
        $map['member.isUser'] = array('eq', 0);
        $result = D('Member')->ajaxMemberPage($map,$page);
        $this->ajaxReturn($result);
    }

    

}

<?php
namespace Admin\Controller;

/**
 * 后台登录历史记录控制器
 * @author liuyang <QQ:594353482>
 */
class LoginHistoryController extends AdminCommonController {


    /**
     * 后台用户登录历史
     * @return none
     */
    public function userIndex(){
        $name = trim(I('get.name', ''));
        if(!empty($name)){
            $where['member.real_name'] = array('like', "%$name%");
            $where['member.username'] = array('like', "%$name%");
            $where['member.phone'] = array('like', "%$name%");
            $where['login_history.failure_name'] = array('like', "%$name%");
            $where['_logic'] = 'OR';
            $map['_complex'] = $where;
        }
        $map['type'] = 0;
        $data = D('LoginHistory')->getLoginHistoryByPage($map);
        $memberModel = D('Member');
        foreach ($data['list'] as $key => $value) {
            if ($value['member_id'] > 0) {
                $data['list'][$key]['failure_name'] = $memberModel->getMemberNameById($value['member_id']);
            }
        }
        $this->assign('type', 0);
        $this->assign('page',$data['page']);
        $this->assign('list',$data['list']);
        $this->display('index');
    }

    /**
     * 后台管理员登录历史
     * @return none
     */
    public function managerIndex(){
        $name = trim(I('get.name', ''));
        if(!empty($name)){
            $where['member.real_name'] = array('like', "%$name%");
            $where['member.username'] = array('like', "%$name%");
            $where['member.phone'] = array('like', "%$name%");
            $where['login_history.failure_name'] = array('like', "%$name%");
            $where['_logic'] = 'OR';
            $map['_complex'] = $where;
        }
        $map['type'] = 1;
        $data = D('LoginHistory')->getLoginHistoryByPage($map);
        $memberModel = D('Member');
        foreach ($data['list'] as $key => $value) {
            if ($value['member_id'] > 0) {
                $data['list'][$key]['failure_name'] = $memberModel->getMemberNameById($value['member_id']);
            }
        }
        $this->assign('type', 1);
        $this->assign('page',$data['page']);
        $this->assign('list',$data['list']);
        $this->display('index');
    }

    public function exportExcel(){
        $type = I('type', '');
        if ($type != '') {
            $map['type'] = $type;
        }
        $name = trim(I('name', ''));
        if(!empty($name)){
            $where['member.real_name'] = array('like', "%$name%");
            $where['member.username'] = array('like', "%$name%");
            $where['member.phone'] = array('like', "%$name%");
            $where['login_history.failure_name'] = array('like', "%$name%");
            $where['_logic'] = 'OR';
            $map['_complex'] = $where;
        }
        $data = D('LoginHistory')->getLoginHistoryByExcel($map);
        if (empty($data)) {
            exit;
        }
        $memberModel = D('Member');
        foreach ($data as $key => $value) {
            if ($value['member_id'] > 0) {
                $data[$key]['failure_name'] = $memberModel->getMemberNameById($value['member_id']);
            }
            if ($value['status'] == 0) {
                $data[$key]['status'] = '成功';
            } else {
                $data[$key]['status'] = '失败';
            }
            $data[$key]['login_time'] = time_format($value['login_time']);
            unset($data[$key]['member_id']);
        }
        p($data);die;
        $title_array = array('ID','用户名','登录时间','登录IP','登录状态','登录备注');
        array_unshift($data, $title_array);
        $count = count($data);
        create_xls($data, '鲁南制药人员登录历史记录表', '鲁南制药人员登录历史记录表', '鲁南制药人员登录历史记录表',array('A','B','C','D','E','F'),$count);
    }

}

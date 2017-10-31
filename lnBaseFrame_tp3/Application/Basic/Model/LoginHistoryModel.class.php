<?php
namespace Basic\Model;
use Think\Model;
/**
 * 登陆历史
 * @author liuyang <QQ:594353482>
 *
 */
class LoginHistoryModel extends Model{
	protected $insertFields = array('member_id','failure_name','login_time','status','remark','type','login_ip');

	protected $updateFields = array('member_id','failure_name','login_time','status','remark','type','login_ip');

	protected function _before_insert(&$data,$option){
		
	}

	protected function _before_update(&$data,$option){

	}

	//新增加登录记录
    public function addMemberLoginLog($type = 0, $failure_name = '', $member_id = 0, $status = 0, $remark = ''){
        $data = array();
        $data['login_time'] = time();
        $data['member_id'] = $member_id;
        $data['status'] = $status;
        $data['remark'] = $remark;
        $data['type'] = $type;
        $data['login_ip'] = get_client_ip();
        $data['failure_name'] = $failure_name;
        $this->data($data)->add();
    }

}

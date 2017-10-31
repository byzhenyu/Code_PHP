<?php
namespace Admin\Model;
use Think\Model;
/**
 * 后台登陆历史
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

    public function getLoginHistoryByPage($where){
        $count = $this->alias('login_history')
        	->join('__MEMBER__ as member on member.id = login_history.member_id','left')
        	->where($where)->count();
        $pageData = get_page($count);
        $list = $this->alias('login_history')
        	->join('__MEMBER__ as member on member.id = login_history.member_id','left')
        	->where($where)
	        ->field('login_history.*')
	        ->limit($pageData['limit'])->order('id desc ')->select();

        return array('page'=>$pageData['page'],'list'=>$list);
	}

	public function getLoginHistoryByExcel($where){
        $list = $this->alias('login_history')
        	->join('__MEMBER__ as member on member.id = login_history.member_id','left')
        	->where($where)
	        ->field('login_history.id,login_history.failure_name,login_history.member_id,login_history.login_time,login_history.login_ip,login_history.status,login_history.remark')->order('id desc')->select();

        return $list;
	}

}

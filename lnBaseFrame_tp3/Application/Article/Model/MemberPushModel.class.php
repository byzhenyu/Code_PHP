<?php
namespace Article\Model;
use Think\Model;

class MemberPushModel extends Model{
	
	protected $insertFields = array('id','member_id','push_id','add_time');
	protected $selectFields = array('id','member_id','push_id','add_time');

	protected $_validate = array(
		array('push_id', 'require', '推送文章id参数错误', 1, 'regex', 1),
	);
	
	protected function _before_insert(&$data,$options) {
		$data['member_id'] = UID;
		$data['add_time'] = time();
	}
}
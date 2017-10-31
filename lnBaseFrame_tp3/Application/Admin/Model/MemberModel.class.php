<?php
namespace Admin\Model;
use Think\Model;
use Common\Tools\Emchat;
/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class MemberModel extends Model{
	protected $insertFields = array('real_name','username','department_id','position_id','area_id','email','phone','pay_password','password','sex','birthday','isAdmin','isUser','isService','photo_path','financial_code','file_no','disabled','height','nation','school_and_major','education','graduation_time','now_family_address','origin_place','card_id','card_address','zipcode','school_position','honor','political_landscape','school_contacts','school_contacts_phone','hobby','is_join_training','family_phone','remark','training_time','post_status','attendance_number');
	protected $updateFields = array('id','real_name','department_id','position_id','area_id','email','phone','password','pay_password','sex','birthday','isAdmin','isUser','isService','photo_path','financial_code','file_no', 'status','disabled','height','nation','school_and_major','education','graduation_time','now_family_address','origin_place','card_id','card_address','zipcode','school_position','honor','political_landscape','school_contacts','school_contacts_phone','hobby','is_join_training','family_phone','remark','training_time','post_status','attendance_number');
	protected $selectFields = array('id','real_name','username','department_id','position_id','area_id','email','phone','sex','birthday','isAdmin','isUser','isService','photo_path','financial_code','file_no','status','disabled', 'emchat_username','post_status','attendance_number');

	protected $_validate = array(
		array('username', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 1),
		array('username', 'checkName', '用户名必须以字母开头', self::MUST_VALIDATE, 'callback', 1),
		array('username', '', '用户名已被注册', self::MUST_VALIDATE, 'unique',1), //用户名被占用

		array('real_name', '1,30', '真实姓名长度有误', self::MUST_VALIDATE, 'length', 1),

		array('phone', 'isMobile', '手机号不是11位合法的手机号', self::MUST_VALIDATE, 'function', 3),
		// array('phone', '','手机号码已被注册', self::EXISTS_VALIDATE, 'unique',1), //手机号被占用   */
		array('phone', 'checkPhone','手机号码已被注册', self::MUST_VALIDATE, 'callback',3), //手机号被占用   */

		array('password', '36,41', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 1), //密码长度不合法, 只注册时验证
		array('password', '36,41', '登录密码长度不合法', self::VALUE_VALIDATE, 'length', 2), // 不为空时验证

		array('pay_password', '36,41', '验证密码长度不合法', self::MUST_VALIDATE, 'length', 1), //密码长度不合法, 只注册时验证
		array('pay_password', '36,41', '验证密码长度不合法', self::VALUE_VALIDATE, 'length', 2), // 不为空时验证

		array('department_id', 'number', '非法数据, 所属部门字段', self::MUST_VALIDATE, 'function', 3),
		array('department_id', '1,100000', '请选择用户所属部门', self::MUST_VALIDATE, 'between', 3),

		array('position_id', 'number', '非法数据, 职位字段', self::MUST_VALIDATE, 'function', 3),
		array('position_id', '1,100000', '请选择用户职位', self::MUST_VALIDATE, 'between', 3),

		array('area_id', 'number', '非法数据, 区域字段', self::MUST_VALIDATE, 'function', 3),

		array('email', 'isEmail', '邮箱地址不合法', self::VALUE_VALIDATE, 'function', 3), // 不为空时验证

		array('sex', array(0,1,2), '非法数据, 性别字段', self::EXISTS_VALIDATE, 'in', 3),
//		array('birthday', 'validateDate', '用户生日不是一个合法的日期', self::VALUE_VALIDATE, 'function', 3),
		array('isAdmin', array(0,1), '非法数据, 用户是否可以登录后台字段', self::MUST_VALIDATE, 'in', 3),
		array('isUser', array(0,1), '非法数据, 用户是否可以登录前台字段', self::MUST_VALIDATE, 'in', 3),
		array('isService', array(0,1), '非法数据, 用户是否是客户字段', self::MUST_VALIDATE, 'in', 3),
		array('disabled', array(0,1), '非法数据, 用户是否启用', self::MUST_VALIDATE, 'in', 3),
		array('post_status', array(1,2,3,4), '非法数据, 用户岗位状态', self::MUST_VALIDATE, 'in', 3),
		array('attendance_number', '4,30', '考勤编号不正确, 请输入4到30位字符', self::MUST_VALIDATE, 'length', 3),
		array('financial_code', '0,50', '用户财务编码长度错误', self::EXISTS_VALIDATE, 'length', 3),
		array('file_no', '0,50', '用户档案号长度错误', self::EXISTS_VALIDATE, 'length', 3),
		array('photo_path', '0,255', '用户头像长度有误', self::EXISTS_VALIDATE, 'length', 3),
		array('remark', '0,150', '用户签名长度过长', self::EXISTS_VALIDATE, 'length', 3),
		array('honor', '0,150', '用户荣誉长度过长', self::EXISTS_VALIDATE, 'length', 3),
		array('hobby', '0,150', '用户爱好长度过长', self::EXISTS_VALIDATE, 'length', 3),
	);



	// 判断手机号码是否已经有了, 判断时不判断自已
	protected function checkPhone($data){
		$id = I('id', 0, 'intval');
		$where['phone'] = $data;
		// $where['status']= array('eq',0);
		//$where['disabled']= array('eq',0);
		if ($id > 0) {
			$where['id'] = array('neq', $id );
		}
		$count = $this->where($where)->count();
		if ($count > 0) {
			return false;
		}
		return true;
	}

	// 判断用户名首字符必须为字母, 且是字母或数字或字母数字的组合
	protected function checkName($data){
		$firstCode = substr($data, 0, 1);
		if (ctype_alpha($firstCode)) {
			if (ctype_alnum($data)) {
				return true;
			}
		}
		return false;
	}

	public function getMemberInfo($map, $field=null){
		if ($field == null) $field = $this->selectFields;

		$info = $this->where($map)->field($field)->find();

		if ($info) {
	        $info['photo_path_thumb_120'] = thumb($info['photo_path']);
	        $info['photo_path_thumb_220'] = thumb($info['photo_path'], 220, 220);
		}

		return $info;
	}

	public function getMemberByPage($map, $field=null, $order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;

        $keywords = I('keywords', '');
        if ($keywords) {
       	 	$where = 'real_name like "%'. $keywords .'%" or username like "%'. $keywords .'%" or phone like "%'. $keywords .'%"';
        }

		$count = $this->distinct(true)->where($where)->where($map)->count();

        $page = get_page($count);

        $list = $this->distinct(true)->field($field)->join('__MEMBER_ROLE__ member_role on member_role.member_id=__MEMBER__.id','left')->where($where)->where($map)->limit($page['limit'])->order($order)->select();
        //echo $this->_sql();
        return array('list' => $list,'page' => $page);
	}
	//导出Excel的数据
	public function getMemberForExcel($map, $field=null, $order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;
        $keywords = I('keywords', '');
        if ($keywords) {
       	 	$where = 'real_name like "%'. $keywords .'%" or username like "%'. $keywords .'%" or phone like "%'. $keywords .'%"';
        }
        $list = $this->distinct(true)->field($field)->join('__MEMBER_ROLE__ member_role on member_role.member_id=__MEMBER__.id','left')->where($where)->where($map)->order($order)->select();
        return $list;
	}

	/**
	 * 获取用户数据  不分页
	 * @author liuyang   594353482@qq.com
	 * 
	 */
	public function getMemberList($where, $field=null, $order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;
        $list = $this->field($field)->where($where)->order($order)->select();
        return $list;
	}
	protected function _before_insert(&$data, $option){
		if ($data['area_id'] <= 0 || $data['area_id'] > 100000) {
			$this->error = '请选择用户所属' . C('AREA_NAME');
			return false;
		}

		$data['reg_time'] = time();
		$data['reg_ip'] = get_client_ip();
		// 判断密码为空就不修改这个字段
		$data['password'] = md5($data['password']);
		$data['pay_password'] = md5($data['pay_password']);

		unset($data['id']);
		if(empty($data['birthday'])){
			unset($data['birthday']);
		}else{
			$data['birthday']= strtotime(I('birthday'));
		}
		//用户姓名拼音处理
		$real_name = $data['real_name'];
        $pinyin = new \Common\Tools\ChineseToPinyin();
        $data['pinyin'] = $pinyin->getAllPinyi($real_name);

		// 生成环信账号
		$emchat_username = $this->_datetimeRand(); // 用户账号
		$emchat_password = randNumber(16);	// 用户密码
		$emchat = new Emchat();
        $result = $emchat->createUser($emchat_username,  $emchat_password); //创建环信用户
        if ($result['error'] != '') {
        	$this->error = '聊天账号创建失败';
            return false;
        }
		$data['emchat_username'] = $emchat_username;
		$data['emchat_password'] = $emchat_password;
	}

	protected function _after_insert(&$data,$option){

		$member_id = $data['id'];
		$role_ids = I('role_id');
		$memberRole = M('MemberRole');
		foreach ($role_ids as $k => $v) {
			$temp['member_id'] = $member_id;
			$temp['role_id'] = $v;
			$memberRole->data($temp)->add();
		}
	}

	protected function _before_update(&$data, $option){
		// 判断密码为空就不修改这个字段
		if(empty($data['password'])){
			unset($data['password']);
		} else{
			$data['password'] = md5($data['password']);
		}
		// 判断密码为空就不修改这个字段
		if(empty($data['pay_password'])){
			unset($data['pay_password']);
		} else{
			$data['pay_password'] = md5($data['pay_password']);
		}
		//用户姓名拼音处理
		$real_name = $data['real_name'];
        if (!empty($real_name)) {
            $pinyin = new \Common\Tools\ChineseToPinyin();
            $pinyin_name = $pinyin->getAllPinyi($real_name);
            $data['pinyin'] = $pinyin_name;
        }

		if ($data['id'] == 1) {
			unset($data['disabled']);
			unset($data['status']);
			unset($data['isAdmin']);
			unset($data['isService']);
		}

		// 用户名不可以修改
		unset($data['username']);
		unset($data['id']);

	}

	public function getMemberNameById($id=0){
		$memberName = $this->where("id=$id")->getField('real_name');
		return $memberName;
	}

	/**
	 * 生成日期与随机数字的字符串, 用下划线分隔
	 * @return String 日期_时间_毫秒微秒_4位随机数
	 * example 上传的文件名, 环信用户的用户名
	 */
	private function _datetimeRand(){
	    return date('Ymd_His') .'_'. rand(100000,999999);
	}

	//create by yangchunfu	
	public function getPhoneByMemberIds($ids){
		$where['id'] = array('in', $ids);
		$where['status'] = 0;

		$phoneList = $this->where($where)->getField('phone', true);

		return $phoneList;
	}
}

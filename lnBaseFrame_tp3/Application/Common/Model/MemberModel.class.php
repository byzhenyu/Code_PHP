<?php
namespace Common\Model;
use Think\Model;
/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class MemberModel extends Model{

	protected $_validate = array(
        array('username', '4,20', '用户名不正确, 请输入4到20位字符', self::MUST_VALIDATE, 'length', 4),
        array('password', '36,46', '登录密码长度不合法', self::MUST_VALIDATE, 'length', 4), //密码长度不合法, 只注册时验证
	);

	/**
	 * 用户登录认证
	 * @param  string  $password sha1 加密后的验证密码
	 * @return bool
	 */
	public function payPassword($password=''){
		$pay_password = $this->where('id='. UID)->getField('pay_password');
		if (md5($password) === $pay_password) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 用户登录认证
	 * @param  string  $username 用户名
	 * @param  string  $password 用户密码
	 * @param  string  $type 登录用户类型 admin||user
	 * @return array
	 */
	public function login($username='', $password='', $type=''){
		if ($username == '' ||  $password == '' || $type == '') {
			exit('参数错误!');
		}
		$map['status'] = 0;
		/* 获取用户数据 */
		$member = $this
			->field('id,username,password,isAdmin,isUser,disabled,real_name,phone,photo_path,machine_code,last_login_time,position_id,emchat_username,emchat_password,birthday,post_status')
			->where($map)->where('username="'. $username .'" or phone="'. $username .'"')
			->find();

		// echo md5($password);echo '||||'; echo $member['password'];
		if(is_array($member)){
			/* 验证用户密码 */

			if(md5($password) === $member['password']){
				if ($type == 'admin' && $member['isAdmin'] != 0) {
					return V(0, '用户无权限登录');
				}
				if ($type == 'user' && $member['isUser'] != 0) {
					return V(0, '用户无权限登录');
				}
				if ($member['post_status'] > 2) {
					return V(0, '只有实习和在岗状态才能登录');
				}
				if ($member['disabled'] != 0) {
					return V(0, '用户账号已经被禁用');
				}
				$this->updateLogin($member['id']); //更新用户登录信息
				//登录成功，返回用户信息
				return V(1, '登录成功', $member);
			} else {
				return V(0, '用户名或密码错误!');
			}
		} else {
			return V(0, '用户名或密码错误.');
		}
	}

	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid){
		$data = array(
			'id'              => $uid,
			'last_login_time' => NOW_TIME,
			'last_login_ip'   => get_client_ip(1),
		);
		$this->save($data);
	}
}

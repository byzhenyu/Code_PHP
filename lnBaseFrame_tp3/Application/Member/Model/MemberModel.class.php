<?php
namespace Member\Model;
use Think\Model;
/**
 * 后台用户管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class MemberModel extends Model{
	protected $updateFields = array('id','real_name','password','pay_password','sex','birthday','photo_path');
	protected $selectFields = array('id','real_name','username','department_id','position_id','area_id','email','phone','sex','birthday','photo_path','financial_code','file_no');

	protected $_validate = array(
        array('real_name', '2,30', '姓名长度有误', self::MUST_VALIDATE, 'length', 3),
		array('email', 'isEmail', '邮箱地址不合法', self::VALUE_VALIDATE, 'function', 3), // 不为空时验证
		array('sex', array(0,1,2), '非法数据, 性别字段', self::EXISTS_VALIDATE, 'in', 3),
		// array('birthday', 'validateDate', '用户生日不是一个合法的日期', self::VALUE_VALIDATE, 'function', 3),
		array('photo_path', '0,255', '用户头像长度有误', self::EXISTS_VALIDATE, 'length', 3),
		array('remark', '0,150', '用户签名长度过长', self::EXISTS_VALIDATE, 'length', 3),
		array('password', '20,40', '密码长度不合法', self::EXISTS_VALIDATE, 'length', 3),
		array('pay_password', '20,40', '验证密码长度不合法', self::EXISTS_VALIDATE, 'length', 3),


	);

	public function getMemberList($where, $field=null, $order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;

        $map['status'] = 0;
        $map['disabled'] = 0;
        $list = $this->field($field)->where($map)->where($where)->order($order)->select();
        foreach ($list as $key => $value) {
			if ($value['birthday'] > 0) {
				$list[$key]['birthday'] = time_format($value['birthday'], 'Y-m-d');
			}
	        $list[$key]['photo_path_thumb_120'] = thumb($value['photo_path']);
	        $list[$key]['photo_path_thumb_220'] = thumb($value['photo_path'], 220, 220);
        }
        return $list;
	}

	public function getMemberByPage($map, $field=null, $page_size = 10,$order='reg_time desc, id desc'){
		if ($field == null) $field = $this->selectFields;

        $keywords = I('keywords', '');
        if ($keywords) {
       	 	$where = 'username like "%'. $keywords .'%" or real_name like "%'. $keywords .'%" or phone like "%'. $keywords .'%"';
        }

		$count = $this->where($where)->where($map)->count();

        $page = get_page($count,$page_size);

        $list = $this->field($field)->where($where)->where($map)->limit($page['limit'])->order($order)->select();
        //echo $this->_sql();
        return array('list' => $list,'page' => $page);
	}

    //这是api接口
	public function getMemberInfo($id=0){
		if($id == 0) $id = UID;

		$info = $this->where(array('id'=>$id))->field('id,real_name,department_id,position_id,area_id,phone,sex,birthday,photo_path')->find();

        return $info;
	}

    //这个是api接口的方法
	public function updataMemberInfo(){
		$info = $this->create();
        if($info){
            $birthday = I('birthday','');
            $info['birthday'] = strtotime($birthday);
            $res = $this->where('id='.UID)->save($info);
            if($res !== false){
                return V(1, '保存信息成功');;
            }else{
                return V(0, '保存信息失败, 未知原因');;
            }
        }else{
            return V(0, $this->getError());
        }
	}


	public function modifyLoginPassword($login_password,$new_login_password){
		$password = $this->where(array('id'=>UID))->field('password')->find();
		$md5_login_password = md5($login_password);
		if($md5_login_password != $password['password']){
			return 0;  //原密码不正确
		}else{
			$data['password'] = md5($new_login_password);
			$this->where(array('id'=>UID))->save($data);
			return 1;
		}
	}

	public function modifyPayPassword($password,$new_password){
		$pay_password = $this->where(array('id'=>UID))->field('pay_password')->find();
		$md5_pay_password = md5($password);
		if($md5_pay_password!= $pay_password['pay_password']){
			return 0;  //原密码不正确
		}else{
			$pay_password = md5($new_password);
			$data['pay_password']=$pay_password;
			$this->where(array('id'=>UID))->save($data);
			return 1;
		}
	}

	/**
	 * 保存用户头像
	 * @author liuyang 594353482
	 */

	public function saveHeadPortrait($photo_path){
		$data['photo_path'] = $photo_path;
		$result = $this->where(array('id'=>UID))->data($data)->save();
		if ($result === false) {
			return V(0, '保存头像失败, 未知原因');
		}
		return V(1, '保存头像成功');
	}

	//获取用户信息
	public function getMemberInfoByEmchatId($emchat_username){
		$info = $this->where(array('emchat_username'=>$emchat_username))->field('id,real_name,department_id,position_id,area_id,phone,sex,birthday,photo_path')->find();

        return $info;
	}

}

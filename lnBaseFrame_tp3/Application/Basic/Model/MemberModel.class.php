<?php
namespace Basic\Model;
use Think\Model;
/**
 * 用户基础数据获取
 * @author liuyang 594353482
 *
 */
class MemberModel extends Model{

	protected $selectFields = array('real_name', 'department_id', 'area_id', 'phone');

	public function getMemberNameById($id=0){
		$memberName = $this->where("id=$id")->getField('real_name');
		return $memberName;
	}
	//create by yangchunfu
    public  function getMemberInfoById($id, $fields = null){
        if($fields == null){
            $fields = $this->selectFields;
        }

        $memberInfo = $this->where("id=$id")->field($fields)->find();

        return $memberInfo;
    }

    public function getMemberList($fields = null){
        if($fields == null){
            $fields = $this->selectFields;
        }

        $memberInfo = $this->where('status=0 and disabled=0')->field($fields)->select();

        return $memberInfo;
    }
}

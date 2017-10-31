<?php
namespace Basic\Model;
use Think\Model;
/**
 * 菜单管理
 * @author zhaojiping QQ:17620286 liniukeji.com
 *
 */
class NavModel extends Model{
	protected $selectFields = array('id','title', 'url');

	/**
	 * 获取WEB前端使用的菜单
	 * @param int $parent_id 父级菜单ID, 如果不传或0, 返回顶级菜单
	 * @return array 菜单
	 */
	public function getWebNavList($parent_id=0){
		$where['type'] = 1;
        $where['parent_id'] = $parent_id;

        // 不同的用户有不同的菜单
        $member_level = member_level();
        if ($member_level === 'MEMBER') $where['is_member'] = 0;
        else if ($member_level === 'MANAGER') $where['is_manager'] = 0;
        else if ($member_level === 'BOSS') $where['is_boss'] = 0;

        $mainNav = $this->getNavList($where);
        return $mainNav;
	}

	// 获取菜单的分类
	public function getNavList($map, $field=null, $order='sort asc'){
		if ($field == null) $field = $this->selectFields;

        $where['disabled'] = 0;
        $where['status'] = 0;

        $list = $this->field($field)->where($where)->where($map)->order($order)->select();
        //echo $this->_sql();
        return $list;
	}

}

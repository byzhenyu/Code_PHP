<?php
namespace Admin\Model;
use Think\Model;
/**
 * 后台区域
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 *
 */
class NavModel extends Model{
	protected $insertFields = array('parent_id','title','type','img','url','add_time','status','disabled','sort','is_manager','is_boss','is_member');
	protected $updateFields = array('id','parent_id','title','type','img','url','add_time','status','disabled','sort','is_manager','is_boss','is_member');
	protected $selectFields = array('id','parent_id','title as name','type','add_time','status','disabled','sort','is_manager','is_boss','is_member');

	protected $_validate = array(
		array('title', 'require', '标题名称不能为空', 1, 'regex', 3),
		array('title', '1,60', '标题名称长度有误,请输入1到60个字符', 1, 'length', 3),
		array('img', 'require', '图片不能为空！', 0, 'regex', 3),
		array('img', '1,255', '图片长度有误', 0, 'length', 3),
		array('url', 'require', '链接地址不能为空！', 0, 'regex', 3),
		array('url','1,255','跳转url有误！', 0, 'length', 3),
		array('disabled', 'require', '是否启用禁用有误！', 0, 'regex', 3),
		array('sort','0,1000','排序范围在0--1000', 0,'between',3),
		array('is_manager','0,1','非部长特权存储值有误！', 0,'between',3),
		array('is_boss','0,1','部长特权存储值有误！', 0,'between',3),
		array('is_member','0,1','部长特权存储值有误！', 0,'between',3),
	);

	//添加时间
	protected function _before_insert(&$data, $option){
		$data['add_time']=NOW_TIME;
	}
	protected function _before_update(&$data, $option){
		
	}
	//轮播图列表
	public function navList($where, $field=null, $order="sort asc, id desc"){
		if ($field == null) $field = $this->selectFields;

		$list = $this->where($where)->field($field)->order($order)->select();
		return $list;
	}
	//修改轮播图的详情页
	public function detailInfo($id){
		$info = $this->find($id);
		return $info;
	}
	
}
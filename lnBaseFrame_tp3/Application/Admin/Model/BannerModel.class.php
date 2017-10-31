<?php
namespace Admin\Model;
use Think\Model;
/**
 * 后台轮播图、登录背景图
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 *
 */
class BannerModel extends Model{
	protected $insertFields = array('title','type','img','url','article_id','open_type','content','add_time','disabled','sort','is_default');
	protected $updateFields = array('id','title','type','img','url','article_id','open_type','content','add_time','status','disabled','sort','is_default');
	protected $selectFields = array('id','title','type','add_time','open_type','status','disabled','sort','is_default');

	protected $_validate = array(
		array('title', 'require', '标题名称不能为空', 1, 'regex', 3),
		array('title', '1,60', '标题名称长度有误,请输入1到60个字符', 1, 'length', 3),
		array('img', 'require', '图片不能为空', 1, 'regex', 3),
		array('img', '1,255', '图片长度有误', 0, 'length', 3),
		array('content', 'require', '文章内容不能为空', 0, 'regex', 3),
		array('url','/^http:\/\//','跳转url有误！必须以http://开头', 0, 'regex', 3),
		array('url','1,255','跳转url有误！', 0, 'length', 3),
		array('article_id', '1,1000000000', '选择打开文章有误', 0, 'between', 3),
		array('disabled', 'require', '是否启用禁用有误', 0, 'regex', 3),
		array('sort','1,1000','排序必须填写', 0,'between',3),
		array('is_default','1,10','是否默认为启动也必须选择', 0,'between',3),
	);

	//添加时间
	protected function _before_insert(&$data, $option){
		$data['add_time'] = NOW_TIME;
	}
	protected function _before_update(&$data, $option){
		$data['update_time'] = NOW_TIME;
	}
	//轮播图列表
	public function bannerList($where, $field=null, $order='sort asc, id desc'){
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
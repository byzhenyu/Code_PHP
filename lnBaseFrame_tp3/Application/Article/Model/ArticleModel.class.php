<?php
namespace Article\Model;
use Think\Model;

/**
 * 文章列表Model类
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 *
 */
class ArticleModel extends Model{
	protected $selectFields = array('id', 'name', 'introduce', 'content', 'add_time', 'click_count');

	public function ArticleList($where, $field=null, $order='sort asc, id desc'){
		if ($field == null) $field = $this->selectFields;
		$where['display'] = 0;
		$where['status'] = 0;
		$count = $this->where($where)->count();
        $page = get_page($count, 10);
		$data = $this
				->where($where)
				->limit($page['limit'])
				->field($field)
				->order($order)
				->select();
		return $data = array(
			'data' => $data,
			'page' => $page
		);
	}

	public function ArticleInfo($where, $field=null, $order='sort asc, id desc'){
		if ($field == null) $field = $this->selectFields;
		$where['display'] = 0;
		$where['status'] = 0;
		// 增加点击量
		$this->where($where)->setInc('click_count', 1);
		$info = $this->where($where)->field($field)->find();
		return $info;
	}
}
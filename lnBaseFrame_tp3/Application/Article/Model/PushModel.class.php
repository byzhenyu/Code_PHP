<?php
namespace Article\Model;
use Think\Model;

/**
 * 推送列表Model类
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 *
 */
class PushModel extends Model{

	public function getPushList($where){
		$where['status'] = 0;
		$where['send_state'] = 0;
		$where['member_id'] = array('in', '0, '.UID.'');
		$count = $this->where($where)->count();
        $page = get_page($count, 10);
		$data = $this
				->where($where)
				->limit($page['limit'])
				->field('id, title, url, content, open_type, img, description, type, push_time, is_see')
				->order('push_time desc')
				->select();
		foreach ($data as $key => $v) {
			$data[$key]['push_time'] = time_format($v['push_time']);
			$data[$key]['is_click'] = 1;
			if ($v['type'] == 5) {//官方公告
				$data[$key]['content'] = $data[$key]['description'];
			}

		}
		return array('page' => $page, 'data' => $data);
	}

	/**
	 * 更新push表是否已经查看
	 * @param int $push_id push表主键id
	 * return true 
	 */
	public function updateIsSee($push_id){
		$result = $this->where(array('id' => $push_id))->setField('is_see', 1);
		return true;
	}
}
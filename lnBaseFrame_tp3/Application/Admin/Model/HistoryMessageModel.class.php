<?php
namespace Admin\Model;
use Think\Model;
/**
 * 后台历史记录
 * @author wangzhiliang QQ:1337841872 liniukeji.com
 *
 */
class HistoryMessageModel extends Model{

	protected $selectFields = array('id','send_member_id','receive_member_id','message_payload','send_time','message_from','message_type');

	// 获取聊天记录
	public function getHistoryMessage($where, $field = null, $order = 'create_time desc'){
		if ($field == null) {
			$field = $this->selectFields;
		}
		$count = $this->where($where)->count();
        $page = get_page($count);
        $list = $this->field($field)->where($where)->limit($page['limit'])->order($order)->select();
        // p($this->_sql());die;
        foreach ($list as $key => $value) {
        	$list[$key]['message_payload'] = unserialize($value['message_payload']);
        	$list[$key]['message_from_name'] = M('Member')->where(array('emchat_username' => $value['message_from']))->getField('real_name');
            if ($list[$key]['message_payload']['bodies'][0]['type'] == 'txt') {
                $list[$key]['message_payload']['bodies'][0]['msg'] = str_replace('<', '&lt;', $list[$key]['message_payload']['bodies'][0]['msg']);
            }
        }
        return array(
        	'data' => $list,
        	'page' => $page,
        );
	}

	// 拉取环信聊天记录写入本地数据库
    public function getChatRecord(){
        $lastMessageInfo    = $this->Order('id DESC')->limit(1)->find(); //查询之前获取的最后一条聊天记录信息
        $lastTimestamp      = $lastMessageInfo['send_time']; //获取的最后一条聊天记录的发送时间 作为本次获取环信聊天记录的起始时间
        if(empty($lastTimestamp)) {
            $lastTimestamp = time(); //获取当前时间戳
        }
        $messageCount = $this->_getChatRecord($lastTimestamp); //获取环信服务器聊天记录并保存到本地服务器
    }

	/**
     * [getChatRecord 拉取环信聊天记录]
     * @param  integer $timestamp [聊天记录拉取起始时间戳 13位]
     * @return [type]             [返回拉取聊天记录的条数]
     */
    private function _getChatRecord($timestamp = 0){
        $result       = array();
        $emchat       = new \Common\Tools\Emchat();
        $ql           = urlencode("select * where timestamp > ") . $timestamp; //获取当前时间的13位时间戳
        $messageCount = 0;
        $messageType  = array('txt', 'img', 'audio', 'video', 'loc');

        $cursor = "";
        do {
            //拉取环信服务器聊天记录 1000条每次
            $result = $emchat->getChatRecordForPage($ql, 1000, $emchat->readCursor('chatfile.txt'));
            if(!empty($result)) {
                foreach($result['entities'] as $data) {
                    if(in_array($data['payload']['bodies']['0']['type'], $messageType)) { //抓取符合要求的消息
                        if($this->saveChatRecord($data)) {//保存消息记录到本地
                            $messageCount++;
                        }                           
                    }
                }
            }
            $cursor = $emchat->readCursor('chatfile.txt');
        } while (!empty($cursor));//游标如果不为空，接着拉取聊天记录
        return $messageCount;      
    }

    protected function saveChatRecord($data) {
        $member          = D('Admin/Member');
        $send_member_id    = $member->where("emchat_username='{$data['from']}'")->getField('id');
        $receive_member_id = $member->where("emchat_username='{$data['to']}'")->getField('id');
        $message_type    = $data['payload']['bodies']['0']['type'];
        $chat_type       = $data['chat_type'];
        //txt:文本消息, img:图片, loc：位置, audio：语音 video：视频 file：文件
        if($message_type == 'txt') {
            $message_type = 0;
        } elseif ($message_type == 'img') {
            $message_type = 1;
        } elseif ($message_type == 'audio') {
            $message_type = 2;
        } elseif ($message_type == 'video') {
            $message_type = 3;
        } elseif ($message_type == 'loc') {
            $message_type = 4;          
        }
        $adddata = array(
        	'uuid' => $data['uuid'],
		    'message_uuid' => $data['uuid'],
		    'create_time' => $data['created'],
		    'modify_time' => $data['modified'],
		    'send_time' => $data['timestamp'],
		    'send_member_id' => $send_member_id,
		    'receive_member_id' => $receive_member_id,
		    'message_from' => $data['from'],
		    'message_chat_type' => ($chat_type == 'chat')?0:1,
		    'group_id' => ($data['groupId'] == '')?0:$data['groupId'],
		    'message_to' => $data['to'],
		    'message_id' => $data['msg_id'],
		    'message_type' => $message_type,
		    'message_payload' => serialize($data['payload'])
        );
        $res = M('HistoryMessage')->add($adddata); //逐条保存聊天记录到本地数据库
        return $res;
    }
}
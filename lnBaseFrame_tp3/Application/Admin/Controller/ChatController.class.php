<?php
namespace Admin\Controller;

use Common\Tools\Emchat;

/**
 * 后台首页控制器
 * create by zhaojiping <QQ:17620286>
 */
class ChatController extends AdminCommonController {

    /**
     * 后台首页
     */
    public function index(){
    	// 获取用户自己的信息
        $where['id'] = UID;
        $myInfo = D('Member')->getMemberInfo($where, array('real_name','username','department_id','position_id','area_id','phone','sex','birthday','photo_path','financial_code','file_no','emchat_username','emchat_password'));
        // p($myInfo);

        // 获取好友的列表
        $map['to'] = $myInfo['emchat_username'];
        $friend_list = M('chatFriends')->field('from')->where($map)->limit(30)->order('update_time desc')->select();

        $this->myInfo = json_encode($myInfo);
        $this->friend_list = json_encode($friend_list);
        $this->display();
    }

    // 获取用户聊天记录
    public function getHistoryMessage(){
        // 需要先拉取聊天记录
        $MessageModel = D('Admin/HistoryMessage');
        $MessageModel->getChatRecord();
        $message_from = I('message_from', '');
        $message_to = I('message_to', '');

        $where['_string'] = ' ( message_from = "'.$message_from.'" AND message_to = "'.$message_to.'" )  OR ( message_to = "'.$message_from.'" AND message_from = "'.$message_to.'" ) ';
        $model = D('Admin/HistoryMessage');
        $data = $model->getHistoryMessage($where);
        $this->assign('message_to', $message_to);
        $this->assign('data', $data['data']);
        $this->assign('page', $data['page']);
        $this->display('getHistoryMessage');
    }

    /*测试函数*/
    public function send_message(){
        $emchat_username = '20161012_110720_189511';
        
        $from = '20161012_110720_189511';
        $target_type = 'users';
        $target = array($emchat_username);
        $content = '测试[):]信息';
        $ext['a'] = 'a';
        $emchat = new Emchat();
        $rs = $emchat->sendText($from, $target_type, $target, $content, $ext);

    }

    public function getFriendInfo(){
        $emchat_username = I('emchat_username', '');
        if ($emchat_username == '') {
            $this->ajaxReturn(V(0, '参数错误!'));
        }
        $where['emchat_username'] = $emchat_username;
        $info = D('Member')->getMemberInfo($where);
        if ($info) {
            $this->ajaxReturn(V(1, '好友信息', $info));
        } else {
            $this->ajaxReturn(V(0, '未获取到好友信息, 未知原因'));
        }
    }

    // 更新好友列表
    public function updateFriendList(){
        $from = trim(I('from', ''));
        $to = trim(I('to', ''));

        if ($from == '' || $to == '') {
            $this->ajaxReturn(V(0, '参数错误'));
        }

        $data['update_time'] = time();
        $data['from'] = $from;
        $data['to'] = $to;
        $where['from'] = $from;
        $where['to'] = $to;

        $chatFriends = M('ChatFriends');
        $result = $chatFriends->where($where)->find();
        if ($result) {
            if ($chatFriends->where($where)->data($data)->save()) {
                $this->ajaxReturn(V(1, '好友列表更新成功'));
            }
        } else {
            if ($chatFriends->data($data)->add()) {
                $this->ajaxReturn(V(1, '好友列表添加成功'));
            }
        }
        $this->ajaxReturn(V(0, '好友列表更新失败, 未知原因'));
    }
}

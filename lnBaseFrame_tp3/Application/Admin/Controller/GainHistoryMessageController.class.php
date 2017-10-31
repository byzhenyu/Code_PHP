<?php
namespace Admin\Controller;
use Think\Controller;

/**
 * 获取环信聊天记录
 * @author wangzhiliang <1337841872@qq.com>
 */
class GainHistoryMessageController extends Controller {

    // 拉取聊天记录
    public function getChatRecord(){
        $MessageModel = D('Admin/HistoryMessage');
        $MessageModel->getChatRecord();
    }
}
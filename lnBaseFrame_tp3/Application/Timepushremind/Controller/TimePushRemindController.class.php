<?php
namespace Timepushremind\Controller;
use Think\Controller;

/**
 * 定时推送
 * @author wangzhiliang <1337841872@qq.com>
 * @param type 推送类型 
 */
class TimePushRemindController extends Controller {

	protected function _initialize(){

        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config = api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

    }

	// 定时任务
	public function timePushRemind(){
		D('Common/PushRegular')->regularPushList(time());
		echo 'success';
	}
   
}
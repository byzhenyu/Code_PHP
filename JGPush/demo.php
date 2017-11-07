//function 添加

/**
 * 极光推送通用消息 - 新版
 * @param string $alert 提示标题
 * @param int $type 信息类型
 * @param array $userId 用户id 可传数组
 * @param string $msg 信息内容
 * @param array $tag 标签
 * @param array $platform 推送app端array('ios', 'android')
 * @author 颜廷超 <yantc@liuniukeji.com>
 */
function JGPush($alert = '', $type = 1, $userId = null, $msg = '', $tag = null, $platform = null) {
    require_once('./Plugins/JGPush/autoload.php');
    $client = new \JPush\Client(C('JiGuang')['AppKey'], C('JiGuang')['MasterSecret'], './Runtime/jpush.log');
    $APNs_production = C('JiGuang')['APNs_production']?true:false;
    $extras = array(
        'type' => $type,
        'content' => $msg
    );
    $client = $client->push();
    if ($platform) {
        $client = $client->setPlatform($platform);
    } else {
        $client = $client->setPlatform('all');
    }
    $client = $client->setNotificationAlert($alert);
    $client = $client->iosNotification($alert, ['extras' => $extras]);
    $client = $client->androidNotification($alert, ['extras' => $extras]);
    //$client = $client->message($alert, ['extras' => $extras]);
    if ($userId) {
        $client->addAlias($userId);
    } elseif ($tag) {
        $client->addTag($tag);
    } else {
        $client = $client->addAllAudience();
    }

    $client =$client->options(array('apns_production' => $APNs_production,));

    try {
        $response = $client->send();
        //print_r($response);
        return $response;
        //return json_encode($response);
    } catch (\JPush\Exceptions\APIConnectionException $e) {
        return $e;
        //print $e;
    } catch (\JPush\Exceptions\APIRequestException $e) {
        return $e;
    }
}
   
   
   //config 添加
       // 极光推送 快帮学堂
    'JiGuang' => array(
        'AppKey' => 'c0281379c9f5d5413a...',
        'MasterSecret' => '785d56523811cd7bc.....',
        'APNs_production'=> true,//True 表示推送生产环境，False 表示要推送开发环境；
    ),
   
   //使用
   $response = JGPush($pushInfo['title'], $pushInfo['type'], null, $pushInfo['id'],$pushInfo['tags']);
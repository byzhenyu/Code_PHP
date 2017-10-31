<?php

// 常量定义
const VERSION    = '1.0.0.0';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

//用于测试打印数组数据
function p($arr){
	header('content-type:text/html;charset=utf-8');
	echo '<pre>';
	print_r($arr);
	echo '</pre>';
}

/**
  * 用于API调式时输出LOG文件
  * @param mixed $value 要打印的数年据
  * @param string $file 文件要保存的完整路径, 含文件名, 默认在当前控制器目录下同名.log
  * @return null 无返回值
  * by zhaojiping liuniukeji.com
  */
function LL($value='', $file=''){
    if ($file == '') {
        $file = './Application/'. MODULE_NAME .'/Controller/'. CONTROLLER_NAME .'Controller.class.log';
    }
    error_log(print_r($value,1),3, $file);
}

/**
 * 返回JSON通一格式
 * by zhaojiping liuniukeji.com
 */
function V($status=-1, $info='', $data=''){
    if ($status == -1) {
        exit('参数调用错误');
    }
    return array('status'=>$status, 'info'=>$info, 'data'=>$data);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function user_is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return $user;
    }
}

/**
 * 判断职位是不是普通用户
 * @param int $position_id 职位ID
 * @return bool
 * by zhaojiping liuniukeji.com
 */
function is_member($position_id=0){
    $is_member = M('Position')->where("parent_id=$position_id and status=0")->count();
    if ($is_member <= 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * 判断用户的等级
 * @param int $uid 用户member的id, 默认为当前登录用户
 * @return string MEMBER 业务员; BOSS 部长; MANAGER 管理员
 * by zhaojiping liuniukeji.com
 */
function member_level($uid=0){
    if ($uid == 0) $uid = UID;

    $position_id = M('Member')->where('id='. $uid)->getField('position_id');
    $is_member = M('Position')->where("parent_id=$position_id and status=0")->count();
    if ($is_member <= 0) {
        return 'MEMBER';
    } else {
        $is_boss = M('Position')->where("id=$position_id and parent_id=0")->count();
        if ($is_boss > 0) {
            return 'BOSS';
        } else {
            return 'MANAGER';
        }
    }
}

/**
 * 多维数组中查找特定value的方法, 并unset
 * $array 要查找的源数组
 * $key 数组的key名
 * $value 数组的key对应的值
 * return array 返回处理完的数组
 * by zhaojiping liuniukeji.com
 */
function unset_search_array($array, $key, $value){
    foreach($array as $keyp=>$valuep){
        if($valuep[$key]==$value){
            unset($array[$keyp]);
        }
    }
    return $array;
}

/**
 * 递归重组数据节点信息为多维数组
 * Array $node 要重组的数组
 * Int $pid 顶级的ID
 * String $pName 父级字段名称
 * by zhaojiping liuniukeji.com
 */
function node_merge($node, $pid = 0, $pName='pid'){
    $arr = array();

    foreach($node as $v){
        if($v[$pName] == $pid){
            $v['child'] = node_merge($node, $v['id']);
            $arr[] = $v;
        }
    }
    return $arr;
}

/**
 * 通用分页处理函数
 * @param Int $count 总条数
 * @param int $page_size 分页大小
 * @return Array  ['page']分页数据  ['limit']查询调用的limit条件
 */
function get_page($count, $page_size=0){
    if ($page_size == 0) $page_size = C('PAGE_SIZE');
	$page = new \Think\Page($count, $page_size);
	$page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
	$show = $page->show();
	$limit = $page->firstRow.','.$page->listRows;
	return array('page'=>$show,'limit'=>$limit);
}

//基于数组创建目录和文件
function create_dir_or_files($files){
	foreach ($files as $key => $value) {
		if(substr($value, -1) == '/'){
			mkdir($value);
		}else{
			@file_put_contents($value, '');
		}
	}
}

/**
 * 通用图片上传函数
 * @param String $imgname 上传文件域的NAME属性
 * @param type $dirname  上传文件存储目录
 * @param type $thumb    需要生成多少个缩略图
 * @return Array
 */
function upload($imgname,$dirname,$thumb=array()){
	if(isset($_FILES[$imgname]) && $_FILES[$imgname]['error'] == 0){
		$upload = new \Think\Upload();
		$rootpath = C('UPLOAD_ROOTPATH');
		$upload->savePath = $rootpath;
		$upload->maxSize = intval(C('IMAGE_MAXSIZE'))*1024*1024;
		$upload->exts = C('ALLOW_IMG_EXT');
		$upload->savePath = $dirname.'/';
		$info = $upload->upload(array($imgname=>$_FILES[$imgname]));
		if(!$info){
			return array('status'=>0,'error'=>$upload->getError());
		}else{
			$ret['status'] = 1;
			$ret['image']['origin'] = $origin_img = $info[$imgname]['savepath'].$info[$imgname]['savename'] ;
			if(is_array($thumb) && !empty($thumb)){
				$image = new \Think\Image();
				foreach($thumb as $k => $v){
					$ret['image']['thumb'][$k] = $info[$imgname]['savepath'].'thumb_'.$k.'_'.$info[$imgname]['savename'];
					$image->open($rootpath.$origin_img);
					$image->thumb($v[0],$v[1])->save($rootpath.$ret['image']['thumb'][$k]);
				}
			}
		}
		return $ret;
	}
}

/**
 * 通用视频上传函数
 * @param String $imgname 上传文件域的NAME属性
 * @param type $dirname  上传文件存储目录
 * @return Array
 */
function upload_video($videoname,$dirname){
	if(isset($_FILES[$videoname]) && $_FILES[$videoname]['error'] == 0){
		$upload = new \Think\Upload();
		$rootpath = C('UPLOAD_ROOTPATH');
		$upload->savePath = $rootpath;
		$upload->maxSize = intval(C('VIDEO_MAXSIZE'))*1024*1024;
		$upload->exts = C('ALLOW_VIDEO_EXT');
		$upload->savePath = $dirname.'/';
		$info = $upload->upload(array($videoname=>$_FILES[$videoname]));
		if(!$info){
			return array('status'=>0,'error'=>$upload->getError());
		}else{
			$ret['status'] = 1;
			$ret['path'] = $origin_img = $info[$videoname]['savepath'].$info[$videoname]['savename'] ;
		}
		return $ret;
	}
}


/**
 * 声音上传函数
 * @param String $imgname 上传文件域的NAME属性
 * @param type $dirname  上传文件存储目录
 * @return Array
 */
function upload_voice($voicename,$dirname){
	if(isset($_FILES[$voicename]) && $_FILES[$voicename]['error'] == 0){
		$upload = new \Think\Upload();
		$rootpath = C('UPLOAD_ROOTPATH');
		$upload->savePath = $rootpath;
		$upload->maxSize = intval(C('VOICE_MAXSIZE'))*1024*1024;
		$upload->exts = C('ALLOW_VOICE_EXT');
		$upload->savePath = $dirname.'/';
		$info = $upload->upload(array($voicename=>$_FILES[$voicename]));
		if(!$info){
			return array('status'=>0,'error'=>$upload->getError());
		}else{
			$ret['status'] = 1;
			$ret['path'] = $origin_img = $info[$voicename]['savepath'].$info[$voicename]['savename'] ;
		}
		return $ret;
	}
}


/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}


/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 */
function ln_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是ln_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 */
function ln_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}


/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}


/**
 * 获取随机位数数字
 * @param integer $len 长度
 * @return string
 */
function randNumber($len = 4){
    $chars = str_repeat('0123456789', 10);
    $chars = str_shuffle($chars);
    $str = substr($chars, 0, $len);
    return $str;
}

/**
 * 手机格式验证
 * @param string $mobile 验证的手机号码
 * @return boolean
 */
function isMobile($mobile){
	if ( !empty($mobile) ) {
        if( preg_match("/^1[34578]\d{9}$/", $mobile) ){
            return true;
        }
	}
	return false;
}

/**
 * 验证 短信验证是否正确
 * @param unknown_type $code
 * @param unknown_type $mobile
 * create by 王之亮 2016-10-14
 */
function checkPhoneVerify($code,$mobile){
    $where['code'] = $code;
    $where['mobile'] = $mobile;
    $where['type'] = 0;
    $where['end_time'] = array('EGT', NOW_TIME);
    $shortMessage = M('MobileSendCode');
    $count = $shortMessage->where($where)->count();
    if ($count <= 0) {
        return false;
    }else{
        return true;
    }
}

/**
 * 电子邮箱格式验证
 * @param  string $email 验证的邮件地址
 * @return boolean
 */
function isEmail($email) {
    if ( !empty($email) ){
        if( preg_match('/^[a-z0-9]+([\+_\-\.]?[a-z0-9]+)*@([a-z0-9]+[\-]?[a-z0-9]+\.)+[a-z]{2,6}$/i', $email) ){
            return true;
        }
    }
    return false;
}

/**
 * 判断是否为合法的日期
 * @param string $date 验证的日期
 * @return boolean
 * by zhaojiping <QQ: 17620286>
 */
function validateDate($date) {
    $dateArr = explode("-", $date);
    if (is_numeric($dateArr[0]) === false || is_numeric($dateArr[1]) === false || is_numeric($dateArr[2]) === false) return false;

    if($dateArr[0] > 2050 || $dateArr[0] < 1900) return false;
    if($dateArr[1] > 12 || $dateArr[1] < 1) return false;

    if($dateArr[1] == 2){
        if($dateArr[0] % 100 == 0){
            if($dateArr[0] % 400 == 0){
                if($dateArr[2] > 29 || $dateArr[2] < 0) return false;
            }else{
                if($dateArr[2] > 28 || $dateArr[2] < 0) return false;
            }
        }else{
            if($dateArr[0] % 4 == 0){
                if($dateArr[2] > 29 || $dateArr[2] < 0) return false;
            }else{
                if($dateArr[2] > 28 || $dateArr[2] < 0) return false;
            }
        }
    }
    if($dateArr[1] == 1 || $dateArr[1] == 3 ||$dateArr[1] == 5 ||$dateArr[1] == 7
            ||$dateArr[1] == 8 ||$dateArr[1] == 10 ||$dateArr[1] == 12 ){
        if($dateArr[2] > 31 || $dateArr[2] < 1) return false;
    }else{
        if($dateArr[2] > 30 || $dateArr[2] < 1) return false;
    }
    return true;
}
/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'parent_id', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}


/**
 * 时间戳格式化
 * @param int $time
 * @return string 格式化后的时间字符串
 */
function time_format($time = NULL,$style='Y-m-d H:i:s'){
    if (empty($time)) {
        return '';
    }
    $time = $time === NULL ? '' : intval($time);
    return date($style, $time);
}


// 获取用户性别
function show_sex($sex) {
    switch ($sex) {
        case 0  : return    '<span class = "sex_0">先生</span>';   break;
        case 1  : return    '<span class = "sex_1">女士</span>';   break;
        case 2  : return    '<span class = "sex_2">保密</span>';   break;
        default : return    false;      break;
    }
}

function show_sex_text($sex) {
    switch ($sex) {
        case 0  : return    '先生';   break;
        case 1  : return    '女士';   break;
        case 2  : return    '保密';   break;
        default : return    false;      break;
    }
}

/**
 * 根据关键词[全拼音|简写拼音|汉字]进行数据检索
 * @param String $tableName 需要查询的表名
 * @param String $fields 需要查询的字段
 * @param String $keywords 关键词信息
 */
function getDataByKeywords($tableName='',$field='',$where = array()){
    if ($field == '')   $field = 'name';
    $keywords = trim(I('get.keywords',''));

    if($keywords == ''){
        exit(json_encode(V(0,'请输入关键字')));
    }
    if(preg_match("/^[a-zA-Z\s]+$/",$keywords))
        //关键词为拼音全拼或简拼时,查询条件为表字段(即pinyin字段)
        $where['pinyin'] = array('like','%'.$keywords.'%');
    else
        $where[$field] = array('like','%'.$keywords.'%');
    //取出表主键
    $pk = M($tableName)->getPk();
    //根据关键字所匹配的字段集合数组
    $matchKeys = M($tableName)->field($pk.','.$field.' as name')->where($where)->select();
    if(count($matchKeys) > 0){
        exit(json_encode(V(1,'',$matchKeys)));
    }else{
        exit(json_encode(V(0,'暂无匹配结果')));
    }
}


/**
 * 获取父节点的所有子节点, 包含父节点本身
 *
 * @param array  $table: 操作的表名
 * @param int    $parent_id: 父节点的ID
 * @param string $childIds: 内部传递ID使用, 外部调用时无需要传递
 * @return array 所有子节点的ID
 * create by zhaojiping QQ:17620286
 */
function getChildIds($table, $parent_id, &$childIds = ''){
    if ($childIds == '') $childIds = $parent_id;

    $ids = M($table)->where('parent_id in('. $parent_id .')')->getField('id',true);

    $ids = implode(',', $ids);
    // dump($ids);die;
    //未找到,返回已经找到的
    if ($ids){
        //添加到集合中
        $childIds .= ','. $ids; // 1,2,3,4
        //递归查找
        getChildIds($table, $ids, $childIds);
    }
    return explode(',', $childIds);
}

/**
 * 获取节点的所有父节点, 包含节点本身
 *
 * @param array  $table: 操作的表名
 * @param int    $id: 节点的ID
 * @param string $pIds: 内部传递ID使用, 外部调用时无需要传递
 * @return array 所有父节点的ID
 * create by liuyang  594353482
 */
function getParentIds($table, $id, &$pIds = ''){
    if ($pIds == '') $pIds = $id;

    $parent_ids = M($table)->where('id ='. $id)->getField('parent_id');
    //未找到,返回已经找到的
    if ($parent_ids > 0){
        //添加到集合中
        $pIds .= ','. $parent_ids; // 1,2,3,4
        //递归查找
        getParentIds($table, $parent_ids, $pIds);
    }
    return explode(',', $pIds);
}

/**
 * 获取图片缩略图 如果缩略图不存在则生成
 * @param string $filename 要生成缩略图的原图地址
 * @param int $width 生成缩略图的宽度
 * @param int $height 生成缩略图的高度
 * @return mixed 正常返回缩略图的地址
 * create by zhaojpiing QQ: 17620286
 */
function thumb($filename, $width=120, $height=120){
    if ($filename == '') {
        return '';
    }
    $info = pathinfo($filename);

    // 如果图片已经是缩略图, 直接返回
    $thumbFlag = '_' . $width .'_'. $height;
    $thumbFlagLen = strlen($thumbFlag);
    if (substr($info['filename'], -$thumbFlagLen) == $thumbFlag && file_exists($filename)) {
        return '/' . $filename;
    }

    $oldFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . '.' . $info['extension'];
    $thumbFile = $info['dirname'] . DIRECTORY_SEPARATOR . $info['filename'] . $thumbFlag .'.' . $info['extension'];

    $oldFile = str_replace('\\', '/', $oldFile);
    $thumbFile = str_replace('\\', '/', $thumbFile);

    $filename = ltrim($filename, '/');
    $oldFile = ltrim($oldFile, '/');
    $thumbFile = ltrim($thumbFile, '/');

    //如果原图不存在, 清除缩略图, 返回原图地址
    if (!file_exists($oldFile)) {
        @unlink($thumbFile);
        return '/' . $oldFile;
    }else if(file_exists($thumbFile)){ //缩图已存在, 直接返回缩略图
        return '/' . $thumbFile;
    }else{ //生成缩略图
        $oldimageinfo = getimagesize($oldFile);
        $old_image_width = intval($oldimageinfo[0]);
        $old_image_height = intval($oldimageinfo[1]);
        if ($old_image_width <= $width && $old_image_height <= $height) {
            @unlink($thumbFile);
            @copy($oldFile, $thumbFile);

            return '/' . $thumbFile;

        } else {
            $image = new \Think\Image();
            if ($old_image_width < $old_image_height) {
                $myHeight = $old_image_height * $width / $old_image_width;
                // 压缩
                $image->open($oldFile)->thumb($width, $myHeight, \Think\Image::IMAGE_THUMB_SCALE)->save($thumbFile, null, 100, false);
            } else {
                $myWidth = $old_image_width * $height / $old_image_height;
                // 压缩
                $image->open($oldFile)->thumb($myWidth, $height, \Think\Image::IMAGE_THUMB_SCALE)->save($thumbFile, null, 100, false);
            }

            if (intval($height) == 0 || intval($width) == 0) {
                exit('/' . $oldFile);
            }
            //dump($image);exit;
            // 再居中截取
            $image->open($thumbFile)->thumb($width, $height, \Think\Image::IMAGE_THUMB_CENTER)->save($thumbFile, null, 95, false);

            //缩图失败
            if (!$image) {
              $thumbFile = $oldFile;
            }

            return '/' . $thumbFile;
        }
    }
}


/**
 * 极光推送通用消息
 * @param unknown $alert  提示标题
 * @param unknown $type 信息类型
 * @param unknown $userId 用户id 可传数组
 * @param unknown $msg  信息内容
 * @param unknown $title  信息标题
*/
function jPush( $alert, $type, $userId = null, $msg = '') {
    require_once ('./Plugins/JPush/JPush.php');
    try {
        $client = new \JPush( C( 'USER_PUSH_APIKEY' ), C( 'USER_PUSH_SECRETKEY' ) );

        $extras = array (
                'type' => $type,
                'alert' => $alert,
                'content' => $msg
        );

        $client = $client->push();
        $client = $client->setPlatform( 'all' );
        $client = $client->setNotificationAlert( $alert );
        $client = $client->addIosNotification( $alert, 'default', null, null, null, $extras );
        //$client = $client->setMessage ( $alert, $alert, 'type', $extras );
        $client = $client->addAndroidNotification( $alert, $alert, null, $extras );
        $client = $client->setOptions( 100000, 3600, null, false ); //测试环境
        //$client = $client->setOptions ( 100000, 3600, null, true ); //生产环境
        if ($userId) {
            // $client = $client->addRegistrationId ( $registrationIds );
            $client->addAlias( $userId );
        } else {
            $client = $client->addAllAudience();
        }

        $result = $client->send();
        // echo 'Result=' . json_encode ( $result ) . $br;
        return json_encode( $result );
    }catch (Exception $e){
        return $e->getMessage();
    }
}
//无限极分类
function getTree($arr,$parent_id=0, &$tree = array()){
    foreach($arr as $key => $value){
        if($value['parent_id'] == $parent_id){
            $tree[] = $value;
            getTree($arr, $value['id'], $tree);
        }
    }

    return $tree;
}

// 获取是否
function show_bool($bool) {
    switch ($bool) {
        case 2  : return    '<span class = "bool_0">是</span>';   break;
        case 1  : return    '<span class = "bool_1">否</span>';   break;
        default : return    false;      break;
    }
}

function _show_bool($bool) {
    switch ($bool) {
        case 0  : return    '<span class = "bool_0">是</span>';   break;
        case 1  : return    '<span class = "bool_1">否</span>';   break;
        default : return    false;      break;
    }
}

function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
        $indexKeyIsNull            = (is_null($indexKey))?true :false;
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
        $result                         = array();
        foreach((array)$input as $key=>$row){
            if($columnKeyIsNumber){
                $tmp= array_slice($row, $columnKey, 1);
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
            }else{
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
            }
            if(!$indexKeyIsNull){
                if($indexKeyIsNumber){
                  $key = array_slice($row, $indexKey, 1);
                  $key = (is_array($key) && !empty($key))?current($key):null;
                  $key = is_null($key)?0:$key;
                }else{
                  $key = isset($row[$indexKey])?$row[$indexKey]:0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}

/**
 * 手机图片上传
 * @param $img: 旧图片地址
 * @param $obj: 上传的表单名称
 * @param $path: 上传的文件目录
 * @return mixed 上传成功, 返回上传的图片地址, 上传失败返加-1或0
 */
function app_upload_img($obj='photo', $img='',  $path='Visit', $uid=UID){
    if (isset($_FILES[$obj]['tmp_name']) && !empty($_FILES[$obj]['tmp_name'])) {

        // 旧图片地址得到图片名称
        $img = basename($img);
        if ($img == '' || empty($img) || $img == null) {
            $img = createFileName('jpg');
        }

        $createImgPath = '.'. C('UPLOAD_PICTURE_ROOT') .'/'. $uid ;
        if ( !is_dir($createImgPath) ) {
            mkdir($createImgPath);
        }

        if ($path != '') {
            $createImgPath = $createImgPath .'/'. $path;
            if ( !is_dir($createImgPath) ) {
                mkdir($createImgPath);
            }
        }

        $target_path = $createImgPath .'/'. $img ; //接收文件目录
        if (move_uploaded_file( $_FILES[$obj]['tmp_name'], $target_path )) {
            if (substr($target_path, 0, 1) == '.') {
                $target_path = substr($target_path, 1);
            }
            return $target_path;
        } else {
            return -1;
        }
    } else {
        return 0;
    }
}

/**
  * 生成文件扩展名, 如果没有传文件的名称
  * @param $ext: 生成文件默认文件名称
  */
function createFileName($ext='png'){
   return date('Ymd_His') .'_'. microtime(true)*10000 .'_' . rand(1000,9999) .'.' . $ext;
}

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

/**
 * 数组转xls格式的excel文件
 * @param  array  $data      需要生成excel文件的数组
 * @param  string $filename  生成的excel文件名
 *      示例数据：
        $data = array(
            array(NULL, 2010, 2011, 2012),
            array('Q1',   12,   15,   21),
            array('Q2',   56,   73,   86),
            array('Q3',   52,   61,   69),
            array('Q4',   30,   32,    0),
           );
 * @param  string  $subject    excel主题
 * @param  string  $title    excel标题
 * @param  array  $sheet    需要处理的单元格样式
 * @param  int  $count    excel数据行
 */
function create_xls($data,$filename='鲁南制药.xls',$subject='鲁南制药',$title='鲁南制药',$sheet=array(), $count = 0){
    ini_set('max_execution_time', '0');
    Vendor('PHPExcel.PHPExcel');
    $filename=str_replace('.xls', '', $filename);
    $phpexcel = new PHPExcel();
    $phpexcel->getProperties()
        ->setCreator("admin")
        ->setLastModifiedBy("admin")
        ->setTitle("鲁南制药")
        ->setSubject($subject)
        ->setDescription('')
        ->setKeywords($subject)
        ->setCategory("");
    $phpexcel->setActiveSheetIndex(0);
    $phpexcel->getActiveSheet()->freezePane('A2');//冻结首行
    foreach ($sheet as $key => $value) {
        //设置单元格宽度
        $phpexcel->getActiveSheet()->getColumnDimension($value)->setWidth(20);
        //设置标题行字体样式
        $phpexcel->getActiveSheet()->getStyle($value.'1')->getFont()->setName('微软雅黑');
        $phpexcel->getActiveSheet()->getStyle($value.'1')->getFont()->setSize(12);
        $phpexcel->getActiveSheet()->getStyle($value.'1')->getFont()->setBold(true);
    }
    $maxrow = $sheet[count($sheet)-1];
    //设置居中
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        
    //所有垂直居中
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        
    //设置单元格边框
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    //设置单元格格式为文本
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
    //设置自动换行
    $phpexcel->getActiveSheet()->getStyle('A1:'.$maxrow.$count)->getAlignment()->setWrapText(true);

    $phpexcel->getActiveSheet()->fromArray($data);
    $phpexcel->getActiveSheet()->setTitle($title);
    $phpexcel->setActiveSheetIndex(0);
    ob_end_clean();//清除缓冲区,避免乱码 
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    //多浏览器下兼容中文标题
    $encoded_filename =  urlencode($filename);
    $ua = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/IE/", $ua)) {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
       
    } else if (preg_match("/Firefox/", $ua)) {
        header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '.xls"');
    } else {
        header('Content-Disposition: attachment; filename="' . $encoded_filename . '.xls"');
         
    }
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0
    $objwriter = PHPExcel_IOFactory::createWriter($phpexcel, 'Excel5');
    $objwriter->save('php://output');
    exit;
}




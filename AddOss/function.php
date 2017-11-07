<?php

// 1 申请key和secret 创建Bucket需要注意的是要把读写权限改为： 公共读
// 2 /Application/Common/Conf/config.php
/*
'ALIOSS_CONFIG'          => array(
    'KEY_ID'             => '', // 阿里云oss key_id
    'KEY_SECRET'         => '', // 阿里云oss key_secret
    'END_POINT'          => '', // 阿里云oss endpoint
    'BUCKET'             => ''  // bucken 名称
    ),

'NEED_UPLOAD_OSS'        => array( // 需要上传的目录
    '/Upload/avatar',
    '/Upload/cover',
    '/Upload/image/webuploader',
    '/Upload/video',
    ),
*/
// 3 Alioss文件夹拷贝到/ThinkPHP/Library/Vendor/Alioss

/**
 * 实例化阿里云oos
 * @return object 实例化得到的对象
 */
function new_oss(){
    vendor('Alioss.autoload');
    $config=C('ALIOSS_CONFIG');

    $oss=new \OSS\OssClient($config['KEY_ID'],$config['KEY_SECRET'],$config['END_POINT']);
    return $oss;
}

/**
 * 上传文件到oss并删除本地文件
 * @param  string $path 文件路径
 * @return bollear      是否上传
 */
function oss_upload($path){
    // 获取bucket名称
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    // 先统一去除左侧的.或者/ 再添加./
    $oss_path=ltrim($path,'./');
    $path='./'.$oss_path;
    if (file_exists($path)) {
        // 实例化oss类
        $oss=new_oss();
        // 上传到oss
        $oss->uploadFile($bucket,$oss_path,$path);
        // 如需上传到oss后 自动删除本地的文件 则删除下面的注释
        // unlink($path);
        return true;
    }
    return false;
}

/**
 * 删除oss上指定文件
 * @param  string $object 文件路径 例如删除 /Public/README.md文件  传Public/README.md 即可
 */
function oss_delet_object($object){
    // 实例化oss类
    $oss=new_oss();
    // 获取bucket名称
    $bucket=C('ALIOSS_CONFIG.BUCKET');
    $test=$oss->deleteObject($bucket,$object);
}

/**
 * 获取oss的文件链接
 * @param  string $path 文件路径
 * @return string       http连接
 */
function get_url($path){
    // 如果是空；返回空
    if (empty($path)) {
        return '';
    }
    // 如果已经有http直接返回
    if (strpos($path, 'http://')!==false) {
        return $path;
    }
    // 判断是否使用了oss
    $alioss=C('ALIOSS_CONFIG');
    if (empty($alioss['KEY_ID'])) {
        return 'http://'.$_SERVER['HTTP_HOST'].$path;
    }else{
        return 'http://'.$alioss['BUCKET'].'.'.$alioss['END_POINT'].$path;
    }

}



/**
 * 上传文件类型控制 此方法仅限ajax上传使用
 * @param  string   $path    字符串 保存文件路径示例： /Upload/image/
 * @param  string   $format  文件格式限制
 * @param  integer  $maxSize 允许的上传文件最大值 52428800
 * @return booler   返回ajax的json格式数据
 */
function ajax_upload($path='file',$format='empty',$maxSize='52428800'){
    ini_set('max_execution_time', '0');
    // 去除两边的/
    $path=trim($path,'/');
    // 添加Upload根目录
    $path=strtolower(substr($path, 0,6))==='upload' ? ucfirst($path) : 'Upload/'.$path;
    // 上传文件类型控制
    $ext_arr= array(
            'image' => array('gif', 'jpg', 'jpeg', 'png', 'bmp'),
            'photo' => array('jpg', 'jpeg', 'png'),
            'flash' => array('swf', 'flv'),
            'media' => array('swf', 'flv', 'mp3', 'wav', 'wma', 'wmv', 'mid', 'avi', 'mpg', 'asf', 'rm', 'rmvb'),
            'file' => array('doc', 'docx', 'xls', 'xlsx', 'ppt', 'htm', 'html', 'txt', 'zip', 'rar', 'gz', 'bz2','pdf')
        );
    if(!empty($_FILES)){
        // 上传文件配置
        $config=array(
                'maxSize'   =>  $maxSize,               // 上传文件最大为50M
                'rootPath'  =>  './',                   // 文件上传保存的根路径
                'savePath'  =>  './'.$path.'/',         // 文件上传的保存路径（相对于根路径）
                'saveName'  =>  array('uniqid',''),     // 上传文件的保存规则，支持数组和字符串方式定义
                'autoSub'   =>  true,                   // 自动使用子目录保存上传文件 默认为true
                'exts'      =>    isset($ext_arr[$format])?$ext_arr[$format]:'',
            );
        // 实例化上传
        $upload=new \Think\Upload($config);
        // 调用上传方法
        $info=$upload->upload();
        $data=array();
        if(!$info){
            // 返回错误信息
            $error=$upload->getError();
            return V(0, $error);
        }else{
            // 返回成功信息
            foreach($info as $file){
                $ret['path']=trim($file['savepath'].$file['savename'],'.');
                $ret['path2'] = trim($file['savepath'],'.');               
                $to = dirname(dirname(dirname(dirname(__FILE__)))); 
                $name = md5(date('YmdHis')).".jpg";
                $from = $to.$ret['path'];
                //服务器安装ffmpeg后生成视频截图
                $str = "ffmpeg -i ".$from." -y -f mjpeg -ss 1 -t 1 -s 320x320 ".$to.$ret['path2'].$name;
                exec($str);

            // 暂时不上传到阿里   $ossvideo= oss_upload($ret['path']);
            //暂时不上传到阿里    $ret['osspath'] =get_url($ret['path']);
             //   $data['name'] =$ret['osspath'];
                $ossvideo= oss_upload($ret['path']);
                $ret['osspath'] =get_url($ret['path']);
                $data['nameosspath'] =$ret['osspath'];
                $data['name'] =$ret['path'];
                $data['imgpath'] =$ret['path2'].$name;

             //  $data['imgpathto'] = $to;
              //  $data['imgpathfrom'] = $from;
                return V(1, '成功',$data);
            }
        }
    }
}

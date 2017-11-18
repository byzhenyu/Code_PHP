# Ln_Public_Code_PHP

六牛用到的基础框架及通用组件常用组件著名使用方法
提交保证不会对其他版本造成影响

AddOSS 阿里云OSS使用方法
=================
上传者杜登峰  
第一步  
申请key和secret 创建Bucket需要注意的是要把读写权限改为： 公共读
  
第二步
  
/Application/Common/Conf/config.php 里添加以下配置
  

	'ALIOSS_CONFIG'    => array(
    'KEY_ID'          => '', // 阿里云oss key_id
    'KEY_SECRET'      => '', // 阿里云oss key_secret
    'END_POINT'       => '', // 阿里云oss endpoint
    'BUCKET'          => ''  // bucken 名称
    ),

	'NEED_UPLOAD_OSS'        => array( // 需要上传的目录
    '/Upload/avatar',
    '/Upload/cover',
    '/Upload/image/webuploader',
    '/Upload/video',
    ),
  
第三步
  
Alioss文件夹拷贝到/ThinkPHP/Library/Vendor/Alioss
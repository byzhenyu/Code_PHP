<?php

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX'    => 'ln_', // 缓存前缀
    'DATA_CACHE_TYPE'      => 'File', // 数据缓存类型
    
    'DEFAULT_MODULE'     => 'Admin',
    'MODULE_DENY_LIST'   => array('Common'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'liuniukeji', //默认数据加密KEY

    /* 调试配置 */
    'SHOW_PAGE_TRACE' => true,

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => false, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 1, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符
    'URL_HTML_SUFFIX'      => '', // 伪静态


    /* 数据库配置 */
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '118.190.21.60', // 服务器地址120.27.121.206
    'DB_NAME'   => 'ln_baseframe', // 数据库名
    'DB_USER'   => 'admin', // 用户名
    'DB_PWD'    => 'liuniukeji',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'ln_', // 数据库表前缀

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__ARTICLE__'    => __ROOT__ . '/'. APP_NAME . '/Article/Static',
        '__BASIC__'    => __ROOT__ . '/'. APP_NAME . '/Basic/Static',
        '__PUBLIC_IMG__'    => __ROOT__ . '/Public/Static/images',
        '__COMMON__'     => __ROOT__ . '/'. APP_NAME . '/Common/Static',
        '__HOME__'     => __ROOT__ . '/'. APP_NAME . '/Home/Static',
        '__MEMBER__'     => __ROOT__ . '/'. APP_NAME . '/Member/Static',
    ),

   	//文件上传路径
    'UPLOAD_ROOTPATH' => './Uploads/',

    //是否开启session
    'SESSION_AUTO_START' => true,

	/*图片上传*/
	'IMAGE_MAXSIZE' => '10M',
    'ALLOW_IMG_EXT' => array('jpg', 'pjpeg', 'bmp', 'gif', 'png', 'jpeg'),

	/*视频上传*/
	'VIDEO_MAXSIZE' => '100M',
    'ALLOW_VIDEO_EXT' => array('mp4','wma'),
	/*声音上传*/
	'VOICE_MAXSIZE' => '10M',
    'ALLOW_VOICE_EXT' => array('mp3'),

    // APP 传输是否加密 true 为加密 false为不加密
    'APP_DATA_ENCODE' => false,

    /* 图片上传相关配置 */
    'PICTURE_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 2*1024*1024, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,gif,png,jpeg', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/Picture/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）

    // 前台所用图片上传目录
    'UPLOAD_PICTURE_ROOT' => '/Uploads/Picture',

    /* 附件上传相关配置 */
    'FIELD_UPLOAD' => array(
        'mimes'    => '', //允许上传的文件MiMe类型
        'maxSize'  => 0, //上传的文件大小限制 (0-不做限制)
        'exts'     => 'jpg,png,gif,jpeg,doc,docx,ppt,pptx,pps,xls,xlsx,pot,vsd,rtf,wps,et,dps,pdf,txt', //允许上传的文件后缀
        'autoSub'  => true, //自动子目录保存文件
        'subName'  => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath' => './Uploads/UploadsField/', //保存根路径
        'savePath' => '', //保存路径
        'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'  => '', //文件保存后缀，空则使用原后缀
        'replace'  => false, //存在同名是否覆盖
        'hash'     => true, //是否生成hash编码
        'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
    ), //图片上传相关配置（文件上传类配置）
    'UPLOAD_FIELD_ROOT' => '/Uploads/UploadsField',

    'PAGE_SIZE' => 10, //page_size


    // 极光推送
    'USER_PUSH_APIKEY'    => 'b4888dfb01ca0bfdc6a17907',
    'USER_PUSH_SECRETKEY' => '891da667f2c4ed975dada00b',

    'LUNAN_ONLIEN_PROXY'=>0,//是否启用代理,用于无法访问网络的内网通过代理实现网络通讯
    'LUNAN_ONLIEN_PROXY_PORT'=>'8080',//代理端口
    'LUNAN_ONLIEN_PROXY_IP'=>'192.168.8.5',//代理IP

    'LUNAN_ONLIEN_OUT_IP'=>'60.213.49.35:6191',//外网地址
);

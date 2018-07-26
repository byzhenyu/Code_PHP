<?php
//防止SQL注入代码，请将该文件中前两段代码添加到自己项目index.php的最前面即可
//判断是否含有SQL注入并跳出
function sqlInj($value) {
    if (is_string($value)) {
        $arr =array('UPDATEXML','UPDATE','WHERE','EXEC','INSERT','SELECT','DELETE','COUNT','CHR','MID','MASTER','TRUNCATE','DECLARE','BIND','DROP'
        ,'CREATE',' EXP ','EXP%',' OR ','XOR',' LIKE ','NOTLIKE','NOT BETWEEN','NOTBETWEEN','BETWEEN','NOTIN','NOT IN','CONTACT','EXTRACTVALUE'
        ,'LOAD_FILE','INFORMATION_SCHEMA','INFORMATION_SCHEMA','outfile','%20','into','union');
        foreach ($arr as $a) {
            if (stripos($value, $a) !== false) exit(json_encode(array('status' => -1, 'info' => '参数错误，含有敏感字符' . $a, 'data' => array($a)), 0));
        }
    } elseif (is_array($value)) {
        foreach ($value as $v) {
            sqlInj($v);
        }
    }
}

//防止微信支付宝回调被屏蔽
if (stripos($_SERVER['PHP_SELF'], 'wxNotify') === false && stripos($_SERVER['PHP_SELF'], 'alipayNotify') === false)
    sqlInj($_REQUEST);//不是回调方法时执行防止SQL注入代码


if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define ( 'APP_DEBUG', true );

// APP 目录名称
define ('APP_NAME', 'Application');

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
define ( 'APP_PATH', './Application/' );

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/');

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';
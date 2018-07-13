<?php

//判断是否含有注入并跳出
function sqlInj($value) {
    $arr = explode('|', 'UPDATEXML|UPDATE|WHERE|EXEC|INSERT|SELECT|DELETE|COUNT|CHR|MID|MASTER|TRUNCATE|DECLARE|BIND|DROP|CREATE| EXP |EXP%| OR |XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|CONTACT|EXTRACTVALUE|LOAD_FILE|INFORMATION_SCHEMA|outfile|%20|into|union|user_gold|user_silver');

    if (is_string($value)) {
        foreach ($arr as $a) {
            if (stripos($value, $a) !== false) exit(json_encode(array('status'=>-1, 'info'=>'参数错误，含有敏感字符' . $a,'data'=>array($a)), 0));
        }
    } elseif (is_array($value)) {
        foreach ($value as $v) {
            sqlInj($v);
        }
    }
}

//过滤请求参数
foreach ($_REQUEST as $key => $value) {
    sqlInj($value);
}


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
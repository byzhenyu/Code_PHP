<?php

/**
 * 获取密码安全等级
 * @param string $password 登录密码  
 * @return int -1密码长度不足，1弱 2中 3高
 */
function checkLoginPassword($password) {     
    $password_strlen = strlen($password);
    if ($password_strlen < 8) {
        return -1;
    }
    if (preg_match('/^([0-9]{8,})$/', $password)) {         
        return 1;     
    } 
    else if(preg_match('/^[0-9 a-z]{8,}$/', $password)) {        
        return 2;     
    }
    else if(preg_match('/^[0-9 a-z A-Z !@#$%^&*]{8,}$/', $password)) {         
        return 3;     
    }     
    return 1; 
}
/**
 * 6位纯支付密码验证
 * @param string $password 支付密码  
 * @return array
 */
function checkPayPassword($password) {     
    if ($password == '123456') {
        return V(0, '该支付密码不可用');
    }
    if (!preg_match('/^([0-9]{6})$/', $password)) {         
        return V(0, '请设置正确的支付密码'); 
    } 
    return V(1, '设置成功'); 
}

?>
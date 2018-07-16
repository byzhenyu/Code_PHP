<?php
/**
 * Aes 加密
 * @param string $str     待加密串
 * @param string $user_id 用户标示id
 * @author chen
 * @return string  32位加密字符串
 */
function Ase_Encrypt($str = null,$user_id = 0) {
    $aes = new \Common\Tools\Aes();
    $str = $str.'-'.$user_id;
    return $aes->aes128cbcEncrypt($str);
}

/**
 * Aes 解密
 * @param string $str 32  加解密后字符串
 * @author chen
 * @return string  解密字符串
 */
function Ase_Decrypt($str = null) {
    $aes = new \Common\Tools\Aes();
    $str = $aes->aes128cbcHexDecrypt($str);
    $arr = @explode('-', $str);
    return floatval($arr[0]);
}

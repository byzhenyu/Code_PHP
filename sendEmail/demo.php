<?php

/**
 * @param $address 邮箱地址
 * @param $subject 发送主题
 * @param $content 发送内容
 */
function sendmail($address,$subject,$content){

    $email_smtp = "ssl://smtp.mxhichina.com";
    $email_username = "";  // 发送用户邮箱
    $email_password = "";  // 密码
    $email_from_name = ""; // 主题 类似签名

    require_once("class.phpmailer.php");
    require_once("class.smtp.php");
    $phpmailer=new \Phpmailer();
    // 设置PHPMailer使用SMTP服务器发送Email
    $phpmailer->IsSMTP();
    // 设置为html格式
    $phpmailer->IsHTML(true);
    // 设置邮件的字符编码'
    $phpmailer->CharSet='UTF-8';
    // 设置SMTP服务器。
    $phpmailer->Host=$email_smtp;
    // 设置为"需要验证"
    $phpmailer->SMTPAuth=true;
    // 设置用户名
    $phpmailer->Username=$email_username;
    // 设置密码
    $phpmailer->Password=$email_password;
    // 设置邮件头的From字段。
    $phpmailer->From=$email_username;
    // 设置发件人名字
    $phpmailer->FromName=$email_from_name;
    // 添加收件人地址，可以多次使用来添加多个收件人
    $phpmailer->SMTPSecure = 'ssl';
    $phpmailer->Port = 465;
    //发送改为465端口   ssl方式
        $phpmailer->AddAddress($address);
        // 设置邮件标题
        $phpmailer->Subject=$subject;
        // 设置邮件正文
        $phpmailer->Body=$content;
        // 发送邮件。

        // error_log(print_r($phpmailer->ErrorInfo,1),3,__FILE__.'.log');

        if(!$phpmailer->Send()) {
            $phpmailererror=$phpmailer->ErrorInfo;
            var_dump($phpmailererror);die;
            return array("status"=>0,"message"=>$phpmailererror);
        }else{
            return array("status"=>1,'message'=>'邮件已发送');
        }
    }

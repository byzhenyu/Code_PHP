<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo C('WEB_SITE_TITLE');?></title>
    <script>
        // 防止iframe
        if(self != top)
            top.location.replace(self.location);
    </script>
    <link rel="stylesheet" type="text/css" href="/Application/Common/Static/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Application/Member/Static/css/login.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/toastr/toastr.css" media="all">
    <style type="text/css">
        .verifycode {
            width: 220px;
            border-radius: 20px;
        }
    </style>
</head>
<body>
    <div class="main-header">
        <div class="main-container">
            <div class="logo"></div>
        </div>
    </div>
    <div class="main-container">
        <!-- 主体 -->
        <div class="login-body">
            <div class="login-header">
                <div class="login-title"><?php echo C('WEB_SITE_TITLE');?>登录</div>
            </div>
            <div class="login-main pr">
                <form action="<?php echo U('doLogin');?>" method="post" class="login-form">
                    <div class="item-box">
                        <div class="item">
                            <span>账号 : </span>
                            <input type="text" name="username" placeholder="请填写用户名或手机号" autocomplete="off" />
                        </div>
                        <div class="item">
                            <span>密码 : </span>
                            <input type="password" name="password" placeholder="请填写密码" autocomplete="off" />
                        </div>
                        <div class="item" style="width:348px;margin-left: 112px;">
                            <div class="verifycode" style="float:left;">
                                <span style="margin-left: -100px">验证码 : </span>
                                <input type="text" name="verify" class="verify_input" placeholder="请填写验证码" autocomplete="off" style="width:100px;">
                                <a class="reloadverify" title="换一张" href="javascript:void(0)">换一张？</a>
                            </div>
                            <span style="float: left;">
                                <img class="verifyimg reloadverify" style="width: 100px;height: 40px;" alt="点击切换" src="<?php echo U('Public/verify');?>">
                            </span>
                        </div>
                        <div style="clear:both;"></div>
                    </div>
                    <div class="item">
                        <input type="hidden" name="security_code" id="security_code" value="<?php echo ($security_code); ?>">
                        <span style="visibility: hidden;">占位 : </span>
                        <button class="login-btn" type="submit" style="margin-top: 25px;margin-left: -50px">登 录</button>
                        <div style="height: 40px;line-height: 40px;margin-top: 25px;float: right;"><a href="<?php echo U('forget_password');?>" target="_blank">忘记密码?</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="main-footer">
        <div><?php echo C(WEB_SITE_ICP);?></div>
    </div>
	<!--[if lt IE 9]-->
    <script type="text/javascript" src="/Public/jquery-1.10.2.min.js"></script>
    <!--[endif]-->
    <!--[if gte IE 9]-->
    <script type="text/javascript" src="/Public/jquery-2.0.3.min.js"></script>
    <!--[endif]-->
    <script type="text/javascript" src="/Public/assets/js/common.js"></script>
    <script type="text/javascript" src="/Public/assets/js/hex_sha1.js"></script>
    <script type="text/javascript" src="/Public/toastr/toastr.js" ></script>
    <script type="text/javascript">

    	/* 登陆表单获取焦点变色 */
        $(".login-form").on("focus", "input", function () {
            $(this).closest('input').addClass('focus');
        }).on("blur", "input", function () {
            $(this).closest('input').removeClass('focus');
        });

    	//表单提交
    	$(document)
	    	.ajaxStart(function(){
	    		$("button:submit").addClass("log-in").attr("disabled", true);
	    	})
	    	.ajaxStop(function(){
	    		$("button:submit").removeClass("log-in").attr("disabled", false);
	    	});

    	$("form").submit(function(){
    		var self = $(this);
            var username = $('input[name="username"]');
            var password = $('input[name="password"]');
            
            
            if (username.val().length < 4 || username.val().length >= 20 ) {
                toastr.error('用户名输入错误');
                username.focus();
                return false;
            }
            if (password.val().length < 4 || password.val().length > 44) {
                toastr.error('密码输入错误');
                password.focus();
                return false;
            }
            // 对发送出去的代码进行加密,  如果超过40位, 当做已经加密过, 不再加密
            if (password.val().length < 40) {
                var hex_password = hex_sha1(password.val())
                password.val( hex_password );
                // console.log( hex_password );
                // return false;
            }

    		$.post(self.attr("action"), self.serialize(), function(data){
                // console.log(data);
    			if(data.status == 1){
    				window.location.href = "<?php echo U('Home/Index/index');?>";
    			} else {
                    password.val('');
    				toastr.error(data.info);
    				//刷新验证码
    				$(".reloadverify").click();
    			}
    		}, "json");

            return false;
    	});

        function ResizeWindow(){
            h = $(window).height();
            w = $(window).width();

            $('body').height(h);
            $('.login-body').css('left', w/2 - $('.login-body').outerWidth()/2);
            $('.login-body').css('top', h/2 - $('.login-body').outerHeight()/2);

        }

		$(function(){

            ResizeWindow();
            $(window).resize(function(){
                ResizeWindow();
            })

			//初始化选中用户名输入框
			$("input[name='username']").focus();
			//刷新验证码
			var verifyimg = $(".verifyimg").attr("src");
            $(".reloadverify").click(function(){
                if( verifyimg.indexOf('?')>0){
                    $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
                }else{
                    $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
                }
            });

		});
    </script>
</body>
</html>
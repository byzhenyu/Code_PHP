<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo C('WEB_SITE_TITLE');?></title>
    <link rel="stylesheet" type="text/css" href="/Application/Common/Static/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Application/Common/Static/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/toastr/toastr.css" media="all">
    <!--[if lt IE 9]-->
    <script type="text/javascript" src="/Public/jquery-1.10.2.min.js"></script>
    <!--[endif]-->
    <script type="text/javascript" src="/Public/jquery-2.0.3.min.js"></script>
    
</head>
<body>
    <div class="main-header" style="border:0px solid red">
        <div class="main-container">
            <div class="logo fl" ></div>
            <a href="<?php echo U('/Member/Public/logout');?>" class="logout fr" title="安全退出"></a>
            <div class="nav-container fr">
                <ul id="main_nav">
                    <?php if(is_array($minaNav)): foreach($minaNav as $key=>$v): ?><li><a href="<?php echo U($v['url'], array('main_nav_id'=>$v['id']));?>" target="mainIframe"><span><?php echo ($v["title"]); ?></span></a></li><?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
    </div>
    <!-- 内容区 -->
    <div id="main_body" class="main-body" style="border: 0px solid blue">
        <iframe src="about:blank" name="mainIframe" id="mainIframe" style="height:100%; width:100%;" frameborder="0"></iframe>
    </div>
    <!-- /内容区 -->

    <script type="text/javascript" src="/Public/toastr/toastr.js" ></script>
    <script type="text/javascript" src="/Public/assets/plugins/layer-v2.0/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/assets/plugins/laydate-v1.1/laydate/laydate.js"></script>
    <script>
        function ResizeWindow(){
            windowHight = $(window).height();
            windowWidth = $(window).width();

            headerHeight = $('.main-header').outerHeight(true);
            // console.log(windowHight - headerHeight - footerHeight);
            $('#main_body, #mainIframe').height(windowHight - headerHeight - 9);

        }

        $(function(){
            //定位
            ResizeWindow();
            $(window).resize(function(){
                ResizeWindow();
            });

            $('#main_nav a').on('click', function(){
                $('#main_nav a').removeClass('main-nav-selected').find('span').removeClass('main-nav-selected-color');
                $(this).addClass('main-nav-selected').find('span').addClass('main-nav-selected-color');
            })

            // 默认点击主菜单的第一个
            $('#main_nav a span').eq(0).click();
        })
    </script>
    
</body>
</html>
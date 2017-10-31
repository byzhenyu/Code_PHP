<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1">
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?></title>
    <link rel="stylesheet" type="text/css" href="/Application/Common/Static/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Application/Common/Static/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/toastr/toastr.css" media="all">
    
	<style type="text/css">
		.content-list ul li a{padding: 5px 200px 5px 5px}
		.content-list ul li span{color: #a9a9a9;}
	</style>

    <!--[if lt IE 9]-->
    <script type="text/javascript" src="/Public/jquery-1.10.2.min.js"></script>
    <!--[endif]-->
    <script type="text/javascript" src="/Public/jquery-2.0.3.min.js"></script>
</head>
<body>
    <!-- 内容区 -->
    <div class="main-container cf" style="margin-top: 10px;">
        
            <div id="sub-nav" class="fl">
                <div class="sub-nav-top"></div>
                <?php echo W('Common/SubNav/getlist');?>
                <div class="sub-nav-bottom"></div>
            </div>
        
        <div class="fr">
            <div id="data" class="cf">
                
	<div class="content-list">
		<ul>
			<?php if(is_array($data)): foreach($data as $key=>$v): ?><li>
					<div class="content-list-title fl">
						<?php if($v['url'] != ''): ?><a href="<?php echo ($v['url']); ?>" title="<?php echo ($v['name']); ?>">
						<?php else: ?>
							<a href="<?php echo U('Home/Index/articleDetail', array('article_id' => $v['id'], 'current_id' => $current_id, 'main_nav_id' => $main_nav_id));?>" title="<?php echo ($v['name']); ?>"><?php endif; ?>
						
							<?php echo ($v['name']); ?>
						</a>
					</div>
					<div class="content-list-time fr" style="color: #404040;">
						<?php echo (time_format($v['add_time'],'Y-m-d')); ?>
					</div>
				</li><?php endforeach; endif; ?>
		</ul>
	</div>
	<div class="page text-center">
		<?php echo ($page); ?>
	</div>

            </div>
        </div>
    </div>
    <!-- /内容区 -->

    <div class="main-footer" style="border:0px solid red">
        <div>
            <?php echo C('WEB_SITE_ICP');?>
        </div>
    </div>

    <script type="text/javascript" src="/Public/assets/js/common.js" ></script>
    <script type="text/javascript" src="/Public/toastr/toastr.js" ></script>
    <script type="text/javascript" src="/Public/assets/plugins/layer-v2.0/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/assets/plugins/laydate-v1.1/laydate/laydate.js"></script>
    <script>
        // 定义全局变量
        RECYCLE_URL = "<?php echo U('recycle');?>"; // 默认逻辑删除操作执行的地址
        RESTORE_URL = "<?php echo U('restore');?>"; // 默认逻辑删除恢复执行的地址
        DELETE_URL = "<?php echo U('del');?>"; // 默认删除操作执行的地址
        UPLOAD_IMG_URL = "<?php echo U('uploadImg');?>"; // 默认上传图片地址
        DELETE_FILE_URL = "<?php echo U('delFile');?>"; // 默认删除图片执行的地址
        CHANGE_STAUTS_URL = "<?php echo U('changeDisabled');?>"; // 修改数据的启用状态
    </script>
    <script type="text/javascript">
        function ResizeWindow(){
            documentHeight = $(document).height();
            windowHeight = $(window).height();
            if (documentHeight <= windowHeight) {
                $(".main-footer").css({'position':'fixed', 'bottom':'0'});
            } else {
                $(".main-footer").css({'position':'static'});
            }

        }

        $(function(){
            //定位
            ResizeWindow();
            $(window).resize(function(){
                ResizeWindow();
            });
        })
    </script>
    
	<script type="text/javascript">
	</script>

    <!-- 隐藏域存储分页数据，模拟排名，在线考试团队，子页面跳转主页面使用 -->
    <input type="hidden" id="p_div_ranking" value="1"/>
    <input type="hidden" id="p_div_team" value="1"/>
</body>
</html>
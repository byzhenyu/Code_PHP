<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?></title>
    <link rel="stylesheet" type="text/css" href="/Application/Admin/Static/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Application/Admin/Static/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Application/Admin/Static/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/assets/css/style.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Application/Admin/Static/css/default_color.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/toastr/toastr.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/dropdownlist/dropdownlist.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/layui/css/layui.css" media="all">
    <script type="text/javascript" src="/Public/layui/layui.js"></script>
    <!--[if lt IE 9]-->
    <script type="text/javascript" src="/Application/Admin/Static/js/jquery-1.10.2.min.js"></script>
    <!--[endif]-->
    <script type="text/javascript" src="/Application/Admin/Static/js/jquery-2.0.3.min.js"></script>
    
</head>
<body>
    
    <!-- 内容区 -->
    <div id="content">
        
	<!-- 标题栏 -->
	<div class="tab-wrap" style="margin: 10px;" >
		<ul class="tab-nav nav">
			<li class="current"><a href="<?php echo U('adminSetting');?>">个人信息设置</a></li>
		</ul>
		<div class="tab-content">
			<!-- 修改密码表单 -->
			<form action="<?php echo U();?>" method="post" class="form-horizontal ajaxForm">
		        <div class="form-item">
		            <label class="item-label">真实姓名<span class="check-tips"><b>*</b></span></label>
		            <div class="controls">
		                <input type="text" class="text input-5x" name="real_name" value="<?php echo ((isset($info["real_name"]) && ($info["real_name"] !== ""))?($info["real_name"]):''); ?>" placeholder="请输入您的真实姓名">
		            </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">登录密码<span class="check-tips">(如果不修改请留空)</span></label>
		            <div class="controls">
		      			<input type="password" class="text input-4x" name="password" placeholder="4位及以上字符">
		      			<input type="password" class="text input-4x" name="re_password" placeholder="确认登录密码">
		            </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">电子邮箱</label>
		            <div class="controls">
		                <input type="text" class="text input-large" name="email" value="<?php echo ((isset($info["email"]) && ($info["email"] !== ""))?($info["email"]):''); ?>" >
		            </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">生日</label>
		            <div class="controls">
		                <input class="text input-4x" name="birthday" onclick="laydate()" value="<?php echo (date('Y-m-d',$info["birthday"])); ?>" >
		            </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">性别</label>
		            <div class="controls">
		                <label class="wf-form-label-rc">
                        	<input type="radio" value="0" name="sex" checked="checked" id="isMan" > 先生
                        </label>
                        <label class="wf-form-label-rc">
                            <input type="radio" value="1" name="sex" id="isWomen" > 女士
                        </label>
                        <?php if($info['sex'] == 1): ?><script>
                        		$('#isWomen').attr('checked', 'checked');
                        	</script><?php endif; ?>
		            </div>
		        </div>

		        <div class="form-item">
		            <label class="item-label">头像 <span class="check-tips"> <b>*</b> (用户头像, 请上传大于400*400的正方形图片)</span></label>
		            <div class="controls">
		                <div>
							<img src="<?php echo ($info["photo_path"]); ?>" style="height:129px; width:129px;" id="img_"/>
						<div>
		                <input type="hidden" value="<?php echo ($info["photo_path"]); ?>" name="photo_path" id="img"/>
						<input class="btn btn-default btn-xs" type="button" value="上传" id="btnUpload" />
						<input class="btn btn-danger btn-xs" type="button" value="删除" onclick="delFile($('#img').val(), '')" id="btn_delete_" />
						<?php if($info["photo_path"] == ''): ?><script>
								$("#img_, #btn_delete_").hide();
							</script><?php endif; ?>
		            </div>
		        </div>
		        <div class="form-item">
		            <label class="item-label">个性签名<span class="check-tips"></span></label>
		            <div class="controls">
		      			<textarea name="remark" rows="5" cols="57"><?php echo ((isset($info["remark"]) && ($info["remark"] !== ""))?($info["remark"]):''); ?></textarea> 
		            </div>
		        </div>

	            <div class="tw-tool-bar-bot">
	                <button type="submit" class="tw-act-btn-confirm">提交</button>
	            </div>
		    </form>
		</div>
	</div>

    </div>
    <!-- /内容区 -->
    <!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Application/Admin/Static/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    <script type="text/javascript" src="/Public/toastr/toastr.js" ></script>
    <script type="text/javascript" src="/Public/assets/js/wf-list.js" ></script>
    <script type="text/javascript" src="/Public/assets/plugins/layer-v2.0/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/assets/plugins/laydate-v1.1/laydate/laydate.js"></script>
    <script type="text/javascript" src="/Public/assets/js/common.js"></script>
    <script type="text/javascript" src="/Public/dropdownlist/dropdownlist.js"></script>
    <script type="text/javascript" src="/Application/Admin/Static/js/common.js"></script>
    <script type="text/javascript" src="/Public/layui/layui.js"></script>
    <script>
        // 定义全局变量
        RECYCLE_URL = "<?php echo U('recycle');?>"; // 默认逻辑删除操作执行的地址
        RESTORE_URL = "<?php echo U('restore');?>"; // 默认逻辑删除恢复执行的地址
        DELETE_URL = "<?php echo U('del');?>"; // 默认删除操作执行的地址
        UPLOAD_IMG_URL = "<?php echo U('uploadImg');?>"; // 默认上传图片地址
        UPLOAD_FIELD_URL = "<?php echo U('uploadField');?>"; // 默认上传图片地址
        DELETE_FILE_URL = "<?php echo U('delFile');?>"; // 默认删除图片执行的地址
        CHANGE_STAUTS_URL = "<?php echo U('changeDisabled');?>"; // 修改数据的启用状态
    </script>
    
    <script type="text/javascript" charset="utf-8" src="/Public/ajaxupload.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Application/Common/Static/js/imgupload.js"></script>
    <script type="text/javascript" src="/Public/assets/js/hex_sha1.js"></script>
	<script>
		$(function(){
			ajaxUpload('#btnUpload', '#img', 'Member', '');
        })

		function validate(){
        	var password = $('input[name="password"]');
        	var re_password = $('input[name="re_password"]');

        	if ($.trim(password.val()) != '' || $.trim(re_password.val()) != '') {
	        	if (password.val().length < 4 || password.val().length > 40) {
	                toastr.error('密码长度4-20位');
	                password.focus();
	                return false;
	            }
	            if (password.val() != re_password.val()) {
	            	toastr.error('两次登录密码不相同');
	            	password.focus();
	            	return false;
	            }
	            // 对发送出去的代码进行加密,  如果超过40位, 当做已经加密过, 不再加密
	            if (password.val().length < 40 ) {
	                password.val( $.trim( hex_sha1( password.val() ) ) );
	                re_password.val( $.trim( hex_sha1( re_password.val() ) ) );
	                //console.log(hex_sha1(password.val()));
	            }
	        }
	    }
	</script>

</body>
</html>
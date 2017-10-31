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
    
	<link rel="stylesheet" type="text/css" href="/Application/Admin/Static/css/multiple-select.css" media="all">

</head>
<body>
    
    <!-- 内容区 -->
    <div id="content">
        
	<div class="tw-layout">
		<div class="tw-list-hd">
			<?php echo isset($info['id'])?'编辑':'新增';?>用户
		</div>
		<form action="<?php echo U();?>" method="post" class="ajaxForm">
			<div class="tw-list-wrap tw-edit-wrap">
	            <table class="wf-form-table">
	                <colgroup>
	                    <col width="15%">
	                    <col width="35%">
	                    <col width="15%">
	                    <col width="35%">
	                </colgroup>
	                <tbody>
                            <tr>
                                <th colspan="4" class="information"><div class="fl offset">用户信息</div></th>
                            </tr>
                            
                            <tr>
	                        <th><em>*</em>用户名:</th>
	                        <td>
	                            <input type="text" class="text input-5x" name="member[username]" value="<?php echo ($info['username']); ?>" placeholder="4-20位字母或数字, 首字符必须为字母" id="username"
		                            <?php if($info["id"] > 0): ?>disabled="disabled" readonly="true"<?php endif; ?>
		                        >
	                        </td>
	                        <th><em>*</em>真实姓名:</th>
	                        <td>
	                            <input type="text" class="text input-5x" name="member[real_name]" value="<?php echo ($info['real_name']); ?>" placeholder="用户真实姓名">
	                        </td>
	                    </tr> 
	                    <tr>
	                        <th><em>*</em>手机号:</th>
	                        <td>
	                            <input type="text" class="text input-5x" name="member[phone]" value="<?php echo ($info['phone']); ?>" maxlength="11" onpaste="this.value=this.value.replace(/[^\d]/g,'')" onkeyup="this.value=this.value.replace(/[^\d]/g,'')" placeholder="11位手机号码, 可用于登录">
	                        </td>
	                        <th>电子邮箱:</th>
	                        <td>
	                            <input type="text" class="text input-5x" name="member[email]" value="<?php echo ($info['email']); ?>" placeholder="用户电子邮箱">
	                        </td>
	                    </tr>
	                    <tr>
	                        <th>
	                        	<?php if($info['id'] <= 0): ?><em>*</em><?php endif; ?>登录密码:
	                        </th>
	                        <td>
	                            <input type="password" id="password" class="text input-5x" name="member[password]" value=""
	                            	placeholder = "<?php if($info["id"] <= 0): ?>用户登录密码, 4位及以上<?php else: ?>如果不修改, 请留空(4位以上字符)<?php endif; ?>">
	                        </td>
	                        <th>
	                        	<?php if($info['id'] <= 0): ?><em>*</em><?php endif; ?>确认登录密码:
	                        </th>
	                        <td>
	                            <input type="password" id="re_password" class="text input-5x" name="member[re_password]" value="" placeholder="再次输入登录密码">
	                        </td>
	                    </tr>

	                    <tr>
	                        <th>
	                        	<?php if($info['id'] <= 0): ?><em>*</em><?php endif; ?>验证码密码:
	                        </th>
	                        <td>
	                            <input type="password" id="pay_password" class="text input-5x" name="member[pay_password]" value=""
	                            	placeholder = "<?php if($info["id"] <= 0): ?>用户验证码密码, 6位数字<?php else: ?>如果不修改, 请留空(6位数字)<?php endif; ?>">
	                        </td>
	                        <th>
	                        	<?php if($info['id'] <= 0): ?><em>*</em><?php endif; ?>确认验证码密码:
	                        </th>
	                        <td>
	                            <input type="password" id="re_pay_password" class="text input-5x" name="member[re_pay_password]" value="" placeholder="再次输入验证码密码">
	                        </td>
	                    </tr>
	                    <tr>
	                        <th><em>*</em>岗位状态:</th>
	                        <td>
                                <select name="member[post_status]" id="post_status">
		                            <option value="">--请选择岗位状态--</option>
		                            <option value="1">实习</option>
		                            <option value="2">在岗</option>
		                            <option value="3">待岗</option>
		                            <option value="4">离职</option>
		                        </select>
		                        <script>
                            		$('#post_status').val("<?php echo ((isset($info["post_status"]) && ($info["post_status"] !== ""))?($info["post_status"]):''); ?>");
                            	</script>
	                        </td>
	                        <th><em>*</em>权限分组:</th>
	                        <td>
								<select name="member_group_id" id="member_group_id">、
		                            <option value="">--请选择分组--</option>
		                            <?php if(is_array($authGroupList)): $i = 0; $__LIST__ = $authGroupList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
		                        </select>
		                        <script>
                            		$('#member_group_id').val("<?php echo ((isset($info["member_group_id"]) && ($info["member_group_id"] !== ""))?($info["member_group_id"]):''); ?>");
                            	</script>
	                        </td>
	                    </tr>
	                    <tr>
                                <th>用户财务编号:</th>
	                        <td>
                                    <input class="text input-5x" name="member[financial_code]" value="<?php echo ($info["financial_code"]); ?>" placeholder="请输入用户财务编号">
	                        </td>
                                    <th>用户档案号:</th>
	                        <td>
                                    <input class="text input-5x" name="member[file_no]" value="<?php echo ($info["file_no"]); ?>" placeholder="请输入用户档案号">
	                        </td>
	                    </tr>

	                    <tr>
	                    	<th>用户所属角色:</th>
	                    	<td colspan="3">
                                    <select id="role_member" multiple="multiple">
	                                	<?php if(is_array($roleList)): $i = 0; $__LIST__ = $roleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" ><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							        </select>
							        <input type="hidden" name="memberRole" id="memberRole"></input>
	                    	</td>
	                    </tr>
	                    <tr>
                                <th>后台登录权限:</th>
	                        <td>
	                        	<label class="wf-form-label-rc">
	                            	<input type="radio" value="0" name="member[isAdmin]" id="isAdminTure"> 可登录后台
	                            </label>
	                            <label class="wf-form-label-rc">
		                            <input type="radio" value="1" name="member[isAdmin]" id="isAdminFalse" checked="checked"> 不可登录后台
	                            </label>
	                            <?php if($info['isAdmin'] == 1): ?><script>
	                            		$('#isAdminFalse').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                            <?php if($info['isAdmin'] == 0): ?><script>
	                            		$('#isAdminTure').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                            <?php if($info['username'] == 'admin'): ?><script>
	                            		$('#isAdminFalse,#isAdminTure').attr('disabled', 'disabled');
	                            	</script><?php endif; ?>
	                        </td>
		        			<th>前台登录权限:</th>
	                        <td>
	                        	<label class="wf-form-label-rc">
	                            	<input type="radio" value="0" name="member[isUser]" id="isUserTure" checked="checked"> 可登录前台
	                            </label>
	                            <label class="wf-form-label-rc">
		                            <input type="radio" value="1" name="member[isUser]" id="isUserFalse" > 不可登录前台
	                            </label>
	                            <?php if($info['isUser'] == 1): ?><script>
	                            		$('#isUserFalse').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                        </td>
	                     </tr>
	                    <tr>
	                    <tr>
                                    <th>是否启用:</th>
	                        <td>
	                        	<label class="wf-form-label-rc">
	                            	<input type="radio" value="0" name="member[disabled]" checked="checked" id="disabledTrue"> 启用
	                            </label>
	                            <label class="wf-form-label-rc">
		                            <input type="radio" value="1" name="member[disabled]" id="disbledFalse"> 禁用
	                            </label>
	                            <?php if($info['disabled'] == 1): ?><script>
	                            		$('#disbledFalse').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                            <?php if($info['username'] == 'admin'): ?><script>
	                            		$('#disabledTrue,#disbledFalse').attr('disabled', 'disabled');
	                            	</script><?php endif; ?>
	                        </td>
                                    <th>客服人员</th>
	                        <td>
                                    <label class="wf-form-label-rc">
                                    <input type="radio" value="0" name="member[isService]" id="isServiceTrue"> 是
	                            </label>
	                            <label class="wf-form-label-rc">
                                        <input type="radio" value="1" name="member[isService]" checked="checked" id="isServiceFalse"> 否
	                            </label>
	                            <?php if($info['isService'] == 1): ?><script>
	                            		$('#isServiceFalse').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                            <?php if($info['username'] == 'admin'): ?><script>
	                            		$('#isServiceTrue,#isServiceFalse').attr('disabled', 'disabled');
	                            	</script><?php endif; ?>
	                        </td>
	                     </tr>
	                    <tr>
                            <th>用户备注:</th>
	                        <td colspan="3">
                                <textarea type="text" rows="2" cols="57" name="member[remark]" placeholder="用户的备注, 最长150个字符"><?php echo ($info['remark']); ?></textarea>
	                        </td>
                        </tr>
	                    <tr>
                            <tr>
                                <th colspan="4" class="information"><div class="fl offset">其他信息</div></th>
                            </tr>
	                    <tr>
                                <th>性别:</th>
	                        <td>
		                        <label class="wf-form-label-rc">
	                            	<input type="radio" value="0" name="member[sex]" checked="checked" id="isMan" > 先生
	                            </label>
	                            <label class="wf-form-label-rc">
		                            <input type="radio" value="1" name="member[sex]" id="isWomen" > 女士
	                            </label>
	                            <?php if($info['sex'] == 1): ?><script>
	                            		$('#isWomen').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                        </td>

                                <th>生日</th>
                                <td>
                                    <input type="text" id="birthday" class="text input-3x laydate-icon" name="member[birthday]"  onclick="laydate({format: 'YYYY-MM-DD', istime:false, festival: true})" value="<?php echo (time_format($info["birthday"],'Y-m-d')); ?>" placeholder="请选择生日">
                                </td>
                            </tr>   
                            <tr>
                                <th>身高:</th>
	                        <td>
                                    <input class="text input-5x" name="member[height]" value="<?php echo ($info['height']); ?>" placeholder="请输入用户身高">
	                        </td>
                                <th>民族:</th>
	                        <td>
                                    <input class="text input-5x" name="member[nation]" value="<?php echo ($info['nation']); ?>" placeholder="请输入用户民族">
	                        </td>
	                    </tr>
                            <tr>
                                <th>身份证号:</th>
	                        <td>
                                    <input class="text input-5x" name="member[card_id]" value="<?php echo ($info['card_id']); ?>" placeholder="请输入用户身份证号">
	                        </td>
                                <th>身份证住址:</th>
	                        <td>
                                    <input class="text input-5x" name="member[card_address]" value="<?php echo ($info['card_address']); ?>" placeholder="请输入用户身份证住址">
	                        </td>
	                    </tr>
                            <tr>
                                <th>现家庭住址:</th>
	                        <td>
                                    <input class="text input-5x" name="member[now_family_address]" value="<?php echo ($info['now_family_address']); ?>" placeholder="请输入用户现家庭住址">
	                        </td>
                                <th>籍贯:</th>
	                        <td>
                                    <input class="text input-5x" name="member[origin_place]" value="<?php echo ($info['origin_place']); ?>" placeholder="请输入用户籍贯">
	                        </td>
	                    </tr>
                            <tr>
                                <th>邮编:</th>
	                        <td>
                                    <input class="text input-5x" name="member[zipcode]" value="<?php echo ($info['zipcode']); ?>" placeholder="请输入用户邮编">
	                        </td>
                                <th>家庭电话:</th>
	                        <td>
                                    <input class="text input-5x" name="member[family_phone]" value="<?php echo ($info['family_phone']); ?>" placeholder="请输入用户家庭电话">
	                        </td>
	                    </tr>
                            <tr>
                               <th>政治面貌:</th>
                               <td colspan="3">
                                    <label class="wf-form-label-rc">
                                        <input type="radio" value="0" name="member[political_landscape]" id="political_people" checked="checked"> 群众
	                            </label>
	                            <label class="wf-form-label-rc">
                                        <input type="radio" value="1" name="member[political_landscape]" id="political_member"> 团员
	                            </label>
	                            <label class="wf-form-label-rc">
                                        <input type="radio" value="2" name="member[political_landscape]" id="political_party"> 党员
	                            </label>
	                            <?php if($info['political_landscape'] == 1): ?><script>
                                            $('#political_member').attr('checked', 'checked');
	                            	</script>
                                    <elseif condition="$info['political_landscape'] eq 2">
	                            	<script>
                                            $('#political_party').attr('checked', 'checked');
	                            	</script><?php endif; ?>
	                        </td>
	                    </tr>
	                    <tr>
                                <th>用户头像:</th>
	                        <td colspan="3">
                                    <div>
                                        <img src="<?php echo ($info["photo_path"]); ?>" style="height:129px; width:129px;" id="img_"/>
                                    <div>
                                    <input type="hidden" value="<?php echo ($info["photo_path"]); ?>" name="member[photo_path]" id="img"/>
                                    <input class="btn btn-default btn-xs" type="button" value="上传" id="btnUpload" />
                                    <input class="btn btn-danger btn-xs" type="button" value="删除" onclick="delFile($('#img').val(), '')" id="btn_delete_" />
                                    <?php if($info["photo_path"] == ''): ?><script>
                                            $("#img_, #btn_delete_").hide();
                                        </script><?php endif; ?>
                                    <span class="check-tips">（用户头像, 请上传大于400*400的正方形图片）</span>
	                        </td>
	                    </tr>
                            <tr>
                                <th>获得的荣誉:</th>
                                <td colspan="3">
                                    <textarea type="text" rows="2" cols="57" name="member[honor]" placeholder="用户获得的荣誉, 最长150个字符"><?php echo ($info['honor']); ?></textarea>
	                        </td>
                            </tr>
                            <tr>
                                <th>个人爱好:</th>
                                <td colspan="3">
                                    <textarea type="text" rows="2" cols="57" name="member[hobby]" placeholder="用户的个人爱好, 最长150个字符"><?php echo ($info['hobby']); ?></textarea>
	                        </td>
	                    </tr>
	                </tbody>
	            </table>
	        </div>
	        <div class="tw-tool-bar-bot">
                <input type="hidden" name="id" value="<?php echo ($info['id']); ?>" >
                <button type="submit" class="tw-act-btn-confirm"  >提交</button>
                <button type="button" onclick="goback()" class="tw-act-btn-cancel">返回</button>
            </div>
        </form>
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
    <script type="text/javascript" src="/Application/Admin/Static/js/multiple-select.js"></script>
	<script>
		
        $(function(){
            ajaxUpload('#btnUpload', '#img', 'Member', '');
        })
    	
	    $('#role_member').multipleSelect({
	    	placeholder: "请选择角色",
	        width: '600px',
	        onCheckAll: function() {
                var ids = $("#role_member").multipleSelect("getSelects");
                $('#memberRole').val(ids);
            },
            onUncheckAll: function() {
                var ids = $("#role_member").multipleSelect("getSelects");
                $('#memberRole').val(ids);
            },
            onOptgroupClick: function(view) {
                var ids = $("#role_member").multipleSelect("getSelects");
                $('#memberRole').val(ids);
            },
            onClick: function(view) {
                var ids = $("#role_member").multipleSelect("getSelects");
                $('#memberRole').val(ids);
            }
	    });
        // 密码验证和加密
        function validate(){
        	var password = $('input[name="member[password]"]');
        	var re_password = $('input[name="member[re_password]"]');

        	var pay_password = $('input[name="member[pay_password]"]');
        	var re_pay_password = $('input[name="member[re_pay_password]"]');

        	if ($.trim(password.val()) != '' || $.trim(re_password.val()) != '') {
	        	if (password.val().length < 4 || password.val().length > 20) {
	                toastr.error('密码长度4-20位');
	                $('#password').val('');
			        $('#re_password').val('');
	                password.focus();
	                return false;
	            }
	            if (password.val() != re_password.val()) {
	            	toastr.error('两次登录密码不相同');
	            	$('#password').val('');
			        $('#re_password').val('');
	            	password.focus();
	            	return false;
	            }
        	}

        	if ($.trim(pay_password.val()) != '' || $.trim(re_pay_password.val()) != '') {
        		if (pay_password.val().length != 40) {
		        	if (pay_password.val().length == 6 ) {
		        		if ( /^\d+$/.test(pay_password.val()) == false ) {
		        			toastr.error('验证码密码必须为6位纯数字');
		        			$('#pay_password').val('');
			        		$('#re_pay_password').val('');
		                	pay_password.focus();
		                	return false;
		        		}
		            } else {
		            	toastr.error('验证码密码必须为6位数字');
		            	$('#pay_password').val('');
			        	$('#re_pay_password').val('');
		                pay_password.focus();
		                return false;
		            }
        		}

	            if (pay_password.val() != re_pay_password.val()) {
	            	toastr.error('两次验证码密码不相同');
	            	$('#pay_password').val('');
			        $('#re_pay_password').val('');
	            	pay_password.focus();
	            	return false;
	            }
        	}

        	if ($.trim(password.val()) != '' || $.trim(re_password.val()) != '') {
	            // 对发送出去的代码进行加密,  如果超过40位, 当做已经加密过, 不再加密
	            if (password.val().length < 40 ) {
	                password.val( $.trim( hex_sha1( password.val() ) ) );
	                re_password.val( $.trim( hex_sha1( re_password.val() ) ) );
	                //console.log(hex_sha1(password.val()));
	            }
        	}

        	if ($.trim(pay_password.val()) != '' || $.trim(re_pay_password.val()) != '') {
	            // 对发送出去的代码进行加密,  如果超过40位, 当做已经加密过, 不再加密
	            if (pay_password.val().length == 6 ) {
	                pay_password.val( hex_sha1( $.trim(pay_password.val() ) ) );
	                re_pay_password.val( hex_sha1( $.trim(re_pay_password.val() ) ) );
	                //console.log(hex_sha1(password.val()));
	            }
        	}
        }
        $('.show-img').click(function(){
            layer.open({
                type: 1,
                title: false,
                closeBtn: 0,
                skin: 'layui-layer-nobg', //没有背景色
                shadeClose: true,
                content:"<img src="+$(this).attr('src')+">"
            });
        });
        function callback(data){
	        if(data.status == 1){
	        	_IS_SUBMIT_SUCCESS = true;

	            if (data.info != '' && typeof(data.info) != 'undefined')  toastr.success(data.info);;
	            //跳转页面
	            if ( typeof(_TARGET_URL) != 'undefined' && _TARGET_URL != '') {
	                window.location.href = _TARGET_URL;
	            }
	            //刷新页面
	            if ( typeof(_NEED_REFRESH) != 'undefined' && _NEED_REFRESH == true) {
	                location.reload();
	            }
	        } else {
	            if (data.info != '' && typeof(data.info) != 'undefined') 
	            	toastr.error(data.info);
	            else  
	            	toastr.error('未定义错误!');
	        }
	        $('#password').val('');
	        $('#re_password').val('');
	        $('#pay_password').val('');
	        $('#re_pay_password').val('');
        }
    </script>

</body>
</html>
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
        

<div class="tw-layout">
		<div class="tw-list-hd">
			权限管理
		</div>
		<div class="tw-list-top">
			<div class="tw-tool-bar">
				<a class="layui-btn layui-btn-normal" href="<?php echo U('createGroup');?>">
                    <i class="tw-icon-plus-circle"></i> 添加
                </a>
			</div>
		</div>
		<div class="tw-list-wrap">
            <table class="tw-table tw-table-list tw-table-fixed">
                <colgroup>
                	<col width="50">
                    <col width="10%">
                    <col>
                    <col width="50">
                    <col width="400">
                </colgroup>
                <thead>
                     <tr>
                     	<th class="">ID</th>
						<th class="">用户组</th>
						<th class="">描述</th>
						<th class="">状态</th>
						<th class="">操作</th>
						</tr>
                </thead>
                <tbody>
                   <?php if(!empty($_list)): if(is_array($_list)): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
						<td class="text-center"> <?php echo ($vo["id"]); ?> </td>
						<td class="text-left"> <?php echo ($vo["title"]); ?> </td>
						<td class="text-left"><?php echo mb_strimwidth($vo['description'],0,60,"...","utf-8");?></td>
						<td><?php echo ($vo["status_text"]); ?></td>
						<td>
						<a class="layui-btn" href="<?php echo U('AuthManager/access?group_name='.$vo['title'].'&group_id='.$vo['id']);?>">
			                <i></i> 授权
			            </a>
						
						<?php if(($vo["status"]) == "0"): ?><a class="layui-btn layui-btn-disabled" onclick="updateStatus('<?php echo U('AuthManager/changeStatus?method=forbidGroup&id='.$vo['id']);?>')">
			                <i></i> 禁用
			            </a>
							<?php else: ?>
							<a class="layui-btn layui-btn-danger" onclick="updateStatus('<?php echo U('AuthManager/changeStatus?method=resumeGroup&id='.$vo['id']);?>')">
			                <i></i> 启用
			            </a><?php endif; ?>
							<a class="layui-btn layui-btn-normal" href="<?php echo U('AuthManager/editgroup?id='.$vo['id']);?>" >
								<i></i> 修改
							</a>
			                <a class="layui-btn layui-btn-danger"
                                   onclick="javascript:recycle(<?php echo ($vo['id']); ?>, '确认删除?!此步骤无法恢复!')">
                                    <i></i> 删除
                                </a>
			                </td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php else: ?>
					<td colspan="5" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
				</tbody>
			</table>
		</div>
		<div class="page">
		        <?php echo ($_page); ?>
		    </div>
	</div>
	<script>
		function updateStatus(url){
	         $.get(url, function(data){
	            if(data.status == '1'){
	                window.location.reload();
	                toastr.success(data.info);
	            }else{
	                toastr.error(data.info); 
	            }
	        });
		}
	</script>

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
    
</body>
</html>
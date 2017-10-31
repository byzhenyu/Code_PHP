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
    <!--[if lt IE 9]-->
    <script type="text/javascript" src="/Application/Admin/Static/js/jquery-1.10.2.min.js"></script>
    <!--[endif]-->
    <script type="text/javascript" src="/Application/Admin/Static/js/jquery-2.0.3.min.js"></script>
    
</head>
<body>
    
    <!-- 内容区 -->
    <div id="content">
        

	<div class="tw-layout">
	<!-- 标题框 -->
		<div class="tw-list-hd">
			管理员/用户回收站
		</div>
		<div class="tw-list-top">
			<div class="tw-tool-bar">
                        <a class="tw-tool-btn-del" onclick="javascript:restore('chkbId')">
	                <i class="tw-icon-minus-circle"></i> 批量还原
	            </a>
			</div>
			<form action="/index.php/Admin/Member/recyclelist" method="get" id='frmSearch'>
				<div class="tw-search-bar">
					<div class="search-form fr cf">
			            <div class="sleft">
			                <input type="text" name="keywords" class="search-input" value="<?php echo I('keywords', '');?>" placeholder="真实姓名/登录名/手机号">
			                <a type="submit" class="sch-btn" onclick="$('#frmSearch').submit();"><i class="btn-search"></i></a>
			            </div>
			        </div>
				</div>
			</form>
		</div>
		<div class="tw-list-wrap">
            <table class="tw-table tw-table-list tw-table-fixed">
                <thead>
                    <tr>
                        <th width="10" class="row-selected">
                            <input class="checkbox check-all" type="checkbox">
                        </th>
						<th width="50">ID</th>
						<th width="15%">真实姓名</th>
						<th width="15%">登录名</th>
						<th width="15%">手机号</th>
						<th width="15%">性别</th>
						<th width="15%">状态</th>
						<th width="250">操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
								<td><input class="ids row-selected" type="checkbox" name="chkbId" value="<?php echo ($v["id"]); ?>"></td>
								<td><?php echo ($v["id"]); ?> </td>
								<td class="text-left"><?php echo ($v["real_name"]); ?></td>
								<td class="text-left"><?php echo ($v["username"]); ?></td>
								<td class="text-left"><?php echo ($v["phone"]); ?></td>
								<td><?php echo show_sex($v['sex']);?></td>
								<td><?php echo show_disabled($v['disabled']);?></td>
								<td>
									<?php echo change_disabled($v['id'], $v['disabled']);?>
						            <a class="tw-tool-btn-add" onclick="javascript:restore(<?php echo ($v['id']); ?>)">
						                <i class="tw-icon-plus-circle"></i> 还原
						            </a>
					           </td>
							</tr><?php endforeach; endif; else: echo "" ;endif; ?>
					<?php else: ?>
						<td colspan="8" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
        		</tbody>
            </table>
			<div class="page"><?php echo ($page['page']); ?></div>
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
    
	<script type="text/javascript">
	</script>
 

</body>
</html>
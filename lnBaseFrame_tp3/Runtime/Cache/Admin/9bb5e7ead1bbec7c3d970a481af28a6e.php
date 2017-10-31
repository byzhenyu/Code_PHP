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
			配置管理 
		</div>
		 <div class="tw-list-top">
				<div class="tw-tool-bar">
	    		<a class="layui-btn layui-btn-normal" href="<?php echo U('add');?>">
	                <i class="tw-icon-plus-circle"></i> 添加
	            </a>
	            <a class="layui-btn" href="<?php echo U('sort?group='.I('group'),'','');?>" >
	                <i class="tw-icon-cogs"></i> 排序
	            </a>
	          </div>
	      	  <form action="/index.php/Admin/Config/index" method="get" id='frmSearch'>
                <div class="tw-search-bar">
			      <div class="search-form fr cf">
				      <div class="sleft">
						  <input type="text" name="name" class="search-input" value="<?php echo I('name');?>" placeholder="请输入配置名称">
						  <a type="submit" class="sch-btn" onclick="$('#frmSearch').submit();"><i class="btn-search"></i></a>
					  </div>
			      </div>
	           </div>
            </form>
	    </div>

	<div class="tw-list-wrap">
		<table class="tw-table tw-table-list tw-table-fixed">
				<colgroup>
		                    <col width="50">
		                    <col>
		                    <col width="25%">
		                    <col width="100">
		                    <col width="100">
		                    <col width="200">
		       </colgroup>
			<thead>
				<tr>
					<th>ID</th>
					<th>名称</th>
					<th>标题</th>
					<th>分组</th>
					<th>类型</th>
					<th>操作</th>
				</tr>
			</thead>
			<tbody>
				<?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i;?><tr>
						<td><?php echo ($config["id"]); ?></td>
						<td class="text-left"><?php echo ($config["name"]); ?></td>
						<td class="text-left"><?php echo ($config["title"]); ?></td>
						<td><?php echo (get_config_group($config["group"])); ?></td>
						<td><?php echo (get_config_type($config["type"])); ?></td>
						<td>
							<a class="layui-btn layui-btn-normal"  href="<?php echo U('edit?id='.$config['id']);?>">
			                    <i></i> 修改
			                </a>
			                <a class="layui-btn layui-btn-danger" onclick="javascript:recycle(<?php echo ($config['id']); ?>, '确认删除?!此步骤无法恢复!')">
                                <i></i> 删除
                            </a>
						</td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php else: ?>
				<td colspan="6" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
			</tbody>
		</table>
		<!-- 分页 -->
	    <div class="page">
	        <?php echo ($_page); ?>
	    </div>
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
    
<script type="text/javascript">
$(function(){
	//点击排序
	$('.list_sort').click(function(){
		var url = $(this).attr('url');
		var ids = $('.ids:checked');
		var param = '';
		if(ids.length > 0){
			var str = new Array();
			ids.each(function(){
				str.push($(this).val());
			});
			param = str.join(',');
		}

		if(url != undefined && url != ''){
			window.location.href = url + '/ids/' + param;
		}
	});
});
</script>

</body>
</html>
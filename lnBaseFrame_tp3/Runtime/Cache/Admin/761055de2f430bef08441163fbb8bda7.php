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
            <?php echo isset($info['id'])?'编辑':'新增';?>文章
        </div>

	    <div class="tw-list-wrap tw-edit-wrap">
            <form action="/index.php/Admin/Article/edit" method="post" class="form-horizontal ajaxForm">
                <div class="form-item">
                    <label class="item-label">文章标题名称<span class="check-tips"><b>*</b>（输入文章标题）</span></label>
                    <div class="controls">
                        <input type="text" class="text input-large" name="name" value="<?php echo ($info["name"]); ?>">
                    </div>
                </div>

                <div class="form-item">
                    <label class="item-label">文章分类<span class="check-tips"><b>*</b>（选择文章分类）</span></label>
                    <div class="controls">
                        <select name="category_id">
                            <?php if(is_array($ArticleCategoryTree)): $i = 0; $__LIST__ = $ArticleCategoryTree;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"
                            <?php if($vo["id"] == $info['category_id'] ): ?>selected="selected"<?php endif; ?>
                            ><?php echo ($vo["title_show"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    </div>
                </div>

                <div class="form-item">
                    <label class="item-label">上传标题图片<span class="check-tips">（用于上传标题图片 请上传大于400*400的正方形图片）</span></label>
                    <div class="controls">
                        <div>
                            <img src="<?php echo ($info["photo_path"]); ?>" style="height:129px; width:129px;" id="img_"/>
                        </div>
                        <input type="hidden" value="<?php echo ($info["photo_path"]); ?>" name="photo_path" id="img" />
                            <input class="btn btn-default btn-xs" type="button" value="上传" id="btnUpload"/>
                            <input class="btn btn-danger btn-xs" type="button" value="删除" onclick="delFile($('#img').val(), '')" id="btn_delete_" />
                            <?php if($info["photo_path"] == ''): ?><script>
                                    $("#img_, #btn_delete_").hide();
                                </script><?php endif; ?>
                    </div>
                </div>

                <div class="form-item">
                    <label class="item-label">文章简介<span class="check-tips"><b>*</b>（输入文章简介）</span></label>
                    <div class="controls">
                        <textarea name="introduce" rows="5" cols="57"><?php echo ($info["introduce"]); ?></textarea><br/>
                    </div>
                </div>
                <div class="form-item">
                    <label class="item-label">文章内容<span class="check-tips"><b>*</b>（输入文章内容）</span></label>
                    <div class="controls">
                        <textarea name="content" id="UE"><?php echo (htmlspecialchars_decode($info["content"])); ?></textarea><br/>
                    </div>
                </div>
                <div class="form-item">
                    <label class="item-label">是否显示<span class="check-tips"><b>*</b></span></label>
                    <div class="controls">
                        <?php if($info["display"] == 1): ?><label class="radio"><input type="radio" value="0" name="display" >是</label>
                            <label class="radio"><input type="radio" value="1" name="display" checked="checked" >否</label>
                        <?php else: ?>
                            <label class="radio"><input type="radio" value="0" name="display" checked="checked" >是</label>
                            <label class="radio"><input type="radio" value="1" name="display" >否</label><?php endif; ?>
                    </div>
                </div>
                 <div class="tw-tool-bar-bot">
                    <button type="submit" class="tw-act-btn-confirm">提交</button>
                    <button type="button" onclick="goback()" class="tw-act-btn-cancel">返回</button>
                </div>
                    <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>"/>
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
    
    <script type="text/javascript" charset="utf-8" src="/Public/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/ueditor/ueditor.all.min.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/ajaxupload.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Application/Common/Static/js/imgupload.js"></script>
    <script>
        UE.getEditor('UE',{
            autoHeightEnabled: false,
            initialFrameWidth: '700',
            initialFrameHeight: 350
        });
        $(function(){
            ajaxUpload('#btnUpload', $("#img"), 'Article', '');
        })
    </script>

</body>
</html>
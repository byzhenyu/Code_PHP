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
    <div class="tw-list-hd">
      导入用户数据
    </div>
    <div class="tw-list-wrap tw-edit-wrap">
      <form action="<?php echo U();?>" method="post" class="form-horizontal ajaxForm">
            <div class="form-item">
                <label class="item-label">上传Excel<span class="check-tips">（附件格式：xls、xlsx）</span></label>
                <div class="controls">
                  <div>
              <div style="height:20px; width:100px;" id="img1_div"/><a href="" id="img1_" download=""></a></div>
            </div>
            <input type="hidden" value="" name="attach_path" id="enclosure" />
            <input class="btn btn-default btn-xs" type="button" value="上传" id="btnUpload1"/>
            <input class="btn btn-danger btn-xs" type="button" value="删除" onclick="delFileField($('#enclosure').val(), '')" id="btn_delete1_" />
              <script>
                $("#img1_div, #btn_delete1_").hide();
              </script>
            </div>
            <div class="tw-tool-bar-bot">
                <button type="submit" class="tw-act-btn-confirm">提交</button>
                <input class="btn btn-primary radius" type="button" onclick="download()" value="&nbsp;&nbsp;下载模板文件&nbsp;&nbsp;">
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
    <script type="text/javascript" charset="utf-8" src="/Application/Admin/Static/js/admin.js"></script>
    <script>
        $(function(){
          ajaxUploadField('#btnUpload1', $("#enclosure"), 'Excel', '');
        })
        function download(){
          window.location.href="/Uploads/demo/member.xls";
        }
    </script>

</body>
</html>
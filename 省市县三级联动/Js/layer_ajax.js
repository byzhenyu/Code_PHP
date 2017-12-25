/**
 * Copyright (c) 山东六牛网络科技有限公司 https://liuniukeji.com
 * 管理员列表
 * @Description     layer弹窗+ajax分页
 * @Author         谢有辉 QQ：565356915
 * @Copyright      Copyright (c) 山东六牛网络科技有限公司 保留所有版权(https://www.liuniukeji.com)
 * @Date           2017
 * @CreateBy       PhpStorm
 */
// ajax 抓取页面 form 为表单id  page 为当前第几页
function ajax_get_table(form, page, c, a) {
    cur_page = page; // 定义全局变量cur_page，表示当前分页是第几页
    $.ajax({
        type: "POST",
        url: "/index.php/Admin/" + c + '/' + a + "/p/" + page,//+tab,
        data: $('#' + form).serialize(),// 你的formid
        success: function (data) {
            $("#ajax_return").html('');
            $("#ajax_return").append(data);
        }
    });
}

/**
 *
 * 参数解释：
 * title    标题
 * url      请求的url
 * id       需要操作的数据id
 * w        弹出层宽度（缺省调默认值）
 * h        弹出层高度（缺省调默认值）
 * c        控制器
 * a        方法名
 */
function layer_show(title, url, w, h, c, a) {
    if (title == null || title == '') {
        title = false;
    }
    if (w == null || w == '') {
        w = 800;
    }
    if (h == null || h == '') {
        h = ($(window).height() - 50);
    }
    layer.open({
        type: 2,
        area: [w + 'px', h + 'px'],
        fix: false, //不固定
        maxmin: true,
        shade: 0.4,
        shadeClose: false,
        title: [title, 'font-size: 18px;font-weight:border'],
        content: [url, 'no'],
        end: function () {
            if (c) {
                $(document).ready(function () {
                    // 点击关闭弹窗后重新加载当前页，调用上面配置的全局变量page，实现无刷新重新加载
                    ajax_get_table('search_form', cur_page, c, a);
                });
            }
        }
    });
}

// 自动关闭layer弹窗
function closeLayer() {
    var index = parent.layer.getFrameIndex(window.name);
    parent.layer.close(index);
}
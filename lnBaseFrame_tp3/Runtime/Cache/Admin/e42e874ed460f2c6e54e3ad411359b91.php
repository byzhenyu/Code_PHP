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
    
    <style>
        #chat_box{ border: 1px solid #ddd; width: 90%; min-height: 530px; margin: 30px auto 0 auto; }
        .friend-photo img{ height: 60px; width: 60px; border-radius: 50%; margin-right: 5px; }
        #friend_list{ overflow-y: auto; background: #eaeaea; border-right: 1px solid #ddd; }
        .friend-block { border-bottom: 1px solid #ddd; padding: 5px; }
        .friend-block .friend-info { overflow: hidden; width: 140px; }
        .friend-block .friend-info input { background: none; border: 0px; }
        .friend-block .friend-info .realname{ font-weight: bold; }
            .new-message{ color: red; }
        .friend-block .friend-info .position_name, .friend-block .friend-info .department_name{ font-size: 11px; height: 20px; padding: 0px; margin: 0px;}
        .friend-click{ background: #cacdd3; }
        #chat_container { overflow: hidden; }
        #chat_recorder{ background: #f5f5f5;}
        .chat-item{ display: none; overflow-y: auto; border: 0px solid red; height: 100%;}
        .message-block{ margin: 2px 15px; width: 90%; border: 0px solid red; }
        .realname{ padding: 0px; }
        .time { font-size: 11px; color: #aaa; }
        .message-info{ padding: 5px; border: 1px solid #e7e7e7; background: #fff; border-radius: 5px; max-width: 80%; min-width: 50px; word-break:break-all;}
        img.message_image_thumb{ max-height: 160px; max-width: 160px; }
        .message_image_big{ display: none; }
        .message_image_big img{ margin: auto; }
        .message-block-self{ }
        .my_name{ text-align: right }
        .send-result{ color: red; }
        .facebox{ box-shadow:none; }
        #chat_input{ display: none;  border-top: 1px solid #ddd; background: #f9f9f9; }
        .btn-bar{ overflow: hidden; }
        .icon { display: inline-block; color: #666; padding: 5px; height: 20px; width: 30px; cursor: default; border: 0px solid red; }
        .picture-icon {padding: 0px 5px;}
        #picture_input{filter:alpha(opacity=1);  opacity: 0.01; height: 20px; overflow: hidden; width: 40px; position: relative; margin-left: -45px; border: 1px solid #f9f9f9;  }
        .face-content{}
        .face-list{ padding: 5px; }
        .face-list li{ list-style: none; float: left; padding: 2px; }
        .face-list li img{ cursor: pointer; }
        .chat-text{ }
        .chat-text textarea{ border: 0px; padding: 5px; background: transparent; }
        .chat-text #send_message{ position: relative; margin-top: -45px; margin-right: 0px;  }
    </style>

</head>
<body>
    
    <!-- 内容区 -->
    <div id="content">
        
    <div id="chat_box">
        <div id="friend_list" class="fl"></div>

        <div id="chat_container" class="fr">
            <div id="chat_recorder"></div>

            <div id="chat_input" class='cf'>
                <div class="btn-bar cf">
                    <div class="picture fl">
                        <div class="picture-icon icon">图片</div>
                        <input type="file" name="file" id='picture_input' onchange="sendImg()"/>
                    </div>

                    <div class="face fl">
                        <div class="face-content hidden">
                            <ul class="face-list"></ul>
                        </div>
                        <div class="face-icon icon" href="javascript:void(0)">表情</div>
                    </div>

                    <div class="history fl">
                        <div class="history-icon icon" href="javascript:void(0)">记录</div>
                    </div>
                </div>

                <div class="chat-text">
                    <textarea id="message_text" onkeydown="keySendText(event);"></textarea>
                    <input type="button" id="send_message" data-emchat_username="" onclick="sendTxt();" class="btn fr" value=" 发 送 (Ctrl+Enter)" />
                </div>

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
        // 全局变量
        var _UPDATE_FRIEND_LIST = "<?php echo U('updateFriendList');?>"; // 更新最近好友
        var _GET_FRIEND_INFO = "<?php echo U('getFriendInfo');?>"; // 获取好友详细信息
        var _EMOJI_PATH = "/Application/Admin/Static/images/faces/"; // 表情包地址
        var _EMCHAT = <?php echo json_encode(C('EMCHAT'));?>;  // 平台基础信息
        var _MY_INFO = <?php echo ($myInfo); ?>;  // 客户自己的信息
        var _FRIEND_LIST = <?php echo ($friend_list); ?>;

    </script>

    <script type="text/javascript" src="/Application/Admin/Static/js/chat/strophe.js"></script>
    <script type="text/javascript" src="/Application/Admin/Static/js/chat/websdk-1.1.2.js"></script>
    <script type="text/javascript" src="/Application/Admin/Static/js/chat/webim.config.js"></script>
    <script type="text/javascript" src="/Application/Admin/Static/js/chat/chat.js"></script>
    <script type="text/javascript">

        // 关闭所有弹窗(表情选择框)
        $('.chat-text, #chat_recorder, #friend_list').click(function(){
            layer.closeAll();
        })

        $('.face-icon').click(function(){
            var position = $('.face-icon').position();
            // LL(position);
            var left = position.left;
            var top = position.top;
            top -= 200;

            var html = $('.face-content').html();
            var index = layer.open({
                type: 1,
                title: false,
                closeBtn: 1,
                area: ['280px', '200px'],
                offset: [ top, left ],
                shade: false,
                shadeClose: true,
                skin: 'facebox',
                content: html
            });
        })
        $('.history').click(function(){
            var message_from = $('#send_message').attr('data-emchat_username');
            var message_to =  _MY_INFO['emchat_username'];
            openLayerPopup("<?php echo U('Admin/Chat/getHistoryMessage');?>/message_from/"+message_from+"/message_to/"+message_to,"聊天记录");
        })

        function insertEmji(obj){
            str = $(obj).attr('data-key');
            insertText($('#message_text')[0], str);
        }

        function sendImg() {
            var emchat_username = $('#send_message').attr('data-emchat_username');

            var id = conn.getUniqueId();
            var msg = new WebIM.message('img', id);
            var input = $('#picture_input')[0];//选择图片的input
            var file = WebIM.utils.getFileUrl(input);
            var allowType = {
                "jpg": true,
                "gif": true,
                "png": true,
                "bmp": true
            };
            if (file.filetype.toLowerCase() in allowType) {
                msg.set({
                    apiUrl: WebIM.config.apiURL,
                    file: file,
                    to: emchat_username,
                    onFileUploadError: function (error) {
                        alert(error)
                    },//图片上传失败
                    onFileUploadComplete: function (data) {
                        var src = data.uri + '/' + data.entities[0].uuid;
                        addSelfMessage(id, src, emchat_username, true, 'img'); //添加自己的聊天
                    },
                    success: function (id, serverMsgId) {
                        $("#sendimg").css('src', '');
                    },//图片消息发送成功
                    flashUpload: WebIM.flashUpload
                });

                str = '<img src="" onclick="getbigimg(this);" class="message_image_thumb" />';
                str +='<div><div class="message_image_big"><img src="" /></div></div>'; // 原图大小
                addSelfMessage(id, str, emchat_username, false, 'img'); //添加自己的聊天
                conn.send(msg.body);
            }

            // 滚动条
            $('#chat' + emchat_username).scrollTop($('#chat' + emchat_username)[0].scrollHeight);
        }

        function sendTxt(emchat_username) {
            $('#message_text').focus();

            var emchat_username = $('#send_message').attr('data-emchat_username');
            var txt = $('#message_text').val();

            var id = conn.getUniqueId();//生成本地消息id
            if ($.trim(txt) != '') {
                var msg = new WebIM.message('txt', id);//创建文本消息
                 
                msg.set({
                    msg: txt,
                    to: emchat_username,
                    success: function ( id, serverMsgId ) {
                        // console.log('消息发送成功');
                        $('#message_text').val('');

                        addSelfMessage(id, txt, emchat_username, true); //添加自己的聊天
                    }//消息发送成功回调   
                }); 
            } else {
                layer.tips(' 聊天内容不能为空 ', '#send_message', {
                    tips: [1, '#900'] //还可配置颜色
                });
                return false;
            }
            
            addSelfMessage(id, txt, emchat_username, false); //添加自己的聊天
            conn.send(msg.body);

        }

        // 显示新消息标志
        function showNewMessageFlag(emchat_username, is_history){
            if (is_history != true && $('#chat' + emchat_username).is(':visible') == false) {
                $('#' + emchat_username).find('.realname').addClass('new-message');
            }
        }

        /**
         * 新建好友
         * @param string emchat_username 好友的ID
         * @param bool is_history 是不是来自历史记录, 如果是, 不显示为有新消息
        */
        function addFirend(emchat_username, is_history){
            // ajax保存/更新好友列表
            $.post(_UPDATE_FRIEND_LIST, {"from":emchat_username, "to":_MY_INFO['emchat_username']}, function(data){
                // LL(data);
                if (data['status'] != 1) {
                    alert($data['info']);
                }
            });

            // alert($('#' + emchat_username).length);
            // 如果列表中已经有好友了, 就不再添加, 直接跳到最上部
            if ($('#' + emchat_username).length > 0) {
                var obj = $('#' + emchat_username).clone(true);
                $('#' + emchat_username).remove();
                $('#friend_list').prepend(obj);

                showNewMessageFlag(emchat_username, is_history); // 显示新消息标志

                return false;
            }

            // 加入好友列表
            var str = '';
            str += '<div class="friend-block cf" onclick="javascript:friend_click(this)" id="'+ emchat_username +'">';
                str += '<div class="friend-photo fl"><img src="" class="photo_path" onerror="javascript:this.src=\'/Application/Common/Static/images/touxiang.png\';"/></div>';
                str += '<div class="friend-info fr">';
                    str += '<input type="text" class="realname" readonly value="' + emchat_username + '"/>';
                    str += '<input type="text" class="department_name" readonly value=""/>'
                    str += '<input type="text" class="position_name" readonly value=""/>';
                str += '</div>';
            str += '</div>';
            $('#friend_list').prepend(str);

            showNewMessageFlag(emchat_username, is_history); // 显示新消息标志

            // 建立聊天对话框
            if ($('#chat' + emchat_username).length <= 0) {
                var s = '<div id="chat'+ emchat_username +'" class="chat-item"></div>'
                $('#chat_recorder').append(s);
            }

            // 获取好友信息
            $.post(_GET_FRIEND_INFO, {"emchat_username":emchat_username}, function(data){
                var realname = data['data']['real_name'];
                if (data['status'] == 1) {
                    // 异步, 重置名称
                    $('#' + emchat_username).find('.photo_path').attr('src', data['data']['photo_path_thumb_120']);
                    if (realname.length > 4) {
                        $('#' + emchat_username).find('.realname').val(realname.substring(0,4)+'...');
                    } else {
                        $('#' + emchat_username).find('.realname').val(realname);
                    }
                    $('#' + emchat_username).find('.department_name').val(data['data']['department_name']);
                    $('#' + emchat_username).find('.position_name').val(data['data']['position_name']);
                    // 反射聊天对话框里的名称
                    $('#chat' + emchat_username).find('.realname').html(realname);
                } else {
                    alert('接收到一条新消息, 但是出现错误:' + data['info']);
                }
            })
        }

        function addSelfMessage(id, message, emchat_username, is_success, type){
            if (type != 'img') {
                message = encode(message);

                // 替换所有表情
                message = message.replace(/\[(.+?)\]/g, function(m){
                    if (typeof(WebIM.Emoji.map[m]) !== 'undefined') {
                        return '<img src="' + WebIM.Emoji.path + WebIM.Emoji.map[m] +'" />';
                    } else {
                        return m;
                    }
                });
            }

            var str = '';
            str += '<div class="message-block message-block-self fr cf" id="recorder'+ id +'">';
                str += '<div class="my_name">我</div>';
                str += '<div class="message-info fr">';
                    str += '<span class="send-result" id="send_result'+ id +'">发送中...</span>';
                    str += message;
                str += '</div>';
            str += '</div>'

            if ($('#recorder' + id).length > 0) {
                if (is_success == true) {
                    $('#send_result' + id).hide();
                    if (type == 'img') {
                        $('#recorder' + id).find('img').attr('src', message);
                    }
                }
            } else {
                $('#chat' + emchat_username).append(str);
            }

            // 滚动条
            $('#chat' + emchat_username).scrollTop($('#chat' + emchat_username)[0].scrollHeight);
        }

        function addMessage(data){
            emchat_username = data['from'];
            message = data['data'];
            time = data['delay'];
            if (time) {
                time = formatCSTDate(time, "MM-dd hh:mm");
            } else {
                time = formatDate((new Date()),"MM-dd hh:mm");
            }

            var str = '';
            str += '<div class="message-block fl cf">';
                str += '<div class="cf">';
                    str += '<div class="realname fl"></div>';
                str += '<div class="time fl"> &nbsp; [' + time + '] </div>';
                str += '</div>';
                str += '<div class="message-info fl">';
                    str +=  message;
                str += '</div>';
            str += '</div>'

            $('#chat' + emchat_username).append(str);

            // 显示昵称
            var realname = $('#' + emchat_username).find('.realname').val();
            $('#chat' + emchat_username).find('.realname').html(realname);

            // 滚动条
            $('#chat' + emchat_username).scrollTop($('#chat' + emchat_username)[0].scrollHeight);
        }

        function friend_click(self){
            var emchat_username = $(self).attr('id');
            // LL(emchat_username);
            // 好友列表改变样式
            $(self).addClass('friend-click').siblings().removeClass('friend-click');
            $(self).find('.realname').removeClass('new-message');

            // 显示聊天记录
            $('.chat-item').hide(); // 隐藏其它好友聊天对话框
            $('#chat' + emchat_username).show(); // 显示当前好友对话框
            $('#chat_input').show();

            // 设为发送消息的对象
            $('#send_message').attr('data-emchat_username', emchat_username);

            // 滚动条
            $('#chat' + emchat_username).scrollTop($('#chat' + emchat_username)[0].scrollHeight);
        }

        function onTextMessage(message){
            //LL(message);
            message['data'] = encode(message['data']);
            addFirend(message['from']);
            addMessage(message);
        }

        function onEmojiMessage(message){
            //LL(message);
            addFirend(message['from']);

            // 处理信息
            var brief = '';
            var msg = message['data'];
            for (var i = 0, l = msg.length; i < l; i++) {
                brief += msg[i].type === 'emoji' ? '<img src="' + WebIM.utils.parseEmoji(encode(msg[i].data)) + '" />' : encode(msg[i].data);
            }
            message['data'] = brief;
            addMessage(message);
        }

        function onPictureMessage(message){
            // LL(message);

            addFirend(message['from']);

            // 处理信息
            str = '<img src="' + message['url'] + '" onclick="getbigimg(this);" class="message_image_thumb" />';
            str +='<div><div class="message_image_big"><img src="' + message['url'] + '" /></div></div>'; // 原图大小

            message['data'] = str;
            addMessage(message);
        }

        function resize(){
            var w = $(window).width();
            var h = $(window).height();

            $('#chat_box').width(w - 60);
            $('#chat_box').height(h - 60);

            var mainWidth = $('#chat_box').width();
            var mainHeight = $('#chat_box').height();

            var friendListWidth = 240;
            // 好友列表
            $('#friend_list').width(friendListWidth-1).height(mainHeight);

            // 右则
            $('#chat_container').width(mainWidth - friendListWidth);
            $('#chat_container').height(mainHeight);

            // 文本输入区大小
            var chatRecorderHeight = 160;
            var btnBarHeight = 30;
            $('#chat_input').height(chatRecorderHeight);
            $('#message_text').width(mainWidth - friendListWidth).height(chatRecorderHeight - btnBarHeight);

            chatRecorderHeight = $('#chat_input').outerHeight(true);
            // 工具栏大小
            $('.btn-bar').height(btnBarHeight);

            // 聊天记录
            $('#chat_recorder').width(mainWidth - friendListWidth).height(mainHeight - chatRecorderHeight);
        }

        // 生成表情
        function createFaces(){
            var emojiArr = [];
            for (var i in WebIM.Emoji.map) {
                emojiArr.push('<li><img data-key="' + i + '" src="' + WebIM.Emoji.path + WebIM.Emoji.map[i] + '" onclick="javascript:insertEmji(this)" /></li>');
            }

            $('.face-list').append(emojiArr);
        }

        // 获取好友列表
        function getFriendList(){
            var lenght = _FRIEND_LIST.length;
            for (var i = lenght - 1; i >= 0; i--) {
                addFirend(_FRIEND_LIST[i]['from'], true);
            }
        }

        // 快捷键发送
        function keySendText(event){
            if (event.ctrlKey && event.keyCode == 13) {
                sendTxt();
            }
        }

        $(function(){
            layer.msg('连接中...', {
                icon: 16, 
                shade: 0.01, 
                time: 0
            });

            resize();
            $(window).resize(function(){
                resize();
            })

            // 获取好友列表
            getFriendList();

            // 生成表情
            createFaces();

        })

    </script>

</body>
</html>
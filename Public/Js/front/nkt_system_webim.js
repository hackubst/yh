var conn = new WebIM.connection({
    https: WebIM.config.https,
    url: WebIM.config.xmppURL,
    isAutoLogin: WebIM.config.isAutoLogin,
    isMultiLoginSessions: WebIM.config.isMultiLoginSessions
});

var friendList = [];
var to_user_name = '';
var un_read_list = [];

conn.listen({
    onOpened: function ( message ) {          //连接成功回调
        // 如果isAutoLogin设置为false，那么必须手动设置上线，否则无法收消息
        // 手动上线指的是调用conn.setPresence(); 如果conn初始化时已将isAutoLogin设置为true
        // 则无需调用conn.setPresence();
        console.log("opened");
        console.log('登录成功！');
        getFriendList();
        $('.login').hide();
    },
    onClosed: function ( message ) {
        alert('客服在异地登录');
        location.href="/Acp";
    },         //连接关闭回调
    onTextMessage: function ( message ) {
        console.log(message);

        /*先取消所有头像高亮*/
        //$('.friend_list .list_item').removeClass('active');
        //判断是否有左边头像
        var li = $('#'+message.from+'');
        if(li.length>0){
            //li.addClass('active');
            //li.trigger("click");

        }else{
            //生成左边的头像信息
            var list_item =getFriendHeaderHtml(message.from);
            $('#friend_list').append(list_item);
            var li = $('#'+message.from+'');
            //li.trigger("click");

        }


        // 先隐藏所有消息窗口，显示当前
        //$('.msg_window>div').hide();
        // 插入对方回复
        var user_content = $('#content_'+message.from+'');
        var imgurl = $('#'+ message.from + '_img').attr('src');

        console.log(user_content);

        if(user_content.length>0){
            //user_content.show();
            console.log(message.data);
            //直接插入
            var node = getGetCommetHtml(message.data,getNowFormatDate(),imgurl,message.id);
            user_content.append(node);


        }else{
            //生成右边的窗口在插入
            //var html = '<div id="content_'+message.from+'"></div>';
            //$('.msg_window').append(html);
            //var user_content = $('#content_'+message.from+'');
            //user_content.hide();
            //var node = getGetCommetHtml(message.data,getNowFormatDate(),imgurl,message.id);

            //user_content.append(node);
            un_read_list.push(message.from);
            //getReplyListByUsername(message.from,user_content,'');
        }

        $("#"+message.from + "_span").show();


        //滑动到底部
        $("#msg_window").scrollTop(user_content.height());

    },    //收到文本消息
    onEmojiMessage: function ( message ) {},   //收到表情消息
    onPictureMessage: function ( message ) {}, //收到图片消息
    onCmdMessage: function ( message ) {},     //收到命令消息
    onAudioMessage: function ( message ) {},   //收到音频消息
    onLocationMessage: function ( message ) {},//收到位置消息
    onFileMessage: function ( message ) {},    //收到文件消息
    onVideoMessage: function (message) {
        var node = document.getElementById('privateVideo');
        var option = {
            url: message.url,
            headers: {
                'Accept': 'audio/mp4'
            },
            onFileDownloadComplete: function (response) {
                var objectURL = WebIM.utils.parseDownloadResponse.call(conn, response);
                node.src = objectURL;
            },
            onFileDownloadError: function () {
                console.log('File down load error.')
            }
        };
        WebIM.utils.download.call(conn, option);
    },   //收到视频消息
    onPresence: function ( message ) {
        handlePresence(message);
    },    //收到联系人订阅请求、处理群组、聊天室被踢解散等消息
    onRoster: function ( message ) {},         //处理好友申请
    onInviteMessage: function ( message ) {},  //处理群组邀请
    onOnline: function () {},                  //本机网络连接成功
    onOffline: function () {},                 //本机网络掉线
    onError: function ( message ) {},          //失败回调
    onBlacklistUpdate: function (list) {       //黑名单变动
                                               // 查询黑名单，将好友拉黑，将好友从黑名单移除都会回调这个函数，list则是黑名单现有的所有好友信息
        console.log(list);
    }
});

// 本Demo登陆测试帐号：mengyuanyuan，密码：123456, 测试对方账号：asdfghj，密码：123456
//默认连接
// var options = {
//     apiUrl: WebIM.config.apiURL,
//     user: "mengyuanyuan",
//     pwd: "123456",
//     appKey: WebIM.config.appkey
// };
// conn.open(options);

// 注册
var register = function () {
    var username = $('#username').val();
    var password = $('#password').val();
    var nickname = $('#nickname').val();
    // 用户名非空校验
    if (!isEmpty(username)||$.trim(username)==''){
        alert('用户名不能为空');
        return;
    }

    // 密码非空校验
    if (!isEmpty(password)||$.trim(password)==''){
        alert('密码不能为空');
        return;
    }

    // 昵称非空校验
    if (!isEmpty(nickname)){
        alert('昵称不能为空');
        return;
    }

    var option = {
        username: username,
        password: password,
        nickname: nickname,
        appKey: WebIM.config.appkey,
        success: function () {
            console.log('注册成功!');
        },
        error: function () {
            console.log('注册失败');
        },
        apiUrl: WebIM.config.apiURL
    };
    conn.signup(option);
};
// 登录
var login = function(){
    var username = $('#username').val();
    var password = $('#password').val();
    // 用户名非空校验
    if (!isEmpty(username)){
        alert('用户名不能为空');
        return;
    }
    // 密码非空校验
    if (!isEmpty(password)){
        alert('密码不能为空');
        return;
    }
    var options = {
        apiUrl: WebIM.config.apiURL,
        user: username,
        pwd: password,
        appKey: WebIM.config.appkey
    };
    conn.open(options);
};

function auto_login(uname,pwd){
    var username = uname;
    var password = pwd;
    // 用户名非空校验
    if (!isEmpty(username)){
        alert('用户名不能为空');
        return;
    }
    // 密码非空校验
    if (!isEmpty(password)){
        alert('密码不能为空');
        return;
    }

    var options = {
        apiUrl: WebIM.config.apiURL,
        user: username,
        pwd: password,
        appKey: WebIM.config.appkey,
    };
    conn.open(options);
};

// 是否为空
function isEmpty(value){
    var validateReg = /^\S+$/;
    return validateReg.test(value);
}


// 获取选中用户ID
var toUser = null;
var toUser_nickname = null;
$('.friend_list').on('click','.list_item',function(){

    // 当前用户背景高亮
    $('.list_item').removeClass('active');
    $(this).addClass('active');

    // 获取当前用户ID
    toUser = $(this).attr('data-userId');
    to_user_name = toUser;
    $('#'+to_user_name+'_span').hide();
    var user_content = $('#content_'+toUser+'');
    // 隐藏全部的聊天框
    $('.msg_window>div').hide();
    // console.log(user_content);
    // 判断右边窗口是否存在
    if(user_content.length>0){
        user_content.show();
    }else{
        // 生成右边的窗口
        var html = '<div id="content_'+toUser+'"></div>';
        // 插入
        $('.msg_window').append(html)
        var user_content = $('#content_'+toUser+'');
        getReplyListByUsername(toUser,user_content,'')
    }

    //获取当前用户昵称
    toUser_nickname = $(this).find('.nickname').html();
    console.log(toUser);//用户ID
    $('#toUser_nickname').html(toUser_nickname);
})
// 私聊发送文本消息，发送表情同发送文本消息，只是会在对方客户端将表情文本进行解析成图片
var sendPrivateText = function () {
    var id = conn.getUniqueId();
    var msg = new WebIM.message('txt', id);
    var text = $("#text").val();//要发送的消息
    toUser = toUser;
    console.log(msg);
    // console.log(text);
    console.log(toUser);
    if(toUser==null){
        alert('请选择聊天用户！');
        return
    }
    if ($.trim(text)=='') {
        alert("输入内容不能为空!");
        return;
    }

    msg.set({
        msg: text,                       // 消息内容
        to: toUser,                          // 接收消息对象
        roomType: false,
        success: function (id, serverMsgId) {
            console.log("send private text Success");
            $.post('/AcpChat/ajax_add_reply',{'to_user_name':to_user_name,'message':text,'user_type':1,'message_id':id},function(data){
                if(data == 'success'){
                    console.log('数据库保存消息成功');
                }else{
                    console.log('数据库保存消息失败');
                }
            });
            // console.log(id,serverMsgId);
        }
    });
    msg.body.chatType = 'singleChat';
    conn.send(msg.body);
    console.log(msg.body.msg);
    // 要插入的窗口
    var  root_el = $('#content_'+toUser+'');
    // 获取当前时间

    // 插入聊天记录
    var node = getSendCommetHtml(msg.body.msg,getNowFormatDate(),selfimgurl);
    root_el.append(node);


    var user_p = $('#'+to_user_name+'_p');
    $(user_p).html(msg.body.msg);
    //滚动到最底部
    $("#msg_window").scrollTop(root_el.height());
    $("#text").val("");
};


/**
 * 获取好友列表
 */
function getFriendList() {
    //如果指定回复用户，将不进行好友列表拉取
    if(from_user_id!=""||from_user_id.length>0){
        console.log('from_user_id:'+from_user_id);
        return;
    }

    conn.getRoster({
        success: function (roster) {
            //console.log(roster);
            //获取好友列表，并进行好友列表渲染，roster格式为：
            /** [
             {
               jid:'asemoemo#chatdemoui_test1@easemob.com',
               name:'test1',
               subscription: 'both'
             }
             ]
             */
            friendList = [];
            $('#friend_list').html('');
            for (var i = 0, l = roster.length; i < l; i++) {
                var ros = roster[i];
                console.log((ros));
                //ros.subscription值为both/to为要显示的联系人，此处与APP需保持一致，才能保证两个客户端登录后的好友列表一致
                if (ros.subscription === 'both' || ros.subscription === 'to' || ros.subscription === 'from') {
                    console.log((ros.name));
                    friendList.push(ros);
                    //$('#friend_list').append("<li onclick='setChatUser(this)'><img src='head.png' class='head_img'/><span>"+ros.name+"</span></li>");

                    //if(ros.name == 'pozx')
                    //    continue;
                    var html = getFriendHeaderHtml(ros.name);
                    $('#friend_list').append(html);

                    var result = un_read_list.indexOf(ros.name);;
                    if(result!=-1){
                        $("#"+ros.name + "_span").show();
                    }


                    getUserInfoByUsername(ros.name);
                }
            }
        }
    });
}

//构造左边的联系人列表
function getFriendHeaderHtml(name){

    var html = '<li class="list_item" data-userId="'+name+'" id="'+ name +'">'
        +  '<img id="'+ name + '_img" src="" alt="" onerror="default_img(this)">'
        + '<div class="name_info">'
        + '<div><h4 class="nickname">'+name+'</h4><span  id="'
        + name
        +'_span" style="width: 10px;height: 10px;border-radius: 5px;background: red;float: right;display: none"></span></div>'
        + '<p class="reply_prev" id="'+ name +'_p">----</p>'
        + '</div></li>';
    return html;
}

//构造发送的聊天气泡
function getSendCommetHtml(msg,time,headimgurl){
    var node = '<div class="other">'
        +		'<div class="clearfix">'
        +			'<img src="'+headimgurl+'" alt="" onerror="default_img(this)">'
        +			'<div class="other_text">'+msg+'</div>'
        +		'</div>'
        +		'<p class="time">'+ time  +'</p>'
        +	'</div>';
    return node;
}

//构造发送的聊天气泡
function getGetCommetHtml(msg,time,headimgurl,id){
    //如果这条消息已经存在，就不进行添加
    var obj = $("#"+id);
    if(obj.length>0){
        return '';
    }
    var node = '<div class="my" id="'+ id +'">'
        +				'<div class="clearfix">'
        +					'<img src="'+headimgurl+'" alt="" onerror="default_img(this)">'
        +					'<div class="my_text">'+msg +'</div>'
        +				'</div>'
        +				'<p class="time">'+ time  +'</p>'
        +			'</div>';
    return node;
}

// 添加好友,目前自动加好友了，message不用管
var addFriends = function () {
    conn.subscribe({
        to:toUser,
        // Demo里面接收方没有展现出来这个message，在status字段里面
        message: '加个好友呗!',
    });
};


//收到联系人订阅请求的处理方法，具体的type值所对应的值请参考xmpp协议规范
var handlePresence = function ( e ) {

    //对方收到请求加为好友

    if (e.type === 'subscribe') {
        console.log("收到请求加为好友");
        /*同意添加好友操作的实现方法*/
        console.log("同意加为好友");
        conn.subscribed({
            to: e.from,
            message : '[resp:true]'
        });
        console.log("反向加好友");
        conn.subscribe({//需要反向添加对方好友，即自动加好友
            to: e.from,
            message : '[resp:true]'
        });
    }

    if(e.type == "subscribed"){
        getFriendList();
    }

    if(e.type == "unsubscribed"){
        console.log("被"+ e.from + "删除好友");
    }
};

/**
 * 检查用户是否是在好友列表里面
 */
function checkFriend(){
    var inFriendList = false;
    for (var i = 0, l = friendList.length; i < l; i++) {
        var ros = friendList[i];
        //ros.subscription值为both/to为要显示的联系人，此处与APP需保持一致，才能保证两个客户端登录后的好友列表一致
        if (ros.subscription === 'both') {
            if(ros.name == toUser){
                inFriendList = true;
                break;
            }
        }
    }
    return inFriendList;
}



window.onload = function () {
    // 注册
    //document.getElementById('register').onclick = register;
    // 登录
    //document.getElementById('login').onclick = login;
    //私聊
    document.getElementById('privateText').onclick = function(){
        if(!checkFriend()){
            console.log("还不是好友");
            addFriends();
        }
        sendPrivateText();
    };

};

function getNowFormatDate() {
    var date = new Date();
    var seperator1 = "-";
    var seperator2 = ":";
    var month = date.getMonth() + 1;
    var strDate = date.getDate();
    if (month >= 1 && month <= 9) {
        month = "0" + month;
    }
    if (strDate >= 0 && strDate <= 9) {
        strDate = "0" + strDate;
    }
    var currentdate = date.getFullYear() + seperator1 + month + seperator1 + strDate
        + " " + date.getHours() + seperator2 + date.getMinutes()
        + seperator2 + date.getSeconds();
    return currentdate;
}


function getUserInfoByUsername(username){
    $.post('/Global/getUserInfoById',{'username':username},function(data){
        var info = JSON.parse(data);
        if(info.code==0){
            var nickname = info.realname;
            var headimgurl = info.headimgurl;
            var role_type = info.role_type
            //$(obj).html($(obj).html()+'('+nickname+')');
            $('.nickname').each(function(){
                if($(this).html() == username){
                    //$(this).html($(this).html()+'('+ nickname +')');
                    $(this).html(nickname+"(用户)");
                    $('#'+username+'_img').attr('src',headimgurl);
                    console.log(headimgurl);
                    console.log(1111111111);
                }
            })

            if(info.is_read == 0){
                $("#" + username + "_span").show();
            }
        }
    })
}



//根据用户名获取聊天记录
function getReplyListByUsername(username,content,node){
    console.log(username);
    console.log('aaaaaaaa');
    $.post('/AcpChat/getReplyListByUserId',{'username':username},function(data){
        var info = JSON.parse(data);
        if(info.code==0){
            console.log(info);
            $(content).html('');
            //$(obj).html($(obj).html()+'('+nickname+')');
            for(var i=0;i<info.data.length;i++){
                var html='';
                if(info.data[i].f_uid == username){
                    html = getGetCommetHtml(info.data[i].message,info.data[i].time,info.data[i].headimgurl,info.data[i].message_id)
                }else{
                    html = getSendCommetHtml(info.data[i].message,info.data[i].time,info.data[i].headimgurl)
                }

                if(html!=''){
                    content.append(html);
                }
            }
            //检查未读消息数量
            check_un_read();
            content.append(node);
        }
    });
}
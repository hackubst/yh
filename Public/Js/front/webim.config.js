var WebIM = {};
WebIM.config = {
    xmppURL: 'im-api.easemob.com',            // xmpp Server地址，对于在console.easemob.com创建的appKey，固定为该值

    apiURL: 'http://a1.easemob.com',          // rest Server地址，对于在console.easemob.com创建的appkey，固定为该值

    appkey: '1126170823178654#xaiobie',        // App key

    https: false,                            // 是否使用https

    isMultiLoginSessions: true,              // 是否开启多页面同步收消息

    isAutoLogin: true                         // 自动上线，（如设置为false，则表示离线，无法收消息）
}

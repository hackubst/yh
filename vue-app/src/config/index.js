export default {
	// 判断是否 ios
	is_ios: function is_ios() {
	    let browserName = navigator.userAgent.toLowerCase();
	    return /(iphone|ipod|ipad)/i.test(browserName);
	},
	// 判断是否 安卓
	is_android: function is_android() {
	    let browserName = navigator.userAgent.toLowerCase();
	    return /(android)/i.test(browserName);
	},
	// 判断是否 微信浏览器
	is_wechat: function is_wechat() {
	    let browserName = navigator.userAgent.toLowerCase();
	    return /(MicroMessenger)/i.test(browserName);
	},
	// 普通页面回退
	back: function native_back() {
		let _json = JSON.stringify({
            event: "back",
            params: ""
        });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 普通页面进入
	jump: function native_jump(url) {
		let _json = JSON.stringify({
            event: "to_jump",
            params: {"url": url}
       	});
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 销毁当前页
	jump_and_finish: function(url) {
		let _json = JSON.stringify({
            event: "jump_and_finish",
            params: {"url": url}
       	});
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 页面有弹层显示
	show_alert: function() {
		let _json = JSON.stringify({
            event: "show_alert",
            params: ""
       	});
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 页面没有弹层
	no_alert: function() {
		let _json = JSON.stringify({
            event: "no_alert",
            params: ""
       	});
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 通知APP支付
	pay: function (payInfo) {
		// console.log(payInfo);
		let _json = JSON.stringify({
            event: "native_pay",
            params: payInfo
       	});
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	wxPay: function(payInfo){
		let _json = JSON.stringify({
            event: "wx_pay",
            params: payInfo
		   });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	aliPay: function(payInfo){
		let _json = JSON.stringify({
            event: "ali_pay",
            params: payInfo
       	});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 返回时关闭多页,pageName: 页面路由
	close_multy_page: function(pageName) {
		let _json = JSON.stringify({
            event: "close_multy_page",
            params: pageName     // 数据格式，Array
       	});
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 点击扫码
	// scan_type, 1: 
	normal_scan: function(scan_type,role_type,app_scan_type) {
		let _json = JSON.stringify({
	        event: "normal_scan",
	        params: {
	        	scan_type: scan_type,
	        	role_type: role_type,
	        	app_scan_type: app_scan_type
	        }
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 调用内置浏览器
	open_browser: function(url) {
		let _json = JSON.stringify({
	        event: "native_browser",
	        params: {"url": url}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 绑定微信
	bind_wx: function () {
		let _json = JSON.stringify({
	        event: "bind_wx",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	wxFriends: function(){
		let _json = JSON.stringify({
	        event: "wx_friends",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	wxCircle: function(){
		let _json = JSON.stringify({
	        event: "wx_circle",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	nativeScan: function(){
		let _json = JSON.stringify({
	        event: "native_scan",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	setJpushId:function(){
		let _json = JSON.stringify({
	        event: "setJpushId",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	login:function(){
		let _json = JSON.stringify({
	        event: "login",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	logout:function(){
		let _json = JSON.stringify({
	        event: "logout",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	share:function(){
		let _json = JSON.stringify({
	        event: "share",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 上传头像
	nativeUploadHeadimg: function() {
		let _json = JSON.stringify({
	        event: "native_upload_headimg",
	        params: ''
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 上传图片，可选拍摄或相册
	nativeUploadImg(album, num, isCrop) { // album: 0 拍摄， 1相册; isCrop: 0 不裁剪， 1 裁剪
		let _json = JSON.stringify({
	        event: "native_upload_img",
	        params: {
				album: album,
				maxnum: num,
				isCrop: isCrop
	        }
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 上传不同类型的图片
	nativeUploadImgByType(type) { // album: 0 拍摄， 1相册
		let _json = JSON.stringify({
	        event: "native_upload_img_type",
	        params: {
				type: type
	        }
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 打开淘宝 app
	openTaobao:function(item_link){
		let _json = JSON.stringify({
	        event: "native_open_taobao",
	        params: {
				item_link: item_link
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 原生粘贴
	nativePaste: function () {
		let _json = JSON.stringify({
			event: "native_paste",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 清除缓存
	cleanCache: function () {
		let _json = JSON.stringify({
			event: "native_clean_cache",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// ios首页弹窗
	indexPop: function () {
		let _json = JSON.stringify({
			event: "native_first_login",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_complete_data: function () {
		let _json = JSON.stringify({
			event: "native_complete_data",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_back_register: function () {
		let _json = JSON.stringify({
			event: "native_back_register",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	send_complete_data: function (res) {
		let _json = JSON.stringify({
			event: "send_complete_data",
	        params: res
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_open_scan: function(){
		let _json = JSON.stringify({
			event: "native_open_scan",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_chat: function(){
		let _json = JSON.stringify({
			event: "native_chat",
	        params: {}
		});
		let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_show_tabBar:function(show){
		console.log('native_show_tabBar', show);
		let _json = JSON.stringify({
	        event: "native_show_tabBar",
	        params: {
				show: show
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_chat_call:function(user_id, time, type){
		let _json = JSON.stringify({
	        event: "native_chat_call",
	        params: {
				user_id: user_id,
				time: time,
				type: type
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_chat_photoWord:function(user_id, time, type,headimgurl){
		let _json = JSON.stringify({
	        event: "native_chat_photoWord",
	        params: {
				user_id: user_id,
				time: time,
				type: type,
				headimgurl:headimgurl
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_change_head: function(headimgurl){
		let _json = JSON.stringify({
	        event: "native_change_head",
	        params: {
				headimgurl:headimgurl
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	logout: function(){
		let _json = JSON.stringify({
	        event: "logout",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_clear_cache: function(){
		let _json = JSON.stringify({
	        event: "native_clear_cache",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_post_share: function(shareObj){
		let _json = JSON.stringify({
	        event: "native_post_share",
	        params: {
				shareObj: shareObj
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_get_area: function(shareObj){
		let _json = JSON.stringify({
	        event: "native_get_area",
	        params: {
				shareObj: shareObj
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_login: function(){
		console.log('登录')
		let _json = JSON.stringify({
	        event: "native_login",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_popup_window: function(){
		let _json = JSON.stringify({
	        event: "native_popup_window",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 病例档案页面返回原生
	jumpReturn: function(){
		let _json = JSON.stringify({
	        event: "jumpReturn",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	// 用户端滚动公告跳到咨询列表
	jumpInformation: function(){
		let _json = JSON.stringify({
	        event: "jumpInformation",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_change_identity:function(is_doctor, new_pwd){
		let _json = JSON.stringify({
	        event: "native_change_identity",
	        params: {
				is_doctor: is_doctor,
				new_pwd: new_pwd
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_open_qq: function(qqNumber){
		let _json = JSON.stringify({
	        event: "native_open_qq",
	        params: {
				qqNumber: qqNumber
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_join_qq: function(qqNumber, key){
		let _json = JSON.stringify({
	        event: "native_join_qq",
	        params: {
				qqNumber: qqNumber,
				key: key
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	send_paste: function(url){
		let _json = JSON.stringify({
	        event: "send_paste",
	        params: {
				content: url
			}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	},
	native_clean_url: function(){
		let _json = JSON.stringify({
	        event: "native_clean_url",
	        params: {}
	    });
        let browserName = navigator.userAgent.toLowerCase();
        if(/(iphone|ipod|ipad)/i.test(browserName)){
			window.webkit.messageHandlers.webviewEvent.postMessage(_json);
		}else if(/(android)/i.test(browserName)){
			window.ResultAndroid.webviewEvent(_json);
		}
	}
}

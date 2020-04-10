/**********************************************************
 *
 * webview 和 native 交互接口
 *
 * @author tale
 *
 **********************************************************
 */

// 判断是否 ios
function is_ios() {
    var browserName = navigator.userAgent.toLowerCase();
    return /(iphone|ipod|ipad)/i.test(browserName);
}
// 判断是否 微信浏览器
function is_wechat() {
    var browserName = navigator.userAgent.toLowerCase();
    return /(MicroMessenger)/i.test(browserName);
}
// 判断是否 mobile
function is_mobile() {
    var browserName = navigator.userAgent.toLowerCase();
    return /(blackberry|playbook|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|ipad|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i.test(browserName);
}
// webview 统一触发 native 事件的入口（用于webview调用APP事件）
function native_listen(event, params) {
    // alert('native_listen,event:' + event);
    // alert('native_listen,params:' + params);
    if (typeof event != 'string' || !event.length) {
        throw new Error('native_listen');
    }
    try {
        if (is_wechat()) {
            // webview 中执行 wechat 事件（每个页面都要复写此方法）
			//return excute_wechat(event, params);
			return '';
        }
        var _json = JSON.stringify({
            event: event,
            params: params
        });
        console.log(_json);
        if (is_ios()) {
            return window.webkit.messageHandlers.webviewEvent.postMessage(_json);
        } else {
            return ResultAndroid.webviewEvent(_json);
        }
    } catch (_e) {
        alert(_e);
        return;
    }
}
// 页面跳转至另一个 webview 的简单事件封装
function native_jump(page, url) {
    return native_listen('jump', {
        page: page,
        url: url
    });
}
// 页面回退事件的封装
function native_back() {
    return native_listen('back');
}
// 弹出页面确认框事件的封装
function native_confirm(message, yes, no) {
    if (typeof yes == 'function') {
        add_native_callback('confirm_yes', yes);
    }
    if (typeof no == 'function') {
        add_native_callback('confirm_no', no);
    }
    return native_listen('confirm', {
        message: message
    });
}
// native 事件回调队列
var _native_callback_query;
// 避免重复引入文件导致队列被整体覆盖的问题
if (_native_callback_query == null) {
    _native_callback_query = [];
}
/**
 * 添加一个 native 事件回调
 * 注意：相同事件名的回调函数会被覆盖，除非业务逻辑需要覆盖，否则请避免重名！
 */
function add_native_callback(event, callback) {
    if (typeof event == 'string' && event.length > 0 && typeof callback == 'function') {
        _native_callback_query[event] = callback;
    } else {
        throw new Error("add_native_callback");
    }
}
// native 统一触发 webview 事件的入口（用于APP调用webview事件）
function webview_listen(event, params) {
    // alert('webview_listen,event:' + event);
    // alert('webview_listen,params:' + params);
    try {
        var callback = _native_callback_query[event];
        if (typeof callback == 'function') {
            // 触发事件回调
            if (typeof params == 'string') {
                params = JSON.parse(params);
            }
            return callback(params);
        } else {
            throw new Error('the callback function of "' + event + '" event is not found!');
        }
    } catch (_e) {
        alert(_e);
        return 'listen error!';
    }
}

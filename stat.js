//判断终端类型
 var browser={
    versions:function(){
           var u = navigator.userAgent, app = navigator.appVersion; 
           return {//移动终端浏览器版本信息
                trident: u.indexOf('Trident') > -1, //IE内核
                presto: u.indexOf('Presto') > -1, //opera内核
                webKit: u.indexOf('AppleWebKit') > -1, //苹果、谷歌内核
                gecko: u.indexOf('Gecko') > -1 && u.indexOf('KHTML') == -1, //火狐内核
                mobile: !!u.match(/AppleWebKit.*Mobile.*/), //是否为移动终端
                ios: !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/), //ios终端
                android: u.indexOf('Android') > -1 || u.indexOf('Linux') > -1, //android终端或者uc浏览器
                iPhone: u.indexOf('iPhone') > -1 , //是否为iPhone或者QQHD浏览器
                iPad: u.indexOf('iPad') > -1, //是否iPad
                webApp: u.indexOf('Safari') == -1 //是否web应该程序，没有头部与底部
            };
         }(),
         language:(navigator.browserLanguage || navigator.language).toLowerCase()
}

var cookie = {};

cookie.set = function(name, value, time)
{
    var exp  = new Date();
    exp.setTime(exp.getTime() + time);
    document.cookie = name + "="+ escape(value) + ";expires=" + exp.toGMTString()+';path=/';
}

cookie.get = function(name)
{
	var arr = document.cookie.match(new RegExp("(^| )"+ name +"=([^;]*)(;|$)"));
	if(arr != null) return unescape(arr[2]);
	return null;
}

function getTerminalId()
{
	if (browser.versions.ios)
	{
		return 1;
	}
	else if (browser.versions.mobile)
	{
		return 2;
	}
	else if (browser.versions.android)
	{
		return 3;
	}
	else if (browser.versions.iPhone)
	{
		return 4;
	}
	else if (browser.versions.iPad)
	{
		return 5;
	}

	return 0;
}

function detectOS() {  
    var sUserAgent = navigator.userAgent;  
    var isWin = (navigator.platform == "Win32") || (navigator.platform == "Windows");  
    var isMac = (navigator.platform == "Mac68K") || (navigator.platform == "MacPPC") || (navigator.platform == "Macintosh") || (navigator.platform == "MacIntel");  
    if (isWin) {  
        var isWinXP = sUserAgent.indexOf("Windows NT 5.1") > -1 || sUserAgent.indexOf("Windows XP") > -1;  
        if (isWinXP) return 1;  																				//WinXP
        var isWin7 = sUserAgent.indexOf("Windows NT 6.1") > -1 || sUserAgent.indexOf("Windows 7") > -1;  
        if (isWin7) return 2;  																					//Win7
        var isWinVista= sUserAgent.indexOf("Windows NT 6.0") > -1 || sUserAgent.indexOf("Windows Vista") > -1;  
        if (isWinVista) return 3;  																				//WinVista
        var isWin2K = sUserAgent.indexOf("Windows NT 5.0") > -1 || sUserAgent.indexOf("Windows 2000") > -1;  
        if (isWin2K) return 4; 																					//Win2000 
        var isWin2003 = sUserAgent.indexOf("Windows NT 5.2") > -1 || sUserAgent.indexOf("Windows 2003") > -1;  
        if (isWin2003) return 5;  																				//Win2003
    }  
    if (isMac) return 6;																						//Mac  
    var isUnix = (navigator.platform == "X11") && !isWin && !isMac;  
    if (isUnix) return 7;  																						//Unix
    var isLinux = (String(navigator.platform).indexOf("Linux") > -1);  
    if (isLinux) return 8; 																						//Linux 
    return 0;  																									//other
}  
 
function getBrowserVersion()
{
	var browser=navigator.appName;
	var b_version=navigator.appVersion;
	if(browser=="Microsoft Internet Explorer")
	{
		var version=b_version.split(";");
		var trim_Version=version[1].replace(/[ ]/g,"");

		if (trim_Version.indexOf('MSIE6') > -1)
		{
			return 1;
		}
		else if (trim_Version.indexOf('MSIE7') > -1)
		{
			return 2;
		}
		else if (trim_Version.indexOf('MSIE8') > -1)
		{
			return 3;
		}
		else if (trim_Version.indexOf('MSIE9') > -1)
		{
			return 4;
		}
		else if (trim_Version.indexOf('MSIE10') > -1)
		{
			return 5;
		}
	}
	else if(navigator.userAgent.indexOf('Chrome') > -1)
	{
	   	return 6;
	}
	else if(navigator.userAgent.indexOf('Firefox') > -1)
	{
	   	return 7;
	}
	else if(browser=="Opera")
	{
	   	return 8;
	}
	return 0;
}

function getScreenId(width, height)
{
	if (width == 640 && height == 960)
	{
		return 1;
	}
	else if (width == 800)
	{
		if (height == 600)
		{
			return 2;
		}
		else if(height == 1200)
		{
			return 3;
		}
	}
	else if (width == 1024)
	{
		if (height == 600)
		{
			return 4;
		}
		else if(height == 768)
		{
			return 5;
		}
		else if(height == 1536)
		{
			return 6;
		}
	}
	else if (width == 1152)
	{
		if (height == 864)
		{
			return 7;
		}
		else if(height == 1728)
		{
			return 8;
		}
	}
	else if (width == 1280)
	{
		if (height == 720)
		{
			return 9;
		}
		else if(height == 768)
		{
			return 10;
		}
		else if(height == 800)
		{
			return 11;
		}
		else if(height == 900)
		{
			return 12;
		}
		else if(height == 960)
		{
			return 13;
		}
		else if(height == 1024)
		{
			return 14;
		}
		else if(height == 1600)
		{
			return 15;
		}
		else if(height == 2048)
		{
			return 16;
		}
	}
	else if (width == 1360)
	{
		if (height == 768)
		{
			return 17;
		}
		else if(height == 1024)
		{
			return 18;
		}
	}
	else if (width == 1400)
	{
		if (height == 1050)
		{
			return 19;
		}
		else if(height == 2100)
		{
			return 20;
		}
	}
	else if (width == 1440 && height == 900)
	{
		return 21;
	}
	else if (width == 1600)
	{
		if (height == 600)
		{
			return 22;
		}
		else if(height == 1024)
		{
			return 23;
		}
		else if(height == 1200)
		{
			return 24;
		}
		else if(height == 2400)
		{
			return 25;
		}
	}
	else if (width == 1680 && height == 1050)
	{
		return 26;
	}
	else if (width == 1792 && height == 1344)
	{
		return 27;
	}
	else if (width == 1800 && height == 1440)
	{
		return 28;
	}
	else if (width == 1920)
	{
		if (height == 1080)
		{
			return 29;
		}
		else if(height == 1200)
		{
			return 30;
		}
		else if(height == 1440)
		{
			return 31;
		}
	}
	else if (width == 2048)
	{
		if (height == 768)
		{
			return 32;
		}
		else if(height == 1536)
		{
			return 33;
		}
		else if(height == 3072)
		{
			return 34;
		}
	}
	else if (width == 2304 && height == 864)
	{
		return 35;
	}
	else if (width == 2560)
	{
		if (height == 800)
		{
			return 36;
		}
		else if(height == 1024)
		{
			return 37;
		}
	}
	else if (width == 2800 && height == 1050)
	{
		return 38;
	}
	else if (width == 3200 && height == 1200)
	{
		return 39;
	}
	else if (width == 4096 && height == 1536)
	{
		return 40;
	}
	else
	{
		return 0;
	}
}

function getLanguageId(language)
{
	if (language == 'zh-cn')
	{
		return 1;
	}
	else if(language == 'zh-tw')
	{
		return 2;
	}
	else if(language == 'zh-hk')
	{
		return 3;
	}
	else if(language == 'en_hk')
	{
		return 4;
	}
	else if(language == 'en_us')
	{
		return 5;
	}
	else if(language == 'en_gb')
	{
		return 6;
	}
	else if(language == 'en_ww')
	{
		return 7;
	}
	else if(language == 'en_ca')
	{
		return 8;
	}
	else
	{
		return 0;
	}
}
 
var now = new Date();
year = now.getFullYear();
month = now.getMonth() + 1;
day = now.getDate();
hour = now.getHours();
month = month < 10 ? "0" + month : month;
day = day < 10 ? "0" + day : day;
visit_date = year + "-" + month + "-" + day;

//来路域名
var url = document.referrer;
reg = /http:\/\/([^\/])+/i;
domain = url.match(reg);
domain = (domain + "").split(",");						//转型并分隔
is_entrance = 0;
if (url == '')
{
	is_entrance = 1;
}

//client_key 用于判别新老访客，统计UV，只在入口页插此数据
client_key = cookie.get('_old_customer_');

//entrance_id 非入口页使用，用于标记本次访问的入口页在基础数据表中的ID
entrance_id = cookie.get('_entrance_id_');
have_jump = 1;
if (entrance_id != null)
{
	have_jump = 0;
}
else
{
	entrance_id = 0;
}

//传值到统计服务器上
sendAjax();

//传值到统计服务器上
// $.ajax(
// {
// 	type: "POST",
// 	url: "/DataReceive/receive_data",
// 	data: 
// 	{
// 		"browser": getBrowserVersion(), 
// 		"user_agent": navigator.userAgent, 
// 		"screen_id": getScreenId(screen.width, screen.height), 
// 		"domain": document.domain, 
// 		"visit_url": window.location.href, 
// 		"os_id": detectOS(), 
// 		"add_time": Date.parse(new Date()) / 1000, 
// 		"hour": hour, 
// 		"visit_date": visit_date, 
// 		"language_id": getLanguageId(browser.language), 
// 		"terminal_id": getTerminalId(), 
// 		//"from_domain": domain[0], 
// 		"from_url": document.referrer, 
// 		"is_entrance": is_entrance, 
// 		"page_title": document.title, 
// 		"client_key": client_key == null ? '' : client_key, 
// 		"entrance_id": entrance_id, 
// 		"have_jump": have_jump
// 		// "az_id": az_id,
// 		// "user_id": user_id,
// 		// "ip": ip
// 	},
// 	contentType: "application/json; charset=utf-8",
// 	dataType: "jsonp",
// 	jsonp: "callback",//服务端用于接收callback调用的function名的参数
//     jsonpCallback:"success_jsonpCallback",//callback的function名称
// 	success: function(data)
// 	{
// 		console.log(data);
// 		if (data.client_key != '')
// 		{
// 			cookie.set('_old_customer_', data.client_key, 86400 * 30);	//COOKIE有效期为一个月
// 		}
// 	},
// 	error: function(XMLHttpRequest, textStatus, errorThrown) {   
// 		 /*alert(XMLHttpRequest.status);
// 		 alert(XMLHttpRequest.readyState);
// 		 alert(textStatus);*/
// 		 consoel.log(textStatus+","+errorThrown);
// 	}
// });

// 创建XMLHttpRequest对象
function createXHR() {
    if (typeof XMLHttpRequest != "undefined") {
        return new XMLHttpRequest();
    } else if (typeof ActiveXObject != "undefined") {
        if (typeof arguments.callee.activeXString != "string") {
            var versions = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.3.0","MSXML2.XMLHttp"],
                i, len;
            for (i = 0, len = versions.length; i < len; i++) {
                try {
                    new ActiveXObject(versions[i]);
                    arguments.callee.activeXString = versions[i];
                    break;
                } catch (ex) {
                    //跳过
                }
            }
        }
        return new ActiveXObject(arguments.callee.activeXString);
    } else {
        throw new Error("暂不支持XMLHttpRequest");
    }
}

//发送数据
function sendAjax(){
	var xhr=new createXHR();

	xhr.onreadystatechange = function() {
	    if (xhr.readyState == 4) {
	        if ((xhr.status > 200 && xhr.status < 300) || xhr.status == 304) {
	            // alert(xhr.responseText);
	        } else {
				var obj = eval('(' + xhr.responseText + ')');
				if (obj.client_key != '')
				{
					cookie.set('_old_customer_', obj.client_key, 86400 * 30);	//COOKIE有效期为一个月
				}
	            // alert("请求成功: " + xhr.status+",responseText:"+xhr.responseText);
	        }
	    }
	};

	xhr.open("post", "/DataReceive/receive_data", true);
	xhr.setRequestHeader("Content-Type", "application/json; charset=utf-8");

	var data={
		"browser": getBrowserVersion(), 
		"user_agent": navigator.userAgent, 
		"screen_id": getScreenId(screen.width, screen.height), 
		"domain": document.domain, 
		"visit_url": window.location.href, 
		"os_id": detectOS(), 
		"add_time": Date.parse(new Date()) / 1000, 
		"hour": hour, 
		"visit_date": visit_date, 
		"language_id": getLanguageId(browser.language), 
		"terminal_id": getTerminalId(), 
		"from_domain": domain[0],
		"from_url": document.referrer,
		"is_entrance": is_entrance, 
		"page_title": document.title, 
		"client_key": client_key == null ? '' : client_key, 
		"entrance_id": entrance_id, 
		"have_jump": have_jump,
		"az_id": az_id,
		"user_id": user_id,
		"ip": ip
	};

	var sdata=JSON.stringify(data)
	// alert(sdata);
	xhr.send(sdata);
}

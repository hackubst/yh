<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8" />
	<title>跳转提示</title>
<style type="text/css">
{literal}
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; background: url(/Public/Images/acp/dis_jump1.jpg); }
.clearfix{*zoom:1;}
.clearfix:after{display: block;content: " ";height:0;visibility:hidden;clear: both;}
.fl{float: left;}
.fr{float: right;}
.msgbox{ width: 652px;  height: 293px; margin: 0 auto; margin-top: 100px; padding-top: 107px; background: url(/Public/Images/acp/dis_jump2.png) no-repeat;}
.msgbox-info{width:533px; height: 246px; margin-left: 58px; position: relative;}
.emo{ display: block; width: 61px; height: 61px; position: absolute; top: 55px; left: 99px;}
.success,.error{width:350px; height: 61px;line-height: 1.2em; font-size: 22px;position: absolute; top: 70px; left: 167px;}
.msg-jump{ width: 248px; height: 26px; line-height: 26px; overflow: hidden;position: absolute; top: 138px; left: 175px;}
.msg-jump-le{ float: left; width: 178px; height: 26px; line-height: 26px; color: #3d3f41; text-align: center; font-size: 14px;}
.msg-jump-le b{ color: #ff5400;}
.msg-jump-ri{ float: left; width: 70px; height: 26px; overflow: hidden; text-align: center;}
.msg-jump-ri a{ color: #fff; text-decoration: none; font-size: 14px;}
{/literal}
</style>
</head>
<body>
	<div class="msgbox">
		<div class="msgbox-info clearfix">
			{if isset($message)}
				<span class="emo fl"><img src="/Public/Images/acp/dis_jump4.png"></span>
				<span class="success fl">{$message}</span>
			{else}
				<span class="emo fl"><img src="/Public/Images/acp/dis_jump3.png"></span>
				<span class="error fl">{$error}</span>
			{/if}
			<div class="msg-jump">
				<div class="msg-jump-le">页面将在<b id="wait">{$waitSecond}</b>秒后自动跳转</div>
				<div class="msg-jump-ri"><a id="href" href="{$jumpUrl}">我知道了</a></div>
			</div>
		</div>
		
	</div>

	<!--<div class="system-message" style="display:none;">
		{if isset($message)}
		<h1>:)</h1>
		<p class="success">{$message}</p>
		{else}
		<h1>:(</h1>
		<p class="error">{$error}</p>
		{/if}
		<p class="detail"></p>
		<p class="jump">
		页面自动 <a id="href" href="{$jumpUrl}">跳转</a> 等待时间： <b id="wait">{$waitSecond}</b>
		</p>
	</div>-->
{literal}
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time == 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
{/literal}
</body>
</html>
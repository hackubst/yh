<?php
/* *
 * 功能：快捷登录接口接入页
 * 版本：3.2
 * 修改日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************注意*************************
 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
 * 如果不想使用扩展功能请把扩展功能参数赋空值。
 */
header('Content-Type: text/html; charset=utf-8');
require_once("../alipay.config.php");
require_once("lib/alipay_service.class.php");

//从商品详情页完成快登后，直接进入count页，用cookie实现
$id = @$_GET['id'];
$number = @$_GET['number'];
$color_id = @$_GET['color_id'];
$size_id = @$_GET['size_id'];

if ($id)
{
	//setcookie('fastlogin_goods_id', $id, time() + 60, $cookiepath, $cookiedomain, $cookiesecure);
	setcookie('fastlogin_goods_id', $id);
	setcookie('fastlogin_number', $number);
	setcookie('fastlogin_color_id', $color_id);
	setcookie('fastlogin_size_id', $size_id);
}

setcookie('http_referer', @$_SERVER['HTTP_REFERER']);
/**************************请求参数**************************/

//页面跳转同步通知路径 要用 http://格式的完整路径，不允许加?id=123这类自定义参数
//return_url的域名不能写成http://localhost/alipay.auth.authorize_php_utf8/return_url.php ，否则会导致return_url执行无效
$aliapy_config['return_url']   = 'http://' . $config['server_name'].'/libs/alipay/fastlogin/return_url.php';


//扩展功能参数——防钓鱼//

//防钓鱼时间戳
$anti_phishing_key  = '';
//获取客户端的IP地址，建议：编写获取客户端IP地址的程序
$exter_invoke_ip = '';
//注意：
//1.请慎重选择是否开启防钓鱼功能
//2.exter_invoke_ip、anti_phishing_key一旦被使用过，那么它们就会成为必填参数
//3.开启防钓鱼功能后，服务器、本机电脑必须支持SSL，请配置好该环境。
//示例：
//$exter_invoke_ip = '202.1.1.1';
//$ali_service_timestamp = new AlipayService($aliapy_config);
//$anti_phishing_key = $ali_service_timestamp->query_timestamp();//获取防钓鱼时间戳函数

/*************************************************************/

//构造要请求的参数数组，无需改动
$parameter = array(
        //扩展功能参数——防钓鱼
        "anti_phishing_key"	=> $anti_phishing_key,
		"exter_invoke_ip"	=> $exter_invoke_ip,
);

//构造快捷登录接口
$alipayService = new AlipayService($aliapy_config);
$html_text = $alipayService->alipay_auth_authorize($parameter);
echo $html_text;

?>

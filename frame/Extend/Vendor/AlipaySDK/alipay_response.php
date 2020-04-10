<?php
define('IN_360SHOP', true);

$path = '../../';
include($path . 'extension.inc');
include($path . 'global.'.$phpEx);
//
require(INCLUDES . 'ids.php');

//写日志
$file = fopen($path . 'logs/alipay_log.txt', 'a');
$out_trade_no = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : '';
fwrite($file, date('Y-m-d H:i:s', time() + 28800) . ', 外部交易号：' . $_POST['out_trade_no'] . "\n");

// Set page ID for session management
//
$userdata = session_pagestart($user_ip, PAGE_LOGIN);
init_userprefs($userdata);
//
require(INCLUDES . 'ids.php');

include(INCLUDES . 'page_header.' . $phpEx);

//集成支付宝代码
require_once(LIBS . 'payment'.DS.'alipay.'. $phpEx);

if(isset($_POST['out_trade_no']) && $_POST['out_trade_no'])
{	
	//Post 参数
	$alipay = new alipay();
	$alipay->pay_response_post();
}


?>

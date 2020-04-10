<?php
define('IN_360SHOP', true);

$path = '../../';
include($path . 'extension.inc');
include($path . 'global.'.$phpEx);
//


// Set page ID for session management
//
$userdata = session_pagestart($user_ip, PAGE_LOGIN);
init_userprefs($userdata);
//

include(INCLUDES . 'page_header.' . $phpEx);

//集成支付宝代码
require_once(LIBS . 'voucher'.DS.'alipay.'. $phpEx);

if(isset($_POST['out_trade_no']) && $_POST['out_trade_no'])
{	
	//Post 参数
	$alipay = new v_alipay();
	$alipay->pay_response_post();
}
?>
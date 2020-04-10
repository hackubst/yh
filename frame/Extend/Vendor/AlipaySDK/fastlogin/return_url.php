<?php
/* * 
 * 功能：支付宝页面跳转同步通知页面
 * 版本：3.2
 * 日期：2011-03-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 *************************页面功能说明*************************
 * 该页面可在本机电脑测试
 * 该页面称作“页面跳转同步通知页面”
 * 可放入HTML等美化页面的代码和商户业务逻辑处理程序代码
 * 该页面可以使用PHP开发工具调试，也可以使用写文本函数AlipayFunction.logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
 */
header('Content-Type: text/html; charset=utf-8');
require_once("../alipay.config.php");
require_once("lib/alipay_notify.class.php");

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyReturn();
$user_id = 0;
$session_id = array();
if($verify_result) {
	//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码

		
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
    $alipayid		= @$_GET['user_id'];	//支付宝用户id
    $token			= @$_GET['token'];		//授权令牌
	$user_realname 	= isset($_GET['real_name'])? $_GET['real_name']:'';	//真实姓名

	//执行商户的业务程序
	
	//echo "验证成功<br />";
	//echo "token:".$token;
	
	//检查alipayid
	$sql = "SELECT user_id FROM " . USERS_TABLE . " WHERE alipayid = '".$alipayid."'";
	$result = $db->sql_query($sql);
	$user_id = $db->sql_fetchfield('user_id');
	
	//实例化对象
	include(LIBS . 'libs_reg.' . $phpEx);
	$reg = new reg;
	
	//如果未登录状态
	if($userdata['user_id'] == ANONYMOUS)
	{	
		if($user_id)
		{
			$arr_params = array(
				'alipayid'		=> $alipayid,
				'alipaytoken'	=> $token
			);
			
			if ($user_realname)
			{
				$arr_params['user_realname'] = $user_realname;
			}
			
			$reg->edit($arr_params, $user_id);
			
			//登录seesion
			$session_id = session_begin($user_id, $user_ip, PAGE_INDEX, FALSE, 0, 0);
	
			//更新登录UESRID标签
			$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
			$db->sql_query($sql);
		}
		else
		{
			$arr_params = array(
				'alipayid'		=> $alipayid,
				'user_ip'		=> $user_ip,
				'user_realname'	=> $user_realname,
				'user_lastvisit'=> time(),
				'user_regdate'	=> time(),
				'alipaytoken'	=> $token
			);
	
			if($user_id = $reg->register($arr_params))
			{
				//登录seesion
				$session_id = session_begin($user_id, $user_ip, PAGE_INDEX, FALSE, TRUE, 0);
				
				//更新登录UESRID标签
				$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
				$db->sql_query($sql);
			}
		}
	}
	else
	{
		if($user_id)
		{
			$sql = "UPDATE " . USERS_TABLE . " 
					SET alipayid = '',
					alipaytoken = ''
					WHERE user_id = " . $user_id ." LIMIT 1";
			$db->sql_query($sql);
		}
		
		$arr_params = array(
			'alipayid'		=> $alipayid,
			'alipaytoken'	=> $token
		);

		$reg->edit($arr_params, $userdata['user_id']);
	}

	//跳转到系统首页
	$url = str_replace('&amp;', '&', append_sid('/'));
	
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
	
	//etao专用
	if(@$_GET['target_url'] != "") 
	{
		//程序自动跳转到target_url参数指定的url去
		
		//echo $_GET['target_url'];		
		$str = "<script type='text/javascript'>window.location ='".$_GET['target_url']."';</script>";	
		echo $str;	
	}
	else
	{
		$href_url = isset($_COOKIE['http_referer']) ? $_COOKIE['http_referer'] : '/';

		//取到fastlogin_goods_id，说明它是从商品详情页过来的，跳转到count上
		if (isset($_COOKIE['fastlogin_goods_id']) && $_COOKIE['fastlogin_goods_id'] > 0)
		{
			$href_url = append_sid('/?mod=ucp&do=count&id=' . $_COOKIE['fastlogin_goods_id']);
			
			$cart_array=array(
			'goods_id'	=> $_COOKIE['fastlogin_goods_id'],
			'number'	=> $_COOKIE['fastlogin_number'],
			'color_id'	=> $_COOKIE['fastlogin_color_id'],
			'size_id'	=> $_COOKIE['fastlogin_size_id'],
			);
			
			include(INCLUDES . 'func_cart.php');
			include(INCLUDES . 'func_goods.php');
			add_cart($cart_array);
			
			$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
			$db->sql_query($sql);
			
			setcookie('fastlogin_goods_id', 0);
			setcookie('fastlogin_number', 0);
			setcookie('fastlogin_color_id', 0);
			setcookie('fastlogin_size_id', 0);
		}
		elseif (isset($_COOKIE['http_referer']))
		{
			if(preg_match("/flow.php/i", $_COOKIE['http_referer']))	//说明是从购物车页进来的，
			{
				$href_url = append_sid('/?mod=ucp&do=count');
				setcookie('http_referer', '');
			}
		}
		
		//Header("Location:" . $href_url);
		//redirect($href_url);
		$href_url = ($href_url == '/') ? 'http://'.$config['server_name'] : $href_url;
		
		$str = "<script type='text/javascript'>";
		$str .= "window.opener.location.href = '" . $href_url . "';";
		$str .= "window.close();";
		$str .= "</script>";
		
		echo $str;
	}
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
    echo "验证失败";
}


?>
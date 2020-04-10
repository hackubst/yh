<?php

//载入alipay
require_once(LIBS . 'payment'.DS.'alipay.'. $phpEx);

$ArrVar = array(
'action'		=> (string) '',
'alipay_email'	=> (string) '',
'email'			=> (string) '',
'user_id'		=> (string) '',
'mobile'		=> (string) '',
'phone'			=> (string) '',
'real_name'		=> (string) '',
'gender'		=> (string) '',
'address'		=> (string) '',
'zip'			=> (string) '',
'province'		=> (string) '',
'city'			=> (string) ''
);

foreach ($ArrVar as $var => $default)
{
	$$var = request_var($var, $default, true);
}

//支付宝user_id赋值
$alipayid = $user_id;

//集成支付宝
if($alipay_email
&& eregi("^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$", $alipay_email)
|| eregi("^[0-9]{11}$", $alipay_email))
{
	$alipay  = new alipay();
	$return_url = $alipay->pay_check_user($alipay_email, '/login.php');
	header("Location: ".$return_url);
	exit;

}
else if($email && eregi("^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$", $email))
{
	$alipay  = new alipay();
	if($alipay->pay_check_alilogin_sign())
	{
		//检查邮箱
		$sql = "SELECT user_id,role_type FROM " . USERS_TABLE . " WHERE user_email = '".$email."'";
		$result = $db->sql_query($sql);
		$user_info = $db->sql_fetchrow($result);
		$user_id = isset($user_info['user_id']) ? $user_info['user_id'] : 0;
		if (!$user_id && $alipayid)
		{
			$sql = "SELECT user_id,role_type FROM " . USERS_TABLE . " WHERE alipayid = '".$alipayid."'";
			$result = $db->sql_query($sql);
			$user_info = $db->sql_fetchrow($result);
			$user_id = isset($user_info['user_id']) ? $user_info['user_id'] : 0;
		}

		//实例化对象
		include(LIBS . 'libs_reg.' . $phpEx);
		$reg = new reg; //实例化对象

		if(!$address)
		{
			require_once(LIBS . 'libs_acquisition.'. $phpEx);
			require_once(LIBS . 'payment'.DS.'alipay.'. $phpEx);

			$alipay  = new alipay();
			$link = $alipay->pay_check_address($email);

			$html = new getHtmlContent;
			$html->URL 			= $link;
			$html->startFlag 	= '<is_success>';
			$html->endFlag 		= '</is_success>';
			$sourceStr = $html->getPageContent();

			// 获得网站指定内容
			$sList = $html->getContent( $sourceStr, $html->startFlag, $html->endFlag );

			if($sList == 'T')
			{
				//抓取收货地址
				$html->startFlag 	= '<address>';
				$html->endFlag 		= '</address>';
				$sourceStr = $html->getPageContent();
				// 获得网站指定内容
				$address = $html->getContent( $sourceStr, $html->startFlag, $html->endFlag );
			}
		}

		switch($gender)
		{
			case 'm':
				$gender =1;
				break;
			case 'w':
				$gender =2;
				break;
			default:
				$gender =0;
				break;
		}

		$arr_params = array(
		'alipayid'			=> $alipayid,
		'user_email'		=> $email,
		'user_realname'		=> $real_name,
		'user_phone'		=> $phone,
		'user_mobile'		=> $mobile,
		'user_gender'		=> $gender,
		'user_address'		=> $address,
		'user_zip_code'		=> $zip,
		'user_country'		=> 1,
		'user_province'		=> get_region_id($province),
		'user_city'			=> get_region_id($city)
		);

		if(!$real_name)
		{
			unset($arr_params['user_realname']);
		}
		if(!$phone)
		{
			unset($arr_params['user_phone']);
		}
		if(!$address)
		{
			unset($arr_params['user_address']);
		}
		if(!$zip)
		{
			unset($arr_params['user_zip_code']);
		}
		if(!$province)
		{
			unset($arr_params['user_province']);
		}
		if(!$city)
		{
			unset($arr_params['user_city']);
		}

		if(isset($user_id) && $user_id)
		{
			$reg->edit($arr_params, $user_id);//同步信息
			$session_id = session_begin($user_id, $user_ip, PAGE_INDEX, FALSE, 0, 0);

			//同步登陆前后用户购物车数据
			$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
			$db->sql_query($sql);
		}
		else
		{
			$arr_params += array(
			'user_ip'			=> $user_ip,
			'user_lastvisit'	=> time(),
			'user_regdate'		=> time()
			);

			if($user_id = $reg->register($arr_params))
			{
				$session_id = session_begin($user_id, $user_ip, PAGE_INDEX, FALSE, TRUE, 0);
				//同步登陆前后用户购物车数据
				$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
				$db->sql_query($sql);
			}
		}
	}

	if ($user_info['role_type'] == 'acp')
	{
		//调整到会员首页
		$url = str_replace('&amp;', '&', append_sid('/?mod=acp'));
		redirect($url);
		exit;
	}

	//调整到会员首页
	$url = str_replace('&amp;', '&', append_sid('/'));
	redirect($url);
	exit;
}
else if($user_id && eregi("^[A-Za-z0-9]+$", $user_id))
{
	$alipay  = new alipay();

	if($alipay->pay_check_alilogin_sign())
	{
		//检查邮箱
		$sql = "SELECT user_id FROM " . USERS_TABLE . " WHERE alipayid = '".$alipayid."'";
		$result = $db->sql_query($sql);
		$user_id = $db->sql_fetchfield('user_id');

		//实例化对象
		include(LIBS . 'libs_reg.' . $phpEx);
		$reg = new reg; //实例化对象

		switch($gender)
		{
			case 'm':
				$gender =1;
				break;
			case 'w':
				$gender =2;
				break;
			default:
				$gender =0;
				break;
		}

		$arr_params = array(
		'alipayid'			=> $alipayid,
		'user_realname'		=> $real_name,
		'user_phone'		=> $phone,
		'user_mobile'		=> $mobile,
		'user_gender'		=> $gender,
		'user_address'		=> $address,
		'user_zip_code'		=> $zip,
		'user_country'		=> 1,		
		'user_province'		=> get_region_id($province),
		'user_city'			=> get_region_id($city)
		);

		if(!$real_name)
		{
			unset($arr_params['user_realname']);
		}
		if(!$phone)
		{
			unset($arr_params['user_phone']);
		}
		if(!$address)
		{
			unset($arr_params['user_address']);
		}
		if(!$zip)
		{
			unset($arr_params['user_zip_code']);
		}
		if(!$province)
		{
			unset($arr_params['user_province']);
		}
		if(!$city)
		{
			unset($arr_params['user_city']);
		}

		if(isset($user_id) && $user_id)
		{
			$reg->edit($arr_params, $user_id);//同步信息
			$session_id = session_begin($user_id, $user_ip, PAGE_INDEX, FALSE, 0, 0);
			//同步登陆前后用户购物车数据
			if ($user_id)
			{
				$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
				$db->sql_query($sql);
			}
		}
		else
		{
			$arr_params += array(
			'user_ip'			=> $user_ip,
			'user_lastvisit'	=> time(),
			'user_regdate'		=> time()
			);

			if($user_id = $reg->register($arr_params))
			{
				$session_id = session_begin($user_id, $user_ip, PAGE_INDEX, FALSE, TRUE, 0);
				//同步登陆前后用户购物车数据
				if ($user_id)
				{
					$sql = "UPDATE " . CART_TABLE . " SET user_id = " . $user_id . " WHERE session_id = '" . $session_id['session_id'] . "'";
					$db->sql_query($sql);
				}
			}
		}
	}
	//调整到会员首页
	$url = str_replace('&amp;', '&', append_sid('/'));
	redirect($url);
	exit;
}

?>
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
$verify_result = $alipayNotify->verifyNotify();
$receiveAddress = '';
if($verify_result)
{
	//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码

	/*
	<receiveaddress>
	<address>杭州市西湖区黄姑山路29号颐高创业大厦816</address>
	<address_code>330106</address_code>西湖区
	<city>杭州市</city><fullname>胡建军</fullname>
	<mobile_phone>13588803560</mobile_phone>
	<post>310011</post>
	<prov>浙江省</prov>
	</receiveaddress>";
	*/

	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
	//获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
	//支付宝用户id
	$user_id = $_POST['user_id'];

	//用户选择的收货地址
	$receive_address = (get_magic_quotes_gpc()) ? stripslashes($_POST['receive_address']) : $_POST['receive_address'];

	//对receive_address做XML解析，获得各节点信息
	$para_data = @XML_unserialize($receive_address);

	//获取地址
	$address = '';
	if( ! empty($para_data['receiveAddress']['address']) ) {
		$address = $para_data['receiveAddress']['address'];
	}

	//获取邮编
	$post = '';
	if( ! empty($para_data['receiveAddress']['post']) ) {
		$post = $para_data['receiveAddress']['post'];
	}

	//获取城市
	$city = '';
	if( ! empty($para_data['receiveAddress']['city']) ) {
		$city = $para_data['receiveAddress']['city'];
	}

	//获取省份
	$prov = '';
	if( ! empty($para_data['receiveAddress']['prov']) ) {
		$prov = $para_data['receiveAddress']['prov'];
	}

	//获取收货人名称
	$fullname = '';
	if( ! empty($para_data['receiveAddress']['fullname']) ) {
		$fullname = $para_data['receiveAddress']['fullname'];
	}

	//获取收货人手机
	$mobile_phone = '';
	if( ! empty($para_data['receiveAddress']['mobile_phone']) ) {
		$mobile_phone = $para_data['receiveAddress']['mobile_phone'];
	}
	
	
	//执行商户的业务程序
	//echo "验证成功";
	//echo "<br />";
	//echo $fullname;

	//echo "<pre>";
	//print_r($para_data);
	//exit;
	//echo "receive_address:".$_POST['receive_address'];
	//$receiveAddress = urlencode($address.$fullname);
	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

	include(INCLUDES . 'func_user.'.$phpEx);
	$province_id = get_region_id($prov);
	$city_id 	 = get_region_id($city, true);
	
	$url = 	append_sid("/?mod=ucp&do=count");
	include(LIBS . 'libs_reg.' . $phpEx);
	$reg = new reg; //实例化对象

	if($userdata['user_id'])
	{
		$address_id = 0;
		//循环判断当前是否已有相同的地址
		$sql = "SELECT * FROM " . USERS_ADDRESS . " WHERE user_id = " . $userdata['user_id'];
		$db->sql_query($sql);
		while ($row = $db->sql_fetchrow())
		{
			if ($row['consignee'] == $fullname && $row['province_id'] == $province_id && $row['city_id'] == $city_id &&
			$row['address'] == $address && $row['mobile'] == $mobile_phone)
			{
				$address_id = $row['id'];
			}
		}

		if(!$address_id)
		{
			//写入address表
			$arr_params = array(
			'user_id'		=> $userdata['user_id'],
			'consignee'		=> $fullname,
			'country_id' 	=> 0,
			'province_id'	=> $province_id,
			'city_id'		=> $city_id,
			'address'		=> $address,
			'zipcode'		=> $post,
			'mobile'		=> $mobile_phone,
			'isdefault'		=> 0,
			);

			ucp_data_add($arr_params, USERS_ADDRESS);
			$address_id = $db->sql_nextid();
		}

		$url = append_sid("/?mod=ucp&amp;do=count&amp;action=logistics&amp;aid=" . $address_id);
	}

	//跳转---


	//redirect($url);
	//exit;
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
else {
	//验证失败
	//如要调试，请看alipay_notify.php页面的return_verify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
	echo "验证失败";
	exit;
}
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script type="text/javascript">
window.location="<?php echo $url; ?>";
</script>
<title></title>
</head>
<body>
</body>
</html>
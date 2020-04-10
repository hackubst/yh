<?php
class IndexAction extends FrontAction
{
	function _initialize() 
	{
		if (ACTION_NAME != 'js')
		{
			parent::_initialize();
		}
	}

	//wx-js
	function js()
	{
		//获取jsapi-ticket
		Vendor('Wxin.WeiXin');
		$appid = C('appid');
		$secret = C('appsecret');
		$wx_obj = new WxApi();
		$access_token = WxApi::getAccessToken($appid, $secret);
		$result = $wx_obj->getJsapiTicket($access_token);
		$ticket = $result['ticket'];
$user_id = intval(session('user_id'));
$user_obj = new UserModel($user_id);
$arr = array(
	'ticket'	=> $ticket
);
$user_obj->setUserInfo($arr);
$user_obj->saveUserInfo();
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/Index/js';
		vendor('wxpay.WxPayPubHelper');
		$address_obj = new Address();
		$params = $address_obj->getParametersAll($ticket, $url, $appid);
#echo "<pre>";
#print_r($params);
#die;
		$this->assign('params', $params);
log_file($params['signature']);
		$this->assign('head_title', '微信JS-SDK测试');
		$this->display();
	}

	//unserialize
	function unserialize()
	{
		if (isset($_POST['submit']))
		{
			echo "<pre>反序列化值：";
			print_r(unserialize($_POST['str']));
		}
		$this->display();
	}

	//解析json
	function parse_json()
	{
		print_r(json_decode('{"code":42037,"error_msg":"\u62a2\u5355\u5931\u8d25"}', true));
		if (isset($_POST['submit']))
		{
			echo "<pre>json值：";
			print_r(json_decode($_POST['str']), true);
		}
		$this->display();
	}

	//MD5测试
	function getmd5()
	{
		if (isset($_POST['submit']))
		{
			echo "<h1>MD5值：" . md5($_POST['str']) . "</h1>";
		}
		$this->display();
	}

	//静态页跳转方法
	function redirect($url)
	{
		if (!empty($_POST))
		{
			redirect($url);
		}
	}

	//首页
	public function index()
	{
		//if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
	//	{
			redirect('/IndexPc/index');
		//}
$this->assign('head_title', '金龙28');
$this->display();
	}

	/**
     * 企业付款测试
     */
    public function rebate()
    {
	    vendor("wxpay.WxPayPubHelper");
        $mchPay = new WxMchPay();
        // 用户openid
	$user_id = cur_user_id();
	$user_obj = new UserModel($user_id);
	$user_info = $user_obj->getUserInfo('openid');
	$mchPay->setParameter('openid', 'o7bmws_dlX6MYz1W_zguVlRsUOYA');
        // 商户订单号
        $mchPay->setParameter('partner_trade_no', 'test-'.time());
        // 校验用户姓名选项
        $mchPay->setParameter('check_name', 'NO_CHECK');
        // 企业付款金额  单位为分
        $mchPay->setParameter('amount', 100);
        // 企业付款描述信息
        $mchPay->setParameter('desc', '开发测试');
        // 调用接口的机器IP地址  自定义
        $mchPay->setParameter('spbill_create_ip', '127.0.0.1'); # getClientIp()
        // 收款用户姓名
        // $mchPay->setParameter('re_user_name', 'Max wen');
        // 设备信息
        // $mchPay->setParameter('device_info', 'dev_server');

        $data = $mchPay->getResult();
log_file('pay_result = ' . json_encode($data), 'qiye_pay', true);
        if( !empty($data) )
	{
		if ($data['result_code'] == 'FAIL')
		{
			echo $data['err_code_des'];
		}
		dump($data);
		return $data;
	}
    }

	function baidu_map()
	{
		dump(file_get_contents('http://api.map.baidu.com/place/v2/search?q=乐富海邦园&region=杭州&output=json&ak=1NyB7vqPhw6BhHXUvHum4NeD'));
		#$this->display();
	}

	function chache()
	{
		$arr = json_decode('{"status":"0","msg":"","result":{"lsprefix":"苏","lsnum":"CSG975","carorg":"hangzhou","usercarid":"14652470","count":"2","totalprice":"100","totalscore":"0","list":[{"time":"2017-10-17 07:50:00","address":"和平路和平东路与民祥园路交叉口500米","content":"违反规定停放、临时停车，妨碍其它车辆、行人通行，驾驶人不在现场","legalnum":"10391","price":"50","score":"0","handlefee":"","agency":"","illegalid":"13211098","province":"江苏","city":"徐州","town":"云龙区","lat":"0.0000000000","lng":"0.0000000000","canhandle":"0"},{"time":"2017-10-26 08:01:00","address":"黄山大道与民祥路","content":"违反规定停放、临时停车，妨碍其它车辆、行人通行，驾驶人不在现场","legalnum":"10391","price":"50","score":"0","handlefee":"","agency":"","illegalid":"13211099","province":"江苏","city":"徐州","town":"","lat":"34.2684550176","lng":"117.2499072279","canhandle":"0"}]}}', true);
		dump($arr);
		die;
		$this->assign('head_title', '违章查询');
		$this->display();
	}

	public function quality_ball(){

		$this->assign('head_title', '质量球');
		$this->display();
	}

	// 金龙贷
	public function loan() {
		$this->assign('head_title', '金龙贷');
		$this->display();
	}
}

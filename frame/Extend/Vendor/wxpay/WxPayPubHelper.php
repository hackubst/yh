<?php
/**
 * 微信支付帮助库
 * ====================================================
 * 接口分三种类型：
 * 【请求型接口】--Wxpay_client_
 * 		统一支付接口类--UnifiedOrder
 * 		订单查询接口--OrderQuery
 * 		退款申请接口--Refund
 * 		退款查询接口--RefundQuery
 * 		对账单接口--DownloadBill
 * 		短链接转换接口--ShortUrl
 * 【响应型接口】--Wxpay_server_
 * 		通用通知接口--Notify
 * 		Native支付——请求商家获取商品信息接口--NativeCall
 * 【其他】
 * 		静态链接二维码--NativeLink
 * 		JSAPI支付--JsApi
 * =====================================================
 * 【CommonUtil】常用工具：
 * 		trimString()，设置参数时需要用到的字符处理函数
 * 		createNoncestr()，产生随机字符串，不长于32位
 * 		formatBizQueryParaMap(),格式化参数，签名过程需要用到
 * 		getSign(),生成签名
 * 		arrayToXml(),array转xml
 * 		xmlToArray(),xml转 array
 * 		postXmlCurl(),以post方式提交xml到对应的接口url
 * 		postXmlSSLCurl(),使用证书，以post方式提交xml到对应的接口url
*/
	include_once("SDKRuntimeException.php");
	include_once("WxPay.pub.config.php");

/**
 * 所有接口的基类
 */
class Common_util_pub
{
	function __construct() {
	}

	function trimString($value)
	{
		$ret = null;
		if (null != $value) 
		{
			$ret = $value;
			if (strlen($ret) == 0) 
			{
				$ret = null;
			}
		}
		return $ret;
	}
	
	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	public function createNoncestr( $length = 32 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}
	
	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar;
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}
	
	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj)
	{
		$wxpay_obj = new WXPayModel();
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤二：在string后加入KEY
		$String = $String."&key=".$wxpay_obj->KEY;
		#echo "【string2】".$String."</br>";
		//签名步骤三：MD5加密
		log_file('sign_string = ' . $String, 'mobile_pay_code');
		$String = md5($String);
		#echo "【string3】 ".$String."</br>";
		//签名步骤四：所有字符转为大写
		$result_ = strtoupper($String);
		log_file('sign = ' . $result_, 'mobile_pay_code');
		//echo "【result】 ".$result_."</br>";
		return $result_;
	}
	
	/**
	 * 	作用：array转xml
	 */
	function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
        	 if (is_numeric($val))
        	 {
        	 	$xml.="<".$key.">".$val."</".$key.">"; 

        	 }
        	 else
        	 	$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";  
        }
        $xml.="</xml>";
        return $xml; 
    }
	
	/**
	 * 	作用：将xml转为array
	 */
	public function xmlToArray($xml)
	{		
        //将XML转为array        
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);		
		return $array_data;
	}

	/**
	 * 	作用：以post方式提交xml到对应的接口url
	 */
	public function postXmlCurl($xml,$url,$second=30)
	{
        //初始化curl        
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	curl_setopt($ch,CURLOPT_REFERER, "http://" . $_SERVER['HTTP_HOST'] . '/FrontOrder/order_submit/');   //构造来路
		//设置header
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//post提交方式
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		//运行curl
        $data = curl_exec($ch);
		#curl_close($ch);
		//返回结果
		if($data)
		{
			curl_close($ch);
			return $data;
		}
		else 
		{ 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}

	/**
	 * 	作用：使用证书，以post方式提交xml到对应的接口url
	 */
	function postXmlSSLCurl($xml,$url,$second=30)
	{
		$wxpay_obj = new WXPayModel();
		$ch = curl_init();
		//超时时间
		curl_setopt($ch,CURLOPT_TIMEOUT,$second);
		//这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		//设置header
		curl_setopt($ch,CURLOPT_HEADER,FALSE);
		//要求结果为字符串且输出到屏幕上
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
		//设置证书
		//使用证书：cert 与 key 分别属于两个.pem文件
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLCERT, $wxpay_obj->SSLCERT_PATH);
		//默认格式为PEM，可以注释
		curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
		curl_setopt($ch,CURLOPT_SSLKEY, $wxpay_obj->SSLKEY_PATH);
		//post提交方式
		curl_setopt($ch,CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
		$data = curl_exec($ch);
		//返回结果
		if($data){
			curl_close($ch);
			return $data;
		}
		else { 
			$error = curl_errno($ch);
			echo "curl出错，错误码:$error"."<br>"; 
			echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
			curl_close($ch);
			return false;
		}
	}
	
	/**
	 * 	作用：打印数组
	 */
	function printErr($wording='',$err='')
	{
		print_r('<pre>');
		echo $wording."</br>";
		var_dump($err);
		print_r('</pre>');
	}

	//获取access_token
	private function getAccessToken()
	{
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(file_get_contents("access_token.json"));
		if ($data->expire_time < time()) {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
			$res = json_decode($this->httpGet($url));
			$access_token = $res->access_token;
			if ($access_token) {
				$data->expire_time = time() + 7000;
				$data->access_token = $access_token;
				$fp = fopen("access_token.json", "w");
				fwrite($fp, json_encode($data));
				fclose($fp);
			}
		} else {
			$access_token = $data->access_token;
		}

		return $access_token;
	}
}

/**
 * 请求型接口的基类
 */
class Wxpay_client_pub extends Common_util_pub 
{
	var $parameters;//请求参数，类型为关联数组
	public $response;//微信返回的响应
	public $result;//返回参数，类型为关联数组
	var $url;//接口链接
	var $curl_timeout;//curl超时时间
	
	/**
	 * 	作用：设置请求参数
	 */
	function setParameter($parameter, $parameterValue)
	{
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 	作用：设置标配的请求参数，生成签名，生成接口参数xml
	 */
	function createXml()
	{
		$wxpay_obj = new WXPayModel();
	   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
	   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
	    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
	    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
	    return  $this->arrayToXml($this->parameters);
	}
	
	/**
	 * 	作用：post请求xml
	 */
	function postXml()
	{
	    $xml = $this->createXml();
log_file('xml = ' . $xml, 'add_order', true);
		#dump($xml);
		#die;
		$this->response = $this->postXmlCurl($xml,$this->url,$this->curl_timeout);
		return $this->response;
	}
	
	/**
	 * 	作用：使用证书post请求xml
	 */
	function postXmlSSL()
	{	
	    $xml = $this->createXml();
		$this->response = $this->postXmlSSLCurl($xml,$this->url,$this->curl_timeout);
		return $this->response;
	}

	/**
	 * 	作用：获取结果，默认不使用证书
	 */
	function getResult() 
	{		
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}
}


/**
 * 统一支付接口类
 */
class UnifiedOrder_pub extends Wxpay_client_pub
{
	function __construct()
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;
	}

	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		try
		{
			//检测必填参数
			if($this->parameters["out_trade_no"] == null) 
			{
				throw new SDKRuntimeException("缺少统一支付接口必填参数out_trade_no！"."<br>");
			}elseif($this->parameters["body"] == null){
				throw new SDKRuntimeException("缺少统一支付接口必填参数body！"."<br>");
			}elseif ($this->parameters["total_fee"] == null ) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数total_fee！"."<br>");
			}elseif ($this->parameters["notify_url"] == null) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数notify_url！"."<br>");
			}elseif ($this->parameters["trade_type"] == null) {
				throw new SDKRuntimeException("缺少统一支付接口必填参数trade_type！"."<br>");
			}elseif ($this->parameters["trade_type"] == "JSAPI" &&
				$this->parameters["openid"] == NULL){
				throw new SDKRuntimeException("统一支付接口中，缺少必填参数openid！trade_type为JSAPI时，openid为必填参数！"."<br>");
			}
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		   	$this->parameters["spbill_create_ip"] = $_SERVER['REMOTE_ADDR'];//终端ip	    
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}

	/**
	 * 获取prepay_id
	 */
	function getPrepayId()
	{
		$this->postXml();
		$this->result = $this->xmlToArray($this->response);
log_file('result = ' . json_encode($this->result), 'getPrepayId');
		#var_dump($this->result);
		#die;
		$prepay_id = $this->result["prepay_id"];
		return $prepay_id;
	}

	/**
	 * 获取扫码支付链接
	 */
	function getCodeUrl()
	{
		#var_dump($this->result);
		#die;
		return $this->result['code_url'];
	}
}

/**
 * 订单查询接口
 */
class OrderQuery_pub extends Wxpay_client_pub
{
	function __construct() 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/orderquery";
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;		
	}

	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		try
		{
			//检测必填参数
			if($this->parameters["out_trade_no"] == null && 
				$this->parameters["transaction_id"] == null) 
			{
				throw new SDKRuntimeException("订单查询接口中，out_trade_no、transaction_id至少填一个！"."<br>");
			}
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}

}

/**
 * 退款申请接口
 */
class Refund_pub extends Wxpay_client_pub
{
	
	function __construct() {
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;		
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		try
		{
			//检测必填参数
			if($this->parameters["out_trade_no"] == null && $this->parameters["transaction_id"] == null) {
				throw new SDKRuntimeException("退款申请接口中，out_trade_no、transaction_id至少填一个！"."<br>");
			}elseif($this->parameters["out_refund_no"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数out_refund_no！"."<br>");
			}elseif($this->parameters["total_fee"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数total_fee！"."<br>");
			}elseif($this->parameters["refund_fee"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数refund_fee！"."<br>");
			}elseif($this->parameters["op_user_id"] == null){
				throw new SDKRuntimeException("退款申请接口中，缺少必填参数op_user_id！"."<br>");
			}
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	/**
	 * 	作用：获取结果，使用证书通信
	 */
	function getResult() 
	{		
		$this->postXmlSSL();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}
	
}


/**
 * 退款查询接口
 */
class RefundQuery_pub extends Wxpay_client_pub
{
	
	function __construct() {
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/refundquery";
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;		
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["out_refund_no"] == null &&
				$this->parameters["out_trade_no"] == null &&
				$this->parameters["transaction_id"] == null &&
				$this->parameters["refund_id "] == null) 
			{
				throw new SDKRuntimeException("退款查询接口中，out_refund_no、out_trade_no、transaction_id、refund_id四个参数必填一个！"."<br>");
			}
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}

	/**
	 * 	作用：获取结果，使用证书通信
	 */
	function getResult() 
	{		
		$this->postXmlSSL();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}

}

/**
 * 对账单接口
 */
class DownloadBill_pub extends Wxpay_client_pub
{

	function __construct() 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/pay/downloadbill";
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;		
	}

	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["bill_date"] == null ) 
			{
				throw new SDKRuntimeException("对账单接口中，缺少必填参数bill_date！"."<br>");
			}
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 	作用：获取结果，默认不使用证书
	 */
	function getResult() 
	{		
		$this->postXml();
		$this->result = $this->xmlToArray($this->result_xml);
		return $this->result;
	}
	
	

}

/**
 * 短链接转换接口
 */
class ShortUrl_pub extends Wxpay_client_pub
{
	function __construct() 
	{
		//设置接口链接
		$this->url = "https://api.mch.weixin.qq.com/tools/shorturl";
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;		
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{		
		try 
		{
			if($this->parameters["long_url"] == null ) 
			{
				throw new SDKRuntimeException("短链接转换接口中，缺少必填参数long_url！"."<br>");
			}
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		    return  $this->arrayToXml($this->parameters);
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 获取prepay_id
	 */
	function getShortUrl()
	{
		$this->postXml();
		$prepay_id = $this->result["short_url"];
		return $prepay_id;
	}
	
}

/**
 * 响应型接口基类
 */
class Wxpay_server_pub extends Common_util_pub 
{
	public $data;//接收到的数据，类型为关联数组
	var $returnParameters;//返回参数，类型为关联数组
	
	/**
	 * 将微信的请求xml转换成关联数组，以方便数据处理
	 */
	function saveData($xml)
	{
		$this->data = $this->xmlToArray($xml);
	}
	
	function checkSign()
	{
		$tmpData = $this->data;
		unset($tmpData['sign']);
		$sign = $this->getSign($tmpData);//本地签名
		if ($this->data['sign'] == $sign) {
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 获取微信的请求数据
	 */
	function getData()
	{		
		return $this->data;
	}
	
	/**
	 * 设置返回微信的xml数据
	 */
	function setReturnParameter($parameter, $parameterValue)
	{
		$this->returnParameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		return $this->arrayToXml($this->returnParameters);
	}
	
	/**
	 * 将xml数据返回微信
	 */
	function returnXml()
	{
		$returnXml = $this->createXml();
		return $returnXml;
	}
}


/**
 * 通用通知接口
 */
class Notify_pub extends Wxpay_server_pub 
{

}




/**
 * 请求商家获取商品信息接口
 */
class NativeCall_pub extends Wxpay_server_pub
{
	/**
	 * 生成接口参数xml
	 */
	function createXml()
	{
		$wxpay_obj = new WXPayModel();
		if($this->returnParameters["return_code"] == "SUCCESS"){
		   	$this->returnParameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->returnParameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		    $this->returnParameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->returnParameters["sign"] = $this->getSign($this->returnParameters);//签名
		}
		return $this->arrayToXml($this->returnParameters);
	}
	
	/**
	 * 获取product_id
	 */
	function getProductId()
	{
		$product_id = $this->data["product_id"];
		return $product_id;
	}
	
}

/**
 * 静态链接二维码
 */
class NativeLink_pub  extends Common_util_pub
{
	var $parameters;//静态链接参数
	var $url;//静态链接

	function __construct() 
	{
	}
	
	/**
	 * 设置参数
	 */
	function setParameter($parameter, $parameterValue) 
	{
		$this->parameters[$this->trimString($parameter)] = $this->trimString($parameterValue);
	}
	
	/**
	 * 生成Native支付链接二维码
	 */
	function createLink()
	{
		try 
		{		
			if($this->parameters["product_id"] == null) 
			{
				throw new SDKRuntimeException("缺少Native支付二维码链接必填参数product_id！"."<br>");
			}			
			$wxpay_obj = new WXPayModel();
		   	$this->parameters["appid"] = $wxpay_obj->APPID;//公众账号ID
		   	$this->parameters["mch_id"] = $wxpay_obj->MCHID;//商户号
		   	$time_stamp = time();
		   	$this->parameters["time_stamp"] = "$time_stamp";//时间戳
		    $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
		    $this->parameters["sign"] = $this->getSign($this->parameters);//签名    		
			$bizString = $this->formatBizQueryParaMap($this->parameters, false);
		    $this->url = "weixin://wxpay/bizpayurl?".$bizString;
		}catch (SDKRuntimeException $e)
		{
			die($e->errorMessage());
		}
	}
	
	/**
	 * 返回链接
	 */
	function getUrl() 
	{		
		$this->createLink();
		return $this->url;
	}
}

/**
* JSAPI支付——H5网页端调起支付接口
*/
class JsApi_pub extends Common_util_pub
{
	var $code;//code码，用以获取openid
	var $openid;//用户的openid
	var $parameters;//jsapi参数，格式为json
	var $prepay_id;//使用统一支付接口得到的预支付id
	var $curl_timeout;//curl超时时间

	function __construct() 
	{
		//设置curl超时时间
		$this->curl_timeout = WXPayModel::CURL_TIMEOUT;
	}
	
	/**
	 * 	作用：生成可以获得code的url
	 */
	function createOauthUrlForCode($redirectUrl)
	{
		$wxpay_obj = new WXPayModel();
		$urlObj["appid"] = $wxpay_obj->APPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	/**
	 * 	作用：生成可以获得openid的url
	 */
	function createOauthUrlForOpenid()
	{
		$wxpay_obj = new WXPayModel();
		$urlObj["appid"] = $wxpay_obj->APPID;
		$urlObj["secret"] = $wxpay_obj->APPSECRET;
		$urlObj["code"] = $this->code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->formatBizQueryParaMap($urlObj, false);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
	
	
	/**
	 * 	作用：通过curl向微信提交code，以获取openid
	 */
	function getOpenid()
	{
		$url = $this->createOauthUrlForOpenid();
        //初始化curl
       	$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOP_TIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
        $res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		$this->openid = $data['openid'];
		return $this->openid;
	}

	/**
	 * 	作用：设置prepay_id
	 */
	function setPrepayId($prepayId)
	{
		$this->prepay_id = $prepayId;
	}

	/**
	 * 	作用：设置code
	 */
	function setCode($code_)
	{
		$this->code = $code_;
	}

	/**
	 * 	作用：设置jsapi的参数
	 */
	public function getParameters()
	{
		$wxpay_obj = new WXPayModel();
		$jsApiObj["appId"] = $wxpay_obj->APPID;
		$timeStamp = time();
	    $jsApiObj["timeStamp"] = "$timeStamp";
	    $jsApiObj["nonceStr"] = $this->createNoncestr();
		$jsApiObj["package"] = "prepay_id=$this->prepay_id";
	    $jsApiObj["signType"] = "MD5";
	    $jsApiObj["paySign"] = $this->getSign($jsApiObj);
	    $this->parameters = json_encode($jsApiObj);
		
		return $this->parameters;
	}

	/**
	 * 	作用：设置APP的参数
	 */
	public function getAPPParameters()
	{
		$wxpay_obj = new WXPayModel();
		$timestamp = time();

		$arr = array(
			'appid'		=> $wxpay_obj->APPID,
			'partnerid'	=> $wxpay_obj->MCHID,
			'prepayid'	=> $this->prepay_id,
			'package'	=> 'Sign=WXPay',
			'noncestr'	=> $this->createNoncestr(),
			'timestamp'	=> $timestamp,
		);
		$arr['paySign'] = $this->getSign($arr);
		#dump($arr);
		$this->parameters = json_encode($arr);

		return $this->parameters;
	}
}

/**
 * 收货地址接口
 */
class Address extends Common_util_pub
{
	/**
	 * 	作用：生成签名
	 */
	public function getSign($Obj)
	{
		$wxpay_obj = new WXPayModel();
		foreach ($Obj as $k => $v)
		{
			$Parameters[strtolower($k)] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);
		$String = $this->formatBizQueryParaMap($Parameters, false);
		//echo '【string1】'.$String.'</br>';
		//签名步骤三：MD5加密
		$String = sha1($String);
		//echo "【result】 ".$result_."</br>";
		return $String;
	}
	

	/**
	 * 	作用：设置jsapi的参数
	 */
	public function getParameters()
	{
		$wxpay_obj = new WXPayModel();
		$jsApiObj["appId"] = $wxpay_obj->APPID;
		$timeStamp = time();
	    $jsApiObj["timeStamp"] = "$timeStamp";
	    $jsApiObj["accessToken"] = $this->getAccessToken();
	    $jsApiObj["nonceStr"] = $this->createNoncestr();
	    $jsApiObj["url"] = C('ADDR_URL');
	    $jsApiObj["addrSign"] = $this->getSign($jsApiObj);
	    $jsApiObj["signType"] = "sha1";
	    $jsApiObj["scope"] = "jsapi_address";
		unset($jsApiObj['accessToken']);
	    $this->parameters = json_encode($jsApiObj);
		
		return $this->parameters;
	}

	/**
	 * 获取当前用户的access_token
	 */
	function getAccessToken()
	{
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('access_token');

		return $user_info ? $user_info['access_token'] : '';
	}
}

//企业支付接口
class WxMchPay extends Wxpay_client_pub
{
    /**
     * API 参数
     * @var array
     * 'mch_appid'         # 公众号APPID
     * 'mchid'             # 商户号
     * 'device_info'       # 设备号
     * 'nonce_str'         # 随机字符串
     * 'partner_trade_no'  # 商户订单号
     * 'openid'            # 收款用户openid
     * 'check_name'        # 校验用户姓名选项 针对实名认证的用户
     * 're_user_name'      # 收款用户姓名
     * 'amount'            # 付款金额
     * 'desc'              # 企业付款描述信息
     * 'spbill_create_ip'  # Ip地址
     * 'sign'              # 签名
     */
    public $parameters = [];

    public function __construct($is_bank)
    {
    	if($is_bank){
            $this->url = 'https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';
		}else{
            $this->url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
		}
        $this->curl_timeout = WxPayConf_pub::CURL_TIMEOUT;
    }

    /**
     * 生成请求xml数据
     * @return string
     */
    public function createXml()
    {
        #$this->parameters['mch_appid'] = WxPayConf_pub::APPID;
        #$this->parameters['mchid']     = WxPayConf_pub::MCHID;
        #$this->parameters['nonce_str'] = $this->createNoncestr();
        #$this->parameters['sign']      = $this->getSign($this->parameters);
        $wxpay_obj = new WXPayModel();
        $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
        $this->parameters = array_unique($this->parameters);
        $this->parameters["sign"] = $this->getSign($this->parameters);//签名
		if($GLOBALS['wx_transfer_type'] != 'bank'){
            $this->parameters["mch_appid"] = $wxpay_obj->APPID;//公众账号ID
            $this->parameters["mchid"] = $wxpay_obj->MCHID;//商户号
		}
//        dump($this->parameters);die;
        return $this->arrayToXml($this->parameters);
    }


    /**
     *     作用：使用证书，以post方式提交xml到对应的接口url
     */
    function postXmlSSLCurl($xml,$url,$second=30)
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        //这里设置代理，如果有的话
        //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
        //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
        //设置header
        curl_setopt($ch,CURLOPT_HEADER,FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
        //设置证书
        #curl_setopt($ch,CURLOPT_CAINFO, WxPayConf_pub::SSLROOTCA_PATH);
        //使用证书：cert 与 key 分别属于两个.pem文件
        //默认格式为PEM，可以注释
		$wxpay_obj = new WXPayModel();
        curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLCERT, $wxpay_obj->SSLCERT_PATH);
        //默认格式为PEM，可以注释
        curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
        curl_setopt($ch,CURLOPT_SSLKEY, $wxpay_obj->SSLKEY_PATH);

        //post提交方式
        curl_setopt($ch,CURLOPT_POST, true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$xml);
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            echo "curl出错，错误码:$error"."<br>";
            echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
            curl_close($ch);
            return false;
        }
    }

    /**
	 * 	作用：获取结果，使用证书通信
	 */
	function getResult() 
	{		
		$this->postXmlSSL();
		$this->result = $this->xmlToArray($this->response);
		return $this->result;
	}

}
?>

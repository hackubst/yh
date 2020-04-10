<?php
/**
*  支付方式
*  @ Date : 2014/04/08
*  @ Author : 姜伟
*/

//集成微支付代码
vendor('wxpay.WxPayPubHelper');
class WXPayModel extends PaywayModel
{
	//=======【基本信息设置】=====================================
	//微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看
	public $APPID = '';
	#public $APPID = 'wx3b6bfcb6495dff9e';
	//受理商ID，身份标识
	public $MCHID = '';
	#public $MCHID = '10012915';
	//商户支付密钥Key。审核通过后，在微信发送的邮件中查看
	#public $KEY = 'AQI1289Msf1289NSLEP1829sjdkNDSKL';
	public $KEY = '';
	//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
	#public $APPSECRET = 'f23adf9fb219fdabb7e4cc092c41a96a';
	public $APPSECRET = '';
	//=======【证书路径设置】=====================================
	//证书路径,注意应该填写绝对路径
	#public $SSLCERT_PATH = APP_PATH . 'frame/Extend/Vendor/wxpay/cacert/apiclient_cert.pem';
	#public $SSLKEY_PATH = APP_PATH . 'frame/Extend/Vendor/wxpay/cacert/apiclient_key.pem';
	public $SSLCERT_PATH = '';
	public $SSLKEY_PATH = '';

	//=======【异步通知url设置】===================================
	//异步通知url，商户根据实际开发过程设定
	#public $NOTIFY_URL = 'http://' . $_SERVER['HTTP_HOST'] . '/Front/wxpay_response';
	public $NOTIFY_URL = '';

	//=======【curl超时设置】===================================
	//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
	const CURL_TIMEOUT = 30;

	//是否微信APP支付
	public $is_app = false;

	/**
	 * 构造函数，初始化参数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 取支付方式表中的微支付配置，赋值到成员变量中
	 */
	public function __construct()
	{
		$is_app = APP_TYPE ? true :false;
		$is_app = false;
		$this->is_app = $is_app;
		$pay_tag = $is_app ? 'mobile_wxpay' : 'wxpay';
		if($GLOBALS['wx_merchant_id']){
            $merchant_obj = new WxMerchantModel();
            $pay_config = $merchant_obj->getWxMerchantInfo('wx_merchant_id ='.$GLOBALS['wx_merchant_id']);
            $this->APPID = $pay_config['appid'];
            $this->MCHID = $pay_config['mcid'];
            $this->KEY = $pay_config['pay_key'];
            $this->APPSECRET = $pay_config['appsecret'];
        }else{
            //支付接口参数
            $payway_obj = new PaywayModel();
            $payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
            $pay_config = unserialize($payway_info['pay_config']);
            unset($payway_info);
            $this->APPID = $pay_config['appid'];
            $this->MCHID = $pay_config['mch_id'];
            $this->KEY = $pay_config['key'];
            $this->APPSECRET = $pay_config['appsecret'];
        }
//		if ($is_app)
//		{
//			$this->SSLCERT_PATH = APP_PATH . 'frame/Extend/Vendor/wxpay/cacert/app_apiclient_cert.pem';
//			$this->SSLKEY_PATH = APP_PATH . 'frame/Extend/Vendor/wxpay/cacert/app_apiclient_key.pem';
//		}
//		else
//		{
			$this->SSLCERT_PATH = APP_PATH . 'frame/Extend/Vendor/wxpay/cacert/apiclient_cert.pem';
			$this->SSLKEY_PATH = APP_PATH . 'frame/Extend/Vendor/wxpay/cacert/apiclient_key.pem';
//		}
		$this->NOTIFY_URL = 'http://' . $_SERVER['HTTP_HOST'] . '/Front/wxpay_response';
	}

	/**
	 * 获取支付代码
	 * @author 姜伟
	 * @param int $order_id 订单ID
	 * @param int $voucher_amount 充值金额，$order_id = 0 时有效
	 * @return int $qr_pay_mode 是否扫码支付，1是，0否
	 * @todo 获取支付代码
	 */
	public function pay_code($order_id = 0, $voucher_amount = 0.00, $qr_pay_mode = 0, $is_sms=false)
	{
		/**
		 * JS_API支付demo
		 * ====================================================
		 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
		 * 成功调起支付需要三个步骤：
		 * 步骤1：网页授权获取用户openid
		 * 步骤2：使用统一支付接口，获取prepay_id
		 * 步骤3：使用jsapi调起支付
		*/
		vendor("wxpay.WxPayPubHelper");
		//初始化
		$trade_type = $qr_pay_mode ? 'NATIVE' : 'JSAPI';
		$body = '';
		$out_trade_no = '';
		$total_fee = 0;

		//如果是订单付款，取订单信息，赋值到接口入参
		if($order_id)
		{
			//获取订单信息
			$order_obj = new OrderModel($order_id);
			$order_info = $order_obj->getOrderInfo('pay_amount, total_amount, express_fee, user_address_id, voucher_code');

            if ($order_info['voucher_code'] && $voucher_amount) {
                $total_fee = $voucher_amount < 0.01 ? 0.01 : $voucher_amount;
            } else {
                $total_fee = $order_info['pay_amount'];
                $total_fee = $total_fee < 0.01 ? 0.01 : $total_fee;
            }

			$out_trade_no 	= date('Ymdhms') . '_payorder_' . $order_id;
			$body			= 'pay_order';
			$logistics_fee 	= $order_info['express_fee'];
		}
		else
		{
			$total_fee = $voucher_amount;
			$out_trade_no 	= date('Ymdhms') . '_voucher_' . session('user_id');
			$body			= '钛核科技';
		}

		//使用jsapi接口
		$jsApi = new JsApi_pub();

		//=========步骤1：网页授权获取用户openid============
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('openid');
		$openid = $user_info['openid'];

		//=========步骤2：使用统一支付接口，获取prepay_id============
		//使用统一支付接口
		$unifiedOrder = new UnifiedOrder_pub();

		//设置统一支付接口参数
		//设置必填参数
		//appid已填,商户无需重复填写
		//mch_id已填,商户无需重复填写
		//noncestr已填,商户无需重复填写
		//spbill_create_ip已填,商户无需重复填写
		//sign已填,商户无需重复填写
		if ($trade_type != 'NATIVE')
		{
			$unifiedOrder->setParameter("openid","$openid");//商品描述
		}
		$unifiedOrder->setParameter("body", $body);//商品描述
		//自定义订单号，此处仅作举例
		$timeStamp = time();
		$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
		$unifiedOrder->setParameter("total_fee", floatval($total_fee) * 100);//总金额
		$unifiedOrder->setParameter("notify_url",$this->NOTIFY_URL);//通知地址
		$unifiedOrder->setParameter("trade_type", $trade_type);//交易类型
		//非必填参数，商户可根据实际情况选填
		//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
		//$unifiedOrder->setParameter("device_info","XXXX");//设备号
		//$unifiedOrder->setParameter("attach","XXXX");//附加数据
		//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
		//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
		//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
		//$unifiedOrder->setParameter("openid","XXXX");//用户标识
		//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

        $prepay_id = $unifiedOrder->getPrepayId();
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getParameters();
        return $jsApiParameters;
	}


	/**
     * 获取支付代码
     * @author 姜伟
     * @param int $order_id 订单ID
     * @param int $pay_amount 支付金额，$order_id = 0 且add_freight = false 时有效
     * @return int $qr_pay_mode 是否扫码支付，1是，0否
     * @todo 获取支付代码
     */
    public function mobile_pay_code($order_id = 0, $pay_amount = 0.00, $qr_pay_mode = 0, $add_freight = false)
    {
        vendor("wxpay.WxPayPubHelper");
        //初始化
        $trade_type = 'APP';
        $body = 'weixin';
        $out_trade_no = '';
        $total_fee = 0;
		$this->NOTIFY_URL = 'http://' . $_SERVER['HTTP_HOST'] . '/Front/wxpay_app_response';
        if ($order_id)
        {
            //获取订单信息
            $order_obj = new OrderModel($order_id);
            $order_info = $order_obj->getOrderInfo('pay_amount');

            $total_fee = $order_info['pay_amount'];
#log_file($total_fee);
            $total_fee = $total_fee < 0.01 ? 0.01 : $total_fee;

            $out_trade_no   = date('Ymdhms') . '_payorder_' . $order_id;
            $body           = '订单付款';
            #$logistics_fee     = $order_info['express_fee'];
        }
        else
        {
            $total_fee = $pay_amount;
            $out_trade_no   = date('Ymdhms') . '_voucher_' . session('user_id');
            $body           = '在线充值';
        }

        $total_fee = round($total_fee,2);
        if ($total_fee < 0.01) {
            return false;
        }

        //使用jsapi接口
        $jsApi = new JsApi_pub();

        //=========步骤1：网页授权获取用户openid============
        $user_id = intval(session('user_id'));
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('openid');
        $openid = $user_info['openid'];
$openid = $openid ? $openid : 'oudyit91NPdlEGzT2L6iC33_Z8zg';
log_file('openid = ' . $openid, 'mobile_pay_code');

        //=========步骤2：使用统一支付接口，获取prepay_id============
        //使用统一支付接口
        $unifiedOrder = new UnifiedOrder_pub();

        //设置统一支付接口参数
        //设置必填参数
        //appid已填,商户无需重复填写
        //mch_id已填,商户无需重复填写
        //noncestr已填,商户无需重复填写
        //spbill_create_ip已填,商户无需重复填写
        //sign已填,商户无需重复填写
        //$unifiedOrder->setParameter("openid","$openid");//商品描述
        $unifiedOrder->setParameter("body", $body);//商品描述
        $unifiedOrder->setParameter("mch_id", $this->MCHID);//商品描述
        $unifiedOrder->setParameter("notify_url",$this->NOTIFY_URL);//通知地址
        $unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号
        $unifiedOrder->setParameter("spbill_create_ip", get_client_ip());//交易类型
        $unifiedOrder->setParameter("total_fee", floatval($total_fee) * 100);//总金额
        $unifiedOrder->setParameter("trade_type", $trade_type);//交易类型

        $unifiedOrder->setParameter("limit_pay", 'no_credit');//指定不能使用信用卡支付

        //自定义订单号，此处仅作举例
        $timeStamp = time();

        $prepay_id = $unifiedOrder->getPrepayId();
        //=========步骤3：使用jsapi调起支付============
        $jsApi->setPrepayId($prepay_id);

        $jsApiParameters = $jsApi->getAPPParameters();

        $return_arr = json_decode($jsApiParameters, true);
        return $return_arr;
    }

	/**
	 * 支付相应
	 * @author 姜伟
	 * @param void
	 * @return 订单付款成功返回1，充值成功返回2，失败返回false
	 * @todo 验证来源有效性，获取支付类型（充值/订单付款），如果是充值，充值等额预存款，如果是订单付款，设置订单状态为已付款
	 */
	function pay_response()
	{
log_file('in', 'pay_response', true);
		/**
		 * 通用通知接口demo
		 * ====================================================
		 * 支付完成后，微信会把相关支付和用户信息发送到商户设定的通知URL，
		 * 商户接收回调信息后，根据需要设定相应的处理流程。
		 *
		 * 这里举例使用log文件形式记录回调信息。
		*/
		//使用通用通知接口
		$notify = new Notify_pub();

		//存储微信的回调
		$xml = $GLOBALS['HTTP_RAW_POST_DATA'];
		$notify->saveData($xml);

		//验证签名，并回应微信。
		//对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
		//微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
		//尽可能提高通知的成功率，但微信不保证通知最终能成功。
		if($notify->checkSign() == FALSE){
			$notify->setReturnParameter("return_code","FAIL");//返回状态码
			$notify->setReturnParameter("return_msg","签名失败");//返回信息
		}else{
			$notify->setReturnParameter("return_code","SUCCESS");//设置返回码
		}
		$returnXml = $notify->returnXml();
		echo $returnXml;
		$this->log_result($returnXml);

		//==商户根据实际情况设置相应的处理流程，此处仅作举例=======

		//以log文件形式记录回调信息
		#$this->log_result("【接收到的notify通知】:\n".$xml."\n");

		if($notify->checkSign() == TRUE)
		{
			if ($notify->data["return_code"] == "FAIL") {
				//此处应该更新一下订单状态，商户自行增删操作
				$this->log_result("【通信出错】:\n".$xml."\n");
			}
			elseif($notify->data["result_code"] == "FAIL"){
				//此处应该更新一下订单状态，商户自行增删操作
				$this->log_result("【业务出错】:\n".$xml."\n");
			}
			else{
				//此处应该更新一下订单状态，商户自行增删操作
				$this->log_result("【支付成功】:\n".$xml."\n");
			}

			//商户自行增加处理流程,
			//例如：更新订单状态
			//例如：数据库操作
			//例如：推送支付完成信息
		}

		$Common_util_pub_obj = new Common_util_pub();
		$data = $Common_util_pub_obj->xmlToArray($xml);
		$str = '';
		foreach($data as $key => $val)
		{
			$str .= "&$key=$val";
		}
		$this->log_result('返回的数据：' . $str);

		//获取支付类型
		$row_dd	= isset($data['out_trade_no']) ? explode('_', $data['out_trade_no']) : '';
		$pay_type = $row_dd[1];
		$order_id = ($pay_type == 'payorder') ? $row_dd[2] : 0;

		//获取微支付的反馈参数
		$trade_no = (isset($data['transaction_id'])) ? $data['transaction_id'] : '';	//获取订单号
		$trade_status	= (isset($data['return_code'])) ? $data['return_code']:''; 	//获取微支付反馈过来的状态,根据不同的状态来更新数据库 WAIT_BUYER_PAY(表示等待买家付款);WAIT_SELLER_SEND_GOODS(表示买家付款成功,等待卖家发货);WAIT_BUYER_CONFIRM_GOODS(卖家已经发货等待买家确认);TRADE_FINISHED(表示交易已经成功结束)
		#$this->log_result('支付类型：' . $pay_type . ', order_id = ' . $order_id . ', trade_no = ' . $trade_no . ', trade_status = ' . $trade_status);

		if($order_id)
		{
			#$this->log_result('进入订单支付');
			//获取订单信息
			$order_obj = new OrderModel($order_id);
			try
			{
				$order_info = $order_obj->getOrderInfo('pay_amount, total_amount, express_fee, pay_time, order_status, user_id, voucher_code');
				$user_id = $order_info['user_id'];
			}
			catch (Exception $e)
			{
				return false;
			}

			/* 检查支付的金额是否相符*/
			$total_fee = floatval($order_info['pay_amount']) * 100;

            if ($order_info['voucher_code']) {
                $ticket_obj = new TicketModel();
                $ticket_info = $ticket_obj->getTicketInfo($order_info['voucher_code']);
                $card_code = $ticket_obj->where('voucher_code = ' . $order_info['voucher_code'])->getField('card_code');
                if (!$card_code) return false;
                //验证
                $ticket_money = $ticket_obj->validTicketInfo($ticket_info);
                if (!$ticket_money)
                {
                    return false;
                }
                if ($ticket_money * 100 + $data['total_fee'] != $order_info['pay_amount'] *100)
                {
                    return false;
                }

            } else {

                if ($total_fee != $data['total_fee'])
                {
                    return false;
                }
            }

			//检查订单是否已付款，若已付款，退出
			if ($order_info['order_status'])
			{
				return false;
			}

			//买家付款成功 WAIT_SELLER_SEND_GOODS
			//这里放入你自定义代码,比如根据不同的trade_status进行不同操作
			if ($trade_status == 'SUCCESS')
			{
				//调用订单模型的payOrder方法设置订单状态为已付款
				//$order_obj->payOrder($trade_no, 'wxpay');
				//调用账户模型的addAccount充值等额预存款金额
				$account_obj = new AccountModel();
                $account_obj->addAccount($user_id, 1, intval($data['total_fee']) / 100,
                    '微支付充值', 0, $trade_no);

                $remark = '支付订单';
                //如果有券则支付券
                if ($order_info['voucher_code']) {
                    //调用券支付
                    $bill_number = $card_code . $order_id;
                    $state = D('Ticket')->ticketPay($card_code, $order_info['voucher_code'], $ticket_money, $bill_number);
                    //支付失败跳出
                    if (!$state) {
                        return false;
                    }
                    $code = substr($order_info['voucher_code'], -4);
                    $remark = '支付订单（券：**' . $code . '---抵扣金额：' . $ticket_money . '元)';
                }

                //如果是团购则改变类型
                $change_type = $order_info['is_group_buy'] ? AccountModel::GROUP_BUY_COST : AccountModel::ORDER_COST;

				//调用账户模型的addAccount方法支付该订单
                $account_obj->addAccount($user_id, $change_type, $data['total_fee'] * -1 / 100, $remark, $order_id, $trade_no);

                // 同步订单到ERP系统；
                D('Order')->syncOrderInfo($order_id);

				return 1;
			}
			else
			{
				return false;
			}
		}
		elseif ($pay_type == 'voucher')
		{
			#$this->log_result('进入充值支付');
			//检验是否已处理，若已处理，直接返回false
			$account_obj = new AccountModel();
			$is_handled = $account_obj->checkPayCodeExists($trade_no);
			if ($is_handled)
			{
				#$this->log_result('已处理');
				//已处理，直接返回false
				return false;
			}
			#$this->log_result('未处理');

			//获取user_id
			$user_id = $row_dd[2];
log_file('data: ' . json_encode($data));
log_file('row_dd: ' . json_encode($row_dd));

			if ($trade_status == 'SUCCESS')
			{
				#$this->log_result('支付成功状态，准备处理充值操作');
				//调用账户模型的addAccount充值等额预存款金额
				$account_obj->addAccount($user_id, 1, intval($data['total_fee']) / 100, '微支付充值', 0, $trade_no);
log_file($account_obj->getLastSql());
				#$this->log_result('打印SQL: ' . $account_obj->getLastSql());
				return 2;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	//确认签名
	function pay_check_sign()
	{
		//支付
		$data = isset($_GET['out_trade_no']) ? $_GET : $_POST;
		ksort($data);

		//支付接口参数
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag('alipay');
		$pay_config = unserialize($payway_info['pay_config']);
		unset($payway_info);

		$partner 		= $pay_config['alipay_partner'];
		$security_code 	= $pay_config['alipay_key'];


		$sign = '';
		foreach($data as $key => $val)
		{
			if($val != '' and $key != 'sign' and $key != 'sign_type' and $key != "paycode")
			{
				$sign .= "&$key=$val";
			}
		}

		if($data['sign'] != md5(substr($sign,1).$security_code))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * 获取支付包签约类型列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 直接返回3种签约接口的数组
	 */
	public function getSignTypeList()
	{
		return array(
			array(
				'key'	=> '使用即时到帐交易接口',
				'value'	=> '1'
			),
			array(
				'key'	=> '使用即时担保交易接口',
				'value'	=> '2'
			),
			array(
				'key'	=> '使用标准双接口',
				'value'	=> '0'
			),
		);
	}

	function log_result($str)
	{
		$file = fopen('logs/weixin_response_log.' . date('Y-m-d', time()) . '.txt', 'a');
		fwrite($file, date('Y-m-d H:i:s', time()) . "\n" . $str . "\n");
		fclose($file);
	}



    /**
	 * 退款申请
	 * @author 姜伟
	 * @param int $order_id
	 * @param int $item_refund_change_id 退款表中的id
	 * @param int $type 退款类型，1商家全额退款，3订单全额退款(改为分别退1和2，达到退全额的目的)
	 * @param string $amount
	 * @param string $pay_code
	 * @return boolean
	 */
	function refund($order_id, $item_refund_change_id, $type = 1, $amount = 0.00, $pay_code = '')
	{
		//初始化变量
		$out_trade_no = '';
		$refund_fee = 0.00;
		$total_fee = 0.00;
		$out_refund_no = '';

		//获取订单总金额
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo();
		}
		catch (Exception $e)
		{
			//订单不存在，退出返回false
			return false;
		}

		$out_trade_no = $pay_code ? $pay_code : $order_info['pay_code'];
        $out_refund_no = $out_trade_no . $item_refund_change_id;
        //支付金额
		$total_fee = $order_info['pay_amount'];
log_file('total_fee = ' . $total_fee, 'wxpay');

		//根据type获取不同的退款数据
		switch ($type)
		{
			case 1:
				//商家全额退款
				//获取退款金额
				/*$merchant_order_obj = new MerchantOrderModel();
				$merchant_order_info = $merchant_order_obj->getMerchantOrderInfo('merchant_order_id = ' . $merchant_order_id, 'total_amount, is_refund');
				if (!$merchant_order_info)
				{
					//商家订单不存在，返回false
					return false;
				}*/

				#if ($merchant_order_info['is_refund'] == MerchantOrderModel::REFUNDED || $merchant_order_info['is_refund'] == MerchantOrderModel::ADMIN_REFUNDED)
				#{
				#	//已退款，退出返回false
				#	return false;
				#}
				#$refund_fee = $merchant_order_info['total_amount'];
				$refund_fee = $amount;

				break;
            case 2:
                break;
                #case 3:
				#//订单全额退款
				#$refund_fee = $order_info['pay_amount'];
				#break;
			case 4:
				break;
			case 5:
				break;
			default:
				return false;
		}

		$total_fee = round($total_fee * 100);
		$refund_fee = round($refund_fee * 100);
log_file('total_fee = ' . $total_fee, 'wxpay');
log_file('refund_fee = ' . $refund_fee, 'wxpay');
		//使用退款接口
		$refund = new Refund_pub();
		//设置必填参数 //appid已填,商户无需重复填写 //mch_id已填,商户无需重复填写 //noncestr已填,商户无需重复填写 //sign已填,商户无需重复填写
		$refund->setParameter("out_trade_no","$out_trade_no");//商户订单号
		$refund->setParameter("out_refund_no","$out_refund_no");//商户退款单号
		$refund->setParameter("total_fee","$total_fee");//总金额
		$refund->setParameter("refund_fee","$refund_fee");//退款金额
		$refund->setParameter("op_user_id",$this->MCHID);//操作员
		//非必填参数，商户可根据实际情况选填
		//$refund->setParameter("sub_mch_id","XXXX");//子商户号
		//$refund->setParameter("device_info","XXXX");//设备号
		//$refund->setParameter("transaction_id","XXXX");//微信订单号

		//调用结果
		$refundResult = $refund->getResult();

		//商户根据实际情况设置相应的处理流程,此处仅作举例
log_file('refundResult = ' . json_encode($refundResult), 'wxpay');
		if ($refundResult["return_code"] == "FAIL") {
			#echo "通信出错：".$refundResult['return_msg']."<br>";
			return false;
		}
		else{
			#echo "业务结果：".$refundResult['result_code']."<br>";
			#echo "错误代码：".$refundResult['err_code']."<br>";
			#echo "错误代码描述：".$refundResult['err_code_des']."<br>";
			#echo "公众账号ID：".$refundResult['appid']."<br>";
			#echo "商户号：".$refundResult['mch_id']."<br>";
			#echo "子商户号：".$refundResult['sub_mch_id']."<br>";
			#echo "设备号：".$refundResult['device_info']."<br>";
			#echo "签名：".$refundResult['sign']."<br>";
			#echo "微信订单号：".$refundResult['transaction_id']."<br>";
			#echo "商户订单号：".$refundResult['out_trade_no']."<br>";
			#echo "商户退款单号：".$refundResult['out_refund_no']."<br>";
			#echo "微信退款单号：".$refundResult['refund_idrefund_id']."<br>";
			#echo "退款渠道：".$refundResult['refund_channel']."<br>";
			#echo "退款金额：".$refundResult['refund_fee']."<br>";
			#echo "现金券退款金额：".$refundResult['coupon_refund_fee']."<br>";
			return true;
		}
	}


    function wx_refund($out_trade_no, $refund_fee)
	{
		//获取商户订单号
		$merge_pay_obj = new MergePayModel();
		$merge_pay_info = $merge_pay_obj->getMergePayInfo('pay_code = "' . $out_trade_no . '"', 'out_trade_no, pay_amount, freight_discount');
		$total_fee = $merge_pay_info['pay_amount'];
		$out_trade_no = $merge_pay_info['out_trade_no'];
		$out_refund_no = $out_trade_no . $refund_fee;
		$total_fee = round($total_fee * 100);
		$refund_fee = round($refund_fee * 100);
		$refund = new Refund_pub();
		$refund->setParameter("out_trade_no","$out_trade_no");//商户订单号
		$refund->setParameter("out_refund_no","$out_refund_no");//商户退款单号
		$refund->setParameter("total_fee","$total_fee");//总金额
		$refund->setParameter("refund_fee","$refund_fee");//退款金额
		$refund->setParameter("op_user_id",$this->MCHID);//操作员
		//调用结果
		$refundResult = $refund->getResult();

		//商户根据实际情况设置相应的处理流程,此处仅作举例
		log_file('refundResult = ' . json_encode($refundResult), 'wxpay');
		if ($refundResult["return_code"] == "FAIL") {
	#echo "通信出错：".$refundResult['return_msg']."<br>";
			return false;
		}
	}

    /**
     * 企业付款
     */
    public function qiye_pay($user_id, $amount, $remark = '提现打款')
    {
	    vendor("wxpay.WxPayPubHelper");
        $mchPay = new WxMchPay();
        // 用户openid
		$user_id = $user_id ? $user_id : cur_user_id();
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('openid');
		$mchPay->setParameter('openid', $user_info['openid']);
        // 商户订单号
        $mchPay->setParameter('partner_trade_no', 'pay-' . time());
        // 校验用户姓名选项
        $mchPay->setParameter('check_name', 'NO_CHECK');
        // 企业付款金额  单位为分
        $mchPay->setParameter('amount', $amount);
        // 企业付款描述信息
        $mchPay->setParameter('desc', $remark);
        // 调用接口的机器IP地址  自定义
        $mchPay->setParameter('spbill_create_ip', get_client_ip()); # getClientIp()
        // 收款用户姓名
        // $mchPay->setParameter('re_user_name', 'Max wen');
        // 设备信息
        // $mchPay->setParameter('device_info', 'dev_server');

        $data = $mchPay->getResult();
		log_file('pay_result = ' . json_encode($data), 'qiye_pay', true);
        if( !empty($data) )
		{
			/*if ($data['result_code'] == 'FAIL')
			{
				echo $data['err_code_des'];
			}*/
			#echo dump($data);
			return $data;
		}
    }

    //微信转账到银行卡
    public function transferToBank($bank_no, $realname, $bank_code, $amount){
        vendor("wxpay.WxPayPubHelper");
        $mchPay = new WxMchPay(true);
        $GLOBALS['wx_transfer_type'] = 'bank';
        // 商户订单号
        $mchPay->setParameter('partner_trade_no', $bank_code.date('YmdHis').mt_rand(10000, 99999));
        $mchPay->setParameter('mch_id', $this->MCHID);
        $mchPay->setParameter('enc_bank_no', $bank_no);
        $mchPay->setParameter('enc_true_name', $realname);
        $mchPay->setParameter('bank_code', $bank_code);
        $mchPay->setParameter('amount', $amount);
        $mchPay->setParameter('desc', '提现');

        $data = $mchPay->getResult();
        dump($data);die;
//        log_file('pay_result = ' . json_encode($data), 'qiye_pay', true);
//        if( !empty($data) )
//        {
//            return $data;
//        }
    }
}

<?php
class PayApiModel extends ApiModel
{
	/**
	 * 获取支付方式列表(cheqishi.pay.getPaywayList)
	 * @author zlf
	 * @param array $params 参数列表
	 * @return 成功返回$payway_list，失败退出返回错误码
	 * @todo 获取支付方式列表(cheqishi.pay.getPaywayList)
	 */
	function getPaywayList($params)
	{
		//支付方式列表
		$payway_obj = new PaywayModel();
		$payway_list = $payway_obj->getAppPaywayList('payway_id, pay_tag, pay_name');

		return $payway_list;
	}

	/**
	 * 根据订单ID获取支付页面订单信息(keyue.pay.getPayInfoByOrderId)
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$pay_info，失败返回错误码
	 * @todo 根据订单ID获取支付页面订单信息(keyue.pay.getPayInfoByOrderId)
	 */
	function getPayInfoByOrderId($params)
	{
		$order_id = $params['order_id'];
		$pay_tag = $params['pay_tag'];
		$order_obj = new OrderModel($order_id);
		try
		{	
			$order_info = $order_obj->getOrderInfo('pay_amount, order_sn, order_status, payway', ' AND user_id = ' . session('user_id'));

		}
		catch (Exception $e)
		{	
			ApiModel::returnResult(42038, null, '订单不存在');
		}

		if ($order_info['order_status'] != OrderModel::PRE_PAY)
		{
			ApiModel::returnResult(42039, null, '订单已付款');
		}

		#$order_id = $params['order_id'];
		
		if($pay_tag == 'wxpay' || $pay_tag == 'mobile_wxpay'){
			$pay_obj = new WXPayModel();
			$parameter = $pay_obj->mobile_pay_code($order_id);
		}elseif ( $pay_tag == 'alipay' || $pay_tag == 'mobile_alipay' ){
			//支付宝无线支付接口
			$alipay_obj = new AlipayModel();
			$parameter = $alipay_obj->new_mobile_pay_code($order_id);
		}
		
		return $parameter;
	}


	/**
	 * 根据订单ID用金币支付订单(cheqishi.pay.payOrderByWallet)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回'支付成功'，失败返回'金币不足' OR 错误码
	 * @todo 根据订单ID用金币支付订单(cheqishi.pay.payOrderByWallet)
	 */
	function payOrderByWallet($params)
	{
		//订单信息
		$order_id = $params['order_id'];
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('pay_amount, order_sn, order_status, payway', ' AND user_id = ' . session('user_id'));
		}
		catch (Exception $e)
		{
			ApiModel::returnResult(42038, null, '订单不存在');
		}

		if ($order_info['order_status'] != OrderModel::PRE_PAY)
		{
			ApiModel::returnResult(42039, null, '订单已付款');
		}

		$pay_amount = $order_info['pay_amount'];
		//查看用户电子钱包余额，若不足，提示余额不足，并跳转到充值页面
		$user_obj = new UserModel(session('user_id'));
		$left_money = $user_obj->getLeftMoney();
		if ($left_money < $pay_amount)
		{
			ApiModel::returnResult(42041, null, $GLOBALS['config_info']['SYSTEM_MONEY_NAME'] . '不足');
		}
		else
		{
			$order_obj = new OrderModel($order_id);
			//调用账户模型的addAccount方法支付该订单
			$account_obj = new AccountModel();
			$account_obj->addAccount(session('user_id'), 5, $pay_amount * -1, '支付订单' . $order_info['order_sn'], $order_id);
			return '支付成功';
		}
	}

	/**
	 * 根据充值金额和支付方式获取充值支付页面信息(cheqishi.pay.getRechargeInfo)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$recharge_info，失败返回错误码
	 * @todo 根据充值金额和支付方式获取充值支付页面信息(cheqishi.pay.getRechargeInfo)
	 */
	function getRechargeInfo($params)
	{
		$amount = $params['amount'];
		$payway_id = $params['payway_id'];

		//获取支付方式的pay_tag
		$payway_obj = new PaywayModel();
		$payway_id = $payway_obj->getPayIdByName($payway);
		$payway_info = $payway_obj->getPaywayInfoById($payway_id);
		$pay_tag = $payway_info['pay_tag'];
		$pay_tag = 'mobile_wxpay';
		$pay_info = array();
		if ($pay_tag == 'alipay' || $pay_tag == 'mobile_alipay')
		{
			$payment_obj = new AlipayModel();
			$link = $payment_obj->pay_code(0, $amount);
			$qr_pay_mode_link = $payment_obj->pay_code(0, $amount, 1);
			$pay_info = array(
				'link'				=> $link,
				'qr_pay_mode_link'	=> $qr_pay_mode_link,
			);
		}
		elseif ($pay_tag == 'wxpay' || $pay_tag == 'mobile_wxpay')
		{
			$pay_obj = new WXPayModel();
			$pay_info = $pay_obj->mobile_pay_code(0,$amount);

		}
		
		//获取用户余额
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money');

		$pay_info['amount'] = $amount;
		$pay_info['pay_tag'] = $pay_tag;
		$pay_info['left_money'] = $user_info['left_money'];
		$pay_info['system_money_name'] = $GLOBALS['config_info']['SYSTEM_MONEY_NAME'];
		return $pay_info;
	}

	/**
	 * 获取参数列表
	 * @author zlf
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		$params = array(
			'getPayInfoByOrderId'	=> array(
				array(
					'field'		=> 'order_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41027, 
					'empty_code'=> 44027, 
					'type_code'	=> 45027, 
				),
				array(
					'field'		=> 'pay_tag', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41027, 
					'empty_code'=> 44027, 
					'type_code'	=> 45027, 
				),
			),
			'payOrderByWallet'	=> array(
				array(
					'field'		=> 'order_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41027, 
					'empty_code'=> 44027, 
					'type_code'	=> 45027, 
				),
			),
			'getRechargeInfo'	=> array(
				array(
					'field'		=> 'amount', 
					'min_len'	=> 0.01, 
					'max_len'	=> 1000000, 
					'type'		=> 'float', 
					'required'	=> true, 
					'len_code'	=> 40028, 
					'miss_code'	=> 41028, 
					'empty_code'=> 44028, 
					'type_code'	=> 45028, 
				),
				array(
					'field'		=> 'payway_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41029, 
					'empty_code'=> 44029, 
					'type_code'	=> 45029, 
				),
			),
		);

		return $params[$func_name];
	}
}

<?php
/**
*  支付方式
*  @ Date : 2014/04/09
*  @ Author : 姜伟
*/

class ChinabankModel extends PaywayModel 
{
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
		//支付接口参数
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag('chinabank');
		$pay_config = unserialize($payway_info['pay_config']);
		unset($payway_info);

		//如果是订单付款，取订单信息，赋值到接口入参
		if($order_id)
		{
			$data_orderid       = date('Ymdhms') . '_payorder_' . $order_id;
			//获取订单金额
			$order_obj = new OrderModel($order_id);
			$order_info = $order_obj->getOrderInfo('pay_amount, total_amount, express_fee');
			$data_vamount       = floatval($order_info['pay_amount']);
		}
		else
		{
			$data_orderid       = date('Ymdhms') . '_voucher_' . session('user_id');
			$data_vamount       = $voucher_amount;
		}

		$data_vid           = trim($pay_config['chinabank_account']);
        $data_vmoneytype    = 'CNY';
        $data_vpaykey       = trim($pay_config['chinabank_key']);
        $data_vreturnurl    = 'http://' . $_SERVER['HTTP_HOST'] . ((!$is_sms)?'/FrontUser/chinabank_response':'/AcpConfig/chinabank_response/mod_id/0');

        $MD5KEY =$data_vamount.$data_vmoneytype.$data_orderid.$data_vid.$data_vreturnurl.$data_vpaykey;
        $MD5KEY = strtoupper(md5($MD5KEY));

		$parameter = array(
			'action_url'	=> "https://pay3.chinabank.com.cn/PayGate",
			'v_mid'			=> $data_vid,
			'v_oid'			=> $data_orderid,
			'v_amount'		=> $data_vamount,
			'v_moneytype'	=> $data_vmoneytype,
			'v_url'			=> $data_vreturnurl,
			'v_md5info'		=> $MD5KEY
		);

        return $parameter;
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
        $v_oid          = $_POST['v_oid'];
        $v_pmode        = $_POST['v_pmode'];
        $v_pstatus      = $_POST['v_pstatus'];
        $v_pstring      = $_POST['v_pstring'];
        $v_amount       = $_POST['v_amount'];
        $v_moneytype    = $_POST['v_moneytype'];
        $remark1        = $_POST['remark1'];
        $remark2        = $_POST['remark2'];
        $v_md5str       = $_POST['v_md5str'];
		
		//获取支付类型
		$row_dd	= explode('_', $v_oid);
		$pay_type = $row_dd[1];
		$order_id = ($pay_type == 'payorder') ? $row_dd[2] : 0;

		//支付接口参数
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag('chinabank');
		$pay_config = unserialize($payway_info['pay_config']);
		unset($payway_info);

        /**
         * 重新计算md5的值
         */
        $key = $pay_config['chinabank_key'];
        
        $md5string=strtoupper(md5($v_oid.$v_pstatus.$v_amount.$v_moneytype.$key));

        /* 检查秘钥是否正确 */
        if ($v_md5str == $md5string && $v_pstatus == '20')
        {
            if ($order_id)
            {
				//获取订单信息
				$order_obj = new OrderModel($order_id);
				try
				{
					$order_info = $order_obj->getOrderInfo('pay_amount, total_amount, express_fee, pay_time, order_status, order_type');
				}
				catch (Exception $e)
				{
					return false;
				}

				$total_fee = floatval($order_info['pay_amount']);
				/* 检查支付的金额是否相符*/
				if ($total_fee != $v_amount)
				{
					return false;
				}

				//检查订单是否已付款，若已付款，退出
				if ($order_info['order_status'])
				{
					return false;
				}

				//调用订单模型的payOrder方法设置订单状态为已付款
				$order_obj->payOrder($v_oid);
				return 1;
            }
			elseif ($pay_type == 'voucher')
			{
				//检验是否已处理，若已处理，直接返回false
				$account_obj = new AccountModel();
				$is_handled = $account_obj->checkPayCodeExists($v_oid);
				if ($is_handled)
				{
					//已处理，直接返回false
					return false;
				}

				//获取user_id
				$user_id = $row_dd[2];
				//调用账户模型的addAccount充值等额预存款金额
				$account_obj->addAccount($user_id, 1, $v_amount, '网银在线充值', 0, $v_oid);
				return 2;
			}
        }
        else
        {
            return false;
        }
	}
}

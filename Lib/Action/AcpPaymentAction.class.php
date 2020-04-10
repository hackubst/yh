<?php
/**
 * 支付方式管理类
 * 
 *
 */
class AcpPaymentAction extends AcpAction {
	
	
	 /**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo
     */
	public function AcpPaymentAction()
	{
		parent::_initialize();
	}
	
	/**
     * 显示支付列表
     * @author 姜伟
     * @return void
     * @todo 取出payway表的数据显示。修改支付方式的action，用paytag来区别，用set+paytag来做
     */
	public function list_payment()
	{
		$payway_obj = new PaywayModel();
		$payway_list = $payway_obj->getPaywayList();
		#echo "<pre>";
		#print_r($payway_list);
		#echo "</pre>";
		foreach ($payway_list AS $k => $v)
		{
            if ($payway_list[$k]['pay_tag'] == 'wxpay') {
                $payway_list[$k]['set_link'] = U('/AcpPayment/set_' . $v['pay_tag']);
            }
		}
		$this->assign('payway_list', $payway_list);
		$this->assign('head_title', '支付方式设置');
		$this->display();
	}
		
	/**
     * 修改支付方式设置
     * @author 姜伟
     * @return void
     * @todo 获取地址栏中的pay_tag参数，调用
     */
	public function set_payway()
	{
		$pay_tag = 'alipay';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$this->assign('head_title', '支付方式参数设置');

		if ($pay_tag == 'alipay')
		{
			//支付宝签约类型列表
			$payway_obj = new AlipayModel();
			$sign_type_list = $payway_obj->getSignTypeList();
			$this->assign('sign_type_list', $sign_type_list);
		}

		$this->display();
	}
	
	/**
     * 修改支付宝设置
     * @author 陆宇峰
     * @return void
     * @todo 从payway表取paytag=alipay的数据
     */
	public function set_alipay()
	{
		$act = $this->_post('act');
		if ($act == 'set')
		{
			/*接收表单数据begin*/
			//支付方式描述
			$pay_desc = $this->_post('pay_desc');
			if (!$pay_desc)
			{
				$this->error('对不起，请填写支付方式描述');
			}

			//支付宝收款账户
			$alipay_account = $this->_post('alipay_account');
			if (!$alipay_account)
			{
				$this->error('对不起，请填写支付宝收款账户');
			}

			//支付宝接口签约类型
			$alipay_general_method = intval($this->_post('alipay_general_method'));
			if (!$alipay_general_method)
			{
				$this->error('对不起，请选择签约类型');
			}

			//合作者身份
			$alipay_partner = $this->_post('alipay_partner');
			if (!$alipay_partner)
			{
				$this->error('对不起，请填写合作者身份');
			}

			//安全校验码
			$alipay_key = $this->_post('alipay_key');
			if (!$alipay_key)
			{
				$this->error('对不起，请填写安全校验码');
			}

			//是否启用
			$isuse = intval($this->_post('isuse'));
			/*接收表单数据end*/

			/*保存到数据库begin*/
			//组成数组
			$arr = array(
				'pay_tag'		=> 'alipay',
				'pay_desc'		=> $pay_desc,
				'isuse'			=> $isuse,
				'pay_config'	=> serialize(array(
					'alipay_account'		=> $alipay_account,
					'alipay_partner'		=> $alipay_partner,
					'alipay_key'			=> $alipay_key,
					'alipay_general_method'	=> $alipay_general_method
				)),
			);
			$payway_obj = new PaywayModel();
			$payway_obj->editPayway($arr);
			/*保存到数据库end*/

			//成功提示
			$this->success('恭喜您，参数设置成功！');
		}

		$pay_tag = 'alipay';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$payway_info['pay_config'] = unserialize($payway_info['pay_config']);
		$this->assign('payway_info', $payway_info);

		//支付宝签约类型列表
		$payway_obj = new AlipayModel();
		$sign_type_list = $payway_obj->getSignTypeList();
		$this->assign('sign_type_list', $sign_type_list);

		$this->assign('head_title', '支付宝支付接口参数设置');
		$this->display();
	}
		
	/**
     * 修改微信支付设置
     * @author 陆宇峰
     * @return void
     * @todo 从payway表取paytag=wxpay的数据
     */
	public function set_wxpay()
	{
		$act = $this->_post('act');
		if ($act == 'set')
		{
			/*接收表单数据begin*/
			//支付方式描述
			$pay_desc = $this->_post('pay_desc');
			if (!$pay_desc)
			{
				$this->error('对不起，请填写支付方式描述');
			}

			//APPID
			$appid = $this->_post('appid');
			if (!$appid)
			{
				$this->error('对不起，请填写APPID');
			}

			//MCHID
			$mch_id = $this->_post('mch_id');
			if (!$mch_id)
			{
				$this->error('对不起，请填写MCHID');
			}

			//KEY
			$key = $this->_post('key');
			if (!$key)
			{
				$this->error('对不起，请填写商户支付密钥KEY');
			}

			//APPSECRET
			$appsecret = $this->_post('appsecret');
			if (!$appsecret)
			{
				$this->error('对不起，请填写APPSECRET');
			}

			//是否启用
			$isuse = intval($this->_post('isuse'));
			/*接收表单数据end*/

			/*保存到数据库begin*/
			//组成数组
			$arr = array(
				'pay_tag'		=> 'wxpay',
				'pay_desc'		=> $pay_desc,
				'isuse'			=> $isuse,
				'pay_config'	=> serialize(array(
					'appid'		=> $appid,
					'mch_id'	=> $mch_id,
					'key'		=> $key,
					'appsecret'	=> $appsecret
				)),
			);
			$payway_obj = new PaywayModel();
			$payway_obj->editPayway($arr);
			/*保存到数据库end*/

			//成功提示
			$this->success('恭喜您，参数设置成功！');
		}

		$pay_tag = 'wxpay';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$payway_info['pay_config'] = unserialize($payway_info['pay_config']);
		$this->assign('payway_info', $payway_info);

		$this->assign('head_title', '微信支付接口参数设置');
		$this->display();
	}


	/**
     * 修改微信支付设置
     * @author 陆宇峰
     * @return void
     * @todo 从payway表取paytag=wxpay的数据
     */
	public function set_mobile_wxpay()
	{
		$act = $this->_post('act');
		if ($act == 'set')
		{
			/*接收表单数据begin*/
			//支付方式描述
			$pay_desc = $this->_post('pay_desc');
			if (!$pay_desc)
			{
				$this->error('对不起，请填写支付方式描述');
			}

			//APPID
			$appid = $this->_post('appid');
			if (!$appid)
			{
				$this->error('对不起，请填写APPID');
			}

			//MCHID
			$mch_id = $this->_post('mch_id');
			if (!$mch_id)
			{
				$this->error('对不起，请填写MCHID');
			}

			//KEY
			$key = $this->_post('key');
			if (!$key)
			{
				$this->error('对不起，请填写商户支付密钥KEY');
			}

			//APPSECRET
			$appsecret = $this->_post('appsecret');
			if (!$appsecret)
			{
				$this->error('对不起，请填写APPSECRET');
			}

			//是否启用
			$isuse = intval($this->_post('isuse'));
			/*接收表单数据end*/

			/*保存到数据库begin*/
			//组成数组
			$arr = array(
				'pay_tag'		=> 'mobile_wxpay',
				'pay_desc'		=> $pay_desc,
				'isuse'			=> $isuse,
				'pay_config'	=> serialize(array(
					'appid'		=> $appid,
					'mch_id'	=> $mch_id,
					'key'		=> $key,
					'appsecret'	=> $appsecret
				)),
			);
			$payway_obj = new PaywayModel();
			$payway_obj->editPayway($arr);
			/*保存到数据库end*/

			//成功提示
			$this->success('恭喜您，参数设置成功！');
		}

		$pay_tag = 'mobile_wxpay';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$payway_info['pay_config'] = unserialize($payway_info['pay_config']);
		$this->assign('payway_info', $payway_info);

		$this->assign('head_title', '微信APP支付接口参数设置');
		$this->display('set_wxpay');
	}
	
	/**
     * 修改网银在线设置
     * @author 陆宇峰
     * @return void
     * @todo 从payway表取paytag=chinabank的数据
     */
	public function set_chinabank()
	{
		$act = $this->_post('act');
		if ($act == 'set')
		{
			/*接收表单数据begin*/
			//支付方式描述
			$pay_desc = $this->_post('pay_desc');
			if (!$pay_desc)
			{
				$this->error('对不起，请填写支付方式描述');
			}

			//商户编号
			$chinabank_account = $this->_post('chinabank_account');
			if (!$chinabank_account)
			{
				$this->error('对不起，请填写商户编号');
			}

			//MD5密钥
			$chinabank_key = $this->_post('chinabank_key');
			if (!$chinabank_key)
			{
				$this->error('对不起，请填写MD5密钥');
			}

			//是否启用
			$isuse = intval($this->_post('isuse'));
			/*接收表单数据end*/

			/*保存到数据库begin*/
			//组成数组
			$arr = array(
				'pay_tag'		=> 'chinabank',
				'pay_desc'		=> $pay_desc,
				'isuse'			=> $isuse,
				'pay_config'	=> serialize(array(
					'chinabank_account'		=> $chinabank_account,
					'chinabank_key'			=> $chinabank_key
				)),
			);
			$payway_obj = new PaywayModel();
			$payway_obj->editPayway($arr);
			/*保存到数据库end*/

			//成功提示
			$this->success('恭喜您，参数设置成功！');
		}

		$pay_tag = 'chinabank';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$payway_info['pay_config'] = unserialize($payway_info['pay_config']);
		$this->assign('payway_info', $payway_info);

		$this->assign('head_title', '银联支付接口参数设置');
		$this->display();
	}
	
	/**
     * 修改电子钱包设置
     * @author 陆宇峰
     * @return void
     * @todo 从payway表取paytag=wallet的数据
     */
	public function set_wallet()
	{
		$act = $this->_post('act');
		if ($act == 'set')
		{
			/*接收表单数据begin*/
			//支付方式描述
			$pay_desc = $this->_post('pay_desc');
			if (!$pay_desc)
			{
				$this->error('对不起，请填写支付方式描述');
			}

			//是否启用
			$isuse = intval($this->_post('isuse'));
			/*接收表单数据end*/

			/*保存到数据库begin*/
			//组成数组
			$arr = array(
				'pay_tag'		=> 'wallet',
				'pay_desc'		=> $pay_desc,
				'isuse'			=> $isuse,
			);
			$payway_obj = new PaywayModel();
			$payway_obj->editPayway($arr);
			/*保存到数据库end*/

			//成功提示
			$this->success('恭喜您，参数设置成功！');
		}

		$pay_tag = 'wallet';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$this->assign('payway_info', $payway_info);

		$this->assign('head_title', '电子钱包支付参数设置');
		$this->display();
	}
	
	/**
     * 修改线下付款设置
     * @author 陆宇峰
     * @return void
     * @todo 从payway表取paytag=offline的数据
     */
	public function set_offline()
	{
		$act = $this->_post('act');
		if ($act == 'set')
		{
			/*接收表单数据begin*/
			//支付方式描述
			$pay_desc = $this->_post('pay_desc');
			if (!$pay_desc)
			{
				$this->error('对不起，请填写支付方式描述');
			}

			//是否启用
			$isuse = intval($this->_post('isuse'));
			/*接收表单数据end*/

			/*保存到数据库begin*/
			//组成数组
			$arr = array(
				'pay_tag'		=> 'offline',
				'pay_desc'		=> $pay_desc,
				'isuse'			=> $isuse,
			);
			$payway_obj = new PaywayModel();
			$payway_obj->editPayway($arr);
			/*保存到数据库end*/

			//成功提示
			$this->success('恭喜您，参数设置成功！');
		}

		$pay_tag = 'offline';
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
		$this->assign('payway_info', $payway_info);

		$this->assign('head_title', '线下支付参数设置');
		$this->display();
	}
}
?>

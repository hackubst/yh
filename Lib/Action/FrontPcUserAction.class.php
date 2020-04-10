<?php 
class FrontPcUserAction extends UcpPcAction{
	function _initialize() 
	{
		$user_id = intval(session('user_id'));
		$state = $this->_request('state');
		$white_list = array(
			'bind_mobile',
			'send_vcode',
			'login',
			'regist',
			'merchant_regist',
			'send_vcode_new',
		);
		if (!in_array(ACTION_NAME, $white_list))
		{
			//需要登录才能访问的页面
			parent::_initialize();
		}
		else
		{
			//不需要登录就能访问的页面
			parent::excute_parent_initialize();
		}

		$this->item_num_per_coupon_page = 16;
		$this->item_num_per_account_page = 10;
	}

	//个人中心
	public function personal_center()
	{
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money, total_integral');
		$this->assign('user_info', $user_info);
		//待支付订单数量
		$pre_pay_order_num = D('Order')->getOrderNum('isuse = 1 AND user_id = ' . $user_id . ' AND order_status = ' . OrderModel::PRE_PAY);
		$order_num = D('Order')->getOrderNum('isuse = 1 AND user_id = ' . $user_id);
		$this->assign('pre_pay_order_num', $pre_pay_order_num);
		$this->assign('order_num', $order_num);

		$this->assign('head_title', '个人中心');
		$this->display();
	}

	//个人资料
	public function personal_data()
	{
		//获取用户资料
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('realname, sex, birthday, mobile, tel, email, qq, address, remark, province_id, city_id, area_id');

		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();
		$this->assign('province_list', $province_list);

		$this->assign('user_info', $user_info);
		$this->assign('head_title', '个人资料');
		$this->display();
	}


	
	//账户余额
	public function my_account()
	{
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		//余额
		$user_info = $user_obj->getUserInfo('left_money');
		$this->assign('left_money', $user_info['left_money']);

		$where = 'user_id = ' . $user_id;
		$account_obj = new AccountModel();
		//分页处理
        import('ORG.Util.Pagelist');
		$total = $account_obj->getAccountNum($where);   
        $Page = new Pagelist($total,$this->item_num_per_account_page);
		$account_obj->setStart($Page->firstRow);
        $account_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$account_list = $account_obj->getAccountList('remark, addtime, amount_before_pay, amount_out, amount_in', $where, 'addtime DESC');
		$this->assign('account_list', $account_list);

		/*---充值开始 ---*/
		$act = $this->_post('act');
		if ($act == 'pay')
		{
			//充值金额
			$coin_num = $this->_post('coin_num');
			if (!$coin_num)
			{
				$this->error('对不起，请填写充值金额');
			}
			$coin_num = floatval($coin_num);
			if ($coin_num < 0.01)
			{
				$this->error('对不起，充值金额不得小于0.01元');
			}

			//支付方式
			$payway_id = intval($this->_post('payway_id'));
			if (!$payway_id)
			{
				$this->error('对不起，请选择支付方式');
			}

			//获取支付方式的pay_tag
			$payway_obj = new PaywayModel();
			$payway_info = $payway_obj->getPaywayInfoById($payway_id);
			$pay_tag = $payway_info['pay_tag'];

			if ($pay_tag == 'alipay')
			{
				$payment_obj = new AlipayModel();
				$link = $payment_obj->pay_code(0, $coin_num);
				redirect($link);
			}
			elseif ($pay_tag == 'chinabank')
			{
				$bank_link = '/FrontOrder/recharge_bank/amount/' . $coin_num;
				redirect($bank_link);

				/*$payment_obj = new ChinabankModel();
				$parameter = $payment_obj->pay_code(0, $coin_num);
				$this->assign('parameter', $parameter);*/
			}
			elseif ($pay_tag == 'wxpay')
			{
				$wxpay_obj = new WXPayModel();
				//JSAPI
				$jsApiParameters = $wxpay_obj->pay_code(0, $coin_num);
				//NATIVE
				#$nativeApiParameters = $wxpay_obj->pay_code(0, $coin_num, 1);
				#$this->assign('nativeApiParameters', $nativeApiParameters);
				$this->assign('jsApiParameters', $jsApiParameters);
			}

			$this->assign('amount', $coin_num);
			$this->assign('pay_tag', $pay_tag);
		
			#//调用支付宝模型的pay_code获取支付链接
			#$alipay_obj = new AlipayModel();
			#$link = $alipay_obj->pay_code(0, $coin_num);
			#redirect($link);
		}

		$this->assign('act', $act);
		
		/*---充值结束 ---*/

		//支付方式列表
		$payway_obj = new PaywayModel();
		$payway_list = $payway_obj->getPaywayList();
		$this->assign('payway_list', $payway_list);

		$this->assign('head_title', '账户余额');
		$this->display();
	}


	//优惠券
	public function coupon_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$coupon_obj = new CouponModel();
		//分页处理
        import('ORG.Util.Pagelist');
		$total = $coupon_obj->getCouponNum($where);   
        $Page = new Pagelist($total,$this->item_num_per_coupon_page);
		$coupon_obj->setStart($Page->firstRow);
        $coupon_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$coupon_list = $coupon_obj->getCouponList('', $where, 'addtime DESC');
		$coupon_list = $coupon_obj->getListData($coupon_list);
   		
		$this->assign('coupon_list', $coupon_list);
		$this->assign('total', $total);
		$this->assign('head_title', '优惠券');
		$this->display();
	}

	//需求提交
	public function requirment_submit()
	{
		$this->assign('head_title', '需求提交');
		$this->display();
	}

	//需求列表
	public function requirment_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$user_require_obj = new UserRequirementModel();
		//分页处理
        import('ORG.Util.Pagelist');
		$total = $user_require_obj->getUserRequirementNum($where);   
        $Page = new Pagelist($total,$this->item_num_per_coupon_page);
		$user_require_obj->setStart($Page->firstRow);
        $user_require_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$requirement_list = $user_require_obj->getUserRequirementList('', $where, 'addtime DESC');
		$this->assign('requirement_list', $requirement_list);
		$this->assign('head_title', '需求列表');
		$this->display();
	}

	//需求详情
	public function requirment_detail()
	{
		$user_requirement_id = intval($this->_get('id'));
		if (!$user_requirement_id)
		{
			$redirect = U('/FrontUser/requirement_list');
			$this->alert('对不起，需求不存在！', $redirect);
		}
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$require_log_obj = M('UserRequirementLog');	
		$require_list = $require_log_obj->field('')->where($where . ' AND user_requirement_id = ' . $user_requirement_id)->select();
		$this->assign('require_list', $require_list);

		$user_req_info = D('UserRequirement')->getUserRequirementById($user_requirement_id);
		#echo "<pre>";print_r($user_req_info);die;
		$this->assign('user_req_info', $user_req_info);
		
		$this->assign('head_title', '需求详情');
		$this->display();
	}

	//登录
	public function login()
	{
		$act = $this->_post('action');
		if ($act == 'login')
		{
			//$mobile = $this->_post('mobile');
			$password = $this->_post('password');
			$username = $this->_post('username');
			$user_obj = new UserModel();
			$user_info = $user_obj->getUserInfo('user_id,is_enable', 'role_type = 3 AND password = "' . Md5($password) . '" AND username = "' . $username . '"');

			if(!$user_info){
				$this->error('抱歉，账号或密码不正确');
			}
			if($user_info['is_enable'] == 2){
				$this->error('抱歉，您的账号已被禁用。');
			}
			
			$user1_obj = new UserModel($user_info['user_id']);
			$user1_info = $user1_obj->getUserInfo('user_id, password', 'user_id = ' . $user_info['user_id']);

			if($user1_info['password'] == MD5($password) ){
				session('user_id', $user1_info['user_id']);	
				$redirect = $this->_request('redirect');
				$url = $redirect ? url_jiemi($redirect) : U('/');
				redirect($url);
				#$this->alert('登录成功。', $url);
			}else if($user1_info['password'] != MD5($password)){
				$this->error('密码错误。');

			}else{
				$this->alert('用户登录失败。');

			}
		}
		//获取配置数据
		$config = $this->system_config;
		$this->assign('config', $config);
		$this->assign('head_title', '登录');
		$this->display();
	}

	//退出登录
	function logout()
	{
		session('user_id', null);
		redirect('/FrontUser/login');
		#$this->alert('退出成功', U('/FrontUser/login'));
	}

	//充值记录页
	public function recharge_list()
	{
		//获取充值列表
		$user_id = intval(session('user_id'));
		$where = 'change_type <= 2 AND user_id = ' . $user_id;
		$account_obj = new AccountModel();
		//总数
		$total = $account_obj->getAccountNum($where);
		$account_obj->setStart(0);
        $account_obj->setLimit($this->item_num_per_recharge_log_page);
		$account_list = $account_obj->getAccountList('remark, addtime, amount_before_pay, amount_after_pay, amount_in', $where, 'addtime DESC');
		#echo $account_obj->getLastSql();
		#echo "<pre>";
		#print_r($account_list);
		#die;
		$this->assign('account_list', $account_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_recharge_log_page);

		$this->assign('head_title', '充值记录');
		$this->display();
	}

	//微信支付测试页
	public function wxpay()
	{
		$wxpay_obj = new WXPayModel();
		$pay_amount = 1;
		$jsApiParameters = $wxpay_obj->pay_code(0, $pay_amount);
		$this->assign('jsApiParameters', $jsApiParameters);
		#print_r(json_decode($jsApiParameters, true));
		#echo $jsApiParameters;
		#die;

		$this->assign('head_title', '测试微信支付');
		$this->display();
	}

	//保存用户信息
	function save_user_info()
	{
		$realname = $this->_post('realname');
		$sex = $this->_post('sex');
		$birthday = $this->_post('birthday');
		$mobile = $this->_post('mobile');
		$tel = $this->_post('tel');
		$email = $this->_post('email');
		$qq = $this->_post('qq');
		$province_id = $this->_post('province_id');
		$city_id = $this->_post('city_id');
		$area_id = $this->_post('area_id');
		$address = $this->_post('address');
		$remark = $this->_post('remark');
		$user_id = intval(session('user_id'));

		if ($realname && $mobile && $email && $qq && $address && $user_id)
		{
			$arr = array(
				'realname'	=> $realname,
				'sex'	=> $sex,
				'birthday'	=> $birthday,
				'mobile'	=> $mobile,
				'tel'	=> $tel,
				'email'	=> $email,
				'qq'	=> $qq,
				'province_id'	=> $province_id,
				'city_id'	=> $city_id,
				'area_id'	=> $area_id,
				'address'	=> $address,
				'remark'	=> $remark
			);
			$user_obj = new UserModel($user_id);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//保存需求
	function save_requirement()
	{
		$require_time = $this->_post('require_time');
		$requirement = $this->_post('requirement');
		$attachment = $this->_post('attachment');
		$user_id = intval(session('user_id'));

		if ($requirement && $user_id)
		{
			$arr = array(
				'require_time'	=> $require_time,
				'requirement'	=> $requirement,
				'attachment'	=> $attachment,
				'user_id'	=> $user_id,
				'addtime'	=> time()
			);
			$user_require_obj = new UserRequirementModel();
			$success = $user_require_obj->addRequirement($arr);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 支付宝付款成功后的回调页面
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用支付宝支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function alipay_response()
	{
		$alipay_obj = new AlipayModel();
		$return_state = $alipay_obj->pay_response();
		if ($return_state == 1)
		{
			$this->alert('恭喜您，订单付款成功', U('/FrontOrder/pre_deliver_order'));
		}
		elseif ($return_state == 2)
		{
			$this->alert('恭喜您，充值成功', U('/FrontUser/my_account'));
		}
		else
		{
			$this->alert('对不起，非法访问', U('/'));
		}
	}

	/**
	 * 微信付款成功后的回调页面
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用微信支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function wxpay_response()
	{
		$wxpay_obj = new WXPayModel();
		$return_state = $wxpay_obj->pay_response();
		if ($return_state == 1)
		{
			$this->alert('恭喜您，订单付款成功', U('/FrontOrder/pre_deliver_order'));
		}
		elseif ($return_state == 2)
		{
			$this->alert('恭喜您，充值成功', U('/FrontUser/my_account'));
		}
		else
		{
			$this->error('对不起，非法访问', U('/'));
		}
	}

	/**
	 * 网银在线付款成功后的回调页面
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用网银在线支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function chinabank_response()
	{
		$chinabank_obj = new ChinabankModel();
		$return_state = $chinabank_obj->pay_response();
		if ($return_state == 1)
		{
			$this->alert('恭喜您，订单付款成功', U('/UcpOrder/get_pre_stockup_order_list'));
		}
		elseif ($return_state == 2)
		{
			$this->alert('恭喜您，充值成功', U('/Ucp'));
		}
		else
		{
			$this->alert('对不起，非法访问', U('/'));
		}
	}

	//异步获取财务列表
	public function get_account_list()
	{
		$user_id = intval(session('user_id'));
		$firstRow = I('post.firstRow');
		$opt = I('post.opt');
		$where = $opt == 'recharge' ? 'change_type <= 2' : '1';
		$where .= ' AND user_id = ' . $user_id;
		$account_obj = new AccountModel();
		//订单总数
		$total = $account_obj->getAccountNum($where);

		if ($firstRow < ($total - 1) && $user_id)
		{
			$account_obj->setStart($firstRow);
			$account_obj->setLimit($this->item_num_per_recharge_log_page);
			$account_list = $account_obj->getAccountList('remark, addtime, amount_before_pay, amount_after_pay, amount_in', $where, 'addtime DESC');
			foreach ($account_list AS $k => $v)
			{
				$account_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			}
			echo json_encode($account_list);
			exit;
		}

		exit('failure');
	}

	//发送验证码
	function send_vcode()
	{
		$mobile = $this->_post('mobile');
		$type = $this->_post('type');
		$user_id = session('user_id');;

		if (is_numeric($mobile) && is_numeric($type) && strlen($mobile) == 11 && $user_id)
		{
			//获取验证码
			$verify_code_obj = new VerifyCodeModel();
			$verify_code = $verify_code_obj->generateVerifyCode();
			if ($verify_code)
			{
				$sms_obj = new SMSModel();
				$send_state = $sms_obj->sendVerifyCode($mobile, $verify_code);
				#echo $verify_code_obj->getLastSql();
				#$send_state = 1;
				echo $send_state ? 'success' : 'failure';
				exit;
			}
		}
		exit('failure');
	}

		//验证手机号
	public function bind_mobile()
	{
		header("Content-Type:text/html; charset=utf-8");
		//获取手机号
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('mobile, mobile_registered');
		$this->assign('mobile', $user_info['mobile']);

		$act = $this->_post('action');
		if ($act == 'bind')
		{
			$mobile = $this->_post('mobile');
			$verify_code = $this->_post('register-valNum');

			//验证手机号和验证码合法性
			$verify_code_obj = new VerifyCodeModel();
			if (!$verify_code_obj->checkVerifyCodeValid($verify_code))
			{
				$this->alert('对不起，验证码无效');
			}

			if ($mobile == $user_info['mobile'])
			{
				//解除绑定
				$arr = array(
					'mobile'	=> ''
				);
				$user_obj->setUserInfo($arr);
				$success = $user_obj->saveUserInfo();

				if ($success)
				{
					$this->alert('恭喜您，手机号解绑成功，请绑定新的手机号');
				}
				else
				{
					$this->alert('抱歉，系统错误，解绑失败');
				}
			}
			else
			{
				//查看当前手机号是否已绑定
				#$num = $user_obj->getUserNum('mobile = "' . $mobile . '"');
				$user_obj2 = new UserModel();
				$user_info = $user_obj2->getUserInfo('user_id, mobile, mobile_registered', 'mobile = "' . $mobile . '"');
				if ($user_info && $user_info['mobile_registered'])
				{
					#$this->alert('对不起，手机号 ' . $mobile . ' 已绑定，请尝试用其它手机号绑定');
					//手机号已在APP注册，将微信注册信息覆盖到已注册的手机账户上
					$success = $user_obj2->bindWeixinMobile($user_id, $user_info['user_id']);
					if ($success)
					{
						session('user_id', $user_info['user_id']);
						$url = U('/FrontUser/personal_center');
						$this->alert('恭喜您，绑定成功，已同步您的APP信息', $url);
					}
					else
					{
						$this->alert('抱歉，系统错误，绑定失败');
					}
				}

				//绑定手机号
				$arr = array(
					'mobile'	=> $mobile
				);
				$user_obj->setUserInfo($arr);
				$success = $user_obj->saveUserInfo();

				if ($success)
				{
					$url = U('/FrontUser/personal_center');
					$this->alert('恭喜您，绑定成功', $url);
				}
				else
				{
					$this->alert('抱歉，系统错误，绑定失败');
				}
			}
		}

		$this->assign('head_title', '绑定手机号');
		$this->display();
	}

}

<?php
class FrontUserAction extends UcpAction{
	function _initialize()
	{
		$user_id = intval(session('user_id'));
		$state = $this->_request('state');
		if (ACTION_NAME != 'bind_mobile' && ACTION_NAME != 'send_vcode')
		{
			parent::_initialize();
		}

		/*if ((ACTION_NAME == 'bind_mobile' || ACTION_NAME == 'bind_mobile') && $state != 1 && $user_id)
		{
			header("Content-Type:text/html; charset=utf-8");
			die('请通过微信访问');
		}*/
		#parent::_initialize();
		#$this->item_num_per_account_page = $GLOBALS['config_info']['item_num_per_account_page'];
		$this->item_num_per_account_page = C('ITEM_NUM_PER_ACCOUNT_PAGE');
	}

	//个人中心
	public function personal_center()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('nickname, headimgurl, left_money, consumed_money, user_rank_id, is_extend_user');
			$where = 'user_id = ' . $user_id;
			//获取等级名称
			$user_rank_obj = new UserRankModel();
			$user_rank_info = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_info['user_rank_id'], 'rank_name');
			$user_info['rank_name'] = $user_rank_info['rank_name'];
			unset($user_rank_info['user_rank_id']);
			//获取用户积分数
			$user_integral_obj = new IntegralModel();
			$user_integral_list = $user_integral_obj->getIntegralList('end_integral', $where, 'addtime DESC');
			$this->assign('user_integral_list', $user_integral_list);
			$this->assign('user_info', $user_info);
		}
		
		//分销是否开启
		$is_fenxiao_open = $GLOBALS['config_info']['IS_FENXIAO_OPEN'];
		$this->assign('is_fenxiao_open', $is_fenxiao_open);
		//底部导航赋值
		$this->assign('nav', 'per_center');
		
		$this->assign('head_title', '个人中心');
		$this->display();
	}

	//个人中心V2
	public function personal_center_v2()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('nickname, headimgurl, left_money, consumed_money, user_rank_id, is_extend_user');
			$where = 'user_id = ' . $user_id;
			//获取等级名称
			$user_rank_obj = new UserRankModel();
			$user_rank_info = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_info['user_rank_id'], 'rank_name');
			$user_info['rank_name'] = $user_rank_info['rank_name'];
			unset($user_rank_info['user_rank_id']);
			//获取用户积分数
			$user_integral_obj = new IntegralModel();
			$user_integral_list = $user_integral_obj->getIntegralList('end_integral', $where, 'addtime DESC');
			$this->assign('user_integral_list', $user_integral_list);
			$this->assign('user_info', $user_info);
		}

		//分销是否开启
		$is_fenxiao_open = $GLOBALS['config_info']['IS_FENXIAO_OPEN'];
		$this->assign('is_fenxiao_open', $is_fenxiao_open);

		//底部导航赋值
		$this->assign('nav', 'per_center');
		$this->assign('head_title', '个人中心');
		$this->display();
	}

	//个人资料
	public function personal_data()
	{
		//获取用户资料
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('nickname, realname, mobile, email, province_id, city_id, area_id, address, street, baby_birthday');

	        if (!$user_info['mobile']) $this->redirect('bind_mobile');

			
			$this->assign('user_info', $user_info);
		}
		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();

	    //街道列表
	    $street_list = D('Street')->getStreetList('', 'isuse = 1');
	    $this->assign('street_list', $street_list);

		$this->assign('province_list', $province_list);
		$this->assign('head_title', '个人资料');
		$this->display();
	}

	//钱包
	public function wallet()
	{
		//获取用户资料
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('left_money');
	        $user_info['member_card_id'] = $member_card_id = $user_obj->getMemberCardID($user_id);

			$this->assign('user_info', $user_info);
		}
		
		$this->assign('head_title', '我的钱包');
		//底部导航赋值
		$this->assign('nav', 'wallet');
		$this->display();
	}

	//用户评价页
	public function assessment()
	{
        //die("功能在开发中");
		//订单ID
		$order_id = $this->_get('order_id');
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, pay_amount, express_fee', ' AND order_status = ' . OrderModel::CONFIRMED);
		}
		catch (Exception $e)
		{
			$this->alert('对不起，订单不存在或订单未确认！', U('/FrontMall/mall_home'));
		}

		$act = $this->_request('act');
		if ($act == 'assess')
		{
			//评价信息
			$assess_info = array(
				'complain_user_ids'	=> $this->_request('complain_user_ids'),
				'remark_str'		=> $this->_request('remark_str'),
				'role_type_str'		=> $this->_request('role_type_str'),
				'score'				=> $this->_request('score'),
				'user_id'			=> intval(session('user_id')),
			);
			$success = $order_obj->commentOrder($assess_info);
			if ($success)
			{
				$this->alert('恭喜您，评价成功', '/FrontOrder/pre_confirm_order');
			}
			else
			{
				$this->alert('抱歉，评价失败', 'FrontUser/assessment/order_id/' . $order_id);
			}
		}

		//获取投诉对象列表
		//$complain_target_list = $order_obj->getComplainTargetList($order_id);
		//$this->assign('complain_target_list', $complain_target_list);
		$this->assign('order_id', $order_id);
		$this->assign('head_title', '用户评价');
		$this->display();
	}

	//申请退款页，接受订单ID，获取订单相关的商家和镖师，供用户选择投诉对象用。
	public function apply_refund()
	{
		//订单ID
		$order_id = $this->_get('order_id');
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, order_status, pay_amount, express_fee');
		}
		catch (Exception $e)
		{
			$this->alert('对不起，订单不存在', U('/FrontMall/mall_home'));
		}

		if ($order_info['order_status'] == OrderModel::REFUNDING)
		{
			$this->alert('对不起，该订单正在申请退款中', U('/FrontOrder/order_detail/order_id/' . $order_id));
		}

		$img_url = $this->_request('img_url');
		if (isset($_POST['img_url']))
		{
			//退款信息
			$apply_info = array(
				'proof'			=> $img_url,
				'refund_money'	=> $order_info['pay_amount'],
				'reason'		=> $this->_request('reason'),
				'refund_type'	=> $this->_request('refund_type'),
			);
			$success = $order_obj->refundApply($apply_info);
			if ($success)
			{
				$this->alert('恭喜您，申请退款成功', '/FrontOrder/refunding_order');
			}
			else
			{
				$this->alert('抱歉，申请退款失败');
			}
		}

		//退款原因列表
		#$order = new OrderModel();
        $arr_refund_reason = $order_obj->getRefundReasonList();
		$this->assign('arr_refund_reason', $arr_refund_reason);

		$this->assign('complain_target_list', $complain_target_list);
		$this->assign('order_id', $order_id);
		$this->assign('head_title', '申请退款');
		$this->display();
	}

	//设置
	public function setup()
	{
		//获取用户手机信息
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('mobile');
			$this->assign('user_info', $user_info);
			//获取客服电话
			$this->assign('customer_tel',$GLOBALS['config_info']['CUSTOMER_SERVICE_TELEPHONE']);
			$this->assign('qr_code_kf_path',$GLOBALS['config_info']['QR_CODE_KF']);
		}
		
		//底部导航赋值
		$this->assign('nav', 'per_center');
		$this->assign('head_title', '设置');
		$this->display();
	}

	//意见反馈
	public function suggest()
	{
		$this->assign('head_title', '意见反馈');
		$this->display();
	}

	//验证手机号
	public function bind_mobile()
	{	
		header("Content-Type:text/html; charset=utf-8");
		//获取手机号
		$user_id = intval(session('user_id'));
		$this->assign('user_id', $user_id);
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('mobile, mobile_registered', 'role_type = 3 AND user_id = ' . $user_id);
			$this->assign('mobile', $user_info['mobile']);
		}
		

		$act = $this->_post('action');
		if ($act == 'bind')
		{
			$mobile = $this->_post('mobile');
			$verify_code = $this->_post('register-valNum');

			//验证手机号和验证码合法性
			$verify_code_obj = new VerifyCodeModel();
			if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile))
			{
				$this->alert('对不起，验证码无效');
			}

			//更换手机号
			if($user_info['mobile']){
				$user_obj2 = new UserModel();
				$user_info = $user_obj2->getUserInfo('user_id, mobile', 'mobile = "' . $mobile . '"');
				if($user_info){
					$this->alert('抱歉，该手机号已被绑定');
				}else{
					$arr = array(
					'mobile'	=> $mobile
					);
					$user_obj->setUserInfo($arr);
					$success = $user_obj->saveUserInfo();
					if ($success) {
						$this->alert('恭喜您，更换手机号成功');
					} else {
						$this->alert('抱歉，系统错误，更换手机号失败');
					}
				}
			}

			if ($mobile == $user_info['mobile']) {
				//解除绑定
				/*$arr = array(
					'mobile'	=> ''
				);
				$user_obj->setUserInfo($arr);
				$success = $user_obj->saveUserInfo();

				if ($success) {
					$this->alert('恭喜您，手机号解绑成功，请绑定新的手机号');
				} else {
					$this->alert('抱歉，系统错误，解绑失败');
				}*/

			} else {
				//查看当前手机号是否已绑定
				#$num = $user_obj->getUserNum('mobile = "' . $mobile . '"');
				$user_obj2 = new UserModel();
				$user_info = $user_obj2->getUserInfo('user_id, mobile', 'mobile = "' . $mobile . '"');
				if ($user_info) {
					//手机号已在APP注册，将微信注册信息覆盖到已注册的手机账户上
					$success = $user_obj2->bindWeixinMobile($user_id, $user_info['user_id']);
					if ($success) {

                        //D('MemberCard')->createNewMemberForERPSystemByUserID($user_info['user_id']);

						session('user_id', $user_info['user_id']);
						$url = U('/FrontUser/personal_data');
						$this->alert('恭喜您，绑定成功，已同步您的资料', $url);
					} else {
						$this->alert('抱歉，系统错误，绑定失败');
					}
				} else {
                    //绑定手机号
                    $arr = array(
                        'mobile'	=> $mobile
                    );
                    $user_obj->setUserInfo($arr);
                    $success = $user_obj->saveUserInfo();
					log_file('sql = ' . $user_obj->getLastSql(), 'bind_mobile', true);
                    if ($success) {
                        //D('MemberCard')->createNewMemberForERPSystemByUserID($user_id);
                        $url = U('/FrontUser/personal_data');
                        $this->alert('恭喜您，绑定成功', $url);
                    } else {
                        $this->alert('抱歉，系统错误，绑定失败');
                    }
                }

			}
		}
		$this->assign('head_title', '绑定手机号');
		$this->display();
	}

	//财务往来明细页 by cc
	public function account_list()
	{
		$user_id = intval(session('user_id'));
		$change_type = intval($this->_request('change_type'));
		$this->assign('change_type', $change_type);
		$where = 'user_id = ' . $user_id;
		$where .= $change_type ? ' AND change_type = ' . $change_type : '';
		$account_obj = new AccountModel();
		//总数
		$total = $account_obj->getAccountNum($where);
		$account_obj->setStart(0);
        $account_obj->setLimit($this->item_num_per_account_page);
		$account_list = $account_obj->getAccountList('user_id,remark, addtime, amount_before_pay, amount_after_pay, amount_in, amount_out, order_id, change_type, operater', $where, 'addtime DESC');
		$account_list = $account_obj->getListData($account_list);
		#echo "<pre>";
		#print_r($user_id);
		#die;
		$this->assign('account_list', $account_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_account_page);

		$this->assign('head_title', '财务明细');
		$this->display();
	}

	//充值记录页 by cc
	public function recharge_list()
	{
		//获取充值列表
		$user_id = intval(session('user_id'));
		$where = 'change_type = 1 AND user_id = ' . $user_id;
		$account_obj = new AccountModel();
		//总数
		$total = $account_obj->getAccountNum($where);
		$account_obj->setStart(0);
        $account_obj->setLimit($this->item_num_per_account_page);
		$account_list = $account_obj->getAccountList('remark, addtime, amount_before_pay, amount_after_pay, amount_in, change_type', $where, 'addtime DESC');
		$account_list = $account_obj->getListData($account_list);
		$this->assign('account_list', $account_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_account_page);

		$this->assign('head_title', '充值记录');
		$this->display();
	}

	//积分明细 by cc
	public function integral_list()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$where = 'user_id = ' . $user_id;
			$integral_obj = new IntegralModel();
			//总数
			$total = $integral_obj->getIntegralNum($where);
			$integral_obj->setStart(0);
	        $integral_obj->setLimit($this->item_num_per_account_page);
			$integral_list = $integral_obj->getIntegralList('remark, addtime, start_integral, end_integral, integral, id, change_type, operater', $where, 'addtime DESC');
			$integral_list = $integral_obj->getListData($integral_list);

			$this->assign('integral_list', $integral_list);
			$this->assign('total', $total);
			$this->assign('firstRow', $this->item_num_per_account_page);
		}
		

		$this->assign('head_title', '积分明细');
		$this->display();
	}


	//镖金明细
	public function freight_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$freight_obj = new FreightModel();
		//总数
		$total = $freight_obj->getFreightNum($where);
		$freight_obj->setStart(0);
        $freight_obj->setLimit($this->item_num_per_account_page);
		$freight_list = $freight_obj->getFreightList('remark, addtime, start_freight, end_freight, freight, change_type', $where, 'addtime DESC');
		$freight_list = $freight_obj->getListData($freight_list);
		$this->assign('freight_list', $freight_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_account_page);
		$this->assign('head_title', '我的镖金');
		$this->display();
	}
	//异步获取标金列表
	public function get_freight_list()
	{
		$user_id = intval(session('user_id'));
		$firstRow = I('post.firstRow');
		$where = 'user_id = ' . $user_id;
		$freight_obj = new FreightModel();
		//订单总数
		$total = $freight_obj->getFreightNum($where);

		if ($firstRow < ($total - 1) && $user_id)
		{
			$freight_obj->setStart($firstRow);
			$freight_obj->setLimit($this->item_num_per_account_page);
			$freight_list = $freight_obj->getFreightList('remark, addtime, start_freight, end_freight, freight, change_type', $where, 'addtime DESC');
			$freight_list = $freight_obj->getListData($freight_list);

			foreach ($freight_list AS $k => $v)
			{
				$freight_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			}

			echo json_encode($freight_list);
			exit;
		}

		exit('failure');
	}

	//镖师详情
	public function foot_man_detail()
	{
		$footman_id = intval($this->_request('foot_man_id'));

		//获取镖师的用户信息
		$user_obj = new UserModel($footman_id);
		$user_info = $user_obj->getUserInfo('realname, mobile');

		$footman_obj = new FootManModel();
		$footman_info = $footman_obj->getFootManInfo('foot_man_id =' . $footman_id, 'head_photo, meters, score_avg, total_order_num, on_time_rate');
		$this->assign('footman_info', $footman_info);
		$this->assign('user_info', $user_info);
		$this->assign('head_title', $user_info['realname'] . '镖师');
		$this->display();
	}

	//消息中心 by zlf
	public function message_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'reply_user_id = ' . $user_id;
		$message_obj = new MessageModel();
		//得到未读消息数量
		$not_read_num = $message_obj->getMessageNum($where . ' AND is_read = 0');
		//总数
		$total = $message_obj->getMessageNum($where);

		//得到消息列表
		$message_obj->setStart(0);
        $message_obj->setLimit($this->item_num_per_account_page);
		$message_list = $message_obj->getMessageList('message_id, addtime, message_type, message_contents, is_read', $where, 'addtime DESC');
		$message_list = $message_obj->getListData($message_list);

		$this->assign('not_read_num',$not_read_num);
		$this->assign('message_list',$message_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_account_page);
		$this->assign('head_title', '消息中心');
		$this->display();
	}

	//异步获取消息列表
	public function get_message_list()
	{

		$firstRow = I('post.firstRow');
		$user_id = intval(session('user_id'));
		$where = 'reply_user_id = ' . $user_id;
		$message_obj = new MessageModel();

		//总数
		$total = $message_obj->getMessageNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$message_obj->setStart($firstRow);
	        $message_obj->setLimit($this->item_num_per_account_page);
			$message_list = $message_obj->getMessageList('message_id, addtime, message_type, message_contents, is_read', $where, 'addtime DESC');
			$message_list = $message_obj->getListData($message_list);

			echo json_encode($message_list);
			exit;
		}

		exit('failure');
	}

	//消息详情 by cc
	public function message_detail()
	{
		$id = intval($this->_get('id'));
		$message_obj = new MessageModel();
		$where = 'message_id = ' . $id;

		$message_info = $message_obj->getMessageInfo($where, 'message_id, addtime, message_type, message_contents, is_read');
		#消息类型，1发送，2回复，3管理员群发
        if ($message_info['message_type'] == 1) {
            $type_text = '发送';
        }elseif ($message_info['message_type'] == 2) {
            $type_text = '回复';
        }elseif ($message_info['message_type'] == 3) {
            $type_text = '管理员群发';
        }
		$this->assign('message_info', $message_info);
		$this->assign('type_text', $type_text);
		$this->assign('head_title', '消息详情');
		$this->display();
	}

	//充值页
	public function recharge()
	{
session('recharge', 1);
redirect('/FrontOrder/pay_order/');
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
				//改成直接跳转支付，故去除扫码支付
				#$qr_pay_mode_link = $payment_obj->pay_code(0, $coin_num, 1);
				#$this->assign('qr_pay_mode_link', $qr_pay_mode_link);
				#$this->assign('link', $link);
				redirect($link);
			}
			elseif ($pay_tag == 'chinabank')
			{
				$payment_obj = new ChinabankModel();
				$parameter = $payment_obj->pay_code(0, $coin_num);
				$this->assign('parameter', $parameter);
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
		//获取用户余额
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money');
		$this->assign('user_info', $user_info);

		//支付方式列表
		$payway_obj = new PaywayModel();
		$payway_list = $payway_obj->getPaywayList();
		$this->assign('payway_list', $payway_list);

		//系统内部货币名称
		$this->assign('SYSTEM_MONEY_NAME', $GLOBALS['config_info']['SYSTEM_MONEY_NAME']);
		//1RMB充值的系统内部货币数量
		$this->assign('COINS_PER_RMB', $GLOBALS['config_info']['COINS_PER_RMB']);
		$this->assign('head_title', '充值');
		//底部导航赋值
		$this->assign('nav', 'wallet');
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
		$email = $this->_post('email');
		$province_id = $this->_post('province_id');
		$city_id = $this->_post('city_id');
		$area_id = $this->_post('area_id');
		$street_id = intval($this->_post('street'));
		$baby_birthday = $this->_post('baby_birthday');
        $baby_birthday = strtotime($baby_birthday);
		$address = $this->_post('address');
		$user_id = intval(session('user_id'));
        //log_file('realname:'. $realname . ',province_id:' . $province_id . ',city_id:'. $city_id . ', area_id:' . $area_id . ',street:'. $street_id . ',baby_birthday:' . $baby_birthday . ',user_id:' . $user_id . ', address:' . $address);

		if ($user_id) {
			$arr = array(
				'realname'	=> $realname,
				'email'	=> $email,
				'province_id'	=> $province_id ? intval($province_id) : 0,
				'city_id'		=> $city_id ? intval($city_id) : 0,
				'area_id'		=> $area_id ? intval($area_id) : 0,
                'street'   => $street_id,
                'address'  => $address,
                'baby_birthday' => $baby_birthday,
			);
			$user_obj = new UserModel($user_id);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();
            D('MemberCard')->createNewMemberForERPSystemByUserID($user_id);
			//echo $success ? 'success' : 'failure';
		}

		exit('success');
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
			$this->alert('恭喜您，充值成功', U('/FrontUser/personal_data'));
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
			$this->alert('恭喜您，充值成功', U('/FrontUser/personal_data'));
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
		$change_type = intval($this->_request('change_type'));
		$where = $opt == 'recharge' ? 'change_type = 1' : 'change_type > 1';
		$where .= ' AND user_id = ' . $user_id;
		$where .= $change_type ? ' AND change_type = ' . $change_type : '';
		$account_obj = new AccountModel();
		//订单总数
		$total = $account_obj->getAccountNum($where);

		if ($firstRow < $total && $user_id)
		{
			$account_obj->setStart($firstRow);
			$account_obj->setLimit($this->item_num_per_account_page);
			$account_list = $account_obj->getAccountList('user_id,remark, addtime, amount_before_pay, amount_after_pay, amount_in, amount_out, order_id, change_type, operater', $where, 'addtime DESC');
			$account_list = $account_obj->getListData($account_list);

			foreach ($account_list AS $k => $v)
			{
				$account_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			}

			echo json_encode($account_list);
			exit;
		}

		exit('failure');
	}

	//异步获取积分列表
	public function get_integral_list()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$firstRow = I('post.firstRow');
			$where = 'user_id = ' . $user_id;
			$integral_obj = new IntegralModel();
			//订单总数
			$total = $integral_obj->getIntegralNum($where);

			if ($firstRow < ($total - 1) && $user_id)
			{
				$integral_obj->setStart($firstRow);
				$integral_obj->setLimit($this->item_num_per_account_page);
				$integral_list = $integral_obj->getIntegralList('remark, addtime, start_integral, end_integral, integral, id, change_type, operater', $where, 'addtime DESC');
				$integral_list = $integral_obj->getListData($integral_list);

				foreach ($integral_list AS $k => $v)
				{
					$integral_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
				}

				echo json_encode($integral_list);
				exit;
			}
		}
		

		exit('failure');
	}

	//发送验证码
	function send_vcode()
	{	
		$mobile = $this->_post('mobile');
		$type = $this->_post('type');
		$user_id = session('user_id');
		if (is_numeric($mobile) && is_numeric($type) && strlen($mobile) == 11 && $user_id)
		{
			//获取验证码
			$verify_code_obj = new VerifyCodeModel();
			$verify_code = $verify_code_obj->generateVerifyCode($mobile);
			if ($verify_code)
			{	
				$config_obj = new ConfigBaseModel();
				$sms_type = $config_obj->getConfig('sms_type');
				if($sms_type == 2){
					vendor('ChuanglanSmsHelper.ChuanglanSmsApi');
					$sms_obj = new ChuanglanSmsApi();
					$send_state = $sms_obj->sendSMS($mobile,'【b2c】您好，您的验证码是'.$verify_code, 'true');
					$result = $sms_obj->execResult($send_state);
					//dump($result);
					//echo isset($result[1]) && $result[1]==0 ? 'success' : 'failure';
					if(isset($result[1]) && $result[1]==0){
						$this->ajaxReturn('success');
					}else{
						echo "failure";
					}
				}else{
					$sms_obj = new SMSModel();
					$send_state = $sms_obj->sendVerifyCode($mobile, $verify_code);
					#echo $verify_code_obj->getLastSql();
					#$send_state = 1;
					echo $send_state ? 'success' : 'failure';
					exit;
				}
				
			}
		}
		exit('failure');
	}

	//获取微信支付的参数
	function get_wx_param()
	{
		$coin_num = $this->_post('coin_num');
		$user_id = intval(session('user_id'));

		if (is_numeric($coin_num) && $user_id)
		{
			$wxpay_obj = new WXPayModel();
			//JSAPI
			$jsApiParameters = $wxpay_obj->pay_code(0, $coin_num);
			log_file($jsApiParameters);
			$this->json_exit($jsApiParameters);
		}

		$this->json_exit('failure');
	}

	/**
     * 储存反馈意见
     * @author zlf
     * @return void
     * @todo 储存反馈意见
     */
    public function save_suggest_info(){
        $user_id = intval(session('user_id'));
        $message = $this->_post('message');

        if ($user_id && $message)
        {
            $user_suggest_obj = new UserSuggestModel();
            $arr = array(
                'user_id'		=> $user_id,
                'message'		=> $message,
            );
            $success = $user_suggest_obj->addAdvice($arr);
log_file($user_suggest_obj->getLastSql(), 'aa', true);

            echo $success ? 'success' : 'failure';
            exit;
        }

        exit('failure');
    }

    // 会员卡绑定信息
    // @author wsq
    public function bind_member_card()
    {
		//获取用户资料
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('mobile');

	        if (!$user_info['mobile']) {
	            $this->redirect('personal_data');
	        }

	        $obj = new MemberCardModel();
	        $member_card_list = $obj->where('user_id=%d', $user_id)->select();
	        $member_card_list = $obj->getListData($member_card_list);
	        $this->assign('member_card_list', $member_card_list);
	        $user_info['member_card_id'] = count($member_card_list) ? 1:NULL;


			$this->assign('user_info', $user_info);
		}
		
		$this->assign('head_title', '会员卡'.($user_info['member_card_id'] ? "详情":"绑定"));
		//底部导航赋值
		$this->assign('nav', 'wallet');
		$this->display('bind_member_card');
    }

    public function ajax_bind_card()
    {
        $user_id = intval(session('user_id'));
        $member_card_id = I('post.num', 0, 'int');
        $status = false;

        if ($user_id && $member_card_id) {
            $status = D('MemberCard')->bindMemberCard($user_id, $member_card_id);
        }

        exit($status===true ? 'success' : $status);
    }

    public function more_card_bind()
    {
		//获取用户资料
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('mobile');

        if (!$user_info['mobile']) {
            $this->redirect('personal_data');
        }

		$this->assign('user_info', $user_info);
		$this->assign('head_title', '会员卡绑定');
		//底部导航赋值
		$this->assign('nav', 'wallet');
		$this->display('more_card_bind');
    }

    //专属二维码
	public function my_qr_code()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0 ){
			$my_url = 'http://' . $_SERVER['HTTP_HOST'] . '?rec_user_id=' . $user_id;
			// echo $my_url;die;
			//获取二维码地址
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('qr_code,headimgurl');
			$qr_code_path = C('IMG_DOMAIN') . $user_info['qr_code'];
			$this->assign('qr_code', $qr_code_path);
			$this->assign('user_info', $user_info);
		}
		
		#$this->assign('my_url', $my_url);
		$this->assign('head_title', '推荐朋友注册');
		$this->display();
	}

    //好友推荐页面，
    //用来进入公众号的首次关注
    //@author wsq
	public function show_qr_code()
	{
		//$rec_user_id  = I('get.rec_user_id', 0, 'int');
		//$user_info    = D('User')->where('user_id =' . $rec_user_id)->field('nickname, role_type, qr_code')->find();

        ////if(!$user_info) $this->redirect('/FrontUser/register');
		////获取二维码地址
		////$qr_code_path = get_img_url($user_info['qr_code']);

        $qr_code_path = $GLOBALS['config_info']['QR_CODE'];
		$this->assign('qr_code', $qr_code_path);
		$this->assign('role_type', $user_info['role_type']);
		#$this->assign('my_url', $my_url);
		$this->display('my_qr_code');
	}

    // 获取券列表
    // @author wsq
    public function show_ticket()
    {
        $user_id = intval(session('user_id'));
        $model = D('Ticket');
        $model->syncTicketInfo($user_id);
        $list = $model->getTicketList('','user_id='.$user_id.' AND status= 2 AND period_of_validity > '.NOW_TIME);
        $this->assign('ticket_list', $list);
        $this->display('show_ticket');
    }

	//我的团队
	public function my_team()
	{
		$user_id = intval(session('user_id'));
		$this->assign('user_id', $user_id);
		$user_obj = new UserModel($user_id);
		$account_obj = new AccountModel();

		//一级代理人数
		$first_agent_num = $user_obj->getUserNum('first_agent_id = ' . $user_id);
		$this->assign('first_agent_num', $first_agent_num);

		//二级代理人数
		$second_agent_num = $user_obj->getUserNum('second_agent_id = ' . $user_id);
		$this->assign('second_agent_num', $second_agent_num);

		//三级代理人数
		$third_agent_num = $user_obj->getUserNum('third_agent_id = ' . $user_id);
		$this->assign('third_agent_num', $third_agent_num);

		//一级代理收益
		$first_agent_gain = $account_obj->sumAccount('user_id = ' . $user_id . ' AND change_type = ' . AccountModel::FIRST_LEVEL_AGENT_GAIN);
		$this->assign('first_agent_gain', floatval($first_agent_gain));

		//二级代理收益
		$second_agent_gain = $account_obj->sumAccount('user_id = ' . $user_id . ' AND change_type = ' . AccountModel::SECOND_LEVEL_AGENT_GAIN);
		$this->assign('second_agent_gain', floatval($second_agent_gain));

		//三级代理收益
		$third_agent_gain = $account_obj->sumAccount('user_id = ' . $user_id . ' AND change_type = ' . AccountModel::THIRD_LEVEL_AGENT_GAIN);
		$this->assign('third_agent_gain', floatval($third_agent_gain));

		$fenxiao_level = $GLOBALS['config_info']['FENXIAO_LEVEL'];
		$this->assign('fenxiao_level', $fenxiao_level);

		$this->assign('head_title', '我的收益');
		$this->display();
	}

	//代理列表
	public function agent_list()
	{
		$user_id = intval(session('user_id'));
		$agent_type = intval($this->_request('agent_type'));
		$firstRow = intval($this->_request('firstRow'));
		$fetch_num = intval($this->_request('fetch_num'));
		$fetch_num = $fetch_num ? $fetch_num : 8;
		$where = 'is_enable = 1 AND ';
		$where .= $agent_type == 1 ? 'first_agent_id = ' . $user_id : ($agent_type == 2 ? 'second_agent_id = ' . $user_id : 'third_agent_id = ' . $user_id);
		$this->assign('user_id', $user_id);
		$user_obj = new UserModel();
		$total = $user_obj->getUserNum($where);
		$user_obj->setStart($firstRow);
		$user_obj->setLimit($fetch_num);
		$user_list = $user_obj->getUserList('headimgurl, nickname, consumed_money, province, city, reg_time', $where, 'reg_time');
		#echo $user_obj->getLastSql();
		#die;

		if (IS_AJAX && IS_POST)
		{
			#echo $user_obj->getLastSql();
			#dump($user_list);
			//异步请求
			foreach ($user_list AS $k => $v)
			{
				$user_list[$k]['reg_time'] = date('Y-m-d H:i:s', $v['reg_time']);
			}
			$this->json_exit($user_list);
		}
		else
		{
			//同步请求
			$this->assign('user_list', $user_list);
			$this->assign('agent_type', $agent_type);
			$this->assign('firstRow', $firstRow + $fetch_num);
			$this->assign('total', $total);

			$head_title = '我的';
			$head_title .= $agent_type == 1 ? '一级代理' : ($agent_type == 2 ? '二级代理' : '三级代理');
			$this->assign('head_title', $head_title);
			$this->display();
		}
	}

	//查看我的优惠券
	function my_coupon()
	{
		$user_id = intval(session('user_id'));
        if (!$user_id)
        {
            $this->error('对不起，您还没登录！');
        }

        $user_vouchers_obj = new UserVouchersModel;

        // 未使用
        $where = 'isuse = 1 AND end_time > '.NOW_TIME.' AND user_id = '.$user_id;
        $user_vouchers_obj->setLimit(1000);
        $list_one = $user_vouchers_obj->getUserVouchersList('',$where);
        $list_one = $user_vouchers_obj->getListData($list_one);
        // dump($list_one);exit;
        $this->assign('list_one', $list_one);

        // 已使用
        $where = 'isuse = 0 AND user_id = '.$user_id;
        $user_vouchers_obj->setLimit(1000);
        $list_two = $user_vouchers_obj->getUserVouchersList('',$where);
        $list_two = $user_vouchers_obj->getListData($list_two);
        $this->assign('list_two', $list_two);

        // 已过期
        $where = 'isuse = 1 AND end_time < '.NOW_TIME.' AND user_id = '.$user_id;
        $user_vouchers_obj->setLimit(1000);
        $list_three = $user_vouchers_obj->getUserVouchersList('',$where);
        $list_three = $user_vouchers_obj->getListData($list_three);
        $this->assign('list_three', $list_three);

		$this->assign('head_title', '我的优惠券');
		$this->display();
	}

	//设置提成比例
	function set_rate()
	{
		$user_id = cur_user_id();
		$user_obj = new UserModel($user_id);

		//表单提交处理
		$act = $this->_post('act');
		if ($act == 'save')
		{
			$first_agent_rate = floatval($this->_post('first_agent_rate'));
			$arr = array(
				'first_agent_rate'	=> $first_agent_rate
			);
			$user_obj->setUserInfo($arr);
			$user_obj->saveUserInfo();
			$this->alert('恭喜您，设置成功！', '/FrontUser/set_rate');
		}

		$user_info = $user_obj->getUserInfo('first_agent_rate');
		$first_agent_rate = $user_info['first_agent_rate'] > 0 ? $user_info['first_agent_rate'] : (100 - $GLOBALS['config_info']['FIRST_LEVEL_AGENT_RATE']);

		$this->assign('first_agent_rate', $first_agent_rate);
		$this->assign('head_title', '提成设置');
		$this->display();
	}

	//成为分销商
	public function be_seller()
	{
		$this->assign('head_title', '成为分销商');
		$this->display();
	}

	//合伙人列表页（一、二、三级）
	public function partner_list()
	{
		$this->assign('head_title', '合伙人列表页');
		$this->display();
	}

	//合伙人订单列表页（一、二、三级）
	public function partner_order_list()
	{
		$this->assign('head_title', '合伙人订单列表');
		$this->display();
	}

	//我的财富奖励明细
	public function award_list()
	{
		$this->assign('head_title', '奖励明细');
		$this->display();
	}

	//添加支付宝
	public function add_alipay()
	{
		$user_id = cur_user_id();
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('alipay_account, alipay_account_name');
		$this->assign('user_info', $user_info);

		$act = $this->_request('act');
		if ($act == 'save')
		{
			$alipay_account_name = $this->_request('zfb_name');
			$alipay_account = $this->_request('zfb_id');

			$arr = array(
				'alipay_account_name'	=> $alipay_account_name,
				'alipay_account'		=> $alipay_account,
			);
			$user_obj->setUserInfo($arr);
			$user_obj->saveUserInfo();
			$this->alert('恭喜您，账号绑定成功！', '/FrontUser/personal_center');
		}

		$this->assign('head_title', '添加支付宝');
		$this->display();
	}

	//申请提现
	public function getcash()
	{
		$user_id = cur_user_id();
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money');
		$this->assign('user_info', $user_info);

		//今日是否提现
		$deposit_apply_obj = new DepositApplyModel();
		$deposit_num = $deposit_apply_obj->getDepositApplyNum('addtime >= ' . strtotime('today') . ' AND user_id = ' . $user_id);
		$this->assign('deposit_num', $deposit_num);

		$act = $this->_request('act');
		if ($act == 'save')
		{
			$money = $this->_request('money');

			$arr = array(
				'money'			=> $money,
				'user_id'		=> $user_id,
			);
			$deposit_apply_obj = new DepositApplyModel();
			$deposit_apply_obj->addDepositApply($arr);
			$this->alert('恭喜您，提现申请已提交！', '/FrontUser/personal_center');
		}

		$this->assign('deposit_fee', $GLOBALS['config_info']['DEPOSIT_FEE']);
		$this->assign('no_footer', 1);
		$this->assign('head_title', '申请提现');
		$this->display();
	}

	//积分兑换记录
	public function integral_exchange_list()
	{
		$order_obj = new OrderModel();
		$where = 'user_id ='.session('user_id') . ' and is_integral = 1';
		$total = $order_obj->getOrderNum($where);
		
		$firstRow = intval($this->_request('firstRow'));
		$order_obj->setStart($firstRow);
        $order_obj->setLimit($this->item_num_per_account_page);
		$order_list = $order_obj->getOrderList('order_id,order_sn, order_status,pay_amount, integral_amount',$where, 'addtime desc');
		foreach ($order_list as $k => $v) {
			switch ($v['order_status']) {
				case 1:
					$order_list[$k]['order_status'] = '支付成功';
					break;
				case 2:
					$order_list[$k]['order_status'] = '已发货';
					break;
				case 3:
					$order_list[$k]['order_status'] = '已收货';
					break;
				case 5:
					$order_list[$k]['order_status'] = '退款处理中';
					break;
				case 6:
					$order_list[$k]['order_status'] = '退款受理中';
					break;
				default:
					$order_list[$k]['order_status'] = '未支付';
					break;
			}

			$integral_exchange_record_item_obj = new IntegralExchangeRecordItemModel($v['order_id']);
			$item_info = $integral_exchange_record_item_obj->getOrderItemList('item_name,item_id');

			$order_list[$k]['item_name'] = $item_info[0]['item_name'];
			$order_list[$k]['item_id'] = $item_info[0]['item_id'];
		}

		if(IS_POST && IS_AJAX){
			$this->json_exit($order_list);
		}
		$this->assign('order_list', $order_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_account_page);
		$this->assign('head_title', '积分兑换记录');
		$this->display();
	}


	//提现申请
	public function deposit_apply(){
		$user_id = session('user_id');
		$money = I('money','','intval');
		if(!$money){
			$this->ajaxReturn(array('code'=>1, 'msg'=>'请输入提现金额'));
		}
		if($money < $GLOBALS['config_info']['CASH_LIMIT']){
			$this->ajaxReturn(array('code'=>1, 'msg'=>'单次提现金额不得低于'.$GLOBALS['config_info']['CASH_LIMIT'].'元'));
		}

		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money');
		if($user_info['left_money'] < $money){
			$this->ajaxReturn(array('code'=>1, 'msg'=>'您的余额不足'));
		}

		//一天内是否提现
		//$cash_time = strtotime('today') - 3600 * 24 * 7;
		$deposit_apply_obj = new DepositApplyModel();
		$deposit_num = $deposit_apply_obj->getDepositApplyNum('addtime >= ' . strtotime('today') . ' AND user_id = ' . $user_id);

		if($deposit_num){
			$this->ajaxReturn(array('code'=>1, 'msg'=>'一天只能提现一次，您在一天内已提现过'));
		}

		$arr = array(
			'money'			=> $money,
			'user_id'		=> $user_id,
		);
		if($deposit_apply_obj->addDepositApply($arr)){
			$this->ajaxReturn(array('code'=>0, 'msg'=>'提现申请成功'));
		}
			

	}


	//app端头像上传
	public function setAvater(){
		if(IS_AJAX&&IS_POST){
			$user_id = session('user_id');
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('headimgurl');
			$this->ajaxReturn($user_info['headimgurl']);
		}
	}

	//获取快递单查询结果
	function query_express()
	{
		$order_id = intval($this->_request('order_id'));
		$order_obj = new OrderModel($order_id);

		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, is_end, last_search_time, express_number, com, detail', ' AND express_number != ""');
		}
		catch (Exception $e)
		{
			$return_arr = array(
				'code'	=> -1,
				'msg'	=> '对不起，订单未发货或发货人未填写快递单号！'
			);
			$this->json_exit($return_arr);
		}

        if (!$order_info['is_end'] && ($order_info['last_search_time'] + 7200) < NOW_TIME)
        {
			log_file(json_encode($order_info), 'aaaa', true);
            // 如果快递还是运输中，并且当前距离最后查询时间大于2小时，则调用API查询快递信息

            vendor('juhe.kuaidi');
            $exp = new exp(C('JUHE_KUAIDI_KEY')); //初始化类

            $com    = $order_info['com'];
            $no     = $order_info['express_number'];
            $result = $exp->query($com,$no); //执行查询

            if($result['error_coke'] == 0) {//查询成功
                $order_info['detail'] = json_encode($result['result']['list']);// 快递明细
                $order_info['is_end'] = $result['result']['status'];// 快递是否结束标识
            }

            $order_info['last_search_time'] = NOW_TIME;// 标记当前查询时间

            // 保存信息
            $order_obj->setOrderInfo($order_info);
			$order_obj->saveOrderInfo();
			log_file($order_obj->getLastSql(), 'aaaa', true);
			#echo $order_obj->getLastSql();
			#die;
        }

        // 获取快递公司名称
        $company_list = ShippingCompanyModel::getCompanyList();
        $order_info['com'] = $company_list[$order_info['com']];

        // json 解码
        $order_info['detail'] = json_decode($order_info['detail'],true);
        // 倒序
        $order_info['detail'] = array_reverse($order_info['detail']);
		//返回状态
		$order_info['code'] = 0;

		$this->json_exit($order_info);
	}
}

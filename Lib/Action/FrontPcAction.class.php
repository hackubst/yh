<?php
// 用于前台展示的基类
class FrontPcAction extends GlobalAction {

	/**
	 * 初始化函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用父类初始化方法，控制前台的页面展示，COIE值设置，公用链接等
	 */
	function _initialize()
	{
		parent::_initialize();

		//获取用户信息
		$user_id = intval(session('user_id'));

		//if (!$user_id)
		//{
		//	/*if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
		//	{
		//		//微信用户获取用户ID
		//		$this->get_weixin_user_info();
		//	}
		//	else
		//	{
		//		//非微信用户获取用户ID
		//		$this->get_user_info();
		//	}*/
		//	//没有登录
		//	$state = intval($this->_get('login_state'));
		//	if (!(ACTION_NAME == 'login' && !$state))
		//	{
		//		if ($state)
		//		{
		//			/*$url = '/FrontAddress/add_address';
		//			$this->alert('您还没有设置地址，请先添加一个地址', $url);*/
		//		}
		//		else
		//		{
		//			if (ACTION_NAME != 'login')
		//			{
		//				$url = '/FrontUser/login/login_state/1';
		//				redirect($url);
		//			}
		//		}
		//	}
		//}
		//else
		//{
		//	$user_obj = new UserModel();
		//	$user_info = $user_obj->getUserInfo('user_id', 'user_id = ' . $user_id);
		//}
        $this->assign('pc_site', 1);

		/*** 链接传值begin ***/
		//商城首页 going-zlf ok
		$this->assign('home_link', U('/'));
		//APP商城首页
		$this->assign('app_index_link', U('/FrontMall/app_index'));
		//商品分类列表页（新品列表,开业装修列表，降价商品,套餐商品，搜索结果页） going-zlf OK
		$this->assign('item_list_link', '/FrontMall/item_list/');
		//商品详情页 (商品详情页，套餐商品详情) going-zlf OK
		$this->assign('item_detail_link', '/FrontMall/item_detail/item_id/');
		//日常快速订单 going-zlf OK
		$this->assign('daily_item_link', U('/FrontMall/daily_item'));
		//历史常购订单 going-zlf OK
		$this->assign('history_item_link', '/FrontMall/history_item');
		//首批订单 going-zlf OK
		$this->assign('first_order_link', U('/FrontMall/first_order'));
		//积分兑换（列表）going-zlf OK
		$this->assign('integral_exchange_link', U('/FrontMall/integral_exchange'));
		//积分兑换商品详情 going-zlf OK
		$this->assign('integral_item_detail_link', '/FrontMall/integral_item_detail/item_id/');
		//我的收藏 going-zlf OK
		$this->assign('my_collect_link', U('/FrontMall/my_collect'));

		//个人中心首页 going-zlf OK
		$this->assign('personal_center_link', U('/FrontUser/personal_center'));
		//个人信息  going-zlf OK
		$this->assign('personal_data_link', U('/FrontUser/personal_data'));
		//账户余额（充值，交易记录） going-zlf OK
		$this->assign('my_account_link', U('/FrontUser/my_account'));
		//优惠券 going-zlf OK
		$this->assign('coupon_list_link', U('/FrontUser/coupon_list'));
		//需求提交 going-zlf OK
		$this->assign('requirment_submit_link', '/FrontUser/requirment_submit');
		//需求列表 going-zlf OK
		$this->assign('requirment_list_link', '/FrontUser/requirment_list');
		//需求详情 going-zlf
		$this->assign('requirment_detail_link', '/FrontUser/requirment_detail/id/');
		//登录 going-zlf OK
		$this->assign('login_link', '/FrontUser/login');
		//退出 going-zlf OK
		$this->assign('logout_link', '/FrontUser/logout');
		//修改密码 going-zlf OK
		$this->assign('edit_pwd_link', U('/FrontPassword/edit_pwd'));

		//我的收货地址列表页 going-wsq
		$this->assign('address_list_link', U('/FrontAddress/address_list'));
		//我的地址-添加地址 going-wsq
		$this->assign('add_address_link', U('/FrontAddress/add_address'));
		//我的地址-修改地址 going-wsq
		$this->assign('edit_address_link', '/FrontAddress/edit_address/address_id/');
		//公告列表 going-zlf OK
		$this->assign('notice_list_link', '/FrontNews/notice_list');
		//公告详情页 going-zlf OK
		$this->assign('notice_detail_link', '/FrontNews/notice_detail/id/');
		//底部文章详情页 going-zlf OK
		$this->assign('help_detail_link', '/FrontHelp/help_detail/id/');

		//我的积分(积分交易记录) going-wsq
		$this->assign('integral_list_link', '/FrontIntegral/integral_list');
		//积分兑换商品 going-wsq
		$this->assign('integral_item_link', '/FrontIntegral/integral_item');
		//积分换购记录 going-wsq
		$this->assign('integral_exchange_record_link', '/FrontIntegral/integral_exchange_record');
		//积分兑换明细详情 going-wsq
		$this->assign('integral_exchange_detail_link', '/FrontIntegral/integral_exchange_detail/integral_id/');
		
		//积分购物车页面 going-jw
		$this->assign('integral_cart_link', U('/FrontCart/integral_cart'));
		//购物车页面 going-jw  OK
		$this->assign('cart_link', U('/FrontCart/cart'));
		//订单提交页 going-jw  OK
		$this->assign('order_submit_link', C('ADDR_URL'));
		//订单支付 going-jw  OK
		$this->assign('pay_order_link', '/FrontOrder/pay_order/order_id/');
		//付款成功 going-jw XX
		$this->assign('pay_success_link', '/FrontOrder/pay_success');
		//订单列表页-全部订单 going-jw OK
		$this->assign('all_order_link', U('/FrontOrder/all_order'));
		//订单列表页-待支付 OK
		$this->assign('pre_pay_order_link', U('/FrontOrder/pre_pay_order'));
		//订单列表页-待发货  OK
		$this->assign('pre_deliver_order_link', U('/FrontOrder/pre_deliver_order'));	
		//订单详情 going-jw OK
		$this->assign('order_detail_link', '/FrontOrder/order_detail/order_id/');
		//申请退货 going-jw OK
		$this->assign('refund_apply_link', '/FrontOrder/refund_apply/order_id/');
		//查看退货详情 going-jw OK
		$this->assign('order_refund_detail_link', '/FrontOrder/order_refund_detail/order_id/');
		/*** 链接传值end ***/

		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('nickname, user_rank_id, left_integral');

		if(!$user_id && strtoupper(PHP_OS)!="LINUX")
		{
			//session('user_id', 1);
			//$user_id = 1;
		}

		//传值到前台
		$user_id = intval(session('user_id'));
		$this->assign('user_id', $user_id ? $user_id : 0);
		$this->assign('user_nickname', $user_info['nickname']);

		$this->assign('client_ip', get_client_ip());	
		$config = $this->system_config;
		//全国投诉热线
		$this->assign('complains_hotline',$config['COMPLAINTS_HOTLINE']);
		//订货热线
		$this->assign('order_hotline',$config['ORDER_HOTLINE']);
		//售后热线
		$this->assign('service_hotline',$config['SERVICE_HOTLINE']);
		//总QQ号服务
		$this->assign('customer_service_qq',$config['CUSTOMER_SERVICE_QQ']);
		//底部备案
		$this->assign('official_record',$config['OFFICIAL_RECORD']);

		//获取购物车信息
		$this->get_cart();

		//底部配置信息
		$this->get_footer();

		//获取客服列表
		$this->get_service_list();

		//获取用户等级和积分总数
		$user_rank_info = D('UserRank')->getUserRankInfo('user_rank_id = ' . $user_info['user_rank_id'], 'rank_name');
		$this->assign('user_rank_name', $user_rank_info['rank_name']);	
		$this->assign('total_integral', $user_info['left_integral']);	

		//获取分类信息
		$sort_obj = new SortModel();
		$class_list = $sort_obj->getCategoryList();
		$this->assign('class_list', $class_list);

		//面包屑导航数据获取
		$this->get_breadcrum();
	}

	/**
	 * 根据COIE获取用户信息，判断是否会员，若是，写session后退出；否则为之注册
	 * @author 姜伟
	 * @param void
	 * @return user_id
	 * @todo 
	 */
	function get_user_info()
	{
		$user_obj = new UserModel();
		$user_info = $user_obj->getUserInfo('user_id', 'user_coie = "' . $GLOBALS['user_coie'] . '"');
		$user_id = 0;
		if ($user_info)
		{
			//用户存在，设置session
			$user_id = $user_info['user_id'];
			session('user_id', $user_id);
		}
		else
		{
			//用户不存在，为之注册
			$arr = array(
				'user_coie'           => $GLOBALS['user_coie'],
				'role_type'             => 3,
				'is_enable'             => 1,
				'reg_time'              => time(),
			);
			$user_id = $user_obj->addUser($arr);
			session('user_id', $user_id);
		}

		return $user_id;
	}

	/**
	 * 获取微信用户信息，判断是否会员，若是，写session后退出；否则为之注册
	 * @author 姜伟
	 * @param void
	 * @return user_id
	 * @todo 
	 */
	function get_weixin_user_info()
	{
		$appid = C('appid');
		$secret = C('appsecret');

		if (!isset($_GET['code']))
		{
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			//获取授权码的接口地址
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($redirect_uri) .'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
			$user_model = M('users');
			$user_info = $user_model->field('user_id, current_planter_id, role_type, openid, nickname, sex, headimgurl, access_ten, refresh_ten, ten_expires_time')->where('user_coie = "' . $GLOBALS['user_coie']. '"')->find();
			if (!$user_info)
			{
				redirect($url);
			}

			//访问令牌过期
			if ($user_info['ten_expires_time'] < time())
			{
				//刷新令牌存在，用之交换访问令牌
				if ($user_info['refresh_ten'])
				{
					$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_ten?appid=' . $appid . '&grant_type=refresh_ten&refresh_ten=' . $user_info['refresh_ten'];
					Vendor('Wxin.WeiXin');
					$wx_obj = new WeiXinUser($appid, $secret);
					$wx_obj->getAccessTen($url);
				}
				else
				{
					redirect($url);
				}
			}
			else
			{
				session('user_id', $user_info['user_id']);
				session('planter_id', $user_info['current_planter_id']);
			}
		}
		else
		{
			$url = "https://api.weixin.qq.com/sns/oauth2/access_ten?appid=" . $appid . "&secret=" . $secret . "&code=" . $_GET['code'] .  "&grant_type=authorization_code";
			Vendor('Wxin.WeiXin');
			$wx_obj = new WeiXinUser($appid, $secret);
			$wx_obj->getAccessTen($url);
		}

		return $user_info['user_id'];
	}

	/**
	 * 404页面
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 输出404页面信息
	 */
	public function page_not_found()
	{
		$this->display();
	}

	/**
	 * 支付宝付款成功后的异步回调处理
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用支付宝支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function alipay_response()
	{
		//通知记录
		$file = fopen('Logs/alipay_response_log.txt', 'a');
		fwrite($file, date('Y-m-d H:i:s', time()) . "\t" . (isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : 'invalid visit') . "\n");
		fclose($file);

		$alipay_obj = new AlipayModel();
		$alipay_obj->pay_response();
	}
	

	/**
	 * 支付宝付款成功后的异步回调处理
	 * @author zt
	 * @param void
	 * @return void
	 * @todo 调用支付宝支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function alipay_response_post()
	{
		//通知记录
		#$file = fopen('Logs/alipay_response_log.txt', 'a');
		$file = fopen('logs/alipay_response_log.' . date('Y-m-d', time()) . '.txt', 'a');
		fwrite($file, date('Y-m-d H:i:s', time()) . "\t" . (isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : 'invalid visit') . "\n");
		fclose($file);
	
		$alipay_obj = new AlipayModel();
		$return_state = $alipay_obj->pay_response_post();
		if ($return_state == 2)
		{
			$total_fee = $_POST['total_fee'];	//充值的总额
			require_once('Common/func_sms.php');
			$sms_total = sms_recharge($total_fee);	//为客户充值的金额
				
			//短信充值日志的记录
			$account = new AccountModel();
			$account->addSMSPayLog($_POST['out_trade_no'],1,$total_fee,$sms_total);
				
			$this->success('恭喜您，充值成功', '/AcpConfig/sms_config/mod_id/0');
		}
		else
		{
			$this->error('对不起，非法访问', U('/'));
		}
	}
	
	/**
	 * 微信付款成功后的异步回调处理
	 * @author zt
	 * @param void
	 * @return void
	 * @todo 调用微信支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function wxpay_response()
	{
		//通知记录
		#$file = fopen('logs/weixin_response_log.' . date('Y-m-d', time()) . '.txt', 'a');
		#$str = '';
		#foreach($_REQUEST as $key => $val)
		#{
			#$str .= "&$key=$val";
		#}
		#fwrite($file, date('Y-m-d H:i:s', time()) . "\t" . 'post str: ' . $str . "\n");
		#fwrite($file, date('Y-m-d H:i:s', time()) . "\t" . (isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : 'invalid visit') . "\n");
		#fclose($file);

		$wxpay_obj = new WXPayModel();
		$wxpay_obj->pay_response();
	}
	

	//js弹窗函数
	protected function alert($msg, $redirect = '')
	{
		echo '<script>alert("' . $msg . '");</script>';
		if ($redirect)
		{
			echo '<script>location.href="' . $redirect . '"</script>';
		}
		else
		{
			echo '<script>location.href="' . url_jiemi($this->cur_url) . '"</script>'; 
		}
		exit;
	}

	//客服
	function customer_service()
	{
	}

	//底部公共模块
	function get_footer()
	{
		//获取文章列表
		$helpCenter = new HelpCenterModel();		
		$fields = 'help_sort_id,help_sort_name';
		$where = 'isuse=1';
		$helpCenterCategory = new HelpCenterCategoryModel();
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields, $where);
		
		foreach($helpCenterCategoryList as $key => $val)
		{
			$fields = 'help_id,title';
			$where = 'isuse=1 AND help_sort_id=' . $val['help_sort_id'];
			$helpCenterCategoryList[$key]['left_help_center_menu'] = $helpCenter->getHelpList('', 'serial ASC,addtime DESC', $fields, $where);
		}
		
		$this->assign('help_center_category_list', $helpCenterCategoryList);

	}

	//获取客服列表
	function get_service_list()
	{
		$customer_obj = new CustomerServiceOnlineModel();
		$customer_prev_list = $customer_obj->getCustomerServiceOnlineList('customer_service_online_name, account', 'is_after_service = 0 AND isuse = 1');
		$customer_after_list = $customer_obj->getCustomerServiceOnlineList('customer_service_online_name, account', 'is_after_service = 1 AND isuse = 1');
		$this->assign('customer_prev_list', $customer_prev_list);
		$this->assign('customer_after_list', $customer_after_list);

	}

	//购物车信息
	function get_cart()
	{
		$where = ' AND is_integral = 0';
		$cart_obj = new ShoppingCartModel();
		$cart_list = $cart_obj->getShoppingCartList('shopping_cart_id, item_id, item_package_id, mall_price, real_price, number, item_name, small_pic', $where);
		$this->assign('cart_list', $cart_list);

		//购物车内商品总价
		$cart_total_amount = $cart_obj->sumCartAmount($where);

		//购物车内商品总数量
		$cart_total_num = $cart_obj->getShoppingCartNum($where);

		$this->assign('cart_total_amount', $cart_total_amount);
		$this->assign('cart_total_num', $cart_total_num);
	}

	//获取面包屑导航数据
	function get_breadcrum()
	{
		$class_id = $this->_request('class_id');
		$sort_id = $this->_request('sort_id');
		$item_id = intval($this->_request('item_id'));
		$Class = D('Class');
		$Sort = D('Sort');
		$Item = D('Item');
		//有一二级分类时
		if(ctype_digit($class_id)){
			$class_name = $Class->getClassField($class_id, 'class_name');
			$this->assign('class_id', $class_id);
			$this->assign('class_name', $class_name);

		}else if(ctype_digit($sort_id)){
			$sort_name = $Sort->getSortField($sort_id, 'sort_name');
			$sort_class_id = $Sort->getSortField($sort_id, 'class_id');
			$class_name = $Class->getClassField($sort_class_id, 'class_name');
			$this->assign('sort_id', $sort_id);
			$this->assign('sort_name', $sort_name);
			$this->assign('class_id', $sort_class_id);
			$this->assign('class_name', $class_name);
		}
		//商品详情时
		if($item_id){
			$item_info = $Item->getItemInfo('item_id = ' . $item_id, 'class_id, sort_id');
			if($item_info['sort_id']){
				$sort_name = $Sort->getSortField($item_info['sort_id'], 'sort_name');
				$sort_class_id = $Sort->getSortField($item_info['sort_id'], 'class_id');
				$class_name = $Class->getClassField($sort_class_id, 'class_name');
				$this->assign('sort_id', $item_info['sort_id']);
				$this->assign('sort_name', $sort_name);
				$this->assign('class_id', $sort_class_id);
				$this->assign('class_name', $class_name);
			}else if($item_info['class_id']){
				$class_name = $Class->getClassField($item_info['class_id'], 'class_name');
				$this->assign('class_id', $item_info['class_id']);
				$this->assign('class_name', $class_name);
			}

		}
	}

	/**
	 * 附件上传处理
	 * @author zlf
	 * @return string 返回JSON字符串
	 * @todo 调用upFileHandler函数处理上传附件
	 *
	 */
	public function uploadHandler()
	{
		$user_id = intval(session('user_id'));
		upFileHandler($_FILES['upfile'], '/user/' . $user_id);
	}

}

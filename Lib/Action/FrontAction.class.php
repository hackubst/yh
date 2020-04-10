<?php
// 用于前台展示的基类
class FrontAction extends GlobalAction {

	/**
	 * 初始化函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用父类初始化方法，控制前台的页面展示，COOKIE值设置，公用链接等
	 */
	function _initialize()
	{
#session('user_address_id', null);
#session('area_id', null);
#die;
#session('user_id', 39454);
#session('user_id', null); die;
		/*if (ACTION_NAME != 'address_list')
		{
			parent::_initialize();
		}

		if (ACTION_NAME == 'address_list')
		{
			header("Content-Type:text/html; charset=utf-8");
			#die('请通过微信访问');
		}*/
		parent::_initialize();
		$footer_path = APP_PATH . '/Tpl/footer.html';
		/*if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
		{*/
			$base_path = dirname($this->_get_template_file('mall_home'));
#$this->assign('base_path', $base_path);
			$this->assign('header_path', $base_path ? $base_path . '/header.html' : '../header.html');
			$footer_path = $base_path ? $base_path . '/footer.html' : '../footer.html';
			$tpl_path = dirname($this->_get_template_rel_file('mall_home'));
			$this->assign('tpl_path', $tpl_path);
		//}
		$this->assign('footer_path', $footer_path);
#echo MODULE_NAME . '/' . ACTION_NAME;
		#echo 'aa';
		#die;
		//过滤PC端访问
		if (strpos(strtolower(ACTION_NAME), 'response'))
		{
			return;
		}
		//session有效时间设置
        ini_set('session.gc_maxlifetime', 1440);

		//获取用户信息
		$user_id = intval(session('user_id'));
/*if ($user_id == 7088)
{
	session('user_address_id', null);
	session('cur_lon', null);
	session('cur_lat', null);
die;
}*/
		#session('user_id', 7203);
		#session('user_address_id', null);
		#echo $user_id;
		#die;

        //推广员ID 验证是是否是推广员
        $parent_id = I('get.register_p', 0, 'int');
        if ($parent_id) {
            $info = M('Users')->where('role_type = 5 AND is_enable = 1 AND user_id = ' . $parent_id)->find();
            if ($info) {
                $this->parentId = $parent_id;
            } else {
                $this->parentId = 0;
            }
        }

		if (!$user_id && strtoupper(PHP_OS) != 'LINUX')
		{
			session('user_id', 39454);
		}

		if (!$user_id && strtoupper(PHP_OS) == 'LINUX')
		{
			#$this->get_weixin_user_info();
			if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
			{
				//微信用户获取用户ID
				#$this->get_weixin_user_info();
			}
			else
			{
				//非微信用户获取用户ID
				#$this->get_user_info();
			}
			$this->assign('first', 'yes');
		}
		else
		{
			$user_obj = new UserModel();
			$user_info = $user_obj->getUserInfo('user_id', 'user_id = ' . $user_id);
		}

		/*** 链接传值begin ***/
		//首页 XX
		$this->assign('home_link', U('/'));
		//商城首页 
		$this->assign('mall_home_link', '/FrontMall/mall_home');
        //会员卡功能
		$this->assign('member_card_bind_link', '/FrontUser/bind_member_card');
		$this->assign('more_card_bind_link', '/FrontUser/more_card_bind');
		$this->assign('show_ticket_link', '/FrontUser/show_ticket');
		//商家分类列表页 
		$this->assign('mall_list_link', '/FrontMall/mall_list');
		//商家店铺商品列表页
		$this->assign('item_list_link', '/FrontMall/item_list');
		//商品详情页
		$this->assign('item_detail_link', '/FrontMall/item_detail/item_id/');
		//我的收藏
		$this->assign('my_collect_link', U('/FrontMall/my_collect'));
		//商家店铺详情页
		$this->assign('mall_detail_link', '/FrontMall/mall_detail/merchant_id/');
		//购物车页面
		$this->assign('cart_link', U('/FrontCart/cart'));
		//修改密码
		$this->assign('edit_pwd_link', U('/FrontPassword/edit_pwd'));
		//个人中心首页
		$this->assign('personal_center_link', U('/FrontUser/personal_center'));
		//专属二维码 OK
		$this->assign('my_qr_code_link', '/FrontUser/my_qr_code');
		//个人资料
		$this->assign('personal_data_link', U('/FrontUser/personal_data'));
		//充值页
		$this->assign('recharge_link', '/FrontUser/recharge/');
		//钱包
		$this->assign('wallet_link', U('/FrontUser/wallet'));
		//用户评价页
		$this->assign('assessment_link', '/FrontUser/assessment');
		//绑定手机号
		$this->assign('bind_mobile_link', U('/FrontUser/bind_mobile'));
		//申请退款页
		$this->assign('apply_refund_link', '/FrontUser/apply_refund');
		//充值记录页
		$this->assign('recharge_list_link', U('/FrontUser/recharge_list'));
		//财务明细
		$this->assign('account_list_link', U('/FrontUser/account_list'));
		//积分明细
		$this->assign('integral_list_link', U('/FrontUser/integral_list'));
		//镖金明细/我的镖金
		$this->assign('freight_list_link', U('/FrontUser/freight_list'));
		//我的镖金优惠券
		$this->assign('freight_coupon_list_link', U('/FrontShare/get_freight_coupon_list'));
		//镖师详情页
		$this->assign('foot_man_detail_link', '/FrontUser/foot_man_detail/foot_man_id/');
		//消息中心
		$this->assign('message_list_link', U('/FrontUser/message_list'));
		//消息详情页
		$this->assign('message_detail_link', '/FrontUser/message_detail/id/');
		//意见反馈
		$this->assign('suggest_link', '/FrontUser/suggest');
		//设置页
		$this->assign('setup_link', U('/FrontUser/setup'));
		//新闻列表页
		$this->assign('news_list_link', U('/FrontNews/news_list'));
		//新闻详情页
		$this->assign('news_detail_link', '/FrontNews/news_detail/id/');
		//关于我们
		$this->assign('about_us_link', '/FrontHelp/about');
		//系统公告列表页
		$this->assign('notice_list_link', U('/FrontNews/notice_list'));
		//系统公告详情页
		$this->assign('notice_detail_link', '/FrontNews/notice_detail/id/');
		//搜索页
		$this->assign('search_link', U('/FrontSearch/search'));
		//商家搜索结果页
		$this->assign('search_shop_result_link', '/FrontSearch/search_shop_result');
		//商品搜索结果页
		$this->assign('search_item_result_link', '/FrontSearch/search_item_result');
		//帮助中心列表页
		$this->assign('help_list_link', U('/FrontHelp/help_list'));
		//帮助中心详情页
		$this->assign('help_detail_link', '/FrontHelp/help_detail/id/');
		//切换城市
		$this->assign('city_list_link', U('/FrontAddress/city_list'));
		//我的地址列表页
		$this->assign('address_list_link', U('/FrontAddress/address_list'));
		//我的地址-添加地址
		$this->assign('add_address_link', U('/FrontAddress/add_address'));
		//我的地址-修改地址
		$this->assign('edit_address_link', '/FrontAddress/edit_address/address_id/');
		//地图详情页
		$this->assign('map_detail_link', '/FrontAddress/map_detail');
		//订单提交页
		$this->assign('order_submit_link', C('ADDR_URL'));
		//订单支付
		$this->assign('pay_order_link', '/FrontOrder/pay_order/order_id/');
		//订单列表页-全部订单
		$this->assign('all_order_link', U('/FrontOrder/all_order'));
		//订单列表页-待支付
		$this->assign('pre_pay_order_link', U('/FrontOrder/pre_pay_order'));
		//订单列表页-待发货
		$this->assign('pre_deliver_order_link', U('/FrontOrder/pre_deliver_order'));
		//订单列表页-待确认
		$this->assign('pre_confirm_order_link', U('/FrontOrder/pre_confirm_order'));
		//订单列表页-待评价
		$this->assign('pre_review_order_link', U('/FrontOrder/pre_review_order'));
		//订单列表页-退款售后
		$this->assign('pre_refund_order_link', U('/FrontOrder/pre_refund_order'));
		//订单详情
		$this->assign('order_detail_link', '/FrontOrder/order_detail/order_id/');

		//成为分销商
		$this->assign('be_seller_link', '/FrontUser/be_seller');
		//个人中心首页(新版)
		$this->assign('personal_center_v2_link', U('/FrontUser/personal_center_v2'));
		//合伙人列表页（一、二、三级）
		$this->assign('partner_list_link', '/FrontUser/partner_list/level/');
		//合伙人订单列表页（一、二、三级）
		$this->assign('partner_order_list_link', '/FrontUser/partner_order_list/level/');
		//财富奖励明细
		$this->assign('award_list_link', '/FrontUser/award_list');
		//积分商城首页
		$this->assign('integral_mall_home_link', '/FrontMall/integral_mall_home');
		//积分商城列表
		$this->assign('integral_item_list_link', '/FrontMall/integral_item_list');
		//积分商品详情
		$this->assign('integral_item_detail_link', '/FrontMall/integral_item_detail/item_id/');
		//积分兑换记录
		$this->assign('integral_exchange_list_link', '/FrontUser/integral_exchange_list');
		//积分商品订单详情
		$this->assign('integral_order_detail_link', '/FrontMall/integral_order_detail/order_id/');
		//提现申请
		$this->assign('getcash_link', '/FrontUser/getcash');

		//活动相关
		//活动专区
		$this->assign('coupon_index_link', '/FrontCoupon/coupon_index');
		//特价商品列表
		$this->assign('item_coupon_list_link', '/FrontCoupon/item_coupon_list');
		//特价商品列表
		$this->assign('item_coupon_list_link', '/FrontCoupon/item_coupon_list');
		//满减活动列表
		$this->assign('full_minus_list_link', '/FrontCoupon/full_minus_list');
		//买赠活动列表
		$this->assign('buy_gifts_list_link', '/FrontCoupon/buy_gifts_list');
		/*** 链接传值end ***/

		//生成二维码
		get_qr_code();

		//if(strtoupper(PHP_OS)!="LINUX")
		/*$user_id = intval(session('user_id'));
		if (!$user_id)
		{
			session('user_id', 6892);
			$user_id = 6892;
			//测试，上线后删除
			#$user_address_id = $user_address_id ? $user_address_id : 41;
			#session('user_address_id', $user_address_id);
		}*/

		//查看用户是否绑定手机号
		/*$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('mobile, role_type');
		if (ACTION_NAME != 'share' && ACTION_NAME != 'alipay_response' && ACTION_NAME != 'alipay_response_post' && ACTION_NAME != 'wxpay_response' && $user_info['mobile'] == '')
		{
			//未绑定手机号，跳转到绑定手机号页面
			$url = '/FrontUser/bind_mobile/state/1';
			#redirect($url);
		}*/

		//是否关注微信号，没有则跳转到扫码页面
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('subscribe, openid');
		if (!$user_info['openid']) {
			//$this->get_weixin_user_info();
		}

		if (strtoupper(PHP_OS) == 'LINUX' && intval($user_info['subscribe']) != 1  && ACTION_NAME != 'show_qr_code') 
		{
			//调用微信查看是否关注
			$appid = C('APPID');
			$secret = C('APPSECRET');
			Vendor('Wxin.WeiXin');
			$token_id = WxApi::getAccessToken($appid, $secret);
			$wx_obj = new WxApi($token_id);
			$user_info_wx = $wx_obj->user_info($user_info['openid']);
			if ($user_info_wx['subscribe'] == 1) {
				//已关注修改数据库中的值
				$user_obj->editUserInfo(array('subscribe' => 1));
			} else {
				#redirect('/FrontUser/show_qr_code');
			}
		}


		/*** 当前用户是否已确认地址begin ***/
		$user_address_id = intval(session('user_address_id'));
		#echo '<pre>';
		#print_r($user_address_id);
		#die;
		if(!$user_address_id)
		{
			$member_obj = new MemberModel($user_id);
			$member_info = $member_obj->getMemberInfo('member_id = ' . $user_id, 'default_user_address_id');
			$user_address_id = $member_info['default_user_address_id'];
			session('user_address_id', $user_address_id);
		}

		#var_dump($user_id);
		#var_dump($user_address_id);
		#die;
		/*if(!$user_address_id)
		{
			//没有设置过地址
			$state = intval($this->_get('addr_state'));
			if (ACTION_NAME != 'wxpay_response' && ACTION_NAME != 'alipay_response_post' && ACTION_NAME != 'alipay_response' && !(ACTION_NAME == 'add_address' && !$state))
			{
				if ($state)
				{
					$url = '/FrontAddress/add_address';
					$this->alert('您还没有设置地址，请先添加一个地址', $url);
				}
				else
				{
					if (ACTION_NAME != 'add_address')
					{
						$url = '/FrontAddress/add_address/addr_state/1';
						redirect($url);
					}
				}
			}
		}*/
		$this->assign('cur_user_address_id', $user_address_id);
		/*** 当前用户是否已确认地址end ***/

		/*if ($user_info['role_type'] != 3 && ACTION_NAME != 'item_list')
		{
			//非会员只能浏览商品列表页
			#$url = '/FrontUser/bind_mobile/state/1';
			#redirect($url);
			die('请用会员账号登录');
		}*/

		/*** 用户选择默认地址begin ***/
		$user_address_obj = new UserAddressModel();
		$deflt_addr_info = $user_address_obj->getDefaultAddress($user_id);
		$default_addr_areastr = AreaModel::getAreaString($deflt_addr_info['province_id'], $deflt_addr_info['city_id'], $deflt_addr_info['area_id']);
		$default_addr_string = $default_addr_areastr . $deflt_addr_info['address'];
		$this->assign('default_addr_string', $default_addr_string);	
		if (!$deflt_addr_info)
		{
			//未设置默认地址
			$this->assign('has_deflt_addr', 'false');
			#$redirect = U('FrontAddress/address_list');
			#$this->alert('您未设置默认收货地址，前去设置');
		}
		/*** 用户选择默认地址end ***/
	
		//获取分享代码
		Vendor('jssdk');
		$jssdk = new JSSDK(C("APPID"), C("APPSECRET"));
		$signPackage = $jssdk->getSignPackage(); 
		// dump($signPackage);die;
		$this->signPackage = $signPackage;
		$this->assign('signPackage', $signPackage);

		//用户头像
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('headimgurl');
		$item_info['pic'] = $user_info['headimgurl'];
		$pic = $pic ?  'http://' . $_SERVER['HTTP_HOST'] . $pic : $item_info['pic'];
		$share_info['pic'] = $pic;
		$this->assign('share_info', $share_info);
		$wx_share_link = get_url() . '?rec_user_id=' . $user_id;
		$this->assign('wx_share_link', $wx_share_link);
		log_file('user_id = ' . $user_id . ', action_name = ' . ACTION_NAME . ', share_info = ' . json_encode($share_info), 'share');
		#log_file(json_encode($signPackage), 'share');

	log_file('wx_share_link = ' . $wx_share_link, 'detail');
	log_file(json_encode($signPackage), 'detail');

		//传值到前台
		$user_id = intval(session('user_id'));
		$this->assign('user_id', $user_id ? $user_id : 0);
		$this->assign('client_ip', get_client_ip());	
	}

	/**
	 * 根据COOKIE获取用户信息，判断是否会员，若是，写session后退出；否则为之注册
	 * @author 姜伟
	 * @param void
	 * @return user_id
	 * @todo 
	 */
	function get_user_info()
	{
		//要过滤的user_agent
		$agent_arr = array(
			'Baiduspider',	//百度
			'Googlebot',	//谷歌
			'iaskspider',	//新浪
			'Sogou',	//搜狗
			'YodaoBot',	//网易有道
			'msnbot',	//微软msn
			'bot',	//所有
			'Bot',	//所有
			'spider',//所有
			'Spider',//所有
			'AppleWebKit',//所有
			'Alibaba.Security.Heimdall',
			'Gecko/20100101',
		);
		foreach ($agent_arr AS $k => $v)
		{
			if (strpos($_SERVER['HTTP_USER_AGENT'], $v))
			{
				log_file('爬虫' . $v, 'spider');
				return;
			}
		}
		$user_obj = new UserModel();
		$user_info = $user_obj->getUserInfo('user_id', 'role_type = 3 AND user_cookie = "' . $GLOBALS['user_cookie'] . '"');
		if ($user_info)
		{
			//用户存在，设置session
			session('user_id', $user_info['user_id']);
		}
		else
		{
			//用户不存在，为之注册
			$arr = array(
				'user_cookie'           => $GLOBALS['user_cookie'],
				'role_type'             => 3,
				'is_enable'             => 1,
				'reg_time'              => time(),
			);
			$user_id = $user_obj->addUser($arr);
			$member_obj = new MemberModel();
			$arr = array(
				'member_id'	=> $user_id
			);
			$member_obj->addMember($arr);
log_file('user_agent = ' . $_SERVER['HTTP_USER_AGENT'], 'user');
			session('user_id', $user_id);
		}
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
		$appid = C('APPID');
		$secret = C('APPSECRET');

		if (!isset($_GET['code']))
		{
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
log_file($redirect_uri);
			//获取授权码的接口地址
			$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $appid . '&redirect_uri=' . urlencode($redirect_uri) .'&response_type=code&scope=snsapi_base&state=1#wechat_redirect';
			$user_model = M('users');
			$user_info = $user_model->field('user_id, role_type, openid, nickname, sex, headimgurl, access_token, refresh_token, token_expires_time')->where('user_cookie = "' . $GLOBALS['user_cookie']. '"')->find();
			if (!$user_info)
			{
				redirect($url);
			}

			//访问令牌过期
			if ($user_info['token_expires_time'] < time())
			{
				//刷新令牌存在，用之交换访问令牌
				if ($user_info['refresh_token'])
				{
					$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $user_info['refresh_token'];
					Vendor('Wxin.WeiXin');
					$wx_obj = new WeiXinUser($appid, $secret);
					$wx_obj->getAccessToken($url);
				}
				else
				{
					redirect($url);
				}
			}
			else
			{
				session('user_id', $user_info['user_id']);
			}
		}
		else
		{
			$rec_user_id = $_GET['rec_user_id'];
			$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $appid . "&secret=" . $secret . "&code=" . $_GET['code'] .  "&grant_type=authorization_code";
			Vendor('Wxin.WeiXin');
			$wx_obj = new WeiXinUser($appid, $secret, $this->parentId);
			$wx_obj->getAccessToken($url, $rec_user_id);
		}
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
		$success = $alipay_obj->pay_response();
		if ($success)
		{
			echo 'success';
		}
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
		#log_file('wxpay_response: ' . json_encode($_REQUEST));

		$wxpay_obj = new WXPayModel();
		$success = $wxpay_obj->pay_response();
		if ($success)
		{
			echo 'success';
		}
	}

	/**
	 * 微信APP支付付款成功后的异步回调处理
	 * @author zt
	 * @param void
	 * @return void
	 * @todo 调用微信支付模型验证来源可靠性后，获取订单号，调用订单模型的payOrder方法设置订单状态为已付款
	 */
	public function wxpay_app_response()
	{
		$wxpay_obj = new WXPayModel();
		//$wxpay_obj->is_app = true;
		$success = $wxpay_obj->pay_response();
		if ($success)
		{
			echo 'success';
		}
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

	/**
	 * 图片上传处理
	 * @author 姜伟
	 * @return string 返回JSON字符串
	 * @todo 调用upImageHandler函数处理上传图片
	 *
	 */
	public function uploadHandler()
	{
		$user_id = intval(session('user_id'));
		upImageHandler($_FILES['upfile'], '/user/' . $user_id);
	}

	/**
	 * 根据经纬度获取最近的建筑物信息
	 * @author 姜伟
	 * @return string 返回JSON字符串
	 * @todo 根据经纬度获取最近的建筑物信息
	 *
	 */
	public function getBuildingInfo()
	{
		$lon = $this->_request('lon');
		$lat = $this->_request('lat');
		$user_id = intval(session('user_id'));

		if ($lat && $lon && $user_id)
		{
			$building_obj = new BuildingModel();
			$building_info = $building_obj->getNearbyBuildingInfo($lon, $lat);
			unset($building_info['distance']);
			session('building_name',$building_info['building_name']);
			session('lon', $lon);
			session('lat', $lat);
			session('area_id', $building_info['area_id']);
log_file("\n" . 'lon = ' . $lon . ', lat  = ' . $lat, 'building');
send_email('获取建筑物, cur_lon = ' . $lon . ', cur_lat = ' . $lat . ', user_id = ' . $user_id, '', '', false);
log_file('building_info = ' . json_encode($building_info) . ', sql  = ' . $building_obj->getLastSql(), 'building');
log_file('building_name = ' . $building_info['building_name'], 'building');
$user_obj = new UserModel($user_id);
$user_info = $user_obj->getUserInfo('nickname, mobile, realname');
log_file('user_id = ' . $user_id . ', nickname = ' . $user_info['nickname'] . ', mobile = ' . $user_info['mobile'], 'building');
#send_email(json_encode($building_info), $building_info['area_id'] . ', ' . $building_obj->getLastSql(), '', true);
			$this->json_exit($building_info);
		}

		$this->json_exit('failure');
	}

	/**
	 * 根据经纬度获取
	 * @author 姜伟
	 * @return string 返回JSON字符串
	 * @todo 调用upImageHandler函数处理上传图片
	 *
	 */
	public function getBuildingList()
	{
		$lon = $this->_request('lon');
		$lat = $this->_request('lat');
		$user_id = intval(session('user_id'));

		if ($lat && $lon && $user_id)
		{
			$building_obj = new BuildingModel();
			$building_list = $building_obj->getNearbyBuildingList($lon, $lat);
			foreach ($building_list AS $k => $v)
			{
				unset($building_list[$k]['distance']);
			}
			$this->json_exit($building_list);
		}

		$this->json_exit('failure');
	}

	/**
	 * 设置红包已分享
	 * @author 姜伟
	 * @return string 返回JSON字符串
	 * @todo 设置红包已分享
	 */
	public function share_freight_coupon()
	{
		$freight_activity_id = $this->_request('freight_activity_id');
		$user_id = intval(session('user_id'));

		if ($freight_activity_id && $user_id)
		{
			$freight_activity_obj = new FreightActivityModel($freight_activity_id);
			$where = 'shared = 0 AND user_id = ' . $user_id . ' AND deadline > ' . time() . ' AND freight_activity_id = ' . $freight_activity_id;
			if ($num = $freight_activity_obj->getFreightActivityNum($where) > 0)
			{
				$arr = array(
						'shared'	=> 1
					    );
				$freight_activity_obj->editFreightActivity($arr);
				$this->json_exit('success');
			}
		}

		$this->json_exit('failure');
	}
}

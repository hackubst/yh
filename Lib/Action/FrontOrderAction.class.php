<?php 
class FrontOrderAction extends UcpAction
{
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_page = C('ITEM_NUM_PER_PAGE');
		$this->order_num_per_page = C('ITEM_NUM_PER_PAGE');
	}

	//提交订单，取订单表数据，订单商家及商家商品数据，判断订单是否多商家，是否需要拆分，若需要拆分，拆分成3类，并一一计算3种拆分方式下的镖金及总价，用户选择并提交后，走付款流程。
	public function order_submit()
	{
		$user_id = cur_user_id();
		define('MAX', 10000000);
		vendor('wxpay.WxPayPubHelper');
		//获取参数
		$address_obj = new Address();
		$parameters = $address_obj->getParameters();
		$this->assign('parameters', $parameters);

		//商品列表初始化
		$item_list = array();

		//商品详情模板2--立即购买
		$now_buy = I('now_buy');
		//拼团参数
		$group_buy_id = intval($this->_request('group_buy_id'));
		if ($group_buy_id)
		{
			$redirect = U('/FrontGroupBuy/get_group_buy_list');
			//如果是拼团，根据group_buy_id取商品数据
			$group_buy_obj = new GroupBuyModel();
			$group_buy_info = $group_buy_obj->getGroupBuyInfo('isuse = 1 AND group_buy_id = ' . $group_buy_id, '');
			if (!$group_buy_info)
			{
				$this->alert('对不起，该活动不存在', $redirect);
			}

			//有效期
			if ($group_buy_info['start_time'] > time())
			{
				$this->alert('对不起，活动未开始', $redirect);
			}

			//有效期
			if ($group_buy_info['end_time'] <= time())
			{
				$this->alert('对不起，活动已结束', $redirect);
			}

			//是否已拼团成功
			if ($group_buy_info['status'] == 2)
			{
				$this->alert('对不起，活动已结束', $redirect);
			}

			//是否已参团
			$group_buy_user_obj = new GroupBuyUserModel();
			$group_buy_user_num = $group_buy_user_obj->getGroupBuyUserNum('group_buy_id = ' . $group_buy_id . ' AND user_id = ' . $user_id);
			if ($group_buy_user_num)
			{
				$this->alert('对不起，您已参团，无法再次参团', $redirect);
			}

			//根据商品ID获取商品信息
			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemInfo('item_id = ' . $group_buy_info['item_id'], 'item_id, mall_price, unit, item_name, base_pic');
			$item_info['user_id'] = $user_id;
			$item_info['mall_price'] = $group_buy_info['group_price'];
			$item_info['real_price'] = $item_info['mall_price'];
			$item_info['total_price'] = $item_info['mall_price'];
			$item_info['number'] = 1;
			require_cache('Common/func_item.php');
			$small_pic = small_img($item_info['base_pic']);
			$item_info['small_pic'] = $small_pic;
			unset($item_info['base_pic']);
			$item_list = array($item_info);
		}elseif($now_buy == 1){//商品详情页立即购买
			//接收post过来的数据
			$item_id = I('item_id');
			$number = I('number');
			$item_id = is_numeric($item_id) ? $item_id : intval(url_jiemi($item_id));
			if (!$item_id)
			{
				$this->alert('对不起，非法访问', U('/FrontMall/mall_home'));
			}

			//获取商品信息
			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemInfo('item_id = ' . intval($item_id), 'item_name, base_pic, mall_price, unit');
			if (!$item_info)
			{
				$this->alert('对不起，非法访问', U('/FrontMall/mall_home'));
			}

	        require_cache('Common/func_item.php');
			$small_pic = small_img($item_info['base_pic']);
			$item_sku_price_id = 0;	//先置为0
			$property = I('property');	//先置为空字符串
			$property = rtrim($property, ',');
			$pro_arr = explode(',', $property);
			//商品是否有规格
			if($property){
				$item_sku_obj = new ItemSkuModel();
				$sku = $item_sku_obj->getSkuPriceInfo('item_id ='.$item_id. ' and property_value1 ='. $pro_arr[0] .' and property_value2 ='. $pro_arr[1]);
				$item_sku_price_id = $sku[0]['item_sku_price_id'];
			}
			$added_num = '';
			$number_str = '';
			$arr = array(
				'user_id'			=> $user_id,
				'item_id'			=> $item_id,
				'mall_price'		=> $item_info['mall_price'],
				'real_price'		=> $item_info['mall_price'],
				'total_price'		=> $item_info['mall_price'] * $number,
				'number'			=> $number,
				'unit'				=> $item_info['unit'],
				'addtime'			=> time(),
				'item_name'			=> $item_info['item_name'],
				'small_pic'			=> $small_pic,
				'item_sku_price_id' => $item_sku_price_id,
				'property'			=> $property,
			);
			$item_list[0] = $arr;
		}else
		{
			//商品数量列表
			$number_list = $this->_request('number_list');
			$shopping_cart_id_list = $this->_request('shopping_cart_id_list');
			#log_file('number_list = ' . $number_list . ', shopping_cart_id_list = ' . $shopping_cart_id_list, 'order_submit');
			$number_str = $number_list;
			$number_list	= explode(',', $number_list);
			$shopping_cart_id_str = $shopping_cart_id_list;
			$shopping_cart_id_list	= explode(',', $shopping_cart_id_list);
			$count1 = count($number_list);
			$count2 = count($shopping_cart_id_list);

			//若数组长度不等，报错退出
			if (!$count1 || ($count1 != $count2))
			{
				$this->alert('对不起，非法访问', U('/FrontMall/mall_home'));
			}

			//获取商品信息列表，订单信息列表
			$cart_obj = new ShoppingCartModel();
			$cart_list = $cart_obj->getShoppingCartList('', ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')', 'merchant_id DESC, addtime DESC');
			if (!$cart_list)
			{
				$this->alert('对不起，请不要重复下单！', '/');
			}
			$item_obj = new ItemModel();
			foreach ($cart_list AS $k => $v)
			{
				if ($v['shopping_cart_id'] != $shopping_cart_id_list[$k])
				{
					$this->alert('对不起，非法访问', U('/FrontMall/mall_home'));
				}

				//判断库存是否足够
				$stock_enough = $item_obj->checkStockEnough($v['item_id'], $v['item_sku_price_id'], $number_list[$k]);
				if (!$stock_enough)
				{
					$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
					$item_name = $item_info ? $item_info['item_name'] : '';
					$this->alert('对不起，商品“' . $item_name . '”库存不足，请重新选择要购买的商品', U('/FrontCart/cart'));
				}

				//检验是否限购
			/*if ($item_obj->checkPurchaseLimit($v['item_id'], $number_list[$k]))
			{
				$this->alert('对不起，商品“' . $item_name . '”购买数量达到上限，请重新选择要购买的商品', U('/FrontCart/cart'));
			}*/

				$cart_list[$k]['number'] = $number_list[$k];
				if (!$number_list[$k])
				{
					unset($cart_list[$k]);
				}
				unset($cart_list[$k]['shopping_cart_id']);
			}
			$item_list = $cart_list;
			#log_file('cart_list = ' . json_encode($cart_list), 'order_submit');
		}
			#dump($item_list);
			#die;
		
		//获取默认地址
		$user_id = intval(session('user_id'));
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getDefaultAddress($user_id);
		if ($user_address_info)
		{
			$user_address_info['area_string'] = AreaModel::getAreaString($user_address_info['province_id'], $user_address_info['city_id'], $user_address_info['area_id']);
		}
		$this->assign('user_address_info', $user_address_info);

		//门店列表
		$node_list = D('Dept')->where('isuse = 1')->order('serial')->select();
		$this->assign('node_list', $node_list);

		//优惠信息
		$promo_obj = new PromoBaseModel();
		$promo_info = $promo_obj->getCoupon($item_list, 0, false);
		//获取订单信息
		$order_info = OrderModel::getOrderInfoByItemList($item_list);
		#echo "<pre>";
		#print_r($order_info);
		$order_info = array_merge($order_info, $promo_info);
		$order_info['group_buy_id'] = $group_buy_id;
		$order_info['is_group_buy'] = $group_buy_id ? 1 : 0;
		#$order_info['address_info'] = $user_address_info;
		#print_r($order_info);
		#die;

		//写临时订单表
		$order_temp_obj = new OrderTempModel();
		$order_temp_id = $order_temp_obj->addOrder($order_info);

		//赋值
		$this->assign('order_temp_id', $order_temp_id);
		$this->assign('order_info', $order_info);
		#echo "<pre>";
		#print_r($order_temp_id);
		#print_r($order_info);
		#print_r($item_list);
		#die;

		//支付方式列表
		$payway_obj = new PaywayModel();
        $payway_list = $payway_obj->getPaywayList('', ' AND (pay_tag = "mobile_wxpay" OR pay_tag = "wallet")');
		//$payway_list = $payway_obj->getPaywayList();
		#echo $payway_obj->getLastSql();
		#die;
		$this->assign('payway_list', $payway_list);
		//获取用户余额,支付密码,并密码验证
		$pay_password = $this->_post('pay_password');
		$total_pay_amount = $this->_post('total_pay_amount');
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money, pay_password');
        //$user_info['member_card_id'] = $user_obj->getMemberCardID($user_id);
        //$this->assign('member_card_id', substr($user_info['member_card_id'], -4,4));
		$left_money = $user_info['left_money'];
		$user_pay_password = $user_info['pay_password'];
		$this->assign('user_info', $user_info);
		$this->assign('pay_password', $user_info['pay_password'] ? 1 : 0);

		//收货地址列表
		$user_address_obj = new UserAddressModel();
		$user_address_list = $user_address_obj->getUserAddressList('user_address_id, province_id, city_id, area_id, mobile, address, realname, longitude, latitude', 'isuse = 1 AND user_id = ' . $user_id, 'use_time DESC, addtime DESC');
		foreach ($user_address_list AS $k => $v)
		{
			$user_address_list[$k]['area_string'] = AreaModel::getAreaString($v['province_id'], $v['city_id'], $v['area_id']);
		}
		$this->assign('user_address_list', $user_address_list);

		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();
		$this->assign('province_list', $province_list);

        //获取券列表
        $ticket_obj = new TicketModel();
        //$ticket_obj->syncTicketInfo($user_id);
        $ticket_list = $ticket_obj->getTicketList('','is_only_show =0 AND user_id='.$user_id.' AND status= 2 AND period_of_validity > '.NOW_TIME);
        $ticket_list = $ticket_obj->getDataList($ticket_list);
		$this->assign('ticket_list', $ticket_list);
		$coupon_desc = PromoBaseModel::getPromoDesc($promo_info);
		$this->assign('coupon_desc', $coupon_desc['coupon_desc']);

		$this->assign('head_title', '提交订单');
		$this->display();
	}

	//查询条件
	private function get_search_condition()
	{
		//商品名称
		$item_name = $this->_request('item_name');
		if ($item_name)
		{
			$order_item_obj = new OrderItemModel();
			$order_ids = $order_item_obj->getOrderIdByItemName($item_name);
			$order_ids = $order_ids ? $order_ids : 0;
			$where .= ' AND tp_order.order_id IN (' . $order_ids . ')';
		}

		//订单状态
		$order_status = $this->_request('order_status');
		$valid_status = OrderModel::getValidStatus();
		if (ctype_digit($order_status) && in_array($order_status, $valid_status))
		{
			$where .= ' AND order_status = ' . $order_status;
		}

		//$opt
		$opt = $this->_request('opt');
		if ($opt == 'pre_pay')
		{
			$where .= ' AND order_status = ' . OrderModel::PRE_PAY;
		}
		elseif ($opt == 'payed')
		{
			$where .= ' AND order_status = ' . OrderModel::PAYED;
		}
		elseif ($opt == 'delivered')
		{
			$where .= ' AND order_status = ' . OrderModel::DELIVERED;
		}

		$this->assign('opt', $opt);
		$this->assign('item_name', $item_name);
		$this->assign('order_status', $order_status);
		return $where;
	}

	//获取订单列表
	private function get_order_list($where, $head_title, $opt)
	{
		$where .= ' and is_integral = 0 and user_id ='. intval(session('user_id'));
		if(intval(session('user_id') != 0)){
			//获取订单列表
			$order_obj = new OrderModel();
			//总数
			$total = $order_obj->getOrderNum($where);
			$order_obj->setStart(0);
	        $order_obj->setLimit($this->order_num_per_page);
			$order_list = $order_obj->getOrderList('order_status, order_id, pay_amount, express_fee, addtime, dept_code', $where, 'addtime DESC');
	log_file($order_obj->getLastSql(), 'cc', true);
			$order_list = $order_obj->getListDataFront($order_list);
			foreach ($order_list AS $k => $v)
			{
				$order_list[$k]['order_status_name'] = OrderModel::convertOrderStatus($v['order_status']);
			}
			$this->assign('order_list', $order_list);
			$this->assign('total', $total);
			$this->assign('firstRow', $this->order_num_per_page);
			#echo "<pre>";print_r($order_list);die;$this->order_num_per_page

			//待支付订单数量
			$pre_pay_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::PRE_PAY. ' and is_integral = 0');
			$this->assign('pre_pay_order_num', $pre_pay_order_num);

			//待发货订单数量
			$pre_deliver_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::PAYED. ' and is_integral = 0');
			$this->assign('pre_deliver_order_num', $pre_deliver_order_num);

			//待确认订单数量
			$pre_confirm_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::DELIVERED. ' and is_integral = 0');
			$this->assign('pre_confirm_order_num', $pre_confirm_order_num);
		}
		//底部导航赋值
		$this->assign('nav', 'order');
		$this->assign('head_title', '全部订单');
		$this->assign('opt', $opt);
		$this->assign('pre_pay', OrderModel::PRE_PAY);
		$this->assign('delivered', OrderModel::DELIVERED);
		$this->assign('confirmed', OrderModel::CONFIRMED);
		$this->display(APP_PATH . 'Tpl/FrontOrder/get_order_list.html');
	}

	//全部订单页
	public function all_order()
	{
		$where = 'isuse = 1';
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'all');
	}

	//待支付订单
	public function pre_pay_order()
	{
		$where = 'isuse = 1  AND order_status = ' . OrderModel::PRE_PAY;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'pre_pay');
	}

	//待发货订单
	public function pre_deliver_order()
	{
		$where = 'isuse = 1  AND order_status = ' . OrderModel::PAYED;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'payed');
	}

	//待确认订单
	public function pre_confirm_order()
	{
		$where = 'isuse = 1  AND order_status = ' . OrderModel::DELIVERED;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'delivered');
	}
	
	//待评价订单
	public function pre_review_order()
	{
		$where = 'isuse = 1  AND order_status = ' . OrderModel::CONFIRMED;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'delivered');
	}
		
	//退款售后订单
	public function pre_refund_order()
	{
		$where = 'isuse = 1  AND order_status = ' . OrderModel::DELIVERED;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'delivered');
	}

	//退款中订单
	public function refunding_order()
	{
		$where = 'isuse = 1  AND order_status = ' . OrderModel::REFUNDING;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'refunding');
	}

	//订单详情
	public function order_detail()
	{
		//获取分享代码
		Vendor('jssdk');
		$jssdk = new JSSDK(C("APPID"), C("APPSECRET"));
		$signPackage = $jssdk->GetSignPackage(); 
		$this->signPackage = $signPackage;
		$this->assign('signPackage', $signPackage);

		//订单ID
		$user_id = intval(session('user_id'));
		$order_id = $this->_get('order_id');
		if ($order_id)
		{
			session('order_id', $order_id);
			redirect('http://' . $_SERVER['HTTP_HOST']	. '/FrontOrder/order_detail/');
		}
		$order_id = intval(session('order_id'));

		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, order_status, pay_amount, total_amount, express_fee, addtime, pay_time, confirm_time, user_address_id, payway,express_number,express_company_id', ' AND user_id = ' . $user_id);
		}
		catch (Exception $e)
		{
			$this->alert('对不起，订单不存在！', U('/FrontMall/mall_home'));
		}

		/*** 获取订单流程信息begin ***/
		//订单状态
		$order_info['order_status_name'] = OrderModel::convertOrderStatus($order_info['order_status']);

		//收货人信息
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id'] . ' AND user_id = ' . $user_id);

		$order_info['address'] = AreaModel::getAreaString($user_address_info['province_id'], $user_address_info['city_id'], $user_address_info['area_id']) . ' ' . $user_address_info['address'];

		$order_info['realname'] = $user_address_info['realname'];
		$order_info['mobile'] = $user_address_info['mobile'];

		//支付方式名称
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfo('payway_id = ' . $order_info['payway'], 'pay_name');
		$order_info['payway_name'] = $payway_info['pay_name'];

		//获取订单商品信息
		#$fields = 'order_item_id,tp_order_item.merchant_id, item_id, item_name, item_sn, number, unit, real_price, small_pic, shop_name';
		$order_info['order_item_list'] = $order_obj->getOrderItemList('item_id, item_name, number, small_pic, real_price, tp_order_item.order_item_id', 'tp_order_item.order_id = ' . $order_id);

		#echo $order_obj->getLastSql();
		#echo "<pre>";
		#print_r($order_info);
		#die;
		//物流信息
		if($order_info['order_status']!= OrderModel::PRE_PAY)
		{
			//todo 将物流公司显示出来
		//获取物流名称
			$shipping_company_obj = new ShippingCompanyModel();
			$shipping_company_info = $shipping_company_obj->getShippingCompanyInfoById($order_info['express_company_id']);

        $express_obj  = new ShippingCompanyModel();
        $express_info = $express_obj->getShippingCompanyInfoById($order_info['express_company_id']);
        $order_info['express_company_name'] =$express_info['shipping_company_name'];
		}

		//图片域名
		$this->assign('IMG_DOMAIN', C('IMG_DOMAIN'));
		$this->assign('order_info', $order_info);
		$this->assign('head_title', '订单详情');
		$this->display();
	}

	//订单付款
	public function pay_order()
	{
		/*vendor('wxpay.WxPayPubHelper');
		//获取参数
		$address_obj = new Address();
		$parameters = $address_obj->getParameters();
		$this->assign('parameters', $parameters);*/

		$order_id = $this->_get('order_id');
		
		if ($order_id)
		{
			session('order_id', $order_id);
			redirect('http://' . $_SERVER['HTTP_HOST']	. '/FrontOrder/pay_order/');
		}
		$order_id = intval(session('order_id'));
		$this->assign('order_id', $order_id);
		$recharge = intval(session('recharge'));
		session('recharge', null);

		if ($recharge)
		{
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
			$this->display(APP_PATH . '/Tpl/FrontUser/recharge.html');
		}
		else
		{
			//订单信息
			$order_obj = new OrderModel($order_id);
			$order_info = $order_obj->getOrderInfo('pay_amount, order_status, payway', ' AND user_id = ' . session('user_id'));
			$redirect = U('/FrontOrder/pre_pay_order');
			if (!$order_info)
			{
				//$this->alert('对不起，订单不存在', $redirect);
				$msg_arr = array('code'=>-1,'msg'=>'对不起，订单不存在', 'url' => $redirect);
				$this->assign('err_msg', $msg_arr);
			}

			if ($order_info['order_status'] != OrderModel::PRE_PAY)
			{
				//$this->alert('对不起，当前订单状态无法付款', $redirect);
				$msg_arr = array('msg'=>'对不起，当前订单状态无法付款', 'url' => $redirect);
				$this->assign('err_msg', $msg_arr);
			}
			$this->assign('order_info', $order_info);
			$pay_amount = $order_info['pay_amount'];

			//商品库存是否足够
			$order_obj2 = new OrderModel();
			$order_list = $order_obj2->getOrderList('order_id', 'order_id = ' . $order_id);
			$item_obj = new ItemModel();
			foreach ($order_list AS $order)
			{
				$order_item_obj = new OrderItemModel();
				$item_list = $order_item_obj->getOrderItemList('item_id, item_sku_price_id, item_name, number, real_price', 'tp_order_item.order_id = ' . $order['order_id']);
#echo $order_item_obj->getLastSql();
#echo "<pre>";
#print_r($item_list);
#die;
				foreach ($item_list AS $item)
				{
					$stock_enough = $item_obj->checkStockEnough($item['item_id'], $item['item_sku_price_id'], $item['number']);
					if (!$stock_enough)
					{
						//$this->alert('手慢了，商品“' . $item['item_name'] . '”已售罄，请重新下单', '/FrontCart/cart.html');
						$msg_arr = array('code'=>-1,'msg'=>'手慢了，商品“' . $item['item_name'] . '”已售罄，请重新下单', 'url' => '/FrontCart/cart.html');
						$this->assign('err_msg', $msg_arr);
					}

					//检验是否限购
					/*if ($item_obj->checkPurchaseLimit($item['item_id'], $item['number']))
					{
						$this->alert('对不起，商品“' . $item['item_name'] . '”购买数量达到上限，请重新选择要购买的商品', U('/FrontCart/cart'));
					}*/
				}
			}
            $user_id = intval(session('user_id'));
            $user_obj = new UserModel($user_id);
            $user_info = $user_obj->getUserInfo('left_money, pay_password');
            //$user_info['member_card_id'] = $user_obj->getMemberCardID($user_id);
            //$this->assign('member_card_id', substr($user_info['member_card_id'], -4,4));

			$act = $this->_post('act');
			$pay_password = $this->_post('pay_password');
            $card_code = $this->_post('card_code');
			if ($act == 'pay')
			{
				//支付方式
				$payway_id = intval($this->_post('payway_id'));
				if (!$payway_id)
				{
					//$this->alert('对不起，请选择支付方式');
					$msg_arr = array('code'=>-1,'msg'=>'对不起，请选择支付方式');
					$this->assign('err_msg', $msg_arr);
				}

				//获取支付方式的pay_tag
				$payway_obj = new PaywayModel();
				$payway_info = $payway_obj->getPaywayInfoById($payway_id);
				$pay_tag = $payway_info['pay_tag'];


				if ($pay_tag == 'wxpay')
				{
					$wxpay_obj = new WXPayModel();
					//JSAPI
					$jsApiParameters = $wxpay_obj->pay_code($order_id, $pay_amount);
					$this->assign('jsApiParameters', $jsApiParameters);
#echo $jsApiParameters;
#die;
				}
				elseif ($pay_tag == 'wallet')
				{
					//查看用户电子钱包余额，若不足，提示余额不足，并跳转到充值页面
					$left_money = $user_info['left_money'];
					$user_pay_password = $user_info['pay_password'];
					if ($left_money < $pay_amount)
					{
						//$this->alert('对不起，' . $GLOBALS['config_info']['SYSTEM_MONEY_NAME'] . '不足，请使用其他方式支付');
						$msg_arr = array('code'=>-1,'msg'=>'对不起，' . $GLOBALS['config_info']['SYSTEM_MONEY_NAME'] . '不足，请使用其他方式支付');
						$this->assign('err_msg', $msg_arr);
					}
					else
					{
						//支付密码验证支付			
						if(!$user_pay_password)
						{
							//$this->alert('未设置支付密码，前去设置', U('/FrontPassword/edit_pwd'));
							$msg_arr = array('msg'=>'未设置支付密码，前去设置', 'url'=> '/FrontPassword/edit_pwd');
							$this->assign('err_msg', $msg_arr);
						}
						else
						{
							if($user_pay_password == MD5($pay_password))
							{
								$order_obj->setOrderInfo(array('payway' => $payway_id));
								$order_obj->saveOrderInfo();
#send_email('支付方式payway = ' . $payway_id, 'sql = ' . $order_obj->getLastSql());
								//调用账户模型的addAccount方法支付该订单
								$account_obj = new AccountModel();
								$account_obj->addAccount(session('user_id'), 5, $pay_amount * -1, '支付订单', $order_id);
                                D('Order')->syncOrderInfo($order_id);
								//$this->alert('恭喜您，订单付款成功', U('/FrontOrder/pre_deliver_order'));
								$msg_arr = array('msg'=>'恭喜您，订单付款成功', 'url' => '/FrontOrder/pre_deliver_order');
								$this->assign('err_msg', $msg_arr);
							}
							else
							{
								//$this->alert('密码错误');
								$msg_arr = array('code'=>-1,'msg'=>'密码错误');
								$this->assign('err_msg', $msg_arr);
							}
						}
					}
				}
                // wsq added 会员卡支付
                else if ($pay_tag == "cardpay") {
                    //支付密码验证支付			
					$user_pay_password = $user_info['pay_password'];
                    if(!$user_pay_password)
                    {
                        $this->alert('未设置支付密码，前去设置', U('/FrontPassword/edit_pwd'));
                    }

                    if($pay_password && $user_info['pay_password'] == MD5($pay_password)) {
                        //$member_card_id = $user_info['member_card_id'];
                        $trade_no = D('MemberCard')->payByCard($user_id, $card_code, $order_id);
                        // todo: 后续逻辑待处理
                        // 接口处理成功
                        if ($trade_no) {
                            //调用订单模型的payOrder方法设置订单状态为已付款
                            //$order_obj->payOrder($trade_no, 'cardpay');
                            //调用账户模型的addAccount充值等额预存款金额
                            $account_obj = new AccountModel();
                            $account_obj->addAccount($user_id, 1, $pay_amount, '会员卡支付充值', 0, $trade_no);
                            //调用账户模型的addAccount方法支付该订单
                            $account_obj->addAccount(
                                $user_id, 5, -1 * $pay_amount, '支付订单', $order_id);

                            $order_obj->setOrderInfo(array('payway' => $payway_id));
                            $order_obj->saveOrderInfo();
                            // 同步订单到ERP系统；
                            D('Order')->syncOrderInfo($order_id);

                            $this->alert('恭喜您，订单付款成功', U('/FrontOrder/pre_deliver_order'));

                        } else {
                            // 接口返回失败
                            // todo:查询订单是否支付成功
                            $this->alert('系统繁忙，请稍后再试');
                        }

                    }
                    else {
                        $this->alert('密码错误');
                    }
                }

				$this->assign('amount', $pay_amount);
				$this->assign('pay_tag', $pay_tag);

#//调用支付宝模型的pay_code获取支付链接
#$alipay_obj = new AlipayModel();
#$link = $alipay_obj->pay_code(0, $pay_amount);
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
			$payway_list = $payway_obj->getPaywayList('', ' AND (pay_tag = "mobile_wxpay" OR pay_tag = "wallet")');
			//$payway_list = $payway_obj->getPaywayList();
			$this->assign('payway_list', $payway_list);

			//系统内部货币名称
			$this->assign('SYSTEM_MONEY_NAME', $GLOBALS['config_info']['SYSTEM_MONEY_NAME']);
			$this->assign('head_title', '订单付款');
			$this->display();
		}
	}

	//修改订单
	public function edit_order()
	{
		$this->assign('head_title', '修改订单');
		$this->display();
	}

	//修改订单状态
	function set_order_state()
	{
		$order_id = I('post.order_id');
		$order_status = I('post.order_status');
		$user_id = intval(session('user_id'));

		if (ctype_digit($order_id) && ctype_digit($order_status) && $user_id)
		{
			//查看是否当前用户的订单
			$order_obj = new OrderModel($order_id);
			$order_info = $order_obj->getOrderInfo('order_status', ' AND user_id = ' . $user_id);
			if (!$order_info)
			{
				exit('failure');
			}

			$success = $order_obj->setOrderState(intval($order_status));
			echo $success ? OrderModel::convertOrderStatus($order_status) : 'failure';
			exit;
		}

		exit('failure');
	}

	//生成订单
	function add_order()
	{
		$order_temp_id = $this->_post('order_temp_id');
		$payway_id = $this->_post('payway_id');
		$payway_name = $this->_post('payway_name');
		$pay_password = $this->_post('pay_password');
		$card_code = $this->_post('card_code');
		$user_remark = $this->_post('user_remark');
        $send_way = intval($this->_post('send_way'));
        $send_way = $send_way ? $send_way : OrderModel::MAIN_FACTORY;
        $need_deliever = intval($this->_post('need_deliever'));
		$user_id = intval(session('user_id'));

        $ticket_send = intval($this->_post('ticket_send'));
        $voucher_code = strip_tags($this->_post('voucher_code'));
        $user_vouchers_id = intval($this->_post('user_vouchers_id'));

		if ($order_temp_id && $payway_id && $user_id)
		{
			$order_temp_obj = new OrderTempModel($order_temp_id);
			$order_info = $order_temp_obj->getOrderInfo();
			if (!$order_info)
			{
				$this->json_exit('order not exists');
			}
			$order_info = json_decode($order_info['order_info'], true);

			#dump($user_vouchers_id);
			$order_info = PromoBaseModel::changeVouchers($user_vouchers_id, $order_info);
			#dump($order_info['coupon']);

			//支付总价
			$pay_amount += $order_info['total_amount'] + $order_info['express_fee'];

            //根据配送方式是否减去运费 (自提则减去）
            if (!$need_deliever) {
                $pay_amount = $pay_amount - $order_info['express_fee'];
            } 

			//获取支付方式的pay_tag
			$payway_obj = new PaywayModel();
			$payway_info = $payway_obj->getPaywayInfoById($payway_id);
			$pay_tag = $payway_info['pay_tag'];
            log_file('payway_id = ' . $payway_id . ', sql = ' .
                $payway_obj->getLastSql() . ', pay_tag = ' . $pay_tag, 'add_order', true);

            $user_obj = new UserModel($user_id);
            $user_info = $user_obj->getUserInfo('left_money, pay_password,left_integral, is_enable');
            //dump($user_info);
			if ($user_info['is_enable'] == 2)
			{
				//已禁用
				$return_arr = array(
					'code'	=> -4,
					'msg'	=> '抱歉，您的账户已被禁用，无法下单'
				);
				$this->json_exit($return_arr);
			}

			//如果是用券支付，则验证券，1.如果券金额大于商品金额，支付金额则为0,走钱包流程，记录过程  2. 如果券金额小于商品金额，把剩下的钱按要求支付，写两次记录
            //如果是2, 钱包，先支付券，再支付钱包， 卡，先支付卡，再支付券， 微信，先支付微信，再支付券
            trace($ticket_send . ' :' . $voucher_code, 'ticket_debug');
            if ($ticket_send && $voucher_code) {
                //验证是否已提交到订单中，且未支付, 则此卡不可用
                //$state_order = M('Order')->where('voucher_code = ' . $voucher_code)->count();
                //if ($state_order) {
                //    $this->json_exit(array('code' => 5, 'msg' => '此券已经使用'));
                //}
                
                $ticket_obj = new TicketModel();
                $ticket_info = $ticket_obj->getTicketInfo($voucher_code);
                $card_code = $ticket_obj->where('voucher_code = ' . $voucher_code)->getField('card_code');
                //验证
                $ticket_money = $ticket_obj->validTicketInfo($ticket_info, true);
                if (!$ticket_money || !$card_code)
                {
                    $this->json_exit(array('code' => 5, 'msg' => '券不合法'));
                }
                //$ticket_money = 999;

                if ($ticket_money >= $pay_amount) {
                    $ticket_pay_amount = $pay_amount;
                    $pay_amount = 0.00;   
                } else {
                    $ticket_pay_amount = $ticket_money;
                    $pay_amount = $pay_amount - $ticket_money;
                }
            }

			if ($pay_tag == 'wallet')
			{
				//验证支付密码
				if($pay_password)
                {
                    if ($user_info['left_money'] < $pay_amount)
                    {
                        //余额不足
                        $return_arr = array(
                            'code'			=> 1,
                        );
                        $this->json_exit($return_arr);
                    }

                    if($user_info['pay_password'] != MD5($pay_password))
                    {
                        //密码错误
                        $return_arr = array(
                            'code'			=> 2,
                        );
                        $this->json_exit($return_arr);
                    }
                }
				else
				{
					//未输入密码
					$return_arr = array(
						'code'			=> 3,
					);
					$this->json_exit($return_arr);
				}
            } 
            // wsq added 会员卡支付
            else if ($pay_tag == "cardpay") {
                if($pay_password) {
                    if ($user_info['pay_password'] != MD5($pay_password)) {
                        //密码错误
                        $return_arr = array(
                            'code'			=> 2,
                        );
                        $this->json_exit($return_arr);
                    }
				}
				else
				{
					//未输入密码
					$return_arr = array(
						'code'			=> 3,
					);
					$this->json_exit($return_arr);
				}
            }

			$item_obj = new ItemModel();
log_file(json_encode($order_info['item_list']), 'add_order', true);
			foreach ($order_info['item_list'] AS $item)
			{
				//检验库存是否足够
                $stock_enough = 
                    $item_obj->checkStockEnough($item['item_id'], $item['item_sku_price_id'], $item['number']);
				if (!$stock_enough)
				{
					$return_arr = array(
						'code'	=> 4,
						'msg'	=> '手慢了，商品“' . $item['item_name'] . '”已售罄，请重新下单'
					);
					$this->json_exit($return_arr);
				}

				//检验是否限购
					/*if ($item_obj->checkPurchaseLimit($item['item_id'], $item['number']))
					{
						$return_arr = array(
							'code'	=> 4,
							'msg'	=> '对不起，由于商品“' . $item['item_name'] . '”限购，无法下单，请重新下单'
						);
						$this->json_exit($return_arr);
					}*/
			}

			$order_obj = new OrderModel();
            $order_info['express_fee'] = $need_deliever? $order_info['express_fee'] : 0;
			
			//订单信息数组
			$arr = array(
				'pay_amount'		=> $order_info['total_amount'] + $order_info['express_fee'],
				'total_amount'		=> $order_info['total_amount'],
				'express_fee'		=> $order_info['express_fee'],
				'weight'			=> $order_info['total_weight'],
				'payway'			=> $payway_id,
				'user_address_id'	=> $order_info['user_address_id'],
				'user_remark'		=> $user_remark,
                'dept_code'			=> $send_way,
                'need_deliever'		=> $need_deliever,
                'voucher_code'		=> $voucher_code,
                'group_buy_id'		=> $order_info['group_buy_id'],
                'is_group_buy'		=> $order_info['is_group_buy'],
			);
log_file('arr = ' . json_encode($arr), 'add_order', true);
			$order_id = $order_obj->addOrder($arr, $order_info['item_list'], $order_info['coupon']);
            log_file('生成订单：order_temp_id = ' . $order_temp_id 
                . ', order_id = ' . $order_id . ', sql = ' . $order_obj->getLastSql(), 'add_order');

			if ($pay_tag == 'wxpay' || $pay_tag == 'mobile_wxpay')
			{
				$wxpay_obj = new WXPayModel();
				//JSAPI
				$is_app = I('is_app');
					if(!$is_app){
	                $jsApiParameters = $wxpay_obj->pay_code($order_id, $pay_amount);
	                #$jsApiParameters = substr($jsApiParameters, 1, count($jsApiParameters) - 1);
	                #$jsApiParameters = substr($jsApiParameters, 0, -1);
	                log_file($jsApiParameters, 'add_order', true);
	                log_file('jsApiParameters = ' . $jsApiParameters, 'add_order', true);
						$return_arr = array(
							'code'		=> 0,
							'jsApiParameters'	=> $jsApiParameters,
							'order_id' => $order_id
						);
					$this->json_exit($return_arr);
				}else{
					$return_arr = array(
							'order_id' => $order_id
						);
					$this->json_exit($return_arr);
				}
			}
			elseif ($pay_tag == 'wallet')
			{
				//查看用户电子钱包余额，若不足，提示余额不足，并跳转到充值页面
				$user_obj = new UserModel(session('user_id'));
				$left_money = $user_obj->getLeftMoney();
				if ($left_money < $pay_amount)
				{
					$return_arr = array(
						'code'		=> -1,
						'order_id'	=> $order_id,
					);
					$this->json_exit($return_arr);
				}
				else
				{
					$account_obj = new AccountModel();
                    $remark = '支付订单';
                    if ($ticket_pay_amount) {
                        //调用券支付
                        $state = D('Ticket')->ticketPay($card_code, $voucher_code, $ticket_pay_amount, $order_id, $user_id);
                        //支付失败跳出
                        if (!$state) {
                            $this->json_exit(array('code' => 6, 'msg' => '券支付失败'));
                        }
                        //写账户表先充值等额金额
                        //$account_obj->addAccount($user_id, 1, $ticket_pay_amount, '券支付充值', 0, $voucher_code);
                        $code = substr($voucher_code, -4);
                        $remark = '支付订单（券：**' . $code . '---抵扣金额：' . $ticket_pay_amount . '元)';
                    }
					//调用账户模型的addAccount方法支付该订单
                trace($pay_amount, 'ticket_debug_amomnt');

					//如果是团购则改变类型
					$change_type = $order_info['is_group_buy'] ? AccountModel::GROUP_BUY_COST : AccountModel::ORDER_COST;

					$account_obj->addAccount(session('user_id'), $change_type, $pay_amount * -1, $remark, $order_id, $voucher_code);

                    // 同步订单到ERP系统；
                    D('Order')->syncOrderInfo($order_id);

					$this->json_exit(array(
						'code'	=> 0,
						'msg'	=> 'success'
					));
					#$this->alert('恭喜您，订单付款成功', U('/FrontOrder/pre_deliver_order'));
				}
            } else if ($pay_tag == "cardpay") {
                //$member_card_id = $user_info['member_card_id'];
                $trade_no = D('MemberCard')->payByCard($user_id, $card_code, $order_id, $pay_amount);
                // todo: 后续逻辑待处理
                // 接口处理成功
                if ($trade_no) {
                    $remark = '支付订单';

                    //如果券支付存在，再支付券
                    if ($ticket_pay_amount) {
                        //调用券支付
                        $state = D('Ticket')->ticketPay($card_code, $voucher_code, $ticket_pay_amount, $order_id, $user_id);
                        //支付失败跳出
                        if (!$state) {
                            $this->json_exit(array('code' => 6));
                        }
                        //写账户表先充值等额金额
                        //$account_obj->addAccount($user_id, 1, $ticket_pay_amount, '券支付充值', 0, $voucher_code);
                        //调用账户模型的addAccount方法支付该订单
                        //$account_obj->addAccount($user_id, 5, -1 * $ticket_pay_amount, '支付订单', $order_id);
                        $code = substr($voucher_code, -4);
                        $remark = '支付订单（券：**' . $code . '---抵扣金额：' . $ticket_pay_amount . '元)';
                    }

                    //调用订单模型的payOrder方法设置订单状态为已付款
                    //$order_obj->payOrder($trade_no, 'wxpay');
                    //调用账户模型的addAccount充值等额预存款金额
                    $account_obj = new AccountModel();
                    $account_obj->addAccount($user_id, 1, $pay_amount, '会员卡支付充值', 0, $trade_no);
                    //调用账户模型的addAccount方法支付该订单
                    $account_obj->addAccount(
                    $user_id, 5, -1 * $pay_amount, $remark, $order_id, $voucher_code);

                    

                    // 同步订单到ERP系统；
                    D('Order')->syncOrderInfo($order_id);

                    $this->json_exit(array(
                        'code'	=> 0,
                        'msg'	=> 'success'
                    ));

                } else {
                    // 接口返回失败
                    // todo:查询订单是否支付成功
                    $this->json_exit(array(
                        'code'	=> -1,
                        'order_id'	=> $order_id,
                    ));
                }

            }
			#echo "<pre>";
			#var_dump($pay_amount);
			#print_r($order_info);
			#die;
		}

		$this->json_exit('failure');
	}

	//异步获取订单列表
	public function order_list()
	{
		$firstRow = I('post.firstRow');
		$user_id = intval(session('user_id'));
		$order_obj = new OrderModel();
		$where = 'user_id = ' . $user_id . ' AND isuse = 1';
		$where .= $this->get_search_condition();
		//订单总数
		$total = $order_obj->getOrderNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$order_obj->setStart($firstRow);
			$order_obj->setLimit($this->order_num_per_page);
			//获取订单列表
			$order_list = $order_obj->getOrderList('order_status, order_id, pay_amount, express_fee, addtime', $where, 'addtime DESC');
			$order_list = $order_obj->getListDataFront($order_list);
			echo json_encode($order_list);
			exit;
		}

		exit('failure');
	}

	//运费计算接口
	function cal_express_fee()
	{
		$city_id = $this->_post('city_id');
		$total_amount = $this->_post('total_amount');
		$total_weight = $this->_post('total_weight');
		$user_id = intval(session('user_id'));

		if (ctype_digit($city_id) && is_numeric($total_amount) && is_numeric($total_weight) && $user_id)
		{
			$city_obj = M('address_city');
			$city_info = $city_obj->where('city_id = ' . $city_id)->find();
			if (!$city_info)
			{
				exit('failure');
			}

			//调用物流模型ShippingCompanyModel的calculateShippingFee
			$default_express_company = $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY'];
			$shipping_company_obj = new ShippingCompanyModel();
			$express_fee = $shipping_company_obj->calculateShippingFee($default_express_company, $city_info['province_id'], $total_weight, $total_amount);

			$return_arr = array(
				'city'			=> $city_info['city_name'],
				'express_fee'	=> $express_fee,
			);

			echo $express_fee ? json_encode($return_arr) : 'failure';
			exit;
		}

		exit('failure');
	}

	//去付款，获取付款ID
	function merge_order()
	{
		$order_id = intval($this->_request('order_id'));
		$user_id = intval(session('user_id'));
		if ($user_id && $order_id)
		{
			//获取订单信息
			$order_obj = new OrderModel($order_id);
			try
			{
				$order_info = $order_obj->getOrderInfo('pay_amount, real_freight', ' AND user_id = ' . $user_id);
			}
			catch (Exception $e)
			{
				$this->json_exit('order not exists');
			}

			//生成订单付款
			$merge_pay_obj = new MergePayModel();
			$merge_pay_info = array(
				'user_id'		=> $user_id,
				'pay_amount'	=> $order_info['pay_amount'],
				'total_freight'	=> $order_info['real_freight'],
				'addtime'		=> time(),
			);
			$merge_pay_id = $merge_pay_obj->addMergePay($merge_pay_info);
			$order_obj->setOrderInfo(array('merge_pay_id' => $merge_pay_id));
			$order_obj->saveOrderInfo();

			$return_arr = array(
				'code'			=> 0,
				'merge_pay_id'	=> $merge_pay_id,
			);
			$this->json_exit($return_arr);
		}
	}

	//获取某订单的镖师坐标
	function get_location()
	{
		$order_id = intval($this->_request('order_id'));
		$user_id = intval(session('user_id'));
		if ($user_id && $order_id)
		{
			//获取订单信息
			$order_obj = new OrderModel($order_id);
			try
			{
				$order_info = $order_obj->getOrderInfo('foot_man_id', ' AND user_id = ' . $user_id);
			}
			catch (Exception $e)
			{
				$return_arr = array(
					'code'	=> -1,
					'msg'	=> 'order not exists',
				);
				$this->json_exit($return_arr);
			}

			//获取内存中该镖师的坐标
			$push_obj = new PushModel();
			$foot_man_info = $push_obj->getMemoryData('user_' . $order_info['foot_man_id']);
			$return_arr = array(
				'code'	=> 0,
				'lon'	=> $foot_man_info['longitude'],
				'lat'	=> $foot_man_info['latitude'],
			);
			$this->json_exit($return_arr);
		}
	}

	//获取某订单的推送人数及抢单状态
	function getRobInfo()
	{
		$order_id = intval($this->_request('order_id'));
		$user_id = intval(session('user_id'));
		if ($user_id && $order_id)
		{
			//获取订单信息，获取抢单状态
			$order_rob_obj = new OrderRobModel();
			$order_rob_info = $order_rob_obj->getOrderRobInfo('order_id = ' . $order_id, 'simulated_push_num, order_rob_id, state');
			$order_rob_num = intval($order_rob_info['simulated_push_num']);
			//获取随机的推送数量
			if ($order_rob_num < 300)
			{
				$order_rob_num = $order_rob_num + rand(0,3);
			}
			//将模拟数据写回数据库
			$arr = array(
				'simulated_push_num'	=> $order_rob_num
			);
			$order_rob_obj = new OrderRobModel($order_rob_info['order_rob_id']);
			$order_rob_obj->editOrderRob($arr);

			#$order_rob_info = $order_rob_obj->getOrderRobInfo('order_id = ' . $order_id, 'state');
			$state = $order_rob_info ? $order_rob_info['state'] : 0;
			$return_arr = array(
				'code'			=> 0,
				'pushed_num'	=> $order_rob_num,
				'state'			=> $state,
			);
			log_file($order_rob_obj->getLastSql());

			//若已抢单，获取镖师信息
			if ($state)
			{
				//抢单镖师ID
				$order_obj = new OrderModel($order_id);
				try
				{
					$order_info = $order_obj->getOrderInfo('foot_man_id');
					$foot_man_obj = new FootManModel($order_info['foot_man_id']);
					//获取镖师信息：姓名，头像，好评率，电话
					$foot_man_info = $foot_man_obj->getFootManInfo('foot_man_id = ' . $order_info['foot_man_id'], 'realname, mobile, score_avg, head_photo');
					foreach ($foot_man_info AS $k => $v)
					{
						$return_arr[$k] = $v;
					}
				}
				catch (Exception $e)
				{
				}
			}
			log_file(arrayToString($return_arr));

			$this->json_exit($return_arr);
		}
	}

	//镖金加价
	function add_freight()
	{
		$payway_id = $this->_post('payway_id');
		$order_id = $this->_post('order_id');
		$pay_password = $this->_post('pay_password');
		$price = $this->_post('price');
		$user_id = intval(session('user_id'));

		if (ctype_digit($order_id) && $payway_id && is_numeric($price) && $user_id)
		{
			//获取订单信息，判断当前订单状态是否可以执行加价操作
			$order_obj = new OrderModel($order_id);
			try
			{
				$order_info = $order_obj->getOrderInfo('order_status', ' AND user_id = ' . $user_id);
			}
			catch (Exception $e)
			{
				$return_arr = array(
					'code'			=> 4,
				);
				$this->json_exit($return_arr);
			}
			
			if ($order_info['order_status'] != OrderModel::PAYED)
			{
				$return_arr = array(
					'code'			=> -2,
				);
				$this->json_exit($return_arr);
			}

			//支付总价
			$pay_amount = $price;

			//获取支付方式的pay_tag
			$payway_obj = new PaywayModel();
			$payway_info = $payway_obj->getPaywayInfoById($payway_id);
			$pay_tag = $payway_info['pay_tag'];

			if ($pay_tag == 'wallet')
			{
				//验证支付密码
				$user_obj = new UserModel($user_id);
				$user_info = $user_obj->getUserInfo('left_money, pay_password');
				if($pay_password)
				{
					if ($user_info['left_money'] < $pay_amount)
					{
						//余额不足
						$return_arr = array(
							'code'			=> 1,
						);
						$this->json_exit($return_arr);
					}

					if($user_info['pay_password'] != MD5($pay_password))
					{
						//密码错误
						$return_arr = array(
							'code'			=> 2,
						);
						$this->json_exit($return_arr);
					}
				}
				else
				{
					//未输入密码
					$return_arr = array(
						'code'			=> 3,
					);
					$this->json_exit($return_arr);
				}
            }

            $user_id = intval(session('user_id'));
            $user_obj = new UserModel($user_id);
			//成功添加的订单数量和预添加的一致，走支付流程
			if ($pay_tag == 'wxpay') {
				$wxpay_obj = new WXPayModel();
				//JSAPI，加价参数赋为true
                #$pay_amount = 0.01;
				$jsApiParameters = $wxpay_obj->pay_code($order_id, $pay_amount, 0, true);
                //send_email('加镖金' . $pay_amount, $jsApiParameters, '', true);
				$this->json_exit($jsApiParameters);
				//NATIVE
				#$nativeApiParameters = $wxpay_obj->pay_code(0, $coin_num, 1);
				#$this->assign('nativeApiParameters', $nativeApiParameters);
				#$this->assign('jsApiParameters', $jsApiParameters);
			} else if ($pay_tag == 'wallet') {
				//查看用户电子钱包余额，若不足，提示余额不足，并跳转到充值页面
				$left_money = $user_obj->getLeftMoney();
				if ($left_money < $pay_amount) {
					$return_arr = array(
						'code'		=> -1,
						'order_id'	=> $order_id,
					);
					$this->json_exit($return_arr);
				} else {
					//调用账户模型的addAccount方法支付该订单
					$account_obj = new AccountModel();
					$account_obj->user_id = $user_id;
					$account_obj->payway = $payway_id;
					$success = $account_obj->addAccount($user_id, 7, $pay_amount * -1, '订单加镖金', $order_id);
					$this->json_exit(array(
						'code'	=> $success,
						'msg'	=> 'success'
					));
				}
            }

			#echo "<pre>";
			#var_dump($pay_amount);
			#die;
		}

		$this->json_exit(array('code' => -1, 'msg' => '参数有误'));
	}
	
	function jsapi()
	{
		//获取分享代码
		Vendor('jssdk');
		$jssdk = new JSSDK(C("APPID"), C("APPSECRET"));
		$signPackage = $jssdk->GetSignPackage(); 
		$this->signPackage = $signPackage;
		$this->assign('signPackage', $signPackage);
		$this->display();
	}

	/**
	 * 获取条件获取优惠信息
	 * @author jw
	 * @param array $params 参数列表
	 * @return 成功返回$coupon_info数组，失败退出返回错误码
	 * @todo 获取条件获取优惠信息
	 * 参考：订单提交页
	 */
	function getCouponInfo()
	{
		$user_id = cur_user_id();
		$shopping_cart_id_list = $this->_request('cart_ids');
		$number_list = $this->_request('number_str');
		$item_list = array();
		$where = '';

		if ($shopping_cart_id_list && $number_list)
		{
			$number_str = $number_list;
			$number_list	= explode(',', $number_list);
			$shopping_cart_id_str = $shopping_cart_id_list;
			$shopping_cart_id_list	= explode(',', $shopping_cart_id_list);
			$count1 = count($number_list);
			$count2 = count($shopping_cart_id_list);
			//若数组长度不等，报错退出
			if (!$count1 || ($count1 != $count2))
			{
				ApiModel::returnResult(42045, null, '对不起，购物车和商品数量不一致');
			}
			$where =  ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')';
		}

		//获取商品信息列表，订单信息列表
		$cart_obj = new ShoppingCartModel();
		$item_list = $cart_obj->getShoppingCartList('', $where, 'merchant_id DESC, addtime DESC');

		if ($shopping_cart_id_list && $number_list)
		{
			foreach ($item_list AS $k => $v)
			{
				$item_list[$k]['number'] = $number_list[$k];
			}
		}

		//优惠信息
		$promo_obj = new PromoBaseModel();
		$promo_info = $promo_obj->getCoupon($item_list);
		#echo "<pre>";
		#print_r($promo_info);
		#die;

		$promo_info = PromoBaseModel::getPromoDesc($promo_info);
		#echo "<pre>";
		#print_r($promo_info);
		#die;

		$this->json_exit($promo_info);
	}

	//积分商城消费
	public function add_integral_order(){

		$integral_exchange = I('integral');
		$item_id = I('item_id');
		
		//$item_info = $item_obj->getItemInfo('item_id ='.$item_id);
		//根据商品ID获取商品信息
			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemInfo('item_id = ' . $item_id, 'item_id, mall_price, unit, item_name, base_pic, integral_exchange');
			$item_info['user_id'] = $user_id;
			$item_info['mall_price'] = $group_buy_info['group_price'];
			$item_info['real_price'] = $item_info['mall_price'];
			$item_info['total_price'] = $item_info['mall_price'];
			$item_info['number'] = 1;
			require_cache('Common/func_item.php');
			$small_pic = small_img($item_info['base_pic']);
			$item_info['small_pic'] = $small_pic;
			unset($item_info['base_pic']);

			


		$stock_enough = $item_obj->checkStockEnough($item_id);

		if($integral_exchange != $item_info['integral_exchange']){
			$this->json_exit(array('msg' =>'error'));
		}
		//库存不足
		if(!$stock_enough){
			$return_arr = array(
				'code'	=> 2,
				'msg'	=> '手慢了，商品“' . $item_info['item_name'] . '”已售罄，请重新下单'
			);
			$this->json_exit($return_arr);
		}
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_integral, left_money');
		$left_integral = $user_info['left_integral'];
		$left_money = $user_info['left_money'];
		$use_money = 0.00;
		$use_integral = $integral_exchange;
		if($left_integral < $item_info['integral_exchange']){
			
			$use_money = $item_info['integral_exchange'] - $left_integral;
			$use_integral = $left_integral;
			if($use_money > $user_info['left_money']){
				$return_arr = array(
					'code'	=> 3,
					'msg'	=> '积分与现金余额不足'
				);
				$this->json_exit($return_arr);
			}
		}
		
		//获取默认地址
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getDefaultAddress($user_id);

		if ($user_address_info)
		{
			$user_address_info['area_string'] = AreaModel::getAreaString($user_address_info['province_id'], $user_address_info['city_id'], $user_address_info['area_id']);
		}
		$item_list = array($item_info);

		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfoByTag('wallet');
		$data = array(
			'pay_amount' => $use_money,
			'total_amount' =>$use_money,
			'user_address_id' => $user_address_info['user_address_id'],
			'integral_amount' => $use_integral,
			'need_deliever' => 1,
			'is_integral' => 1,
			'addtime' => time(),
			'payway' => $payway_info['payway_id']
			);
		
		$order_obj = new OrderModel();
		if($order_id = $order_obj->addOrder($data)){

			$integral_exchange_record_item_obj = new IntegralExchangeRecordItemModel($order_id);
			$integral_exchange_record_item_obj->addItem($item_list);

			
			if($use_integral > 0){
				$integral_obj = new IntegralModel();
				$integral_obj->addIntegral(session('user_id'), IntegralModel::INTEGRAL_MONEY_COST, $use_integral * -1, '积分商城消费', $order_id);
			}
			if($use_money > 0){
				$account_obj = new AccountModel();
				$account_obj->addAccount(session('user_id'), AccountModel::INTEGRAL_MONEY_COST, $use_money * -1, '积分商城消费', $order_id);
				/*$integral_obj = new IntegralModel();
				$integral_obj->addIntegral(session('user_id'), IntegralModel::INTEGRAL_RETURN, $use_money , '积分返还', $order_id);*/
			}
			//减少库存
			$item_obj = new ItemModel();
			$item_obj->deductItemStock($item_id);
			
			$order_obj2 = new OrderModel($order_id);
			$order_obj2->setOrderState(OrderModel::PAYED);
			$data = array('pay_time' => time());
			$order_obj2->setOrderInfo($data);
			$order_obj2->saveOrderInfo();
			
			$return_arr = array(
				'code'	=> 1,
				'msg'	=> '兑换成功'
			);
			$this->json_exit($return_arr);
		}
	}

	public function taihekeji()
    {
        $this->assign('head_title','钛核科技');
        $this->display();
    }
	public function pay_taihekeji()
    {
        $wxpay_obj = new WXPayModel();
        //JSAPI
        $jsApiParameters = $wxpay_obj->pay_code(time(), 78);
        $this->assign('jsApiParameters', $jsApiParameters);
    }
}

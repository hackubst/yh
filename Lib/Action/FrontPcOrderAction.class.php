<?php 
class FrontPcOrderAction extends FrontPcAction
{
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_page = $GLOBALS['config_info']['ITEM_NUM_PER_PAGE'];
		$this->order_num_per_page = $GLOBALS['config_info']['ITEM_NUM_PER_PAGE'];
	}

	//提交订单
	public function order_submit()
	{
		#vendor('wxpay.WxPayPubHelper');
		//获取参数
		/*$address_obj = new Address();
		$parameters = $address_obj->getParameters();
		$this->assign('parameters', $parameters);*/

		//商品数量列表
		$number_list = $this->_request('number_list');
		$shopping_cart_id_list = $this->_request('shopping_cart_id_list');
		$number_str = $number_list;
		$number_list	= explode(',', $number_list);
		$shopping_cart_id_str = $shopping_cart_id_list;
		$shopping_cart_id_list	= explode(',', $shopping_cart_id_list);
		$count1 = count($number_list);
		$count2 = count($shopping_cart_id_list);
		//若数组长度不等，报错退出
		if (!$count1 || ($count1 != $count2))
		{
			$this->alert('对不起，非法访问', U('/'));
		}

		//获取商品信息列表，订单信息列表
		$cart_obj = new ShoppingCartModel();
		$cart_list = $cart_obj->getShoppingCartList('', ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')', 'addtime DESC');
		#echo "<pre>";print_r($cart_list);die;
		$item_obj = new ItemModel();
		//商品优惠前总价（按批发价算）
		$pre_total_amount = 0.00;
		//商品总价
		$total_amount = 0.00;
		//实付商品总价
		$pay_amount = 0.00;
		//商品优惠前总价（按市场价算）
		$total_wholesale_price = 0.00;
		//商品总成本价
		$cost_price = 0.00;
		//商品总优惠价
		$discount_amount = 0.00;
		//商品总重量
		$total_weight = 0;
		//总积分抵扣额
		$total_integral = 0.00;
		#echo "<pre>";print_r($cart_list);die;
		foreach ($cart_list AS $k => $v)
		{
			if ($v['shopping_cart_id'] != $shopping_cart_id_list[$k])
			{
				$this->alert('对不起，非法访问', U('/'));
			}

			//分别判断商品库存和套餐商品库存是否足够
			if($v['item_package_id'] != 0){
				$package_stock_enough = $item_obj->checkPackageStockEnough($v['item_package_id'], $number_list[$k]);
				if (!$package_stock_enough)
				{
					$package_item_info = D('ItemPackage')->getItemPackageInfo('item_package_id = ' . $v['item_package_id'], 'item_name');
					$package_item_name = $package_item_info ? $package_item_info['item_name'] : '';
					$this->alert('对不起，商品“' . $package_item_name . '”库存不足，请重新选择要购买商品',  U('FrontCart/cart'));
				}
			}else{
				$stock_enough = $item_obj->checkStockEnough($v['item_id'], $v['item_sku_price_id'], $number_list[$k]);
				if (!$stock_enough)
				{
					$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
					$item_name = $item_info ? $item_info['item_name'] : '';
					$this->alert('对不起，商品“' . $item_name . '”库存不足，请重新选择要购买商品', U('FrontCart/cart'));
				}
			}
			

			$cart_list[$k]['number'] = $number_list[$k];
			$total_amount += $v['real_price'] * $number_list[$k];
			$total_weight += $v['weight'] * $number_list[$k];
			$total_integral += $v['integral_num'];
			if (!$number_list[$k])
			{
				unset($cart_list[$k]);
			}
			unset($cart_list[$k]['shopping_cart_id']);
		}
		
		//获取用户信息
		$user_id = intval(session('user_id'));		
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money,left_integral,user_rank_id,user_address_id');
		//可抵扣总积分
		$total_integral = $user_info['left_integral'] >= $total_integral ? $total_integral : $user_info['left_integral'];
		//应付金额
		#$pay_amount = floatval($total_amount - $total_integral);

		//订单折扣计算
		$user_rank_id = $user_info['user_rank_id'];
		$pay_amount_bak = round($total_amount, 2);
		$pay_amount = PromotionBaseModel::calculateOrderCoupon($total_amount, $user_rank_id);
		#echo "<pre>"; print_r($pay_amount);print_r($total_amount);print_r($total_integral);die;
		
		$discount_amount = round($pay_amount_bak - $pay_amount, 2);
		#$total_amount -= round($discount_amount, 2);
		$pay_amount -= round($total_integral, 2);
		//四舍五入保留两位小数
		$total_amount = sprintf('%.2f', $total_amount);
		#echo $pay_amount;echo $total_amount;die;

		#$count_page_info['order_info']['total_amount'] = $total_amount;
		#$count_page_info['order_info']['pay_amount'] = $pay_amount;
		//计算单品优惠
		$item_coupon_info = PromotionBaseModel::getAllCoupon($total_amount, $cart_list, $user_rank_id);
		foreach ($item_coupon_info['item_list'] as $key => $value) {
			// if($item_coupon_info[$key]['discount_ary']['promo_discount'])
				$total_item_coupon += $item_coupon_info['item_list'][$key]['discount_ary']['promo_discount']['amount'];
		}
		#echo "<pre>";print_r($total_item_coupon);print_r($item_coupon_info);die;
		$this->assign('item_coupon_info', $item_coupon_info);
		$this->assign('total_item_coupon', $total_item_coupon);

		$pay_amount -= round($total_item_coupon, 2);
		$pay_amount = sprintf('%.2f', $pay_amount);
		$discount_amount += round($total_item_coupon, 2);


		//获取商品信息列表
		$cart_obj = new ShoppingCartModel();
		$item_list = $cart_obj->getShoppingCartList('', ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')');
		foreach ($item_list AS $k => $v)
		{
			$item_list[$k]['number'] = $number_list[$k];
		}
		
		$this->assign('item_list', $item_list);

		//支付方式列表
		$payway_obj = new PaywayModel();
		$payway_list = $payway_obj->getPaywayList();
		$this->assign('payway_list', $payway_list);

		//获取用户余额
		
		$this->assign('user_info', $user_info);
		$this->assign('left_money', $user_info['left_money']);
		$this->assign('all_total_integral', $user_info['left_integral']);


		//收货地址列表和默认地址
		$user_address_obj = new UserAddressModel();
		$user_address_list = $user_address_obj->getUserAddressList('user_address_id, province_id, city_id, area_id, mobile, address, realname', 'user_id = ' . $user_id, 'use_time DESC, addtime DESC');

		foreach ($user_address_list AS $k => $v)
		{
			$user_address_list[$k]['area_string'] = AreaModel::getAreaString($v['province_id'], $v['city_id'], $v['area_id']) . $v['address'];
		}
		$this->assign('user_address_list', $user_address_list);
		$this->assign('default_user_address', $user_info['user_address_id']);
		#echo "<pre>";print_r($user_info['user_address_id']);die;

		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();
		$this->assign('province_list', $province_list);

		//当前用户的优惠券列表
		$freight_coupon_obj = new CouponModel();
		$freight_coupon_list = $freight_coupon_obj->getCouponList('num, coupon_id, deadline', 'state = 0 AND user_id = ' . $user_id . ' AND deadline >= ' . time() . ' AND price_limit <= ' . $total_amount, 'num DESC, deadline ASC');
		#echo "<pre>";print_r($freight_coupon_list);die;
		$this->assign('freight_coupon_list', $freight_coupon_list);

		//订单优惠信息
		$coupon_info = PromotionBaseModel::getAllCoupon($total_amount, $cart_list, $user_rank_id);
		$this->assign('coupon_info', $coupon_info);
		//检查是否有单品或订单优惠
		foreach ($coupon_info['item_list'] as $key => $value) {
			if($coupon_info['item_list'][$key]['discount_ary']['promo_discount']['name'])
				$is_single_coupon = true;
		}
		if($coupon_info['order_discount_ary']['name'] || $is_single_coupon){
			$has_coupon = 1;
			$this->assign('has_coupon', $has_coupon);
		}
		#echo "<pre>";print_r($is_single_coupon);print_r($coupon_info);echo "</pre>";die;


		$this->assign('total_amount', $total_amount);
		$this->assign('total_weight', $total_weight);
		$this->assign('total_integral', $total_integral);
		$this->assign('pay_amount', $pay_amount);
		$this->assign('discount_amount', $discount_amount);

		$this->assign('shopping_cart_id_str', $shopping_cart_id_str);
		$this->assign('number_str', $number_str);
		$this->assign('cart_list', $cart_list);
		
		//调用物流模型ShippingCompanyModel的calculateShippingFee
		if (!$province_id)
		{
			//获取当前IP所在省份城市
			$area_info = getIPSource(get_client_ip());
			$province_id = $area_info['province_id'];
			$city = $area_info['city_name'];
		}
		$default_express_company = $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY'];
		$shipping_company_obj = new ShippingCompanyModel();
		$this->assign('city', $city);
		$this->assign('express_fee', $shipping_company_obj->calculateShippingFee($default_express_company, $province_id, $total_weight, $total_amount));

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
			#echo $order_item_obj->getLastSql();
			#var_dump($order_ids);die;
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
		#$where = '';
		//获取订单列表
		$order_obj = new OrderModel();
		//分页处理
        import('ORG.Util.Pagelist');
        //总数
		$total = $order_obj->getOrderNum($where);
        $Page = new Pagelist($total, $this->order_num_per_page);
        $show = $Page->show();
        $this->assign('show', $show);

        $order_obj->setStart($Page->firstRow);
        $order_obj->setLimit($Page->listRows);
        $fields = 'user_address_id, order_status, order_id, order_sn, pay_amount, total_amount, addtime, payway, express_company_id';
		$order_list = $order_obj->getOrderList($fields, $where, 'addtime DESC');		
		$order_list = $order_obj->getListData($order_list);


		$this->assign('order_list', $order_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->order_num_per_page);

		//待支付订单数量
		$pre_pay_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::PRE_PAY);
		$this->assign('pre_pay_order_num', $pre_pay_order_num);

		//待发货订单数量
		$pre_deliver_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::PAYED);
		$this->assign('pre_deliver_order_num', $pre_deliver_order_num);

		//待确认订单数量
		$pre_confirm_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::DELIVERED);
		$this->assign('pre_confirm_order_num', $pre_confirm_order_num);

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
		$where = 'isuse = 1 AND user_id = ' . intval(session('user_id'));
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'all');
	}

	//待支付订单
	public function pre_pay_order()
	{
		$where = 'isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::PRE_PAY;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'pre_pay');
	}

	//待发货订单
	public function pre_deliver_order()
	{
		$where = 'isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::PAYED;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'payed');
	}

	//待确认订单
	public function pre_confirm_order()
	{
		$where = 'isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::DELIVERED;
		$where .= $this->get_search_condition();
		$this->get_order_list($where, '全部订单', 'delivered');
	}

	//订单详情
	public function order_detail()
	{
		//订单ID
		$order_id = $this->_get('order_id');
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, order_status, pay_amount, total_amount, user_address_id, express_company_id, express_number, express_fee, payway, addtime, pay_time, user_remark');
		}
		catch (Exception $e)
		{
			$this->alert('对不起，订单不存在！', U('/FrontOrder/all_order'));
		}
		//订单支付后获得积分
        $order_info['total_integral']  = intval($order_info['pay_amount']);

		//订单状态
		$order_info['order_status_name'] = OrderModel::convertOrderStatus($order_info['order_status']);
		//收货人信息
		$user_address_obj   = new UserAddressModel();
        $user_address_info  = $user_address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id']);
        $order_info['consignee']  = $user_address_info['realname'];
        $order_info['mobile']  = $user_address_info['mobile'];
		$order_info['area_string'] = AreaModel::getAreaString($user_address_info['province_id'], $user_address_info['city_id'], $user_address_info['area_id']) . $user_address_info['address'];

        $user_info = D('User')->getUserInfo('email, tel', 'user_id = ' . intval(session('user_id')));
        $order_info['email']  = $user_info['email'];
        $order_info['tel']  = $user_info['tel'];

		unset($order_info['province_id']);
		unset($order_info['city_id']);
		unset($order_info['area_id']);
		unset($order_info['address']);

		//获取订单商品信息
		$order_info['order_item_list'] = $order_obj->getOrderItemList('');
		foreach ($order_info['order_item_list'] as $key => $value) {
			$order_info['order_item_list'][$key]['total_price'] = $value['real_price'] * $value['number'];		
			$order_info['order_item_list'][$key]['get_integral'] = intval($order_info['order_item_list'][$key]['total_price']);		
		}
		#echo "<pre>";print_r($order_info);print_r($order_info['order_item_list']);die;
		$this->assign('order_info', $order_info);
		$this->assign('head_title', '订单详情');
		$this->display();
	}

	//退款申请
	public function refund_apply()
	{
		$order_id = $this->_request('order_id');
		$order_obj = new OrderModel($order_id);
		$item_refund_obj = new ItemRefundChangeModel();
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_status, addtime, pay_amount');
		}
		catch (Exception $e)
		{
			$this->alert('抱歉，订单不存在', U('/FrontOrder/all_order'));
		}

		if ($order_info['order_status'] != OrderModel::PAYED)
		{
			$this->alert('抱歉，当前订单状态不能退款', U('/FrontOrder/all_order'));
		}

		//获取订单商品列表
		if($item_refund_obj->getItemRefundChangeInfo('order_id = ' . $order_id, 'item_ids')){
			unset($order_item_list);
		}else{
			$order_item_list = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price', 'order_id = ' . $order_id);
		}
		/*$order_item_list = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price', 'order_id = ' . $order_id);
		$refund_item_ids = $item_refund_obj->getItemRefundChangeInfo('order_id = ' . $order_id, 'item_ids');
		$refund_item_ids = explode(',', $refund_item_ids['item_ids']);
		$refund_item_ids = array_filter($refund_item_ids);
		foreach ($order_item_list as $key => $value) {
			$item_list[$key] = $order_item_list[$key]['item_id'];
		}
		$arr_diff1 = array_diff($item_list, $refund_item_ids);
		$arr_diff2 = array_diff($refund_item_ids, $item_list);
		$arr_diff = array_merge($arr_diff1, $arr_diff2);
		foreach ($arr_diff as $key => $value) {
			$diff_item_list[$key]['diff_order_list'] = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price', 'item_id = ' . $value . ' AND order_id = ' . $order_id);
		}*/
		#echo "<pre>";echo $order_obj->getLastSql();print_r($items_refund_list); print_r($refund_item_ids);print_r($order_item_list);print_r($refund_item_ids);die;

		//处理退款请求
		$act = $this->_post('act');
		$number_list = $this->_post('number_list');
		$item_id_list = $this->_post('item_id_list');
		$refund_price = floatval($this->_post('refund_price'));
		if ($act == 'refund')
		{
			$arr = array(
				'item_nums'	=> $number_list,
				'item_ids'	=> $item_id_list,
				'refund_money'	=> $refund_price,
			);
			$order_obj->refundApply($arr);
			$this->alert('退款申请已提交', '/FrontOrder/order_refund_detail/order_id/' . $order_id);
		}

		$this->assign('order_info', $order_info);
		#$this->assign('item_list', $diff_item_list);
		$this->assign('item_list', $order_item_list);
		$this->assign('head_title', '申请退款');
		$this->display();
	}

	//退款详情
	public function order_refund_detail()
	{
		$order_id = $this->_request('order_id');
		$order_obj = new OrderModel($order_id);
		$item_refund_obj = new ItemRefundChangeModel();		
		$user_id = intval(session('user_id'));
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, order_status, pay_time, total_amount, pay_amount', ' AND user_id = ' . $user_id);
		}
		catch (Exception $e)
		{
			#die($order_obj->getLastSql());
			$this->alert('抱歉，订单不存在', U('/FrontOrder/all_order'));
		}

		if (!$order_info['refund_money_back_time'])
		{
			//若当前未退回款项，则显示预计的退回款项时间
			$order_info['refund_money_back_time_str'] = $order_info['refund_pass_time'] ? $order_info['refund_pass_time'] + 86400 : $order_info['refund_apply_time'] + 86400 * 4;
			$order_info['refund_money_back_time_str'] = '预计' . date('Y-m-d H:i', $order_info['refund_money_back_time_str']) . '之前';
		}
		else
		{
			$order_info['refund_money_back_time_str'] = date('Y-m-d H:i', $order_info['refund_money_back_time_str']);
		}

		if (!$order_info['refund_pass_time'])
		{
			//若当前未通过退款，则显示预计的退款时间，退款时间起3天内
			$order_info['refund_pass_time_str'] = '预计' . date('Y-m-d H:i', $order_info['refund_apply_time'] + 86400 * 3) . '之前';
		}
		else
		{
			$order_info['refund_pass_time_str'] = date('Y-m-d H:i', $order_info['refund_pass_time_str']);
		}

		//获取订单商品列表
		$order_item_list = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price', 'order_id = ' . $order_id);
		$refund_item_num_ids = $item_refund_obj->getItemRefundChangeInfo('order_id = ' . $order_id, 'item_ids,item_nums');
		$refund_item_ids = explode(',', $refund_item_num_ids['item_ids']);
		$refund_num_ids = explode(',', $refund_item_num_ids['item_nums']);
		foreach ($refund_item_ids as $key => $value) {
			$diff_item_list[$key]['diff_order_list'] = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price', 'item_id = ' . $value . ' AND order_id = ' . $order_id);
		}
		foreach ($diff_item_list as $key => $value) {
			$diff_item_list[$key]['diff_order_list'][0]['number'] = $refund_num_ids[$key];
				
		}
		#echo $order_obj->getLastSql();
		#echo "<pre>";print_r($diff_item_list);print_r($refund_item_ids);print_r($refund_num_ids);die;
		#print_r($order_info);
		

		//获取退货记录
		$item_refund_list = $item_refund_obj->getItemRefundList('item_refund_change_id, handle_time, addtime, order_id, admin_remark, state','order_id = ' . $order_id);
		foreach ($item_refund_list as $key => $value) {
			$item_refund_list[$key]['state_name'] = $item_refund_obj->convertApplyState($value['state']);
		}
		$this->assign('order_info', $order_info);
		$this->assign('item_list', $diff_item_list);
		$this->assign('item_refund_list', $item_refund_list);
		$this->assign('head_title', '申请退款详情');
		$this->display();
	}

	//支付成功
	public function pay_success()
	{
		$order_id = $this->_request('order_id');
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_status, confirm_code, order_type, merchant_id, meal_time', ' AND user_id = ' . intval(session('user_id')));
		}
		catch (Exception $e)
		{
			$this->alert('抱歉，订单不存在', U('/'));
		}

		if ($order_info['order_status'] != OrderModel::PAYED)
		{
			$this->alert('抱歉，订单不存在', U('/'));
		}

		//获取订单商品列表
		$order_item_list = $order_obj->getOrderItemList('item_name, number');

		//商家名称
		$merchant_obj = new MerchantModel($order_info['merchant_id']);
		$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $order_info['merchant_id'], 'shop_name');

		$this->assign('order_info', $order_info);
		$this->assign('item_list', $order_item_list);
		$this->assign('shop_name', $merchant_info['shop_name']);
		$this->assign('head_title', '支付成功');
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
		$payway_id = $this->_get('payway_id');
		if ($order_id)
		{
			session('order_id', $order_id);
			session('payway_id', $payway_id);
			redirect('http://' . $_SERVER['HTTP_HOST']	. '/FrontOrder/pay_order/');
		}
		$order_id = intval(session('order_id'));
		$payway_id = intval(session('payway_id'));
		//订单信息
		$order_obj = new OrderModel($order_id);
		$redirect = U('/FrontOrder/pre_pay_order');
		try
		{
			$order_info = $order_obj->getOrderInfo('pay_amount, order_sn, order_status, coupon_id, coupon_amount', ' AND user_id = ' . session('user_id'));
		}
		catch (Exception $e)
		{
			$this->alert('对不起，无效的订单号！', $redirect);
		}

		if ($order_info['order_status'] != OrderModel::PRE_PAY)
		{
			$this->alert('对不起，该订单已付款，您不能重复支付', $redirect);
		}

		$this->assign('order_info', $order_info);
		$pay_amount = $order_info['pay_amount'];
		$act = $this->_post('act');
		//支付宝
		$payment_obj = new AlipayModel();
		$link = $payment_obj->pay_code($order_id, $pay_amount);
		$this->assign('link', $link);
		//网银支付
		$payment_obj = new ChinabankModel();
		$parameter = $payment_obj->pay_code($order_id, $pay_amount);
		$this->assign('parameter', $parameter);
		$this->assign('amount', $pay_amount);
		$this->assign('integral', intval($pay_amount));
		$this->assign('order_id', $order_id);
		//获取优惠券信息
		$coupon_obj = new CouponModel();
		$coupon_info = $coupon_obj->getCouponInfo('coupon_id = ' . $order_info['coupon_id'], 'coupon_id, order_id');
		#echo "<pre>";print_r($coupon_info);die;
		//余额支付
		//查看用户电子钱包余额，若不足，提示余额不足，并跳转到充值页面
		$wallet_pay = $this->_post('wallet_pay');
		if($wallet_pay == 'wallet'){

			$user_obj = new UserModel(session('user_id'));
			$left_money = $user_obj->getLeftMoney();
			if ($left_money < $pay_amount)
			{
				$this->alert('对不起，' . $GLOBALS['config_info']['SYSTEM_MONEY_NAME'] . '不足，请充值', '/FrontUser/my_account');
			}
			else
			{
				//使用优惠券
				if ($coupon_info)
				{
					$tag = $coupon_obj->useCoupon($order_info['coupon_id'], $order_id, $order_info['coupon_amount']);
					
				}
				$order_obj = new OrderModel($order_id);
				//调用账户模型的addAccount方法支付该订单
				$account_obj = new AccountModel();
				$account_obj->addAccount(session('user_id'), 5, $pay_amount * -1, '支付订单' . $order_info['order_sn'], $order_id, 'wallet');
				$this->alert('恭喜您，订单付款成功', U('/FrontOrder/all_order'));
			}
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
		//获取默认支付方式
		$pay_tag = $payway_obj->getPayTagById($payway_id);
		$this->assign('pay_tag', $pay_tag);


		//获取银行列表
		$bank_list = $payway_obj->getBankList();
		$this->assign('bank_list', $bank_list);

		//系统内部货币名称
		$this->assign('SYSTEM_MONEY_NAME', $GLOBALS['config_info']['SYSTEM_MONEY_NAME']);
		$this->assign('head_title', '订单付款');
		$this->display();
	}

	//充值-银行卡
	public function recharge_bank()
	{
		$amount = $this->_get('amount');
		if ($amount)
		{
			session('amount', $amount);
			redirect('http://' . $_SERVER['HTTP_HOST']	. '/FrontOrder/recharge_bank/');
		}
		$amount = floatval(session('amount'));

		$this->assign('amount', $amount);

		//获取银行列表
		$payway_obj = new PaywayModel();
		$bank_list = $payway_obj->getBankList();
		$this->assign('bank_list', $bank_list);

		$this->assign('head_title', '网银充值');
		$this->display();
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
		$user_address_id = $this->_post('user_address_id');
		$shopping_cart_id_str = $this->_post('shopping_cart_id_str');
		$number_str = $this->_post('number_str');
		$user_remark = $this->_post('user_remark');
		$pay_amount = $this->_post('pay_amount');
		$payway_name = $this->_post('payway_name');
		$total_integral = $this->_post('total_integral');
		$freight_coupon_id = intval($this->_post('freight_coupon_id')) ? intval($this->_post('freight_coupon_id')) : 0;
		$total_discount_amount = $this->_post('total_discount_amount');
		$user_id = intval(session('user_id'));

		if (ctype_digit($user_address_id) && $shopping_cart_id_str && $number_str && $pay_amount && $user_id)
		{

			$shopping_cart_id_list = explode(',', $shopping_cart_id_str);
			$number_list = explode(',', $number_str);

			if (count($shopping_cart_id_list) && (count($shopping_cart_id_list) == count($number_list)))
			{
				$cart_obj = new ShoppingCartModel();
				$cart_list = $cart_obj->getShoppingCartList('', ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')', 'addtime DESC');
				$cart_list = $cart_obj->getListData($cart_list);
				if (!$cart_list)
				{
					exit('failure');
				}
				$item_obj = new ItemModel();
				$total_amount = 0.00;
				$total_weight = 0;
				foreach ($cart_list AS $k => $v)
				{
					$cart_list[$k]['number'] = $number_list[$k];
					$total_amount += $v['real_price'] * $number_list[$k];
					$total_weight += $v['weight'] * $number_list[$k];
					if (!$number_list[$k])
					{
						unset($cart_list[$k]);
					}
				}

				//获取收货地址信息
				$user_address_obj = new UserAddressModel();
				$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id);
				if (!$user_address_info)
				{

					exit('收货地址错误');
				}
				$freight_coupon_amount = 0.00;
				//判断优惠券是否有效
				if ($freight_coupon_id)
				{
					$freight_coupon_obj = new CouponModel();
					$freight_coupon_info = $freight_coupon_obj->getCouponInfo('coupon_id = ' . $freight_coupon_id, 'num');
					$freight_coupon_amount = $freight_coupon_info['num'] ? $freight_coupon_info['num'] : 0.00;
					$num = $freight_coupon_obj->getCouponNum('state = 0 AND coupon_id = ' . $freight_coupon_id);
					if (!$num)
					{
						$return_arr = array(
							'code'	=> 5,
							'msg'	=> '无效的优惠券，请重新选择优惠券',
						);
						$this->json_exit($return_arr);
					}
				}

				//计算运费
				//调用物流模型ShippingCompanyModel的calculateShippingFee
				$default_express_company = $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY'];
				$shipping_company_obj = new ShippingCompanyModel();
				$express_fee = $shipping_company_obj->calculateShippingFee($default_express_company, $user_address_info['province_id'], $total_weight, $total_amount);


				//订单信息数组
				$arr = array(
					'pay_amount'		=> $pay_amount,
					'total_amount'		=> $total_amount,
					'integral_amount'		=> $total_integral,
					// 'cost_amount'		=> $total_amount,
					'express_fee'		=> $express_fee,
					'weight'			=> $total_weight,
					'user_address_id'	=> $user_address_id,
					'payway'		=> $payway_name,
					'user_remark'		=> $user_remark,
					'coupon_id'		=> $freight_coupon_id,
					'coupon_amount'		=> $freight_coupon_amount,
					'system_discount_amount'		=> $total_discount_amount,
				);
				$order_obj = new OrderModel();
				$success = $order_obj->addOrder($arr, $cart_list);
				#echo "<pre>";echo $order_obj->getLastSql();die('5');
				echo $success ? $success : 'failure';
				exit;
			}
		}

		exit('failure');
	}

	//生成首次订单
	function add_first_order()
	{
		$item_str = $this->_post('item_str');
		$number_str = $this->_post('number_str');
		$user_id = intval(session('user_id'));

		if ($item_str && $number_str && $user_id)
		{
			$item_list = explode(',', $item_str);
			$number_list = explode(',', $number_str);

			if (count($item_list) && (count($item_list) == count($number_list)))
			{
				$item_obj = null;
				$fields = 'item_id, item_name, mall_price';
				$item_obj = new ItemModel();

				$item_list = $item_obj->getItemList($fields, ' isuse = 1 AND item_id IN (' . $item_str . ')', 'addtime DESC');
				$item_list = $item_obj->getListData($item_list);
				if (!$item_list)
				{
					exit('failure');
				}
				#echo "<pre>";print_r($item_list);die;
				$total_amount = 0.00;
				foreach ($item_list AS $k => $v)
				{
					$item_list[$k]['number'] = $number_list[$k];
					$total_amount += $v['vip_price'] * $number_list[$k];
					if (!$number_list[$k])
					{
						unset($item_list[$k]);
					}
				}
				//为0时默认为1分钱
				//$total_amount = $total_amount > 0.00 ? $total_amount : 0.01;

				//检查库存
				foreach ($item_list as $k => $v) {
					$stock_enough = $item_obj->checkStockEnough($v['item_id'], $v['item_sku_price_id'], $number_list[$k]);
					if (!$stock_enough)
					{
						$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
						$item_name = $item_info ? $item_info['item_name'] : '';
						/*$stock_arr = array(
							'item_name'		=> $item_name,
						);*/
						#echo json_encode($stock_arr);
						exit('nostock');
						#$this->alert('对不起，商品“' . $item_name . '”库存不足，请重新选择要购买商品', U('FrontCart/cart'));
					}
				}
				
				//订单信息数组
				$arr = array(
					'pay_amount'		=> $total_amount,
					'total_amount'		=> $total_amount,
				);
				$order_obj = new OrderModel();
				$success = $order_obj->addOrder($arr, $item_list);
				echo $success ? $success : 'failure';
				exit;
			}
		}

		exit('failure');
	}

	//异步获取订单列表
	public function order_list()
	{
		$firstRow = I('post.firstRow');
		$user_id = intval(session('user_id'));
		$order_obj = new OrderModel();
		$where = 'isuse = 1';
		$where .= $this->get_search_condition();
		//订单总数
		$total = $order_obj->getOrderNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$order_obj->setStart($firstRow);
			$order_obj->setLimit($this->order_num_per_page);
			//获取订单列表
			$order_list = $order_obj->getOrderList('order_status, order_id, pay_amount, addtime', $where, 'addtime DESC');
			foreach ($order_list AS $k => $v)
			{
				$order_list[$k]['order_status_name'] = OrderModel::convertOrderStatus($v['order_status']);
				$order_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			}
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

	//获取支付宝银行支付链接
	function getPayLink()
	{
		$pay_tag = $this->_post('pay_tag');
		$user_id = intval(session('user_id'));
		$order_id = intval(session('order_id'));

		if ($pay_tag && $order_id && $user_id)
		{
			$payment_obj = new AlipayModel();
			$link = $payment_obj->pay_code($order_id, 0, 0, 0, $pay_tag);
			$return_arr = array(
				'code'	=> 0,
				'link'	=> $link,
			);
			echo json_encode($return_arr);
			exit;
			#$this->json_exit($return_arr);
		}

		exit('failure');
	}

	//获取支付宝银行充值链接
	function getPayBankLink()
	{
		$pay_tag = $this->_post('pay_tag');
		$amount = $this->_post('amount');
		$user_id = intval(session('user_id'));

		if ($pay_tag && $amount && $user_id)
		{
			$payment_obj = new AlipayModel();
			$link = $payment_obj->pay_code(0, $amount, 0, 0, $pay_tag);
			$return_arr = array(
				'code'	=> 0,
				'link'	=> $link,
			);
			echo json_encode($return_arr);
			exit;
			#$this->json_exit($return_arr);
		}

		exit('failure');
	}

}

<?php
class OrderApiModel extends ApiModel
{
	/**
	 * 提交订单(cheqishi.order.addOrder)
	 * @author zlf
	 * @param array $params 参数列表
	 * @return 成功返回$order_id数组，失败退出返回错误码
	 * @todo 提交订单(cheqishi.order.addOrder)
	 */
	function addOrder($params)
	{
		$user_address_id = $params['user_address_id'];
		$shopping_cart_id_str = $params['shopping_cart_ids'];
		$number_str = $params['number_str'];
		$user_remark = $params['user_remark'];
		#$payway_id = $params['payway_id'];
		$payway_name = $params['payway_name'];
		$user_id = intval(session('user_id'));

		$shopping_cart_id_list = explode(',', $shopping_cart_id_str);
		$number_list = explode(',', $number_str);

		if (count($shopping_cart_id_list) && (count($shopping_cart_id_list) == count($number_list)))
		{
			$cart_obj = new ShoppingCartModel();
			$cart_list = $cart_obj->getShoppingCartList('', ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')', 'addtime DESC');
			if (!$cart_list)
			{
				ApiModel::returnResult(42037, null, '无效的购物车ID列表');
			}

			$item_obj = new ItemModel();
			//商品总价
			$total_amount = 0.00;
			//实付商品总价
			$pay_amount = 0.00;
			//商品总优惠价
			$discount_amount = 0.00;
			//商品总重量
			$total_weight = 0;
			//总积分抵扣额
			$total_integral = 0.00;
			foreach ($cart_list AS $k => $v)
			{
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

			//订单折扣计算
			$user_rank_id = $user_info['user_rank_id'];
			$pay_amount_bak = round($total_amount, 2);
			$pay_amount = PromotionBaseModel::calculateOrderCoupon($total_amount, $user_rank_id);
			
			$discount_amount = round($pay_amount_bak - $pay_amount, 2);

			#$total_amount -= round($discount_amount, 2);
			$pay_amount -= round($total_integral, 2);
			//四舍五入保留两位小数
			$total_amount = sprintf('%.2f', $total_amount);

			//计算单品优惠
			$item_coupon_info = PromotionBaseModel::getAllCoupon($total_amount, $cart_list, $user_rank_id);
			foreach ($item_coupon_info['item_list'] as $key => $value) {
				// if($item_coupon_info[$key]['discount_ary']['promo_discount'])
					$total_item_coupon += $item_coupon_info['item_list'][$key]['discount_ary']['promo_discount']['amount'];
			}
			#echo "<pre>";/*print_r($total_item_coupon);*/print_r($item_coupon_info);die;

			$pay_amount -= round($total_item_coupon, 2);
			$pay_amount = sprintf('%.2f', $pay_amount);
			$discount_amount += round($total_item_coupon, 2);

			#echo "<pre>";print_r($discount_amount);print_r($pay_amount);print_r($total_amount);die;


			//获取收货地址信息
			$user_address_obj = new UserAddressModel();
			$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = user_address_id AND user_id = ' . $user_id);
			if (!$user_address_info)
			{
				ApiModel::returnResult(42037, null, '无效的收获地址');
			}

			//计算运费
			//调用物流模型ShippingCompanyModel的calculateShippingFee
			$default_express_company = $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY'];
			$shipping_company_obj = new ShippingCompanyModel();
			$express_fee = $shipping_company_obj->calculateShippingFee($default_express_company, $user_address_info['province_id'], $total_weight, $total_amount);

			//订单信息数组
			$arr = array(
				'pay_amount'		=> $pay_amount + $express_fee,
				'total_amount'		=> $total_amount,
				'integral_amount'		=> $total_integral,
				'system_discount_amount'		=> $discount_amount,
				#'cost_amount'		=> $total_amount,
				'express_fee'		=> $express_fee,
				'weight'			=> $total_weight,
				/*'consignee'			=> $user_address_info['realname'],
				'mobile'			=> $user_address_info['mobile'],
				'province_id'		=> $user_address_info['province_id'],
				'city_id'			=> $user_address_info['city_id'],
				'area_id'			=> $user_address_info['area_id'],
				'address'			=> $user_address_info['address'],*/
				'user_address_id'	=> $user_address_id,
				'user_remark'		=> $user_remark,
				#'payway'			=> $payway_id,
				'payway'		=> $payway_name,
			);

			$order_obj = new OrderModel();
			#echo "<pre>";
			#print_r($cart_list);
			$order_id = $order_obj->addOrder($arr, $cart_list);

			return $order_id;
		}
	
		ApiModel::returnResult(42037, null, '购物车ID和商品数量不对应');
	}

	/**
	 * 根据订单ID获取订单信息(cheqishi.order.getOrderInfo)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$order_info，失败返回错误码
	 * @todo 根据订单ID获取订单信息(cheqishi.order.getOrderInfo)
	 */
	function getOrderInfo($params)
	{
		//订单ID
		$order_id = $params['order_id'];
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, order_status, pay_amount, total_amount, user_address_id, payway, addtime, pay_time, user_remark, merchant_remark, star_num, integral_amount, coupon_amount, express_fee');
		}
		catch (Exception $e)
		{
			ApiModel::returnResult(42038, null, '订单不存在');
		}

		//订单状态
		$order_info['order_status_name'] = OrderModel::convertOrderStatus($order_info['order_status']);

        //用户地址信息
        $where = 'user_address_id = ' .$order_info['user_address_id'];
        $userAddress_obj = new UserAddressModel();
        $userAddress_info = $userAddress_obj->getUserAddressInfo($where, 'user_address_id, realname, mobile, user_id, address, province_id, city_id, area_id');
        $userAddress_info['area_string'] = AreaModel::getAreaString($userAddress_info['province_id'], $userAddress_info['city_id'], $userAddress_info['area_id']);

//        $order_info['userAddress_info'] = $userAddress_info;

        $order_info['user_address_info'] = $userAddress_info;

        //供货商
		$order_info['shop_name'] = $GLOBALS['config_info']['SHOP_NAME'];
		unset($order_info['province_id']);
		unset($order_info['city_id']);
		unset($order_info['area_id']);
		unset($order_info['address']);

		//获取订单商品信息
            $order_info['order_item_list'] = $order_obj->getOrderItemList('order_id, item_id, item_name, item_sn, item_sku_price_id, property, number, real_price, small_pic');
		require_cache('Common/func_item.php');

        $item_total_num = '';
        foreach ($order_info['order_item_list'] AS $k => $v)
		{
			if (!is_numeric(strpos($v['small_pic'], '_s')))
			{
				//不是小图
				$order_info['order_item_list'][$k]['small_pic'] = small_img($v['small_pic']);
			}

            $item_total_num += $v['number'];
        }
        $order_info['item_total_num'] = $item_total_num;

		return $order_info;
	}

    /**
     * 订单发货 (merchant.order.deliverGoods)
     * @author clk
     * @param array $params
     * @return 成功返回'发货成功'，失败返回错误码
     * @todo 订单发货(merchant.order.deliverGoods)
     */
    function deliverGoods($params)
    {
        $order_id = $params['order_id'];
        $order_obj = new OrderModel($order_id);
        $express_company_id = $params['express_company_id'];
        $express_number = $params['express_number'];

        // 查看订单是否存在
        $order_info = $order_obj->getOrderInfo('order_status');
        if (!$order_info)
        {
            ApiModel::returnResult(42038, null, '订单不存在');
        }

        $success = $order_obj->deliverOrder($express_company_id, $express_number);
        if (!$success)
        {
            ApiModel::returnResult(-1, null, '系统错误');
        }

        return '发货成功';
    }



	/**
	 * 取消订单(merchant.order.cancelOrder)
	 * @author clk
	 * @param array $params
	 * @return 成功返回'取消成功'，失败返回错误码
	 * @todo 取消订单(merchant.order.cancelOrder)
	 */
	function cancelOrder($params)
	{
		$order_id = $params['order_id'];
		$order_obj = new OrderModel($order_id);

		$order_info = $order_obj->getOrderInfo('order_status');
		if (!$order_info)
		{
			ApiModel::returnResult(42038, null, '订单不存在');
		}

		$success = $order_obj->setOrderState(OrderModel::CANCELED);
		if (!$success)
		{
			ApiModel::returnResult(-1, null, '系统错误');
		}

		return '取消成功';
	}

	/**
	 * 确认收货(cheqishi.order.confirmOrder)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回'确认成功'，失败返回错误码
	 * @todo 确认收货(cheqishi.order.confirmOrder)
	 */
	function confirmOrder($params)
	{
		$user_id = intval(session('user_id'));
		$order_id = $params['order_id'];
		$order_obj = new OrderModel($order_id);

        $user_id = 61979;

		//查看是否当前用户的订单
		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_status', ' AND user_id = ' . $user_id);
		}
		catch (Exception $e)
		{
			ApiModel::returnResult(42038, null, '订单不存在');
		}

		$success = $order_obj->setOrderState(OrderModel::CONFIRMED);
		if (!$success)
		{
			self::returnResult(-1, null, '系统错误');
		}

		return '确认收货成功';
	}

	/**
	 * 根据订单状态获取订单列表(cheqishi.order.getOrderList)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$order_list，失败返回错误码
	 * @todo 根据订单状态获取订单列表(cheqishi.order.getOrderList)
	 */
	function getOrderList($params)
	{
		#$order_num_per_page = $GLOBALS['config_info']['ITEM_NUM_PER_PAGE'];
		$order_status = isset($params['order_status']) ? $params['order_status'] : '';
		$firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $order_num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $order_num_per_page = isset($order_num_per_page) ? $order_num_per_page : 10;

        $order_sn = isset($params['order_sn']) ? $params['order_sn'] : "";

		$where = 'isuse = 1';
        $where .= $order_status == '' ? '' : ' AND order_status = ' . $order_status;
        $where .= $order_sn == '' ? '' : ' AND order_sn like "%' .$order_sn .'%"';


        //获取订单列表
		$order_obj = new OrderModel();

		//总数
		$total = $order_obj->getOrderNum($where);
        $order_obj->setStart($firstRow);
        $order_obj->setLimit($order_num_per_page);
		$order_list = $order_obj->getOrderList('user_address_id, order_status, order_id, order_sn, pay_amount, total_amount, addtime, payway, express_company_id, express_fee', $where, 'addtime DESC');

        #log_file(intval(session('user_id')));
		#log_file($order_obj->getLastSql());

        //$order_list = $order_obj->getListData($order_list);


        foreach ($order_list AS $k  => $v)
		{
			$order_list[$k]['order_status_name'] = OrderModel::convertOrderStatus($v['order_status']);
		}

		//退款订单则返回订单商品列表
        /*
		foreach ($order_list AS $k => $v)
		{
			if ($v['order_status'] == OrderModel::REFUNDING || $v['order_status'] == OrderModel::REFUND_CONFIRMD || $v['order_status'] == OrderModel::REFUND_DELIVEING || $v['order_status'] == OrderModel::REFUND_DELIVER_CONFIRMD || $v['order_status'] == OrderModel::REFUNDED)
			{
				$order_obj = new OrderModel($v['order_id']);
				$order_list[$k]['item_list'] = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price, small_pic');
            }
		}
        */

        // 所有订单返回订单商品列表
        foreach ($order_list AS $k => $v) {

            // 商品列表
            $order_obj = new OrderModel($v['order_id']);
            $item_list = $order_obj->getOrderItemList('item_id, item_name, item_sn, number, real_price, small_pic');
            $order_list[$k]['item_list'] = $item_list;

            // 商品总数
            $item_total_num = '';
            foreach ($item_list AS $item_k => $item_v) {

                $item_total_num += $item_v['number'];
            }
            $order_list[$k]['item_total_num'] = $item_total_num;

            //用户地址信息
            $where = 'user_address_id = ' .$v['user_address_id'];
            $userAddress_obj = new UserAddressModel();
            $user_address_info = $userAddress_obj->getUserAddressInfo($where, 'user_address_id, realname, mobile, user_id');

            $order_list[$k]['user_address_info'] = $user_address_info;
        }

		//待支付订单数量
		$pre_pay_order_num = $order_obj->getOrderNum('isuse = 1' . ' AND order_status = ' . OrderModel::PRE_PAY);

		//待发货订单数量
		$pre_deliver_order_num = $order_obj->getOrderNum('isuse = 1' . ' AND order_status = ' . OrderModel::PAYED);

		//待确认订单数量
		$pre_confirm_order_num = $order_obj->getOrderNum('isuse = 1' . ' AND order_status = ' . OrderModel::DELIVERED);

		//退款订单数量
		#$pre_refund_order_num = $order_obj->getOrderNum('isuse = 1 AND user_id = ' . intval(session('user_id')) . ' AND order_status = ' . OrderModel::REFUNDING);

		$return_arr = array(
			'pre_pay'				=> OrderModel::PRE_PAY,
			'delivered'				=> OrderModel::DELIVERED,
			'confirmed'				=> OrderModel::CONFIRMED,
			#'refund'				=> OrderModel::REFUNDING,
			'NextFirstRow'			=> $firstRow + $order_num_per_page,
			'order_list'			=> $order_list,
			'total'					=> $total,
			'pre_pay_order_num'		=> $pre_pay_order_num,
			'pre_deliver_order_num'	=> $pre_deliver_order_num,
			'pre_confirm_order_num'	=> $pre_confirm_order_num,
			#'pre_refund_order_num'	=> $pre_refund_order_num,
		);
		return $return_arr;
	}

	/**
	 * 申请退款(cheiqishi.order.refundApply)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回'退款申请已提交'，失败返回错误码
	 * @todo 申请退款(cheiqishi.order.refundApply)
	 */
	function refundApply($params)
	{
		$user_id = intval(session('user_id'));
		$order_id = $params['order_id'];
		$reason = $params['reason'];
		$order_obj = new OrderModel($order_id);

		//查看是否当前用户的订单
		$order_obj = new OrderModel($order_id);
		//$order_info = $order_obj->getOrderInfo('order_status', ' AND user_id = ' . $user_id);

        $order_info = $order_obj->getOrderInfo('order_status');
        if (!$order_info)
		{
			ApiModel::returnResult(42038, null, '订单不存在');
		}

		if ($order_info['order_status'] != OrderModel::PAYED)
		{
			ApiModel::returnResult(40038, null, '当前订单状态不能退款');
		}

		//执行退款操作
		$arr = array(
			#'refund_reason'		=> $reason,
			'addtime'	=> time()
		);
		$success = $order_obj->refundApply($arr);

		if (!$success)
		{
			ApiModel::returnResult(-1, null, '系统错误');
		}
		return '退款已受理';
	}


    /**
     * 确认退款(merchant.order.passRefundApply)
     * @author clk
     * @param array $params
     * @return 成功返回'退款成功'，失败返回错误码
     * @todo 确认退款(merchant.order.passRefundApply)
     */
    function passRefundApply($params)
    {
        $order_id = $params['order_id'];

        $order_obj = new OrderModel($order_id);
        $order_info = $order_obj->getOrderInfo('order_status');
        if (!$order_info)
        {
            ApiModel::returnResult(42038, null, '订单不存在');
        }

        if ($order_info['order_status'] != OrderModel::PAYED)
        {
            ApiModel::returnResult(40038, null, '当前订单状态不能退款');
        }

        //执行退款操作
        $arr = array(
            #'refund_reason'		=> $reason,
            'addtime'	=> time()
        );
        $success = $order_obj->refundApply($arr);

        if (!$success)
        {
            ApiModel::returnResult(-1, null, '系统错误');
        }
        return '退款申请已提交';
    }

    /**
     * 拒绝退款(merchant.order.refuseRefundChangeApply)
     * @author clk
     * @param array $params
     * @return 成功返回'退款成功'，失败返回错误码
     * @todo 拒绝退款(merchant.order.refuseRefundChangeApply)
     */
    function refuseRefundChangeApply($params)
    {
        $order_id = $params['order_id'];

        $order_obj = new OrderModel($order_id);
        $order_info = $order_obj->getOrderInfo('order_status');
        if (!$order_info)
        {
            ApiModel::returnResult(42038, null, '订单不存在');
        }

        if ($order_info['order_status'] != OrderModel::PAYED)
        {
            ApiModel::returnResult(40038, null, '当前订单状态不能退款');
        }

        //执行退款操作
        $arr = array(
            #'refund_reason'		=> $reason,
            'addtime'	=> time()
        );
        $success = $order_obj->refundApply($arr);

        if (!$success)
        {
            ApiModel::returnResult(-1, null, '系统错误');
        }
        return '退款申请已提交';
    }


    /**
     * 改变订单收货地址 (merchant.order.changeAddress)
     * @author clk
     * @param array $params
     * @return 成功返回'订单地址修改成功'，失败返回错误码
     * @todo 改变订单地址(merchant.order.changeAddress)
     */
    function changeAddress($params) {

        $order_id = $params['order_id'];


        //查看是否当前用户的订单
        $order_obj = new OrderModel($order_id);
        $order_info = $order_obj->getOrderInfo('order_status, user_address_id, order_id');
        if (!$order_info)
        {
            ApiModel::returnResult(42038, null, '订单不存在');
        }

        $userAddress_obj = new UserAddressModel($order_info['user_address_id']);

        $success = $userAddress_obj->editUserAddress($params);
        if (!$success) {

            ApiModel::returnResult(-1, null, '系统错误');
        }

        return  "修改成功";
    }

    /**
     * 改变订单物流信息 (merchant.order.editOrderInfo)
     * @author clk
     * @param array $params
     * @return 成功返回'物流修改成功'，失败返回错误码
     * @todo 编辑订单 信息(merchant.order.editOrderInfo)
     */

    function editOrderInfo($params) {

        $order_id = $params['order_id'];

        //查看是否当前用户的订单
        $order_obj = new OrderModel($order_id);
        $order_obj->setOrderInfo($params);
        $success = $order_obj->saveOrderInfo();

        if (!ctype_digit((string) $success))
        {
            ApiModel::returnResult(-1, null, '系统错误');
        }

        return '修改成功';
    }

    /**
     * 获取物流公司列表 (merchant.order.getShippingCompanyList)
     * @author clk
     * @param array $params
     * @return 成功返回物流公司列表，失败返回错误码
     * @todo 获取物流公司列表 (merchant.order.getShippingCompanyList)
     */

    function getShippingCompanyList($params) {

        $shippingCompany_obj = new ShippingCompanyModel();
        $shippingCompany_list = $shippingCompany_obj->getAllShippingCompanyList();

        return $shippingCompany_list;
    }

    /**
     * 获取订单状态变更列表 (merchant.order.getOrderLogList)
     * @author clk
     * @param array $params
     * @return 成功返回订单状态变更列表，失败返回错误码
     * @todo 获取订单状态变更列表 (merchant.order.getOrderLogList)
     */

    function getOrderLogList($params) {

        $order_id = $params['order_id'];
        $orderLog_obj = new OrderLogModel();
        $orderLog_list = $orderLog_obj->getOrderLogList($order_id);

        return $orderLog_list;
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
			'addOrder'	=> array(
				array(
					'field'		=> 'user_address_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41032, 
					'empty_code'=> 44032, 
					'type_code'	=> 45032, 
				),
				array(
					'field'		=> 'shopping_cart_ids', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41033, 
					'empty_code'=> 44033, 
					'type_code'	=> 45033, 
				),
				array(
					'field'		=> 'number_str', 
					'type'		=> 'string',
					'required'	=> true, 
					'miss_code'	=> 41034, 
					'empty_code'=> 44034, 
					'type_code'	=> 45034, 
				),
				/*array(
					'field'		=> 'payway_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41035, 
					'empty_code'=> 44035, 
					'type_code'	=> 45035, 
				),*/
				array(
					'field'		=> 'payway_name', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41036, 
					'empty_code'=> 44036, 
					'type_code'	=> 45036, 
				),
				array(
					'field'		=> 'user_remark', 
				),
			),
			'getOrderInfo'	=> array(
				array(
					'field'		=> 'order_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41037, 
					'empty_code'=> 44037, 
					'type_code'	=> 45037, 
				),
			),
            'changeAddress'	=> array(
                array(
                    'field'		=> 'order_id',
                    'type'		=> 'int',
                    'required'	=> true,
                ),
                array(
                    'field'		=> 'address',
                    'type'		=> 'string',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'realname',
                    'type'		=> 'string',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'mobile',
                    'type'		=> 'string',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'province_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),         array(
                    'field'		=> 'city_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'area_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
            ),
            'editOrderInfo'	=> array(
                array(
                    'field'		=> 'order_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41037,
                    'empty_code'=> 44037,
                    'type_code'	=> 45037,
                ),
                array(
                    'field'		=> 'express_company_id',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'express_number',
                    'type'		=> 'string',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'merchant_remark',
                    'type'		=> 'string',
                    'required'	=> false,
                ),
                array(
                    'field'		=> 'star_num',
                    'type'		=> 'int',
                    'required'	=> false,
                ),
            ),
			'cancelOrder'	=> array(
				array(
					'field'		=> 'order_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41037, 
					'empty_code'=> 44037, 
					'type_code'	=> 45037, 
				),
			),
			'confirmOrder'	=> array(
				array(
					'field'		=> 'order_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41037, 
					'empty_code'=> 44037, 
					'type_code'	=> 45037, 
				),
			),
            'deliverGoods'	=> array(
                array(
                    'field'		=> 'order_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41037,
                    'empty_code'=> 44037,
                    'type_code'	=> 45037,
                ),
                array(
                    'field'		=> 'express_company_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'required'	=> true,
                    'miss_code'	=> 41037,
                    'empty_code'=> 44037,
                    'type_code'	=> 45037,
                ),
                array(
                    'field'		=> 'express_number',
                    'type'		=> 'string',
                    'required'	=> true,
                    'required'	=> true,
                    'miss_code'	=> 41037,
                    'empty_code'=> 44037,
                    'type_code'	=> 45037,                 ),
            ),
			'getOrderList'	=> array(
				array(
					'field'		=> 'order_status',
                    'type'		=> 'int',
                ),
                array(
                    'field'		=> 'order_type',
                    'type'		=> 'int',
                ),
                array(
                    'field'		=> 'order_sn',
                    'type'		=> 'string',
                ),
				array(
					'field'		=> 'firstRow',
                    'type'		=> 'int',
                ),
                array(
                    'field'		=> 'fetchNum',
                    'type'		=> 'int',
                ),
			),
			'refundApply'	=> array(
				array(
					'field'		=> 'order_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41037, 
					'empty_code'=> 44037, 
					'type_code'	=> 45037, 
				),
			),
            'getOrderLogList'	=> array(
                array(
                    'field'		=> 'order_id',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41037,
                    'empty_code'=> 44037,
                    'type_code'	=> 45037,
                ),
            ),
		);

		return $params[$func_name];
	}
}

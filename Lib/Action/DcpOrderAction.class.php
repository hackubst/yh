<?php
/**
 * 管理员订单管理控制器
 * @access public
 * @author wzg
* @Date 2014-09-04
 */
class DcpOrderAction extends DcpAction
{
    /**
     * 初始化
     * @author wzg
     * @return void
     * @todo 初始化方法
     */
	public function _initialize()
	{
        parent::_initialize();

        //获取查看所负责门店
        $Muser = new UserModel();
        $user_id = intval(session('user_id'));
        $this->userId = $user_id;
        $this->storeSn = $Muser->where('user_id = ' . $user_id)->getField('dept_list');
        //$this->deptList = explode(',', $store_sn);
        $this->queryWhere = ' AND dept_code IN (' . $this->storeSn . ')';
	}

	/**
	 * 获取列表页的查询条件
	 * @author wzg
	 * @param void
	 * @return string $where SQL查询where字句 
	 * @todo 获取列表页表单提交的查询条件，过滤合法性后，组织成SQL查询的where子句
	 */
	private function get_search_condition()
	{
		//是否显示物流公司选择
		$show_express_company_status = (ACTION_NAME == 'get_pre_refund_order_list' || ACTION_NAME == 'get_pre_exchange_order_list') ? false : true;

		//是否显示处理状态
		$show_handling_status = (ACTION_NAME == 'get_refunded_order_list' || ACTION_NAME == 'get_exchanged_order_list') ? true : false;

		//初始化SQL查询的where子句
		$where = '';

		//订单编号
		$order_sn = $this->_request('order_sn');
		if ($order_sn)
		{
			$where .= ' AND order_sn = "' . $order_sn . '"';
		}

		//订单状态
		$order_status = $this->_request('order_status');
		$order_status = ($order_status == '' || $order_status == -1) ? -1 : intval($order_status);
		if ($order_status != -1)
		{
			$where .= ' AND order_status = ' . $order_status;
		}

		//订单退换货处理状态
		$state = $this->_request('apply_state');
		$state = ($state == '') ? -1 : $state;
		$state = intval($state);
		if ( $state >= 0 && $show_handling_status )
		{
			$where .= ' AND state = ' . intval($state);
		}

		//物流公司
		$express_company_id = $this->_request('express_company_id');
		$express_company_id = intval($express_company_id);
		if ($express_company_id && $show_express_company_status)
		{
			$where .= ' AND express_company_id = ' . $express_company_id;
		}

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

		//收货人
		$consignee = $this->_request('consignee');
		if ($consignee)
		{
			$where .= ' AND consignee LIKE "%' . $consignee . '%"';
		}

		/*订单添加时间begin*/
		//起始时间
		$start_time = $this->_request('start_time');
		$start_time = str_replace('+', ' ', $start_time);
		$start_time = strtotime($start_time);
		#echo $start_time;
		if ($start_time)
		{
			$where .= ' AND tp_order.addtime >= ' . $start_time;
		}

		//结束时间
		$end_time = $this->_request('end_time');
		$end_time = str_replace('+', ' ', $end_time);
		$end_time = strtotime($end_time);
		if ($end_time)
		{
			$where .= ' AND tp_order.addtime <= ' . $end_time;
		}
		/*订单添加时间end*/
		#echo $where;
		//重新赋值到表单
		$this->assign('order_sn', $order_sn ? $order_sn : '');
		$this->assign('state', $state);
		$this->assign('express_company_id', $express_company_id ? $express_company_id : '');
		$this->assign('item_name', $item_name);
		$this->assign('consignee', $consignee);
		$this->assign('start_time', $start_time ? $start_time : '');
		$this->assign('end_time', $end_time ? $end_time : '');
		$this->assign('show_handling_status', $show_handling_status);
		$this->assign('show_express_company_status', $show_express_company_status);
		$this->assign('order_status', $order_status);

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $order_sn ? '/order_sn/' . $order_sn : '';
		$redirect .= $item_name ? '/item_name/' . $item_name : '';
		$redirect .= $consignee ? '/consignee/' . $consignee : '';
		$redirect .= $start_time ? '/start_time/' . $start_time : '';
		$redirect .= $end_time ? '/end_time/' . $end_time : '';
		$redirect .= $order_status ? '/order_status/' . $order_status : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

	/**
	 * 获取订单列表，公共方法
	 * @author wzg
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
	 * @return void
	 * @todo 获取订单列表，公共方法
	 */
	function order_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();
        $where .= $this->queryWhere;
		$order_obj = new OrderModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $order_obj->getOrderNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$order_obj->setStart($Page->firstRow);
        $order_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$order_list = $order_obj->getOrderList('', $where, ' addtime DESC');
        //echo $order_obj->getLastSql();die;
		$order_list = $order_obj->getListData($order_list);

		$this->assign('order_list', $order_list);
		#echo "<pre>";
		#print_r($order_list);
		#echo "</pre>";
		#echo $order_obj->getLastSql();

		//获取物流公司列表
		$shipping_company_obj = new ShippingCompanyModel();
		$shipping_company_list = $shipping_company_obj->getShippingCompanyList();
		$this->assign('express_company_list', $shipping_company_list);

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/DcpOrder/get_order_list.html');
	}

	/**
	 * 获取待付款订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_pre_pay_order_list()
	{
		$this->order_list('isuse = 1 and order_status = ' . OrderModel::PRE_PAY, '待付款的订单', 'pre_pay');
	}

	/**
	 * 获取退货受理中订单
	 * @author wsq 2015-04-28
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_accepted_refund_order_list()
	{
		$this->order_list('isuse = 1 and order_status = ' . OrderModel::REFUND_CONFIRMD, '退货受理中', 'refund_confirmd');
	}

	/**
	 * 获取 退货商品寄送中的订单
	 * @author wsq 2015-04-28
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_delivering_refund_order_list()
	{
		$this->order_list('isuse = 1 and order_status = ' . OrderModel::REFUND_DELIVEING, '退货商品寄送中的订单', 'REFUND_DELIVEING');
	}

	/**
	 * 获取待发货订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_pre_deliver_order_list()
	{
		$this->order_list('isuse = 1 AND order_status = ' . OrderModel::PAYED, '待发货的订单', 'PAYED');
	}

	/**
	 * 获取已发货订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_delivered_order_list()
	{
		$this->order_list('isuse = 1 AND order_status = ' . OrderModel::DELIVERED, '已发货的订单', 'DELIVERED');
	}

	/**
	 * 获取已确认订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_confirmed_order_list()
	{
		$this->order_list('isuse = 1 AND order_status = ' . OrderModel::CONFIRMED, '已确认的订单', 'CONFIRMED');
	}

	/**
	 * 获取已取消/关闭订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_canceled_order_list()
	{
		$this->order_list('isuse = 1 AND order_status = ' . OrderModel::CANCELED, '已取消/关闭的订单', 'CANCELED');
	}

	/**
	 * 获取所有订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_all_order_list()
	{
		$this->order_list('isuse = 1', '所有订单', 'ALL');
	}

	/**
	 * 查看订单详情
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 从地址栏获取订单ID，调用获取订单模型的getOrderInfo方法获取订单信息
	 */
	public function order_detail()
	{
		//接收订单ID，验证订单ID有效性
		$redirect = $this->_get('redirect');
		$redirect = $redirect ? url_jiemi($redirect) : U('/DcpOrder/get_pre_pay_order_list');
		$order_id = $this->_get('order_id');
		$order_id = intval($order_id);

		//根据$redirect获取$action_name，并将当前标记的菜单编号改为$action_name对应的菜单编号
		$path_info = explode('/', $redirect);

		if (!$order_id)
		{
			$this->error('订单号不存在', $redirect);
		}

		//调用订单模型的getOrderInfo获取订单信息
		$order_obj = new OrderModel($order_id);
		#echo $order_obj->list_fields;

		try
		{
			$order_info = $order_obj->getOrderInfo($order_obj->list_fields . ', weight, system_discount_amount, user_address_id, user_remark, express_number, express_company_id, user_id');
		}
		catch (Exception $e)
		{
			$this->error('无效的订单号', $redirect);
		}
		#echo "<pre>";
		#print_r($order_info);
		#echo "</pre>";

		//调用订单商品模型获取订单商品列表
		$order_item_obj = new OrderItemModel($order_id);
		$item_list = $order_item_obj->getOrderItemList();

		//遍历商品，获取商品的SKU，计算单品促销后的价格
		foreach ($item_list AS $key => $item_info)
		{
			//获取商品SKU
			if ($item_info['item_sku_price_id'] && !$item_info['property'])
			{
				//调用商品SKU模型获取SKU规格
				$item_sku_obj = new ItemSkuModel($item_info['item_sku_price_id']);
				$item_sku_info = $item_sku_obj->getSkuInfo($item_info['item_sku_price_id'], 'property_value1, property_value2');
				$property = '';
				if ($item_sku_info)
				{
                    $PropValue   = D('PropertyValue');
                    $prop_value1 = $PropValue->getPropertyValueField($item_sku_info['property_value1'], 'property_value');
                    $prop_value2 = $PropValue->getPropertyValueField($item_sku_info['property_value2'], 'property_value');
					$property .= $prop_value1 ? $prop_value1 : '   ';
					$property .= $prop_value2 ? '，' . $prop_value2 : '';
				}
				$item_list[$key]['property'] = $property;
			}
			else
			{
				$item_list[$key]['property'] = '';
			}
		}

        # wsq added
        # 获取加盟商资料
		$fields    = 'username, realname, mobile, email, qq';
		$user_obj  = new UserModel($order_info['user_id']);
		$user_info = $user_obj->getUserInfo($fields);

        $this->assign('user_info', $user_info);

        # 获取收货地址
        $address_obj    = new UserAddressModel();
        $address_info   = $address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id']);
        $address_detail = $address_obj->getAddressDetail($address_info);

        $this->assign('user_address_info', $address_info);
        $this->assign('address_detail',    $address_detail);

        # wsq added 2015-05-01
        # 获取物流信息
        $express_obj  = new ShippingCompanyModel();
        $express_info = $express_obj->getShippingCompanyInfoById($order_info['express_company_id']);
        $this->assign('express_company_name', $express_info['shipping_company_name']);

		//将订单状态转化成文字
		$order_info['order_status_num'] = $order_info['order_status'];
		$order_info['order_status'] = $order_obj->convertOrderStatus($order_info['order_status']);
		$this->assign('order_info', $order_info);
		$this->assign('item_list', $item_list);
		$this->assign('order_type', $order_type);

		//获取订单状态变化明细
		$order_log_list = $order_obj->getOrderLogList();
		$this->assign('order_log_list', $order_log_list);

		//获取礼品信息
#		$gift_list = $order_obj->getGiftList();
#		$this->assign('gift_list', $gift_list);
		#echo "<pre>";
		#print_r($gift_list);
		#echo "</pre>";

		$this->assign('head_title', '查看订单详情');
		$this->display();
	}

	/**
	 * 修改订单信息
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 从地址栏获取订单ID，调用获取订单模型的getOrderInfo方法获取订单信息
	 */
	public function edit_order()
	{
		$redirect = $this->_get('redirect');
		$redirect = $redirect ? url_jiemi($redirect) : U('/DcpOrder/get_pre_pay_order_list');
		$action = $this->_post('act');

		//根据$redirect获取$action_name，并将当前标记的菜单编号改为$action_name对应的菜单编号
		$path_info = explode('/', $redirect);
		if (isset($path_info[2]))
		{
			$menu_no = $this->get_in_menu(1, $path_info[2]);
			$this->assign("menu_no", $menu_no);
		}

		if ($action == 'submit')
		{
			/*接收表单数据，过滤后，调用订单模型的setOrderInfo方法设置订单信息后，调用saveOrderInfo方法保存到数据库*/
			$form_data = array();
			$form_data = array(
				array('name' => 'order_id', 'tip_name' => '订单号', 'data_type' => 'int'),
				array('name' => 'consignee', 'tip_name' => '收货人姓名'),
				array('name' => 'mobile', 'tip_name' => '联系手机', 'check_func' => 'check_mobile'),
				array('name' => 'province_id', 'tip_name' => '省份', 'data_type' => 'int'),
				array('name' => 'city_id', 'tip_name' => '城市', 'data_type' => 'int'),
				array('name' => 'area_id', 'tip_name' => '地区', 'data_type' => 'int', 'require' => 0),
				array('name' => 'address', 'tip_name' => '详细地址'),
				array('name' => 'express_company_id', 'tip_name' => '物流公司', 'data_type' => 'int'),
		//		array('name' => 'express_company_name', 'tip_name' => '物流公司'),
				array('name' => 'pay_amount', 'tip_name' => '订单实收款', 'data_type' => 'float'),
			);

			$form_data = $this->check_form_data($form_data);

			//调用订单模型的方法设置和保存订单信息
			$order_obj = null;
			if ($order_type == 3)
			{
				$order_obj = new VirtualStockOrderModel($form_data['order_id']);
			}
			else
			{
				$order_obj = new OrderModel($form_data['order_id']);
			}

			//查看数据库中是否存在该订单
			try
			{
				$order_arr = $order_obj->getOrderInfo($form_data['order_id']);
			}
			catch(Exception $e)
			{
				$this->error('对不起，该订单不存在！', $_SERVER['PATH_INFO']);
			}

            $user_address_obj  = new UserAddressModel($order_arr['user_address_id']);

            $user_address_data = array(
					'realname'				=> $form_data['consignee'],
					'mobile'				=> $form_data['mobile'],
					'province_id'			=> $form_data['province_id'],
					'city_id'				=> $form_data['city_id'],
					'area_id'				=> $form_data['area_id'],
                    'address'				=> $form_data['address'],
                );

            $user_address_obj->editUserAddress($user_address_data);

			//组织数组
			$order_arr = array();
			if ($order_type == 3)
			{
				$order_arr = array(
					'pay_amount'			=> $form_data['pay_amount']
				);
			}
			else
			{
				$order_arr = array(
					'express_company_id'	=> $form_data['express_company_id'],
					'express_fee'			=> $form_data['express_fee'],
					'express_number'		=> $this->_post('express_number'),
					'pay_amount'			=> $form_data['pay_amount']
				);
			}
			$order_obj->setOrderInfo($order_arr);
			$order_obj->saveOrderInfo();


			
			$this->success('恭喜您，订单信息修改成功！');
		}

		//接收订单ID，验证订单ID有效性
		$order_id = $this->_get('order_id');
		$order_id = intval($order_id);
		if (!$order_id)
		{
			$this->error('订单号不存在', $redirect);
		}

		//调用订单模型的getOrderInfo获取订单信息
		$order_obj = null;
		if ($order_type == 3)
		{
			$order_obj = new VirtualStockOrderModel($order_id);
		}
		else
		{
			$order_obj = new OrderModel($order_id);
		}
		#echo $order_obj->list_fields;
		try
		{
			//@#$加入pay_amount
			#$order_info = $order_obj->getOrderInfo($order_obj->list_fields . ', weight, discount_amount, address, mobile, user_remark, pay_amount, admin_remark, express_number, express_company_id, province_id, city_id, area_id, user_id');
			$order_info = $order_obj->getOrderInfo($order_obj->list_fields . ', weight, system_discount_amount, user_address_id, user_remark, express_number, express_company_id, user_id');
		}
		catch (Exception $e)
		{
			$this->error('无效的订单号', $redirect);
		}
		#echo "<pre>";
		#print_r($order_info);
		#echo "</pre>";

		//调用订单商品模型获取订单商品列表
		$order_item_obj = new OrderItemModel($order_info['order_id']);
		$item_list = $order_item_obj->getOrderItemList();

		//遍历商品，获取商品的SKU，计算单品促销后的价格
		foreach ($item_list AS $key => $item_info)
		{
			//获取商品SKU
			if ($item_info['item_sku_price_id'] && !$item_info['property'])
			{
				//调用商品SKU模型获取SKU规格
				$item_sku_obj = new ItemSkuModel($item_info['item_sku_price_id']);
				$item_sku_info = $item_sku_obj->getSkuInfo($item_info['item_sku_price_id'], 'property_value1, property_value2');
				$property = '';
				if ($item_sku_info)
				{
                    $PropValue   = D('PropertyValue');
                    $prop_value1 = $PropValue->getPropertyValueField($item_sku_info['property_value1'], 'property_value');
                    $prop_value2 = $PropValue->getPropertyValueField($item_sku_info['property_value2'], 'property_value');
					$property .= $prop_value1 ? $prop_value1 : '   ';
					$property .= $prop_value2 ? '，' . $prop_value2 : '';
				}
				$item_list[$key]['property'] = $property;
			}
			else
			{
				$item_list[$key]['property'] = '';
			}
		}
		#echo "<pre>";
		#print_r($item_list);
		#echo "</pre>";

        # 获取收货信息
        $user_address_obj   = new UserAddressModel();
        $user_address_info  = $user_address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id']);
        $this->assign('user_address_info', $user_address_info);
		//地区赋值
		$this->assign('province_id', $user_address_info['province_id']);
		$this->assign('city_id',     $user_address_info['city_id']);
		$this->assign('area_id',     $user_address_info['area_id']);
		$this->assign('address',     $user_address_info['address']);

		//将订单状态转化成文字
		$order_info['order_status_num'] = $order_info['order_status'];
		$order_info['order_status'] = $order_obj->convertOrderStatus($order_info['order_status']);


		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();
		$this->assign('province_list', $province_list);

		//获取物流公司列表
		$shipping_company_obj = new ShippingCompanyModel();
		$shipping_company_list = $shipping_company_obj->getShippingCompanyList();
		$this->assign('express_company_list', $shipping_company_list);

		//根据user_id获取分销商信息
		$user_obj = new UserModel($user_info['user_id']);
		$fields = 'username, realname, mobile, email,address';
		$user_info = $user_obj->getUserInfo($fields);

		$this->assign('user_info', $user_info);
		$this->assign('order_info', $order_info);
		$this->assign('item_list', $item_list);
		$this->assign('order_type', $order_type);
		$this->assign('head_title', '修改订单信息');

		$this->display();
	}

	/**
	 * 批量验证、过滤表单数据
	 * @author wzg
	 * @param array $form_data 要验证的数据项列表
	 * @return array $check_form_data 返回过滤后的数据项列表
	 * @todo 遍历传入的表单项，对每一项进行数据验证和过滤，最后返回过滤后的数据项列表
	 */
	private function check_form_data($form_data)
	{
		$url = $_SERVER['PATH_INFO'];
		$check_form_data = array();
		//遍历数据项
		foreach ($form_data AS $key => $value)
		{
			//验证是否为空
			$form_item = $this->_post($value['name']);
			if (!isset($value['require']) && $form_item == '')
			{
				$this->error('对不起，' . $value['tip_name'] . '不能为空！', $url);
			}

			//验证数据类型
			if ($form_item && isset($value['data_type']) && $value['data_type'] == 'int')
			{
				$form_item = intval($form_item);
				if (!$form_item)
				{
					$this->error('对不起，' . $value['tip_name'] . '必须是整数！', $url);
				}
			}
			elseif ($form_item && isset($value['data_type']) && $value['data_type'] == 'float')
			{
				$form_item = $form_item == 0.00 ? floatval(0.00) : floatval($form_item);
				if (!is_float($form_item))
				{
					$this->error('对不起，' . $value['tip_name'] . '必须是小数！', $url);
				}
			}

			//个性化验证，通过check_func指定的方法验证
			if ($form_item && $value['check_func'] && !$this->$value['check_func']($form_item))
			{
				$this->error('对不起，' . $value['tip_name'] . '格式有误！', $url);
			}

			$check_form_data[$value['name']] = $form_item;
		}

		return $check_form_data;
	}

	function check_mobile($mobile)
	{
		return preg_match("/^1[3|5|8]{1}[0-9]{9}$/",$mobile);
	}

	/**
	 * 导出历史订单
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 获取表单数据，组成数组后作为订单模型getOrderListByQueryString方法的导出历史订单
	 */
	public function order_export()
	{
		$act = $this->_post('act');
	
		if ($act == 'export')
		{
			//初始化SQL查询的where子句
			$where = '1';

			//订单状态
			$order_status = $this->_post('order_status');
			if ($order_status)
			{
				$where .= ' AND order_status = ' . intval($order_status);
			}

			/*订单添加时间begin*/
			//起始时间
			$start_time = $this->_request('start_time');
			$start_time = strtotime($start_time);
			if ($start_time)
			{
				$where .= ' AND addtime >= ' . $start_time . '';
			}

			//结束时间
			$end_time = $this->_request('end_time');
			$end_time = strtotime($end_time);
			if ($end_time)
			{
				$where .= ' AND addtime <= ' . $end_time . '';
			}

            $where .= $this->queryWhere;
			/*订单添加时间end*/

			//初始化订单对象
			$order_obj = new OrderModel();
			$order_obj->setStart(0);
			$order_obj->setLimit(1000);

			//@#$加入pay_amount
			#$fields = 'order_id, order_sn, pay_amount, total_amount, system_discount_amount, item_gifts, order_gifts, order_type, source, order_status, consignee, address, mobile, express_company_name, express_number, express_fee, weight, payway_name, addtime, pay_time, user_remark, admin_remark';
			#$fields = 'order_id, order_sn, pay_amount, total_amount, system_discount_amount,  source, order_status,  express_number, express_fee, weight, payway, addtime, pay_time, user_remark';
			$arr = $order_obj->getOrderList('', $where);
            $arr = $order_obj->getListData($arr);
            #echo "<pre>";
            #var_dump($arr);

			//系统名称
			$system_name = $GLOBALS['config_info']['shop_name'];

			//引入PHPExcel类库
            vendor('Excel.PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("360shop")
                    ->setLastModifiedBy("360shop")
                    ->setTitle($system_name . "订单报表")
                    ->setSubject($system_name . "订单报表")
                    ->setDescription($system_name . "订单报表")
                    ->setKeywords($system_name . ",订单,报表")
                    ->setCategory($system_name . "订单报表");

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet(0)->setTitle($system_name . '订单报表');          //标题
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);      //单元格宽度
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);      //设置字体大小
            $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '订单号');
            $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '商品总金额');
            $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '商品折扣');
            $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '订单应付金额');
            $objPHPExcel->getActiveSheet(0)->setCellValue('E1', '订单状态');
            $objPHPExcel->getActiveSheet(0)->setCellValue('F1', '收货人姓名');
            $objPHPExcel->getActiveSheet(0)->setCellValue('G1', '收货地址');
            $objPHPExcel->getActiveSheet(0)->setCellValue('H1', '收货人手机');
            $objPHPExcel->getActiveSheet(0)->setCellValue('I1', '物流公司名称');
            $objPHPExcel->getActiveSheet(0)->setCellValue('J1', '物流单号');
            $objPHPExcel->getActiveSheet(0)->setCellValue('K1', '物流费用');
            $objPHPExcel->getActiveSheet(0)->setCellValue('L1', '商品总重量');
            $objPHPExcel->getActiveSheet(0)->setCellValue('M1', '支付方式');
            $objPHPExcel->getActiveSheet(0)->setCellValue('N1', '下单时间');
            $objPHPExcel->getActiveSheet(0)->setCellValue('O1', '支付时间');
            $objPHPExcel->getActiveSheet(0)->setCellValue('P1', '用户留言');

            //每行数据的内容
            foreach ($arr as  $value) {
                static $i  = 2;
               
                //订单号
                $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, "'" . $value['order_sn']);

                //商品总金额
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $value['total_amount']);

                //商品总成本价
                $objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $value['system_discount_amount']);

                //商品总折扣
                $objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $value['pay_amount']);

                //礼品
				//$gifts = array_merge(unserialize($value['item_gifts']), unserialize($value['item_gifts']));
				//$gift_name_list = '';
				//foreach ($gifts AS $gift_info)
				//{
				//	if ($gift_info['gift_name'])
				//	{
				//		$gift_name_list .= $gift_info['gift_name'] . ', ';
				//	}
				//}
				//unset($arr[$i]['item_gifts']);
				//unset($arr[$i]['order_gifts']);
				//$gift_name_list = $gift_name_list ? substr($gift_name_list, 0, -1) : '';
                //$objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $gift_name_list);

                //订单状态
				$order_status = $order_obj->convertOrderStatus($value['order_status']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $order_status);

                //收货人姓名
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, $value['consignee']);

                //收货地址
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $value['address']);

                //收货人手机
                $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $value['mobile']);

                //物流公司名称
                $objPHPExcel->getActiveSheet(0)->setCellValue('I' . $i, $value['express_company_name']);

                //物流单号
                $objPHPExcel->getActiveSheet(0)->setCellValue('J' . $i, $value['express_number']);

                //物流费用
                $objPHPExcel->getActiveSheet(0)->setCellValue('K' . $i, $value['express_fee']);

                //商品总重量
                $objPHPExcel->getActiveSheet(0)->setCellValue('L' . $i, $value['weight']);

                //支付方式
                $objPHPExcel->getActiveSheet(0)->setCellValue('M' . $i, $value['payway']);

                //下单时间
                $objPHPExcel->getActiveSheet(0)->setCellValue('N' . $i, date('Y-m-d H:i:s', $value['addtime']));

                //支付时间
                $objPHPExcel->getActiveSheet(0)->setCellValue('O' . $i, date('Y-m-d H:i:s', $value['pay_time']));

                //用户留言
                $objPHPExcel->getActiveSheet(0)->setCellValue('P' . $i, $value['user_remark']);

                //管理员留言
                //$objPHPExcel->getActiveSheet(0)->setCellValue('R' . $i, $value['admin_remark']);

                $i++;
            }
            
            //直接输出文件到浏览器下载
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="订单报表_' . date("YmdHis") . '.xls"');
            header('Cache-Control: max-age=0');
            ob_clean(); //关键
            flush(); //关键
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
		}

		$this->assign('head_title', '导出历史订单');
		$this->display();
	}

	/**
	 * 获取订单列表，公共方法
	 * @author wzg
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
	 * @return void
	 * @todo 获取订单列表，公共方法
	 */
	private function refund_change_order_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();

        //只查看自已所管理的店订单
        $order_ids = M('Order')->field('order_id')->where('true' . $this->queryWhere)->select();
        if ($order_ids) {
            $order_id_list = array();
            foreach ($order_ids AS $k=>$v) array_push($order_id_list, $v['order_id']);
            $order_ids = implode(',', $order_id_list);
            if ($order_ids) {
                $where .= ' AND tp_order.order_id IN (' . $order_ids . ')';
            } else {
                $where .= ' AND false';
            }
        } else {
            $where .= ' AND false';
        }

		$item_refund_change_obj = new ItemRefundChangeModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $item_refund_change_obj->getItemRefundChangeNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$item_refund_change_obj->setStart($Page->firstRow);
        $item_refund_change_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$order_list = $item_refund_change_obj->getItemRefundChangeList('', $where, ' addtime DESC');
        //echo $item_refund_change_obj->getLastSql();
		#$order_list = $item_refund_change_obj->getListData($order_list);

		$this->assign('order_list', $order_list);
		#echo "<pre>";
		#print_r($order_list);
		#echo "</pre>";
		#echo $item_refund_change_obj->getLastSql();

		//获取物流公司列表
		$shipping_company_obj = new ShippingCompanyModel();
		$shipping_company_list = $shipping_company_obj->getShippingCompanyList();
		$this->assign('express_company_list', $shipping_company_list);

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/DcpOrder/get_refund_change_order_list.html');
	}

	/**
	 * 获取待退款订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_pre_refund_order_list()
	{
		$this->refund_change_order_list('operate_type = 1 AND state = 0 AND tp_item_refund_change.isuse = 1', '待退款订单', 'PRE_REFUND');
	}

	/**
	 * 获取所有退款订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_refunded_order_list()
	{
		$this->refund_change_order_list('operate_type = 1 AND tp_item_refund_change.isuse = 1', '所有退款订单', 'REFUNDED');
	}

	/**
	 * 获取所有换货订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_changed_order_list()
	{
		$this->refund_change_order_list('operate_type = 2 AND tp_item_refund_change.isuse = 1', '所有换货订单', 'CHANGED');
	}

	/**
	 * 获取待换货订单列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单数据，验证数据有效性，组成入参，调用订单模型的方法获取订单列表
	 */
	public function get_pre_change_order_list()
	{
		$this->refund_change_order_list('operate_type = 2 AND state = 0 AND tp_item_refund_change.isuse = 1', '待换货订单', 'PRE_CHANGE');
	}

	/**
	 * 退换货申请详情查看
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 调用订单模型的getOrderInfo方法获取订单信息，退换货模型的getRefundChangeInfo方法获取退货申请信息
	 */
	public function order_refund_change_apply_detail()
	{
		$order_id = $this->_get('order_id');
		$order_id = intval($order_id);
		$redirect = $this->_get('redirect');
		$redirect = $redirect ? url_jiemi($redirect) : U('/DcpOrder/get_pre_refund_order_list');
		if (!$order_id)
		{
			$this->error('', $_SERVER['PATH_INFO']);
		}

		//根据$redirect获取$action_name，并将当前标记的菜单编号改为$action_name对应的菜单编号
		//$path_info = explode('/', $redirect);
		//if (isset($path_info[2]))
		//{
		//	$action_name = $path_info[2];
		//	$menu_no = $this->get_in_menu(3, $action_name);
		//	$this->assign("menu_no", $menu_no);
		//}

		//订单退货申请信息
		$item_refund_change_id = $this->_get('item_refund_change_id');
		$item_refund_change_obj = new ItemRefundChangeModel($item_refund_change_id);
		$refund_apply_info = $item_refund_change_obj->getRefundChangeInfo();
		if (!$refund_apply_info)
		{
			$this->error('无效的订单号1', $redirect);
		}
		$refund_apply_info['item_ids'] = explode(',', $refund_apply_info['item_ids']);
		$refund_apply_info['item_nums'] = explode(',', $refund_apply_info['item_nums']);
		
		$refund_apply_info['status'] = $item_refund_change_obj->convertApplyState($refund_apply_info['status']);
		$this->assign('refund_apply_info', $refund_apply_info);
		$this->assign('operate_type_name', $refund_apply_info['operate_type'] == 1 ? '退款' : '换货');

		//调用订单模型的getOrderInfo获取订单信息
		$order_obj = new OrderModel($order_id);
		#echo $order_obj->list_fields;
		try
		{
			$order_info = $order_obj->getOrderInfo($order_obj->list_fields . ', weight, system_discount_amount, user_remark, express_number, express_company_id, user_id');
		}
		catch (Exception $e)
		{
            echo $order_obj->getLastSql();die;
			$this->error('无效的订单号', $redirect);
		}
		
		//调用分销商模型获取分销商信息
		$fields = 'username, realname, mobile, email';
		$user_obj = new UserModel($order_info['user_id']);
		$user_info = $user_obj->getUserInfo($fields);
		#echo "<pre>";
		#print_r($user_info);
		#echo "</pre>";

		//调用订单商品模型获取订单商品列表
		$order_item_obj = new OrderItemModel($order_info['order_id']);
		$item_list = $order_item_obj->getOrderItemList();

		//遍历商品，获取商品的SKU，计算单品促销后的价格
		foreach ($item_list AS $key => $item_info)
		{
			//获取退款数量
			$find = false;
			foreach ($refund_apply_info['item_ids'] AS $k => $v)
			{
				if ($item_info['order_item_id'] == $v)
				{
					$find = true;
					break;
				}
			}
			$item_list[$key]['refund_number'] = $find ? $refund_apply_info['item_nums'][$k] : $item_info['number'];

			//获取商品SKU
			if ($item_info['item_sku_price_id'] && !$item_info['property'])
			{
				//调用商品SKU模型获取SKU规格
				$item_sku_obj = new ItemSkuModel($item_info['item_sku_price_id']);
				$item_sku_info = $item_sku_obj->getSkuInfo($item_info['item_sku_price_id'], 'property_value1, property_value2');
				$property = '';
				if ($item_sku_info)
				{
                    $PropValue   = D('PropertyValue');
                    $prop_value1 = $PropValue->getPropertyValueField($item_sku_info['property_value1'], 'property_value');
                    $prop_value2 = $PropValue->getPropertyValueField($item_sku_info['property_value2'], 'property_value');
					$property .= $prop_value1 ? $prop_value1 : '   ';
					$property .= $prop_value2 ? '，' . $prop_value2 : '';
				}
				$item_list[$key]['property'] = $property;
			}
			else
			{
				$item_list[$key]['property'] = '';
			}
		}
		#echo "<pre>";
		#print_r($item_list);
		#echo "</pre>";

		//将订单状态转化成文字
		$order_info['order_status'] = $order_obj->convertOrderStatus($order_info['order_status']);
		
		$this->assign('order_info', $order_info);
		$this->assign('user_info', $user_info);
		$this->assign('item_list', $item_list);

		$this->assign('head_title', '查看订单退款申请');
		$this->display();
	}

    /**
     * 订单按日统计
     */
    public function order_stat_by_day() 
    {
		$add_time = $this->_post('add_time');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($add_time)
		{
			$start_time = strtotime(date('Y-m-d', strtotime($add_time)));
			#$end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
			$end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
			$date = date('Y-m-d', strtotime($add_time));
		}
		else
		{
			//今天0点的时间戳
			$end_time = strtotime(date('Y-m-d', time())) + 86400;

			//昨天0点的时间戳
			#$start_time = strtotime(date('Y-m-d', time())) - 86400;
			$start_time = strtotime(date('Y-m-d', time()));
			$date  = date('Y-m-d', $start_time);
		}

		//获取订单统计数据
		$order_obj = new OrderModel();
		$order_stat_list = $order_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%H") AS hour, SUM(pay_amount) AS total_amount')->where('addtime >= ' . $start_time . ' AND addtime <= ' . $end_time . $this->queryWhere)->group('hour')->order('addtime DESC')->select();

		$new_order_stat_list = array();
		for ($i = 0; $i <= 24; $i ++)
		{
			$new_order_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($order_stat_list AS $key => $val)
		{
			$new_order_stat_list[intval($val['hour'])] = $val['total_amount'];
		}

		$this->assign('order_amount_stat_list', $new_order_stat_list);

		$this->assign('date', $date);
		#echo "<pre>";
		#print_r($order_stat_list);
		#print_r($new_uv_list);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '订单按日统计');
        $this->display();
    }

    /**
     * 订单按月统计
     */
    public function order_stat_by_month() 
    {
		$year = $this->_post('year');
		$month = $this->_post('month');
		$year = $year ? $year : date('Y');
		$month = $month ? $month : date('m');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($year && $month)
		{
			$this->assign('year', $year);
			$this->assign('month', $month);
			$start_time = mktime(0, 0, 0, $month, 1, $year);
			if ($month == 12)
			{
				$year ++;
				$month = 1;
			}
			else
			{
				$month ++;
			}

			$end_time = mktime(0, 0, 0, $month, 1, $year) - 1;
			$date = $year . '-' . date('m');
		}

		//获取订单统计数据
		$order_obj = new OrderModel();
		$order_stat_list = $order_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%d") AS day, SUM(pay_amount) AS total_amount, SUM(pay_amount - express_fee) AS profit')->where('addtime >= ' . $start_time . ' AND addtime <= ' . $end_time . $this->queryWhere)->group('day')->order('addtime DESC')->select();

		$new_order_stat_list = array();
		for ($i = 0; $i <= 30; $i ++)
		{
			$new_order_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($order_stat_list AS $key => $val)
		{
			$new_order_stat_list[intval($val['day'])] = $val['total_amount'];
		}

		$this->assign('order_amount_stat_list', $new_order_stat_list);

		$new_order_stat_list = array();
		for ($i = 0; $i <= 30; $i ++)
		{
			$new_order_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($order_stat_list AS $key => $val)
		{
			$new_order_stat_list[intval($val['day'])] = $val['profit'];
		}

		$this->assign('cost_amount_stat_list', $new_order_stat_list);
		$this->assign('date', $date);
		$this->assign('day_num', date('d', mktime(0,0,0,$month + 1,0,$year)));

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '订单按月统计');
        $this->display();
    }

    /**
     * 订单按年统计
     */
    public function order_stat_by_year() 
    {
		$year = $this->_post('year');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($year)
		{
			$start_time = mktime(0, 0, 0, 1, 1, $year);
			$end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
			$date = date('Y-m-d', strtotime($year));
		}
		else
		{
			$year = date('Y');
			$start_time = mktime(0, 0, 0, 1, 1, $year);
			$end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
		}
		$this->assign('year', $year);

		//获取订单统计数据
		$order_obj = new OrderModel();
		$order_stat_list = $order_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%m") AS month, SUM(pay_amount) AS total_amount, SUM(pay_amount -  express_fee) AS profit')->where('addtime >= ' . $start_time . ' AND addtime <= ' . $end_time . $this->queryWhere)->group('month')->order('addtime DESC')->select();

		$new_order_stat_list = array();
		for ($i = 0; $i <= 12; $i ++)
		{
			$new_order_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($order_stat_list AS $key => $val)
		{
			$new_order_stat_list[intval($val['month'])] = $val['total_amount'];
		}

		$this->assign('order_amount_stat_list', $new_order_stat_list);

		$new_order_stat_list = array();
		for ($i = 0; $i <= 12; $i ++)
		{
			$new_order_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($order_stat_list AS $key => $val)
		{
			$new_order_stat_list[intval($val['month'])] = $val['profit'];
		}

		$this->assign('cost_amount_stat_list', $new_order_stat_list);
		$this->assign('date', $date);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '订单按年统计');
        $this->display();
    }

    /**
     * 订单按年统计
     */
    public function order_stat_by_area() 
    {
        // 获取参数
        $type        = $this->_post('area');
        $province_id = intval($this->_post('province_id'));
        $city_id     = intval($this->_post('city_id'));
        $area_id     = intval($this->_post('area_id'));
        $big_area_id = intval($this->_post('big_area_id'));

        $type_str    = "全部类型";

        if ($type) {
            if ($type == 'small_area') {
                if (!$area_id && !$city_id && !$province_id) 
                    $this->error('请选择省市区', U('/DcpOrder/order_stat_by_area'));

                // 拼接城市信息
                $address_condition = ' AND province_id =  ' . $province_id;
                if ($city_id) $address_condition .= ' AND city_id = ' . $city_id;
                if ($area_id) $address_condition .= ' AND area_id = ' . $area_id;

                $type_str      = "区域";
                $city_obj      = M('address_city');
                $city_list     = $city_obj->field('city_id, city_name')->where('province_id = '. $province_id )->select();

                $area_obj      = new AreaModel();
                $area_list     = $area_obj->field('area_id, area_name')->where('city_id = '. $city_id )->select();

                $area_str      = $area_obj->getAreaString($province_id, $city_id, $area_id);

                $this->assign('province_id', $province_id);
                $this->assign('city_id', $city_id);
                $this->assign('area_id', $area_id);
                $this->assign('city_list', $city_list);
                $this->assign('area_list', $area_list);
                $this->assign('small_area', 1);

            } else if ($type == 'big_area') {
                if ($big_area_id<0) 
                    $this->error('请选择大区', U('/DcpOrder/order_stat_by_area'));

                $big_area_obj  = M('big_area');
                $area_info     = $big_area_obj->where('big_area_id = ' . $big_area_id)->find();
                $type_str      = '大区';
                $area_str      = $area_info['area_name'];
                $address_condition = " AND province_id IN (" . $area_info['province_ids'] . ")";
                $this->assign('big_area_id', $big_area_id);

            }
        }

        $area_obj   = new AreaModel();

		$year       = date('Y');
		$month      = date('m');
		$start_time = 0;
		$end_time   = 0;

		if ($year && $month)
		{
			$this->assign('year', $year);
			$this->assign('month', $month);
			$start_time = mktime(0, 0, 0, $month, 1, $year);
			if ($month == 12)
			{
				$year ++;
				$month = 1;
			}
			else
			{
				$month ++;
			}

			$end_time = mktime(0, 0, 0, $month, 1, $year) - 1;
			$date = $year . '-' . date('m');

		}
		$order_obj = new OrderModel();

		//获取订单统计数据
        // 获取本月统计
        $where  = 'tp_order.addtime >= ' . $start_time . ' AND tp_order.addtime <= ' . $end_time . $address_condition;
        $join   = 'tp_user_address AS address ON address.user_address_id = tp_order.user_address_id';
        $field  = 'DATE_FORMAT(FROM_UNIXTIME(tp_order.addtime), "%d") AS day, SUM(pay_amount) AS total_amount';
		$order_stat_list = $order_obj->field($field)->join($join)->where($where)->group('day')->order('tp_order.addtime DESC')->select();

        //获取上月统计
        $start_time_last_month = strtotime("-1 month", $start_time);
        $end_time_last_month   = strtotime("-1 month", $end_time);
        $where_last_month      = 'tp_order.addtime >= ' . $start_time_last_month . ' AND tp_order.addtime <= ' . $end_time_last_month . $address_condition;
        $order_stat_list_last_month = $order_obj->field($field)->join($join)->where($where_last_month)->group('day')->order('tp_order.addtime DESC')->select();

        #echo $order_obj->getLastSql();die;

		$new_order_stat_list   = array();
		$new_order_amount_list = array();

		for ($i = 0; $i <= 30; $i ++)
		{
			$new_order_stat_list[$i]   = 0;
            $new_order_amount_list[$i] = 0;
		}

		//组成数组
		foreach ($order_stat_list AS $key => $val)
		{
			$new_order_stat_list[intval($val['day'])]   = $val['total_amount'];
		}

		foreach ($order_stat_list_last_month AS $key => $val)
		{
			$new_order_amount_list[intval($val['day'])] = $val['total_amount'];
		}

		$this->assign('order_amount_stat_list', $new_order_stat_list);
		$this->assign('cost_amount_stat_list',  $new_order_amount_list);

		$this->assign('date', $date . ' ' . $area_str . '  ' .$type_str);
		$this->assign('day_num', date('d', mktime(0,0,0,$month + 1,0,$year)));

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '订单按月统计');

        //获取大区列表
        $big_area_obj  = M('big_area');
        $big_area_list = $big_area_obj->field('big_area_id, area_name')->select();
        $this->assign('big_area_list', $big_area_list);

        //获取省份列表
        $province      = M('address_province');
        $province_list = $province->field('province_id, province_name')->select();
        $this->assign('province_list', $province_list);

        $this->display();
    }

    //
    // 确认退货并退款；
    //
    public function confirm_refund_deliving(){
    
        $order_id      = intval($this->_post('order_id'));
        $refund_money  = $this->_post('refund_total');

		if ($order_id && $refund_money)
		{
            $order_obj  = new OrderModel($order_id);
			$order_info = $order_obj->getOrderInfo('user_id,order_sn');

            if (!$order_info) exit('failure');

            $user_id    = $order_info['user_id'];
            if (!$user_id) exit('failure');

            //更新订单状态
            $order_obj->confirmRefundDeliving();
            //调整余额
            //增加user表的left_money字段，并往account表插入一条记录
            $change_type  = 8; //订单退款；
            $AccountModel = new AccountModel();
            $tag          = $AccountModel->addAccount($user_id, $change_type, $refund_money, $order_info['order_sn'] . '订单退款', '', '');

			echo $tag ? 'success' : 'failure';
			exit;
		}
		exit('failure');
        
    }

    // 发货
    // @author wsq
    public function deliver_order()
    {
		$order_id = I('post.order_id', 0, 'int');
		$express_company_id = I('post.express_company_id', 0, 'int');
		$express_number = I('post.express_number', NULL, 'htmlspecialchars');

		if ($order_id && $express_number && $express_company_id)
		{
			$order_obj = new OrderModel($order_id);
			$order_obj->deliverOrder($express_company_id, $express_number);
			exit('success');
		}
		exit('failure');
    }

/**
	 * 为某笔订单设置已线下收款
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID、交易单号、管理员备注，调用财务模型的addAcccount为该用户充值一笔与订单金额等值的预存款，再调用一次addAccount用于支付该笔订单，最后调用订单模型的payOrder方法改变订单状态
	 */
	public function offline_pay()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		$proof = $this->_post('proof');
		$admin_remark = $this->_post('admin_remark');

		if ($order_id && $proof && $admin_remark)
		{
			//调用订单模型的getOrderInfo方法获取订单信息
			$order_obj = new OrderModel($order_id);

			try
			{
				$order_info = $order_obj->getOrderInfo('user_id, pay_amount');
			}
			catch (Exception $e)
			{
				echo 'failure';
				exit;
			}
			
			$order_obj = new AccountModel();
			//为该用户充值一笔与订单金额等值的预存款
			$state1 = $order_obj->addAccount($order_info['user_id'], 2, floatval($order_info['pay_amount']), $admin_remark, 0, $proof);
            log_file($state1);
			//支付该笔订单
			$state2 = $order_obj->addAccount($order_info['user_id'], 5, floatval($order_info['pay_amount']) * -1, $admin_remark, $order_id, $proof);
            log_file($state2);

			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

    /**
	 * 删除订单
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 获取表单提交上来的订单ID，过滤有些性后，调用订单模型的cancelOrder方法删除订单
	 */
	public function delete_order()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		$order_type = $this->_post('order_type');
		$is_virtual_stock_order = (intval($order_type) == 3) ? true : false;

		if ($order_id)
		{
			$order_obj = null;
			if ($is_virtual_stock_order)
			{
				require_once('Lib/Model/VirtualStockOrderModel.class.php');
				$order_obj = new VirtualStockOrderModel($order_id);
			}
			else
			{
				require_once('Lib/Model/OrderModel.class.php');
				$order_obj = new OrderModel($order_id);
			}
			$order_obj->cancelOrder();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 批量删除订单
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 获取表单提交上来的订单ID列表，转化成数组，遍历之，对每一个订单调用订单模型的cancelOrder方法删除订单
	 */
	public function batch_delete()
	{
		$order_ids = $this->_post('order_ids');

		if ($order_ids)
		{
			$order_ids = explode(',', $order_ids);
			//调用订单模型的cancelOrder删除订单
			require_once('Lib/Model/OrderModel.class.php');
			$order_obj = null;
			foreach ($order_ids AS $key => $order_id)
			{
				if ($order_id)
				{
					$order_obj = new OrderModel($order_id);
					$order_obj->cancelOrder();
				}
			}
			
			echo 'success';
			exit;
		}

		echo 'failure';
		exit;
	}

    /**
	 * 通过退换货申请
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 从表单获取退换货申请ID，调用退换货模型的passItemRefundChangeApply方法设置通过
	 */
	public function passItemRefundChangeApply()
	{
		$item_refund_change_id = intval($this->_request('item_refund_change_id'));

		if ($item_refund_change_id)
		{
			
			//调用退换货模型的passItemRefundChangeApply方法设置通过
			 //require_once('Lib/Model/ItemRefundChangeModel.class.php');
			 $item_refund_change_obj = new ItemRefundChangeModel($item_refund_change_id);

			//通过退款状态更改
			$item_refund_change_obj->passItemRefundChangeApply();
			
			echo 'success';
			exit;
		}

		echo 'failure';
		exit;
	}

	/**
	 * 拒绝退换货申请
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 从表单获取退换货申请ID，调用退换货模型的refuseItemRefundChangeApply方法设置拒绝
	 */
	public function refuseItemRefundChangeApply()
	{
		$item_refund_change_id = $this->_post('item_refund_change_id');
		$item_refund_change_id = intval($item_refund_change_id);

		$admin_remark = $this->_post('admin_remark');

		if ($item_refund_change_id && $admin_remark)
		{
			//调用退换货模型的refuseItemRefundChangeApply方法设置拒绝
			require_once('Lib/Model/ItemRefundChangeModel.class.php');
			$item_refund_change_obj = new ItemRefundChangeModel($item_refund_change_id);
			$item_refund_change_obj->refuseItemRefundChangeApply($admin_remark);
			echo 'success';
			exit;
		}

		echo 'failure';
		exit;
	}
}

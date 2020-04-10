<?php
/**
 * 管理员订单管理异步处理控制器
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
class AcpOrderAjaxAction extends AcpAction {

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用父类的_initialize方法过滤非法操作
	 */
	public function AcpOrderAjaxAction()
	{
		parent::_initialize();
	}

	/**
	 * 删除订单商品
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取表单提交上来的订单商品ID，过滤有些性后，调用订单模型的deleteItem方法删除订单商品
	 */
	public function delete_item()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);
		$order_item_id = $this->_post('order_item_id');
		$order_item_id = intval($order_item_id);

		$order_type = $this->_post('order_type');
		$is_virtual_stock_order = (intval($order_type) == 3) ? true : false;

		if ($order_id && $order_item_id)
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
				$order_obj = new OrderItemModel($order_id);
			}
			$order_obj->deleteItem($order_item_id);
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 删除订单
	 * @author 姜伟
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
	 * @author 姜伟
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
	 * 获取管理员订单备注
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID，调用订单模型的getOrderInfo方法获取管理员订单备注信息
	 */
	public function get_order_remark()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		if ($order_id)
		{
			require_once('Lib/Model/OrderModel.class.php');
			$order_obj = new OrderModel($order_id);
			$order_info = $order_obj->getOrderInfo('admin_remark');
			if ($order_info)
			{
				echo $order_info['admin_remark'];
				exit;
			}
		}
		echo 'failure';
		exit;
	}

	/**
	 * 管理员给添加订单备注
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID和备注内容，调用订单模型的setOrderInfo设置订单备注信息后，调用saveOrderInfo方法保存订单信息
	 */
	public function remark_order()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		$remark = $this->_post('remark');

		$order_type = $this->_post('order_type');
		$is_virtual_stock_order = (intval($order_type) == 3) ? true : false;

		if ($order_id && $remark)
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
			$order_obj->setOrderInfo(array('admin_remark' => $remark));
			$order_obj->saveOrderInfo();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 修改订单商品总价格和物流费用
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID、订单商品总价格和订单物流费用，调用订单模型的setOrderInfo设置订单总价格和订单物流费用后，调用saveOrderInfo方法保存订单信息
	 */
	public function edit_price()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		//@#$加入pay_amount
		#$change_total_amount = $this->_post('change_total_amount');
		#$change_total_amount = floatval($change_total_amount);
		#$change_express_fee = $this->_post('change_express_fee');
		#$change_express_fee = floatval($change_express_fee);
		$change_pay_amount = $this->_post('change_pay_amount');

		#if ($order_id && is_float($change_total_amount) && is_float($change_express_fee))
		if ($order_id && is_numeric($change_pay_amount))
		{
			$order_obj = new OrderModel($order_id);
			$order_info = array(
				//@#$加入pay_amount
				#'total_amount'	=> $change_total_amount,
				#'express_fee'	=> $change_express_fee
				'pay_amount'	=> $change_pay_amount
			);
			$order_obj->setOrderInfo($order_info);
			$order_obj->saveOrderInfo();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 关闭订单
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID，调用订单模型的cancelOrder关闭订单
	 */
	public function cancel_order()
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
	 * 获取物流公司列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取物流公司列表，并转化成下拉选择的dom返回给前台
	 */
	public function get_express_company_list()
	{
		$express_company_id = $this->_post('express_company_id');
		$express_company_id = intval($express_company_id);

		if ($express_company_id)
		{
			require_once('Lib/Model/ShippingCompanyModel.class.php');
			$shipping_company_obj = new ShippingCompanyModel();
			$shipping_company_list = $shipping_company_obj->getShippingCompanyList();

			//转化成下拉选择的dom
			$html = '<div class="formitems inline">
						<label class="fi-name">
							<span class="colorRed">*</span>选择物流公司：</label>
						<div class="form-controls">
						<select name="change_express_company" id="change_express_company">
							<option value="" selected>--选择物流公司--</option>';
			foreach ($shipping_company_list AS $key => $value)
			{
				$html .= '<option value="' . $value['shipping_company_id'] . '"';
				if ($express_company_id == $value['shipping_company_id'])
				{
					$html .= ' selected';
				}
				$html .= '>';
				$html .= $value['shipping_company_name'];
				$html .= '</option>';
			}
			$html .= '</select>
					<span class="fi-help-text hide"></span>
				</div>
			</div>';
			echo $html;
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 修改订单物流公司
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID、物流公司ID和物流公司名称，调用订单模型的setOrderInfo设置订单物流公司名称和物流公司ID后，调用saveOrderInfo方法保存订单信息
	 */
	public function edit_express_company()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		$express_company_id = $this->_post('change_express_company_id');
		$express_company_id = intval($express_company_id);
		$express_company_name = $this->_post('change_express_company_name');

		if ($order_id && $express_company_id && $express_company_name)
		{
			require_once('Lib/Model/OrderModel.class.php');
			$order_obj = new OrderModel($order_id);
			$order_info = array(
				'express_company_id'	=> $express_company_id,
				'express_company_name'	=> $express_company_name
			);
			$order_obj->setOrderInfo($order_info);
			$order_obj->saveOrderInfo();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 修改发货订单（物流单号）
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID、物流单号，调用订单模型的setOrderInfo设置物流单号后，调用saveOrderInfo方法保存订单信息
	 */
	public function edit_express_number()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		$express_number = $this->_post('change_express_number');

		if ($order_id && $express_number)
		{
			require_once('Lib/Model/OrderModel.class.php');
			$order_obj = new OrderModel($order_id);
			$order_info = array(
				'express_number'	=> $express_number
			);
			$order_obj->setOrderInfo($order_info);
			$order_obj->saveOrderInfo();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 为某笔订单设置已线下收款
	 * @author 姜伟
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
			$order_obj->addAccount($order_info['user_id'], 2, floatval($order_info['pay_amount']), $admin_remark, 0, $proof);
			//支付该笔订单
			$order_obj->addAccount($order_info['user_id'], 5, floatval($order_info['pay_amount']) * -1, $admin_remark, $order_id, $proof);

			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 根据订单号列表获取订单商品列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID列表，调用订单商品模型的getOrderItemList方法获取商品列表，并以json形式返回
	 */
	public function get_order_item_list()
	{
		$order_ids = $this->_post('order_ids');
		//排序
		$order_type = $this->_post('order_type');

		if ($order_ids)
		{
			//调用订单商品模型的getOrderItemList方法获取商品信息列表
			require_once('Lib/Model/OrderItemModel.class.php');
			$order_item_obj = new OrderItemModel();
			//排序
			$order = $order_type == 1 ? 'item_id DESC' : 'order_id DESC';
			$item_list = $order_item_obj->getOrderItemList('order_id, item_name, item_sn, item_sku_price_id, number, from_virtual_stock', 'order_id IN (' . $order_ids . ')', $order);
			#echo $order_item_obj->getLastSql();die;
			if (!$item_list)
			{
				echo 'failure';
				exit;
			}
			
			foreach ($item_list AS $key => $item_info)
			{
				//获取商品SKU
				if ($item_info['item_sku_price_id'])
				{
					//调用商品SKU模型获取SKU规格
					require_once('Lib/Model/ItemSkuModel.class.php');
					$item_sku_obj = new ItemSkuModel($item_info['item_sku_price_id']);
					$item_sku_info = $item_sku_obj->getSkuInfo($item_info['item_sku_price_id'], 'property_value1, property_value2');
					$property = '';
					if ($item_sku_info)
					{
						$property .= $item_sku_info['property_value1'] ? $item_sku_info['property_value1'] : '   ';
						$property .= $item_sku_info['property_value2'] ? $item_sku_info['property_value2'] : '';
					}
					$item_list[$key]['property'] = $property;
				}
				else
				{
					$item_list[$key]['property'] = '';
				}
			}
			echo json_encode($item_list);
			exit;
		}

		echo 'failure';
		exit;
	}

	/**
	 * 根据订单号列表获取订单信息、订单商品列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID列表，调用订单模型的getOrderInfo方法获取订单信息、订单商品模型的getOrderItemList方法获取商品列表，并以json形式返回
	 */
	public function get_deliver_info_list()
	{
		$order_ids = $this->_post('order_ids');
		//排序
		$order_type = $this->_post('order_type');

		if ($order_ids)
		{
			//调用订单商品模型的getOrderItemList方法获取商品信息列表
			require_once('Lib/Model/OrderItemModel.class.php');
			$order_item_obj = new OrderItemModel();
			//排序
			$order = $order_type == 1 ? 'item_id DESC' : 'order_id DESC';
			$item_list = $order_item_obj->getOrderItemList('order_id, item_name, item_sn, item_sku_price_id, number', 'order_id IN (' . $order_ids . ')', $order);
			#echo $order_item_obj->getLastSql();die;
			if (!$item_list)
			{
				echo 'failure';
				exit;
			}
			
			$order_id = 0;
			//订单计数器
			$i = -1;
			//订单商品计数器
			$j = -1;
			$deliver_info_list = array();
			foreach ($item_list AS $key => $item_info)
			{
				if ($order_id == 0 || $order_id != $item_info['order_id'])
				{
					$i ++;
					$j = -1;
					$order_id = $item_info['order_id'];
					//调用订单模型的getOrderInfo获取订单信息
					require_once('Lib/Model/OrderModel.class.php');
					$order_obj = new OrderModel($order_id);
					$order_info = $order_obj->getOrderInfo($order_obj->list_fields . ', weight, discount_amount, address, mobile, user_remark, admin_remark, express_number, express_company_id');
					$deliver_info_list[$i]['order_info'] = $order_info;
				}
				$j ++;

				$deliver_info_list[$i]['item_list'][$j] = $item_info;
				$deliver_info_list[$i]['order_info']['addtime'] = date('Y-m-d H:i:s', $order_info['addtime']);

				//获取商品SKU
				if ($item_info['item_sku_price_id'])
				{
					//调用商品SKU模型获取SKU规格
					require_once('Lib/Model/ItemSkuModel.class.php');
					$item_sku_obj = new ItemSkuModel($item_info['item_sku_price_id']);
					$item_sku_info = $item_sku_obj->getSkuInfo($item_info['item_sku_price_id'], 'property_value1, property_value2');
					$property = '';
					if ($item_sku_info)
					{
						$property .= $item_sku_info['property_value1'] ? $item_sku_info['property_value1'] : '   ';
						$property .= $item_sku_info['property_value2'] ? $item_sku_info['property_value2'] : '';
					}
					#$item_list[$key]['property'] = $property;
					$deliver_info_list[$i]['item_list'][$j]['property'] = $property;
				}
				else
				{
					#$item_list[$key]['property'] = '';
					$deliver_info_list[$i]['item_list'][$j]['property'] = '';
				}
			}
			echo json_encode($deliver_info_list);
			exit;
		}

		echo 'failure';
		exit;
	}

	/**
	 * 批量发货
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取订单ID列表，遍历之，对每一个订单调用订单模型的deliverOrder方法设置订单已发货
	 */
	public function batch_deliver()
	{
		$order_ids = $this->_post('order_ids');
		//起始物流单号
		$start_express_number = $this->_post('start_express_number');
		//切割后4位
		$start_express_number_tail = substr($start_express_number, -4, strlen($start_express_number));
		$start_express_number_tail = intval($start_express_number_tail);
		$start_express_number_head = substr($start_express_number, 0, -5);

		if ($order_ids)
		{
			$order_ids = explode(',', $order_ids);
			//调用订单模型的deliverOrder设置订单已发货
			require_once('Lib/Model/OrderModel.class.php');
			$order_obj = null;
			$i = 0;
			foreach ($order_ids AS $key => $order_id)
			{
				if ($order_id)
				{
					$express_number = $start_express_number_head . ($start_express_number_tail + $i);
					$order_obj = new OrderModel($order_id);
					$order_obj->setOrderInfo(array('express_number' => $express_number));
					$order_obj->deliverOrder();
					$i ++;
				}
			}
			
			echo 'success';
			exit;
		}

		echo 'failure';
		exit;
	}

	/**
	 * 设置退换货申请表中的退还金额
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从表单获取退换货申请ID和退还金额，调用退换货模型的setRefundMoney方法设置退换货金额
	 */
	public function set_refund_money()
	{
		$item_refund_change_id = $this->_post('item_refund_change_id');
		$item_refund_change_id = intval($item_refund_change_id);

		$refund_money = $this->_post('refund_money');
		$refund_money = floatval($refund_money);

		if ($item_refund_change_id && $refund_money)
		{
			//调用退换货模型的setRefundMoney方法设置退换货金额
			require_once('Lib/Model/ItemRefundChangeModel.class.php');
			$item_refund_change_obj = new ItemRefundChangeModel($item_refund_change_id);
			$item_refund_change_obj->setRefundMoney($refund_money);
			#echo $item_refund_change_obj->_sql();
			echo 'success';
			exit;
		}

		echo 'failure';
		exit;
	}

	/**
	 * 通过退换货申请
	 * @author 姜伟
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
	 * @author 姜伟
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

	/**
	 * 订单设置备货完成
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取表单提交上来的订单ID，过滤有些性后，调用订单模型的stockupOrder方法设置订单已备货
	 */
	public function stockup_order()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		if ($order_id)
		{
			$order_obj = new OrderModel($order_id);
			$order_obj->stockupOrder();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}

	/**
	 * 订单设置已发货
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取表单提交上来的订单ID，过滤有些性后，调用订单模型的deliverOrder方法设置订单已发货
	 */
	public function deliver_order()
	{
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		if ($order_id)
		{
			$order_obj = new OrderModel($order_id);
			$order_obj->deliverOrder();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
	}
	
	/**
	 * 批量打印快递单
	 *
	 * @author zhengzhen
	 * @todo AJAX异步批量生成快递单打印数据
	 *
	 */
	public function batch_shipping_print()
	{
		if($this->isAjax())
		{
			$printAction = $this->_get('print_action');
			$expressCompanyId = $this->_get('express_company_id');
			$orderId = $this->_get('order_id');
			$start = $this->_get('start');
			$shippingPrint = new ShippingPrintModel('real');
			switch($printAction)
			{
				case 'selected':
					$where = ' AND order_id IN (' . $orderId . ')';
					$result = $shippingPrint->batchShippingPrint($expressCompanyId, null, null, $where);
					$isFinish = 1;
					break;
				default:
					$this->_ajaxFeedback(0, null, '非法操作！');
			}
			
			if(!$result)
			{
				$this->_ajaxFeedback(0, null, '对不起，数据初始化失败，请稍后重试！');
			}
			$result['other']['START'] = $start;
			$result['other']['IS_FINISH'] = $isFinish;
			$this->_ajaxFeedback(1, $result);
		}
	}
}

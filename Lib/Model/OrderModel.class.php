<?php
/**
 * 订单模型
 * @access public
 * @author 姜伟
 * @Date 2014-09-04
 */
class OrderModel extends Model
{
    const MAIN_FACTORY=7000;
	/**
	 * 订单ID
	 */
    protected $orderId = 0;
    
	/**
	 * 订单信息数组
	 */
    protected $data = array();

	/**
	 * 订单商品对象
	 */
	protected $order_item_obj = null;

	/**
	 * 订单列表页的查询条件组合，where字句，默认为空
	 */
	public $where = '';

	/*常量定义begin*/

    /*
     * 0未付款，1已付款，2已发货，3已确认收货，4已取消, 5退款中, 6退款受理中，7退货商品寄送中，8 退款货物确认收货，9已退款
     * modified by wsq 2015-04-28
     */
	const PRE_PAY                 = 0; //未付款状态
	const PAYED                   = 1; //已付款状态/待发货
	const DELIVERED               = 2; //已发货状态
	const CONFIRMED               = 3; //已确认收货/已完成订单
	const CANCELED                = 4; //已取消/关闭
	const REFUNDING               = 5; //退款中
	const REFUND_CONFIRMD         = 6; //退款受理中
	const REFUND_DELIVEING        = 7; //退货寄送中
	const REFUND_DELIVER_CONFIRMD = 8; //退货确认收货
	const REFUNDED                = 9; //已退款

	const STOCKUPED               = 10; //已备货状态
	const CHANGING                = 11; //申请换货中
	const CHANGED                 = 12; //已换货
	//已评价
	const COMMENTED = 13;
	/*常量定义end*/

	/**
	 * 订单列表页的字段列表
	 */
    public $list_fields = 'order_id,order_sn, total_amount, pay_amount, payway, source, express_fee, order_status, addtime';

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param int $order_id
	 * @return void
	 * @todo 构造函数
	 */
	public function __construct($order_id)
    {
		$this->db(0);
		$this->orderId = $order_id;
		$this->tableName = C('DB_PREFIX') . 'order';
	}

	/**
	 * 设置订单状态
	 * @author 姜伟
	 * @param int $order_status
	 * @return void
	 * @todo 设置订单状态，同时执行新状态触发事件函数，记录订单操作日志
	 */
    public function setOrderState($order_status)
    {
        $order_status = intval($order_status);
		if (!$order_status) {
			return false;
		}

		switch($order_status)
		{
			case self::PRE_PAY:
				#echo '订单未付款';
				break;
			case self::PAYED:
				#echo '订单已付款，待备货';
				$this->payOrderEvent();
				break;
			case self::STOCKUPED:
				#echo '订单待发货';
				$this->stockupOrderEvent();
				break;
			case self::DELIVERED:
				#echo '订单已发货';
				$this->deliverOrderEvent();
				break;
			case self::REFUNDING:
				#echo '订单待退货';
				$this->refundApplyEvent();
				break;
			case self::REFUNDED:
				#echo '订单已退货';
				$this->passRefundApplyEvent();
				break;
			case self::CHANGING:
				#echo '订单待换货';
				$this->exchangeItemApplyEvent();
				break;
			case self::CHANGED:
				#echo '订单已换货';
				$this->passExchangeItemApplyEvent();
				break;
			case self::CONFIRMED:
				#echo '订单已确认';
				$this->data['confirm_time'] = time();#jx add 增加确认时间
				$this->confirmOrderEvent();
				break;
			case self::CANCELED:
				#echo '订单已取消，已关闭';
				$this->cancelOrderEvent();
				break;
            case self::REFUND_CONFIRMD:
                //$order_status = '退款受理中';
                $this->passRefundApplyConfirmdEvent();
                break;
            case self::REFUND_DELIVEING:
               // $order_status = '退货寄送中';
                break;
            case self::REFUND_DELIVER_CONFIRMD:
                //$order_status = '退货确认收货';
                break;
            case self::COMMENTED:
                break;
			default:
				break;
		}

		//获取当前订单状态
		$start_order_status = $this->getOrderState();

		//修改订单状态
		$this->__set('order_status', $order_status);
		$this->saveOrderInfo();

        // wsq new added
        // 同步订单状态到ERP系统
        $this->syncOrderStatus($this->getOrderId());

        log_file($this->getLastSql());

		//写日志
		$this->saveLog($start_order_status, $order_status, session('user_id'));
		return true;
    }


	/**
	 * 获取订单状态
	 * @author 姜伟
	 * @param void
	 * @return int $order_status
	 * @todo 若当前对象中已存在订单状态，直接返回，否则从数据库中取
	 */
    public function getOrderState()
    {
		$order_status = $this->__get('order_status');

		if (!empty($order_status))
		{
			return $order_status;
		}

		//从数据库中取
		$order_info = $this->getOrderInfo('order_status');
        //echo $this->getLastSql();die;

		$this->checkOrderInfoValid($order_info);

		return $order_info['order_status'];
    }
    
	/**
	 * 设置订单信息
	 * @author 姜伟
	 * @param array	$order_info	订单信息数组，一维
	 * @return void
	 * @todo 修改订单信息，写入订单日志表，并保存到数据库，若涉及了订单状态变更，执行触发的事件函数
	 */
    public function setOrderInfo($order_info)
    {
		//加入pay_amount
		#if (isset($order_info['pay_amount']))
		#{
		#	if (!isset($order_info['discount_amount']) || !isset($order_info['cost_amount']))
		#	{
		#		//获取当前订单商品总价、优惠价
		#		$pre_order_info = $this->getOrderInfo('pay_amount, discount_amount');
		#		$discount_amount = 0.00;
		#		if ($pre_order_info['pay_amount'] > $order_info['pay_amount'])
		#		{
		#			//减价了，优惠增加
		#			$discount_amount = floatval($pre_order_info['discount_amount']) + (floatval($pre_order_info['pay_amount']) - floatval($order_info['pay_amount']));
		#		}
		#		else
		#		{
		#			//加价了，优惠减少
		#			$discount_amount = floatval($pre_order_info['discount_amount']) - (floatval($pre_order_info['pay_amount']) - floatval($order_info['pay_amount']));
		#		}
		#		$order_info['discount_amount'] = $discount_amount;
		#	}
		#}

		foreach ($order_info AS $key => $value)
		{
			$this->data[$key] = $value;
		}
    }
    
	/**
	 * 保存订单信息到数据库
	 * @author 姜伟
	 * @param array	$order_info	订单信息数组，一维
	 * @return void
	 * @todo 更新订单表数据库，修改当前订单ID对应的订单信息
	 */
    public function saveOrderInfo()
    {

		if (!$this->data || empty($this->data))
		{
			return 0;
		}

		if ($this->orderId)
		{
			$order_id_field = $this->getOrderIdField();
			return $this->where($order_id_field . ' = ' . $this->orderId)->save($this->data);
		}
		else
		{
			return $this->add($this->data);
		}
    }
   
	/**
	 * 获取订单信息
	 * @author 姜伟
	 * @param string $fields 为空则返回所有字段，尽量不要为空
	 * @return array	$order_info	订单信息数组，一维
	 * @todo 若当前对象中已存在订单信息，直接返回，否则根据当前订单号查找tp_order表中的订单信息，并返回
	 */
    public function getOrderInfo($fields, $where = '', $log = false)
    {
		//检验订单号合法性
		$this->checkOrderIdvalid();
		#log_file('log = ' . ($log ? 1 : 0), 'a', true);

		//查找数据库
		$order_id_field = $this->getOrderIdField();
		$order_info = $this->field($fields)->where($order_id_field . ' = ' . $this->orderId . $where)->find();
		#log_file($this->getLastSql(), 'a', true);
		#log_file(json_encode($order_info), 'a', true);
		/*if ($log)
		{
			echo $this->getLastSql();
			dump($order_info);
			$this->checkOrderInfoValid($order_info);
die;
		}*/

		//订单合法性检验
		$this->checkOrderInfoValid($order_info);

		return $order_info;
    }

    /**
	 * 获取订单信息
	 * @author zlf
	 * @param string $fields 为空则返回所有字段，尽量不要为空
	 * @return array	$order_info	订单信息数组，一维
	 * @todo 若当前对象中已存在订单信息，直接返回，否则根据当前订单号查找tp_order表中的订单信息，并返回
	 */
    public function getOrderByInfo($fields, $where = '')
    {

		$order_info = $this->field($fields)->where($where)->find();
		return $order_info;
    }
    
	/**
	 * 获取订单关联的商品列表
	 * @author 姜伟
	 * @param string $fields 字段 
	 * @param string $where 查询条件
	 * @param string $order 排序条件
	 * @return array	$item_info	商品信息数组，二维
	 * @todo 调用商品订单模型的getOrderItemList方法获取
	 */
    public function getOrderItemList($fields = '', $where = '', $order = '')
    {
		$order_item_obj = $this->getOrderItemInstance();
		return $order_item_obj->getOrderItemList($fields, $where, $order);
    }
   
	/**
	 * 往订单中批量添加商品
	 * @author 姜伟
	 * @param array	$item_info	商品信息数组，二维
	 * @return 	void
	 * @todo 检验订单状态，如果订单不是未付款的状态，即订单状态不等于0，不能修改；1、将商品关联订单号添加到订单商品关联表中；2、更改订单表中的物流费用；3、记录订单变更日志；
	 */
    public function batchAddItem($item_info)
    {
		$order_status = (int) $this->getOrderState();
		if ($order_status !== self::PRE_PAY)
		{
			//抛出异常
			throw new RuntimeExceptionModel('只有未付款状态的订单才能添加商品');
		}
		#echo "<pre>";print_r($item_info);die('1.4');

		//将商品信息插入到订单商品表中
		$order_item_obj = $this->getOrderItemInstance();
		$item_info = $order_item_obj->addItem($item_info);
		#echo "<pre>";print_r($item_info);die('1.5');


		//计算订单物流费用
		$this->data['express_fee'] = $this->getExpressFee();

		$this->saveOrderInfo($this->data);
		return $item_info;
    }
   
	/**
	 * 生成订单
	 * @author 姜伟
	 * @param array	$order_info	订单信息数组
	 * @param array	$item_info	商品信息数组
	 * @return $orderId	生成的订单ID
	 * @todo 1、为了防止攻击/篡改订单、商品价格，还是要对商品和订单的价格重新进行计算，根据商品信息计算每件商品的实际支付金额，商品总金额，优惠总金额，总成本价，物流费用，订单总金额；2、将订单信息插入订单表中，得到订单ID；3、将商品关联第2步中生成的订单ID，一一插入订单商品关联表中
	 */
    public function addOrder($order_info, $item_info, $coupon)
    {
		//创建订单，将订单基本信息保存到数据库
		$order_info['order_sn'] = $this->generateOrderSn();
		$order_info['addtime'] = time();
		$order_info['isuse'] = 1;
		$order_info['order_status'] = self::PRE_PAY;
		$order_info['user_id'] = session('user_id');

		//地址信息
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id'], 'realname, mobile, province_id, city_id, area_id, address');
		if ($user_address_info)
		{
			$order_info = array_merge($order_info, $user_address_info);
			$area_obj = new AreaModel();
			$area_string = $area_obj->getAreaString($order_info['province_id'], $order_info['city_id'], $order_info['area_id']);
			$order_info['address'] = $area_string . $order_info['address'];
		}
		$this->data = $order_info;
		$this->orderId = $this->saveOrderInfo();
		#echo $this->getLastSql();
		#die;

		//记录订单优惠
		$order_coupon_obj = new OrderCouponModel();
		$order_coupon_info = array(
			'order_id'	=> $this->orderId,
			'coupon'	=> json_encode($coupon),
		);
		$order_coupon_obj->addOrderCoupon($order_coupon_info);
		log_file('add_order_coupon sql = ' . $order_coupon_obj->getLastSql() . ', order_coupon_info = ' . json_encode($order_coupon_info), 'addOrder');

		//删除购物车内相关商品
		#$shopping_cart_obj = new ShoppingCartModel();
		#$shopping_cart_obj->batchDeleteShoppingCart($item_info);

		//将商品信息关联订单ID插入到订单商品关联表中，返回每件商品的实际支付金额，成本价，批发价信息
		#echo "<pre>";print_r($item_info);die('1.2');

		$item_info = $this->batchAddItem($item_info);
		$order_info['pay_amount'] = floatval($item_info['pay_amount']);
		$order_info['cost_amount'] = floatval($item_info['cost_amount']);
		$this->saveOrderInfo();

		//执行addOrderEvent方法，执行生成订单触发的事件
		$this->addOrderEvent();

		//写订单日志
		#$this->saveLog();
		return $this->orderId;
    }

	/**
	 * 删除订单，原则上不能删除，如果客户非要删除，才提供
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 假删除，将订单表中的isuse字段设为0
	 */
    public function deleteOrder()
    {
		//检验订单状态，如果订单不是未付款的状态，不是未付款状态直接抛出异常
		$order_status = (int) $this->getOrderState();
		if ($order_status !== self::PRE_PAY)
		{
			//抛出异常
			throw new RuntimeExceptionModel('只有已关闭状态的订单才能删除');
			return false;
		}

		//假删除，将订单表中的isuse字段设为0
		$this->data['isuse'] = 0;
		$this->saveOrderInfo();

		return true;
    }
    
	/**
	 * 获取订单物流费用
	 * @author 姜伟
	 * @param void
	 * @return float	$express_fee	物流费用
	 * @todo 若当前对象中已存在订单物流费用，直接取，否则从数据库中取
	 */
    public function getExpressFee()
    {
		$express_fee = 0.00;
		if ($express_fee = $this->__get('express_fee'))
		{
			return $express_fee;
		}

		//检验订单号合法性
		$this->checkOrderIdvalid();

		//查找数据库
		$order_id_field = $this->getOrderIdField();
		$order_info = $this->getOrderInfo('express_fee');

		return $order_info['express_fee'];
    }

	/**
	 * 写订单操作日志
	 * @author 姜伟
	 * @param int	$start_order_status
	 * @param int	$end_order_status
	 * @param int	$user_id
	 * @return void
	 * @todo 记录订单操作前状态，操作后状态，操作人用户ID，操作人IP，操作时间，订单号及备注信息，备注信息如：管理员于2014:02:24 09:50将订单状态由已备货改为已发货
	 */
    public function saveLog($start_order_status, $end_order_status, $user_id)
    {
		$order_log_obj = new OrderLogModel();
		$order_log_obj->saveLog($start_order_status, $end_order_status, $this->orderId, $user_id);
    }
    
	/**
	 * 支付订单
	 * @author 姜伟
	 * @param string $pay_code 第三方支付平台返回的交易码
	 * @return 成功返回true，失败返回false
	 * @todo 调用setOrderState方法，传入订单状态已付款参数1
	 */
    public function payOrder($pay_code, $pay_tag = '')
    {
		$pay_code = $pay_code ? $pay_code : '';

		//获取支付方式名称
		$payway_obj = new PaywayModel();
		if ($pay_tag)
		{
			//$payway_info = $payway_obj->getPaywayInfoByTag($pay_tag);
			//$payway_name = $payway_info['pay_name'];

		}
		else
		{
			$order_info = $this->getOrderInfo('payway');
			$payway_name = $order_info['payway'];

			#$payway_info = $payway_obj->getPaywayInfoById($order_info['payway']);
		}
		#$payway_name = $payway_info['pay_name'];

		$arr = array(
			'pay_code'		=> $pay_code,
			//'payway'	    => $payway_name,
			'payway'	    => $pay_tag,
			'pay_time'		=> time(),
		);
		$this->setOrderInfo($arr);
		$this->saveOrderInfo();
		#echo $this->getLastSql();
		#die;

		return $this->setOrderState(self::PAYED);
    }
    
	/**
	 * 订单已备货
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用setOrderState方法，传入订单状态已付款参数2
	 */
    public function stockupOrder()
    {
		return $this->setOrderState(self::STOCKUPED);
    }
    
	/**
	 * 订单发货
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用setOrderState方法，传入订单状态已付款参数3
	 */
    public function deliverOrder($express_com = '', $express_number=0)
    {
        $status = $this->setOrderState(self::DELIVERED);
        $this->where('order_id = %d', $this->orderId)->save(
            array(
                'express_number'	=> $express_number,
                'com'				=> $express_com,
            )
        );
		return $status;
    }    
	/**
	 * 订单退货申请
	 * @author 姜伟
	 * @param array	$apply_info
	 * @return void
	 * @todo 调用退换货模型的itemRefundChangeApply方法将退货申请信息保存到退换货申请表中，调用setOrderState方法，传入订单状态已付款参数4
	 */
    public function refundApply($apply_info)
    {
		$apply_info['order_id'] = $this->getOrderId();
		//调用退换货模型的itemRefundChangeApply方法将退货申请信息保存到退换货申请表中
		$item_refund_change_obj = new ItemRefundChangeModel(0, 1);
		$item_refund_change_obj->itemRefundChangeApply($apply_info);

		//改变订单状态
		return $this->setOrderState(self::REFUNDING);
    }
    
	/**
	 * 通过订单退货申请
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用退换货模型的passItemRefundChangeApply方法，调用setOrderState方法，传入订单状态已付款参数5
	 */
    public function passRefundApply($ItemRefundChangeId)
    {
		//改变订单状态
		$this->setOrderState(self::REFUND_CONFIRMD);
    }
    
	/**
	 * 订单换货申请
	 * @author 姜伟
	 * @param array	$apply_info
	 * @return void
	 * @todo 调用退换货模型的itemRefundChangeApply方法，调用setOrderState方法，传入订单状态已付款参数6
	 */
    public function changeItemApply($apply_info)
    {
		$apply_info['order_id'] = $this->getOrderId();
		//调用退换货模型的itemRefundChangeApply方法将退货申请信息保存到退换货申请表中
		$item_refund_change_obj = new ItemRefundChangeModel(0, 2);
		$item_refund_change_obj->itemRefundChangeApply($apply_info);

		//改变订单状态
		$this->setOrderState(self::CHANGING);
    }
    
	/**
	 * 通过订单换货申请
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用退换货模型的passItemRefundChangeApply方法，调用setOrderState方法，传入订单状态已付款参数7
	 */
    public function passExchangeApply()
    {
		//改变订单状态
		$this->setOrderState(self::CHANGED);
    }
    
	/**
	 * 确认订单
	 * @author 姜伟
	 * @param void
	 * @return 成功返回true
	 * @todo 调用setOrderState方法将订单状态改为完成订单OrderModel::CONFIRMED（已确认/已完成），将订单消费总金额加入代理商总消费金额中
	 */
    public function confirmOrder($confirm_code)
    {
		//获取订单信息
		$order_info = $this->getOrderInfo('order_status, confirm_code');

		//检验订单状态，如果订单不是已发货、申请退货、申请换货状态，不予操作，直接返回false
		if (!($order_info['order_status'] == self::DELIVERED || $order_info['order_status'] == self::REFUNDING || $order_info['order_status'] == self::CHANGING) || $order_info['order_status'] == self::CONFIRMED)
		{
			return false;
		}

		//检查收货码是否一致
		if ($order_info['confirm_code'] != $confirm_code)
		{
			return false;
		}

		//改变订单状态
		$this->setOrderState(self::CONFIRMED);

		//其他事件，未完成###
		return true;
    }
     
	/**
	 * 关闭/取消订单
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 改变订单状态为已取消
	 */
    public function cancelOrder()
    {
		return $this->setOrderState(self::CANCELED);
    }
   
	/**
	 * 查询$where子句过滤后的订单数量
	 * @author 姜伟
	 * @param string $where where子句，查询条件
	 * @return int $order_num
	 * @todo 从订单表tp_order中查找满足where条件的元组数量
	 */
    public function getOrderNum($where)
    {
		return $this->where($where)->count();
    }

	/**
	 * 生成订单触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function addOrderEvent()
    {
		#$mobile = $this->data['mobile'];
		#$order_sn = $this->data['order_sn'];
		#$pay_amount = $this->data['pay_amount'];
		#$sms_obj = new SMSModel();
		#$result = $sms_obj->sendOrderCreate($mobile, $order_sn, $pay_amount);
    }

	/**
	 * 支付订单触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function payOrderEvent()
    {
		//获取商品列表
		$item_list = $this->getOrderItemList();
		#echo "<pre>";echo $this->getLastSql(); print_r($item_list);die;
		foreach ($item_list AS $k => $v)
		{
			//减库存
			$item_obj = new ItemModel($v['item_id']);
			$item_obj->deductItemStock($v['item_id'], $v['item_sku_price_id'], $v['number']);
		}
		//扣除相应积分和增加购物获得的积分
		$user_obj = new UserModel(session('user_id'));
		//$order_obj = new OrderModel($order_id);
		$order_info = $this->getOrderInfo('integral_amount, order_sn, pay_amount, order_id');
		//调用账户模型的addAccount方法支付该订单
		/*$integral_obj = new IntegralModel();
		$integral_obj->addIntegral(session('user_id'), 6, $order_info['integral_amount'] * -1, '订单支付抵扣' . $order_info['order_sn'], $order_info['order_id']);
		$integral_obj->addIntegral(session('user_id'), 2, $order_info['pay_amount'], '订单赠送' . $order_info['order_sn'], $order_info['order_id']);*/

		$order_info = $this->getOrderInfo('pay_time, pay_amount, user_id');
		$item_list = $this->getOrderItemList('item_name, number, real_price, total_amount, tp_order_item.merchant_id', 'tp_order_item.order_id = ' . $this->orderId);

		//变更相关的优惠券状态，将参与的满减活动记录到用户满减活动表中
		$order_coupon_obj = new OrderCouponModel();
		$order_coupon = $order_coupon_obj->getOrderCouponInfo('order_id = ' . $this->orderId, 'coupon');
		$user_discount_minus_obj = new UserDiscountMinusModel();
		$user_buy_give_obj = new UserBuyGiveModel();
		$user_vouchers_obj = new UserVouchersModel();
		$order_coupon = json_decode($order_coupon['coupon'], true);
log_file('order_coupon = ' . json_encode($order_coupon), 'useCoupon');
		foreach ($order_coupon AS $k => $v)
		{
			if ($v['type'] == 'user_vouchers')
			{
				$user_vouchers_obj->useCoupon($v['id'], $this->orderId, $order_info['pay_amount']);
log_file('useCoupon: sql = ' . $user_vouchers_obj->getLastSql(), 'useCoupon');
			}
			elseif ($v['type'] == 'buy_give')
			{
				$arr = array(
					'order_id'			=> $this->orderId,
					'user_id'			=> $order_info['user_id'],
					'buy_give_id'		=> $v['id'],
					'order_amount'		=> $order_info['pay_amount'],
					'merchant_id'		=> 0,
					'addtime'			=> time(),
				);
				$user_buy_give_obj->addUserBuyGive($arr);
				log_file('add sql = ' . $user_buy_give_obj->getLastSql(), 'addUserBuyGive');
			}
			elseif ($v['type'] == 'discount_minus')
			{
				$arr = array(
					'order_id'			=> $this->orderId,
					'user_id'			=> $order_info['user_id'],
					'discount_minus_id'	=> $v['id'],
					'num'				=> $v['num'],
					'merchant_id'		=> 0,
					'order_amount'		=> $order_info['pay_amount'],
					'addtime'			=> time(),
				);
				$user_discount_minus_obj->addUserDiscountMinus($arr);
				log_file('add sql = ' . $user_discount_minus_obj->getLastSql(), 'addUserDiscountMinus');
			}
			elseif ($v['type'] == 'item')
			{
				$arr = array(
					'order_id'			=> $this->orderId,
					'order_amount'		=> $order_info['pay_amount'],
					'user_id'			=> $order_info['user_id'],
					'merchant_id'		=> $v['merchant_id'],
					'special_offer_id'	=> $v['special_offer_id'],
					'item_id'			=> $v['item_id'],
					'used_time'			=> $v['num'],
				);
				$user_special_offer_obj->addUserSpecialOffer($arr);
				log_file('add sql = ' . $user_special_offer_obj->getLastSql(), 'addUserSpecialOffer');
			}
		}
    }
    
	/**
	 * 订单已备货触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发送短信/邮件等
	 */
    public function stockuporderevent()
    {
    }

	/**
	 * 订单发货触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function deliverOrderEvent()
    {
    	//app端通知推送
    	$order_info = $this->getOrderInfo('user_id, order_id');
    	$user_obj = new UserModel($order_info['user_id']);
    	$user_info = $user_obj->getUserInfo('jpush_reg_id');
    	if($user_info['jpush_reg_id']){
    		$jpush_obj = new PushModel();
    		$url = 'http://'. $_SERVER['HTTP_HOST'] . '/FrontOrder/order_detail/order_id/'.$order_info['order_id'];
    		$r = $jpush_obj->jpush_user('您的订单已发货，点击查看', $order_info['user_id'], 'order_detail',  $url);
    	}
    	
    }

	/**
	 * 订单退货申请触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function refundApplyEvent()
    {
    }

	/**
	 * 通过订单退货申请触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function passRefundApplyEvent()
    {
    }

	/**
	 * 拒绝订单退换货申请触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等，订单状态改为已发货
	 */
    public function refuseRefundApplyEvent()
    {
		//获取当前订单状态
		$start_order_status = $this->getOrderState();

		//修改订单状态
		$this->__set('order_status', self::DELIVERED);
		$this->saveOrderInfo();

		//写日志
		$this->saveLog($start_order_status, $order_status, session('user_id'));
    }

	/**
	 * 订单换货申请触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function exchangeItemApplyEvent()
    {
    }

	/**
	 * 通过订单换货申请触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function passExchangeItemApplyEvent()
    {
    }

	/**
	 * 确认订单触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function confirmOrderEvent()
    {
    	//积分返还
    	$order_info = $this->getOrderInfo('user_id, pay_amount,is_integral, order_id, order_sn');
    	if($order_info['pay_amount'] > 0){
    		$integral_obj = new IntegralModel();
			$integral_obj->addIntegral(session('user_id'), 2, $order_info['pay_amount'], '订单赠送' . $order_info['order_sn'], $order_info['order_id']);
    	}
		//分销提成处理
		$is_fenxiao_open = $GLOBALS['config_info']['IS_FENXIAO_OPEN'];
		if ($is_fenxiao_open)
		{
			$this->gain();
		}
		//发短信
		#$this->data = $this->getOrderInfo('mobile, order_sn, pay_amount');
		#$mobile = $this->data['mobile'];
		#$order_sn = $this->data['order_sn'];
		#$sms_obj = new SMSModel();
		#$result = $sms_obj->sendOrderCheck($mobile, $order_sn);
    }

	/**
	 * 关闭/取消订单触发的事件集合
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 发邮件，发短信等
	 */
    public function cancelOrderEvent()
    {
    }

	/**
	 * 根据传入的查询条件查找订单表中的订单列表
	 * @author 姜伟
	 * @param string $fields
	 * @param string $where
	 * @param string $groupby
	 * @return array $order_list
	 * @todo 根据where子句查询订单表中的订单信息，并以数组形式返回
	 */
    public function getOrderList($fields = '', $where = '', $order = '', $limit = '', $groupby = '')
	{
		return $this->field($fields)->where($where)->group($groupby)->order($order)->limit()->select();
	}

	/**
	 * 获取列表页订单列表
	 * @author 姜伟
	 * @param array $order_list
	 * @return array $order_list
	 * @todo 获取列表页订单列表
	 */
    public function getListData($order_list)
	{
		foreach ($order_list AS $k => $v)
		{
			$order_list[$k]['order_status_name'] = $this->convertOrderStatus($v['order_status']);
            # 获取快递公司名称
            $express_obj  = new ShippingCompanyModel();
            $express_info = $express_obj->getShippingCompanyInfoById($v['express_company_id']);
			$order_list[$k]['express_company_name'] = $express_info['shipping_company_name']? $express_info['shipping_company_name'] : '--';
            # 获取收货人名字
            $user_address_obj   = new UserAddressModel();
            $user_address_info  = $user_address_obj->getUserAddressInfo('user_address_id = ' . $v['user_address_id']);
            $province_id        = $user_address_info['province_id'];
            $city_id            = $user_address_info['city_id'];
            $area_id            = $user_address_info['area_id'];
            $address_detail     = $user_address_info['address'];

            $area_obj           = new AreaModel();
            $address            = $area_obj->getAreaString($province_id, $city_id, $area_id);

            $order_list[$k]['consignee']  = $user_address_info['realname'] ? $user_address_info['realname']:'--';
            $order_list[$k]['address']    = $address ? $address : '--';
            $order_list[$k]['mobile']     = $user_address_info['mobile']? $user_address_info['mobile'] : '--';

            //支付方式
            $payway_obj = new PaywayModel();
			$payway_info = $payway_obj->getPaywayInfo('payway_id = ' . $v['payway'], 'pay_name');
			$order_list[$k]['payway'] = $payway_info['pay_name'];
		}

		return $order_list;
	}

	/**
	 * 检查当前订单号合法性
	 * @author 姜伟
	 * @param void
	 * @return boolean
	 * @todo 若不合法，抛出异常，合法返回true
	 */
	public function checkOrderIdvalid()
	{
		if (!$this->orderId)
		{
			//抛出异常
			throw new Exception('订单ID不存在');
		}

		return true;
	}
 
	/**
	 * 获取订单商品对象
	 * @author 姜伟
	 * @param void
	 * @return Object 订单商品对象
	 * @todo 若当前对象已存在订单商品对象，直接返回，否则引入订单商品模型类，初始化订单商品对象
	 */
    public function getOrderItemInstance()
    {
		if ($this->order_item_obj)
		{
			$this->order_item_obj->orderId = $this->orderId;
			return $this->order_item_obj;
		}

		$model = 'OrderItemModel';
		$order_item_obj = new $model($this->orderId);
		$this->order_item_obj = $order_item_obj;

		return $order_item_obj;
    }
	
	/**
	 * 设置当前对象订单ID
	 * @author zhengzhen
	 * @param void
	 * @return void
	 * @todo 设置 $this->orderId为$orderId
	 */
	public function setOrderId($orderId)
	{
		$this->orderId = $orderId;
	}

	/**
	 * 获取当前对象订单ID
	 * @author 姜伟
	 * @param void
	 * @return int $this->orderId 订单商品对象
	 * @todo 返回 $this->orderId
	 */
    public function getOrderId()
    {
		return $this->orderId;
	}

	/**
	 * 获取当前对象订单ID字段名
	 * @author 姜伟
	 * @param void
	 * @return string 订单ID字段名
	 * @todo 根据订单类型获取订单ID字段名
	 */
    public function getOrderIdField()
    {
		return 'order_id';
	}

	/**
	 * 根据传入的订单信息判断订单是否合法，若不合法，抛出异常
	 * @author 姜伟
	 * @param array $order_info 订单信息数组
	 * @return void
	 * @todo 根据传入的订单信息判断订单是否合法，若不合法，抛出异常
	 */
    public function checkOrderInfoValid($order_info)
    {
		if (!$order_info)
		{
			//抛出异常
			throw new Exception('订单不存在');
		}
	}

	/**
	 * 设置属性$this->where
	 * @author 姜伟
	 * @param string where 查询字句，英文逗号隔开
	 * @return void
	 * @todo 将当前对象的where属性设置为传入的where
	 */
    public function setWhere($where = '')
    {
		if ($where)
		{
			$this->where .= $where;
		}
		else
		{
			$this->where = '';
		}
	}

	/**
	 * 设置属性$this->list_fields
	 * @author 姜伟
	 * @param string list_fields 要获取的字段列表，英文逗号隔开
	 * @return void
	 * @todo 将当前对象的list_fields属性设置为传入的list_fields
	 */
    public function setListFields($list_fields)
    {
		if ($list_fields)
		{
			$this->list_fields = $list_fields;
		}
	}

	/**
	 * 订单状态数字转化文字
	 * @author 姜伟
	 * @param int $order_status
	 * @return string $order_status
	 * @todo 将订单状态由数字转化成文字后返回
     *
			self::PRE_PAY	               => '未付款',
			self::PAYED		               => '已付款',
			self::STOCKUPED	               => '待发货',
			self::DELIVERED	               => '已发货',
			self::REFUNDING	               => '申请退款中',
			self::CHANGING	               => '申请换货中',
            self::REFUND_CONFIRMD          => '退款受理中',
            self::REFUND_DELIVEING         => '退货寄送中',
            self::REFUND_DELIVER_CONFIRMD  => '退货确认收货',
            self::REFUNDED                 => '已退款',
			self::CHANGED	               => '已换货',
			self::CONFIRMED	               => '已确认',
			self::CANCELED	               => '已关闭'
	 */
    public function convertOrderStatus($order_status)
    {
		switch ($order_status)
		{
			case self::PRE_PAY:
				$order_status = '等待买家付款';
				break;
			case self::PAYED:
				$order_status = '买家已付款';
				break;
			case self::STOCKUPED:
				$order_status = '等待卖家发货';
				break;
			case self::DELIVERED:
				$order_status = '卖家已发货';
				break;
			case self::REFUNDING:
				$order_status = '买家申请退款';
				break;
            case self::REFUND_CONFIRMD:
                $order_status = '退款受理中';
                break;
            case self::REFUND_DELIVEING:
                $order_status = '退货寄送中';
                break;
            case self::REFUND_DELIVER_CONFIRMD:
                $order_status = '退货确认收货';
                break;
			case self::REFUNDED:
				$order_status = '订单已退货款';
				break;
			case self::CHANGING:
				$order_status = '买家申请换货';
				break;
			case self::CHANGED:
				$order_status = '订单已换货';
				break;
			case self::CONFIRMED:
				$order_status = '订单已完成';
				break;
			case self::CANCELED:
				$order_status = '订单已关闭';
				break;
			case self::COMMENTED:
				$order_status = '订单已评价';
				break;
			default:
				$order_status = '';
				break;
		}

		return $order_status;
	}

	/**
	 * 生成订单号
	 * @author 姜伟
	 * @param void
	 * @return string $order_sn
	 * @todo 查询当天数据库中的订单量$count，订单号即为日期 . ($count + 1)，如201403010099，2014年3月1日的第99个订单
	 */
    public function generateOrderSn()
    {
		//当天0点的时间戳 
		$timestamp = strtotime(date('Y-m-d', time()));
		//当天订单总量
		$count = $this->where('addtime >=' . $timestamp)->count();
		//后缀位数
		$len = strlen((string) $count);
		//少于4位取4位，否则不变
		$len = $len < 4 ? 4 : $len;
		$suffix = sprintf("%0" . $len . "d", $count);
		$order_sn = date('Ymd') . $suffix;

		return $order_sn;
    }

	/**
	 * 获取当前订单状态变化明细
	 * @author 姜伟
	 * @param void
	 * @return array $order_log_list
	 * @todo 调用订单日志模型的getOrderLogList方法获取订单日志列表
	 */
    public function getOrderLogList()
	{
		$order_log_obj = new OrderLogModel();
		return $order_log_obj->getOrderLogList($this->orderId);
	}

	/**
	 * 根据订单号取order_id
	 * @author 姜伟
	 * @param string $order_sn
	 * @return int $order_id
	 * @todo 查询订单表，根据order_sn查order_id
	 */
	public function getOrderIdByOrderSn($order_sn)
	{
		$order_id = $this->where('order_sn = "' . $order_sn . '"')->getField('order_id');
		return $order_id;
	}

	/**
	 * 获取礼品信息
	 * @author 姜伟
	 * @param void
	 * @return array $gift_list
	 * @todo 获取当前对象data数组中的item_gifts和order_gifts，将之
	 */
	public function getGiftList()
	{
		$gift_list = array();
		if (!isset($this->data['order_gifts']) || !isset($this->data['item_gifts']))
		{
			$this->getOrderInfo('order_gifts, item_gifts');
		}

		$order_gifts = unserialize($this->data['order_gifts']);
		$item_gifts = unserialize($this->data['item_gifts']);
		$gift_list = array_merge($order_gifts, $item_gifts);

		return $gift_list;
	}

	/**
	 * 获取订单状态列表
	 * @author 姜伟
	 * @param void
	 * @return array $order_status_list
	 * @todo 获取订单状态列表
     *
	 */
	public static function getOrderStatusList()
	{
		$order_status_list = array(
			self::PRE_PAY	               => '未付款',
			self::PAYED		               => '已付款',
			self::STOCKUPED	               => '待发货',
			self::DELIVERED	               => '已发货',
			self::REFUNDING	               => '申请退款中',
			self::CHANGING	               => '申请换货中',
            self::REFUND_CONFIRMD          => '退款受理中',
            self::REFUND_DELIVEING         => '退货寄送中',
            self::REFUND_DELIVER_CONFIRMD  => '退货确认收货',
            self::REFUNDED                 => '已退款',
			self::CHANGED	               => '已换货',
			self::CONFIRMED	               => '已确认',
			self::CANCELED	               => '已关闭',
			self::COMMENTED	               => '已评价',
		);
		return $order_status_list;
	}

	/**
	 * 获取有效的状态数组
	 * @author 姜伟
	 * @param void
	 * @return array
	 * @todo 获取有效的状态数组
	 */
	public static function getValidStatus()
	{
		return array(OrderModel::PRE_PAY, OrderModel::PAYED, OrderModel::DELIVERED, OrderModel::CONFIRMED);
	}

	/**
	 * 查询$where子句过滤后的订单数量
	 * @author zlf
	 * @param string $where where子句，查询条件
	 * @return int $order_num
	 * @todo 从订单表tp_order中查找满足where条件的元组数量
	 */
    public function getOrderNumByQueryString($where)
    {
		$order_info = $this->field('COUNT(*) AS total')->where($where)->find();
		return $order_info['total'];
    }

	/**
	 * 确认退款货物
	 * @author wsq
	 */
    public function confirmRefundDeliving() 
    {
		$this->setOrderState(self::CONFIRMED);
    }

    /**
     * 根据商品列表获取订单信息
     * @author 姜伟
     * @param array $item_list
     * @return array 商品信息
     * @todo 根据商品列表获取订单信息
     */
    public static function getOrderInfoByItemList($item_list, $need_deliever=true)
	{
		//计算商品总价和总重量
		$total_amount = 0.00;
		//$total_weight = 0;
		foreach ($item_list AS $k => $v)
		{
			$total_amount += $v['real_price'] * $v['number'];
			$total_weight += $v['weight'] * $v['number'];
		}

		//获取省份ID
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getDefaultAddress();
		$province_id = intval($user_address_info['province_id']);

		//计算运费
		//$shipping_company_obj = new ShippingCompanyModel();
		//$express_fee = $shipping_company_obj->calculateShippingFee($province_id, $total_weight, $total_amount);
        if ($need_deliever) {
            $config_obj = new ConfigBaseModel();
            $express_fee = $config_obj->getConfig('base_fare');

            //计算满减运费
            $full_cost = $config_obj->getConfig('full_cost');
            //$minus_cost = $config_obj->getConfig('minus_cost');
            if ($total_amount >= $full_cost && $need_deliever) {
                $express_fee = 0;
            }
        } else {
            $express_fee = 0;
        }

		//返回数组
		$order_arr = array(
			'total_amount'		=> $total_amount,
			'total_weight'		=> $total_weight,
			'express_fee'		=> $express_fee,
			'user_address_id'	=> $user_address_info['user_address_id'],
			'item_list'			=> $item_list,
		);

		#echo "<pre>";
		#print_r($item_list);
		#print_r($order_arr);

		return $order_arr;
	}

	/**
	 * 获取前台列表页订单列表
	 * @author 姜伟
	 * @param array $order_list
	 * @return array $order_list
	 * @todo 获取前台列表页订单列表
	 */
    public function getFrontListData($order_list)
	{
		foreach ($order_list AS $k => $v)
		{
			//订单状态
			$order_list[$k]['order_status_name'] = $this->convertOrderStatus($v['order_status']);

            if ($v['order_status'] != self::CONFIRMED && 
                $v['order_status'] != self::CANCELED &&
                $v['order_status'] != self::PRE_PAY ) 
            {
                $remote_status = $this->getERPOrderStatus($v['order_id']);
                if ($remote_status != false && 
                    $remote_status != $v['order_status'])
                {
                    $this->where('order_id=%d', $v['order_id'])
                        ->setField('order_status', $remote_status);
                }
            }

			//商品列表
			$this->orderId = $v['order_id'];
			$order_item_list = $this->getOrderItemList();
			$item_str = '';
			foreach ($order_item_list AS $key => $val)
			{
				$item_str .= $val['item_name'] . '*' . $val['number'];
				if (count($order_item_list) - 1 != $key)
				{
					$item_str .= '、';
				}
			}

            $send_way = D('Dept')->getSendWayInfo($v['dept_code']);
            $order_list[$k]['send_way'] = $send_way;
			$order_list[$k]['item_str'] = $item_str;
		}

		return $order_list;
	}

	/**
	 * 获取前台列表页订单列表新版数据
	 * @author zlf
	 * @param array $order_list
	 * @return array $order_list
	 * @todo 获取前台列表页订单列表
	 */
    public function getListDataFront($order_list)
	{
		foreach ($order_list AS $k => $v)
		{
			//订单状态
			$order_list[$k]['order_status_name'] = $this->convertOrderStatus($v['order_status']);

            if ($v['order_status'] != self::CONFIRMED && 
                $v['order_status'] != self::CANCELED &&
                $v['order_status'] != self::PRE_PAY ) 
            {
                $remote_status = $this->getERPOrderStatus($v['order_id']);
                if ($remote_status != false && 
                    $remote_status != $v['order_status'])
                {
                    $this->where('order_id=%d', $v['order_id'])
                        ->setField('order_status', $remote_status);
                }
            }

			//商品列表
			$this->orderId = $v['order_id'];
			$order_item_list = $this->getOrderItemList();
			$order_list[$k]['item_list'] = $order_item_list;
			/*foreach ($order_item_list AS $key => $val)
			{
				$order_list[$key]['item_list'] = $val;
				
			}*/

            $send_way = D('Dept')->getSendWayInfo($v['dept_code']);
            $order_list[$k]['send_way'] = $send_way;
            $order_list[$k]['addtime'] = date('Y-m-d H:m:s', $v['addtime']);
            #echo "<pre>";print_r($order_list);die;
			#$order_list[$k]['item_str'] = $item_str;
		}

		return $order_list;
	}

	/**
     * 获取退款原因列表
     * @author zlf
     * @param void
     * @return array $refund_reason
     * @todo 获取退款原因列表
     */
    public static function getRefundReasonList()
    {
        return array(
            '1' => '未收到货',
            '2' => '超时',
            '3' => '质量问题',
            '4' => '收到的商品数量与购买的数量不一致',
            '5' => '镖师态度太差',
            '6' => '其他原因',
        );
    }

    // 同步订单信息到 ERP系统
    // @author wsq
    /*
        BillNumber 订单号，建议格式：年月日+4位流水，应该唯一 201601040001 是
        DeptCode 出货部门编码，门店自提的为具体门店编码，否则为配送编码。 3000 是
        accDate 制单日期 20160104 是
        accTime 制单时间 185801 是 
        BuildManCode 制单人编码 9000 是
        BuildManName 制单人名称 张三 是
        Remark 备注 否
        CardCode 会员卡号 否
        WholeSaleState 订单状态：订单状态[ -1待审核、 0 已审核、1 已分配 2 已拣货 3 已发货 4 已收款 5 已整单拒收 6 已收货 7 已取消 8 缺货等待 9 已退款] 0 是
        CustomerCode 客户编码， 0000007 是
        PurchaserName 收货人姓名 李四 是
        PurchaserAddress 收货人地址 浙江省 是
        PurchaserPhone 收货人手机 13888888888 手机电话必填一项 PurchaserMobile 收货人电话
        RecMoney 应付金额，本单金额 199 是
        TrafficNO 运单号，发货后如有物流信息更新在这里 否
        PayModeDesc 支付信息，已支付的信息 否
        SubmitTime 审核时间，格式yyyyMMddHHmmss 否
        SubmitManCode 审核人编码 否
        SubmitManName 审核人名称 否
        ModifyManCode 最后修改人编码 否
        ModifyManName 最后修改人名称 否
        ModifyRemark 修改备注 否
        Details 商品明细，另一个结构体，可多个组成数组。 是
            InsideId 商品序号，从0开始累加 是
            GoodsCode 商品编码 是
            GoodsBarCode 条码 是
            Amount 数量 是
            WsPrice 价格 是
            WsMoney 金额 是
            ManualAgio 折扣 是
            Remark 备注 
            否
     */
    protected function formatSyncData($order_id) 
    {
        $return_array = NULL;
        if ($order_id) {
            //调用订单商品模型获取订单商品列表
            $order_info = $this->where('order_id=%d', $order_id)->find();
            $user_address_id = $order_info['user_address_id'];
            $user_address_info = D('UserAddress')->where('user_address_id=%d', intval($user_address_id))->find();
            $order_item_obj = new OrderItemModel($order_id);
            $item_list = $order_item_obj->getOrderItemList();
            $item_detail = array();

            if (!$item_list || !$user_address_info) return NULL;

            foreach ($item_list AS $k => $v) {
                array_push($item_detail,
                    array(
                        'InsideId' => $k,
                        'GoodsCode' => $v['goods_code'],
                        'GoodsBarCode' => $v['item_sn'],
                        'Amount' => $v['number'],
                        'WsPrice' => $v['real_price'],
                        //todo: 如果有折扣 WsMoney = 数量*单价-折扣
                        'WsMoney' => floatval($v['real_price']) *intval($v['number']), 
                        'ManualAgio' => 0,
                        'Remark' => '',
                    )
                ); 
            }

            $Remark =intval($order_info['need_deliever']) ? "邮寄发货" : "店铺自提";
            $return_array = array(
                'BillNumber' => $order_info['order_sn'],
                'DeptCode' => $order_info['dept_code'], //todo 默认是7000 总仓库，后期门店自提作为扩展;
                //'DeptCode' => self::MAIN_FACTORY, //todo 默认是7000 总仓库，后期门店自提作为扩展;
                'accDate' => date("Ymd", $order_info['addtime']),
                'accTime' => date("Hms", $order_info['addtime']),
                'BuildManName' => '0000', // 000 默认为管理员
                'BuildManCode' => '0000',
                'Remark' => $Remark . ";" . $order_info['user_remark'],
                'WholeSaleState' => $this->mapToERPStatus($order_info['order_status']),
                'CustomerCode' => $order_info['user_id'],
                'PurchaserName' => $user_address_info['realname'],
                'PurchaserAddress' => AreaModel::getAreaString(
                        $user_address_info['province_id'],
                        $user_address_info['city_id'],
                        $user_address_info['area_id']
                    ) . $user_address_info['address'],
                'PurchaserMobile' => $user_address_info['mobile'],
                'RecMoney' => $order_info['pay_amount'],
                'Details' => $item_detail,
            );
        }

        return $return_array;
    }

    // 同步订单信息
    // @author wsq
    protected function sync($data, $type) {
        // 数据进行json 格式转换
        $data = json_encode($data);
        // 通过接口发送数据
        $status = D('Connection')->getResult(
            array(
                'type' => $type,
                'msg' => $data,
            )
        );

        return  $status['Sucess'] ? $status['Sucess'] : false;
    }

    // 同步订单信息到ERP 系统
    // @author wsq
    public function syncOrderInfo($order_id)
    {
        // 格式化数据
        $data = $this->formatSyncData($order_id);

        if ($data) {
            return  $this->sync($data, ConnectionModel::OP_SYNC_ORDER_INFO);
        } 

        return false;
    }

    // 同步订单状态信息到 ERP 系统
    /*
     *
     BillBuildDate 订单的制单日期，格式YYYYMMDD
     DeptCode 订单产生时的出货部门编码
     BillNumber 订单编号
     WholeSaleState 状态
     TrafficNO 运单号
     */
    // @author wsq
    public function syncOrderStatus($order_id)
    {
        $order_info = $this->where('order_id = %d', intval($order_id))->find();
        if ($order_info) {
            $data = array(
                'BillBuildDate' => date("Ymd", $order_info['addtime']),
                'DeptCode' => $order_info['dept_code'],
                'BillNumber' => $order_info['order_sn'],
                'WholeSaleState' => $this->mapToERPStatus($order_info['order_status']),
                'TrafficNO' => $order_info['express_number'],
            );
            //return true;
            return  $this->sync($data, ConnectionModel::OP_SYNC_ORDER_STATUS);
        }

        return false;
    }


    // 映射订单状态
    // @author wsq
    // 订单状态：订单状态
    // [ -1待审核、 0 已审核、1 已分配 
    // 2 已拣货 3 已发货 4 已收款 
    // 5 已整单拒收 6 已收货 7 已取消 8 缺货等待 9 已退款]
	//const PRE_PAY                 = 0; //未付款状态
	//const PAYED                   = 1; //已付款状态/待发货
	//const DELIVERED               = 2; //已发货状态
	//const CONFIRMED               = 3; //已确认收货/已完成订单
	//const CANCELED                = 4; //已取消/关闭
	//const REFUNDING               = 5; //退款中
	//const REFUND_CONFIRMD         = 6; //退款受理中
	//const REFUND_DELIVEING        = 7; //退货寄送中
	//const REFUND_DELIVER_CONFIRMD = 8; //退货确认收货
	//const REFUNDED                = 9; //已退款

	//const STOCKUPED               = 10; //已备货状态
	//const CHANGING                = 11; //申请换货中
	//const CHANGED                 = 12; //已换货
    public static function mapToERPStatus($status)
    {
        switch (intval($status)) {
			case self::PAYED:     return 0;
			case self::DELIVERED: return 3;
			case self::REFUNDING: return 5;
			case self::CONFIRMED: return 6;
            case self::CANCELED:  return 7;
			default: return -2;
        }
    }

    // 映射本地订单状态
    // @author wsq
    public static function mapToLocalStatus($status)
    {
        switch (intval($status)) {
        //case -1: return self::PRE_PAY;
        case  0: return self::PAYED;
        //case  1: return self::PRE_PAY;
        //case  2: return self::PRE_PAY;
        case  3: return self::DELIVERED;
        //case  4: return self::PRE_PAY;
        case  5: return self::REFUNDING;
        case  6: return self::CONFIRMED;
        case  7: return self::CANCELED;
        //case  8: return self::PRE_PAY;
        case  9: return self::CONFIRMED;
        default: return false;
        }
    }

    // 获取ERP订单信息
    // @author wsq
    public function getERPOrderStatus($order_id)
    {
        $order_info = $this->where('order_id=%d',intval($order_id))->find();
        if ($order_info) {
            $BillBuildDate =  date("Ymd", $order_info['addtime']);
            $DeptCode = $order_info['dept_code'];
            $BillNumber = $order_info['order_sn'];
            $data = array(
                'BillNumber' => $BillNumber,
                'DeptCode' => $DeptCode,
                'BillBuildDate' => $BillBuildDate,
            );

            $result = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_GET_ORDER_STATUS,
                    'msg' => json_encode($data),
                )
            );

            if ($result['Sucess']) {
                $remote_status = $result['Data']['0']['WholeSaleState'];
                return $this->mapToLocalStatus($remote_status);
            } else {
                return false;
            }
        }
        return false;
    }

    // 退款确认按钮
    // todo:暂时退款到个人中心的金币里面, 后续考虑原路退
    // todo:现原路退回，如果是微信则退到微信里，如果是钱包就退回钱包，wzg
    // @author wsq
    protected function passRefundApplyConfirmdEvent()
    {
        $order_info = $this->where('order_id='.intval($this->orderId))->find();
        if ($order_info) {
        	if($order_info['pay_amount'] > 0){
        		$account_obj = new AccountModel();
           		$account_obj->addAccount($order_info['user_id'], AccountModel::ORDER_REFOUND, $order_info['pay_amount'], '用户申请退款'.$order_info['order_sn'], $this->orderId);
        	}
            if($order_info['integral_amount'] > 0){
            	$integral_obj = new IntegralModel();
            	$integral_obj->addIntegral($order_info['user_id'], IntegralModel::ORDER_REFOUND, $order_info['integral_amount'], '用户申请退款'.$order_info['order_sn'], $this->orderId);
            }
            
        }
    }

	/**
	 * 评价订单
	 * @author 姜伟
	 * @param array $assess_info
	 * @return 成功返回true
	 * @todo 调用setOrderState方法将订单状态改为COMMENTED已评价
	 */
    public function commentOrder($assess_info)
    {
		//获取订单信息
		$order_info = $this->getOrderInfo('order_status');

		//检验订单状态，如果订单不是已确认状态，不予操作，直接返回false
		if ($order_info['order_status'] != self::CONFIRMED)
		{
			return false;
		}

		//执行评价操作
		$score = $assess_info['score'];
        $user_comment_merchant_obj = new UserCommentMerchantModel();

        if ($score) {
            $arr = array(
                'member_id'		=> $assess_info['user_id'],
                'merchant_id'	=> 1,
                'order_id'		=> $this->orderId,
                'score'			=> $score,
            );
            $user_comment_merchant_obj->addUserCommentMerchant($arr);
        } else {
            //商家
            $arr = array(
                'member_id'		=> $assess_info['user_id'],
                'merchant_id'	=> 1,
                'order_id'		=> $this->orderId,
                'score'			=> $score,
                'remark'		=> $assess_info['remark_str'],
            );
            $user_comment_merchant_obj->addUserCommentMerchant($arr);
        }

		//改变订单状态
		$this->setOrderState(self::COMMENTED);
		return true;
    }

	//提成
	function gain()
	{
		/*** 给上级增加相应比例的提成begin ***/
		//获取订单信息
		$order_info = $this->getOrderInfo('order_id, user_id, pay_amount, pay_code');
		$user_id = $order_info['user_id'];
		$pay_code = $order_info['pay_code'];

		/*** 提成打款begin ***/
		//获取上级
		$user_obj = new UserModel($order_info['user_id']);
		$user_info = $user_obj->getUserInfo('nickname, first_agent_id, second_agent_id, third_agent_id');
		log_file('user sql = ' . $user_obj->getLastSql(), 'wxpay');
		log_file('user_info = ' . json_encode($user_info), 'wxpay');

		//获取分销等级
		$fenxiao_level = $GLOBALS['config_info']['FENXIAO_LEVEL'];

		if ($user_info['first_agent_id'])
		{
			#$agent_rate = $GLOBALS['config_info']['FIRST_LEVEL_AGENT_RATE'];
			$user_obj2 = new UserModel($user_info['first_agent_id']);
			$user_info2 = $user_obj2->getUserInfo('first_agent_rate');
			$agent_rate = $user_info2['first_agent_rate'] > 0 ? $user_info2['first_agent_rate'] : $GLOBALS['config_info']['FIRST_LEVEL_AGENT_RATE'];
			$amount = $agent_rate / 100 * $order_info['pay_amount'];
			$account_obj = new AccountModel();
			$account_obj->addAccount($user_info['first_agent_id'], AccountModel::FIRST_LEVEL_AGENT_GAIN, $amount, '一级推荐用户消费提成');

			//推送消息给用户
			$msg = array(
				'first'		=> '您有一个一级推荐用户在平台产生了消费，感谢您的推荐！',
				'keyword1'	=> $user_info['nickname'],
				'keyword2'	=> $amount . '元',
				'url'		=> 'http://' . $_SERVER['HTTP_HOST'] . '/FrontUser/my_wallets/',
			);
			PushModel::wxPush($user_info['first_agent_id'], 'rec_user_pay', $msg);

			/*** 给上级增加相应比例的提成end ***/
		}

		if ($fenxiao_level >= 2 && $user_info['second_agent_id'])
		{
			/*** 给上上级增加相应比例的提成begin ***/
			#$agent_rate = $GLOBALS['config_info']['SECOND_LEVEL_AGENT_RATE'];
			$user_obj2 = new UserModel($user_info['second_agent_id']);
			$user_info2 = $user_obj2->getUserInfo('first_agent_rate');
			$agent_rate = $user_info2['first_agent_rate'] > 0 ? $user_info2['first_agent_rate'] : $GLOBALS['config_info']['SECOND_LEVEL_AGENT_RATE'];
			$amount = $agent_rate / 100 * $order_info['pay_amount'];
			$account_obj = new AccountModel();
			$account_obj->addAccount($user_info['second_agent_id'], AccountModel::SECOND_LEVEL_AGENT_GAIN, $amount, '二级推荐用户消费提成');

			//推送消息给用户
			$msg = array(
				'first'		=> '您有一个二级推荐用户在平台产生了消费，感谢您的推荐！',
				'keyword1'	=> $user_info['nickname'],
				'keyword2'	=> $amount . '元',
				'url'		=> 'http://' . $_SERVER['HTTP_HOST'] . '/FrontUser/my_wallets/',
			);
			PushModel::wxPush($user_info['second_agent_id'], 'rec_user_pay', $msg);

			/*** 给上上级增加相应比例的提成end ***/
		}

		if ($fenxiao_level == 3 && $user_info['third_agent_id'])
		{
			/*** 给上上上级增加相应比例的提成begin ***/
			#$agent_rate = $GLOBALS['config_info']['THIRD_LEVEL_AGENT_RATE'];
			$user_obj2 = new UserModel($user_info['third_agent_id']);
			$user_info2 = $user_obj2->getUserInfo('first_agent_rate');
			$agent_rate = $user_info2['first_agent_rate'] > 0 ? $user_info2['first_agent_rate'] : $GLOBALS['config_info']['SECOND_LEVEL_AGENT_RATE'];
			$amount = $agent_rate / 100 * $order_info['pay_amount'];
			$account_obj = new AccountModel();
			$account_obj->addAccount($user_info['third_agent_id'], AccountModel::THIRD_LEVEL_AGENT_GAIN, $amount, '三级推荐用户消费提成');

			//推送消息给用户
			$msg = array(
				'first'		=> '您有一个三级推荐用户在平台产生了消费，感谢您的推荐！',
				'keyword1'	=> $user_info['nickname'],
				'keyword2'	=> $amount . '元',
				'url'		=> 'http://' . $_SERVER['HTTP_HOST'] . '/FrontUser/my_wallets/',
			);
			PushModel::wxPush($user_info['third_agent_id'], 'rec_user_pay', $msg);

			/*** 给上上上级增加相应比例的提成end ***/
		}
		/*** 提成打款end ***/
	}
 
    /**
     * 查找订单是否超时未确认，确认之，并推送用户
     * @author cc
     * @param void
     * @return boolean
     * @todo 查找订单是否超时未确认，确认之，并推送用户
	 * @test 订单是否确认，用户是否接收到通知
     */
    public function autoConfirmOrder()
    {
		$order_auto_confirm_time = $GLOBALS['config_info']['ORDER_AUTO_CONFIRM_TIME'];
		$deadtime = time() - intval($order_auto_confirm_time) * 3600;
		#$deadtime = time() - 30;
		$order_list = $this->field('order_id')->where('order_status = ' . self::DELIVERED . ' AND start_delivery_time < ' . $deadtime)->select();
		#echo $this->getLastSql();
		#echo "<pre>";
		#print_r($order_list);
		if ($order_list)
		{
			log_file('auto_confirm_order: sql = ' . $this->getLastSql());
			log_file('order_list = ' . json_encode($order_list));
		}
		foreach ($order_list AS $key => $v)
		{
			//确认订单
			$order_obj = new OrderModel($v['order_id']);
			$order_obj->confirmOrder();
/*
			//通知用户，###未完成
			$order_info = $order_obj->getOrderInfo('user_id, order_sn, addtime, start_delivery_time, delivery_time, confirm_time');

			//获取商品列表
			$order_item_obj = new OrderItemModel();
			$order_item_list = $order_item_obj->getOrderItemList('item_name, number', 'order_id = ' . $v['order_id']);
			$item_str = '';
			foreach ($order_item_list AS $val)
			{
				$item_str .= $val['item_name'] . '*' . $val['number'] . ',';
			}
			$item_str = substr($item_str, 0, -1);

			$msg = array(
				'first'		=> '您的订单已确认',
				'keyword1'	=> $order_info['order_sn'],
 
				'keyword2'	=> $item_str,
				'keyword3'	=> date('Y-m-d H:i', $order_info['addtime']),
				'keyword4'	=> date('Y-m-d H:i', $order_info['start_delivery_time'] ? $order_info['start_delivery_time'] : $order_info['delivery_time']),
				'keyword5'	=> date('Y-m-d H:i', $order_info['confirm_time']),
				'order_id'	=> $v['order_id'],
			);
			$success = PushModel::wxPush($order_info['user_id'], 'confirm', $msg);*/
		}
	}
}

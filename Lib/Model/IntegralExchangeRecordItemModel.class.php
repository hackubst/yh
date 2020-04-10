<?php
/**
 * 订单商品模型
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
class IntegralExchangeRecordItemModel extends Model
{
	/**
	 * 订单ID
	 */
    public $orderId = 0;

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param int $order_id 订单ID
	 * @return void
	 * @todo 初始化数据库，数据表名称，订单ID
	 */
    public function IntegralExchangeRecordItemModel($order_id)
    {
		$this->db(0);
		$this->orderId = $order_id;
		$this->tableName = C('DB_PREFIX') . 'integral_exchange_record_item';
	}
 
	/**
	 * 获取订单关联的商品列表
	 * @author 姜伟
	 * @param string $fields 字段 
	 * @param string $where 查询条件
	 * @param string $order 排序条件
	 * @return array	$item_info	商品信息数组，二维
	 * @todo 根据当前订单号查找tp_order_item表中关联的商品数据，并返回
	 */
    public function getOrderItemList($fields = '', $where = '', $order = '')
    {
		//检验订单号合法性
		if (!$where)
		{
			$this->checkOrderIdvalid();
		}

		//从数据库中查询
		$where = $where ? $where : 'exchange_record_id = ' . $this->orderId;
		return $this->field($fields)->where($where)->order($order)->select();
    }
    
	/**
	 * 往订单中添加商品
	 * @author 姜伟
	 * @param array	$item_info	商品信息数组，二维，必须包含item_id和item_sku_price_id
	 * @param int $agent_rank_id 代理商等级ID
	 * @return 	void
	 * @todo 将商品信息数组添加到商品订单表中
	 */
    public function addItem($item_info, $agent_rank_id = 0)
    {
    	
		//检验订单号合法性
		$this->checkOrderIdvalid();

		//计算商品实际支付价格、总价、总成本价、总重量，获取商品基本信息，赋值到数组中
		$total_amount = 0.00;
		$cost_amount = 0.00;
		$total_weight = 0.00;
		foreach ($item_info AS $key => $item)
		{

			$item_obj = new ItemModel();
			#$item_real_price = $item_obj->calculateItemRealPrice($item['item_id'], $item['item_sku_price_id'], $agent_rank_id);

			//获取商品信息
			$fields = 'item_name, item_sn, weight, mall_price, base_pic';
			$item_basic_info = $item_obj->getItemInfo('item_id = ' . $item['item_id'], $fields);
			#echo $item_obj->getLastSql();
			#echo "<pre>";print_r($item_basic_info);die;

			//获取商品货号
			$item_sn = '';
			if (!$item_sku_price_id)
			{
				$item_sn = $item_basic_info['item_sn'];
			}
			else
			{
				$item_sku_obj = new ItemSkuModel();
				$item_sku_info = getItemSkuInfo($item_sku_price_id, 'item_sn');
				$item_sn = $item_sku_info['item_sn'];
			}

			unset($item_info[$key]['order_item_id']);
			$item_info[$key]['exchange_record_id'] = $this->orderId;
			$item_info[$key]['item_sku_price_id'] = 0;
			$item_info[$key]['item_name'] = $item_basic_info['item_name'];
			$item_info[$key]['weight'] = $item_basic_info['weight'];
			$item_info[$key]['item_sn'] = $item_sn;
			$item_info[$key]['mall_price'] = floatval($item_basic_info['mall_price']);
			$item_info[$key]['vip_price'] = $item_basic_info['mall_price'];
			$item_info[$key]['small_pic'] = $item_basic_info['base_pic'];
			$item_info[$key]['real_price'] = $item_basic_info['mall_price'];
			$item_info[$key]['cost_price'] = floatval($item_basic_info['cost_price']);
			unset($item_info[$key]['user_id']);
			unset($item_info[$key]['total_price']);
			unset($item_info[$key]['addtime']);
			unset($item_info[$key]['link_item']);
			unset($item_info[$key]['small_img']);
			unset($item_info[$key]['middle_img']);
			unset($item_info[$key]['status']);
			unset($item_info[$key]['integral_num']);
			unset($item_info[$key]['weight']);
			unset($item_info[$key]['mall_price']);
			unset($item_info[$key]['shopping_cart_id']);
			unset($item_info[$key]['is_integral']);
			unset($item_info[$key]['item_package_id']);
			unset($item_info[$key]['integral_exchange']);
			$total_amount += floatval($item_basic_info['vip_price']) * $item_info[$key]['number'];
			$cost_amount += floatval($item_basic_info['cost_price']) * $item_info[$key]['number'];
			unset($item_info[$key]['vip_price']);

		}
		
		//将商品信息关联订单插入到数据库
		$this->addAll($item_info);
		
		#echo "</pre>";
		$return_arr = array(
			'total_amount'	=> $total_amount,
			'cost_amount'	=> $cost_amount,
		);
		return $return_arr;
		//INSERT INTO `tp_order_item` (`user_id`,`item_id`,`mall_price`,`real_price`,`total_price`,`item_sku_price_id`,`number`,`item_name`,`property`,`small_pic`,`weight`,`addtime`,`order_id`,`item_sn`,`cost_price`) VALUES ('1','503',0,0,'30.00','877','2',null,'颜色:灰色 尺寸:大',null,null,'1412218884',676,null,0),('1','503',0,0,'30.00','876','3',null,'颜色:白色 尺寸:小',null,null,'1412218884',676,null,0)
    }
    
	/**
	 * 删除订单中的一个商品
	 * @author 姜伟
	 * @param int	$item_id	商品ID
	 * @return 	void
	 * @todo 从订单商品关联表中删除该订单中的该商品
	 */
    public function deleteItem($item_id)
    {
    }
 
	/**
	 * 删除订单中的所有商品
	 * @author 姜伟
	 * @param void
	 * @return 	void
	 * @todo 从订单商品关联表中删除该订单中的所有商品
	 */
    public function deleteAllItem()
    {
		//检验订单号合法性
		$this->checkOrderIdvalid();

		$this->where('order_id = ' . $this->orderId)->delete();
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
			throw new Exception('订单ID不存在');
		}

		return true;
	}

	/**
	 * 从数据库中获取当前订单的某商品信息
	 * @author 姜伟
	 * @param int $item_id
	 * @param int $item_sku_price_id
	 * @param string $fields
	 * @return array 订单商品信息
	 * @todo 若$item_sku_price_id为0，根据$item_id查，否则根据$item_sku_price_id查
	 */
	public function getOrderItemInfo($item_id, $item_sku_price_id, $fields = '')
	{
		if (intval($item_sku_price_id))
		{
			return $this->field('order_item_id, number' . $fields)->where('item_sku_price_id = ' . $item_sku_price_id)->find();
		}

		if (intval($item_id))
		{
			return $this->field('order_item_id, number' . $fields)->where('item_id = ' . $item_id)->find();
		}

		return null;
	}

	/**
	 * 从数据库中获取当前订单的某商品信息
	 * @author 姜伟
	 * @param int $order_item_id
	 * @param string $fields
	 * @return array 订单商品信息
	 * @todo 根据$order_item_id查
	 */
	public function getOrderItemInfoById($order_item_id, $fields = '')
	{
		if (intval($order_item_id))
		{
			return $this->field($fields)->where('order_item_id = ' . $order_item_id)->find();
		}
		return null;
	}

	/**
	 * 修改订单商品信息
	 * @author 姜伟
	 * @param int $order_item_id
	 * @param array $order_item_info
	 * @return void
	 * @todo 将订单商品信息保存到订单商品表中
	 */
	public function setOrderItemInfo($order_item_id, $order_item_info)
	{
		if ($order_item_info)
		{
			$this->where('order_item_id = ' . $order_item_id)->save($order_item_info);
		}
	}

	/**
	 * 根据商品名称查询订单ID列表
	 * @author 姜伟
	 * @param string $item_name 商品名称
	 * @return string 订单ID列表，英文逗号隔开
	 * @todo 根据商品名称查询订单商品表，返回匹配的订单ID列表
	 */
	public function getOrderIdByItemName($item_name)
	{
		if ($item_name)
		{
			$order_info = $this->field('GROUP_CONCAT(order_id) AS order_ids')->where('item_name LIKE "%' . $item_name . '%"')->group('"1"')->find();
			return $order_info['order_ids'];
		}
		return null;
	}
}

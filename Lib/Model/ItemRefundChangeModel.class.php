<?php
/**
 * 退换货申请
 * @ access public
 * @ author 姜伟
 * @ Date 2014-02-20
 */
class ItemRefundChangeModel extends Model
{
	/**
	 * 退换货ID，对应于退换货申请表中的主键值
	 */
    public $ItemRefundChangeId = 0;

	/**
	 * 退换货类型，1为退货，2为换货
	 */
    public $operate_type = 0;

	/**
	 * 查询条件，where字句
	 */
    public $where = '';

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param int $ItemRefundChangeId	退换货ID
	 * @param int $operate_type 退换货类型
	 * @return void
	 * @todo 检验参数合法性，
	 */
    public function ItemRefundChangeModel($ItemRefundChangeId = 0, $operate_type)
    {
		$this->db(0);
		$this->tableName = C('DB_PREFIX') . 'item_refund_change';
		if ($ItemRefundChangeId)
		{
			$this->ItemRefundChangeId = $ItemRefundChangeId;
			return;
		}

		if ($operate_type == 1 || $operate_type == 2)
		{
			$this->operate_type = $operate_type;
			return;
		}
		else
		{
			trigger_error('无效的参数operate_type');
		}
    }

	/**
	 * 订单退换货申请
	 * @author 姜伟
	 * @param array	$apply_info
	 * @return void
	 * @todo 检查参数合法性，并保存到数据库
	 */
    public function itemRefundChangeApply($apply_info)
    {
		//检查参数合法性
		$this->checkFieldValid($apply_info);

		//保存到数据库
		$apply_info['operate_type'] = $this->operate_type;
		$apply_info['state'] = 0;
		$apply_info['addtime'] = time();
		$apply_info['isuse'] = 1;

		$this->add($apply_info);
		#echo "<pre>";echo $this->getLastSql();print_r($apply_info);die;

    }
 
	/**
	 * 编辑订单退换货申请
	 * @author 姜伟
	 * @param array	$apply_info
	 * @return void
	 * @todo 检查参数合法性，并保存到数据库
	 */
    public function editItemRefundChange($apply_info)
    {
		//检查参数合法性
		$this->checkFieldValid($apply_info);

		//保存到数据库
		$this->where('item_refund_change_id = ' . $this->ItemRefundChangeId)->save($apply_info);
    }
       
	/**
	 * 通过订单退换货申请
	 * @author 姜伟
	 * @param string $admin_remark 管理员备注
	 * @return void
	 * @todo 将退换货申请表中的该元组的state字段改为1，调用订单模型的passRefundApply/passExchangeApply方法变更订单表中的该订单状态
	 */
    public function passItemRefundChangeApply($admin_remark = '')
    {
		$ItemRefundChangeId = intval($this->ItemRefundChangeId);
		if ($ItemRefundChangeId)
		{
			//获取申请信息
			$apply_info = $this->getRefundChangeInfo();
			if ($apply_info)
			{
				$order_id = $apply_info['order_id'];
				//调用订单模型的方法变更订单状态
				$order_obj = new OrderModel($order_id);
				//$order_obj->setWhere();
				//$order_info = $order_obj->getOrderInfo('order_type');
				//if ($order_info && $order_info['order_type'] == 2)
				//{
				//	$order_obj = new VirtualStockOrderModel($order_id);
				//}
                $order_obj->passRefundApply($apply_info['item_ids']);

				//if ($apply_info['operate_type'] == 1)
				//{
				//}
				//elseif ($apply_info['operate_type'] == 2)
				//{
				//	$order_obj->passExchangeApply();
				//}
			}

			$arr = array(
				'state' => 1,
				'handle_time' => time(),
				'admin_remark' => $admin_remark
			);
            //die;
			$this->where('item_refund_change_id = ' . $ItemRefundChangeId)->save($arr);
            return true;
		}

		return false;
    }
    
	/**
	 * 拒绝订单退换货申请
	 * @author 姜伟
	 * @param string $admin_remark
	 * @return void
	 * @todo 将退换货申请表中的该元组的state字段改为2
	 */
    public function refuseItemRefundChangeApply($admin_remark = '')
    {
		if ($ItemRefundChangeId = intval($this->ItemRefundChangeId))
		{
			$this->where('item_refund_change_id = ' . $ItemRefundChangeId)->save(array('state' => 2, 'handle_time' => time(), 'admin_remark' => $admin_remark));

			//获取申请信息
			$apply_info = $this->getRefundChangeInfo();
			if ($apply_info)
			{
				//调用订单模型的方法变更订单状态
				require_once('Lib/Model/OrderModel.class.php');
				$order_obj = new OrderModel($apply_info['order_id']);
				$order_obj->refuseRefundApplyEvent();

				return true;
			}
		}

		return false;
    }
     
	/**
	 * 获取退换货申请数量
	 * @author 姜伟
	 * @param int $where，where查询子句
	 * @return int $num
	 * @todo 根据传入的where查询子句获取记录数量
	 */
    public function getItemRefundChangeNum($where = '')
    {
		$num = $this->where($where)->count();
		return $num;
	}

	/**
	 * 获得退换货申请信息和订单信息列表
	 * @author 姜伟
	 * @param string $fields
	 * @param string $where
	 * @param string $groupby
	 * @return array $order_list
	 * @return array $order_list
	 * @todo 根据where子句查询订单表中的订单信息，并以数组形式返回
	 */
    public function getItemRefundChangeList($fields = '', $where = '', $order = '', $limit = '', $groupby = '')
	{
		$fields = $fields ? $fields : 'item_refund_change_id, tp_order.order_id, item_ids, refund_money, reason, proof, state, ' . $this->tableName . '.addtime, handle_time, order_sn, pay_amount, tp_order.integral_amount, tp_order.total_amount, tp_order.express_fee, ' . $this->tableName . '.admin_remark';
		$order_list = $this->field($fields)->join(C('DB_PREFIX') . 'order AS tp_order ON tp_order.order_id = ' . $this->tableName . '.order_id ')->where($where)->group($groupby)->order($order)->limit($limit)->select();
		#echo $this->getLastSql();
		return $order_list;
    }
    
	/**
	 * 获得退货申请列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询数据库中operate_type=1的数据列表并以数组形式返回
	 */
    public function getItemRefundApplyList()
	{
		$refund_apply_list = $this->field('item_refund_change_id, tp_order.order_id, item_ids, refund_money, reason, proof, state, ' . $this->tableName . '.addtime, handle_time, tp_order.agent_name, pay_amount, tp_order.total_amount, tp_order.express_fee, ' . $this->tableName . '.admin_remark')->join(C('DB_PREFIX') . 'order AS tp_order ON tp_order.order_id = ' . $this->tableName . '.order_id ')->where('operate_type = 1 AND ' . $this->tableName . '.isuse = 1')->limit()->select();
		#echo $this->getLastSql();
		return $refund_apply_list;
    }

     /**
     * 获取退款信息
     * @author zlf
     * @param int $item_id 商品id
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找退款信息表中的相关数据并返回
     */
    public function getItemRefundChangeInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

     /**
     * 获取退款申请列表
     * @author zlf
     * @param int $item_id 商品id
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找退款信息表中的相关数据并返回
     */
    public function getItemRefundList($fields = '', $where)
    {
    	/*$refund_apply_list = $this->field('item_refund_change_id, handle_time, tp_order.order_sn, ' . $this->tableName . '.order_id, ' . $this->tableName . '.addtime, ' . $this->tableName . '.admin_remark')->where('operate_type = 1 AND ' . $this->tableName . '.isuse = 1 AND ' . $where)->join(C('DB_PREFIX') . 'order AS tp_order ON tp_order.order_id = ' . $this->tableName . '.order_id ')->limit()->select();
		echo "<pre>"; echo $this->getLastSql();die;
		return $refund_apply_list;*/
		return $this->field($fields)->where($where)->select();
    }
    
	/**
	 * 获得未处理的退货申请列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询数据库中operate_type=1，state=0的数据列表并以数组形式返回
	 */
    public function getUnhandledItemRefundApplyList()
    {
		$change_apply_list = $this->field('item_refund_change_id, tp_order.order_id, order_sn, item_ids, refund_money, reason, proof, state,' . $this->tableName.'.addtime' . ', handle_time, tp_order.agent_name, pay_amount, tp_order.total_amount, tp_order.express_fee, ' . $this->tableName . '.admin_remark')->join(C('DB_PREFIX') . 'order AS tp_order ON tp_order.order_id = ' . $this->tableName . '.order_id ')->where('operate_type = 1 AND state = 0 AND ' . $this->tableName . '.isuse = 1' . $this->where)->order($this->tableName.'.addtime DESC')->limit()->select();
		#echo $this->getLastSql();
		return $change_apply_list;
    }
     
	/**
	 * 获得换货申请列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询数据库中operate_type=2的数据列表并以数组形式返回
	 */
    public function getItemChangeApplyList()
    {
		$change_apply_list = $this->field('item_refund_change_id, tp_order.order_id, item_ids, refund_money, reason, proof, state, ' . $this->tableName . '.addtime, handle_time, tp_order.agent_name, pay_amount, tp_order.total_amount, tp_order.express_fee, ' . $this->tableName . '.admin_remark')->join(C('DB_PREFIX') . 'order AS tp_order ON tp_order.order_id = ' . $this->tableName . '.order_id ')->where('operate_type = 2 AND ' . $this->tableName . '.isuse = 1')->limit()->select();
		#echo $this->getLastSql();die;
		return $change_apply_list;
    }
    
    /**
	 * 获得换货申请列表
	 * @author zlf
	 * @param void
	 * @return void
	 * @todo 查询数据库中operate_type=2的数据列表并以数组形式返回
	 */
    public function getItemChangeList($fields = '', $where)
    {
		return $this->field($fields)->where($where)->select();
		
    }

	/**
	 * 获得未处理的换货申请列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询数据库中operate_type=2，state=0的数据列表并以数组形式返回
	 */
    public function getUnhandledItemChangeApplyList()
    {
		$change_apply_list = $this->field('item_refund_change_id, tp_order.order_id, order_sn, item_ids, refund_money, reason, proof, state, ' . $this->tableName . '.addtime, handle_time, tp_order.agent_name, tp_order.total_amount, pay_amount, tp_order.express_fee, ' . $this->tableName . '.admin_remark')->join(C('DB_PREFIX') . 'order AS tp_order ON tp_order.order_id = ' . $this->tableName . '.order_id ')->where('operate_type = 2 AND state = 0 AND ' . $this->tableName . '.isuse = 1' . $this->where)->limit()->select();
		#echo $this->getLastSql();
		return $change_apply_list;
    }
    
	/**
	 * 根据查询where子句获得查询列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 分页查询数据库中符合$where条件的数据列表并以数组形式返回
	 */
    public function getListByQueryString($where)
    {
		return $this->field('item_refund_change_id, order_id, item_ids, refund_money, reason, proof, state, addtime')->where($where)->limit()->select();
    }
    
	/**
	 * 修改退换货信息
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 检查数据合法性，并将数据保存到数据库
	 */
    public function editItemRefundChangeApply()
    {
		//检查参数合法性
		$this->checkFieldValid($apply_info);
		return $this->where('item_refund_change_id = ' . $this->ItemRefundChangeId)->save($apply_info);
    }

	/**
	 * 验证退换货信息合法性
	 * @author 姜伟
	 * @param array	$apply_info
	 * @return void
	 * @todo 遍历必要字段数组，一一检验传入的数组中是否包含该字段，若不包含，报错退出
	 */
	private function checkFieldValid($apply_info)
	{
		$field_list = array('order_id, item_ids, reason, proof');

		foreach ($field_list AS $field)
		{
			if (!isset($apply_info[$field]) || !intval($apply_info[$field]))
			{
				trigger_error('缺少参数' . $field . '或无效的参数' . $field);
			}
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
			$this->where = $where;
		}
	}

	/**
	 * 根据当前对象的ItemRefundChangeId字段获取退换货信息
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询退换货表中主键值为当前对象的ItemRefundChangeId的元组信息，并以数组形式返回
	 */
    public function getRefundChangeInfoByWhere($where, $fields = '')
	{
		return $this->field($fields)->where($where)->find();
	}

	/**
	 * 根据当前对象的ItemRefundChangeId字段获取退换货信息
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询退换货表中主键值为当前对象的ItemRefundChangeId的元组信息，并以数组形式返回
	 */
    public function getRefundChangeInfo()
	{
		$this->ItemRefundChangeId = intval($this->ItemRefundChangeId);
		if ($this->ItemRefundChangeId)
		{
			return $this->where('item_refund_change_id = ' . $this->ItemRefundChangeId)->find();
		}

		return null;
	}

	/**
	 * 设置当前退换货申请的退还金额
	 * @author 姜伟
	 * @param float $refund_money
	 * @return 失败返回false，成功返回数据库修改记录数量
	 * @todo 将退换货表中当前申请的退还金额改为$refund_money
	 */
    public function setRefundMoney($refund_money)
	{
		$this->ItemRefundChangeId = intval($this->ItemRefundChangeId);
		$refund_money = floatval($refund_money);
		if ($this->ItemRefundChangeId && $refund_money)
		{
			return $this->where('item_refund_change_id = ' . $this->ItemRefundChangeId)->save(array('refund_money' => $refund_money));
		}

		return false;
	}

	/**
	 * 处理状态数字转化文字
	 * @author 姜伟
	 * @param int $state
	 * @return string $state
	 * @todo 将订单来源由数字转化成文字后返回
	 */
    public function convertApplyState($state)
    {
		switch ($state)
		{
			case 0:
				$state = '待处理';
				break;
			case 1:
				$state = '已受理';
				break;
			case 2:
				$state = '已拒绝';
				break;
			case 3:
				$state = '已确认收货';
				break;
			default:
				$state = '';
				break;
		}

		return $state;
	}

	/**
	 * 获取退换货申请总数
	 * @author 姜伟
	 * @param int $apply_type，退换货类型，1退货，2换货
	 * @param int $where，where查询子句
	 * @return int $order_list
	 * @todo 退换货申请表关联订单表，根据传入的where查询子句获取记录数量
	 */
    public function getRefundChangeOrderNum($apply_type = 0, $where = '')
    {
		if ($apply_type)
		{
			$where .= $where ? $where . ' AND operate_type = ' . $apply_type : 'operate_type = ' . $apply_type;
		}

		$apply_info = $this->field('COUNT(*) AS total')->where($where)->find();
		return $apply_info ? $apply_info['total'] : 0;
	}

	/**
	 * 获取退换货申请信息及订单信息列表
	 * @author 姜伟
	 * @param string $fields 字段列表，英文逗号隔开
	 * @param int $apply_type，退换货类型，1退货，2换货
	 * @param int $where，where查询子句
	 * @return int $order_list
	 * @todo 退换货申请表关联订单表，根据传入的where查询子句获取订单列表
	 */
    public function getRefundChangeOrderList($fields, $apply_type = 0, $where = '')
    {
		if ($apply_type)
		{
			$where .= ' AND operate_type = ' . $apply_type;
		}

		$order_list = $this->field($fields)->join(C('DB_PREFIX') . 'order ON ' . C('DB_PREFIX') . 'order.order_id = ' . $this->tableName . '.order_id')->where('1' . $where)->order('addtime desc')->limit()->select();
		return $order_list;
	}

	/**
	 * 查看某订单是否存在正在处理的退货申请
	 * @author 姜伟
	 * @param int $order_id
	 * @return boolean 存在返回true，不存在返回false
	 * @todo 查看退换货表中是否存在订单ID为$order_id且状态为0的记录，若存在，返回true，否则返回false
	 */
    public function checkUnhandledOrderApplyExists($order_id)
    {
		$apply_info = $this->field('order_id')->where('state = 0 AND order_id = ' . intval($order_id))->find();
		return $apply_info ? true : false;
	}
 
	/**
	 * 通过订单退换货申请
	 * @author 姜伟
	 * @param string $admin_remark 管理员备注
	 * @return void
	 * @todo 
	 */
    public function setItemRefundChangeApplyState($state, $where = '')
	{
		$this->where($where)->save(array('state' => intval($state)));
	}
}

<?php

/**
 * 订单模型
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
class OrderLogModel extends Model
{
	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据表名称
	 */
    public function OrderLogModel()
    {
		$this->db(0);
		$this->tableName = C('DB_PREFIX') . 'order_log';
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
    public function saveLog($start_order_status, $end_order_status, $order_id, $user_id)
    {
		//插入数据库的字段
		$arr = array(
			'order_id'				=> $order_id,
			'user_id'				=> $user_id,
			'start_order_status'	=> $start_order_status,
			'end_order_status'		=> $end_order_status,
			'addtime'				=> time(),
			'ip'					=> get_client_ip(),
			'remark'				=> '用户名为' . $_SESSION['user_info']['username'] . '、ID为' . $user_id . '的用户于' . date('Y-m-d H:i:s', time()) . '将订单状态由' . $this->getOrderStateName($start_order_status) . '改为' . $this->getOrderStateName($end_order_status)
		);

		$this->add($arr);
    }

	public function getOrderStateName($order_status)
	{
		switch($order_status)
		{
			case OrderModel::PRE_PAY:
				return '未付款';
			case OrderModel::PAYED:
				return '已付款/待备货';
			case OrderModel::STOCKUPED:
				return '待发货';
			case OrderModel::DELIVERED:
				return '已发货';
			case OrderModel::REFUNDING:
				return '待退货';
			case OrderModel::REFUNDED:
				return '已退货';
			case OrderModel::CHANGING:
				return '待换货';
			case OrderModel::CHANGED:
				return '已换货';
			case OrderModel::CONFIRMED:
				return '已确认';
			case OrderModel::CANCELED:
				return '已关闭/已取消';
		}
	}

	/**
	 * 获取某订单状态变化明细
	 * @author 姜伟
	 * @param int $order_id 订单ID
	 * @return array $order_log_list
	 * @todo 查询订单日志表中该订单的状态变化记录
	 */
    public function getOrderLogList($order_id)
	{
		$order_log_list = $this->field('user_id, start_order_status, end_order_status, addtime, ip, remark')->where('order_id = ' . $order_id)->select();
		foreach ($order_log_list AS $k => $v)
		{
			$order_log_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
			$order_log_list[$k]['start_order_status'] = $this->getOrderStateName($v['start_order_status']);
			$order_log_list[$k]['end_order_status'] = $this->getOrderStateName($v['end_order_status']);
			$order_log_list[$k]['i'] = $k + 1;
		}

		return $order_log_list;
	}
}

<?php
/**
 * 分销商订单模型
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
require_once('Lib/Model/OrderBaseModel.class.php');
class AgentOrderModel extends OrderBaseModel
{
	/**
	 * 构造函数
	 * @author 姜伟
	 * @param int $order_id 订单ID
	 * @return void
	 * @todo 初始化数据库，数据表名称，订单ID
	 */
    public function __construct($order_id)
    {
		$this->db(0);
		$this->orderId = $order_id;
		$this->tableName = C('DB_PREFIX') . 'order';
		$this->order_type = 'AgentOrder';
	}
}

<?php
/**
 * 临时订单模型
 * @access public
 * @author 姜伟
 * @Date 2014-04-03
 */
class OrderTempModel extends Model
{
	/**
	 * 临时订单ID
	 */
    protected $orderTempId = 0;

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
    public function OrderTempModel($orderTempId = 0)
	{
		$this->db(0);
		$this->orderTempId = $orderTempId;
		$this->tableName = C('DB_PREFIX') . 'order_temp';
	}

	/**
	 * 生成临时订单，结算页用
	 * @author 姜伟
	 * @param array	$order_info	订单信息数组
	 * @param array	$item_info	商品信息数组
	 * @return $TemporderId	生成的订单ID
	 * @todo 将订单信息，商品信息序列化后存入临时订单表中
	 */
    public function addOrder($order_info = array())
    {
		$arr = array(
			'order_info'	=> json_encode($order_info),
			'user_id'		=> session('user_id'),
			'addtime'		=> time(),
		);
		#echo "<pre>";
		#print_r($arr);
		#echo "</pre>";

		return $this->add($arr);
    }

	/**
	 * 查询临时订单
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 查询临时订单表中当前对象id对应的元组
	 */
    public function getOrderInfo()
    {
		$user_id = session('user_id');
		return $this->where('user_id = ' . $user_id . ' AND order_temp_id = ' . $this->orderTempId)->find();
    }

	/**
	 * 删除临时订单
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 删除临时订单表中当前对象id对应的元组
	 */
    public function deleteOrder()
    {
		$user_id = session('user_id');
		return $this->where('user_id = ' . $user_id)->delete();
    }
}

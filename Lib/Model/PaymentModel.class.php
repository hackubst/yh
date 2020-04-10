<?php
/**
 * 支付方式模型基类
 * @access public
 * @author 姜伟
 * @Date 2014-04-03
 */
class PaymentModel extends Model
{
	/**
	 * 支付方式ID
	 */
    protected $paymentId = 0;

	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
    public function PaymentModel($paymentId = 0)
	{
		$this->db(0);
		$this->paymentId = intval($paymentId);
		$this->tableName = C('DB_PREFIX') . 'payway';
	}

	/**
	 * 获取支付方式信息
	 * @author 姜伟
	 * @param string $fields
	 * @param string $where
	 * @return array $payment_info
	 * @todo 根据当前对象的paymentId获取支付方式信息
	 */
    public function getPaymentInfo($fields = '', $where = '')
    {
		if (!$where)
		{
			if ($this->paymentId)
		}
		return $this->field($fields)->where($where)->find();
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
		return $this->where('user_id = ' . $user_id . ' AND order_temp_id = ' . $this->orderTempId)->delete();
    }

    /** */
    public void setPaymentInfo(Object 设置支付信息，参数 支付信息数组)
    {
    
    }
    
    /** */
    public void generatePayCode(Object 生成支付代码，无参)
    {
    
    }
    
    /** */
    public void payResponse(Object 支付成功后的处理方法，无参)
    {
    
    }
    
    /** */
    public void checkPaySign(Object 检验 签名正确性，参数 签名)
    {
    
    }
    
    /** */
    public void generatePaySign(Object 生成签名，无参)
    {
    
    }
}

<?php
/**
 * 分销模型类
 */

class FenxiaoModel extends Model
{
    /**
     * 构造函数
     * @author 姜伟
     * @param void
     * @return void
     * @todo 
     */
    public function FenxiaoModel()
    {
    }

	/**
     * 分销是否开启
     * @author 姜伟
     * @param void
     * @return void
     * @todo 分销是否开启
     */
    public static function isFenxiaoOpen()
    {
		return $GLOBALS['config_info']['IS_FENXIAO_OPEN'];
    }

    /**
     * 一级代理提成处理
     * @author 姜伟
     * @param int $order_id 
     * @param int $user_id 
     * @param float $amount
     * @return 
     * @todo 一级代理提成处理
     */
    public function fenxiao9($order_id, $user_id, $amount)
    {
    }

    /**
     * 二级代理提成处理
     * @author 姜伟
     * @param int $order_id 
     * @param int $user_id 
     * @param float $amount
     * @return 
     * @todo 二级代理提成处理
     */
    public function fenxiao10($order_id, $user_id, $amount)
    {
    }

    /**
     * 三级代理提成处理
     * @author 姜伟
     * @param int $order_id 
     * @param int $user_id 
     * @param float $amount
     * @return 
     * @todo 三级代理提成处理
     */
    public function fenxiao11($order_id, $user_id, $amount)
    {
    }
}

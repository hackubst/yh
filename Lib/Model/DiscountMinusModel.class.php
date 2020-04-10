<?php
/**
 * 优惠券模型类
 */

class DiscountMinusModel extends PromoBaseModel
{
    // 优惠券id
    public $discount_minus_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param $discount_minus_id 优惠券ID
     * @return void
     * @todo 初始化优惠券id
     */
    public function DiscountMinusModel($discount_minus_id)
    {
        parent::__construct('discount_minus');

        if ($discount_minus_id = intval($discount_minus_id))
		{
            $this->discount_minus_id = $discount_minus_id;
		}
    }

    /**
     * 获取优惠券信息
     * @author 姜伟
     * @param int $discount_minus_id 优惠券id
     * @param string $fields 要获取的字段名
     * @return array 优惠券基本信息
     * @todo 根据where查询条件查找优惠券表中的相关数据并返回
     */
    public function getDiscountMinusInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改优惠券信息
     * @author 姜伟
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 修改优惠券信息
     */
    public function editDiscountMinus($arr)
    {
        return $this->where('discount_minus_id = ' . $this->discount_minus_id)->save($arr);
    }

    /**
     * 添加优惠券
     * @author 姜伟
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 添加优惠券
     */
    public function addDiscountMinus($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除优惠券
     * @author 姜伟
     * @param int $discount_minus_id 优惠券ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delDiscountMinus($discount_minus_id)
    {
        if (!is_numeric($discount_minus_id)) return false;
		return $this->where('discount_minus_id = ' . $discount_minus_id)->delete();
    }

    /**
     * 根据where子句获取优惠券数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的优惠券数量
     * @todo 根据where子句获取优惠券数量
     */
    public function getDiscountMinusNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据merchant_id查询是否已优惠券
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果
     * @todo 查询是否已优惠券
     */
    public function getMerchantIsDiscountMinus($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询优惠券信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 优惠券基本信息
     * @todo 根据SQL查询字句查询优惠券信息
     */
    public function getDiscountMinusList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取优惠券列表页数据信息列表
     * @author 姜伟
     * @param array $discount_minus_list
     * @return array $discount_minus_list
     * @todo 根据传入的$discount_minus_list获取更详细的优惠券列表页数据信息列表
     */
    public function getListData($discount_minus_list)
    {
		foreach ($discount_minus_list AS $k => $v)
		{
		}

		return $discount_minus_list;
    }

    /**
     * 获取用户在店铺中的满减优惠和推荐优惠
     * @author 姜伟
     * @param int $merchant_id
     * @param float $total_amount
     * @return array $coupon_info
     * @todo 获取用户在店铺中的满减优惠和推荐优惠
	 * 1、查该商家是否存在进行中的满减活动，若有，遍历之（按订单满多少的金额降序排列）；若无，退出，返回两个空数组；
	 * 2、将符合条件(scope满足，use_time未超)的活动放入同一数组A中，并记录扣减的金额；
	 * 3、取数据库中刚好不满足条件的优惠存入推荐优惠B数组中，记录需要再消费可享受该优惠的金额；
	 * 4、选择A数组中限制金额最大(从事实情况来说，这也是抵扣额度最大的)的作为优惠，并放入优惠数组C中；
	 * 5、返回数组B和数组C
     */
    public function getCouponInfo($merchant_id, $total_amount)
    {
		$where_arr = $this->getWhere($merchant_id, $total_amount);
		$where = $where_arr['where'];
		$rec_where = $where_arr['rec_where'];

		//字段
		$fields = 'discount_minus_id, merchant_id, num, amount_limit, use_time, title, scope';

		//获取可用满减活动列表
		$coupon_list = $this->getDiscountMinusList($fields, $where, 'amount_limit DESC');

		foreach ($coupon_list AS $k => $v)
		{
			//判断scope是否满足条件
			if (!$this->checkScope($v['scope']))
			{
				unset($coupon_list[$k]);
			}

			$this->discount_minus_id = $v['discount_minus_id'];
			//判断是否超出使用/参与次数限制
			if ($this->checkIsUserTimeLimited($v['use_time']))
			{
				unset($coupon_list[$k]);
			}
		}

		$coupon_list = resort($coupon_list);

		//已享受优惠
		$shared_coupon = $coupon_list ? $coupon_list[0] : null;

		//推荐优惠
		$rec_coupon = $this->getDiscountMinusInfo($rec_where, $fields);
		$rec_coupon = $rec_coupon ? $rec_coupon : null;
		if ($rec_coupon)
		{
			$rec_coupon['need_consume'] = $rec_coupon['amount_limit'] - $total_amount;
		}

		//返回的数组
		$return_arr = array(
			'shared_coupon'	=> $shared_coupon,
			'rec_coupon'	=> $rec_coupon,
		);

		return $return_arr;
	}

    /**
     * 获取某用户某活动参与次数
     * @author 姜伟
     * @param int $user_id
     * @return boolean
     * @todo 获取某用户某活动参与次数
     */
	function getUsedTime($user_id = 0)
	{
		$user_id = $user_id ? $user_id : intval(session('user_id'));
		$where = 'user_id = ' . $user_id . ' AND discount_minus_id = ' . $this->discount_minus_id;
		$user_discount_minus_obj = new UserDiscountMinusModel();
		$used_time = $user_discount_minus_obj->getUserDiscountMinusNum($where);

		return $used_time ? $used_time : 0;
	}
}

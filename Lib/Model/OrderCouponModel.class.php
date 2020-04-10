<?php
/**
 * 订单优惠模型类
 */

class OrderCouponModel extends Model
{
    // 订单优惠id
    public $order_coupon_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $order_coupon_id 订单优惠ID
     * @return void
     * @todo 初始化订单优惠id
     */
    public function OrderCouponModel($order_coupon_id)
    {
        parent::__construct('order_coupon');

        if ($order_coupon_id = intval($order_coupon_id))
		{
            $this->order_coupon_id = $order_coupon_id;
		}
    }

    /**
     * 获取订单优惠信息
     * @author 姜伟
     * @param int $order_coupon_id 订单优惠id
     * @param string $fields 要获取的字段名
     * @return array 订单优惠基本信息
     * @todo 根据where查询条件查找订单优惠表中的相关数据并返回
     */
    public function getOrderCouponInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改订单优惠信息
     * @author 姜伟
     * @param array $arr 订单优惠信息数组
     * @return boolean 操作结果
     * @todo 修改订单优惠信息
     */
    public function editOrderCoupon($arr)
    {
        return $this->where('order_coupon_id = ' . $this->order_coupon_id)->save($arr);
    }

    /**
     * 添加订单优惠
     * @author 姜伟
     * @param array $arr 订单优惠信息数组
     * @return boolean 操作结果
     * @todo 添加订单优惠
     */
    public function addOrderCoupon($arr)
    {
        if (!is_array($arr)) return false;

        return $this->add($arr);
    }

    /**
     * 删除订单优惠
     * @author 姜伟
     * @param int $order_coupon_id 订单优惠ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delOrderCoupon($order_coupon_id)
    {
        if (!is_numeric($order_coupon_id)) return false;
		return $this->where('order_coupon_id = ' . $order_coupon_id)->delete();
    }

    /**
     * 根据where子句获取订单优惠数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的订单优惠数量
     * @todo 根据where子句获取订单优惠数量
     */
    public function getOrderCouponNum($where = '')
    {
        return $this->where($where)->count();
    }
    
    /**
     * 根据merchant_id查询是否已订单优惠
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果 
     * @todo 查询是否已订单优惠
     */
    public function getMerchantIsOrderCoupon($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询订单优惠信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 订单优惠基本信息
     * @todo 根据SQL查询字句查询订单优惠信息
     */
    public function getOrderCouponList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取订单优惠列表页数据信息列表
     * @author 姜伟
     * @param array $order_coupon_list
     * @return array $order_coupon_list
     * @todo 根据传入的$order_coupon_list获取更详细的订单优惠列表页数据信息列表
     */
    public function getListData($order_coupon_list)
    {
		foreach ($order_coupon_list AS $k => $v)
		{
			//商户信息
			$merchant_obj = new MerchantModel($v['merchant_id']);
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $v['merchant_id'], 'shop_name, isuse, make_time_avg, on_time_rate, score_avg, logo, longitude, latitude, trading_area_id, class_id');
			//获取商户类型
			$class_obj = new ClassModel();
			$class_name = $class_obj->getClassField($merchant_info['class_id'], 'class_name');
			//获取商户商圈
			$area_obj = new TradingAreaModel();
			$area_name = $area_obj->getTradingAreaInfo('trading_area_id =' . $merchant_info['trading_area_id'], 'trading_area_name');
			$order_coupon_list[$k]['shop_name'] = $merchant_info['shop_name'];
			$order_coupon_list[$k]['make_time_avg'] = $merchant_info['make_time_avg'];
			$order_coupon_list[$k]['score_avg'] = $merchant_info['score_avg'];
			$order_coupon_list[$k]['on_time_rate'] = $merchant_info['on_time_rate'];
			$order_coupon_list[$k]['logo'] = $merchant_info['logo'];
			$order_coupon_list[$k]['class_name'] = $class_name;
			$order_coupon_list[$k]['area_name'] = $area_name['trading_area_name'];

			$status = '';
			if ($merchant_info['isuse'] == 0)
			{
				$status = '已失效';
			}
			elseif ($merchant_info['isuse'] == 1)
			{
				$status = '线上商户';
			}

			//用户名称
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname, realname');
			$username = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
			$order_coupon_list[$k]['username'] = $username;
		}

		return $order_coupon_list;
    }
}

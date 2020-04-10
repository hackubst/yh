<?php
/**
 * 优惠券模型类
 */

class UserDiscountMinusModel extends BaseModel
{
    // 优惠券id
    public $user_discount_minus_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param $user_discount_minus_id 优惠券ID
     * @return void
     * @todo 初始化优惠券id
     */
    public function UserDiscountMinusModel($user_discount_minus_id)
    {
        parent::__construct('user_discount_minus');

        if ($user_discount_minus_id = intval($user_discount_minus_id))
		{
            $this->user_discount_minus_id = $user_discount_minus_id;
		}
    }

    /**
     * 获取优惠券信息
     * @author 姜伟
     * @param int $user_discount_minus_id 优惠券id
     * @param string $fields 要获取的字段名
     * @return array 优惠券基本信息
     * @todo 根据where查询条件查找优惠券表中的相关数据并返回
     */
    public function getUserDiscountMinusInfo($where, $fields = '')
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
    public function editUserDiscountMinus($arr)
    {
        return $this->where('user_discount_minus_id = ' . $this->user_discount_minus_id)->save($arr);
    }

    /**
     * 添加优惠券
     * @author 姜伟
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 添加优惠券
     */
    public function addUserDiscountMinus($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除优惠券
     * @author 姜伟
     * @param int $user_discount_minus_id 优惠券ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delUserDiscountMinus($user_discount_minus_id)
    {
        if (!is_numeric($user_discount_minus_id)) return false;
		return $this->where('user_discount_minus_id = ' . $user_discount_minus_id)->delete();
    }

    /**
     * 根据where子句获取优惠券数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的优惠券数量
     * @todo 根据where子句获取优惠券数量
     */
    public function getUserDiscountMinusNum($where = '')
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
    public function getMerchantIsUserDiscountMinus($merchant_id)
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
    public function getUserDiscountMinusList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取优惠券列表页数据信息列表
     * @author 姜伟
     * @param array $user_discount_minus_list
     * @return array $user_discount_minus_list
     * @todo 根据传入的$user_discount_minus_list获取更详细的优惠券列表页数据信息列表
     */
    public function getListData($list)
    {
        if (!count($list)) return NULL;

        $merchant_obj = D('Merchant');
        $order_obj    = D('Order');
        $discount_obj = D('DiscountMinus');

        foreach ($list AS $k => $v)
        {
            //商户信息
            if ($v['merchant_id']) {
                $shop_name = $merchant_obj->where('merchant_id = ' . $v['merchant_id'])
                    ->getField('shop_name');

                $shop_name = $shop_name ? $shop_name : '未设置';
                $list[$k]['shop_name'] = $shop_name;
            }

            //用户名称
            if ($v['user_id']) {
                $user_obj = new UserModel($v['user_id']);
                $user_info = $user_obj->getUserInfo('nickname, realname');
                $username = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
                $list[$k]['username'] = $username ? $username : '未设置';
            }

            //订单信息
            if ($v['order_id']) {
                $order_sn  = $order_obj->where('order_id = ' . $v['order_id'])
                    ->getField('order_sn');
                $list[$k]['order_sn'] = $order_sn ? $order_sn : '--';
            }
            //减免信息
            if ($v['discount_minus_id']) {
                $discount_info = $discount_obj->where('discount_minus_id =' . $v['discount_minus_id'])->field('amount_limit, num')->find();
                if ($discount_info) {
                    $list[$k]['content'] = '满' .$discount_info['amount_limit']
                        . '减' . $discount_info['num'];
                } else {
                    $list[$k]['content'] = '--';
                }

            }

        }

        return $list;
    }
}

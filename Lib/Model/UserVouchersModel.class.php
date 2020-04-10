<?php
/**
 * 优惠券模型类
 */

class UserVouchersModel extends PromoBaseModel
{
    // 优惠券id
    public $user_vouchers_id;
    public $vouchers_id;

    /**
     * 构造函数
     * @author wsq
     * @param $user_vouchers_id 优惠券ID
     * @return void
     * @todo 初始化优惠券id
     */
    public function UserVouchersModel($user_vouchers_id)
    {
        parent::__construct('user_vouchers');

        if ($user_vouchers_id = intval($user_vouchers_id))
		{
            $this->user_vouchers_id = $user_vouchers_id;
		}
    }

    /**
     * 获取优惠券信息
     * @author wsq
     * @param int $user_vouchers_id 优惠券id
     * @param string $fields 要获取的字段名
     * @return array 优惠券基本信息
     * @todo 根据where查询条件查找优惠券表中的相关数据并返回
     */
    public function getUserVouchersInfo($where, $fields = '', $orderby = '')
    {
		return $this->field($fields)->where($where)->order($orderby)->find();
    }

    /**
     * 修改优惠券信息
     * @author wsq
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 修改优惠券信息
     */
    public function editUserVouchers($arr)
    {
        return $this->where('user_vouchers_id = ' . $this->user_vouchers_id)->save($arr);
    }

    /**
     * 添加优惠券
     * @author wsq
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 添加优惠券
     */
    public function addUserVouchers($arr)
    {
        if (!is_array($arr)) return false;

		$arr['isuse'] = 1;

        return $this->add($arr);
    }

    /**
     * 删除优惠券
     * @author wsq
     * @param int $user_vouchers_id 优惠券ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delUserVouchers($user_vouchers_id)
    {
        if (!is_numeric($user_vouchers_id)) return false;
		return $this->where('user_vouchers_id = ' . $user_vouchers_id)->delete();
    }

    /**
     * 根据where子句获取优惠券数量
     * @author wsq
     * @param string|array $where where子句
     * @return int 满足条件的优惠券数量
     * @todo 根据where子句获取优惠券数量
     */
    public function getUserVouchersNum($where = '')
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
    public function getMerchantIsUserVouchers($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询优惠券信息
     * @author wsq
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 优惠券基本信息
     * @todo 根据SQL查询字句查询优惠券信息
     */
    public function getUserVouchersList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取优惠券列表页数据信息列表
     * @author wsq
     */
    public function getListData($list)
    {
        $merchant_obj = D('Merchant');
        $order_obj    = D('Order');
        $vouchers_obj = D('Vouchers');

        foreach ($list AS $k => $v) {
            // //商户信息
            // $shop_name = $merchant_obj->where('merchant_id = ' . $v['merchant_id'])->getField('shop_name');
            // $shop_name = $shop_name? $shop_name :'系统平台';
            // $list[$k]['shop_name'] = $shop_name;

            //用户名称
            // $user_obj  = new UserModel($v['user_id']);
            // $user_info = $user_obj->getUserInfo('nickname, realname');
            // $username  = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
            // $list[$k]['username'] = $username? $username: '未设置';

            //商品信息
            // $order_sn = $order_obj->where('order_id = ' . $v['order_id'])->getField('order_sn');
            // $list[$k]['order_sn'] = $order_sn ? $order_sn : '--';
            // $list[$k]['status'] = $v['isuse'] ? '进行中':'未启用';

            //获取优惠券信息
            $vouchers_info = $vouchers_obj->where('vouchers_id = ' . $v['vouchers_id'])->field('amount_limit,num,genre_id')->find();

            //拼接活动信息
            $list[$k]['content'] = $shop_name . ' 满' . $vouchers_info['amount_limit'] . ' 抵' . $vouchers_info['num'];

            $list[$k]['use_limit_desc'] = CouponSetModel::getUseLimitDesc($vouchers_info['amount_limit'], 0,0, $vouchers_info['genre_id']);

        }
        return $list;
    }

    /**
     * 获取前台优惠券列表页数据信息列表
     * @author jw
     */
    public function getFrontListData($list)
    {
        $merchant_obj = D('Merchant');
        $order_obj    = D('Order');
        $vouchers_obj = D('Vouchers');

		$merchant_obj = new MerchantModel();
		foreach ($list AS $k => $v)
		{
			//商家logo
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $v['merchant_id'], 'logo');
            $list[$k]['logo'] = $merchant_info['logo'];

            //拼接活动信息
            $list[$k]['content'] = $shop_name . ' 满' . $v['amount_limit'] . ' 抵' . $v['num']/* . '（可使用' . $v['use_time'] . '次）'*/;
            $list[$k]['valid_date'] = '有效期' . date('m.d H:i', $v['start_time']) . '-' . date('m.d H:i', $v['end_time']);

			//状态
			$status = $v['isuse'] ? ($v['end_time'] > time() ? 1 : 2) : 0;
            $list[$k]['status'] = $status;
        }
        return $list;
    }

    /**
     * 获取用户在店铺/平台已享受的抵价优惠和推荐优惠
     * @author 姜伟
     * @param int $merchant_id
     * @param int $total_amount
     * @return array $coupon_info
     * @todo 获取用户在店铺/平台已享受的抵价优惠和推荐优惠
	 * 1、查该用户是否存在可用的该商家/平台优惠券，若有，遍历之（按订单满多少的金额降序排列）；若无，退出，返回两个空数组；
	 * 2、将符合条件(scope满足，use_time未超)的活动放入同一数组A中；
	 * 3、取数据库中刚好不满足条件的优惠存入推荐优惠B数组中，记录需要再消费可享受该优惠的金额；
	 * 4、选择A数组中限制金额最大(从事实情况来说，这也是优惠最大的)的作为优惠，并放入优惠数组C中；
	 * 5、返回数组B和数组C
     */
    public function getCouponInfo($merchant_id, $total_amount, $user_id = 0)
    {
		//获取用户ID
		$user_id = $user_id ? $user_id : intval(session('user_id'));
		$where_arr = $this->getWhere($merchant_id, $total_amount);
		$where = $where_arr['where'];
		$rec_where = $where_arr['rec_where'];
		$where .= ' AND user_id = ' . $user_id;
		$rec_where .= ' AND user_id = ' . $user_id;

		//字段
		$fields = 'user_vouchers_id, vouchers_id, merchant_id, num, amount_limit, use_time, title, scope';

		//获取可用满减活动列表
		$coupon_list = $this->getUserVouchersList($fields, $where, 'amount_limit DESC, num DESC');
#echo "<pre>";
#echo $this->getLastSql();
#print_r($coupon_list);

		$vouchers_obj = new VouchersModel();
		foreach ($coupon_list AS $k => $v)
		{
			//判断scope是否满足条件
			if (!$this->checkScope($v['scope']))
			{
				unset($coupon_list[$k]);
			}

			$this->vouchers_id = $v['vouchers_id'];

			//判断是否超出使用/参与次数限制
			$vouchers_info = $vouchers_obj->getVouchersInfo('vouchers_id = ' . $v['vouchers_id'], 'use_time');
			$use_time = $vouchers_info && $vouchers_info['use_time'] ? $vouchers_info['use_time'] : 0;
#echo 'use_time = ' . $use_time . ', num = ' . $num . "<br>";
			if ($use_time > 0 && $num = $this->checkIsUserTimeLimited($use_time))
			{
				unset($coupon_list[$k]);
			}
		}

		$coupon_list = resort($coupon_list);

		//已享受优惠
		$shared_coupon = $coupon_list ? $coupon_list[0] : null;
		if ($shared_coupon)
		{
			$shared_coupon['type'] = $merchant_id ? 'merchant' : 'system';
			#$total_amount = $shared_coupon['num'] > $total_amount ? 0 : $total_amount - $shared_coupon['num'];
#echo "total_amount = " . $total_amount . "<br>";
		}

		//推荐优惠
		$rec_where .= ' AND amount_limit > ' . $total_amount;
		$rec_where .= $shared_coupon ? ' AND vouchers_id != ' . $shared_coupon['vouchers_id'] : '';
		$rec_coupon = $this->getUserVouchersInfo($rec_where, $fields, 'amount_limit ASC');
#echo $this->getLastSql() . "<br>";
		$rec_coupon = $rec_coupon ? $rec_coupon : null;
		if ($rec_coupon)
		{
			//判断scope是否满足条件
			if (!$this->checkScope($rec_coupon['scope']))
			{
				$rec_coupon = null;
			}
			else
			{
				$this->vouchers_id = $rec_coupon['vouchers_id'];

				//判断是否超出使用/参与次数限制
				$vouchers_info = $vouchers_obj->getVouchersInfo('vouchers_id = ' . $rec_coupon['vouchers_id'], 'use_time');
				$use_time = $vouchers_info && $vouchers_info['use_time'] ? $vouchers_info['use_time'] : 0;
				if ($use_time > 0 && $num = $this->checkIsUserTimeLimited($use_time))
				{
					$rec_coupon = null;
				}
			}
		}

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
		$where = 'isuse = 0 AND user_id = ' . $user_id . ' AND vouchers_id = ' . $this->vouchers_id;
		$used_time = $this->getUserVouchersNum($where);

		return $used_time ? $used_time : 0;
	}

    /**
     * 使用优惠券
     * @author 姜伟
     * @param int $user_vouchers_id
     * @return boolean
     * @todo 使用优惠券
     */
	function useCoupon($user_vouchers_id, $order_id, $order_amount)
	{
		$this->user_vouchers_id = $user_vouchers_id;
		$arr = array(
			'isuse'		=> 0,
			'order_amount'	=> $order_amount,
			'order_id'	=> $order_id,
			'use_time'	=> time(),
		);
		return $this->editUserVouchers($arr);
	}

    /**
     * 返还优惠券
     * @author 姜伟
     * @param int $user_vouchers_id
     * @return boolean
     * @todo 返还优惠券
     */
	function returnCoupon($user_vouchers_id)
	{
		$this->user_vouchers_id = $user_vouchers_id;
		$arr = array(
			'isuse'		=> 1,
			'order_amount'	=> 0,
			'order_id'	=> 0,
			'use_time'	=> 0,
		);
		return $this->editUserVouchers($arr);
	}
}

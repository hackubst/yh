<?php
/**
 * 买赠活动模型类
 */

class BuyGiveModel extends PromoBaseModel
{
    // 买赠活动id
    public $buy_give_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param $buy_give_id 买赠活动ID
     * @return void
     * @todo 初始化买赠活动id
     */
    public function BuyGiveModel($buy_give_id)
    {
        parent::__construct('buy_give');

        if ($buy_give_id = intval($buy_give_id))
		{
            $this->buy_give_id = $buy_give_id;
		}
    }

    /**
     * 获取买赠活动信息
     * @author 姜伟
     * @param int $buy_give_id 买赠活动id
     * @param string $fields 要获取的字段名
     * @return array 买赠活动基本信息
     * @todo 根据where查询条件查找买赠活动表中的相关数据并返回
     */
    public function getBuyGiveInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改买赠活动信息
     * @author 姜伟
     * @param array $arr 买赠活动信息数组
     * @return boolean 操作结果
     * @todo 修改买赠活动信息
     */
    public function editBuyGive($arr)
    {
        return $this->where('buy_give_id = ' . $this->buy_give_id)->save($arr);
    }

    /**
     * 添加买赠活动
     * @author 姜伟
     * @param array $arr 买赠活动信息数组
     * @return boolean 操作结果
     * @todo 添加买赠活动
     */
    public function addBuyGive($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除买赠活动
     * @author 姜伟
     * @param int $buy_give_id 买赠活动ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delBuyGive($buy_give_id)
    {
        if (!is_numeric($buy_give_id)) return false;
		return $this->where('buy_give_id = ' . $buy_give_id)->delete();
    }

    /**
     * 根据where子句获取买赠活动数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的买赠活动数量
     * @todo 根据where子句获取买赠活动数量
     */
    public function getBuyGiveNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据merchant_id查询是否已买赠活动
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果
     * @todo 查询是否已买赠活动
     */
    public function getMerchantIsBuyGive($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询买赠活动信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 买赠活动基本信息
     * @todo 根据SQL查询字句查询买赠活动信息
     */
    public function getBuyGiveList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取买赠活动列表页数据信息列表
     * @author 姜伟
     * @param array $buy_give_list
     * @return array $buy_give_list
     * @todo 根据传入的$buy_give_list获取更详细的买赠活动列表页数据信息列表
     */
    public function getListData($buy_give_list)
    {
		foreach ($buy_give_list AS $k => $v)
		{
			//商户信息
			$merchant_obj = new MerchantModel($v['merchant_id']);
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $v['merchant_id'], 'shop_name');
			$buy_give_list[$k]['shop_name'] = $merchant_info['shop_name'];

			//手机号
			$user_obj = new UserModel($v['merchant_id']);
			$user_info = $user_obj->getUserInfo('mobile');
			$buy_give_list[$k]['mobile'] = $user_info['mobile'];

			//赠送描述
			$buy_give_list[$k]['send_desc'] = self::getSendDesc($v);

			//状态
			$buy_give_list[$k]['isuse_desc'] = $v['isuse'] ? '有效' : '无效';
		}

		return $buy_give_list;
    }

    /**
     * 获取赠送描述
     * @author 姜伟
     * @param array $data
     * @param boolean $api
     * @return array $send_desc
     * @todo 获取赠送描述
     */
    public static function getSendDesc($data, $api = false)
    {
		$send_desc = '';
		if ($data['gift_id'])
		{
			$gift_obj = new GiftModel();
			$gift_info = $gift_obj->getGiftInfo('gift_id = ' . $data['gift_id'], 'gift_name');
			$send_desc .= '礼品：' . $gift_info['gift_name'];
		}

		if ($data['vouchers_id'])
		{
			$new_line = $api ? "\n" : "<br>";
			$vouchers_obj = new VouchersModel();
			$vouchers_info = $vouchers_obj->getVouchersInfo('vouchers_id = ' . $data['vouchers_id'], '');
			$send_desc .= $send_desc ? $new_line : '';
			$send_desc .= $vouchers_info['merchant_id'] ? "商家" : '系统';
			$send_desc .= '优惠券：面额' . $vouchers_info['num'] . '，满' . $vouchers_info['amount_limit'] . '元可使用，赠送' . $data['give_num'] . '张';
			#echo "<hr>" . $vouchers_info['amount_limit'] . "<hr>";
		}

		return $send_desc;
	}

    /**
     * 获取镖金券是否有效列表
     * @author 姜伟
     * @param void
     * @return array $isuse_list
     * @todo 获取镖金券是否有效列表
     */
    public static function getIsuseList()
    {
		return array(
			'0'	=> '是',
			'1'	=> '否',
		);
    }

    /**
     * 获取用户在店铺/平台已享受的买赠优惠和推荐优惠
     * @author 姜伟
     * @param int $merchant_id
     * @param int $total_amount
     * @return array $coupon_info
     * @todo 获取用户在店铺/平台已享受的买赠优惠和推荐优惠
	 * 1、查该商家/平台是否存在进行中的买赠活动，若有，遍历之（按订单满多少的金额降序排列）；若无，退出，返回两个空数组；
	 * 2、将符合条件(scope满足，use_time未超)的活动放入同一数组A中；
	 * 3、取数据库中刚好不满足条件的优惠存入推荐优惠B数组中，记录需要再消费可享受该优惠的金额；
	 * 4、选择A数组中限制金额最大(从事实情况来说，这也是优惠最大的)的作为优惠，并放入优惠数组C中；
	 * 5、返回数组B和数组C
     */
    public function getCouponInfo($merchant_id, $total_amount)
    {
		$where_arr = $this->getWhere($merchant_id, $total_amount);
		$where = $where_arr['where'];
		$rec_where = $where_arr['rec_where'];

		//字段
		$fields = 'buy_give_id, merchant_id, gift_id, vouchers_id, give_num, amount_limit, use_time, title, scope';

		//获取可用满减活动列表
		$coupon_list = $this->getBuyGiveList($fields, $where, 'amount_limit DESC');
		#echo $this->getLastSql();

		foreach ($coupon_list AS $k => $v)
		{
			//判断scope是否满足条件
			if (!$this->checkScope($v['scope']))
			{
				unset($coupon_list[$k]);
			}

			$this->buy_give_id = $v['buy_give_id'];
			//判断是否超出使用/参与次数限制
			if ($this->checkIsUserTimeLimited($v['use_time']))
			{
				unset($coupon_list[$k]);
			}
		}

		$coupon_list = resort($coupon_list);

		//已享受优惠
		$shared_coupon = $coupon_list ? $coupon_list[0] : null;
		if ($shared_coupon)
		{
			//获取奖品
			$shared_coupon['send_desc'] = self::getSendDesc($shared_coupon, true);
		}

		//推荐优惠
		$rec_coupon = $this->getBuyGiveInfo($rec_where, $fields);
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
		$where = 'user_id = ' . $user_id . ' AND buy_give_id = ' . $this->buy_give_id;
		$user_buy_give_obj = new UserBuyGiveModel();
		$used_time = $user_buy_give_obj->getUserBuyGiveNum($where);

		return $used_time ? $used_time : 0;
	}
}

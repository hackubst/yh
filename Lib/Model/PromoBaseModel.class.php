<?php
/**
 * 促销基类
 */

class PromoBaseModel extends BaseModel
{
	public $area_id;

	//常量
	const SCOPE_ALL = 0;
	const SCOPE_WX = 1;
	const SCOPE_APP = 2;

    /**
     * 判断客户端条件是否满足
     * @author 姜伟
     * @param int $scope
     * @return boolean
     * @todo 判断客户端条件是否满足
     */
	function checkScope($scope)
	{
		$client_scope = intval(session('client_scope'));
		if ($scope != self::SCOPE_ALL && $client_scope != $scope)
		{
			return false;
		}

		return true;
	}

    /**
     * 判断客户端条件是否满足
     * @author 姜伟
     * @param int $scope
     * @return boolean
     * @todo 判断客户端条件是否满足
     */
	function checkIsUserTimeLimited($use_time)
	{
		$used_time = $this->getUsedTime($user_id);
		if ($use_time <= $used_time)
		{
			return true;
		}

		return false;
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
	}

    /**
     * 获取where查询条件
     * @author 姜伟
     * @param int $merchant_id
     * @param float $total_amount
     * @param float $genre_ids
     * @return boolean
     * @todo 获取where查询条件
     */
	function getWhere($merchant_id, $total_amount, $genre_ids)
	{
		//启用
		$where = 'isuse = 1';
		//时间在有效期内
		$where .= ' AND start_time <= ' . time() . ' AND end_time > ' . time();
		//当前商家
		$where .= ' AND (merchant_id = 0 OR merchant_id = ' . $merchant_id . ')';
		$rec_where = $where;
		#$rec_where .= ' AND amount_limit > ' . $total_amount;
		//满足满额条件
		$where .= ' AND amount_limit <= ' . $total_amount;
		//三级分类条件
		$where .= ' AND genre_id IN(' . $genre_ids . ')';

		//返回结果
		$return_arr = array(
			'where'		=> $where,
			'rec_where'	=> $rec_where,
		);
		#dump($return_arr);

		return $return_arr;
	}

    /**
     * 根据传入的商品信息列表获取各类商品优惠、商家抵价/满减/买赠优惠、平台抵价/买赠优惠
     * @author 姜伟
     * @param array $item_list
     * @param int $user_id
     * @return array coupon_info
	 * 1、$total_amount & $discount_amount & $coupon 每个商家的优惠后总金额、优惠总金额、优惠内容；
	 * 2、$total_amount 优惠前商品总金额；
	 * 3、$item_amount 订单优惠后总价（镖金除外）；
	 * 4、$discount_amount 优惠总金额；
	 * 5、$system_discount_amount 系统优惠总金额；
	 * 6、$coupon_info 订单所有优惠内容；
	 * 7、$total_amount 优惠券商品总金额；
     * @todo 根据传入的商品信息列表获取各类商品优惠、商家抵价/满减/买赠优惠、平台抵价/买赠优惠
	 * 1、将商品数组重组为根据商家ID分组的数组，计算商品总金额(优惠前总价)total_amount；
	 * 2、遍历商品列表，计算每个商品的实际价格，同时计算每个商家的订单总价；
	 * 3、分别调用店铺满减/买赠/抵用模型的getCouponInfo方法获取店铺优惠和推荐优惠，并从中选出一个最合适的推荐优惠(支付最少的价格就可以享受到的)；
	 * 4、分别调用平台买赠/抵用模型的getCouponInfo方法获取平台优惠和推荐优惠，并从中选出一个最合适的推荐优惠(支付最少的价格就可以享受到的)；
	 * 5、计算最终的订单商品总金额total_amount；
	 * 6、返回total_amount和所有已享受优惠、推荐优惠（最多两个）；
     */
	function getCoupon($item_list, $user_id = 0, $recal_price = true)
	{
		#dump($item_list);
		//初始化
		$return_arr = array();
		$total_amount = 0;
		$discount_amount = 0;
		$system_discount_amount = 0;
		$coupon = array();
		$rec_coupon = array();

		//计算商品总金额(优惠前总价)total_amount
		$new_item_list = array();
		//三级分类ID列表
		$genre_id_arr = array();
		$item_obj = new ItemModel();
		foreach ($item_list AS $k => $v)
		{
#echo 'mall_price = ' . $v['mall_price'] . ', number = ' . $v['number'] . "<br>";
			$real_price = $v['real_price'];
			if ($recal_price)
			{
				$real_price = $item_obj->calculateItemRealPrice($v['item_id'], $v['item_sku_price_id'], $v['number']);
				$item_list[$k]['real_price'] = $real_price;
			}
			$total_amount += $real_price * $v['number'];
			if (!in_array($v['genre_id'], $genre_id_arr))
			{
				$genre_id_arr[] = $v['genre_id'];
			}
		}
		//三级分类ID字符串
		$genre_ids = implode(',', $genre_id_arr);
		$genre_ids .= $genre_ids ? ', 0' : '0';

		/*** 满减优惠begin ***/
		//获取满足条件的满减活动
		$discount_minus_obj = new DiscountMinusModel();
		$where = $discount_minus_obj->getWhere(0, $total_amount, $genre_ids);
		$discount_minus_list = $discount_minus_obj->getDiscountMinusList('', $where['where'], 'num DESC');
		#echo $discount_minus_obj->getLastSql();
		#dump($discount_minus_list);
		#echo $total_amount . "<br>";
		foreach ($discount_minus_list AS $k => $v)
		{
			$is_valid = $discount_minus_obj->isPromoValid($v, $item_list, $total_amount);
			if ($is_valid)
			{
				$coupon[] = array(
					'type'			=> 'discount_minus',
					'id'			=> $v['discount_minus_id'],
					'num'			=> $v['num'],
					'amount_limit'	=> $v['amount_limit'],
					'desc'			=> '满减活动【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '减' . $v['num'],
				);
				$total_amount -= $v['num'];
				$discount_amount += $v['num'];
			}
			else
			{
				$rec_coupon[] = array(
					'type'			=> 'discount_minus',
					'id'			=> $v['discount_minus_id'],
					'num'			=> $v['num'],
					'amount_limit'	=> $v['amount_limit'],
					'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
					'desc'			=> '再消费' . ($v['amount_limit'] - $total_amount) . '元，可享受满减活动【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '减' . $v['num'],
				);
			}
		}
		#echo $total_amount . "<br>";
		#dump($coupon);

		//无分类要求活动
		$rec_where = $where['rec_where'] . ' AND amount_limit > ' . $total_amount;
		$v = $discount_minus_obj->getDiscountMinusInfo($rec_where, '', 'amount_limit ASC');
		if ($v)
		{
			$rec_coupon[] = array(
				'type'			=> 'discount_minus',
				'id'			=> $v['discount_minus_id'],
				'num'			=> $v['num'],
				'amount_limit'	=> $v['amount_limit'],
				'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
				'desc'			=> '再消费' . ($v['amount_limit'] - $total_amount) . '元，可享受满减活动【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '减' . $v['num'],
			);
		}
		#dump($rec_coupon);
		#dump($discount_minus_list);
		/*** 满减优惠end ***/

		/*** 优惠券活动begin ***/
		//获取满足条件的优惠券
		$user_vouchers_obj = new UserVouchersModel();
		$where = $user_vouchers_obj->getWhere(0, $total_amount, $genre_ids);
		$user_vouchers_list = $user_vouchers_obj->getUserVouchersList('user_vouchers_id, num, title, amount_limit, genre_id, start_time, end_time', $where['where'], 'num DESC');
		#echo $user_vouchers_obj->getLastSql();
		#dump($user_vouchers_list);
		#echo $total_amount . "<br>";
		$tmp_max_num = 0;
		$best_vouchers_id = 0;
		$tmp_best_coupon = array();
		foreach ($user_vouchers_list AS $k => $v)
		{
			$is_valid = $user_vouchers_obj->isPromoValid($v, $item_list, $total_amount);
			if ($is_valid)
			{
				if ($tmp_max_num < $v['num'])
				{
					//多选一，找出最优者
					$tmp_best_coupon = array(
						'type'			=> 'user_vouchers',
						'id'			=> $v['user_vouchers_id'],
						'num'			=> $v['num'],
						'amount_limit'	=> $v['amount_limit'],
						'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
						'desc'			=> '优惠券【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '优惠' . $v['num'],
					);
					$tmp_max_num = $v['num'];
					$best_vouchers_id = $v['user_vouchers_id'];
					#$total_amount -= $v['num'];
				}
			}
			else
			{
				$rec_coupon[] = array(
					'type'			=> 'user_vouchers',
					'id'			=> $v['user_vouchers_id'],
					'num'			=> $v['num'],
					'amount_limit'	=> $v['amount_limit'],
					'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
					'desc'			=> '再消费' . ($v['amount_limit'] - $total_amount) . '元，可享受优惠券【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '优惠' . $v['num'],
				);
				unset($user_vouchers_list[$k]);
			}
		}

		//选中最优者，扣款
		$total_amount -= $tmp_max_num;
		$discount_amount += $tmp_max_num;
		if (!empty($tmp_best_coupon))
		{
			$coupon[] = $tmp_best_coupon;
		}
		#echo $total_amount . "<br>";
		#dump($coupon);

		//无分类要求活动
		$rec_where = $where['rec_where'] . ' AND amount_limit > ' . $total_amount;
		$v = $user_vouchers_obj->getUserVouchersInfo($rec_where, '', 'amount_limit ASC');
		if ($v)
		{
			$rec_coupon[] = array(
				'type'			=> 'user_vouchers',
				'id'			=> $v['user_vouchers_id'],
				'num'			=> $v['num'],
				'amount_limit'	=> $v['amount_limit'],
				'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
				'desc'			=> '再消费' . ($v['amount_limit'] - $total_amount) . '元，可享受优惠券【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '优惠' . $v['num'],
			);
		}
		#dump($rec_coupon);
		#dump($user_vouchers_list);
		/*** 优惠券活动end ***/

		/*** 买赠活动begin ***/
		//获取满足条件的买赠活动
		$buy_give_obj = new BuyGiveModel();
		$where = $buy_give_obj->getWhere(0, $total_amount, $genre_ids);
		$buy_give_list = $buy_give_obj->getBuyGiveList('', $where['where']);
		#echo $buy_give_obj->getLastSql();
		#dump($buy_give_list);
		#die;
		foreach ($buy_give_list AS $k => $v)
		{
			$is_valid = $buy_give_obj->isPromoValid($v, $item_list, $total_amount);
			if ($is_valid)
			{
				$coupon[] = array(
					'type'			=> 'buy_give',
					'id'			=> $v['buy_give_id'],
					'amount_limit'	=> $v['amount_limit'],
					'desc'			=> '买赠活动【' . $v['title'] . '】',
				);
			}
			else
			{
				$rec_coupon[] = array(
					'type'			=> 'buy_give',
					'id'			=> $v['buy_give_id'],
					'amount_limit'	=> $v['amount_limit'],
					'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
					'desc'			=> '再消费' . ($v['amount_limit'] - $total_amount) . '元，可享受买赠活动【' . $v['title'] . '】',
				);
			}
		}
		#dump($coupon);

		//无分类要求活动
		$rec_where = $where['rec_where'] . ' AND amount_limit > ' . $total_amount;
		$v = $buy_give_obj->getBuyGiveInfo($rec_where, '', 'amount_limit ASC');
		if ($v)
		{
			$rec_coupon[] = array(
				'type'			=> 'buy_give',
				'id'			=> $v['buy_give_id'],
				'amount_limit'	=> $v['amount_limit'],
				'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
				'desc'			=> '再消费' . ($v['amount_limit'] - $total_amount) . '元，可享受买赠活动【' . $v['title'] . '】',
			);
		}
		#dump($rec_coupon);
		/*** 买赠活动end ***/

		/* 1、$total_amount & $discount_amount & $coupon 优惠后总金额、优惠总金额、优惠内容；
		 * 2、$total_amount 优惠后商品总金额；
		 * 3、$discount_amount 优惠总金额；
		 * 4、$coupon_info 订单所有优惠内容；
		 * 5、$user_vouchers_list 优惠券列表（只能选其一进行抵扣）
		 * */
		$return_arr = array(
			'total_amount'				=> $total_amount,
			'discount_amount'			=> $discount_amount,
			'best_vouchers_id'			=> $best_vouchers_id,
			'best_vouchers_num'			=> $tmp_max_num,
			'coupon'					=> !empty($coupon) ? $coupon : null,
			'rec_coupon'				=> !empty($rec_coupon) ? $rec_coupon : null,
			'user_vouchers_list'		=> $user_vouchers_list,
			'item_list'					=> $item_list,
		);

		#echo "<pre>";
		#print_r($return_arr);
		#die;

		return $return_arr;
	}

    /**
     * 获取促销描述
     * @author 姜伟
     * @param array $promo_info
     * @return array $promo_info
     * @todo 获取促销描述
     */
	public static function getPromoDesc($promo_info)
	{
		$coupon_desc = '';
		if ($order_info['total_amount'] == $order_info['discount_amount'] && empty($promo_info['rec_coupon']))
		{
			$coupon_desc = '当前无优惠~';
		}
		else
		{
			$coupon_desc = '优惠前总价：' . ($promo_info['total_amount'] + $promo_info['discount_amount']) . "<br>";
			$coupon_desc .= '优惠后总价：' . $promo_info['total_amount'] . "<br>";
		}
		$i = 0;
		foreach ($promo_info['coupon'] AS $k => $v)
		{
			$coupon_desc .= ($i + 1) . '. ' . $v['desc'] . "<br>";
			$i++;
		}
		foreach ($promo_info['rec_coupon'] AS $k => $v)
		{
			$coupon_desc .= ($i + 1) . '. ' . $v['desc'] . "<br>";
			$i++;
		}

		$return_arr = array(
			'total_amount'		=> $order_info['total_amount'],
			'discount_amount'	=> $order_info['discount_amount'],
			'coupon_desc'		=> $coupon_desc,
		);
		return $return_arr;
	}

    /**
     * 获取商品是否满足促销(满减/买赠等)的where查询条件
     * @author 姜伟
     * @param int $genre_id
     * @return boolean
     * @todo 获取商品是否满足促销(满减/买赠等)的where查询条件
     */
	public static function getItemWhere($genre_id = 0)
	{
		$merchant_id = cur_merchant_id();
		return '(merchant_id = ' . $merchant_id . ' OR merchant_id = 0) AND start_time <= ' . time() . ' AND end_time > ' . time() . ' AND (genre_id = ' . $genre_id . ' OR genre_id = 0)';
	}

    /**
     * 商品三级分类是否满足买赠条件
     * @author 姜伟
     * @param int $genre_id
     * @return boolean
     * @todo 商品三级分类是否满足买赠条件
     */
	public static function isItemBugGive($genre_id = 0)
	{
		$where = self::getItemWhere($genre_id);
		$buy_give_obj = new BuyGiveModel();
		$num = $buy_give_obj->getBuyGiveNum($where);
		return $num ? 1 : 0;
	}

    /**
     * 商品三级分类是否满足满减条件
     * @author 姜伟
     * @param int $genre_id
     * @return boolean
     * @todo 商品三级分类是否满足满减条件
     */
	public static function isItemDiscountMinus($genre_id = 0)
	{
		$where = self::getItemWhere($genre_id);
		$discount_minus_obj = new DiscountMinusModel();
		$num = $discount_minus_obj->getDiscountMinusNum($where);
		return $num ? 1 : 0;
	}

    /**
     * 商品列表是否满足某促销活动
     * @author 姜伟
     * @param array $promo_info
     * @param array $item_list
     * @param float $total_amount
     * @return boolean
     * @todo 商品列表是否满足某促销活动
     */
	public function isPromoValid($promo_info, $item_list, $total_amount)
	{
		//若三级分类为0，直接判断所有总价
		$genre_id = $promo_info['genre_id'];
		$amount_limit = $promo_info['amount_limit'];
		if (!$genre_id)
		{
			if ($total_amount >= $amount_limit)
			{
				return 1;
			}
		}

		//若三级分类不为0，计算该三级分类下的商品总价
		$total_amount = 0.00;
		foreach ($item_list AS $k => $v)
		{
			if ($v['genre_id'] == $genre_id)
			{
				$total_amount += $v['real_price'] * $v['number'];
			}
		}

		return $total_amount >= $amount_limit ? 1 : 0;
	}

    /**
     * 更换优惠券
     * @author 姜伟
     * @param int $user_vouchers_id
     * @param array $order_info
     * @return boolean
     * @todo 更换优惠券
     */
	public static function changeVouchers($user_vouchers_id, $order_info)
	{
		foreach ($order_info['coupon'] AS $k => $v)
		{
			if ($v['type'] == 'user_vouchers')
			{
				unset($order_info['coupon'][$k]);
				$order_info['total_amount'] += $v['num'];
				$order_info['discount_amount'] -= $v['num'];
			}
		}

		foreach ($order_info['user_vouchers_list'] AS $k => $v)
		{
			if ($user_vouchers_id == $v['user_vouchers_id'])
			{
				$vouchers_coupon = array(
					'type'			=> 'user_vouchers',
					'id'			=> $v['user_vouchers_id'],
					'num'			=> $v['num'],
					'amount_limit'	=> $v['amount_limit'],
					'genre_id'		=> $v['genre_id'],	//方便用户直接点击跳转到三级分类商品列表页去消费
					'desc'			=> '优惠券【' . $v['title'] . '】' . '：满' . $v['amount_limit'] . '优惠' . $v['num'],
				);
				$order_info['total_amount'] -= $v['num'];
				$order_info['discount_amount'] += $v['num'];
				$order_info['coupon'][] = $vouchers_coupon;
			}
		}

		return $order_info;
	}
}

<?php

/**
*  促销基类 模型 ----> 这是一个抽象类，具体实现在请在子类(订单促销和商品促销 )中进行
*  @ Project : Untitled
*  @ File Name : PromotionBaseModel.class.php
*  @ Date : 2014/2/27
*  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
*/


abstract class PromotionBaseModel extends Model
{

	/**
     * @desc 添加促销方案
     */
	abstract function addPromotion($data);

	/**
     * @desc 获取促销方案信息
     *  
     */
	abstract function getPromotion($data);

	/**
     * @desc 修改促销方案
     * 
     */
	abstract function editPromotion($promoid,$data);

	/**
     * @desc 删除促销方案
     * 
     */
	abstract function deletePromotion($promoid);


	/**
     * @desc 设置当前促销方案无效
     * 
     */
	abstract function disablePromotion($promoid, $isuse);

	/**
     * 获取礼品信息列表
     * @author 姜伟
     * @param array $coupon_info 促销信息数组，内包含礼品信息数组
     * @return void
     * @todo 遍历促销信息数组，若礼品存在，将礼品信息返回
     */
	public static function getGifts($coupon_info)
	{
		$gifts = array();

		#echo "<pre>";
		#print_r($coupon_info);
		#echo "</pre>";
		foreach ($coupon_info AS $k => $v)
		{
			if (($k == 'gift_info' || $k == 'gift_list') && !empty($v))
			{
				foreach ($v AS $val)
				{
					$promotion_id = isset($val['promotion_order_gift_id']) ? $val['promotion_order_gift_id'] : $val['promotion_item_gift_id'];
					$gift_info = array(
					'gift_id'		=> $val['gift_id'],
					'gift_name'		=> $val['gift_name'],
					'gift_total'	=> $val['gift_total'],
					'market_price'	=> $val['market_price'],
					'small_pic'		=> $val['small_pic'],
					'gift_sn'		=> $val['gift_sn'],
					'promotion_id'	=> $promotion_id
					);
					$gifts[] = $gift_info;
				}
			}
		}
		#echo "<pre>gifts: ";
		#print_r($gifts);
		#echo "</pre>";

		return $gifts;
	}

	public static function getAllCoupon($total_amount, $item_list, $agent_rank_id)
	{
		$ItemPriceRank = D('ItemPriceRank');

		#$BuyItemGiveGiftModel = new BuyItemGiveGiftModel();
		$ItemDiscountModel = new PromotionItemDiscountModel();
		#$OrderAmountMoreThanGiveGiftModel = new OrderAmountMoreThanGiveGiftModel();

		//取会员等级的资料
		$agent_rank_obj = new UserRankModel();
		$rank_info = $agent_rank_obj->getAgentRankInfoById($agent_rank_id);

		//单品已获赠的礼品数组
		$item_gift_ary = array();
		//单品多买后可获赠的礼品数组
		$item_gift_recommed_ary = array();

		//单品继续多买可享受的折扣数组
		$item_discount_recommed_ary = array();

		//订单可享受的折扣数组
		$order_discount_ary = array();
		//订单继续增加金额后可享受的折扣数组
		$order_discount_recommed_ary = array();

		//订单已获赠的礼品数组
		$order_gift_ary = array();
		//订单增加金额后可获赠的礼品数组
		$order_gift_recommed_ary = array();

		//订单满额包邮
		$order_shipment_ary = array();

		//订单新人下单优惠
		$order_newuser_ary = array();

		foreach ($item_list AS $temp => $value)
		{
			if (!isset($value['item_id']) || $value['item_id'] == 0) continue;	//容个错

			$discount_ary = array();
			$discount_sum_total = 0;

			/******************************以下为单品优惠*******************************/

			//根据SKU循环出每个SKU对应的会员等级价格

			foreach ($value['sku'] AS $sku_temp => $sku_value)
			{
				//取出会员折扣带来的单品优惠
				if ($agent_rank_id)
				{
					$discount_total = 0;
					//获得会员等级带来的优惠，sku和会员等级价绑定的价格策略还没加进来
					$rank_price = $ItemPriceRank->getItemRankPrice($agent_rank_id, $value['item_id'], $sku_temp);
					if (!$rank_price)
					{
						//获取代理商等级折扣
						$agent_rank_obj = new UserRankModel($agent_rank_id);
						$discount_rate = $agent_rank_obj->getDiscountById($agent_rank_id);
						$rank_price = $value['total_price'] * ($discount_rate / 100);
					}
					
					//实际减了多少钱
					$discount_total = round($value['total_price'] - $rank_price, 2);

					$discount_ary[$sku_temp]['rank_discount']['name'] = $rank_info['rank_name'];	//优惠的名称
					$discount_ary[$sku_temp]['rank_discount']['type'] = 1;	//优惠的类型，1是折扣，2是减钱
					$discount_ary[$sku_temp]['rank_discount']['total'] = $rank_info['discount'];	//减钱的标准
					$discount_ary[$sku_temp]['rank_discount']['amount'] = sprintf("%.2f", $discount_total);	//减少了多少钱
				}
				
				//计算单品各个SKU加在一起的商品数量
				
				#$value['number']+=$value['number'];
				
				$discount_sum_total_ary[$sku_temp] = $discount_total;
				
			}
			
			//取单品混批带来的优惠
			$itemWholesaleRule = M('item_wholesale_rule');
			$mixed_price = $itemWholesaleRule->where('item_id=' . $value['item_id'] . ' AND start_num<=' . $value['number'] . ' AND end_num>=' . $value['number'])->getField('price');
			$mixed_price = $mixed_price ? round($mixed_price, 2) : 0.00;

			$discount_ary['mixed_discount']['name'] = '混批优惠';	//优惠的名称
			$discount_ary['mixed_discount']['type'] = 2;	//优惠的类型，1是折扣，2是减钱 todo
			$discount_ary['mixed_discount']['total'] = $mixed_price;	//减钱的标准 todo还没模型
			$discount_ary['mixed_discount']['amount'] = sprintf("%.2f", $mixed_price);	//减少了多少钱 todo还没模型

			$discount_sum_total+=$mixed_price;

			//取单品打折/减价带来的优惠
			$promo_ary = $ItemDiscountModel->getCouponInfoByItemId($value['item_id'], $value['number'], $value['real_price'], time(), $agent_rank_id);
			if (!empty($promo_ary['promoinfo']))
			{
				$discount_ary['promo_discount']['name'] = $promo_ary['promoinfo']['title'];	//优惠的名称
				$discount_ary['promo_discount']['type'] = $promo_ary['promoinfo']['discount_type'];	//优惠的类型，1是折扣，2是减钱
				$discount_ary['promo_discount']['total'] = $promo_ary['promoinfo']['discount_total'];	//减钱的标准

				//计算实际优惠了多少钱
				if ($promo_ary['promoinfo']['discount_type'] == 1)	//折扣
				{
					$promo_price = ($value['total_price'] - $discount_total - $mixed_price) * (1-$promo_ary['promoinfo']['discount_total']);
				}
				else //直接减钱
				{
					$promo_price = $promo_ary['promoinfo']['discount_total'];
				}
				
				$promo_price = round($promo_price, 2);
				$discount_ary['promo_discount']['amount'] = sprintf("%.2f", $promo_price);	//减少了多少钱

				$discount_sum_total+=$promo_price;
			}

			//取单品继续多买可享受的折扣
			if (!empty($promo_ary['recommend']))
			{
				$item_discount_recommed_ary[$value['item_id']] = $promo_ary['recommend'];
			}

			/******************************以下为单品赠品*******************************/
			//取单品可获赠的礼品列表
			/*$gift_list = $BuyItemGiveGiftModel->getCouponInfoByItemId($value['item_id'], $value['number'], time());
			$gift_list['item_name'] = $value['item_name'];//输出一个商品名，用以追溯赠品来源
			$item_gift_ary[$value['item_id']] = $gift_list;
			//取单品继续多买可获赠的礼品列表
			$gift_recommend_list = $BuyItemGiveGiftModel->getPromoInfoForItemGift($value['item_id'], $value['number']);
			foreach ($gift_recommend_list AS $gift_recommend_list_t => $gift_recommend_list_v)
			{
				$gift_recommend_list[$gift_recommend_list_t]['item_name'] = $value['item_name'];
			}

			$item_gift_recommed_ary[$value['item_id']] = $gift_recommend_list;*/

			$item_list[$temp]['discount_ary'] = $discount_ary;
			
			//根据SKU获得的会员等级价格，组成一个数组，到最后加上单品的其它优惠得到最终优惠数据
			foreach ($discount_sum_total_ary AS $discount_sum_total_ary_temp => $discount_sum_total_ary_value)
			{
				$discount_sum_total_ary[$discount_sum_total_ary_temp] = $discount_sum_total_ary_value + $discount_sum_total;
			}
			$item_list[$temp]['discount_sum_total_ary'] = $discount_sum_total_ary;
		}

		/*********************************以下为订单层面获得的优惠*****************************/
		//获得当前订单享受的折扣/减钱
		$OrderPromotionModel = new OrderPromotionModel();
		$promo_ary = $OrderPromotionModel->getCouponInfoByShoppingTotalPrice($total_amount, $agent_rank_id);

		if (isset($promo_ary['promotion']) && isset($promo_ary['promotion']['title']))
		{
			$order_discount_ary['name'] = $promo_ary['promotion']['title'];	//优惠的名称
			$order_discount_ary['type'] = $promo_ary['promotion']['discount_type'];	//优惠的类型，1是折扣，2是减钱
			$order_discount_ary['total'] = $promo_ary['promotion']['discount_total'];	//减钱的标准
			$order_discount_ary['amount'] = round(sprintf("%.2f", $total_amount-$promo_ary['new_price']), 2);	//能减多少钱
		}

		//获得当前订单继续增加金额可享受的折扣/减钱
		$promo_ary = $OrderPromotionModel->getOrderPromotionRecommend($total_amount, $agent_rank_id);
		if (isset($promo_ary) && isset($promo_ary[0]['title']))
		{
			$order_discount_recommed_ary['name'] = $promo_ary[0]['title'];	//优惠的名称
			$order_discount_recommed_ary['type'] = $promo_ary[0]['discount_type'];	//优惠的类型，1是折扣，2是减钱
			$order_discount_recommed_ary['total'] = $promo_ary[0]['discount_total'];	//减钱的标准
			$order_discount_recommed_ary['amount'] = 0;	//能减多少钱，因为是还没达到，返回0就可以
		}

		//获得新人下单优惠
		global $config_info;
		if ($config_info['NEW_USER_DISCOUNT_STATUS'] && $config_info['NEW_USER_DISCOUNT_TOTAL'])
		{
			$user_id = intval(session('user_id'));
			if ($user_id)
			{
				$order_obj = new OrderModel();
				$payed_order_num = $order_obj->getOrderNumByQueryString('order_status > 0 AND user_id = ' . $user_id);
				//不存在成交订单
				if (!$payed_order_num)
				{
					$order_newuser_ary['name'] = '新人首单享' . $config_info['NEW_USER_DISCOUNT_TOTAL'].'折';	//优惠的名称
					$order_newuser_ary['type'] = 1;	//优惠的类型，1是折扣，2是减钱
					$order_newuser_ary['total'] = $config_info['NEW_USER_DISCOUNT_TOTAL']/10;	//减钱的标准
					$order_newuser_ary['amount'] = round(($total_amount - $order_discount_ary['amount']) * (100-$config_info['NEW_USER_DISCOUNT_TOTAL'])/100, 2);	//能减多少钱
				}
			}
		}

		/*********************************以下为订单层面获得的包邮*****************************/
		//获得当前订单的包邮状态
		if ($config_info['FREE_SHIPPING_STATUS'] && $total_amount >= $config_info['FREE_SHIPPING_TOTAL'])
		{
			$order_shipment_ary['name'] = '满' . $config_info['FREE_SHIPPING_TOTAL'] . '包邮';	//优惠的名称
			$order_shipment_ary['type'] = 0;	//优惠的类型，包邮就写0
			$order_shipment_ary['total'] = 0;	//减钱的标准，包邮还是写0
			$order_shipment_ary['amount'] = 0;	//能减多少钱，依然是0
		}

		/*********************************以下为订单层面获得的礼品*****************************/
		//获得当前订单的可获赠的礼品列表
		/*$OrderAmountMoreThanGiveGiftModel = new OrderAmountMoreThanGiveGiftModel();
		$gift_list = $OrderAmountMoreThanGiveGiftModel->getCouponInfoByShoppingTotalPrice($total_amount, $agent_rank_id);
		if (isset($gift_list['rule']) && isset($gift_list['rule'][0]['title']))
		{
			$order_gift_ary['name'] = $gift_list['rule'][0]['title'];	//优惠的名称
			$order_gift_ary['total'] = $gift_list['rule'][0]['order_total'];	//赠送的金额标准
			$order_gift_ary['gift_list'] = isset($gift_list['gift_list'][0]) ? $gift_list['gift_list'][0] : array();
		}

		//获得当前订单继续增加金额可获赠的礼品列表
		$gift_list = $OrderAmountMoreThanGiveGiftModel->getPromotionRecommondForOrderGift($total_amount, $agent_rank_id);
		if (isset($gift_list[0]['rule']) && isset($gift_list[0]['rule']['title']))
		{
			$order_gift_recommed_ary['name'] = $gift_list[0]['rule']['title'];	//优惠的名称
			$order_gift_recommed_ary['total'] = $gift_list[0]['rule']['order_total'];	//赠送的金额标准
			$order_gift_recommed_ary['gift_list'] = isset($gift_list[0]['gift_list'][0]) ? $gift_list[0]['gift_list'][0] : array();
		}*/

		$coupon_ary = array(
		'item_list' 					=> $item_list,
		'item_gift_ary' 				=> $item_gift_ary,
		'item_gift_recommed_ary' 		=> $item_gift_recommed_ary,
		'item_discount_recommed_ary' 	=> $item_discount_recommed_ary,
		'order_discount_ary' 			=> $order_discount_ary,
		'order_discount_recommed_ary' 	=> $order_discount_recommed_ary,
		'order_gift_ary' 				=> $order_gift_ary,
		'order_gift_recommed_ary' 		=> $order_gift_recommed_ary,
		'order_shipment_ary' 			=> $order_shipment_ary,
		'order_newuser_ary' 			=> $order_newuser_ary,
		);

		#echo "<pre>";
		#print_r($coupon_ary);
		return $coupon_ary;
	}


	//陆备份
	public static function getAllCoupon__($total_amount, $item_list, $agent_rank_id)
	{
		$item_gifts = array();
		$item_gifts_tmp = array();
		$order_gifts = array();
		$item_discount_info = array();
		$order_discount_info = array();
		$item_discount_recommend_info = array();
		$item_give_gift_recommend_info = array();
		#$BuyItemGiveGiftModel = new BuyItemGiveGiftModel();
		$ItemDiscountModel = new PromotionItemDiscountModel();
		$i = 0;
		//代理商等级优惠
		$agent_rank_discount = array();

		foreach ($item_list AS $k => $v)
		{
			//买单品送礼品活动中已享受的促销信息
			$coupon_info = $BuyItemGiveGiftModel->getCouponInfoByItemId($v['item_id'], $v['number'], time());
			if (!empty($coupon_info['gift_info']))
			{
				foreach ($coupon_info['gift_info'] AS $key => $val)
				{
					#$item_gifts[$val['gift_id']] = $val;	//调试时开启
					#if (isset($item_gifts[$val['gift_id']]))
					#{
					#$item_gifts[$val['gift_id']]['title'] .= '；购买 ' . $val['item_total'] . ' 件 “' . $v['item_name'] . '” 送 ' . $val['gift_total'] . ' 件 “' . $val['gift_name'] . ' ”';
					#}
					#else
					#{
					$item_gifts[$i]['title'] = '购买 ' . $val['item_total'] . ' 件 “' . $v['item_name'] . '” 送 ' . $val['gift_total'] . ' 件 “' . $val['gift_name'] . ' ”';
					$item_gifts[$i]['small_pic'] = $val['small_pic'];
					$i ++;
					#}
					#echo "<pre>";
					#print_r($v);
					#echo "</pre>";
				}
			}

			//买单品送礼品活动中推荐的促销信息
			/*$coupon_info = $BuyItemGiveGiftModel->getPromoInfoForItemGift($v['item_id'], $v['number']);
			if (!empty($coupon_info))
			{
				foreach ($coupon_info AS $key => $val)
				{
					#$item_give_gift_recommend_info[] = $val;	//调试时开启
					$item_give_gift_recommend_info[]['title'] = '购买 ' . $val['item_total'] . ' 件 “' . $v['item_name'] . '” 送 ' . $val['gift_total'] . ' 件 “' . $val['gift_name'] . ' ”';
				}
			}*/
			#echo "<br>item_id = " . $v['item_id'] . ', number = ' . $v['number'] . "<br>";

			//单品降价打折活动中已享受和推荐的促销信息
			$coupon_info = $ItemDiscountModel->getCouponInfoByItemId($v['item_id'], $v['number'], $v['real_price'], time(), $agent_rank_id);
			#echo "<pre>";
			#print_r($coupon_info);
			#echo "</pre>";
			if (!empty($coupon_info['promoinfo']))
			{
				#$item_discount_info[] = $coupon_info['promoinfo'];	//调试时开启
				$item_discount_info[]['title'] = $coupon_info['promoinfo']['title'];
			}
			if (!empty($coupon_info['recommend']))
			{
				#$item_discount_recommend_info[] = $coupon_info['recommend'];
				$item_discount_recommend_info[]['title'] = $coupon_info['recommend']['title'];
			}

			//代理商等级优惠
			if ($agent_rank_id)
			{
				// 获取该代理商等级价格
				$ItemPriceRank = D('ItemPriceRank');
				$rank_price = $ItemPriceRank->getItemAgentRankPrice($item_id, $agent_rank_id);
				if ($rank_price)
				{
					$agent_rank_discount[]['title'] = '购买商品“' . $v['item_name'] . '”享受代理商等级价 ' . $rank_price . '/件';
				}
				else
				{
					//获取代理商等级折扣
					$agent_rank_obj = new UserRankModel($agent_rank_id);
					$discount_rate = $agent_rank_obj->getDiscountById($agent_rank_id);
					if ($discount_rate && $discount_rate < 100)
					{
						$agent_rank_discount[]['title'] = '购买商品“' . $v['item_name'] . '”享受代理商等级折扣：' . $discount_rate . '折';
					}
				}
			}
		}

		//订单送礼品活动已享受的优惠信息
		$OrderAmountMoreThanGiveGiftModel = new OrderAmountMoreThanGiveGiftModel();
		$coupon_info = $OrderAmountMoreThanGiveGiftModel->getCouponInfoByShoppingTotalPrice($total_amount, $agent_rank_id);
		#echo "<pre>";
		#print_r($coupon_info);
		#echo "</pre>";
		foreach ($coupon_info['gift_list'] AS $key => $val)
		{
			$order_gifts[$key]['rule'] = $coupon_info['rule'][$key]['title'];
			$order_gifts[$key]['gift_list']['title'] = $val['gift_name'];
			$order_gifts[$key]['gift_list']['small_pic'] = $val['small_pic'];
		}

		//订单送礼活动的推荐
		$coupon_info = $OrderAmountMoreThanGiveGiftModel->getPromotionRecommondForOrderGift($total_amount, $agent_rank_id);
		foreach ($coupon_info AS $k => $v)
		{
			$order_give_gift_recommend_info[$k]['rule'] = $v['rule']['title'];
			foreach ($v['gift_list'] AS $key => $val)
			{
				$order_give_gift_recommend_info[$k]['gift_list']['title'] = $val['gift_name'];
				$order_give_gift_recommend_info[$k]['gift_list']['small_pic'] = $val['small_pic'];
			}
		}

		//订单降价打折活动的推荐
		$OrderPromotionModel = new OrderPromotionModel();
		$coupon_info = $OrderPromotionModel->getOrderPromotionRecommend($total_amount, $agent_rank_id);
		foreach ($coupon_info AS $key => $val)
		{
			$order_discount_recommend_info[$key]['title'] = $val['title'];
		}

		//订单打折活动已享受的优惠信息
		$coupon_info = $OrderPromotionModel->getCouponInfoByShoppingTotalPrice($total_amount, $agent_rank_id);
		#echo "<pre>";
		#print_r($coupon_info);
		#echo "</pre>";
		if (!empty($coupon_info['promotion']))
		{
			$order_discount_info['title'] = $coupon_info['promotion']['title'];
		}

		//新人下单优惠
		$new_user_discount = array();
		global $config_info;
		if ($config_info['NEW_USER_DISCOUNT_STATUS'] && $config_info['NEW_USER_DISCOUNT_TOTAL'])
		{
			$user_id = intval(session('user_id'));
			if ($user_id)
			{
				$order_obj = new OrderModel();
				$payed_order_num = $order_obj->getOrderNumByQueryString('order_status > 0 AND user_id = ' . $user_id);
				//不存在成交订单
				if (!$payed_order_num)
				{
					$new_user_discount['title'] = '新人下单享受' . $config_info['NEW_USER_DISCOUNT_TOTAL'] . '折优惠';
				}
			}
		}

		//订单满额包邮优惠
		$order_more_than_delivery_free = array();
		if ($config_info['FREE_SHIPPING_STATUS'] && $total_amount >= $config_info['FREE_SHIPPING_TOTAL'])
		{
			$order_more_than_delivery_free['title'] = '订单满' . $config_info['FREE_SHIPPING_TOTAL'] . '元包邮';
		}

		#echo "<pre>";
		#echo "已享受单品赠品";
		#print_r($item_gifts);
		#echo "推荐单品赠品";
		#print_r($item_give_gift_recommend_info);
		#echo "享受单品折扣";
		#print_r($item_discount_info);
		#echo "推荐单品折扣";
		#print_r($item_discount_recommend_info);
		#echo "已享受订单赠品";
		#print_r($order_gifts);
		#echo "推荐订单赠品";
		#print_r($order_give_gift_recommend_info);
		#echo "享受订单折扣";
		#print_r($order_discount_info);
		#echo "推荐订单折扣";
		#print_r($order_discount_recommend_info);
		#echo "新人下单折扣";
		#print_r($new_user_discount);
		#echo "订单满额包邮";
		#print_r($order_more_than_delivery_free);
		#echo "</pre>";

		$coupon_info = array(
		'item_gifts'						=> $item_gifts,
		'item_give_gift_recommend_info'		=> $item_give_gift_recommend_info,
		'item_discount_info'				=> $item_discount_info,
		'item_discount_recommend_info'		=> $item_discount_recommend_info,
		'order_gifts'						=> $order_gifts,
		'order_give_gift_recommend_info'	=> $order_give_gift_recommend_info,
		'order_discount_info'				=> $order_discount_info,
		'order_discount_recommend_info'		=> $order_discount_recommend_info,
		'new_user_discount'					=> $new_user_discount,
		'order_more_than_delivery_free'		=> $order_more_than_delivery_free,
		'agent_rank_discount'				=> $agent_rank_discount,
		);

		return $coupon_info;
	}


	/**
	 * 计算订单折扣优惠（包括新人）
	 * @author 姜伟
	 * @param $total_amount 订单总价
	 * @param $agent_rank_id 代理商等级ID
	 * @return @total_amount 优惠后订单总价
	 */
	public function calculateOrderCoupon($total_amount, $agent_rank_id = 0)
	{
		//订单满额优惠
		$OrderPromotionModel = new OrderPromotionModel();
		$coupon_info = $OrderPromotionModel->getCouponInfoByShoppingTotalPrice($total_amount, $agent_rank_id);
		#echo "<pre>";print_r($coupon_info);die;
		$total_amount = !empty($coupon_info) ? floatval($coupon_info['new_price']) : $total_amount;

		//新人下单优惠
		global $config_info;
		if ($config_info['NEW_USER_DISCOUNT_STATUS'] && $config_info['NEW_USER_DISCOUNT_TOTAL'])
		{
			$user_id = intval(session('user_id'));
			if ($user_id)
			{
				$order_obj = new OrderModel();
				$payed_order_num = $order_obj->getOrderNumByQueryString('order_status > 0 AND user_id = ' . $user_id);
				//不存在成交订单
				if (!$payed_order_num)
				{
					$total_amount = $total_amount * floatval($config_info['NEW_USER_DISCOUNT_TOTAL']) / 100;
				}
			}
		}

		return $total_amount;
	}

	/**
	 * 计算单品折扣
	 * @author 姜伟
	 * @param array $pre_item_list 商品信息数组，二维
	 * @return array $item_list
	 * @todo 
	 */
	public static function getItemCoupon($pre_item_list, $is_virtual_stock_order = 0)
	{
		$item_list = array();

		$user_id = intval(session('user_id'));
		$agent_rank_id = isset($_SESSION['user_info']) ? $_SESSION['user_info']['agent_rank_id'] : 0;
		$pc_item_obj = new ItemModel();
		//合并不同sku的同一商品
		$i = 0;

		foreach ($pre_item_list AS $k => $v)
		{
			//计算除混批和优惠活动外的实际价格
			$real_price = 0.00;
			//调用商品模型的calculateItemRealPrice方法获取商品的代理商等级价
			$real_price = $pc_item_obj->calculateItemRealPrice($v['item_id'], $v['item_sku_price_id'], $v['number'], $agent_rank_id, false);
			$pre_item_list[$k]['real_price'] = $real_price;
			$v['real_price'] = $real_price;

			$stock = 0;
			//判断该商品在虚拟仓中是否有库存
			if (!$is_virtual_stock_order)
			{
				$virtual_stock_obj = new VirtualStockModel();
				$stock = $virtual_stock_obj->getItemStock($user_id, $v['item_id'], $v['item_sku_price_id']);
			}
			#echo 'item_id = ' . $v['item_id'] . ', item_sku_price_id = ' . $v['item_sku_price_id'] . ', stock = ' . $stock . ', number = ' . $v['number'] . "<br>";
			$from_virtual_stock = $stock >= $v['number'] ? 1 : 0;
			#var_dump($from_virtual_stock);
			#echo "<br>";

			//同一商品是否已存在标记
			$tag = false;

			if (!$from_virtual_stock)
			{
				foreach ($item_list AS $key => $value)
				{
					if ($value['item_id'] == $v['item_id'] && $value['from_virtual_stock'] == 0)
					{
						$tag = true;
						break;
					}
				}
			}

			if ($tag)
			{
				$item_list[$key]['number'] += intval($v['number']);
				$item_list[$key]['total_price'] += floatval($real_price) * intval($v['number']);
			}
			else
			{
				foreach ($v AS $key_name => $field_value)
				{
					$item_list[$i][$key_name] = $field_value;
				}
				$item_list[$i]['from_virtual_stock'] = $from_virtual_stock;
				$item_list[$i]['total_price'] = floatval($real_price) * intval($v['number']);
			}

			$i ++;
		}

		#echo "<pre>";
		#print_r($item_list);
		#echo "</pre>";

		//遍历数组中的每一个商品，计算混批优惠和优惠活动后的实际价格，保存到数组中
		foreach ($item_list AS $key => $value)
		{
			$total_price = $value['total_price'];
			$pre_total_price = $total_price;
			$number = $value['number'];

			//计算混批优惠后的单品总价
			$itemWholesaleRule = M('item_wholesale_rule');
			$discount_price = $itemWholesaleRule->where('item_id=' . $value['item_id'] . ' AND start_num<=' . $number . ' AND end_num>=' . $number)->getField('price');
			$discount_price = $discount_price ? floatval($discount_price) : 0.00;
			$total_price -= $discount_price * $number;

			//每件商品的平均实际价格
			$real_price = floatval($total_price) / intval($value['number']);
			$wholesale_price = $pc_item_obj->getWholesalePrice($value['item_id']);

			//调用商品折扣促销模型的getCouponInfoByItemId方法获取商品优惠信息，并从中取出商品优惠后价格
			$item_discount_obj = new PromotionItemDiscountModel();
			$coupon_info = $item_discount_obj->getCouponInfoByItemId($value['item_id'], $value['number'], $real_price, time(), $agent_rank_id);
			#echo "<pre>";
			#print_r($coupon_info);
			#echo "</pre>";
			$total_price = $coupon_info['totalprice'];

			//每件商品的混批优惠价格+优惠活动优惠价
			$item_list[$key]['discount_price'] = (floatval($pre_total_price) - floatval($total_price)) / intval($value['number']);

			$item_list[$key]['real_price'] = $real_price;
			$item_list[$key]['total_price'] = $total_price;
			$item_list[$key]['wholesale_price'] = $wholesale_price;
		}

		//将商品价格及是否从虚拟仓信息保存到欲返回的数组
		foreach ($pre_item_list AS $k => $v)
		{
			#echo '<hr>item_id = ' . $v['item_id'] . ', item_sku_price_id = ' . $v['item_sku_price_id'] . ', stock = ' . $stock . ', number = ' . $v['number'] . "<br>";
			foreach ($item_list AS $key => $value)
			{
				#echo 'item_id = ' . $value['item_id'] . ', item_sku_price_id = ' . $value['item_sku_price_id'] . ', stock = ' . $stock . ', number = ' . $value['number'] . ', from_virtual_stock = ' . $value['from_virtual_stock'] . ', real_price = ' . $value['real_price'] . "<br>";
				//单种商品（包括sku）的实际优惠后（所有优惠）单价
				$real_price = floatval($v['real_price']) - floatval($value['discount_price']);
				//单种商品（包括sku）的实际优惠后（所有优惠）总价
				$total_price = $real_price * $v['number'];
				if ($value['from_virtual_stock'])
				{
					if ($v['item_id'] == $value['item_id'] && $v['item_sku_price_id'] == $value['item_sku_price_id'])
					{
						#echo 'aaa';
						$pre_item_list[$k]['from_virtual_stock'] = 1;
						#$pre_item_list[$k]['real_price'] = 0;
						#$pre_item_list[$k]['total_price'] = 0;

						$pre_item_list[$k]['real_price'] = sprintf('%.2f', $real_price);
						$pre_item_list[$k]['wholesale_price'] = $value['wholesale_price'];
						$pre_item_list[$k]['total_price'] = sprintf('%.2f', $total_price);
						break;
					}
				}
				elseif ($v['item_id'] == $value['item_id'])
				{
					#echo 'bbb';
					$pre_item_list[$k]['from_virtual_stock'] = 0;
					$pre_item_list[$k]['wholesale_price'] = $value['wholesale_price'];

					$pre_item_list[$k]['real_price'] = sprintf('%.2f', $real_price);
					$pre_item_list[$k]['total_price'] = sprintf('%.2f', $total_price);
					break;
				}
			}
			#echo "<pre>";
			#print_r($pre_item_list);
			#echo "</pre>";
		}
		#echo "<pre>";
		#print_r($pre_item_list);
		#echo "</pre>";

		return $pre_item_list;
	}
}
?>

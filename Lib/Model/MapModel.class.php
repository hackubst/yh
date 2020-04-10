<?php
/**
 * 地图模型类
 */

class MapModel extends BaseModel
{
    /**
     * 根据商品列表（根据商家分组，带是否即时）获取订单方案信息
     * @author 姜伟
     * @param $item_list
     * @param $user_address_id
     * @return array 3种方案
     * @todo 根据商品列表（根据商家分组，带是否即时）获取订单方案信息
     */
    public static function getOrderSolutionList($item_list, $user_address_id)
	{
		//根据item_list获取商家信息列表（是否即时，坐标）
		$merchant_list = self::getOrderMerchantList($item_list);

		//根据商品信息获取用户及各商家之间的距离信息数组
		$distance_arr = self::getDistanceList($merchant_list, $user_address_id);

		//根据商家列表获取初始的订单列表（根据非即时订单拆分）
		$initial_info = self::getInitialOrderList($merchant_list, $distance_arr);
		$order_arr = $initial_info['order_arr'];
		$left_merchant_list = $initial_info['left_merchant_list'];
		
		$solution_list = array();
		//速度优先
		$speed_solution = self::getSpeedSolution($item_list, $merchant_list, $distance_arr);

		//系统推荐
    	$system_recommend_solution = self::getSystemRecommendSolution($order_arr, $left_merchant_list, $distance_arr);

		//镖金最少
    	$least_freight_solution = self::getLeastFreightSolution($item_list, $merchant_list, $distance_arr);
		//将商品信息，镖金信息，商家店名加入订单方案中
		#echo "速度优先&最少镖金<pre>";
		$system_recommend_solution = self::getOrderList($system_recommend_solution, $merchant_list, $item_list);
		$least_freight_solution = self::getOrderList($least_freight_solution, $merchant_list, $item_list);
		#print_r($speed_solution);
		#print_r($system_recommend_solution);
		#print_r($least_freight_solution);
		#die;

		$return_arr = array(
			'system_recommend_solution'	=> $system_recommend_solution,
			'least_freight_solution'	=> $least_freight_solution,
			'speed_solution'			=> $speed_solution,
		);

		$total_amount_arr = array();
		foreach ($return_arr AS $k => $v)
		{
			$total_amount = 0.00;
			$item_total_amount = 0.00;
			foreach ($v AS $key => $val)
			{
				$total_amount += $val['total_amount'] + $val['freight'];
				$item_total_amount += $val['total_amount'];
			}
			$total_amount_arr[$k]['total_amount'] = $total_amount;
		}
		$total_amount_arr['item_total_amount'] = $item_total_amount;
		$return_arr['total_amount_arr'] = $total_amount_arr;
		$return_arr['total_amount_arr'] = $total_amount_arr;

		#echo "<pre>";
		#print_r($return_arr);
		#die;
		return $return_arr;
	}

    /**
     * 根据路径信息、订单信息和即时商家列表，得到最少镖金数、路径方案、订单方案
     * @author 姜伟
     * @param $order_arr
     * @param $merchant_list
     * @param $distance_arr	路径距离
     * @return array 包含最佳路径$route和最短路径$d
     * @todo 根据路径信息、订单信息和即时商家列表，得到最少镖金数、路径方案、订单方案
     */
    public static function getLeastFreightSolution($item_list, $merchant_list, $distance_arr)
	{
		$i = 0;
		$order_arr = array();
		$left_merchant_list = array();
		foreach ($merchant_list AS $k => $v)
		{
			if ($i == 0)
			{
				$order_arr[0]['d'] = $distance_arr[$k][0];	//总距离
				$order_arr[0]['route'] = $k . ',0';		//路径
				$order_arr[0]['merchant_list'][0] = array(
					'p'			=> $k,		//商家ID
					#'shop_name'	=> $v['shop_name'],		//商家店名
				);
			}
			else
			{
				//即时商家
				unset($v['longitude']);
				unset($v['latitude']);
				$left_merchant_list[$k] = $v;
			}
			$i ++;
		}
		#echo "<pre>";
		#print_r($order_arr);
		#print_r($left_merchant_list);
		#die;

		//调用系统推荐方法
    	$order_arr = self::getSystemRecommendSolution($order_arr, $left_merchant_list, $distance_arr);

		return $order_arr;
	}

    /**
     * 速度优先方案
     * @author 姜伟
     * @param array $item_list
     * @param array $merchant_list
     * @param array $distance_arr
     * @return $order_arr
     * @todo 速度优先方案
     */
    public static function getSpeedSolution($item_list, $merchant_list, $distance_arr)
    {
		$order_arr = array();
		$i = 0;
		foreach ($merchant_list AS $k => $v)
		{
			//非即时商家
			$order_arr[$i]['d'] = $distance_arr[$k][0];	//总距离
			$order_arr[$i]['freight'] = self::calTotayPrice($distance_arr[$k][0]);	//总镖金
			$order_arr[$i]['route'] = $k . ',0';		//路径
			$order_arr[$i]['shop_name']	= $v['shop_name'];		//商家店名
			$order_arr[$i]['merchant_id']	= $v['merchant_id'];		//商家店名
			$order_arr[$i]['logo']	= $v['logo'];		//商家店名
			$order_arr[$i]['item_list']	= $item_list[$k]['item_list'];		//商家店名
			$order_arr[$i]['is_immediate']	= $v['is_immediate'];		//商家店名

			//计算订单商品总价
			$total_amount = 0.00;
			foreach ($item_list[$k]['item_list'] AS $key => $val)
			{
				$total_amount += $val['real_price'] * $val['number'];
			}
			$order_arr[$i]['total_amount']	= $total_amount;		//订单商品总价
			$i ++;
		}

		return $order_arr;
    }

    /**
     * 将某个商家加入某个订单，得到最佳路径及最短路径
     * @author 姜伟
     * @param $order_info
     * @param $merchant_info
     * @param $arr	路径距离
     * @return array 包含最佳路径$route和最短路径$d
     * @todo 将某个商家加入某个订单，得到最佳路径及最短路径
     */
    public static function getShortestRoute($order_info, $merchant_info, $arr)
    {
		//当前最短距离
		$d = $order_info['d'];

		//当前即时商家
		$p2 = $merchant_info['p'];
		//d1，即时商家到用户之间的距离
		$d1 = self::getDistance($arr, $p2, 0);
		##echo "当前最短距离d = $d, $p2 到用户距离d1 = $d1 <br>";

		$d2 = MAX;
		$d_min = MAX;
		$route = '';

		//当前所有商家点集数组
		$r = explode(',', $order_info['route']);

		//遍历当前订单的商家列表，将当前即时商家插入到每两个点直接计算总距离d3，若d3 < d2，覆盖d2；
		$p4 = $r[0];
		foreach ($r AS $km => $m)
		{
			//将当前即时订单加入到该商家m之前，得出总距离d3
			$d3 = 0;
			//当前商家点p3
			$p3 = $km == 0 ? $p2 : $p4;
			//路径2
			$route2 = '';
			##echo "rr = <pre>";
			##print_r($r);

			for ($i = 0; $i < count($r); $i ++)
			{
				##echo "p3: $p3; p4: " . $r[$i] . ", m = $m<br>";
				if ($km)
				{
					//插在中间的情况
					#$p3 = $p3 == -1 ? $r[$i] : $p3;
					if ($m == $r[$i])
					{
						$route2 .= ',' . $p2 . ',' . $r[$i];
						$p3 = $r[$i];
						//插在中间的情况
						##echo "d3加之前=$d3,d=" . self::getDistance($arr, $p2, $r[$i]);
						$d3 += self::getDistance($arr, $p2, $r[$i]);
						##echo "d3加1上$p2" . $r[$i] . "=$d3,d=" . self::getDistance($arr, $p2, $r[$i-1]);
						$d3 += $arr[$p2][$r[$i-1]];
						##echo "d3加2上$p2" . $r[$i-1] . "=$d3,d=" . self::getDistance($arr, $p2, $r[$i-1]) . "<br>";
					}
					else
					{
						##echo "d3加之前=$d3,d=" . self::getDistance($arr, $p3, $r[$i]);
						$d3 += self::getDistance($arr, $p3, $r[$i]);
						##echo "d3加3上$p3" . $r[$i] . "=$d3,<br>";
						$p3 = $r[$i];
						$route2 .= $i == 0 ? $p3 : ',' . $p3;
					}
					##echo "i = $i, d3 = $d3, route2 = " . $route2 . "<br>";
				}
				else
				{
					//插在最前面的情况
					##echo "d3加之前=$d3,d=" . self::getDistance($arr, $p3, $r[$i]);
					$d3 += self::getDistance($arr, $p3, $r[$i]);
					##echo "d3加4上$p3" . $r[$i] . "=$d3,<br>";
					if ($m == $r[$i])
					{
						$route2 .= $i == 0 ? $p2 . ',' . $r[$i] : ',' . $p2 . ',' . $r[$i];
						$p3 = $i == 0 ? $r[$i] : $p2;
					}
					else
					{
						$p3 = $r[$i];
						$route2 .= $i == 0 ? $p3 : ',' . $p3;
					}
					##echo "i = $i, d3 = $d3, route2 = " . $route2 . "<br>";
				}
			}

			//若增加的距离比当前增加的最短距离短
			$d5 = $d3 - $d;
			if ($d5 < $d_min)
			{
				//若加入后的总距离比分开短
				$added_distance = $d3 - $d - $d1;
				//另外找镖师的价格
				$added_price1 = self::calTotayPrice($added_distance);
				//加在当前订单的价格
				$added_price2 = self::calPrice($added_distance);
				##echo "added_price1 = $added_price1, added_price2 = $added_price2<br>";
				if ($added_price1 > $added_price2)	//最少镖金和系统推荐用
				#if (0)	//速度优先用
				{
					$d2 = $d3;
					$d_min = $d5;
					$route = $route2;
					$order_num = $key;
				}
			}
			#echo "d3 = $d3, route2 = $route2 <br><br>";
			$p4 = $m;
		}

		$return_arr = array(
			'd2'	=> $d2,
			'd_min'	=> $d_min,
			'route'	=> $route2,
		);
		#echo "<pre>";
		#print_r($return_arr);
		#die;
		return $return_arr;
    }

    /**
     * 根据路径信息、订单信息和即时商家列表，得到最佳路径及最短路径
     * @author 姜伟
     * @param $order_arr
     * @param $merchant_list
     * @param $arr	路径距离
     * @return array 包含最佳路径$route和最短路径$d
     * @todo 将某个商家加入某个订单，得到最佳路径及最短路径
     */
    public static function getSystemRecommendSolution($order_arr, $merchant_list, $arr)
	{
		//系统推荐单笔订单商家上限
		$order_merchant_num_limit = $GLOBALS['config_info']['ORDER_MERCHANT_NUM_LIMIT'];

		foreach ($merchant_list AS $k => $v)
		{
			//将当前即时订单加入到当前非即时订单中，计算加入后的距离d2；若遍历完后，d2 <= d + d1，将该即时商家加入
			$d2 = MAX;
			$d_min = MAX;
			//加入当前非即时订单后，哪个订单的总距离最短
			$order_num = -1;
			//加入当前非即时订单后，总距离最短的路径列表，
			$route = '';

			foreach ($order_arr AS $key => $val)
			{
				if (count($val['merchant_list']) >= $order_merchant_num_limit)
				{
					//商家数量超出系统推荐订单数量上限，跳过
					continue;
				}

				$route_info = MapModel::getShortestRoute($val, array('p' => $k), $arr);
				$route2 = $route_info['route'];
				$d3 = $route_info['d2'];
				$d5 = $route_info['d_min'];
				if ($d5 < $d_min)
				{
					$d2 = $d3;
					$d_min = $d5;
					$route = $route2;
					$order_num = $key;
				}
			}

			##echo "<hr>d2 = $d2, route = $route, order_num = $order_num <hr>";
			if ($d2 != MAX)
			{
				//找到可以加入的订单，将当前商家加入到当前订单中
				$order_arr[$order_num]['merchant_list'][] = array(
					'p'	=> $k,		//商家
				);

				$order_arr[$order_num]['d'] = $d2;	//总距离
				$order_arr[$order_num]['route'] = $route;		//路径
			}
			else
			{
				//找不到可以加入的订单，新增一个订单
				$index = count($order_arr);
				$order_arr[$index]['merchant_list'][] = array(
					'p'	=> $k,		//商家
				);

				$order_arr[$index]['d'] = self::getDistance($distance_arr, $k, 0);	//总距离
				$order_arr[$index]['route'] = $k . ',0';		//路径
			}
			##echo "<hr><hr>";
			##echo "<pre>";
			##print_r($order_arr);
			##echo "<hr><hr>";
		}

		return $order_arr;
	}

    /**
     * 将商品信息，镖金信息，商家店名加入订单方案中
     * @author 姜伟
     * @param array $order_arr
     * @param array $merchant_list
     * @param array $item_list
     * @return $order_arr
     * @todo 将商品信息，镖金信息，商家店名加入订单方案中
     */
    public static function getOrderList($order_arr, $merchant_list, $item_list)
	{
		$order_list = array();
		foreach ($order_arr AS $k => $v)
		{
			$immediate_num = 0;
			$unimmediate_num = 0;
			$order_list[$k]['d'] = $v['d'];
			$order_list[$k]['route'] = $v['route'];
			//获取商家信息
			foreach ($v['merchant_list'] AS $key => $val)
			{
				//商家ID，店名
				$order_list[$k][$key]['merchant_id'] = $val['p'];
				$order_list[$k][$key]['shop_name'] = $merchant_list[$val['p']]['shop_name'];
				$order_list[$k][$key]['logo'] = $merchant_list[$val['p']]['logo'];
				$order_list[$k][$key]['is_immediate'] = $merchant_list[$val['p']]['is_immediate'];
				$is_immediate = $merchant_list[$val['p']]['is_immediate'];
				//即时商家数，非即时商家数
				$immediate_num += $is_immediate ? 1 : 0;
				$unimmediate_num += $is_immediate ? 0 : 1;

				//商品列表
				$order_list[$k][$key]['item_list'] = $item_list[$val['p']]['item_list'];
			}

			//计算镖金
			$order_list[$k]['freight'] = self::calTotayPrice($v['d'], $immediate_num, $unimmediate_num);

			//计算商品总价
			$order_list[$k]['total_amount'] = self::calItemPrice($order_list[$k]);
		}

		#echo "<pre>";
		#print_r($order_list);
		#die;
		return $order_list;
	}

    /**
     * 根据商家列表获取出事的订单列表（根据非即时订单拆分）
     * @author 姜伟
     * @param array $merchant_list
     * @param array $distance_arr
     * @return $order_arr
     * @todo 根据商家列表获取出事的订单列表（根据非即时订单拆分）
     */
    public static function getInitialOrderList($merchant_list, $distance_arr)
    {
		$order_arr = array();
		$i = 0;
		$left_merchant_list = array();
		foreach ($merchant_list AS $k => $v)
		{
			if ($v['is_immediate'])
			{
				//即时商家
				unset($v['longitude']);
				unset($v['latitude']);
				$left_merchant_list[$k] = $v;
			}
			else
			{
				//非即时商家
				$order_arr[$i]['d'] = $distance_arr[$k][0];	//总距离
				$order_arr[$i]['route'] = $k . ',0';		//路径
				$order_arr[$i]['merchant_list'][0] = array(
					'p'					=> $k,		//商家ID
					#'shop_name'			=> $v['shop_name'],		//商家店名
				);
				$i ++;
			}
		}
		#echo "<pre>";
		#print_r($order_arr);
		#print_r($left_merchant_list);
		#die;

		return array(
			'order_arr'			=> $order_arr,
			'left_merchant_list'=> $left_merchant_list,
		);
    }

    /**
     * 根据item_list获取商家信息列表（是否即时，坐标）
     * @author 姜伟
     * @param array $item_list
     * @return $merchant_list
     * @todo 根据item_list获取商家信息列表（是否即时，坐标）
     */
    public static function getOrderMerchantList($item_list, $item_tag = false)
    {
		$merchant_list = array();
		$merchant_obj = new MerchantModel();
		$i = 1;
		foreach ($item_list AS $k => $v)
		{
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $k, 'longitude, latitude, shop_name, logo, online');
			if ($merchant_info['online'] == 0)
			{
				unset($item_list[$k]);
				continue;
			}
			#echo $merchant_obj->getLastSql() . "<br>";
			//需要带商品信息
			if ($item_tag)
			{
				$merchant_info['item_list'] = $v['item_list'];
			}
			$merchant_info['is_immediate'] = $v['is_immediate'];
			$merchant_info['merchant_id'] = $k;
			#$merchant_info['online'] = $merchant_info['online'];
			$merchant_list[$k] = $merchant_info;
			$i ++;
		}

		return $merchant_list;
    }

    /**
     * 根据经纬度计算两个点之间的距离
     * @author 姜伟
     * @param float $lon1
     * @param float $lat1
     * @param float $lon2
     * @param float $lat2
     * @return $distance
     * @todo 根据经纬度计算两个点之间的距离
     */
    public static function calDistance($lon1, $lat1, $lon2, $lat2)
    {
		define('R', 6370996.81);
		define('pi', 3.1415926536);
		$distance = R * acos(cos($lat1*pi()/180 )*cos($lat2*pi()/180)*cos($lon1*pi()/180 -$lon2*pi()/180) + sin($lat1*pi()/180 )*sin($lat2*pi()/180));
		log_file('计算距离：lon1 = ' . $lon1 . ', lat1 = ' . $lat1 . ', lon2 = ' . $lon2 . ', lat2 = ' . $lat2 . ', distance = ' . $distance, 'distance');
		return round($distance);
    }

    /**
     * 根据商品信息获取用户及各商家之间的距离信息数组
     * @author 姜伟
     * @param array $merchant_list
     * @param int $user_address_id
     * @return $merchant_list
     * @todo 根据商品信息获取用户及各商家之间的距离信息数组
     */
    public static function getDistanceList($merchant_list, $user_address_id)
    {
		$distance_arr = array();
		//用户坐标
		$user_address_obj = new UserAddressModel();
		$member_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'longitude, latitude');
		foreach ($merchant_list AS $k => $v)
		{
			foreach ($merchant_list AS $key => $val)
			{
				#echo $k . "<br>";
				//到用户的距离
				$distance_arr[$k][0] = self::calDistance($member_info['longitude'], $member_info['latitude'], $v['longitude'], $v['latitude']);
				//到该商家的距离
				$distance_arr[$k][$key] = self::calDistance($val['longitude'], $val['latitude'], $v['longitude'], $v['latitude']);
			}
		}

		return $distance_arr;
    }

    /**
     * 获取两点之间的距离
     * @author 姜伟
     * @param array $arr 距离信息数组
     * @param int $p1 第一个点
     * @param int $p2 第二个点
     * @return float $d 距离
     * @todo 获取两点之间的距离
     */
    public static function getDistance($arr, $p1, $p2)
    {
		$d = $arr[$p1][$p2];
		return $d;
    }

    /**
     * 单商家根据距离计算起步价以外的镖金
     * @author 姜伟
     * @param float $d
     * @return int $freight 镖金
     * @todo 单商家根据距离计算起步价以外的镖金
     */
    public static function calPrice($d)
    {
		$freight_per_mile = $GLOBALS['config_info']['FREIGHT_PER_MILE'];
		$d = $d / 1000;
		$freight = $d * $freight_per_mile;
		return round($freight);
    }

    /**
     * 单商家根据距离计算镖金
     * @author 姜伟
     * @param float $d
     * @param int $immediate_num
     * @param int $unimmediate_num
     * @return int $freight 镖金
     * @todo 根据距离计算镖金
     */
    public static function calTotayPrice($d, $immediate_num = 0, $unimmediate_num = 0)
    {
		$foot_man_starting_price = $GLOBALS['config_info']['FOOT_MAN_STARTING_PRICE'];
		$starting_price_miles_limit = $GLOBALS['config_info']['STARTING_PRICE_MILES_LIMIT'];
		$freight_per_mile = $GLOBALS['config_info']['FREIGHT_PER_MILE'];
		$d = $d / 1000;

		//基本价格
		$freight = ($d <= $starting_price_miles_limit) ? $foot_man_starting_price : $foot_man_starting_price + ($d - $starting_price_miles_limit) * $freight_per_mile;

		if ($immediate_num + $unimmediate_num > 1)
		{
			//多商家情况下才有补贴
			//即时商家补贴
			$freight_per_realtime_merchant = $GLOBALS['config_info']['FREIGHT_PER_REALTIME_MERCHANT'];
			$freight += $immediate_num * $freight_per_realtime_merchant;

			//非即时商家补贴
			$freight_per_no_realtime_merchant = $GLOBALS['config_info']['FREIGHT_PER_NO_REALTIME_MERCHANT'];
			$freight += $unimmediate_num * $freight_per_no_realtime_merchant;
		}
		send_email('计算镖金算法', ' freight = ' . $freight . ', freight = ' . round($freight));

		return $freight < 1 ? $freight : round($freight);
    }

    /**
     * 计算商品总价
     * @author 姜伟
     * @param array $order_arr
     * @return int $freight 镖金
     * @todo 计算商品总价
     */
    public static function calItemPrice($order_arr)
    {
		$total_amount = 0.00;
		foreach ($order_arr AS $key => $val)
		{
			if (is_array($val))
			{
				foreach ($val['item_list'] AS $k => $v)
				{
					$total_amount += $v['real_price'] * $v['number'];
				}
			}
		}

		return $total_amount;
    }

    /**
     * 根据商家商品列表获取单商家订单信息
     * @author 姜伟
     * @param array $item_list
     * @param int $user_address_id
     * @return array 商品信息
     * @todo 根据商家商品列表获取单商家订单信息
     */
    public static function getSingleMerchantOrderInfo($item_list, $user_address_id)
	{
		//根据item_list获取商家信息列表（是否即时，坐标）
		$merchant_list = self::getOrderMerchantList($item_list);
		//根据商品信息获取用户及各商家之间的距离信息数组
		$distance_arr = self::getDistanceList($merchant_list, $user_address_id);

		//根据商家列表获取初始的订单列表（根据非即时订单拆分）
		$order_arr = self::getSpeedSolution($item_list, $merchant_list, $distance_arr);
		#echo "<pre>";
		#print_r($item_list);
		#print_r($order_arr);

		return $order_arr ? $order_arr[0] : array();
	}

	/**
     * 更新内存中的用户信息
     * @author 姜伟
     * @param string $key
     * @param string $value
     * @return boolean
     * @todo 更新内存中的用户信息
     */
    public static function flushUserInfo($key, $value, $user_id = 0)
	{
log_file('eeeee' . strtoupper(PHP_OS));
		if (strtoupper(PHP_OS) == 'LINUX')
		{
			$user_id = $user_id ? $user_id : session('user_id');
			$redis = new Redis();
			$redis->connect('localhost', 6379);
			$user_info = $redis->get('user_' . $user_id);
			$user_info = self::parseUserInfo($user_info);
log_file('dddd.' . arrayToString($user_info));
			$user_info[$key] = $value;
			return $redis->set('user_' . $user_id, self::generateUserInfo($user_info));
		}
		return false;
	}

	/**
     * 解析内存中的用户信息
     * @author 姜伟
     * @param str $user_info
     * @return boolean
     * @todo 解析内存中的用户信息
     */
    public static function parseUserInfo($user_info)
	{
		$user_info = json_decode($user_info, true);
		
		return $user_info;
	}

	/**
     * 将数组形式的user_info转化成字符串+END形式
     * @author 姜伟
     * @param str $user_info
     * @return boolean
     * @todo 将数组形式的user_info转化成字符串+END形式
     */
    public static function generateUserInfo($user_info)
	{
		return json_encode($user_info);
	}

	/**
     * 获取某订单的推送镖师列表
     * @author 姜伟
     * @param int $order_id
     * @return array $foot_man_list
     * @todo 获取某订单的推送镖师列表
     */
    public static function getFootManList($order_id)
	{
		$foot_man_list = array();
		$order_obj = new OrderModel($order_id);
		$merchant_order_obj = new MerchantOrderModel();
		$order_info = $order_obj->getOrderInfo('user_address_id, item_amount, express_fee, real_freight, route, meters, award_num');
		$merchant_list = $merchant_order_obj->field('foot_man_first_tag_id, foot_man_second_tag_id, foot_man_third_tag_id')->join('tp_merchant AS m ON m.merchant_id = tp_merchant_order.merchant_id')->where('order_id = ' . $order_id)->select();
log_file('/********************** begin merchant_list = ' . json_encode($merchant_list) . ', sql = ' . $merchant_order_obj->getLastSql(), 'isValidFootMan');
#log_file('/********************** begin merchant_list = ' . json_encode($merchant_list) . ', sql = ' . $merchant_order_obj->getLastSql(), 'calPushTime1');
		if ($order_info)
		{
			//获取用户小区/写字楼
			$user_address_id = $order_info['user_address_id'];
			$user_address_obj = new UserAddressModel();
			$user_building_name = $user_address_obj->getBuildingName($user_address_id);

			//获取第一个商家所在小区/写字楼，经纬度，先模拟，未完成
			$route = explode(',', $order_info['route']);
			$merchant_id = intval($route[0]);
			$merchant_obj = new MerchantModel($merchant_id);
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $merchant_id, 'building_id, longitude, latitude');
			log_file('get building_name. sql = ' . $merchant_obj->getLastSql());
			log_file('get building_name. merchant_info = ' . arrayToString($merchant_info));
			$building_obj = new BuildingModel();
			$building_info = $building_obj->getBuildingInfo('building_id = ' . $merchant_info['building_id'], 'building_name');
			$building_name = $building_info ? $building_info['building_name'] : '';
			$merchant_building_name = $building_name;
			log_file('get building_name. sql2 = ' . $building_obj->getLastSql(), 'isValidFootMan');
			log_file(json_encode($merchant_info), 'isValidFootMan');
			$lon = $merchant_info['longitude'];
			$lat = $merchant_info['latitude'];

			//订单中商家数量
			$merchant_order_obj = new MerchantOrderModel();
			$merchant_num = $merchant_order_obj->getMerchantOrderNum('order_id = ' . $order_id);

			//推送的消息内容
			$msg = array(
				'opt'	=> 'op',
				'id'	=> $order_id,
				'f'		=> $order_info['real_freight'],
				'd'		=> $order_info['meters'],
				'm'		=> $merchant_building_name,
				'u'		=> $user_building_name,
			);
			if ($order_info['award_num'])
			{
				$msg['awd'] = $order_info['award_num'];
			}
			if ($merchant_num > 1)
			{
				$msg['n'] = $merchant_num;
			}

			//获取所有上班的镖师
			$foot_man_obj = new FootManModel();
			$foot_man_list = $foot_man_obj->getFootManListInMemory(FootManModel::ONLINE);
log_file('foot_man_list = ' . json_encode($foot_man_list), 'isValidFootMan');

			/*** 取满足条件的待推送镖师，位数为total_push_num(配置项)begin ***/
			//距离上限，超过该值则不取
			$distance_limit = $GLOBALS['config_info']['DISTANCE_LIMIT'];
			//同时进行中的订单上限，超过该值则不取
			$going_order_num_limit = $GLOBALS['config_info']['FOOT_MAN_ORDER_NUM_LIMIT'];
log_file('9pre: foot_man_list = ' . json_encode($foot_man_list));
log_file('foot_man_list = ' . json_encode($foot_man_list), 'push');
			foreach ($foot_man_list AS $k => $v)
			{

//正式版过滤
if (C('IS_TEST'))
{
	if ($v['user_id'] < 10000000)
	{
log_file('过滤: IS_TEST = ' . C('IS_TEST') . ', user_id = ' . $v['user_id'], 'isValidFootMan');
		unset($foot_man_list[$k]);
		continue;
	}
}
else
{
	if ($v['user_id'] > 10000000)
	{
		unset($foot_man_list[$k]);
log_file('过滤: IS_TEST = ' . C('IS_TEST') . ', user_id = ' . $v['user_id'], 'isValidFootMan');
		continue;
	}
}


log_file('v = ' . json_encode($v), 'isValidFootMan');
				if (OrderModel::isValidFootMan($order_info['item_amount'], $merchant_list, $v['rank_id'], intval($v['foot_man_first_tag_id']), intval($v['foot_man_second_tag_id']), intval($v['foot_man_third_tag_id']), $v['user_id']) == false)
				{
log_file('订单金额不足被移除：foot_man_id = ' . $v['user_id'], 'isValidFootMan');
					//超过该上限，将该镖师移出数组
					unset($foot_man_list[$k]);
					continue;
				}

				$foot_man_list[$k]['f'] = OrderModel::calFreight($order_info['express_fee'], $v['rank_id'], intval($v['foot_man_first_tag_id']), intval($v['foot_man_second_tag_id']), intval($v['foot_man_third_tag_id']));
				//镖师进行中订单数量
				#$going_order_num1 = $order_obj->getOrderNum('isuse = 1 AND foot_man_id = ' . $v['foot_man_id'] . ' AND order_status = ' . OrderModel::PRE_DELIVERY);
				#$going_order_num2 = $order_obj->getOrderNum('isuse = 1 AND foot_man_id = ' . $v['user_id'] . ' AND order_status = ' . OrderModel::PRE_DELIVERY);
				$going_order_num = $v['going_order_num'];
#$success = send_email('镖师进行中订单：内存中user_id = ' . $v['user_id'] . '，根据user_id计算出来的going_order_num = ' . $going_order_num2 . ', 内存中foot_man_id = ' . $v['foot_man_id'] . '，根据foot_man_id计算出来的going_order_num = ' . $going_order_num1 . ', 内存中的订单going_order_num = ' . $going_order_num);
				//计算与第一个商家的距离
				$distance = self::calDistance($v['longitude'], $v['latitude'], $lon, $lat);
log_file('9pre: foot_man_id = ' . $v['user_id'] . ' distance = ' . $distance . ', going_order_num_limit = ' . $going_order_num_limit . ', going_order_num = ' . $going_order_num);
				if ($distance > $distance_limit || $going_order_num >= $going_order_num_limit)
				{
log_file('超出距离被过滤: foot_man_id = ' . $v['user_id'] . ' distance = ' . $distance . ', going_order_num_limit = ' . $going_order_num_limit . ', going_order_num = ' . $going_order_num, 'isValidFootMan');
					//超过该上限，将该镖师移出数组
					unset($foot_man_list[$k]);
				}
				else
				{
					$foot_man_list[$k]['d'] = $distance;
				}
			}

			//距离第一个商家的位置排序
log_file('排序前foot_man_list = ' . json_encode($foot_man_list), 'push');
			$foot_man_list = self::sortFootManByDistanceASC($foot_man_list, 'd', $GLOBALS['config_info']['TOTAL_PUSH_NUM']);
log_file('排序后foot_man_list = ' . json_encode($foot_man_list), 'push');

			//计算推送时间，去除多余信息，加入推送信息
			foreach ($foot_man_list AS $k => $v)
			{
				$foot_man_list[$k]['opt'] = 'order_push';
				$foot_man_list[$k]['push_time'] = self::calPushTime($v, $k);
				$foot_man_list[$k]['order_id'] = $order_id;
				$foot_man_list[$k]['state'] = 0;
				$msg['f'] = $v['f'];
				$foot_man_list[$k]['msg'] = $msg;

				//多余信息
				unset($foot_man_list[$k]['pre_push_dead_time']);
				unset($foot_man_list[$k]['last_push_time']);
				unset($foot_man_list[$k]['lon']);
				unset($foot_man_list[$k]['lat']);
				unset($foot_man_list[$k]['online']);
				unset($foot_man_list[$k]['going_order_num']);
				unset($foot_man_list[$k]['rank_id']);
				unset($foot_man_list[$k]['meters']);
				unset($foot_man_list[$k]['score_avg']);
				unset($foot_man_list[$k]['total_order_num']);
				unset($foot_man_list[$k]['on_time_rate']);
			}

			/*** 取满足条件的待推送镖师，位数为total_push_num(配置项)begin ***/
		}
	log_file('/********************** end foot_man_list = ' . json_encode($foot_man_list), 'calPushTime1');

		return $foot_man_list;
	}

	public static function sortFootManByDistanceASC($arr, $field, $total_push_num)
	{
		$count = count($arr);
		for ($i = 0; $i < $count; $i ++)
		{
			$min = $i;
			for ($j = $i + 1; $j < $count; $j ++)
			{
				if ($arr[$j][$field] < $arr[$min][$field])
				{
					$temp = $arr[$j];
					$arr[$j] = $arr[$min];
					$arr[$min] = $temp;
				}
			}

			if ($i == $total_push_num - 1)
			{
				break;
			}
		}

log_file('过滤foot_man_list = ' . json_encode($arr), 'push');
		//多出来从数组中移除
		for ($i = $total_push_num; $i < $count; $i ++)
		{
			unset($arr[$i]);
		}

		return $arr;
	}

	public static function calPushTime($foot_man_info, $index)
	{
log_file('index = ' . $index, 'index');
		//foot_man_receive_order_inteval: 镖师每间隔多少秒接受服务器新订单推送
		$foot_man_receive_order_inteval = $GLOBALS['config_info']['FOOT_MAN_RECEIVE_ORDER_INTEVAL'];
		//每次推送镖师数量
		$foot_man_limit_per_push = $GLOBALS['config_info']['FOOT_MAN_LIMIT_PER_PUSH'];
		//延迟秒数
		$delay_time = floor($index / $foot_man_limit_per_push);
		//上次发送时间
		$last_push_time = $foot_man_info['last_push_time'];
		//消息队列中最后一个订单的推送时间
		$pre_push_dead_time = $foot_man_info['pre_push_dead_time'];

		//当前时间
		$time = time();
		//push_time
		$push_time = 0;
		if ($pre_push_dead_time < $time)
		{
			//消息队列中没有消息了
			//若上次发送时间在foot_man_receive_order_inteval秒以内
			if ($time - $last_push_time < $foot_man_receive_order_inteval)
			{
				$push_time = $last_push_time + $foot_man_receive_order_inteval;
			}
			//若上次发送时间在foot_man_receive_order_inteval秒以外，马上发送，置0不变
		}
		else
		{
			//消息队列中还有消息
			$push_time = $pre_push_dead_time + $foot_man_receive_order_inteval;
		}

		//按优先级延迟分批发送处理
		if ($push_time < $time + $delay_time)
		{
			$push_time = $time + $delay_time;
		}

		//更新消息队列中最后一个订单的推送时间字段
		self::flushUserInfo('pre_push_dead_time', $push_time, $foot_man_info['user_id']);
log_file('计算镖师push_time： last_push_time = ' . $last_push_time . ', 内存中镖师信息 = ' . json_encode($foot_man_info) . ', 消息队列中最后一个订单的推送时间 = ' . $pre_push_dead_time . ', push_time = ' . $push_time, 'push');
log_file('计算镖师push_time： last_push_time = ' . $last_push_time . ', 内存中镖师信息 = ' . json_encode($foot_man_info) . ', 消息队列中最后一个订单的推送时间 = ' . $pre_push_dead_time . ', push_time = ' . $push_time, 'calPushTime1');

		return $push_time;
	}

    /**
     * 根据order_id推送订单给镖师
     * @author 姜伟
     * @param $order_id
     * @return array 3种方案
     * @todo 根据order_id推送订单给镖师
     */
    public static function pushOrder($order_id)
	{
		/*** 推送镖师begin ***/
		//获取推送的镖师列表
		$foot_man_list = MapModel::getFootManList($order_id);
log_file('order_id = ' . $order_id . ', foot_man_list = ' . json_encode($foot_man_list), 'isValidFootMan');
		log_file('3pre push order. order_id = ' . $order_id);
		log_file(arrayToString($foot_man_list));
		$order_push_arr = array();
		$push_obj = new PushModel();
		foreach ($foot_man_list AS $k => $v)
		{
			//一一推送镖师
			$user_id = $v['user_id'];
			$msg = $v;
			$push_obj->push($user_id, $msg);

			//保存到数据库
			$order_push_arr[] = array(
				'order_id'		=> $order_id,
				'foot_man_id'	=> $user_id,
				'push_time'		=> $v['push_time'],
				'addtime'		=> time(),
				'content'		=> json_encode($v['msg']),
			);
		}
		$order_push_obj = new OrderPushModel();
		$order_push_obj->addAll($order_push_arr);
		/*** 推送镖师end ***/
	}
}

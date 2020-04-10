<?php
/**
 * 商家模型类
 */

class MerchantModel extends BaseModel
{
    // 商家id
    public $merchant_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $merchant_id 商家ID
     * @return void
     * @todo 初始化商家id
     */
    public function MerchantModel($merchant_id)
    {
        parent::__construct('merchant');

        if ($merchant_id = intval($merchant_id))
		{
            $this->merchant_id = $merchant_id;
		}
    }

    /**
     * 获取商家信息
     * @author 姜伟
     * @param int $merchant_id 商家id
     * @param string $fields 要获取的字段名
     * @return array 商家基本信息
     * @todo 根据where查询条件查找商家表中的相关数据并返回
     */
    public function getMerchantInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改商家信息
     * @author 姜伟
     * @param array $arr 商家信息数组
     * @return boolean 操作结果
     * @todo 修改商家信息
     */
    public function editMerchant($arr)
    {
        return $this->where('merchant_id = ' . $this->merchant_id)->save($arr);
    }

    /**
     * 添加商家
     * @author 姜伟
     * @param array $arr 商家信息数组
     * @return boolean 操作结果
     * @todo 添加商家
     */
    public function addMerchant($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除商家
     * @author 姜伟
     * @param int $merchant_id 商家ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delMerchant($merchant_id)
    {
        if (!is_numeric($merchant_id)) return false;
		return $this->where('merchant_id = ' . $merchant_id)->delete();
    }

    /**
     * 获取商家所有信息，包含user表中的
     * @author 姜伟
     * @param string $where
     * @param string $fields
     * @return string $shop_name
     * @todo 获取商家所有信息，包含user表中的
     */
    public function getMerchantInfoAll($where, $fields = '')
    {
		return $this->field($fields)->join('tp_users AS u ON u.user_id = tp_merchant.merchant_id')->where($where)->find();
    }
 
    /**
     * 根据merchant_id获取商家名称
     * @author 姜伟
     * @param int $merchant_id
     * @return string $shop_name
     * @todo 根据merchant_id获取商家名称
     */
    public function getMerchantName($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->getField('shop_name');
    }

    /**
     * 根据where子句获取商家数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的商家数量
     * @todo 根据where子句获取商家数量
     */
    public function getMerchantNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询商家信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 商家基本信息
     * @todo 根据SQL查询字句查询商家信息
     */
    public function getMerchantUserList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->join('tp_users AS u ON u.user_id = tp_merchant.merchant_id')->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 根据where子句查询商家信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 商家基本信息
     * @todo 根据SQL查询字句查询商家信息
     */
    public function getMerchantList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取商家列表页数据信息列表
     * @author 姜伟
     * @param array $merchant_list
     * @return array $merchant_list
     * @todo 根据传入的$merchant_list获取更详细的商家列表页数据信息列表
     */
    public function getListData($merchant_list, $is_front = true)
    {
    	$special_obj = new SpecialOfferModel();
    	$vouchers_obj = new VouchersModel();
		foreach ($merchant_list AS $k => $v)
		{
			//商家分类名称
			$class_obj = new ClassModel($v['class_id']);
			$class_info = $class_obj->getClassInfo('class_id = ' . $v['class_id'], 'class_name');
			$merchant_list[$k]['class_name'] = $class_info['class_name'];

			//与用户的距离
			$cur_lon = session('cur_lon');
			$cur_lat = session('cur_lat');
			if(!($cur_lon && $cur_lat))
			{
				$user_address_id = intval(session('user_address_id'));
				$user_address_obj = new UserAddressModel();
				$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'longitude, latitude');
				$cur_lon = $user_address_info['longitude'];
				$cur_lat = $user_address_info['latitude'];
			}
			$merchant_list[$k]['distance'] = MapModel::calDistance($cur_lon, $cur_lat, $v['longitude'], $v['latitude']);
			$merchant_distance_limit = $GLOBALS['config_info']['MERCHANT_DISTANCE_LIMIT'];
log_file($merchant_distance_limit * 1000 . ', ' . $merchant_list[$k]['distance'] . ', lon = ' . $cur_lon . ', lat = ' . $cur_lon . ', lon2 = ' . $v['longitude'] . ', lat2 = ' . $v['latitude'], 'lonlat');
			if ($is_front && $merchant_distance_limit * 1000 < $merchant_list[$k]['distance'])
			{
				unset($merchant_list[$k]);
				continue;
			}

			//商圈
			$trading_area_obj = new TradingAreaModel();
			$trading_area_info = $trading_area_obj->getTradingAreaInfo('trading_area_id = ' . $v['trading_area_id'], 'trading_area_name');
			$merchant_list[$k]['trading_area_name'] = $trading_area_info['trading_area_name'];

			//是否特价商家和有券商家
			$now = time();
			$s_where = 'merchant_id = ' . $v['merchant_id'] . ' AND isuse = 1 AND (scope = 0 OR scope = 1) AND (is_adv_display = 2 OR is_adv_display = 0) AND (start_time < ' .  $now . ' AND end_time > ' . $now . ')';
			$special_total = $special_obj->getSpecialOfferNum($s_where);
			$merchant_list[$k]['is_special'] = $special_total > 0 ? 1 : 0;
			$v_where = 'merchant_id = ' . $v['merchant_id'] . ' AND isuse = 1 AND (scope = 0 OR scope = 1) AND (start_time < ' .  $now . ' AND end_time > ' . $now . ')';
			$vouchers_total = $vouchers_obj->getVouchersNum($v_where);
			$merchant_list[$k]['is_vouchers'] = $vouchers_total > 0 ? 1 : 0;
		}

		return $merchant_list;
    }

    /**
     * 获取商家流水列表页数据信息列表
     * @author 姜伟
     * @param array $merchant_list
     * @return array $merchant_list
     * @todo 根据传入的$merchant_list获取更详细的商家流水列表页数据信息列表
     */
    public function getStatListData($merchant_list, $is_front = true)
    {
		foreach ($merchant_list AS $k => $v)
		{
			//商家分类名称
			$class_obj = new ClassModel($v['class_id']);
			$class_info = $class_obj->getClassInfo('class_id = ' . $v['class_id'], 'class_name');
			$merchant_list[$k]['class_name'] = $class_info['class_name'];

            $building_info = D('Building')->where('building_id = %d', $v['building_id'])->find();

			//所在区域
            $area_string = AreaModel::getAreaString(
                $building_info['province_id'],
                $building_info['city_id'],
                $building_info['area_id']
            );
			$area_string = $area_string ? $area_string : '【' . $v['provice'] . '】' . '【' . $v['city'] . '】';
			$merchant_list[$k]['area_string'] = $area_string;
		}

		return $merchant_list;
    }

    /**
     * 获取数据库查询用的商家排序项列表
     * @author 姜伟
     * @param void
     * @return array $merchant_orderby_list
     * @todo 获取数据库查询用的商家排序项列表
     */
    public static function getMerchantOrderbyList()
    {
		return array(
			'0'	=> 'serial ASC',
			'1'	=> 'score_avg DESC',
			'2'	=> 'make_time_avg ASC',
			'3'	=> 'total_sales_num DESC',
			'4'	=> 'on_time_rate DESC',
			'5'	=> 'refuse_order_rate ASC',
			'6'	=> 'distance ASC',
			'7'	=> 'rec_serial ASC',
		);
	}

    /**
     * 获取前台展示的商家排序项列表
     * @author 姜伟
     * @param void
     * @return array $merchant_orderby_list
     * @todo 获取前台展示的商家排序项列表
     */
    public static function getFrontMerchantOrderbyList()
    {
		return array(
			'0'	=> '默认排序',
			'1'	=> '信誉最好',
			'2'	=> '制作时间最短',
			'3'	=> '销量最高',
			'4'	=> '制作最准时',
			'5'	=> '拒单最少',
			'6'	=> '离我最近',
		);
	}

	//根据商品列表获取根据商家分组的商品列表，带是否即时商家字段
	static function getMerchantItem($item_list)
	{
		$new_item_list = array();
		foreach ($item_list AS $k => $v)
		{
			$merchant_id = $v['merchant_id'];
			$is_immediate = $v['is_immediate'];
			unset($v['is_immediate']);
			$new_item_list[$merchant_id]['item_list'][] = $v;
			if (!isset($new_item_list[$merchant_id]['is_immediate']) || !$new_item_list[$merchant_id]['is_immediate'])
			{
				$new_item_list[$merchant_id]['is_immediate'] = $is_immediate;
			}
		}

		return $new_item_list;
	}

	//生成计算距离的sql字段
	public static function getDistanceSql($lon, $lat)
	{
		return 'longitude, latitude, SQRT(POWER(longitude - ' . $lon . ', 2) + POWER(latitude - ' . $lat . ', 2)) AS distance';
	}

	//根据排序字段给商家排序
	public static function orderMerchantList($merchant_list, $orderby)
	{
		$merchant_orderby_list = self::getMerchantOrderbyList();
		$orderby = $merchant_orderby_list[$orderby];
		$orderby = explode(' ', $orderby);
		$asc = $orderby[1] == 'ASC' ? true : false;
		$count = count($merchant_list);
		//升序/降序
		if ($asc)
		{
			$merchant_list = select_sort_asc($merchant_list, $orderby[0]);
		}
		else
		{
			$merchant_list = select_sort_desc($merchant_list, $orderby[0]);
		}
		return $merchant_list;
	}

	/**
	 * 根据商家名称查询商家ID列表
	 * @author 姜伟
	 * @param string $shop_name 商家名称
	 * @return string 商家ID列表，英文逗号隔开
	 * @todo 根据商家名称查询商家ID列表
	 */
	public function getMerchantIdByShopName($shop_name)
	{
		if ($shop_name)
		{
			$merchant_info = $this->field('GROUP_CONCAT(merchant_id) AS merchant_ids')->where('shop_name LIKE "%' . $shop_name . '%"')->group('"1"')->find();
			return $merchant_info['merchant_ids'];
		}
		return null;
	}

    /**
     * 重新计算商家排序项和综合排序项
     * @author 姜伟
     * @param int $item_id
     * @param string $serial_type
     * @param string $serial_value
     * @param boolean $write_tag	是否需要写数据库
     * @return boolean
     * @todo 重新计算商家排序项和综合排序项
     */
    public function recalculateSerialItem($merchant_id, $serial_type, $serial_value, $write_tag = true)
    {
		$score_avg_percent = 0.2;
		$make_time_avg_percent = 0.1;
		$make_time_avg_divisor = 5;
		$total_sales_num_percent = 0.4;
		$total_sales_num_divisor = 1;
		$on_time_rate_percent = 0.2;
		$refuse_order_rate_percent = 0.1;
		//起始时间记为3天前
		$start_time = time() - 86400 * 3;

		switch($serial_type)
		{
			case 'score_avg':
				//平均评分
				if ($write_tag)
				{
					//获取新的平均评分
					$user_comment_merchant_obj = new UserCommentMerchantModel();
					$user_comment_merchant_info = $user_comment_merchant_obj->field('COUNT(*) AS total_num, SUM(score) AS total_score')->where('merchant_id = ' . $merchant_id)->find();
					//获取好评总数
					$merchant_info = $this->getMerchantInfo('merchant_id = ' . $merchant_id, 'credit_num');
					$credit_num = $serial_value ? 1 : 0;
					$credit_num = $merchant_info['credit_num'] + $credit_num;

					$serial_value = $user_comment_merchant_info['total_score'] / $user_comment_merchant_info['total_num'];

					#echo $user_comment_merchant_obj->getLastSql();
					#echo "<pre>";
					#print_r($user_comment_merchant_info);
					#die;
					$arr = array(
						$serial_type	=> $serial_value,
						'credit_num'	=> $credit_num,
					);
					$this->where('merchant_id = ' . $merchant_id)->save($arr);
					#echo $this->getLastSql();
				}
				break;
			case 'make_time_avg':
				if ($write_tag)
				{
					//获取订单平均制作时间
					$merchant_order_obj = new MerchantOrderModel();
					$merchant_order_info = $merchant_order_obj->field('AVG(foot_man_pickup_time - merchant_check_time) AS make_time_avg')->where('foot_man_pickup_time > 0 AND merchant_check_time > 0 AND merchant_id = ' . $merchant_id)->find();
					$serial_value = $merchant_order_info['make_time_avg'];

					#echo $merchant_order_obj->getLastSql();
					#echo "<pre>";
					#print_r($merchant_order_info);
					#die;
					$arr = array(
						$serial_type	=> $serial_value,
					);
					$this->where('merchant_id = ' . $merchant_id)->save($arr);
					#echo $this->getLastSql();
				}
				//商品价格
				break;
			case 'total_sales_num':
				//商家总销量
				if ($write_tag)
				{
					$merchant_info = $this->getMerchantInfo('merchant_id = ' . $merchant_id, 'total_sales_num');
					$serial_value = $merchant_info['total_sales_num'] + $serial_value;
					$arr = array(
						$serial_type	=> $serial_value
					);
					$this->where('merchant_id = ' . $merchant_id)->save($arr);
				}
				break;
			case 'on_time_rate':
				//及时率
				if ($write_tag)
				{
					//获取订单总数
					$merchant_order_obj = new MerchantOrderModel();
					$merchant_order_info = $merchant_order_obj->field('COUNT(*) AS total_order_num')->where('foot_man_pickup_time > 0 AND merchant_check_time > 0 AND merchant_id = ' . $merchant_id)->find();
					$total_order_num = $merchant_order_info['total_order_num'];

					//获取及时到达的订单总数
					$merchant_order_info = $merchant_order_obj->field('COUNT(*) AS total_order_num')->where('foot_man_pickup_time > 0 AND merchant_check_time > 0 AND merchant_id = ' . $merchant_id . ' AND (foot_man_pickup_time - merchant_check_time) >= merchant_promise_time * 60')->find();
					$total_on_time_order_num = $merchant_order_info['total_order_num'];
					$serial_value = $total_on_time_order_num / $total_order_num * 100;
					#echo $merchant_order_obj->getLastSql();
					#var_dump($total_on_time_order_num);
					#var_dump($total_order_num);
					$arr = array(
						$serial_type	=> $serial_value
					);
					$this->where('merchant_id = ' . $merchant_id)->save($arr);
					#echo $this->getLastSql();
				}
				break;
			case 'refuse_order_rate':
log_file('111', 'merchant');
				//拒单率
				if ($write_tag)
				{
					//获取订单总数
					$merchant_order_obj = new MerchantOrderModel();
					$merchant_order_info = $merchant_order_obj->field('COUNT(*) AS total_order_num')->where('merchant_id = ' . $merchant_id)->find();
					$total_order_num = $merchant_order_info['total_order_num'];

					//获取被拒绝的订单总数
					$merchant_order_info = $merchant_order_obj->field('COUNT(*) AS total_order_num')->where('(is_refund = ' . MerchantOrderModel::MANUAL_REFUSED . ' OR is_refund = ' . MerchantOrderModel::TIMEOUT_REFUSED . ') AND merchant_id = ' . $merchant_id)->find();
					$total_refused_order_num = $merchant_order_info['total_order_num'] + 1;
					$serial_value = $total_refused_order_num / $total_order_num * 100;
#log_file('112, ' . $total_refused_order_num . ', ' . $total_order_num . ', ' . $serial_value, 'merchant');
					#echo "<pre>";
					#echo $merchant_order_obj->getLastSql();
					#var_dump($total_refused_order_num);
					#var_dump($total_order_num);
					$arr = array(
						$serial_type	=> $serial_value
					);
					$this->where('merchant_id = ' . $merchant_id)->save($arr);
					#echo $this->getLastSql();
				}
				break;
			default:
				return false;
		}

		$merchant_info = $this->getMerchantInfo('merchant_id = ' . $merchant_id, 'score_avg, total_sales_num, make_time_avg, on_time_rate, refuse_order_rate');
		$serial = $total_sales_num_percent * $merchant_info['total_sales_num'] / $total_sales_num_divisor + $score_avg_percent * $merchant_info['score_avg'] / 86400 + $make_time_avg_percent * $merchant_info['make_time_avg'] / $make_time_avg_divisor + $on_time_rate_percent * $merchant_info['on_time_rate'] / 100 + $refuse_order_rate_percent * $merchant_info['refuse_order_rate'] / 100;
log_file('计算serial = ' . $serial, 'merchant');
		#log_file('total_sales_num_percent = ' . $total_sales_num_percent . ', total_sales_num = ' . $merchant_info['total_sales_num'] . ', total_sales_num_divisor = ' . $total_sales_num_divisor . ', score_avg_percent = ' . $);
		#var_dump($serial);

		//保存到数据库
		$arr = array(
			'serial'	=> $serial
		);
		$success = $this->where('merchant_id = ' . $merchant_id)->save($arr);
log_file('计算serial = ' . $this->getLastSql(), 'merchant');
		return $success;
	}
}

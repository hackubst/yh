<?php
/**
 * 镖金活动模型类
 */

class FreightActivityModel extends BaseModel
{
    // 镖金活动id
    public $freight_activity_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param $freight_activity_id 镖金活动ID
     * @return void
     * @todo 初始化镖金活动id
     */
    public function FreightActivityModel($freight_activity_id)
    {
        parent::__construct('freight_activity');

        if ($freight_activity_id = intval($freight_activity_id))
		{
            $this->freight_activity_id = $freight_activity_id;
		}
    }

    /**
     * 获取镖金活动信息
     * @author 姜伟
     * @param int $freight_activity_id 镖金活动id
     * @param string $fields 要获取的字段名
     * @return array 镖金活动基本信息
     * @todo 根据where查询条件查找镖金活动表中的相关数据并返回
     */
    public function getFreightActivityInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 获取镖金活动信息
     * @author 姜伟
     * @param int $freight_activity_id 镖金活动id
     * @param string $fields 要获取的字段名
     * @return array 镖金活动基本信息
     * @todo 根据where查询条件查找镖金活动表中的相关数据并返回
     */
    public function getFreightActivityInfoAll($where, $fields = '', $orderby = '')
    {
		return $this->field($fields)->join('tp_freight_activity_rule AS fcr ON fcr.freight_activity_rule_id = tp_freight_activity.freight_activity_rule_id')->where($where)->order($orderby)->find();
    }

    /**
     * 修改镖金活动信息
     * @author 姜伟
     * @param array $arr 镖金活动信息数组
     * @return boolean 操作结果
     * @todo 修改镖金活动信息
     */
    public function editFreightActivity($arr)
    {
        return $this->where('freight_activity_id = ' . $this->freight_activity_id)->save($arr);
    }

    /**
     * 添加镖金活动
     * @author 姜伟
     * @param array $arr 镖金活动信息数组
     * @return boolean 操作结果
     * @todo 添加镖金活动
     */
    public function addFreightActivity($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除镖金活动
     * @author 姜伟
     * @param int $freight_activity_id 镖金活动ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delFreightActivity($freight_activity_id)
    {
        if (!is_numeric($freight_activity_id)) return false;
		return $this->where('freight_activity_id = ' . $freight_activity_id)->delete();
    }

    /**
     * 根据where子句获取镖金活动数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的镖金活动数量
     * @todo 根据where子句获取镖金活动数量
     */
    public function getFreightActivityNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询镖金活动信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 镖金活动基本信息
     * @todo 根据SQL查询字句查询镖金活动信息
     */
    public function getFreightActivityList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取活动类型列表
     * @author 姜伟
     * @param void
     * @return array $activity_type_list
     * @todo 获取活动类型列表
     */
    public static function getActivityTypeList()
    {
		return array(
			'0'	=> '默认规则',
			'1'	=> '订单红包',
			'2'	=> '新用户红包',
			'3'	=> '节假日红包',
			'4'	=> '其他',
		);
    }

    /**
     * 获取镖金活动列表页数据信息列表
     * @author 姜伟
     * @param array $freight_activity_list
     * @return array $freight_activity_list
     * @todo 根据传入的$freight_activity_list获取更详细的镖金活动列表页数据信息列表
     */
    public function getListData($freight_activity_list)
    {
		foreach ($freight_activity_list AS $k => $v)
		{
			//获取活动信息
			$freight_activity_rule_obj = new FreightActivityRuleModel();
			$freight_activity_rule_info = $freight_activity_rule_obj->getFreightActivityRuleInfo('freight_activity_rule_id = ' . $v['freight_activity_rule_id'], 'activity_small_pic, activity_name, total_freight, coupon_valid_day_num');
			$freight_activity_list[$k]['activity_small_pic'] = $freight_activity_rule_info['activity_small_pic'];
			$freight_activity_list[$k]['activity_name'] = $freight_activity_rule_info['activity_name'];
			$freight_activity_list[$k]['total_freight'] = $freight_activity_rule_info['total_freight'];
			$freight_activity_list[$k]['coupon_valid_day_num'] = $freight_activity_rule_info['coupon_valid_day_num'];
			//获取用户姓名
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('realname, nickname');
			$freight_activity_list[$k]['realname'] = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
		}

		return $freight_activity_list;
    }

    /**
     * 根据活动ID随机获得一张优惠券
     * @author 姜伟
     * @param int $freight_activity_id
     * @return int 优惠券面额
     * @todo 根据活动ID随机获得一张优惠券，ID无效返回0
     */
    public function getCouponNum($freight_activity_id)
    {
		$freight_activity_info = $this->getFreightActivityInfo('freight_activity_id = ' . $freight_activity_id, '');
		//剩余份数，已发送总数
		$freight_coupon_obj = new FreightCouponModel();
		$freight_coupon_info = $freight_coupon_obj->field('COUNT(*) AS total, SUM(num) AS send_num')->where('freight_activity_id = ' . $freight_activity_id)->find();
		$send_num = $freight_coupon_info['send_num'] ? $freight_coupon_info['send_num'] : 0;
		//剩余份数
		$left_num = $freight_activity_info['total_num'] - $freight_coupon_info['total'];
		if ($left_num == 0)
		{
			//已被抢光
			return 0;
		}

		$freight_activity_rule_obj = new FreightActivityRuleModel();
		$freight_activity_rule_info = $freight_activity_rule_obj->getFreightActivityRuleInfo('freight_activity_rule_id = ' . $freight_activity_info['freight_activity_rule_id']);
		//最多发放优惠数
		$total_freight = $freight_activity_rule_info['total_freight'];
		//单个红包镖金上限
		$limit_freight = $freight_activity_rule_info['limit_freight'];

		//剩余总面额
		$left_freight = $total_freight - $send_num;
		#echo 'total_freight = ' . $total_freight . "<br>";
		#echo 'send_num = ' . $send_num . "<br>";
		#echo 'left_freight = ' . $left_freight . "<br>";
		//剩余单次可发放最大值
		$max_num = $left_freight - $left_num - 1;
		#echo 'max_num = ' . $max_num . "<br>";
		$max_num = $max_num >= $limit_freight ? $limit_freight : $max_num;
		#echo 'max_num = ' . $max_num . "<br>";
		return rand(1, $max_num);
	}

    /**
     * 根据订单ID生成/获取订单红包
     * @author 姜伟
     * @param int $order_id
     * @return int $freight_activity_id 0配送员当前无活动，-1表示无效的订单
     * @todo 根据订单ID生成/获取订单红包
     */
    public function generateFreightActivity($order_id)
	{
		#$user_id = intval(session('user_id'));

		//安全过滤，查看订单是否属于当前用户，防止恶意刷券
		$order_obj = new OrderModel();
		#$order_num = $order_obj->getOrderNum('order_id = ' . $order_id . ' AND user_id = ' . $user_id);
		//解决配送员端确认收货时无法收到红包的bug
		$order_num = $order_obj->getOrderNum('order_id = ' . $order_id);
		if (!$order_num)
		{
			return -1;
		}

		//安全过滤，获取当前订单的确认时间，若确认时间不是今天，不给予红包
		$order_obj = new OrderModel($order_id);
		$order_info = $order_obj->getOrderInfo('confirm_time, user_id');
		$user_id = $order_info['user_id'];
		//今天0点的时间戳
		$today_time = strtotime(date('Y-m-d', time()));
		if ($order_info['confirm_time'] < $today_time)
		{
			//在今天之前确认的订单，不给予红包
			return -1;
		}

		//是否已生成该订单的红包
		$freight_activity_info = $this->getFreightActivityInfo('order_id = ' . $order_id, 'freight_activity_id');
		if ($freight_activity_info)
		{
			//已生成，直接返回活动ID
			return $freight_activity_info['freight_activity_id'];
		}

		//未生成，生成之
		//查看是否存在当天的活动规则
		//今天0点的时间戳
		$today_time = strtotime(date('Y-m-d', time()));
		$freight_activity_rule_obj = new FreightActivityRuleModel();
		$freight_activity_rule_info = $freight_activity_rule_obj->getFreightActivityRuleInfo('activity_type = 1 AND day_time = ' . $today_time);
log_file($freight_activity_rule_obj->getLastSql(), 'coupon');
log_file(json_encode($freight_activity_rule_info), 'coupon');
		if (!$freight_activity_rule_info)
		{
			//采用默认的规则
			$freight_activity_rule_info = $freight_activity_rule_obj->getFreightActivityRuleInfo('activity_type = 0');
		}

		//根据total_num判断是否生成红包
		$total_num = $freight_activity_rule_info['total_num'];
		if (!$total_num)
		{
			//当前无活动，直接返回0
			return 0;
		}

		//添加一个订单红包
		$arr = array(
			'freight_activity_rule_id'	=> $freight_activity_rule_info['freight_activity_rule_id'],
			'activity_type'				=> $freight_activity_rule_info['activity_type'],
			'order_id'					=> $order_id,
			'user_id'					=> $user_id,
			'total_num'					=> $freight_activity_rule_info['total_num'],
			'limit_freight'				=> $freight_activity_rule_info['limit_freight'],
			'valid_day_num'				=> $freight_activity_rule_info['coupon_valid_day_num'],
			'deadline'					=> time() + $freight_activity_rule_info['activity_valid_day_num'] *86400,	//活动截止日期，当前时间+活动有效天数的时间戳
		);
		$freight_activity_id = $this->addFreightActivity($arr);
		#echo $this->getLastSql();
		return $freight_activity_id;
	}
}

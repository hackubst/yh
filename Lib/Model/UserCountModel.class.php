<?php
/**
 * 
 * @author zhoutao0928@sina.com  zhoutao@360shop.cc
 * @date 2014-05-06
 * @desc 会员统计 (全部会员) 供acp首页的会员统计用
 */


class UserCountModel extends Model{
	
	public function __construct()
	{
		parent::__construct();
		$this->db(0);
		$this->trueTableName = C('DB_PREFIX').'users';
	}
	
/**
	 * 传入年份，计算该年年初的时间戳并返回
	 * @author 姜伟
	 * @param int $year 年份
	 * @return int $timestamp 该年年初的时间戳
	 * @todo 用mktime方法
	 */
    public function getYearStartTimestamp($year)
    {
		$year = intval($year);
		if (!$year)
		{
			trigger_error(__CLASS__ . ', ' . __FUNCTION__ . ', 年份参数year有误');
		}

		return mktime(0, 0, 0, 1, 1, $year);
    }

	/**
	 * 传入年份和月份，计算该年份下该月月初的时间戳并返回
	 * @author 姜伟
	 * @param int $year 年份
	 * @param int $month 月份
	 * @return int $timestamp 该年份下该月月初的时间戳
	 * @todo 用mktime方法
	 */
    public function getMonthStartTimestamp($year, $month)
    {
		$year = intval($year);
		if (!$year)
		{
			trigger_error(__CLASS__ . ', ' . __FUNCTION__ . ', 年份参数year有误');
		}

		$month = intval($month);
		if (!$month)
		{
			trigger_error(__CLASS__ . ', ' . __FUNCTION__ . ', 年份参数month有误');
		}


		return mktime(0, 0, 0, $month, 1, $year);
    }

	/**
	 * 传入年份和月份，计算该年份下该月月末的时间戳并返回
	 * @author 姜伟
	 * @param int $year 年份
	 * @param int $month 月份
	 * @return int $timestamp 该年份下该月月末的时间戳
	 * @todo 调用getMonthStartTimestamp计算下个月月初的时间戳，减去1秒返回
	 */
    public function getMonthEndTimestamp($year, $month)
    {
		if ($month == 12)
		{
			$year ++;
			$month = 1;
		}
		else
		{
			$month ++;
		}

		$end_time = $this->getMonthStartTimestamp($year, $month) - 1;
		return $end_time;
    } 
	
    
    /**
     * 查询$where子句过滤后的会员总数量
     * @author zt
     * @param string $where where子句，查询条件
     * @return int $user_num
     * @todo 从订单表tp_users中查找满足where条件的元组数量
     */
    public function getUserNumByQueryString($where)
    {
    	$user_info = $this->field('COUNT(*) AS total')->where($where)->find();
    	return $user_info['total'];
    }
    
    /**
     * 根据传入的查询条件查找用户表中的用户数量列表
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $groupby
     * @return array $order_list
     * @todo 根据where子句查询会员表中的会员信息，并以数组形式返回
     */
    public function getUserListByQueryString($fields, $where, $groupby = '', $order = '')
    {
    	return $this->field($fields)->where($where)->group($groupby)->order()->limit()->select();
    }
    
    
    /******************** 会员按日统计 ************************/
    public function getDayTotalUserNum($year,$month,$day)
    {
    	//当天的起始时间
		$start_time = strtotime($year.'-'.$month.'-'.$day);
		//当天的结束时间
		$end_time = $start_time+24*3600-1;	//减去一秒
		
		//调用getUserNumByQueryString获取会员总数
		$day_total_user_num = $this->getUserNumByQueryString('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time);
		
		return $day_total_user_num;
    }
	
    /**
     * 获取某日按小时统计的新会员数量
     * @author zt
     * @param int $year	年。必须
     * @param int $month 月。必须
     * @param int $day 日。必须
     * @return array $hour_stat_data_list
     * @todo 从订单表中统计出当日按小时分组的新会员数量的数据列表，并以数组形式返回
     */
    public function getStatDataGroupByHours($year,$month,$day)
    {
    	//当天的起始时间
    	$start_time = strtotime($year.'-'.$month.'-'.$day);
    	//当天的结束时间
    	$end_time = $start_time+24*3600-1;	//减去一秒
    
    	//调用getUserListByQueryString获取会员总数
    	$fields = 'COUNT(*) AS total, DATE_FORMAT(FROM_UNIXTIME(reg_time), "%H") AS hours';
    	$where = 'reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time;
    	$groupby = 'hours';
    	$hour_stat_data_list = $this->getUserListByQueryString($fields, $where, $groupby);
    	return $hour_stat_data_list;
    }
    
    /************************** 会员按星期统计  *********************************/
    
    /**
     * @todo 根据某个具体的日期来获取当周的新会员数(根据上周统一周天的具体的日期，那么比如今天是星期三，这个日期就是上周三的日期)
     * @param int $year  年。必须
     * @param int $month 月。必须
     * @param int $day 日。必须
     */
    public function getWeekTotalUserNum($year,$month,$day)
    {
    	$w = date('w',strtotime($year.'-'.$month.'-'.$day));	//该日期是当周的第几天  比如 2014-05-06是星期二，此时$w为2，0表示星期日
    
    	$start_day = $day-$w+1;		//当周的起始日期(星期一作为第一天，所以加1)
    	$end_day   = $day-$w+7;		//当周的结束日期(星期日作为最后一天)
    	$start_time = mktime(0, 0, 0, $month, $start_day, $year);	//当周的起始时间戳
    	$end_time	= mktime(23, 59, 59, $month, $end_day, $year);		//当周的结束时间戳
    	//调用getUserNumByQueryString获取会员总数
    	$day_total_order_num = $this->getUserNumByQueryString('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time);
    	#echo date('Y-m-d',$start_time);echo '<br/>';
    	#echo date('Y-m-d',$end_time);echo '<hr/>';
    	return $day_total_order_num;
    
    }
    
    /**
     * @todo 按一周中的七日来统计的新会员列表(根据上周统一周天的具体的日期，比如今天是星期三，那么这个日期就是上周三的日期)
     * @param int $year  年。必须	 比如2014
     * @param int $month 月。必须	 比如05
     * @param int $day 日。必须	 比如06
     */
    public function getStatDateGroupByWeekDay($year,$month,$day)
    {
    	$w = date('w',strtotime($year.'-'.$month.'-'.$day));	//该日期是当周的第几天  比如 2014-05-06是星期二，此时$w为2，0表示星期日
    
    	$start_day = $day-$w+1;		//当周的起始日期(星期一作为第一天，所以加1)
    	$end_day   = $day-$w+7;		//当周的结束日期(星期日作为最后一天)
    	$start_time = mktime(0, 0, 0, $month, $start_day, $year);	//当周的起始时间戳
    	$end_time	= mktime(23, 59, 59, $month, $end_day, $year);		//当周的结束时间戳
    	//调用getUserListByQueryString获取会员总数
    	$fields = 'COUNT(*) AS total, DATE_FORMAT(FROM_UNIXTIME(reg_time), "%w") AS weekday';
    	$where = 'reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time;
    	$groupby = 'weekday';
    	$week_day_stat_data_list = $this->getUserListByQueryString($fields, $where, $groupby);#echo $this->getLastSql();
    	return $week_day_stat_data_list;
    }
    
     /************************** 会员按月统计  *********************************/
    
    /**
     * 获取某月总新进会员数
     * @author zt
     * @param int $year
     * @param int $month
     * @return int $month_total_user_num
     * @todo 从用户表中统计出从该月月初到月末之间的总新进会员数
     */
    public function getMonthTotalUserNum($year, $month)
    {
    	//月初时间戳
    	$start_time = $this->getMonthStartTimestamp($year, $month);
    	//月末时间戳，下一月月初的时间戳减去1秒
    	$end_time = $this->getMonthEndTimestamp($year, $month);
    
    	//调用getUserNumByQueryString获取会员总数
    	$month_total_user_num = $this->getUserNumByQueryString('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time);
    
    	return $month_total_user_num;
    }
    
    /**
     * 获取某年某月中按天统计的会员总数数组列表
     * @author zt
     * @param int $year
     * @param int $month
     * @return array $day_stat_data_list
     * @todo 从订单表中统计出从该月月初到月末之间的按日分组的订单量数据列表，并以数组形式返回
     */
    public function getStatDataGroupByDay($year, $month)
    {
    	//月初时间戳
    	$start_time = $this->getMonthStartTimestamp($year, $month);
    	//月末时间戳，下一月月初的时间戳减去1秒
    	$end_time = $this->getMonthEndTimestamp($year, $month);
    
    	//调用getOrderListByQueryString获取会员总数
    	$fields = 'COUNT(*) AS total, DATE_FORMAT(FROM_UNIXTIME(reg_time), "%d") AS day';
    	$where = 'reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time;
    	$groupby = 'day';
    	$month_stat_data_list = $this->getUserListByQueryString($fields, $where, $groupby);
    	#echo $this->getLastSql();
    
    	return $month_stat_data_list;
    }
	
    
    /************************** 会员按年统计  *********************************/
    /**
     * 获取某年会员总数
     * @author zt
     * @param int $year
     * @return int $year_total_user_num
     * @todo 从订单表中统计出从该年年初到年末之间的总订单量
     */
    public function getYearTotalUserNum($year)
    {
    	//年初时间戳
    	$start_time = $this->getYearStartTimestamp($year);
    	//年末时间戳，下一年年初的时间戳减去1秒
    	$end_time = $this->getYearStartTimestamp(intval($year) + 1) - 1;
    
    	//调用getUserNumByQueryString获取已确认订单总量
    	$year_total_user_num = $this->getUserNumByQueryString('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time);
    	return $year_total_user_num;
    }
    
    /**
     * 获取某年中按月统计的会员总数列表
     * @author zt
     * @param int $year
     * @return array $month_stat_data_list
     */
    public function getStatDataGroupByMonth($year)
    {
    	//年初时间戳
    	$start_time = $this->getYearStartTimestamp($year);
    	//年末时间戳，下一年年初的时间戳减去1秒
    	$end_time = $this->getYearStartTimestamp(intval($year) + 1) - 1;
    
    	//调用getOrderNumByQueryString获取会员总数
    	$fields = 'COUNT(*) AS total, DATE_FORMAT(FROM_UNIXTIME(reg_time), "%m") AS month';
    	$where = 'reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time;
    	$groupby = 'month';
    	$month_stat_data_list = $this->getUserListByQueryString($fields, $where, $groupby);
    
    	return $month_stat_data_list;
    }
}

?>
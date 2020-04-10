<?php
// mcp统计相关操作 
class AcpStatAction extends AcpAction
{
    /**
     * 初始化
     * @author 姜伟
     * @return void
     * @todo 初始化方法
     */
	function _initialize()
	{
        parent::_initialize();
    }

	/**
     * 今日流量明细
     */
    public function today_flow_detail() 
    {
		//今天0点的时间戳
		$today = strtotime(date('Y-m-d', time()));

		//获得今日pv总数
		$pv_main = M('pv_main');
		$pv_list = $pv_main->field('COUNT(*) AS pv_total')->where('add_time >= ' . $today)->order('add_time DESC')->order('add_time DESC')->select();

		//获得今日uv总数
		$uv_list = $pv_main->field('id')->where('add_time >= ' . $today)->group('client_key')->order('add_time DESC')->order('add_time DESC')->select();

		$uv_total = 0;

		//计算uv总数
		foreach ($uv_list AS $key => $val)
		{
			$uv_total += 1;
		}

		//新访客数
		$new_visitor_list = $pv_main->field('COUNT(*) AS new_visitor_num')->where('add_time >= ' . $today . ' AND is_new_visitor = 1')->order('add_time DESC')->order('add_time DESC')->select();

		//IP总数
		$ip_num = $pv_main->field('COUNT(distinct ip) AS ip_num')->where('add_time >= ' . $today)->find();

		$pv_detail = M('view_pv');
		$total = $pv_detail->where('add_time >= ' . $today)->count();  //获取总数
    	/***处理分页开始***/
    	import('ORG.Util.Pagelist');                        // 导入分页类
    	$per_page_num = 20;                 //分页 没页显示条数
    	$Page = new Pagelist($total, $per_page_num);           // 实例化分页类 传入总记录数和每页显示的记录数
    	$page_str = $Page->show();                         //分页输出
		$this->assign('show', $page_str);
    	/***处理分页结束***/

		//今日访问明细
		$pv_detail_list = $pv_detail->field('add_time, user_id, visit_url, page_title, ip, is_new_visitor, terminal_id, browser, os_id, screen_id, language_id')->where('add_time >= ' . $today)->limit($Page->firstRow.','.$Page->listRows)->order('add_time DESC')->select();

		$this->assign('pv_detail_list', $pv_detail_list);
    	$this->assign('page_str', $page_str);
		$this->assign('pv_total', $pv_list[0]['pv_total']);
		$this->assign('uv_total', $uv_total);
		$this->assign('new_visitor_num', $new_visitor_list[0]['new_visitor_num']);
		$this->assign('old_visitor_num', $uv_total - $new_visitor_list[0]['new_visitor_num']);
		$this->assign('ip_num', $ip_num['ip_num']);

		//获取tab参数
		$tab_tag = $this->_get('f');
		$tab_tag = $tab_tag ? $tab_tag : 1;
		$this->assign('tab_tag', $tab_tag);

        //TITLE中的页面标题
  		$this->assign('head_title', '今日流量明细');
  		$this->display();
    }

    /**
     * 历史流量报表-日
     */
    public function history_flow_detail_d() 
    {
		$add_time = $this->_post('add_time');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($add_time)
		{
			$start_time = strtotime(date('Y-m-d', strtotime($add_time)));
			$end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
			$date = date('Y-m-d', strtotime($add_time));
		}
		else
		{
			//今天0点的时间戳
			$end_time = strtotime(date('Y-m-d', time())) + 86400;

			//昨天0点的时间戳
			#$start_time = strtotime(date('Y-m-d', time())) - 86400;
			$start_time = strtotime(date('Y-m-d', time()));
			$date  = date('Y-m-d', $start_time);
		}

		//获得今日分时段流量数据pv
		$pv_main = M('pv_main');
		$pv_list = $pv_main->field('DATE_FORMAT(FROM_UNIXTIME(add_time), "%H") AS hour, COUNT(*) AS pv_num')->where('add_time >= ' . $start_time . ' AND add_time <= ' . $end_time)->group('hour')->order('add_time DESC')->select();

		$new_pv_list = array();
		for ($i = 0; $i <= 24; $i ++)
		{
			$new_pv_list[$i] = 0;
		}

		//组成数组
		foreach ($pv_list AS $key => $val)
		{
			$new_pv_list[intval($val['hour'])] = $val['pv_num'];
		}

		//获得今日分时段流量数据uv
		$uv_list = $pv_main->field('client_key, DATE_FORMAT(FROM_UNIXTIME(add_time), "%H") AS hour')->where('add_time >= ' . $start_time . ' AND add_time <= ' . $end_time)->group('hour, client_key')->order('add_time DESC')->select();

		$new_uv_list = array();
		for ($i = 0; $i <= 24; $i ++)
		{
			$new_uv_list[$i] = 0;
		}

		//组成数组
		foreach ($uv_list AS $key => $val)
		{
			$new_uv_list[intval($val['hour'])] += 1;
		}

		$this->assign('pv_list', $new_pv_list);
		$this->assign('uv_list', $new_uv_list);
		$this->assign('date', $date);
		#echo "<pre>";
		#print_r($new_pv_list);
		#print_r($new_uv_list);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '历史流量日统计报表');
        $this->display();
    }

    /**
     * 历史流量报表-月
     */
    public function history_flow_detail_m() 
    {
		$year = $this->_post('year');
		$month = $this->_post('month');
		$year = $year ? $year : date('Y');
		$month = $month ? $month : date('m');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($year && $month)
		{
			$this->assign('year', $year);
			$this->assign('month', $month);
			$start_time = mktime(0, 0, 0, $month, 1, $year);
			if ($month == 12)
			{
				$year ++;
				$month = 1;
			}
			else
			{
				$month ++;
			}

			$end_time = mktime(0, 0, 0, $month, 1, $year) - 1;
			$date = $year . '-' . date('m');
		}

		//获得今日分时段流量数据pv
		$pv_main = M('pv_main');
		$pv_list = $pv_main->field('DATE_FORMAT(FROM_UNIXTIME(add_time), "%d") AS day, COUNT(*) AS pv_num')->where('add_time >= ' . $start_time . ' AND add_time <= ' . $end_time)->group('day')->order('add_time DESC')->select();

		$new_pv_list = array();
		for ($i = 0; $i <= 30; $i ++)
		{
			$new_pv_list[$i] = 0;
		}

		//组成数组
		foreach ($pv_list AS $key => $val)
		{
			$new_pv_list[intval($val['day'])] = $val['pv_num'];
		}

		//获得今日分时段流量数据uv
		$uv_list = $pv_main->field('client_key, DATE_FORMAT(FROM_UNIXTIME(add_time), "%d") AS day')->where('add_time >= ' . $start_time . ' AND add_time <= ' . $end_time)->group('day, client_key')->order('add_time DESC')->select();

		$new_uv_list = array();
		for ($i = 0; $i <= 30; $i ++)
		{
			$new_uv_list[$i] = 0;
		}

		//组成数组
		foreach ($uv_list AS $key => $val)
		{
			$new_uv_list[intval($val['day'])] += 1;
		}

		$this->assign('pv_list', $new_pv_list);
		$this->assign('uv_list', $new_uv_list);
		$this->assign('date', $date);
		$this->assign('day_num', date('d', mktime(0,0,0,$month + 1,0,$year)));

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '历史流量月统计报表');
        $this->display();
    }

    /**
     * 历史流量报表-年
     */
    public function history_flow_detail_y() 
    {
		$year = $this->_post('year');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($year)
		{
			$start_time = mktime(0, 0, 0, 1, 1, $year);
			$end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
			$date = date('Y-m-d', strtotime($year));
		}
		else
		{
			$year = date('Y');
			$start_time = mktime(0, 0, 0, 1, 1, $year);
			$end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
		}
		$this->assign('year', $year);

		//获得今日分时段流量数据pv
		$pv_main = M('pv_main');
		$pv_list = $pv_main->field('DATE_FORMAT(FROM_UNIXTIME(add_time), "%m") AS month, COUNT(*) AS pv_num')->where('add_time >= ' . $start_time . ' AND add_time <= ' . $end_time)->group('month')->order('add_time DESC')->select();

		$new_pv_list = array();
		for ($i = 0; $i <= 12; $i ++)
		{
			$new_pv_list[$i] = 0;
		}

		//组成数组
		foreach ($pv_list AS $key => $val)
		{
			$new_pv_list[intval($val['month'])] = $val['pv_num'];
		}

		//获得今日分时段流量数据uv
		$uv_list = $pv_main->field('client_key, DATE_FORMAT(FROM_UNIXTIME(add_time), "%m") AS month')->where('add_time >= ' . $start_time . ' AND add_time <= ' . $end_time)->group('month, client_key')->order('add_time DESC')->select();

		$new_uv_list = array();
		for ($i = 0; $i <= 12; $i ++)
		{
			$new_uv_list[$i] = 0;
		}

		//组成数组
		foreach ($uv_list AS $key => $val)
		{
			$new_uv_list[intval($val['month'])] += 1;
		}

		$this->assign('pv_list', $new_pv_list);
		$this->assign('uv_list', $new_uv_list);
		$this->assign('date', $date);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '历史流量年统计报表');
        $this->display();
    }
}

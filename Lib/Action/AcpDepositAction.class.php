<?php
/**
 * 提现类
 */

class AcpDepositAction extends AcpAction
{

    //提现统计类型  1提现按日统计 2提现按月统计 3提现按年统计
    const DEPOSIT_STAT_BY_DAY   = 1;
    const DEPOSIT_STAT_BY_MONTH = 2;
    const DEPOSIT_STAT_BY_YEAR  = 3;

	public function AcpDepositAction()
	{
		parent::_initialize();
	}

	private function get_search_condition()
	{
		//初始化SQL查询的where子句
		$where = '';

		//提现类型
		$deposit_type = $this->_request('deposit_type');
		if (is_numeric($deposit_type) && $deposit_type)
		{
			if ($deposit_type == 1)
			{
				$where .= ' AND role_type = 2';
			}
			elseif ($deposit_type == 2)
			{
				$where .= ' AND role_type = 4 AND deposit_type = 1';
			}
			elseif ($deposit_type == 3)
			{
				$where .= ' AND role_type = 4 AND deposit_type = 2';
			}
		}

		#echo $where;
		//重新赋值到表单
		$this->assign('deposit_type', $deposit_type);

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $deposit_type ? '/deposit_type/' . $deposit_type : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

	/**
	 * 获取提现列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取提现列表，公共方法
     */
	function deposit_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();
		$deposit_obj = new DepositApplyModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $deposit_obj->getDepositApplyNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$deposit_obj->setStart($Page->firstRow);
        $deposit_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$deposit_list = $deposit_obj->getDepositApplyList('', $where, ' addtime DESC');
		$deposit_list = $deposit_obj->getListData($deposit_list);
		$this->assign('deposit_list', $deposit_list);
		#echo "<pre>";
		#print_r($deposit_list);
		#echo "</pre>";
		#echo $deposit_obj->getLastSql();

		//获取提现类型列表
		$this->assign('deposit_type_list', DepositApplyModel::getDepositTypeSelectList());

		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/AcpDeposit/get_deposit_list.html');
	}

	public function get_deposit_list()
	{
		$this->deposit_list('state = 1', '提现列表');
	}

	private function get_apply_search_condition()
	{
		//初始化SQL查询的where子句
		$where = '';

		//状态
		$state = $this->_request('state');
		if (is_numeric($state) && $state != -1)
		{
			$where .= ' AND state = ' . $state;
		}

		//提现类型
		$deposit_type = $this->_request('deposit_type');
		if (is_numeric($deposit_type) && $deposit_type)
		{
			if ($deposit_type == 1)
			{
				$where .= ' AND role_type = 2';
			}
			elseif ($deposit_type == 2)
			{
				$where .= ' AND role_type = 4 AND deposit_type = 1';
			}
			elseif ($deposit_type == 3)
			{
				$where .= ' AND role_type = 4 AND deposit_type = 2';
			}
		}

		//重新赋值到表单
		$this->assign('state', $state);
		$this->assign('deposit_type', $deposit_type);

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= is_numeric($state) ? '/state/' . $state : '';
		$redirect .= is_numeric($deposit_type) ? '/deposit_type/' . $deposit_type : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

	/**
	 * 获取提现列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取提现列表，公共方法
     */
	function deposit_apply_list($where, $head_title, $opt)
	{
		$where .= $this->get_apply_search_condition();
		$deposit_apply_obj = new DepositApplyModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $deposit_apply_obj->getDepositApplyNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$deposit_apply_obj->setStart($Page->firstRow);
        $deposit_apply_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$deposit_apply_list = $deposit_apply_obj->getDepositApplyList('', $where, ' addtime DESC');
		$deposit_apply_list = $deposit_apply_obj->getListData($deposit_apply_list);
		$this->assign('deposit_apply_list', $deposit_apply_list);
		#echo "<pre>";
		#print_r($deposit_apply_list);
		#echo "</pre>";
		#echo $deposit_apply_obj->getLastSql();

		//状态列表
		$this->assign('state_list', DepositApplyModel::getStateList());
		//提现类型列表
		$this->assign('deposit_type_list', DepositApplyModel::getDepositTypeSelectList());

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/AcpDeposit/get_deposit_apply_list.html');
	}

	public function get_deposit_apply_list()
	{
		$this->deposit_apply_list('1', '提现列表');
	}

	/**
	 * 拒绝提现申请
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 拒绝提现申请
	 */
	function refuse()
	{
		$deposit_apply_id = intval($this->_post('deposit_apply_id'));
		$admin_remark = $this->_post('admin_remark');

		if (is_numeric($deposit_apply_id) && $admin_remark)
		{
			$deposit_apply_obj = new DepositApplyModel($deposit_apply_id);
			$arr = array(
				'admin_remark'	=> $admin_remark,
			);
			$deposit_apply_obj->refuseDepositApply();
			$success = $deposit_apply_obj->editDepositApply($arr);

			//推送配送员/云仓，#@#未完成
			/*$push_arr = array(
				'opt'		=> '',
				'push_time'	=> 0,
				'msg'		=> array(
					'opt'		=> 'admin_refund_apply',
					'order_id'	=> $this->orderId,
					'state'		=> $refund_state == MerchantOrderModel::ADMIN_REJECTED ? 2 : 1,	//退款状态，2拒绝，1同意
				),
			);
			$push_obj = new PushModel();
			$push_obj->push($foot_man_id, $push_arr);*/
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 通过/拒绝提现申请
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 通过/拒绝提现申请
	 */
	function set_state()
	{
		$deposit_apply_id = intval($this->_post('deposit_apply_id'));
		$state = $this->_post('state');

		if (is_numeric($deposit_apply_id) && is_numeric($state))
		{
			$deposit_apply_obj = new DepositApplyModel($deposit_apply_id);
			$success = $deposit_apply_obj->where('deposit_apply_id ='.$deposit_apply_id)->save(['state' => 1,'pass_time' => time()]);
//			$success = $deposit_apply_obj->passDepositApply();
			echo $success && $success > 0 ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

    /**
     * 提现按日统计
     */
    public function deposit_stat_by_day()
    {
		$add_time = $this->_post('add_time');
		$start_time = 0;
		$end_time = 0;
		$date = '';

		if ($add_time)
		{
			$start_time = strtotime(date('Y-m-d', strtotime($add_time)));
			#$end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
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

		$where = 'state = 1 AND addtime >= ' . $start_time . ' AND addtime <= ' . $end_time;

		//获取今日提现按日统计数
		$deposit_obj = new DepositApplyModel();
		$deposit_stat_list = $deposit_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%H") AS hour, COUNT(*) AS reg_num, SUM(money) AS sum')->where($where)->group('hour')->order('addtime DESC')->select();

		$new_deposit_stat_list = array();
		for ($i = 0; $i <= 24; $i ++)
		{
			$new_deposit_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['hour'])] = $val['reg_num'];
		}

		$this->assign('uv_list', $new_deposit_stat_list);
		$this->assign('deposit_stat_list', $new_deposit_stat_list);

		$new_deposit_stat_list = array();
		for ($i = 0; $i <= 24; $i ++)
		{
			$new_deposit_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['hour'])] = $val['sum'];
		}

		$this->assign('sum_deposit_stat_list', $new_deposit_stat_list);

		$this->assign('date', $date);
		#echo "<pre>";
		#print_r($new_pv_list);
		#print_r($new_uv_list);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '提现按日统计');
        $this->display();
    }

    /**
     * 提现按月统计
     */
    public function deposit_stat_by_month()
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

		$where = 'state = 1 AND addtime >= ' . $start_time . ' AND addtime <= ' . $end_time;

		//获取今日提现按月统计数
		$deposit_obj = new DepositApplyModel();
		$deposit_stat_list = $deposit_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%d") AS day, COUNT(*) AS reg_num, SUM(money) AS sum')->where($where)->group('day')->order('addtime DESC')->select();
		#echo $deposit_obj->getLastSql();

		$new_deposit_stat_list = array();
		for ($i = 0; $i <= 30; $i ++)
		{
			$new_deposit_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['day'])] = $val['reg_num'];
		}

		$this->assign('deposit_stat_list', $new_deposit_stat_list);

		$new_deposit_stat_list = array();
		for ($i = 0; $i <= 30; $i ++)
		{
			$new_deposit_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['day'])] = $val['sum'];
		}

		$this->assign('sum_deposit_stat_list', $new_deposit_stat_list);

		$this->assign('date', $date);
		$this->assign('day_num', date('d', mktime(0,0,0,$month + 1,0,$year)));

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '提现按月统计');
        $this->display();
    }

    /**
     * 提现按年统计
     */
    public function deposit_stat_by_year()
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

		$where = 'state = 1 AND addtime >= ' . $start_time . ' AND addtime <= ' . $end_time;

		//获取今日提现按年统计数
		$deposit_obj = new DepositApplyModel();
		$deposit_stat_list = $deposit_obj->field('DATE_FORMAT(FROM_UNIXTIME(addtime), "%m") AS month, COUNT(*) AS reg_num, SUM(money) AS sum')->where($where)->group('month')->order('addtime DESC')->select();

		$new_deposit_stat_list = array();
		for ($i = 0; $i <= 12; $i ++)
		{
			$new_deposit_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['month'])] = $val['reg_num'];
		}

		$this->assign('deposit_stat_list', $new_deposit_stat_list);

		$new_deposit_stat_list = array();
		for ($i = 0; $i <= 12; $i ++)
		{
			$new_deposit_stat_list[$i] = 0;
		}

		//组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['month'])] = floatval($val['sum']);
		}

		$this->assign('sum_deposit_stat_list', $new_deposit_stat_list);

		$this->assign('date', $date);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '提现按年统计');
        $this->display();
    }

    /**
     * 导出提现申请数据
     * @author 姜伟
     * @return void
     * @todo 从account表导出数据，支持选定代理商或起始时间
     */
    public function export_deposit_apply() {

		$action		= $this->_request('action');
		$role_type	= $this->_request('role_type');
		$start_time	= $this->_request('start_time', '');
		$end_time	= $this->_request('end_time', '');
        if($start_time)
        {
            $start_time = str_replace('+', ' ', $start_time);
			$start_time = strtotime($start_time);
        }
        if($end_time)
        {
            $end_time = str_replace('+', ' ', $end_time);
			$end_time = strtotime($end_time);
        }

		$this->assign('start_time', $start_time);
		$this->assign('end_time', $end_time);
		$where = '1';

		if ($role_type)
		{
			$where .= ' AND role_type =' .$role_type;
		}
		//通过时间区间筛选数据
		if($start_time)
		{
			$where .= ' AND addtime >= ' . $start_time;
		}
		if($end_time)
		{
			$where .= ' AND addtime <= ' . $end_time;
		}

		$start_time = date('Y-m-d H:i:s', $start_time);
		$end_time = date('Y-m-d H:i:s', $end_time);
        if($action == 'export')
        {
			//获取总条数
			$deposit_apply    = new DepositApplyModel();

            //获取数据
            $arr     = $deposit_apply->where($where)->order('addtime')->select();

            //引入PHPExcel类库
            vendor('Excel.PHPExcel');
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->getProperties()->setCreator("Da")
                    ->setLastModifiedBy("Da")
                    ->setTitle("Office 2007 XLSX Test Document")
                    ->setSubject("Office 2007 XLSX Test Document")
                    ->setDescription("Test document for Office 2007 XLSX,generated using PHP classes.")
                    ->setKeywords("office 2007 openxml php")
                    ->setCategory("Test result file");

            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet(0)->setTitle('提现申请信息列表');          //标题
            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);      //单元格宽度
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial'); //设置字体
            $objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);      //设置字体大小
            $objPHPExcel->getActiveSheet(0)->setCellValue('A1', '用户名称');
            $objPHPExcel->getActiveSheet(0)->setCellValue('B1', '手机号码');
            $objPHPExcel->getActiveSheet(0)->setCellValue('C1', '支付宝账户');
            $objPHPExcel->getActiveSheet(0)->setCellValue('D1', '支付宝账户户名');
            $objPHPExcel->getActiveSheet(0)->setCellValue('E1', '提现金额');
            $objPHPExcel->getActiveSheet(0)->setCellValue('F1', '提交时间');
            $objPHPExcel->getActiveSheet(0)->setCellValue('G1', '管理员留言');
            $objPHPExcel->getActiveSheet(0)->setCellValue('H1', '状态');

			$arr = $deposit_apply->getListData($arr, true);
			#echo "<pre>";
			#print_r($arr);
			#die;

            //每行数据的内容
            foreach ($arr as  $value) {
                static $i  = 2;

                //用户名称
                $objPHPExcel->getActiveSheet(0)->setCellValue('A' . $i, $value['username']);
                $objPHPExcel->getActiveSheet(0)->setCellValue('B' . $i, $value['mobile']);
                //提现账号
				$objPHPExcel->getActiveSheet(0)->setCellValue('C' . $i, $v['alipay_account']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('D' . $i, $value['alipay_account_name']);
                //提现金额
                $objPHPExcel->getActiveSheet(0)->setCellValue('E' . $i, $value['money']);
                //提交时间
                $objPHPExcel->getActiveSheet(0)->setCellValue('F' . $i, date('Y-m-d H:m:s', $value['addtime']));
                //管理员留言
                $objPHPExcel->getActiveSheet(0)->setCellValue('G' . $i, $value['admin_remark']);
				//状态
                $objPHPExcel->getActiveSheet(0)->setCellValue('H' . $i, $value['state_name']);

                $i++;
            }

            //直接输出文件到浏览器下载
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="提现申请信息列表_' . date("YmdHis") . '.xls"');
            header('Cache-Control: max-age=0');
            ob_clean(); //关键
            flush(); //关键
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
        }

        if($start_time == 'end_time')
		{
            $start_time = '';
		}

        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
		$this->assign('head_title', '导出提现申请信息');

        $this->display();
    }


    //提现统计
    public function deposit_stat()
    {   $year       = $this->_post('year');
        $year       = $year? $year:date('Y');
		$year2      = $this->_post('year2');
        $year2      = $year2? $year2:date('Y');
		$month      = $this->_post('month');
        $month      = $month? $month:date('m');
		$date_type  = intval($this->_post('date_type'));
		$date_type  = $date_type ? $date_type : 1;
		$add_time   = $this->_post('add_time');
		$where      = 'state = 1';
		$start_time = 0;
		$end_time   = 0;
        $format     = '"%H"';
        $count      = 0;
		$date       = '';
		$this->assign('year', date('Y'));
		$this->assign('month', date('m', strtotime('-1 month')));
		$this->assign('year2', date('Y'));
		$this->assign('add_time', date('Y-m-d', strtotime('-1 month')));

        $count = 31;

        $new_deposit_stat_list = array();
		for ($i = 1; $i <= $count; $i++)
		{
			$new_deposit_stat_list[$i] = 0;
			$sum_deposit_stat_list[$i] = 0;
		}

        //组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['time'])] = $val['reg_num'];
			$sum_deposit_stat_list[intval($val['time'])] = $val['sum'];
		}

		$this->assign('new_deposit_stat_list', $new_deposit_stat_list);
		$this->assign('sum_deposit_stat_list', $sum_deposit_stat_list);
		$this->assign('date_type', $date_type);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '提现统计');
        $this->display(APP_PATH . '/Tpl/AcpDeposit/deposit_stat.html');
    }

    //提现统计
    public function user_stat()
    {   $year       = $this->_post('year');
        $year       = $year? $year:date('Y');
		$year2      = $this->_post('year2');
        $year2      = $year2? $year2:date('Y');
		$month      = $this->_post('month');
        $month      = $month? $month:date('m');
		$date_type  = intval($this->_post('date_type'));
		$date_type  = $date_type ? $date_type : 1;
		$add_time   = $this->_post('add_time');
		$where      = 'state = 1';
		$start_time = 0;
		$end_time   = 0;
        $format     = '"%H"';
        $count      = 0;
		$date       = '';
		$this->assign('year', date('Y'));
		$this->assign('month', date('m', strtotime('-1 month')));
		$this->assign('year2', date('Y'));
		$this->assign('add_time', date('Y-m-d', strtotime('-1 month')));

        $count = 31;

        $new_deposit_stat_list = array();
		for ($i = 1; $i <= $count; $i++)
		{
			$new_deposit_stat_list[$i] = 0;
			$sum_deposit_stat_list[$i] = 0;
		}

        //组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['time'])] = $val['reg_num'];
			$sum_deposit_stat_list[intval($val['time'])] = $val['sum'];
		}

		$this->assign('new_deposit_stat_list', $new_deposit_stat_list);
		$this->assign('sum_deposit_stat_list', $sum_deposit_stat_list);
		$this->assign('date_type', $date_type);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '注册统计');
        $this->display(APP_PATH . '/Tpl/AcpDeposit/user_stat.html');
    }

    //提现统计
    public function charge_stat()
    {   $year       = $this->_post('year');
        $year       = $year? $year:date('Y');
		$year2      = $this->_post('year2');
        $year2      = $year2? $year2:date('Y');
		$month      = $this->_post('month');
        $month      = $month? $month:date('m');
		$date_type  = intval($this->_post('date_type'));
		$date_type  = $date_type ? $date_type : 1;
		$add_time   = $this->_post('add_time');
		$where      = 'state = 1';
		$start_time = 0;
		$end_time   = 0;
        $format     = '"%H"';
        $count      = 0;
		$date       = '';
		$this->assign('year', date('Y'));
		$this->assign('month', date('m', strtotime('-1 month')));
		$this->assign('year2', date('Y'));
		$this->assign('add_time', date('Y-m-d', strtotime('-1 month')));

        $count = 31;

        $new_deposit_stat_list = array();
		for ($i = 1; $i <= $count; $i++)
		{
			$new_deposit_stat_list[$i] = 0;
			$sum_deposit_stat_list[$i] = 0;
		}

        //组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['time'])] = $val['reg_num'];
			$sum_deposit_stat_list[intval($val['time'])] = $val['sum'];
		}

		$this->assign('new_deposit_stat_list', $new_deposit_stat_list);
		$this->assign('sum_deposit_stat_list', $sum_deposit_stat_list);
		$this->assign('date_type', $date_type);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '充值统计');
        $this->display(APP_PATH . '/Tpl/AcpDeposit/charge_stat.html');
    }

    //提现统计
    public function profit_stat()
    {   $year       = $this->_post('year');
        $year       = $year? $year:date('Y');
		$year2      = $this->_post('year2');
        $year2      = $year2? $year2:date('Y');
		$month      = $this->_post('month');
        $month      = $month? $month:date('m');
		$date_type  = intval($this->_post('date_type'));
		$date_type  = $date_type ? $date_type : 1;
		$add_time   = $this->_post('add_time');
		$where      = 'state = 1';
		$start_time = 0;
		$end_time   = 0;
        $format     = '"%H"';
        $count      = 0;
		$date       = '';
		$this->assign('year', date('Y'));
		$this->assign('month', date('m', strtotime('-1 month')));
		$this->assign('year2', date('Y'));
		$this->assign('add_time', date('Y-m-d', strtotime('-1 month')));

        $count = 31;

        $new_deposit_stat_list = array();
		for ($i = 1; $i <= $count; $i++)
		{
			$new_deposit_stat_list[$i] = 0;
			$sum_deposit_stat_list[$i] = 0;
		}

        //组成数组
		foreach ($deposit_stat_list AS $key => $val)
		{
			$new_deposit_stat_list[intval($val['time'])] = $val['reg_num'];
			$sum_deposit_stat_list[intval($val['time'])] = $val['sum'];
		}

		$this->assign('new_deposit_stat_list', $new_deposit_stat_list);
		$this->assign('sum_deposit_stat_list', $sum_deposit_stat_list);
		$this->assign('date_type', $date_type);

        //TITLE中的页面标题
		$this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '利润统计');
        $this->display(APP_PATH . '/Tpl/AcpDeposit/profit_stat.html');
    }

	/**
	 * 通过提现申请，企业打款
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 通过提现申请，企业打款
	 */
	function qiye_pay()
	{
		$deposit_apply_id = intval($this->_post('deposit_apply_id'));
		if (is_numeric($deposit_apply_id))
		{
			//获取打款信息
			$deposit_apply_obj = new DepositApplyModel($deposit_apply_id);
			$info = $deposit_apply_obj->getDepositApplyInfo('deposit_apply_id = ' . $deposit_apply_id, 'user_id, money');
			$user_id = $info['user_id'];

			//实际打款金额
			$real_money = DepositApplyModel::calRealMoney($info['money']);

			//打款
			$wxpay_obj = new WXPayModel();
			$result = $wxpay_obj->qiye_pay($user_id, $real_money);

			//通过提现申请
			if ($result['result_code'] == 'FAIL')
			{
				$this->ajaxReturn(array('msg'=>$result['err_code_des'], 'code'=>0));
				//$this->json_exit($result['err_code_des'], -1);
			}
			else
			{
				$success = $deposit_apply_obj->passDepositApply();
				$this->ajaxReturn(array('msg'=>'打款成功', 'code'=>1));
			}
		}
		$this->ajaxReturn(array('msg'=>'提现信息不存在', 'code'=>0));
		//$this->json_exit('提现信息不存在', -1);
	}
}
?>

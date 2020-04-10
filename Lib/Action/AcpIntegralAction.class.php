<?php

class AcpIntegralAction extends AcpAction
{
	/**
     * 初始化
     * @author 姜伟
     * @return void
     * @todo 初始化方法
     */
	public function _initialize()
	{
        parent::_initialize();
	}

	private function get_integral_search_condition()
	{
		//初始化SQL查询的where子句
		$where = '';

		//加盟商名称
		$nickname = $this->_request('nickname');
		if ($nickname)
		{
			$user_obj = new UserModel();
			$user_ids = $user_obj->getUserIdByNickname($nickname);
			$user_ids = $user_ids ? $user_ids : 0;
			#echo $user_obj->getLastSql();
			#var_dump($user_ids);die;
			$where .= ' AND user_id IN (' . $user_ids . ')';
		}

		//加盟商名称
		$realname = $this->_request('realname');
		if ($realname)
		{
			$user_obj = new UserModel();
			$user_ids = $user_obj->getUserIdByRealname($realname);
			$user_ids = $user_ids ? $user_ids : 0;
			#echo $user_obj->getLastSql();
			#var_dump($user_ids);die;
			$where .= ' AND user_id IN (' . $user_ids . ')';
		}

		//加盟商名称
		$mobile = $this->_request('mobile');
		if ($mobile)
		{
			$user_obj = new UserModel();
			$user_ids = $user_obj->getUserIdByMobile($mobile);
			$user_ids = $user_ids ? $user_ids : 0;
			#echo $user_obj->getLastSql();
			#var_dump($user_ids);die;
			$where .= ' AND user_id IN (' . $user_ids . ')';
		}

		//变动类型
		$change_type = $this->_request('change_type');
		if (is_numeric($change_type) && $change_type > 0)
		{
			$where .= ' AND change_type = ' . intval($change_type);
		}

		/*提交时间begin*/
		//起始时间
		$start_time = $this->_request('start_time');
		$start_time = str_replace('+', ' ', $start_time);
		$start_time = strtotime($start_time);
		#echo $start_time;
		if ($start_time)
		{
			$where .= ' AND addtime >= ' . $start_time;
		}

		//结束时间
		$end_time = $this->_request('end_time');
		$end_time = str_replace('+', ' ', $end_time);
		$end_time = strtotime($end_time);
		if ($end_time)
		{
			$where .= ' AND addtime <= ' . $end_time;
		}
		/*提交时间end*/
		#echo $where;
		//重新赋值到表单
		$this->assign('realname', $realname);
		$this->assign('change_type', $change_type);
		$this->assign('start_time', $start_time ? $start_time : '');
		$this->assign('end_time', $end_time ? $end_time : '');

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $change_type ? '/change_type/' . $change_type : '';
		$redirect .= $start_time ? '/start_time/' . $start_time : '';
		$redirect .= $end_time ? '/end_time/' . $end_time : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}
	
	/**
	 * 获取用户积分明细列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取用户积分明细列表，公共方法
     */
	function integral_detail_list($where, $head_title, $opt)
	{
		$where .= $this->get_integral_search_condition();
		$integral_obj = new IntegralModel(); 

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $integral_obj->getIntegralNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$integral_obj->setStart($Page->firstRow);
        $integral_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$integral_list = $integral_obj->getIntegralList('', $where, ' addtime DESC');
		$integral_list = $integral_obj->getListData($integral_list);
		$this->assign('integral_list', $integral_list);

		// echo "<pre>";
		// print_r($integral_list);
		// echo "</pre>";
		// echo $integral_obj->getLastSql();

		//变动类型列表
		$this->assign('change_type_list', IntegralModel::getChangeTypeList());
		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/AcpIntegral/get_user_integral_detail.html');
	}

	public function get_user_integral_detail()
	{
        $user_id = intval(I('user_id'));
        $where = $user_id ? 'user_id = ' . $user_id : '1 = 1';
		$this->integral_detail_list($where, '会员积分明细');
	}
	
	/**
	 * 获取用户积分列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取用户积分列表，公共方法
     */
	function integral_list($where, $head_title, $opt)
	{
		$where .= $this->get_integral_search_condition();
		$user_obj = new UserModel(); 

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $user_obj->getUserNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$user_obj->setStart($Page->firstRow);
        $user_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$integral_list = $user_obj->getUserList('', $where, ' user_id DESC');
		// $integral_list = $user_obj->getListData($integral_list);
		$this->assign('integral_list', $integral_list);

		// echo "<pre>";
		// print_r($integral_list);
		// echo "</pre>";
		// echo $user_obj->getLastSql();

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/AcpIntegral/get_user_integral_list.html');
	}

	public function get_user_integral_list()
	{
		$this->integral_list('role_type = 3', C('USER_NAME') . '积分列表');
	}

	public function get_integral_recharge_list()
	{
		$this->integral_detail_list('user_type = 2 AND change_type = 1', '商家积分充值列表');
	}

	/**
     * 积分变动管理
     * @author cc
     * @param void
     * @return void
     * @todo 积分变动管理
     */
    //获取一级分类
	function get_level_one() {
		$class_obj = new IntegralChangeClassModel();
		$where = '';
		//数据总量
		$total = $class_obj->getClassNum($where);

		//处理分页
		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$class_obj->setStart($Page->firstRow);
    $class_obj->setLimit($Page->listRows);

		$page_str = $Page->show();
		$this->assign('page_str',$page_str);

		$class_list = $class_obj->getClassList();
    
		$this->assign('question_class_list', $class_list);
		$this->display();

  	}

  	function get_level_two() {
		$sort_obj = new IntegralChangeSortModel();
    $class_obj = new IntegralChangeClassModel();

		//数据总量
		$where = '';
		$total = $sort_obj->getSortNum($where);

		//处理分页
		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$sort_obj->setStart($Page->firstRow);
    $sort_obj->setLimit($Page->listRows);

		$page_str = $Page->show();
		$this->assign('page_str',$page_str);

		$class_list = $sort_obj->getSortList();

    foreach ($class_list as $key => $item) {
      $class_list[$key]['class_name'] = $class_obj->getClassField($item['class_id'],'class_name');
    }

		$this->assign('sort_list', $class_list);
		$this->display();

  	}

  	//批量删除一级分类
  	function batch_delete_level_one () {
  
		$question_ids = $this->_post('question_class_ids');

        if ($question_ids) {
            $question_id_ary = explode(',', $question_ids);
            $success_num = 0;
            $question_obj = new IntegralChangeClassModel($question_id);
            $sort_obj = new IntegralChangeSortModel();
            foreach ($question_id_ary AS $question_id)
            {
                $num      = $sort_obj->getSortNum('class_id = '. $question_id);
                if ($num) continue;
                $success_num += $question_obj->delClass();
            }
            echo $success_num ? 'success' : 'failure';
            exit;
        }

		exit('failure');
  }

  	//批量删除二级分类
  	function batch_delete_level_two () {
  
		$question_ids = $this->_post('question_class_ids');

		if ($question_ids) {
			$question_id_ary = explode(',', $question_ids);
			$success_num = 0;
      $question_obj = new IntegralChangeSortModel();
			foreach ($question_id_ary AS $question_id)
			{
				$success_num += $question_obj->delSort($question_id);
			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
  }
	//添加一级分类
	function add_level_one() {

		$act = $this->_post('act');
		if ($act == 'add') {
			$_post = $this->_post();
			$class_name	= $_post['class_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
			
			//表单验证
			if (!$class_name) {
				$this->error('请填写分类名称！');
			}

			if (!ctype_digit($serial)) {
				$this->error('请填写排序号！');
			}

			if (!ctype_digit($isuse)) {
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'class_name'	=> $class_name,
				'serial'		=> $serial,
				'isuse'			=> $isuse,
			);

			$class_obj = new IntegralChangeClassModel();
			$success = $class_obj->addClass($arr);

			if ($success) {
				$this->success('恭喜您，分类添加成功！', '/AcpIntegral/add_level_one/mod_id/' . $this->mod_id);
			} else {
				$this->error('抱歉，问题添加失败！', '/AcpIntegral/add_level_one/mod_id/' . $this->mod_id);
			}
		}

		$this->assign('head_title', '添加一级分类');
		$this->display();
	}


  	//添加二级分类
  	function add_level_two () {
    $level_one_obj  = new IntegralChangeClassModel();
    $level_one_list = $level_one_obj->getClassList();

		$act = $this->_post('act');
		if ($act == 'add') {
			$_post = $this->_post();
			$class_name	= $_post['class_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
      $class_id = $_post['class_id'];
			
			//表单验证
			if (!$class_name) {
				$this->error('请填写分类名称！');
			}

			if (!ctype_digit($serial)) {
				$this->error('请填写排序号！');
			}

			if (!ctype_digit($isuse)) {
				$this->error('请选择是否有效！');
			}

      if (!ctype_digit($class_id)) {
				$this->error('请选择一级分类！');
      } 

			$arr = array(
        'sort_name'	  => $class_name,
				'serial'		  => $serial,
				'isuse'			  => $isuse,
        'class_id'    => $class_id,
			);

			$class_obj = new IntegralChangeSortModel();
			$success   = $class_obj->addSort($arr);
      $url       = '/AcpIntegral/add_level_two/mod_id/' . $this->mod_id;

			if ($success) {
				$this->success('恭喜您，分类添加成功！', $url);
			} else {
				$this->error('抱歉，分类添加失败！', $url);
			}
		}
    
		$this->assign('level_one_list', $level_one_list);
		$this->assign('head_title', '添加二级分类');
		$this->display();
  }
        
  	//删除一级分类
	public function delete_level_one() {
		$class_id = intval($this->_post('class_id'));
		if ($class_id) {
			$question_class_obj = new IntegralChangeClassModel($class_id);
            $question_sort_obj = new IntegralChangeSortModel();
            $num = $question_sort_obj->where('class_id = ' . $class_id)->count();
            if ($num) exit('failure');
			$success = $question_class_obj->delClass();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

  	//删除二级分类
	public function delete_level_two() {
		$class_id = intval($this->_post('sort_id'));
		if ($class_id) {
			$question_class_obj = new IntegralChangeSortModel();
			$success = $question_class_obj->delSort($class_id);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//修改一级分类
	function edit_level_one () {
		$redirect = U('/AcpIntegral/get_level_one/mod_id/' . $this->mod_id);
		$class_id = intval($this->_get('class_id'));
		if (!$class_id) {
			$this->error('对不起，非法访问！', $redirect);
		}

		$class_obj = new IntegralChangeClassModel();
		$class_info = $class_obj->getClass($class_id);

		if (!$class_info) {
			$this->error('对不起，不存在相关分类！', $redirect);
		}

		$act = $this->_post('act');

		if($act == 'edit') {
			$_post = $this->_post();
			$class_name	= $_post['class_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
			
			//表单验证
			if(!$class_name) {
				$this->error('请填写分类名称！');
			}

			if(!ctype_digit($serial)) {
				$this->error('请填写排序号！');
			}

			if(!ctype_digit($isuse)) {
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'class_name'	=> $class_name,
				'serial'		=> $serial,
				'isuse'			=> $isuse,
			);

			$class_obj = new IntegralChangeClassModel($class_id);
      $url = '/AcpIntegral/edit_level_one/class_id/' . $class_id . '/mod_id/' . $this->mod_id;

			if ($class_obj->setClass($class_id,$arr)) {
				$this->success('恭喜您，分类修改成功！', $url) ;
			} else {
				$this->error('抱歉，分类修改失败！', $url);
			}

		}

		$this->assign('class_info', $class_info);
		$this->assign('head_title', '修改一级分类');
		$this->display();
	}

	//修改二级分类
	function edit_level_two () {
		$redirect = U('/AcpIntegral/get_level_two/mod_id/' . $this->mod_id);
		$sort_id = intval($this->_get('sort_id'));

    $level_one_obj  = new IntegralChangeClassModel();
    $level_one_list = $level_one_obj->getClassList();

		if (!$sort_id) {
			$this->error('对不起，非法访问！', $redirect);
		}

		$sort_obj = new IntegralChangeSortModel();
		$class_info = $sort_obj->getSort($sort_id);

		if (!$class_info) {
			$this->error('对不起，不存在相关分类！', $redirect);
		}
// dump($class_info);die;
    $class_info['class_name'] = $level_one_obj->getClassField($class_info['class_id'],'class_name');

		$act = $this->_post('act');

		if($act == 'edit') {
			$_post = $this->_post();
			$class_name	= $_post['sort_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
      $class_id = $_post['class_id'];
			
			//表单验证
			if (!$class_name) {
				$this->error('请填写分类名称！');
			}

			if (!ctype_digit($serial)) {
				$this->error('请填写排序号！');
			}

			if (!ctype_digit($isuse)) {
				$this->error('请选择是否有效！');
			}

      if (!ctype_digit($class_id)) {
				$this->error('请选择一级分类！');
      } 

			$arr = array(
        'sort_name'	  => $class_name,
				'serial'		  => $serial,
				'isuse'			  => $isuse,
        'class_id'    => $class_id,
			);

      $url = '/AcpIntegral/edit_level_two/sort_id/' . $sort_id . '/mod_id/' . $this->mod_id;

			if ($sort_obj->setSort($sort_id,$arr)) {
				$this->success('恭喜您，分类修改成功！', $url) ;
			} else {
				$this->error('抱歉，分类修改失败！', $url);
			}

		}

		$this->assign('level_one_list', $level_one_list);
		$this->assign('class_info', $class_info);
    $this->assign('head_title', '修改二级分类');
		$this->display();
	}

	/**
     * 调整积分
     * @author cc
     * @param void
     * @return void
     * @todo 调整积分
     */
    public function get_user_integral_edit()
    {
        $action    = $this->_post('action');
        $redirect  = $this->_get('redirect');
        $redirect  = ($redirect)?url_jiemi($redirect):'/AcpIntegral/get_user_integral_detail';

        $user_id   = intval(I('user_id'));
        $user_obj  = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('username, user_id','user_id = ' . $user_id);

        if (!$user_info)
        {
            $this->error('无效的用户号', $redirect);
        }
        $this->assign('user_info', $user_info);
                        
        if($action == 'edit')            //提交动作
        {
            /* post提交 begin */
            $data      = array();
            $type      = intval($this->_post('type'));
            $integral  = $this->_post('integral');
            $class_id  = $this->_post('class_id');
            $sort_id   = $this->_post('sort_id');
            /* post提交 end */
            
            /* 数据验证 begin */
            if(!$integral || !is_numeric($integral))
            {
                $this->error('对不起，变动数量必须为数字！');
            }
            if(!$class_id)
            {
                $this->error('对不起，请选择一级变动类型！');
            }
            if(!$sort_id)
            {
                $this->error('对不起，请选择二级变动类型！');
            }
            /* 数据验证 end */

            /* 写入数据库 begin */
            if ($type == 0) {
            	$integral = -1 * $integral;
            }

            $integral_obj = new IntegralModel();
            $type_list    = IntegralModel::getChangeTypeList();
            $remark       = '后台添加' . $type_list[$sort_id];

            if ($integral_obj->addIntegral($user_id, $sort_id, $integral, $remark)) {
                $this->success(C('AGENT_NAME') . '添加成功！', $redirect);

            }else{
                $this->error(C('AGENT_NAME') . '添加失败！');           //错误信息

            }
            /* 写入数据库 end */
        }

        //获取
        $class_list = D('IntegralChangeClass')->getClassList();
        $this->assign('class_list', $class_list);
        foreach ($class_list as $key => $value) {
            $selectOneVal[$value['class_name']] = $value['integral_change_class_id'];
        }
        $this->assign('selectOneVal', json_encode($selectOneVal));

        $this->assign('head_title','添加' . C('AGENT_NAME'));
        $this->display();
    }

    /**
	 * 获取城市列表
	 * class_id	省份ID
	 */
	function get_sort_list($class_id) {
		$sort_list = D('integralChangeSort')->field('integral_change_sort_id,sort_name')->where('class_id = ' . $class_id)->select();
		// dump($sort_list);echo D('integralChangeSort')->getLastSql();
		foreach ($sort_list as $key => $value) {
        	$selectTwoVal[$value['sort_name']] = $value['integral_change_sort_id'];
        }
		echo json_encode($selectTwoVal);
		exit;
	}

	private function get_search_condition()
	{
		//是否显示物流公司选择
		$show_express_company_status = (ACTION_NAME == 'get_pre_refund_order_list' || ACTION_NAME == 'get_pre_exchange_order_list') ? false : true;

		//是否显示处理状态
		$show_handling_status = (ACTION_NAME == 'get_refunded_order_list' || ACTION_NAME == 'get_exchanged_order_list') ? true : false;

		//初始化SQL查询的where子句
		$where = '1';

		//订单编号
		$order_sn = $this->_request('order_sn');
		if ($order_sn)
		{
			$where .= ' AND exchange_record_sn = "' . $order_sn . '"';
		}

		//订单状态
		$order_status = $this->_request('order_status');
		$order_status = ($order_status == '' || $order_status == -1) ? -1 : intval($order_status);
		if ($order_status != -1)
		{
			$where .= ' AND order_status = ' . $order_status;
		}

		//订单退换货处理状态
		$state = $this->_request('apply_state');
		$state = ($state == '') ? -1 : $state;
		$state = intval($state);
		if ( $state >= 0 && $show_handling_status )
		{
			$where .= ' AND state = ' . intval($state);
		}

		//物流公司
		$express_company_id = $this->_request('express_company_id');
		$express_company_id = intval($express_company_id);
		if ($express_company_id && $show_express_company_status)
		{
			$where .= ' AND express_company_id = ' . $express_company_id;
		}

		//商品名称
		$item_name = $this->_request('item_name');
		if ($item_name)
		{
			$order_item_obj = new OrderItemModel();
			$order_ids = $order_item_obj->getOrderIdByItemName($item_name);
			$order_ids = $order_ids ? $order_ids : 0;
			#echo $order_item_obj->getLastSql();
			#var_dump($order_ids);die;
			$where .= ' AND tp_order.order_id IN (' . $order_ids . ')';
		}

		//收货人
		$consignee = $this->_request('consignee');
		if ($consignee)
		{
			$where .= ' AND consignee LIKE "%' . $consignee . '%"';
		}

		/*订单添加时间begin*/
		//起始时间
		$start_time = $this->_request('start_time');
		$start_time = str_replace('+', ' ', $start_time);
		$start_time = strtotime($start_time);
		#echo $start_time;
		if ($start_time)
		{
			$where .= ' AND addtime >= ' . $start_time;
		}

		//结束时间
		$end_time = $this->_request('end_time');
		$end_time = str_replace('+', ' ', $end_time);
		$end_time = strtotime($end_time);
		if ($end_time)
		{
			$where .= ' AND addtime <= ' . $end_time;
		}
		/*订单添加时间end*/
		#echo $where;
		//重新赋值到表单
		$this->assign('order_sn', $order_sn ? $order_sn : '');
		$this->assign('state', $state);
		$this->assign('express_company_id', $express_company_id ? $express_company_id : '');
		$this->assign('item_name', $item_name);
		$this->assign('consignee', $consignee);
		$this->assign('start_time', $start_time ? $start_time : '');
		$this->assign('end_time', $end_time ? $end_time : '');
		$this->assign('show_handling_status', $show_handling_status);
		$this->assign('show_express_company_status', $show_express_company_status);
		$this->assign('order_status', $order_status);

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $order_sn ? '/order_sn/' . $order_sn : '';
		$redirect .= $item_name ? '/item_name/' . $item_name : '';
		$redirect .= $consignee ? '/consignee/' . $consignee : '';
		$redirect .= $start_time ? '/start_time/' . $start_time : '';
		$redirect .= $end_time ? '/end_time/' . $end_time : '';
		$redirect .= $order_status ? '/order_status/' . $order_status : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

    /**
	 * 获取订单信息
	 * @author wsq
	 */
    public function get_order_list(){
		$where    .= $this->get_search_condition();
		$order_obj = new IntegralExchangeRecordModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $order_obj->getOrderNum($where);
        $Page  = new Pagelist($count,C('PER_PAGE_NUM'));
		$order_obj->setStart($Page->firstRow);
        $order_obj->setLimit($Page->listRows);
        $show  = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$order_list = $order_obj->getRecordList('', $where, ' addtime DESC');
		$order_list = $order_obj->getListData($order_list);

		$this->assign('order_list', $order_list);
		#echo "<pre>";
		#print_r($order_list);
		#echo "</pre>";
		#echo $order_obj->getLastSql();

		//获取物流公司列表
		$shipping_company_obj  = new ShippingCompanyModel();
		$shipping_company_list = $shipping_company_obj->getShippingCompanyList();
		$this->assign('express_company_list', $shipping_company_list);

		$this->assign('opt', $opt);
		$this->assign('head_title', "礼品兑换信息");
        $this->display();
    
    }

	/**
	 * 查看订单详情
	 * @author wsq
	 * @param void
	 * @return void
	 * @todo 从地址栏获取订单ID，调用获取订单模型的getOrderInfo方法获取订单信息
	 */
	public function order_detail()
	{
		//接收订单ID，验证订单ID有效性
		$redirect = $this->_get('redirect');
		$redirect = $redirect ? url_jiemi($redirect) : U('/AcpOrder/get_pre_pay_order_list');
		$order_id = $this->_get('order_id');
		$order_id = intval($order_id);

		//根据$redirect获取$action_name，并将当前标记的菜单编号改为$action_name对应的菜单编号

		if (!$order_id)
		{
			$this->error('订单号不存在', $redirect);
		}

		//调用订单模型的getOrderInfo获取订单信息
		$order_obj = new IntegralExchangeRecordModel($order_id);
		#echo $order_obj->list_fields;

		try
		{
			$order_info = $order_obj->getOrderInfo('');
		}
		catch (Exception $e)
		{
			$this->error('无效的订单号', $redirect);
		}
		#echo "<pre>";
		#print_r($order_info);
		#echo "</pre>";

		//调用订单商品模型获取订单商品列表
		$order_item_obj = new IntegralExchangeRecordItemModel($order_id);
		$item_list = $order_item_obj->getOrderItemList();

		//遍历商品，获取商品的SKU，计算单品促销后的价格
		foreach ($item_list AS $key => $item_info)
		{
			//获取商品SKU
			if ($item_info['item_sku_price_id'] && !$item_info['property'])
			{
				//调用商品SKU模型获取SKU规格
				$item_sku_obj = new ItemSkuModel($item_info['item_sku_price_id']);
				$item_sku_info = $item_sku_obj->getSkuInfo($item_info['item_sku_price_id'], 'property_value1, property_value2');
				$property = '';
				if ($item_sku_info)
				{
                    $PropValue   = D('PropertyValue');
                    $prop_value1 = $PropValue->getPropertyValueField($item_sku_info['property_value1'], 'property_value');
                    $prop_value2 = $PropValue->getPropertyValueField($item_sku_info['property_value2'], 'property_value');
					$property .= $prop_value1 ? $prop_value1 : '   ';
					$property .= $prop_value2 ? '，' . $prop_value2 : '';
				}
				$item_list[$key]['property'] = $property;
			}
			else
			{
				$item_list[$key]['property'] = '';
			}
		}

        # wsq added
        # 获取加盟商资料
		$fields    = 'username, realname, mobile, email, qq';
		$user_obj  = new UserModel($order_info['user_id']);
		$user_info = $user_obj->getUserInfo($fields);

        $this->assign('user_info', $user_info);

        # 获取收货地址
        $address_obj    = new UserAddressModel();
        $address_info   = $address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id']);
        $address_detail = $address_obj->getAddressDetail($address_info);

        $this->assign('user_address_info', $address_info);
        $this->assign('address_detail',    $address_detail);

        # wsq added 2015-05-01
        # 获取物流信息
        $express_obj  = new ShippingCompanyModel();
        $express_info = $express_obj->getShippingCompanyInfoById($order_info['express_company_id']);
        $this->assign('express_company_name', $express_info['shipping_company_name']);

		//将订单状态转化成文字
		$order_info['order_status_num'] = $order_info['order_status'];
		$order_info['order_status'] = $order_obj->convertOrderStatus($order_info['order_status']);
		$this->assign('order_info', $order_info);
		$this->assign('item_list', $item_list);
		$this->assign('order_type', $order_type);

		//获取订单状态变化明细
		$order_log_list = $order_obj->getOrderLogList();
		$this->assign('order_log_list', $order_log_list);

		//获取礼品信息
#		$gift_list = $order_obj->getGiftList();
#		$this->assign('gift_list', $gift_list);
		#echo "<pre>";
		#print_r($gift_list);
		#echo "</pre>";

		$this->assign('head_title', '查看订单详情');
		$this->display();
	}

    // 兑换订单发货
    public function deliver_order() {
        // body...
		$order_id = $this->_post('order_id');
		$order_id = intval($order_id);

		if ($order_id)
		{
			$order_obj = new IntegralExchangeRecordModel($order_id);
			$order_obj->deliverOrder();
			echo 'success';
			exit;
		}
		echo 'failure';
		exit;
    }

}

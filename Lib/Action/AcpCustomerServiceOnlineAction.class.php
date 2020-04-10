<?php
/**
 * acp后台客服类
 */
class AcpCustomerServiceOnlineAction extends AcpAction
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
		$this->assign('ITEM_NAME', C('ITEM_NAME'));
    }

	/**
	 * 接收搜索表单数据，组织返回where子句
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
	 */
	function get_search_condition()
	{
		//初始化查询条件
		$where = '';

		//客服名称
		$customer_service_online_name = $this->_request('customer_service_online_name');
		if ($customer_service_online_name)
		{
			$where .= ' AND customer_service_online_name LIKE "%' . $customer_service_online_name . '%"';
		}

		//客服类型
		$service_type = $this->_request('service_type');
		if (ctype_digit($service_type))
		{
			$where .= ' AND service_type = ' . $service_type;
		}

		//售前/售后
		$is_after_service = $this->_request('is_after_service');
		if (ctype_digit($is_after_service))
		{
			$where .= ' AND is_after_service = ' . $is_after_service;
		}

		//产品ID
		$item_id = $this->_request('item_id');
		if (ctype_digit($item_id))
		{
			$where .= ' AND item_id = ' . $item_id;
		}

		//添加时间范围起始时间
		$start_date = $this->_request('start_date');
		$start_date = str_replace('+', ' ', $start_date);
		$start_date = strtotime($start_date);
		if ($start_date)
		{
			$where .= ' AND addtime >= ' . $start_date;
		}

		//添加时间范围结束时间
		$end_date = $this->_request('end_date');
		$end_date = str_replace('+', ' ', $end_date);
		$end_date = strtotime($end_date);
		if ($end_date)
		{
			$where .= ' AND addtime <= ' . $end_date;
		}

		//重新赋值到表单
		$this->assign('customer_service_online_name', $customer_service_online_name);
		$this->assign('service_type', $service_type);
		$this->assign('item_id', $item_id);
		$this->assign('is_after_service', $is_after_service);
		$this->assign('start_date', $start_date ? $start_date : '');
		$this->assign('end_date', $end_date ? $end_date : '');

		return $where;
	}

	/**
	 * 获取客服列表，公共方法
	 * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt
	 * @return void
	 * @todo 获取客服列表，公共方法
	 */
	function customer_service_online_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();
		$customer_service_online_obj = new CustomerServiceOnlineModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $customer_service_online_obj->getCustomerServiceOnlineNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$customer_service_online_obj->setStart($Page->firstRow);
        $customer_service_online_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$customer_service_online_list = $customer_service_online_obj->getCustomerServiceOnlineList('', $where, ' addtime DESC');
		$customer_service_online_list = $customer_service_online_obj->getListData($customer_service_online_list);
		#echo $customer_service_online_obj->getLastSql();

		$this->assign('customer_service_online_list', $customer_service_online_list);
		#echo "<pre>";
		#print_r($customer_service_online_list);
		#echo "</pre>";
		#echo $customer_service_online_obj->getLastSql();

		$this->assign('head_title', $head_title);
		$this->assign('opt', $opt);
		$this->display(APP_PATH . 'Tpl/AcpCustomerServiceOnline/get_customer_service_online_list.html');
	}

	/**
	 * 上架的客服列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取上架的客服列表
	 */
	function get_onsale_customer_service_online_list()
	{
		$this->customer_service_online_list('isuse = 1', '上架的' . C('ITEM_NAME') . '列表', 'onsale');
	}

	/**
	 * 下架的客服列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取下架的客服列表
	 */
	function get_store_customer_service_online_list()
	{
		$this->customer_service_online_list('isuse = 0', '下架的' . C('ITEM_NAME') . '列表', 'store');
	}

	/**
     * 添加客服
     * @author 姜伟
     * @return void
     * @todo 上传新客服
     */
	public function add_customer_service_online()
	{
        $this->assign('head_title', '添加客服');
        $act = I('post.act');

        // 添加客服
		if ($act == 'add')
		{
			/*** 接收并验证表单数据begin ***/
			//客服昵称
			$customer_service_online_name = $this->_post('customer_service_online_name');
			$this->assign('customer_service_online_name', $customer_service_online_name);
			$service_type = $this->_post('service_type');
			$is_after_service = $this->_post('is_after_service');
			$this->assign('is_after_service', $is_after_service);
			$isuse = $this->_post('isuse');
			$this->assign('isuse', $isuse);
			$item_id = $this->_post('item_id');
			$this->assign('item_id', $item_id);
			$account = $this->_post('account');
			$this->assign('account', $account);

			if (!$customer_service_online_name)
			{
				$this->error('对不起，请填写客服昵称！');
			}

			if (!ctype_digit($service_type))
			{
				$this->error('对不起，请选择客服类型！');
			}

			/*if (!ctype_digit($item_id))
			{
				$this->error('对不起，请选择产品！');
			}
*/
			if (!ctype_digit($is_after_service))
			{
				$this->error('对不起，请选择售前/售后！');
			}

			if (!ctype_digit($isuse))
			{
				$this->error('对不起，请选择是否上架！');
			}

			if (!$account)
			{
				$this->error('对不起，请填写客服账号！');
			}

			$arr = array(
				'customer_service_online_name'	=> $customer_service_online_name,
				'service_type'					=> $service_type,
				'isuse'							=> $isuse,
				'is_after_service'				=> $is_after_service,
				'item_id'						=> $item_id,
				'account'						=> $account,
				'addtime'						=> time(),
			);

			$customer_service_online_obj = new CustomerServiceOnlineModel();
			$success = $customer_service_online_obj->addCustomerServiceOnline($arr);
			$opt = $isuse ? 'onsale' : 'store';
			$redirect = U('/AcpCustomerServiceOnline/get_' . $opt . '_customer_service_online_list');
			if ($success)
			{
				$this->success('恭喜，添加成功！', $redirect);
			}
			else
			{
				$this->error('对不起，添加失败！');
			}
			/*** 接收并验证表单数据end ***/
        }

		//获取客服类型列表
		$this->assign('service_type_list', CustomerServiceOnlineModel::get_service_type_list());
		
		//获取产品列表
		$this->assign('item_list', CustomerServiceOnlineModel::get_item_list());

		//售前/售后
		$this->assign('is_after_service_list', CustomerServiceOnlineModel::get_is_after_service_list());

        $this->display();
	}
		
	/**
     * 修改客服
     * @author 姜伟
     * @return void
     * @todo 修改客服
     */
	public function edit_customer_service_online()
	{
		$redirect = U('/AcpCustomerServiceOnline/get_onsale_customer_service_online_list');
		$customer_service_online_id = intval($this->_get('customer_service_online_id'));
		if (!$customer_service_online_id)
		{
			$this->error('对不起，非法访问！', $redirect);
		}
		$customer_service_online_obj = new CustomerServiceOnlineModel($customer_service_online_id);
		$customer_service_online_info = $customer_service_online_obj->getCustomerServiceOnlineInfo('customer_service_online_id = ' . $customer_service_online_id);
		if (!$customer_service_online_info)
		{
			$this->error('对不起，不存在相关客服！', $redirect);
		}

		foreach ($customer_service_online_info AS $k => $v)
		{
			if ($k == 'service_type' || $k == 'item_id' || $k == 'is_after_service')
			{
				$v = intval($v);
			}
			$this->assign($k, $v);
		}

		$act = $this->_post('act');
        // 修改客服
		if ($act == 'edit')
		{
			/*** 接收并验证表单数据begin ***/
			//客服昵称
			$customer_service_online_name = $this->_post('customer_service_online_name');
			$this->assign('customer_service_online_name', $customer_service_online_name);
			$service_type = $this->_post('service_type');
			$is_after_service = $this->_post('is_after_service');
			$this->assign('is_after_service', $is_after_service);
			$isuse = $this->_post('isuse');
			$this->assign('isuse', $isuse);
			$item_id = $this->_post('item_id');
			$this->assign('item_id', $item_id);
			$account = $this->_post('account');
			$this->assign('account', $account);

			if (!$customer_service_online_name)
			{
				$this->error('对不起，请填写客服昵称！');
			}

			if (!ctype_digit($service_type))
			{
				$this->error('对不起，请选择客服类型！');
			}

			/*if (!ctype_digit($item_id))
			{
				$this->error('对不起，请选择产品！');
			}*/

			if (!ctype_digit($is_after_service))
			{
				$this->error('对不起，请选择售前/售后！');
			}

			if (!ctype_digit($isuse))
			{
				$this->error('对不起，请选择是否上架！');
			}

			if (!$account)
			{
				$this->error('对不起，请填写客服账号！');
			}

			$arr = array(
				'customer_service_online_name'	=> $customer_service_online_name,
				'service_type'					=> $service_type,
				'isuse'							=> $isuse,
				'is_after_service'				=> $is_after_service,
				'item_id'						=> $item_id,
				'account'						=> $account,
				'addtime'						=> time(),
			);

			$customer_service_online_obj = new CustomerServiceOnlineModel($customer_service_online_id);
			//将客服基本信息保存到数据库
			if ($customer_service_online_obj->editCustomerServiceOnline($arr))
			{
				$this->success('恭喜您，客服编辑成功！');
			}
			else
			{
				$this->error('对不起，客服编辑失败！');
			}
        }

		//获取客服类型列表
		$this->assign('service_type_list', CustomerServiceOnlineModel::get_service_type_list());
		
		//获取产品列表
		$this->assign('item_list', CustomerServiceOnlineModel::get_item_list());

		//售前/售后
		$this->assign('is_after_service_list', CustomerServiceOnlineModel::get_is_after_service_list());

        $this->assign('head_title', '修改客服');
        $this->display();
	}

	/**
	 * 客服上下架
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 
	 */
	function set_customer_service_online_isuse()
	{
		$customer_service_online_id = intval($this->_post('customer_service_online_id'));
		$isuse = $this->_post('isuse');
		if ($customer_service_online_id && ctype_digit($isuse))
		{
			$customer_service_online_obj = new CustomerServiceOnlineModel($customer_service_online_id);
			$arr = array(
				'isuse'	=> $isuse
			);
			$success = $customer_service_online_obj->editCustomerServiceOnline($arr);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 客服批量上下架
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 
	 */
	function batch_set_customer_service_online_isuse()
	{
		$customer_service_online_ids = $this->_post('customer_service_online_ids');
		$isuse = $this->_post('isuse');
		if ($customer_service_online_ids && ctype_digit($isuse))
		{
			$customer_service_online_id_ary = explode(',', $customer_service_online_ids);
			$success_num = 0;
			foreach ($customer_service_online_id_ary AS $customer_service_online_id)
			{
				$customer_service_online_obj = new CustomerServiceOnlineModel($customer_service_online_id);
				$arr = array(
					'isuse'	=> $isuse
				);
				$success = $customer_service_online_obj->editCustomerServiceOnline($arr);
				$success_num += $success ? 1 : 0;
			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}
}

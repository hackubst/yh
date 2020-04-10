<?php 
class FrontPlanterAction extends FrontAction{
	private $item_num_per_my_plant_page;
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_my_plant_page = $GLOBALS['config_info']['ITEM_NUM_PER_MY_PLANT_PAGE'];
		require_cache('Common/func_item.php');
	}

	//查询条件
	private function get_search_condition()
	{
		//植物状态，1进行中，2已完成
		$state = $this->_request('state');
		$state = $state == 2 ? 2 : 1;
		$where .= $state == 1 ? ' AND is_reap = 0' : ' AND is_reap = 1';

		$this->assign('state', $state);
		return $where;
	}

	//潘朵拉BOX首页
	public function pandorabox()
	{
		//判断是否已有种植机
		$planter_id = intval(session('planter_id'));
		$url = '';

		if ($planter_id)
		{
			//已有种植机，继续判断当前是否存在进行中的植物
			$planter_seed_obj = new PlanterSeedModel();
			$planting_seed_num = $planter_seed_obj->getPlanterSeedNum('is_reap = 0 AND planter_id = ' . $planter_id);

			if ($planting_seed_num)
			{
				//获取当前模拟的planter_seed_id
				$planter_log_obj = new PlanterLogModel();
				$planter_log_info = $planter_log_obj->getPlanterLogInfo('planter_id = ' . $planter_id, 'planter_seed_id', 'addtime DESC');
				if (!$planter_log_info)
				{
					//不存在日志，以不存在正在进行的植物处理
					$url = '/FrontPlanter/choose_plant';
				}
				else
				{
					//存在进行中的植物，跳转到主控台
					$url = '/FrontControl/main_home/planter_seed_id/' . $planter_log_info['planter_seed_id'];
				}
			}
			else
			{
				//不存在正在进行的植物
				$url = '/FrontPlanter/choose_plant';
			}
		}
		else
		{
			//未绑定种植机，跳转到种植机向导页面
			$url = '/FrontHelp/welcome';
		}

		redirect($url);
	}

	//种植机向导页
	public function guide()
	{
		$this->assign('head_title', '潘朵拉BOX');
		$this->display();
	}

	//首次绑定
	public function first_bind()
	{
		$this->assign('head_title', '首次绑定');
		$this->display();
	}

	//绑定机器
	public function bind_machine()
	{
		$act = $this->_post('act');
		if ($act == 'bind')
		{
			$serial_num = $this->_post('serial_num');
			$this->assign('serial_num', $serial_num);
			$planter_obj = new PlanterModel();
			$planter_info = $planter_obj->getPlanterInfo('serial_num = "' . $serial_num .  '"', 'planter_id, bind_time, user_id, last_visit_time');
			if (!$planter_info)
			{
				$this->alert('对不起，无效的序列号！');
			}

			if ($planter_info['user_id'])
			{
				$this->alert('对不起，该序列号已使用！');
			}

			if (!$planter_info['last_visit_time'])
			{
				$this->alert('对不起，请先将机器连接wifi！');
			}
			//绑定
			$planter_obj = new PlanterModel($planter_info['planter_id']);
			$arr = array(
				'bind_time'	=> time(),
				'user_id'	=> intval(session('user_id')),
			);
			$success1 = $planter_obj->editPlanter($arr);

			//将该机器设为默认机器
			$arr = array(
				'current_planter_id'	=> $planter_info['planter_id']
			);
			$user_obj = new UserModel(intval(session('user_id')));
			$user_obj->setUserInfo($arr);
			$success2 = $user_obj->saveUserInfo();

			//设置session-planter_id
			session('current_planter_id', $planter_info['planter_id']);

			if ($success1 && $success2)
			{
				$redirect = U('/FrontPlanter/choose_plant');
				$this->alert('恭喜您，绑定成功！', $redirect);
			}
			else
			{
				$this->alert('抱歉，系统错误，绑定失败，请稍后再试或联系系统管理员！');
			}
		}
		$this->assign('head_title', '绑定机器');
		$this->display();
	}

	//连接wifi
	public function connect_wifi()
	{
		$this->redirect(U('/Index/connect_machine'));
		$this->assign('head_title', '连接wifi');
		$this->display();
	}

	//连接种植机
	public function connect_machine()
	{
		//获取用户基本资料
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('nickname');
		$this->assign('user_info', $user_info);

		//获取种植机列表
		$planter_obj = new PlanterModel();
		$planter_list = $planter_obj->getPlanterList('planter_id, planter_name', 'user_id = ' . $user_id, 'bind_time DESC');
		$planter_auth_obj = new PlanterAuthModel();
		$planter_auth_list = $planter_auth_obj->getPlanterAuthList('p.planter_id, p.planter_name', 'tp_planter_auth.user_id = ' . $user_id);
		array_merge($planter_auth_list, $planter_list);
		$len = count($planter_list);
		foreach ($planter_auth_list AS $k => $v)
		{
			$planter_list[$k + $len] = $v;
		}

		$this->assign('planter_list', $planter_list);
		#echo "<pre>";
		#echo $planter_auth_obj->getLastSql();
		#print_r($planter_list);
		#print_r($planter_auth_list);
		#die;
		$this->assign('current_planter_id', intval(session('planter_id')));
		$this->assign('head_title', '连接种植机');
		$this->display();
	}

	//选择植物
	public function choose_plant()
	{
		/*** 获取商品列表begin ***/
		$class_id = $this->_get('class_id');
		if (!$class_id)
		{
			//获取种子分类ID
			$class_obj = new ClassModel();
			$class_info = $class_obj->getClassInfo('class_tag = "seed"', 'class_id');
			$class_id = $class_info ? $class_info['class_id'] : 0;
		}

		//种子列表
		$item_obj = new ItemModel();
		$seed_list = $item_obj->getItemListGroupBySort($class_id, 100);
		$this->assign('item_list', $seed_list);
		/*** 获取商品列表end ***/
		$this->assign('head_title', '选择植物');
		$this->display();
	}

	//我种植的植物
	public function my_plant()
	{
		//进行中的植物列表
		$planter_id = intval(session('planter_id'));
		if (!$planter_id)
		{
			$redirect = U('/FrontMall/mall_home/class_tag/planter');
			$this->alert('对不起，您当前没有绑定种植机，将为您导航到商城！', $redirect);
		}
		$where = 'planter_id = ' . $planter_id;
		$where .= $this->get_search_condition();
		$planter_seed_obj = new PlanterSeedModel();
		//总数
		$total = $planter_seed_obj->getPlanterSeedNum($where);
		$planter_seed_obj->setStart(0);
        $planter_seed_obj->setLimit($this->item_num_per_my_plant_page);
		$planter_seed_list = $planter_seed_obj->getPlanterSeedList('planter_seed_id, item_id, state, plant_time, item_name, base_pic', $where, 'plant_time DESC');
		foreach ($planter_seed_list AS $k => $v)
		{
			$planter_seed_list[$k]['state'] = SeedStateModel::convertState($v['state']);
			$planter_seed_list[$k]['base_pic'] = middle_img($v['base_pic']);
		}
		$this->assign('planter_seed_list', $planter_seed_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_my_plant_page);

		//进行中植物数量
		$going_num = $planter_seed_obj->getPlanterSeedNum('planter_id = ' . $planter_id . ' AND is_reap = 0');
		$this->assign('going_num', $going_num);

		//已完成植物数量
		$complete_num = $planter_seed_obj->getPlanterSeedNum('planter_id = ' . $planter_id . ' AND is_reap = 1');
		$this->assign('complete_num', $complete_num);

		$this->assign('head_title', '我种植的植物');
		$this->display();
	}
	
	//我种植的植物状态详情
	public function my_plant_detail()
	{
		//获取当前种植机ID
		$user_id = intval(session('user_id'));
		$planter_id = session('planter_id');
		if (!$planter_id)
		{
			$redirect = U('/FrontMall/mall_home/class_tag/planter');
			$this->alert('对不起，您当前没有绑定种植机，将为您导航到商城！', $redirect);
		}

		//种植机种子信息和种子id
		$planter_seed_id = $this->_get('planter_seed_id');
		$planter_seed_obj = new PlanterSeedModel();
		$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_seed_id = ' . $planter_seed_id . ' AND planter_id = ' . $planter_id, 'seed_id, state');
		if (!$planter_seed_info)
		{
			$redirect = U('/FrontPlanter/my_plant');
			$this->alert('对不起，您当前没有选择植物，将为您导航到我的植物！', $redirect);
		}

		$this->assign('planter_seed_id', $planter_seed_id);
		$this->assign('default_state', $planter_seed_info['state']);
		$seed_id = $planter_seed_info['seed_id'];

		//获取种子信息
		$item_obj = new ItemModel();
		$item_info = $item_obj->getItemInfo('item_id = ' . $seed_id, 'item_id, item_name');
		if (!$item_info)
		{
			$redirect = U('/FrontMall/mall_home/class_tag/seed');
			$this->alert('对不起，您当前没有选择种子，将为您导航到商城！', $redirect);
		}
		$this->assign('item_info', $item_info);

		//获取种植机种子日志
		$planter_log_obj = new PlanterLogModel();
		$planter_log_list = $planter_log_obj->getPlanterLogList('end_seed_state, addtime', 'planter_seed_id = ' . $planter_seed_id, 'start_seed_state ASC');
		foreach ($planter_log_list AS $k => $v)
		{
			$planter_log_list[$k]['state'] = SeedStateModel::convertState($v['end_seed_state']);
		}
		$this->assign('planter_log_list', $planter_log_list);
		#echo $planter_log_obj->getLastSql();
		#echo "<pre>";
		#print_r($planter_log_list);
		#die;

		$this->assign('head_title', $item_info['item_name'] . '的生长过程');
		$this->display();
	}

	//切换种植机
	public function change_machine()
	{
		$planter_id = $this->_post('planter_id');
		$user_id = intval(session('user_id'));

		if ($user_id && ctype_digit($planter_id))
		{
			$user_obj = new UserModel($user_id);
			$success = $user_obj->changeCurrentPlanter($planter_id);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//修改种植机名称
	public function set_planter_name()
	{
		$planter_id = $this->_post('planter_id');
		$planter_name = $this->_post('planter_name');

		if ($planter_name && ctype_digit($planter_id))
		{
			$user_id = intval(session('user_id'));
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id . ' AND user_id = ' . $user_id, 'planter_id');
			if (!$planter_info)
			{
				exit('failure');
			}

			$arr = array(
				'planter_name'	=> $planter_name
			);
			$success = $planter_obj->editPlanter($arr);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//异步获取植物列表
	public function get_seed_list()
	{
		$firstRow = I('post.firstRow');
		$planter_id = intval(session('planter_id'));
		$where = 'planter_id = ' . $planter_id;
		$where .= $this->get_search_condition();
		$planter_seed_obj = new PlanterSeedModel();
		//总数
		$total = $planter_seed_obj->getPlanterSeedNum($where);

		if ($firstRow <= ($total - 1) && $planter_id)
		{
			$planter_seed_obj->setStart($firstRow);
			$planter_seed_obj->setLimit($this->item_num_per_my_plant_page);
			//获取植物列表
			$planter_seed_list = $planter_seed_obj->getPlanterSeedList('planter_seed_id, item_id, state, plant_time, item_name, base_pic', $where, 'plant_time DESC');
			foreach ($planter_seed_list AS $k => $v)
			{
				$planter_seed_list[$k]['state'] = SeedStateModel::convertState($v['state']);
				$planter_seed_list[$k]['plant_time'] = date('Y-m-d H:i:s', $v['plant_time']);
				$planter_seed_list[$k]['base_pic'] = middle_img($v['base_pic']);
			}
			echo json_encode($planter_seed_list);
			exit;
		}

		exit('failure');
	}
}

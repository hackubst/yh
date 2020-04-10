<?php 
class FrontControlAction extends FrontAction{
	function _initialize() 
	{
		parent::_initialize();
	}

	//获取公共数据
	private function get_common_data($serial)
	{
		//获取当前种植机ID
		$user_id = intval(session('user_id'));
		$planter_id = session('planter_id');
$planter_id = $user_id == 1 ? 2001 : $planter_id;
		if (!$planter_id)
		{
			$redirect = U('/FrontMall/mall_list/class_tag/planter');
			$this->alert('对不起，您当前没有绑定种植机，将为您导航到商城！', $redirect);
		}

		//获取当前种植机信息
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id);
		//展会临时方案，光照强度随机在800-1000之间，温度随机在20-30之间
		#$planter_info['outside_temperature'] = rand(200,300);
		#$planter_info['illuminance'] = rand(8000,10000);
		#$planter_info['outside_temperature'] = ($planter_info['outside_temperature'] > 1000 && $planter_info['outside_temperature'] < 10000) ? $planter_info['outside_temperature'] / 100 : $planter_info['outside_temperature'] / 10;
		$this->assign('planter_info', $planter_info);
		#echo $planter_obj->getLastSql();
		#echo "<pre>";
		#print_r($planter_info);
		#die;

		$command = array();
		$len = strlen($planter_info['box_state']);
		for ($i = 0; $i < $len; $i++)
		{
			$command[$i] = $planter_info['box_state'][$i];
		}

		$this->assign('command', $command);
		$this->assign('ton', $planter_info['ton']);
		$this->assign('toff', $planter_info['toff']);

		$seed_id = 0;
		if ($serial == 1)
		{
			//植物信息和植物状态信息
			$seed_id = $this->_get('seed_id');
		
			//当前种植机内是否存在正在种植的植物
			$planter_seed_obj = new PlanterSeedModel();
			$num = $planter_seed_obj->getPlanterSeedNum('is_reap = 0 AND planter_id = ' . $planter_id);
			$this->assign('planter_seed_num', $num);
			#echo "<pre>";
			#print_r($planter_info);
			#print_r($seed_state_list);
			#die;
		}
		elseif ($serial == 2)
		{
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
		}
		elseif ($serial == 3)
		{
			//种植机种子信息和种子id
			$planter_seed_id = $this->_get('planter_seed_id');
			$planter_seed_obj = new PlanterSeedModel();
			$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_seed_id = ' . $planter_seed_id . ' AND planter_id = ' . $planter_id, 'seed_id');
			if (!$planter_seed_info)
			{
				$redirect = U('/FrontPlanter/my_plant');
				$this->alert('对不起，您当前没有选择植物，将为您导航到我的植物！', $redirect);
			}
			$this->assign('planter_seed_id', $planter_seed_id);
			$seed_id = $planter_seed_info['seed_id'];

			//验证数据有效性，is_diy=1去种植机种子状态关联表中去状态值，is_diy=0去种子状态表取状态值
			$seed_state_id = $this->_get('seed_state_id');
			$is_diy = $this->_get('is_diy');
			if (!ctype_digit($is_diy) || !ctype_digit($seed_state_id))
			{
				$redirect = U('/FrontMall/mall_home');
				$this->alert('抱歉，系统错误，将为您导航到商城！', $redirect);
			}

			$state_info = array();
			$planter_seed_state_obj = new PlanterSeedStateModel();
			$state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('planter_seed_id = ' . $planter_seed_id, 'outside_temperature, humidity, illuminance_limit, state');
			if (!$state_info)
			{
				$seed_state_obj = new SeedStateModel();
				$state_info = $seed_state_obj->getSeedStateInfo('seed_state_id = ' . $seed_state_id, 'outside_temperature, humidity, illuminance_limit, state');
			}

			if (empty($state_info))
			{
				$redirect = U('/FrontMall/mall_list/class_tag/planter');
				$this->alert('抱歉，系统错误，将为您导航到商城！', $redirect);
			}

			$this->assign('state_info', $state_info);
			#echo "<pre>";
			#print_r($state_info);
			#die;
			$this->assign('is_diy', $is_diy);
			$this->assign('seed_state_id', $seed_state_id);
		}

		if ($serial != 4)
		{
			//获取种子信息
			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemInfo('item_id = ' . $seed_id, 'item_id, item_name');
			if (!$item_info)
			{
				$redirect = U('/FrontMall/mall_list/class_tag/seed');
				$this->alert('对不起，您当前没有选择种子，将为您导航到商城！', $redirect);
			}
			$this->assign('item_info', $item_info);

			//种子状态信息
			$seed_state_obj = new SeedStateModel();
			$seed_state_list = $seed_state_obj->getSeedStateList('seed_state_id, state, outside_temperature, humidity, illuminance_limit, img_path', 'seed_id = ' . $seed_id, 'state ASC');
			if ($serial == 3)
			{
				$user_id = intval(session('user_id'));
				$planter_seed_state_obj = new PlanterSeedStateModel();
				foreach ($seed_state_list AS $k => $v)
				{
					$planter_seed_state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('user_id = ' . $user_id . ' AND seed_id = ' . $item_info['item_id'] . ' AND state = ' . $v['state'], 'outside_temperature, humidity, illuminance_limit');
					if ($planter_seed_state_info)
					{
						foreach ($planter_seed_state_info AS $key => $val)
						{
							$seed_state_list[$k][$key] = $val;
						}
					}
				}
			}
			$this->assign('seed_state_list', $seed_state_list);
			#echo "<pre>";
			#print_r($planter_info);
			#print_r($seed_state_list);
			#die;
		}
	}

	public function select()
	{
		$this->get_common_data(1);
		$this->assign('head_title', '选择状态页');
		$this->display();
	}

	public function main_jhq()
	{
		$this->get_common_data(4);
		//获取城市名称
		$area_info = getIPSource(get_client_ip());
		$city = $area_info['city_name'];
		$city = substr_utf8($city, 1, strlen_utf8($city) - 1, false) == '市' ? substr_utf8($city, strlen_utf8($city) - 1, 0, false) : $city;
		//获取天气信息
		$weather_obj = new WeatherModel();
		$weather_info = $weather_obj->getWeatherInfo($city);
		$this->assign('weather_info', $weather_info);
		#echo "<pre>";
		#print_r($weather_info);
		#print_r($aqi_info);
		#die;
		$this->assign('head_title', '净化器');
		$this->display();
	}

	public function main_select()
	{
		$this->get_common_data(1);
		$this->assign('head_title', '选择状态页');
		$this->display();
	}
	
	public function main_home()
	{
		$this->get_common_data(2);
		$this->assign('head_title', '植物状态页');
		$this->display();
	}
	
	public function main_diy()
	{
		$this->get_common_data(3);
		$this->assign('head_title', '修改状态页');
		$this->display();
	}

	//发送命令
	public function send_command()
	{
		$serial = $this->_post('btn_serial');
		$command = $this->_post('command');
		$planter_id = $this->_post('planter_id');

		if (ctype_digit($serial) && ctype_digit($command) && ctype_digit($planter_id))
		{
			$user_id = intval(session('user_id'));
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
			if (!$planter_info || !$planter_obj->checkPriv())
			{
				exit('failure');
			}

			$success = $planter_obj->sendCommand($serial, $command);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//设置状态
	public function set_state()
	{
		$state = $this->_post('state');
		$seed_id = $this->_post('seed_id');
		$planter_seed_id = $this->_post('planter_seed_id');
		$planter_id = $this->_post('planter_id');

		if (ctype_digit($state) && ctype_digit($planter_seed_id) && ctype_digit($seed_id) && ctype_digit($planter_id))
		{
			$user_id = intval(session('user_id'));
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
			if (!$planter_info || !$planter_obj->checkPriv())
			{
				exit('failure');
			}

			//获取该种子当前状态的种子状态ID
			$seed_state_obj = new SeedStateModel();
			$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_id = ' . $seed_id . ' AND state = ' . $state, 'seed_state_id');
			if (!$seed_state_info)
			{
				exit('failure');
			}

			//修改种植机表的模拟状态
			$arr = array(
				'state'			=> $state,
				'seed_state_id'	=> $seed_state_info['seed_state_id']
			);
			$success1 = $planter_obj->editPlanter($arr);

			//修改种植机种子表的状态
			$planter_seed_obj = new PlanterSeedModel();
			$success2 = $planter_seed_obj->setState($planter_id, $planter_seed_id, $state);

			echo $success2 ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//种植新种子
	public function plant_new_seed()
	{
		$planter_id = $this->_post('planter_id');
		$seed_id = $this->_post('seed_id');
		$seed_state_id = $this->_post('seed_state_id');
		$state = $this->_post('state');

		if (ctype_digit($state) && ctype_digit($seed_state_id) && ctype_digit($seed_id) && ctype_digit($planter_id))
		{
			$user_id = intval(session('user_id'));
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
			if (!$planter_info || !$planter_obj->checkPriv())
			{
				exit('failure');
			}

			//将该种子加入到种植机种子关联表中
			$arr = array(
				'planter_id'	=> $planter_id,
				'seed_id'		=> $seed_id,
				'state'			=> $state,
				'plant_time'	=> time(),
			);
			$planter_seed_obj = new PlanterSeedModel();
			$success1 = $planter_seed_obj->addPlanterSeed($arr);

			//修改种植机表的模拟状态
			$arr = array(
				'state'			=> $state,
				'seed_state_id'	=> $seed_state_id
			);
			$planter_obj->editPlanter($arr);

			echo $success1 ? $success1 : 'failure';
			exit;
		}

		exit('failure');
	}

	//保存diy修改状态的结果
	public function save_state()
	{
		$tem = $this->_post('tem');
		$dam = $this->_post('dam');
		$light = $this->_post('light');
		$seed_state_id = $this->_post('seed_state_id');
		$planter_id = $this->_post('planter_id');
		$is_diy = $this->_post('is_diy');
		$seed_id = $this->_post('seed_id');
		$state = $this->_post('state');
		$planter_seed_id = $this->_post('planter_seed_id');

		if (is_numeric($tem) && is_numeric($dam) && is_numeric($light) && is_numeric($seed_state_id) && is_numeric($planter_id) && is_numeric($is_diy) && is_numeric($seed_id) && is_numeric($state) && is_numeric($planter_seed_id))
		{
			//权限判断
			$user_id = intval(session('user_id'));
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
			if (!$planter_info || !$planter_obj->checkPriv())
			{
				exit('failure');
			}

			//获取$planter_seed_id
			$planter_seed_obj = new PlanterSeedModel();
			$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_id = ' . $planter_id . ' AND planter_seed_id = ' . $planter_seed_id, 'planter_seed_id', 'plant_time DESC');
			if (!$planter_seed_info)
			{
				exit('failure');
			}

			$planter_seed_state_obj = new PlanterSeedStateModel($seed_state_id);
			$planter_seed_state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('planter_seed_id = ' . $planter_seed_info['planter_seed_id'] . ' AND seed_id = ' . $seed_id . ' AND state = ' . $state, 'planter_seed_state_id');
			$arr = array(
				'outside_temperature'	=> $tem,
				'humidity'				=> $dam,
				'illuminance_limit'		=> $light,
				'seed_id'				=> $seed_id,
				'planter_id'			=> $planter_id,
				'state'					=> $state,
				'planter_seed_id'		=> $planter_seed_info['planter_seed_id'],
				'user_id'				=> $user_id,
			);
			$success = false;
			if (!$planter_seed_state_info)
			{
				//加入一条
				$success = $planter_seed_state_obj->addPlanterSeedState($arr);
				//将planter表中的planter_seed_state_id设为success
				$arr2 = array(
					'planter_seed_state_id'	=> $success,
					'seed_state_id'			=> $seed_state_id,
				);
				$planter_obj->editPlanter();
			}
			else
			{
				$planter_seed_state_obj = new PlanterSeedStateModel($planter_seed_state_info['planter_seed_state_id']);
				//修改原有
				$success = $planter_seed_state_obj->editPlanterSeedState($arr);
			}

			//更新内存中的command
			$planter_obj->flushCommand('ADC2', $tem * 10);
			$planter_obj->flushCommand('ADC3', $dam * 10);
			$planter_obj->flushCommand('ADC4', $light);

			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//设置喷雾时间 
	public function set_spray_time()
	{
		$planter_id = intval(session('planter_id'));
		$user_id = intval(session('user_id'));
		$ton = $this->_post('ton');
		$toff = $this->_post('toff');

		if ($planter_id && $user_id && ctype_digit($ton) && ctype_digit($toff))
		{
			//权限判断
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
			if (!$planter_info || !$planter_obj->checkPriv())
			{
				exit('failure');
			}

			$planter_obj->flushCommand('T1', sprintf("%04d", $ton));
			$planter_obj->flushCommand('T2', sprintf("%04d", $toff));

			$arr = array(
				'ton'	=> $ton,
				'toff'	=> $toff,
			);
			$success = $planter_obj->editPlanter($arr);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}
	
	//切换冒险/自动模式
	public function set_mode()
	{
		$planter_id = intval(session('planter_id'));
		$user_id = intval(session('user_id'));
		$mode = $this->_post('mode');

		if ($planter_id && $user_id && ctype_digit($mode))
		{
			$mode = $mode == 1 ? 1 : 0;
			//权限判断
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
			if (!$planter_info || !$planter_obj->checkPriv())
			{
				exit('failure');
			}

			$planter_obj->flushCommand('MD', $mode);

			$arr = array(
				'is_risk_mode'	=> $mode,
			);
			$success = $planter_obj->editPlanter($arr);
			/*$redis = new Redis();
			$redis->connect('localhost', 6379);
			$command_info = $redis->get('command_' . $planter_id);
			echo "<pre>";
			print_r($command_info);*/
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}
}

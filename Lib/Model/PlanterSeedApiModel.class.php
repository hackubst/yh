<?php
class PlanterSeedApiModel extends ApiModel
{
	//当前绑定的种植机ID
	private $planter_id;

	function _initialize()
	{
		$this->planter_id = intval(session('planter_id'));
		if (!$this->planter_id)
		{
			ApiModel::returnResult(40012, null, '当前未绑定种植机，无法操作');
		}
	}

	/**
	 * 获取当前种植机当前进行中的植物列表
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回$planter_seed_list，失败退出返回错误码
	 * @todo 获取当前种植机当前进行中的植物列表
	 */
	function getPlanterSeedList($params)
	{
		$item_num_per_my_plant_page = $GLOBALS['config_info']['ITEM_NUM_PER_MY_PLANT_PAGE'];
		$firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;
		$planter_id = $this->planter_id;
		$where = 'planter_id = ' . $planter_id;
		$state = isset($params['state']) ? $params['state'] : 1;
		$where .= $state == 1 ? ' AND is_reap = 0' : ' AND is_reap = 1';
		$planter_seed_obj = new PlanterSeedModel();
		//总数
		$total = $planter_seed_obj->getPlanterSeedNum($where);
		$planter_seed_list = array();

		if (($total == 0 || $firstRow <= ($total - 1)) && $planter_id)
		{
			$planter_seed_obj->setStart($firstRow);
			$planter_seed_obj->setLimit($item_num_per_my_plant_page);
			//获取植物列表
			require_cache('Common/func_item.php');
			$planter_seed_list = $planter_seed_obj->getPlanterSeedList('planter_seed_id, item_id, state, plant_time, item_name, base_pic', $where, 'plant_time DESC');
			foreach ($planter_seed_list AS $k => $v)
			{
				$planter_seed_list[$k]['state'] = SeedStateModel::convertState($v['state']);
				$planter_seed_list[$k]['plant_time'] = date('Y-m-d H:i:s', $v['plant_time']);
				$planter_seed_list[$k]['base_pic'] = middle_img($v['base_pic']);
			}
			#$planter_seed_list[$k]['outside_temperature'] /= 10;
			#$planter_seed_list[$k]['humidity'] /= 10;
		}
		else
		{
			ApiModel::returnResult(40018, null, '没有更多记录了');
		}

		$planter_seed_info = array(
			'total_num'			=> $total,
			'nextFirstRow'		=> $firstRow + $item_num_per_my_plant_page + $firstRow,
			'planter_seed_list'	=> $planter_seed_list
		);

		return $planter_seed_info;
	}

	/**
	 * 种植新植物
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'种植成功'，失败返回错误码
	 * @todo 种植新植物
	 */
	function plantNewSeed($params)
	{
		$planter_id = $this->planter_id;
		$seed_id = $params['seed_id'];
		$seed_state_id = $params['seed_state_id'];
		$state = $params['state'];
		$user_id = intval(session('user_id'));

		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
		#echo $planter_obj->getLastSql();
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		//将该种子加入到种植机种子关联表中
		$arr = array(
			'planter_id'	=> $planter_id,
			'seed_id'		=> $seed_id,
			'state'			=> $state,
			'plant_time'	=> time(),
		);
		$planter_seed_obj = new PlanterSeedModel();
		$planter_seed_id = $planter_seed_obj->addPlanterSeed($arr);

		//修改种植机表的模拟状态
		$arr = array(
			'state'			=> $state,
			'seed_state_id'	=> $seed_state_id
		);
		$planter_obj->editPlanter($arr);

		$return_arr = array(
			'planter_seed_id'	=> $planter_seed_id
		);

		return $return_arr;
	}

	/**
	 * 根据种植机植物ID获取种植机植物信息
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_seed_info，失败返回错误码
	 * @todo 根据种植机植物ID获取种植机植物信息
	 */
	function getPlanterSeedInfo($params)
	{
		$planter_id = $this->planter_id;
		$planter_seed_id = $params['planter_seed_id'];
		$planter_seed_obj = new PlanterSeedModel();
		$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_seed_id = ' . $planter_seed_id . ' AND planter_id = ' . $planter_id, 'seed_id, state');
		if (!$planter_seed_info)
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		return $planter_seed_info;
	}

	/**
	 * 改变种植机状态
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'修改成功'，失败返回错误码
	 * @todo 改变种植机状态
	 */
	function setPlanterSeedState($params)
	{
		$state = $params['state'];
		$seed_id = $params['seed_id'];
		$planter_seed_id = $params['planter_seed_id'];
		$planter_id = $this->planter_id;

		$user_id = intval(session('user_id'));
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		//获取该种子当前状态的种子状态ID
		$seed_state_obj = new SeedStateModel();
		$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_id = ' . $seed_id . ' AND state = ' . $state, 'seed_state_id');
		if (!$seed_state_info)
		{
			ApiModel::returnResult(42022, null, '植物状态不存在，请检查seed_id参数是否正确');
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

		return '修改成功';
	}

	/**
	 * 恢复种植机状态
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'修改成功'，失败返回错误码
	 * @todo 恢复种植机状态
	 */
	function recoverPlanterSeedState($params)
	{
		$state = $params['state'];
		$seed_id = $params['seed_id'];
		$planter_seed_id = $params['planter_seed_id'];
		$planter_id = $this->planter_id;

		$user_id = intval(session('user_id'));
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_seed_state_id, seed_state_id');
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		//获取该种子当前状态的种子状态ID
		$seed_state_obj = new SeedStateModel();
		$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_id = ' . $seed_id . ' AND state = ' . $state, 'seed_state_id, outside_temperature, humidity, illuminance_limit, illuminance_hour');
		if (!$seed_state_info)
		{
			ApiModel::returnResult(42022, null, '植物状态不存在，请检查seed_id参数是否正确');
		}

		//修改种植机表的模拟状态
		$arr = array(
			'state'			=> $state,
			'seed_state_id'	=> $seed_state_info['seed_state_id']
		);
		$success1 = $planter_obj->editPlanter($arr);

		//获取当前模拟的种植机参数信息
		if ($planter_info['seed_state_id'] == $seed_state_info['seed_state_id'])
		{
			//当前种植机状态为要恢复的状态，修改数据库中的参数，内存中的参数
			$p_arr = array(
				'outside_temperature'	=> $seed_state_info['outside_temperature'] * 10,
				'humidity'				=> $seed_state_info['humidity'] * 10,
				'illuminance_limit'		=> $seed_state_info['humidity'],
				'illuminance_hour'		=> $seed_state_info['illuminance_hour'],
				'planter_seed_state_id'	=> 0,
			);
			$planter_obj->editPlanter($p_arr);

			//更新内存中的command
			$planter_obj->flushCommand('ADC2', $seed_state_info['outside_temperature'] * 10);
			$planter_obj->flushCommand('ADC3', $seed_state_info['humidity'] * 10);
			$planter_obj->flushCommand('ADC4', $seed_state_info['illuminance_limit']);
		}

		//获取该种子当前状态的种子状态ID
		$planter_seed_state_obj = new PlanterSeedStateModel();
		$planter_seed_state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('planter_seed_id = ' . $planter_seed_id . ' AND state = ' . $state, 'planter_seed_state_id');
		$planter_seed_state_id = $planter_seed_state_info ? $planter_seed_state_info['planter_seed_state_id'] : 0;

		//修改种植机种子表的状态
		$planter_seed_state_obj = new PlanterSeedStateModel($planter_seed_state_id);
		$success2 = $planter_seed_state_obj->delPlanterSeedState($planter_seed_state_id);
log_file($planter_seed_state_obj->getLastSql());

		unset($seed_state_info['seed_state_id']);
		unset($seed_state_info['illuminance_hour']);
		return $seed_state_info;
	}

	/**
	 * 获取当前种植机的默认状态信息
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_seed_state_info，失败返回错误码
	 * @todo 获取当前种植机的默认状态信息
	 */
	function getPlanterSeedState($params)
	{
		$planter_id = $this->planter_id;
		$planter_seed_id = $params['planter_seed_id'];
		$seed_state_id = $params['seed_state_id'];

		//权限判断
		$planter_seed_obj = new PlanterSeedModel();
		$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_seed_id = ' . $planter_seed_id . ' AND planter_id = ' . $planter_id, 'seed_id');
		if (!$planter_seed_info)
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		//获取植物状态信息，若用户已经自定义了该植物状态，取用户自定义的参数，否则取系统默认的状态参数
		$state_info = array();
		$planter_seed_state_obj = new PlanterSeedStateModel();
		$state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('planter_seed_id = ' . $planter_seed_id, 'outside_temperature, humidity, illuminance_limit, state');
		if (!$state_info)
		{
			$seed_state_obj = new SeedStateModel();
			$state_info = $seed_state_obj->getSeedStateInfo('seed_state_id = ' . $seed_state_id, 'outside_temperature, humidity, illuminance_limit, state');
		}
		$state_info['outside_temperature'] /= 10;
		$state_info['humidity'] /= 10;

		if (empty($state_info))
		{
			ApiModel::returnResult(-1, null, '系统错误');
		}

		return $state_info;
	}

	/**
	 * 根据planter_seed_id获取种植机种子日志列表
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_seed_log_list，失败返回错误码
	 * @todo 根据planter_seed_id获取种植机种子日志列表
	 */
	function getPlanterSeedLogList($params)
	{
		$planter_seed_id = $params['planter_seed_id'];

		//权限判断
		$planter_seed_obj = new PlanterSeedModel();
		$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_seed_id = ' . $planter_seed_id . ' AND planter_id = ' . $this->planter_id, 'seed_id');
		if (!$planter_seed_info)
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		//获取种植机种子日志
		$planter_log_obj = new PlanterLogModel();
		$planter_log_list = $planter_log_obj->getPlanterLogList('end_seed_state, addtime', 'planter_seed_id = ' . $planter_seed_id, 'start_seed_state ASC');
		foreach ($planter_log_list AS $k => $v)
		{
			$planter_log_list[$k]['state'] = SeedStateModel::convertState($v['end_seed_state']);
			unset($planter_log_list[$k]['end_seed_state']);
		}

		return $planter_log_list;
	}

	/**
	 * 修改当前种植机默认参数信息
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'修改成功'，失败返回错误码
	 * @todo 修改当前种植机默认参数信息
	 */
	function editPlanterSeedInfo($params)
	{
log_file('planter_id = ' . session('planter_id'));
log_file('params: ' . arrayToString($params));
		$tem = $params['t_num'];
		$dam = $params['h_num'];
		$light = $params['i_num'];
		$seed_state_id = $params['seed_state_id'];
		$planter_id = $this->planter_id;
		$seed_id = $params['seed_id'];
		$state = $params['state'];
		$planter_seed_id = $params['planter_seed_id'];
		$user_id = intval(session('user_id'));

		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		//获取$planter_seed_id
		$planter_seed_obj = new PlanterSeedModel();
		$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_id = ' . $planter_id . ' AND planter_seed_id = ' . $planter_seed_id, 'planter_seed_id', 'plant_time DESC');
		if (!$planter_seed_info)
		{
			ApiModel::returnResult(42023, null, '无效的planter_seed_id');
		}

		$planter_seed_state_obj = new PlanterSeedStateModel($seed_state_id);
		$planter_seed_state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('planter_seed_id = ' . $planter_seed_info['planter_seed_id'] . ' AND seed_id = ' . $seed_id . ' AND state = ' . $state, 'planter_seed_state_id');
		$arr = array(
			'outside_temperature'	=> $tem * 10,
			'humidity'				=> $dam * 10,
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
		}
		else
		{
			$planter_seed_state_obj = new PlanterSeedStateModel($planter_seed_state_info['planter_seed_state_id']);
			//修改原有
			$success = $planter_seed_state_obj->editPlanterSeedState($arr);
		}
log_file($planter_seed_state_obj->getLastSql());

		//更新内存中的command
		$planter_obj->flushCommand('ADC2', $tem * 10);
		$planter_obj->flushCommand('ADC3', $dam * 10);
		$planter_obj->flushCommand('ADC4', $light);

		return '修改成功';
	}

	/**
	 * 获取参数列表
	 * @author 姜伟
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		$params = array(
			'plantNewSeed'	=> array(
				array(
					'field'		=> 'seed_id',
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41018, 
					'empty_code'=> 44018, 
					'type_code'	=> 45018, 
				),
				array(
					'field'		=> 'seed_state_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41019, 
					'empty_code'=> 44019, 
					'type_code'	=> 45019, 
				),
				array(
					'field'		=> 'state', 
					'min_len'	=> 1, 
					'max_len'	=> 9, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40020, 
					'miss_code'	=> 41020, 
					'empty_code'=> 44020, 
					'type_code'	=> 45020, 
				),
			),
			'getPlanterSeedInfo'	=> array(
				array(
					'field'		=> 'planter_seed_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41021, 
					'empty_code'=> 44021, 
					'type_code'	=> 45021, 
				),
			),
			'setPlanterSeedState'	=> array(
				array(
					'field'		=> 'state', 
					'min_len'	=> 1, 
					'max_len'	=> 9, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40020, 
					'miss_code'	=> 41020, 
					'empty_code'=> 44020, 
					'type_code'	=> 45020, 
				),
				array(
					'field'		=> 'seed_id',
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41018, 
					'empty_code'=> 44018, 
					'type_code'	=> 45018, 
				),
				array(
					'field'		=> 'planter_seed_id',
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41021, 
					'empty_code'=> 44021, 
					'type_code'	=> 45021, 
				)
			),
			'recoverPlanterSeedState'	=> array(
				array(
					'field'		=> 'state', 
					'min_len'	=> 1, 
					'max_len'	=> 9, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40020, 
					'miss_code'	=> 41020, 
					'empty_code'=> 44020, 
					'type_code'	=> 45020, 
				),
				array(
					'field'		=> 'seed_id',
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41018, 
					'empty_code'=> 44018, 
					'type_code'	=> 45018, 
				),
				array(
					'field'		=> 'planter_seed_id',
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41021, 
					'empty_code'=> 44021, 
					'type_code'	=> 45021, 
				)
			),
			'getPlanterSeedList'	=> array(
				array(
					'field'		=> 'state',
				),
				array(
					'field'		=> 'firstRow', 
				),
			),
			'getPlanterSeedState'	=> array(
				array(
					'field'		=> 'seed_state_id',
					'min_len'	=> 1, 
					'max_len'	=> 9, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40020, 
					'miss_code'	=> 41020, 
					'empty_code'=> 44020, 
					'type_code'	=> 45020, 
				),
				array(
					'field'		=> 'planter_seed_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41021, 
					'empty_code'=> 44021, 
					'type_code'	=> 45021, 
				),
			),
			'editPlanterSeedInfo'	=> array(
				array(
					'field'		=> 't_num', 
					'min_len'	=> 18, 
					'max_len'	=> 50, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40022, 
					'miss_code'	=> 41022, 
					'empty_code'=> 44022, 
					'type_code'	=> 45022, 
				),
				array(
					'field'		=> 'h_num', 
					'min_len'	=> 20, 
					'max_len'	=> 100, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40023, 
					'miss_code'	=> 41023, 
					'empty_code'=> 44023, 
					'type_code'	=> 45023, 
				),
				array(
					'field'		=> 'i_num', 
					'min_len'	=> 300, 
					'max_len'	=> 80000, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40024, 
					'miss_code'	=> 41024, 
					'empty_code'=> 44024, 
					'type_code'	=> 45024, 
				),
				array(
					'field'		=> 'seed_id',
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41018, 
					'empty_code'=> 44018, 
					'type_code'	=> 45018, 
				),
				array(
					'field'		=> 'seed_state_id',
					'min_len'	=> 1, 
					'max_len'	=> 9, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40020, 
					'miss_code'	=> 41020, 
					'empty_code'=> 44020, 
					'type_code'	=> 45020, 
				),
				array(
					'field'		=> 'planter_seed_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41021, 
					'empty_code'=> 44021, 
					'type_code'	=> 45021, 
				),
				array(
					'field'		=> 'state', 
					'min_len'	=> 1, 
					'max_len'	=> 9, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40020, 
					'miss_code'	=> 41020, 
					'empty_code'=> 44020, 
					'type_code'	=> 45020, 
				)
			),
			'getPlanterSeedLogList'	=> array(
				array(
					'field'		=> 'planter_seed_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41021, 
					'empty_code'	=> 44021, 
					'type_code'	=> 45021, 
				),
			),
		);

		return $params[$func_name];
	}
}

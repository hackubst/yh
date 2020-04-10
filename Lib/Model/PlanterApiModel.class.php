<?php
class PlanterApiModel extends ApiModel
{
	//当前绑定的种植机ID
	private $planter_id;

	function _initialize()
	{
		$this->planter_id = intval(session('planter_id'));
		$api_name = $_POST['api_name'];
		if (!$this->planter_id && $api_name != 'plant.planter.bindNewPlanter')
		{
			ApiModel::returnResult(40012, null, '当前未绑定种植机，无法操作');
		}
	}

	/**
	 * 绑定新的种植机
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回'绑定成功'，失败退出返回错误码
	 * @todo 
	 */
	function bindNewPlanter($params)
	{
		$planter_obj = new PlanterModel();
		$planter_info = $planter_obj->getPlanterInfo('serial_num = "' . $params['serial_num'] .  '"', 'planter_id, bind_time, user_id, last_visit_time');
		if (!$planter_info)
		{
			ApiModel::returnResult(40013, null, '无效的序列号');
		}

		if ($planter_info['user_id'])
		{
			ApiModel::returnResult(40013, null, '该序列号已使用');
		}

		if (!$planter_info['last_visit_time'])
		{
			ApiModel::returnResult(40014, null, '请将该机器先连上wifi');
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

		if (!$success1 || !$success2)
		{
			ApiModel::returnResult(42013, null, '系统错误，绑定失败');
		}

		session('planter_id', $planter_info['planter_id']);
		return '绑定成功';
	}

	/**
	 * 获取当前用户种植机列表
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_list，失败返回错误码
	 * @todo 获取当前用户种植机列表
	 */
	function getUserPlanterList($params)
	{
		$user_id = intval(session('user_id'));
		//获取种植机列表
		$planter_obj = new PlanterModel();
		$planter_obj->setLimit(100);
		$planter_list = $planter_obj->getPlanterList('planter_id, planter_name', 'user_id = ' . $user_id, 'bind_time DESC');

		//被授权的种植机列表
		$planter_auth_obj = new PlanterAuthModel();
		$planter_auth_list = $planter_auth_obj->getPlanterAuthList('p.planter_id, p.planter_name', 'tp_planter_auth.user_id = ' . $user_id);
		array_merge($planter_auth_list, $planter_list);
		$len = count($planter_list);
		foreach ($planter_auth_list AS $k => $v)
		{
			$planter_list[$k + $len] = $v;
		}

		return $planter_list;
	}

	/**
	 * 获取当前用户所有(可授权)的种植机列表
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_list，失败返回错误码
	 * @todo 获取当前用户所有(可授权)的种植机列表
	 */
	function getOwnedPlanterList($params)
	{
		$user_id = intval(session('user_id'));
		//获取种植机列表
		$planter_obj = new PlanterModel();
		$planter_obj->setLimit(100);
		$planter_list = $planter_obj->getPlanterList('planter_id, planter_name', 'user_id = ' . $user_id, 'bind_time DESC');

		return $planter_list;
	}

	/**
	 * 获取某种植机的授权列表
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_auth_list，失败返回错误码
	 * @todo 获取某种植机的授权列表
	 */
	function getPlanterAuthList($params)
	{
		$user_id = intval(session('user_id'));
		$planter_id = intval($params['planter_id']);
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('user_id = ' . $user_id . ' AND planter_id = ' . $planter_id);
		if (!$planter_info)
		{
			ApiModel::returnResult(42014, null, '权限不足');
		}
		//授权列表
		$planter_auth_obj = new PlanterAuthModel();
		$planter_auth_list = $planter_auth_obj->getPlanterAuthList('tp_planter_auth.user_id', 'tp_planter_auth.planter_id = ' . $planter_id);
		foreach ($planter_auth_list AS $k => $v)
		{
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname, mobile');
			$planter_auth_list[$k]['nickname'] = $user_info['nickname'];
			$planter_auth_list[$k]['mobile'] = $user_info['mobile'];
			unset($planter_auth_list[$k]['user_id']);
		}

		return $planter_auth_list;
	}

	/**
	 * 获取某种植机的分享信息
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_auth_list，失败返回错误码
	 * @todo 获取某种植机的分享信息
	 */
	function getPlanterShareInfo($params)
	{
		$user_id = intval(session('user_id'));
		$planter_id = intval($params['planter_id']);
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('user_id = ' . $user_id . ' AND planter_id = ' . $planter_id, 'planter_name');
		if (!$planter_info)
		{
			ApiModel::returnResult(42014, null, '权限不足');
		}

		/** 分享信息begin **/
		//链接
		$planter_share_obj= new PlanterShareModel();
		$share_code = $planter_share_obj->generateShareCode();
		$arr = array(
			'planter_id'	=> $planter_id,
			'share_code'	=> $share_code,
		);
		$planter_share_obj->addPlanterShare($arr);
		$link = C('DOMAIN') . '/' . 'FrontShare/share/share_code/' . $share_code;

		//标题
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('nickname, realname, headimgurl');
		$nickname = $user_info['nickname'] ? $user_info['nickname'] : $user_info['realname'];
		$title = '您的好友“' .$nickname . '”';
		$title .= '邀请您一起控制潘朵拉智能种植机“' . $planter_info['planter_name'] . '”';
		$content = $title;

		//图片
		$img = $user_info['headimgurl'];
		if(!@fopen($img, 'r'))
		{
			$img = C('DOMAIN') . '/Public/Images/front/share_logo.png';
		}

		/** 分享信息end **/
		$return_arr = array(
			'link'		=> $link,
			'title'		=> $title,
			'content'	=> $content,
			'img'		=> $img,
		);

		return $return_arr;
	}

	/**
	 * 获取种植机基本信息接口
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$planter_info，失败返回错误码
	 * @todo 获取用户基本信息接口
	 */
	function getPlanterInfo($params)
	{
		$planter_id = $this->planter_id;
		$planter_obj = new PlanterModel($this->planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $this->planter_id, 'planter_name, box_state, outside_temperature, humidity, illuminance, alarm, state, last_visit_time, is_risk_mode, ton, toff');
		$planter_info['outside_temperature'] /= 10;
		$planter_info['humidity'] /= 10;
		$planter_info['online'] = $planter_info['last_visit_time'] + $GLOBALS['config']['planter_online_time'] >= time() ? 1 : 0;		//是否在线
		$seed_id = 0;
		$planter_seed_id = 0;
		//已有种植机，继续判断当前是否存在进行中的植物
		$planter_seed_obj = new PlanterSeedModel();
		$planting_seed_num = $planter_seed_obj->getPlanterSeedNum('is_reap = 0 AND planter_id = ' . $planter_id);
		#$planting_seed_info = $planter_seed_obj->getPlanterSeedInfo('is_reap = 0 AND planter_id = ' . $planter_id , 'planter_seed_id', 'plant_time DESC');

		if ($planting_seed_num)
		{
			//获取当前模拟的planter_seed_id
			$planter_log_obj = new PlanterLogModel();
			$planter_log_info = $planter_log_obj->getPlanterLogInfo('planter_id = ' . $planter_id, 'planter_seed_id', 'addtime DESC');
			if ($planter_log_info)
			{
				$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('is_reap = 0 AND planter_seed_id = ' . $planter_log_info['planter_seed_id']);
				if ($planter_seed_info)
				{
					$planter_seed_id = $planter_log_info['planter_seed_id'];
				}
				else
				{
					$planting_seed_info = $planter_seed_obj->getPlanterSeedInfo('is_reap = 0 AND planter_id = ' . $planter_id , 'planter_seed_id', 'plant_time DESC');
					if ($planting_seed_info)
					{
						$planter_seed_id = $planting_seed_info['planter_seed_id'];
					}
				}
				#$planter_seed_id = $planter_log_info['planter_seed_id'];
				$planter_seed_obj = new PlanterSeedModel();
				$planter_seed_info = $planter_seed_obj->getPlanterSeedInfo('planter_seed_id = ' . $planter_seed_id . ' AND planter_id = ' . $planter_id, 'seed_id, state');
				$seed_id = $planter_seed_info ? $planter_seed_info['seed_id'] : $seed_id;
			}
		}
		$planter_info['seed_id'] = $seed_id;
		$planter_info['planter_seed_id'] = $planter_seed_id;

		return $planter_info;
	}

	/**
	 * 切换种植机
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'切换成功'，失败返回错误码
	 * @todo 切换种植机
	 */
	function changeCurrentPlanter($params)
	{
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$success = $user_obj->changeCurrentPlanter($params['planter_id']);
		if (!$success)
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		session('planter_id', $params['planter_id']);
		return '修改成功';
	}

	/**
	 * 切换冒险/自动模式
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'切换成功'，失败返回错误码
	 * @todo 切换冒险/自动模式
	 */
	function setMode($params)
	{
		$planter_id = intval(session('planter_id'));
		$user_id = intval(session('user_id'));
		$mode = $params['mode'];
		$mode = $mode == 1 ? 1 : 0;

		//权限判断
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		$planter_obj->flushCommand('MD', $mode);

		$arr = array(
			'is_risk_mode'	=> $mode,
		);
		$success = $planter_obj->editPlanter($arr);

		return '切换成功';
	}

	/**
	 * 修改种植机名称
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'修改成功'，失败返回错误码
	 * @todo 修改种植机名称
	 */
	function setPlanterName($params)
	{
		$user_id = intval(session('user_id'));
		$planter_obj = new PlanterModel($params['planter_id']);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $params['planter_id'], 'planter_id');
	
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		$arr = array(
			'planter_name'	=> $params['planter_name']
		);
		$success = $planter_obj->editPlanter($arr);

		if (!$success)
		{
			ApiModel::returnResult(-1, null, '系统错误');
		}

		return '修改成功';
	}

	/**
	 * 开关控制
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'切换成功'，失败返回错误码
	 * @todo 开关控制
	 */
	function setBoxState($params)
	{
		$user_id = intval(session('user_id'));
		$planter_obj = new PlanterModel($this->planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $this->planter_id, 'planter_id');
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		$success = $planter_obj->sendCommand($params['serial'], $params['state']);
		if (!$success)
		{
			ApiModel::returnResult(-1, null, '系统错误');
		}

		return '切换成功';
	}

	/**
	 * 灌溉时间设置
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'设置成功'，失败返回错误码
	 * @todo 灌溉时间设置
	 */
	function setWaterTime($params)
	{
		$planter_id = intval(session('planter_id'));
		$user_id = intval(session('user_id'));
		$ton = $params['ton'];
		$toff = $params['toff'];

		//权限判断
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id, 'planter_id');
		if (!$planter_info || !$planter_obj->checkPriv())
		{
			ApiModel::returnResult(42014, null, '权限不足或服务器错误造成无法修改');
		}

		$planter_obj->flushCommand('T1', sprintf("%04d", $ton));
		$planter_obj->flushCommand('T2', sprintf("%04d", $toff));

		$arr = array(
			'ton'	=> $ton,
			'toff'	=> $toff,
		);
		$success = $planter_obj->editPlanter($arr);

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
			'bindNewPlanter'	=> array(
				array(
					'field'		=> 'serial_num', 
					'required'	=> true, 
					'miss_code'	=> 41013, 
				),
			),
			'changeCurrentPlanter'	=> array(
				array(
					'field'		=> 'planter_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41014, 
					'empty_code'=> 44014, 
					'type_code'	=> 45014, 
				),
			),
			'setPlanterName'	=> array(
				array(
					'field'		=> 'planter_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41014, 
					'empty_code'=> 44014, 
					'type_code'	=> 45014, 
				),
				array(
					'field'		=> 'planter_name', 
					'min_len'	=> 1, 
					'max_len'	=> 6, 
					'type'		=> 'string', 
					'required'	=> true, 
					'len_code'	=> 40015, 
					'miss_code'	=> 41015, 
					'empty_code'=> 44015, 
					'type_code'	=> 45015, 
				)
			),
			'setBoxState'	=> array(
				array(
					'field'		=> 'serial', 
					'type'		=> 'int', 
					'min_len'	=> 1, 
					'max_len'	=> 6, 
					'required'	=> true, 
					'len_code'	=> 40016, 
					'miss_code'	=> 41016, 
					'empty_code'=> 44016, 
					'type_code'	=> 45016, 
				),
				array(
					'field'		=> 'state', 
					'min_len'	=> 0, 
					'max_len'	=> 1, 
					'type'		=> 'int', 
					'required'	=> true, 
					'len_code'	=> 40017, 
					'miss_code'	=> 41017, 
					'empty_code'=> 44017, 
					'type_code'	=> 45017, 
				)
			),
			'setMode'	=> array(
				array(
					'field'		=> 'mode', 
					'type'		=> 'int', 
					'min_len'	=> 0, 
					'max_len'	=> 1, 
					'required'	=> true, 
					'len_code'	=> 40054, 
					'miss_code'	=> 41054, 
					'empty_code'=> 44054, 
					'type_code'	=> 45054, 
				),
			),
			'setWaterTime'	=> array(
				array(
					'field'		=> 'ton', 
					'type'		=> 'int', 
					'min_len'	=> 0, 
					'max_len'	=> 9999, 
					'required'	=> true, 
					'len_code'	=> 40055, 
					'miss_code'	=> 41055, 
					'empty_code'=> 44055, 
					'type_code'	=> 45055, 
				),
				array(
					'field'		=> 'toff', 
					'type'		=> 'int', 
					'min_len'	=> 0, 
					'max_len'	=> 9999, 
					'required'	=> true, 
					'len_code'	=> 40056, 
					'miss_code'	=> 41056, 
					'empty_code'=> 44056, 
					'type_code'	=> 45056, 
				),
			),
			'getPlanterAuthList'	=> array(
				array(
					'field'		=> 'planter_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41014, 
					'empty_code'=> 44014, 
					'type_code'	=> 45014, 
				),
			),
			'getPlanterShareInfo'	=> array(
				array(
					'field'		=> 'planter_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41014, 
					'empty_code'=> 44014, 
					'type_code'	=> 45014, 
				),
			),
		);

		return $params[$func_name];
	}
}

<?php
/**
 * acp后台种子状态类
 */
class AcpSeedStateAction extends AcpAction {

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
     * 植物状态列表页
     * @author 姜伟
     * @return void
     * @todo 初始化方法
     */
	function get_seed_state_list()
	{
		//初始化查询条件
		$where = '1';
		//种子名称
		$item_name = $this->_request('item_name');
		if ($item_name)
		{
			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemList('GROUP_CONCAT(item_id) AS item_id_str', 'item_name LIKE "%' . $item_name . '%"', '', 'item_name');
			$item_id_str = $item_info ? $item_info[0]['item_id_str'] : 0;
			$where .= ' AND seed_id IN(' . $item_id_str . ')';
		}

		$seed_state_obj = new SeedStateModel();
        //分页处理
        import('ORG.Util.Pagelist');
        $count = $seed_state_obj->getSeedStateNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$seed_state_obj->setStart($Page->firstRow);
        $seed_state_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$seed_state_list = $seed_state_obj->getSeedStateList('', $where, 'state ASC');
		$seed_state_list = $seed_state_obj->getListData($seed_state_list);
		$this->assign('seed_state_list', $seed_state_list);

		//重新赋值到表单
		$this->assign('item_name', $item_name);
		$this->assign('head_title', '植物状态列表页');
		$this->display();
    }

	/**
     * 添加种子状态
     * @author 姜伟
     * @return void
     * @todo 上传新种子状态
     */
	public function add_seed_state()
	{
        $this->assign('head_title', '添加种子状态');
        $act = I('post.act');

        // 添加种子状态
		if ($act == 'add')
		{
			/*** 接收并验证表单数据begin ***/
			$seed_id = $this->_post('seed_id');
			$this->assign('seed_id', $seed_id);
			$state = $this->_post('state');
			$this->assign('state', $state);
			$temperature = $this->_post('temperature');
			$this->assign('temperature', $temperature);
			$outside_temperature = $this->_post('outside_temperature');
			$this->assign('outside_temperature', $outside_temperature);
			$humidity = $this->_post('humidity');
			$this->assign('humidity', $humidity);
			$illuminance_limit = $this->_post('illuminance_limit');
			$this->assign('illuminance_limit', $illuminance_limit);
			$illuminance_hour = $this->_post('illuminance_hour');
			$this->assign('illuminance_hour', $illuminance_hour);
			$liquid_level = $this->_post('liquid_level');
			$this->assign('liquid_level', $liquid_level);
			$last_day = $this->_post('last_day');
			$this->assign('last_day', $last_day);
			$img_path = $this->_post('img_url');
			$this->assign('img_path', $img_path);

			if (!$seed_id)
			{
				$this->error('对不起，请选择种子！');
			}

			if (!$state)
			{
				$this->error('对不起，请选择种子状态！');
			}
			//查看该种子是否已添加该状态
			$seed_state_obj = new SeedStateModel();
			$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_id = ' . $seed_id . ' AND state = ' . $state);
			if ($seed_state_info)
			{
				$this->error('对不起，该植物已编辑过该状态！');
			}


			if (!$temperature)
			{
				$this->error('对不起，请填写土壤温度！');
			}

			if (!$outside_temperature)
			{
				$this->error('对不起，请填写空气温度！');
			}

			if (!$humidity)
			{
				$this->error('对不起，请填写空气湿度！');
			}

			if (!$illuminance_limit)
			{
				$this->error('对不起，请填写光照强度！');
			}

			if (!$illuminance_hour)
			{
				$this->error('对不起，请填写光照时长！');
			}

			if (!$liquid_level)
			{
				$this->error('对不起，请填写液位！');
			}

			if (!$last_day)
			{
				$this->error('对不起，请填写该状态持续天数！');
			}

			if (!$img_path)
			{
				$this->error('对不起，请上传种子在该状态的示例图片！');
			}

			$arr = array(
				'seed_id'				=> $seed_id,
				'state'					=> $state,
				'temperature'			=> $temperature,
				'outside_temperature'	=> $outside_temperature,
				'humidity'				=> $humidity,
				'illuminance_limit'		=> $illuminance_limit,
				'illuminance_hour'		=> $illuminance_hour,
				'liquid_level'			=> $liquid_level,
				'last_day'				=> $last_day,
				'img_path'				=> $img_path,
			);

			$success = $seed_state_obj->addSeedState($arr);
			$redirect = U('/AcpSeedState/get_seed_state_list');
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

		//获取种子分类ID
		$class_obj = new ClassModel();
		$class_info = $class_obj->getClassInfo('class_tag = "seed"', 'class_id');
		$class_id = $class_info ? $class_info['class_id'] : 0;

		//获取种子列表
		$item_obj = new ItemModel();
		$item_obj->setLimit(1000);
		$item_list = $item_obj->getItemList('item_id, item_name', 'class_id = ' . $class_id, 'addtime DESC');
		$this->assign('item_list', $item_list);

		//获取状态列表
		$this->assign('state_list', SeedStateModel::getStateList());
        $this->display();
	}
	
	/**
     * 修改种子状态
     * @author 姜伟
     * @return void
     * @todo 修改种子状态
     */
	public function edit_seed_state()
	{
		$redirect = U('/AcpSeedState/get_seed_state_list');
		$seed_state_id = intval($this->_get('seed_state_id'));
		if (!$seed_state_id)
		{
			$this->error('对不起，非法访问！', $redirect);
		}
		$seed_state_obj = new SeedStateModel($seed_state_id);
		$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_state_id = ' . $seed_state_id);
		if (!$seed_state_info)
		{
			$this->error('对不起，不存在相关种子状态！', $redirect);
		}

		foreach ($seed_state_info AS $k => $v)
		{
			$this->assign($k, $v);
		}

		$act = $this->_post('act');
        // 修改种子状态
		if ($act == 'edit')
		{
			/*** 接收并验证表单数据begin ***/
			$seed_id = $this->_post('seed_id');
			$this->assign('seed_id', $seed_id);
			$state = $this->_post('state');
			$this->assign('state', $state);
			$temperature = $this->_post('temperature');
			$this->assign('temperature', $temperature);
			$outside_temperature = $this->_post('outside_temperature');
			$this->assign('outside_temperature', $outside_temperature);
			$humidity = $this->_post('humidity');
			$this->assign('humidity', $humidity);
			$illuminance_limit = $this->_post('illuminance_limit');
			$this->assign('illuminance_limit', $illuminance_limit);
			$illuminance_hour = $this->_post('illuminance_hour');
			$this->assign('illuminance_hour', $illuminance_hour);
			$liquid_level = $this->_post('liquid_level');
			$this->assign('liquid_level', $liquid_level);
			$last_day = $this->_post('last_day');
			$this->assign('last_day', $last_day);
			$img_path = $this->_post('img_url');
			$this->assign('img_path', $img_path);

			if (!$seed_id)
			{
				$this->error('对不起，请选择种子！');
			}

			if (!$state)
			{
				$this->error('对不起，请选择种子状态！');
			}
			//查看该种子是否已添加该状态
			$seed_state_obj = new SeedStateModel();
			$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_id = ' . $seed_id . ' AND state = ' . $state . ' AND seed_state_id != ' . $seed_state_id);
#echo $seed_state_obj->getLastSql();
#die;
			if ($seed_state_info)
			{
				$this->error('对不起，该植物已编辑过该状态！');
			}

			if (!$temperature)
			{
				$this->error('对不起，请填写土壤温度！');
			}

			if (!$outside_temperature)
			{
				$this->error('对不起，请填写空气温度！');
			}

			if (!$humidity)
			{
				$this->error('对不起，请填写空气湿度！');
			}

			if (!$illuminance_limit)
			{
				$this->error('对不起，请填写光照强度！');
			}

			if (!$illuminance_hour)
			{
				$this->error('对不起，请填写光照时长！');
			}

			if (!$liquid_level)
			{
				$this->error('对不起，请填写液位！');
			}

			if (!$last_day)
			{
				$this->error('对不起，请填写该状态持续天数！');
			}

			if (!$img_path)
			{
				$this->error('对不起，请上传种子在该状态的示例图片！');
			}

			$arr = array(
				'seed_id'				=> $seed_id,
				'state'					=> $state,
				'temperature'			=> $temperature,
				'outside_temperature'	=> $outside_temperature,
				'humidity'				=> $humidity,
				'illuminance_limit'		=> $illuminance_limit,
				'illuminance_hour'		=> $illuminance_hour,
				'liquid_level'			=> $liquid_level,
				'last_day'				=> $last_day,
				'img_path'				=> $img_path,
			);
			$seed_state_obj = new SeedStateModel($seed_state_id);
			//将种子状态基本信息保存到数据库
			if ($seed_state_obj->editSeedState($arr))
			{
				$this->success('恭喜您，编辑成功！');
			}
			else
			{
				$this->error('对不起，编辑失败！');
			}
        }

		//获取种子分类ID
		$class_obj = new ClassModel();
		$class_info = $class_obj->getClassInfo('class_tag = "seed"', 'class_id');
		$class_id = $class_info ? $class_info['class_id'] : 0;

		//获取种子列表
		$item_obj = new ItemModel();
		$item_obj->setLimit(1000);
		$item_list = $item_obj->getItemList('item_id, item_name', 'class_id = ' . $class_id, 'addtime DESC');
		$this->assign('item_list', $item_list);

		//获取状态列表
		$this->assign('state_list', SeedStateModel::getStateList());
        $this->assign('head_title', '修改种子状态');
        $this->display();
	}
}
?>

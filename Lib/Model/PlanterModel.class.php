<?php
/**
 * 种植机模型类
 */

class PlanterModel extends Model
{
    // 种植机id
    public $planter_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_id 种植机ID
     * @return void
     * @todo 初始化种植机id
     */
    public function PlanterModel($planter_id)
    {
        parent::__construct('planter');

        if ($planter_id = intval($planter_id))
		{
            $this->planter_id = $planter_id;
		}
    }

    /**
     * 获取种植机信息
     * @author 姜伟
     * @param int $planter_id 种植机id
     * @param string $fields 要获取的字段名
     * @return array 种植机基本信息
     * @todo 根据where查询条件查找种植机表中的相关数据并返回
     */
    public function getPlanterInfo($where, $fields = '')
    {
		$planter_info = array();
		$where = $where ? $where : 'planter_id = ' . intval($this->planter_id);

		if (!$this->planter_id)
		{
			$planter_info = $this->field('planter_id')->where($where)->find();
			$this->planter_id = $planter_info['planter_id'];
		}

		if(strtoupper(PHP_OS)!="LINUX")
		{
			//windows下直接取数据库
			$planter_info = $this->field($fields)->where($where)->find();
		}
		else
		{
			//linux下先从内存中取
			$redis = new Redis();
			$redis->connect('localhost', 6379);
			/*$json = array(
				"ADC1"=>"21",
				"ADC2"=>"26",
				"ADC3"=>"70",
				"ADC4"=>"1100",
				"ADC5"=>"1",
				"time"=>"1420100000"
			);
			$command_info = $redis->set('status_' . $this->planter_id, json_encode($json));
			$command_info = $redis->set('command_' . $this->planter_id, 'GPIO=101011&T1=1800&T2=1800&T3=2&ADC1=20&ADC2=25&ADC3=60&ADC4=10000&ADC5=0END');*/
			$command_info = $redis->get('command_' . $this->planter_id);
			$status_info = $redis->get('status_' . $this->planter_id);
			$command_info = self::parseCommand($command_info);
			if (!isset($command_info['GPIO']) || !$command_info['GPIO'])
			{
				$command_info = $redis->set('command_' . $this->planter_id, 'GPIO=101011&T1=360&T2=28440&ADC1=20&ADC2=25&ADC3=60&ADC4=10000&ADC5=0&MD=0END');
			}
			if (!$status_info)
			{
				$planter_info = $this->field($fields)->where($where)->find();
			}
			else
			{
				$status_info = json_decode($status_info, true);
				$planter_info = array(
					'planter_id'			=> $this->planter_id,
					'temperature'			=> $status_info['ADC1'],
					'outside_temperature'	=> $status_info['ADC2'],
					'humidity'				=> $status_info['ADC3'],
					'illuminance'			=> $status_info['ADC4'],
					'alarm'					=> $status_info['ADC5'],
					'box_state'				=> $command_info['GPIO'],
					'is_risk_mode'			=> $command_info['MD'],
					'last_visit_time'		=> $status_info['time'],
					'ton'					=> $command_info['T1'],
					'toff'					=> $command_info['T2'],
				);

				$arr = $this->field($fields)->where($where)->find();
				$planter_info = array_merge($arr, $planter_info);
			}
		}
		#echo "<pre>";
		#print_r($planter_info);
		#die;
		return $planter_info;
    }

    /**
     * 修改种植机信息
     * @author 姜伟
     * @param array $arr 种植机信息数组
     * @return boolean 操作结果
     * @todo 修改种植机信息
     */
    public function editPlanter($arr)
    {
		if (isset($arr['seed_state_id']))
		{
			$seed_state_obj = new SeedStateModel($arr['seed_state_id']);
			$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_state_id = ' . intval($arr['seed_state_id']), 'illuminance_hour');
			if ($seed_state_info)
			{
				//更新内存中的command
				$this->flushKey('light_total_' . $this->planter_id, intval($seed_state_info['illuminance_hour']) * 3600);
			}
		}
        return $this->where('planter_id = ' . $this->planter_id)->save($arr);
    }

    /**
     * 添加种植机
     * @author 姜伟
     * @param array $arr 种植机信息数组
     * @return boolean 操作结果
     * @todo 添加种植机
     */
    public function addPlanter($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除种植机
     * @author 姜伟
     * @param int $planter_id 种植机ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanter($planter_id)
    {
        if (!is_numeric($planter_id)) return false;
        return $this->where('planter_id = ' . $planter_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种植机数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种植机数量
     * @todo 根据where子句获取种植机数量
     */
    public function getPlanterNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种植机信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种植机基本信息
     * @todo 根据SQL查询字句查询种植机信息
     */
    public function getPlanterList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取种植机列表页数据信息列表
     * @author 姜伟
     * @param array $planter_list
     * @return array $planter_list
     * @todo 根据传入的$planter_list获取更详细的种植机列表页数据信息列表
     */
    public function getListData($planter_list)
    {
		foreach ($planter_list AS $k => $v)
		{
			//获取内存中的该种植机信息
			$planter_obj = new PlanterModel($v['planter_id']);
			$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $v['planter_id']);
			$planter_list[$k]['temperature'] = $planter_info['temperature'];
			$planter_list[$k]['outside_temperature'] = $planter_info['outside_temperature'];
			$planter_list[$k]['humidity'] = $planter_info['humidity'];
			$planter_list[$k]['illuminance'] = $planter_info['illuminance'];
			$planter_list[$k]['alarm'] = $planter_info['alarm'];
			$planter_list[$k]['box_state'] = $planter_info['box_state'];
			$planter_list[$k]['last_visit_time'] = $planter_info['last_visit_time'];

			//用户姓名
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('realname');
			$planter_list[$k]['realname'] = $user_info['realname'];

			//模拟种子状态名称
			$seed_state_obj = new SeedStateModel();
			$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_state_id = ' . $v['seed_state_id'], 'state, seed_id');
			$state_name = SeedStateModel::convertState($seed_state_info['state']);

			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemInfo('item_id = ' . $seed_state_info['seed_id'], 'item_name');

			$planter_list[$k]['seed_state_name'] = $item_info['item_name'] . '-' . $state_name;
			//冒险模式
			$planter_list[$k]['is_risk_mode'] = $v['is_risk_mode'] ? '开启' : '关闭';
			$planter_list[$k]['link_user'] = U('/AcpUser/user_detail/user_id/' . $v['user_id']);
		}

		return $planter_list;
    }

	/**
     * 发送命令
     * @author 姜伟
     * @param int $serial 命令序号
     * @param int $command 命令开关，1开0关
     * @return 成功返回1，失败返回0
     * @todo 
     */
    public function sendCommand($serial, $command)
    {
		$planter_info = $this->getPlanterInfo('planter_id = ' . $this->planter_id, 'box_state');
		if (!$planter_info)
		{
			return 0;
		}

		$new_command = '';
		$len = strlen($planter_info['box_state']);
		$serial = intval($serial) - 1;
		$command = intval($command);
		for ($i = 0; $i < $len; $i++)
		{
			if ($i == $serial)
			{
				$new_command .= $command;
			}
			else
			{
				$new_command .= $planter_info['box_state'][$i];
			}
		}

		$arr = array(
			'box_state'		=> $new_command,
		);

		//更新内存中的command
		$this->flushCommand('GPIO', $new_command);

		$state = $this->where('planter_id = ' . $this->planter_id)->save($arr);
		return $state;
	}

	/**
     * 根据key更新内存中的值
     * @author 姜伟
     * @param string $key
     * @param string $value
     * @return boolean
     * @todo 根据key更新内存中的值
     */
    public function flushKey($key, $value)
	{
		if (strtoupper(PHP_OS) == 'LINUX')
		{
			$redis = new Redis();
			$redis->connect('localhost', 6379);
			return $redis->set($key, $value);
		}
		return false;
	}

	/**
     * 更新内存中的command
     * @author 姜伟
     * @param string $key
     * @param string $value
     * @return boolean
     * @todo 更新内存中的command
     */
    public function flushCommand($key, $value)
	{
		if (strtoupper(PHP_OS) == 'LINUX')
		{
			$redis = new Redis();
			$redis->connect('localhost', 6379);
			$command_info = $redis->get('command_' . $this->planter_id);
			$command_info = self::parseCommand($command_info);
			$command_info[$key] = $value;
			return $redis->set('command_' . $this->planter_id, self::generateCommand($command_info));
		}
		return false;
	}

	/**
     * 将数组形式的command_info转化成字符串+END形式
     * @author 姜伟
     * @param str $command_info
     * @return boolean
     * @todo 将数组形式的command_info转化成字符串+END形式
     */
    public static function generateCommand($command_info)
	{
		$command_str = '';
		foreach ($command_info AS $k => $v)
		{
			$command_str .= '&' . $k . '=' . $v;
		}
		$command_str = substr($command_str, 1) . 'END';
		
		return $command_str;
	}

	/**
     * 解析内存中的command
     * @author 姜伟
     * @param str $command_info
     * @return boolean
     * @todo 解析内存中的command
     */
    public static function parseCommand($command_info)
	{
		$command_info = substr($command_info, 0, -3);
		$command_info = explode('&', $command_info);
		$command_arr = array();
		foreach ($command_info AS $k => $v)
		{
			$command = explode('=', $v);
			$command_arr[$command[0]] = $command[1];
		}
		
		return $command_arr;
	}

	/**
     * 判断来访机器是否合法
     * @author 姜伟
     * @param void
     * @return boolean
     * @todo 判断来访机器是否合法
     */
    public function checkVisitValid()
	{
		return true;
	}

	/**
     * 判断用户是否有权限操作当前机器
     * @author 姜伟
     * @param void
     * @return boolean
     * @todo 判断用户是否有权限操作当前机器
     */
    public function checkPriv()
	{
		//先查看当前种植机的直接所有者是否当前用户
		$user_id = intval(session('user_id'));
		$count = $this->where('user_id = ' . $user_id . ' AND planter_id = ' . $this->planter_id)->count();
		if ($count)
		{
			return true;
		}

		//再查看授权表中是否有将该种植机授权给当前用户
		$planter_auth_obj = new PlanterAuthModel();
		$planter_auth_info = $planter_auth_obj->getPlanterAuthInfo('isuse = 1 AND user_id = ' . $user_id . ' AND planter_id = ' . $this->planter_id);
		return $planter_auth_info ? true : false;
	}

	/**
	 * 生成序列号
	 * @author 姜伟
	 * @param void
	 * @return string $serial_num
	 * @todo 生成序列号
	 */
	public function generateSerialNum()
	{
		$serial_num = '';
		$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$valid = false;
		while (!$valid)
		{
			for ($i = 0; $i < 12; $i ++)
			{	
				//6位数字
				$serial_num .= $str[mt_rand(0, 61)];
			}
			//查找数据库，若不存在该序列号，则为有效
			$planter_info = $this->getPlanterInfo('serial_num = "' . $serial_num . '"');
			if ($planter_info)
			{
				//已存在，无效，重新来过
				$serial_num = '';
			}
			else
			{
				//有效，置为true
				$valid = true;
			}
		}

		return $serial_num;
	}

	/**
	 * 生成生产ID
	 * @author 姜伟
	 * @param void
	 * @return string $product_id
	 * @todo 生成生产ID
	 */
	public function generateProductID($no)
	{
		//生成ID
		$product_id = '';
		//序号
		$serial = '00001';
		//年份
		$year = date('y', time());

		//查找数据库，若不存在该序列号，则为有效
		$planter_info = $this->field('product_id')->order('planter_id DESC')->find();
		if ($planter_info && $planter_info['product_id'])
		{
			$product_id = $planter_info['product_id'];
			$serial = substr($product_id, 7, 5);
			$serial = intval($serial) + 1;
			$serial = sprintf('%05d', $serial);
		}

		$product_id = 'ID' . $year . $no . $serial;

		return $product_id;
	}

	/**
	 * 获取批次
	 * @author 姜伟
	 * @param void
	 * @return string $no
	 * @todo 获取批次
	 */
	public function getNextNo()
	{
		//批次
		$no = '001';

		//查找数据库，若不存在该序列号，则为有效
		$planter_info = $this->field('product_id')->order('planter_id DESC')->find();
		if ($planter_info && $planter_info['product_id'])
		{
			$product_id = $planter_info['product_id'];
			$no = substr($product_id, 4, 3);
			$no = intval($no) + 1;
			$no = sprintf('%03d', $no);
		}

		return $no;
	}
}

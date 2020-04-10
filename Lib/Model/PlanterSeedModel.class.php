<?php
/**
 * 种植机种子模型类
 */

class PlanterSeedModel extends Model
{
    // 种植机种子id
    public $planter_seed_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_seed_id 种植机种子ID
     * @return void
     * @todo 初始化种植机种子id
     */
    public function PlanterSeedModel($planter_seed_id)
    {
        parent::__construct('planter_seed');

        if ($planter_seed_id = intval($planter_seed_id))
		{
            $this->planter_seed_id = $planter_seed_id;
		}
    }

    /**
     * 获取种植机种子信息
     * @author 姜伟
     * @param int $planter_seed_id 种植机种子id
     * @param string $fields 要获取的字段名
     * @return array 种植机种子基本信息
     * @todo 根据where查询条件查找种植机种子表中的相关数据并返回
     */
    public function getPlanterSeedInfo($where, $fields = '', $order = '')
    {
		return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * 修改种植机种子信息
     * @author 姜伟
     * @param array $arr 种植机种子信息数组
     * @return boolean 操作结果
     * @todo 修改种植机种子信息
     */
    public function editPlanterSeed($arr)
    {
        return $this->where('planter_seed_id = ' . $this->planter_seed_id)->save($arr);
    }

    /**
     * 添加种植机种子
     * @author 姜伟
     * @param array $arr 种植机种子信息数组
     * @return boolean 操作结果
     * @todo 添加种植机种子
     */
    public function addPlanterSeed($arr)
    {
        if (!is_array($arr)) return false;

		$arr['plant_time'] = time();
        $planter_seed_id = $this->add($arr);

		//写日志
		$planter_log_obj = new PlanterLogModel();
		$planter_log_obj->addPlanterLog(0, $arr['state'], $arr['planter_id'], $arr['seed_id'], $planter_seed_id);

		return $planter_seed_id;
    }

    /**
     * 删除种植机种子
     * @author 姜伟
     * @param int $planter_seed_id 种植机种子ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanterSeed($planter_seed_id)
    {
        if (!is_numeric($planter_seed_id)) return false;
        return $this->where('planter_seed_id = ' . $planter_seed_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种植机种子数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种植机种子数量
     * @todo 根据where子句获取种植机种子数量
     */
    public function getPlanterSeedNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种植机种子信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种植机种子基本信息
     * @todo 根据SQL查询字句查询种植机种子信息
     */
    public function getPlanterSeedList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->join(C('DB_PREFIX') . 'item AS i ON i.item_id = tp_planter_seed.seed_id')->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取种植机种子列表页数据信息列表
     * @author 姜伟
     * @param array $planter_seed_list
     * @return array $planter_seed_list
     * @todo 根据传入的$planter_seed_list获取更详细的种植机种子列表页数据信息列表
     */
    public function getListData($planter_seed_list)
    {
		foreach ($planter_seed_list AS $k => $v)
		{
			$planter_seed_list[$k]['link_planter_seed'] = U('/FrontPlanterSeed/planter_seed_detail/planter_seed_id/' . $v['planter_seed_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$planter_seed_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$planter_seed_list[$k]['status'] = $status;
		}

		return $planter_seed_list;
    }
 
    /**
	 * 根据id获取上一篇、下一篇文章
	 * @param int $id 种植机种子ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回种植机种子信息，否则返回false
	 * @author 姜伟
	 * @todo 根据id获取上一篇、下一篇文章
	 *
	 */
    public function getNextPlanterSeed($id, $fields = '')
    {
		if($id < 0)
		{
			return false;
		}

		//获取种植机种子信息
		$planter_seed_info = $this->getPlanterSeedInfo('planter_seed_id = ' . $id, 'item_id');
		if (!$planter_seed_info)
		{
			return false;
		}
		$item_id = $planter_seed_info['item_id'];

		//获取上一个种植机种子
		$last_planter_seed = $this->field('planter_seed_id, planter_seed_name')->where('item_id = ' . $item_id . ' AND planter_seed_id < ' . $id)->order('planter_seed_id DESC')->limit('0,1')->find();

		//获取下一个种植机种子
		$next_planter_seed = $this->field('planter_seed_id, planter_seed_name')->where('item_id = ' . $item_id . ' AND planter_seed_id > ' . $id)->order('planter_seed_id ASC')->limit('0,1')->find();

		return array(
			'last_planter_seed'	=> $last_planter_seed,
			'next_planter_seed'	=> $next_planter_seed,
		);
    }
 
    /**
	 * 变更种植机内种子状态
	 * @param int $planter_id
	 * @param int $planter_seed_id
	 * @param int $state
	 * @return mixed 成功返回种植机种子信息，否则返回false
	 * @author 姜伟
	 * @todo 变更种植机内种子状态，取出该种植机关联的最新的一条该种子记录，修改之；修改完后写种子状态变更日志
	 *
	 */
    public function setState($planter_id, $planter_seed_id, $state)
    {
		//获取种植机种子信息
		$planter_seed_info = $this->getPlanterSeedInfo('planter_id = ' . $planter_id . ' AND planter_seed_id = ' . $planter_seed_id, 'planter_seed_id, state, seed_id', 'plant_time DESC');
		if (!$planter_seed_info)
		{
			return false;
		}
		$planter_seed_id = $planter_seed_info['planter_seed_id'];

		//获取该植物最后一个状态信息
		$seed_state_obj = new SeedStateModel();
		$seed_state_info = $seed_state_obj->getSeedStateInfo('seed_id = ' . $planter_seed_info['seed_id'], 'state', 'state DESC');
		$last_seed_state = $seed_state_info ? $seed_state_info['state'] : 0;
		$is_reap = $last_seed_state == $state ? 1 : 0;
		$reap_time = $last_seed_state == $state ? time() : 0;

		//修改状态
		$arr = array(
			'state'		=> $state,
			'is_reap'	=> $is_reap,
			'reap_time'	=> $reap_time,
		);
		$this->planter_seed_id = $planter_seed_id;
		$success = $this->editPlanterSeed($arr);

		//写日志
		$planter_log_obj = new PlanterLogModel();
		$planter_log_obj->addPlanterLog($planter_seed_info['state'], $state, $planter_id, $planter_seed_info['seed_id'], $planter_seed_id);

		return $success;
    }
}

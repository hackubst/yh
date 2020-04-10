<?php
/**
 * 种植机种子状态模型类
 */

class PlanterSeedStateModel extends Model
{
    // 种植机种子状态id
    public $planter_seed_state_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_seed_state_id 种植机种子状态ID
     * @return void
     * @todo 初始化种植机种子状态id
     */
    public function PlanterSeedStateModel($planter_seed_state_id)
    {
        parent::__construct('planter_seed_state');

        if ($planter_seed_state_id = intval($planter_seed_state_id))
		{
            $this->planter_seed_state_id = $planter_seed_state_id;
		}
    }

    /**
     * 获取种植机种子状态信息
     * @author 姜伟
     * @param int $planter_seed_state_id 种植机种子状态id
     * @param string $fields 要获取的字段名
     * @return array 种植机种子状态基本信息
     * @todo 根据where查询条件查找种植机种子状态表中的相关数据并返回
     */
    public function getPlanterSeedStateInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改种植机种子状态信息
     * @author 姜伟
     * @param array $arr 种植机种子状态信息数组
     * @return boolean 操作结果
     * @todo 修改种植机种子状态信息
     */
    public function editPlanterSeedState($arr)
    {
        return $this->where('planter_seed_state_id = ' . $this->planter_seed_state_id)->save($arr);
    }

    /**
     * 添加种植机种子状态
     * @author 姜伟
     * @param array $arr 种植机种子状态信息数组
     * @return boolean 操作结果
     * @todo 添加种植机种子状态
     */
    public function addPlanterSeedState($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除种植机种子状态
     * @author 姜伟
     * @param int $planter_seed_state_id 种植机种子状态ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanterSeedState($planter_seed_state_id)
    {
        if (!is_numeric($planter_seed_state_id)) return false;
        return $this->where('planter_seed_state_id = ' . $planter_seed_state_id)->delete();
    }

    /**
     * 根据where子句获取种植机种子状态数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种植机种子状态数量
     * @todo 根据where子句获取种植机种子状态数量
     */
    public function getPlanterSeedStateNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种植机种子状态信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种植机种子状态基本信息
     * @todo 根据SQL查询字句查询种植机种子状态信息
     */
    public function getPlanterSeedStateList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取种植机种子状态列表页数据信息列表
     * @author 姜伟
     * @param array $planter_seed_state_list
     * @return array $planter_seed_state_list
     * @todo 根据传入的$planter_seed_state_list获取更详细的种植机种子状态列表页数据信息列表
     */
    public function getListData($planter_seed_state_list)
    {
		foreach ($planter_seed_state_list AS $k => $v)
		{
			$planter_seed_state_list[$k]['link_planter_seed_state'] = U('/FrontPlanterSeedState/planter_seed_state_detail/planter_seed_state_id/' . $v['planter_seed_state_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$planter_seed_state_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$planter_seed_state_list[$k]['status'] = $status;
		}

		return $planter_seed_state_list;
    }
 
    /**
	 * 根据id获取上一篇、下一篇文章
	 * @param int $id 种植机种子状态ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回种植机种子状态信息，否则返回false
	 * @author 姜伟
	 * @todo 根据id获取上一篇、下一篇文章
	 *
	 */
    public function getNextPlanterSeedState($id, $fields = '')
    {
		if($id < 0)
		{
			return false;
		}

		//获取种植机种子状态信息
		$planter_seed_state_info = $this->getPlanterSeedStateInfo('planter_seed_state_id = ' . $id, 'item_id');
		if (!$planter_seed_state_info)
		{
			return false;
		}
		$item_id = $planter_seed_state_info['item_id'];

		//获取上一个种植机种子状态
		$last_planter_seed_state = $this->field('planter_seed_state_id, planter_seed_state_name')->where('item_id = ' . $item_id . ' AND planter_seed_state_id < ' . $id)->order('planter_seed_state_id DESC')->limit('0,1')->find();

		//获取下一个种植机种子状态
		$next_planter_seed_state = $this->field('planter_seed_state_id, planter_seed_state_name')->where('item_id = ' . $item_id . ' AND planter_seed_state_id > ' . $id)->order('planter_seed_state_id ASC')->limit('0,1')->find();

		return array(
			'last_planter_seed_state'	=> $last_planter_seed_state,
			'next_planter_seed_state'	=> $next_planter_seed_state,
		);
    }

}

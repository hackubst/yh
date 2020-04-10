<?php
/**
 * 种植机操作日志模型类
 */

class PlanterLogModel extends Model
{
    // 种植机操作日志id
    public $planter_log_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_log_id 种植机操作日志ID
     * @return void
     * @todo 初始化种植机操作日志id
     */
    public function PlanterLogModel($planter_log_id)
    {
        parent::__construct('planter_log');

        if ($planter_log_id = intval($planter_log_id))
		{
            $this->planter_log_id = $planter_log_id;
		}
    }

    /**
     * 获取种植机操作日志信息
     * @author 姜伟
     * @param int $planter_log_id 种植机操作日志id
     * @param string $fields 要获取的字段名
     * @return array 种植机操作日志基本信息
     * @todo 根据where查询条件查找种植机操作日志表中的相关数据并返回
     */
    public function getPlanterLogInfo($where, $fields = '', $order = '')
    {
		return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * 修改种植机操作日志信息
     * @author 姜伟
     * @param array $arr 种植机操作日志信息数组
     * @return boolean 操作结果
     * @todo 修改种植机操作日志信息
     */
    public function editPlanterLog($arr)
    {
        return $this->where('planter_log_id = ' . $this->planter_log_id)->save($arr);
    }

    /**
     * 添加种植机操作日志
	 * @author 姜伟
	 * @param int	$start_planter_state
	 * @param int	$end_planter_state
	 * @param int	$planter_id
	 * @param int	$seed_id
	 * @param int	$planter_seed_id
	 * @return void
	 * @todo 记录种植机种子操作前状态，操作后状态，操作人用户ID，操作人IP，操作时间，种植机种子号及备注信息，备注信息如：管理员于2014:02:24 09:50将种植机种子状态由已备货改为已发货
	 */
    public function addPlanterLog($start_planter_state, $end_planter_state, $planter_id, $seed_id, $planter_seed_id)
    {
		//获取种子名称
		$item_obj = new ItemModel();
		$item_info = $item_obj->getItemInfo('item_id = ' . $seed_id, 'item_name');
		if (!$item_info)
		{
			return false;
		}

		$remark = $start_planter_state == 0 ? '用户ID为' . $user_id . '的用户于' . date('Y-m-d H:i:s', time()) . '种下该种子' : '用户ID为' . $user_id . '的用户于' . date('Y-m-d H:i:s', time()) . '将种植机种子状态由' . SeedStateModel::convertState($start_planter_state) . '改为' . SeedStateModel::convertState($end_planter_state);
		//插入数据库的字段
		$arr = array(
			'planter_id'			=> $planter_id,
			'planter_seed_id'		=> $planter_seed_id,
			'seed_id'				=> $seed_id,
			'seed_name'				=> $item_info['item_name'],
			'user_id'				=> intval(session('user_id')),
			'start_seed_state'		=> $start_planter_state,
			'end_seed_state'		=> $end_planter_state,
			'addtime'				=> time(),
			'ip'					=> get_client_ip(),
			'remark'				=> $remark
		);

		$this->add($arr);
    }

    /**
     * 删除种植机操作日志
     * @author 姜伟
     * @param int $planter_log_id 种植机操作日志ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanterLog($planter_log_id)
    {
        if (!is_numeric($planter_log_id)) return false;
        return $this->where('planter_log_id = ' . $planter_log_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种植机操作日志数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种植机操作日志数量
     * @todo 根据where子句获取种植机操作日志数量
     */
    public function getPlanterLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种植机操作日志信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种植机操作日志基本信息
     * @todo 根据SQL查询字句查询种植机操作日志信息
     */
    public function getPlanterLogList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取种植机操作日志列表页数据信息列表
     * @author 姜伟
     * @param array $planter_log_list
     * @return array $planter_log_list
     * @todo 根据传入的$planter_log_list获取更详细的种植机操作日志列表页数据信息列表
     */
    public function getListData($planter_log_list)
    {
		foreach ($planter_log_list AS $k => $v)
		{
			$planter_log_list[$k]['link_planter_log'] = U('/FrontPlanterLog/planter_log_detail/planter_log_id/' . $v['planter_log_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$planter_log_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$planter_log_list[$k]['status'] = $status;
		}

		return $planter_log_list;
    }
}

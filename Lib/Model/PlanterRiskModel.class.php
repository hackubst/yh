<?php
/**
 * 冒险模式模型类
 */

class PlanterRiskModel extends Model
{
    // 冒险模式id
    public $planter_risk_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_risk_id 冒险模式ID
     * @return void
     * @todo 初始化冒险模式id
     */
    public function PlanterRiskModel($planter_risk_id)
    {
        parent::__construct('planter_risk');

        if ($planter_risk_id = intval($planter_risk_id))
		{
            $this->planter_risk_id = $planter_risk_id;
		}
    }

    /**
     * 获取冒险模式信息
     * @author 姜伟
     * @param int $planter_risk_id 冒险模式id
     * @param string $fields 要获取的字段名
     * @return array 冒险模式基本信息
     * @todo 根据where查询条件查找冒险模式表中的相关数据并返回
     */
    public function getPlanterRiskInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改冒险模式信息
     * @author 姜伟
     * @param array $arr 冒险模式信息数组
     * @return boolean 操作结果
     * @todo 修改冒险模式信息
     */
    public function editPlanterRisk($arr)
    {
        return $this->where('planter_risk_id = ' . $this->planter_risk_id)->save($arr);
    }

    /**
     * 添加冒险模式
     * @author 姜伟
     * @param array $arr 冒险模式信息数组
     * @return boolean 操作结果
     * @todo 添加冒险模式
     */
    public function addPlanterRisk($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除冒险模式
     * @author 姜伟
     * @param int $planter_risk_id 冒险模式ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanterRisk($planter_risk_id)
    {
        if (!is_numeric($planter_risk_id)) return false;
        return $this->where('planter_risk_id = ' . $planter_risk_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取冒险模式数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的冒险模式数量
     * @todo 根据where子句获取冒险模式数量
     */
    public function getPlanterRiskNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询冒险模式信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 冒险模式基本信息
     * @todo 根据SQL查询字句查询冒险模式信息
     */
    public function getPlanterRiskList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取冒险模式列表页数据信息列表
     * @author 姜伟
     * @param array $planter_risk_list
     * @return array $planter_risk_list
     * @todo 根据传入的$planter_risk_list获取更详细的冒险模式列表页数据信息列表
     */
    public function getListData($planter_risk_list)
    {
		foreach ($planter_risk_list AS $k => $v)
		{
			$planter_risk_list[$k]['link_planter_risk'] = U('/FrontPlanterRisk/planter_risk_detail/planter_risk_id/' . $v['planter_risk_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$planter_risk_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$planter_risk_list[$k]['status'] = $status;
		}

		return $planter_risk_list;
    }
}

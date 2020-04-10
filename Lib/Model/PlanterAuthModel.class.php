<?php
/**
 * 种植机授权模型类
 */

class PlanterAuthModel extends Model
{
    // 种植机授权id
    public $planter_auth_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_auth_id 种植机授权ID
     * @return void
     * @todo 初始化种植机授权id
     */
    public function PlanterAuthModel($planter_auth_id)
    {
        parent::__construct('planter_auth');

        if ($planter_auth_id = intval($planter_auth_id))
		{
            $this->planter_auth_id = $planter_auth_id;
		}
    }

    /**
     * 获取种植机授权信息
     * @author 姜伟
     * @param int $planter_auth_id 种植机授权id
     * @param string $fields 要获取的字段名
     * @return array 种植机授权基本信息
     * @todo 根据where查询条件查找种植机授权表中的相关数据并返回
     */
    public function getPlanterAuthInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改种植机授权信息
     * @author 姜伟
     * @param array $arr 种植机授权信息数组
     * @return boolean 操作结果
     * @todo 修改种植机授权信息
     */
    public function editPlanterAuth($arr)
    {
        return $this->where('planter_auth_id = ' . $this->planter_auth_id)->save($arr);
    }

    /**
     * 添加种植机授权
     * @author 姜伟
     * @param array $arr 种植机授权信息数组
     * @return boolean 操作结果
     * @todo 添加种植机授权
     */
    public function addPlanterAuth($arr)
    {
        if (!is_array($arr)) return false;

		$arr['auth_time'] = time();

        return $this->add($arr);
    }

    /**
     * 删除种植机授权
     * @author 姜伟
     * @param int $planter_auth_id 种植机授权ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanterAuth($planter_auth_id)
    {
        if (!is_numeric($planter_auth_id)) return false;
        return $this->where('planter_auth_id = ' . $planter_auth_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种植机授权数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种植机授权数量
     * @todo 根据where子句获取种植机授权数量
     */
    public function getPlanterAuthNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种植机授权信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种植机授权基本信息
     * @todo 根据SQL查询字句查询种植机授权信息
     */
    public function getPlanterAuthList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->join('tp_planter AS p ON p.planter_id = tp_planter_auth.planter_id')->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取种植机授权列表页数据信息列表
     * @author 姜伟
     * @param array $planter_auth_list
     * @return array $planter_auth_list
     * @todo 根据传入的$planter_auth_list获取更详细的种植机授权列表页数据信息列表
     */
    public function getListData($planter_auth_list)
    {
		foreach ($planter_auth_list AS $k => $v)
		{
			$planter_auth_list[$k]['link_planter_auth'] = U('/FrontPlanterAuth/planter_auth_detail/planter_auth_id/' . $v['planter_auth_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$planter_auth_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$planter_auth_list[$k]['status'] = $status;
		}

		return $planter_auth_list;
    }
 
    /**
	 * 根据id获取上一篇、下一篇文章
	 * @param int $id 种植机授权ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回种植机授权信息，否则返回false
	 * @author 姜伟
	 * @todo 根据id获取上一篇、下一篇文章
	 *
	 */
    public function getNextPlanterAuth($id, $fields = '')
    {
		if($id < 0)
		{
			return false;
		}

		//获取种植机授权信息
		$planter_auth_info = $this->getPlanterAuthInfo('planter_auth_id = ' . $id, 'item_id');
		if (!$planter_auth_info)
		{
			return false;
		}
		$item_id = $planter_auth_info['item_id'];

		//获取上一个种植机授权
		$last_planter_auth = $this->field('planter_auth_id, planter_auth_name')->where('item_id = ' . $item_id . ' AND planter_auth_id < ' . $id)->order('planter_auth_id DESC')->limit('0,1')->find();

		//获取下一个种植机授权
		$next_planter_auth = $this->field('planter_auth_id, planter_auth_name')->where('item_id = ' . $item_id . ' AND planter_auth_id > ' . $id)->order('planter_auth_id ASC')->limit('0,1')->find();

		return array(
			'last_planter_auth'	=> $last_planter_auth,
			'next_planter_auth'	=> $next_planter_auth,
		);
    }

}

<?php
/**
 * 种植机分享模型类
 */

class PlanterShareModel extends Model
{
    // 种植机分享id
    public $planter_share_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $planter_share_id 种植机分享ID
     * @return void
     * @todo 初始化种植机分享id
     */
    public function PlanterShareModel($planter_share_id)
    {
        parent::__construct('planter_share');

        if ($planter_share_id = intval($planter_share_id))
		{
            $this->planter_share_id = $planter_share_id;
		}
    }

    /**
     * 获取种植机分享信息
     * @author 姜伟
     * @param int $planter_share_id 种植机分享id
     * @param string $fields 要获取的字段名
     * @return array 种植机分享基本信息
     * @todo 根据where查询条件查找种植机分享表中的相关数据并返回
     */
    public function getPlanterShareInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改种植机分享信息
     * @author 姜伟
     * @param array $arr 种植机分享信息数组
     * @return boolean 操作结果
     * @todo 修改种植机分享信息
     */
    public function editPlanterShare($arr)
    {
        return $this->where('planter_share_id = ' . $this->planter_share_id)->save($arr);
    }

    /**
     * 添加种植机分享
     * @author 姜伟
     * @param array $arr 种植机分享信息数组
     * @return boolean 操作结果
     * @todo 添加种植机分享
     */
    public function addPlanterShare($arr)
    {
        if (!is_array($arr)) return false;

		$arr['share_time'] = time();

        return $this->add($arr);
    }

    /**
     * 删除种植机分享
     * @author 姜伟
     * @param int $planter_share_id 种植机分享ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delPlanterShare($planter_share_id)
    {
        if (!is_numeric($planter_share_id)) return false;
        return $this->where('planter_share_id = ' . $planter_share_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种植机分享数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种植机分享数量
     * @todo 根据where子句获取种植机分享数量
     */
    public function getPlanterShareNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种植机分享信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种植机分享基本信息
     * @todo 根据SQL查询字句查询种植机分享信息
     */
    public function getPlanterShareList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->join('tp_planter AS p ON p.planter_id = tp_planter_share.planter_id')->where($where)->order($orderby)->limit($limit)->select();
    }

	/**
	 * 生成授权码
	 * @author 姜伟
	 * @param void
	 * @return string $share_code
	 * @todo 生成授权码
	 */
    public function generateShareCode()
    {
		//生成随机32位字符串
		$tag = false;
		while (!$tag)
		{
			$share_code = randLenString(32, -1);
			$planter_share_info = $this->getPlanterShareInfo('share_code = "' . $share_code . '"', 'planter_share_id');
			if (!$planter_share_info)
			{
				$tag = true;
			}
		}

		return $share_code;
    }
}

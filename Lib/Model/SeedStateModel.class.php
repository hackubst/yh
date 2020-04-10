<?php
/**
 * 种子状态模型类
 */

class SeedStateModel extends Model
{
    // 种子状态id
    public $seed_state_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $seed_state_id 种子状态ID
     * @return void
     * @todo 初始化种子状态id
     */
    public function SeedStateModel($seed_state_id)
    {
        parent::__construct('seed_state');

        if ($seed_state_id = intval($seed_state_id))
		{
            $this->seed_state_id = $seed_state_id;
		}
    }

    /**
     * 获取种子状态信息
     * @author 姜伟
     * @param int $seed_state_id 种子状态id
     * @param string $fields 要获取的字段名
     * @return array 种子状态基本信息
     * @todo 根据where查询条件查找种子状态表中的相关数据并返回
     */
    public function getSeedStateInfo($where, $fields = '', $order = '')
    {
		return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * 修改种子状态信息
     * @author 姜伟
     * @param array $arr 种子状态信息数组
     * @return boolean 操作结果
     * @todo 修改种子状态信息
     */
    public function editSeedState($arr)
    {
        return $this->where('seed_state_id = ' . $this->seed_state_id)->save($arr);
    }

    /**
     * 添加种子状态
     * @author 姜伟
     * @param array $arr 种子状态信息数组
     * @return boolean 操作结果
     * @todo 添加种子状态
     */
    public function addSeedState($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除种子状态
     * @author 姜伟
     * @param int $seed_state_id 种子状态ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delSeedState($seed_state_id)
    {
        if (!is_numeric($seed_state_id)) return false;
        return $this->where('seed_state_id = ' . $seed_state_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种子状态数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种子状态数量
     * @todo 根据where子句获取种子状态数量
     */
    public function getSeedStateNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种子状态信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种子状态基本信息
     * @todo 根据SQL查询字句查询种子状态信息
     */
    public function getSeedStateList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取种子状态列表页数据信息列表
     * @author 姜伟
     * @param array $seed_state_list
     * @return array $seed_state_list
     * @todo 根据传入的$seed_state_list获取更详细的种子状态列表页数据信息列表
     */
    public function getListData($seed_state_list)
    {
		foreach ($seed_state_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['seed_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['seed_id'], 'item_name');
			$seed_state_list[$k]['item_name'] = $item_info['item_name'];
			$seed_state_list[$k]['state_name'] = self::convertState($v['state']);
		}

		return $seed_state_list;
    }

    /**
     * 获取植物状态列表
     * @author 姜伟
     * @param void
     * @return array $state_list
     * @todo 获取植物状态列表
     */
    public static function getStateList()
    {
		$state_list = array(
			'1'		=> '种子期',
			'2'		=> '萌发期',
			'3'		=> '幼苗期',
			'4'		=> '根茎叶期',
			'5'		=> '花芽期',
			'6'		=> '开花期',
			'7'		=> '传粉期',
			'8'		=> '结果期',
			'9'		=> '种子形成期',
		);
		return $state_list;
    }

    /**
     * 种子状态数字转化成文字
     * @author 姜伟
     * @param int $state
     * @return array $state_list
     * @todo 种子状态数字转化成文字
     */
    public static function convertState($state)
    {
		$state_list = self::getStateList();
		return $state_list[intval($state)];
    }
}

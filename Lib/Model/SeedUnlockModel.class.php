<?php
/**
 * 种子解锁模型类
 */

class SeedUnlockModel extends Model
{
    // 种子解锁id
    public $seed_unlock_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $seed_unlock_id 种子解锁ID
     * @return void
     * @todo 初始化种子解锁id
     */
    public function SeedUnlockModel($seed_unlock_id)
    {
        parent::__construct('seed_unlock');

        if ($seed_unlock_id = intval($seed_unlock_id))
		{
            $this->seed_unlock_id = $seed_unlock_id;
		}
    }

    /**
     * 获取种子解锁信息
     * @author 姜伟
     * @param int $seed_unlock_id 种子解锁id
     * @param string $fields 要获取的字段名
     * @return array 种子解锁基本信息
     * @todo 根据where查询条件查找种子解锁表中的相关数据并返回
     */
    public function getSeedUnlockInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改种子解锁信息
     * @author 姜伟
     * @param array $arr 种子解锁信息数组
     * @return boolean 操作结果
     * @todo 修改种子解锁信息
     */
    public function editSeedUnlock($arr)
    {
        return $this->where('seed_unlock_id = ' . $this->seed_unlock_id)->save($arr);
    }

    /**
     * 添加种子解锁
     * @author 姜伟
     * @param array $arr 种子解锁信息数组
     * @return boolean 操作结果
     * @todo 添加种子解锁
     */
    public function addSeedUnlock($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除种子解锁
     * @author 姜伟
     * @param int $seed_unlock_id 种子解锁ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delSeedUnlock($seed_unlock_id)
    {
        if (!is_numeric($seed_unlock_id)) return false;
        return $this->where('seed_unlock_id = ' . $seed_unlock_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取种子解锁数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的种子解锁数量
     * @todo 根据where子句获取种子解锁数量
     */
    public function getSeedUnlockNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询种子解锁信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 种子解锁基本信息
     * @todo 根据SQL查询字句查询种子解锁信息
     */
    public function getSeedUnlockList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取种子解锁列表页数据信息列表
     * @author 姜伟
     * @param array $seed_unlock_list
     * @return array $seed_unlock_list
     * @todo 根据传入的$seed_unlock_list获取更详细的种子解锁列表页数据信息列表
     */
    public function getListData($seed_unlock_list)
    {
		foreach ($seed_unlock_list AS $k => $v)
		{
			$seed_unlock_list[$k]['link_seed_unlock'] = U('/FrontSeedUnlock/seed_unlock_detail/seed_unlock_id/' . $v['seed_unlock_id']);
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
			$seed_unlock_list[$k]['item_name'] = $item_info['item_name'];
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '已下架';
			}
			else
			{
				$status = '上架中';
			}
			$seed_unlock_list[$k]['status'] = $status;
		}

		return $seed_unlock_list;
    }
 
    /**
	 * 根据id获取上一篇、下一篇文章
	 * @param int $id 种子解锁ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回种子解锁信息，否则返回false
	 * @author 姜伟
	 * @todo 根据id获取上一篇、下一篇文章
	 *
	 */
    public function getNextSeedUnlock($id, $fields = '')
    {
		if($id < 0)
		{
			return false;
		}

		//获取种子解锁信息
		$seed_unlock_info = $this->getSeedUnlockInfo('seed_unlock_id = ' . $id, 'item_id');
		if (!$seed_unlock_info)
		{
			return false;
		}
		$item_id = $seed_unlock_info['item_id'];

		//获取上一个种子解锁
		$last_seed_unlock = $this->field('seed_unlock_id, seed_unlock_name')->where('item_id = ' . $item_id . ' AND seed_unlock_id < ' . $id)->order('seed_unlock_id DESC')->limit('0,1')->find();

		//获取下一个种子解锁
		$next_seed_unlock = $this->field('seed_unlock_id, seed_unlock_name')->where('item_id = ' . $item_id . ' AND seed_unlock_id > ' . $id)->order('seed_unlock_id ASC')->limit('0,1')->find();

		return array(
			'last_seed_unlock'	=> $last_seed_unlock,
			'next_seed_unlock'	=> $next_seed_unlock,
		);
    }

}

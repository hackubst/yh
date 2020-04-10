<?php
/**
 * 用户需求Model
 *
 * @author zlf
 *
 */
class UserRequirementModel extends Model
{

    const UNFINISHED = 0;
    const FINISHED   = 1;
    const REJECTED   = 2;

	/**
	 * 添加用户需求
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zlf
	 * @todo 向表tp_user_requirment插入一条记录
	 *
	 */
    public function __construct($id=0)
    {
        parent::__construct('user_requirement');
        $this->id = $id;
    }

	public function addRequirement(array $data)
    {
		$this->create($data);
		return $this->add();
	}
	
	/**
	 * 通过ID获取用户需求信息
	 *
	 * @param int $id 用户需求ID
	 * @return mixed 成功返回用户需求信息，否则返回false
	 * @author zlf
	 * @todo 获取表tp_user_requirement中user_requirement_id为$id的信息
	 *
	 */
    public function getUserRequirementById($id)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('user_requirement_id=' . $id)->find();
    }
    
    /**
     * 根据where子句查询用户需求信息
     * @author zlf
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 用户需求基本信息
     * @todo 根据SQL查询字句查询用户需求信息
     */
    public function getUserRequirementList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }
	

	
	/**
	 * 删除用户需求
	 *
	 * @param int $id 用户需求ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zlf
	 * @todo 删除表tp_user_requirement中user_requirement_id为$id的记录
	 *
	 */
    public function deleteUserRequirement($id)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('user_requirement_id=' . $id)->delete();
    }

    /**
     * 根据where子句获取用户需求数量
     * @author zlf
     * @param string|array $where where子句
     * @return int 满足条件的用户需求数量
     * @todo 根据where子句获取用户需求数量
     */
    public function getUserRequirementNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 获取需求状态列表
     * @author zlf
     * @param void
     * @return array $state_list
     * @todo 获取需求状态列表
     */
    public static function getStateList()
    {
		return array(
			'0'	=> '未解决',
			'1'	=> '已解决',
			'2'	=> '已拒绝',
		);
    }

    /**
     * 获取用户需求列表页数据信息列表
     * @author zlf
     * @param array $user_requirement_list
     * @return array $user_requirement_list
     * @todo 根据传入的$user_requirement_list获取更详细的用户需求列表页数据信息列表
     */
    public function getListData($user_requirement_list)
    {
		foreach ($user_requirement_list AS $k => $v)
		{
			//状态
			$state_list = self::getStateList();
			$state_name = $state_list[$v['state']];
			$requirment = $v['requirement'];
            $requirment = utf8_strlen($requirment) > 8 ? substr_utf8($requirment, 8,0) . '...' : $requirment;

            //用户名
			$user_obj   = new UserModel($v['user_id']);
			$user_info  = $user_obj->getUserInfo('realname, nickname');
            $user_name  = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];

			$user_requirement_list[$k]['requirement_short'] = $requirment;
			$user_requirement_list[$k]['state_name']        = $state_name;
			$user_requirement_list[$k]['user_name']         = $user_name? $user_name:"匿名";
			$user_requirement_list[$k]['user_id']           = $v['user_id']? $v['user_id']: -1;

		}

		return $user_requirement_list;
    }

    public function editUserRequirement($user_requirement_id, $arr)
    {
        if (!$user_requirement_id) return false;
        if (!is_array($arr)) return false;

		return $this->where('user_requirement_id = ' . $user_requirement_id)->save($arr);
    }
}
?>

<?php
/**
 * 用户建议Model
 *
 * @author zhengzhen
 * @date 2014/3/21
 *
 */
class UserSuggestModel extends Model
{
	/**
	 * 添加给点建议
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_user_suggest插入一条记录
	 *
	 */
	public function addAdvice(array $data)
    {
		$data['message_type'] = 1;
		$this->create($data);
		return $this->add();
	}
	
	/**
	 * 添加售后问题
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_user_suggest插入一条记录
	 *
	 */
	public function addService(array $data)
    {
		$data['message_type'] = 2;
		$this->create($data);
		return $this->add();
	}
	
	/**
	 * 修改给点建议
	 *
	 * @param int $id 用户建议ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_user_suggest中user_suggest_id为$id数据为$data
	 *
	 */
	public function saveAdvice($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		$this->create($data);
		return $this->where('user_suggest_id=' . $id)->save();
	}
	
	/**
	 * 修改售后问题
	 *
	 * @param int $id 用户建议ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_user_suggest中user_suggest_id为$id数据为$data
	 *
	 */
	public function saveService($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		$this->create($data);
		return $this->where('user_suggest_id=' . $id)->save();
	}
	
	/**
	 * 通过ID获取用户建议信息
	 *
	 * @param int $id 用户建议ID
	 * @return mixed 成功返回用户建议信息，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_user_suggest中user_suggest_id为$id的信息
	 *
	 */
    public function getUserSuggestById($id)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('user_suggest_id=' . $id)->find();
    }
    
    /**
     * 根据where子句查询意见反馈信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 意见反馈基本信息
     * @todo 根据SQL查询字句查询意见反馈信息
     */
    public function getUserSuggestList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }
	
	/**
	 * 获取用户建议列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回用户建议列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_user_suggest中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getAdviceList($limit = '', $order = '', $fields = '', $where = '')
    {
		$_this = $this;
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		
		if($order)
		{
			$_this = $_this->order($order);
		}
		
		if($fields)
		{
			$_this = $_this->field($fields);
		}
		
		if($where)
		{
			$where .= ' AND message_type=1';
			$_this = $_this->where($where);
		}
		
		return $_this->select();
    }
	
	/**
	 * 获取分页的用户建议列表
	 *
	 * @param string $where 查询条件，默认''
	 * @param int $rows 每页显示数，默认15
	 * @return mixed 成功返回用户建议列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_user_suggest中所有字段，获取$rows条数
	 *
	 */
    public function getAdviceListPage($where = '', $rows = 15)
    {
		$total = $this->where($where)->count();
		if(!$total)
		{
			return false;
		}
		
		//分页处理
		import('ORG.Util.Pagelist');
		$Page = new Pagelist($total, $rows);
		$pagination = $Page->show();
		$limit = $Page->firstRow . ',' . $Page->listRows;
		$order = 'addtime DESC';
		$result = $this->getAdviceList($limit, $order, '', $where);
		if($result)
		{
			$result[] = $pagination;
		}
		return $result;
    }
	
	/**
	 * 获取售后问题列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @param string $order 排序，默认''
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @param string $where 查询条件，默认''
	 * @return mixed 成功返回用户建议列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询表tp_user_suggest中$fields字段，获取$limit条数，以$order排序
	 *
	 */
    public function getServiceList($limit = '', $order = '', $fields = '', $where = '')
    {
		$_this = $this;
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		
		if($order)
		{
			$_this = $_this->order($order);
		}
		
		if($fields)
		{
			$_this = $_this->field($fields);
		}
		
		if($where)
		{
			$where .= ' AND message_type=2';
			$_this = $_this->where($where);
		}
		
		return $_this->select();
    }
	
	/**
	 * 删除用户建议
	 *
	 * @param int $id 用户建议ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_user_suggest中user_suggest_id为$id的记录
	 *
	 */
    public function deleteUserSuggest($id)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('user_suggest_id=' . $id)->delete();
    }

    /**
     * 根据where子句获取意见反馈数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的意见反馈数量
     * @todo 根据where子句获取意见反馈数量
     */
    public function getUserSuggestNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 获取意见状态列表
     * @author 姜伟
     * @param void
     * @return array $state_list
     * @todo 获取意见状态列表
     */
    public static function getStateList()
    {
		return array(
			'0'	=> '未读',
			'1'	=> '无用',
			'2'	=> '有待商榷',
			'3'	=> '有用',
			'4'	=> '将更新到系统中',
			#'5'	=> '已更新到系统中',
		);
    }

    /**
     * 获取意见类型列表
     * @author 姜伟
     * @param void
     * @return array $type_list
     * @todo 获取意见状态列表
     */
    public static function getTypeList()
    {
		return array(
			'1'	=> '给点意见',
			'2'	=> '售后问题',
		);
    }

    /**
     * 获取意见反馈列表页数据信息列表
     * @author 姜伟
     * @param array $user_suggest_list
     * @return array $user_suggest_list
     * @todo 根据传入的$user_suggest_list获取更详细的意见反馈列表页数据信息列表
     */
    public function getListData($user_suggest_list)
    {
		foreach ($user_suggest_list AS $k => $v)
		{
			//状态
			$state_list = self::getStateList();
			$state_name = $state_list[$v['state']];
			$user_suggest_list[$k]['state_name'] = $state_name;

			//用户名称
			$username = '';
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('role_type, realname');
			$role_type = intval($user_info['role_type']);
			if ($role_type == 3)
			{
				$username = '【会员】' . $user_info['realname'];
			}
			elseif ($role_type == 4)
			{
				$username = '【镖师】' . $user_info['realname'];
			}
			$user_suggest_list[$k]['username'] = $username;

			//类型
			$type_list = self::getTypeList();
			$type_name = $type_list[$v['message_type']];
			$user_suggest_list[$k]['message_type_name'] = $type_name;
		}

		return $user_suggest_list;
    }

}
?>
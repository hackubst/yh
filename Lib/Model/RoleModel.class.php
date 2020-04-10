<?php

/**
 * 角色模型，角色也称角色分组，以下统称角色
 * @ access public
 * @ author jiangwei
 * @ Date 2014-03-24
 */
class RoleModel extends Model
{
	/**
	 * 角色ID，对应tp_users_group数据表中的group_id
	 */
    public $group_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param int $group_id 角色ID
     * @return void
     * @todo 初始化数据库，数据表名及角色ID字段
     */
    public function RoleModel($group_id = 0)
    {
		if ($group_id = intval($group_id))
		{
			$this->group_id = $group_id;
		}

		$this->db(0);
		$this->tableName = C('DB_PREFIX') . 'users_group';
    }
    
    /**
     * 获取角色信息，若当前对象的信息为空，则从数据库中取
     * @author 姜伟
     * @param string $fields 字段列表，英文逗号隔开
     * @param string $where where子句
	 * @return 角色信息数组，$this->data
	 * @todo 返回当前对象的data信息数组
     */
    public function getRoleInfo($fields = '', $where = '')
    {
		if (!empty($this->data))
		{
			return $this->data;
		}

		//从数据库中查找当前角色ID的角色信息
		$role_info = array();
		if ($where)
		{
			return $this->field($fields)->where($where)->find();
		}
		elseif ($this->checkFieldValid())
		{
			return $this->field($fields)->where('group_id = ' . $this->group_id)->find();
		}

		return null;
    }

      /**
     * 判断当前对象group_id合法性
     * @author 姜伟
     * @param void
     * @return boolean $valid
	 * @todo 若group_id为合法整数，则返回true，否则返回false
     */
    public function checkFieldValid()
    {
		//过滤group_id
		$group_id = intval($this->group_id);
		if ($group_id)
		{
			return 'group_id = ' . $group_id;
		}

		return false;
    }
     
    /**
     * 设置角色ID
     * @author 姜伟
     * @param int $group_id
     * @return void
	 * @todo 设置当前对象的group_id
     */
    public function setGroupId($group_id)
    {
		$this->group_id = intval($group_id);
    }

    /**
     * 设置角色信息，赋给$data属性
     * @author 姜伟
     * @param array $data，角色信息数组，由控制层检验数据合法性后传入
     * @return 角色信息数组，$this->data
	 * @todo 根据传入的data数组设置当前对象的data属性。
     */
    public function setRoleInfo($data)
    {
		//一级菜单ID
		$id_arr = array();
		$priv_arr = explode(',', $data['priv_str']);
		
		foreach ($priv_arr AS $k => $v)
		{
			$id = substr($v, 0, 2);
			if (!in_array($id, $id_arr))
			{
				$id_arr[] = $id;
			}
		}

		$id_str = implode(',', $id_arr);
		$data['priv_str'] = $data['priv_str'] . ',' . $id_str;
		foreach ($data AS $k => $v)
		{
			$this->__set($k, $v);
		}
    }

    /**
     * 将当前角色信息data保存到数据库
     * @author 姜伟
     * @param void
     * @return int $num 返回数据表中被影响的行数
	 * @todo 将当前对象的data属性数据保存到数据库
     */
    public function saveRoleInfo()
	{
		if (!empty($this->data) && $this->checkFieldValid())
		{
			return $this->where('group_id = ' . $this->group_id)->save($this->data);
		}

		return 0;
	}
  
	/**
	 * 获取所有角色列表
	 * @author 姜伟
	 * @param void
	 * @return int $num 返回数据表中被影响的行数
	 * @todo 获取所有的角色列表
	 */
	public function getRoleList($where='', $order='', $group='')
	{
		$r = $this->where($where)->group($group)->order($order)->limit()->select();
		return $r;
	}
  
	/**
	 * 获取所有角色总数
	 * @author 姜伟
	 * @param void
	 * @return int $num 返回数据表中被影响的行数
	 * @todo 获取所有的角色列表
	 */
	public function getRoleNum($where='')
	{
		$count = 0;
		$count = $this->where($where)->count();
		return $count;
	}

	/**
	 * 添加一个角色
	 * @author 姜伟
	 * @param void
	 * @return int $num 返回数据表中被影响的行数
	 * @todo 添加一个角色
	 */
	public function addRole($data)
	{
		if(!$data || empty($data))
		{
			return false;
		}

		//一级菜单ID
		$id_arr = array();
		$priv_arr = explode(',', $data['priv_str']);
		
		foreach ($priv_arr AS $k => $v)
		{
			$id = substr($v, 0, 2);
			if (!in_array($id, $id_arr))
			{
				$id_arr[] = $id;
			}
		}

		$id_str = implode(',', $id_arr);
		$data['priv_str'] = $data['priv_str'] . ',' . $id_str;

		$group_id = $this->add($data);
		return $group_id;
	}

	/**
	 * 删除一个角色
	 * @author 姜伟
	 * @param void
	 * @return int $num 返回数据表中被影响的行数，-1代表ID无效，-2代表存在关联管理员，无法删除
	 * @todo 删除一个角色，删除之前判断当前角色是否存在关联的管理员，若存在，不允许删除
	 */
	public function deleteRole()
	{
		if ($this->checkFieldValid())
		{
			//查看当前用户表中是否有关联该角色的管理员
			require_once('Lib/Model/AdminModel.class.php');
			$admin_obj = new AdminModel();
			if ($admin_obj->checkRelatedUserExists($this->group_id))
			{
				//存在关联管理员，返回-2
				return -2;
			}

			return $this->where('group_id = ' . $this->group_id)->delete();
		}

		return -1;
	}

	/**
	 * 获取权限列表
	 * @author 姜伟
	 * @param void
	 * @return array $priv_list
	 * @todo 引入权限文件，过滤不必要的字段后，返回权限列表
	 */
	public function getPrivList()
	{
		require('Conf/acp_priv.php');
		$priv_list = array();
		foreach ($admin_menu_file AS $k => $v)
		{
			foreach ($v AS $key => $value)
			if ($key == 'name' || $key == 'menu_list')
			{
				$priv_list[$k][$key] = $value;
			}
		}

		return $priv_list;
	}
}

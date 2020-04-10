<?php
/**
 * 用户角色基类
 * @ access public
 * @ author zhoutao0928@sina.com 、zhoutao@360shop.cc
 * @ Date 2014-03-20
 */
class UsersGroupModel extends Model
{
	protected $tableName = 'users_group';
	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * @access public
	 * @todo 获取所有的角色列表
	 * @return 返回查询结果
	 */
	public function getUserGroupList($where='', $order='')
	{
		$r = $this->where($where)->order($order)->limit()->select();
		return $r;
	}
	
	/**
	 * @access public
	 * @todo 根据角色ID获取角色的详细信息 
	 * @return 返回查询结果
	 */
	public function getGroupInfoByGroupId($group_id)
	{
		if(!$group_id)
		{
			return FALSE;
		}
		$r = $this->where('group_id = '.$group_id)->find();
		return $r;
	}
	
}
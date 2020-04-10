<?php
/**
 * 团购用户表模型类
 */

class GroupBuyUserModel extends Model
{
    private $group_buy_user_id;
    /**
     * 构造函数
     * @author wzg
     * @todo 构造函数
     */
    public function __construct($group_buy_user_id=0)
    {
        parent::__construct();
        $this->group_buy_user_id = $group_buy_user_id;
    }

    public function getGroupBuyUserNum($where='') {
      return $this->where($where)->count();
    }

    /**
     * 添加团购用户表
     * @author wzg
     * @param array $arr_group_buy_user 团购用户表数组
     * @return boolean 操作结果
     * @todo 添加团购用户表
     */
    public function addGroupBuyUser($arr_group_buy_user)
    {
        if (!is_array($arr_group_buy_user)) return false;
        return $this->add($arr_group_buy_user);
    }

    /**
     * 删除团购用户表
     * @author wzg
     * @param string $group_buy_user_id 团购用户表ID
     * @return boolean 操作结果
     * @todo 删除团购用户表
     */
    public function delGroupBuyUser()
    {
        if (!is_numeric($this->group_buy_user_id)) return false;
        return $this->where('group_buy_user_id = ' . $this->group_buy_user_id)->delete();
    }

    /**
     * 更改团购用户表
     * @author wzg
     * @param int $group_buy_user_id 团购用户表ID
     * @param array $arr_group_buy_user 团购用户表数组
     * @return boolean 操作结果
     * @todo 更改团购用户表
     */
    public function setGroupBuyUser($group_buy_user_id, $arr_group_buy_user)
    {
        if (!is_numeric($group_buy_user_id) || !is_array($arr_group_buy_user)) return false;
        return $this->where('group_buy_user_id = ' . $group_buy_user_id)->save($arr_group_buy_user);
    }

    /**
     * 获取团购用户表
     * @author wzg
     * @param int $group_buy_user_id 团购用户表ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 团购用户表
     * @todo 获取团购用户表
     */
    public function getGroupBuyUser($group_buy_user_id, $fields = null)
    {
        if (!is_numeric($group_buy_user_id))   return false;
        return $this->field($fields)->where('group_buy_user_id = ' . $group_buy_user_id)->find();
    }

    /**
     * 获取团购用户表某个字段的信息
     * @author wzg
     * @param int $group_buy_user_id 团购用户表ID
     * @param string $field 查询的字段名
     * @return array 团购用户表
     * @todo 获取团购用户表某个字段的信息
     */
    public function getGroupBuyUserField($group_buy_user_id, $field)
    {
        if (!is_numeric($group_buy_user_id))   return false;
        return $this->where('group_buy_user_id = ' . $group_buy_user_id)->getField($field);
    }

    /**
     * 获取所有团购用户表列表
     * @author wzg
     * @param string $where where子句
     * @return array 团购用户表列表
     * @todo 获取所有团购用户表列表
     */
    public function getGroupBuyUserList($where = null)
    {
        return $this->where($where)->order('serial')->select();
    }
 
    /**
     * 获取分类信息
     * @author wzg
     * @param string $where where子句
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找商品表中的相关数据并返回
     */
    public function getGroupBuyUserInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }
}

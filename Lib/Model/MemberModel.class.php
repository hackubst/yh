<?php
/**
 * 会员模型类
 */

class MemberModel extends Model
{
    // 会员id
    public $member_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $member_id 会员ID
     * @return void
     * @todo 初始化会员id
     */
    public function MemberModel($member_id)
    {
        parent::__construct('member');

        if ($member_id = intval($member_id))
		{
            $this->member_id = $member_id;
		}
    }

    /**
     * 获取会员信息
     * @author 姜伟
     * @param int $member_id 会员id
     * @param string $fields 要获取的字段名
     * @return array 会员基本信息
     * @todo 根据where查询条件查找会员表中的相关数据并返回
     */
    public function getMemberInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改会员信息
     * @author 姜伟
     * @param array $arr 会员信息数组
     * @return boolean 操作结果
     * @todo 修改会员信息
     */
    public function editMember($arr)
    {
        return $this->where('member_id = ' . $this->member_id)->save($arr);
    }

    /**
     * 添加会员
     * @author 姜伟
     * @param array $arr 会员信息数组
     * @return boolean 操作结果
     * @todo 添加会员
     */
    public function addMember($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除会员
     * @author 姜伟
     * @param int $member_id 会员ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delMember($member_id)
    {
        if (!is_numeric($member_id)) return false;
		return $this->where('member_id = ' . $member_id)->delete();
    }

    /**
     * 根据where子句获取会员数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的会员数量
     * @todo 根据where子句获取会员数量
     */
    public function getMemberNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询会员信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 会员基本信息
     * @todo 根据SQL查询字句查询会员信息
     */
    public function getMemberList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取会员列表页数据信息列表
     * @author 姜伟
     * @param array $member_list
     * @return array $member_list
     * @todo 根据传入的$member_list获取更详细的会员列表页数据信息列表
     */
    public function getListData($member_list)
    {
		foreach ($member_list AS $k => $v)
		{
			//用户名称
			$user_obj = new UserModel($v['member_id']);
			$user_info = $user_obj->getUserInfo('nickname, realname');
			$username = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
			$member_list[$k]['username'] = $username;
		}

		return $member_list;
    }
}

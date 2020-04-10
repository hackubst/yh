<?php
/**
 * 推广记录模型类
 * table_name = tp_invite_log
 * py_key = invite_log_id
 */

class InviteLogModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化推广记录id
     */
    public function InviteLogModel()
    {
        parent::__construct('invite_log');
    }

    /**
     * 获取推广记录信息
     * @author 姜伟
     * @param int $invite_log_id 推广记录id
     * @param string $fields 要获取的字段名
     * @return array 推广记录基本信息
     * @todo 根据where查询条件查找推广记录表中的相关数据并返回
     */
    public function getInviteLogInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改推广记录信息
     * @author 姜伟
     * @param array $arr 推广记录信息数组
     * @return boolean 操作结果
     * @todo 修改推广记录信息
     */
    public function editInviteLog($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加推广记录
     * @author 姜伟
     * @param array $arr 推广记录信息数组
     * @return boolean 操作结果
     * @todo 添加推广记录
     */
    public function addInviteLog($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除推广记录
     * @author 姜伟
     * @param int $invite_log_id 推广记录ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delInviteLog($invite_log_id,$opt = false)
    {
        if (!is_numeric($invite_log_id)) return false;
        if($opt)
        {
            return $this->where('invite_log_id = ' . $invite_log_id)->delete();
        }else{
           return $this->where('invite_log_id = ' . $invite_log_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取推广记录数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的推广记录数量
     * @todo 根据where子句获取推广记录数量
     */
    public function getInviteLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询推广记录信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 推广记录基本信息
     * @todo 根据SQL查询字句查询推广记录信息
     */
    public function getInviteLogList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getInviteLogField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取推广记录列表页数据信息列表
     * @author 姜伟
     * @param array $InviteLog_list
     * @return array $InviteLog_list
     * @todo 根据传入的$InviteLog_list获取更详细的推广记录列表页数据信息列表
     */
    public function getListData($invite_log_list)
    {
        foreach ($invite_log_list as $k => $v) {
            
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname,id','user_id ='.$v['user_id']);
            $invite_log_list[$k]['nickname'] = $user_info['nickname'] ? : '';
            $invite_log_list[$k]['id'] = $user_info['id'] ? : '';
        }
        return $invite_log_list;
    }

}

<?php
/**
 * 管理员操作记录模型类
 * table_name = tp_admin_log
 * py_key = admin_log_id
 */

class AdminLogModel extends Model
{
    const LOGIN = 1;//管理员登陆
    const RANK = 2 ;// 修改排行榜
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化管理员操作记录id
     */
    public function AdminLogModel()
    {
        parent::__construct('admin_log');
    }

    /**
     * 获取管理员操作记录信息
     * @author 姜伟
     * @param int $admin_log_id 管理员操作记录id
     * @param string $fields 要获取的字段名
     * @return array 管理员操作记录基本信息
     * @todo 根据where查询条件查找管理员操作记录表中的相关数据并返回
     */
    public function getAdminLogInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改管理员操作记录信息
     * @author 姜伟
     * @param array $arr 管理员操作记录信息数组
     * @return boolean 操作结果
     * @todo 修改管理员操作记录信息
     */
    public function editAdminLog($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
            
        return $this->where($where)->save($arr);
    }

    /**
     * 添加管理员操作记录
     * @author 姜伟
     * @param array $arr 管理员操作记录信息数组
     * @return boolean 操作结果
     * @todo 添加管理员操作记录
     */
    public function addAdminLog($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除管理员操作记录
     * @author 姜伟
     * @param int $admin_log_id 管理员操作记录ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delAdminLog($admin_log_id,$opt = false)
    {
        if (!is_numeric($admin_log_id)) return false;
        if($opt)
        {
            return $this->where('admin_log_id = ' . $admin_log_id)->delete();
        }else{
           return $this->where('admin_log_id = ' . $admin_log_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取管理员操作记录数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的管理员操作记录数量
     * @todo 根据where子句获取管理员操作记录数量
     */
    public function getAdminLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询管理员操作记录信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 管理员操作记录基本信息
     * @todo 根据SQL查询字句查询管理员操作记录信息
     */
    public function getAdminLogList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getAdminLogField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取管理员操作记录列表页数据信息列表
     * @author 姜伟
     * @param array $AdminLog_list
     * @return array $AdminLog_list
     * @todo 根据传入的$AdminLog_list获取更详细的管理员操作记录列表页数据信息列表
     */
    public function getListData($AdminLog_list)
    {
        
    }

}

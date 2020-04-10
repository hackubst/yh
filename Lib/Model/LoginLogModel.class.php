<?php
/**
 * 登录日志模型类
 */

class LoginLogModel extends Model
{
    // 登录日志id
    public $login_log_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $login_log_id 登录日志ID
     * @return void
     * @todo 初始化登录日志id
     */
    public function LoginLogModel($login_log_id)
    {
        parent::__construct('login_log');

        if ($login_log_id = intval($login_log_id))
		{
            $this->login_log_id = $login_log_id;
		}
    }

    /**
     * 获取登录日志信息
     * @author 姜伟
     * @param int $login_log_id 登录日志id
     * @param string $fields 要获取的字段名
     * @return array 登录日志基本信息
     * @todo 根据where查询条件查找登录日志表中的相关数据并返回
     */
    public function getLoginLogInfo($where, $fields = '',$order = '')
    {
		return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * 修改登录日志信息
     * @author 姜伟
     * @param array $arr 登录日志信息数组
     * @return boolean 操作结果
     * @todo 修改登录日志信息
     */
    public function editLoginLog($arr)
    {
        return $this->where('login_log_id = ' . $this->login_log_id)->save($arr);
    }

    /**
     * 添加登录日志
     * @author 姜伟
     * @param array $arr 登录日志信息数组
     * @return boolean 操作结果
     * @todo 添加登录日志
     */
    public function addLoginLog($arr)
    {
		$arr['login_time'] = time();
		$arr['user_id'] = intval(session('user_id'));
		$arr['ip'] = get_client_ip();
		$area_info = getIPSource($arr['ip']);
		$arr['ip_address'] = $area_info['province_name'] . $area_info['city_name'];

        $login_log_id = $this->add($arr);
log_file('addLoginLog sql = ' . $this->getLastSql(), 'addLoginLog');
return $login_log_id;
    }

    /**
     * 添加用户登录日志
     * @author 姜伟
     * @param array $arr 登录日志信息数组
     * @return boolean 操作结果
     * @todo 添加登录日志
     */
    public function addApiLoginLog($user_id,$status)
    {
        $arr['login_time'] = time();
        $arr['user_id'] = $user_id;
        $arr['ip'] = get_client_ip();
        $area_info = getCityByIp($arr['ip']);
        $arr['ip_address'] = $area_info['province_name'] . $area_info['city_name'];
        $arr['status'] = $status;
        $login_log_id = $this->add($arr);
log_file('addLoginLog sql = ' . $this->getLastSql(), 'addLoginLog');
return $login_log_id;
    }

    /**
     * 删除登录日志
     * @author 姜伟
     * @param int $login_log_id 登录日志ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delLoginLog($login_log_id)
    {
        if (!is_numeric($login_log_id)) return false;
        return $this->where('login_log_id = ' . $login_log_id)->delete();
    }

    /**
     * 根据where子句获取登录日志数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的登录日志数量
     * @todo 根据where子句获取登录日志数量
     */
    public function getLoginLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询登录日志信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 登录日志基本信息
     * @todo 根据SQL查询字句查询登录日志信息
     */
    public function getLoginLogList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取登录日志列表页数据信息列表
     * @author 姜伟
     * @param array $login_log_list
     * @return array $login_log_list
     * @todo 根据传入的$login_log_list获取更详细的登录日志列表页数据信息列表
     */
    public function getListData($login_log_list)
    {
		foreach ($login_log_list AS $k => $v)
		{
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname');
			$login_log_list[$k]['nickname'] = $user_info['nickname'];
		}

		return $login_log_list;
    }
}

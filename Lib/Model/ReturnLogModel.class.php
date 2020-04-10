<?php
/**
 * 返利记录模型类
 * table_name = tp_return_log
 * py_key = return_log_id
 */

class ReturnLogModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化返利记录id
     */
    public function ReturnLogModel()
    {
        parent::__construct('return_log');
    }

    /**
     * 获取返利记录信息
     * @author 姜伟
     * @param int $return_log_id 返利记录id
     * @param string $fields 要获取的字段名
     * @return array 返利记录基本信息
     * @todo 根据where查询条件查找返利记录表中的相关数据并返回
     */
    public function getReturnLogInfo($where, $fields = '',$order = '')
    {
		return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * 修改返利记录信息
     * @author 姜伟
     * @param array $arr 返利记录信息数组
     * @return boolean 操作结果
     * @todo 修改返利记录信息
     */
    public function editReturnLog($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加返利记录
     * @author 姜伟
     * @param array $arr 返利记录信息数组
     * @return boolean 操作结果
     * @todo 添加返利记录
     */
    public function addReturnLog($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除返利记录
     * @author 姜伟
     * @param int $return_log_id 返利记录ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delReturnLog($return_log_id,$opt = false)
    {
        if (!is_numeric($return_log_id)) return false;
        if($opt)
        {
            return $this->where('return_log_id = ' . $return_log_id)->delete();
        }else{
           return $this->where('return_log_id = ' . $return_log_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取返利记录数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的返利记录数量
     * @todo 根据where子句获取返利记录数量
     */
    public function getReturnLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询返利记录信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 返利记录基本信息
     * @todo 根据SQL查询字句查询返利记录信息
     */
    public function getReturnLogList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getReturnLogField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取返利记录列表页数据信息列表
     * @author 姜伟
     * @param array $ReturnLog_list
     * @return array $ReturnLog_list
     * @todo 根据传入的$ReturnLog_list获取更详细的返利记录列表页数据信息列表
     */
    public function getListData($return_log_list)
    {
        foreach ($return_log_list as $k => $v) {
            switch ($v['return_type']) {
                case MarketingRuleModel::WEEKLOSS :
                    $return_type = '亏损返利';
                    break;
                case MarketingRuleModel::DAILYCHARGE :
                    $return_type = '首充返利';
                    break;
                case MarketingRuleModel::BETTING :
                    $return_type = '下线投注返利';
                    break;
                case MarketingRuleModel::SELF_BETTING :
                    $return_type = '有效流水投注返利';
                    break;
                default:
                    # code...
                    break;
            }
            $return_log_list[$k]['return_type'] = $return_type;
            $return_log_list[$k]['addtime_str'] = date('Y-m-d H:i:s',$v['addtime']);
        }
        return $return_log_list;
    }

    public function getListDataResult($return_log)
    {
        $user_obj = new UserModel();
        foreach ($return_log as $k => $v)
        {
            $return_log[$k]['lower_id'] = $user_obj->getParamUserInfo('user_id ='.$v['lower_id'])['id'] ? : '';
            $return_log[$k]['id'] = $user_obj->getParamUserInfo('user_id ='.$v['user_id'])['id'] ? : '';
            $return_log[$k]['money'] = feeHandle($v['money']);
        }
        return $return_log;
    }

}

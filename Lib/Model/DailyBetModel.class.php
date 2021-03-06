<?php
/**
 * 每日投注模型类
 * table_name = tp_daily_win
 * py_key = daily_win_id
 */

class DailyBetModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化每日投注id
     */
    public function DailyBetModel()
    {
        parent::__construct('daily_bet');
    }

    /**
     * 获取每日投注信息
     * @author 姜伟
     * @param int $daily_win_id 每日投注id
     * @param string $fields 要获取的字段名
     * @return array 每日投注基本信息
     * @todo 根据where查询条件查找每日投注表中的相关数据并返回
     */
    public function getDailyBetInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改每日投注信息
     * @author 姜伟
     * @param array $arr 每日投注信息数组
     * @return boolean 操作结果
     * @todo 修改每日投注信息
     */
    public function editDailyBet($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加每日投注
     * @author 姜伟
     * @param array $arr 每日投注信息数组
     * @return boolean 操作结果
     * @todo 添加每日投注
     */
    public function addDailyBet($arr)
    {
        if (!is_array($arr)) return false;

        return $this->add($arr);
    }

    /**
     * 删除每日投注
     * @author 姜伟
     * @param int $daily_win_id 每日投注ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delDailyBet($daily_win_id,$opt = false)
    {
        if (!is_numeric($daily_win_id)) return false;
        if($opt)
        {
            return $this->where('daily_win_id = ' . $daily_win_id)->delete();
        }else{
           return $this->where('daily_win_id = ' . $daily_win_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取每日投注数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的每日投注数量
     * @todo 根据where子句获取每日投注数量
     */
    public function getDailyBetNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询每日投注信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 每日投注基本信息
     * @todo 根据SQL查询字句查询每日投注信息
     */
    public function getDailyBetList($fields = '', $where = '', $orderby = '', $group = '',$limit = null)
    {
        $limit = $limit ? '': $limit ;
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit($limit)->select();
    }

    public function getDailyBetAllList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getDailyBetField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取每日投注列表页数据信息列表
     * @author 姜伟
     * @param array $DailyBet_list
     * @return array $DailyBet_list
     * @todo 根据传入的$DailyBet_list获取更详细的每日投注列表页数据信息列表
     */
    public function getListData($daily_win_list)
    {
        if(count($daily_win_list) == 1 && $daily_win_list[0]['user_id'] == 0)
        {
            return array();
        }
        foreach ($daily_win_list as $k => $v) {
        
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname','user_id ='.$v['user_id']);

            $daily_win_list[$k]['nickname'] = $user_info['nickname'];
        }
        return $daily_win_list;
    }

}

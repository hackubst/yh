<?php
/**
 * 分销商利润模型类
 * table_name = tp_agent_income
 * py_key = agent_income_id
 */

class AgentIncomeModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化分销商利润id
     */
    public function AgentIncomeModel()
    {
        parent::__construct('agent_income');
    }

    /**
     * 获取分销商利润信息
     * @author 姜伟
     * @param int $agent_income_id 分销商利润id
     * @param string $fields 要获取的字段名
     * @return array 分销商利润基本信息
     * @todo 根据where查询条件查找分销商利润表中的相关数据并返回
     */
    public function getAgentIncomeInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改分销商利润信息
     * @author 姜伟
     * @param array $arr 分销商利润信息数组
     * @return boolean 操作结果
     * @todo 修改分销商利润信息
     */
    public function editAgentIncome($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加分销商利润
     * @author 姜伟
     * @param array $arr 分销商利润信息数组
     * @return boolean 操作结果
     * @todo 添加分销商利润
     */
    public function addAgentIncome($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除分销商利润
     * @author 姜伟
     * @param int $agent_income_id 分销商利润ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delAgentIncome($agent_income_id,$opt = false)
    {
        if (!is_numeric($agent_income_id)) return false;
        if($opt)
        {
            return $this->where('agent_income_id = ' . $agent_income_id)->delete();
        }else{
           return $this->where('agent_income_id = ' . $agent_income_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取分销商利润数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的分销商利润数量
     * @todo 根据where子句获取分销商利润数量
     */
    public function getAgentIncomeNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询分销商利润信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 分销商利润基本信息
     * @todo 根据SQL查询字句查询分销商利润信息
     */
    public function getAgentIncomeList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getAgentIncomeField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取分销商利润列表页数据信息列表
     * @author 姜伟
     * @param array $AgentIncome_list
     * @return array $AgentIncome_list
     * @todo 根据传入的$AgentIncome_list获取更详细的分销商利润列表页数据信息列表
     */
    public function getListData($AgentIncome_list)
    {
        foreach ($AgentIncome_list as $k => $v) {

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('realname,id','user_id ='.$v['agent_id']);
            if($user_info)
            {
                $AgentIncome_list[$k]['nickname'] = $user_info['realname'];
                $AgentIncome_list[$k]['id'] = $user_info['id'];
            }
            $AgentIncome_list[$k]['gain_money'] = feeHandle($v['gain_money']);

        }
        return $AgentIncome_list;
    }

}

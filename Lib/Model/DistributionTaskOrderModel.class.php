<?php
/**
 * 任务订单记录模型类
 * table_name = tp_distribution_task_order
 * py_key = distribution_task_order_id
 */

class DistributionTaskOrderModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化任务订单记录id
     */
    public function DistributionTaskOrderModel()
    {
        parent::__construct('distribution_task_order');
    }

    /**
     * 获取任务订单记录信息
     * @author 姜伟
     * @param int $distribution_task_order_id 任务订单记录id
     * @param string $fields 要获取的字段名
     * @return array 任务订单记录基本信息
     * @todo 根据where查询条件查找任务订单记录表中的相关数据并返回
     */
    public function getDistributionTaskOrderInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改任务订单记录信息
     * @author 姜伟
     * @param array $arr 任务订单记录信息数组
     * @return boolean 操作结果
     * @todo 修改任务订单记录信息
     */
    public function editDistributionTaskOrder($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加任务订单记录
     * @author 姜伟
     * @param array $arr 任务订单记录信息数组
     * @return boolean 操作结果
     * @todo 添加任务订单记录
     */
    public function addDistributionTaskOrder($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除任务订单记录
     * @author 姜伟
     * @param int $distribution_task_order_id 任务订单记录ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delDistributionTaskOrder($distribution_task_order_id,$opt = false)
    {
        if (!is_numeric($distribution_task_order_id)) return false;
        if($opt)
        {
            return $this->where('distribution_task_order_id = ' . $distribution_task_order_id)->delete();
        }else{
           return $this->where('distribution_task_order_id = ' . $distribution_task_order_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取任务订单记录数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的任务订单记录数量
     * @todo 根据where子句获取任务订单记录数量
     */
    public function getDistributionTaskOrderNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询任务订单记录信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 任务订单记录基本信息
     * @todo 根据SQL查询字句查询任务订单记录信息
     */
    public function getDistributionTaskOrderList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getDistributionTaskOrderField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取任务订单记录列表页数据信息列表
     * @author 姜伟
     * @param array $DistributionTaskOrder_list
     * @return array $DistributionTaskOrder_list
     * @todo 根据传入的$DistributionTaskOrder_list获取更详细的任务订单记录列表页数据信息列表
     */
    public function getListData($DistributionTaskOrder_list)
    {
        
    }

}

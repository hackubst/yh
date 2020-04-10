<?php
/**
 * 机器人表模型类
 * table_name = tp_robot
 * py_key = robot_id
 */

class RobotModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化机器人表id
     */
    public function RobotModel()
    {
        parent::__construct('robot');
    }

    /**
     * 获取机器人表信息
     * @author 姜伟
     * @param int $robot_id 机器人表id
     * @param string $fields 要获取的字段名
     * @return array 机器人表基本信息
     * @todo 根据where查询条件查找机器人表表中的相关数据并返回
     */
    public function getRobotInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改机器人表信息
     * @author 姜伟
     * @param array $arr 机器人表信息数组
     * @return boolean 操作结果
     * @todo 修改机器人表信息
     */
    public function editRobot($where='',$arr)
    {
        if (!is_array($arr)) return false;
        return $this->where($where)->save($arr);
    }

    /**
     * 添加机器人表
     * @author 姜伟
     * @param array $arr 机器人表信息数组
     * @return boolean 操作结果
     * @todo 添加机器人表
     */
    public function addRobot($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除机器人表
     * @author 姜伟
     * @param int $robot_id 机器人表ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delRobot($robot_id,$opt = false)
    {
        if (!is_numeric($robot_id)) return false;
        if($opt)
        {
            return $this->where('robot_id = ' . $robot_id)->delete();
        }else{
           return $this->where('robot_id = ' . $robot_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取机器人表数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的机器人表数量
     * @todo 根据where子句获取机器人表数量
     */
    public function getRobotNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询机器人表信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 机器人表基本信息
     * @todo 根据SQL查询字句查询机器人表信息
     */
    public function getRobotList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getRobotField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取机器人表列表页数据信息列表
     * @author 姜伟
     * @param array $Robot_list
     * @return array $Robot_list
     * @todo 根据传入的$Robot_list获取更详细的机器人表列表页数据信息列表
     */
    public function getListData($Robot_list)
    {
        
    }

}

<?php
/**
 * 等级模型类
 * table_name = tp_level
 * py_key = level_id
 */

class LevelModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化等级id
     */
    public function LevelModel()
    {
        parent::__construct('level');
    }

    /**
     * 获取等级信息
     * @author 姜伟
     * @param int $level_id 等级id
     * @param string $fields 要获取的字段名
     * @return array 等级基本信息
     * @todo 根据where查询条件查找等级表中的相关数据并返回
     */
    public function getLevelInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改等级信息
     * @author 姜伟
     * @param array $arr 等级信息数组
     * @return boolean 操作结果
     * @todo 修改等级信息
     */
    public function editLevel($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加等级
     * @author 姜伟
     * @param array $arr 等级信息数组
     * @return boolean 操作结果
     * @todo 添加等级
     */
    public function addLevel($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除等级
     * @author 姜伟
     * @param int $level_id 等级ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delLevel($level_id,$opt = false)
    {
        if (!is_numeric($level_id)) return false;
        if($opt)
        {
            return $this->where('level_id = ' . $level_id)->delete();
        }else{
           return $this->where('level_id = ' . $level_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取等级数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的等级数量
     * @todo 根据where子句获取等级数量
     */
    public function getLevelNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询等级信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 等级基本信息
     * @todo 根据SQL查询字句查询等级信息
     */
    public function getLevelList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getLevelField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取等级列表页数据信息列表
     * @author 姜伟
     * @param array $Level_list
     * @return array $Level_list
     * @todo 根据传入的$Level_list获取更详细的等级列表页数据信息列表
     */
    public function getListData($Level_list)
    {
        
    }

}

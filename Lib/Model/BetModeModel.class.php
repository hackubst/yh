<?php
/**
 * 投注模式模型类
 * table_name = tp_bet_mode
 * py_key = bet_mode_id
 */

class BetModeModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化投注模式id
     */
    public function BetModeModel()
    {
        parent::__construct('bet_mode');
    }

    /**
     * 获取投注模式信息
     * @author 姜伟
     * @param int $bet_mode_id 投注模式id
     * @param string $fields 要获取的字段名
     * @return array 投注模式基本信息
     * @todo 根据where查询条件查找投注模式表中的相关数据并返回
     */
    public function getBetModeInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改投注模式信息
     * @author 姜伟
     * @param array $arr 投注模式信息数组
     * @return boolean 操作结果
     * @todo 修改投注模式信息
     */
    public function editBetMode($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加投注模式
     * @author 姜伟
     * @param array $arr 投注模式信息数组
     * @return boolean 操作结果
     * @todo 添加投注模式
     */
    public function addBetMode($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除投注模式
     * @author 姜伟
     * @param int $bet_mode_id 投注模式ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delBetMode($bet_mode_id,$opt = false)
    {
        if (!is_numeric($bet_mode_id)) return false;
        if($opt)
        {
            return $this->where('bet_mode_id = ' . $bet_mode_id)->delete();
        }else{
           return $this->where('bet_mode_id = ' . $bet_mode_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取投注模式数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的投注模式数量
     * @todo 根据where子句获取投注模式数量
     */
    public function getBetModeNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询投注模式信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 投注模式基本信息
     * @todo 根据SQL查询字句查询投注模式信息
     */
    public function getBetModeList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }
    public function getBetModeListALL($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getBetModeField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取投注模式列表页数据信息列表
     * @author 姜伟
     * @param array $BetMode_list
     * @return array $BetMode_list
     * @todo 根据传入的$BetMode_list获取更详细的投注模式列表页数据信息列表
     */
    public function getListData($bet_mode_list)
    {
        foreach ($bet_mode_list as $k => $v) {

//            $bet_mode_list[$k]['bet_json'] = json_decode($v['bet_json'],true);
        }
        return $bet_mode_list;
    }

}

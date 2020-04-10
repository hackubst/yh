<?php
/**
 * 红包领取记录模型类
 * table_name = tp_red_packet_log
 * py_key = red_packet_log_id
 */

class RedPacketLogModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化红包领取记录id
     */
    public function RedPacketLogModel()
    {
        parent::__construct('red_packet_log');
    }

    /**
     * 获取红包领取记录信息
     * @author 姜伟
     * @param int $red_packet_log_id 红包领取记录id
     * @param string $fields 要获取的字段名
     * @return array 红包领取记录基本信息
     * @todo 根据where查询条件查找红包领取记录表中的相关数据并返回
     */
    public function getRedPacketLogInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改红包领取记录信息
     * @author 姜伟
     * @param array $arr 红包领取记录信息数组
     * @return boolean 操作结果
     * @todo 修改红包领取记录信息
     */
    public function editRedPacketLog($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加红包领取记录
     * @author 姜伟
     * @param array $arr 红包领取记录信息数组
     * @return boolean 操作结果
     * @todo 添加红包领取记录
     */
    public function addRedPacketLog($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除红包领取记录
     * @author 姜伟
     * @param int $red_packet_log_id 红包领取记录ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delRedPacketLog($red_packet_log_id,$opt = false)
    {
        if (!is_numeric($red_packet_log_id)) return false;
        if($opt)
        {
            return $this->where('red_packet_log_id = ' . $red_packet_log_id)->delete();
        }else{
           return $this->where('red_packet_log_id = ' . $red_packet_log_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取红包领取记录数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的红包领取记录数量
     * @todo 根据where子句获取红包领取记录数量
     */
    public function getRedPacketLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询红包领取记录信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 红包领取记录基本信息
     * @todo 根据SQL查询字句查询红包领取记录信息
     */
    public function getRedPacketLogList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getRedPacketLogField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取红包领取记录列表页数据信息列表
     * @author 姜伟
     * @param array $RedPacketLog_list
     * @return array $RedPacketLog_list
     * @todo 根据传入的$RedPacketLog_list获取更详细的红包领取记录列表页数据信息列表
     */
    public function getListData($red_packet_log_list)
    {
        foreach ($red_packet_log_list as $k => $v) {

            $red_packet_obj = new RedPacketModel();
            $red_packet_info = $red_packet_obj->getRedPacketInfo('red_packet_id ='.$v['red_packet_id'],'type,title,user_id');

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname','user_id = '.$red_packet_info['user_id']);

            $red_packet_log_list[$k]['nickname'] = $user_info['nickname'] ? : '';
            $red_packet_log_list[$k]['type'] = $red_packet_info['type'] ? : '';
            $red_packet_log_list[$k]['title'] = $red_packet_info['title'] ? : '';
            $red_packet_log_list[$k]['money'] = feeHandle($red_packet_log_list[$k]['money']);
        }
        return $red_packet_log_list;
    }

    //后台数据详情
    public function getDataList($red_packet_log_list)
    {
        foreach ($red_packet_log_list as $k => $v) {

            $red_packet_obj = new RedPacketModel();
            $red_packet_info = $red_packet_obj->getRedPacketInfo('red_packet_id ='.$v['red_packet_id'],'type,title,user_id');

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname,id','user_id = '.$v['user_id']);

            $red_packet_log_list[$k]['nickname'] = $user_info['nickname'] ? : '';
            $red_packet_log_list[$k]['id'] = $user_info['id'] ? : '';
            $red_packet_log_list[$k]['type'] = $red_packet_info['type'] ? : '';
            $red_packet_log_list[$k]['title'] = $red_packet_info['title'] ? : '';
            $red_packet_log_list[$k]['money'] = feeHandle($red_packet_log_list[$k]['money']);
        }
        return $red_packet_log_list;
    }

}

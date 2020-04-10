<?php
/**
 * 红包模型类
 * table_name = tp_red_packet
 * py_key = red_packet_id
 */

class RedPacketModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化红包id
     */
    public function RedPacketModel()
    {
        parent::__construct('red_packet');
    }

    /**
     * 获取红包信息
     * @author 姜伟
     * @param int $red_packet_id 红包id
     * @param string $fields 要获取的字段名
     * @return array 红包基本信息
     * @todo 根据where查询条件查找红包表中的相关数据并返回
     */
    public function getRedPacketInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改红包信息
     * @author 姜伟
     * @param array $arr 红包信息数组
     * @return boolean 操作结果
     * @todo 修改红包信息
     */
    public function editRedPacket($where='',$arr)
    {
        if (!is_array($arr)) return false;
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加红包
     * @author 姜伟
     * @param array $arr 红包信息数组
     * @return boolean 操作结果
     * @todo 添加红包
     */
    public function addRedPacket($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除红包
     * @author 姜伟
     * @param int $red_packet_id 红包ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delRedPacket($red_packet_id,$opt = false)
    {
        if (!is_numeric($red_packet_id)) return false;
        if($opt)
        {
            return $this->where('red_packet_id = ' . $red_packet_id)->delete();
        }else{
           return $this->where('red_packet_id = ' . $red_packet_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取红包数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的红包数量
     * @todo 根据where子句获取红包数量
     */
    public function getRedPacketNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询红包信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 红包基本信息
     * @todo 根据SQL查询字句查询红包信息
     */
    public function getRedPacketList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getRedPacketField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取红包列表页数据信息列表
     * @author 姜伟
     * @param array $RedPacket_list
     * @return array $RedPacket_list
     * @todo 根据传入的$RedPacket_list获取更详细的红包列表页数据信息列表
     */
    public function getListData($red_packet_list)
    {
        foreach ($red_packet_list as $k => $v) {
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname,id','user_id ='.$v['user_id']);
            $red_packet_list[$k]['nickname'] = $user_info['nickname'] ? : '';
            $red_packet_list[$k]['id'] = $user_info['id'] ? : '';
            $red_packet_list[$k]['red_jiami_id'] = url_jiami(strval($v['red_packet_id'])) ? : '';
            //feeHandle
            $red_packet_list[$k]['total_money'] = feeHandle( $red_packet_list[$k]['total_money']);
            $red_packet_list[$k]['each_money'] = feeHandle( $red_packet_list[$k]['each_money']);
            $red_packet_list[$k]['residue_money'] = feeHandle( $red_packet_list[$k]['residue_money']);


        }
        return $red_packet_list;
    }

    /**
     * 加密红包id
     * @author 姜伟
     * @param array $RedPacket_list
     * @return array $RedPacket_list
     * @todo 根据传入的$RedPacket_list获取更详细的红包列表页数据信息列表
     */
    public function getJiaMiData($red_packet_list)
    {

        foreach ($red_packet_list as $k => $v) {
    
            $red_packet_list[$k]['jiami_red_packet_id'] = url_jiami(strval($v['red_packet_id']));
            $red_packet_list[$k]['residue_money'] = feeHandle($red_packet_list[$k]['residue_money']);
            $red_packet_list[$k]['total_money'] = feeHandle($red_packet_list[$k]['total_money']);
        }
        return $red_packet_list;
    }



}

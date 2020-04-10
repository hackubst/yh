<?php
/**
 * 排行榜模型类
 * table_name = tp_rank_list
 * py_key = rank_list_id
 */

class RankListModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化排行榜id
     */
    public function RankListModel()
    {
        parent::__construct('rank_list');
    }

    /**
     * 获取排行榜信息
     * @author 姜伟
     * @param int $rank_list_id 排行榜id
     * @param string $fields 要获取的字段名
     * @return array 排行榜基本信息
     * @todo 根据where查询条件查找排行榜表中的相关数据并返回
     */
    public function getRankListInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改排行榜信息
     * @author 姜伟
     * @param array $arr 排行榜信息数组
     * @return boolean 操作结果
     * @todo 修改排行榜信息
     */
    public function editRankList($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加排行榜
     * @author 姜伟
     * @param array $arr 排行榜信息数组
     * @return boolean 操作结果
     * @todo 添加排行榜
     */
    public function addRankList($arr)
    {
        if (!is_array($arr)) return false;

        return $this->add($arr);
    }

    /**
     * 删除排行榜
     * @author 姜伟
     * @param int $rank_list_id 排行榜ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delRankList($rank_list_id,$opt = false)
    {
        if (!is_numeric($rank_list_id)) return false;
        if($opt)
        {
            return $this->where('rank_list_id = ' . $rank_list_id)->delete();
        }else{
           return $this->where('rank_list_id = ' . $rank_list_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取排行榜数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的排行榜数量
     * @todo 根据where子句获取排行榜数量
     */
    public function getRankListNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询排行榜信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 排行榜基本信息
     * @todo 根据SQL查询字句查询排行榜信息
     */
    public function getRankListList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getRankListField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取排行榜列表页数据信息列表
     * @author 姜伟
     * @param array $RankList_list
     * @return array $RankList_list
     * @todo 根据传入的$RankList_list获取更详细的排行榜列表页数据信息列表
     */
    public function getListData($rank_list_list)
    {
        // if(count($rank_list_list) == 1 && $rank_list_list[0]['user_id'] == 0)
        // {
        //     return array();
        // }
        foreach ($rank_list_list as $k => $v) {
            
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname,id','user_id ='.$v['user_id']);
            if($user_info)
            {
                $rank_list_list[$k]['nickname'] = $user_info['nickname'];
                $rank_list_list[$k]['id'] = $user_info['id'];
            }
            $rank_list_list[$k]['win'] = feeHandle($v['win']);
//            $rank_list_list[$k]['reward'] = feeHandle($v['reward']);
            $rank_list_list[$k]['order'] = $k+1;
            // unset($rank_list_list[$k]['win']);
        }
        return $rank_list_list;
    }

    /**
     * 获取排行榜列表页数据信息列表
     * @param $rank_list_list
     * @param $robot_list
     * @return mixed
     */
    public function getTodayData($rank_list_list, $robot_list)
    {
        foreach ($rank_list_list as $k => $v) {
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname', 'user_id =' . $v['user_id']);
            if ($user_info) {
                $rank_list_list[$k]['nickname'] = $user_info['nickname'];
            }
            if ($v['total'] <= 0) {
                unset($rank_list_list[$k]);
            }
        }
        $rank_list_list = $rank_list_list?:[];
        foreach ($robot_list as $ki => &$vi) {
            $vi['nickname'] = $vi['robot_name'];
            $vi['total'] = $vi['today_money'];
        }
        unset($vi);
        $list = array_merge($rank_list_list, $robot_list);
        $key = array_column($list, 'total');
        array_multisort($key, SORT_DESC, $list);
        foreach ($list as $kk => &$vv) {
            $vv['order'] = $kk + 1;
        }
        unset($vv);
        $list = array_slice($list,0,20);
        return $list;
    }


    //排行榜数据过期处理
    public function getDataList($rank_list_list)
    {
//        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $start_time = strtotime(date('Y-m-d',time()));
        foreach ($rank_list_list as $k => $v) {
            $rank_list_list[$k]['addtime_str'] = date('Y-m-d H:i:s',$v['addtime']);
            //昨天之前未领取的设置为已过期，不可领取
            if($v['is_received'] == 0 && $v['addtime'] < $start_time ){
                $rank_list_list[$k]['is_received'] = 2;
            }
        }
        return $rank_list_list;
    }

}

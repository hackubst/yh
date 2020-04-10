<?php
/**
 * 环信群组成员
 * Created by PhpStorm.
 * User: lkp
 * Date: 2017/8/29
 * Time: 16:36
 */
class HxGroupMemberModel extends Model{

    /**
     * 根据where条件获取用户信息
     * @creator 刘康平
     * @param string $field
     * @param string $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getGroupMemberList($field='',$where='',$order='',$limit=''){
        return $this
            ->field($field)
            ->join('tp_users AS u ON u.user_id = tp_hx_group_member.user_id')
            ->where($where)
            ->order($order)
            ->limit()
            ->select();
    }

    /**
     * 添加群成员
     * @creator 刘康平
     * @param $arr
     * @return bool
     */
    public function addGroupMember($arr){
        if(empty($arr)){
            return false;
        }
        return $this->add($arr);
    }

    /**
     * 根据where条件获取记录的数目
     * @creator 刘康平
     * @param $where
     * @return mixed
     */
    public function getMemberNum($where){
        return $this->where($where)->count();
    }

    /**获取群成员详情
     * @creator 刘康平
     * @param $hx_group_member_list
     * @return mixed
     */
    public function getMemberDetail($hx_group_member_list){
        for ($i=0;$i<count($hx_group_member_list);$i++){
            $user_id = $hx_group_member_list[$i]['user_id'];
            $user_obj = new UserModel();
            $user_info = $user_obj->userDetail('user_id = '.$user_id);
            $hx_group_member_list[$i] = array_merge($hx_group_member_list[$i],$user_info);
        }
        return $hx_group_member_list;
    }

    /**
     * 根据where条件删除群成员
     * @creator 刘康平
     * @param $where
     * @return bool
     */
    public function delMember($where){
        if(empty($where)){
            return false;
        }
        return $this->where($where)->delete();
    }
}
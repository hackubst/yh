<?php
/**
 * Created by PhpStorm.
 * User: lkp
 * Date: 2017/8/29
 * Time: 11:49
 */
class HxGroupModel extends Model {
    /**
     * 获取群组列表
     * @creator 刘康平
     * @param string $field
     * @param string $where
     * @param string $order
     * @param string $limit
     * @return mixed
     */
    public function getHxGroupList($field='',$where='',$order='',$limit=''){
        return $this->field($field)->where($where)->order($order)->limit()->select();
    }

    /**
     * 添加一条记录
     * @creator 刘康平
     * @param $arr
     * @return mixed
     */
    public function addGroupInfo($arr){
        if(empty($arr)){
            return $arr;
        }
        return $this->add($arr);
    }

    /**
     * 根据where条件获取一组信息
     * @creator 刘康平
     * @param string $where
     * @param string $field
     * @return mixed
     */
    public function getGroupInfo($where='',$field=''){
        return $this->field($field)->where($where)->find();
    }

    /**
     * 根据where条件来更改群信息
     * @creator 刘康平
     * @param $where
     * @param $arr
     * @return mixed
     */
    public function editGroupInfo($where,$arr){
        return $this->where($where)->save($arr);
    }

    /**
     * 获取相符条件的记录数目
     * @creator 刘康平
     * @param $where
     * @return mixed
     */
    public function getGroupNum($where){
        return $this->where($where)->count();
    }

    public function delGroup($where){
        return $this->where($where)->delete();
    }














}
<?php


class HgResultModel extends Model
{
    public function getInfo($where,$field)
    {
        return $this->where($where)->field($field)->find();
    }

    public function getList($where,$field)
    {
        return $this->where($where)->field($field)->limit()->find();
    }

    public function editInfo($where,$arr)
    {
        return $this->where($where)->save($arr);
    }

    public function delInfo($where)
    {
        return $this->where($where)->delete();
    }
}
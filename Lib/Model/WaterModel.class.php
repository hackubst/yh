<?php
/**
 * 收藏模型类
 */

class WaterModel extends Model
{




    /**
     * 获取收藏信息
     * @author 姜伟
     * @param int $water_id 收藏id
     * @param string $fields 要获取的字段名
     * @return array 收藏基本信息
     * @todo 根据where查询条件查找收藏表中的相关数据并返回
     */
    public function getWaterInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改收藏信息
     * @author 姜伟
     * @param array $arr 收藏信息数组
     * @return boolean 操作结果
     * @todo 修改收藏信息
     */
    public function editWater($where,$arr)
    {
        return $this->where($where)->save($arr);
    }

    /**
     * 添加收藏
     * @author 姜伟
     * @param array $arr 收藏信息数组
     * @return boolean 操作结果
     * @todo 添加收藏
     */
    public function addWater($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除收藏
     * @author 姜伟
     * @param int $water_id 收藏ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delWater($where)
    {
        return $this->where($where)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取收藏数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的收藏数量
     * @todo 根据where子句获取收藏数量
     */
    public function getWaterNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询收藏信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 收藏基本信息
     * @todo 根据SQL查询字句查询收藏信息
     */
    public function getWaterList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }



}

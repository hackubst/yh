<?php


class OmitModel extends Model
{
    // 收藏id
    public $omit_id;



    /**
     * 获取收藏信息
     * @author 姜伟
     * @param int $omit_id 收藏id
     * @param string $fields 要获取的字段名
     * @return array 收藏基本信息
     * @todo 根据where查询条件查找收藏表中的相关数据并返回
     */
    public function getOmitInfo($where, $fields = '')
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
    public function editOmit($where,$arr)
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
    public function addOmit($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();

        return $this->add($arr);
    }


    /**
     * 根据where子句获取收藏数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的收藏数量
     * @todo 根据where子句获取收藏数量
     */
    public function getOmitNum($where = '')
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
    public function getOmitList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }


    public function checkOmit()
    {
        for ($j=1;$j<7;$j++) {
            for ($i=1;$i<=49;$i++) {
                $this->add([
                    'type'  =>  $j,
                    'type_name' =>  '正'.$j.'特',
                    'number'    =>  $i
                ]);
            }
        }

    }

}

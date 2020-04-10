<?php
/**
 * 轮播图片模型类
 */

class AdvModel extends Model
{
    // 轮播图片id
    public $adv_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $adv_id 轮播图片ID
     * @return void
     * @todo 初始化轮播图片id
     */
    public function AdvModel($adv_id)
    {
        parent::__construct('adv');

        if ($adv_id = intval($adv_id))
        {
            $this->adv_id = $adv_id;
        }
    }

    /**
     * 获取轮播图片信息
     * @author 姜伟
     * @param int $adv_id 轮播图片id
     * @param string $fields 要获取的字段名
     * @return array 轮播图片基本信息
     * @todo 根据where查询条件查找轮播图片表中的相关数据并返回
     */
    public function getAdvInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改轮播图片信息
     * @author 姜伟
     * @param array $arr 轮播图片信息数组
     * @return boolean 操作结果
     * @todo 修改轮播图片信息
     */
    public function editAdv($arr)
    {
        return $this->where('adv_id = ' . $this->adv_id)->save($arr);
    }

    /**
     * 添加轮播图片
     * @author 姜伟
     * @param array $arr 轮播图片信息数组
     * @return boolean 操作结果
     * @todo 添加轮播图片
     */
    public function addAdv($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除轮播图片
     * @author 姜伟
     * @param int $adv_id 轮播图片ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delAdv($adv_id)
    {
        if (!is_numeric($adv_id)) return false;
        return $this->where('adv_id = ' . $adv_id)->delete();
    }

    /**
     * 根据where子句获取轮播图片数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的轮播图片数量
     * @todo 根据where子句获取轮播图片数量
     */
    public function getAdvNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询轮播图片信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 轮播图片基本信息
     * @todo 根据SQL查询字句查询轮播图片信息
     */
    public function getAdvList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

}

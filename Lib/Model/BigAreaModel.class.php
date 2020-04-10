<?php
/**
 * 商品一级分类模型类
 */

class BigAreaModel extends Model
{
    private $big_area_id;
    /**
     * 构造函数
     * @author 姜伟
     * @todo 构造函数
     */
    public function __construct($big_area_id=0)
    {
        parent::__construct("big_area");
        $this->big_area_id = $big_area_id;
    }

    public function getBigAreaNum($where='') {
      return $this->where($where)->count();
    }

    /**
     * 添加一级分类
     * @author 姜伟
     * @param array $arr_class 一级分类数组
     * @return boolean 操作结果
     * @todo 添加一级分类
     */
    public function addBigArea($arr)
    {
        if (!is_array($arr)) return false;
        return $this->add($arr);
    }

    /**
     * 删除一级分类
     * @author 姜伟
     * @param string $class_id 一级分类ID
     * @return boolean 操作结果
     * @todo 删除一级分类
     */
    public function delBigArea()
    {
        if (!is_numeric($this->big_area_id)) return false;
        return $this->where('big_area_id = ' . $this->big_area_id)->delete();
    }

    /**
     * 更改一级分类
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @param array $arr_class 一级分类数组
     * @return boolean 操作结果
     * @todo 更改一级分类
     */
    public function setBigArea($big_area_id, $arr)
    {
        if (!is_numeric($big_area_id) || !is_array($arr)) return false;
        return $this->where('big_area_id = ' . $big_area_id)->save($arr);
    }

    /**
     * 获取一级分类
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 一级分类
     * @todo 获取一级分类
     */
    public function getBigAreaInfo($big_area_id, $fields = null)
    {
        if (!is_numeric($big_area_id))   return false;
        return $this->field($fields)->where('big_area_id = ' . $big_area_id)->find();
    }

    /**
     * 获取一级分类某个字段的信息
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @param string $field 查询的字段名
     * @return array 一级分类
     * @todo 获取一级分类某个字段的信息
     */
    public function getBigAreaField($big_area_id, $field)
    {
        if (!is_numeric($big_area_id))   return false;
        return $this->where('big_area_id = ' . $big_area_id)->getField($field);
    }

    /**
     * 获取所有一级分类列表
     * @author 姜伟
     * @param string $where where子句
     * @return array 一级分类列表
     * @todo 获取所有一级分类列表
     */
    public function getBigAreaList($where = null)
    {
        return $this->where($where)->select();
    }

    public function getListData($big_area_list)
    {
        foreach($big_area_list AS $k=>$v) {
            $province_ids_array = explode(',',$v['province_ids']);
            $province_obj       = M('address_province');
            $province_str       = '';
            foreach($province_ids_array AS $index => $province_id) {
                $province_info  = $province_obj->where('province_id = ' . $province_id)->find();
                $province_str  .= ($province_info['province_name'] . ', ');
            }
            $big_area_list[$k]['province_names'] = strlen($province_str) > 0 ? substr($province_str, 0 , strlen($province_str)-2) : "数据错误，请修改分区信息！";
        }

        return $big_area_list;
    }
 
    /**
     * 获取分类信息
     * @author 姜伟
     * @param string $where where子句
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找商品表中的相关数据并返回
     */
    public function getBigAreaInfos($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }
}

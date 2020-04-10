<?php
/**
 * 商品一级分类模型类
 */

class IntegralChangeClassModel extends Model
{
    private $class_id;
    /**
     * 构造函数
     * @author 姜伟
     * @todo 构造函数
     */
    public function __construct($class_id=0)
    {
        parent::__construct();
        $this->class_id = $class_id;
    }

    public function getClassNum($where='') {
      return $this->where($where)->count();
    }

    /**
     * 添加一级分类
     * @author 姜伟
     * @param array $arr_class 一级分类数组
     * @return boolean 操作结果
     * @todo 添加一级分类
     */
    public function addClass($arr_class)
    {
        if (!is_array($arr_class)) return false;
        return $this->add($arr_class);
    }

    /**
     * 删除一级分类
     * @author 姜伟
     * @param string $class_id 一级分类ID
     * @return boolean 操作结果
     * @todo 删除一级分类
     */
    public function delClass()
    {
        if (!is_numeric($this->class_id)) return false;
        return $this->where('integral_change_class_id = ' . $this->class_id)->delete();
    }

    /**
     * 更改一级分类
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @param array $arr_class 一级分类数组
     * @return boolean 操作结果
     * @todo 更改一级分类
     */
    public function setClass($class_id, $arr_class)
    {
        if (!is_numeric($class_id) || !is_array($arr_class)) return false;
        return $this->where('integral_change_class_id = ' . $class_id)->save($arr_class);
    }

    /**
     * 获取一级分类
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 一级分类
     * @todo 获取一级分类
     */
    public function getClass($class_id, $fields = null)
    {
        if (!is_numeric($class_id))   return false;
        return $this->field($fields)->where('integral_change_class_id = ' . $class_id)->find();
    }

    /**
     * 获取一级分类某个字段的信息
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @param string $field 查询的字段名
     * @return array 一级分类
     * @todo 获取一级分类某个字段的信息
     */
    public function getClassField($class_id, $field)
    {
        if (!is_numeric($class_id))   return false;
        return $this->where('integral_change_class_id = ' . $class_id)->getField($field);
    }

    /**
     * 获取所有一级分类列表
     * @author 姜伟
     * @param string $where where子句
     * @return array 一级分类列表
     * @todo 获取所有一级分类列表
     */
    public function getClassList($where = null)
    {
        return $this->where($where)->order('serial')->select();
    }
 
    /**
     * 获取分类信息
     * @author 姜伟
     * @param string $where where子句
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找商品表中的相关数据并返回
     */
    public function getClassInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }
}

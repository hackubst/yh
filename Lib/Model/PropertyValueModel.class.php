<?php
/**
 * 规格值模型类
 */

class PropertyValueModel extends Model
{
    /**
     * 构造函数
     * @author 张勇
     * @todo 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 添加规格值
     * @author 张勇
     * @param array $arr_property_value 规格值数组
     * @return boolean 操作结果
     * @todo 添加规格值
     */
    public function addPropertyValue($arr_property_value)
    {
        if (!is_array($arr_property_value)) return false;
        return $this->add($arr_property_value);
    }

    /**
     * 删除某个规格的规格值
     * @author 张勇
     * @param int $property_id 规格ID
     * @return boolean 操作结果
     * @todo 删除某个规格的规格值
     */
    public function delPropertyValue($property_id)
    {
        if (!is_numeric($property_id)) return false;
        return $this->where('property_id = ' . $property_id)->delete();
    }

    /**
     * 更改规格值信息
     * @author 张勇
     * @param int $property_value_id 规格值ID
     * @param array $arr_property_value 规格值数组
     * @return boolean 操作结果
     * @todo 更改规格值信息
     */
    public function setPropertyValue($property_value_id, $arr_property_value)
    {
        if (!is_numeric($property_value_id) || !is_array($arr_property_value)) return false;
        return $this->where('property_value_id = ' . $property_value_id)->save($arr_property_value);
    }

    /**
     * 获取规格值信息
     * @author 张勇
     * @param int $property_value_id 规格值ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 规格值信息
     * @todo 获取规格值信息
     */
    public function getPropertyValue($property_value_id, $fields = null)
    {
        if (!is_numeric($property_value_id))   return false;
        return $this->field($fields)->where('property_value_id = ' . $property_value_id)->find();
    }

    /**
     * 获取规格值某个字段的信息
     * @author 张勇
     * @param int $property_value_id 规格值ID
     * @param string $field 查询的字段名
     * @return array 规格值信息
     * @todo 获取规格值某个字段的信息
     */
    public function getPropertyValueField($property_value_id, $field)
    {
        if (!is_numeric($property_value_id))   return false;
        return $this->where('property_value_id = ' . $property_value_id)->getField($field);
    }

    /**
     * 获取某个规格的规格值列表
     * @author 张勇
     * @param int $property_id 规格ID
     * @return array 规格值列表
     * @todo 获取某个规格的规格值列表
     */
    public function getPropertyValueList($property_id)
    {
        return $this->where('property_id = ' . $property_id)->order('property_value_id')->select();
    }
}

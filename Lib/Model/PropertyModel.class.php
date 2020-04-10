<?php
/**
 * 规格属性模型类
 */

class PropertyModel extends Model
{

    // 自动验证
    protected $_validate = array(
        array('property_name','require','属性名称必须填写！'),
        array('property_name', '', '属性名称已经存在！', 0, 'unique', 1),
    );

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
     * 删除规格属性
     * @author 张勇
     * @param int $property_id 规格属性ID
     * @return boolean 操作结果
     * @todo 删除规格属性
     */
    public function delProperty($property_id)
    {
        if (!is_numeric($property_id)) return false;
        return $this->where('property_id = ' . $property_id)->delete();
    }

    /**
     * 删除某个商品类型的规格属性
     * @author 张勇
     * @param int $item_type_id 商品类型ID
     * @return boolean 操作结果
     * @todo 删除某个商品类型的规格属性
     */
    public function delTypeProperty($item_type_id)
    {
        if (!is_numeric($item_type_id)) return false;
        return $this->where('item_type_id = ' . $item_type_id)->delete();
    }

    /**
     * 更改规格属性信息
     * @author 张勇
     * @param int $property_id 规格属性ID
     * @param array $arr_property 规格属性数组
     * @return boolean 操作结果
     * @todo 更改规格属性信息
     */
    public function setProperty($property_id, $arr_property)
    {
        if (!is_numeric($property_id) || !is_array($arr_property)) return false;
        return $this->where('property_id = ' . $property_id)->save($arr_property);
    }

    /**
     * 获取规格属性信息
     * @author 张勇
     * @param int $property_id 规格属性ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 规格属性信息
     * @todo 获取规格属性信息
     */
    public function getProperty($property_id, $fields = null)
    {
        if (!is_numeric($property_id))   return false;
        return $this->field($fields)->where('property_id = ' . $property_id)->find();
    }

    /**
     * 获取规格属性某个字段的信息
     * @author 张勇
     * @param int $property_id 规格属性ID
     * @param string $field 查询的字段名
     * @return array 规格属性信息
     * @todo 获取规格属性某个字段的信息
     */
    public function getPropertyField($property_id, $field)
    {
        if (!is_numeric($property_id))   return false;
        return $this->where('property_id = ' . $property_id)->getField($field);
    }

    /**
     * 获取所有规格属性列表
     * @author 张勇
     * @return array 规格属性列表
     * @todo 获取所有规格属性列表
     */
    public function getPropertyList()
    {
        return $this->select();
    }

    /**
     * 获取某个商品类型的属性列表
     * @author 张勇
     * @param int $item_type_id 商品类型ID
     * @return array 规格属性列表
     * @todo 获取某个商品类型的列表
     */
    public function getItemTypePropertyList($item_type_id)
    {
        return $this->where('item_type_id = ' . $item_type_id)->select();
    }


    public function getPropertyIds($item_type_id){
        return $this->where('item_type_id ='.$item_type_id)->getField('property_id', true);
    }
}

<?php
/**
 * 商品类型模型类
 */

class ItemTypeModel extends Model
{

    // 自动验证
    protected $_validate = array(
        array('item_type_name','require','商品类型名称必须填写！'),
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
     * 删除商品类型
     * @author 张勇
     * @param string $item_type_id 商品类型ID
     * @return boolean 操作结果
     * @todo 删除商品类型
     */
    public function delItemType($item_type_id)
    {
        if (!is_numeric($item_type_id)) return false;
        return $this->where('item_type_id = ' . $item_type_id)->delete();
    }

    /**
     * 更改商品类型信息
     * @author 张勇
     * @param int $item_type_id 商品类型ID
     * @param array $arr_item_type 商品类型信息数组
     * @return boolean 操作结果
     * @todo 更改商品类型信息
     */
    public function setItemType($item_type_id, $arr_item_type)
    {
        if (!is_numeric($item_type_id) || !is_array($arr_item_type)) return false;
        return $this->where('item_type_id = ' . $item_type_id)->save($arr_item_type);
    }

    /**
     * 获取商品类型信息
     * @author 张勇
     * @param int $item_type_id 商品类型ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 商品类型信息
     * @todo 获取商品类型信息
     */
    public function getItemType($item_type_id, $fields = null)
    {
        if (!is_numeric($item_type_id))   return false;
        return $this->field($fields)->where('item_type_id = ' . $item_type_id)->find();
    }

    /**
     * 获取商品类型某个字段的信息
     * @author 张勇
     * @param int $item_type_id 商品类型ID
     * @param string $field 查询的字段名
     * @return array 商品类型信息
     * @todo 获取商品类型某个字段的信息
     */
    public function getItemTypeField($item_type_id, $field)
    {
        if (!is_numeric($item_type_id))   return false;
        return $this->where('item_type_id = ' . $item_type_id)->getField($field);
    }

    /**
     * 获取所有商品类型列表
     * @author 张勇
     * @param string $where where子句
     * @return array 商品类型列表
     * @todo 获取所有商品类型列表
     */
    public function getItemTypeList($where = null)
    {
        return $this->where($where)->order('item_type_id')->select();
    }
}
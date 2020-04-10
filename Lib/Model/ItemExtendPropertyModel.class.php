<?php
/**
 * 商品扩展属性关联类
 */

class ItemExtendPropertyModel extends Model
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
     * 通过扩展属性ID获取关联的商品列表
     * @author 张勇
     * @param string $property_id 扩展属性id
     * @return array 商品id数组
     * @todo 通过扩展属性ID获取关联的商品列表
     */
    public function getItemListByPropertyValueId($property_id)
    {
        if (!is_numeric($property_id)) return false;
        return $this->where('property_value_id = ' . $property_id)->field('item_id')->select();
    }

    /**
     * 通过商品ID获取关联的扩展属性列表
     * @author 张勇
     * @param string $item_id 商品id
     * @return array 扩展属性列表
     * @todo 通过商品ID获取关联的扩展属性列表
     */
    public function getPropertyListByItemId($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->getField('property_value_id', true);
    }
    
    /**
     * @access public
     * @todo 根据商品ID获取商品的扩展属性信息
     * @author zhoutao@360shop.cc 、 zhoutao0928@sina.com
     * @param int $item_id
     */
    public function getItemExtendPropertyInfo($item_id)
    {
    	if (!is_numeric($item_id)) return false;
    	$main_table = $this->trueTableName;
    	$pro_table  = C('DB_PREFIX').'property';
    	$pro_value_table = C('DB_PREFIX').'property_value';
    	$r = array();
    	$r = $this->join($pro_value_table.' AS pv ON pv.property_value_id = '.$main_table.'.property_value_id')
    			  ->join('RIGHT JOIN '.$pro_table.' AS pt ON pt.property_id = pv.property_id')
    			  ->where($main_table.'.item_id = '.$item_id)
    			  ->field('pt.property_name,pv.property_value')
    			  ->order('pt.serial DESC,pv.serial DESC')
    			  ->select();
    	return $r;
    }
    
    

    /**
     * 删除某件商品的扩展属性信息
     * @author 张勇
     * @param string $item_id 商品id
     * @return boolean 操作结果
     * @todo 删除某件商品的扩展属性信息
     */
    public function delItemProperty($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->delete();
    }

    /**
     * 删除某个扩展属性关联商品的信息
     * @author 张勇
     * @param string $property_value_id 扩展属性id
     * @return boolean 操作结果
     * @todo 删除某个扩展属性关联商品的信息
     */
    public function delPropertyItem($property_value_id)
    {
        if (!is_numeric($property_value_id)) return false;
        return $this->where('property_value_id = ' . $property_value_id)->delete();
    }
}
<?php
/**
 * 品牌模型类
 */

class BrandModel extends Model
{

    // 自动验证
    protected $_validate = array(
        array('brand_name','require','品牌名称必须填写！'),
        array('brand_url','url','链接地址格式不正确！', 2),
        array('brand_name', '', '品牌名称已经存在！', 0, 'unique', 1),
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
     * 添加品牌
     * @author 张勇
     * @param array $arr_brand 品牌信息
     * @return boolean 操作结果
     * @todo 添加品牌
     */
    public function addBrand($arr_brand)
    {
        if (!is_array($arr_brand)) return false;
        return $this->add($arr_brand);
    }

    /**
     * 删除品牌
     * @author 张勇
     * @param string $brand_id 品牌ID
     * @return boolean 操作结果
     * @todo 删除品牌
     */
    public function delBrand($brand_id)
    {
        if (!is_numeric($brand_id)) return false;

        // 删除品牌Logo
        if ($brand_logo = $this->getBrandField($brand_id, 'brand_logo')) {
            unlink($brand_logo);
        }

        return $this->where('brand_id = ' . $brand_id)->delete();
    }

    /**
     * 更改品牌信息
     * @author 张勇
     * @param int $brand_id 品牌ID
     * @param array $arr_brand 品牌信息数组
     * @return boolean 操作结果
     * @todo 更改品牌信息
     */
    public function setBrand($brand_id, $arr_brand)
    {
        if (!is_numeric($brand_id) || !is_array($arr_brand)) return false;
        return $this->where('brand_id = ' . $brand_id)->save($arr_brand);
    }

    /**
     * 获取品牌信息
     * @author 张勇
     * @param int $brand_id 品牌ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 品牌信息
     * @todo 获取品牌信息
     */
    public function getBrand($brand_id, $fields = null)
    {
        if (!is_numeric($brand_id))   return false;
        return $this->field($fields)->where('brand_id = ' . $brand_id)->find();
    }

    /**
     * 获取品牌某个字段的信息
     * @author 张勇
     * @param int $brand_id 品牌ID
     * @param string $field 查询的字段名
     * @return array 品牌信息
     * @todo 获取品牌某个字段的信息
     */
    public function getBrandField($brand_id, $field)
    {
        if (!is_numeric($brand_id))   return false;
        return $this->where('brand_id = ' . $brand_id)->getField($field);
    }

    /**
     * 获取品牌列表
     * @author 张勇
     * @param string $where where子句
     * @return array 品牌列表
     * @todo 获取品牌列表
     */
    public function getBrandList($where = null)
    {
        return $this->where($where)->order('serial')->limit()->select();
    }

    public function getBrandNum($where='') {
      return $this->where($where)->count();
    }
}

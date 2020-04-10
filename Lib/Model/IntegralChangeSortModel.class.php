<?php
/**
 * 商品二级分类模型类
 */

class IntegralChangeSortModel extends Model
{
    /**
     * 构造函数
     * @author 姜伟
     * @todo 构造函数
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getSortNum($where = '') {
            return $this->where($where)->count();
    }

    /**
     * 添加二级分类
     * @author 姜伟
     * @param array $arr_sort 二级分类数组
     * @return boolean 操作结果
     * @todo 添加二级分类
     */
    public function addSort($arr_sort)
    {
        if (!is_array($arr_sort)) return false;
        return $this->add($arr_sort);
    }

    /**
     * 删除二级分类
     * @author 姜伟
     * @param string $sort_id 二级分类ID
     * @return boolean 操作结果
     * @todo 删除二级分类
     */
    public function delSort($sort_id)
    {
        if (!is_numeric($sort_id)) return false;
        return $this->where('integral_change_sort_id = ' . $sort_id)->delete();
    }

    /**
     * 删除一级分类下的所有二级分类
     * @author 姜伟
     * @param string $class_id 一级分类ID
     * @return boolean 操作结果
     * @todo 删除二级分类
     */
    public function delClassSort($class_id)
    {
        if (!is_numeric($class_id)) return false;
        return $this->where('class_id = ' . $class_id)->delete();
    }

    /**
     * 更改二级分类
     * @author 姜伟
     * @param int $sort_id 二级分类ID
     * @param array $arr_sort 二级分类数组
     * @return boolean 操作结果
     * @todo 更改二级分类
     */
    public function setSort($sort_id, $arr_sort)
    {
        if (!is_numeric($sort_id) || !is_array($arr_sort)) return false;
        return $this->where('integral_change_sort_id = ' . $sort_id)->save($arr_sort);
    }

    /**
     * 获取二级分类
     * @author 姜伟
     * @param int $sort_id 二级分类ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array 二级分类
     * @todo 获取二级分类
     */
    public function getSort($sort_id, $fields = null)
    {
        if (!is_numeric($sort_id))   return false;
        return $this->field($fields)->where('integral_change_sort_id = ' . $sort_id)->find();
    }

    /**
     * 获取二级分类某个字段的信息
     * @author 姜伟
     * @param int $sort_id 二级分类ID
     * @param string $field 查询的字段名
     * @return array 二级分类
     * @todo 获取二级分类某个字段的信息
     */
    public function getSortField($sort_id, $field)
    {
        if (!is_numeric($sort_id))   return false;
        return $this->where('integral_change_sort_id = ' . $sort_id)->getField($field);
    }

    /**
     * 获取所有二级分类列表
     * @author 姜伟
     * @param string $where where子句
     * @return array 二级分类列表
     * @todo 获取所有二级分类列表
     */
    public function getSortList($where = null, $limit = null)
    {
        if ($limit) {
            $_this = $this->limit($limit);
        }

     return $this->where($where)->order('serial')->limit($limit)->select();
    
    }

    /**
     * 获取一级分类下所有启用的二级分类列表
     * @author 姜伟
     * @param int $class_id 一级分类ID
     * @return array 二级分类列表
     * @todo 获取一级分类下所有isuse=1的二级分类列表
     */
    public function getClassSortList($class_id)
    {
        if (!is_numeric($class_id))   return false;
        return $this->where('class_id = ' . $class_id . ' AND isuse = 1')->order('serial')->select();
    }

    public function getCategoryList()
    {
        $Class = D('Class');
        $Sort  = D('Sort');
        $Item  = D('Item');
        $arr_category = array();

        // 获取所有一级分类
        $arr_class = $Class->getClassList('isuse=1');
        foreach ($arr_class as $k1 => $class)
        {
            $arr_category[$k1] = $class;
            // 获取一级分类下的二级分类
            $arr_sort = $Sort->getClassSortList($class['class_id']);
            $arr_category[$k1]['sort_list'] = $arr_sort;
        }

        return $arr_category;
    }
}

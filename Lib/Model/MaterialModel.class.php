<?php
/**
 * 实物表模型类
 * table_name = tp_material
 * py_key = material_id
 */

class MaterialModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化实物表id
     */
    public function MaterialModel()
    {
        parent::__construct('material');
    }

    /**
     * 获取实物表信息
     * @author 姜伟
     * @param int $material_id 实物表id
     * @param string $fields 要获取的字段名
     * @return array 实物表基本信息
     * @todo 根据where查询条件查找实物表表中的相关数据并返回
     */
    public function getMaterialInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改实物表信息
     * @author 姜伟
     * @param array $arr 实物表信息数组
     * @return boolean 操作结果
     * @todo 修改实物表信息
     */
    public function editMaterial($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加实物表
     * @author 姜伟
     * @param array $arr 实物表信息数组
     * @return boolean 操作结果
     * @todo 添加实物表
     */
    public function addMaterial($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除实物表
     * @author 姜伟
     * @param int $material_id 实物表ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delMaterial($material_id,$opt = false)
    {
        if (!is_numeric($material_id)) return false;
        if($opt)
        {
            return $this->where('material_id = ' . $material_id)->delete();
        }else{
           return $this->where('material_id = ' . $material_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取实物表数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的实物表数量
     * @todo 根据where子句获取实物表数量
     */
    public function getMaterialNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询实物表信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 实物表基本信息
     * @todo 根据SQL查询字句查询实物表信息
     */
    public function getMaterialList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getMaterialField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取实物表列表页数据信息列表
     * @author 姜伟
     * @param array $Material_list
     * @return array $Material_list
     * @todo 根据传入的$Material_list获取更详细的实物表列表页数据信息列表
     */
    public function getListData($Material_list)
    {
        
    }

}

<?php
class CategoryApiModel extends ApiModel
{
	/**
	 * 获取商品一级分类列表(cheqishi.category.getClassList)
	 * @author zlf
	 * @param array $params 参数列表
	 * @return 成功返回$class_list，失败退出返回错误码
	 * @todo 获取商品一级分类列表(cheqishi.category.getClassList)
	 */
	function getClassList($params)
	{
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		return $class_list;

	}

	/**
	 * 获取商品一级分类下的二级分类列表(cheqishi.category.getSortListByClassId)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$sort_list，失败返回错误码
	 * @todo 获取商品一级分类下的二级分类列表(cheqishi.category.getSortListByClassId)
	 */
	function getSortListByClassId($params)
	{
		$class_id = $params['class_id'];
		$sort_obj = new SortModel();
		$sort_list = $sort_obj->getClassSortList($class_id);
		return $sort_list;
	}

	/**
	 * 获取参数列表
	 * @author zlf
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		$params = array(
			'getSortListByClassId'	=> array(
				array(
					'field'		=> 'class_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41050, 
					'empty_code'=> 44050, 
					'type_code'	=> 45050, 
				),
			),
		);

		return $params[$func_name];
	}
}

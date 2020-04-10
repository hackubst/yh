<?php
/**
 * 有关商品的公共函数
 * Date: 2014-02-28
 * Time: 14:16
 */

/**
 * 根据原图地址获取大图地址
 * @author 姜伟
 * @param string $base_img 原图地址
 * @return string 大图地址
 */
function big_img($base_img)
{
	$base_img = strstr($base_img, '/Uploads/');
    $arr_img = explode('.', $base_img);
    return $arr_img[0] . C('BIG_IMG_SUFFIX') . '.' . $arr_img[1];
}

/**
 * 根据原图地址获取中图地址
 * @author 姜伟
 * @param string $base_img 原图地址
 * @return string 中图地址
 */
function middle_img($base_img)
{
	$base_img = strstr($base_img, '/Uploads/');
    $arr_img = explode('.', $base_img);
    return $arr_img[0] . C('MIDDLE_IMG_SUFFIX') . '.' . $arr_img[1];
}

/**
 * 根据原图地址获取小图地址
 * @author 姜伟
 * @param string $base_img 原图地址
 * @return string 小图地址
 */
function small_img($base_img)
{	
	$base_img = strstr($base_img, '/Uploads/');
    $arr_img = explode('.', $base_img);
    return $arr_img[0] . C('SMALL_IMG_SUFFIX') . '.' . $arr_img[1];
}

/**
 * 获取所有启用的商品分类
 * @author 姜伟
 * @return array 商品分类信息
 * @todo 获取所有isuse=1的商品分类
 */
function get_category()
{
    $Class = new ClassModel();
    $Sort  = new SortModel();
    $Genre = new GenreModel();
    $Item  = new ItemModel();

    $arr_category = array();

    // 获取所有一级分类
    $arr_class = $Class->getClassList('isuse=1');

    foreach ($arr_class as $k1 => $class) {
        $arr_category[$k1] = $class;
        $arr_category[$k1]['item_num'] = $Item->getItemCountByClassId($class['class_id']);

        // 获取一级分类下的二级分类
        $arr_sort = $Sort->getClassSortList($class['class_id']);
        $arr_category[$k1]['sort_info'] = $arr_sort;

		foreach ($arr_sort as $k2 => $sort)
		{
            // 获取二级分类下的三级分类
            $arr_genre = $Genre->getSortGenreList($sort['sort_id']);
			#echo $Genre->getLastSql() . "<br>";
            $arr_category[$k1]['sort_info'][$k2]['genre_info'] = $arr_genre;

            $arr_category[$k1]['sort_info'][$k2]['item_num'] = $Item->getItemCountBySortId($sort['sort_id']);
		}
    }

    return $arr_category;
}

/**
 * @todo 面包屑导航所需的信息(商品分类信息数组) --->商品列表页
 * @author zhoutao
 * @return array
 */
function get_listBread($params)
{
	$Class = D('Class');
	$Sort  = D('ClassSort');
	$Genre = D('ClassGenre');
	 
	$arr = array();
	if($params['class_id'])
	{
		$class_info = $Class->getClass($params['class_id']);
		if(!$class_info)
		{
			return $arr;
		}
	}
	if($params['class_sort_id'])
	{
		$sort_info = $Sort->getSort($params['class_sort_id']);
		if(!$sort_info)
		{
			return $arr;
		}
		$class_info = $Class->getClass($sort_info['class_id']);
		if(!$class_info)
		{
			return $arr;
		}
	}
	if($params['class_genre_id'])
	{
		$genre_info = $Genre->getGenre($params['class_genre_id']);
		if(!$genre_info)
		{
			return $arr;
		}
		$sort_info = $Sort->getSort($genre_info['sort_id']);
		if(!$sort_info)
		{
			return $arr;
		}
		$class_info = $Class->getClass($sort_info['class_id']);
		if(!$class_info)
		{
			return $arr;
		}
	}
	if($class_info)
	{
		$arr[] = array(
				'url'		=>	U('/item_list/class_id/'.$class_info['class_id']),
				'name'		=>	$class_info['class_name']
		);
	}
	if($sort_info)
	{
		$arr[] = array(
				'url'		=>	U('/item_list/class_sort_id/'.$sort_info['class_sort_id']),
				'name'		=>	$sort_info['sort_name']
		);
	}
	if($genre_info)
	{
		$arr[] = array(
				'url'		=>	U('/item_list/class_genre_id/'.$genre_info['class_genre_id']),
				'name'		=>	$genre_info['genre_name']
		);
	}
	
	if($params['item_name'])
	{
		$arr[] = array(
				'url'		=>	U('/ItemDisplay/index/id/'.$params['id']),
				'name'		=>	$params['item_name']
		);
	}
	
	if($params['q'])
	{
		$q = $params['q'];
		$arr[] = array(
				'url'	=>	'javascript:;',
				'name'	=>	'搜索“ '.($params['s']?url_jiemi($q):$q).' ”的结果'
		);
	}
	return $arr;
}

/**
 * 获取启用的商品分类树
 * @author 姜伟
 * @return array 商品分类信息
 * @todo 主要用于下拉菜单
 */
function get_category_tree()
{
    $arr_category = get_category();
    foreach ($arr_category as $k1 => $v1) {
        if (isset($v1['sort_info']) && $v1['sort_info']) {
            foreach ($v1['sort_info'] as $k2 => $v2) {
                if ($v2['sort_name'])
                    $arr_category[$k1]['sort_info'][$k2]['sort_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;└─' . $v2['sort_name'];

                if (isset($v2['genre_info']) && $v2['genre_info']) {
                    foreach ($v2['genre_info'] as $k3 => $v3) {
                        if ($v3['genre_name'])
                            $arr_category[$k1]['sort_info'][$k2]['genre_info'][$k3]['genre_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└─' . $v3['genre_name'];
                    }
                }
            }
        }
    }

    return $arr_category;
}

/**
 * 获取所有启用的商品类型
 * @author 张勇
 * @return array 商品类型信息
 * @todo 获取所有isuse=1的商品类型
 */
function get_item_type()
{
    return D('ItemType')->getItemTypeList('isuse=1');
}

/**
 * 获取某个商品类型所有启用的扩展属性
 * @author 张勇
 * @param int $type_id 商品类型id
 * @return array 扩展属性信息
 */
function get_type_extend_prop($type_id)
{
    $Property      = D('Property');
    $PropertyValue = D('PropertyValue');

    $arr_prop = $Property->where('isuse = 1 AND is_extended_property = 1 AND item_type_id = ' . $type_id)->order('serial')->select();
    foreach ($arr_prop as $k => $prop) {
        $arr_prop[$k]['prop_value'] = $PropertyValue->where('isuse = 1 AND property_id = ' . $prop['property_id'])->order('serial, property_value_id')->select();
    }

    return $arr_prop;
}

/**
 * 获取某个商品类型所有启用的规格属性
 * @author 张勇
 * @param int $type_id 商品类型id
 * @return array 规格属性信息
 */
function get_type_sku($type_id)
{
    $Property      = D('Property');
    $PropertyValue = D('PropertyValue');

    $arr_sku = $Property->where('isuse = 1 AND is_extended_property = 2 AND item_type_id = ' . $type_id)->order('serial')->select();
    foreach ($arr_sku as $k => $sku) {
        $arr_sku[$k]['prop_value'] = $PropertyValue->where('isuse = 1 AND property_id = ' . $sku['property_id'])->order('serial')->select();
    }

    return $arr_sku;
}

/**
 * 获取某件商品的分销商等级价格信息
 * @author 张勇
 * @param int $item_id 商品id
 * @return array 商品的分销商等级价格信息
 */
function get_item_rank_price($item_id) {
    $data = D('AgentRank')->join(C('DB_PREFIX').'item_price_rank AS p ON p.agent_rank_id = ' . C('DB_PREFIX')
        . 'agent_rank.agent_rank_id AND p.item_id = ' . $item_id)
        ->field(C('DB_PREFIX') . 'agent_rank.agent_rank_id, rank_name, price')
        ->select();
    return $data;
}

/**
 * 获取所有启用的品牌
 * @author 张勇
 * @return array 品牌信息
 * @todo 获取所有isuse=1的品牌
 */
function get_brand()
{
    return D('Brand')->getBrandList('isuse=1');
}


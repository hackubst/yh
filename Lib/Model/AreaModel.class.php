<?php
/**
 * 地区模型
 * @access public
 * @author 姜伟
 * @Date 2014-05-28
 */
class AreaModel extends Model
{
    function __construct() {}

	public static function getAreaString($province_id, $city_id, $area_id = 0)
	{
		$area_string = '';
		//省份
		$province_obj = M('address_province');
		$province_name = $province_obj->where('province_id = ' . intval($province_id))->getField('province_name');
		$area_string .= $province_name ? $province_name : '';

		//城市
		$city_obj = M('address_city');
		$city_name = $city_obj->where('city_id = ' . intval($city_id))->getField('city_name');
		$area_string .= $city_name ? ' ' . $city_name : '';

		//地区
		$area_name = '';
		if ($area_id)
		{
			$area_obj = M('address_area');
			$area_name = $area_obj->where('area_id = ' . intval($area_id))->getField('area_name');
			$area_string .= $area_name ? ' ' . $area_name : '';
		}

		return $area_string . ' ';
	}

	public static function getProvince_id($province_id = 0, $city_id = 0, $area_id = 0)
	{
		$area_string = array();
		
		//省份
		$province_obj = M('address_province');
		$province_name = $province_obj->where('province_id = ' . intval($province_id))->getField('province_name');
		$area_string['province_name'] = $province_name ? $province_name : '';
		
		

		//城市
		$city_obj = M('address_city');
		$city_name = $city_obj->where('city_id = ' . intval($city_id))->getField('city_name');
		$area_string['city_name'] = $city_name ? $city_name : '';

		//地区
		$area_name = '';
		
		$area_obj = M('address_area');
		$area_name = $area_obj->where('area_id = ' . intval($area_id))->getField('area_name');
		$area_string['area_name'] = $area_name ? $area_name : '';
		

		return $area_string;
	}

	//根据省份名称和城市名称获取省份和城市ID
	function getIdByName($province, $city)
	{
		//初始化
		$province_id = 0;
		$city_id = 0;

		//获取city_id
		$city_obj = M('address_city');
		$city_info = $city_obj->field('city_id, province_id')->where('city_name LIKE "' . $city . '%"')->find();
log_file($city_info . ' sql = ' . $city_obj->getLastSql(), 'wxpay');

		return $city_info;
	}
}

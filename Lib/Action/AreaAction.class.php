<?php
// 地区选择操作
class AreaAction extends Action {
	/**
	 * 获取城市列表
	 * province_id	省份ID
	 */
	function get_city_list($province_id) {
		$city = M('address_city');
		$city_list = $city->field('city_id,city_name')->where('province_id = ' . $province_id)->select();
		echo json_encode($city_list);
		exit;
	}

	/**
	 * 获取地区列表
	 * area_id	地区ID
	 */
	function get_area_list($city_id) {
		$area = M('address_area');
		$area_list = $area->field('area_id,area_name')->where('city_id = ' . $city_id)->select();
		echo json_encode($area_list);
		exit;
	}


    /**
     * 获取街道列表
     * ID：street_id
     * @author wzg
     */
    public function get_street_list() {
        $area_id = I('post.area_id', 0, 'int');
        log_file($area_id);
        $street_obj = M('Street');
        $street_list = $street_obj->field('street_id, street_name')->where('area_id = ' . $area_id)->select();
        exit(json_encode($street_list));
    }
}

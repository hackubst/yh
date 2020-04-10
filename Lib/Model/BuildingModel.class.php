<?php
/**
 * 小区/写字楼模型类
 */

class BuildingModel extends BaseModel
{
    // 小区/写字楼id
    public $building_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $building_id 小区/写字楼ID
     * @return void
     * @todo 初始化小区/写字楼id
     */
    public function BuildingModel($building_id)
    {
        parent::__construct('building');

        if ($building_id = intval($building_id))
		{
            $this->building_id = $building_id;
		}
    }

    /**
     * 获取小区/写字楼信息
     * @author 姜伟
     * @param int $building_id 小区/写字楼id
     * @param string $fields 要获取的字段名
     * @return array 小区/写字楼基本信息
     * @todo 根据where查询条件查找小区/写字楼表中的相关数据并返回
     */
    public function getBuildingInfo($where, $fields = '', $orderby = '')
    {
		return $this->field($fields)->where($where)->order($orderby)->find();
    }

    /**
     * 修改小区/写字楼信息
     * @author 姜伟
     * @param array $arr 小区/写字楼信息数组
     * @return boolean 操作结果
     * @todo 修改小区/写字楼信息
     */
    public function editBuilding($arr)
    {
        return $this->where('building_id = ' . $this->building_id)->save($arr);
    }

    /**
     * 添加小区/写字楼
     * @author 姜伟
     * @param array $arr 小区/写字楼信息数组
     * @return boolean 操作结果
     * @todo 添加小区/写字楼
     */
    public function addBuilding($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除小区/写字楼
     * @author 姜伟
     * @param int $building_id 小区/写字楼ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delBuilding($building_id)
    {
        if (!is_numeric($building_id)) return false;
		return $this->where('building_id = ' . $building_id)->delete();
    }

    /**
     * 根据where子句获取小区/写字楼数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的小区/写字楼数量
     * @todo 根据where子句获取小区/写字楼数量
     */
    public function getBuildingNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询小区/写字楼信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 小区/写字楼基本信息
     * @todo 根据SQL查询字句查询小区/写字楼信息
     */
    public function getBuildingList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取小区/写字楼状态列表
     * @author 姜伟
     * @param void
     * @return array $isuse_list
     * @todo 获取小区/写字楼状态列表
     */
    public static function getIsuseList()
    {
		return array(
			'0'	=> '禁用',
			'1'	=> '启用',
		);
    }

    /**
     * 获取小区/写字楼列表页数据信息列表
     * @author 姜伟
     * @param array $building_list
     * @return array $building_list
     * @todo 根据传入的$building_list获取更详细的小区/写字楼列表页数据信息列表
     */
    public function getListData($building_list)
    {
		foreach ($building_list AS $k => $v)
		{
			$isuse_list = self::getIsuseList();
			$building_list[$k]['isuse_name'] = $isuse_list[$v['isuse']];

			//获取省市县
			$building_list[$k]['area_string'] = AreaModel::getAreaString($v['province_id'], $v['city_id'], $v['area_id']);
		}

		return $building_list;
    }

    /**
     * 根据经纬度获取最近的建筑物信息
     * @author 姜伟
     * @param int $lon
     * @param int $lat
     * @return array $building_info
     * @todo 根据经纬度获取最近的建筑物信息
     */
	function getNearbyBuildingInfo($lon, $lat)
	{
		$building_info = $this->getBuildingInfo('', 'building_id, building_name, area_id, ' . 'SQRT(POWER(longitude - ' . $lon . ', 2) + POWER(latitude - ' . $lat . ', 2)) AS distance', 'distance ASC');
		return $building_info;
	}

    /**
     * 根据经纬度获取最近的建筑物信息列表，10条
     * @author 姜伟
     * @param int $lon
     * @param int $lat
     * @return array $building_list
     * @todo 根据经纬度获取最近的建筑物信息列表，10条
     */
	function getNearbyBuildingList($lon, $lat)
	{
		$building_list = $this->getBuildingList('building_id, building_name, longitude, latitude, ' . 'SQRT(POWER(longitude - ' . $lon . ', 2) + POWER(latitude - ' . $lat . ', 2)) AS distance', '', 'distance ASC');
		return $building_list;
	}

    /**
     * 根据经纬度获取最近的1个建筑物信息列表
     * @author 姜伟
     * @param int $lon
     * @param int $lat
     * @return array $building_list
     * @todo 根据经纬度获取最近的1个建筑物信息列表、距离当前坐标距离、附近商家数量
     */
	function getNearbyBuilding($lon, $lat)
	{
		$this->setLimit(1);
		$building_list = $this->getNearbyBuildingList($lon, $lat);
		$building_info = $building_list[0];
		$building_info['distance'] = MapModel::calDistance($lon, $lat, $building_info['longitude'], $building_info['latitude']);
		$merchant_distance_limit = $GLOBALS['config_info']['MERCHANT_DISTANCE_LIMIT'];
		$merchant_obj = new MerchantModel();
		$where = 'isuse = 1 AND online = 1';
		$has_shop = 0;
		$merchant_obj->setStart(0);
		$merchant_obj->setLimit(1);
		$merchant_list = $merchant_obj->getMerchantList('merchant_id, rec_serial, shop_name, logo, online, ' . MerchantModel::getDistanceSql($building_info['longitude'], $building_info['latitude']), $where, 'distance ASC');
		$distance = MapModel::calDistance($merchant_list[0]['longitude'], $merchant_list[0]['latitude'], $building_info['longitude'], $building_info['latitude']);
		if ($distance < $merchant_distance_limit)
		{
			$has_shop = 1;
		}
		$building_info['has_shop'] = $has_shop;

		return $building_info;
	}

	//获取建筑物名称
	function getBuildingName($building_id)
	{
		return $this->where('building_id = ' . $building_id)->getField('building_name');
	}
}

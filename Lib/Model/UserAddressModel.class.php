<?php
/**
 * 用户地址模型类
 */

class UserAddressModel extends Model
{
    // 用户地址id
    public $user_address_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $user_address_id 用户地址ID
     * @return void
     * @todo 初始化用户地址id
     */
    public function UserAddressModel($user_address_id)
    {
        parent::__construct('user_address');

        if ($user_address_id = intval($user_address_id))
		{
            $this->user_address_id = $user_address_id;
		}
    }

    /**
     * 获取用户地址信息
     * @author 姜伟
     * @param int $user_address_id 用户地址id
     * @param string $fields 要获取的字段名
     * @return array 用户地址基本信息
     * @todo 根据where查询条件查找用户地址表中的相关数据并返回
     */
    public function getUserAddressInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改用户地址信息
     * @author 姜伟
     * @param array $arr 用户地址信息数组
     * @return boolean 操作结果
     * @todo 修改用户地址信息
     */
    public function editUserAddress($arr)
    {
        return $this->where('user_address_id = ' . $this->user_address_id)->save($arr);
    }

    /**
     * 添加用户地址
     * @author 姜伟
     * @param array $arr 用户地址信息数组
     * @return boolean 操作结果
     * @todo 添加用户地址
     */
    public function addUserAddress($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
		$arr['isuse'] = isset($arr['isuse']) ? $arr['isuse'] : 1;

        return $this->add($arr);
    }

    /**
     * 删除用户地址
     * @author 姜伟
     * @param int $user_address_id 用户地址ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delUserAddress($user_address_id)
    {
        if (!is_numeric($user_address_id)) return false;
        return $this->where('user_address_id = ' . $user_address_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取用户地址数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的用户地址数量
     * @todo 根据where子句获取用户地址数量
     */
    public function getUserAddressNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询用户地址信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 用户地址基本信息
     * @todo 根据SQL查询字句查询用户地址信息
     */
    public function getUserAddressList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取用户地址列表页数据信息列表
     * @author 姜伟
     * @param array $user_address_list
     * @return array $user_address_list
     * @todo 根据传入的$user_address_list获取更详细的用户地址列表页数据信息列表
     */
    public function getListData($user_address_list)
    {
		foreach ($user_address_list AS $k => $v)
		{
			$user_address_list[$k]['area_string'] = AreaModel::getAreaString($v['province_id'], $v['city_id'], $v['area_id']) . $v['address'];
			#unset($user_address_list[$k]['province_id']);
			#unset($user_address_list[$k]['city_id']);
			#unset($user_address_list[$k]['area_id']);
            //是否是默认地址判断
            $user_address_obj   = new UserModel($v['user_id']);
            $default_address_id = $user_address_obj->where('user_id = ' . $v['user_id'])->getField('user_address_id');

            $user_address_list[$k]['is_default'] = $default_address_id == $v['user_address_id'] ? 1 : 0;
		}

		return $user_address_list;
    }

    /**
     * 获取用户默认地址信息
     * @author 姜伟
     * @param void
     * @return array $user_address_info
     * @todo 获取用户默认地址信息
     */
    public function getDefaultAddress()
    {
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_address_info = $user_obj->getUserInfo('user_address_id');
		return $this->where('user_address_id = ' . $user_address_info['user_address_id'])->field('')->find();
	}

    /**
     * 获取用户收获地址信息
     * @author wsq
     * @param void
     * @return array $user_address_info
     * @todo 获取用户默认地址信息
     */
    public function getAddressDetail($address_info)
    {
        if (!is_array($address_info)) return "请添加收货地址";

        $area_obj     = new AreaModel();
        $address_pre  = $area_obj->getAreaString(
            $address_info['province_id'], 
            $address_info['city_id'], 
            $address_info['area_id']
        );

        return $address_pre . $address_info['address'];

	}
}

<?php
class AddressApiModel extends ApiModel
{
	/**
	 * 获取收货地址列表(cheqishi.address.getAddressList)
	 * @author wsq
	 * @param array $params 参数列表
	 * @return 成功返回$user_address_list，失败退出返回错误码
	 * @todo 获取收货地址列表(cheqishi.address.getAddressList)
	 */
	function getAddressList($params)
	{
		$user_id           = intval(session('user_id'));
		$user_address_obj  = new UserAddressModel();
        $user_obj          = new UserModel();

        $user_address_list = $user_address_obj->getUserAddressList(
            'user_address_id, province_id, city_id, area_id, mobile, address, realname, user_id', //feilds
            'user_id = ' . $user_id . ' AND isuse = 1 ',  //where
            'use_time DESC, addtime DESC' //orderBy
        );

		foreach ($user_address_list AS $k => $v)
		{
            $user_address_list[$k]['area_string'] = AreaModel::getAreaString(
                $v['province_id'], 
                $v['city_id'], 
                $v['area_id']
            );

			//unset($user_address_list[$k]['province_id']);
			//unset($user_address_list[$k]['city_id']);
			//unset($user_address_list[$k]['area_id']);
            //是否是默认地址判断
            $default_address_id = $user_obj->where('user_id = ' . $v['user_id'])
                ->getField('user_address_id');

            $user_address_list[$k]['is_default'] = $default_address_id == $v['user_address_id'] ? 1 : 0;
		}

		return $user_address_list;
	}

	/**
	 * 添加收货地址(cheqishi.address.addAddress)
	 * @author wsq
	 * @param array $params
	 * @return 成功返回'添加成功'，失败返回错误码
	 * @todo 添加收货地址(cheqishi.address.addAddress)
	 */
	function addAddress($params)
	{
        //基本信息
		$realname = $params['realname'];
		$mobile   = $params['mobile'];
		$user_id  = intval(session('user_id'));

		$address  = $params['address'];
		$area_id  = $params['area_id'];
		$city_id  = $params['city_id'];
		$province_id = $params['province_id'];

        $set_default = intval($params['set_default']); //是否设为默认地址

        if (!$mobile && !checkMobile($mobile)) {
			ApiModel::returnResult(42038, null, '手机号码格式有误');
		}

        //添加信息
		$user_address_obj = new UserAddressModel();
        $user_address_id = $user_address_obj->addUserAddress(
            array(
                'user_id'	=> $user_id,
                'realname'	=> $realname,
                'mobile'	=> $mobile,
                'city_id'	=> $city_id,
                'area_id'	=> $area_id,
                'address'	=> $address,
                'addtime'   => time(),
                'isuse'     => 1,
                'province_id'	=> $province_id,
            )
        );

        //查询是否存在默认地址,不存在则添加
        $user_obj           = new UserModel($user_id);
        $default_address_id = $user_obj->where('user_id = ' . $user_id)->getField('user_address_id');
        $user_address_info  = $user_address_obj->getUserAddressInfo(
            'user_address_id = ' . $default_address_id, //where
            'user_address_id' //field
        );

        //设置默认地址
        if ($user_address_id && ($set_default == 1 || !$user_address_info)) {
            $user_obj->setUserInfo(array(
                'user_address_id' => $user_address_id,
            ));
            $user_obj->saveUserInfo();
        }

		return '添加成功';
	}

	/**
	 * 修改收货地址(cheqishi.address.editAddress)
	 * @author wsq
	 * @param array $params
	 * @return 成功返回'修改成功'，失败返回错误码
	 * @todo 修改收货地址(cheqishi.address.editAddress)
	 */
	function editAddress($params)
	{
        //获取基本信息
		$user_address_id = $params['address_id'];
		$realname        = $params['realname'];
		$mobile          = $params['mobile'];
		$user_id         = intval(session('user_id'));

		$province_id     = $params['province_id'];
		$city_id         = $params['city_id'];
		$area_id         = $params['area_id'];
		$address         = $params['address'];

        $set_default     = intval($params['set_default']); //是否设为默认地址

        if (!$mobile && !checkMobile($mobile)) {
			ApiModel::returnResult(42038, null, '手机号码格式有误');
		}

        //查询条目是否存在
		$user_address_obj  = new UserAddressModel($user_address_id);
        $user_address_info = $user_address_obj->getUserAddressInfo(
            'user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id,  //where
            'user_address_id'  //field
        );

		if (!$user_address_info) {
			ApiModel::returnResult(42038, null, '收货地址信息不存在');

		}

        //保存修改信息
        $success = $user_address_obj->editUserAddress(
            array(
                'realname'	=> $realname,
                'mobile'	=> $mobile,
                'city_id'	=> $city_id,
                'area_id'	=> $area_id,
                'address'	=> $address,
                'user_id'	=> $user_id,
                'province_id'	=> $province_id,
            )
        );

        //查询是否存在默认地址,不存在则添加
        $user_obj           = new UserModel($user_id);
        $default_address_id = $user_obj->where('user_id = ' . $user_id)->getField('user_address_id');
        $user_address_info  = $user_address_obj->getUserAddressInfo(
            'user_address_id = ' . $default_address_id, //where
            'user_address_id' //field
        );

        //设置默认地址
        if ($user_address_id && ($set_default == 1 || !$user_address_info)) {
            $user_obj->setUserInfo(array(
                'user_address_id' => $user_address_id,
            ));
            $user_obj->saveUserInfo();
        }

		return '修改成功';
	}

	/**
	 * 获取收货地址信息(cheqishi.address.getAddressInfo)
	 * @author wsq
	 * @param array $params
	 * @return 成功返回$address_info，失败返回错误码
	 * @todo 获取收货地址信息(cheqishi.address.getAddressInfo)
	 */
	function getAddressInfo($params)
	{
		$user_address_id = intval($params['address_id']);
		$user_id         = intval(session('user_id'));

        //查询信息是否存在
		$user_address_obj = new UserAddressModel($user_address_id);
        if (!$user_address_id)
        {
            ApiModel::returnResult(42038, null, '收货地址信息不存在');
        }

        $user_address_info = $user_address_obj->getUserAddressInfo(
            'user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id,  //where
            'user_address_id, realname, mobile, province_id, city_id, area_id, address'  //field
        );

        if (!$user_address_info)
        {
            ApiModel::returnResult(42038, null, '收货地址信息不存在');
        }

        $user_address_info['area_string'] = AreaModel::getAreaString(
            $user_address_info['province_id'], 
            $user_address_info['city_id'], 
            $user_address_info['area_id']
        );

		return $user_address_info;
	}

	/**
	 * 获取省份列表(merchant.address.getProvinceList)
	 * @author clk
	 * @param array $params
	 * @return 成功返回$province_list，失败返回错误码
	 * @todo 获取省份列表(merchant.address.getProvinceList)
	 */
	function getProvinceList($params)
	{
		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();
		return $province_list;
	}

	/**
	 * 根据省份获取城市列表(merchant.address.getCityList)
	 * @author clk
	 * @param array $params
	 * @return 成功返回$city_list，失败返回错误码
	 * @todo 根据省份获取城市列表(merchant.address.getCityList)
	 */
	function getCityList($params)
	{
		$province_id = $params['province_id'];

		$city = M('address_city');
		$city_list = $city->field('city_id,city_name')->where('province_id = ' . $province_id)->select();

		return $city_list;
	}

	/**
	 * 根据城市获取地区列表(merchant.address.getAreaList)
	 * @author clk
	 * @param array $params
	 * @return 成功返回$area_list，失败返回错误码
	 * @todo 根据城市获取地区列表(merchant.address.getAreaList)
	 */
	function getAreaList($params)
	{
		$city_id = $params['city_id'];
		$area = M('address_area');
		$area_list = $area->field('area_id,area_name')->where('city_id = ' . $city_id)->select();

		return $area_list;
	}

	/**
	 * 计算运费
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回$express_fee，失败返回错误码
	 * @todo 计算运费
	 */
	function calExpressFee($params)
	{
		$city_id = $params['city_id'];
		$total_amount = $params['total_amount'];
		$total_weight = $params['total_weight'];
		$user_id = intval(session('user_id'));

		$city_obj = M('address_city');
		$city_info = $city_obj->where('city_id = ' . $city_id)->find();
		if (!$city_info)
		{
			ApiModel::returnResult(42042, null, '无效的city_id');
		}

		//调用物流模型ShippingCompanyModel的calculateShippingFee
		$default_express_company = $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY'];
		$shipping_company_obj = new ShippingCompanyModel();
		$express_fee = $shipping_company_obj->calculateShippingFee($default_express_company, $city_info['province_id'], $total_weight, $total_amount);

		$return_arr = array(
			'city_name'		=> $city_info['city_name'],
			'express_fee'	=> $express_fee,
		);

		return $return_arr;
	}

	/**
	 * 获取参数列表
	 * @author 姜伟
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		$params = array(
			'addAddress'	=> array(
				array(
					'field'		=> 'realname', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41038, 
					'empty_code'=> 44038, 
					'type_code'	=> 45038, 
				),
				array(
					'field'		=> 'mobile', 
					'min_len'	=> 11, 
					'max_len'	=> 11, 
					'type'		=> 'string', 
					'required'	=> true, 
					'len_code'	=> 40039, 
					'miss_code'	=> 41039, 
					'empty_code'=> 44039, 
					'type_code'	=> 45039, 
				),
				array(
					'field'		=> 'province_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41040, 
					'empty_code'=> 44040, 
					'type_code'	=> 45040, 
				),
				array(
					'field'		=> 'city_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41041, 
					'empty_code'=> 44041, 
					'type_code'	=> 45041, 
				),
				array(
					'field'		=> 'area_id', 
					'type'		=> 'int', 
					'required'	=> false, 
					'empty_code'=> 44042, 
					'type_code'	=> 45042, 
				),
				array(
					'field'		=> 'address', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41043, 
					'empty_code'=> 44043, 
					'type_code'	=> 45043, 
				),
				array(
					'field'		=> 'set_default', 
					'type'		=> 'int', 
					'required'	=> false, 
					'miss_code'	=> 41051, 
					'empty_code'=> 44051, 
					'type_code'	=> 45051, 
				),
			),
			'editAddress'	=> array(
				array(
					'field'		=> 'address_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41032, 
					'empty_code'=> 44032, 
					'type_code'	=> 45032, 
				),

				array(
					'field'		=> 'realname', 
					'type'		=> 'string', 
					'required'	=> false, 
					'miss_code'	=> 41038, 
					'empty_code'=> 44038, 
					'type_code'	=> 45038, 
				),
				array(
					'field'		=> 'mobile', 
					'min_len'	=> 11, 
					'max_len'	=> 11, 
					'type'		=> 'string', 
					'required'	=> false, 
					'len_code'	=> 40039, 
					'miss_code'	=> 41039, 
					'empty_code'=> 44039, 
					'type_code'	=> 45039, 
				),
				array(
					'field'		=> 'province_id', 
					'type'		=> 'int', 
					'required'	=> false, 
					'miss_code'	=> 41040, 
					'empty_code'=> 44040, 
					'type_code'	=> 45040, 
				),
				array(
					'field'		=> 'city_id', 
					'type'		=> 'int', 
					'required'	=> false, 
					'miss_code'	=> 41041, 
					'empty_code'=> 44041, 
					'type_code'	=> 45041, 
				),
				array(
					'field'		=> 'area_id', 
					'type'		=> 'int', 
					'required'	=> false, 
					'empty_code'=> 44042, 
					'type_code'	=> 45042, 
				),
				array(
					'field'		=> 'address', 
					'type'		=> 'string', 
					'required'	=> false, 
					'miss_code'	=> 41043, 
					'empty_code'=> 44043, 
					'type_code'	=> 45043, 
				),
				array(
					'field'		=> 'set_default', 
					'type'		=> 'int', 
					'required'	=> false, 
					'miss_code'	=> 41051, 
					'empty_code'=> 44051, 
					'type_code'	=> 45051, 
				),
			),
			'getCityList'	=> array(
				array(
					'field'		=> 'province_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41040, 
					'empty_code'=> 44040, 
					'type_code'	=> 45040, 
				),
			),
			'getAreaList'	=> array(
				array(
					'field'		=> 'city_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41041, 
					'empty_code'=> 44041, 
					'type_code'	=> 45041, 
				)
			),
			'calExpressFee'	=> array(
				array(
					'field'		=> 'city_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41041, 
					'empty_code'=> 44041, 
					'type_code'	=> 45041, 
				),
				array(
					'field'		=> 'total_weight', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41052, 
					'empty_code'=> 44052, 
					'type_code'	=> 45052, 
				),
				array(
					'field'		=> 'total_amount', 
					'type'		=> 'float', 
					'required'	=> true, 
					'miss_code'	=> 41053, 
					'empty_code'=> 44053, 
					'type_code'	=> 45053, 
				),
			),
			'getAddressInfo'	=> array(
				array(
					'field'		=> 'address_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41032, 
					'empty_code'=> 44032, 
					'type_code'	=> 45032, 
				),
			),
		);

		return $params[$func_name];
	}
}

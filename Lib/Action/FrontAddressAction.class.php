<?php 
class FrontAddressAction extends FrontAction{
	function _initialize() 
	{
		if (ACTION_NAME != 'save_address' && ACTION_NAME != 'save_wx_address')
		{
			parent::_initialize();
		}
	}

	//获取公共数据
	function get_common_data($opt)
	{
		//获取省份列表
		$province = M('address_province');
		$province_list = $province->field('province_id, province_name')->select();
		$this->assign('province_list', $province_list);
		$this->assign('opt', $opt);
	}

	//切换城市
	public function city_list()
	{
		/*** 获取全部城市列表begin ***/
		$area_obj = new AreaModel();
		#$city_list = $area_obj->getCityListGroupByPy();
		#$this->assign('city_list', $city_list);
		$area_list = $area_obj->getAreaListGroupByPy();
		$this->assign('area_list', $area_list);
		/*** 获取全部城市列表end ***/

		/*** 获取已开通城市列表begin ***/
		#$opened_city_list = $area_obj->getOpenedCityList();
		#$this->assign('opened_city_list', $opened_city_list);
		$opened_area_list = $area_obj->getOpenedAreaList();
		$this->assign('opened_area_list', $opened_area_list);
		/*** 获取已开通城市列表end ***/

		/*** 获取热门城市列表begin ***/
		#$hot_city_list = $area_obj->getHotCityList();
		#$this->assign('hot_city_list', $hot_city_list);
		$hot_area_list = $area_obj->getHotAreaList();
		$this->assign('hot_area_list', $hot_area_list);
		/*** 获取热门城市列表end ***/
		
		/*** 获取最近访问城市列表begin ***/
		$city_change_log_obj = new CityChangeLogModel();
		$city_change_log_list = $city_change_log_obj->getCityChangeLogDistList('end_area_id', 'user_id = ' . intval(session('user_id')), 'addtime ASC', '6');
		$city_change_log_list = $city_change_log_obj->getListData($city_change_log_list);
		$this->assign('city_change_log_list', $city_change_log_list);
		/*** 获取最近访问城市列表end ***/

		/*** 获取用户当前默认城市begin ***/
		$default_area = $area_obj->getDefaultCity();
		$point = $area_obj->getLocation();
		$this->assign('point', $point);
		$area_obj = M('address_area');
		$area_name = $area_obj->where('area_id = ' . intval($default_area))->getField('area_name');
		$this->assign('default_area', $default_area);
		$this->assign('default_area_name', $area_name);
		$this->assign('default_area_id', intval($default_area));
		/*** 获取用户当前默认城市end ***/

		#echo "<pre>";
		#print_r($city_list);
		#print_r($opened_city_list);
		#print_r($hot_city_list);
		#print_r($area_list);
		#print_r($opened_area_list);
		#print_r($hot_area_list);
		#die;

		$this->assign('head_title', '切换城市');
		$this->display();
	}

	//地址管理列表页
	public function address_list()
	{
		$jump = I('jump');
		if($jump == 'cart'){
			$jump_link = '/FrontCart/cart';
		}else{
			$jump_link = '/FrontUser/personal_center';
		}
		$this->assign('jump', $jump_link);
		$this->assign('jump_type', $jump);
		$cart_url = session('cart_url');
		$user_id = intval(session('user_id'));
		vendor('wxpay.WxPayPubHelper');
		//获取参数
		$address_obj = new Address();
		$parameters = $address_obj->getParameters();
		$this->assign('parameters', $parameters);

		$user_address_obj = new UserAddressModel();
		$user_address_obj->setLimit(100);
		$user_address_list = $user_address_obj->getUserAddressList('user_address_id, province_id, city_id, area_id, mobile, address, realname', 'isuse = 1 AND user_id = ' . $user_id, 'addtime DESC, use_time DESC');
		$user_address_list = $user_address_obj->getListData($user_address_list);
		$user_default_addr = $user_address_obj->getDefaultAddress($user_id);
		$this->assign('user_address_list', $user_address_list);
		$this->assign('user_default_addr', $user_default_addr);
		$this->assign('head_title', '收货地址');
		#echo '<pre>';
		#print_r($user_default_addr);
		#die;
		//底部导航赋值
		$this->assign('nav', 'per_center');
		$this->assign('cart_url', $cart_url);
		$this->display();
	}

	//地址管理-添加地址
	public function add_address()
	{
		$jump = I('jump');
		$this->assign('jump', $jump);
		//$lon = session('lon');
		//$lat = session('lat');
		$user_id = intval(session('user_id'));		
		#$area_obj = new AreaModel();
		#$point = $area_obj->getLocation();
		#$this->assign('point', $point);
		
		//获取用户姓名和绑定手机
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('realname, mobile');
		
		$this->assign('user_info', $user_info);
		$this->get_common_data(1);
		$this->assign('head_title', '添加地址');
		//$this->assign('lon', $lon);
		//$this->assign('lat', $lat);
		$this->display(APP_PATH . 'Tpl/FrontAddress/edit_address.html');
	}

	//地址管理-修改地址测试
	public function edit_address_test()
	{
		$user_address_id = intval($this->_get('address_id'));
		$user_address_obj = new UserAddressModel($user_address_id);
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'user_address_id, province_id, city_id, area_id, mobile, realname, address, latitude, longitude');
		$this->assign('user_address_info', $user_address_info);
		
		$this->get_common_data(2);
		$this->assign('head_title', '修改地址');
		$this->display();
	}
	
	//地址管理-修改地址
	public function edit_address()
	{
		$jump = I('jump');
		$this->assign('jump', $jump);
		$lon = session('lon');
		$lat = session('lat');
		$user_address_id = intval($this->_get('address_id'));
		$user_address_obj = new UserAddressModel($user_address_id);
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'user_address_id, province_id, city_id, area_id, mobile, realname, address, building_id, latitude, longitude');
		if (!$user_address_info)
		{
			$redirect = U('/FrontAddress/address_list');
			$this->alert('对不起，不存在该收货地址！', $redirect);
		}
		$this->assign('user_address_info', $user_address_info);
		$this->get_common_data(2);
		$this->assign('keyword', $keyword);
		$this->assign('building_list', $building_list);
		$this->assign('lon', $lon);
		$this->assign('lat', $lat);
		$this->assign('head_title', '修改地址');
		$this->display();
	}

	//异步获取写字楼搜索结果列表
	public function get_building_list()
	{
		$keyword = I('post.keyword');
		$user_id = intval(session('user_id'));
		//获取搜索小区写字楼列表
		$where = 'isuse = 1';
		$where .= ' AND building_name LIKE "%' . $keyword . '%"';
		
		
		//商品总数
		#$total = $item_obj->getItemNum($where);

		if ($keyword && $user_id)
		{
			$building_obj = new BuildingModel();
			$building_list = $building_obj->getBuildingList('building_id, building_name, longitude, latitude', $where, 'addtime DESC');
			echo json_encode($building_list);
			exit;
		}

		exit('failure');
	}
	
	//商户地图详细页
	public function map_detail()
	{
		$merchant_id = intval($this->_request('merchant_id'));
		$merchant_obj = new MerchantModel();
		$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $merchant_id, 'shop_name, longitude, latitude');
		if (!$merchant_info)
		{
			$this->alert('对不起，不存在该商户！');
		}
		$this->assign('merchant_info', $merchant_info);

		$this->assign('head_title', $merchant_info['shop_name'] . '位置');
		$this->display();
	}
	

	//保存地址
	function save_address()
	{
		$user_address_id = $this->_post('address_id');
		$realname = $this->_post('realname');
		$mobile = $this->_post('mobile');
		$province_id = intval($this->_post('province_id'));
		$city_id = intval($this->_post('city_id'));
		$area_id = intval($this->_post('area_id'));
		$address = $this->_post('address');
		$user_id = intval(session('user_id'));

		if ($realname && $mobile && $province_id && $city_id && $user_id && $address)
		{
			$add = true;
			$user_address_obj = new UserAddressModel($user_address_id);
			if ($user_address_id)
			{
				$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id, 'user_address_id');
				$add = $user_address_info ? false : true;
			}

			$arr = array(
				'realname'		=> $realname,
				'mobile'		=> $mobile,
				'province_id'	=> $province_id,
				'city_id'		=> $city_id,
				'area_id'		=> $area_id,
				'address'		=> $address,
				'user_id'		=> $user_id,
			);

			$success = false;
			if($add)
			{
				//添加
				$success = $user_address_obj->addUserAddress($arr);
				$user_address_id = $success;
			}
			else
			{
				//修改
				$success = $user_address_obj->editUserAddress($arr);
			}

			/*** 自动保存用户信息begin ***/
			//$user_obj = new UserModel($user_id);
			//$user_info = $user_obj->getUserInfo('mobile, realname');
			//$user_data = array();
			//if (!$user_info['mobile'])
			//{
			//	//用户未填写手机号，自动为其保存
			//	$user_data['mobile'] = $mobile;
			//}

			//if (!$user_info['realname'])
			//{
			//	//用户未填写姓名，自动为其保存
			//	$user_data['realname'] = $realname;
			//}

			//if (!empty($user_data))
			//{
			//	//若需要保存的用户信息不为空，执行保存操作
			//	$user_obj->setUserInfo($user_data);
			//	$user_obj->saveUserInfo();
			//}
			/*** 自动保存用户信息end ***/

			echo $success ? $success : 'failure';
			exit;
		}
		log_file('edit_address_failure1');
		exit('failure');
	}

	//保存地址
	function save_wx_address()
	{
		$realname = $this->_post('realname');
		$mobile = $this->_post('mobile');
		$area_id = $this->_post('area_id');
		$address = $this->_post('address');
		$user_id = intval(session('user_id'));

		if ($realname && $mobile && ctype_digit($area_id) && $user_id && $address)
		{
			//获取province_id & city_id
			$province_id = 0;
			$city_id = 0;

			//先从地区级获取
			$area_obj = M('address_area');
			$area_info = $area_obj->where('area_id = ' . $area_id)->find();
			if ($area_info)
			{
				//说明是地区是三级的(非直辖市)
				$city_id = $area_info['city_id'];
			}
			else
			{
				//说明是地区是两级的(直辖市)
				$city_id = $area_id;
				$area_id = 0;
			}

			//获取省份ID
			$city_obj = M('address_city');
			$city_info = $city_obj->where('city_id = ' . $city_id)->find();
			$province_id = $city_info ? $city_info['province_id'] : 0;

			$user_address_obj = new UserAddressModel();
			$arr = array(
				'realname'		=> $realname,
				'mobile'		=> $mobile,
				'province_id'	=> $province_id,
				'city_id'		=> $city_id,
				'area_id'		=> $area_id,
				'address'		=> $address,
				'user_id'		=> $user_id,
			);

			//添加
			$success = $user_address_obj->addUserAddress($arr);
			$return_arr = array(
				'province_id'		=> $province_id,
				'city_id'		=> $city_id,
				'area_id'		=> $area_id,
				'user_address_id'	=> $success,
				'city'			=> $city_info['city_name'],
			);

			echo $success ? json_encode($return_arr) : 'failure';
			exit;
		}

		exit('failure');
	}
	
	//删除地址
	function delete_address()
	{
		$user_address_id = $this->_post('address_id');
		$user_id = intval(session('user_id'));

		if ($user_id && is_numeric($user_address_id))
		{
			$user_address_obj = new UserAddressModel($user_address_id);
			$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id, 'user_address_id');
			$success = false;
			if ($user_address_info)
			{
				$success = $user_address_obj->delUserAddress($user_address_id);
			}
			#echo 'user_id = ' . $user_id;
			#echo $user_address_obj->getLastSql();
			#die;

			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//根据城市名称和区县名称获取区县ID
	function get_area_id()
	{
		$area = $this->_post('area');
		$city = $this->_post('city');
		$user_id = intval(session('user_id'));

		if ($area && $city && $user_id)
		{
			$area_obj = new AreaModel();
			$area_id = $area_obj->getAreaID($area, $city);
			$this->json_exit(array('area_id' => $area_id));
		}

		$this->json_exit(array('area_id' => $area_id));
	}

	//城市切换写日志
	function log_city_change()
	{
		$area_id = $this->_post('area_id');
		$ip_area_id = $this->_post('ip_area_id');
		$default_area = $this->_post('default_area');
		$user_id = intval(session('user_id'));
		session('area_id', $area_id);

		if ($area_id && $ip_area_id && default_area && $user_id)
		{
			$city_change_log_obj = new CityChangeLogModel();
			$arr = array(
				'user_id'		=> $user_id,
				'ip_area_id'	=> $ip_area_id,
				'end_area_id'	=> $area_id,
				'start_area_id'	=> $default_area,
			);
			$success = $city_change_log_obj->addCityChangeLog($arr);
			$this->json_exit($success ? 'success' : 'failure');
		}

		$this->json_exit('failure');
	}
	//设置用户默认地址 zlf
	function set_default_address()
	{
		$user_addr_id = $this->_post('user_address_id');
		$user_id = intval(session('user_id'));
		
		if (ctype_digit($user_addr_id) && $user_id)
		{
			$arr = array(
				'user_address_id'	=> $user_addr_id,
			);
			$user_obj = new UserModel($user_id);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();
			#echo $user_obj->getLastSql();
            $user_address_id = D('User')->where('user_id = %d', $user_id)->getField('user_address_id');
#log_file($user_obj->getLastSql());
			//默认地址保存会话中
			if($success) {
				session('user_address_id', $user_addr_id);
			}else{
			}
			
			echo is_numeric($success) ? 'success' : 'failure1';
			exit;
		}

		exit('failure');
	}
	//传入购物车地址 zlf
	function set_cart_url()
	{
		$cart_url = $this->_post('cart_url');
		if($cart_url){
			session('cart_url', $cart_url);
			echo 'success';
			exit;
		}
		exit('failure');
		
	}
}

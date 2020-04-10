<?php 
class FrontPcAddressAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
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

	//地址管理列表页
	public function address_list()
	{
		$user_id = intval(session('user_id'));
		$user_address_obj = new UserAddressModel();
		$user_address_list = $user_address_obj->getUserAddressList('user_address_id, province_id, city_id, area_id, mobile, address, realname', 'user_id = ' . $user_id . ' AND isuse = 1 ', 'use_time DESC, addtime DESC');
		$user_address_list = $user_address_obj->getListData($user_address_list);
		$this->assign('user_address_list', $user_address_list);
		$this->assign('head_title', '收货地址管理');
		$this->display();
	}

	//地址管理-添加地址
	public function add_address()
	{
		$this->get_common_data(1);
		$user_id       = intval(session('user_id'));
        $realname      = $this->_post('realname');
        $mobile        = $this->_post('mobile');
        $province_id   = $this->_post('province_id');
        $city_id       = $this->_post('city_id');
        $area_id       = $this->_post('area_id');
        $address       = $this->_post('address');
        $default       = $this->_post('defalut');
        $user_id       = intval(session('user_id'));
        $Users         = new UserModel($user_id);
        $default_address_id = $Users->where('user_id = ' . $user_id)->getField('user_address_id');
        if (!$default_address_id) $this->assign('defalut',1);

        if ($this->_post()) {

            $this->assign('realname', $realname);
            $this->assign('mobile', $mobile);
            $this->assign('address', $address);

            $city_obj      = M('address_city');
            $area_obj      = M('address_area');
            $city_list     = $city_obj->field('city_id, city_name')->where('province_id = '. $province_id )->select();
            $area_list     = $area_obj->field('area_id, area_name')->where('city_id = '. $city_id )->select();
            $this->assign('area_list', $area_list);
            $this->assign('city_list', $city_list);

            if (!$realname) $this->error('请输入收货人姓名!');
            if (!$mobile || !checkMobile($mobile)) $this->error('请输入正确的手机号码!' );
            if (!$province_id && !$city_id && !$area_id) $this->error('请选择省市区!');
            if (!$address) $this->error('请输入具体的收货地址');
        
            $user_address_data = array(
                'realname'            => $realname,
                'mobile'              => $mobile,
                'user_id'             => $user_id,
                'province_id'         => intval($province_id),
                'city_id'             => intval($city_id),
                'area_id'             => intval($area_id),
                'address'             => $address,
                'addtime'             => time(),
                'isuse'               => 1,
            );

            $user_address_obj   = new UserAddressModel();
            $user_address_id    = $user_address_obj->addUserAddress($user_address_data);

            //设置为默认收货地址；
            if ($default == "yes" || !$default_address_id) {
                $Users = new UserModel($user_id);
                $Users->setUserInfo(array('user_address_id' => $user_address_id));
                $Users->saveUserInfo();
            }

            if ($user_address_id) {
                $this->success('恭喜,添加地址成功!', U('/FrontAddress/address_list'));
            } else {
                $this->error('对不起,添加地址失败，请稍候再试!');
            }

        }


		$this->assign('head_title', '地址管理-添加地址');
		$this->display(APP_PATH . 'Tpl/FrontAddress/edit_address.html');
	}

	//地址管理-修改地址
	public function edit_address()
	{
        // 获取地址信息
		$user_address_id   = intval($this->_get('address_id'));
		$user_address_obj  = new UserAddressModel($user_address_id);
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'user_address_id, province_id, city_id, area_id, mobile, realname, address');

		if (!$user_address_info)
		{
			$redirect = U('/FrontAddress/address_list');
			$this->alert('对不起，不存在该收货地址！', $redirect);
		}

        //鉴定是否为默认收获地址
        $user_id     = intval(session('user_id'));
        $Users       = new UserModel($user_id);
        $default_address_id = $Users->where('user_id = ' . $user_id)->getField('user_address_id');
        if (!$default_address_id || $user_address_id == $default_address_id) $this->assign('defalut', 1);

        $this->assign('realname', $user_address_info['realname']);
        $this->assign('mobile', $user_address_info['mobile']);
        $this->assign('province_id', $user_address_info['province_id']);
        $this->assign('city_id', $user_address_info['city_id']);
        $this->assign('area_id', $user_address_info['area_id']);
        $this->assign('address', $user_address_info['address']);

        $city_obj      = M('address_city');
        $area_obj      = M('address_area');
        $city_list     = $city_obj->field('city_id, city_name')->where('province_id = '. $user_address_info['province_id'])->select();
        $area_list     = $area_obj->field('area_id, area_name')->where('city_id = '. $user_address_info['city_id'])->select();
        $this->assign('area_list', $area_list);
        $this->assign('city_list', $city_list);


        if ($this->_post()) {
            $user_id       = intval(session('user_id'));
            $realname      = $this->_post('realname');
            $mobile        = $this->_post('mobile');
            $province_id   = $this->_post('province_id');
            $city_id       = $this->_post('city_id');
            $area_id       = $this->_post('area_id');
            $address       = $this->_post('address');
            $default       = $this->_post('defalut');

            if (!$realname) $this->error('请输入收货人姓名!');
            if (!$mobile || !checkMobile($mobile)) $this->error('请输入正确的手机号码!' );
            if (!$province_id && !$city_id && !$area_id) $this->error('请选择省市区!');
            if (!$address) $this->error('请输入具体的收货地址');

        
            //保存地址数据
            $user_address_data = array(
                'realname'            => $realname,
                'mobile'              => $mobile,
                'user_id'             => $user_id,
                'province_id'         => intval($province_id),
                'city_id'             => intval($city_id),
                'area_id'             => intval($area_id),
                'address'             => $address,
                'addtime'             => time(),
                'isuse'               => 1,
            );

            if ($user_address_obj->editUserAddress($user_address_data)) {

                //设置为默认收货地址；
                $default_address_id = $Users->where('user_id = ' . $user_id)->getField('user_address_id');
                if ($default == "yes" || !$default_address_id) {
                    $Users->setUserInfo(array('user_address_id' => $user_address_id));
                    $Users->saveUserInfo();
                } 

                $this->success('恭喜, 修改地址成功!', U('/FrontAddress/address_list'));
            } else {
                $this->error('对不起,修改地址失败，请稍候再试!');
            }
        }
		$this->assign('user_address_info', $user_address_info);

		$this->get_common_data(2);
		$this->assign('head_title', '收货地址管理-修改地址');
		$this->display();
	}

	//保存地址
	function save_address()
	{
		$user_address_id = $this->_post('address_id');
		$realname = $this->_post('realname');
		$mobile = $this->_post('mobile');
		$province_id = $this->_post('province_id');
		$city_id = $this->_post('city_id');
		$area_id = $this->_post('area_id');
		$address = $this->_post('address');
		$user_id = intval(session('user_id'));

		if ($realname && $mobile && ctype_digit($province_id) && ctype_digit($city_id) && ctype_digit($area_id) && $user_id && $address)
		{
			$add = true;
			$user_address_obj = new UserAddressModel($user_address_id);
			if ($user_address_id)
			{
				$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id, 'user_address_id');
				$add = $user_address_info ? false : true;
			}

			$arr = array(
				'realname'	=> $realname,
				'mobile'	=> $mobile,
				'province_id'	=> $province_id,
				'city_id'	=> $city_id,
				'area_id'	=> $area_id,
				'address'	=> $address,
				'user_id'	=> $user_id,
			);

			$success = false;
			if ($add)
			{
				//添加
				$success = $user_address_obj->addUserAddress($arr);
			}
			else
			{
				//修改
				$success = $user_address_obj->editUserAddress($arr);
			}

			echo $success ? $success : 'failure';
			exit;
		}

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

    //删除收货地址
    //@author wsq
    public function del_address ()
    {
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$user_address_obj = new UserAddressModel();

			if($user_address_obj->delUserAddress($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，地址删除成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，地址删除失败，请稍后重试！');
		}
    }
}

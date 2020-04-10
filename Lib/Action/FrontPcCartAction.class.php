<?php 
class FrontPcCartAction extends FrontPcAction
{
	function _initialize() 
	{
		parent::_initialize();
	}

	/**
	 * 购物车页面
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 根据展示当前用户购物车内商品获取促销信息，将促销信息和购物车商品信息一同传到前台
	 */
	public function cart()
	{
		//获取购物车内商品列表
		$user_id = intval(session('user_id'));
		$where = ' AND is_integral = 0';
		$cart_obj = new ShoppingCartModel();
		$cart_list = $cart_obj->getShoppingCartList('shopping_cart_id, item_id, item_package_id, mall_price, real_price, number, total_price, item_sku_price_id, item_name, property, small_pic, integral_num', $where);
		$item_obj = new ItemModel();
		$item_package_obj = new ItemPackageModel();
		#echo "<pre>";print_r($cart_list);die;
		foreach ($cart_list as $key => $value) {
			if($value['item_id'] == 0){
				$item_package_info = $item_package_obj->getItemPackageInfo('item_package_id = ' . $value['item_package_id'], 'integral_exchange_rate');
				$cart_list[$key]['integral_rate'] = $item_package_info['integral_exchange_rate'];
			}else{
				$item_info = $item_obj->getItemInfo('item_id = ' . $value['item_id'], 'integral_exchange_rate');
				$cart_list[$key]['integral_rate'] = $item_info['integral_exchange_rate'];
			}
			
		}
		$this->assign('cart_list', $cart_list);
		#echo "<pre>";print_r($cart_list);die;
		//购物车内商品总价
		$total_amount = $cart_obj->sumCartAmount($where);

		//购物车内商品总数量
		$total_num = $cart_obj->getShoppingCartNum($where);

		$this->assign('total_amount', $total_amount);
		$this->assign('total_num', $total_num);

		$this->assign('head_title', '购物车');
		$this->display();
	}

	/**
	 * 积分购物车页面
	 * @author zlf
	 * @param void
	 * @return void
	 * @todo 根据展示当前用户购物车内商品获取促销信息，将促销信息和购物车商品信息一同传到前台
	 */
	public function integral_cart()
	{
		//获取购物车内商品列表
		$user_id = intval(session('user_id'));
		$where   = ' AND is_integral = 1';
		$cart_obj = new ShoppingCartModel();
		$cart_list = $cart_obj->getShoppingCartList('shopping_cart_id, item_id, mall_price, real_price, number, total_price, item_name, small_pic', $where);
		$this->assign('cart_list', $cart_list);

		//购物车内商品总价和购物车内商品总数量
		$total_amount  = $cart_obj->sumCartAmount($where);
		$total_num     = $cart_obj->getShoppingCartNum($where);
        //用户总积分
        $user_id       = intval(session('user_id'));
        $Users         = new UserModel($user_id);
        $integral_left = $Users->where('user_id = ' . $user_id)->getField('left_integral');
        $user_address_id = $Users->where('user_id = ' . $user_id)->getField('user_address_id');
        $this->assign('integral_left', $integral_left);

        //购物车为空跳转
        if ($total_num == 0) $this->redirect('/FrontIntegral/integral_item'); 

        $submit = $this->_post('submit');
        if ($submit == 'submit') {
            if (!$user_address_id) {
                $this->error('地址不存在，请添加!', '/FrontAddress/add_address');
            }

            if ($integral_left < $total_amount) {
                $this->error('用户剩余积分不足', '/FrontCart/integral_cart');
            } else {
				$cart_list = $cart_obj->getListData($cart_list);
				if (!$cart_list)
				{
                    $this->error('购物车数据错误，请稍候', '/FrontCart/integral_cart');
				}

				//获取收货地址信息
				$user_address_obj  = new UserAddressModel();
				$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id . ' AND user_id = ' . $user_id);
				if (!$user_address_info)
				{
                    $this->error('收货地址错误,请修改默认地址信息', '/FrontAddress/add_address');
				}

				//订单信息数组
				$arr = array(
					'pay_amount'		=> $total_amount,
					'user_address_id'	=> $user_address_id,
					'payway'		    => '积分兑换',
                    'total_num'         => $total_num,
				);

                //添加到换购表
				$order_obj = new IntegralExchangeRecordModel();
				$success   = $order_obj->addOrder($arr, $cart_list);
                if ($success) {
                    $integral_obj = new IntegralModel();
                    $remark       = '积分兑换-' . $success;
                    $sort_id      = 3;
                    $integral     = -1 * $total_amount;
                    $integral_obj->addIntegral($user_id, $sort_id, $integral, $remark);

                    $this->success('积分换购成功', '/FrontIntegral/integral_exchange_record');
                } else {
                    $this->error('积分换购失败，请稍候再试!', '/FrontIntegral/integral_exchange_record');
                }
            }

        }

		$this->assign('total_amount', $total_amount);
		$this->assign('total_num', $total_num);

		$this->assign('head_title', '积分购物车');
		$this->display();
	}

	/**
	 * 删除购物车商品
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用购物车模型的deleteShoppingCart方法删除购物车中的一个商品
	 */
	public function delete_cart()
	{
		$shopping_cart_id = intval($this->_post('cart_id'));

		if ($shopping_cart_id)
		{
			//调用购物车模型的deleteShoppingCart方法删除购物车中的一个商品
			$cart_model = new ShoppingCartModel($shopping_cart_id);
			$success = $cart_model->deleteShoppingCart();

			echo $success ? 'success' : 'failure';
			exit;
		}

		echo 'failure';
	}

	/**
	 * 批量删除购物车商品
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取提交的购物车id列表，遍历之，对每一个购物车商品，调用购物车模型的deleteShoppingCart方法删除
	 */
	public function batch_delete_cart()
	{
		$shopping_cart_ids = $this->_post('shopping_cart_ids');
		$shopping_cart_ids = explode(',', $shopping_cart_ids);

		$i = 0;
		foreach ($shopping_cart_ids AS $v)
		{
			$shopping_cart_id = intval($v);
			if ($shopping_cart_id)
			{
				//调用购物车模型的deleteShoppingCart方法删除购物车中的一个商品
				$cart_model = new ShoppingCartModel($shopping_cart_id);
				$success = $cart_model->deleteShoppingCart();
				if ($success)
				{
					$i ++;
				}
			}
		}

		//若成功删除数量大于0，返回成功
		if ($i)
		{
			echo 'success';
			exit;
		}

		echo 'failure';
	}

	/**
	 * 更新购物车商品
	 * @author zlf
	 * @param void
	 * @return void
	 * @todo 调用购物车模型的editShoppingCart方法更新购物车中的一个商品
	 */
	public function cartItemNumChange()
	{
		$shopping_cart_id = intval($this->_post('cart_id'));
		$real_price = floatval($this->_post('real_price'));
		$integral_rate = floatval($this->_post('integral_rate'));
		$number = intval($this->_post('item_num'));
		$cart_obj = new ShoppingCartModel();

		if ($shopping_cart_id && $real_price && $number)
		{
			$total_price = $real_price * $number;
			$integral_num = $real_price * $integral_rate * $number / 100;
			$arr = array(
				'shopping_cart_id'	=> $shopping_cart_id,
				'total_price'	=> $total_price,
				'number'	=> $number,
				'integral_num'	=> $integral_num
			);

			//调用购物车模型的editShoppingCart方法更新购物车中的一个商品
			$cart_model = new ShoppingCartModel($shopping_cart_id);
			$success = $cart_model->editShoppingCart($arr);
			#echo "<pre>";echo $cart_model->getLastSql();die;
			$cart_arr = array();
			$cart_arr['total_num'] = $cart_obj->getShoppingCartNum(' AND is_integral = 0');
			$cart_arr['integral_num'] = $integral_num;

			echo $success ? json_encode($cart_arr) : 'failure';
			exit;
		}

		echo 'failure';
	}

	/**
	 * 添加购物车商品
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用购物车模型的addShoppingCart方法添加一个商品到购物车
	 */
	public function add_cart()
	{
		//接收post过来的数据
		$item_id = $this->_post('item_id');
		$package_tag = $this->_post('package_tag');
		$item_id = is_numeric($item_id) ? $item_id : intval(url_jiemi($item_id));
		$integral_num = floatval($this->_post('integral_num'));
		$item_name = $this->_post('item_name');
		$small_pic = $this->_post('small_pic');
		$item_str = $this->_post('item_str');
		$item_str = explode(';', $item_str);
		$first_order = $this->_post('first_order');
		$is_integral = $this->_post('is_integral');

		#$property = $this->_post('property');
		#$item_sku_price_id = intval($this->_post('item_sku_price_id'));
		#$real_price = floatval($this->_post('real_price'));
		#$total_price = floatval($this->_post('total_price'));
		#$number = intval($this->_post('number'));
		//从快速订单走就不判断
		if(!$first_order){
			if (!$item_id || !$small_pic || !$item_name || !count($item_str))
			{
				echo 'failure';
				exit;
			}
		}
		
				

		$cart_model = new ShoppingCartModel();
		$user_id = intval(session('user_id'));
		$added_num = '';
		$number_str = '';
		//是否走快速订单的判断
		if(!$first_order){
			foreach ($item_str AS $k => $v)
			{
				$v = explode(',', $v);
				$property = $v[0];
				$real_price = $v[1];
				$number = $v[2];
				$item_sku_price_id = $v[3];
				$total_price = floatval($v[1]) * intval($v[2]);
				$arr = array(
					'user_id'			=> $user_id,
					'cookie_value'		=> $GLOBALS['shopping_cart_cookie'],
					'real_price'		=> $real_price,
					'total_price'		=> $total_price,
					'integral_num'			=> $integral_num,
					'number'			=> $number,
					'addtime'			=> time(),
					'item_name'			=> $item_name,
					'property'			=> $property,
					'package_tag'			=> $package_tag,
					'is_integral'			=> $is_integral,
					'small_pic'			=> $small_pic
				);
				#echo "<pre>";
				#print_r($arr);
				#echo "</pre>";
				$success = $cart_model->addShoppingCart($item_id, $item_sku_price_id, $arr);
				$added_num .= $success ? $success . ',' : '';
				$number_str .= $success ? $number . ',' : '';
				#echo $cart_model->getLastSql();echo "<br>";
			}
		}else{
			foreach ($item_str AS $k => $v)
			{
				$v = explode(',', $v);
				$item_name = $v[0];
				$real_price = $v[1];
				$small_pic = $v[2];
				$number = $v[3];
				$item_id = $v[4];
				$integral_num = $v[5];
				$total_price = floatval($v[1]) * intval($v[3]);
				$item_sku_price_id = 0;
				$arr = array(
					'user_id'			=> $user_id,
					'cookie_value'		=> $GLOBALS['shopping_cart_cookie'],
					'real_price'		=> $real_price,
					'total_price'		=> $total_price,
					'integral_num'			=> $integral_num,
					'number'			=> $number,
					'addtime'			=> time(),
					'item_name'			=> $item_name,
					'property'			=> '',
					#'package_tag'			=> $package_tag,
					'small_pic'			=> $small_pic
				);
				$success = $cart_model->addShoppingCart($item_id, $item_sku_price_id, $arr);
				$added_num .= $success ? $success . ',' : '';
				$number_str .= $success ? $number . ',' : '';
			}
		}
		
		
		//获取购物车内商品总价格和商品总件数
		#$total_amount = $cart_model->sumCartAmount();
		#$total_item_num = $cart_model->getShoppingCartNum();
		#echo $added_num ? json_encode(array('total_amount' => $total_amount, 'total_item_num' => $total_item_num)) : 'failure';
		//返回购物车ID列表和数量列表
		$return_arr = array(
			'shopping_cart_id_list'	=> substr($added_num, 0, -1),
			'number_list'			=> substr($number_str, 0, -1),
		);
		echo $added_num ? json_encode($return_arr) : 'failure';
		exit;
	}

	/**
	 * 获取购物车内商品总件数和商品总金额
	 * @author 姜伟
	 * @param void
	 * @return json array 如array('total_num' => 3, 'total_amount' => 300.00)
	 * @todo 调用购物车模型的countCartItems获取购物车内商品总件数，调用sumCartAmount获取购物车内商品总金额
	 */
	public function getCartInfo()
	{
		$cart_info = array();

		$cart_model = new ShoppingCartModel();
		$cart_info['total_num'] = $cart_model->getShoppingCartNum();
		$cart_info['total_amount'] = $cart_model->sumCartAmount();

		echo json_encode($cart_info);
		exit;
	}
}

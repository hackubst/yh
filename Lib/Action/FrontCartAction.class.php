<?php 
class FrontCartAction extends FrontAction
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
		if($user_id != 0){
			$cart_obj = new ShoppingCartModel();
			$item_list = $cart_obj->getShoppingCartList('shopping_cart_id, item_id, mall_price, real_price, number, total_price, item_sku_price_id, item_name, property, small_pic, unit', '', 'addtime DESC');

			$item_obj = new ItemModel();
			foreach ($item_list AS $k => $v)
			{
				$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id']);
	            // wsq added 25/02/2016
	            // 如果库存不足或非上架错误，则不显示商品
	            if ($item_info['isuse'] == 0 || $item_info['stock'] == 0) {
	                unset($item_list[$k]);
	                continue;
	            }

				$mall_price = $item_info && $item_info['mall_price'] ? $item_info['mall_price'] : $v['mall_price'];
				$item_list[$k]['mall_price'] = $mall_price;
			}
			$this->assign('item_list', $item_list);

			#echo $cart_obj->getLastSql();
			#echo "<pre>";
			#print_r($item_list);
			#die;
	        $mobile_is_bind = D('User')->where('user_id=%d', intval($user_id))->getField('mobile');
	        $mobile_is_bind = $mobile_is_bind ? $mobile_is_bind : NULL;

			//购物车内商品总价
			$total_amount = $cart_obj->sumCartAmount();

			//购物车内商品总数量
			$total_num = $cart_obj->getShoppingCartNum();
			//获取地址信息
			$user_address_obj = new UserAddressModel();
			#$user_address_list = $user_address_obj->getUserAddressList('user_address_id, province_id, city_id, area_id, mobile, address, realname', 'user_id = ' . $user_id, 'addtime DESC, use_time DESC');
			#$user_address_list = $user_address_obj->getListData($user_address_list);
			$user_default_addr = $user_address_obj->getDefaultAddress($user_id);
			#echo "<pre>";
			#print_r($user_default_addr);
			#die;
	log_file('user_default_addr = ' . $user_default_addr['realname'], 'cart');
	log_file('item_list = ' . json_encode($item_list), 'cart');
			
			$this->assign('user_address_list', $user_address_list);
			$this->assign('user_default_addr', $user_default_addr);

			$this->assign('total_amount', $total_amount);
			$this->assign('total_num', $total_num);
		}
		
		session('cart_url',null);
		$this->assign('head_title', '购物车');
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
		$number = $this->_post('num');
		$item_id = is_numeric($item_id) ? $item_id : intval(url_jiemi($item_id));
		if (!$item_id)
		{
			echo 'failure';
			exit;
		}

		//获取商品信息
		$item_obj = new ItemModel();
		$item_info = $item_obj->getItemInfo('item_id = ' . intval($item_id), 'item_name, base_pic, mall_price, unit');
		if (!$item_info)
		{
			echo 'failure';
			exit;
		}

        require_cache('Common/func_item.php');
		$small_pic = small_img($item_info['base_pic']);

		$cart_model = new ShoppingCartModel();
		$user_id = intval(session('user_id'));
		$item_sku_price_id = 0;	//先置为0
		$property = '';	//先置为空字符串
		$added_num = '';
		$number_str = '';
		$arr = array(
			'user_id'			=> $user_id,
			'cookie_value'		=> $GLOBALS['shopping_cart_cookie'],
			'mall_price'		=> $item_info['mall_price'],
			'real_price'		=> $item_info['mall_price'],
			'total_price'		=> $item_info['mall_price'],
			'number'			=> 1,
			'unit'				=> $item_info['unit'],
			'addtime'			=> time(),
			'item_name'			=> $item_info['item_name'],
			'small_pic'			=> $item_info['base_pic'],
			'property'			=> $property,
		);
		if($number){
			$arr['number'] = $number;
		}
		$success = $cart_model->addShoppingCart($item_id, $item_sku_price_id, $arr);
		$added_num .= $success ? $success . ',' : '';
		$number_str .= $success ? '1,' : '';

		//获取购物车内商品总价格和商品总件数
		#$total_amount = $cart_model->sumCartAmount();
		#$total_item_num = $cart_model->getShoppingCartNum();
		#echo $added_num ? json_encode(array('total_amount' => $total_amount, 'total_item_num' => $total_item_num)) : 'failure';
		//返回购物车ID列表和数量列表
		$return_arr = array(
			'shopping_cart_id_list'	=> substr($added_num, 0, -1),
			'number_list'			=> substr($number_str, 0, -1),
			'totalPrice'			=> $cart_model->sumCartAmount(),
			'totalNum'				=> $cart_model->getShoppingCartNum(),
		);
		echo $added_num > 0 ? json_encode($return_arr) : 'failure';
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
		$cart_info['total_num'] = $cart_model->countCartItems();
		$cart_info['total_amount'] = $cart_model->sumCartAmount();

		echo json_encode($cart_info);
		exit;
	}

}

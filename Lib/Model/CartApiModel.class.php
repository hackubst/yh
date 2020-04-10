<?php
class CartApiModel extends ApiModel
{
	/**
	 * 获取购物车内商品总数(cheqishi.cart.getCartTotalNum)
	 * @author zlf
	 * @param array $params 参数列表
	 * @return 成功返回$total_num，失败退出返回错误码
	 * @todo 获取购物车内商品总数(cheqishi.cart.getCartTotalNum)
	 */
	function getCartTotalNum($params)
	{
		//购物车商品总数
		$cart_obj = new ShoppingCartModel();
		$total_num = $cart_obj->getShoppingCartNum();

		return $total_num;
	}

	/**
	 * 加购物车(cheqishi.cart.addCart)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回'添加成功'，失败返回错误码
	 * @todo 加购物车(cheqishi.cart.addCart)
	 */
	function addCart($params)
	{
		$item_id = $params['item_id'];
		$item_str = $params['item_str'];
		$item_str = explode(';', $item_str);
		$item_obj = new ItemModel();
		$item_info = $item_obj->getItemInfo('item_id = ' . $item_id, 'item_name, base_pic, weight');
		if (!$item_info)
		{
			ApiModel::returnResult(42025, null, '商品不存在');
		}

        require_cache('Common/func_item.php');
		$weight = intval($item_info['weight']);
		$item_name = $item_info['item_name'];
		$small_pic = small_img($item_info['base_pic']);

		$cart_model = new ShoppingCartModel();
		$user_id = intval(session('user_id'));
		$added_num = '';
		$number_str = '';

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
				'real_price'		=> $real_price,
				'total_price'		=> $total_price,
				'weight'			=> $weight,
				'number'			=> $number,
				'addtime'			=> time(),
				'item_name'			=> $item_name,
				'property'			=> $property,
				'small_pic'			=> $small_pic
			);

			$success = $cart_model->addShoppingCart($item_id, $item_sku_price_id, $arr);
			$added_num .= $success > 0 ? $success . ',' : '';
			$number_str .= $success > 0 ? $number . ',' : '';
			#echo $cart_model->getLastSql() . "<br>";
		}
		
		$return_arr = array(
			'shopping_cart_id_list'	=> substr($added_num, 0, -1),
			'number_list'			=> substr($number_str, 0, -1),
		);

		return $return_arr;
	}

	/**
	 * 获取购物车内商品列表(cheqishi.cart.getCartList)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$cart_list，失败返回错误码
	 * @todo 获取购物车内商品列表(cheqishi.cart.getCartList)
	 */
	function getCartList($params)
	{
		//获取购物车内商品列表
		$user_id = intval(session('user_id'));
		$cart_obj = new ShoppingCartModel();
		$cart_list = $cart_obj->getShoppingCartList('shopping_cart_id, item_id, mall_price, real_price, number, total_price, item_sku_price_id, item_name, property, small_pic');

		return $cart_list;
	}

	/**
	 * 将某商品从购物车内删除(cheqishi.cart.deleteCart)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回'删除成功'，失败返回错误码
	 * @todo 将某商品从购物车内删除(cheqishi.cart.deleteCart)
	 */
	function deleteCart($params)
	{
		$shopping_cart_id = intval($params['shopping_cart_id']);
		$cart_model = new ShoppingCartModel($shopping_cart_id);
		$success = $cart_model->deleteShoppingCart();

		return '删除成功';
	}

	/**
	 * 批量删除购物车商品(cheqishi.cart.batchDeleteCart)
	 * @author jw
	 * @param array $params
	 * @return 成功返回'删除成功'，失败返回错误码
	 * @todo 批量删除购物车商品(cheqishi.cart.batchDeleteCart)
	 */
	function batchDeleteCart($params)
	{
		$shopping_cart_ids = $params['shopping_cart_ids'];
		$cart_model = new ShoppingCartModel($shopping_cart_id);
		$success = $cart_model->deleteShoppingCart();

		return '删除成功';
	}

	/**
	 * 根据购物车ID字符串获取购物车内商品列表(cheqishi.cart.getCartListByIds)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$cart_list，失败返回错误码
	 * @todo 根据购物车ID字符串获取购物车内商品列表(cheqishi.cart.getCartListByIds)
	 */
	function getCartListByIds($params)
	{
		//获取购物车内商品列表
		$shopping_cart_id_str = $params['shopping_cart_ids'];
		$user_id = intval(session('user_id'));
		$cart_obj = new ShoppingCartModel();
		$cart_list = $cart_obj->getShoppingCartList('', ' AND shopping_cart_id IN (' . $shopping_cart_id_str . ')', 'addtime DESC');

		return $cart_list;
	}

	/**
	 * 获取参数列表
	 * @author zlf
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		$params = array(
			'addCart'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
				array(
					'field'		=> 'item_str', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41026, 
					'empty_code'=> 44026, 
					'type_code'	=> 45026, 
				),
			),
			'deleteCart'	=> array(
				array(
					'field'		=> 'shopping_cart_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41030, 
					'empty_code'=> 44030, 
					'type_code'	=> 45030, 
				),
			),
			'getCartListByIds'	=> array(
				array(
					'field'		=> 'shopping_cart_ids', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41031, 
					'empty_code'=> 44031, 
					'type_code'	=> 45031, 
				),
			),
			'batchDeleteCart'	=> array(
				array(
					'field'		=> 'shopping_cart_ids', 
					'type'		=> 'string', 
					'required'	=> true, 
					'miss_code'	=> 41031, 
					'empty_code'=> 44031, 
					'type_code'	=> 45031, 
				),
			),
		);

		return $params[$func_name];
	}
}

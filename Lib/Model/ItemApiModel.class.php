<?php
class ItemApiModel extends ApiModel
{
	/**
	 * 根据一级分类ID获取该分类下根据二级分类分组的商品列表
	 * @author zlf
	 * @param array $params 参数列表
	 * @return 成功返回$item_list，失败退出返回错误码
	 * @todo 根据一级分类ID获取该分类下根据二级分类分组的商品列表
	 */
	function getItemListGroupBySort($params)
	{
		/*** 获取商品列表begin ***/
		$class_id = isset($params['class_id']) ? intval($params['class_id']) : 0;
		if (!$class_id)
		{
			//获取种子分类ID
			$class_obj = new ClassModel();
			$class_info = $class_obj->getClassInfo('class_tag = "seed"', 'class_id');
			$class_id = $class_info ? $class_info['class_id'] : 0;
		}

		//种子列表
		$item_obj = new ItemModel();
		$seed_list = $item_obj->getItemListGroupBySort($class_id, 100);

		return $seed_list;
	}

	/**
	 * 根据条件获取商品列表(cheqishi.item.getItemList)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$item_list，失败返回错误码
	 * @todo 根据条件获取商品列表(cheqishi.item.getItemList)
	 */
	function getItemList($params)
	{
		$where1 = 'isuse = 1';
		$where = '';
		$item_name = isset($params['item_name']) ? $params['item_name'] : '';
		$class_id = isset($params['class_id']) ? intval($params['class_id']) : 0;
		$sort_id = isset($params['sort_id']) ? intval($params['sort_id']) : 0;
		$item_tag = isset($params['item_tag']) ? $params['item_tag'] : '';
		$promo_tag = isset($params['promo_tag']) ? $params['promo_tag'] : '';
		$firstRow = isset($params['firstRow']) ? intval($params['firstRow']) : 0;
		$item_num_per_page = $GLOBALS['config_info']['ITEM_NUM_PER_PAGE'];
		
		$where .= $item_name ? ' AND item_name LIKE "%' . $item_name . '%"' : '';
		$where .= $class_id ? ' AND class_id = ' . $class_id : '';
		$where .= $sort_id ? ' AND sort_id = ' . $sort_id : '';
		$where1 = $where1 . $where;
		$where = $where1;
		if($item_tag)
		{
			//获取套餐商品列表
			$item_package_obj = new ItemPackageModel();
	        $package_total = $item_package_obj->getItemPackageNum($where);
	        $item_package_obj->setStart($firstRow);
        	$item_package_obj->setLimit($item_num_per_page);
        	$item_package_list = $item_package_obj->getItemPackageList('item_package_id, item_name, base_pic, mall_price', $where, 'serial ASC,addtime DESC');
			$item_package_list = $item_package_obj->getListData($item_package_list);
			$item_info = array(
				'current_class_id'	=> $class_id,
				'total_num'			=> $package_total ? $package_total : 0,
				'nextFirstRow'		=> $firstRow + $item_num_per_page,
				'item_list'			=> $item_package_list,
			);
		}else{
			//促销商品
			if($promo_tag){
				$promo_item_obj = M('PromotionItemDiscountDetail');
		        $promo_item_list = $promo_item_obj->field('item_id')->select();
		        if ($promo_item_list) {
		            $item_ids = '';
		            foreach ($promo_item_list as $k => $v) {
		                $item_array[] =  $v['item_id']; 
		            }
		            $item_ids = implode(",", $item_array);
		            $where .= ' AND item_id IN (' . $item_ids . ')';
		        }
			}
			//获取商品列表
			$item_obj = new ItemModel();
			//总数
			$total = $item_obj->getItemNum($where);
			$item_obj->setStart($firstRow);
	        $item_obj->setLimit($item_num_per_page);
			$item_list = $item_obj->getItemList('item_id, item_name, base_pic, mall_price', $where, 'serial ASC,addtime DESC');
	log_file(arrayToString($params));
	log_file($where);
	log_file($total);
	log_file($item_obj->getLastSql());
			$item_list = $item_obj->getListData($item_list);
			#echo $item_obj->getLastSql();die;
			$item_info = array(
				'current_class_id'	=> $class_id,
				'total_num'			=> $total ? $total : 0,
				'nextFirstRow'		=> $firstRow + $item_num_per_page,
				'item_list'			=> $item_list,
			);
		}
		
		return $item_info;
	}

	/**
	 * 获取某商品基本信息(cheqishi.item.getItemInfo)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$item_info，失败返回错误码
	 * @todo 获取某商品基本信息(cheqishi.item.getItemInfo)
	 */
	function getItemInfo($params)
	{
		$item_id = $params['item_id'];
		$item_tag = isset($params['item_tag']) ? $params['item_tag'] : '';

		//获取用户等级
        $user_rank_info = D('User')->getUserInfo('user_rank_id', 'user_id = ' . intval(session('user_id')));
	    $user_rank = D('UserRank')->getUserRankInfo('user_rank_id = ' . $user_rank_info['user_rank_id'], 'discount');
	    if($item_tag){
	    	//套餐商品信息
			$item_package_obj = new ItemPackageModel($item_id);
			$item_package_info = $item_package_obj->getItemPackageInfo('item_package_id = ' . $item_id);
			if (!$item_package_info)
			{
				ApiModel::returnResult(42025, null, '套餐商品不存在');
			}
			//vip价格
            $item_package_info['vip_price'] = round($user_rank['discount'] * $item_package_info['mall_price'] / 100, 2);

            //积分可抵扣
            $item_package_info['integral_num'] = round($item_package_info['mall_price'] * $item_package_info['integral_exchange_rate'] / 100, 2);
	    }else{
	    	$item_obj = new ItemModel($item_id);
			$item_info = $item_obj->getItemInfo('item_id = ' . $item_id);
			if (!$item_info)
			{
				ApiModel::returnResult(42025, null, '商品不存在');
			}
		    //vip价格
		    $item_info['vip_price'] = round($user_rank['discount'] * $item_info['mall_price'] / 100, 2);
	        //积分可抵扣
	        $item_info['integral_num'] = round($item_info['mall_price'] * $item_info['integral_exchange_rate'] / 100, 2);

	    }
		

		return $item_info ? $item_info : $item_package_info;
	}

	/**
	 * 获取某商品主图列表(cheqishi.item.getItemPhotoList)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$item_photo_list，失败返回错误码
	 * @todo 获取某商品主图列表(cheqishi.item.getItemPhotoList)
	 */
	function getItemPhotoList($params)
	{
		$item_id = $params['item_id'];
		$item_tag = isset($params['item_tag']) ? $params['item_tag'] : '';
		if($item_tag){
			//套餐商品图片
			$item_package_photo_obj = new ItemPackagePhotoModel();
			$item_photo_list = $item_package_photo_obj->getPhotos($item_id);
		}else{
			//商品图片
			$item_photo_obj = new ItemPhotoModel();
			$item_photo_list = $item_photo_obj->getPhotos($item_id);
		}
		
		return $item_photo_list;
	}

	/**
	 * 获取某商品详情描述(cheqishi.item.getItemDetails)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$item_txt，失败返回错误码
	 * @todo 获取某商品详情描述(cheqishi.item.getItemDetails)
	 */
	function getItemDetails($params)
	{
		$item_id = $params['item_id'];
		//商品详情描述
		$item_txt_obj = new ItemTxtModel();
		$item_txt = html_entity_decode(stripslashes($item_txt_obj->getItemTxt($item_id)));		//商品详情
		$item_txt = preg_replace("/\\\&quot;/", "", $item_txt);

		return $item_txt;
	}

	/**
	 * 获取某套餐商品的商品列表(cheqishi.item.getPackageItemList)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$package_item_list，失败返回错误码
	 * @todo 获取某套餐商品的商品列表(cheqishi.item.getPackageItemList)
	 */
	function getPackageItemList($params)
	{
		$item_id = $params['item_id'];
		//套餐商品的商品列表
		$item_obj = new ItemModel();
		$package_item_obj = new ItemPackageDetailModel();
		$package_item_list = $package_item_obj->getPackageItemList('item_id, item_name, mall_price','item_package_id = ' . $item_id);
		if (!$package_item_list)
		{
			ApiModel::returnResult(42025, null, '套餐商品不存在');
		}
		$package_item_list = $item_obj->getListData($package_item_list);

		return $package_item_list;
	}

	/**
	 * 获取商品规格列表(cheqishi.item.getItemSku)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$item_sku_list，失败返回错误码
	 * @todo 获取商品规格列表(cheqishi.item.getItemSku)
	 */
	function getItemSku($params)
	{
		$item_id = $params['item_id'];
		$item_sku_obj = new ItemSkuModel();
		$item_sku_list = $item_sku_obj->itemSkus($item_id);		
	
		return $item_sku_list;
	}

	/**
	 * 获取商品促销信息(cheqishi.item.getItemPromoInfo)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$promo_discount_info，失败返回错误码
	 * @todo 获取商品促销信息(cheqishi.item.getItemPromoInfo)
	 */
	function getItemPromoInfo($params)
	{
		$item_id = $params['item_id'];
		$promo_item_obj = M('PromotionItemDiscountDetail');
		$promo_discount_obj = M('PromotionItemDiscount');
		$promo_item_info = $promo_item_obj->field('item_id, promotion_item_discount_id')->where('item_id = ' . $item_id)->find();
		if (!$promo_item_info)
		{
			ApiModel::returnResult(42025, null, '商品促销信息不存在');
		}
		
		$promo_discount_info = $promo_discount_obj->field('')->where('isuse = 1 AND promotion_item_discount_id = ' . $promo_item_info['promotion_item_discount_id'])->find();			
	
		return $promo_discount_info;
	}

	/**
	 * 获取某种子状态列表(cheqishi.item.getSeedStateList)
	 * @author zlf
	 * @param array $params
	 * @return 成功返回$seed_state_list，失败返回错误码
	 * @todo 获取某种子状态列表(cheqishi.item.getSeedStateList)
	 */
	function getSeedStateList($params)
	{
		$item_id = $params['item_id'];
		$is_diy = !isset($params['is_diy']) ? 0 : $params['is_diy'];
		$is_diy = $is_diy == 0 ? 0 : 1;

		//获取种子状态列表
		$seed_state_obj = new SeedStateModel();
		$seed_state_list = $seed_state_obj->getSeedStateList('seed_state_id, state, outside_temperature, humidity, illuminance_limit, img_path', 'seed_id = ' . $item_id, 'state ASC');
		if ($is_diy)
		{
			$user_id = intval(session('user_id'));
			$planter_seed_state_obj = new PlanterSeedStateModel();
			foreach ($seed_state_list AS $k => $v)
			{
				$planter_seed_state_info = $planter_seed_state_obj->getPlanterSeedStateInfo('user_id = ' . $user_id . ' AND seed_id = ' . $item_id . ' AND state = ' . $v['state'], 'outside_temperature, humidity, illuminance_limit');
				if ($planter_seed_state_info)
				{
					foreach ($planter_seed_state_info AS $key => $val)
					{
						$seed_state_list[$k][$key] = $val;
					}
				}
				$seed_state_list[$k]['outside_temperature'] /= 10;
				$seed_state_list[$k]['humidity'] /= 10;
			}
		}
		#echo "<pre>";
		#print_r($seed_state_list);
		#die;

		return $seed_state_list;
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
			'getItemListGroupBySort'	=> array(
				array(
					'field'		=> 'class_id', 
				),
			),
			'getItemList'	=> array(
				array(
					'field'		=> 'firstRow', 
				),
				array(
					'field'		=> 'class_id', 
				),
				array(
					'field'		=> 'sort_id', 
				),
				array(
					'field'		=> 'item_tag', 
				),
				array(
					'field'		=> 'promo_tag', 
				),
				array(
					'field'		=> 'item_name', 
				),
			),
			'getItemInfo'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
				array(
					'field'		=> 'item_tag', 
				),
			),
			'getItemPhotoList'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
				array(
					'field'		=> 'item_tag', 
				),
			),
			'getItemDetails'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
			),
			'getPackageItemList'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
			),
			'getItemSku'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
			),
			'getItemPromoInfo'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
			),
			'getSeedStateList'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'type_code'	=> 45025, 
					'required'	=> true, 
					'miss_code'	=> 41025, 
				),
				array(
					'field'		=> 'is_diy', 
				),
			),
		);

		return $params[$func_name];
	}
}

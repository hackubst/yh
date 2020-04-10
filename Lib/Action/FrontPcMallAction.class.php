<?php 
class FrontPcMallAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_page = 16;
		$this->collect_num_per_page = 10;
		$this->item_num_per_collect_page = $GLOBALS['config_info']['ITEM_NUM_PER_COLLECT_PAGE'];
	}

	//获取查询条件
	private function get_search_condition()
	{
		$where = '';
		$item_name = I('cqs');
		$class_id = $this->_request('class_id');
		$sort_id = $this->_request('sort_id');

		$where .= $item_name ? ' AND item_name LIKE "%' . $item_name . '%"' : '';
		$where .= ctype_digit($class_id) ? ' AND class_id = ' . $class_id : '';
		$where .= ctype_digit($sort_id) ? ' AND sort_id = ' . $sort_id : '';
		
		/*$this->assign('class_id', $class_id);
		$this->assign('sort_id', $sort_id);*/
		$this->assign('item_name', $item_name);

		return $where;
	}

	//商品详情页
	public function item_detail()
	{
		$user_id = intval(session('user_id'));
		$item_id = $this->_get('item_id');
		$package_tag = $this->_get('package');
		//获取用户等级
        $user_rank_info = D('User')->getUserInfo('user_rank_id', 'user_id = ' . $user_id);
        $user_rank_obj = new UserRankModel;
	    $user_rank = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_rank_info['user_rank_id'], 'discount');

        //引入公共方法
		require_cache('Common/func_item.php');

		if($item_id){
			if($package_tag == 1){
				//套餐商品信息
				$item_package_obj = new ItemPackageModel($item_id);
				$item_package_info = $item_package_obj->getItemPackageInfo('item_package_id = ' . $item_id);
				if (!$item_package_info)
				{
					$redirect = U('/FrontMall/item_list/package/1');
					$this->alert('对不起，商品不存在！', $redirect);
				}
				//vip价格
	            $item_package_info['vip_price'] = round($user_rank['discount'] * $item_package_info['mall_price'] / 100, 2);

	            //积分可抵扣
	            $item_package_info['integral_num'] = round($item_package_info['mall_price'] * $item_package_info['integral_exchange_rate'] / 100, 2);

				//商品图片
				$item_photo_obj = new ItemPackagePhotoModel();
				$item_photo_list = $item_photo_obj->getItemPhotoList('base_pic', 'item_id = ' . $item_id, 'is_default DESC');
				foreach ($item_photo_list as $k => $v) {
					$item_photo_list[$k]['small_img'] = small_img($v['base_pic']);		
				}
				$this->assign('item_photo_list', $item_photo_list);

				//小图用于传值到购物车
				$item_package_info['small_pic'] = small_img($item_package_info['base_pic']);

				//套餐内商品列表
				$package_item_obj = new ItemPackageDetailModel();
				$package_item_list = $package_item_obj->getPackageItemList('item_id, item_name, mall_price, market_price','item_package_id = ' . $item_id);
				$package_item_list = D('Item')->getListData($package_item_list);
				#echo "<pre>";echo $package_item_obj->getLastSql();print_r($package_item_list);die;
				$this->assign('package_item_list', $package_item_list);

				$this->assign('item_package_info', $item_package_info);
				$this->assign('package_tag', $package_tag);
				$this->assign('head_title', $item_package_info['item_name']);


			}else{
				//普通商品信息
				$item_obj = new ItemModel($item_id);
				$item_info = $item_obj->getItemInfo('item_id = ' . $item_id);
				if (!$item_info)
				{
					$redirect = U('/FrontMall/item_list');
					$this->alert('对不起，商品不存在！', $redirect);
				}
				//vip价格
	            $item_info['vip_price'] = round($user_rank['discount'] * $item_info['mall_price'] / 100, 2);

	            //积分可抵扣
	            $item_info['integral_num'] = round($item_info['mall_price'] * $item_info['integral_exchange_rate'] / 100, 2);
	            
	            //所属品牌
	            if($item_info['brand_id']){
	            	$Brand = D('Brand');
	            	$brand_name = D('Brand')->getBrandField($item_info['brand_id'], 'brand_name');
	            }	    
	            $this->assign('item_brand', $brand_name);     

				//商品图片
				$item_photo_obj = new ItemPhotoModel();
				$item_photo_list = $item_photo_obj->getItemPhotoList('base_pic', 'item_id = ' . $item_id, 'is_default DESC');
				foreach ($item_photo_list as $k => $v) {
					$item_photo_list[$k]['small_img'] = small_img($v['base_pic']);		
				}
				$this->assign('item_photo_list', $item_photo_list);

				//小图用于传值到购物车
				$item_info['small_pic'] = small_img($item_info['base_pic']);


				//商品详情描述
				$item_txt_obj = new ItemTxtModel();
				$item_txt = html_entity_decode(stripslashes($item_txt_obj->getItemTxt($item_id)));		//商品详情
				$item_txt = preg_replace("/\\\&quot;/", "", $item_txt);               //商品详情
				$this->assign('item_txt', $item_txt);

				//商品SKU
				if($item_info['has_sku'])
				{
					$item_sku_obj = new ItemSkuModel();
					$item_sku_list = $item_sku_obj->itemSkus($item_id);
					$this->assign('item_sku_list', $item_sku_list);
				}
				#echo "<pre>";echo $item_sku_obj->getLastSql();print_r($item_sku_list);die;
				
				//是否促销商品
				$promo_item_obj = M('PromotionItemDiscountDetail');
				$promo_discount_obj = M('PromotionItemDiscount');
				$promo_item_info = $promo_item_obj->field('item_id, promotion_item_discount_id')->where('item_id = ' . $item_id)->find();
				if($promo_item_info){
					$promo_discount_info = $promo_discount_obj->field('')->where('isuse = 1 AND promotion_item_discount_id = ' . $promo_item_info['promotion_item_discount_id'])->find();
					//活动是否有效及是否在活动时间内
					/*if($promo_discount_info['isuse'] == 1 && (time() > $promo_discount_info['start_time'] && time() < $promo_discount_info['end_time'])){
						$this->assign('promo_title', $promo_discount_info['title']);
						$this->assign('promo_item_total', $promo_discount_info['item_total']);
						$this->assign('promo_discount_type', $promo_discount_info['discount_type']);
						$this->assign('promo_discount_total', $promo_discount_info['discount_total']);
					}*/
					$this->assign('promo_valid', 'yes');
					$this->assign('promo_discount_info', $promo_discount_info);

					
				}

				$this->assign('item_info', $item_info);
				$this->assign('head_title', $item_info['item_name']);

			}
		}
		//获取商品是否收藏
		$collect_obj = new CollectModel();
		if($user_id)
			$is_collect = $collect_obj->getCollectNum('item_id = ' . $item_id . ' AND user_id = ' . $user_id);
		
		$this->assign('is_collect', $is_collect);

		$this->assign('item_id', $item_id);
		$this->display();
	}

	//积分商品详情页
	public function integral_item_detail()
	{
		$item_id = $this->_get('item_id');
		$item_obj = new ItemModel($item_id);
		$item_info = $item_obj->getItemInfo('item_id = ' . $item_id);
		if (!$item_info)
		{
			$redirect = U('/FrontMall/integral_exchange');
			$this->alert('对不起，商品不存在！', $redirect);
		}
		//四舍五入积分数
		$item_info['mall_price'] = round($item_info['mall_price']);

		//商品图片
		$item_photo_obj = new ItemPhotoModel();
		$item_photo_list = $item_photo_obj->getItemPhotoList('base_pic', 'item_id = ' . $item_id, 'is_default DESC');
		foreach ($item_photo_list as $k => $v) {
			$item_photo_list[$k]['small_img'] = small_img($v['base_pic']);		
		}
		$this->assign('item_photo_list', $item_photo_list);
		 //引入公共方法
		require_cache('Common/func_item.php');
		//小图用于传值到购物车
		$item_info['small_pic'] = small_img($item_info['base_pic']);

		//商品详情描述
		$item_txt_obj = new ItemTxtModel();
		$item_txt = html_entity_decode(stripslashes($item_txt_obj->getItemTxt($item_id)));		//商品详情
		$item_txt = preg_replace("/\\\&quot;/", "", $item_txt);               //商品详情
		$this->assign('item_txt', $item_txt);

		//商品SKU
		if($item_info['has_sku'])
		{
			$item_sku_obj = new ItemSkuModel();
			$item_sku_list = $item_sku_obj->itemSkus($item_id);
			$this->assign('item_sku_list', $item_sku_list);
		}

		//获取商品是否收藏
		$collect_obj = new CollectModel();
		if($user_id)
			$is_collect = $collect_obj->getCollectNum('item_id = ' . $item_id . ' AND user_id = ' . $user_id);
		
		$this->assign('is_collect', $is_collect);
		$this->assign('item_info', $item_info);
		$this->assign('item_id', $item_id);

		$this->assign('head_title', $item_info['item_name']);
		$this->display();
	}


	//商品分类列表页
	public function item_list()
	{
		/*** 获取商品列表begin ***/
		$new_tag = $this->_get('new');
		$new_shop_tag = $this->_get('new_shop');
		$package_tag = $this->_get('package');
		$promo_tag = $this->_get('promo');
		$where = 'isuse = 1';
		$where1 = $this->get_search_condition();
		$where .= $where1;
		//新品
		if($new_tag == 1){
			$new_item_obj = M('NewItem');
	        $new_item_list = $new_item_obj->field('item_id')->select();
	        if ($new_item_list) {
	            $item_ids = '';
	            foreach ($new_item_list as $k => $v) {
	                $item_array[] =  $v['item_id']; 
	            }
	            $item_ids = implode(",", $item_array);
	            $where .= ' AND item_id IN (' . $item_ids . ')';
	        }
		}
		//开业装修
		if($new_shop_tag == 1){
			$new_item_obj = M('NewShopItem');
		    $new_item_list = $new_item_obj->field('item_id')->select();
	        if ($new_item_list) {
	            $item_ids = '';
	            foreach ($new_item_list as $k => $v) {
	                $item_array[] =  $v['item_id']; 
	            }
	            $item_ids = implode(",", $item_array);
	            $where .= ' AND item_id IN (' . $item_ids . ')';
	        }	
		}
		//促销商品
		if($promo_tag == 1){
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
		//套餐商品
		if($package_tag == 1){
			$item_package_obj = new ItemPackageModel();
			//分页处理
	        import('ORG.Util.Pagelist');
	        $package_total = $item_package_obj->getItemPackageNum($where);
	        $Page = new Pagelist($package_total,$this->item_num_per_page);
			$item_package_obj->setStart($Page->firstRow);
	        $item_package_obj->setLimit($Page->listRows);
	        $show = $Page->show();
			$this->assign('show', $show);
			
			$item_package_list = $item_package_obj->getItemPackageList('item_package_id, item_name, base_pic, mall_price, market_price', $where, 'serial ASC,addtime DESC');
			$item_package_list = $item_package_obj->getListData($item_package_list);
			#echo $item_package_obj->getLastSql();print_r($item_package_list); die;
			$this->assign('item_package_list', $item_package_list);
			$this->assign('package_total', $package_total);
			$this->assign('package_tag', $package_tag);

		}else{
			//普通商品列表
			$item_obj = new ItemModel();
			//分页处理
	        import('ORG.Util.Pagelist');
	        $total = $item_obj->getItemNum('is_gift = 0 AND ' . $where);
	        $Page = new Pagelist($total,$this->item_num_per_page);
			$item_obj->setStart($Page->firstRow);
	        $item_obj->setLimit($Page->listRows);
	        $show = $Page->show();
			$this->assign('show', $show);
			
			$item_list = $item_obj->getItemList('item_id, item_name, base_pic, mall_price, market_price', 'is_gift = 0 AND ' . $where, 'serial ASC,addtime DESC');
			$item_list = $item_obj->getListData($item_list);
			#echo $item_obj->getLastSql();die;
			$this->assign('item_list', $item_list);
			$this->assign('total', $total);
			/*** 获取商品列表end ***/
		}

		$this->assign('head_title', '商品列表');
		$this->display();
	}

	
	//积分兑换专区（列表）
	public function integral_exchange()
	{
		$where = 'isuse = 1 AND is_gift = 1';
		//商品列表
		$item_obj = new ItemModel();
		//分页处理
        import('ORG.Util.Pagelist');
        $total = $item_obj->getItemNum($where);
        $Page = new Pagelist($total,$this->item_num_per_page);
		$item_obj->setStart($Page->firstRow);
        $item_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);
		
		$item_list = $item_obj->getItemList('item_id, item_name, base_pic, mall_price', $where, 'serial ASC,addtime DESC');
		$item_list = $item_obj->getListData($item_list);
		#echo "<pre>";print_r($item_list); die;
		$this->assign('item_list', $item_list);
		$this->assign('total', $total);
		$this->assign('head_title', '积分兑换专区');
		$this->display();
	}

	//日常快速订单
	public function daily_item()
	{
		$Sort  = D('Sort');
		$Item  = new ItemModel();
		// 获取要显示的所有二级分类
		$arr_sort = $Sort->getSortList('isuse = 1 AND is_first_order = 1');
		//获取添加到快速订单的商品查询语句
	    $quick_item_list = M('QuickItem')->field('item_id')->select();
        if ($quick_item_list) {
            $item_ids = '';
            foreach ($quick_item_list as $k => $v) {
                $item_array[] =  $v['item_id']; 
            }
            $item_ids = implode(",", $item_array);
            $where .= ' AND item_id IN (' . $item_ids . ')';
        }
        $Item->setLimit(1000);
		foreach ($arr_sort as $k => $v)
		{
			//获取一级分类下得商品
			$item_list = $Item->getItemList('item_id, item_name, mall_price, base_pic, integral_exchange_rate', 'isuse = 1 AND sort_id = ' . $v['sort_id'] . $where, 'serial ASC,addtime DESC');
			$item_list = $Item->getListData($item_list);
			$arr_item[$k]['items'] = $item_list;

		}
		#echo "<pre>";echo $Item->getLastSql();print_r($arr_item);die;
		$sort_total = $Sort->getSortNum('isuse = 1 AND is_first_order = 1');

		$this->assign('sort_list', $arr_sort);
		$this->assign('item_list', $arr_item);
		$this->assign('sort_total', $sort_total);
		$this->assign('head_title', '日常快速订单');
		$this->display();
	}

	//历史常购订单
	public function history_item()
	{
		$user_id = intval(session('user_id'));
		#$user_id = 39576;
		$orderItem = D('OrderItem');
		$Item = D('Item');
		$order_list = D('Order')->getOrderList('order_id', 'order_status != 0 AND isuse = 1 AND user_id = ' . $user_id, 'addtime DESC');
		#echo "<pre>";print_r($order_list);
		foreach ($order_list as $key => $value) {
			#$order_list[$key]['item_list'] = $orderItem->getOrderItemList('item_id', 'order_id = ' . $value['order_id']);
			$order_list[$key]['item_ids'] = $orderItem->where('order_id = ' . $value['order_id'])->field('item_id')->distinct(true)->find();
			/*foreach ($order_list[$key]['item_list'] as $key => $value) {
				$item_list = $orderItem->distinct(true)->field('item_id')->select();			
			}*/
		}
		#print_r($order_list);
		foreach ($order_list as $k => $v) {
			$item_list[$k]['item_info'] = $Item->getItemList('item_id, item_name, mall_price, base_pic, integral_exchange_rate','item_id = ' . $v['item_ids']['item_id']);
			$item_list[$k]['item_info'] = $Item->getListData($item_list[$k]['item_info']);
		}

		#echo "<pre>";echo $Item->getLastSql(); print_r($item_list);die;
		$this->assign('item_list', $item_list);
		
		$this->assign('head_title', '历史常购订单');
		$this->display();
	}

	//首批订单
	public function first_order()
	{
		$where = 'isuse = 1 AND is_gift = 0';
		//获取首批订单查询语句
		$first_item_obj = M('FirstBuyItem');
        $first_item_list = $first_item_obj->field('item_id')->select();
        if ($first_item_list) {
            $item_ids = '';
            foreach ($first_item_list as $k => $v) {
                $item_array[] =  $v['item_id']; 
            }
            $item_ids = implode(",", $item_array);
            $where .= ' AND item_id IN (' . $item_ids . ')';
        }
        //商品列表
		$item_obj = new ItemModel();
		$item_obj->setLimit(1000);		
		$item_list = $item_obj->getItemList('item_id, item_name, base_pic, mall_price, integral_exchange_rate', $where, 'serial ASC,addtime DESC');
		$item_list = $item_obj->getListData($item_list);
		#echo "<pre>"; echo $item_obj->getLastSql();print_r($item_list); die;
		$this->assign('item_list', $item_list);
		$this->assign('head_title', '首批订单');
		$this->display();
	}


	//我的收藏
	public function my_collect()
	{
		//获取收藏列表
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$collect_obj = new CollectModel();
		//分页处理
        import('ORG.Util.Pagelist');
		$total = $collect_obj->getCollectNum($where);   
        $Page = new Pagelist($total,$this->collect_num_per_page);
		$collect_obj->setStart($Page->firstRow);
        $collect_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$collect_list = $collect_obj->getCollectList('', $where, 'addtime DESC');
		$collect_list = $collect_obj->getListData($collect_list);
		#echo '<pre>';
		#print_r($collect_list);
		#echo $collect_obj->getLastSql();
		#die;
		$this->assign('total', $total);
		$this->assign('collect_list', $collect_list);

		$this->assign('head_title', '我的收藏');
		$this->display();
	}

	//取消收藏
	public function cancel_collect()
	{
		$item_id = $this->_post('item_id');
		$user_id = intval(session('user_id'));

		if (ctype_digit($item_id) && $user_id)
		{
			$where = 'item_id = ' . $item_id . ' AND user_id = ' . $user_id;
			$collect_obj = new CollectModel();
			$collect_info = $collect_obj->getCollectInfo($where, 'item_id');
			if (!$collect_info)
			{
				exit('failure');
			}

			//删除
			$success = $collect_obj->where('item_id = ' . $item_id . ' AND user_id = ' . $user_id)->delete();
			if($success)
				$total = $collect_obj->getCollectNum('user_id = ' . $user_id);

			echo $success ? $total : 'failure';
			exit;
		}

		exit('failure');
	}

	//收藏
	public function collect()
	{
		$item_id = $this->_post('item_id');
		$user_id = intval(session('user_id'));

		if (ctype_digit($item_id) && $user_id)
		{
			$where = 'item_id = ' . $item_id . ' AND user_id = ' . $user_id;
			$collect_obj = new CollectModel();
			$collect_info = $collect_obj->getCollectInfo($where, 'item_id');
			if ($collect_info)
			{
				exit('failure');
			}

			//添加
			$arr = array(
				'item_id'	=> $item_id,
				'user_id'	=> $user_id,
			);
			$success = $collect_obj->addCollect($arr);

			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//APP商城首页
	public function app_index()
	{
		//获取轮播图片
		$cust_flash_obj = new CustFlashModel();
		$cust_flash_list = $cust_flash_obj->getCustFlashList('link, pic, title', 'isuse = 1', 'serial', 4);
		$cust_flash_num = $cust_flash_obj->getCustFlashNum();
		$this->assign('cust_flash_num', $cust_flash_num);
		$this->assign('cust_flash_list', $cust_flash_list);

		//热卖排行
		$item_obj = new ItemModel();
		$item_obj->setLimit(10);
		$hot_item_list = $item_obj->getItemList('item_name, item_id, base_pic, mall_price', 'isuse = 1 AND is_gift = 0', 'sales_num DESC');
		$hot_item_list = $item_obj->getListData($hot_item_list);	
		$this->assign('hot_item_list', $hot_item_list);
		#echo "<pre>";echo $item_obj->getLastSql(); print_r($hot_item_list);die;


		//获取显示首页的一级分类
		$Class = new ClassModel();
		$where = 'isuse = 1 AND is_index = 1';
		$arr_class = $Class->where($where)->order('serial')->limit(5)->select();
		#echo "<pre>";print_r($arr_class);die;
		$this->assign('class_list', $arr_class);


		$this->assign('head_title', '车奇士电商服务平台');
		$this->display();

	}

		
}

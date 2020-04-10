<?php 
class FrontMallAction extends FrontAction{
	function _initialize() 
	{
		parent::_initialize();
		#$this->item_num_per_page = $GLOBALS['config_info']['ITEM_NUM_PER_PAGE'];
		$this->item_num_per_page = 10;
		$this->shop_num_per_page = C('SHOP_NUM_PER_PAGE');
		
	}

	//商城公共数据
	function mall_common_data()
	{
		/*** 获取分类列表begin ***/
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);
		/*** 获取分类列表end ***/
	}

	//获取查询条件
	private function get_search_condition()
	{
		$where = '';
		$class_id = $this->_request('class_id');
		$merchant_id = $this->_request('merchant_id');

		$where .= ctype_digit($class_id) ? ' AND class_id = ' . $class_id : '';
		$where .= ctype_digit($merchant_id) ? ' AND merchant_id = ' . $merchant_id : '';
		
		$this->assign('class_id', $class_id);
		$this->assign('merchant_id', $merchant_id);

		return $where;
	}

	//商家店铺详情页
	public function mall_detail()
	{
		$merchant_id = intval($this->_request('merchant_id'));
		$user_id = intval(session('user_id'));

		//商家信息
		$merchant_obj = new MerchantModel($merchant_id);
		$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $merchant_id, '');
		//获取商家分类名称
		$class_obj = new ClassModel($merchant_info['class_id']);
		$class_info = $class_obj->getClassInfo('class_id = ' . $merchant_info['class_id'], 'class_name');
		$merchant_info['class_name'] = $class_info['class_name'];
		//获取与用户的距离
		$user_address_id = intval(session('user_address_id'));
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'longitude, latitude');
		$merchant_info['distance'] = MapModel::calDistance($user_address_info['longitude'], $user_address_info['latitude'], $merchant_info['longitude'], $merchant_info['latitude']);
		//获取商家具体地址和电话
		$user_obj = new UserModel($merchant_id);
		$user_info = $user_obj->getUserInfo('address, mobile');
		$merchant_info['address'] = $user_info['address'];
		$merchant_info['mobile'] = $user_info['mobile'];
		//获取商家是否收藏
		$collect_obj = new CollectModel();
		$is_collect = $collect_obj->getCollectNum('merchant_id = ' . $merchant_id . ' AND user_id = ' . $user_id);
		
		$this->assign('merchant_info', $merchant_info);
		$this->assign('is_collect', $is_collect);

		$this->assign('head_title', $merchant_info['shop_name']);
		$this->display();
	}
	
	//商品详情页，改为一个前台封装好的js弹层方法，用到的页面有1、商家商品列表页；2、商品搜索结果页；3、后期其他可能的商品列表页。
	public function item_detail()
	{
		$item_id = $this->_get('item_id');
		$item_obj = new ItemModel($item_id);
		$item_info = $item_obj->getItemInfo('item_id = ' . $item_id);
		$this->assign('item_id', $item_id);
		
		if (!$item_info)
		{
			$redirect = U('/FrontMall/mall_home');
			$this->alert('对不起，商品不存在！', $redirect);
		}
		//若为积分商品这跳转到积分商品详情
		if($item_info['is_integral'] == 1){
			$this->redirect('/FrontMall/integral_item_detail', array('item_id' =>$item_id));
		}
		//获取商品中图，购物车页，商品详情页都使用该图
		require_cache('Common/func_item.php');
		$item_info['small_pic'] = middle_img($item_info['base_pic']);
		$this->assign('item_info', $item_info);
		#echo "<pre>";print_r($item_info);die;
		//商品图片
		$item_photo_obj = new ItemPhotoModel();
		$item_photo_list = $item_photo_obj->getPhotos($item_id);
		$this->assign('item_photo_list', $item_photo_list);
		#echo "<pre>";print_r($item_photo_list);die;

		//商品详情描述
		$item_txt_obj = new ItemTxtModel();
		$item_txt = html_entity_decode(stripslashes($item_txt_obj->getItemTxt($item_id)));		//商品详情
		$item_txt = preg_replace("/\\\&quot;/", "", $item_txt);               //商品详情
		$this->assign('item_txt', $item_txt);

		//商品SKU
		if ($item_info['has_sku'])
		{
			$item_sku_obj = new ItemSkuModel();
			$item_sku_list = $item_sku_obj->itemSkus($item_id);
			$this->assign('item_sku_list', $item_sku_list);
		}

		//购物车商品总数
		if(intval(session('user_id')) != 0){
			$cart_obj = new ShoppingCartModel();
			$total_num = $cart_obj->getShoppingCartNum();
			$this->assign('total_num', $total_num);
		}
		

		//如果是种子，获取种子状态列表，并传到前台
		$class_obj = new ClassModel();
		$class_info = $class_obj->getClassInfo('class_id = ' . $item_info['class_id'], 'class_tag');
		if ($class_info && $class_info['class_tag'] == 'seed')
		{
			//获取种子状态列表
			//$seed_state_obj = new SeedStateModel();
			//$seed_state_list = $seed_state_obj->getSeedStateList('seed_state_id, state, outside_temperature, humidity, illuminance_limit, img_path', 'seed_id = ' . $item_id, 'state ASC');
			$this->assign('seed_state_list', $seed_state_list);
		}
		$share_info = array(
				'item_name' => $item_info['item_name'],
				'item_desc' => $item_info['item_desc'],
				'basc_pic'  => $item_info['base_pic'] ? 'http://'. $_SERVER['HTTP_HOST'] . $item_info['base_pic'] : '',
				'share_url' => 'http://'. $_SERVER['HTTP_HOST'] .'/FrontMall/item_detail/item_id/'.$item_id.'?rec_user_id='.session('user_id')
				);
		$share_info['item_desc'] = htmlspecialchars_decode($share_info['item_desc']);
		$share_info['item_desc'] = filterAndSubstr($share_info['item_desc']);
		// $share_info = json_encode($share_info);
		$this->assign('share_info', $share_info);
		$this->assign('MALL_PRICE_NAME', $GLOBALS['config_info']['MALL_PRICE_NAME']);
		$this->assign('head_title', $item_info['item_name']);
		$detail_tpl_path = dirname($this->_get_template_rel_file('item_detail'));
		$this->assign('detail_tpl_path', $detail_tpl_path);
		#$this->display(APP_PATH . 'Tpl/FrontMall/item_list.html');
		$this->display();
	}

	//商家店铺商品列表页
	public function item_list()
	{
		/*** 获取商品列表begin ***/
		$class_id = intval($this->_request('class_id'));
        $class_id = $class_id ? 
            $class_id : intval(D('Class')->order('serial ASC')->getField('class_id'));
		$sort_id = intval($this->_request('sort_id'));
		$item_id = intval($this->_request('item_id'));

		//根据item_id获取商品详细信息
		if($item_id)
		{
			$item_obj = new ItemModel();
			$item_info = $item_obj->getItemInfo('item_id = ' . $item_id,'item_id, item_name, item_desc, unit, base_pic, mall_price, class_id, sort_id');
			$class_id = $item_info['class_id'] ? $item_info['class_id'] : $class_id;
			$sort_id = $item_info['sort_id'] ? $item_info['sort_id'] : $sort_id;
			$this->assign('item_info', $item_info);
		}

		/*** 获取商家分类列表begin ***/
		$sort_obj = new SortModel();
		$sort_list = $sort_obj->getSortList('isuse = 1 AND class_id = ' . $class_id);
		$this->assign('sort_list', $sort_list);
		/*** 获取商家分类列表end ***/

		$sort_id = $sort_id ? $sort_id : $sort_list[0]['sort_id'];
		$where = 'class_id = ' . $class_id;
		$where .= ' AND sort_id = ' . $sort_id;

		//获取商品列表（带分类的商品列表）
		$item_obj = new ItemModel();
		//总数
		$total = $item_obj->getItemNum($where);
		#echo $item_obj->getLastSql();
		#die;
		#$item_obj->setStart(0);
		$item_obj->setLimit(1000);
		$item_list = $item_obj->getItemListGroupBySort($class_id, 4);	//第二个参数是分页数
		#$item_list = $item_obj->getSpecItemList($item_list['item_list']);
		$this->assign('item_list', $item_list);
		$this->assign('total', $total);
		$this->assign('firstRow',4);
		#echo $item_obj->getLastSql();
		#echo "<pre>";print_r($item_list);die;
		#print_r($merchant_info);
		#die;

		//商品列表（常规列表）
		$item_common_obj = new ItemModel();
		$item_common_obj->setLimit($this->item_num_per_page);
		$item_common_list = $item_common_obj->getItemListBySortId($sort_id);
		$item_common_list = $item_common_obj->getListData($item_common_list);
		$item_total = $item_common_obj->getItemNum('isuse = 1 AND sort_id = ' . $sort_id);
		$this->assign('item_common_list', $item_common_list);
		$this->assign('item_total', $item_total);

		#echo "<pre>";echo $item_common_obj->getLastSql(); print_r($item_common_list);die;
		//购物车商品总数和总价
		if(intval(session('user_id')) != 0){
			$cart_obj = new ShoppingCartModel();
			$total_amount = $cart_obj->sumCartAmount();
			$total_num = $cart_obj->getShoppingCartNum();
			$this->assign('total_amount', $total_amount);
			$this->assign('total_num', $total_num);
		}
		

		$this->assign('class_id', $class_id);
		$this->assign('cur_sort_id', $sort_id);
		#echo "<pre>";
		#var_dump($sort_list[0]['sort_id']);
		#print_r($sort_list);
		#die;
		if (!$item_info)
		{
			//获取分类名称
			$class_obj = new ClassModel();
			$class_info = $class_obj->getClassInfo('class_id = ' . $class_id, 'class_name');
		}

		//获取一级分类列表
		$class_obj = new ClassModel();
		$class_obj->setLimit(10);
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);

		$list_tpl_path = dirname($this->_get_template_rel_file('item_list'));
		$this->assign('list_tpl_path', $list_tpl_path);
		$this->assign('head_title', $item_info ? $item_info['item_name'] : $class_info['class_name']);
		$this->display();
	}

	//商家分类列表页
	public function mall_list()
	{
		$cur_lon = session('cur_lon');
		$cur_lat = session('cur_lat');
		//商城公共数据
		$this->mall_common_data();	

		/*** 查询条件begin ***/
		$where = 'isuse = 1 AND online = 1';
		$class_id = intval($this->_request('class_id'));
		$trading_area_id = intval($this->_request('trading_area_id'));
		$where .= $class_id ? ' AND class_id = ' . $class_id : '';
		$where .= $trading_area_id ? ' AND trading_area_id = ' . $trading_area_id : '';
		/*** 查询条件end ***/

		/*** 排序条件begin ***/
		$orderby = intval($this->_request('orderby'));
		$this->assign('orderby', $orderby);
		/*** 排序条件end ***/

		$this->assign('class_id', $class_id);
		$this->assign('trading_area_id', $trading_area_id);

		//用户坐标
		$user_address_id = intval(session('user_address_id'));
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'longitude, latitude');

		/*** 获取商家列表begin ***/
		$merchant_obj = new MerchantModel();
		//总数
		$total = $merchant_obj->getMerchantNum($where);

		$merchant_obj->setStart(0);
        $merchant_obj->setLimit($this->shop_num_per_page);
        if($cur_lon && $cur_lat){
			$merchant_list = $merchant_obj->getMerchantList('merchant_id, shop_name, logo, score_avg, trading_area_id, longitude, latitude, make_time_avg, total_sales_num, class_id, on_time_rate, busy_level, refuse_order_rate, online, ' . MerchantModel::getDistanceSql($cur_lon, $cur_lat), $where, 'distance ASC');
	    }else{
			$merchant_list = $merchant_obj->getMerchantList('merchant_id, shop_name, logo, score_avg, trading_area_id, longitude, latitude, make_time_avg, total_sales_num, class_id, on_time_rate, busy_level, refuse_order_rate, online, ' . MerchantModel::getDistanceSql($user_address_info['longitude'], $user_address_info['latitude']), $where, 'distance ASC');
	    }
		$merchant_list = $merchant_obj->getListData($merchant_list);
		//排序
		$merchant_list = MerchantModel::orderMerchantList($merchant_list, $orderby);
		$this->assign('merchant_list', $merchant_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->shop_num_per_page);
		
		/*** 获取商家列表end ***/

		//全部分类
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);
		#echo $merchant_obj->getLastSql();
		#echo "<pre>";
		#print_r($merchant_list);
		#print_r($class_list);
		#die;

		$this->assign('link', '/FrontMall/mall_list');
		$this->assign('head_title', '商家分类列表');
		$this->display();
	}

	//异步获取商家分类列表
	public function get_mall_list()
	{
		//商城公共数据
		$this->mall_common_data();

		$firstRow = I('post.firstRow');
		$orderby = I('post.orderby');
		$class_id = I('post.class_id');
		$trading_area_id = I('post.trading_area_id');
		$user_id = intval(session('user_id'));
		/*** 查询条件begin ***/
		$where = 'isuse = 1 AND online = 1';
		#$class_id = intval($this->_request('class_id'));
		#$trading_area_id = intval($this->_request('trading_area_id'));
		$where .= $class_id ? ' AND class_id = ' . $class_id : '';
		$where .= $trading_area_id ? ' AND trading_area_id = ' . $trading_area_id : '';
		/*** 查询条件end ***/
		//用户坐标
		$user_address_id = intval(session('user_address_id'));
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $user_address_id, 'longitude, latitude');
		
		//全部分类
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);
		/*** 获取商家列表begin ***/
		$merchant_obj = new MerchantModel();
		//总数
		$total = $merchant_obj->getMerchantNum($where);
		
		
		/*** 获取商家列表end ***/

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$merchant_obj->setStart($firstRow);
	        $merchant_obj->setLimit($this->shop_num_per_page);
			$merchant_list = $merchant_obj->getMerchantList('merchant_id, shop_name, logo, score_avg, trading_area_id, longitude, latitude, make_time_avg, total_sales_num, class_id, on_time_rate, busy_level, refuse_order_rate, online, ' . MerchantModel::getDistanceSql($user_address_info['longitude'], $user_address_info['latitude']), $where, 'distance ASC');
			$merchant_list = $merchant_obj->getListData($merchant_list);
			//排序
			$merchant_list = MerchantModel::orderMerchantList($merchant_list, $orderby);
			#echo "<pre>";
		#print_r($total);
		#echo $merchant_obj->getLastSql();
		#die;
			echo json_encode($merchant_list);
			exit;
		}

		exit('failure');
	}

	//异步获取商品列表（分类下的列表）
	public function get_item_list()
	{//dump($_POST);die;
		$firstRow = I('post.firstRow');
		//$merchant_id = I('post.merchant_id');
		//$merchant_class_id = I('post.cur_sort_id');
		$sort_id = I('post.cur_sort_id');
		$user_id = intval(session('user_id'));
		$item_obj = new ItemModel();
		//$where = 'merchant_id = ' . $merchant_id;
		//$where .= ' AND merchant_class_id = ' . $merchant_class_id;
		$where = 'sort_id = '. $sort_id . ' and is_integral =0 and isuse = 1' ;
		//商品总数
		$total = $item_obj->getItemNum($where);

		if ($firstRow <= $total )
		{
			$item_obj->setStart($firstRow);
			$item_obj->setLimit($this->item_num_per_page);
			//获取商品列表
			$item_list = $item_obj->getItemList('*', $where, 'addtime desc');
			foreach ($item_list AS $key => $val)
			{	
				//$path = strstr($val['base_pic'], '/Uploads');
				$item_list[$key]['small_img'] = small_img($val['base_pic']);
			}
		#echo $item_obj->getLastSql();
		#die;	
			echo json_encode($item_list);
			exit;
		}

		exit('failure');
	}

	//异步获取商品列表（常规列表）
	public function get_item_common_list()
	{
		$firstRow = I('post.firstRow');
		$cur_sort_id = I('post.cur_sort_id');
		$user_id = intval(session('user_id'));
		$item_obj = new ItemModel();
		$where = 'isuse = 1 AND sort_id = ' . $cur_sort_id;
		//商品总数
		$total = $item_obj->getItemNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$item_obj->setStart($firstRow);
			$item_obj->setLimit($this->item_num_per_page);
			//获取商品列表
			$item_list = $item_obj->getItemListBySortId($cur_sort_id);
			$item_list = $item_obj->getListData($item_list);
		#echo $item_obj->getLastSql();
		#die;	
			echo json_encode($item_list);
			exit;
		}

		exit('failure');
	}

	//商城首页
	public function mall_home()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('nickname, headimgurl');
			$this->assign('user_info', $user_info);
		}
		#show_debug_info(false);
		#die;
		
		//获取分享代码
		//Vendor('jssdk');
		//$jssdk = new JSSDK(C("APPID"), C("APPSECRET"));
		//$signPackage = $jssdk->GetSignPackage(); 
		//$this->signPackage = $signPackage;
		//$this->assign('signPackage', $signPackage);

		//商城公共数据
		$this->mall_common_data();
		//$building_name = session('building_name');
		//$cur_lon = session('cur_lon');
		//$cur_lat = session('cur_lat');
		//$is_gps = session('is_gps');
		//$this->assign('cur_lon',$cur_lon);
		//$this->assign('is_gps',$is_gps);

		//用户坐标
		#$user_address_id = session('user_address_id');
        #if (!$user_address_id) {
        #    $user_address_id = D('User')->where('user_id=%d', intval($user_id))->getField('user_address_id');
        #    session('user_address_id',$user_address_id);
        #}
        #$user_address_obj = new UserAddressModel();
        //$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . intval($user_address_id), 'longitude, latitude');
        //获取地址参数
        /*$address_obj = new Address();
        $parameters = $address_obj->getParameters();
        $this->assign('parameters', $parameters);*/
        #$user_address_list = $user_address_obj->getUserAddressList('user_address_id, province_id, city_id, area_id, mobile, address, realname', 'isuse = 1 AND user_id = ' . $user_id, 'addtime DESC, use_time DESC');
        #$user_address_list = $user_address_obj->getListData($user_address_list);
        #$user_default_addr = $user_address_obj->getDefaultAddress($user_id);
		#$this->assign('user_address_list', $user_address_list);
		#$this->assign('user_default_addr', $user_default_addr);

		/*** 获取商品列表begin ***/
		//获取商品列表 新品排序
		$item_obj = new ItemModel();
		$item_obj->setLimit(10);
		$item_list = $item_obj->getItemList('item_id, item_name, mall_price, market_price, base_pic, sales_num', 'isuse = 1 AND stock > 1 and is_integral <> 1', 'serial ASC, addtime DESC');	
		$item_list = $item_obj->getListData($item_list);
		$this->assign('item_list', $item_list);
		#echo "<pre>";echo $item_obj->getLastSql(); print_r($item_list);die;
		//商品总数
		$item_num = $item_obj->getItemNum("isuse = 1");
		$this->assign('item_num', $item_num);
		//根据推荐一级分类获取商品列表
		$class_rec_item_obj = new ItemModel();
		$class_rec_item_list = $class_rec_item_obj->getItemListGroupByClass();
		$this->assign('class_rec_item_list', $class_rec_item_list);

		#echo "<pre>";echo $class_rec_item_obj->getLastSql(); print_r($class_rec_item_list);die;
		//获取推荐商品列表
		$rec_item_obj = new ItemModel();
		$rec_item_obj->setLimit(10);
		$rec_item_list = $rec_item_obj->getItemList('item_id, item_name, mall_price, market_price, base_pic, sales_num', 'isuse = 1 AND stock > 1 AND is_recommend = 1','serial');	
		$rec_item_list = $rec_item_obj->getListData($rec_item_list);
		$this->assign('rec_item_list', $rec_item_list);
		//获取上新数量（一天内的）
		$new_item_num = $item_obj->getItemNum('isuse = 1 AND ' . time() - 86400 . ' < addtime');
		$this->assign('new_item_num', $new_item_num);
		//获取人气商品列表（销量排行）
		$hot_item_obj = new ItemModel();
		$hot_item_obj->setLimit(10);
		$hot_item_list = $hot_item_obj->getItemList('item_id, item_name, mall_price, market_price, base_pic, sales_num', 'isuse = 1 AND stock > 1', 'sales_num DESC, is_recommend DESC, addtime DESC');	
		$hot_item_list = $hot_item_obj->getListData($hot_item_list);
		$this->assign('hot_item_list', $hot_item_list);
#echo $item_obj->getLastSql();
#die;
		#item_id, mall_price, market_price, base_pic, sales_num
		#echo "<pre>";print_r($hot_item_list);die;
		#die;
		/*** 获取商品列表end ***/

		/*** 获取通知消息begin ***/
		$notice_obj = new NoticeModel();
		$notice_list = $notice_obj->getNoticeList();
		$this->assign('notice_list',$notice_list);
		/*** 获取通知消息end ***/
		//获取轮播图片
		$cust_flash_obj = new CustFlashModel();
		$cust_flash_list = $cust_flash_obj->getCustFlashList('link, pic', 'isuse = 1', 'serial asc');
		$cust_flash_num = $cust_flash_obj->getCustFlashNum();
		$this->assign('cust_flash_num', $cust_flash_num);
		$this->assign('cust_flash_list', $cust_flash_list);
		$this->assign('building_name', $building_name);
		//获取广告图列表
		$index_ads_obj = new IndexAdsModel();
		$index_ads_big_list = $index_ads_obj->getIndexAdsList('link, pic, size_style', 'size_style = 0 AND isuse = 1');
		$index_ads_middle_list = $index_ads_obj->getIndexAdsList('link, pic, size_style', 'size_style = 1 AND isuse = 1');
		$index_ads_small_list = $index_ads_obj->getIndexAdsList('link, pic, size_style', 'size_style = 2 AND isuse = 1');
		$index_ads_num = $index_ads_obj->getIndexAdsNum();
		$this->assign('index_ads_num', $index_ads_num);
		$this->assign('index_ads_big_list', $index_ads_big_list);
		$this->assign('index_ads_middle_list', $index_ads_middle_list);
		$this->assign('index_ads_small_list', $index_ads_small_list);
		#echo "<pre>";print_r($index_ads_list);die;
		//获取热门搜索词
		$this->assign('hot_keywords', explode(',', $GLOBALS['config_info']['HOT_KEYWORDS']));
		//获取一级分类列表
		$class_obj = new ClassModel();
		$class_obj->setLimit(7);
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);
		//获取首页导航定制列表
		$index_nav_obj = new IndexNavModel();
		$index_nav_num = $index_nav_obj->getIndexNavNum('isuse = 1');
		$index_nav_list = $index_nav_obj->getIndexNavList('','isuse = 1','serial ASC');
		$this->assign('index_nav_list', $index_nav_list);
		$this->assign('index_nav_num', $index_nav_num);
		#echo "<pre>";print_r($cust_flash_list);die;
		#var_dump($cust_flash_list);die;

		#$template_path = dirname($this->_get_template_rel_file('mall_home'));
		#$this->assign('template_path', $template_path);
		#dump($template_path);
		#$this->assign('tpl_path', $template_path);

		$this->assign('head_title', $GLOBALS['config_info']['SHOP_NAME']);
		//底部导航赋值
		$this->assign('nav', 'home');
		$this->display();
	}

	//传入经纬度 zlf
	function set_lon_lat()
	{
		$cur_lon = $this->_post('cur_lon');
		$cur_lat = $this->_post('cur_lat');
		$user_id = intval(session('user_id'));
#log_file('11, cur_lon = ' . $cur_lon . ', cur_lon = ' . $cur_lat, 'lonlat');
send_email('设置经纬度：cur_lon = ' . $cur_lon . ', cur_lat = ' . $cur_lat . ', user_id = ' . $user_id, '', '', false);

		if($cur_lon && $cur_lat){
			session('cur_lon', $cur_lon);
			session('cur_lat', $cur_lat);
			#session('lon', $cur_lon);
			#session('lat', $cur_lat);
			echo 'success';
			exit;
		}
		exit('failure');
		
	}

	//保存经纬度 zlf
	function save_lon_sesion()
	{
		$cur_lon = $this->_post('cur_lon');
		$cur_lat = $this->_post('cur_lat');
		$user_id = intval(session('user_id'));

		if($cur_lon && $cur_lat){
			session('lon', $cur_lon);
			session('lat', $cur_lat);
			session('is_gps', 1);
			echo 'success';
			exit;
		}
		exit('failure');
		
	}


	//全部分类
	public function mall_plant_list()
	{
		$class_id = $this->_get('class_id');
		if (!$class_id)
		{
			//获取种子分类ID
			$class_obj = new ClassModel();
			$class_info = $class_obj->getClassInfo('class_tag = "seed"', 'class_id');
			$class_id = $class_info ? $class_info['class_id'] : 0;
		}

		//获取分类二级分类列表
		$sort_obj = new SortModel();
		$sort_list = $sort_obj->getClassSortList($class_id);
		$this->assign('sort_list', $sort_list);

		//全部分类
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);
		$this->assign('class_id', $class_id);

		//获取热门搜索词
		$this->assign('hot_keywords', explode(',', $GLOBALS['config_info']['HOT_KEYWORDS']));

		$this->assign('head_title', '全部分类');
		$this->display();
	}

	//我的收藏
	public function my_collect()
	{
		//获取收藏列表
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$collect_obj = new CollectModel();
		//总数
		$total = $collect_obj->getCollectNum($where);
		$collect_obj->setStart(0);
   		$collect_obj->setLimit($this->shop_num_per_page);
		$collect_list = $collect_obj->getCollectList('', $where, 'addtime DESC');
		$collect_list = $collect_obj->getListData($collect_list);
		#echo '<pre>';
		#print_r($collect_list);
		#echo $collect_obj->getLastSql();
		#die;
		$this->assign('total', $total);
		$this->assign('firstRow', $this->shop_num_per_page);
		$this->assign('collect_list', $collect_list);

		$this->assign('head_title', '我的收藏');
		$this->display();
	}

	

	//异步获取收藏列表
	public function get_collect_list()
	{
		$firstRow = I('post.firstRow');
		$user_id = intval(session('user_id'));
		$collect_obj = new CollectModel();
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		//订单总数
		$total = $collect_obj->getCollectNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$collect_obj->setStart($firstRow);
			$collect_obj->setLimit($this->shop_num_per_page);
			//获取订单列表
			$collect_list = $collect_obj->getCollectList('', $where, 'addtime DESC');
			$collect_list = $collect_obj->getListData($collect_list);
			echo json_encode($collect_list);
			exit;
		}

		exit('failure');
	}

	//取消收藏
	public function cancel_collect()
	{
		$merchant_id = $this->_post('merchant_id');
		$user_id = intval(session('user_id'));

		if (ctype_digit($merchant_id) && $user_id)
		{
			$where = 'merchant_id = ' . $merchant_id . ' AND user_id = ' . $user_id;
			$collect_obj = new CollectModel();
			$collect_info = $collect_obj->getCollectInfo($where, 'merchant_id');
			
			if (!$collect_info)
			{
				exit('failure');
			}

			//删除
			$success = $collect_obj->where('merchant_id = ' . $merchant_id . ' AND user_id = ' . $user_id)->delete();
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
		$merchant_id = $this->_post('merchant_id');
		$user_id = intval(session('user_id'));

		if (ctype_digit($merchant_id) && $user_id)
		{
			$where = 'merchant_id = ' . $merchant_id . ' AND user_id = ' . $user_id;
			$collect_obj = new CollectModel();
			$collect_info = $collect_obj->getCollectInfo($where, 'merchant_id');
			if ($collect_info)
			{
				exit('failure');
			}

			//添加
			$arr = array(
				'merchant_id'	=> $merchant_id,
				'user_id'	=> $user_id,
			);
			$success = $collect_obj->addCollect($arr);

			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//增加一次点击量
	public function addClickdot()
	{
		$item_id = $this->_post('item_id');
		$user_id = intval(session('user_id'));

		if (ctype_digit($item_id) && $user_id)
		{
			$item_obj = new ItemModel();
			$success = $item_obj->addClickdot($item_id);

			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	//积分商城首页
	public function integral_mall_home()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('nickname, headimgurl, left_integral');
			$this->assign('user_info', $user_info);
		}
		

		//获取轮播图片
		$cust_flash_obj = new CustFlashModel();
		$cust_flash_list = $cust_flash_obj->getCustFlashList('link, pic', 'isuse = 1');
		$cust_flash_num = $cust_flash_obj->getCustFlashNum();
		$this->assign('cust_flash_num', $cust_flash_num);
		$this->assign('cust_flash_list', $cust_flash_list);
		
		//获取一级分类列表
		$class_obj = new ClassModel();
		$class_obj->setLimit(7);
		$class_list = $class_obj->getClassList('isuse = 1  and has_all_integral = 1');
		$this->assign('class_list', $class_list);

		$item_obj = new ItemModel();
		$integral_item_list = $item_obj->getItemList('item_id, item_name, mall_price, market_price, base_pic, sales_num, integral_exchange', 'isuse = 1 AND stock > 1 and is_integral = 1', ' addtime DESC,serial ASC');

		$integral_item_list = $item_obj->getListData($integral_item_list);
		$this->assign('integral_item_list', $integral_item_list);

		$this->assign('head_title', '积分商城首页');
		//底部导航赋值
		$this->assign('nav', 'integral_home');
		$this->display();
	}

	//积分商城列表
	public function integral_item_list()
	{
		$class_id = I('class_id');
		$item_obj = new ItemModel();
		$where = 'isuse = 1 AND stock > 1 and is_integral = 1 and class_id = '.$class_id;
		$total = $item_obj->getItemNum($where);
		$firstRow = intval($this->_request('firstRow'));
		$item_obj->setStart($firstRow);
        $item_obj->setLimit($this->shop_num_per_page);
		$integral_item_list = $item_obj->getItemList('item_id, item_name, mall_price, market_price, base_pic, sales_num, integral_exchange', $where, ' addtime DESC,serial ASC');
		$integral_item_list = $item_obj->getListData($integral_item_list);
		if(IS_POST && IS_AJAX){
			$this->json_exit($integral_item_list);
		}
		$this->assign('integral_item_list', $integral_item_list);
		$this->assign('total', $total);
		$this->assign('class_id', $class_id);
		$this->assign('firstRow', $this->shop_num_per_page);
		$this->assign('head_title', '积分商品列表');
		$this->display();
	}

	//积分商品详情
	public function integral_item_detail()
	{
		$item_id = $this->_get('item_id');
		$item_obj = new ItemModel($item_id);
		$item_info = $item_obj->getItemInfo('item_id = ' . $item_id);
		if (!$item_info)
		{
			$redirect = U('/FrontMall/integral_mall_home');
			$this->alert('对不起，商品不存在！', $redirect);
		}
		if($item_info['is_integral'] == 0){
			$this->redirect('/FrontMall/item_detail', array('item_id' =>$item_id));
		}

		$this->assign('item_id', $item_id);

		//获取商品中图，购物车页，商品详情页都使用该图
		require_cache('Common/func_item.php');
		$item_info['small_pic'] = middle_img($item_info['base_pic']);
		$this->assign('item_info', $item_info);
		#echo "<pre>";print_r($item_info);die;
		//商品图片
		$item_photo_obj = new ItemPhotoModel();
		$item_photo_list = $item_photo_obj->getPhotos($item_id);
		$this->assign('item_photo_list', $item_photo_list);

		//商品详情描述
		$item_txt_obj = new ItemTxtModel();
		$item_txt = html_entity_decode(stripslashes($item_txt_obj->getItemTxt($item_id)));		//商品详情
		$item_txt = preg_replace("/\\\&quot;/", "", $item_txt);               //商品详情
		$this->assign('item_txt', $item_txt);
		$this->assign('item_info', $item_info);
		$this->assign('head_title', '积分商品详情');
		$this->display();
	}

	//积分商品订单信息
	public function integral_order_detail(){

		//获取分享代码
		Vendor('jssdk');
		$jssdk = new JSSDK(C("APPID"), C("APPSECRET"));
		$signPackage = $jssdk->GetSignPackage(); 
		$this->signPackage = $signPackage;
		$this->assign('signPackage', $signPackage);

		//订单ID
		$user_id = intval(session('user_id'));
		$order_id = $this->_get('order_id');
		if ($order_id)
		{
			session('order_id', $order_id);
			redirect('http://' . $_SERVER['HTTP_HOST']	. '/FrontMall/integral_order_detail/'.$order_id);
		}
		$order_id = intval(session('order_id'));

		$order_obj = new OrderModel($order_id);
		try
		{
			$order_info = $order_obj->getOrderInfo('order_id, order_sn, order_status, pay_amount, total_amount, express_fee, addtime, pay_time, confirm_time, user_address_id, payway,express_number,express_company_id,integral_amount', ' AND user_id = ' . $user_id);
		}
		catch (Exception $e)
		{
			$this->alert('对不起，订单不存在！', U('/FrontMall/mall_home'));
		}

		/*** 获取订单流程信息begin ***/
		//订单状态
		$order_info['order_status_name'] = OrderModel::convertOrderStatus($order_info['order_status']);

		//收货人信息
		$user_address_obj = new UserAddressModel();
		$user_address_info = $user_address_obj->getUserAddressInfo('user_address_id = ' . $order_info['user_address_id'] . ' AND user_id = ' . $user_id);

		$order_info['address'] = AreaModel::getAreaString($user_address_info['province_id'], $user_address_info['city_id'], $user_address_info['area_id']) . ' ' . $user_address_info['address'];

		$order_info['realname'] = $user_address_info['realname'];
		$order_info['mobile'] = $user_address_info['mobile'];

		//支付方式名称
		$payway_obj = new PaywayModel();
		$payway_info = $payway_obj->getPaywayInfo('payway_id = ' . $order_info['payway'], 'pay_name');
		$order_info['payway_name'] = $payway_info['pay_name'];

		//获取订单商品信息
		#$fields = 'order_item_id,tp_order_item.merchant_id, item_id, item_name, item_sn, number, unit, real_price, small_pic, shop_name';
		$order_info['order_item_list'] = $order_obj->getOrderItemList('item_id, item_name, number, small_pic, real_price, tp_order_item.order_item_id', 'tp_order_item.order_id = ' . $order_id);

		#echo $order_obj->getLastSql();
		#echo "<pre>";
		#print_r($order_info);
		#die;
		//物流信息
		if($order_info['order_status']!= OrderModel::PRE_PAY)
		{
			//todo 将物流公司显示出来
		//获取物流名称
			$shipping_company_obj = new ShippingCompanyModel();
			$shipping_company_info = $shipping_company_obj->getShippingCompanyInfoById($order_info['express_company_id']);

        $express_obj  = new ShippingCompanyModel();
        $express_info = $express_obj->getShippingCompanyInfoById($order_info['express_company_id']);
        $order_info['express_company_name'] =$express_info['shipping_company_name'];
		}

		//图片域名
		$this->assign('IMG_DOMAIN', C('IMG_DOMAIN'));
		$this->assign('order_info', $order_info);
		$this->assign('head_title', '订单详情');
		$this->display();
	}
}

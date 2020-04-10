<?php 
class FrontSearchAction extends FrontAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_page = C('ITEM_NUM_PER_PAGE');
		$this->shop_num_per_page = C('SHOP_NUM_PER_PAGE');
	}

	//搜索页
	public function search()
	{
		//获取热门搜索词
		$this->assign('hot_keywords', explode(',', $GLOBALS['config_info']['HOT_KEYWORDS']));
		#echo '<pre>';
		#print_r($GLOBALS['config_info']['HOT_KEYWORDS']);
		#die;
		//全部分类
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);

		$this->assign('head_title', '搜索页');
		$this->display();
	}

	//搜索页公共数据
	function search_common_data()
	{
		//全部分类
		$class_obj = new ClassModel();
		$class_list = $class_obj->getClassList('isuse = 1');
		$this->assign('class_list', $class_list);
	}

	//商家搜索结果页
	public function search_shop_result()
	{
		$cur_lon = session('cur_lon');
		$cur_lat = session('cur_lat');
		/*** 查询条件begin ***/
		$where = 'isuse = 1 AND online = 1';
		$shop_name = $this->_request('keyword');
		$class_id = intval($this->_request('class_id'));
		$trading_area_id = intval($this->_request('trading_area_id'));
		$where .= $shop_name ? ' AND shop_name LIKE "%' . $shop_name . '%"' : '';
		$where .= $class_id ? ' AND class_id = ' . $class_id : '';
		$where .= $trading_area_id ? ' AND trading_area_id = ' . $trading_area_id : '';
		/*** 查询条件end ***/

		/*** 排序条件begin ***/
		$orderby = intval($this->_request('orderby'));
		$this->assign('orderby', $orderby);
		/*** 排序条件end ***/
		
		$this->assign('class_id', $class_id);
		$this->assign('keyword', $shop_name);
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

		//获取排序方式列表
		$orderby_list = MerchantModel::getFrontMerchantOrderbyList();
		$this->assign('orderby_list', $orderby_list);

		$this->assign('link', '/FrontSearch/search_shop_result');
		$this->assign('search_type', 0);
		$this->assign('head_title', '商家搜索结果页');
		$this->display();
	}

	//商品搜索结果页
	public function search_item_result()
	{
		/*** 查询条件begin ***/
		$where = 'isuse = 1 AND stock > 0';
		$item_name = $this->_request('keyword');
		$class_id = $this->_request('class_id');
		$class_tag = $this->_request('class_tag');
		$trading_area_id = intval($this->_request('trading_area_id'));

		$where .= $item_name ? ' AND item_name LIKE "%' . $item_name . '%"' : '';
		$where .= ctype_digit($class_id) && $class_id ? ' AND class_id = ' . $class_id : '';
		$where .= $class_tag ? ' AND class_tag = "' . $class_tag . '"' : '';
		/*** 查询条件end ***/

		$this->assign('class_id', $class_id);
		$this->assign('class_tag', $class_tag);
		$this->assign('keyword', $item_name);
		$this->assign('trading_area_id', $trading_area_id);

		/*** 获取商品列表begin ***/
		$item_obj = new ItemModel();
		//总数
		$total = $item_obj->getItemNum($where);
		$item_obj->setStart(0);
        $item_obj->setLimit($this->item_num_per_page);
		$item_list = $item_obj->getItemList('item_id, item_name, item_sn, base_pic, mall_price, stock, sales_num, item_desc', $where, 'serial ASC');
		#echo $item_obj->getLastSql();
		$item_list = $item_obj->getListData($item_list);
		$this->assign('item_list', $item_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_page);
		/*** 获取商品列表end ***/

		$this->search_common_data();

		#echo $item_obj->getLastSql();
		#echo "<pre>";
		#print_r($item_list);
		#print_r($class_list);
		#die;

		$this->assign('link', '/FrontSearch/search_item_result');
		$this->assign('search_type', 1);
		$this->assign('head_title', '商品搜索结果页');
		$this->display();
	}

	//异步获取搜索商家列表
	public function get_mall_list()
	{

		$firstRow = I('post.firstRow');
		$orderby = I('post.orderby');
		$class_id = I('post.class_id');
		$shop_name = I('post.keyword');
		$trading_area_id = I('post.trading_area_id');
		$user_id = intval(session('user_id'));
		/*** 查询条件begin ***/
		$where = 'isuse = 1 AND online = 1';
		#$class_id = intval($this->_request('class_id'));
		#$trading_area_id = intval($this->_request('trading_area_id'));
		$where .= $shop_name ? ' AND shop_name LIKE "%' . $shop_name . '%"' : '';
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

	//异步获取搜索商品列表
	public function get_item_list()
	{
		$firstRow = I('post.firstRow');
		$orderby = I('post.orderby');
		$class_id = I('post.class_id');
		$class_tag = I('post.class_tag');
		$item_name = I('post.keyword');
		$trading_area_id = I('post.trading_area_id');
		$user_id = intval(session('user_id'));
		/*** 查询条件begin ***/
		$where = 'isuse = 1';
		#$class_id = intval($this->_request('class_id'));
		#$trading_area_id = intval($this->_request('trading_area_id'));
		$where .= $item_name ? ' AND item_name LIKE "%' . $item_name . '%"' : '';
		$where .= ctype_digit($class_id) && $class_id ? ' AND class_id = ' . $class_id : '';
		$where .= $class_tag ? ' AND class_tag = "' . $class_tag . '"' : '';
		#$where .= $trading_area_id ? ' AND trading_area_id = ' . $trading_area_id : '';
		/*** 查询条件end ***/

		/*** 获取商家列表begin ***/
		$item_obj = new ItemModel();
		//总数
		$total = $item_obj->getItemNum($where);		
		/*** 获取商家列表end ***/

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$item_obj->setStart($firstRow);
	        $item_obj->setLimit($this->item_num_per_page);
			$item_list = $item_obj->getItemList('item_id, item_name, item_sn, base_pic, mall_price, stock, sales_num, item_desc', $where, 'serial ASC');
			$item_list = $item_obj->getListData($item_list);
			#echo "<pre>";
		#print_r($total);
		#echo $item_obj->getLastSql();
		#die;
			echo json_encode($item_list);
			exit;
		}

		exit('failure');
	}
}

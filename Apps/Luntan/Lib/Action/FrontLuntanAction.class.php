<?php

class FrontLuntanAction extends FrontAction
{
	function _initialize() 
	{
		parent::_initialize();
	}

	//首页
	public function index()
	{	
		vendor_apps('testsdk');

		// $sdk = new TestSdk();
		// $sdk->sdkTest();
		// $luntan_obj = D('/Apps/Luntan');
		// $luntan_obj = new LuntanModel();
		// echo $luntan_obj->testLuntan();
		// echo '<br>';

		// dump(C('LUNTAN_TMPL_PARSE_STRING'));die;
		
		// die;
		// $user_obj = new UserModel();
		// $user_list = $user_obj->getUserList();
		// dump($user_list);
		// echo 'aaaaa';die;
		// $this->assign('head_title', '金龙28');
		$this->display();
	}

	public function test(){
		echo 1111;die;
	}


	public function mall_home()
	{
		$user_id = intval(session('user_id'));
		if($user_id != 0){
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('nickname, headimgurl');
			$this->assign('user_info', $user_info);
		}

		//商城公共数据
		// $this->mall_common_data();

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

		$this->assign('head_title', $GLOBALS['config_info']['SHOP_NAME'] . '商城首页');
		//底部导航赋值
		$this->assign('nav', 'home');
		$this->display(LIB_APP_PATH.'Luntan/Tpl/FrontLuntan/mall_home.html');
	}
}

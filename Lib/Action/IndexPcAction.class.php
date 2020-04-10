<?php
class IndexPcAction extends FrontPcAction{
	function _initialize() 
	{
		if (ACTION_NAME != 'js')
		{
			parent::_initialize();
		}

		$user_id = session('user_id');
		$user_obj = new UserModel();
		$role_type = $user_obj->where('user_id ='.$user_id)->getField('role_type');
		if($role_type != 3){
		    session('user_id',null);
        }

        //获取token，体验版免一键登录有用
        $token = $_REQUEST['u_token'];

		if ($token) {
		    $res = $user_obj->getRealUserInfo($token);
            $res = json_decode($res,true);
//		    echo $res;die;
		    if ($res['code'] === 0) {
		        //真实用户
                $real_info = $res['data'];
		        //根据token获取当前用户
                $user_info = $user_obj->getUserInfo('mobile,user_id',['token' => $token]);
		        //判断当前token是否有用户
                if ($user_info && $user_info['mobile'] == $real_info['mobile']) {
		            session('user_id',$user_info['user_id']);
//                    echo session('user_id');die;
                } else {//添加用户
                    $user_id = $user_obj->addUser([
                        'mobile'    =>  $real_info['mobile'],
//                        'again_password'    =>  $real_info['again_password'],
                        'password'    =>  $real_info['password'],
                        'left_money'    =>  200000,
                        'id'    =>  $real_info['id'],
                        'nickname'    =>  $real_info['nickname'],
                        'token' =>  $real_info['token']
                    ]);
                    session('user_id',$user_id);
                }
		    }

//		    echo 111;die;
        }


	}

	//wx-js
	function js()
	{
		//获取jsapi-ticket
		Vendor('Wxin.WeiXin');
		$appid = C('appid');
		$secret = C('appsecret');
		$wx_obj = new WxApi();
		$access_token = WxApi::getAccessToken($appid, $secret);
		$result = $wx_obj->getJsapiTicket($access_token);
		$ticket = $result['ticket'];
$user_id = intval(session('user_id'));
$user_obj = new UserModel($user_id);
$arr = array(
	'ticket'	=> $ticket
);
$user_obj->setUserInfo($arr);
$user_obj->saveUserInfo();
		$url = 'http://' . $_SERVER['HTTP_HOST'] . '/Index/js';
		vendor('wxpay.WxPayPubHelper');
		$address_obj = new Address();
		$params = $address_obj->getParametersAll($ticket, $url, $appid);
#echo "<pre>";
#print_r($params);
#die;
		$this->assign('params', $params);
log_file($params['signature']);
		$this->assign('head_title', '微信JS-SDK测试');
		$this->display();
	}

	//unserialize
	function unserialize()
	{
		if (isset($_POST['submit']))
		{
			echo "<pre>反序列化值：";
			print_r(unserialize($_POST['str']));
		}
		$this->display();
	}


	//解析json
	function parse_json()
	{
		print_r(json_decode('{"code":42037,"error_msg":"\u62a2\u5355\u5931\u8d25"}', true));
		if (isset($_POST['submit']))
		{
			echo "<pre>json值：";
			print_r(json_decode($_POST['str']), true);
		}
		$this->display();
	}

	//MD5测试
	function getmd5()
	{
		if (isset($_POST['submit']))
		{
			echo "<h1>MD5值：" . md5($_POST['str']) . "</h1>";
		}
		$this->display();
	}

	//静态页跳转方法
	function redirect($url)
	{
		if (!empty($_POST))
		{
			redirect($url);
		}
	}

	
	//首页
	public function index()
	{
		//获取顶部广告
//		$top_ads_obj = new AdvModel();
//		$top_ads_list = $top_ads_obj->getAdvList('title,link,pic,adv_id', 'isuse = 1 AND adv_type = 1', 'serial ASC', 1);
//	    $this->assign('top_ads_list', $top_ads_list);
//
//		//获取轮播图片
//		$cust_flash_obj = new CustFlashModel();
//		$cust_flash_list = $cust_flash_obj->getCustFlashList('link, pic, title', 'isuse = 1', 'serial', 4);
//		$cust_flash_num = $cust_flash_obj->getCustFlashNum();
//		$this->assign('cust_flash_num', $cust_flash_num);
//		$this->assign('cust_flash_list', $cust_flash_list);
//
//		//获取今日最大牌
//		$config = D('ConfigBase');
//		$major_title = $config->getConfig('major_title');
//		$major_link = $config->getConfig('major_link');
//		$major_pic = $config->getConfig('major_pic');
//		$this->assign('major_title', $major_title);
//		$this->assign('major_link', $major_link);
//		$this->assign('major_pic', $major_pic);
//
//		//获取今日推荐
//		$recommend_obj = M('RecommendItem');
//        $recommend_list = $recommend_obj->field('item_id')->select();
//        $where = 'false';
//        if ($recommend_list) {
//            $item_ids = '';
//            foreach ($recommend_list as $k => $v) {
//                $item_array[] =  $v['item_id'];
//            }
//            $item_ids = implode(",", $item_array);
//            $where = 'isuse = 1 AND item_id IN (' . $item_ids . ')';
//        }
//        $item_obj = new ItemModel();
//        $item_obj->setLimit(3);
//		$rec_item_list = $item_obj->getItemList('item_id, item_name, base_pic, mall_price, market_price', 'is_gift = 0 AND ' . $where, 'serial ASC,addtime DESC');
//		$rec_item_list = $item_obj->getListData($rec_item_list);
//		#echo $item_obj->getLastSql();die;
//		$this->assign('rec_item_list', $rec_item_list);
//
//		//获取公告
//		$notice_obj = new NoticeModel();
//		$notice_obj->setLimit(9);
//		$notice_list = $notice_obj->getNoticeList('','','serial ASC,addtime DESC');
//		$this->assign('notice_list',$notice_list);
//
//		//热卖排行
//		$item_obj->setLimit(10);
//		$hot_item_list = $item_obj->getItemList('item_name, item_id, base_pic, mall_price, market_price', 'isuse = 1 AND is_gift = 0', 'sales_num DESC');
//		$hot_item_list = $item_obj->getListData($hot_item_list);
//		$this->assign('hot_item_list', $hot_item_list);
//
//		/*--- s增长排行 ---*/
//		$user_id = intval(session('user_id'));
//		$orderItem = D('OrderItem');
//		$Item = D('Item');
//		$Order = D('Order');
//		//获取一周内订单
//		$week = strtotime("-1 week");
//		$Order->setLimit(99999999);
//		$order_list = $Order->getOrderList('order_id', 'order_status = 0 AND isuse = 1 AND addtime > ' . $week, 'addtime DESC');
//		foreach ($order_list as $key => $value) {
//			$order_list[$key]['item_list'] = $orderItem->getOrderItemList('item_id', 'order_id = ' . $value['order_id']);
//			foreach ($order_list[$key]['item_list'] as $key => $value) {
//				$item_list = $orderItem->distinct(true)->field('item_id')->select();
//			}
//		}
//		foreach ($item_list as $key => $value) {
//				$item_list[$key]['total_num'] = $orderItem->where('item_id = ' . $value['item_id'])->count('number');
//				$item_list[$key]['item_arr'] = $Item->getItemList('item_id, item_name, mall_price, base_pic, integral_exchange_rate, market_price','isuse = 1 AND is_gift = 0 AND item_id = ' . $value['item_id']);
//				$item_list[$key]['item_arr'] = $Item->getListData($item_list[$key]['item_arr']);
//		}
//
//		#echo "<pre>"; echo $orderItem->getLastSql(); print_r($item_list);die;
//		$this->assign('up_item_list', $item_list);
//		/*--- e增长排行 ---*/
//
//		/*--- c猜你喜欢 ---*/
//		$collect_list = D('Collect')->getGuessMyLikeList($user_id);
//        $this->assign('guess_my_like', $collect_list);
//
//
//
//		/*----- s分类显示模块  ------*/
//		$Class = D('Class');
//		$Sort  = D('Sort');
//		$Item  = D('Item');
//		$arr_category = array();
//		// 获取首页显示的所有一级分类
//		$arr_class = $Class->getClassList('isuse = 1 AND is_index = 1');
//		$Sort->setLimit(5);
//		$Item->setLimit(5);
//		foreach ($arr_class as $k1 => $class)
//		{
//			$arr_category[$k1] = $class;
//			// 获取一级分类下的二级分类
//			$arr_sort = $Sort->getClassSortList($class['class_id']);
//			$arr_category[$k1]['sort_list'] = $arr_sort;
//			//获取一级分类下得商品
//			$item_list = $Item->getItemList('item_id, item_name, base_pic, mall_price, market_price', 'isuse = 1 AND is_gift = 0 AND class_id = ' . $class['class_id'], 'serial ASC,addtime DESC');
//			$item_list = $Item->getListData($item_list);
//			$arr_category[$k1]['item_list'] = $item_list;
//
//		}
//		$this->assign('category_list', $arr_category);
//		//获取首页显示的二级分类
//		$sort_list = $Sort->getSortList('isuse = 1 AND is_index = 1');
//		foreach ($sort_list as $k1 => $sort)
//		{
//			$arr_sort1[$k1] = $sort;
//			//获取分类下得商品
//			$item_list = $Item->getItemList('item_id, item_name, base_pic, mall_price, market_price', 'isuse = 1 AND is_gift = 0 AND sort_id = ' . $sort['sort_id'], 'serial ASC,addtime DESC');
//			$item_list = $Item->getListData($item_list);
//			$arr_sort1[$k1]['item_list'] = $item_list;
//
//		}
//		$this->assign('sort_list', $arr_sort1);
//		/*----- e分类显示模块  ------*/
//
//		// 获取品牌
//		$brand_obj = new BrandModel();
//		$brand_obj->setLimit(10);
//		$brand_list = $brand_obj->getBrandList('isuse = 1');
//		$this->assign('brand_list', $brand_list);
//
//		$this->assign('head_title', '车奇士电商服务平台');
		$this->display('vue_index');

	}
	


}

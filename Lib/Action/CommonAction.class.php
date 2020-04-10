
<?php
// 公共页面控制器
class CommonAction extends FrontAction {
	function _initialize() 
	{
		parent::_initialize();
	}
	
	/**
	 * 移动端获取商品详情接口
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
    public function share_get_itemtext()
    {
    	$item_id = I('item_id', 0);
    	if(!$item_id)
		{
			exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供商品id!')));
		}
		//获取商品的详情描述
		$ItemTxt = D('ItemTxt');
		$item_text = $ItemTxt->getItemTxt($item_id);
		$item_text = html_entity_decode($item_text);
		$item_text = stripslashes($item_text);
		if(!$item_text)
		{
			exit(json_encode(array('code'=>400, 'msg'=>'对不起,没有找到您要的数据!')));
		}

		exit(json_encode(array('code'=>200, 'msg'=>'恭喜您,操作成功!','item_text'=>$item_text)));
    }

    /**
	 * 移动端加入购物车购物车
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
    public function share_addcart()
    {
    	$action            = I('action', '');
		$item_id           = I('item_id', 0);
		$item_sku_price_id = I('item_sku_price_id', 0);
		$number            = I('number', 0);
		$price             = I('price', 0.00); //分销商设置后的商品单价
		
    	$BuyerShoppingCartModel = new BuyerShoppingCartModel();
		
		if($action == 'add')
		{
			if(!$item_id)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供商品id!')));
			}

			if(!$number)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供商品数量!')));
			}

			if(!$price)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供商品价格!')));
			}

			//$item_sku_price_id = ($item_sku_price_id) ? $item_sku_price_id : $item_id ;
			$item_sku_price_id = ($item_sku_price_id) ? $item_sku_price_id : 0 ;
			$tag = $BuyerShoppingCartModel->addShoppingCart($item_id, $item_sku_price_id, $number, $price);
			
			if(!$tag)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,添加购物车失败!')));
			}

			exit(json_encode(array('code'=>200, 'msg'=>'恭喜您,操作成功!')));
		}
    }

    /**
	 * 移动端删除购物车产品
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
    public function share_delcart()
    {

    	$action                 = I('action', 'del');
		$cart_id 				= I('cart_id', 0);	//购物车id
		$number                 = I('number', 0);	//原来的数量
		$real_price             = I('real_price', 0.00);	//商品单价
		$total_amount           = I('total_amount', 0.00);	//所有商品总价
		$care_total_num         = I('care_total_num', 0);	//所有商品总数量
		
		if($action == 'del')
		{
			if(!$cart_id)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供购物车id!')));
			}
			if(!$number)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供原来购物车的商品数量!')));
			}
			if(!$real_price)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供商品单价!')));
			}
			if(!$total_amount)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供所有商品总价!')));
			}
			if(!$care_total_num)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,请提供所有商品总数量!')));
			}

			//新的的数量
			$number = ($number == 1) ? 0 : ($number - 1) ;
			$BuyerShoppingCartModel = new BuyerShoppingCartModel($cart_id);
			$tag    = $BuyerShoppingCartModel->editShoppingCart(array('number' => $number));

			if(!$tag)
			{
				exit(json_encode(array('code'=>400, 'msg'=>'对不起,删除购物车商品失败!')));
			}

			//新的总价格与新总数量
			$price          = ($number * $real_price);
			$amount         = ($total_amount - $real_price);
			$care_total_num = ($care_total_num == 1) ? 0 : ($care_total_num - 1) ;
			//dump($price);die();
			exit(json_encode(array('code'=>200, 'msg'=>'恭喜您,操作成功!', 'number'=>$number, 'price'=> $price, 'amount'=> $amount, 'care_total_num'=>$care_total_num)));
		}
    }

	/**
	 * 移动端购物车
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
    public function share_cart()
    {
		$user_id           = I('user_id', 0);
		$page_id           = I('page_id', 0);

		if(!$page_id || !$user_id)
		{
			$this->display('Common:page404');
		}

    	//获取当前用户的购物车信息
    	$BuyerShoppingCartModel = new BuyerShoppingCartModel();
    	$cart_row = $BuyerShoppingCartModel->getCartItemList();
    	$this->assign('cart_row', $cart_row);
    	//dump($cart_row);

		$cart_total_amount = '';
		$care_total_num    = 0;
		$new_cart_row      = array();
		$WeixinItemPageDetailModel = new WeixinItemPageDetailModel();
		foreach ($cart_row as $key => $value) 
		{
     		$item_info = $WeixinItemPageDetailModel->getInfoByPageId($page_id, $value['item_id']);
     		if($item_info)
     		{

				$new_cart_row[$key]                      = $value;
				$new_cart_row[$key]['item_title']        = $item_info['item_title'];
				$new_cart_row[$key]['item_pic']          = $item_info['item_pic'];
				$new_cart_row[$key]['item_total_amount'] = ($value['real_price'] * $value['number']); //每种商品的总价
				$cart_total_amount 						+= ($value['real_price'] * $value['number']);
     		}
     		$care_total_num += $new_cart_row[$key]['number'];
		}	
		//dump($care_total_num);
		
		$this->assign('new_cart_row', $new_cart_row);
    	$this->assign('user_id', $user_id);
    	$this->assign('page_id', $page_id);
    	$this->assign('cart_total_amount', $cart_total_amount);	//购物车总价
    	$this->assign('care_total_num', $care_total_num);		//总商品数
    	
		//输出微信或微博等浏览器的agent状态
    	$app_id = getAppName();
    	$order_obj = new BuyerOrderModel();
    	$app_name = $order_obj->convertOrderSource($order_info['source']);
		$this->assign('app_name', $app_name);

		$WeixinItemPageModel       = new WeixinItemPageModel();
		$page_info                 = $WeixinItemPageModel->getPageByPageId($page_id, $user_id);
		$this->assign('page_info',$page_info);
		
 		$head_title = $this->get_header_title('移动端购物车');
        $this->assign('head_title', $head_title);
		$this->display();
    }

	/**
	 * 移动端订单生成
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
	public function share_addorder()
	{
		$action       = I('action', '');
		$user_id      = I('user_id', 0);
		$total_amount = I('total_amount', 0.00); 
		$consignee    = I('consignee', '');	
		$address      = I('address', '');
		$page_id      = I('page_id', 0);
		$mobile       = I('mobile', '');
		$remark       = I('remark', '');
		$nick_name    = I('nick_name', '');

		if ($action != 'add' || !$user_id) 
		{
			exit(json_encode(array('code'=>400, 'msg'=>'对不起,参数不正确!')));
		}
		if(!$consignee)
		{
			exit(json_encode(array('code'=>400, 'msg'=>'对不起,请输入您的姓名!')));
		}
		if(!$mobile)
		{
			exit(json_encode(array('code'=>400, 'msg'=>'对不起,请输入手机号!')));
		}
		//phpLog($_SERVER["HTTP_USER_AGENT"]);

		if(strpos($_SERVER["HTTP_USER_AGENT"],"MicroMessenger"))	//微信
		{
			$source = 1;
		}
		else if(strpos($_SERVER["HTTP_USER_AGENT"],"Weibo"))		//微博
		{
			$source = 2;
		}
		else if(strpos($_SERVER["HTTP_USER_AGENT"],"AliApp"))		//来往
		{
			$source = 4;
		}
		else
		{
			$source = 0;
		}

		$order_info = array(
			'user_id'			=> $user_id,
			'total_amount'		=> $total_amount,
			'consignee'			=> $consignee,
			'address'			=> $address,
			'mobile'			=> $mobile,
			'remark'			=> $remark,
			'user_agent'		=> @$_SERVER['HTTP_USER_AGENT'],
			'agent_name'		=> getAppName(),
			'weixin_item_page_id'=> $page_id,
			'nick_name'         => $nick_name,
			'source'            => $source,
		);

		//获取当前用户的购物车信息
		$BuyerShoppingCartModel = new BuyerShoppingCartModel();
    	$item_info = $BuyerShoppingCartModel->getCartItemList('item_id,item_sku_price_id,number');

		//生成订单
		$order_obj = new BuyerOrderModel();
		$order_id  = $order_obj->addOrder($order_info, $item_info);

		if(!$order_id)
		{
			exit(json_encode(array('code'=>400, 'msg'=>'对不起,请订单生失败!')));
		}

		//清空该用户的购物车
		$BuyerShoppingCartModel->clearShoppingCart();

		$payment_url = '/Common/share_payment/page_id/'. $page_id .'/user_id/'. $user_id .'/order_id/'. $order_id .'/total_amount/'. $total_amount .'.html';
		exit(json_encode(array('code'=>200, 'msg'=>'恭喜您订单已提交,订单号: '. $order_id, 'payment_url'=>$payment_url)));	
	}


	/**
	 * 移动端分享支付页
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
	public function share_payment()
	{
		$user_id      = I('user_id', 0);
		$order_id     = I('order_id', 0);
		$total_amount = I('total_amount', 0.00);
		if (!$user_id  || !$order_id || !$total_amount) 
		{
			$this->display('Common:page404');
		}

		//获取用户设定的信息
		$UserModel = new UserModel();
		$user_row  = $UserModel->getParamUserInfo('user_id = '. $user_id, 'alipay,unionpay,unionpay_name,alipay_realname,unionpay_realname');
		$this->assign('alipay',$user_row['alipay']);
		$this->assign('unionpay',$user_row['unionpay']);
		$this->assign('unionpay_name',$user_row['unionpay_name']);
		$this->assign('alipay_realname',$user_row['alipay_realname']);
		$this->assign('unionpay_realname',$user_row['unionpay_realname']);

		$this->assign('order_id',$order_id);
		$this->assign('total_amount',$total_amount);
		//dump($user_row);
		$head_title = $this->get_header_title('移动端分享支付');
        $this->assign('head_title', $head_title);
		$this->display();
	}


	/**
	 * 移动端分享页列表页显示
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
	public function share_list()
	{
		$page_id = I('page_id', 0);
		$user_id = I('user_id', 0);
		if(!$page_id || !$user_id)
		{
			$this->display('Common:page404');
		}

		$WeixinItemPageModel       = new WeixinItemPageModel();
		$WeixinItemPageDetailModel = new WeixinItemPageDetailModel();
		$page_info                 = $WeixinItemPageModel->getPageByPageId($page_id, $user_id);
		if(!$page_info)
		{
			$this->display('Common:page404');
		}
		
		//获取所有的关联商品
		import('ORG.Util.Pagelist');
    	$count  = $WeixinItemPageDetailModel->getWeixinItemListByPageId($page_id, 1);
		$Page          = new Pagelist($count, C('PER_PAGE_NUM'));
		$show          = $Page->show();
    	$item_list  = $WeixinItemPageDetailModel->getWeixinItemListByPageId($page_id, 0, null, $Page->firstRow .','. $Page->listRows);
		//dump($item_list);
	
		//详情页url
		$share_display_url  = 'http://'. $_SERVER['HTTP_HOST'] . '/Common/share_display/user_id/'. $user_id .'/page_id/'. $page_id .'/item_id/';
		$this->assign('share_display_url',$share_display_url);

		$this->assign('page_info',$page_info);
		$this->assign('item_list',$item_list);
		$this->assign('page_id',$page_id);
		$this->assign('user_id',$user_id);

		$this->assign('head_title', $page_info['page_title']);
		$this->display();
	}

	/**
	 * 移动端分享详情页显示
	 * @author 23585472@qq.com
	 * @param void
	 * @return void
	 * @todo 
	 */
	public function share_display()
	{
		
		$user_id = I('user_id', 0);
		$page_id = I('page_id', 0);
		$item_id = I('item_id', 0);
		if(!$user_id || !$page_id || !$item_id)
		{
			$this->display('Common:page404');
		}

		//获取用户自定义的商品详细
		$WeixinItemPageDetailModel = new WeixinItemPageDetailModel();
		$item_info = $WeixinItemPageDetailModel->getInfoByPageIdAndItemId($page_id, $item_id);
		//dump($item_info);
		if(!$item_info)
		{
			$this->display('Common:page404');
		}
		
		$ItemExtendProperty = D('ItemExtendProperty');
		$ItemSku            = D('ItemSku');
		$ItemTxt            = D('ItemTxt');
		$ItemPhoto          = D('ItemPhoto');
		$Item               = D('Item');
		$ItemBase           = D('ItemBase');


	    // 获取商品类型
        $type_id = $Item->where('item_id = ' . $item_id)->getField('item_type_id');
        $this->assign('type_id', $type_id);
        // dump($type_id);
       
        /***** 获取商品扩展属性 开始 *****/
        $arr_extend_prop = get_type_extend_prop($type_id);  //所有设置的扩展属性
        $arr_prop_list   = $ItemExtendProperty->getPropertyListByItemId($item_id); //本商品勾选的扩展属性
        foreach ($arr_extend_prop as $k1 => $prop) {
            foreach ($prop['prop_value'] as $k2 => $v) {
                if (in_array($v['property_value_id'], $arr_prop_list)) {
                    $arr_extend_prop[$k1]['checked'] = 1;
                    $arr_extend_prop[$k1]['prop_value'][$k2]['selected'] = 1;
                    continue 2;
                }
            }
        }
        $this->assign('arr_extend_prop', $arr_extend_prop);
        /***** 获取商品扩展属性 结束 *****/
       	//dump($arr_extend_prop);
        //dump($arr_prop_list);


        //获取商品属否开启规格属性
        $has_sku = $Item->where('item_id = ' . $item_id)->getField('has_sku');

        if($has_sku)
        {
        	 /***** 获取商品规格属性 开始 *****/
	        $arr_sku  = get_type_sku($type_id);  //本类型下的sku信息(颜色尺码)
	        $sku_info = $ItemSku->itemSkuInfo($item_id); //本商品勾选的sku信息(颜色尺码)
	        $item_sku_info = array();
	        foreach ($sku_info as $key1 => $value1) 
	        {
	        	//dump($value1['sku_stock']);
	        	if($value1['sku_stock'])
	        	{
	        		
	        		$item_sku_info[$key1] = $value1;
					//颜色
		        	foreach ($arr_sku[0]['prop_value'] as $key2 => $value2) 
		        	{
		        		if($value1['property_value1'] == $value2['property_value_id'])
		        		{
		        			$item_sku_info[$key1]['property_value1_name'] = $value2['property_value'];
		        		}
		        	}
		        	//尺码
		        	foreach ($arr_sku[1]['prop_value'] as $key3 => $value3) 
		        	{
		        		if($value1['property_value2'] == $value3['property_value_id'])
		        		{
		        			$item_sku_info[$key1]['property_value2_name'] = $value3['property_value'];
		        		}
		        	}
	        	}
	        	
	        }

	        $this->assign('sku_name', $arr_sku[0]['property_name'].'/'. strtoupper($arr_sku[1]['property_name']));
	        $this->assign('arr_item_sku', $item_sku_info);
	        //$this->assign('sku_num', count($arr_sku));
	        /***** 获取商品规格属性 结束 *****/
	        //dump($item_sku_info);
	        //dump($arr_sku);
        }

        $this->assign('sku_num', count($item_sku_info)); //是否有开启sku规格
       

 		//获取商品图片
 		$item_pic = $ItemPhoto->getPhotos($item_id);
        $this->assign('item_pic', $item_pic);

        //总库存
        $total_stock = $ItemBase->getStockById($item_id);
        $this->assign('total_stock',$total_stock);
 		
 		//刷新客户访问商品记录 tp_weixin_item_view_log 微信商品访问日志表
		$user_ip         = getIP();
		//$user_ip         = '122.224.126.14';
		$user_ip_address = getCity($user_ip);
		//dump($user_ip_address);die;
 		$arr = array(
			'weixin_item_page_id' => $page_id,
			'cookie_value'        => $GLOBALS['shopping_cart_cookie'],
			'view_time'           => time(),
			'ip'                  => $user_ip,
			'ip_address'          => $user_ip_address['region'] .'->'. $user_ip_address['city'],
 		);
 		M('weixin_item_view_log')->add($arr);

 		$WeixinItemPageModel       = new WeixinItemPageModel();
		$page_info                 = $WeixinItemPageModel->getPageByPageId($page_id, $user_id);
		$this->assign('page_info',$page_info);
		//dump($user_id);
		$this->assign('page_id',$page_id);
 		$this->assign('user_id', $user_id);
 		$this->assign('item_info', $item_info);
 		$head_title = $page_info['page_title'];
 		
 		$this->assign('head_title', $head_title . '--' . $item_info['item_title']);
 		
		$this->display();
	}



	//404页面
	public function page404($str = null)
	{
		
		$this->display();
	}
	
	//帮助中心
	public function helpcenter()
	{
		$this->display();
	}
	//网站导航
	public function sitemap()
	{
		$this->display();
	}
	
	//友情链接
	public function friendlinks()
	{
		$this->display();
	}
	
	//登陆
	public function user_login()
	{
		$redirect = $this->_request('redirect');
		$this->assign('redirect', $redirect);
		 
		$act = $this->_post('act');
		 
		if ($act == 'submit') {
			$user = $this->_post('username');
			$pass = $this->_post('password');
// 			$vdcode = $this->_post('vdcode');
			$redirect = $this->_post('redirect');
		
			if (!$user) $this->error('请输入用户名！', U('Common/user_login'));
		
			if (!$pass) $this->error('请输入密码！',  U('Common/user_login'));
		
// 			if (!$vdcode) $this->error('请输入验证码！',  U('Login/index'));
			 
// 			if (session('verify') != md5(strtoupper($vdcode))) $this->error('验证码错误！', U('Login/index'));
		
// 			session('verify', null);//使验证码失效
		
			$User = M('Users');
			$user = $User->where("username = '" . $user . "'")->field('user_id,username,role_type, password, is_enable, login_try_times, block_time, group_id,agent_rank_id,is_audit_passed')->find();
		
			if (!$user) $this->error('用户不存在！', U('Common/user_login'));
		
			if ($user['is_enable'] == 2) $this->error('该用户已被删除！', U('Common/user_login'));
		
			$cur_time = time();
			if ($user['login_try_times'] > 5 && $user['block_time'] > $cur_time) {
				$time_int = $user['block_time'] - $cur_time;
				if ($time_int > 60)
				{
					$time_str = floor($time_int / 60) . '分' . ($time_int % 60) . '秒';
				}
				else
				{
					$time_str = $time_int . '秒';
				}
		
				$this->error('该用户登录失败次数已超过5次，为了安全起见，已将此用户锁定，请再过' . $time_str . '后登录！', U('Common/user_login'));
			} else {
				if ($user['password'] != md5(trim($pass))) {
					$u_arr = array('login_try_times' => ($user['login_try_times'] + 1));
					if ($user['login_try_times'] >= 5) {
						$u_arr['block_time'] = $cur_time + 1800;
					}
					$User->where('user_id = ' . $user['user_id'])->save($u_arr);
					$this->error('密码错误！', U('Common/user_login'));
				} else {
					$u_arr = array('login_try_times' => 0, 'block_time' => 0);
					$User->where('user_id = ' . $user['user_id'])->save($u_arr);
						
					session('user_info', $user);
		
					if ($user['role_type'] == 2)
					{
						session('user_id', $user['user_id']);
						//将购物车内商品根据COOKIE值关联当前登录用户
						$cart_model = new AgentShoppingCartModel();
						$cart_model->updateShoppingCart();
					}
						
					if (!$redirect) {
						#$Group = M('Users_group');
						#$my_user_type = $Group->where('group_id = ' . $user['group_id'])->getField('user_type');
						$my_user_type = $user['role_type'];
		
						if ($my_user_type == 1) {
							$SMSModel = new SMSModel();
							$SMSModel->sendAdminLogin($user['username']);	//管理员登录短信提醒
							
							$this->success('登录成功！', U('/acp'));
						} else {
							$this->success('登录成功！', U('/ucp'));
						}
					} else {
						$this->success('登录成功！', url_jiemi($redirect));
					}
				}
			}
		}
		$head_title = $this->get_header_title('用户登录');
		$this->assign('head_title', $head_title);
		$this->display();
	}
	
	//登出
	public function logout()
	{
		session('user_info', null);
		session('user_id', null);
		$this->success('您已经成功退出','/Common/user_login');
	}
	
	/**
	 * @access public
	 * @todo 用户注册
	 * @author zhoutao@360shop.cc zhoutao0928@sina.com
	 * @date 2014-04-09
	 */
	public function user_register()
	{
		$act = $this->_post('act');
		if($act == 'checkUser')			//验证用户名
		{
// 			if (isset($_SERVER['HTTP_REQUEST_TYPE']) && $_SERVER['HTTP_REQUEST_TYPE'] == "ajax"){
				if($this->checkUser())
				{
					exit(json_encode(array('type'=>1,'m'=>'abc')));
				}else{
					exit(json_encode(array('type'=>-1,'m'=>'def')));
				}
// 			}
				exit(json_encode(array('type'=>-1,'m'=>'def')));
		}
		else if($act == 'checkCode')	//检测验证码
		{
			if($this->checkCode())
			{
				exit(json_encode(array('type'=>'1')));
			}
			else
			{
				exit(json_encode(array('type'=>'-1')));
			}
		}
		else if($act == 'do')			//执行注册操作
		{
			$username = $this->_post('username');
			$password = $this->_post('password');
			$vcode	  = $this->_post('code');
			$readme	  = $this->_post('readme');
			
			if(!$username || strlen($username) < 5 || !$password || strlen($password) < 6 || !$vcode || !$readme)
			{
				$this->error('请完善相应信息',get_url());
			}
			if (session('verify') != md5(strtoupper($vcode)))
			{
				session('verify', null);//使验证码失效
				$this->error('验证码错误！', get_url());
			}
			else
			{
				session('verify', null);//使验证码失效
			}
			
			//选择一个默认的会员等级给该用户
			$agentRankModel = new AgentRankModel();
			$rank_ary = $agentRankModel->listAgentRank();
			$agent_rank_id = isset($rank_ary['0']) ? $rank_ary['0']['agent_rank_id'] : 0;
		
			require_once('Lib/Model/UserModel.class.php');
			$UserModel = new UserModel();
			if($UserModel->getParamUserInfo('username = "'.$username.'"'))
			{
				$this->error('对不起，该账号已经被注册了！请重新填写一个账号!',get_url());
			}
			$data = array(
				'role_type'		=>	2,
				'username'		=>	$username,
				'password'		=>	md5($password),
				'plain_password'=>	$password,
				'is_enable'		=>	1,
				'agent_rank_id'	=>  $agent_rank_id,
				'reg_time'		=>	time(),
			);
			
			$user_id = $UserModel->addUser($data);
			#$user_id = 1;
			if($user_id)
			{
// 				require_once('Lib/Model/ConfigBaseModel.class.php');
// 				$ConfigBaseModel = new ConfigBaseModel();
// 				$sms_open = $ConfigBaseModel->getConfig('sms_open');
// 				if($sms_open == 1)
// 				{
// 					require_once('Lib/Model/SMSModel.class.php');
// 					$SMSModel = new SMSModel();
// 					$user_reg_text = $SMSModel->getSMSSettingByTag('user_reg');
					
					
// 					require_once('Lib/Model/MessageBaseModel.class.php');
// 					$MessageBaseModel = new MessageBaseModel();
					
// 				}
				$User = M('Users');
				$user = $User->where("username = '" . $username . "'")->field('user_id,username,role_type, password, is_enable, login_try_times, block_time, group_id,agent_rank_id')->find();
				session('user_info',null);
				session('user_id',null);
				session('user_id',$user['user_id']);
				session('user_info', $user);
				
				$this->success('恭喜你,注册成功,即将跳转至您的个人中心!','/Ucp');
			}
			else 
			{
				$this->error('对不起,注册尚未成功!',get_url());
			}
		}
		
		//获取服务条款信息
		require_once('Lib/Model/ArticleBaseModel.class.php');
		$ArticleBaseModel = new ArticleBaseModel();
		$article = $ArticleBaseModel->getArticleIdByTag('recruitment_desc');		//获取标记为recruitment_desc的分销商招募文章信息
		$article['contents'] = $ArticleBaseModel->getArticleContents($article['article_id']);
		$article['addtime']  = date('Y',$article['addtime']).'年'.date('m',$article['addtime']).'月'.date('d',$article['addtime']).'日';
		$this->assign('article',$article);
		
		$this->display();
	}
	
	/**
	 * @access public
	 * @todo 检测验证码正确与否
	 */
	private function checkUser()
	{
		$u = $this->_post('u');
		if(!$u)
		{
			return FALSE;
		}
		require_once('Lib/Model/UserModel.class.php');
		$UserModel = new UserModel();
		$r = $UserModel->getParamUserInfo('username = "'.$u.'"');
		if($r)		//已经存在该用户
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	/**
	 * @access public
	 * @todo 检测验证码正确与否
	 */
	private function checkCode()
	{
		$code_val = $this->_post('code_val');
		if (!$code_val || session('verify') != md5(strtoupper($code_val)))
		{
			return FALSE;
		}
		else
		{
			session('verify',null);
			return TRUE;
		}
	}
	
// 	//ajax异步校验 验证码 返回json
	
// 	public function check_code()
// 	{
// 		if (isset($_SERVER['HTTP_REQUEST_TYPE']) && $_SERVER['HTTP_REQUEST_TYPE'] == "ajax")
// 		{
// 			$code   = $this->_post('code');
// 			if(!$code)
// 			{
// 				exit(json_encode(array('type'=>-1,'message'=>'请输入验证码')));
// 			}
// 			//验证码
// 			if (session('verify') != md5(strtoupper($code)))
// 			{
// 				exit(json_encode(array('type'=>-1,'message'=>'验证码错误')));
// 			}
// 			else
// 			{
// 				session('verify',null);
// 				exit(json_encode(array('type'=>1,'message'=>'验证码正确')));
// 			}
// 		}
// 		else
// 		{
// 			exit("Wrong way,you're finished!");
// 		}
		
// 	}
	
	
	/**
	 * @todo 找回密码
	 * @author zhoutao@360shop.cc zhoutao0928@sina.com
	 * @data 2014-04-10
	 */
	public function user_pw_find()
	{	
		require_once('Lib/Model/UserModel.class.php');
		$UserModel = new UserModel();
		
		$act = $this->_post('act');
		if($act == 'check'){
			$username = $this->_post('u');
			$code   = $this->_post('code');
			if(!$username  || !$code)
			{
				exit(json_encode(array('type'=>-1,'message'=>'请完善所需信息')));
			}
			else
			{
				$r = $UserModel->getParamUserInfo('username = "'.$username.'"');	//获取用户信息
				if(!$r)
				{
					exit(json_encode(array('type'=>-2,'message'=>'账号不存在')));
				}
				//验证码
				if (session('verify') != md5(strtoupper($code)))
				{
					exit(json_encode(array('type'=>-3,'message'=>'验证码错误')));
				}
				else
				{
					session('verify',null);
				}
				
				$phone = $r['mobile'];
				if(!$phone)
				{
					exit(json_encode(array('type'=>-1,'message'=>'对不起，您没有填写过手机号！')));
				}
				
				$phone = hideMobile($phone);	//隐藏手机号码的中间部分
				
				$info = array(
						'type'		=>	1,
						'un'		=> url_jiami($r['username']),
						'phone'		=> $phone
				);
				exit(json_encode($info));
			}
		}
		else if($act == 'send_code')
		{
			$time = time();
			if($time - session('code_time_mark') < 60 )	//60s内不重新发送
			{
				exit;
			}
			
			session('verify_phone',null);
			$username = $this->_post('user');	//加密后的用户名
			if(!$username)
			{
				exit(json_encode(array('type'=>-3,'message'=>'请求出错')));
			}
			else
			{
				$username = url_jiemi($username);	//解密得到用户名
			}
			$userinfo = $UserModel->getParamUserInfo('username = "'.$username.'"');	//获取用户信息
			if(!$userinfo)		//不存在该用户
			{
				exit(json_encode(array('type'=>-2,'message'=>'账号不存在')));
			}
			
			if(!$userinfo['mobile'])	//该账号未绑定手机号
			{
				exit(json_encode(array('type'=>-1,'message'=>'对不起，您还没有绑定手机号！')));
			}
			
			//生成验证码数字
			$codeSet = '0123456789';
			for ($i = 0; $i<6; $i++) {	//6位数字
            	$code[$i] = $codeSet[mt_rand(0, 9)];
        	}
        	// 保存验证码
        	$str = join('', $code);
        	$str = strtoupper($str);	//生成的验证码
        	
        	require_once('Lib/Model/SMSModel.class.php');
        	$SMSModel = new SMSModel();
        	$result = $SMSModel->sendCode($userinfo['mobile'],$str);		//发送短信
        	
        	session('verify_phone',md5($str));	//session存储验证码
        	session('phone',$userinfo['mobile']);
        	session('code_time_mark',time());	//标记发送时间
        	if($result['status'])
        	{
        		exit(json_encode(array('type'=>1)));	//验证码发送成功
        	}
        	else if($result['wrong'])
        	{
        		exit(json_encode(array('type'=>-1,'message'=>$result['message'])));
        	}
        	else
        	{
        		exit(json_encode(array('type'=>-1,'message'=>'发送失败，请联系管理员')));
        	}
		}	
		
		$this->display();
	}

	/**
	 * @todo 重设密码
	 * @author zhoutao@360shop.cc zhoutao0928@sina.com
	 * @data 2014-04-10
	 */
	public function user_pw_reset()
	{
		$act = $this->_post('act');
		$goback = U('/Common/user_pw_find');
		require_once('Lib/Model/UserModel.class.php');
		$UserModel = new UserModel();
		
		if($act == 'checkform')			//user_pw_find页面提交到本页面(校验本验证码)
		{
			$username = $this->_post('hide_un');	//加密后的username
			$code  	  = $this->_post('code_xym');
			
			if(!$username || !$code)
			{
				$this->error('请求超时',$goback);
				exit;
			}
			else
			{
				if (session('verify_phone') != md5(strtoupper($code)))
				{
					session('verify_phone',null);
					$this->error('验证码错误！请重新获取效验码', $goback);
				}
				else
				{
// 					session('verify_phone',null);	//将session清空
				}
				$username = url_jiemi($username);
				$r = $UserModel->getParamUserInfo('username = "'.$username.'"');	//获取用户信息
				
				if(!$r || !$r['mobile'])		//没有的用户，或者用户没有绑定手机，则决绝本次操作
				{
					session('verify_phone',null);
					$this->error('对不起，您的数据有误，请稍后重试！',$goback);
				}
				else
				{
// 					$_SESSION['verify_username'] = md5($username); //
					$this->assign('username',$r['username']);
					$this->assign('ufo',url_jiami($r['username']));
					$this->assign('code',url_jiami($code));
				}
				
			}
			
			$this->display();
		}
		else if($act == 'do')		//执行设置新密码操作
		{
			$password = $this->_post('password');
			$repassword = $this->_post('confirm_password');
			$code = $this->_post('hco');
			$username = $this->_post('hid');		//url_jiami()后的(用户名),保证用户修改的是自己的账号(名id是假象)
			if(!$password || !$repassword || $password != $repassword || $username)
			{
				
			}
			#myprint(url_jiemi($code));
			if(session('verify_phone') != md5(strtoupper(url_jiemi($code))))	//再次验证code
			{
				session('verify_phone',null);
				$this->error('参数有错误，请从新获取验证码',$goback);
			}
			$username = url_jiemi($username);
			$r = $UserModel->getParamUserInfo('username = "'.$username.'"');	//再次获取用户信息
			if(!$r)		//没找到用户，说明表单数据发生了变化，用户更改了表单数据。若果是这样，说明有人有意的恶意篡改他人的账号密码
			{
				session('verify_phone',null);	//将session清空(短信)
				unset($_SESSION);				//强制清空所有的session
				$this->error('非法操作','/');		//强制跳转至首页
			}
			else		//执行更新密码操作
			{
				session('verify_phone',null);
				$newUserModel = new UserModel($r['user_id']);
				$r = $newUserModel->setPassword($password,1);		//设置新的密码
				$this->success('恭喜您，新的密码已经生效了，即将为您跳转至登录页面','/Common/user_login.html');
			}
			
		}
		else
		{
			$this->error('请求超时',$goback);
			exit;
		}
	}
	
	
	//文章列表
	public function article_list()
	{
		$this->display();
	}
	//文章详情
	public function article_display()
	{
		$this->display();
	}
	
}

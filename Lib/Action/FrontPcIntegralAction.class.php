<?php 
class FrontPcIntegralAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->news_num_per_page = C('NEWS_NUM_PER_PAGE');

	}

	//我的积分
	public function integral_list()
	{
		$user_id = intval(session('user_id'));
        
		$integral_obj = new IntegralModel(); 
        $where        = 'user_id = ' . $user_id;
        //分页处理
        import('ORG.Util.Pagelist');
        $count = $integral_obj->getIntegralNum($where);
        $Page  = new Pagelist($count,C('PER_PAGE_NUM'));

		$integral_obj->setStart($Page->firstRow);
        $integral_obj->setLimit($Page->listRows);
        $show  = $Page->show();
		$this->assign('show', $show);

		$integral_list = $integral_obj->getIntegralList('', $where, ' addtime DESC');
		$integral_list = $integral_obj->getListData($integral_list);
		$this->assign('integral_list', $integral_list);
		
		$this->assign('head_title', '我的积分');
		$this->display();
	}
	
	//积分兑换商品
	public function integral_item()
	{
        $user_id   = intval(session('user_id'));

        $item_obj  = new ItemModel();
        $fields    = 'item_id, item_name, mall_price';
        $item_list = $item_obj->getItemList($fields, 'is_gift = 1 AND isuse = 1', ' addtime DESC ');

        if ($this->_post()) {
            $items = $this->_post('item_num');
            $ids   = $this->_post('item_id');

            $item_obj   = new ItemModel();
            $cart_model = new ShoppingCartModel();
            $user_id    = intval(session('user_id'));
            $success    = 0;

            foreach ($items AS $k => $v) {
                $item_id   = $ids[$k];
                $item_info = $item_obj->getItemInfo('item_id = ' . $item_id , '');

                //将订单数量大于0的插入到购物车中;
                if ($item_info && $v > 0) {
                   $success += $cart_model->addShoppingCart($item_id, 0, 
                      array(
                        'user_id'			=> $user_id,
                        'cookie_value'		=> $GLOBALS['shopping_cart_cookie'],
                        'real_price'		=> $item_info['mall_price'],
                        'integral_num'		=> 0,
                        'total_price'		=> $v * $item_info['mall_price'],
                        'number'			=> $v,
                        'property'          => '',
                        'addtime'			=> time(),
                        'item_name'			=> $item_info['item_name'],
                        'is_integral'		=> 1,
                        'small_pic'			=> small_img($item_info['base_pic']),
                    ));
                } // end of if
            } //end of foreach
            if ($success) {
                $this->redirect('/FrontCart/integral_cart/');
            } else {
                $this->error('添加失败，请稍候再试!', '/FrontIntegral/integral_item');
            }
        } // end of post

		$this->assign('page_name', $page_name);
		$this->assign('item_list', $item_list);
		$this->assign('head_title', '积分兑换商品');
		$this->display();
	}

	//积分兑换记录
	public function integral_exchange_record()
	{
        $fields  = 'exchange_record_id,exchange_record_sn,addtime,confirm_time,total_num,pay_amount,order_status';
        $where   = ' user_id = ' . intval(session('user_id'));
        //分页处理
        import('ORG.Util.Pagelist');
        $integral_exchange_record_obj = new IntegralExchangeRecordModel();
        $count = $integral_exchange_record_obj->getOrderNum($where);
        $Page  = new Pagelist($count,C('PER_PAGE_NUM'));

		$integral_exchange_record_obj->setStart($Page->firstRow);
        $integral_exchange_record_obj->setLimit($Page->listRows);
        $show  = $Page->show();
		$this->assign('show', $show);
        $integral_exchange_list       = $integral_exchange_record_obj->getRecordList($fields, $where, ' addtime DESC ');
        $integral_exchange_list       = $integral_exchange_record_obj->getListData($integral_exchange_list);
        #var_dump($integral_exchange_list);die;

        $this->assign('integral_exchange_list', $integral_exchange_list);

		$this->assign('head_title', '积分兑换记录');
		$this->display();
	}

	//积分兑换详情
    //@author wsq
	public function integral_exchange_detail()
	{
        $exchange_record_id = $this->_get('integral_id');
        $user_id            = intval(session('user_id'));

        if (!$exchange_record_id) 
            $this->error('参数错误','/FrontIntegral/integral_exchange_list');

        $fields  = 'exchange_record_id,exchange_record_sn,addtime,confirm_time,total_num,pay_amount,order_status, user_address_id';
        $where   = ' AND user_id = ' . $user_id;

        $integral_exchange_record_obj = new IntegralExchangeRecordModel($exchange_record_id);
        $integral_info                = $integral_exchange_record_obj->getOrderInfo($fields,$where);

        if (!$integral_info) 
            $this->error('记录不存在','/FrontIntegral/integral_exchange_list');

        // 获取地址
        $user_address_id   = $integral_info['user_address_id'];
        $user_address_obj  = new UserAddressModel($user_address_id);
        $user_address_info = $user_address_obj->getUserAddressInfo($user_address_id);

        $area_obj          = new AreaModel();
        $address_str       = $area_obj->getAreaString(
            $user_address_info['province_id'],
            $user_address_info['city_id'],
            $user_address_info['area_id']
        );

        $address_str       = $address_str . $user_address_info['address'];

        // 获取email地址等个人信息
        $user_model        = new UserModel($user_id);
        $email             = $user_model->where('user_id = ' . $user_id)->getField('email');
        $this->assign('email', $email);

        // 获取商品信息
        $items_list        = $integral_exchange_record_obj->getOrderItemList();
        $this->assign('item_list', $items_list);
        #echo "<pre>";var_dump($items_list);die;

        $this->assign('address', $address_str);
        $this->assign('user_address_info', $user_address_info);
        $this->assign('integral_info', $integral_info);
		$this->assign('head_title', '积分兑换详情');
		$this->display();
	}

}

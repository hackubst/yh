<?php 
class FrontGroupBuyAction extends FrontAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_account_page = C('ITEM_NUM_PER_ACCOUNT_PAGE');
	}

	//前台拼团列表页
	public function get_group_buy_list()
	{
		$group_buy_obj = new GroupBuyModel();
		$start = intval($this->_request('start'));
		#$limit = $this->_request('limit');
		$limit = $this->item_num_per_account_page;
		$group_buy_obj->setStart($start);
		$group_buy_obj->setLimit($limit);
		$group_buy_list = $group_buy_obj->getGroupBuyList('', 'isuse = 1 AND start_time <= ' . time() . ' AND end_time > ' . time(), true);
		$this->assign('start', $start);
		$this->assign('page_num', $limit);
		$this->assign('list', $group_buy_list);
		$user_id = intval(session('user_id'));
		$this->assign('head_title', '拼团');
		$this->display();
	}

	//前台拼团详情页
	public function group_buy_item_detail()
	{
		$redirect = U('/FrontGroupBuy/get_group_buy_list');
		$group_buy_id = $this->_request('group_buy_id');
		$group_buy_obj = new GroupBuyModel();
		$group_buy_info = $group_buy_obj->getGroupBuyInfo('isuse = 1 AND start_time <= ' . time() . ' AND end_time > ' . time(), '');
		if (!$group_buy_info)
		{
			$this->alert('对不起，活动不存在', $redirect);
		}
		//时间转换
		$group_buy_info['end_date'] = date('Y-m-d H:i:s', $group_buy_info['end_time']);
		$this->assign('group_buy_info', $group_buy_info);

		$user_id = cur_user_id();
		//获取商品信息
		$item_obj = new ItemModel();
		$item_info = $item_obj->getItemInfo('item_id = ' . $group_buy_info['item_id'], 'mall_price');
		//获取商品详情描述
		$item_txt_obj = new ItemTxtModel();
		$item_txt = html_entity_decode(stripslashes($item_txt_obj->getItemTxt($group_buy_info['item_id'])));		//商品详情
		$item_txt = preg_replace("/\\\&quot;/", "", $item_txt);
		$item_info['detail'] = $item_txt;
		$this->assign('item_info', $item_info);

		$this->assign('head_title', $group_buy_info['group_name']);
		$this->display();
	}

	//前台拼团详情页
	public function go_add_group()
	{
		$group_buy_id = $this->_request('group_buy_id');
		$group_buy_obj = new GroupBuyModel();
		$group_buy_info = $group_buy_obj->getGroupBuyInfo('isuse = 1 AND group_buy_id = ' . $group_buy_id, '');
		//不加购物车，直接跳转到提交订单页，在订单提交页区分，并以group_id生成商品列表，其余流程一致。
		$url = '/FrontOrder/order_submit/group_buy_id/' . $group_buy_id;
		redirect($url);
		$user_id = intval(session('user_id'));
		$this->assign('head_title', $group_buy_info['group_name']);
		$this->display();
	}
}

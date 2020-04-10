<?php 
class FrontShareAction extends FrontAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->item_num_per_freight_coupon_page = C('ITEM_NUM_PER_ACCOUNT_PAGE');
	}

	function freight_coupon_share()
	{
		//获取用户信息，判断是否微信用户
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('openid');
		if (!$user_info || !$user_info['openid'])
		{
			//跳转到关注页
			$url = 'http://mp.weixin.qq.com/s?__biz=MzA5MjI3MzY2NQ==&mid=206239890&idx=1&sn=c2afff8a26d00c8ad3457c5cf4ad0ce9#rd';
			$this->alert('对不起，请先关注', $url);
		}

		$freight_activity_id = intval($this->_get('freight_activity_id'));
		$freight_activity_obj = new FreightActivityModel($freight_activity_id);
		$freight_activity_info = $freight_activity_obj->getFreightActivityInfoAll('freight_activity_id = ' . $freight_activity_id);

		if (!$freight_activity_info)
		{
			$this->alert('抱歉，活动不存在', U('/'));
		}
		$freight_activity_info['activity_small_pic'] = 'http://' . $_SERVER['HTTP_HOST'] . $freight_activity_info['activity_small_pic'];
		$freight_activity_info['adv_link'] = $freight_activity_info['link'];
		$freight_activity_info['link'] = 'http://' . $_SERVER['HTTP_HOST'] . '/FrontShare/freight_coupon_share/freight_activity_id/' . $freight_activity_info['freight_activity_id'];
		$this->assign('freight_activity_info', $freight_activity_info);

		//1活动是否过期，1.1已过期，显示过期界面，state=1，1.2进行中，显示分享者头像，分享者自我介绍，领取者信息列表，判断当前用户是否已抢红包，1.2.1已抢过，中间处显示抢单的红包数量和分享给好友等信息，state=2，1.2.2未抢过，判断红包是否抢完，1.2.2.1红包已抢完，中间处显示红包已抢完，state=3，1.2.2红包未抢完给当前用户生成一张镖金优惠券并显示在中间处，state=2
		$state = 0;

		//获取当前用户优惠券信息
		$where = 'freight_activity_id = ' . $freight_activity_id;
		$freight_coupon_obj = new FreightCouponModel();
		$freight_coupon_id = $freight_coupon_obj->getCoupon($freight_activity_id);

		#var_dump($freight_activity_info);
		#echo time();
		if ($freight_coupon_id == -2)
		{
			//已过期
			$state = 1;
		}
		else
		{
			if ($freight_coupon_id == -1 || $freight_coupon_id > 0)
			{
				//已抢红包
				$freight_coupon_info = $freight_coupon_obj->getFreightCouponInfo($where . ' AND user_id = ' . $user_id, 'num');
				$this->assign('freight_coupon_info', $freight_coupon_info);
				#echo $freight_coupon_obj->getLastSql();
				#echo "<pre>";
				#print_r($freight_coupon_info);
				$state = 2;
			}
			elseif ($freight_coupon_id == 0)
			{
				//已被抢完
				$state = 3;
			}

			//获取分享者信息：头像，昵称
			$user_obj = new UserModel($freight_activity_info['user_id']);
			$user_info = $user_obj->getUserInfo('headimgurl, nickname');
			$this->assign('user_info', $user_info);

			//好友领取列表
			$freight_coupon_list = $freight_coupon_obj->getFreightCouponList('user_id, num', $where);
			$freight_coupon_list = $freight_coupon_obj->getFreightCouponShareList($freight_coupon_list);
			$this->assign('freight_coupon_list', $freight_coupon_list);
			#echo $user_obj->getLastSql();
			#echo "<pre>";
			#print_r($user_info);
			#print_r($freight_coupon_info);
			#print_r($freight_coupon_list);
		}
log_file('freight_activity_info = ' . json_encode($freight_activity_info), 'coupon');

		$this->assign('state', $state);
		#var_dump($state);
		#die;
		$this->assign('head_title', $freight_activity_info['activity_name']);
		$this->display();
	}
	
	//镖金优惠券列表
	public function get_freight_coupon_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$freight_coupon_obj = new FreightCouponModel();
		//总数
		$total = $freight_coupon_obj->getFreightCouponNum($where);
		$freight_coupon_obj->setStart(0);
        $freight_coupon_obj->setLimit($this->item_num_per_freight_coupon_page);
		$freight_coupon_list = $freight_coupon_obj->getFreightCouponList('', $where, 'addtime DESC');
		$freight_coupon_list = $freight_coupon_obj->getListData($freight_coupon_list);
		#echo "<pre>";
		#echo $freight_coupon_obj->getLastSql();
		#print_r($freight_coupon_list);
		#die;
   		
		$this->assign('freight_coupon_list', $freight_coupon_list);
		$this->assign('total', $total);
		$this->assign('firstRow', $this->item_num_per_freight_coupon_page);

		$this->assign('head_title', '我的镖金优惠券');
		$this->display();
	}
	
	//异步获取镖金优惠券列表
	public function freight_coupon_list()
	{
		$user_id = intval(session('user_id'));
		$firstRow = I('post.firstRow');
		$opt = I('post.opt');
		$where = 'user_id = ' . $user_id;
		$freight_coupon_obj = new FreightCouponModel();
		//订单总数
		$total = $freight_coupon_obj->getFreightCouponNum($where);

		if ($firstRow < ($total - 1) && $user_id)
		{
			$freight_coupon_obj->setStart($firstRow);
			$freight_coupon_obj->setLimit($this->item_num_per_freight_coupon_page);
			$freight_coupon_list = $freight_coupon_obj->getFreightCouponList('', $where, 'addtime DESC');
			$freight_coupon_list = $freight_coupon_obj->getListData($freight_coupon_list);

			foreach ($freight_coupon_list AS $k => $v)
			{
				$freight_coupon_list[$k]['deadline'] = date('Y-m-d H:i:s', $v['deadline']);
			}

			echo json_encode($freight_coupon_list);
			exit;
		}

		exit('failure');
	}

}

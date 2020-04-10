<?php
class FrontCouponAction extends FrontAction
{
    public function _initialize()
    {
        $this->item_num_per_account_page = C('ITEM_NUM_PER_ACCOUNT_PAGE');
    }

    //领取页面
    public function get_coupon()
    {
        $user_id = intval(session('user_id'));
        if (IS_POST) {
            $vouchers_id = intval(I('post.id'));
            $card_code   = intval(I('post.card'));

            $vouchers_obj  = new VouchersModel;
            $vouchers_info = $vouchers_obj->getVouchersInfo('vouchers_id = ' . $vouchers_id, 'vouchers_id,num,amount_limit,start_time,end_time,genre_id,title');
            if (!$vouchers_info) {
                exit('1'); // 不存在优惠券
            }

            $member_card_obj  = new MemberCardModel;
            $member_card_info = $member_card_obj->where('user_id = ' . $user_id . ' AND card_code = ' . $card_code)->find();
            if (!$member_card_info) {
                exit('2'); // 不存在会员卡
            }

            $user_vouchers_obj  = new UserVouchersModel;
            $user_vouchers_info = $user_vouchers_obj->getUserVouchersInfo('user_id = ' . $user_id . ' AND vouchers_id = ' . $vouchers_id, '');
            if ($user_vouchers_info) {
                exit('have');
            }

            $vouchers_info['member_card_id'] = $member_card_info['member_card_id'];
            $vouchers_info['user_id']        = $user_id;

            $success = $user_vouchers_obj->addUserVouchers($vouchers_info);

            if ($success) {
                exit('success');
            } else {
                exit('failure');
            }
        } else {

            $member_card_obj = new MemberCardModel;
            $card_list       = $member_card_obj->getPayCardList(session('user_id'));
            $this->assign('card_list', $card_list);
            // dump($card_list);exit;

            $coupon_set_obj = new CouponSetModel;

            $where           = 'type_num = 3 AND start_time < ' . NOW_TIME . ' AND end_time > ' . NOW_TIME;
            $coupon_set_info = $coupon_set_obj->getCouponSetInfo($where, 'vouchers_ids');

            $vouchers_obj  = new VouchersModel;
            $vouchers_list = $vouchers_obj->getVouchersList('', 'vouchers_id IN (' . $coupon_set_info['vouchers_ids'] . ')');
            foreach ($vouchers_list as $k => $v) {
                $vouchers_list[$k]['use_limit_desc'] = CouponSetModel::getUseLimitDesc($v['amount_limit'], 0, 0, $v['genre_id']);
            }
            $this->assign('vouchers', $vouchers_list);
            // dump($vouchers_list);exit;

            $this->assign('head_title', '领取');
            $this->display();
        }
    }

	//活动专区首页
	function coupon_index()
	{
		$this->assign('head_title', '活动专区');
		$this->display();
	}
	
	//特价促销商品列表
	public function item_coupon_list()
	{
		$user_id = intval(session('user_id'));
		
		//初始化对象
		$special_offer_obj = new SpecialOfferModel();
		$where = $special_offer_obj->getListWhere();
		$where .= ' AND is_adv_display = 2';

		//总数
		$total = $special_offer_obj->getSpecialOfferNum($where);

		//分页处理
		$special_offer_obj->setStart(0);
		$special_offer_obj->setLimit($this->item_num_per_freight_coupon_page);

		//字段
		$fields = 'special_offer_id, merchant_id, item_id, start_time, end_time, old_price, promotion_price, use_time, title';

		$special_offer_list = $special_offer_obj->getSpecialOfferList($fields, $where, 'serial ASC,addtime DESC');
		$special_offer_list = $special_offer_obj->getFrontListData($special_offer_list);

		#echo "<pre>";
		#echo $special_offer_obj->getLastSql();
		#print_r($special_offer_list);
		#die;
   	
		$this->assign('special_offer_list', $special_offer_list);
		$this->assign('total', $total);
		$this->assign('firstRow', 0);
		$this->assign('per_page_num', $this->item_num_per_freight_coupon_page);

		$this->assign('head_title', '特价促销商品');
		$this->display();
	}

	//异步获取特价促销商品
	public function ajax_item_coupon_list()
	{
		$user_id = intval(session('user_id'));
		//初始化对象
		$special_offer_obj = new SpecialOfferModel();
		$where = $special_offer_obj->getListWhere();
		$where .= ' AND is_adv_display = 2';

		$firstRow = I('post.firstRow');
		//总数
		$total = $special_offer_obj->getSpecialOfferNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			//分页处理
			$special_offer_obj->setStart($firstRow);
			$special_offer_obj->setLimit($this->item_num_per_freight_coupon_page);

			//字段
			$fields = 'special_offer_id, merchant_id, item_id, start_time, end_time, old_price, promotion_price, use_time, title';
			$special_offer_list = $special_offer_obj->getSpecialOfferList($fields, $where, 'serial ASC,addtime DESC');
			$special_offer_list = $special_offer_obj->getFrontListData($special_offer_list);

			log_file('special_offer_list = ' . json_encode($special_offer_list), 'ajax_item_coupon_list');
			echo json_encode($special_offer_list);
			exit;
		}

		exit('failure');
	}

	//满减活动列表
	public function full_minus_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$freight_coupon_obj = new FreightCouponModel();
		$this->assign('head_title', '满减活动');
		$this->display();
	}

	//买赠活动列表
	public function buy_gifts_list()
	{
		$user_id = intval(session('user_id'));
		$where = 'user_id = ' . $user_id;
		$freight_coupon_obj = new FreightCouponModel();
		$this->assign('head_title', '买赠活动');
		$this->display();
	}
}

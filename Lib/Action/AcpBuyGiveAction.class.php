<?php
/**
 * 买赠活动管理控制器
 *
 */
class AcpBuyGiveAction extends AcpAction
{
    function _initialize()
    {
        parent::_initialize();

        // 引入商品公共函数库
        require_cache('Common/func_item.php');
	}

	private function get_search_condition()
	{
		//初始化SQL查询的where子句
		$where = '';

		//买赠活动状态
		$isuse = $this->_request('isuse');
		if (is_numeric($isuse) && $isuse)
		{
			$where .= ' AND isuse = ' . $isuse;
		}

		/*买赠活动时间begin*/
		//起始时间
		$start_time = $this->_request('start_time');
		$start_time = str_replace('+', ' ', $start_time);
		$start_time = strtotime($start_time);
		#echo $start_time;
		if ($start_time)
		{
			$where .= ' AND addtime >= ' . $start_time;
		}

		//结束时间
		$end_time = $this->_request('end_time');
		$end_time = str_replace('+', ' ', $end_time);
		$end_time = strtotime($end_time);
		if ($end_time)
		{
			$where .= ' AND addtime <= ' . $end_time;
		}
		/*买赠活动时间end*/

		#echo $where;
		//重新赋值到表单
		$this->assign('isuse', $isuse);
		$this->assign('start_time', $start_time ? $start_time : '');
		$this->assign('end_time', $end_time ? $end_time : '');

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $start_time ? '/start_time/' . $start_time : '';
		$redirect .= $end_time ? '/end_time/' . $end_time : '';
		$redirect .= $isuse ? '/isuse/' . $isuse : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

	/**
	 * 获取买赠活动列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取买赠活动列表，公共方法
     */
	function buy_give_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();
		$buy_give_obj = new BuyGiveModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $buy_give_obj->getBuyGiveNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$buy_give_obj->setStart($Page->firstRow);
        $buy_give_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$buy_give_list = $buy_give_obj->getBuyGiveList('', $where, ' addtime DESC');
		$buy_give_list = $buy_give_obj->getListData($buy_give_list);
		$this->assign('buy_give_list', $buy_give_list);
		#echo "<pre>";
		#print_r($buy_give_list);
		#echo "</pre>";
		#echo $buy_give_obj->getLastSql();
		#die;

		//买赠活动类型列表
		$this->assign('num_list', FreightActivityModel::getActivityTypeList());

		//状态列表
		$this->assign('isuse_list', BuyGiveModel::getIsuseList());

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);

		$this->display(APP_PATH . 'Tpl/AcpBuyGive/get_buy_give_list.html');
	}

	public function get_system_buy_give_list()
	{
		$this->buy_give_list('merchant_id = 0', '系统买赠活动列表', 'system');
	}

	public function get_merchant_buy_give_list()
	{
		$this->buy_give_list('merchant_id > 0', '商家买赠活动列表', 'merchant');
	}

	public function get_all_buy_give_list()
	{
		$this->buy_give_list('1', '所有买赠活动列表', 'all');
	}

	/**
     * 添加买赠活动，订单红包
     * @author 姜伟
     * @return void
     * @todo 可选择默认/订单红包
     */
	public function add_buy_give()
	{
		$submit = $this->_post('submit');
		if ($submit == 'submit')				//执行添加操作
		{
            // dump(I('post.'));exit;
			$title   = $this->_post('title');
            $genre_id   = intval($this->_post('genre_id'));
			$gift_id   = intval($this->_post('gift_id'));
			$vouchers_id 	 = intval($this->_post('vouchers_id'));
			$give_num   = $this->_post('give_num');
			$start_time = $this->_request('start_time');
			$start_time = str_replace('+', ' ', $start_time);
			$start_time = strtotime($start_time);
			$end_time = $this->_request('end_time');
			$end_time = str_replace('+', ' ', $end_time);
			$end_time = strtotime($end_time);
			$amount_limit 	 = $this->_post('amount_limit');
			// $use_time 	 = $this->_post('use_time');
			$isuse = $this->_post('isuse');

			if(!$title)
			{
				$this->error('对不起，请填写名称');
			}

			if(!$gift_id && !$vouchers_id)
			{
				$this->error('对不起，礼品和优惠券请至少选择一项');
			}

			if($vouchers_id && !$give_num)
			{
				$this->error('对不起，请填写赠送优惠券张数');
			}

			if($vouchers_id && !is_numeric($give_num))
			{
				$this->error('对不起，赠送优惠券张数必须为大于0的整数，请重新填写');
			}

			if(!$start_time || !$end_time)
			{
				$this->error('对不起，请填写活动有效期');
			}

			if(!$amount_limit)
			{
				$this->error('对不起，请填写结算满多少元可使用');
			}

			if(!is_numeric($amount_limit))
			{
				$this->error('对不起，结算满多少元可使用必须为大于0的整数，请重新填写');
			}

			// if(!$use_time)
			// {
			// 	$this->error('对不起，请填写参与次数限制');
			// }

			// if(!is_numeric($use_time))
			// {
			// 	$this->error('对不起，参与次数限制必须为大于0的整数，请重新填写');
			// }

			$data = array(
					'title'        =>   $title,
                    'genre_id'     =>   $genre_id,
                    'gift_id'      =>	$gift_id,
                    'vouchers_id'  =>	$vouchers_id,
                    'give_num'     =>	$give_num,
                    'start_time'   =>	$start_time,
                    'end_time'     =>	$end_time,
                    'amount_limit' =>	$amount_limit,
                    // 'use_time'  =>	$use_time,
                    'isuse'        =>	$isuse,
			);

			$success = false;
			$buy_give_obj = new BuyGiveModel();
			$success = $buy_give_obj->addBuyGive($data);

			if ($success)
			{
				$this->success('恭喜您，买赠活动添加成功','/AcpBuyGive/get_all_buy_give_list');
			}
			else
			{
				$this->success('抱歉，添加失败');
			}
		}

		//获取礼品列表
		$gift_obj = new GiftModel();
		$gift_list = $gift_obj->getGiftList('', 'merchant_id = 0 AND isuse = 1');
		$this->assign('gift_list', $gift_list);

		//获取优惠券列表
        $vouchers_obj = new VouchersModel;
        $vouchers_list = $vouchers_obj->getVouchersList('vouchers_id,num,amount_limit,title','isuse = 1');
        $this->assign('vouchers_list', $vouchers_list);
        // dump($vouchers_list);exit;

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

		$this->assign('head_title','添加买赠活动');
		$this->display();
	}

	/**
     * 修改买赠活动
     * @author 姜伟
     * @return void
     * @todo 修改买赠活动数据,users表。先判断账号重名
     */
	public function edit_buy_give()
	{
		$redirect = $this->_get('redirect');
		if($redirect)
		{
			$goback = url_jiemi($redirect);
		}

		$buy_give_id = intval($this->_get('buy_give_id'));
		$buy_give_obj = new BuyGiveModel();
		$buy_give_info = $buy_give_obj->getBuyGiveInfo('buy_give_id = ' . $buy_give_id, '');

		if (!$buy_give_info)
		{
			$this->error('抱歉，买赠活动不存在', U('/AcpBuyGive/get_buy_give_list'));
		}

		$submit = $this->_post('submit');
		if($submit == 'submit')				//执行添加操作
		{
			$title   = $this->_post('title');
            $genre_id   = $this->_post('genre_id');
			$gift_id   = $this->_post('gift_id');
			$vouchers_id 	 = $this->_post('vouchers_id');
			$give_num   = $this->_post('give_num');
			$start_time = $this->_request('start_time');
			$start_time = str_replace('+', ' ', $start_time);
			$start_time = strtotime($start_time);
			$end_time = $this->_request('end_time');
			$end_time = str_replace('+', ' ', $end_time);
			$end_time = strtotime($end_time);
			$amount_limit 	 = $this->_post('amount_limit');
			// $use_time 	 = $this->_post('use_time');
			$isuse = $this->_post('isuse');

            if(!$title)
            {
                $this->error('对不起，请填写名称');
            }

			if(!$gift_id && !$vouchers_id)
			{
				$this->error('对不起，礼品和优惠券请至少选择一项');
			}

			if($vouchers_id && !$give_num)
			{
				$this->error('对不起，请填写赠送优惠券张数');
			}

			if($vouchers_id && !is_numeric($give_num))
			{
				$this->error('对不起，赠送优惠券张数必须为大于0的整数，请重新填写');
			}

			if(!$start_time || !$end_time)
			{
				$this->error('对不起，请填写活动有效期');
			}

			if(!$amount_limit)
			{
				$this->error('对不起，请填写结算满多少元可使用');
			}

			if(!is_numeric($amount_limit))
			{
				$this->error('对不起，结算满多少元可使用必须为大于0的整数，请重新填写');
			}

			// if(!$use_time)
			// {
			// 	$this->error('对不起，请填写参与次数限制');
			// }

			// if(!is_numeric($use_time))
			// {
			// 	$this->error('对不起，参与次数限制必须为大于0的整数，请重新填写');
			// }

			$data = array(
					'title'        =>  $title,
                    'genre_id'     =>  $genre_id,
                    'gift_id'      =>	$gift_id,
                    'vouchers_id'  =>	$vouchers_id,
                    'give_num'     =>	$give_num,
                    'start_time'   =>	$start_time,
                    'end_time'     =>	$end_time,
                    'amount_limit' =>	$amount_limit,
                    // 'use_time'  =>	$use_time,
                    'isuse'        =>	$isuse,
			);

			$success = false;
			$buy_give_obj = new BuyGiveModel($buy_give_id);
			$success = $buy_give_obj->editBuyGive($data);
			if ($success)
			{
				$this->success('恭喜您，买赠活动修改成功');
			}
			else
			{
				$this->success('抱歉，修改失败');
			}
		}

		foreach ($buy_give_info AS $k => $v)
		{
			$this->assign($k, $v);
		}
        $this->assign('category_id', $buy_give_info['genre_id']);

		//获取礼品列表
		$gift_obj = new GiftModel();
		$gift_list = $gift_obj->getGiftList('', 'merchant_id = 0 AND isuse = 1');
		$this->assign('gift_list', $gift_list);

		//获取优惠券列表
        $vouchers_obj = new VouchersModel;
        $vouchers_list = $vouchers_obj->getVouchersList('vouchers_id,num,amount_limit, title','isuse = 1');
        $this->assign('vouchers_list', $vouchers_list);
        // dump($vouchers_list);exit;

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

		$this->assign('head_title','编辑买赠活动信息');
		$this->display(APP_PATH . '/Tpl/AcpBuyGive/add_buy_give.html');
	}

	private function get_user_buy_give_search_condition()
	{
		//初始化SQL查询的where子句
		$where = '';

		//买赠活动ID
		$buy_give_id = $this->_request('buy_give_id');
		if (is_numeric($buy_give_id) && $buy_give_id)
		{
			$where .= ' AND buy_give_id = ' . $buy_give_id;
		}

		/*买赠活动时间begin*/
		//起始时间
		$start_time = $this->_request('start_time');
		$start_time = str_replace('+', ' ', $start_time);
		$start_time = strtotime($start_time);
		#echo $start_time;
		if ($start_time)
		{
			$where .= ' AND addtime >= ' . $start_time;
		}

		//结束时间
		$end_time = $this->_request('end_time');
		$end_time = str_replace('+', ' ', $end_time);
		$end_time = strtotime($end_time);
		if ($end_time)
		{
			$where .= ' AND addtime <= ' . $end_time;
		}
		/*买赠活动时间end*/

		#echo $where;
		//重新赋值到表单
		$this->assign('buy_give_id', $buy_give_id);
		$this->assign('start_time', $start_time ? $start_time : '');
		$this->assign('end_time', $end_time ? $end_time : '');

		/*重定向页面地址begin*/
		$redirect = $_SERVER['PATH_INFO'];
		$redirect .= $start_time ? '/start_time/' . $start_time : '';
		$redirect .= $end_time ? '/end_time/' . $end_time : '';
		$redirect .= $buy_give_id ? '/buy_give_id/' . $buy_give_id : '';

		$this->assign('redirect', url_jiami($redirect));
		/*重定向页面地址end*/

		return $where;
	}

	/**
	 * 获取买赠活动列表，公共方法
     * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
     * @todo 获取买赠活动列表，公共方法
     */
	function user_buy_give_list($where, $head_title, $opt)
	{
		$where .= $this->get_user_buy_give_search_condition();
		$user_buy_give_obj = new UserBuyGiveModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $user_buy_give_obj->getUserBuyGiveNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$user_buy_give_obj->setStart($Page->firstRow);
        $user_buy_give_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);

		$user_buy_give_list = $user_buy_give_obj->getUserBuyGiveList('', $where, ' addtime DESC');
		$user_buy_give_list = $user_buy_give_obj->getListData($user_buy_give_list);
		$this->assign('user_buy_give_list', $user_buy_give_list);
		#echo "<pre>";
		#print_r($user_buy_give_list);
		#echo "</pre>";
		#echo $user_buy_give_obj->getLastSql();
		#die;

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);

		$this->display(APP_PATH . 'Tpl/AcpBuyGive/get_buy_give_detail.html');
	}

	public function get_buy_give_detail()
	{
		$this->user_buy_give_list('1', '买赠活动明细', 'all');
	}

}
?>

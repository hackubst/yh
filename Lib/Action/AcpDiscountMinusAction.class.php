<?php
/**
 * acp后台商品类
 */
class AcpDiscountMinusAction extends AcpAction {

    /**
     * 初始化
     * @author 姜伟
     * @return void
     * @todo 初始化方法
     */
	function _initialize()
	{
        parent::_initialize();

        // 引入商品公共函数库
        require_cache('Common/func_item.php');
    }

	/**
	 * 接收搜索表单数据，组织返回where子句
	 * @author wsq
	 * @param void
	 * @return void
	 * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
	 */
	function get_search_condition()
	{
        $where = "";
		//添加时间范围起始时间
		$start_date = $this->_request('start_date');
		$start_date = str_replace('+', ' ', $start_date);
		$start_date = strtotime($start_date);
        $this->assign('start_date', $start_date);
		if ($start_date) {
			$where .= ' AND addtime >= ' . $start_date;
            $this->assign('start_date', $start_date);
		}

		$end_date = $this->_request('end_date');
		$end_date = str_replace('+', ' ', $end_date);
		$end_date = strtotime($end_date);
		if ($end_date) {
			$where .= ' AND addtime <= ' . $end_date;
            $this->assign('end_date', $end_date);
		}

        $shop_name = $this->_request('shop_name');
        if ($shop_name) {
            $merchant_list = D('Merchant')->where('shop_name LIKE "%' . $shop_name . '%"')
                ->field('merchant_id')
                ->select();

            $user_ids  = array();
            if ($merchant_list) {
                foreach ($merchant_list AS $k => $v) array_push($user_ids, $v['merchant_id']);
                $user_ids  = implode(',' , $user_ids);
                $where    .= ' AND merchant_id in ( '. $user_ids . ' )';
            } else {
                $where    .= ' AND false';
            }
            $this->assign('shop_name', $shop_name);
        }

        $user_name = $this->_request('user_name');
        if ($user_name) {
            $user_list  = D('User')->where('nickname LIKE "%' . $user_name . '%"')
                ->field('user_id')
                ->select();

            $user_list2 = D('User')->where('realname LIKE "%' . $user_name . '%"')
                ->field('user_id')
                ->select();

            if (!$user_list && !$user_list2) {
                $where    .= ' AND false';

            } else {
                $user_ids  = array();
                if ($user_list) {
                    foreach ($user_list AS $k => $v) array_push($user_ids, $v['user_id']);
                }

                if ($user_list2) {
                    foreach ($user_list2 AS $k => $v) array_push($user_ids, $v['user_id']);
                }

                $user_ids  = implode(',' , $user_ids);
                $where    .= ' AND user_id in ( '. $user_ids . ' )';
            }

            $this->assign('user_name', $user_name);
        }

        $discount_id = intval($this->_request('discount_id'));
        if ($discount_id)
            $where .= ' AND discount_minus_id = ' . $discount_id;


        return $where;
	}

    //获取满减信息
    //@author wsq
    public function get_discount_minus_list()
    {
        $where  = "1" . $this->get_search_condition();
        $discount_obj = D('DiscountMinus');
        $count        = $discount_obj->getDiscountMinusNum($where);
        $sort         = $this->get_and_set_sort_info(array(
            'amount_limit', 'num', 'use_time',
        ));

        $sort         = $sort ? $sort : ' addtime DESC';

        import('ORG.Util.Pagelist');
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$discount_obj->setStart($Page->firstRow);
        $discount_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$discount_list = $discount_obj->getDiscountMinusList('', $where, $sort);
		$discount_list = $discount_obj->getListData($discount_list);

		$this->assign('discount_list', $discount_list);

		$this->assign('opt', $opt);
		$this->assign('head_title','满减活动列表');
		$this->display();
    }

    //获取满减明细
    //@author wsq
    public function get_discount_minus_detail()
    {
        $where  = "1" . $this->get_search_condition();
        $discount_obj = D('UserDiscountMinus');
        $count        = $discount_obj->getUserDiscountMinusNum($where);
        $sort         = $this->get_and_set_sort_info(array(
            'order_amount', 'num',
        ));

        $sort         = $sort ? $sort : ' addtime DESC';

        import('ORG.Util.Pagelist');
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$discount_obj->setStart($Page->firstRow);
        $discount_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$discount_list = $discount_obj->getUserDiscountMinusList('', $where, $sort);
		$discount_list = $discount_obj->getListData($discount_list);

		$this->assign('discount_list', $discount_list);

		$this->assign('opt', $opt);
		$this->assign('head_title', "获取满减活动明细");
		$this->display();
    }

    /**
     * 添加满减活动，订单红包
     * @author 姜伟
     * @return void
     * @todo 可选择默认/订单红包
     */
    public function add_discount_minus()
    {
        $submit = $this->_post('submit');
        if($submit == 'submit')             //执行添加操作
        {
            // dump(I('post.'));exit;
            $title     = $this->_post('title');
            $genre_id     = intval($this->_post('genre_id'));
            $start_time   = $this->_request('start_time');
            $start_time   = str_replace('+', ' ', $start_time);
            $start_time   = strtotime($start_time);
            $end_time     = $this->_request('end_time');
            $end_time     = str_replace('+', ' ', $end_time);
            $end_time     = strtotime($end_time);
            $num          = $this->_post('num');
            $amount_limit = $this->_post('amount_limit');
            $isuse        = $this->_post('isuse');

            if(!$title)
            {
                $this->error('对不起，请填写满减名称');
            }

            if(!$start_time || !$end_time)
            {
                $this->error('对不起，请填写活动有效期');
            }

            if(!$num)
            {
                $this->error('对不起，请填写满减面额');
            }

            if(!is_numeric($num))
            {
                $this->error('对不起，满减面额必须为大于0的整数，请重新填写');
            }

            if(!$amount_limit)
            {
                $this->error('对不起，请填写结算满多少元可使用');
            }

            if(!is_numeric($amount_limit))
            {
                $this->error('对不起，结算满多少元可使用必须为大于0的整数，请重新填写');
            }

            $data = array(
                    'title'        =>  $title,
                    'genre_id'     =>  $genre_id,
                    'start_time'   =>   $start_time,
                    'end_time'     =>   $end_time,
                    'amount_limit' =>   $amount_limit,
                    'num'          =>   $num,
                    'isuse'        =>   $isuse,
            );

            $success = false;
            $discount_minus_obj = new DiscountMinusModel();
            $success = $discount_minus_obj->addDiscountMinus($data);

            if ($success)
            {
                $this->success('恭喜您，满减活动添加成功','/AcpDiscountMinus/get_discount_minus_list');
            }
            else
            {
                $this->success('抱歉，添加失败');
            }
        }

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
		#dump($this->arr_category);
        $this->assign('arr_category', $this->arr_category);

        $this->assign('head_title','添加满减活动');
        $this->display();
    }

    /**
     * 修改满减活动
     * @author 姜伟
     * @return void
     * @todo 修改满减活动数据,users表。先判断账号重名
     */
    public function edit_discount_minus()
    {
        $redirect = $this->_get('redirect');
        if($redirect)
        {
            $goback = url_jiemi($redirect);
        }

        $discount_minus_id = intval($this->_get('discount_minus_id'));
        $discount_minus_obj = new DiscountMinusModel;
        $discount_minus_info = $discount_minus_obj->getDiscountMinusInfo('discount_minus_id = ' . $discount_minus_id, '');

        if (!$discount_minus_info)
        {
            $this->error('抱歉，满减活动不存在', U('/AcpDiscountMinus/get_discount_minus_list'));
        }

        $submit = $this->_post('submit');
        if($submit == 'submit')             //执行添加操作
        {
            $title        = $this->_post('title');
            $genre_id     = $this->_post('genre_id');
            $start_time   = $this->_request('start_time');
            $start_time   = str_replace('+', ' ', $start_time);
            $start_time   = strtotime($start_time);
            $end_time     = $this->_request('end_time');
            $end_time     = str_replace('+', ' ', $end_time);
            $end_time     = strtotime($end_time);
            $num          = $this->_post('num');
            $amount_limit = $this->_post('amount_limit');
            $isuse        = $this->_post('isuse');

            if(!$title)
            {
                $this->error('对不起，请填写活动名称');
            }

            if(!$start_time || !$end_time)
            {
                $this->error('对不起，请填写活动有效期');
            }

            if(!$num)
            {
                $this->error('对不起，请填写满减面额');
            }

            if(!is_numeric($num))
            {
                $this->error('对不起，满减面额必须为大于0的整数，请重新填写');
            }

            if(!$amount_limit)
            {
                $this->error('对不起，请填写结算满多少元可使用');
            }

            if(!is_numeric($amount_limit))
            {
                $this->error('对不起，结算满多少元可使用必须为大于0的整数，请重新填写');
            }

            $data = array(
                    'title'        =>  $title,
                    'genre_id'     =>  $genre_id,
                    'start_time'   =>   $start_time,
                    'end_time'     =>   $end_time,
                    'amount_limit' =>   $amount_limit,
                    'num'          =>   $num,
                    'isuse'        =>   $isuse,
            );

            $success = false;
            $discount_minus_obj = new DiscountMinusModel($discount_minus_id);
            $success = $discount_minus_obj->editDiscountMinus($data);
            if ($success)
            {
                $this->success('恭喜您，满减活动修改成功');
            }
            else
            {
                $this->error('抱歉，修改失败');
            }
        }

        foreach ($discount_minus_info AS $k => $v)
        {
            $this->assign($k, $v);
        }
        $this->assign('category_id', $discount_minus_info['genre_id']);

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

        $this->assign('head_title','编辑满减活动信息');
        $this->display(APP_PATH . '/Tpl/AcpDiscountMinus/add_discount_minus.html');
    }
}
?>

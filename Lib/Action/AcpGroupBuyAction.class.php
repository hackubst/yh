<?php
/**
 * acp后台团购类
 */
class AcpGroupBuyAction extends AcpAction {

    // 团购模型对象
    protected $GroupBuy;

    /**
     * 初始化
     * @author wzg
     * @return void
     * @todo 初始化方法
     */
	function _initialize()
	{
        parent::_initialize();

        // 实例化团购模型类
        $this->GroupBuy = D('GroupBuy');
    }

	/**
	 * 接收搜索表单数据，组织返回where子句
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
	 */
	function get_search_condition()
	{
		//初始化查询条件
		$where = '';

		//团购名称
		$group_buy_name = $this->_request('group_buy_name');
		if ($group_buy_name)
		{
			$where .= ' AND group_buy_name LIKE "%' . $group_buy_name . '%"';
		}

		//团购货号
		$group_buy_sn = $this->_request('group_buy_sn');
		if ($group_buy_sn)
		{
			$where .= ' AND group_buy_sn = "' . $group_buy_sn . '"';
		}

		//分类
		$category_id = $this->_request('category_id');
		if ($category_id)
		{
            $arr_category = explode('.', $category_id);
			if ($arr_category[0] == 1)
			{
				$where .= ' AND class_id = ' . $arr_category[1];
			}
			elseif ($arr_category[0] == 2)
			{
				$where .= ' AND sort_id = ' . $arr_category[1];
			}
		}

        // 团购状态
		$group_buy_status = $this->_request('group_buy_status');
        if ($group_buy_status) {
            if ($group_buy_status == 'onsale') {
                $condition['_string'] = 'stock > 0 AND stock > stock_alarm';
				$where .= ' AND stock > 0 AND stock > stock_alarm';
			}
			elseif ($group_buy_status == 'alarm')
			{
				$where .= ' AND stock <= stock_alarm';
			}
			elseif ($group_buy_status == 'outstock')
			{
				$where .= ' AND stock < 1';
            }
        }

		//添加时间范围起始时间
		$start_date = $this->_request('start_date');
		$start_date = str_replace('+', ' ', $start_date);
		$start_date = strtotime($start_date);
		if ($start_date)
		{
			$where .= ' AND addtime >= ' . $start_date;
		}

		//添加时间范围结束时间
		$end_date = $this->_request('end_date');
		$end_date = str_replace('+', ' ', $end_date);
		$end_date = strtotime($end_date);
		if ($end_date)
		{
			$where .= ' AND addtime <= ' . $end_date;
		}

		//销售量范围起点
		$start_sales_num = $this->_request('start_sales_num');
		if ($start_sales_num != -1 && $start_sales_num != '')
		{
			$where .= ' AND sales_num >= ' . intval($start_sales_num);
		}

		//销售量范围结束点
		$end_sales_num = $this->_request('end_sales_num');
		if ($end_sales_num != -1 && $end_sales_num != '')
		{
			$where .= ' AND sales_num <= ' . intval($end_sales_num);
		}

		//重新赋值到表单
		$this->assign('group_buy_name', $group_buy_name);
		$this->assign('group_buy_sn', $group_buy_sn);
		$this->assign('start_sales_num', $start_sales_num);
		$this->assign('end_sales_num', $end_sales_num);
		$this->assign('start_date', $start_date ? $start_date : '');
		$this->assign('end_date', $end_date ? $end_date : '');
		$this->assign('category_id', $category_id);
		$this->assign('group_buy_status', $group_buy_status);

		return $where;
	}

	/**
	 * 获取团购列表，公共方法
	 * @author wzg
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
	 * @return void
	 * @todo 获取团购列表，公共方法
	 */
	function group_buy_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();
		$group_buy_obj = new GroupBuyModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $group_buy_obj->getGroupBuyNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$group_buy_obj->setStart($Page->firstRow);
        $group_buy_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$group_buy_list = $group_buy_obj->getGroupBuyList('', $where, ' addtime DESC');
		$group_buy_list = $group_buy_obj->getListData($group_buy_list);

		$this->assign('group_buy_list', $group_buy_list);
		#echo "<pre>";
		#print_r($group_buy_list);
		#echo "</pre>";
		#echo $group_buy_obj->getLastSql();

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->assign('url', '/FrontMall/group_buy_detail/group_buy_id/');
		$this->display(APP_PATH . 'Tpl/AcpGroupBuy/get_group_buy_list.html');
	}

	/**
	 * 出售中的团购列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 获取出售中的团购列表
	 */
	function get_onsale_group_buy_list()
	{
		$this->group_buy_list('isuse = 1', '团购中的商品列表', 'onsale');
	}

	/**
	 * 仓库中的团购列表
	 * @author wzg
	 * @param void
	 * @return void
	 * @todo 获取仓库中的团购列表
	 */
	function get_store_group_buy_list()
	{
		$this->group_buy_list('isuse = 0', '仓库中的团购列表', 'store');
	}

	/**
     * 添加团购
     * @author wzg
     * @return void
     * @todo 上传新团购
     */
	public function add_group_buy()
	{
        $action = I('post.action');

        $GroupBuy = D('GroupBuy');

        // 添加团购
        if ($action == 'add') {
        	if ($GroupBuy->create()) {
                $GroupBuy->addtime = NOW_TIME;
                if($GroupBuy->add())
                {
                    $this->success('添加成功');
                } 
                else 
                {
                    $this->error('添加失败');
                }
            } else {
                $this->error($GroupBuy->getError());
            }
        }

        $this->assign('head_title', '添加团购商品');
        $this->display(APP_PATH . '/Tpl/AcpGroupBuy/add_group_buy.html');
	}

		
	/**
     * 修改团购
     * @author wzg
     * @return void
     * @todo 修改团购
     */
	public function edit_group_buy()
	{
        $group_buy_id = intval($this->_get('group_buy_id'));
		if (!$group_buy_id)
		{
			$this->error('对不起，非法访问！');
		}
		$group_buy_obj = new GroupBuyModel($group_buy_id);
		$group_buy_info = $group_buy_obj->getGroupBuyInfo('group_buy_id = ' . $group_buy_id);
        if ($group_buy_info['item_id']) {
            $group_buy_info['item_name'] = M('Item')->where('item_id = ' . $group_buy_info['item_id'])->getField('item_name');
        }
		if (!$group_buy_info)
		{
			$this->error('对不起，不存在相关团购！');
		}

		$act = $this->_post('action');
        // 修改团购
		if ($act == 'add')
		{
            if($group_buy_obj->create()) {
                if($group_buy_obj->where('group_buy_id = ' . $group_buy_id)->save()) {
                    $this->success('修改成功');
                } else {
                    $this->error('修改失败');
                }
            } else  {
                $this->error($group_buy_obj->getError());
            }
		}
        $this->assign('group_buy_info', $group_buy_info);
        $this->assign('head_title', '修改团购商品');
        $this->display(APP_PATH . '/Tpl/AcpGroupBuy/edit_group_buy.html');
    }

    /**
     * 异步获取商品列表
     * todo 根据传过来的商品名称找到所对应的商品列表
     * @author wzg
     */
    public function get_item_by_name()
    {
        $item_name = I('item_name', '', 'strip_tags');
        if(!$item_name) exit('false');

        $item_obj = new ItemModel();
        $item_obj->setLimit(10);
        $item_list = $item_obj->field('item_id, item_name')->where('isuse != 2 AND stock > 0 AND  item_name LIKE "%' . $item_name . '%"')->limit()->select();

        if (!$item_list) exit('false');

        exit(json_encode($item_list));
    }


    /**
     * 禁用/启用商品
     * @author wzg
     * @return failure/success
     * @todo 修改isuse
     * */
    public function set_isuse()
    {
        $group_buy_id = I('group_buy_id', 0, 'int');
        if(!$group_buy_id) exit('failure');

        $isuse = I('isuse', 0, 'int');
        $isuse = $isuse == 3 ? 0 : $isuse;

        $group_buy_obj = D('GroupBuy');
        $state = $group_buy_obj->where('group_buy_id = ' . $group_buy_id)->setField(array('isuse' => $isuse));

        if($state) {

            exit('success');
        } else {

            exit('failure');
        }
    }

    /**
     * 批量禁用商品
     * @author wzg
     * @return failure/success
     * @todo 修改isuse
     * */
    public function batch_set_isuse()
    {
        $group_buy_ids = I('group_buy_ids', '', 'strip_tags');
        if(!$group_buy_ids) exit('failure');

        $isuse = I('isuse', 0, 'int');
        $isuse = $isuse == 3 ? 0 : $isuse;

        $ids = explode(',', $group_buy_ids);
        $group_buy_obj = D('GroupBuy');
        $num = 0;
        if($ids) {

            foreach($ids AS $id)
            {
                $num += $group_buy_obj->where('group_buy_id = ' . $id)->setField(array('isuse' => $isuse));
            }
        } else {

            exit('failure');
        }

        if($num) {

            exit('success');
        } else {

            exit('failure');
        }
    }

    /**
     * 删除
     * 假删除
     * wzg
     */
    public function delete_group_buy()
    {
        $group_buy_id  = I('post.group_buy_id', 0, 'int');
        if (!$group_buy_id) exit('failure');

        $status = M('GroupBuy')->where('group_buy_id = ' . $group_buy_id)->setField('isuse', 2);

        if ($status) {
            exit('success');
        } else {
            exit('failure');
        }
    }

}

    

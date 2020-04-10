<?php
/**	
 * acp后台种植机类
 */
class AcpPlanterAction extends AcpAction {

    /**
     * 初始化
     * @author 姜伟
     * @return void
     * @todo 初始化方法
     */
	function _initialize()
	{
		if (ACTION_NAME != 'set_remark' && ACTION_NAME != 'set_command' && ACTION_NAME != 'get_new_data')
		{
			parent::_initialize();
		}
    }

	/**
	 * 接收搜索表单数据，组织返回where子句
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 接收表单提交的参数，过滤合法性，组织成where子句并返回
	 */
	function get_search_condition()
	{
		//初始化查询条件
		$where = '';

		//是否绑定
		$is_bind = $this->_request('is_bind');
		if (ctype_digit($is_bind))
		{
			$where .= $is_bind ? ' AND bind_time > 0' : ' AND bind_time = 0';
		}

		//用户ID
		$user_id = $this->_request('user_id');
		if (ctype_digit($user_id))
		{
			$where .= ' AND user_id = ' . $user_id;
		}

		//种植机名称
		$planter_name = $this->_request('planter_name');
		if ($planter_name)
		{
			$where .= ' AND planter_name LIKE "%' . $planter_name . '%"';
		}

		//种植机备注
		$remark = $this->_request('remark');
		if ($remark)
		{
			$where .= ' AND remark LIKE "%' . $remark . '%"';
		}

		//种植机mac地址
		$planter_code = $this->_request('planter_code');
		if ($planter_code)
		{
			$where .= ' AND planter_code LIKE "%' . $planter_code . '%"';
		}

		//序列号
		$serial_num = $this->_request('serial_num');
		if ($serial_num)
		{
			$where .= ' AND serial_num LIKE "%' . $serial_num . '%"';
		}

		//生成ID
		$product_id = $this->_request('product_id');
		if ($product_id)
		{
			$where .= ' AND product_id LIKE "%' . $product_id . '%"';
		}

		//是否冒险模式
		$is_risk_mode = $this->_request('is_risk_mode');
		if (ctype_digit($is_risk_mode) && $is_risk_mode)
		{
			$where .= ' AND is_risk_mode = ' . $is_risk_mode;
		}

		//添加时间范围起始时间
		$start_date = $this->_request('start_date');
		$start_date = str_replace('+', ' ', $start_date);
		$start_date = strtotime($start_date);
		if ($start_date)
		{
			$where .= ' AND bind_time >= ' . $start_date;
		}

		//添加时间范围结束时间
		$end_date = $this->_request('end_date');
		$end_date = str_replace('+', ' ', $end_date);
		$end_date = strtotime($end_date);
		if ($end_date)
		{
			$where .= ' AND bind_time <= ' . $end_date;
		}

		//重新赋值到表单
		$this->assign('is_bind', $is_bind);
		$this->assign('planter_name', $planter_name);
		$this->assign('remark', $remark);
		$this->assign('planter_code', $planter_code);
		$this->assign('serial_num', $serial_num);
		$this->assign('product_id', $product_id);
		$this->assign('is_risk_mode', $is_risk_mode);
		$this->assign('start_date', $start_date ? $start_date : '');
		$this->assign('end_date', $end_date ? $end_date : '');

		return $where;
	}

	function aaa()
	{
		$this->display();
	}

	/**
	 * 获取种植机列表，公共方法
	 * @author 姜伟
	 * @param string $where
	 * @param string $head_title
	 * @param string $opt	引入的操作模板文件
	 * @return void
	 * @todo 获取种植机列表，公共方法
	 */
	function planter_list($where, $head_title, $opt)
	{
		$where .= $this->get_search_condition();
		$planter_obj = new PlanterModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $planter_obj->getPlanterNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$planter_obj->setStart($Page->firstRow);
        $planter_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$planter_list = $planter_obj->getPlanterList('', $where, ' addtime DESC');
		$planter_list = $planter_obj->getListData($planter_list);
		#echo "<pre>";
		#print_r($planter_list);
		#echo "</pre>";
		#echo $planter_obj->getLastSql();
		#die;

		$this->assign('planter_list', $planter_list);

        //是否开启冒险模式
        $is_risk_mode_list =  array(
            '0'	=> '未开启',
            '1'	=> '开启',
        );
        $this->assign('is_risk_mode_list', $is_risk_mode_list);

        //种植机状态
        $is_bind_list =  array(
            '0'	=> '未绑定',
            '1'	=> '已绑定',
        );
        $this->assign('is_bind_list', $is_bind_list);

		$this->assign('opt', $opt);
		$this->assign('head_title', $head_title);
		$this->display(APP_PATH . 'Tpl/AcpPlanter/get_planter_list.html');
	}

	/**
	 * 所有种植机列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取所有种植机列表
	 */
	function get_all_planter_list()
	{
		$this->planter_list('1', '所有种植机', 'all');
	}

	/**
	 * 已绑定种植机列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取已绑定种植机列表
	 */
	function get_binded_planter_list()
	{
		$this->planter_list('bind_time > 0', '已绑定种植机', 'binded');
	}

	/**
	 * 未绑定种植机列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取未绑定种植机列表
	 */
	function get_unbinded_planter_list()
	{
		$this->planter_list('bind_time = 0', '未绑定种植机', 'unbinded');
	}

	/**
	 * 测试种植机
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 测试种植机
	 */
	function planter_test()
	{
		$where = 'bind_time = 0';
		$head_title = '未绑定种植机';
		$opt = 'unbinded';
		$where .= $this->get_search_condition();
		$planter_obj = new PlanterModel();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $planter_obj->getPlanterNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$planter_obj->setStart($Page->firstRow);
        $planter_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('page', $Page);
		$this->assign('show', $show);

		$planter_list = $planter_obj->getPlanterList('', $where, ' addtime DESC');
		$planter_list = $planter_obj->getListData($planter_list);
		#echo "<pre>";
		#print_r($planter_list);
		#echo "</pre>";
		#echo $planter_obj->getLastSql();
		#die;

		$this->assign('planter_list', $planter_list);
		$this->assign('planter_id', count($planter_list) == 1 ? $planter_list[0]['planter_id'] : 0);
		$this->assign('box_state', count($planter_list) == 1 ? $planter_list[0]['box_state'] : '');
		$this->assign('head_title', $head_title);
		$this->display();
	}

	/**
	 * 测试种植机
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 测试种植机
	 */
	function test_planter()
	{
		$act = $this->_post('act');

		if ($act == 'query')
		{
			$where = 'bind_time = 0';
			$head_title = '未绑定种植机';
			$opt = 'unbinded';
			
			//序列号
			$serial_num = $this->_request('serial_num');
			$where .= ' AND serial_num LIKE "%' . $serial_num . '%"';

			$planter_obj = new PlanterModel();
			$this->assign('serial_num', $serial_num);

			//分页处理
			import('ORG.Util.Pagelist');
			$count = $planter_obj->getPlanterNum($where);
			$Page = new Pagelist($count,C('PER_PAGE_NUM'));
			$planter_obj->setStart($Page->firstRow);
			$planter_obj->setLimit($Page->listRows);
			$show = $Page->show();
			$this->assign('page', $Page);
			$this->assign('show', $show);

			$planter_list = $planter_obj->getPlanterList('', $where, ' addtime DESC');
			$planter_list = $planter_obj->getListData($planter_list);
			#echo "<pre>";
			#print_r($planter_list);
			#echo "</pre>";
			#echo $planter_obj->getLastSql();
			#die;

			$this->assign('planter_list', $planter_list);
			$this->assign('planter_id', count($planter_list) == 1 ? $planter_list[0]['planter_id'] : 0);
			$this->assign('box_state', count($planter_list) == 1 ? $planter_list[0]['box_state'] : '');
		}

		$this->assign('act', $act);
		$this->assign('head_title', '出厂前测试种植机');
		$this->display();
	}

	/**
	 * 测试所有种植机
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 测试种植机
	 */
	function test_all_planter()
	{
		$act = $this->_post('act');

		if ($act == 'query')
		{
			$where = '1';
			$head_title = '测试所有种植机';
			$opt = 'all';
			
			//序列号
			$serial_num = $this->_request('serial_num');
			$where .= ' AND serial_num LIKE "%' . $serial_num . '%"';

			$planter_obj = new PlanterModel();
			$this->assign('serial_num', $serial_num);

			//分页处理
			import('ORG.Util.Pagelist');
			$count = $planter_obj->getPlanterNum($where);
			$Page = new Pagelist($count,C('PER_PAGE_NUM'));
			$planter_obj->setStart($Page->firstRow);
			$planter_obj->setLimit($Page->listRows);
			$show = $Page->show();
			$this->assign('page', $Page);
			$this->assign('show', $show);

			$planter_list = $planter_obj->getPlanterList('', $where, ' addtime DESC');
			$planter_list = $planter_obj->getListData($planter_list);
			#echo "<pre>";
			#print_r($planter_list);
			#echo "</pre>";
			#echo $planter_obj->getLastSql();
			#die;

			$this->assign('planter_list', $planter_list);
			$this->assign('planter_id', count($planter_list) == 1 ? $planter_list[0]['planter_id'] : 0);
			$this->assign('box_state', count($planter_list) == 1 ? $planter_list[0]['box_state'] : '');
		}

		$this->assign('act', $act);
		$this->assign('head_title', '所有测试种植机');
		$this->display(APP_PATH . 'Tpl/AcpPlanter/test_planter.html');
	}

	/**
     * 添加种植机
     * @author 姜伟
     * @return void
     * @todo 上传新种植机
     */
	public function add_planter()
	{
        $this->assign('head_title', '添加' . C('ITEM_NAME'));

        $action = I('post.action');

        $Planter = D('Planter');

        // 添加种植机
        if ($action == 'add') {
        	if ($Planter->create()) {
                // 种植机分类
                $arr_category = explode('.', I('category_id'));
                $Planter->class_id = $arr_category[0];
                $Planter->sort_id = $arr_category[1];
				#echo "<pre>";
				#print_r($_POST);
				#echo "</pre>";
				#die;

                // 种植机图片
                $data_photo = array();
                $arr_pic = I('post.pic', array());
                foreach ($arr_pic as $k => $pic) {
                    if ($k == 0) {
                        $Planter->base_pic = $pic;
                        $data_photo[$k]['is_default'] = 1;
                    } else {
                        $data_photo[$k]['is_default'] = 0;
                    }
                    $data_photo[$k]['base_pic'] = $pic;
                }

                $Planter->addtime = time();

                // 添加种植机
                $planter_id = $Planter->add();

				if ($planter_id)
				{
                    // 添加种植机图片
					foreach ($data_photo as $k => $photo)
					{
                        $data_photo[$k]['planter_id'] = $planter_id;

                        // 图片压缩加水印
                        $this->_resizeImg(APP_PATH . ltrim($photo['base_pic'], '/'));
                    }
                    D('PlanterPhoto')->addAll($data_photo);

					/**** 添加种植机属性 开始 *****/
					$PlanterExtendProperty = D('PlanterExtendProperty');
					$PlanterSku = D('PlanterSku');

					// 添加种植机扩展属性
					$data = array();
					$arr_extend_value = I('extend_prop_value', array());
					foreach ($arr_extend_value as $value) {
						$data[] = array(
							'planter_id' => $planter_id,
							'property_value_id' => $value
						);
					}
					$PlanterExtendProperty->addAll($data);

					// 添加种植机规格属性
					$has_sku = I('has_sku', 0);
					$Planter->where('planter_id = ' . $planter_id)->save(array('has_sku' => $has_sku));
					if ($has_sku) {			//如果开启sku
						$new_skus 		= $this->_post('new_sku',array());	 //	为该种植机添加的sku信息
						$total_stock 	= 0;   // 种植机库存
						$PlanterPriceRank 	= D('PlanterPriceRank');
						//新添加的sku信息
						if(!empty($new_skus))
						{
							$data_value = array();	//用来过滤重复的sku数据
							foreach($new_skus as $key=>$value)	//按照前台页面的规定，此处的$value代表一条新的sku临时标记（整数且大于等于1，开始,唯一的）
							{
								$data = array();	//要添加的数据
								$new_sku0_val 	= $this->_post('new_J_sku0_'.$value);
								$new_sku1_val 	= $this->_post('new_J_sku1_'.$value);
								$new_sku_code		= $this->_post('new_sku_code'.$value);
								$new_sku_stock	= $this->_post('new_sku_stock'.$value);
								$new_sku_price	= $this->_post('new_sku_price'.$value);
								// 过滤重复数据
								if (in_array($new_sku0_val . ',' . $new_sku1_val, $data_value)) {
									continue;
								} else {
									$data_value[] = $new_sku0_val . ',' . $new_sku1_val;
								}
								
								$data		= array(
										'planter_code'			=>	$new_sku_code,
										'sku_price'			=>	$new_sku_price,
										'sku_stock'			=>	$new_sku_stock,
										'property_value1'	=>	$new_sku0_val,
										'property_value2'	=>	$new_sku1_val,
										'isuse'				=>	1
								);
								$new_sku_id = $PlanterSku->addSku($planter_id,$data);		//执行添加
								if(!$new_sku_id)
								{
									continue;
								}
								$total_stock += $new_sku_stock;		//库存
								//新的sku为每一个会员级别所设置的价格
								$new_sku_agent_rank_ids 	= $this->_post('new_sku_rank_id'.$value);		//这又是一个数组，代表着所有的用户的级别ID
								$new_sku_agent_rank_price 	= $this->_post('new_sku_rank_price'.$value);	//此也是一个数组，代表着该sku种植机的每一个用户级别的价格。该数组的值是与上面的级别数组一一对应的
								#myprint($new_sku_agent_rank_ids);
								foreach($new_sku_agent_rank_ids as $key0=>$value0)
								{
									if(!$new_sku_agent_rank_price[$key0] || $new_sku_agent_rank_price[$key0] <= 0 )		//该sku上没有设置本等级的价格
									{
										continue;
									}
									$sku_rank_price		= array(
											'planter_sku_price_id'	=>	$new_sku_id,
											'agent_rank_id'		=>	$value0,
											'price'				=>	sprintf('%0.2f',$new_sku_agent_rank_price[$key0])
									);
									$PlanterPriceRank->addSkuRankPrice($planter_id,$sku_rank_price);	//一一添加
								}
							}
						}
						// 修改库存数量
						$Planter->where('planter_id = ' . $planter_id)->save(array('stock' => $total_stock));
					}
					/**** 添加种植机属性 结束 *****/

							/**** 添加种植机详情 开始 *****/
                    $PlanterTxt 	  = D('PlanterTxt');
                    $PlanterTxtPhoto = D('PlanterTxtPhoto');

                    $PlanterTxt->add(array('planter_id' => $planter_id, 'contents' => I('contents')));

                    $arr_txt_photo = I('planter_txt_photo', array());
                    $data = array();
                    foreach ($arr_txt_photo as $photo) {
                        $data[] = array(
                            'planter_id'  => $planter_id,
                            'path_img' => $photo
                        );
                    }
                    $PlanterTxtPhoto->addAll($data);
                    /**** 添加种植机详情 结束 *****/

                    if (I('isuse', 0)) {
                        $link = U('/AcpPlanter/get_onsale_planter_list');
                    } else {
                        $link = U('/AcpPlanter/get_store_planter_list');
                    }
                    $this->success('恭喜您，' . C('ITEM_NAME') . '添加成功！', $link);
                } else {
                    $this->error('对不起，' . C('ITEM_NAME') . '基本信息添加失败！');
                }
            } else {
                $this->error($Planter->getError());
            }
        }

        // 自动生成的种植机货号
        $last_id = intval($Planter->order('planter_id DESC')->getField('planter_id'));
        $planter_code = $this->system_config['SN_PREFIX'] . sprintf('%0' . $this->system_config['SN_LENGTH'] . 's', $last_id + 1);
        $this->assign('planter_code', $planter_code);

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);

        // 获取所有启用的种植机类型
        $this->arr_type = get_planter_type();
        $this->assign('arr_type', $this->arr_type);

        $this->display();
	}
		
	/**
     * 修改种植机
     * @author 姜伟
     * @return void
     * @todo 修改种植机
     */
	public function edit_planter()
	{
		$redirect = U('/AcpPlanter/get_onsale_planter_list');
		$planter_id = intval($this->_get('planter_id'));
		if (!$planter_id)
		{
			$this->error('对不起，非法访问！', $redirect);
		}
		$planter_obj = new PlanterModel($planter_id);
		$planter_info = $planter_obj->getPlanterInfo('planter_id = ' . $planter_id);
		if (!$planter_info)
		{
			$this->error('对不起，不存在相关奖品！', $redirect);
		}

		$act = $this->_post('act');
        // 修改种植机
		if ($act == 'edit')
		{
			// 种植机分类
			$arr_category = explode('.', I('category_id'));
			$arr = array(
				'class_id'		=> $arr_category[0],
				'sort_id'		=> $arr_category[1],
				'planter_name'		=> I('planter_name'),
				'planter_code'		=> I('planter_code'),
				'cost_price'	=> I('cost_price'),
				'mall_price'	=> I('mall_price'),
				'stock'			=> I('stock'),
				'stock_alarm'	=> I('stock_alarm'),
				'weight'		=> I('weight'),
				'isuse'			=> I('isuse'),
			);
			#echo "<pre>";
			#print_r($_POST);
			#echo "</pre>";
			#die;

			// 种植机图片
			$Photo   = D('PlanterPhoto');
			$arr_pic = I('pic', array());
			$old_pic = explode('|', I('old_photo'));

			// 需要删除的图片
			$arr_diff = array_diff($old_pic, $arr_pic);
			#echo "<pre>";
			#print_r($arr_pic);
			#print_r($old_pic);
			#echo "</pre>";
			#die;
			foreach ($arr_diff as $diff) {
				if ($Photo->where('planter_id = ' . $planter_id . " AND base_pic = '" . $diff . "'")->delete() !== false) {
					// 删除原图及其大、中、小图
					@unlink($diff);
					@unlink(big_img($diff));
					@unlink(middle_img($diff));
					@unlink(small_img($diff));
				}
			}

			$data = array();
			foreach ($arr_pic as $k => $pic) {
				if ($k == 0)
				{
					$arr['base_pic'] = $pic;
				}
				$is_default = $k == 0 ? 1 : 0;

				if (in_array($pic, $old_pic)) {
					$Photo->where('planter_id = ' . $planter_id . " AND base_pic = '" . $pic . "'")->save(array('is_default' => $is_default, 'serial' => $k));
				} else {
					$data[] = array(
						'planter_id'    => $planter_id,
						'is_default' => $is_default,
						'base_pic'   => $pic,
						'serial'     => $k
					);

					// 图片压缩加水印
					$this->_resizeImg(APP_PATH . ltrim($pic, '/'));
				}
			}
			if($data)    $Photo->addAll($data);

            /***** 种植机属性 开始 *****/
            // 编辑种植机扩展属性
			$PlanterExtendProperty = D('PlanterExtendProperty');
            $PlanterExtendProperty->delPlanterProperty($planter_id);
            $data = array();
            $arr_extend_value = I('extend_prop_value', array());
            foreach ($arr_extend_value as $value) {
                $data[] = array(
                    'planter_id' => $planter_id,
                    'property_value_id' => $value
                );
            }
            $PlanterExtendProperty->addAll($data);

            // 编辑种植机规格属性
            $has_sku = I('has_sku', 0);
            $arr['has_sku'] = $has_sku;		//是否开启sku
            if ($has_sku) {			//如果开启sku
            	$sku_ids = $this->_post('sku_ids');			//要编辑的sku信息，这是一个数组，由种植机sku的ID组成，这个数组的长度与其它提交的表单的长度始终是一样长的，且一一对应
            	$data_value = array();	//用来过滤重复的sku数据
            	
            	$sku0_arr 		= $this->_post('sku0',array());		//系统规定了每一件种植机最多只有2个规格属性。数组长度与$sku_ids等同
            	$sku1_arr 		= $this->_post('sku1',array());		//同上，这是第二个规格属性（如果存在的话）
            	$sku_code_arr 	= $this->_post('sku_code',array());	//该sku的种植机编号
            	$sku_stock_arr	= $this->_post('sku_stock',array());	//该sku的种植机库存
            	$sku_price_arr	= $this->_post('sku_price',array());	//该sku的种植机价格
            	
				$new_skus 		= $this->_post('new_sku',array());	 //	该种植机新添加的sku信息
				$total_stock = 0;   // 种植机库存
				
				$PlanterSku = D('PlanterSku');
            	$arr_planter_sku 	= $PlanterSku->planterSkuInfo($planter_id);	//获取当前的种植机sku信息
            	$old_sku_id 	= array();
            	foreach($arr_planter_sku as $k=>$v)
            	{
            		if(!in_array($v['planter_sku_price_id'],$sku_ids))		//此条件成立说明某一条原先的sku被删除了（删除操作很危险，因为涉及到购物车还有订单）
            		{
            			$PlanterSku->delSku($v['planter_sku_price_id']);							//这里执行删除sku原始信息
            		}else{
            			$old_sku_id[] = $v['planter_sku_price_id'];	//这里存的是剩下的、以前设置过的sku的ID，即这些sku本次要执行编辑
            		}
            	}
            	#myprint($old_sku_id);
            	foreach($sku_ids as $k0=>$v0)	//循环取数据。(这里的$k0 很重要，用来标示每一条sku对应的一组数据)
            	{
            		$data 		= array();
            		$sku_id 	= $v0;					//	编辑的sku  ID
            		$sku0_val 	= $sku0_arr[$k0];		//  种植机该sku的第一个规格属性值ID。(这里就体现出了$k0这个参数的重要性)
            		$sku1_val 	= $sku1_arr[$k0];		//  种植机该sku的第二个规格属性值ID
            		$sku_code	  	= $sku_code_arr[$k0];		//  种植机该sku的货号
            		$sku_stock	= $sku_stock_arr[$k0];	//  种植机该sku的库存
            		$sku_price	= $sku_price_arr[$k0];	//	种植机该sku的价格
            		// 过滤重复数据
            		if (in_array($sku0_val . ',' . $sku1_val, $data_value)) {
            			continue;
            		} else {
            			$data_value[] = $sku0_val . ',' . $sku1_val;
            		}
            		
            		$data		= array(
            				'planter_code'			=>	$sku_code,
            				'sku_price'			=>	$sku_price,
            				'sku_stock'			=>	$sku_stock,
            				'property_value1'	=>	$sku0_val,
            				'property_value2'	=>	$sku1_val,
            				'isuse'				=>	1
            		);
            		if(in_array($sku_id,$old_sku_id))		//如果本条sku已经存在于旧的sku信息中，那么本次执行更新
            		{
            			$PlanterSku->setSku($sku_id,$data);		//执行更新
            		}
            		else			//否则新增一条sku记录
            		{
            			$PlanterSku->addSku($planter_id,$data);		//执行添加
            		}
            		
            		$sku_rank_price 	= array();		// 该种植机的本条sku的级别价格的设置
            		$sku_rank_id_arr 	= $this->_post('sku_rank_id_'.$sku_id);		//级别ID
            		$sku_rank_price_arr = $this->_post('sku_rank_price_'.$sku_id);	//级别价格，与级别ID一一对应
            		
            		//删除该种植机当前sku条件下所有设置的等级价格(如此操作，逻辑简单)
            		//$PlanterSku->delPlanterSkuRankPrice($planter_id,$sku_id);     //由于前面执行了$PlanterPriceRank->delPlanterAgentRankPrice($planter_id) 所以每一次执行种植机编辑操作，所有的等级价格都清空了，所以这里不再执行
            		foreach($sku_rank_id_arr as $k1=>$v1)
            		{
            			if(!$sku_rank_price_arr[$k1] || $sku_rank_price_arr[$k1] <= 0 )		//该sku上没有设置本等级的价格
            			{
            				continue;
            			}
            			$sku_rank_price		= array(
            					'planter_sku_price_id'	=>	$sku_id,
            					'agent_rank_id'		=>	$v1,
            					'price'				=>	sprintf('%0.2f',$sku_rank_price_arr[$k1])
            			);
            			$PlanterPriceRank->addSkuRankPrice($planter_id,$sku_rank_price);	//重新一一添加
            		}
            		
            		$total_stock += $sku_stock;		//库存加上
            	}
            	
            	//新添加的sku信息
            	if(!empty($new_skus))
            	{
            		foreach($new_skus as $key=>$value)	//按照前台页面的规定，此处的$value代表一条新的sku临时标记（整数且大于等于1，开始,唯一的）
                    {
                    	$data = array();	//要添加的数据
                    	$new_sku0_val 	= $this->_post('new_J_sku0_'.$value);
                    	$new_sku1_val 	= $this->_post('new_J_sku1_'.$value);
                    	$new_sku_code		= $this->_post('new_sku_code'.$value);
                    	$new_sku_stock	= $this->_post('new_sku_stock'.$value);
                    	$new_sku_price	= $this->_post('new_sku_price'.$value);
                    	// 过滤重复数据
                    	if (in_array($new_sku0_val . ',' . $new_sku1_val, $data_value)) {
                    		continue;
                    	} else {
                    		$data_value[] = $new_sku0_val . ',' . $new_sku1_val;
                    	}
                    	$data		= array(
            				'planter_code'			=>	$new_sku_code,
            				'sku_price'			=>	$new_sku_price,
            				'sku_stock'			=>	$new_sku_stock,
            				'property_value1'	=>	$new_sku0_val,
            				'property_value2'	=>	$new_sku1_val,
            				'isuse'				=>	1
            			);
            			$new_sku_id = $PlanterSku->addSku($planter_id,$data);		//执行添加
            			if(!$new_sku_id)
            			{
            				continue;
            			}
            			$total_stock += $new_sku_stock;
            			//新的sku为每一个会员级别所设置的价格
            			$new_sku_agent_rank_ids 	= $this->_post('new_sku_rank_id'.$value);		//这又是一个数组，代表着所有的用户的级别ID
            			$new_sku_agent_rank_price 	= $this->_post('new_sku_rank_price'.$value);	//此也是一个数组，代表着该sku种植机的每一个用户级别的价格。该数组的值是与上面的级别数组一一对应的
            			#myprint($new_sku_agent_rank_ids);
            			foreach($new_sku_agent_rank_ids as $key0=>$value0)
            			{
            				if(!$new_sku_agent_rank_price[$key0] || $new_sku_agent_rank_price[$key0] <= 0 )		//该sku上没有设置本等级的价格
            				{
            					continue;
            				}
            				$sku_rank_price		= array(
            						'planter_sku_price_id'	=>	$new_sku_id,
            						'agent_rank_id'		=>	$value0,
            						'price'				=>	sprintf('%0.2f',$new_sku_agent_rank_price[$key0])
            				);
            				$PlanterPriceRank->addSkuRankPrice($planter_id,$sku_rank_price);	//重新一一添加
            			}
            		}
            	}
            	// 修改库存数量
            	$arr['stock'] = $total_stock; 
            }
            
            /***** 种植机属性 结束 *****/

			/***** 详细描述 开始 *****/
			$PlanterTxt 	  = D('PlanterTxt');
			$PlanterTxtPhoto = D('PlanterTxtPhoto');
			$PlanterTxt->delPlanterTxt($planter_id);
			$PlanterTxt->add(array('planter_id' => $planter_id, 'contents' => I('contents')));

			$PlanterTxtPhoto->delPlanterTxtPhoto($planter_id);
			$arr_txt_photo = I('planter_txt_photo', array());
			$data = array();
			foreach ($arr_txt_photo as $photo) {
				$data[] = array(
					'planter_id'  => $planter_id,
					'path_img' => $photo
				);
			}
			$PlanterTxtPhoto->addAll($data);
			/***** 详细描述 结束 *****/

			//将种植机基本信息保存到数据库
			if ($planter_obj->editPlanter($arr))
			{
				$this->success('恭喜您，' . C('ITEM_NAME') . '编辑成功！');
			}
			else
			{
				$this->error('对不起，' . C('ITEM_NAME') . '编辑失败！');
			}
        }

        // 获取所有启用的分类
        $this->arr_category = get_category_tree();
        $this->assign('arr_category', $this->arr_category);
	
        // 种植机图片
        $arr_photo = array();
        $old_photo = '';
		$PlanterTxt = D('PlanterTxt');
		$PlanterTxtPhoto = D('PlanterTxtPhoto');
		$PlanterPhoto = D('PlanterPhoto');
        $res = $PlanterPhoto->getPhotos($planter_id);
        if ($res) {
            $arr_photo = $res;
            $old_photo = implode('|', $res);
        }
        $this->assign('arr_photo', $arr_photo);
        $this->assign('old_photo', $old_photo);

        // 种植机描述
        $this->assign('contents', $PlanterTxt->getPlanterTxt($planter_id));

        // 种植机描述图片
        $this->assign('arr_txt_photo', $PlanterTxtPhoto->getPlanterTxtPhotoList($planter_id));

        $this->assign('planter', $planter_info);

        // 获取所有启用的种植机类型
        $this->arr_type = get_planter_type();
        $this->assign('arr_type', $this->arr_type);

        // 获取种植机类型
        $type_id = $planter_info['planter_type_id'];
        $this->assign('type_id', $type_id);
        
        /***** 获取种植机扩展属性 开始 *****/
		$PlanterExtendProperty = D('PlanterExtendProperty');
        $arr_extend_prop = get_type_extend_prop($type_id);
        $arr_prop_list   = $PlanterExtendProperty->getPropertyListByPlanterId($planter_id);
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
        /***** 获取种植机扩展属性 结束 *****/

        /***** 获取种植机规格属性 开始 *****/
        $arr_sku = get_type_sku($type_id);
        $this->assign('arr_sku', $arr_sku);
        $this->assign('sku_num', count($arr_sku));
		$PlanterSku = D('PlanterSku');
        $arr_planter_sku = $PlanterSku->planterSkuInfo($planter_id);
        $this->assign('arr_planter_sku', $arr_planter_sku);
        
        /***** 获取种植机规格属性 结束 *****/

        $this->assign('head_title', '修改' . C('ITEM_NAME'));
        $this->display();
	}

    /**
     * 种植机详情图片上传
     * @author 姜伟
     */
    public function upload_desc_pic()
    {
        import("@.Common.EditorUpload");
        $conf = array(
            'imageDomain' => 'http://' . $_SERVER['HTTP_HOST'],
            'rootPath' => APP_PATH . 'Uploads',
            'rootDir' => $GLOBALS['install_info']['dir_name'] . '/planter_txt_photo'
        );
        $eUpload = new EditorUpload($conf);
        $eUpload->save($_FILES['imgFile']);
    }

    /**
     * 图片压缩加水印
     * @param string $base_img 原图地址(绝对路径)
     * @return array 生成的图片信息
     */
    protected function _resizeImg($base_img) {
        import('ORG.Util.Image');
        $Image = new Image();

        $arr_img = array();

        if (!is_file($base_img)) return $arr_img;

        $base_img_info = pathinfo($base_img);
        $img_path = $base_img_info['dirname'] . '/';
        $img_extension = $base_img_info['extension'];
        $img_name = str_replace('.' . $img_extension, '', $base_img_info['basename']);

        /***** 等比缩放 开始 *****/

        // 生成大图
        $big_img_path = $img_path . $img_name . C('BIG_IMG_SUFFIX') . '.' . $img_extension;
        $big_img = $Image->thumb($base_img, $big_img_path, $img_extension, C('BIG_IMG_SIZE'), C('BIG_IMG_SIZE'));

        // 生成中图
        $middle_img_path = $img_path . $img_name . C('MIDDLE_IMG_SUFFIX') . '.' . $img_extension;
        $middle_img = $Image->thumb($base_img, $middle_img_path, $img_extension, C('MIDDLE_IMG_SIZE'), C('MIDDLE_IMG_SIZE'));

        // 生成小图
        $small_img_path = $img_path . $img_name . C('SMALL_IMG_SUFFIX') . '.' . $img_extension;
        $small_img = $Image->thumb($base_img, $small_img_path, $img_extension, C('SMALL_IMG_SIZE'), C('SMALL_IMG_SIZE'));
        /***** 等比缩放 结束 *****/

        $arr_img['big_img']    = $big_img;
        $arr_img['middle_img'] = $middle_img;
        $arr_img['small_img']  = $small_img;

        return $arr_img;
    }

	//设置备注
	function set_remark()
	{
		$new_remark = $this->_post('new_remark');
		$planter_id = intval($this->_post('planter_id'));
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('role_type');

		if ($user_info && $user_info['role_type'] == 1)
		{
			$arr = array(
				'remark'		=> $new_remark,
			);
			$planter_obj = new PlanterModel($planter_id);
			$planter_obj->editPlanter($arr);
			exit('success');
		}

		exit('failure');
	}

	//设置命令
	function set_command()
	{
		$new_command = $this->_post('new_command');
		$md = $this->_post('md');
		$planter_id = intval($this->_post('planter_id'));
		if (!preg_match("/^[01]{6}/", $new_command))
		{
			exit('failure');
		}

		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('role_type');

		if (is_numeric($md) && $user_info && $user_info['role_type'] == 1)
		{
			$md = $md ? 1 : 0;
			//更新内存中的command
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('', 'planter_id');
			if ($planter_info)
			{
				$arr = array(
					'box_state'		=> $new_command,
					'is_risk_mode'	=> $md,
				);
				$planter_obj->editPlanter($arr);
				$planter_obj->flushCommand('GPIO', $new_command);
				$planter_obj->flushCommand('MD', $md);
				exit('success');
			}
		}

		exit('failure');
	}

	//获取新数据
	function get_new_data()
	{
		$planter_id = intval($this->_post('planter_id'));
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('role_type');

		if ($user_info && $user_info['role_type'] == 1)
		{
			//更新内存中的command
			$planter_obj = new PlanterModel($planter_id);
			$planter_info = $planter_obj->getPlanterInfo('', 'outside_temperature, humidity, illuminance, alarm, last_visit_time');
			if ($planter_info)
			{
				$planter_info['code'] = 1;
				$planter_info['last_visit_time'] = date('Y-m-d H:i:s', $planter_info['last_visit_time']);
				exit(json_encode($planter_info));
			}
		}

		exit('failure');
	}
}
?>

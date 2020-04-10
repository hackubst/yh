<?php
/**
 * 文章管理类
 * 
 *
 */
class AcpPromoAction extends AcpAction {
	
	
	 /**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo
     */
	public function AcpPromoAction()
	{
		parent::__construct();
		require_cache('Common/func_item.php');
	}
	
	/**
     * 买商品赠礼品列表
     * @author 陆宇峰
     * @return void
     * @todo 列出商品赠送礼品表。从tp_promotion_item_gift表拉数据。
     */
	public function list_item_gift()
	{
		
		require_once('Lib/Model/OtherItemPromoModel.class.php');
		$OtherItemPromoModel = new OtherItemPromoModel();
		$s 			= $this->_request('s');			//标记是分页数据
		$item_name 	= $this->_request('iname');			
		$start_time = $this->_request('t1');
		$end_time	= $this->_request('t2');
		$isuse		= $this->_request('isuse');
		$act 		= $this->_request('act');
		
		if($item_name && $s == 1)
		{
			$item_name = url_jiemi($item_name);
		}
		
		$start_time = $start_time?strtotime($start_time):0;
		$end_time	= $end_time?strtotime($end_time):0;
		
		$this->assign('s_item_name',$item_name);
		$this->assign('s_isuse',$isuse);
		$this->assign('s_time1',$start_time);
		$this->assign('s_time2',$end_time);
		
		$total = $OtherItemPromoModel->countItemGiftPromo($item_name, $start_time, $end_time, $isuse);            //符合条件的总活动数
		#die($ItemPromotionModel->getLastSql());
		#myprint($total);
		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		//获取查询参数
		$map['iname'] 	= url_jiami($item_name);
		$map['t1']		= $start_time;
		$map['t2']		= $end_time;
		$map['isuse']	= $isuse;
		$map['s']		= 1;
		
		foreach($map as $k=>$v){
			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);
		
		require_once('Lib/Model/UserRankModel.class.php');
		$UserRankModel = new UserRankModel();
		$rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
		$temp_rank = array();
		foreach($rank_list as $k=>$v)
		{
			$rank_id = $v['agent_rank_id'];
			$temp_rank[$rank_id]  = $v['rank_name'];
		}
		
		$OtherItemPromoModel->setStart($Page->firstRow);
		$OtherItemPromoModel->setLimit($Page->listRows);
		$r = $OtherItemPromoModel->getItemGiftPromoList($item_name, $start_time, $end_time, $isuse);
		#echo $OtherItemPromoModel->getLastSql();
		foreach($r as $k=>$v)
		{
			$r[$k]['small_img']  = $v['base_pic'] ? small_img($v['base_pic']) : '';
			$r[$k]['start_time'] = $v['start_time']?date('Y-m-d H:i ',$v['start_time']):'未指定';
			$r[$k]['end_time']   = $v['end_time']?date('Y-m-d H:i ',$v['end_time']):'未指定';
		}
		#myprint($r);
		
		$this->assign('promo_info',$r);
		$this->assign('head_title','指定商品送礼品');
		$this->display();
	}
	
	/**
     * 添加商品赠送礼品
     * @author 陆宇峰
     * @return void
     * @todo 添加商品赠送礼品，添加到tp_promotion_item_gift表。一件商品允许添加多件赠品规则。
     */
	public function add_item_gift()
	{
		$act = $this->_post('act');
		if($act == 'add')			//执行添加操作时
		{
			require_once('Lib/Model/ItemPromotionModel.class.php');
			$ItemPromotionModel = new ItemPromotionModel();
				
			$item_list  	= $this->_post('choose');		//哪些商品参加活动
			$item_total		= $this->_post('i_total');		//需要购买多少件商品
			$gift_id		= $this->_post('gift');			//赠送哪件礼品
			$gift_total		= $this->_post('g_total');		//送多少件
			$start_time 	= $this->_post('time1'); 		//活动开始时间
			$end_time 		= $this->_post('time2');		//活动结束时间
			$isuse			= $this->_post('used');			//是否启用
			$redirect		= $this->_get('redirect');
			$redirect	= $redirect?url_jiemi($redirect): U('/AcpPromo/list_item_gift');
			
			$start_time = $start_time?strtotime($start_time):0;
			$end_time	= $end_time?strtotime($end_time):0;
			if(empty($item_list))
			{
				$this->error('您没有选择参与送礼活动的商品',get_url());
			}
			$false = 0;
			foreach($item_list as $k=>$v)
			{
				$data = array(
					'item_total'	=> $item_total,
					'item_id'		=> $v,
					'gift_id'		=> $gift_id,
					'gift_total'	=> $gift_total,
					'start_time'	=> $start_time,
					'end_time'		=> $end_time,
					'isuse'			=> ($isuse==1)?1:2
				);
				if($ItemPromotionModel->addPromotion($data,2))		//执行添加
				{
					continue;
				}
				else
				{
					$false ++;
				}
			}
			if($false > 0)
			{
				$this->error('对不起3,活动未能成功添加,您可以在尝试一次!',get_url());
			}
			$this->success('恭喜你，活动添加成功',$redirect);
			
			exit;
		}
		
		// 获取所有启用的分类
		$arr_category = get_category_tree();
		$this->assign('arr_category', $arr_category);
		
		// 获取所有启用的品牌
		$arr_brand = get_brand();
		$this->assign('arr_brand', $arr_brand);
		
		// 获取所有启用的商品类型
		$arr_type = get_item_type();
		$this->assign('arr_type', $arr_type);
		
		//获取所有的礼品
		require_once('Lib/Model/GiftModel.class.php');
		$GiftModel = new GiftModel();
		$gift_list = $GiftModel->getGiftList('isuse = 1');
		#myprint($gift_list);
		$this->assign('gift_list',$gift_list);
		
		$this->assign('action_title', '指定商品增礼品');
		$this->assign('action_src', '/AcpPromo/list_item_gift');
		$this->assign('head_title','添加新的商品促销(赠礼品)');
		$this->display();
	}
	
	/**
     * 添加商品赠送礼品
     * @return void
     * @todo 修改商品赠送礼品，tp_promotion_item_gift表
     */
	public function edit_item_gift()
	{
		
		$promotion_item_gift_id = $this->_get('pi');
		$redirect 	= $this->_get('redirect');
		$flag_pi	= $this->_post('f');
		$redirect 	= $redirect?url_jiemi($redirect):U('/AcpPromo/list_item_gift');
		
		if(!$promotion_item_gift_id)
		{
			$this->error('对不起，操作被决绝！',$redirect);
		}
		$promotion_item_gift_id = url_jiemi($promotion_item_gift_id);
		require_once('Lib/Model/OtherItemPromoModel.class.php');
		$OtherItemPromoModel = new OtherItemPromoModel();
		$promo_info = $OtherItemPromoModel->getPromoInfoForItemGift($promotion_item_gift_id);		//获取活动详情
		
		if(!$promo_info)
		{
			$this->error('不存在的活动',$redirect);
		}
		$promo_info['start_time']	= $promo_info['start_time']?date('Y-m-d H:i ',$promo_info['start_time']):'';
		$promo_info['end_time']	= $promo_info['end_time']?date('Y-m-d H:i ',$promo_info['end_time']):'';
		$this->assign('promo_info',$promo_info);
		
		$act = $this->_post('act');
		if($act == 'edit')			//执行添加操作时
		{
			if($promotion_item_gift_id != url_jiemi($flag_pi))
			{
				$this->error('非法请求，操作被决绝！',$redirect);
			}
			$item_list  	= $this->_post('choose');		//哪些商品参加活动
			$item_total		= $this->_post('i_total');		//需要购买多少件商品
			$gift_id		= $this->_post('gift');			//赠送哪件礼品
			$gift_total		= $this->_post('g_total');		//送多少件
			$start_time 	= $this->_post('time1'); 		//活动开始时间
			$end_time 		= $this->_post('time2');		//活动结束时间
			$isuse			= $this->_post('used');			//是否启用
			$redirect		= $this->_get('redirect');
			$redirect	    = $redirect?url_jiemi($redirect):U('/AcpPromo/list_item_gift');
				
			$start_time = $start_time?strtotime($start_time):0;
			$end_time	= $end_time?strtotime($end_time):0;
			
			$data = array(
					'item_total'	=> $item_total,
					'gift_id'		=> $gift_id,
					'gift_total'	=> $gift_total,
					'start_time'	=> $start_time,
					'end_time'		=> $end_time,
					'isuse'			=> ($isuse==1)?1:2
			);
			//$OtherItemPromoModel->updGiftForItem($promotion_item_gift_id,$data);
			/*dump($data);
			dump($OtherItemPromoModel->_sql());
			die;*/
			if($OtherItemPromoModel->updGiftForItem($promotion_item_gift_id,$data))		//执行添加
			{
				
				$this->success('恭喜你，活动编辑成功',$redirect);
			}
			else
			{
				$this->error('对不起,活动未能编辑成功,可能未修改参数,您可以再尝试一次!',get_url());
			}	
			exit;
		}
		
		//获取所有的礼品
		require_once('Lib/Model/GiftModel.class.php');
		$GiftModel = new GiftModel();
		$gift_list = $GiftModel->getGiftList('isuse = 1');
		//dump($gift_list);
		$this->assign('gift_list',$gift_list);
		
		$this->assign('action_title', '指定商品增礼品');
		$this->assign('action_src', '/AcpPromo/list_item_gift');
		$this->assign('head_title','编辑商品促销(赠礼品)');
		$this->display();
	}
	
	/**
     * 删除商品赠送礼品
     * @type AJAX
     * @return json
     * @todo 删除商品赠送礼品，tp_promotion_item_gift表，彻底删除数据
     */
	public function del_item_gift()
	{
		$promotion_item_gift_id  = $this->_post('pi');
		$act 					 = $this->_post('act');
		
		if(!$act || $act != 'del' || !$promotion_item_gift_id)
		{
			exit(json_encode(array('type'=>-1,'message'=>'非法请求，操作被拒绝！')));
		}
		require_once('Lib/Model/OtherItemPromoModel.class.php');
		$OtherItemPromoModel = new OtherItemPromoModel();
		$promotion_item_gift_id = url_jiemi($promotion_item_gift_id);
		if($OtherItemPromoModel->delGiftForItem($promotion_item_gift_id))
		{
			exit(json_encode(array('type'=>1,'message'=>'恭喜您,活动成功删除了！')));
		}
		else
		{
			exit(json_encode(array('type'=>-1,'message'=>'对不起，删除有误！')));
		}	
		
	}

	/**
	 * @access public
     * @return void
     * @todo 商品直接优惠促销列表
     */
	public function list_item_discount()
	{
		require_once('Lib/Model/ItemPromotionModel.class.php');
		$ItemPromotionModel = new ItemPromotionModel();
		
		$s 	 		= $this->_get('s');			//标记是分页数据
		$start_time = $this->_request('t1');
		$end_time	= $this->_request('t2');
		$type		= $this->_request('type');
		$isuse		= $this->_request('isuse');
		$act 		= $this->_request('act');
		
		/*dump(date('Y-m-d',strtotime($start_time)));
		dump(date('Y-m-d',strtotime($end_time)));*/
		$where = '1 AND 1';
		if($start_time)
		{
			$where .= ' AND start_time >='. strtotime($start_time) ;
		}
		if($end_time)
		{
			$where .= ' AND end_time >='. strtotime($end_time) ;
		}
		if($type)
		{
			$where .= ' AND discount_type ='. $type ;
		}
		if($isuse)
		{
			$where .= ' AND isuse ='. $isuse ;
		}
	//	$total = $ItemPromotionModel->countPromotion(strtotime($start_time),strtotime($end_time),$type,$isuse);            
		$total = $ItemPromotionModel->countPromotion($where);            
		//符合条件的总活动数
		//dump($ItemPromotionModel->getLastSql());
		//dump($total);
		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		//获取查询参数
		$map['total'] 	= $total;
		$map['t1']		= strtotime($start_time);
		$map['t2']		= strtotime($end_time);
		$map['type']	= $type;
		$map['isuse']	= $isuse;
		
		foreach($map as $k=>$v){
			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);

		$UserRankModel = new UserRankModel();
		$rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
		$temp_rank = array();
		foreach($rank_list as $k=>$v)
		{
			$rank_id = $v['user_rank_id'];
			$temp_rank[$rank_id]  = $v['rank_name'];
		}
		
		$ItemPromotionModel->setStart($Page->firstRow);
		$ItemPromotionModel->setLimit($Page->listRows);
		$r = $ItemPromotionModel->getPromotionListInfo(strtotime($start_time),strtotime($end_time),$type,$isuse);
		//dump( $ItemPromotionModel->getLastSql());
		foreach($r as $k=>$v)
		{
			$r[$k]['start_time'] = $v['start_time']?date('Y-m-d H:i ',$v['start_time']):'未指定';
			$r[$k]['end_time'] = $v['end_time']?date('Y-m-d H:i ',$v['end_time']):'未指定';
			$rank_need = explode(',',$v['agent_rank_id']);
			$rank_need_list = '';
			foreach($rank_need as $v)
			{
				$rank_need_list .= $temp_rank[$v].',';
			}
			$r[$k]['rank_need_list'] = rtrim($rank_need_list,',');
		}
		
		$this->assign('s_time1',$start_time);
		$this->assign('s_time2',$end_time);
		$this->assign('s_type',$type);
		$this->assign('s_isuse',$isuse);

		$this->assign('promolist',$r);
		$this->assign('head_title','指定商品直接优惠');
		$this->display();
	}
	
	/**
     * 添加商品直接优惠促销
     * @return void
     * @todo 添加数据到tp_promotion_item_discount表，商品明细保存到tp_promotion_item_discount_detail表,同一件item可以添加多个促销规则,暂不判断重复，
     * @todo 参与的会员等级保存到tp_promotion_item_discount_rank表。
     */
	public function add_item_discount()
	{
		$act = $this->_post('act');
		if($act == 'add')			//执行添加操作时
		{
			require_once('Lib/Model/ItemPromotionModel.class.php');
			$ItemPromotionModel = new ItemPromotionModel();
			
			$item_list  	= $this->_post('choose');		//哪些商品参加活动
			$title			= $this->_post('title');		//活动标题
			$item_total		= $this->_post('total');		//需要购买多少件商品
			$discount_type  = $this->_post('dis_type'); 	//类型：1、打折 2、降价
			$discount_total = $this->_post('discount'); 	//促销幅度
			$start_time 	= $this->_post('start_time'); 	//活动开始时间
			$end_time 		= $this->_post('end_time');		//活动结束时间
			$isuse			= $this->_post('used');			//是否启用
			$rank_need		= $this->_post('rank_need'); 	//活动针对的代理商级别
			$redirect		= $this->_get('redirect');
			$redirect	= $redirect?url_jiemi($redirect):U('/AcpPromo/list_item_discount');
			
 		#	myprint($_POST);
			if(!$title || !$discount_type || !$discount_total)
			{
				$this->error('请完善页面必填信息',get_url());
			}
			if(!in_array($discount_type,array(1,2)))
			{
				$this->error('请选择正确的促销类型',get_url());
			}
			if($discount_type == 1)
			{
				if($discount_total <=0.00 || $discount_total > 1.00)
				{
					$this->error('请选择正确的折扣率(0.01~1.00)',get_url());
				}
			}else{
				if($discount_total < 0)
				{
					$this->error('请选择正确的降价额度(不能小于0)',get_url());
				}
			}
			
			$start_time = $start_time?strtotime($start_time):0;
			$end_time	= $end_time?strtotime($end_time):0;
			$data = array(
					'title'			=> $title,
					'item_total'	=> $item_total,
					'discount_type'	=> $discount_type,
					'discount_total'=> $discount_total,
					'start_time'	=> $start_time,
					'end_time'		=> $end_time,
					'isuse'			=> ($isuse==1)?1:2
			);
			$promotion_item_discount_id = $ItemPromotionModel->addPromotion($data);		//执行添加
			
			if($promotion_item_discount_id)
			{
				require_once('Lib/Model/OtherItemPromoModel.class.php');
				$OtherItemPromoModel = new OtherItemPromoModel();
		
				$false = 0;
				if($item_list && !empty($item_list))
				{
					if(!$OtherItemPromoModel->addPromotionItems($promotion_item_discount_id,$item_list))		//设置参加活动的商品
					{
						$this->error('对不起1,活动未能成功添加,您可以再尝试一次!',get_url());
					}
				}
				if($rank_need && !empty($rank_need))		//如果进行级别限制
				{
					if(!$OtherItemPromoModel->setPromotionAgentRandNeed($promotion_item_discount_id,$rank_need)) //执行->设置参加活动需要的级别
					{
						$ItemPromotionModel->editPromotion($promotion_item_discount_id, array('isuse'=>2));
						$this->error('对不起2,活动未能成功添加,您可以再尝试一次!',get_url());
					}
				}
				//$this->success('恭喜你，活动添加成功',$redirect);
				$this->success('恭喜你，活动添加成功', U('/AcpPromo/list_item_discount'));
			}
			else
			{
				$this->error('对不起3,活动未能成功添加,您可以在尝试一次!',get_url());
			}
			exit;
		}
		
        $UserRankModel = new UserRankModel();
        $rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
        $temp_rank = array();
        foreach($rank_list as $k=>$v)
        {
            $rank_id = $v['agent_rank_id'];
            $temp_rank[$rank_id]  = $v['rank_name'];
        }
		
        // 获取所有启用的分类
        $arr_category = get_category_tree();
        $this->assign('arr_category', $arr_category);
        
        // 获取所有启用的品牌
        $arr_brand = get_brand();
        $this->assign('arr_brand', $arr_brand);
        
        // 获取所有启用的商品类型
        $arr_type = get_item_type();
        $this->assign('arr_type', $arr_type);
//         myprint($arr_brand);
        
//         $item_arr = $this->list_item();
//         $this->assign('item_list',$item_arr);
//         myprint($item_arr);
		$this->assign('rank_list',$rank_list);
		$this->assign('action_title', '指定商品直接优惠');
		$this->assign('action_src', '/AcpPromo/list_item_discount');
		$this->assign('head_title','添加商品促销活动');
		
		$this->display();
	}
	
	/**
	 *	@access private
	 *	@todo 获取商品列表（搜索） 
	 *	@return array 返回查询结果，同时分配分页变量数据到smarty模板
	 */
	private function list_item()
	{
		$this->Item = D('Item');
		
		$title 		 = $this->_request('title');
		$category_id = $this->_request('category');
		$brand_id	 = $this->_request('brand');
		$cost_price  = $this->_request('cost_price');
		
		import('ORG.Util.Pagelist');
		
		/***** 组织查询条件 开始 *****/
		$condition = array(
				'isuse'      => 1,
				'is_del'     => 0,
                'is_gift'    => 0,
		);
		if ($title){
			$condition['item_name'] = array('LIKE', "%$title%");
		}
		if ($brand_id){
			$condition['brand_id']  = $brand_id;
		}
		// 分类
		if ($category_id) {
			$arr_category = explode('.', $category_id);
			if ($arr_category[0] == 1) {
				$condition['class_id'] = $arr_category[1];
			} elseif ($arr_category[0] == 2) {
				$condition['sort_id'] = $arr_category[1];
			} elseif ($arr_category[0] == 3) {
				$condition['class_genre_id'] = $arr_category[1];
			}
		}
		
		if($cost_price){
			$condition['mall_price'] = array('EGT',$cost_price);
		}
		/***** 组织查询条件 结束 *****/
		
		// 符合条件的商品总数
        log_file(json_encode($condition));
		$count = $this->Item->getItemNum($condition);
        log_file($this->Item->getLastSql());
		
		//分页开始
		$Page = new Pagelist($count, 5);
		//获取查询参数传递
		$map['title']		= $title;
		$map['category']    = $category_id;
		$map['brand']       = $brand_id;
		$map['cost_price']  = $cost_price;
		//      $map['total'] = $total;
		
		foreach($map as $k=>$v){
			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
        $this->assign('page_str',$page_str);

		$this->Item->setStart($Page->firstRow);
		$this->Item->setLimit($Page->listRows);
		$arr_item = $this->Item->listItem($condition, 'addtime DESC');		//分页取数据
        log_file($this->Item->getLastSql());
		//分页结束
  		#echo $this->Item->getLastSql();echo '<hr/>';
		foreach ($arr_item as $k => $item) {
			$arr_item[$k]['small_img'] = $item['base_pic'] ? small_img($item['base_pic']) : '';
			$arr_item[$k]['item_sn']   = $item['item_sn'] ? $item['item_sn'] : '--';
            $arr_item[$k]['cost_price'] = $item['mall_price'];
		}
		
		return $arr_item;
		
// 		$this->assign('item_list', $arr_item);
// 		$this->display();
	}
	
	
	/**
	 * @access public
	 * @type AJAX
	 * @todo ajax异步请求获取商品的分页数据
	 * 
	 */
	public function get_items_ajax()
	{
		$item_arr = $this->list_item();
        $this->assign('item_list',$item_arr);
        $this->display('list_item');
	}
	
	
	/**
     * 修改商品直接优惠促销
     * @return void
     * @todo 修改tp_promotion_item_discount表
     */
	public function edit_item_discount()
	{
		$promotion_item_discount_id = $this->_get('pi');
		$redirect = $this->_get('redirect');
		$redirect = $redirect?url_jiemi($redirect):U('/AcpPromo/list_item_discount');
		
		if(!$promotion_item_discount_id)
		{
			$this->error('参数错误',$redirect);
		}
		$promotion_item_discount_id = url_jiemi($promotion_item_discount_id);	//活动的ID号
		
		//导入模型
		require_once('Lib/Model/ItemPromotionModel.class.php');
		$ItemPromotionModel = new ItemPromotionModel();
		
		//获取活动本身的详情
		$promo_info = $ItemPromotionModel->getPromotion($promotion_item_discount_id);
		if(!$promo_info)
		{
			$this->error('活动不存在',$redirect);
		}
		else
		{
			$act = $this->_post('act');			//提交表单进行保存时
			if($act == 'edit')
			{
				$item_list  	= $this->_post('choose');		//哪些商品参加活动
				$title			= $this->_post('title');		//活动标题
				$item_total		= $this->_post('total');		//需要购买多少件商品
				$discount_type  = $this->_post('dis_type'); 	//类型：1、打折 2、降价
				$discount_total = $this->_post('discount'); 	//促销幅度
				$start_time 	= $this->_post('start_time'); 	//活动开始时间
				$end_time 		= $this->_post('end_time');		//活动结束时间
				$isuse			= $this->_post('used');			//是否启用
				$rank_need		= $this->_post('rank_need'); 	//活动针对的代理商级别
				$redirect		= $this->_get('redirect');
				$redirect	= $redirect?url_jiemi($redirect):U('/AcpPromo/list_item_discount');
				
				#myprint($_POST);
				if(!$title || !$discount_type || !$discount_total)
				{
					$this->error('请完善页面必填信息',get_url());
				}
				if(!in_array($discount_type,array(1,2)))
				{
					$this->error('请选择正确的促销类型',get_url());
				}
				$start_time = $start_time?strtotime($start_time):0;
				$end_time	= $end_time?strtotime($end_time):0;
				$data = array(
						'title'			=> $title,
						'item_total'	=> $item_total,
						'discount_type'	=> $discount_type,
						'discount_total'=> $discount_total,
						'start_time'	=> $start_time,
						'end_time'		=> $end_time,
						'isuse'			=> ($isuse==1)?1:2
				);
				$ItemPromotionModel->editPromotion($promotion_item_discount_id,$data);		//执行更新
			
				require_once('Lib/Model/OtherItemPromoModel.class.php');
				$OtherItemPromoModel = new OtherItemPromoModel();
			
				$false = 0;
				if($item_list && !empty($item_list))
				{
					$OtherItemPromoModel->addPromotionItems($promotion_item_discount_id,$item_list);		//设置参加活动的商品
				}
				if($rank_need && !empty($rank_need))		//如果进行级别限制
				{
					$OtherItemPromoModel->setPromotionAgentRandNeed($promotion_item_discount_id,$rank_need); //设置参加活动需要的级别
				}
				elseif(empty($rank_need))		//取消所有的级别限制时
				{
					$OtherItemPromoModel->deleteAgentRandNeedByPromotionId($promotion_item_discount_id);
				}
				$this->success('恭喜你，活动编辑成功',$redirect);
				
				exit;
			}
				
			//格式化时间
			$promo_info['start_time'] = $promo_info['start_time']?date('Y-m-d H:i ',$promo_info['start_time']):'';
			$promo_info['end_time'] = $promo_info['end_time']?date('Y-m-d H:i ',$promo_info['end_time']):'';
		}
		
		$this->assign('promo_info',$promo_info);
		
		//获取活动所需要的级别信息
		$rank_need = $ItemPromotionModel->getRankInfoByPromoId($promotion_item_discount_id);
		$this->assign('rank_need',explode(',',$rank_need));
		
		//导入模型
		require_once('Lib/Model/OtherItemPromoModel.class.php');
		$OtherItemPromoModel = new OtherItemPromoModel();
		$item_list = $OtherItemPromoModel->getItemListByPromotionId($promotion_item_discount_id);	//已经参与活动的商品

		foreach($item_list as $k=>$v)
		{
			$item_list[$k]['small_img'] = $v['base_pic'] ? small_img($v['base_pic']) : '';
			$item_list[$k]['cost_price'] = $v['mall_price'];
		}
		$this->assign('item_list',$item_list);
		
		#myprint($item_list);
		// 获取所有启用的分类
		$arr_category = get_category_tree();
		$this->assign('arr_category', $arr_category);
		
		// 获取所有启用的品牌
		$arr_brand = get_brand();
		$this->assign('arr_brand', $arr_brand);
		
		// 获取所有启用的商品类型
		$arr_type = get_item_type();
		$this->assign('arr_type', $arr_type);
		
		//导入模型
		$UserRankModel = new UserRankModel();
		//获取所有的级别信息
		$rank_list  = $UserRankModel->getUserRankList();      
		$this->assign('rank_list',$rank_list);
		
		//title
		$this->assign('action_title', '指定商品直接优惠');
		$this->assign('action_src', '/AcpPromo/list_item_discount');
		$this->assign('head_title','编辑商品优惠活动');
		#myprint($promo_info);
		$this->display();
	}
	
	/**
     * 删除商品直接优惠促销
     * @type AJAX
     * @return void
     * @todo 删除tp_promotion_item_discount表，同时删除tp_promotion_item_discount_detail、tp_promotion_item_discount_rank中promotion_item_discount_id相同的记录
     */
	public function del_item_discount()
	{
		$act		  = $this->_post('act');
		$promo_id = $this->_post('pi');
		if(!$promo_id || $act != 'del')
		{
			exit(json_encode(array('type'=>-1,'message'=>'不合法的请求，操作被拒绝!')));
		}
		$promo_id = url_jiemi($promo_id);
		require_once('Lib/Model/ItemPromotionModel.class.php');
		$ItemPromotionModel = new ItemPromotionModel();
		$ItemPromotionModel->deletePromotion($promo_id);  //删除活动本身
		
		require_once('Lib/Model/OtherItemPromoModel.class.php');
		$OtherItemPromoModel = new OtherItemPromoModel();
		$OtherItemPromoModel->deletePromotionByPromotionId($promo_id);
		$OtherItemPromoModel->deleteAgentRandNeedByPromotionId($promo_id);
		
		exit(json_encode(array('type'=>1,'message'=>'恭喜你，活动删除成功!')));
	}
	
	/**
     * 批量删除商品直接优惠促销
     * @author 陆宇峰
     * @return void
     * @todo 删除tp_promotion_item_discount表，同时删除tp_promotion_item_discount_detail、tp_promotion_item_discount_rank中promotion_item_discount_id相同的记录
     */
	public function del_item_discounts()
	{
		$this->display();
	}
	
	/**
     * 订单满额包邮
     * @author 陆宇峰
     * @return void
     * @todo 
     * @todo config表的free_shipping_status=0（不包邮），1（包邮）；free_shipping_total=包邮额度。如1000元
     * @todo 所以只要改变config表这2个字段就可以
     */
	public function set_free_shipping()
	{
		$act = $this->_post('act');
		if($act == 'save')		//提交保存时
		{
			$free_shipping_total		= $this->_post('free_shipping_sum');
			$free_shipping_status		= $this->_post('free_shipping_open');
			$free_shipping_status 		= $free_shipping_status?1:0;
			if($free_shipping_total == '')
			{
				$this->error('对不起,所需订单额度参数为空。若想全场包邮，请设置为0。',get_url());
			}
			$data = array(
					'free_shipping_status'			=>	$free_shipping_status,
					'free_shipping_total'			=>	$free_shipping_total
			);
			require_once('Lib/Model/ConfigBaseModel.class.php');
			$ConfigBaseModel = new ConfigBaseModel();
			$ConfigBaseModel->setConfigs($data);
			$this->success('恭喜你，参数设置成功了!',get_url());
		}
		
		$config = $this->system_config;
		#myprint($config);
		$this->assign('config',$config);
		$this->assign('head_title','订单满额包邮');
		$this->display();
	}
	
	/**
     * 订单满额打折列表
     * @return void
     * @todo 从tp_promotion_order_discount表取数据，会员等级数据从tp_promotion_order_discount_rank表取
     */
	public function list_order_discount()
	{
		require_once('Lib/Model/OrderPromotionModel.class.php');
		$OrderPromotionModel = new OrderPromotionModel();
		
		$s 	 		= $this->_get('s');			//标记是分页数据
		$start_time = $this->_request('t1');
		$end_time	= $this->_request('t2');
		$type		= $this->_request('type');
		$isuse		= $this->_request('isuse');
		$act 		= $this->_request('act');
		
		
		$where = '';		//查询条件
		
		if($start_time)
		{
			$start_time = $s?$start_time:strtotime($start_time);
			$where .= 'start_time >= '.$start_time;
			$this->assign('s_time1',$start_time);
		}
		
		if($end_time)
		{
			$end_time = $s?$end_time:strtotime($end_time);
			if($where != '')
			{
				$where .= ' AND ';
			}
			$where .= 'end_time <= '.$end_time;
			$this->assign('s_time2',$end_time);
		}
		
		if($type)
		{
			if($where != '')
			{
				$where .= ' AND ';
			}
			$where .= 'discount_type = '.$type;
			$this->assign('s_type',$type);
		}
		
		if($isuse)
		{
			if($where != '')
			{
				$where .= ' AND ';
			}
			$where .= 'isuse = '.$isuse;
			$this->assign('s_isuse',$isuse);
		}
		
		$total = $OrderPromotionModel->countPromotion($where);            //符合条件的总活动数
// 		die($OrderPromotionModel->getLastSql());
		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		//获取查询参数
		$map['total'] 	= $total;
		$map['t1']		= $start_time;
		$map['t2']		= $end_time;
		$map['type']	= $type;
		$map['isuse']	= $isuse;
		
		foreach($map as $k=>$v){
			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);
		
		$UserRankModel = new UserRankModel();
		$rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息

		$temp_rank = array();
		foreach($rank_list as $k=>$v)
		{
			$rank_id = $v['user_rank_id'];
            $rank_list[$k]['agent_rank_id'] = $v['user_rank_id'];
			$temp_rank[$rank_id]  = $v['rank_name'];
		}

		
		$OrderPromotionModel->setStart($Page->firstRow);
		$OrderPromotionModel->setLimit($Page->listRows);
		$r = $OrderPromotionModel->getPromotionList($start_time,$end_time,$type,$isuse);
		#echo $OrderPromotionModel->getLastSql();
		foreach($r as $k=>$v)
		{
			$r[$k]['start_time'] = $v['start_time']?date('Y-m-d H:i ',$v['start_time']):'未指定';
			$r[$k]['end_time'] = $v['end_time']?date('Y-m-d H:i ',$v['end_time']):'未指定';
			$rank_need = explode(',',$v['agent_rank_id']);
			$rank_need_list = '';
			foreach($rank_need as $v)
			{
				$rank_need_list .= $temp_rank[$v].',';
			}
			$r[$k]['rank_need_list'] = rtrim($rank_need_list,',');
		}
		#myprint($r);
		$this->assign('promolist',$r);
		$this->assign('head_title','订单满额优惠');
		$this->display();
	}
	
	/**
     * 添加订单满额打折策略
     * @return void
     * @todo 插入数据到tp_promotion_order_discount表，会员等级写入tp_promotion_order_discount_rank。
     * @todo 优惠幅度，如：打折的话输入0,9，减钱输入30.5;优惠类型，1=打折，2=减钱。添加时要注意之前的数据逻辑，订单金额和优惠幅度之间不能冲突
     */
	public function add_order_discount()
	{
		$act = $this->_post('act');
		if($act == 'add')			//执行添加操作时
		{
			require_once('Lib/Model/OrderPromotionModel.class.php');
			$OrderPromotionModel = new OrderPromotionModel();
			
			$title			= $this->_post('title');		//活动标题
			$order_total	= $this->_post('total');		//需要达到的订单额度
			$discount_type  = $this->_post('dis_type'); 	//类型：1、打折 2、降价
			$discount_total = $this->_post('discount'); 	//促销幅度
			$start_time 	= $this->_post('start_time'); 	//活动开始时间
			$end_time 		= $this->_post('end_time');		//活动结束时间
			$isuse			= $this->_post('used');			//是否启用
			$rank_need		= $this->_post('rank_need'); 	//活动针对的代理商级别
			$redirect		= $this->_get('redirect');
			$redirect	    = $redirect?url_jiemi($redirect):U('/AcpPromo/list_order_discount');
			
 		#	myprint($_POST);
			if(!$title || !$discount_type || !$discount_total)
			{
				$this->error('请完善页面必填信息',get_url());
			}
			if(!in_array($discount_type,array(1,2)))
			{
				$this->error('请选择正确的促销类型',get_url());
			}
			if($discount_type == 1)
			{
				if($discount_total <=0.00 || $discount_total > 1.00)
				{
					$this->error('请选择正确的折扣率(0.01~1.00)',get_url());
				}
			}else{
				if($discount_total < 0)
				{
					$this->error('请选择正确的降价额度(不能小于0)',get_url());
				}
			}
			
			$start_time = $start_time?strtotime($start_time):0;
			$end_time	= $end_time?strtotime($end_time):0;
			$data = array(
					'title'			=> $title,
					'order_total'	=> $order_total,
					'discount_type'	=> $discount_type,
					'discount_total'=> $discount_total,
					'start_time'	=> $start_time,
					'end_time'		=> $end_time,
					'isuse'			=> ($isuse==1)?1:2
			);
			
			$promotion_order_discount_id = $OrderPromotionModel->addPromotion($data);		//执行添加
			
			if($promotion_order_discount_id)
			{
				$false = 0;
				
				if($rank_need && !empty($rank_need))		//如果进行级别限制
				{
					if(!$OrderPromotionModel->setUserRankForOrderDiscount($promotion_order_discount_id,$rank_need)) //执行->设置参加活动需要的级别
					{
						$OrderPromotionModel->disablePromotion($promotion_order_discount_id,2);
						$this->error('对不起2,活动未能成功添加,您可以再尝试一次!',get_url());
					}
				}
				$this->success('恭喜你，活动添加成功','/AcpPromo/list_order_discount');
			}
			else
			{
				$this->error('对不起3,活动未能成功添加,您可以在尝试一次!',get_url());
			}
			exit;
		}
		
        $UserRankModel = new UserRankModel();
        $rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
        $temp_rank = array();
        foreach($rank_list as $k=>$v)
        {
            $rank_id = $v['agent_rank_id'];
            $rank_list[$k]['agent_rank_id'] = $v['user_rank_id'];
            $temp_rank[$rank_id]  = $v['rank_name'];
        }
        
		$this->assign('rank_list',$rank_list);
		$this->assign('action_title', '订单满额优惠'); 
		$this->assign('action_src', '/AcpPromo/list_order_discount');
		$this->assign('head_title','添加订单优惠活动');
		$this->display();
	}
	
	/**
     * 修改订单满额打折策略
     * @author 陆宇峰
     * @return void
     * @todo 修改tp_promotion_order_discount表数据，会员等级写入tp_promotion_order_discount_rank。
     * @todo 优惠幅度，如：打折的话输入0,9，减钱输入30.5;优惠类型，1=打折，2=减钱
     */
	public function edit_order_discount()
	{
		$pi = $this->_get('pi');
		$redirect = $this->_get('redirect');
		$redirect = $redirect?url_jiemi($redirect):'/AcpPromo/list_order_discount';
		if(!$pi)
		{
			$this->error('参数错误',$redirect);
		}
		else
		{
			$promotion_order_discount_id = url_jiemi($pi);
			require_once('Lib/Model/OrderPromotionModel.class.php');
			$OrderPromotionModel = new OrderPromotionModel();
			$promo_info = $OrderPromotionModel->getPromotion($promotion_order_discount_id);
			if(!$promo_info)
			{
				$this->error('不存在的活动',$redirect);
			}
			
			$act = $this->_post('act');			//提交表单进行保存时
			if($act == 'edit')
			{
				$item_list  	= $this->_post('choose');		//哪些商品参加活动
				$title			= $this->_post('title');		//活动标题
				$order_total	= $this->_post('total');		//需要满足的订单额度
				$discount_type  = $this->_post('dis_type'); 	//类型：1、打折 2、降价
				$discount_total = $this->_post('discount'); 	//促销幅度
				$start_time 	= $this->_post('start_time'); 	//活动开始时间
				$end_time 		= $this->_post('end_time');		//活动结束时间
				$isuse			= $this->_post('used');			//是否启用
				$rank_need		= $this->_post('rank_need'); 	//活动针对的代理商级别
				$redirect		= $this->_get('redirect');
				$redirect	= $redirect?url_jiemi($redirect):U('/AcpPromo/list_order_discount');
				
				#myprint($_POST);
				if(!$title || !$discount_type || !$discount_total)
				{
					$this->error('请完善页面必填信息',get_url());
				}
				if(!in_array($discount_type,array(1,2)))
				{
					$this->error('请选择正确的促销类型',get_url());
				}
				$start_time = $start_time?strtotime($start_time):0;
				$end_time	= $end_time?strtotime($end_time):0;
				$data = array(
						'title'			=> $title,
						'order_total'	=> $order_total,
						'discount_type'	=> $discount_type,
						'discount_total'=> $discount_total,
						'start_time'	=> $start_time,
						'end_time'		=> $end_time,
						'isuse'			=> ($isuse==1)?1:2
				);
				
				$OrderPromotionModel->editPromotion($promotion_order_discount_id,$data);		//执行更新
				
				if($rank_need && !empty($rank_need))		//如果进行级别限制
				{
					$OrderPromotionModel->setUserRankForOrderDiscount($promotion_order_discount_id,$rank_need); //设置参加活动需要的级别
				}
				elseif(empty($rank_need))		//取消所有的级别限制
				{
					$OrderPromotionModel->delUserRankForOrderDiscount($promotion_order_discount_id);	
				}
				$this->success('恭喜你，活动编辑成功',$redirect);
				
				exit;
			}
				
			//格式化时间
			$promo_info['start_time'] = $promo_info['start_time']?date('Y-m-d H:i ',$promo_info['start_time']):'';
			$promo_info['end_time'] = $promo_info['end_time']?date('Y-m-d H:i ',$promo_info['end_time']):'';
		}
		
		$this->assign('promo_info',$promo_info);
		
		//获取活动所需要的级别信息
		$rank_need = $OrderPromotionModel->getRankInfoByPromoId($promotion_order_discount_id);
		$this->assign('rank_need',explode(',',$rank_need));
		
		//导入模型
		$UserRankModel = new UserRankModel();
		//获取所有的级别信息
		$rank_list  = $UserRankModel->getUserRankList();      
        foreach($rank_list as $k=>$v)
        {
            $rank_list[$k]['agent_rank_id'] = $v['user_rank_id'];
        }

		$this->assign('rank_list',$rank_list);
		
		$this->assign('action_title', '订单满额优惠');
		$this->assign('action_src', '/AcpPromo/list_order_discount/');
		$this->assign('head_title','编辑订单促销(优惠)');
		$this->display();
	}
	
	/**
     * 删除订单满额优惠(降价打折)策略
     * @return void
     * @todo 删除tp_promotion_order_discount表数据，同时删除tp_promotion_order_discount_rank表promotion_order_discount_id相同的数据
     */
	public function del_order_discount()
	{
		$act		  = $this->_post('act');
		$promo_id = $this->_post('pi');
		if(!$promo_id || $act != 'del')
		{
			exit(json_encode(array('type'=>-1,'message'=>'不合法的请求，操作被拒绝!')));
		}
		$promo_id = url_jiemi($promo_id);
		require_once('Lib/Model/OrderPromotionModel.class.php');
		$OrderPromotionModel = new OrderPromotionModel();
		$OrderPromotionModel->deletePromotion($promo_id);  //删除活动本身
		
		exit(json_encode(array('type'=>1,'message'=>'恭喜你，活动删除成功!')));
	}
	
	/**
     * 订单满额赠送礼品列表
     * @return void
     * @todo 列出tp_promotion_order_gift的数据，礼品详情在tp_promotion_order_gift_detail表，会员等级在tp_promotion_order_gift_rank表
     */
	public function list_order_gift()
	{
		require_once('Lib/Model/OrderPromotionModel.class.php');
		$OrderPromotionModel = new OrderPromotionModel();
		$s 			= $this->_request('s');			//标记是分页数据
		$start_time = $this->_request('t1');
		$end_time	= $this->_request('t2');
		$isuse		= $this->_request('isuse');
		$act 		= $this->_request('act');
		
		$start_time = $start_time?strtotime($start_time):0;
		$end_time	= $end_time?strtotime($end_time):0;
		
		$this->assign('s_isuse',$isuse);
		$this->assign('s_time1',$start_time);
		$this->assign('s_time2',$end_time);
		
		$total = $OrderPromotionModel->countPromoListForOrderGift($start_time, $end_time, $isuse);            //符合条件的总活动数
		#die($OrderPromotionModel->getLastSql());
		#myprint($total);
		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		//获取查询参数
		$map['t1']		= $start_time;
		$map['t2']		= $end_time;
		$map['isuse']	= $isuse;
		$map['s']		= 1;
		
		foreach($map as $k=>$v){
			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);
		
		$UserRankModel = new UserRankModel();
		$rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
		$temp_rank = array();
		foreach($rank_list as $k=>$v)
		{
			$rank_id = $v['agent_rank_id'];
			$temp_rank[$rank_id]  = $v['rank_name'];
		}
		
		$OrderPromotionModel->setStart($Page->firstRow);
		$OrderPromotionModel->setLimit($Page->listRows);
		$r = $OrderPromotionModel->getPromoListForOrderGift($start_time, $end_time, $isuse);
		
		require_once('Lib/Model/GiftModel.class.php');
		$GiftModel = new GiftModel();#myprint($r);
		foreach($r as $k=>$v)
		{
			$r[$k]['small_img']  = $v['base_pic'] ? small_img($v['base_pic']) : '';
			$r[$k]['start_time'] = $v['start_time']?date('Y-m-d H:i ',$v['start_time']):'未指定';
			$r[$k]['end_time']   = $v['end_time']?date('Y-m-d H:i ',$v['end_time']):'未指定';
			$rank_need = explode(',',$v['agent_rank_id']);
			$rank_need_list = '';
			foreach($rank_need as $v0)
			{
				$rank_need_list .= $temp_rank[$v0].',';
			}
			$r[$k]['rank_need_list'] = rtrim($rank_need_list,',');
			$gift_info = $GiftModel->getGiftInfo($v['gift_id']);
			$r[$k]['gift_name']		 = $gift_info['gift_name'];
		}
		
		$this->assign('promo_info',$r);
		
		$this->assign('head_title','订单满额赠礼品');
		$this->display();
	}
	
	/**
     * 添加订单满额赠送礼品
     * @author 陆宇峰
     * @return void
     * @todo 插入tp_promotion_order_gift表，选中的礼品插入tp_promotion_order_gift_detail表，会员等级插入tp_promotion_order_gift_rank表
     */
	public function add_order_gift()
	{
		$act = $this->_post('act');
		if($act == 'add')			//执行添加操作时
		{
			require_once('Lib/Model/OrderPromotionModel.class.php');
			$OrderPromotionModel = new OrderPromotionModel();
				
			$title			= $this->_post('title');		//活动标题
			$order_total	= $this->_post('total');		//需要达到的订单额度
			$gift_id		= $this->_post('gift');			//礼品ID
			$start_time 	= $this->_post('start_time'); 	//活动开始时间
			$end_time 		= $this->_post('end_time');		//活动结束时间
			$isuse			= $this->_post('used');			//是否启用
			$rank_need		= $this->_post('rank_need'); 	//活动针对的代理商级别
			$redirect		= $this->_get('redirect');
			$redirect	    = $redirect?url_jiemi($redirect):U('/AcpPromo/list_order_gift/');
				
			#	myprint($_POST);
			if(!$title || !$order_total || !$gift_id)
			{
				$this->error('请完善页面必填信息',get_url());
			}
			
			$start_time = $start_time?strtotime($start_time):0;
			$end_time	= $end_time?strtotime($end_time):0;
			$data = array(
					'title'			=> $title,
					'order_total'	=> $order_total,
					'start_time'	=> $start_time,
					'end_time'		=> $end_time,
					'isuse'			=> ($isuse==1)?1:2
			);
					#	myprint($_POST);			
 			$promotion_order_gift_id = $OrderPromotionModel->addPromotion($data,2);		//执行添加
					#myprint($promotion_order_gift_id);
					#$promotion_order_gift_id = 9;
			if($promotion_order_gift_id)
			{
				if($rank_need && !empty($rank_need))		//如果进行级别限制
				{
					if(!$OrderPromotionModel->setUserRankForOrderGift($promotion_order_gift_id,$rank_need)) //执行->设置参加活动需要的级别
					{
						$OrderPromotionModel->disableOrderGiftPromotion($promotion_order_gift_id);
						$this->error('对不起2,活动未能成功添加,您可以再尝试一次!',get_url());
					}
				}
				if($gift_id)
				{
					$OrderPromotionModel->setPromotionGift($promotion_order_gift_id,$gift_id);
				}
				$this->success('恭喜你，活动添加成功','/AcpPromo/list_order_gift');
			}
			else
			{
				$this->error('对不起3,活动未能成功添加,您可以在尝试一次!',get_url());
			}
			exit;
		}
		
		$UserRankModel = new UserRankModel();
		$rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
		$temp_rank = array();
		foreach($rank_list as $k=>$v)
		{
			$rank_id = $v['agent_rank_id'];
			$temp_rank[$rank_id]  = $v['rank_name'];
		}
		$this->assign('rank_list',$rank_list);
		
		//获取所有的礼品
		require_once('Lib/Model/GiftModel.class.php');
		$GiftModel = new GiftModel();
		$gift_list = $GiftModel->getGiftList('isuse = 1');
		#myprint($gift_list);
		$this->assign('gift_list',$gift_list);
		
		$this->assign('action_title', '订单满额送礼品'); 
		$this->assign('action_src', '/AcpPromo/list_order_gift');
		$this->assign('head_title','添加订单赠礼品活动');
		$this->display();
	}
	
	/**
     * 修改订单满额赠送礼品
     * @author 陆宇峰
     * @return void
     * @todo 修改tp_promotion_order_gift表，选中的礼品插入tp_promotion_order_gift_detail表，会员等级插入tp_promotion_order_gift_rank表
     */
	public function edit_order_gift()
	{
		$pi	= $this->_get('pi');
		$redirect = $this->_get('redirect');
        $redirect	= $redirect?url_jiemi($redirect):U('/AcpPromo/list_order_gift');
		if(!$pi)
		{
			$this->error('错误的页面请求！',$redirect);
		}
		$promotion_order_gift_id = $pi?url_jiemi($pi):0;

		require_once('Lib/Model/OrderPromotionModel.class.php');
		$OrderPromotionModel = new OrderPromotionModel();
		//获取活动本身的信息
		$promo_info = $OrderPromotionModel->getPromotionForOrderGift($promotion_order_gift_id);
		if(!$promo_info)
		{
			$this->error('对不起，活动不存在！',$redirect);
		}
		
		$act = $this->_post('act');
		if($act == 'edit')			//执行保存操作时
		{
			$title			= $this->_post('title');		//活动标题
			$order_total	= $this->_post('total');		//需要达到的订单额度
			$gift_id		= $this->_post('gift');			//礼品ID
			$start_time 	= $this->_post('start_time'); 	//活动开始时间
			$end_time 		= $this->_post('end_time');		//活动结束时间
			$isuse			= $this->_post('used');			//是否启用
			$rank_need		= $this->_post('rank_need'); 	//活动针对的代理商级别
			$redirect		= $this->_get('redirect');
			$redirect	    = $redirect?url_jiemi($redirect):U('/AcpPromo/list_order_gift');
		
			#	myprint($_POST);
			if(!$title || !$order_total || !$gift_id)
			{
				$this->error('请完善页面必填信息',get_url());
			}
						
			$start_time = $start_time?strtotime($start_time):0;
			$end_time	= $end_time?strtotime($end_time):0;
			$data = array(
					'title'			=> $title,
					'order_total'	=> $order_total,
					'start_time'	=> $start_time,
					'end_time'		=> $end_time,
					'isuse'			=> ($isuse==1)?1:2
			);
				#myprint($data);
			$OrderPromotionModel->editPromotionForOrderGift($promotion_order_gift_id,$data);		//执行更新
			
			$OrderPromotionModel->setUserRankForOrderGift($promotion_order_gift_id,$rank_need); //执行->设置参加活动需要的级别
			if($gift_id)
			{
				$OrderPromotionModel->setPromotionGift($promotion_order_gift_id,$gift_id);
			}
			$this->success('恭喜你，活动添加成功','/AcpPromo/list_order_gift');
			
			exit;
		}
		
		$UserRankModel = new UserRankModel();
		$rank_list  = $UserRankModel->getUserRankList();      //获取所有的级别信息
		$temp_rank = array();
		foreach($rank_list as $k=>$v)
		{
			$rank_id = $v['agent_rank_id'];
			$temp_rank[$rank_id]  = $v['rank_name'];
		}
		$this->assign('rank_list',$rank_list);
		
		//获取所有的礼品
		require_once('Lib/Model/GiftModel.class.php');
		$GiftModel = new GiftModel();
		$gift_list = $GiftModel->getGiftList('isuse = 1');
		$this->assign('gift_list',$gift_list);
		
		$promo_info['start_time']	= $promo_info['start_time']?date('Y-m-d H:i ',$promo_info['start_time']):'';
		$promo_info['end_time']		= $promo_info['end_time']?date('Y-m-d H:i ',$promo_info['end_time']):'';
		$this->assign('promo_info',$promo_info);
		
		$this->assign('rank_need',explode(',',$promo_info['agent_rank_id']));
		
		$this->assign('action_title', '订单满额送礼品');
		$this->assign('action_src', '/AcpPromo/list_order_gift');
		$this->assign('head_title','编辑订单促销活动(满额送礼)');
		$this->display();
	}
	
	/**
     * 删除订单满额赠送礼品
     * @return void
     * @todo 删除tp_promotion_order_gift表，以及tp_promotion_order_gift_detail、tp_promotion_order_gift_rank表中promotion_order_gift_id=当前的数据
     */
	public function del_order_gift()
	{
		$promotion_item_order_id  = $this->_post('pi');
		$act 					 = $this->_post('act');
		
		if(!$act || $act != 'del' || !$promotion_item_order_id)
		{
			exit(json_encode(array('type'=>-1,'message'=>'非法请求，操作被拒绝！')));
		}
		require_once('Lib/Model/OrderPromotionModel.class.php');
		$OrderPromotionModel = new OrderPromotionModel();
		$promotion_item_order_id = url_jiemi($promotion_item_order_id);
		if($OrderPromotionModel->delPromotionForOrderGift($promotion_item_order_id))
		{
			exit(json_encode(array('type'=>1,'message'=>'恭喜您,活动成功删除了！')));
		}
		else
		{
			exit(json_encode(array('type'=>-1,'message'=>'对不起，删除有误！')));
		}
	}
	
	/**
     * 设置新人下单额外的折扣
     * @author 陆宇峰
     * @return void
     * @todo 改变config表中new_user_discount_status（新人下的第1单折扣是否开启）
     * @todo 改变config表中new_user_discount_total（新人下的第1单额外折扣率，100为不打折，95为95折扣） 这2行记录
     */
	public function set_new_user_discount()
	{
		$act = $this->_post('act');
		if($act == 'save')		//提交保存时
		{
			$new_user_discount_total		= intval($this->_post('discount_total'));
			$new_user_discount_status		= $this->_post('new_user_discount_open');
			$new_user_discount_status 		= $new_user_discount_status?1:0;
			
			if($new_user_discount_total == '' || $new_user_discount_total < 1 || $new_user_discount_total > 100)
			{
				$this->error('对不起,折扣率设置错误，请输入一个1-100的整数。',get_url());
			}
			
			$data = array(
					'new_user_discount_status'			=>	$new_user_discount_status,
					'new_user_discount_total'			=>	$new_user_discount_total
			);
			require_once('Lib/Model/ConfigBaseModel.class.php');
			$ConfigBaseModel = new ConfigBaseModel();
			$ConfigBaseModel->setConfigs($data);
			$this->success('恭喜你，参数设置成功了!',get_url());
		}
		
		$config = $this->system_config;
		#myprint($config);
		$this->assign('config',$config);
		$this->assign('head_title','新人下单打折');
		$this->display();
	}
	
		
	/**
     * 营销效果对比
     * @return void
     * @todo 取出多种营销方式，在指定的时间段内对应订单的数量。营销方式以现有的几个大类为准。从
     */
	public function chart_promos_result()
	{
		require_once('Lib/Model/AgentApplyModel.class.php');
		$AgentApplyModel = new AgentApplyModel();
		/************* 按月份统计 *****************/
		$time1 = $this->_post('t1');
		$time2 = $this->_post('t2');
		$act = $this->_post('act');
		
		$time1 = $time1?strtotime($time1):0;
		$time2 = $time2?strtotime($time2):0;
		$this->assign('s_time1',$time1);
		$this->assign('s_time2',$time2);
		
		require_once('Lib/Model/OtherOrderPromoModel.class.php');
		$OtherOrderPromoModel = new OtherOrderPromoModel();
		$r = $OtherOrderPromoModel->getPromosResult($time1,$time2);
		#$this->assign('promos_result',$r);
		#myprint($r);
		//促销的类型：1指定商品直接优惠；2买商品送礼品；3订单满额直接优惠；4满额送礼品；5满额减运费；6新人下单折扣
		$type = array(
				1	=>	'指定商品直接优惠',
				2	=>	'买商品送礼品',
				3	=>	'订单满额直接优惠',
				4	=>	'订单满额送礼品',
				5	=>	'订单满额减运费',
				6	=>	'新人下单折扣'
		);
		$total_info = array();		//每一种促销类型下
		foreach($type as $k0=>$v0)
		{
			$total_info[$v0] = 0;
		}
		foreach($r as $k=>$v)
		{
			$promotion_type = $v['promotion_type'];	//活动类型
			$type_desc = $type[$promotion_type];	//活动类型的描述
			$total_info[$type_desc] = $v['total'];	//该类型下的订单总数
		}
		$this->assign('total_info',$total_info);
		#myprint($total_info);
		$this->assign('head_title','营销方式效果对比');
		$this->display();
	}

    // 获取优惠券列表
    // @author wsq
    public function coupon_list()
    {

		$coupon_obj = new CouponModel();

        $where  = ' 1 ';

        $coupon_name  = $this->_post("coupon_name");
        $coupon_state = $this->_post("apply_state");
        $user_name    = $this->_post("user_name");

        if ($coupon_name) {
            $where .= ' AND coupon_name LIKE "%' . $coupon_name . '%"';
            $this->assign("coupon_name", $coupon_name);
        }
        if (is_numeric($coupon_state) && $coupon_state >=0) {
            $where .= " AND state =" . $coupon_state ;
            $this->assign("state", $coupon_state);
        }

        if ($user_name)  {
            $user_obj     = new UserModel();
            $realname_ids = $user_obj->getUserIdByRealname($user_name);

            if ($realname_ids) $where .= " AND user_id IN (" . $realname_ids . ")";
            else $where .= " AND 0 ";

            $this->assign("user_name", $user_name);
        }

		//分页处理
        import('ORG.Util.Pagelist');
		$total = $coupon_obj->getCouponNum($where);   
        $Page  = new Pagelist($total,$this->item_num_per_coupon_page);
		$coupon_obj->setStart($Page->firstRow);
        $coupon_obj->setLimit($Page->listRows);
        $show  = $Page->show();
		$this->assign('show', $show);

		$coupon_list = $coupon_obj->getCouponList('', $where, 'addtime DESC');
		$coupon_list = $coupon_obj->getListData($coupon_list);
   		
		$this->assign('coupon_list', $coupon_list);
		$this->assign('total', $total);
		$this->assign('head_title', '优惠券');
        $this->display();
    }

    //删除优惠券
    //@author wsq
	public function del_coupon() {
		$coupon_id = intval($this->_post('id'));
		if ($coupon_id) {
			$coupon_obj = new CouponModel($coupon_id);
			$success = $coupon_obj->delCoupon($coupon_id);
			exit($success ? 'success' : 'failure');
		}

		exit('failure');
	}

    //批量删除优惠券
    //@author wsq
    public function batch_delete_coupon() {

        $coupon_ids = $this->_post('ids');

        if ($coupon_ids) {
            $ids_arry = explode(',', $coupon_ids);
            $success_num = 0;
            $coupon_obj = new CouponModel();

            foreach ($ids_arry AS $coupon_id)
            {
                $success_num += $coupon_obj->delCoupon($coupon_id);
            }
            echo $success_num ? 'success' : 'failure';
            exit;
        }

        exit('failure');
    }

    //修改优惠券
    //@author wsq
    public function edit_coupon() 
    {
        $coupon_id = intval($this->_get('coupon_id'));
        //检查校验优惠券号
        if (!$coupon_id) $this->error('优惠券号码有误');

		$coupon_obj    = new CouponModel($coupon_id);
        $coupon_info   = $coupon_obj->getCouponInfo();

        if (!$coupon_info) $this->error('优惠券信息不存在');

		$act = $this->_post('act');

		if($act == 'edit') {
			$_post         = $this->_post();
            $coupon_value  = $_post['coupon_value'];
            $coupon_limit  = $_post['coupon_limit'];
            $day_left      = $_post['day_left'];
			
			//表单验证
            if(!ctype_digit($coupon_value)) {
				$this->error('请填写优惠券面额！');
			}

			if(!ctype_digit($coupon_limit)) {
				$this->error('请填写优惠券使用限制！');
			}

			if(!ctype_digit($day_left)) {
				$this->error('请填写正确的有效期！');
			}

            $data_array = array(
				'num'	        => $coupon_value,
				'price_limit'	=> $coupon_limit,
				'deadline'		=> strtotime("+" . $day_left . " day"),
            );

			if ($coupon_obj->editCoupon($data_array)) {
				$this->success('恭喜您，信息修改成功！') ;
			} else {
				$this->error('抱歉，信息修改失败！');
			}

		}

        $coupon_info = $coupon_obj->getListData(array($coupon_info));

        $this->assign('coupon_info', $coupon_info[0]);
        $this->assign('head_title', '优惠券信息修改');

        $this->display();
    }

    //分发优惠券
    //@author wsq
    public function dispatch_coupon() 
    {
		$user_id       = intval($this->_post('user_id'));
		$num           = intval($this->_post('num'));
		$valid_day_num = intval($this->_post('valid_day_num'));
        $coupon_name   = $this->_post('coupon_name');
        $price_limit   = $this->_post('price_limit');

		if ($user_id && $num && $valid_day_num && $coupon_name && $price_limit)
		{
			$coupon_obj = new CouponModel();
            $coupon_id  = $coupon_obj->addCoupon(
                array(
                    'num'			=> $num,
                    'addtime'		=> time(),
                    'deadline'		=> strtotime("+" . $valid_day_num . "day"),
                    'user_id'		=> $user_id,
                    'coupon_name'	=> $coupon_name,
                    'price_limit'	=> $price_limit,
                    'activity_type'	=> 4,
                )
            );

			echo $coupon_id ? 'success' : 'failure';
			exit;
		}
		exit('failure');
    
    }

    //分发优惠券
    //@author wsq
    public function batch_dispatch_coupon() 
    {
		$user_ids      = $this->_post('user_ids');
		$num           = intval($this->_post('freight_num'));
		$valid_day_num = intval($this->_post('valid_day_num'));
        $coupon_name   = $this->_post('coupon_name');
        $price_limit   = $this->_post('price_limit');

		if ($user_ids && $num && $valid_day_num)
		{
			$coupon_obj = new CouponModel();
			$i = 0;
			$user_ids = explode(',', $user_ids);
			foreach ($user_ids AS $key => $user_id)
			{
				if ($user_id)
				{
					$arr = array(
                        'num'			=> $num,
                        'addtime'		=> time(),
                        'deadline'		=> strtotime("+" . $valid_day_num . "day"),
                        'user_id'		=> $user_id,
                        'coupon_name'	=> $coupon_name,
                        'price_limit'	=> $price_limit,
                        'activity_type'	=> 4,
					);
                    $coupon_id  = $coupon_obj->addCoupon($arr);
					$i += $coupon_id;
				}
			}
            
            echo $i > 0 ? "success":"failure";
			exit;
		}

		echo 'failure';
		exit;
    
    }

    /**
	 * @access public
     * @todo 最大牌的配置数据
     * @return void
     * @author zlf
     */
	public function major_config()
	{
		$act = $this->_post('act');
		if($act == 'save')		//提交保存时
		{
			$major_title  = $this->_post('major_title');
			$major_link = $this->_post('major_link');			
            $major_pic   = $this->_post('major_pic'); 


			if(!$major_title || $major_title == '')
			{
				$this->error('对不起,标题不能为空！',get_url());
			}

			if(!$major_link || $major_link == '')
			{
				$this->error('对不起,链接不能为空！',get_url());
			}

						
			$data = array(
					'major_title'						=>	$major_title,
					'major_link'	=>	$major_link,					
                    'major_pic'         => $major_pic,

			);
			
			$ConfigBaseModel = new ConfigBaseModel();
			$ConfigBaseModel->setConfigs($data);
			$this->success('恭喜你，配置成功了!', '/AcpPromo/major_config/mod_id/7');
		}
		 //用户登录大图
        $major_pic_path = $this->system_config['MAJOR_PIC']? APP_PATH . $this->system_config['MAJOR_PIC']: false;
        if($major_pic_path) $this->assign('major_pic_path', $major_pic_path);
		
	
		$config = $this->system_config;
		$this->assign('head_title','今日最大牌');
		$this->assign('config',$this->system_config);
		$this->display();
	}

}
?>

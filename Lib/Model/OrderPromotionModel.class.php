<?php

/**
*  订单促销管理模型 
*  @ Project : Untitled
*  @ File Name : OrderPromotionModel.class.php
*  @ Date : 2014/2/27
*  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
*/




/** */
class OrderPromotionModel extends PromotionBaseModel
{

    /** 
     * @access public
     * @todo 根据商品ID串计算当前类型促销优惠信息
     * 
     * 
     */
    public function getCouponInfoByItems()

    {
    
    }

    /** 
     * @access public
     * @todo 根据订单号计算当前类型促销优惠信息
     * 
     */
    public function getCouponInfoByOrderId()
    {
    
    }

    /** 
     * @access public
     * @todo 根据当前订单总价来计算降价打折信息
     * @param float $total_price 当前订单的总价格。必须
     * @param int $user_rank  用户等级。 默认用户等级为1
     * @param int $time 计算时的时间。默认参数为NULL，为NULL时按照当前系统时间进行计算
     */
    public function getCouponInfoByShoppingTotalPrice($total_price, $user_rank=1, $time=NULL)
    {
        if(!$total_price)
        {
            return FALSE;
        }
        $time = ($time)?$time:time();
        $temp_price =array();
        
        /*
          //此处还需要计算购买商品组合的优惠信息，传入当前总价和所购商品（组合）。计算后得到新的总价，然后用来新的总价对订单促销进行计算
          
          require_once('Lib/Model/BuyItemsGiveGiftModel.class.php');        //购买商品组合获得的优惠信息
          $BuyItemsGiveGiftModel = new BuyItemsGiveGiftModel();
        */
        
        $time = time();
        $order_discount = M('promotion_order_discount');
        $r = $order_discount->where('order_total <= '.$total_price.' AND isuse = 1')->select();      //获取方案
        if(!$r)                         //没有打折类的活动
        {
         	return array('new_price'=>$total_price,'promotion'=>array());
        }
        foreach($r as $k=>$v)
        {
        	if($v['start_time'])              //如果活动有设置起始时间
        	{
        		if($v['start_time'] > $time)        //如果活动还没开始
        		{
        			continue;
        		}
        	}
        	if($v['end_time'])                //如果活动设置了结束时间
        	{
        		if($v['end_time'] < $time)          //如果该活动已经结束
        		{
        			continue;
        		}
        	}
        	//判断用户的等级是否可以参加本次活动
        	$rank_need = $this->getRankInfoByPromoId($v['promotion_order_discount_id']);
        	if($rank_need)          //如果设置了会员等级限制
        	{
        		$temp_rank_arr = explode(',',$rank_need);          //存放可以参加本次活动的级别ID
        		if(!in_array($user_rank, $temp_rank_arr))       //当前用户不能参与本次活动
        		{
        			continue;
        		}
        	}
        	//到这里，说明订单总额可以参加本次活动
        	//计算参加该活动后的价格
        	if($v['discount_type'] == 2)
        	{
        		$new_price = sprintf('%0.2f',$total_price - $v['discount_total']);
        	}
        	else if($v['discount_type'] == 1)
        	{
        		$new_price = sprintf('%0.2f',$total_price * $v['discount_total']);
        	}
        	$temp_arr[] = array('new_price'=>$new_price,'promotion'=>$v);
    	}
//     	$temp_price[] = $this->calculateDiscount($total_price, $user_rank, $time);           //获取参加各种打折活动后的最低总价格
//     	$temp_price[] = $this->calculateReducePrice($total_price, $user_rank, $time);        //获取参加各种减价活动后的最低总价格
    	#sort($temp_price);
    	$tmp_price = array();
    	$tmp_promo = array();
    	foreach($temp_arr as $k=>$v)
    	{
    		$tmp_price[] = $v['new_price'];
    		$tmp_promo[] = $v['promotion'];
    	}	
    	array_multisort($tmp_price,SORT_ASC,$temp_arr);
    	#myprint($temp_arr);
    	return $temp_arr[0];
    }
    /************** 本类自定义方法 ****************/
     /**
     * @todo 根据订单打折降价活动的活动ID获取活动的参与等级条件
     * @param int  $promo_id 活动ID
     * @return 
     */
    public function getRankInfoByPromoId($promo_id)
    {
        if($promo_id <= 0)
        {
            return FALSE;
        }
        $order_discount_rank = M('promotion_order_discount_rank');
        $r = $order_discount_rank->where('promotion_order_discount_id = '.$promo_id)->getField('agent_rank_id');
        if($r)
        {
            return $r;
        }
        return FALSE;
    }
    
    /**
     * @access public
     * @todo 根据（订单或者购物车）当前总价格计算并获取参加“打折”活动后的总价
     * @param float $totalprice 为参加打折活动前的价格
     * @param int $user_rank    用户等级
     * @param int $time         计算订单价格时的时间戳
     * @return float(or)bool  返回价格或者FALSE
     */
    private function calculateDiscount($totalprice, $user_rank, $time)
    {
        $new_price = $totalprice;   //待返回的价格
        if(!$totalprice)
        {
            return FALSE;
        }
        $temp_arr_price = array();          //临时存放参加各种活动后的价格
        $temp_arr_price[] = $new_price;  
        $time = time();
        $order_discount = M('promotion_order_discount');
        $r = $order_discount->where('order_total <= '.$totalprice.' AND discount_type = 1 AND isuse = 1')->select();      //获取打折类的方案
        if(!$r)                         //没有打折类的活动
        {
            return $new_price;
        }
        foreach($r as $k=>$v)
        {
            if($v['start_time'])              //如果活动有设置起始时间
            {
                if($v['start_time'] > $time)        //如果活动还没开始
                {
                    continue;
                }
            }
            if($v['end_time'])                //如果活动设置了结束时间     
            {
                if($v['end_time'] < $time)          //如果该活动已经结束
                {
                    continue;
                }
            }
            //判断用户的等级是否可以参加本次活动
            $rank_need = $this->getRankInfoByPromoId($v['promotion_order_discount_id']);
            if($rank_need)          //如果设置了会员等级限制
            {   
                $temp_rank_arr = array();          //存放可以参加本次活动的级别ID
                foreach($rank_need as $k0=>$v0)
                {
                    $temp_rank_arr[] = $v0['agent_rank_id'];
                }
                if(!in_array($user_rank, $temp_rank_arr))       //当前用户不能参与本次活动
                {
                    continue;
                }
            }
            //到这里，说明订单总额可以参加本次活动
            //计算参加该活动后的价格
            $temp_arr_price[] = sprintf('%0.2f',$totalprice * $v['discount_total']);
//             echo '<pr>';print_r($temp_arr_price);
//             $zt = array();
        }
         echo '<pre>';print_r($temp_arr_price);
//         sort($temp_arr_price);      //升序排序
        return $temp_arr_price[0];  //返回最低的价格
    }
    
    
    /**
     * @access public
     * @todo 根据（订单或者购物车）当前总价格计算获取参加“减价”活动后的总价
     * @param float $totalprice 为参加打折活动前的价格
     * @param int $user_rank    用户等级
     * @param int $time         计算订单价格时的时间戳
     * @return float(or)bool  返回价格或者FALSE
     */
    public  function calculateReducePrice($totalprice,$user_rank,$time)
    {
        $new_price = $totalprice;   //待返回的价格
        if(!$totalprice)
        {
            return FALSE;
        }
        $temp_arr_price = array();          //临时存放参加各种活动后的价格
        $temp_arr_price[] = $new_price;  
        
        $order_discount = M('promotion_order_discount');
        $r = $order_discount->where('order_total <= '.$totalprice.' AND discount_type = 2 AND isuse = 1')->select();      //获取降价类的方案
        if(!$r)                         //没有降价类的活动
        {
            return $new_price;
        }
        $time = time();
        foreach($r as $k=>$v)
        {
            if($v['start_time'])              //如果活动有设置起始时间
            {
                if($v['start_time'] > $time)        //如果活动还没开始
                {
                    continue;
                }
            }
            if($v['end_time'])                //如果活动设置了结束时间     
            {
                if($v['end_time'] < $time)          //如果该活动已经结束
                {
                    continue;
                }
            }
            //判断用户的等级是否可以参加本次活动
            $rank_need = $this->getRankInfoByPromoId($v['promotion_order_discount_id']);
            if($rank_need)          //如果设置了会员等级限制
            {    
                $temp_rank_arr = explode(',',$rank_need);          //存放可以参加本次活动的级别ID
                if(!in_array($user_rank, $temp_rank_arr))       //当前用户不能参与本次活动
                {
                    continue;
                }
            }
            //到这里，说明订单总额可以参加本次活动
            //计算参加该活动后的价格
            $temp_arr_price[] = sprintf('%0.2f',$totalprice - $v['discount_total']);
        }
        echo '<pre>';print_r($temp_arr_price);
        sort($temp_arr_price);      //升序排序
        return $temp_arr_price[0];  //返回最低的价格
    }
    
    /**
     * @access public
     * @todo 根据（购物车或者订单）总价来获取订单活动的推荐信息
     * @param string $total_amount 当前总价。默认为0，表示购物车中为空或订单总额为空
     * @param int $user_rank 用户级别。默认为0，表示是游客
     */
    
    public function getOrderPromotionRecommend($total_amount=0,$user_rank=0,$more=false)
    {
    	$order_discount = M('promotion_order_discount');
    	$total_amount = $total_amount?$total_amount:0.00;
    	
    	$main_table = C('DB_PREFIX').'promotion_order_discount';
    	$rank_table	= C('DB_PREFIX').'promotion_order_discount_rank';
    	
    	$r = $order_discount->join($rank_table.' AS r ON r.promotion_order_discount_id = '.$main_table.'.promotion_order_discount_id')	
    						->where($main_table.'.order_total >= '.$total_amount.' AND '.$main_table.'.isuse = 1')
    						->field($main_table.'.*,r.agent_rank_id')
    						->select();      //获取打折类的方案
    	#myprint($r);
    	$time = time();
    	$result = array();
    	foreach($r as $k=>$v)
    	{
    		if($v['isuse'] != 1)	//活动不可用
    		{
    			continue;
    		}
    		
    		if($v['start_time'])		//活动设置了起始时间
    		{
    			if($time < $v['start_time'])	//活动未开始
    			{
    				continue;
    			}
    		}
    		if($v['end_time'])
    		{
    			if($time > $v['end_time'])		//活动已经结束
    			{
    				continue;
    			}
    		}
    		
    		if($v['agent_rank_id'])		//设置了等级限制
    		{
    			$rank_arr = explode(',',$v['agent_rank_id']);
    			if(!in_array($user_rank,$rank_arr))		//该用户不能参加本活动
    			{
    				continue;
    			}
    		}
    		$result[] = $v;
    	}
    	
    	if($more)		//在商品详情页展示推荐,这里取2条
    	{
    		return array_slice($result, 0,2);
    	}
    	else	
    	{
    		return array_slice($result, 0,1);
    	}
    	
    	
    }
    
    /**
     * @access public
     * @todo 根据某一种订单促销规则（ID）获取参与该活动后的总价
     * @param $promo_id 订单促销规则ID。必须
     * @param $total_price 当前总价。   必须
     * @param $user_rank   用户等级。   必须
     * @return float(or)bool 返回计算后的总价,参数错误时将返回FALSE
     */
    public function getNewPriceAfterDiscountPromo($promo_id, $total_price, $user_rank)
    {
        if(!$promo_id || !$total_price)
        {
            return FALSE;
        }
        $new_price = $total_price;      //要返回的数据
        //判断用户的等级是否可以参加本次活动
        $rank_need = $this->getRankInfoByPromoId($promo_id);
        if($rank_need)          //如果设置了会员等级限制
        {   
        	$now_rank = explode(',',$rank_need);
            if(!in_array($user_rank,$now_rank))        //该用户等级未能参与到本次活动中
            {
                return $new_price;
            }
        }
        $time = time();
        $order_discount = M('promotion_order_discount');
        $r = $order_discount->where('promotion_order_discount_id = '.$promo_id.' AND isuse = 1')->select();      //获取降价类的方案
        if($r)                          //如果规则存在且当前可用
        {
            if($r['start_time'])        //设置了起始时间
            {
                if($r['start_time'] > $time())      //活动还未开始
                {
                    return $new_price;
                }
            }
            if($r['end_time'])          //设置了结束时间
            {
                if($r['end_time'] < $time())    //活动已经结束
                {
                    return $new_price;
                }
            }
            //执行到这里，说明用户可以参与本次促销
            if($r['discount_type'] == 1)            //如果是打折活动
            {
                $new_price = sprintf('%0.2f',$new_price * $r['discount_total']);
            }
            else                                    //否则是减价活动
            {
                $new_price = sprintf('%0.2f',$new_price - $r['discount_total']);
            }
        }
        return $new_price;
    } 
    


    /**
     * @access public
     * @todo 统计所有的订单(降价打折)促销方案（总数）
     * @param int $start_time 活动起始时间
     * @param int $end_time 活动结束时间
     * @param int $type 活动类型(打折  or 减价)
     * @param int $isuse 活动状态
     * @return 查询结果或者0
     */
    public function countPromotion($start_time=0, $end_time=0, $type=0, $isuse=0)
    {
    		$this->trueTableName = C('DB_PREFIX').'promotion_order_discount';
    		$rank_table = C('DB_PREFIX').'promotion_order_discount_rank';
    		$where = '';
    		if($start_time)
    		{
    			$where .= $this->trueTableName.'.start_time >= '.$start_time;
    		}
    		if($end_time)
    		{
    			if($where != '')
    			{
    				$where .= ' AND ';
    			}
    			$where .= $this->trueTableName.'.end_time <= '.$end_time;
    		}
    		if($type)
    		{
    			if($where != '')
    			{
    				$where .= ' AND ';
    			}
    			$where .= $this->trueTableName.'.discount_type = '.$type;
    		}
    		if($isuse)
    		{
    			if($where != '')
    			{
    				$where .= ' AND ';
    			}
    			$where .= $this->trueTableName.'.isuse = '.$isuse;
    		}
    		 
    		$total = $this->where($where)
    				  	  ->join($rank_table.' as r ON '.$this->trueTableName.'.promotion_order_discount_id = r.promotion_order_discount_id')->field($this->trueTableName.'.*,r.agent_rank_id')
    				  	  ->order($this->trueTableName.'.promotion_order_discount_id DESC')
    				  	  ->count();
    		#echo $this->getLastSql();
    	return $total?$total:0;
    }
    
    /**
     * @access public
     * @todo 获取所有的订单(降价打折)促销方案（列表）
     * @param int $start_time 活动起始时间
     * @param int $end_time 活动结束时间
     * @param int $type 活动类型(打折  or 减价)
     * @param int $isuse 活动状态
     * @return 查询结果或者FALSE
     */
    public function getPromotionList($start_time=0, $end_time=0, $type=0, $isuse=0)
    {
    		$this->trueTableName = C('DB_PREFIX').'promotion_order_discount';
    		$rank_table = C('DB_PREFIX').'promotion_order_discount_rank';
    		$where = '';
    		if($start_time)
    		{
    			$where .= $this->trueTableName.'.start_time >= '.$start_time;
    		}
    		if($end_time)
    		{
    			if($where != '')
    			{
    				$where .= ' AND ';
    			}
    			$where .= $this->trueTableName.'.end_time <= '.$end_time;
    		}
    		if($type)
    		{
    			if($where != '')
    			{
    				$where .= ' AND ';
    			}
    			$where .= $this->trueTableName.'.discount_type = '.$type;
    		}
    		if($isuse)
    		{
    			if($where != '')
    			{
    				$where .= ' AND ';
    			}
    			$where .= $this->trueTableName.'.isuse = '.$isuse;
    		}
    		 
    		$r = $this->where($where)
    				  ->join($rank_table.' as r ON '.$this->trueTableName.'.promotion_order_discount_id = r.promotion_order_discount_id')->field($this->trueTableName.'.*,r.agent_rank_id')
    				  ->limit()
    				  ->order($this->trueTableName.'.promotion_order_discount_id DESC')
    				  ->select();
    		#echo $this->getLastSql();
    		return $r?$r:FALSE;
    }
    
    
    /**
     * @access public
     * @todo 为订单促销方案(降价打折)设置会员等级限制(本函数同时整合了添加和编辑功能)
     * @param int $promotion_order_discount_id 活动的ID
     * @param array $user_rank 要设置的用户级别ID组成的索引数组
     * @return bool 操作成功返回TRUE;否则返回FALSE;
     */
    public function setUserRankForOrderDiscount($promotion_order_discount_id, $user_rank)
    {
        if(!$promotion_order_discount_id || !$user_rank || empty($user_rank))
        {
            return FALSE;
        }
        $order_discount_rank = M('promotion_order_discount_rank');

        $data = array(
            'promotion_order_discount_id'   =>  $promotion_order_discount_id,
        	'agent_rank_id'                 =>  implode(',',$user_rank),
        );

        $r = $order_discount_rank->where('promotion_order_discount_id = '.$promotion_order_discount_id)->select();            //查询该活动是否已经设置过会员等级限制

        if(!$r)             //如果没有设置，则新增
        {
        	$order_discount_rank->add($data);
        	return TRUE;
        }
        else                //如果该活动已经有会员等级限制。则执行更新
        {
            $order_discount_rank->where('promotion_order_discount_id = '.$promotion_order_discount_id)->save($data);
            return TRUE;
        }
        return FALSE;
       
    }
    
    
    /**
     * @access public
     * @todo 统计所有的订单促销方案(送礼)总数
     * @param int $start_time 活动起始时间
     * @param int $end_time 活动结束时间
     * @param int $type 活动类型(打折  or 减价)
     * @param int $isuse 活动状态
     * @return 查询结果
     */
    public function countPromoListForOrderGift($start_time=0, $end_time=0, $isuse=0)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_order_gift';
    	$rank_table = C('DB_PREFIX').'promotion_order_gift_rank';
    	$where = '';
    	if($start_time)
    	{
    		$where .= $this->trueTableName.'.start_time >= '.$start_time;
    	}
    	if($end_time)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $this->trueTableName.'.end_time <= '.$end_time;
    	}
    	if($isuse)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $this->trueTableName.'.isuse = '.$isuse;
    	}
    
    	$total = $this->where($where)
    				  ->join($rank_table.' as r ON '.$this->trueTableName.'.promotion_order_gift_id = r.promotion_order_gift_id')->field($this->trueTableName.'.*,r.agent_rank_id')
    				  ->order($this->trueTableName.'.promotion_order_gift_id DESC')
    				  ->count();
    	#echo $this->getLastSql();
    	return $total?$total:FALSE;
    }
    
    /**
     * @access public 
     * @todo 获取所有的订单促销方案(送礼)列表
     * @param int $start_time 活动起始时间
     * @param int $end_time 活动结束时间
     * @param int $type 活动类型(打折  or 减价)
     * @param int $isuse 活动状态  
     * @return 查询结果
     */
    public function getPromoListForOrderGift($start_time=0, $end_time=0, $isuse=0)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_order_gift';
    	$rank_table = C('DB_PREFIX').'promotion_order_gift_rank';
    	$gift_table = C('DB_PREFIX').'promotion_order_gift_detail';
    	
    	$where = '';
    	if($start_time)
    	{
    		$where .= $this->trueTableName.'.start_time >= '.$start_time;
    	}
    	if($end_time)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $this->trueTableName.'.end_time <= '.$end_time;
    	}
    	if($isuse)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $this->trueTableName.'.isuse = '.$isuse;
    	}
    	 
    	$r = $this->where($where)
    			  ->join($rank_table.' as r ON '.$this->trueTableName.'.promotion_order_gift_id = r.promotion_order_gift_id')
    			  ->join($gift_table.' as g ON '.$this->trueTableName.'.promotion_order_gift_id = g.promotion_order_gift_id')
    			  ->field($this->trueTableName.'.*,r.agent_rank_id,g.gift_id')
    			  ->order($this->trueTableName.'.promotion_order_gift_id DESC')
    			  ->limit()
    			  ->select();
    	#echo $this->getLastSql();
    	return $r?$r:FALSE;
    }
    
    /**
     * @access public
     * @todo 根据订单促销方案ID获取该促销方案详细信息(满额送礼活动)
     * @param int $promotion_order_gift_id 活动ID
     */
    public function getPromotionForOrderGift($promotion_order_gift_id)
    {
    	$order_gift = M('promotion_order_gift');
    	$main_table	= C('DB_PREFIX').'promotion_order_gift';
    	$gift_table = C('DB_PREFIX').'promotion_order_gift_detail';
    	$rank_table = C('DB_PREFIX').'promotion_order_gift_rank';
    	
    	if(!$promotion_order_gift_id)
    	{
    		return FALSE;
    	}
    	
    	$r = $order_gift->where($main_table.'.promotion_order_gift_id = '.$promotion_order_gift_id)
    						->join($gift_table.' as g ON g.promotion_order_gift_id = '.$main_table.'.promotion_order_gift_id')
    						->join($rank_table.' as r ON r.promotion_order_gift_id = '.$main_table.'.promotion_order_gift_id')
    						->field($main_table.'.*,g.gift_id,r.agent_rank_id')
    						->find();
    	return $r?$r:FALSE;
    }
    
    /**
     * @access public
     * @todo 修改促销方案
     * @param int $promoid  订单促销活动的活动ID(降价打折的活动)
     * @param array 数据组装好的数组
     * @return bool 成功返回TRUE;否则返回FALSE
     */
    public function editPromotionForOrderGift($promoid,$data)
    {
    	if(!$promoid || empty($data))
    	{
    		return FALSE;
    	}
    	$order_gift = M('promotion_order_gift');
    	$order_gift->where('promotion_order_gift_id = '.$promoid)->save($data);
    	#die($order_discount->getLastSql());
    	return TRUE;
    }
    
    /**
     * @access public
     * @todo 删除订单(送礼)活动
     * @param int $promoid 订单送礼活动ID。必须
     * 
     */
    public function delPromotionForOrderGift($promoid)
    {
    	if(!$promoid)
    	{
    		return FALSE;
    	}
    	$order_gift = M('promotion_order_gift');
    	return $order_gift->where('promotion_order_gift_id = '.$promoid)->delete();
    }
    
    /**
     * @access public
     * @todo 为订单促销方案(送礼品)设置会员等级限制(本函数同时整合了添加和编辑功能)
     * @param int $promotion_order_gift_id 活动的ID。必须
     * @param array $user_rank 要设置的用户级别ID组成的索引数组。必须
     * @return bool 操作成功返回TRUE;否则返回FALSE;
     */
    public function setUserRankForOrderGift($promotion_order_gift_id, $user_rank)
    {
    	if(!$promotion_order_gift_id)
    	{
    		return FALSE;
    	}
    	$order_discount_rank = M('promotion_order_gift_rank');
    	$data = array(
    			'promotion_order_gift_id'   =>  $promotion_order_gift_id
    	);
    	$r = $order_discount_rank->where('promotion_order_gift_id = '.$promotion_order_gift_id)->select();            //查询该活动是否已经设置过会员等级限制
    	if(!$r)             //如果没有设置，则新增
    	{
    		$data['agent_rank_id'] = implode(',',$user_rank);
    		$order_discount_rank->add($data);
    		return TRUE;
    	}
    	else                //如果该活动已经有会员等级限制。则执行更新
    	{
    		$temp_data['agent_rank_id'] = implode(',',$user_rank);
    		$order_discount_rank->where('promotion_order_gift_id = '.$promotion_order_gift_id)->save($temp_data);
    		return TRUE;
    	}
    	return FALSE;
    	 
    }
    
    
    /**
     * @access public 
     * @todo 为订单（送礼）活动添加（编辑）礼品信息
     * @param int $promotion_item_discount_id 订单送礼活动的活动ID。必须
     * @param int $gift_id 礼品ID
     * @return bool 
     */
    public function setPromotionGift($promotion_item_discount_id, $gift_id)
    {
    	if(!$promotion_item_discount_id || !$gift_id)
    	{
    		return FALSE;
    	}

    	$data = array(
    			'gift_id'	=>	$gift_id
    	);
    	$where = 'promotion_order_gift_id = '.$promotion_item_discount_id;
    	$promotion_order_gift_detail = M('promotion_order_gift_detail');
    	$r = $promotion_order_gift_detail->where($where)->find();	//查找该活动当前是否存在
    	if(!$r)
    	{
    		$data['promotion_order_gift_id'] = $promotion_item_discount_id;
    		$r = $promotion_order_gift_detail->add($data);
    		return $r;
    	}
    	else
    	{
    		$r = $promotion_order_gift_detail->where($where)->save($data);
//     		die($promotion_order_gift_detail->getLastSql());
    		return $r;
    	}	
    }
    
    
    /**
     * @access public 
     * @todo 删除订单促销方案(降价打折)所设置的会员级别限制
     * @param int promotion_order_discount_id 活动的ID。必须
     * @date 2014/04/03
     */
    public function delUserRankForOrderDiscount($promotion_order_discount_id)
    {
    	if(!$promotion_order_discount_id)
    	{
    		return FALSE;
    	}
    	$order_discount_rank = M('promotion_order_discount_rank');
    	return $order_discount_rank->where('promotion_order_discount_id = '.$promotion_order_discount_id)->delete();
    }
    
    
    /************ 以下为覆盖父类的方法并适当扩展 *************/
    /** 
     * @access public
     * @todo 添加订单促销方案
     * @param array $data 组装好的数组数据。 必须 
     * @param int  $type  添加的促销类型。  必须  默认值1（2种针对订单的促销：1代表添加降价打折的活动，2代表添加赠送礼品的活动。）
     * @return int 如果添加成功，则返回插入的规则ID；否则返回 FALSE
     * 
     */
    public function addPromotion($data, $type=1)
    {
        if(empty($data))
        {
            return -1;
        }
        $mod = ($type == 1)?M('promotion_order_discount'):M('promotion_order_gift');   //打折降价则操作表tp_promotion_order_discount，否则操作tp_promotion_order_gift
        
        $last_insert_id = $mod->add($data);
        return $last_insert_id?$last_insert_id:FALSE;
    }
    
    /**
     * @access public
     * @todo 根据订单促销方案ID获取该促销方案详细信息(降价打折的活动)
     *  
     */
    public function getPromotion($promotion_order_discount_id)
    {
        $order_discount = M('promotion_order_discount');
        if(!$promotion_order_discount_id)
        {
            return FALSE;
        }
        $r = $order_discount->where('promotion_order_discount_id = '.$promotion_order_discount_id)->find();
        return $r?$r:FALSE;
    }
    
    /** 
     * @access public
     * @todo 修改促销方案
     * @param int $promoid  订单促销活动的活动ID(降价打折的活动)
     * @param array 数据组装好的数组
     * @return bool 成功返回TRUE;否则返回FALSE
     */
    public function editPromotion($promoid,$data)
    {
        if(!$promoid || empty($data))
        {
            return FALSE;
        }
        $order_discount = M('promotion_order_discount');
        $order_discount->where('promotion_order_discount_id = '.$promoid)->save($data);
        #die($order_discount->getLastSql());
        return TRUE;
    }
    
    /** 
     * @access public
     * @todo 删除促销方案
     * @param int $promoid  活动方案的ID(降价打折的活动)
     * @return bool 操作成功返回TRUE;否则返回FALSE;
     */
    public function deletePromotion($promoid)
    {
        if(!$promoid)
        {
            return FALSE;
        }
        $order_discount = M('promotion_order_discount');
        if($order_discount->where('promotion_order_discount_id = '.$promoid)->delete())
        {
        	$this->delUserRankForOrderDiscount($promoid);
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    
    /** 
     * @access public
     * @todo 设置当前促销方案无效(降价打折的活动)
     * @param int $promoid  活动的ID号
     * @param int $isuse 状态，两种值：1 有效；2 无效
     * @return bool 更新成功返回TRUE;失败返回FALSE;
     */
    public function disablePromotion($promoid, $isuse)
    {
        if(!$promoid || $isuse || ($isuse != 1 && $isuse != 2))
        {
            return FALSE;
        }
        $order_discount = M('promotion_order_discount');
        if($order_discount->where('promotion_order_discount_id = '.$promoid)->save(array('isuse'=>$isuse)))
        {
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }
    /**
     * @access public
     * @todo 设置当前促销方案无效(送礼品活动)
     * @param int $promoid  活动的ID号
     * @param int $isuse 状态，两种值：1 有效；2 无效
     * @return bool 更新成功返回TRUE;失败返回FALSE;
     */
    public function disableOrderGiftPromotion($promoid, $isuse)
    {
    	if(!$promoid || $isuse || ($isuse != 1 && $isuse != 2))
    	{
    		return FALSE;
    	}
    	$order_discount = M('promotion_order_gift');
    	if($order_discount->where('promotion_order_gift_id = '.$promoid)->save(array('isuse'=>$isuse)))
    	{
    		return TRUE;
    	}
    	else
    	{
    		return FALSE;
    	}
    }
    
    
}

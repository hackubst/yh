<?php

/**
*  商品促销管理模型 
*  @ Project : Untitled
*  @ File Name : OrderPromotionModel.class.php
*  @ Date : 2014/2/27
*  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
*/

class ItemPromotionModel extends PromotionBaseModel
{  
    
    /********* 本类自定义方法 ***********/
    
    /** 
     * @access public
     * @todo 根据商品ID获取所有的打折降价促销信息(有效的)
     * @param int $item_id 商品ID。必须
     * @param int $user_rank 用户级别。默认为0表示游客
     * @param int $num 购买的数量
     */
    public function getCouponInfoByItemId($item_id,$user_rank=0,$num=1)
    {
    	if(!$item_id)
    	{
    		return FALSE;
    	}
    	
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail';
        $main_table		= C('DB_PREFIX').'promotion_item_discount';
        $detail_table	= $this->trueTableName;
        $rank_table 	= C('DB_PREFIX').'promotion_item_discount_rank';
        
        $promotion_list = $this->join($rank_table.' AS r ON r.promotion_item_discount_id = '.$detail_table.'.promotion_item_discount_id')
        		  			->join($main_table.' AS m ON m.promotion_item_discount_id = '.$detail_table.'.promotion_item_discount_id')
        		  			->where($detail_table.'.item_id = '.$item_id.' AND m.item_total >= '.$num)
        		  			->field('m.*,'.$detail_table.'.item_id,r.agent_rank_id')
        		  			->select();    #die($this->getLastSql());        
        $couponInfo = array();
        
        $time = time();
        //判断该该商品有没有参加打折降价活动,并且该打折活动当前有效
        if($promotion_list)                      //商品参与了哪些活动
        {
            foreach($promotion_list as $k=>$v)
            {
               if($v['isuse'] != 1)			//活动无效
               {
               		continue;
               }
               
               if($v['start_time'])			//如果设置了起始时间（不设置时，即该项值为0，表示活动在添加时是及时生效的）
               {
               		if($time < $v['start_time'])	//活动未开始
               		{
               			continue;
               		}
               }
               if($v['end_time'])
               {
               		if($time > $v['end_time'])	//活动已经结束
               		{
               			continue;
               		}
        	   }
               if($v['agent_rank_id'])		//参与活动有级别限制
               {
               		$rank_arr = explode(',',$v['agent_rank_id']);
               		if(!in_array($user_rank, $rank_arr))	//用户不能参与本活动
               		{
               			continue;
               		}
               		
               }

               $couponInfo[] = $v;
            }
        }
        return $couponInfo;
    }
    
    
    /**
     * @access public
     * @todo 根据商品ID和当前购买的数量获取该商品所有的送礼活动信息(商品送礼活动的推荐信息)
     * @param int $item_id 商品ID。必须
     * @param int $num 购买是数量
     */
    public function getPromoInfoForItemGift($item_id,$num=1)
    {
    	if(!$item_id)
    	{
    		return FALSE;
    	}
    	$promotion_item_gift = M('promotion_item_gift');
    	$main_table = C('DB_PREFIX').'promotion_item_gift';
    	$gift_table = C('DB_PREFIX').'gift';
    	
    	$promotion_list = $promotion_item_gift->join($gift_table.' AS g ON g.gift_id = '.$main_table.'.gift_id')
    							 
    							->where($main_table.'.item_id = '.$item_id.' AND '.$main_table.'.isuse = 1 AND '.$main_table.'.item_total > '.$num)
    							->field($main_table.'.*,g.gift_name,g.isuse as guse,g.small_pic,g.market_price')
    							->select();
    	$result = array();
    	$time = time();
    	foreach($promotion_list as $k=>$v)
    	{
    		if($v['guse'] != 1)		//礼品不可用
    		{
    			continue;
    		}
    		if($v['stock'] < 0)		//礼品没有库存
    		{
    			continue;
    		}
    		if($v['start_time'])			//如果设置了起始时间（不设置时，即该项值为0，表示活动在添加时是及时生效的）
            {
               	if($time < $v['start_time'])	//活动未开始
               	{
               		continue;
               	}
            }
            if($v['end_time'])
            {
            	if($time > $v['end_time'])	//活动已经结束
               	{
               		continue;
               	}
        	}
    		$result[] = $v;
    	}
    	return $result;
    }
    
    
    /**
     * @todo 根据打折降价活动ID获取该活动方案详情
     * @param int $promo_id 活动的ID
     * @return array 返回活动详细信息
     */
    public function getPromoItemInfoById($promo_id)
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
        $r = $this->where('promotion_item_discount_id = '.$promo_id.' AND isuse = 1')->find();
        return $r;
    }

    /**
     * @todo 根据打折降价活动ID获取参与该活动的用户等级需求
     * @param int $promo_id 活动的ID
     * @return int 有限制则返回等级限制，否则返回-1
     */
    public function getRankInfoByPromoId($promo_id)
    {
        if($promo_id <= 0)
        {
            return -1;
        }
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount_rank';
        $r = $this->where('promotion_item_discount_id = '.$promo_id)->getField('agent_rank_id');
        if($r)
        {
            return $r;
        }
        return -1;
    }
    
    /**
     * @todo 根据打折降价活动ID设置参与该活动的用户等级需求
     * @param int $promo_id 活动的ID
     * @author wsq
     * @return int 有限制则返回等级限制，否则返回-1
     */
    public function setRankInfoByPromoId($promo_id, $ids)
    {
        if($promo_id <= 0)
        {
            return false;
        }

        $promotion_item_rank_obj = M('promotion_item_discount_rank');
        $where                   = 'promotion_item_discount_id' . $promo_id;
        $info                    = $promotion_item_rank_obj->where($where)->find();

        // 构造数据
        $data = array(
            'promotion_item_discount_id' => $promo_id, 
            'agent_rank_id'              => implode(",", $ids),
        );
        $result = false;

        if ($info) {
            $result = $promotion_item_rank_obj->where($where)->save($data);
        } else {
            $result = $promotion_item_rank_obj->add($data);
        }

        return $result;
    }
    
    
    /**
     * @access public
     * @todo 统计所有的商品促销方案
     *  
     */
    public function countPromotion($where='')
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
    	$total = $this->where($where)->count();
    	return $total;
    }
    
      /**
     * @access public
     * @todo 获取所有的商品促销方案（列表）
     * @param string $where 查询的where条件
     * @param string order 查询的order条件
     * @return 查询结果或者FALSE
     */
    public function getPromotionList($where='', $order='')
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
        $r = $this->where($where)->order($order)->limit()->select();
        return $r?$r:FALSE;
    }
    
    /**
     * @access public
     * @todo 获取所有的商品促销方案，关联查询出等级信息（列表）
     * @param int $start_time 活动起始时间
     * @param int $end_time 活动结束时间
     * @param int $type 活动类型 
     * @param int $isuse 活动是否可用
     * @return 查询结果或者FALSE
     */
    public function getPromotionListInfo($start_time=0, $end_time=0, $type=0, $isuse=0)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
    	$rank_table = C('DB_PREFIX').'promotion_item_discount_rank';
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
    			  ->join($rank_table.' as r ON '.$this->trueTableName.'.promotion_item_discount_id = r.promotion_item_discount_id')->field($this->trueTableName.'.*,r.agent_rank_id')
    			  ->limit()
    			  ->order($this->trueTableName.'.promotion_item_discount_id DESC')
    			  ->select();
		#echo $this->getLastSql();
    	return $r?$r:FALSE;
    }
    
   
    
    /********  实现父类的5个抽象方法 **********/
    /** 
     * @access public
     * @todo 添加商品促销方案
     * @param array $data 组装好的数组数据。 必须 
     * @param int  $type  添加的促销类型。  必须  默认值1（2种针对商品的促销：1代表添加降价打折的活动，2代表添加赠送礼品的活动。）
     * @return int 如果添加成功，则返回插入的规则ID；否则返回 false
     * 
     */
    public function addPromotion($data, $type=1)
    {
        $this->trueTableName = ($type == 1)?C('DB_PREFIX').'promotion_item_discount':C('DB_PREFIX').'promotion_item_gift';   //打折降价则操作表tp_promotion_item_discount，否则操作tp_promotion_item_gift
        if(empty($data))
        {
            return FALSE;
        }
        $last_insert_id = $this->add($data);
        return $last_insert_id?$last_insert_id:FALSE;
    }
    
    
    /**
     * @access public
     * @todo  根据商品促销方案ID获取方案信息(商品打折降价活动)
     * @param int 促销方案ID
     * @return array 返回查询结果（一维数组）；如果查询失败，则返回bool型的FALSE
     */
    public function getPromotion($promotion_item_discount_id)
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
        if(!$promotion_item_discount_id)
        {
            return FALSE;
        }
        $r = $this->where('promotion_item_discount_id = '.$promotion_item_discount_id)->find();
        return $r?$r:FALSE;
    }
    
   /**
  * @access public
  * @todo 编辑活动规则
  * @param int $promoid 促销活动的ID号
  * @param array $data  新的数据
  * @return bool 编辑成功则返回TRUE;否则返回FALSE
  */  
	public function editPromotion($promoid,$data)
	{
	    $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
	    if(!$promoid || !$data || empty($data))
	    {
	        return FALSE;
	    }
	    if($this->where('promotion_item_discount_id = '.$promoid)->save($data))
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
     * @todo 删除商品促销方案
     * @param int $promoid 活动方案ID
     * @return bool 删除成功返回TRUE;失败返回FALSE
     */
    public function deletePromotion($promoid)
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
        if($this->where('promotion_item_discount_id = '.$promoid)->delete())
        {
            return TRUE;
        }
        return FALSE;
    }
    
    /** 
     * @access public
     * @todo 设置当前商品促销方案无效
     * @param int $promoid 待编辑的打折促销活动ID
     * @param int $isuse 启用状态的值：1、启用 2、不启用
     * @return bool 操作成功返回TRUE;否则返回FALSE
     */
    public function disablePromotion($promoid, $isuse)
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
        if(!$promoid || !$isuse)
        {
            return FALSE;
        }
        if($this->where('promotion_item_discount_id = '.$promoid)->save(array('isuse'=>$isuse)))
        {
            return TRUE;
        }
        return FALSE;
    }
    
    
}


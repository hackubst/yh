<?php

/**
*  商品促销 其他业务（比如为促销方案增减商品，设置分销商等级限制等等）管理模型 
*  @ Project : Untitled
*  @ File Name : OtherItemPromoModel.class.php
*  @ Date : 2014/02/27
*  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
*/

class OtherItemPromoModel extends ItemPromotionModel
{
    /******* 本类中的自定义方法 *******/
    
	/************************************  对降价打折活动的操作   *******************************************/
    /**
     * @access public
     * @todo 为商品促销方案添加商品（包括编辑商品） tp_promotion_item_discount_detail表
     * @param int $promotion_item_discount_id 商品优惠主表ID（即商品促销方案ID)。 必须
     * @param array $items 参与该促销的商品ID组成的索引数组。 必须 例如array(1,39,22,31)
     * @return int $sum 操作成功则返回的编辑的总条数，否则返回FALSE
     * 
     */
    public function addPromotionItems($promotion_item_discount_id, $items=array())
    {
    	#$this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail';
    	$promotion_detail = M('promotion_item_discount_detail');
        if(!$promotion_item_discount_id || empty($items))
        {
            return FALSE;
        }
        $items = array_unique($items);
        $sum = 0;   //计数器
        //数据
        $data['promotion_item_discount_id'] = $promotion_item_discount_id;
        //先查询出当前参与该项活动的商品ID
       
        $r = $promotion_detail->where('promotion_item_discount_id = '.$promotion_item_discount_id)->field('item_id')->select();

        if(!$r)     //如果该活动目前还没有商品参与，则一一添加
        {
            foreach ($items as $k=>$v)      //循环为商品一一添加活动
            {
                $data['item_id']  = $v;
                if($promotion_detail->add($data))
                {  
                    $sum++;
                }
               # echo $this->getLastSql();echo '<hr/>';
            }
        }
        else                //如果当前方案已经有商品在进行促销
        {
            $now_item = array();        //已经参加该活动的商品ID数组
            foreach($r as $k=>$v)
            {
                $now_item[] = $v['item_id'];
            }
            //这里进行判断，将当前已经参加活动的商品ID与要编辑的商品ID进行比较。如果已经存在，则不操作数据库；如果不存在，则新添加一条；如果表中已经存在，但是本次编辑中却不存在，则删除一条记录
            $items_f = array_flip($items);     //(键值互换，这么做是为了根据商品ID取出该商品ID在数组$items里的位置）参数$items中的元素值代表商品ID，不会存在重复的值
            $delete_need = array();             //等待删除的已经参加活动的商品ID组成的数组
            foreach ($now_item as $k0=>$v0)     //循环当前已经参加该活动的商品ID
            {
                if(in_array($v0, $items))        //1、如果表中已经存在该项数据
                {
                    $key = $items_f[$v0];
                    unset($items[$key]);
                    continue;
                }
                else                            //2、如果不存在，则执行删除操作
                {
                    $delete_need[] = $v0;
                }
            }
            //判断结束。处理数据
            if(!empty($delete_need))        //如果存在要删除的商品
            {
                $this->deletePromotionItems($promotion_item_discount_id, $delete_need);      //执行删除
            }
            
            if(!empty($items))          //当程序走到这里，如果还有需要编辑的商品（即还存在需要加入活动的商品）
            {
                foreach ($items as $k1=>$v1)
                {
                    $data['item_id']  = $v1;
                    if($promotion_detail->add($data))
                    {  
                        $sum++;
                    }
                }
            }
        }
        return $sum?$sum:FALSE;
    }
    
    /**
     * @access public
     * @todo 取消参与打折降价促销方案的商品
     * @param int $promotion_item_discount_id 商品优惠主表ID（即商品促销方案ID)。 必须
     * @param array $items 准备取消活动的商品ID组成的索引数组
     * @return bool 操作成功返回TRUE 否则返回FALSE
     */
    public function deletePromotionItems($promotion_item_discount_id, $items) 
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail';
        if(!$promotion_item_discount_id || empty($items))
        {
            return FALSE;
        }
        $item_ids = implode(',',$items);
        $where = 'promotion_item_discount_id = '.$promotion_item_discount_id. ' AND item_id IN('.$item_ids.')';
        if($this->where($where)->delete())
        {
            return TRUE;
        }
        return FALSE;
    }
    
    
    
    /**
     * @access public
     * @todo 根据活动ID删除该活动下的所有参与该活动的商品记录tp_promotion_item_discount_detail表 
     * @param int $promotion_item_discount_id 活动的ID。必须
     * @return bool 成功返回TRUE 否则返回FALSE
     * @date  2014/04/01
     */
    public function deletePromotionByPromotionId($promotion_item_discount_id)
    {
    	if(!$promotion_item_discount_id)
    	{
    		return FALSE;
    	}
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail';
    	return $this->where('promotion_item_discount_id = '.$promotion_item_discount_id)->delete();
    }
    
    /**
     * @access public
     * @todo 根据活动ID删除该活动下的等级需求记录tp_promotion_item_discount_rank表
     * @param int $promotion_item_discount_id 活动的ID。必须
     * @return bool 成功返回TRUE 否则返回FALSE
     * @date  2014/04/01
     */
    public function deleteAgentRandNeedByPromotionId($promotion_item_discount_id)
    {
    	if(!$promotion_item_discount_id)
    	{
    		return FALSE;
    	}
    	$item_discount_rank = M('promotion_item_discount_rank');
    	return $item_discount_rank->where('promotion_item_discount_id = '.$promotion_item_discount_id)->delete();
    }
    
    /**
     * @todo 根据打折降价活动的活动ID获取参与该活动的所有商品的基本信息
     * @param int $promo_id 活动ID
     * @return 查询结果
     */
    public function getItemListByPromotionId($promo_id)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail'; 
    	 if(!$promo_id)
    	 {
    	 	return FALSE;
    	 }
    	 $main_table = C('DB_PREFIX').'promotion_item_discount_detail'; 
    	 $promotion_detail = M('promotion_item_discount_detail');
    	 $r = $this->where($main_table.'.promotion_item_discount_id = '.$promo_id)
    	 		   ->join(C('DB_PREFIX').'item ON '.$main_table.'.item_id = '.C('DB_PREFIX').'item.item_id')
    	 		   ->field($main_table.'.*,'.C('DB_PREFIX').'item.item_name,base_pic,mall_price')
    	 		   ->select();
    	 return $r;
    }
    
    
    /**
     * @access public
     * @todo 设置参与打折降价活动需要的分销商等级规则
     * @param int $promotion_item_discount_id 商品打折促销活动的方案ID。 必须
     * @param int $agent_rand_id  要设置的用户级别ID组成的索引数组。 必须
     * @return bool 成功返回TRUE 否则返回FALSE
     */
    public function setPromotionAgentRandNeed($promotion_item_discount_id, $user_rank)
    {
        if(!$promotion_item_discount_id || !$user_rank || empty($user_rank))
        {
            return FALSE;
        }
        $item_discount_rank = M('promotion_item_discount_rank');
        $data = array(
            'promotion_item_discount_id'   =>  $promotion_item_discount_id
        );
        $r = $item_discount_rank->where('promotion_item_discount_id = '.$promotion_item_discount_id)->select();            //查询该活动是否已经设置过会员等级限制
        if(!$r)             //如果没有设置，则新增
        {
        	$data['agent_rank_id'] = implode(',',$user_rank);
        	if($item_discount_rank->add($data))
        	{
        		return TRUE;
        	}
        }
        else                //如果该活动已经有会员等级限制。则执行更新
        {
        	$data0['agent_rank_id'] = implode(',',$user_rank);
			$item_discount_rank->where('promotion_item_discount_id = '.$promotion_item_discount_id)->save($data0);
        	return TRUE;
        	
//             $temp_agent_set = array();
//             $now_rank_info = explode(',',$r[0]['agent_rank_id']);
//             foreach($now_rank_info as $v0)     //循环判断当前已经设置的等级
//             {
//                 if(in_array($v0,$user_rank))         //不存在即表示当前要取消该级别用户的参加资格
//                 {
//                 	$temp_agent_set[] = $v0['agent_rank_id'];
//                 #    $item_discount_rank->where('promotion_item_discount_id = '.$promotion_item_discount_id.' AND agent_id = '.$v0)->delete();
//                 }
//             }
//             $new_agent_set = array_diff($temp_agent_set, $user_rank);           //取差集即得要新增的
//             myprint($now_rank_info);
//             foreach($new_agent_set as $v1)
//             {
//                 $data['agent_rank_id'] = $v1;
//                 if($item_discount_rank->save($data))
//                 {
//                     continue;
//                 }
//                 return TRUE;
//             }
        }
        return FALSE;
    }
    

    
    /************************************  对赠送礼品的操作   *******************************************/
    
    /**
     *  @access public
     *  @todo 统计买商品送礼活动的数量
     *  @param string 搜索商品名称的参数。默认为空
     *  @param int $start_time 活动起始时间。默认为空
     *  @param int $end_time 活动结束时间。默认为false,表示忽略该条件。1表示有效，2表示无效
     *  @return int 返回统计的数量
     */
    public function countItemGiftPromo($item_name='', $start_time=0, $end_time=0, $isuse=false)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
    	$main_table = $this->trueTableName;
    	$i_table	= C('DB_PREFIX').'item';
    	
    	$where = '';
    	if($item_name != '')
    	{
    		$where .= 'i.item_name like "%'.$item_name.'%"';
    	}
    	if($start_time)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $main_table.'.start_time >= '.$start_time;
    	}
    	if($end_time)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $main_table.'.end_time <= '.$end_time;
    	}
    	if($isuse)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $main_table.'.isuse >= '.$isuse;
    	}
    	
    	$total = $this->join($i_table.' as i ON '.$main_table.'.item_id = i.item_id')
    				  ->where($where)
    				  ->count();
    	#echo $this->getLastSql();
    	return $total;
    }
    /**
     *  @access public
     *  @todo 获取买商品送礼活动的列表
     *  @param string 搜索商品名称的参数。默认为空
     *  @param int $start_time 活动起始时间。默认为空
     *  @param int $end_time 活动结束时间。默认为false,表示忽略该条件。1表示有效，2表示无效
     *  @return int 返回查找的结果
     */
    public function getItemGiftPromoList($item_name='', $start_time=0, $end_time=0, $isuse=false)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
    	$main_table = $this->trueTableName;
    	$i_table	= C('DB_PREFIX').'item';
    	$g_table	= C('DB_PREFIX').'gift';
    	
    	$where = '';
    	if($item_name != '')
    	{
    		$where .= 'i.item_name like "%'.$item_name.'%"';
    	}
    	if($start_time)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $main_table.'.start_time >= '.$start_time;
    	}
    	if($end_time)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $main_table.'.end_time <= '.$end_time;
    	}
    	if($isuse)
    	{
    		if($where != '')
    		{
    			$where .= ' AND ';
    		}
    		$where .= $main_table.'.isuse >= '.$isuse;
    	}
    	
    	$r = $this->join($i_table.' as i ON '.$main_table.'.item_id = i.item_id')
    			  ->join($g_table.' as g ON '.$main_table.'.gift_id = g.gift_id')
     			  ->where($where)
    			  ->order($main_table.'.promotion_item_gift_id DESC')
    			  ->limit()
    			  ->field($main_table.'.*,i.item_name,i.base_pic,g.gift_name')
    			  ->select();
    	#echo $this->getLastSql();
    	return $r;
    }
    
    
    /**
     *	@access public
     *	@todo 根据商品送礼活动 ID 获取该活动规则的详细信息
     *	@param int $promotion_item_gift_id
     */
    public function getPromoInfoForItemGift($promotion_item_gift_id)
    {
    	if(!$promotion_item_gift_id)
    	{
    		return false;
    	}
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
    	$main_table = $this->trueTableName;
    	$i_table	= C('DB_PREFIX').'item';
    	
    	$r = $this->join($i_table.' as i ON '.$main_table.'.item_id = i.item_id')
    			  ->where($main_table.'.promotion_item_gift_id = '.$promotion_item_gift_id)
                  ->field($main_table.'.promotion_item_gift_id,'.$main_table.'.item_total,'.$main_table.'.item_id,'.$main_table.'.gift_id,'.$main_table.'.gift_total,'.$main_table.'.start_time,'.$main_table.'.end_time,'.$main_table.'.isuse,i.item_name,i.keywords,i.description,i.item_type_id,i.item_sn,i.has_sku,i.base_pic,i.cost_price,i.market_price,i.wholesale_price,i.class_id,i.class_sort_id,i.class_genre_id,i.brand_id,i.stock,i.stock_alarm,i.least_purchase_number,i.weight,i.is_delivery_free,i.sales_num,i.clickdot,i.addtime,i.serial,i.is_del,i.title')
    			  ->find();
    	//echo $this->getLastSql();
    	return $r;
 
    }
    
    
    /**
     * @access public
     * @todo 为某一件商品添加一件赠品（本函数一次只能添加一条记录，多个礼品请多次调用）
     * @param int $item_id 商品ID。 必须
     * @param int $item_total 购买的数量。  必须
     * @param int $gift_id 礼品的ID。   必须
     * @param int $gift_total 赠送礼品的数量。默认为1( tinyint(4)型，最大数值为127)
     * @param int $start_time 赠礼品活动的开始时间。默认为0 表示及时生效
     * @param int $end_time 赠送礼品活动的结束时间。默认为0，表示不指定结束时间，永远生效
     * @praam int $isuse   活动是否有效，有两种值：1、有效 2、无效。 默认为1
     * @return int 添加成功返回插入ID（大于0）
     *              其他值返回：-1 参数有问题；-2 礼品本身状态不可用；-3 礼品库存不够；-4 插入失败
     */
    public function addGiftForItem($item_id, $item_total, $gift_id, $gift_total=1, $start_time=0, $end_time=0, $isuse=1)
    {
        if(!$item_id || $item_total<1 || !$gift_id)     //如果未指定商品ID或者指定的购买量小于1件，又或者没有指定礼品ID
        {
            return -1;
        }
        //判断礼品是否有效
        require_once('Lib/Model/GiftModel.class.php');
        $GiftModel = new GiftModel();
        $giftinfo = $GiftModel->getGiftInfo($gift_id);
        if($giftinfo)       //如果存在该礼品
        {
            if($giftinfo['isuse'] != 1)         //如果礼品本身的状态不可用
            {
                return -2;
            }
            if($giftinfo['stock'] <1)           //礼品已经送完或者库存不够
            {
                return -3;
            }
        }
        else
        {
            return -1;
        }
        $data = array(          //组装数据
            'item_total'    =>  $item_total,
            'item_id'       =>  $item_id,
            'gift_id'       =>  $gift_id,
            'gift_total'    =>  $gift_total,
            'start_time'    =>  $start_time,
            'end_time'      =>  $end_time,
            'isuse'         =>  $isuse
        );
        
        $this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
        $last_insert = $this->add($data);       //插入数据
        myprint($this->getLastSql());
        return $last_insert?$last_insert:-4;
    }

    /**
     * @access public 
     * @todo 删除特定ID的礼品赠送（针对商品的活动）
     * @param int $promotion_item_gift_id 。必须
     * @return bool  成功返回TRUE 失败返回FALSE
     * 
     */
    public function delGiftForItem($promotion_item_gift_id)
    {
        if(!$promotion_item_gift_id)
        {
            return FALSE;
        }
        $this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
        if($this->where('promotion_item_gift_id = '.$promotion_item_gift_id)->delete())
        {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * @access public
     * @todo 更新买商品赠送礼品信息
     * @param int $promotion_item_gift_id 待更新的礼品表序号即tp_promotion_item_gift的主键（非礼品ID）。必须
     * @param array $data 要更新的信息组成的数组（格式参考TP框架CURD操作）。必须
     * @return bool 成功返回TRUE 否则返回FALSE
     */
    public function updGiftForItem($promotion_item_gift_id, $data)
    {
        if(!$promotion_item_gift_id || !$data || empty($data))
        {
            return FALSE;
        }
        $this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
        if($this->where('promotion_item_gift_id = '.$promotion_item_gift_id)->save($data))
        {
            return TRUE;
        }
        return FALSE;
    }
    
    /**
     * @access public
     * @todo 根据商品ID获取该商品的所有促销礼品方案信息
     * @param int $goods_id 商品ID。 必须
     * @return 
     */
    public function getGiftByGoodsId($goods_id)
    {
        if(!$goods_id)
        {
            return FALSE;
        }
        $this->trueTableName = C('DB_PREFIX').'promotion_item_gift';
        $r = $this->where('item_id = '.$goods_id)->select();
        return $r?$r:FALSE;
    }
}

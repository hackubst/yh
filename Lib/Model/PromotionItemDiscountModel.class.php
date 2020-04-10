<?php

/**
*  商品促销 单品限时减价打折 
*  @ Project : Untitled
*  @ File Name : OrderPromotionModel.class.php
*  @ Date : 2014/2/27
*  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
*/




/** */

class PromotionItemDiscountModel extends ItemPromotionModel
{ 
     /********* 覆盖父类的第一个方法 ***********/
    /** 
     * @access public
     * @todo 根据商品购买信息计算当前优惠信息
     * @param int $goods_id 商品ID。 必须
     * @param int $num 商品的数量。 必须
     * @param float $price 该商品当前的单价。 必须
     * @param int   $time 购买该商品的时间。 必须
     * @param int   $user_rank 用户等级。 必须
     * @return array $couponInfo 返回促销的详细信息(该商品)
     *          ↓参数说明举例如下↓
     *          array(
     *              'itemId'    =>  1,          //购买的商品的ID号(传入时的参数值的返回)
     *              'num'       =>  3,          //该商品购买的数量(传入时的参数值的返回)
     *              'price'     =>  22.50,      //该商品的单价(传入时的参数值的返回)
     *              'totalprice'=>  64.50,      //综合计算后的商品总价
     *              'promoinfo' =>  array(      //该商品当前已经享有的降价打折优惠活动的规则信息(取自买商品得优惠表主表，参数不再注释，请参考tp_promotion_item_discount表)。如果未能参加活动，该值为空数组
     *                              'promotion_item_discount_id' => 1,
                                    'item_total' => 4,
                                    'discount_type' => 1,
                                    'discount_total' => 0.88,
                                    'start_time' => 0,
                                    'end_time' => 0,
                                    'isuse' => 1
     *                              )
     *              'recommend' =>  array(      //够买该商品的推荐方案信息（购买更多优惠更多,没有时该元素值为空数组）
     *                              
     *                              )
     *          )
     */
    public function getCouponInfoByItemId($goods_id, $num, $price, $time, $user_rank)
    {
    	$this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail';
        $num = ($num<=1)?1:$num;
        $totalprice = sprintf('%.2f',$num * $price);                //当前商品未参与促销前的总价
        $newpriceInfo = array();                        //该商品参加各种打折活动后的价格，函数将从中这些价格中抽出最低的一个返回（这是一个数组，因为同一件商品能同时参与多种活动，而本数组中存储的是参加每一种活动后的总价及相关信息）
        $newpriceInfo[] = array(                        //该数组的第一个元素表示默认总价格，即为不参与打折降价活动的商品原价信息
                'nowprice'  => $totalprice,         //新的价格
                'promoinfo' => array()              //参与的活动的规则信息
        );
        $couponInfo = array(            //函数要返回的信息
            'itemId'    =>  $goods_id,
            'num'       =>  $num,
            'price'     =>  $price,
            'totalprice'=>  $totalprice,
            'promoinfo' =>  array(),
            'recommend' =>  array()
        );    
               
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount_detail';
        $main_table		= C('DB_PREFIX').'promotion_item_discount';
        $detail_table	= $this->trueTableName;
        $rank_table 	= C('DB_PREFIX').'promotion_item_discount_rank';
        
        $r = $this->join($rank_table.' AS r ON r.promotion_item_discount_id = '.$detail_table.'.promotion_item_discount_id')
        		  ->join($main_table.' AS m ON m.promotion_item_discount_id = '.$detail_table.'.promotion_item_discount_id')
        		  ->where($detail_table.'.item_id = '.$goods_id)
        		  ->field('m.*,'.$detail_table.'.item_id,r.agent_rank_id')
        		  ->select();				//判断该该商品有没有参加打折降价活动,并且该打折活动当前有效
        if($r)                             //如果该商品有参加促销活动
        {
            $prormo_id_arr = array();      //存放促销方案ID
            //循环去一一判断该活动的有效性，有效则计算新价格
            foreach ($r as $k=>$v)
            {
            	$promo_id_arr[] = $v['promotion_item_discount_id'];
                   //按该规则对商品进行详细的价格处理
                   if($v['isuse'] !=1)            //如果该促销当前无效
                   {
                       continue;
                   }
                   if($num < $v['item_total'])      //如果购买的数量不够
                   {
                       continue;
                   }
                   if($v['start_time'])            //如果该活动设置了起始时间（没有设置起止时间说明活动在添加时是及时生效的）
                   {
                       if($v['start_time']>$time)  //如果活动还没有开始
                       {
                           continue;
                       }
                   }
                   if($v['end_time'])              //如果该活动设置了结束时间（没有设置结束时间说明活动可以一直进行）
                   {
                       if($v['end_time']<$time)    //如果该活动已经结束
                       {
                           continue;
                       }
                   }
                   if($v['agent_rank_id'])			//如果活动有级别限制
                   {
                   		$agent_rank_arr = explode(',',$v['agent_rank_id']);
                   		if(!in_array($user_rank,$agent_rank_arr))	//如果用户不能参与本次活动
                   		{
                   			continue;
                   		}
                   }
                   
                   //而当程序执行到这里，则说明该用户对于该商品能够参加本次活动，计算新的商品总价并存入数组
                   $newpriceInfo[] = $this->coutPrice($v, $num, $price);    //计算该商品参加本项活动后的价格
                   
            }
//             echo $price.' '.$num.'<hr/>';
//             myprint($newpriceInfo);
            //排序取出当前购买量的最优惠价格
            if(count($newpriceInfo)>1)
            {
                $temp_price_arr 	= array();      //临时数据
                $temp_promoinfo_arr = array();  //临时数据
                foreach ($newpriceInfo as $k0=>$v0)
                {
                    $temp_price_arr[] 	  = $v0['nowprice'];
                    $temp_promoinfo_arr[] = $v0['promoinfo'];
                }
                array_multisort($temp_price_arr, SORT_ASC, $newpriceInfo);      //按总价进行升序排序
                $couponInfo['totalprice'] = $newpriceInfo[0]['nowprice'];       //最终价格
                $couponInfo['promoinfo']  = $newpriceInfo[0]['promoinfo'];       //最终有效的活动方案详情
            }
            
            $couponInfo['recommend'] = array();             //该商品的推荐信息，引导用户购买跟多该商品可以获取更高的优惠
            $useful_promo_id_str 	 = implode(',',$promo_id_arr);
            $recommend = $this->getRecommend($useful_promo_id_str, $num);
            if($recommend)
            {
                $couponInfo['recommend'] = $recommend;
            }
            return $couponInfo;
        }
        return $couponInfo;     
    }
    
    
    /**
     * @access private
     * @todo 获取：购买一定数量商品获得更好优惠的推荐信息
     * @param string $promoid_s 某商品参加的所有的降价活动的活动ID组成的字符串。必须 例如 '1,3,5'表示该商品参加了活动1,3,5
     * @param int   $num    该商品当前的购买量
     * @retrun array 查询结果
     */
    private function getRecommend($promoid_s,$num)
    {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount';
        $r = $this->where('promotion_item_discount_id IN('.$promoid_s.') AND item_total >'.$num.' AND isuse = 1')->order('item_total ASC')->select(); //获取购买量比当前购买量更多时的促销信息
        //die($this->getLastSql());
        $time = time();     //当前时间
        if($r)
        {
           foreach($r as $k=>$v)
           {
               if($v['isuse'] == 2)         //如果该活动无效
               {
                   continue;
               }
               if($v['start_time'] && $v['start_time']>$time)       //如果活动还没开始
               {
                   continue;
               }
               if($v['end_time'] && $v['end_time']<$time)           //如果活动已经结束
               {
                   continue;
               }
               return $v;           //如果通过了以上的条件过滤，那么此处的$v即代表了与当前购买量最邻近的促销方案
           }
        }
        return array();      //否则返回空结果集;
    }
    
    /**
     * @access private
     * @todo 计算商品参加打折促销后的价格
     * @param array $promo 降价打折促销规则的详细信息
     * @param int $num 购买的数量
     * @param float $price 单价
     * 
     */
    private function coutPrice($promo, $num, $price)
    {
        $arr = array();
        $arr['promoinfo'] = $promo;
        $newprice = 0.00;
        if($promo['discount_type'] == 1)    //如果是打折
        {
            $arr['nowprice'] = sprintf('%.2f',$num*$price*$promo['discount_total']);
        }
        else                //否则是直接减价
        {
            $arr['nowprice'] = sprintf('%.2f',($price-$promo['discount_total'])*$num);
        }
        return $arr;
    }
    
    /**
     * @access private
     * @todo 验证该商品参加优惠打折时需要的会员等级，判断当前用户的等级是否符合
     * @param int $PromoId 打折降价活动的活动ID
     * @param int $user_rank 用户等级
     * @return bool 如果用户可以参加本次活动，则返回TRUE;否则返回FALSE
     */
    private function getPromoAgentNeed($PromoId, $user_rank) {
        $this->trueTableName = C('DB_PREFIX').'promotion_item_discount_rank';
        $r = $this->where('promotion_item_discount_id = '.$PromoId)->select();
        if($r)          //如果设置了会员等级限制
        {   
            $now_ranks = array();
            if($r['agent_rank_id'])
            {
            	$now_ranks = explode(',',$r['agent_rank_id']);
            }
            if(!in_array($user_rank,$now_ranks))        //该用户等级未能参与到本次活动中
            {
                return FALSE;
            }
        }
        return TRUE;
       
    }
    
    
    
}

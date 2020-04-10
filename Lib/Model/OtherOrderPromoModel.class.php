<?php
/**
*  订单促销 其他业务管理模型 
*  @ File Name : OrderPromotionModel.class.php
*  @ Date : 2014/03/05
*  @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
*/

 class OtherOrderPromoModel extends OrderPromotionModel
{
     public function __construct()
     {
     	parent::__construct();
     }
     
     /**
      * @access public
      * @todo 统计参与到各类活动的订单总数 (tp_promotion_log表的操作)
      * 
      */
     public function getPromosResult($time1=0,$time2=0,$where='')
     {
     	$promotion_log = M('promotion_log');
     	$where = '';
     	if($time1)
     	{
     		if($where != '')
     		{
     			$where .= ' AND ';
     		}
     		$where .= 'addtime >= '.$time1;
     	}
     	if($time2)
     	{
     		if($where != '')
     		{
     			$where .= ' AND ';
     		}
     		$where .= 'addtime <= '.$time2;
     	}
     	
     	$result = $promotion_log->where($where)
     							->group('promotion_type')
     							->order('addtime DESC')
     							->field('DISTINCT order_id,promotion_type,COUNT("order_id") AS total')
     							->select();
     	#die($promotion_log->getLastSql());
     	return $result;
     	
     	
     }
     
}

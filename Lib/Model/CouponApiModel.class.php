<?php
class CouponApiModel extends ApiModel
{
    /**
     * 获取优惠券列表
     * @author wsq
     */

    public function getCouponList($params){
        //获取基本信息
		$firstRow    = isset($params['firstRow']) ? $params['firstRow'] : 0;
		$user_id     = intval(session('user_id'));

        //获取分页信息
		$num_per_page = C('PER_PAGE_NUM');
		$num_per_page = isset($params['fetch_num']) ? intval($params['fetch_num']) : $num_per_page;

		//总数
		$coupon_obj = new CouponModel();
        $where        = 'user_id = ' . $user_id;
        $total        = $coupon_obj->getCouponNum($where);

		if ($user_id && ($total != 0 && $firstRow <= ($total - 1))) {
            $coupon_obj->setStart($firstRow);
            $coupon_obj->setLimit($num_per_page);

            //获取优惠券列表
            $coupon_list = $coupon_obj->getCouponList(
                'coupon_id, coupon_name, price_limit, num, order_id, deadline, state, use_time',  
                $where, 
                'addtime DESC'
            );

            foreach ($coupon_list AS $key => $value) {
                $order_obj = new OrderModel($v['order_id']);
                $order_sn  = '--';
                try {
                    $order_info = $order_obj->getOrderInfo('order_sn', '');
                    $order_sn   = $order_info['order_sn'] ? $order_info['order_sn'] : "--";
                } catch (Exception $e) {}

                $coupon_list[$key]['order_sn'] = $order_sn;
                unset($coupon_list[$key]['order_id']);
            }

		} else {
			ApiModel::returnResult(40018, null, '没有更多记录了');

		}

		return array(
			'total_num'			=> $total,
			'nextFirstRow'		=> $num_per_page + $firstRow,
			'coupon_list'		=> $coupon_list,
		);
    }


	/**
	 * 获取参数列表
	 * @author 姜伟
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
    function getParams($func_name)
    {
        $params = array(
            'getCouponList' => array(
                array(
                    'field'		=> 'firstRow', 
                ),
                array(
                    'field'		=> 'fetch_num', 
                ),
            ),
        );

        return $params[$func_name];
    }
}

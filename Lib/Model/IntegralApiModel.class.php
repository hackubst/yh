<?php
class IntegralApiModel extends ApiModel
{
    /**
     * 获取积分收支明细
     * @author wsq
     */

    public function getIntegralList($params){
        //获取基本信息
		$firstRow    = isset($params['firstRow']) ? $params['firstRow'] : 0;
		$user_id     = intval(session('user_id'));

        //获取分页信息
		$num_per_page = C('PER_PAGE_NUM');
		$num_per_page = isset($params['fetch_num']) ? intval($params['fetch_num']) : $num_per_page;

		//总数
		$integral_obj = new IntegralModel(); 
        $where        = 'user_id = ' . $user_id;
        $total        = $integral_obj->getIntegralNum($where);

		if ($user_id && ($total != 0 && $firstRow <= ($total - 1))) {
            $integral_obj->setStart($firstRow);
            $integral_obj->setLimit($num_per_page);

            //获取积分列表
            $integral_list = $integral_obj->getIntegralList( 
                'integral_id, change_type, integral, start_integral, end_integral,addtime, remark, id',
                $where, 
                ' addtime DESC'
            );

            foreach ($integral_list AS $key => $value) {
                $order_obj = new OrderModel($v['id']);
                $order_sn  = '--';
                try {
                    $order_info = $order_obj->getOrderInfo('order_sn', '');
                    $order_sn   = $order_info['order_sn'] ? $order_info['order_sn'] : "--";
                } catch (Exception $e) {}

                $integral_list[$key]['order_sn'] = $order_sn;
                unset($integral_list[$key]['id']);
            }

		} else {
			ApiModel::returnResult(40018, null, '没有更多记录了');

		}

		return array(
			'total_num'			=> $total,
			'nextFirstRow'		=> $num_per_page + $firstRow,
			'integral_list'		=> $integral_list,
		);
    }


    /**
     * 获取积分兑换列表
     * @author wsq
     */
    public function getIntegralExchangeList($params) {
        //获取基本信息
		$firstRow    = isset($params['firstRow']) ? $params['firstRow'] : 0;
		$user_id     = intval(session('user_id'));

        //获取分页信息
		$num_per_page = C('PER_PAGE_NUM');
		$num_per_page = isset($params['fetch_num']) ? intval($params['fetch_num']) : $num_per_page;

		//总数
		$integral_exchange_record_obj = new IntegralExchangeRecordModel(); 
        $where        = 'user_id = ' . $user_id;
        $total        = $integral_exchange_record_obj->getOrderNum($where);

		if ($user_id && ($total != 0 && $firstRow <= ($total - 1))) {
            $integral_exchange_record_obj->setStart($firstRow);
            $integral_exchange_record_obj->setLimit($num_per_page);

            //获取积分兑换列表
            $fields  = 'exchange_record_id,exchange_record_sn,addtime,total_num,pay_amount,order_status';
            $integral_list = $integral_exchange_record_obj->getRecordList( 
                $fields,
                $where, 
                ' addtime DESC'
            );

		} else {
			ApiModel::returnResult(40018, null, '没有更多记录了');

		}

		return array(
			'total_num'		=> $total,
			'nextFirstRow'	=> $num_per_page + $firstRow,
			'integral_exchage_list'	=> $integral_list,
		);
        
    }

    /**
     * 获取积分兑换商品列表
     * @author wsq
     */
    public function getIntegralExchangeInfo($params) {

        $exchange_record_id = intval($params['integral_exchange_id']);
        $user_id            = intval(session('user_id'));

        $fields  = 'exchange_record_sn,addtime,total_num,pay_amount,order_status, user_address_id';
        $where   = ' AND user_id = ' . $user_id;

        $integral_exchange_record_obj = new IntegralExchangeRecordModel($exchange_record_id);

        try { 
            $integral_info  = $integral_exchange_record_obj->getOrderInfo($fields,$where);
            $integral_info['order_status_name'] = $integral_exchange_record_obj->convertOrderStatus(
                $integral_info['order_status']
            );

            unset($integral_info['order_status']);

        } catch(Exception $e) {
            ApiModel::returnResult(40018, null, '记录不存在');
        }

        return $integral_info;
    }

    /**
     * 获取积分兑换商品列表
     * @author wsq
     */
    public function getIntegralExchangeItemList($params) {
    
        // 获取商品信息
        $exchange_record_id = intval($params['integral_exchange_id']);
        $integral_exchange_record_obj = new IntegralExchangeRecordModel($exchange_record_id);

        try { 
            $items_list        = $integral_exchange_record_obj->getOrderItemList();

        } catch(Exception $e) {
            ApiModel::returnResult(40018, null, '记录不存在');
        }

        if (!$items_list) ApiModel::returnResult(40018, null, '记录不存在');

        return $items_list;
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
            'getIntegralList' => array(
                array(
                    'field'		=> 'firstRow', 
                ),
                array(
                    'field'		=> 'fetch_num', 
                ),
            ),
            'getIntegralExchangeList' => array(
                array(
                    'field'		=> 'firstRow', 
                ),
                array(
                    'field'		=> 'fetch_num', 
                ),
            ),
            'getIntegralExchangeInfo' => array(
                array(
                    'field'		=> 'integral_exchange_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41025, 
					'empty_code'=> 44025, 
					'type_code'	=> 45025, 
                ),
            ),
            'getIntegralExchangeItemList' => array(
                array(
                    'field'		=> 'integral_exchange_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41025, 
					'empty_code'=> 44025, 
					'type_code'	=> 45025, 
                ),
            ),
        );

        return $params[$func_name];
    }
}

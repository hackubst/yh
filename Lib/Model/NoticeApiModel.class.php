<?php
class NoticeApiModel extends ApiModel
{

    /**
     * 获取未读通知数量(merchant.notice.getNoticeNum)
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回通知数，失败退出返回错误码
     * @todo 获取未读通知数量(merchant.notice.getNoticeNum)
     */
    function getNoticeNum($params)
    {
        $order_obj = new OrderModel();

        //待支付订单数量
        $pre_pay_order_num = $order_obj->getOrderNum('isuse = 1' . ' AND order_status = ' . OrderModel::PRE_PAY);

        //已付款/待发货订单数量
        $payed_order_num = $order_obj->getOrderNum('isuse = 1' . ' AND order_status = ' . OrderModel::PAYED);

        //申请退款订单数量
        $refunding_order_num = $order_obj->getOrderNum('isuse = 1' . ' AND order_status = ' . OrderModel::REFUNDING);

        $order_notice_num = $pre_pay_order_num + $payed_order_num + $refunding_order_num;

        // 库存预警商品数
        $item_obj = new ItemModel();
        $where =  'stock <=  stock_alarm '. ' AND stock > 0';
        $item_alarm_num = $item_obj->getItemNum($where);

        return array(

            order_notice_num => $order_notice_num,
            item_alarm_notice_num => $item_alarm_num +0,
            total => $order_notice_num + $item_alarm_num,
        );
    }

    /**
     * 获取消息通知分类(merchant.notice.getNoticeCategory)
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回通知列表，失败退出返回错误码
     * @todo 获取消息通知分类(merchant.notice.getNoticeCategory)
     */
    function getNoticeCategory($params)
    {
        $order_obj = new OrderModel();

        //待支付订单数量
        $where = 'order_status = ' .OrderModel::PRE_PAY;
        $pre_pay_order_num = $order_obj->getOrderNum('isuse = 1 AND ' .$where);
        $pre_pay_order_list = $order_obj->getOrderList('order_id, order_sn , addtime', $where, 'addtime DESC');
        $pre_pay_order_info = reset($pre_pay_order_list);
        $pre_pay_order_info['total'] = $pre_pay_order_num;

        //已付款/待发货订单数量
        $where = 'order_status = ' .OrderModel::PAYED;
        $payed_order_num = $order_obj->getOrderNum('isuse = 1 AND ' .$where);
        $payed_order_list = $order_obj->getOrderList('order_id, order_sn , addtime', $where, 'addtime DESC');
        $payed_order_info = reset($payed_order_list);
        $payed_order_info['total'] = $payed_order_num;

        //申请退款订单数量
        $where = 'order_status = ' .OrderModel::REFUNDING;
        $refunding_order_num = $order_obj->getOrderNum('isuse = 1 AND ' .$where);
        $refunding_order_info = $order_obj->getOrderList('order_id, order_sn , addtime', $where, 'addtime DESC');
        $refunding_order_info = reset($refunding_order_info);
        $refunding_order_info['total'] = $refunding_order_num;

        return array(

            pre_pay_order_info => $pre_pay_order_info,
            payed_order_info => $payed_order_info,
            refunding_order_info => $refunding_order_info,
        );
    }


    /**
     * 获取订单消息通知列表 (merchant.notice.getOrderNoticeList)
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回订单通知列表，失败退出返回错误码
     * @todo 获取订单消息通知列表 (merchant.notice.getOrderNoticeList)
     */
    function getOrderNoticeList($params)
    {
        $where = '';
        switch ($params['notice_type']) {

            case 1:
                $where = 'order_status = ' .OrderModel::PRE_PAY;
                break;
            case 2:
                $where = 'order_status = ' .OrderModel::PAYED;
                break;
            case 3:
                $where = 'order_status = ' .OrderModel::REFUNDING;
                break;
            default:
                $where = '';
        }

        $order_obj = new OrderModel();

        //待支付订单数量
        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $order_num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $order_num_per_page = isset($order_num_per_page) ? $order_num_per_page : 10;

        $order_obj->setStart($firstRow);
        $order_obj->setLimit($order_num_per_page);

        $order_list = $order_obj->getOrderList('user_id, user_address_id, order_status, order_id, order_sn, pay_amount, total_amount, addtime, payway, express_company_id', $where, 'addtime DESC');

        // 所有订单返回订单商品列表
        foreach ($order_list AS $k => $v) {

            $order_obj = new OrderModel($v['order_id']);


            $order_info = $order_obj->getOrderInfo('user_id');

            // 获取买家姓名
            $user_obj = new UserModel($order_info['user_id']);
            $user_info = $user_obj->getUserInfo('nickname');
            $order_list[$k]['user_name'] = $user_info['nickname'];

            // 拼接商品名称
            $item_list = $order_obj->getOrderItemList('item_id, item_name, number');

            $item_name = '';
            foreach ($item_list AS $item_k => $item_v) {

                 $item_name .= ' ' .$item_v['item_name'];
            }

            $order_list[$k]['item_names'] = $item_name;
        }

        return $order_list;
    }

    /**
     * 获取库存预警商品列表 (merchant.notice.getItemAlarmNoticeList)
     * @author clk
     * @param array $params 参数列表
     * @return 成功返回库存预警通知列表，失败退出返回错误码
     * @todo 获取库存预警商品列表 (merchant.notice.getItemAlarmNoticeList)
     */
    function getItemAlarmNoticeList($params)
    {
        $item_obj = new ItemModel();
        $where =  'stock <=  stock_alarm '. ' AND stock > 0';
        $item_list = $item_obj->getItemList('', $where, ' addtime DESC');
        //$item_list = $item_obj->getListData($item_list);

        return $item_list;
    }

    /**
     * 获取参数列表
     * @author clk
     * @param
     * @return 参数列表
     * @todo 获取参数列表
     */
    function getParams($func_name)
    {
        $params = array(
            'getOrderNoticeList'	=> array(
                array(
                    'field'		=> 'notice_type',
                    'type'		=> 'int',
                    'required'	=> true,
                    'miss_code'	=> 41057,
                    'empty_code'=> 44057,
                    'type_code'	=> 45057,
                ),
            ),
        );

        return $params[$func_name];
    }
}

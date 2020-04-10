<?php
/**
 * 门店管理员权限数组
 * 
 * id 为权限唯一标识符
 * name 为权限或菜单对应的名字
 * mod_do_url 为对应的网址，其中do必须以acp_开头
 * in_menu 当此为空时表示本身就是左侧菜单；不为空时，其值必须是id，此id就是当' . C('USER_NAME') . '处于此权限页面时对应左侧菜单的特殊显示的权限id
 * 
 * ID规则：第一级为01至99的数字；
 *        第二级为0101至9999的数字，其中前两位为上级ID值；
 *        第三级为010101至999999的数字，其中最前面两位为顶级ID值，中间两位为上级ID值；
 *        以此类推
 */
$admin_menu_file = array();

$admin_menu_file[0] = array('id' => '01', 'mod_name' => 'System', 'name' => '系统管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/DcpConfig/base_config');
$admin_menu_file[0]['menu_list'] = array(
	'系统设置'	=> array(
 		array('id' => '0186', 'name' => '密码修改', 'mod_do_url' => '/DcpConfig/edit_password', 'in_menu' => ''),
	),
);

$admin_menu_file[3] = array('id' => '04', 'mod_name' => 'Order', 'name' => '订单管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/DcpOrder/get_pre_pay_order_list');
$admin_menu_file[3]['menu_list'] = array(
	'待处理订单'	=> array(
		array('id' => '0401', 'name' => '待付款订单', 'mod_do_url' => '/DcpOrder/get_pre_pay_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0402', 'name' => '已付款/待发货订单', 'mod_do_url' => '/DcpOrder/get_pre_deliver_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0403', 'name' => '已发货订单', 'mod_do_url' => '/DcpOrder/get_delivered_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0429', 'name' => '发货', 'mod_do_url' => '/DcpOrder/deliver_order', 'in_menu' => '0401'),//going-WSQ
		array('id' => '0404', 'name' => '订单详情', 'mod_do_url' => '/DcpOrder/order_detail', 'in_menu' => '0401'),//going-WSQ
		array('id' => '0405', 'name' => '修改订单', 'mod_do_url' => '/DcpOrder/edit_order', 'in_menu' => '0401'),//going-WSQ
		array('id' => '0406', 'name' => '待退货订单', 'mod_do_url' => '/DcpOrder/get_pre_refund_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0418', 'name' => '退货受理中订单', 'mod_do_url' => '/DcpOrder/get_accepted_refund_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0419', 'name' => '退货商品寄送中订单', 'mod_do_url' => '/DcpOrder/get_delivering_refund_order_list', 'in_menu' => ''),//going-WSQ
#		array('id' => '0420', 'name' => '已退货订单', 'mod_do_url' => '/DcpOrder/get_refunded_order_list', 'in_menu' => ''),//going-WSQ
		#array('id' => '0407', 'name' => '待换货订单', 'mod_do_url' => '/DcpOrder/get_pre_change_order_list', 'in_menu' => ''),
		array('id' => '0408', 'name' => '查看退货订单', 'mod_do_url' => '/DcpOrder/order_refund_change_apply_detail', 'in_menu' => '0418'),//going-WSQ
		array('id' => '0420', 'name' => '线下收款', 'mod_do_url' => '/DcpOrder/offline_pay', 'in_menu' => '0401'),//going-WSQ
		array('id' => '0421', 'name' => '退款确认收货', 'mod_do_url' => '/DcpOrder/confirm_refund_deliving', 'in_menu' => '0401'),//going-WSQ
		array('id' => '0422', 'name' => '删除订单', 'mod_do_url' => '/DcpOrder/delete_order', 'in_menu' => '0401'),//going-WSQ
		array('id' => '0423', 'name' => '批量删除订单', 'mod_do_url' => '/DcpOrder/batch_delete', 'in_menu' => '0401'),//going-WSQ
	),
	'历史订单'	=> array(
		array('id' => '0409', 'name' => '所有订单', 'mod_do_url' => '/DcpOrder/get_all_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0410', 'name' => '所有退货订单', 'mod_do_url' => '/DcpOrder/get_refunded_order_list', 'in_menu' => ''),//going-WSQ
		#array('id' => '0411', 'name' => '所有换货订单', 'mod_do_url' => '/DcpOrder/get_changed_order_list', 'in_menu' => ''),
		array('id' => '0412', 'name' => '所有确认订单', 'mod_do_url' => '/DcpOrder/get_confirmed_order_list', 'in_menu' => ''),//going-WSQ
		array('id' => '0416', 'name' => '所有取消订单', 'mod_do_url' => '/DcpOrder/get_canceled_order_list', 'in_menu' => ''),
	),//going-WSQ
	'订单统计'	=> array(
		array('id' => '0413', 'name' => '订单日统计', 'mod_do_url' => '/DcpOrder/order_stat_by_day', 'in_menu' => ''),//going-WSQ
		array('id' => '0414', 'name' => '订单月统计', 'mod_do_url' => '/DcpOrder/order_stat_by_month', 'in_menu' => ''),//going-WSQ
		array('id' => '0417', 'name' => '订单年统计', 'mod_do_url' => '/DcpOrder/order_stat_by_year', 'in_menu' => ''),//going-WSQ
		//array('id' => '0421', 'name' => '订单区域统计', 'mod_do_url' => '/DcpOrder/order_stat_by_area', 'in_menu' => ''),//going-WSQ
		array('id' => '0415', 'name' => '导出订单', 'mod_do_url' => '/DcpOrder/order_export', 'in_menu' => ''),//可以根据区域、省市县、时间、门店编码等筛选导出			//going-WSQ
	),
);

/*$admin_menu_file[4] = array('id' => '05', 'mod_name' => 'Account', 'name' => '财务管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/DcpFinance/get_account_apply_list');
$admin_menu_file[4]['menu_list'] = array(
	'日常处理'	=> array(
		#array('id' => '0501', 'name' => '入账申请列表', 'mod_do_url' => '/DcpFinance/get_account_apply_list', 'in_menu' => ''),
		array('id' => '0502', 'name' => '调整余额', 'mod_do_url' => '/DcpFinance/edit_account', 'in_menu' => ''),//增加手机号、QQ号，修改字段名称展示之，加导出按钮，跳到财务导出页导出Excel	//going-WSQ
		array('id' => '0503', 'name' => '财务变动明细', 'mod_do_url' => '/DcpFinance/get_account_log', 'in_menu' => ''),//修改字段名称展示之	//going-WSQ
	),
	'财务统计'	=> array(
		array('id' => '0506', 'name' => '充值日统计', 'mod_do_url' => '/DcpFinance/recharge_stat_by_day', 'in_menu' => ''),
		array('id' => '0507', 'name' => '充值月统计', 'mod_do_url' => '/DcpFinance/recharge_stat_by_month', 'in_menu' => ''),
		array('id' => '0508', 'name' => '充值年统计', 'mod_do_url' => '/DcpFinance/recharge_stat_by_year', 'in_menu' => ''),
		array('id' => '0505', 'name' => '导出财务数据', 'mod_do_url' => '/DcpFinance/export_account', 'in_menu' => ''),//增加筛选项，可根据手机号、门店编号导出	//going-WSQ
	),
);*/

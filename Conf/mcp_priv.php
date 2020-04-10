<?php
/**
 * 管理员权限数组
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

$_mod_id    = 0;
$_sort_id   = 10;

$admin_menu_file[$_mod_id] = array('id' => $_sort_id, 'mod_name' => 'System', 'name' => '系统管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/McpConfig/base_config');
$admin_menu_file[0]['menu_list'] = array(
	'系统设置'	=> array(
		array('id' => $_sort_id.'02', 'name' => '首页', 'mod_do_url' => '/McpConfig/home_page', 'in_menu' => ''),
		array('id' => $_sort_id.'01', 'name' => '修改密码', 'mod_do_url' => '/McpConfig/set_password', 'in_menu' => ''),
		),
);



$admin_menu_file[++$_mod_id] = array('id' => ++$_sort_id, 'mod_name' => 'User', 'name' => '代理商信息管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/McpUser/get_all_user_list');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	'代理商信息管理'	=> array(
		array('id' => $_sort_id.'03', 'name' => '管理个人信息', 'mod_do_url' => '/McpUser/get_agent_info', 'in_menu' => ''),//OK-CC
		array('id' => $_sort_id.'04', 'name' => '编辑个人信息', 'mod_do_url' => '/McpUser/edit_agent_info', 'in_menu' => $_sort_id.'03'),
        array('id' => $_sort_id.'05', 'name' => '余额变动明细', 'mod_do_url' => '/McpFinance/get_agent_account_log', 'in_menu' => $_sort_id.'03'),
	),

);


$admin_menu_file[++$_mod_id] = array('id' =>  ++$_sort_id, 'mod_name' => 'Account', 'name' => '财务管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/McpFinance/get_account_apply_list');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	 '日常处理'	=> array(
	 	array('id' => $_sort_id.'01', 'name' => '金豆充值', 'mod_do_url' => '/McpFinance/recharge_list', 'in_menu' => ''),
         array('id' => $_sort_id.'03', 'name' => '用户金豆变动明细', 'mod_do_url' => '/McpFinance/get_account_log', 'in_menu' => '')
	 ),

	'提现管理'	=> array(
		array('id' => $_sort_id.'14', 'name' => '提现申请', 'mod_do_url' => '/McpDeposit/deposit_apply', 'in_menu' => ''),
		array('id' => $_sort_id.'15', 'name' => '提现申请列表', 'mod_do_url' => '/McpDeposit/get_deposit_list', 'in_menu' => ''),
	),
    '点卡兑换管理'	=> array(
        // array('id' => $_sort_id.'18', 'name' => '兑换卡列表', 'mod_do_url' => '/McpGift/get_gift_card_list', 'in_menu' => ''),
        array('id' => $_sort_id.'22', 'name' => '卡密核销', 'mod_do_url' => '/McpGift/get_gift_password_list', 'in_menu' => ''),
        array('id' => $_sort_id.'23', 'name' => '使用卡密', 'mod_do_url' => '/McpGift/use_gift_password', 'in_menu' => $_sort_id.'22'),
        array('id' => $_sort_id.'20', 'name' => '卡密核销记录', 'mod_do_url' => '/McpGift/get_checked_gift_list', 'in_menu' => ''),
    ),
    '红包管理'	=> array(
        array('id' => $_sort_id.'31', 'name' => '添加红包', 'mod_do_url' => '/McpRedPacket/add_red_package', 'in_menu' => ''),
        array('id' => $_sort_id.'32', 'name' => '后台发放红包列表', 'mod_do_url' => '/McpRedPacket/admin_red_packet_list', 'in_menu' => ''),
        array('id' => $_sort_id.'33', 'name' => '红包领取列表', 'mod_do_url' => '/McpRedPacket/get_red_packet_log_list', 'in_menu' => $_sort_id.'32'),
    ),
);



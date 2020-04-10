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

$admin_menu_file[$_mod_id] = array('id' => $_sort_id, 'mod_name' => 'System', 'name' => '系统管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpConfig/base_config');
$admin_menu_file[0]['menu_list'] = array(
	'系统设置'	=> array(
//        array('id' => '0197', 'name' => '生成模型方法', 'mod_do_url' => '/AcpConfig/create_model', 'in_menu' => ''),
//		#array('id' => $_sort_id.'01', 'name' => '头部菜单设置', 'mod_do_url' => '/AcpConfig/list_menu', 'in_menu' => ''),
		array('id' => $_sort_id.'03', 'name' => '首页', 'mod_do_url' => '/AcpConfig/home_page', 'in_menu' => ''),
		array('id' => $_sort_id.'01', 'name' => '修改密码', 'mod_do_url' => '/AcpConfig/set_password', 'in_menu' => ''),
		array('id' => $_sort_id.'02', 'name' => '基础设置', 'mod_do_url' => '/AcpConfig/base_config', 'in_menu' => ''),			//OK-CC
		#array('id' => $_sort_id.'98', 'name' => '有效流水设置', 'mod_do_url' => '/AcpConfig/valid_flow_config', 'in_menu' => ''),			//OK-CC
		#array('id' => $_sort_id.'92', 'name' => '配送费设置', 'mod_do_url' => '/AcpConfig/send_setting', 'in_menu' => ''),			//OK-CC
//		array('id' => $_sort_id.'99', 'name' => '新用户关注设置', 'mod_do_url' => '/AcpConfig/subscribe_set', 'in_menu' => ''),
		#array('id' => $_sort_id.'35', 'name' => '高级设置', 'mod_do_url' => '/AcpConfig/advanced_setting', 'in_menu' => ''),
//		array('id' => $_sort_id.'03', 'name' => '短信设置', 'mod_do_url' => '/AcpConfig/sms_config', 'in_menu' => ''),
//		array('id' => $_sort_id.'37', 'name' => '支付方式设置', 'mod_do_url' => '/AcpPayment/list_payment', 'in_menu' => ''),	//OK-DONE
		#array('id' => $_sort_id.'30', 'name' => '支付宝接口设置', 'mod_do_url' => '/AcpPayment/set_alipay', 'in_menu' => $_sort_id.'37'),//OK-DONE
		#array('id' => $_sort_id.'31', 'name' => '网银在线接口设置', 'mod_do_url' => '/AcpPayment/set_chinabank', 'in_menu' => $_sort_id.'37'),//OK-DONE
		#array('id' => $_sort_id.'32', 'name' => '电子钱包设置', 'mod_do_url' => '/AcpPayment/set_wallet', 'in_menu' => $_sort_id.'37'),//OK-DONE
//		array('id' => $_sort_id.'38', 'name' => '微信支付设置', 'mod_do_url' => '/AcpPayment/set_wxpay', 'in_menu' => $_sort_id.'37'),//OK-DONE
//		array('id' => $_sort_id.'39', 'name' => '微信APP支付设置', 'mod_do_url' => '/AcpPayment/set_mobile_wxpay', 'in_menu' => $_sort_id.'37'),//OK-DONE
		#array('id' => $_sort_id.'33', 'name' => '线下付款设置', 'mod_do_url' => '/AcpPayment/set_offline', 'in_menu' => $_sort_id.'37'),//OK-DONE
		// array('id' => $_sort_id.'34', 'name' => '关于潘朵拉', 'mod_do_url' => '/AcpArticle/edit_about', 'in_menu' => ''),
// 		array('id' => $_sort_id.'05', 'name' => '邮件设置', 'mod_do_url' => '/AcpConfig/email_config', 'in_menu' => ''),
// 		array('id' => $_sort_id.'86', 'name' => '运费设置', 'mod_do_url' => '/AcpConfig/shopping_fare', 'in_menu' => ''),
// 		array('id' => $_sort_id.'56', 'name' => '公众号菜单设置', 'mod_do_url' => '/AcpConfig/menu_set', 'in_menu' => ''),
// 		array('id' => $_sort_id.'57', 'name' => '公众号关键字回复设置', 'mod_do_url' => '/AcpConfig/auto_reply_list', 'in_menu' => ''),
// 		array('id' => $_sort_id.'58', 'name' => '增加关键字回复', 'mod_do_url' => '/AcpConfig/add_auto_reply', 'in_menu' => $_sort_id.'57'),
// 		array('id' => $_sort_id.'59', 'name' => '修改关键字回复', 'mod_do_url' => '/AcpConfig/edit_auto_reply', 'in_menu' => $_sort_id.'57'),
// 		array('id' => $_sort_id.'60', 'name' => '公众号消息群发设置', 'mod_do_url' => '/AcpConfig/set_mass_msg', 'in_menu' => ''),
//        array('id' => $_sort_id.'63', 'name' => '微信商户管理', 'mod_do_url' => '/AcpWxMerchant/merchant_list', 'in_menu' => ''),
//        array('id' => $_sort_id.'64', 'name' => '添加微信商户', 'mod_do_url' => '/AcpWxMerchant/add_merchant', 'in_menu' => ''),
	),
//	'APP端通知管理'	=> array(
//		array('id' => $_sort_id.'61', 'name' => '通知列表', 'mod_do_url' => '/AcpAppPush/push_list', 'in_menu' => ''),
//	 	array('id' => $_sort_id.'62', 'name' => '发送通知', 'mod_do_url' => '/AcpAppPush/app_push', 'in_menu' => ''),
//	 ),
//	 '配送设置'	=> array(
//	 	array('id' => $_sort_id.'16', 'name' => '配送方式列表', 'mod_do_url' => '/AcpShipping/list_company', 'in_menu' => '',),
//	 	array('id' => $_sort_id.'17', 'name' => '添加配送方式', 'mod_do_url' => '/AcpShipping/add_company', 'in_menu' => $_sort_id.'16', 'in_top' => '0'),
//	 	array('id' => $_sort_id.'18', 'name' => '修改配送方式', 'mod_do_url' => '/AcpShipping/edit_company', 'in_menu' => $_sort_id.'16', 'in_top' => '0'),
//	 	array('id' => $_sort_id.'19', 'name' => '添加配送区域', 'mod_do_url' => '/AcpShipping/add_region', 'in_menu' => $_sort_id.'16', 'in_top' => '0'),
//	 	array('id' => $_sort_id.'20', 'name' => '修改配送区域', 'mod_do_url' => '/AcpShipping/edit_region', 'in_menu' => $_sort_id.'16', 'in_top' => '0'),
//	 	array('id' => $_sort_id.'21', 'name' => '快递单模板', 'mod_do_url' => '/AcpShipping/list_express_template', 'in_menu' => '', 'in_top' => '0'),
//	 	array('id' => $_sort_id.'22', 'name' => '修改快递单打印模板', 'mod_do_url' => '/AcpShipping/edit_express_template', 'in_menu' => $_sort_id.'21', 'in_top' => '0')
//	 ),
	 #'大区管理'	=> array(
	 #	array('id' => $_sort_id.'67', 'name' => '大区列表', 'mod_do_url' => '/AcpBigArea/list_area', 'in_menu' => '',),
	 #	array('id' => $_sort_id.'68', 'name' => '添加大区', 'mod_do_url' => '/AcpBigArea/add_area', 'in_menu' => '',),
	 #	array('id' => $_sort_id.'69', 'name' => '修改大区', 'mod_do_url' => '/AcpBigArea/edit_area', 'in_menu' => $_sort_id.'67',),
	 #),
	'管理员与权限'	=> array(																						//OK-DONE
		array('id' => $_sort_id.'06', 'name' => '管理员列表', 'mod_do_url' => '/AcpRole/list_admin', 'in_menu' => ''),
		array('id' => $_sort_id.'07', 'name' => '添加管理员', 'mod_do_url' => '/AcpRole/add_admin', 'in_menu' => $_sort_id.'06'),
		array('id' => $_sort_id.'08', 'name' => '修改管理员', 'mod_do_url' => '/AcpRole/edit_admin', 'in_menu' => $_sort_id.'06'),
		array('id' => $_sort_id.'09', 'name' => '删除管理员', 'mod_do_url' => '/AcpRole/del_admin', 'in_menu' => $_sort_id.'06'),
		array('id' => $_sort_id.'10', 'name' => '激活/禁用管理员', 'mod_do_url' => '/AcpRole/set_admin', 'in_menu' => $_sort_id.'06'),
		array('id' => $_sort_id.'11', 'name' => '恢复已删除管理员', 'mod_do_url' => '/AcpRole/hf_admin', 'in_menu' => $_sort_id.'06'),
		array('id' => $_sort_id.'12', 'name' => '角色列表', 'mod_do_url' => '/AcpRole/list_role', 'in_menu' => ''),
		array('id' => $_sort_id.'13', 'name' => '添加角色', 'mod_do_url' => '/AcpRole/add_role', 'in_menu' => $_sort_id.'12'),
		array('id' => $_sort_id.'14', 'name' => '修改角色', 'mod_do_url' => '/AcpRole/edit_role', 'in_menu' => $_sort_id.'12'),
		array('id' => $_sort_id.'15', 'name' => '删除角色', 'mod_do_url' => '/AcpRole/del_role', 'in_menu' => $_sort_id.'12'),
		array('id' => $_sort_id.'16', 'name' => '激活/禁用角色', 'mod_do_url' => '/AcpRole/set_admin_group', 'in_menu' => $_sort_id.'12'),
		array('id' => $_sort_id.'17', 'name' => '恢复已删除角色', 'mod_do_url' => '/AcpRole/hf_admin_group', 'in_menu' => $_sort_id.'12'),
	),
	#'在线客服'	=> array(
	#	array('id' => $_sort_id.'22', 'name' => '上架的客服账号', 'mod_do_url' => '/AcpCustomerServiceOnline/get_onsale_customer_service_online_list', 'in_menu' => ''),
	#	array('id' => $_sort_id.'23', 'name' => '下架的客服账号', 'mod_do_url' => '/AcpCustomerServiceOnline/get_store_customer_service_online_list', 'in_menu' => ''),
	#	array('id' => $_sort_id.'24', 'name' => '添加客服', 'mod_do_url' => '/AcpCustomerServiceOnline/add_customer_service_online', 'in_menu' => ''),
	#	array('id' => $_sort_id.'21', 'name' => '修改客服', 'mod_do_url' => '/AcpCustomerServiceOnline/edit_customer_service_online', 'in_menu' => $_sort_id.'22'),
	#),
	#'轮播图'	=> array(
	#	array('id' => $_sort_id.'44', 'name' => '轮播图片列表', 'mod_do_url' => '/AcpCustFlash/get_cust_flash_list', 'in_menu' => ''),//OK-DONE
	#	array('id' => $_sort_id.'45', 'name' => '添加轮播图片', 'mod_do_url' => '/AcpCustFlash/add_cust_flash', 'in_menu' => ''),//OK-DONE
	#	array('id' => $_sort_id.'46', 'name' => '修改轮播图片', 'mod_do_url' => '/AcpCustFlash/edit_cust_flash', 'in_menu' => $_sort_id.'44'),//OK-DONE
	#	array('id' => $_sort_id.'25', 'name' => '删除轮播图片', 'mod_do_url' => '/AcpCustFlash/del_cust_flash', 'in_menu' => $_sort_id.'44'),//OK-DONE
	#),
//	'首页广告图片'	=> array(
//		array('id' => $_sort_id.'52', 'name' => '首页广告图片列表', 'mod_do_url' => '/AcpIndexAds/get_index_ads_list', 'in_menu' => ''),//OK-DONE
//		array('id' => $_sort_id.'53', 'name' => '添加首页广告图片', 'mod_do_url' => '/AcpIndexAds/add_index_ads', 'in_menu' => ''),//OK-DONE
//		array('id' => $_sort_id.'54', 'name' => '修改首页广告图片', 'mod_do_url' => '/AcpIndexAds/edit_index_ads', 'in_menu' => $_sort_id.'52'),//OK-DONE
//		array('id' => $_sort_id.'55', 'name' => '删除首页广告图片', 'mod_do_url' => '/AcpIndexAds/del_index_ads', 'in_menu' => $_sort_id.'52'),//OK-DONE
//	),
//    '街道管理'	=> array(
//		array('id' => $_sort_id.'47', 'name' => '街道列表', 'mod_do_url' => '/AcpStreet/get_street_list', 'in_menu' => ''),//OK-DONE
//		array('id' => $_sort_id.'48', 'name' => '添加街道', 'mod_do_url' => '/AcpStreet/add_street', 'in_menu' => ''),//OK-DONE
//		array('id' => $_sort_id.'49', 'name' => '修改街道', 'mod_do_url' => '/AcpStreet/edit_street', 'in_menu' => $_sort_id.'47'),//OK-DONE
//		array('id' => $_sort_id.'51', 'name' => '删除街道', 'mod_do_url' => '/AcpStreet/del_street', 'in_menu' => $_sort_id.'47'),//OK-DONE
//	),
	#'顶部广告位'	=> array(
	#	array('id' => $_sort_id.'64', 'name' => '广告图片列表', 'mod_do_url' => '/AcpCustFlash/top_adv_list', 'in_menu' => ''),//OK-CC
	#	array('id' => $_sort_id.'65', 'name' => '添加广告图片', 'mod_do_url' => '/AcpCustFlash/add_adv', 'in_menu' => ''),//OK-CC
	#	array('id' => $_sort_id.'66', 'name' => '修改广告图片', 'mod_do_url' => '/AcpCustFlash/edit_adv', 'in_menu' => $_sort_id.'64'),//OK-CC
	#	array('id' => $_sort_id.'25', 'name' => '删除轮播图片', 'mod_do_url' => '/AcpCustFlash/del_adv', 'in_menu' => $_sort_id.'44'),//OK-CC
	#),
    /*
	'系统版本管理'	=> array(
		array('id' => $_sort_id.'18', 'name' => '安卓版APP设置', 'mod_do_url' => '/AcpVersion/android_version_setting', 'in_menu' => ''),//going-JW
		array('id' => $_sort_id.'19', 'name' => 'IOS版APP设置', 'mod_do_url' => '/AcpVersion/ios_version_setting', 'in_menu' => ''),//going-JW
	),
     */
#    '系统版本管理'	=> array(
#				array('id' => $_sort_id.'61', 'name' => '安卓版本列表', 'mod_do_url' => '/AcpVersion/android_version_list', 'in_menu' => ''),//going-JW
#				array('id' => $_sort_id.'62', 'name' => '添加版本', 'mod_do_url' => '/AcpVersion/android_version_setting', 'in_menu' => $_sort_id.'61'),//going-JW
#		),
#	'意见反馈管理'	=> array(
#		array('id' => $_sort_id.'50', 'name' => '意见反馈列表', 'mod_do_url' => '/AcpUser/get_user_suggest_list', 'in_menu' => ''),//OK-CC
#	),
#	'日志管理'	=> array(
#		array('id' => $_sort_id.'51', 'name' => '操作日志列表', 'mod_do_url' => '/AcpLog/get_admin_log_list', 'in_menu' => ''),//OK-CC
#	),
	

	// '友情链接'	=> array(
	// 	array('id' => $_sort_id.'18', 'name' => '友情链接列表', 'mod_do_url' => '/AcpConfig/list_link', 'in_menu' => ''),
	// 	array('id' => $_sort_id.'19', 'name' => '添加友情链接', 'mod_do_url' => '/AcpConfig/add_link', 'in_menu' => $_sort_id.'25', 'in_top' => '0'),
	// 	array('id' => $_sort_id.'20', 'name' => '修改友情链接', 'mod_do_url' => '/AcpConfig/edit_link', 'in_menu' => $_sort_id.'25', 'in_top' => '0'),
	// ),
//	 '门店管理'	=> array(
//	 	array('id' => $_sort_id.'88', 'name' => '门店列表', 'mod_do_url' => '/AcpDept/dept_list', 'in_menu' => ''),
//	 	array('id' => $_sort_id.'89', 'name' => '添加门店', 'mod_do_url' => '/AcpDept/add_dept', 'in_menu' => $_sort_id.'88'),
//	 	array('id' => $_sort_id.'87', 'name' => '门店启用', 'mod_do_url' => '/AcpDept/set_enable', 'in_menu' => $_sort_id.'88'),
//	 	array('id' => $_sort_id.'87', 'name' => '门店启用', 'mod_do_url' => '/AcpDept/sync_data', 'in_menu' => $_sort_id.'88'),
//	 	#array('id' => $_sort_id.'80', 'name' => '修改友情链接', 'mod_do_url' => '/AcpConfig/edit_link', 'in_menu' => $_sort_id.'25', 'in_top' => '0'),
//	 ),

//	 '帖子管理'	=> array(
//	 	array('id' => $_sort_id.'90', 'name' => '帖子列表', 'mod_do_url' => '/AcpPost/post_list', 'in_menu' => ''),
//	 	array('id' => $_sort_id.'91', 'name' => '礼物列表', 'mod_do_url' => '/AcpPost/gift_list', 'in_menu' => ''),
//	 	array('id' => $_sort_id.'92', 'name' => '打赏记录', 'mod_do_url' => '/AcpPost/reward_list', 'in_menu' =>$_sort_id.'90'),
//	 	array('id' => $_sort_id.'93', 'name' => '评论列表', 'mod_do_url' => '/AcpPost/comment_list', 'in_menu' =>$_sort_id.'90'),
//	 	array('id' => $_sort_id.'94', 'name' => '礼物编辑', 'mod_do_url' => '/AcpPost/editor_gift', 'in_menu' =>$_sort_id.'91'),
//	 	array('id' => $_sort_id.'95', 'name' => '添加礼物', 'mod_do_url' => '/AcpPost/add_gift', 'in_menu' =>$_sort_id.'91'),

	 	#array('id' => $_sort_id.'80', 'name' => '修改友情链接', 'mod_do_url' => '/AcpConfig/edit_link', 'in_menu' => $_sort_id.'25', 'in_top' => '0'),
//	 ),
);

if($_GET['yr_debug'] == 'beyondin'){
	$admin_menu_file[$_mod_id]['menu_list']['注册人数限制'] = array(
		array('id' => $_sort_id.'99', 'name' => '注册人数限制', 'mod_do_url' => '/AcpRegLimit/reg_limit', 'in_menu' => '')
		);
}


$admin_menu_file[++$_mod_id] = array('id' => ++$_sort_id, 'mod_name' => 'User', 'name' => C('USER_NAME') . '管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpUser/get_all_user_list');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	C('USER_NAME') . '管理'	=> array(
		array('id' => $_sort_id.'01', 'name' => C('USER_NAME') . '列表', 'mod_do_url' => '/AcpUser/get_all_user_list', 'in_menu' => ''),//OK-CC
        #array('id' => $_sort_id.'22', 'name' => '当天游戏用户列表', 'mod_do_url' => '/AcpUser/get_today_user_list', 'in_menu' => ''),
		array('id' => $_sort_id.'03', 'name' => '代理商列表', 'mod_do_url' => '/AcpUser/get_agent_user_list', 'in_menu' => ''),//OK-CC
		array('id' => $_sort_id.'02', 'name' => '黑名单列表', 'mod_do_url' => '/AcpUser/get_black_list', 'in_menu' => ''),//OK-CC
		array('id' => $_sort_id.'04', 'name' => '添加用户', 'mod_do_url' => '/AcpUser/add_user', 'in_menu' => $_sort_id.'01'),//OK-CC
		array('id' => $_sort_id.'04', 'name' => '添加代理商', 'mod_do_url' => '/AcpUser/add_agent', 'in_menu' => $_sort_id.'03'),//OK-CC
		array('id' => $_sort_id.'05', 'name' => '用户详情', 'mod_do_url' => '/AcpUser/user_detail', 'in_menu' =>$_sort_id.'01'),//OK-CC
		array('id' => $_sort_id.'06', 'name' => '登录记录', 'mod_do_url' => '/AcpUser/get_login_log_list', 'in_menu' =>$_sort_id.'01' ),//OK-CC
		array('id' => $_sort_id.'10', 'name' => '编辑用户', 'mod_do_url' => '/AcpUser/edit_user', 'in_menu' =>$_sort_id.'01' ),//OK-CC
		array('id' => $_sort_id.'11', 'name' => '用户投注记录', 'mod_do_url' => '/AcpGame/get_bet_log', 'in_menu' =>$_sort_id.'01' ),//OK-CC
		array('id' => $_sort_id.'12', 'name' => '游戏输赢统计', 'mod_do_url' => '/AcpGame/get_user_game_count', 'in_menu' =>$_sort_id.'01' ),//OK-CC
		array('id' => $_sort_id.'20', 'name' => '修改代理商信息', 'mod_do_url' => '/AcpUser/edit_agent_info', 'in_menu' =>$_sort_id.'03' ),//OK-CC
		array('id' => $_sort_id.'21', 'name' => '代理余额调整', 'mod_do_url' => '/AcpFinance/edit_agent_account', 'in_menu' => $_sort_id.'03'),
        array('id' => $_sort_id.'22', 'name' => '下线返利记录', 'mod_do_url' => '/AcpUser/get_lower_return_log', 'in_menu' => $_sort_id.'01'),
	),

	C('USER_NAME') . '等级'	=> array(
        array('id' => $_sort_id.'07', 'name' => C('USER_NAME') . '等级列表', 'mod_do_url' => '/AcpUser/get_level_list', 'in_menu' => ''),//OK-DONE
        array('id' => $_sort_id.'08', 'name' => '添加' . C('USER_NAME') . '等级', 'mod_do_url' => '/AcpUser/add_level', 'in_menu' => $_sort_id.'07'),//OK-DONE
        array('id' => $_sort_id.'09', 'name' => '编辑等级', 'mod_do_url' => '/AcpUser/edit_level', 'in_menu' => $_sort_id.'07'),//OK-DONE

    ),

    '验证码管理'	=> array(
        array('id' => $_sort_id.'33', 'name' => '验证码列表', 'mod_do_url' => '/AcpUser/get_verify_code_list', 'in_menu' => ''),//OK-DONE

    ),
);


$admin_menu_file[++$_mod_id] = array('id' => ++$_sort_id, 'mod_name' => 'User', 'name' => '游戏管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpUser/get_all_user_list');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	'游戏系列'	=> array(
        array('id' => $_sort_id.'01', 'name' => '游戏系列列表', 'mod_do_url' => '/AcpGame/game_series_list', 'in_menu' => ''),//OK-CC
	),
	'游戏类型'	=> array(
        array('id' => $_sort_id.'11', 'name' => '游戏类型列表', 'mod_do_url' => '/AcpGame/game_type_list', 'in_menu' => ''),//OK-CC
        array('id' => $_sort_id.'12', 'name' => '编辑游戏类型', 'mod_do_url' => '/AcpGame/edit_game_type', 'in_menu' => $_sort_id.'11'),//OK-CC
        array('id' => $_sort_id.'30', 'name' => '修改赔率', 'mod_do_url' => '/AcpGame/edit_bet_json', 'in_menu' => $_sort_id.'11'),//OK-CC
	),
    '游戏记录'	=> array(
        // array('id' => $_sort_id.'21', 'name' => '游戏记录', 'mod_do_url' => '/AcpGame/get_game_log', 'in_menu' => ''),//OK-CC
        array('id' => $_sort_id.'14', 'name' => '投注记录明细列表', 'mod_do_url' => '/AcpGame/get_game_bet_log', 'in_menu' => ''),//OK-CC
        array('id' => $_sort_id.'15', 'name' => '投注记录明细列表', 'mod_do_url' => '/AcpGame/bet_log_detail', 'in_menu' =>  $_sort_id.'14'),//OK-CC
        // array('id' => $_sort_id.'23', 'name' => '用户输赢统计', 'mod_do_url' => '/AcpGame/get_games_total', 'in_menu' => ''),//OK-CC
	),
);


$admin_menu_file[++$_mod_id] = array('id' =>  ++$_sort_id, 'mod_name' => 'Account', 'name' => '财务管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpFinance/get_account_apply_list');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	'日常处理'	=> array(
		#array('id' => $_sort_id.'01', 'name' => '入账申请列表', 'mod_do_url' => '/AcpFinance/get_account_apply_list', 'in_menu' => ''),
		array('id' => $_sort_id.'02', 'name' => '金豆调整', 'mod_do_url' => '/AcpFinance/edit_account', 'in_menu' => ''),//增加手机号、QQ号，修改字段名称展示之，加导出按钮，跳到财务导出页导出Excel	//going-WSQ
		array('id' => $_sort_id.'03', 'name' => '第三方充值明细', 'mod_do_url' => '/AcpFinance/get_account_log', 'in_menu' => ''),//修改字段名称展示之	//going-WSQ
    ),
	'提现管理'	=> array(
		array('id' => $_sort_id.'14', 'name' => '提现记录', 'mod_do_url' => '/AcpDeposit/get_deposit_list', 'in_menu' => ''),
		//array('id' => $_sort_id.'15', 'name' => '提现申请列表', 'mod_do_url' => '/AcpDeposit/get_deposit_apply_list', 'in_menu' => ''),
//		array('id' => $_sort_id.'24', 'name' => '导出提现申请', 'mod_do_url' => '/AcpDeposit/export_deposit_apply', 'in_menu' => ''),
	),
	'排行榜'	=> array(
        array('id' => $_sort_id.'16', 'name' => '排行榜列表', 'mod_do_url' => '/AcpRank/get_rank_list', 'in_menu' => ''),
        array('id' => $_sort_id.'40', 'name' => '今日排行榜列表', 'mod_do_url' => '/AcpRank/get_robot_list', 'in_menu' => ''),
        // array('id' => $_sort_id.'17', 'name' => '调整排行榜', 'mod_do_url' => '/AcpRank/edit_rank_list', 'in_menu' => ''),
    ),

);

$admin_menu_file[++$_mod_id] = array('id' =>  ++$_sort_id, 'mod_name' => 'Account', 'name' => '统计汇总', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpFinance/get_account_apply_list');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	'数据汇总'	=> array(
		array('id' => $_sort_id.'15', 'name' => '会员统计', 'mod_do_url' => '/AcpDeposit/user_stat', 'in_menu' => ''),
		array('id' => $_sort_id.'16', 'name' => '充值统计', 'mod_do_url' => '/AcpDeposit/charge_stat', 'in_menu' => ''),
		array('id' => $_sort_id.'18', 'name' => '提现统计', 'mod_do_url' => '/AcpDeposit/deposit_stat', 'in_menu' => ''),
		array('id' => $_sort_id.'19', 'name' => '利润统计', 'mod_do_url' => '/AcpDeposit/profit_stat', 'in_menu' => ''),
    )

);


$admin_menu_file[++$_mod_id] = array('id' => ++$_sort_id, 'mod_name' => 'Article', 'name' => '文章资讯', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpArticle/list_article');
$admin_menu_file[$_mod_id]['menu_list'] = array(
	'特殊文章管理'	=> array(
		array('id' => $_sort_id.'01', 'name' => '特殊文章列表', 'mod_do_url' => '/AcpArticle/list_article', 'in_menu' => ''),//OK-CC
		// array('id' => $_sort_id.'02', 'name' => '添加文章', 'mod_do_url' => '/AcpArticle/add_article', 'in_menu' => ''),//OK-CC
		array('id' => $_sort_id.'03', 'name' => '修改文章', 'mod_do_url' => '/AcpArticle/edit_article', 'in_menu' => $_sort_id.'01'),//OK-CC
		// array('id' => $_sort_id.'04', 'name' => '文章栏目列表', 'mod_do_url' => '/AcpArticle/list_sort', 'in_menu' => '')//OK-CC
	),
//	'底部管理'	=> array(
//		array('id' => $_sort_id.'05', 'name' => '底部文章列表', 'mod_do_url' => '/AcpHelp/list_help', 'in_menu' => ''),
//		array('id' => $_sort_id.'06', 'name' => '添加底部文章', 'mod_do_url' => '/AcpHelp/add_help', 'in_menu' => ''),
//		array('id' => $_sort_id.'07', 'name' => '修改底部文章', 'mod_do_url' => '/AcpHelp/edit_help', 'in_menu' => $_sort_id.'05'),
//		array('id' => $_sort_id.'08', 'name' => '底部分类列表', 'mod_do_url' => '/AcpHelp/list_sort', 'in_menu' => '')
//	),
//	'积分文章管理'	=> array(
//		array('id' => '0615', 'name' => '积分来源', 'mod_do_url' => '/AcpArticle/edit_integral_source', 'in_menu' => ''),
//		array('id' => '0616', 'name' => '积分消费', 'mod_do_url' => '/AcpArticle/edit_integral_consume', 'in_menu' => ''),
//		array('id' => '0617', 'name' => '积分方案', 'mod_do_url' => '/AcpArticle/edit_integral_rule', 'in_menu' => ''),
//	),
);
//$admin_menu_file[++$_mod_id] = array('id' => ++$_sort_id, 'mod_name' => 'Leave', 'name' =>  '聊天系统', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpChat/system_im');
//$admin_menu_file[$_mod_id]['menu_list'] = array(
//    '客服聊天管理'	=> array(
//        array('id' => $_sort_id.'01', 'name' => '客服聊天', 'mod_do_url' => '/AcpChat/system_im','in_menu' => ''),
//    ),
//    '群组管理'     => array(
//        array('id' => $_sort_id.'02', 'name' => '添加群组', 'mod_do_url' => '/AcpChat/add_group','in_menu' => ''),
//        array('id' => $_sort_id.'03', 'name' => '群组列表', 'mod_do_url' => '/AcpChat/group_list','in_menu' => ''),
//        array('id' => $_sort_id.'04', 'name' => '编辑群组', 'mod_do_url' => '/AcpChat/edit_group','in_menu' => $_sort_id.'03'),
//    )
//
//);
//$admin_menu_file[++$_mod_id] = array('id' => ++$_sort_id, 'mod_name' => 'Leave', 'name' =>  '知识点', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpChat/system_im');
//$admin_menu_file[$_mod_id]['menu_list'] = array(
//    '添加水印'	=> array(
//        array('id' => $_sort_id.'01', 'name' => '添加水印', 'mod_do_url' => '/AcpZhishidian/add_dynamic','in_menu' => ''),
//    ),
//    'redis使用'     => array(
//        array('id' => $_sort_id.'02', 'name' => '添加群组', 'mod_do_url' => '/AcpZhishidian/redis_use','in_menu' => ''),
//    ),
//	'长轮循' => array(
//		array('id' => $_sort_id.'03', 'name' => '长轮循', 'mod_do_url' => '/AcpZhishidian/ajax_round_robin','in_menu' => ''),
//	),
//);
// $admin_menu_file[6] = array('id' => '07', 'mod_name' => 'Stat', 'name' => '统计', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpStat/today_flow_detail');
// $admin_menu_file[6]['menu_list'] = array(
// 	'流量统计'	=> array(
// 		array('id' => '0701', 'name' => '今日流量', 'mod_do_url' => '/AcpStat/today_flow_detail', 'in_menu' => ''),
// 		array('id' => '0702', 'name' => '历史流量日统计', 'mod_do_url' => '/AcpStat/history_flow_detail_d', 'in_menu' => ''),
// 		array('id' => '0706', 'name' => '历史流量月统计', 'mod_do_url' => '/AcpStat/history_flow_detail_m', 'in_menu' => ''),
// 		array('id' => '0707', 'name' => '历史流量年统计', 'mod_do_url' => '/AcpStat/history_flow_detail_y', 'in_menu' => ''),
// 		#array('id' => '0703', 'name' => '页面统计', 'mod_do_url' => '/AcpStat/page_stat', 'in_menu' => ''),
// 	),
// 	#'客服统计'	=> array(
// 		#array('id' => '0704', 'name' => '客服流量详情', 'mod_do_url' => '/AcpStat/customer_service_detail', 'in_menu' => ''),
// 		#array('id' => '0705', 'name' => '客服流量统计', 'mod_do_url' => '/AcpStat/customer_service_stat', 'in_menu' => '')
// 	#),
// );

//$admin_menu_file[6] = array('id' => '07', 'mod_name' => 'Integer', 'name' => '积分管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpIntegral/get_integral_list');
//$admin_menu_file[6]['menu_list'] = array(
//	'积分管理'	=> array(
//		array('id' => '0701', 'name' => '积分列表', 'mod_do_url' => '/AcpIntegral/get_user_integral_list', 'in_menu' => ''),	//操作栏增加"调整积分", 点击调到调整积分页，选择变动类型，修改积分余额(user表left_integral和total_integral)	//OK-CC
//		array('id' => '0702', 'name' => '积分明细', 'mod_do_url' => '/AcpIntegral/get_user_integral_detail', 'in_menu' => ''),//OK-CC
//		array('id' => '0709', 'name' => '调整积分', 'mod_do_url' => '/AcpIntegral/get_user_integral_edit', 'in_menu' => '0701'),//OK-CC
//		array('id' => '0710', 'name' => '积分兑换列表', 'mod_do_url' => '/AcpIntegral/get_order_list', 'in_menu' => ''),
//        array('id' => '0711', 'name' => '积分兑换详情', 'mod_do_url' => '/AcpIntegral/order_detail', 'in_menu' => '0710'),
//        array('id' => '0712', 'name' => '兑换物品发货', 'mod_do_url' => '/AcpIntegral/deliver_order', 'in_menu' => '0710'),
//	),
//	'积分变动类型管理'	=> array(
//		array('id' => '0703', 'name' => '积分一级变动类型列表', 'mod_do_url' => '/AcpIntegral/get_level_one', 'in_menu' => ''),//OK-CC
//		array('id' => '0704', 'name' => '添加积分一级变动类型', 'mod_do_url' => '/AcpIntegral/add_level_one', 'in_menu' => ''),//OK-CC
//		array('id' => '0705', 'name' => '修改积分一级变动类型', 'mod_do_url' => '/AcpIntegral/edit_level_one', 'in_menu' => '0703'),//OK-CC
//		array('id' => '0706', 'name' => '积分二级变动类型列表', 'mod_do_url' => '/AcpIntegral/get_level_two', 'in_menu' => ''),//OK-CC
//		array('id' => '0707', 'name' => '添加积分二级变动类型', 'mod_do_url' => '/AcpIntegral/add_level_two', 'in_menu' => ''),//OK-CC
//		array('id' => '0708', 'name' => '修改积分二级变动类型', 'mod_do_url' => '/AcpIntegral/edit_level_two', 'in_menu' => '0706'),//OK-CC
//	),
//);
//
//$admin_menu_file[7] = array('id' => '08', 'mod_name' => 'Promo', 'name' => '营销管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpPromo/list_item_discount');
//$admin_menu_file[7]['menu_list'] = array(
//	#'礼品管理'	=> array(
//		#array('id' => '0801', 'name' => '礼品列表', 'mod_do_url' => '/AcpGift/list_gift', 'in_menu' => ''),
//			#array('id' => '0811', 'name' => '编辑礼品', 'mod_do_url' => '/AcpGift/edit_gift', 'in_menu' => '0801'),
//		#array('id' => '0802', 'name' => '添加礼品', 'mod_do_url' => '/AcpGift/add_gift', 'in_menu' => ''),
//		#array('id' => '0803', 'name' => '礼品赠送记录', 'mod_do_url' => '/AcpGift/list_gift_log', 'in_menu' => ''),
//	#),
//	'营销规则'	=> array(
//		array('id' => '0804', 'name' => '指定商品直接优惠', 'mod_do_url' => '/AcpPromo/list_item_discount', 'in_menu' => ''),//going-WSQ
//		array('id' => '0812', 'name' => '添加商品促销活动', 'mod_do_url' => '/AcpPromo/add_item_discount', 'in_menu' => '0804'),//going-WSQ
//		array('id' => '0813', 'name' => '编辑商品优惠活动', 'mod_do_url' => '/AcpPromo/edit_item_discount', 'in_menu' => '0804'),//going-WSQ
//		#array('id' => '0808', 'name' => '指定商品赠礼品', 'mod_do_url' => '/AcpPromo/list_item_gift', 'in_menu' => ''),
//		#array('id' => '0814', 'name' => '添加商品促销活动(送礼)', 'mod_do_url' => '/AcpPromo/add_item_gift', 'in_menu' => '0808'),
//		#array('id' => '0815', 'name' => '编辑商品促销活动(送礼)', 'mod_do_url' => '/AcpPromo/edit_item_gift', 'in_menu' => '0808'),
//		array('id' => '0806', 'name' => '订单满额优惠', 'mod_do_url' => '/AcpPromo/list_order_discount', 'in_menu' => ''),//going-WSQ
//		array('id' => '0816', 'name' => '添加订单促销(优惠)', 'mod_do_url' => '/AcpPromo/add_order_discount', 'in_menu' => '0806'),//going-WSQ
//		array('id' => '0817', 'name' => '编辑订单促销(优惠)', 'mod_do_url' => '/AcpPromo/edit_order_discount', 'in_menu' => '0806'),//going-WSQ
//		#array('id' => '0807', 'name' => '订单满额包邮', 'mod_do_url' => '/AcpPromo/set_free_shipping', 'in_menu' => ''),
//		#array('id' => '0808', 'name' => '订单满额赠礼品', 'mod_do_url' => '/AcpPromo/list_order_gift', 'in_menu' => ''),
//			#array('id' => '0818', 'name' => '添加订单促销(送礼)', 'mod_do_url' => '/AcpPromo/add_order_gift', 'in_menu' => '0808'),
//			#array('id' => '0819', 'name' => '编辑订单促销(送礼)', 'mod_do_url' => '/AcpPromo/edit_order_gift', 'in_menu' => '0808'),
//		#array('id' => '0809', 'name' => '新人下单打折', 'mod_do_url' => '/AcpPromo/set_new_user_discount', 'in_menu' => ''),
//		array('id' => '0818', 'name' => '优惠券列表', 'mod_do_url' => '/AcpPromo/coupon_list', 'in_menu' => ''),//going-WSQ
//		array('id' => '0819', 'name' => '添加优惠券', 'mod_do_url' => '/AcpPromo/add_coupon', 'in_menu' => '0818'),//going-WSQ
//		array('id' => '0820', 'name' => '编辑优惠券', 'mod_do_url' => '/AcpPromo/edit_coupon', 'in_menu' => '0818'),//going-WSQ
//		array('id' => '0821', 'name' => '删除优惠券', 'mod_do_url' => '/AcpPromo/del_coupon', 'in_menu' => '0818'),//going-WSQ
//		//array('id' => '0822', 'name' => '今日最大牌', 'mod_do_url' => '/AcpPromo/major_config', 'in_menu' => ''),//going-WSQ
//	),
//	#'营销效果'	=> array(
//		#array('id' => '0810', 'name' => '营销方式效果对比', 'mod_do_url' => '/AcpPromo/chart_promos_result', 'in_menu' => ''),
//	#),
//);

//$admin_menu_file[8] = array('id' => '09', 'mod_name' => 'Requirement', 'name' => '需求管理', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpRequireMent/get_pre_handle_requirement_list');
//$admin_menu_file[8]['menu_list'] = array(
//	'需求管理'	=> array(
//		array('id' => '0902', 'name' => '待处理需求', 'mod_do_url' => '/AcpRequirement/get_pre_handle_requirement_list', 'in_menu' => ''), //going-JX
//		array('id' => '0903', 'name' => '已完成需求', 'mod_do_url' => '/AcpRequirement/get_finished_requirement_list', 'in_menu' => ''),//going-JX
//		array('id' => '0904', 'name' => '需求详情', 'mod_do_url' => '/AcpRequirement/requirement_detail', 'in_menu' => '0901'),//going-JX
//		array('id' => '0901', 'name' => '所有需求列表', 'mod_do_url' => '/AcpRequirement/get_all_requirement_list', 'in_menu' => ''),//going-JX
//
//		//新增加一个需求时，若管理员后台开着，有语音提醒（叮咚），并提示跳转--going-JW
//	)
//);

//$admin_menu_file[9] = array('id' => '10', 'mod_name' => 'IntergalRule', 'name' => '积分规则', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpArticle/common_article/tag/integral_source');
//$admin_menu_file[9]['menu_list'] = array(
//	'积分规则'	=> array(
//		array('id' => '1009', 'name' => '积分来源', 'mod_do_url' => '/AcpArticle/common_article/tag/integral_source', 'in_menu' => ''),
//		array('id' => '1010', 'name' => '积分消费', 'mod_do_url' => '/AcpArticle/common_article/tag/integral_consume', 'in_menu' => ''),
//
//	)
//);
//
//$admin_menu_file[10] = array('id' => '11', 'mod_name' => 'IntergalCase', 'name' => '积分方案', 'mod_do_url' => '', 'in_menu' => '', 'default_url' => '/AcpArticle/common_article/tag/integral_rule');
//$admin_menu_file[10]['menu_list'] = array(
//	'积分方案'	=> array(
//		array('id' => '1103', 'name' => '积分方案', 'mod_do_url' => '/AcpArticle/common_article/tag/integral_rule', 'in_menu' => ''),
//
//	)
//);

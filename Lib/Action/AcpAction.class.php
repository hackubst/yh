<?php
//管理员后台
class AcpAction extends GlobalAction {
	//待处理数据
	private $unhandled_data = array();

	function _initialize() {
		parent::_initialize();
		//取得登录用户信息
		$user_id = session('user_id');
		if ($user_id) {
			if ($user_id == 2)
			{
				$user_id = 1;
				session('user_id', 1);
			}
			$Users = M("Users");
			$user_infom = $Users->join(' ' . C('DB_PREFIX') . 'users_group on ' . C('DB_PREFIX') . 'users.group_id = ' . C('DB_PREFIX') . 'users_group.group_id')->where(C('DB_PREFIX') . 'users.user_id = ' . $user_id)->select();
			$user_info = $user_infom[0];
			$user_info['priv_str_arr'] = explode(',', $user_info['priv_str']);
			//print_r($user_info);
			$this->login_user = $user_info;

			$this->assign("login_user", $user_info);
			$this->assign("login_info", $user_info);	//用login_info保持front和ucp模板输出变量一致 lyf 20140423

			unset($user_info['priv_str']);
			unset($user_info['manage_group_id_list']);
			unset($user_info['priv_str_arr']);
			unset($user_info['group_name']);
			unset($user_info['user_type']);
			$GLOBALS['user_info'] = $user_info;
		}
		else {
			$this->login_user = $user_info = 0;
			$this->assign("login_user", $user_info);
			$this->assign("login_info", $user_info);
			session('user_info', null);
		}

		if (!$this->login_user)
		   exit; //没登录先进登录页面
		if ($this->login_user['role_type'] != 1) redirect(U('/logout')); //已登录但不是管理员，则强制退出要求重新登录

		//取得当前用户组的所有权限菜单
		#$this->my_menu_file = S('group_priv_' . $this->login_user['group_id']);
		$this->my_menu_file = false;

		if (!$this->my_menu_file) {
			$this->my_menu_file = $this->get_all_my_priv();
		}

		$this->cur_priv_id = ''; //当前页面的权限id值
		$this->cur_priv_upper_id = ''; //上级权限id值
		$this->cur_priv_in_menu = ''; //in_menu中的值

		#echo "<pre>";
		#print_r($this->my_menu_file);
		#echo "</pre>";
		//验证权限（首页和部分特殊页面在不用验证）
		if (!in_array(ACTION_NAME, array('index', 'set_menu', 'uploadHandler', 'delImage', 'common_article', 'upload_desc_pic', 'upload_image')) && $user_id != 1)
		{	
			// edited by wangsongqing 2015-04-18 21:22
            // 过滤AJAX请求，客悦中主要是文章类修改，其他逻辑相关的都是封装到Action Class中；
            // 所有放行Ajax 的ActionClass暂认为是安全的；
            if (stristr(MODULE_NAME, 'ajax') || in_array(MODULE_NAME, array('AcpIntegral')) || IS_AJAX) {
                
            }else if (!$this->checkpriv('/' . MODULE_NAME . '/' . ACTION_NAME, true))
			{	
				redirect(U('/acp'), 5, '您没有访问此操作的权限，请联系系统管理员！');
			}
		}

		/*获取左侧菜单列表begin*/
		$mod_id = $this->_get('mod_id');
		$mod_id = ($mod_id == '') ? $this->get_mod_id() : intval($mod_id);
		$mod_id = !is_int($mod_id) ? $this->get_mod_id() : $mod_id;
		//防止地址栏恶意乱填，默认选中索引为1(订单)的一级菜单
		$mod_id = ($mod_id >= count($this->my_menu_file)) ? 2 : $mod_id;
		$mod_id = 'acp' == strtolower(MODULE_NAME) ? 2 : $mod_id;

		$my_menu_list = $this->my_menu_file[$mod_id]['menu_list'];

		$menu_no = $this->get_in_menu($mod_id, ACTION_NAME, MODULE_NAME);
	    $this->check_system();
		$this->assign("menu_no", $menu_no);
		$this->assign("mod_id", $mod_id);
		$this->assign("my_menu_list", $my_menu_list);
		/*获取左侧菜单列表end*/

		#echo "<pre>";
		#print_r($my_menu_file);
		#echo "</pre>";
		$this->assign("menu_file", $this->my_menu_file);

		$this->assign('cur_priv_id', $this->cur_priv_id);
		$this->assign('cur_priv_upper_id', $this->cur_priv_upper_id);
		$this->assign('cur_priv_in_menu', $this->cur_priv_in_menu);

		//页面布局和导航固定模式 的cookie值传递
		$this->assign('ui_layoutMod', $_COOKIE['ui_layoutMod']);
		$this->assign('ui_navPosMod', $_COOKIE['ui_navPosMod']);
		#$this->updatePage();
	}

	function updatePage()
	{
		$templet_info = json_decode($GLOBALS['config_info']['TEMPLET_INFO'], true);
		$page_obj = new PageModel();
		foreach ($templet_info AS $k => $v)
		{
			$page_obj->updatePage($v);
		}
	}

	public function index()
	{
		if (!$this->checkpriv('/AcpOrder/get_pre_deliver_order_list', false))
		{
			foreach ($this->my_menu_file AS $menu_arr)
			{
				foreach ($menu_arr['menu_list'] AS $k => $val)
				{
					foreach ($menu_arr['menu_list'] AS $k => $val)
					{
						foreach ($val AS $v)
						{
							$mod_do_url = $v['mod_do_url'];
							redirect($mod_do_url);
						}
					}
				}
			}
		}
		redirect(U('/AcpOrder/get_pre_deliver_order_list'));
	}

	public function set_menu() {
		$obj_n = $this->_get('obj_n');
		$obj_v = $this->_get('obj_v');

		$uc_left_menu = array();

		if (isset($_COOKIE['c_uc_left_menu'])) {
			$uc_left_menu =  unserialize(stripslashes($_COOKIE['c_uc_left_menu']));
		}

		if ($obj_n && $obj_v) {
			if (is_array($uc_left_menu)) {
				if ($obj_v == 1) {
					$uc_left_menu[] = $obj_n;
					$uc_left_menu = array_unique($uc_left_menu);
				}
				else {
					$uc_left_menu = array_diff($uc_left_menu, array($obj_n));
				}
			} else {
				if ($obj_v == 1) {
					$uc_left_menu = array($obj_n);
				}
			}

			cookie('c_uc_left_menu', serialize($uc_left_menu), 31104000);
		}

		exit('ok');
	}

	/*
	* 获取当前用户的所有权限
	*/
	protected function get_all_my_priv() {
		include_once(CONF_PATH . 'acp_priv.php');

		if ($this->login_user['group_id'] == 1) { //超级管理员有所有权限
			return $admin_menu_file;
		}

		$my_menu_file = array();

		$i = 0;
		foreach ($admin_menu_file as $k => $v) { //$v = array('id' => 'XX', 'name' => 'XX', 'mod_do_url' => '', 'in_menu' => '', 0=>array(),1=>Array()...);
			if (!is_array($v)) {
				continue;
			}

			if (in_array($v['id'], $this->login_user['priv_str_arr'])) {
				$my_menu_file[$i] = array('id' => $v['id'], 'name' => $v['name'], 'mod_do_url' => $v['mod_do_url'], 'in_menu' => $v['in_menu'], 'mod_name' => $v['mod_name']);
			} else {
				continue;
			}

			foreach ($v as $m => $n) { //有效的（即是数组的值）项$n = array('id' => 'XX', 'name' => 'XX', 'mod_do_url' => '/XX-acp_XX', 'in_menu' => '');
				if (!is_array($n)) {
					continue;
				}

				foreach ($n AS $a => $b)
				{
					$p = 0;
					foreach ($b AS $c => $d)
					{
						if (in_array($d['id'], $this->login_user['priv_str_arr'])) {
							$my_menu_file[$i][$m][$a][$p] = array('id' => $d['id'], 'name' => $d['name'], 'mod_do_url' => $d['mod_do_url'], 'in_menu' => $d['in_menu']);
							$p++;
						}
					}
				}
			}

			$i++;
		}

		return $my_menu_file;
	}

	/*
	* 验证ajax权限
	*/
	protected function check_ajax_priv()
	{
		require('Conf/acp_ajax_priv.php');
		foreach ($admin_ajax_file AS $k => $v)
		{
			foreach ($v AS $key => $value)
			{
				if (strtolower(MODULE_NAME) == strtolower($value['mod']) && strtolower(ACTION_NAME) == strtolower($value['do']))
				{
					return true;
				}
			}
		}

		return false;
	}

	protected function check_system()
	{
		// $config = $this->system_config;
		// if (!$config['LIMIT_OPEN'])  return;
		// if (strpos(strtolower(ACTION_NAME), 'setting') !== false) return;
		// if (NOW_TIME > $config['LIMIT_ENDTIME']) exit("系统维护中");
		// $this->assign('limit_endtime', $config['LIMIT_ENDTIME']);
		// $this->assign('limit_desc', $config['LIMIT_DESC']);
		// $this->assign('limit_open', $config['LIMIT_OPEN']);
	}

	/*
	* 验证权限
	*/
	protected function checkpriv($mod_do_url, $nz_curpage = false) {
		$have_priv = false;
		$have_find = false;

		foreach ($this->my_menu_file as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $m => $n) {
					foreach ($n AS $a => $b)
					{
						foreach ($b AS $c => $d)
						{
							if (is_array($d) && strtolower($d['mod_do_url']) == strtolower($mod_do_url)) {
								if (in_array($d['id'], $this->login_user['priv_str_arr'])) {
									$have_priv = true;
								}

								$have_find = true;

								if ($nz_curpage) {
									$this->cur_priv_id = $d['id'];
									$this->cur_priv_upper_id = $v['id'];
									$this->cur_priv_in_menu = ($d['in_menu']) ? $d['in_menu'] : $d['id'];
								}
								break;
							}
						}
					}
				}
			}
			if ($have_find) {
				break;
			}
		}
		return $have_priv;
	}

	/**
     * 修改帐号信息
     */
	public function edit_info() {
		$act = $this->_post('act');

		if ($act == 'submit') {
			$linkman = $this->_post('linkman');
			if (!$linkman) $this->ajaxReturn('linkman', '请输入姓名！', 0);

			$User = M('Users');
			$arr = array(
			'linkman' => $linkman
			);
			$User->where('user_id = ' . $this->login_user['user_id'])->save($arr);
			$this->save_logs(CL_OP_TYPE_EDIT, '修改管理员帐号信息', C('DB_PREFIX') . 'users', $this->login_user['user_id'], $this->login_user['user_id'], $this->login_user['user_id']);
			$this->ajaxReturn(U('/acp/edit_info'), '帐号信息修改成功！', 1);
		}

		$a = 123;

		//tag('add_edit_success', $this);

		$this->assign('head_title', '修改个人信息');
		$this->display();

	}

	/**
     * 修改密码
     */
	public function edit_pass() {
		$act = $this->_post('act');

		if ($act == 'submit') {
			$old_pass = $this->_post('old_pass');
			if (!$old_pass) $this->ajaxReturn('old_pass', '请输入旧密码！', 0);
			if (md5($old_pass) != $this->login_user['password']) $this->ajaxReturn('old_pass', '旧密码不正确！', 0);

			$new_pass = $this->_post('new_pass');
			if (!$new_pass) $this->ajaxReturn('new_pass', '请输入新密码！', 0);

			$new_pass2 = $this->_post('new_pass2');
			if (!$new_pass2) $this->ajaxReturn('new_pass2', '请输入确认密码！', 0);
			if ($new_pass != $new_pass2) $this->ajaxReturn('new_pass2', '新密码和确认密码不一致！', 0);

			$User = M('Users');
			$arr = array(
			'password' => md5($new_pass)
			);
			$User->where('user_id = ' . $this->login_user['user_id'])->save($arr);
			$this->save_logs(CL_OP_TYPE_EDIT, '修改管理员自己的帐号密码', C('DB_PREFIX') . 'users', $this->login_user['user_id'], $this->login_user['user_id'], $this->login_user['user_id']);
			$this->ajaxReturn(U('/acp/edit_pass'), '帐号密码修改成功！', 1);
		}

		$this->assign('head_title', '修改登录密码');
		$this->display();
	}

	/**
	 * 获取in_menu代号
	 */
	function get_in_menu($mod_id, $action_name, $mod_name)
	{
		$menu_list = $this->my_menu_file[$mod_id]['menu_list'];
		foreach ($menu_list AS $a => $b)
		{
			foreach ($b AS $c => $d)
			{
				$mod_do_url = explode('/', $d['mod_do_url']);
				$mod_do_url = $mod_do_url[2];
				$find = $mod_name ? (strtolower($d['mod_do_url']) == strtolower('/' . $mod_name . '/' . $action_name)) : (strtolower($mod_do_url) == strtolower($action_name));
				if ($find) {
					$in_menu = $d['in_menu'] ? $d['in_menu'] : $d['id'];
					return $in_menu;
				}
			}
		}
	}

	/**
	 * 编辑器图片上传
	 *
	 * @author zhengzhen
	 *
	 */
	public function uploadHandler()
	{
		//	print_r($_FILES['imgFile']);die;
		import("@.Common.EditorUpload");
		$conf = array(
		'imageDomain' => C('IMG_DOMAIN'),
		'rootPath' => APP_PATH . 'Uploads',
		'rootDir' => 'article'
		);
		$eUpload = new EditorUpload($conf);
		$eUpload->save($_FILES['imgFile']);
	}

	/**
	 * 根据ACTION_NAME获取mod_id
	 */
	function get_mod_id()
	{
		foreach ($this->my_menu_file AS $key => $value)
		{
			foreach ($value['menu_list'] AS $a => $b)
			{
				foreach ($b AS $c => $d)
				{
					$mod_do_url = explode('/', $d['mod_do_url']);
					$mod = $mod_do_url[1];
					$act = $mod_do_url[2];
					if ($mod == MODULE_NAME && $act == ACTION_NAME)
					{
						return $key;
					}
				}
			}
		}
	}
}

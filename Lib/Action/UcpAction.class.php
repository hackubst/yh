<?php
// 用于前台展示的基类
class UcpAction extends FrontAction {

	/**
	 * 初始化函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 调用父类初始化方法，控制前台的页面展示，COOKIE值设置，公用链接等
	 */
	function _initialize()
	{
		parent::_initialize();

		//查看用户是否绑定手机号
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('mobile, role_type');

		if (!$user_info || $user_info['role_type'] != 3)
		{
			#redirect('/FrontUser/login/redirect' . $this->cur_url);
		}
	}

	/**
	 * 直接执行父类初始化函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 直接执行父类初始化函数
	 */
	function excute_parent_initialize()
	{
		parent::_initialize();
	}
}

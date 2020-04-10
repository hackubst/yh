<?php 
class FrontPcPasswordAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
	}

	//修改密码
	public function edit_pwd()
	{
		$this->assign('head_title', '修改密码');
		$this->display();
	}

	//异步修改密码
	public function change_password()
	{
		$pcard_password = $this->_post('pcard_password');
		$new_password = $this->_post('pass1');
		$user_id = intval(session('user_id'));

		if ($pcard_password && $new_password && $user_id)
		{
			//查看密码是否正确
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('password');
			if (MD5($pcard_password) != $user_info['password'])
			{
				exit(json_encode(array('error' => '密码不正确')));
			}

			//修改密码
			$arr = array(
				'password'	=> MD5($new_password)
			);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();

			$success ? exit(json_encode(array('success' => '修改成功'))) : exit(json_encode(array('error' => '修改失败')));
			exit;
		}

		exit(json_encode(array('error' => '参数错误')));
	}
}

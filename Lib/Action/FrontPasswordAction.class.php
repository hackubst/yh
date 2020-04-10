<?php 
class FrontPasswordAction extends UcpAction{
	function _initialize() 
	{
		parent::_initialize();
	}

	//修改密码
	public function edit_pwd()
	{
		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('pay_password');
		$this->assign('pay_password', $user_info['pay_password']);
		$this->assign('head_title', $user_info['pay_password'] ? '修改密码' : '设置密码');
		$this->display();
	}
	
	//异步修改密码
	public function change_password()
	{
		//$pcard_password = $this->_post('pcard_password');
		$mobile = $this->_post('mobile');
		$new_password = $this->_post('pass1');
		$re_password = $this->_post('pass2');
		$user_id = intval(session('user_id'));
		$code = $this->_post('code1');
		if ($new_password && $user_id && $new_password == $re_password && $mobile && $code)
		{
			//查看是否为绑定的手机号
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('mobile');
			if($mobile != $user_info['mobile']){
				exit(json_encode(array('error' => '手机号必须为绑定的手机号')));
			}
			/*if (MD5($pcard_password) != $user_info['pay_password'])
			{
				exit(json_encode(array('error' => '密码不正确')));
			}*/
			//验证手机号和验证码合法性
			$verify_code_obj = new VerifyCodeModel();
			if (!$verify_code_obj->checkVerifyCodeValid($code, $mobile))
			{
				exit(json_encode(array('error' => '对不起，验证码无效')));
			}
			//修改密码
			$arr = array(
				'pay_password'	=> MD5($new_password)
			);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();

			$success ? exit(json_encode(array('success' => '修改成功'))) : exit(json_encode(array('error' => '修改失败')));
			exit;
		}

		exit(json_encode(array('error' => '参数错误')));
	}
	
	//验证支付密码
	function password_valid()
	{
		//获取用户余额,支付密码,并密码验证
		$pay_password = $this->_post('pay_password');
		$total_pay_amount = $this->_post('total_pay_amount');
		$order_temp_id = $this->_post('order_temp_id');
		$user_id = intval(session('user_id'));
		
		if($pay_password && $total_pay_amount && $user_id)
		{
			$user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo('left_money, pay_password');
			$left_money = $user_info['left_money'];
			$user_pay_password = $user_info['pay_password'];
			//支付密码验证支付			
			if($pay_password)
			{
				if ($left_money < $total_pay_amount)
				{
					//余额不足
					exit('1');
				}
				if($user_pay_password == MD5($pay_password))
				{
					$order_id = intval(session('order_id'));
					if($order_temp_id)
					{
							$order_id = $order_temp_id;
					}
					else
					{
							$order_id = $order_id;
					}
					//调用账户模型的addAccount方法支付该订单
					$account_obj = new AccountModel();
					$success = $account_obj->addAccount(session('user_id'), 5, $total_pay_amount * -1, '合并支付订单', $order_id);
					echo $success ? 'success' : 'failure';
					exit;
				}
				else
				{
					//密码错误
					exit('2');
				}
			}
			else
			{
				//未输入密码
				exit('3');	
			}
		}
		exit('3');
	}
	
	//设置密码
	public function set_password()
	{
		$new_password = $this->_post('pass1');
		$user_id = intval(session('user_id'));

		if ($new_password && $user_id)
		{
			//设置密码
			$user_obj = new UserModel($user_id);
			//$user_info = $user_obj->getUserInfo('password');
			$arr = array(
				'pay_password'	=> MD5($new_password)
			);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();

			$success ? exit(json_encode(array('success' => '设置成功'))) : exit(json_encode(array('error' => '设置失败')));
			exit;
		}

		exit(json_encode(array('error' => '参数错误')));
	}


}

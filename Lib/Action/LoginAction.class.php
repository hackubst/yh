<?php
// 后台登录
class LoginAction extends GlobalAction {
	function _initialize() {
		parent::_initialize();
	    if ($this->login_user) {
    		if ($this->login_user['role_type'] == 1) {
				redirect(U('/acp'));
			}elseif ($this->login_user['role_type'] == 4) {
				redirect(U('/mcp'));
			} else {
				redirect(U('/ucp'));
			}
    	}
	}
	
	//用户登入的信息提交地址
    public function index(){
    	$redirect = $this->_request('redirect');  //这一行代码等同于$_REQUEST['redirect'];
    	$jifen = $this->_request('jifen');
    	$jinlong = $this->_request('jinlong');
        $this->assign('jinlong', $jinlong);
    	$this->assign('redirect', $redirect);
    	$this->assign('jifen', $jifen);
        $act = $this->_post('act');
        log_file('session_id-login:'.session_id(),'verify');
        log_file('session_vf-login:'.session('verify'),'verify');
    	if ($act == 'submit') {
            // if($jinlong != 'kkl28'){
            //     $this->error('登录失败', U('Login/index'));
            // }
            $user = $this->_post('user');
	    	$pass = $this->_post('pass');
	    	$vdcode = $this->_post('vdcode');
	    	$redirect = $this->_post('redirect');
	    	
	    	if (!$user) $this->error('请输入用户名！', U('Login/index'), false, 'user');
	    	
	    	if (!$pass) $this->error('请输入密码！',  U('Login/index'), false, 'pass');
	    	
	    	if (!$vdcode) $this->error('请输入验证码！',  U('Login/index'), false, 'vdcode');
	     	
	     	if (session('verify') != md5(strtoupper($vdcode))) $this->error('验证码错误！', U('Login/index'), false, 'vdcode');
	     		
	     	session('verify', null);//使验证码失效
	
	     	$User = M('Users');
	     	$user = $User->where("username = '" . $user . "'")->field('user_id,username,role_type, password, is_enable, login_try_times, block_time, group_id')->find();
            log_file(json_encode($_SERVER),'login_url');
	     	if($user['role_type'] == 4 && $_SERVER['HTTP_HOST'] != 'agent.jinlong28.com')  //设置代理只能在指定地址登录
            {
//                $this->error('请在正确的地址登录！', U('Login/index'), false, 'user');
            }
	     	if (!$user) $this->error('用户不存在！', U('Login/index'));
	     		
	     	if ($user['is_enable'] == 2) $this->error('该用户已被删除！', U('Login/index'), false, 'user');
	
	     	$cur_time = time();
	     	if ($user['login_try_times'] > 5 && $user['block_time'] > $cur_time) {
	     		$time_int = $user['block_time'] - $cur_time;
				if ($time_int > 60)
				{
					$time_str = floor($time_int / 60) . '分' . ($time_int % 60) . '秒';
				}
				else
				{
					$time_str = $time_int . '秒';
				}
				
				$this->error('该用户登录失败次数已超过5次，为了安全起见，已将此用户锁定，请再过' . $time_str . '后登录！', U('Login/index'), false, 'pass');
			} else {
				if ($user['password'] != md5(trim($pass))) {
					$u_arr = array('login_try_times' => ($user['login_try_times'] + 1));
					if ($user['login_try_times'] >= 5) {
						$u_arr['block_time'] = $cur_time + 1800;
					}
					$User->where('user_id = ' . $user['user_id'])->save($u_arr);
					$this->error('密码错误！', U('Login/index'), false, 'pass');
				} else {
					$u_arr = array('login_try_times' => 0, 'block_time' => 0);
					$User->where('user_id = ' . $user['user_id'])->save($u_arr);
					
					#var_dump($user);die;
					session('user_info', $user);
					session('user_id', $user['user_id']);

					if ($user['role_type'] == 2)
					{
						//将购物车内商品根据COOKIE值关联当前登录用户
						#$cart_model = new AgentShoppingCartModel();
						#$cart_model->updateShoppingCart();
					}

					if ($user['role_type'] == 1)
					{
						// $login_log_obj = new LoginLogModel();
						// $login_log_obj->addLoginLog();
						$admin_log_obj = new AdminLogModel();
						$admin_log_obj->addAdminLog(array('type' => AdminLogModel::LOGIN));
					}
					
					if (!$redirect) {
						#$Group = M('Users_group');
						#$my_user_type = $Group->where('group_id = ' . $user['group_id'])->getField('user_type');
						$my_user_type = $user['role_type'];
		
						if ($my_user_type == 1) {
							#$SMSModel = new SMSModel();
							#$SMSModel->sendAdminLogin($user['username']);	//管理员登录短信提醒
							
							$this->success('登录成功！', U('/acp'));
                        } else if ($my_user_type == 4) {
                            //代理商 
                            $this->success('登录成功!', U('/mcp'));
						} else {
							$this->success('登录成功！', U('/ucp'));
						}
					} else {
						$this->success('登录成功！', url_jiemi($redirect));
					}
				}
			}
    	}
		$head_title = $this->get_header_title('登录页');
		$this->assign('head_title', $head_title);
		$this->display();
    }
}

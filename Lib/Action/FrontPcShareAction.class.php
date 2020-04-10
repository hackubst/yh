<?php 
class FrontPcShareAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
	}

	//分享页面
	function share()
	{
		if (!strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger'))
		{
			die('请通过微信访问');
		}
		$user_id = intval(session('user_id'));
		$code = $this->_get('share_code');
		$planter_share_obj = new PlanterShareModel();
		$planter_share_info = $planter_share_obj->getPlanterShareInfo('share_code = "' . $code . '"', 'planter_id, planter_share_id, state');
		if (!$planter_share_info)
		{
			$this->alert('无效的链接', U('/'));
		}
		//获取种植机名称
		$planter_obj = new PlanterModel();
		$planter_info = $planter_obj->getPlanterInfo('planter_id = '. $planter_share_info['planter_id'], 'planter_name');
		//获取好友昵称和头像
		$user_obj = new UserModel();
		$user_info = $user_obj->getUserInfo('nickname, headimgurl', 'user_id = '. $user_id);

		$state = $planter_share_info['state'];
#var_dump($state);
		if ($state == 0)
		{
			//是否已授权
			$planter_obj = new PlanterModel($planter_share_info['planter_id']);
			if ($planter_obj->checkPriv())
			{
				//该用户已有权限
				$state = 2;
			}
			else
			{
				//使用链接
				$planter_share_obj = new PlanterShareModel($planter_share_info['planter_share_id']);
				$arr = array(
					'auth_time'	=> time(),
					'user_id'	=> $user_id,
					'state'		=> 1,
				);
				$planter_share_obj->editPlanterShare($arr);

				//授权给该用户
				$arr = array(
					'user_id'		=> $user_id,
					'planter_id'	=> $planter_share_info['planter_id'],
					'auth_time'		=> time(),
					'isuse'			=> 1,
				);
				$planter_auth_obj = new PlanterAuthModel();
				$planter_auth_obj->addPlanterAuth($arr);
#echo $planter_auth_obj->getLastSql();
#die;
			}
		}

		//state=1链接已失效，state=0绑定成功，state=2已授权，无需再授权
		$this->assign('state', $state);
		$this->assign('planter_name', $planter_info['planter_name']);
		$this->assign('user_name', $user_info['nickname']);
		$this->assign('user_headimg', $user_info['headimgurl']);

		$this->assign('head_title', '种植机分享');
		$this->display();
	}
}

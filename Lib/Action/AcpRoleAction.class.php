<?php
/**
 * 管理员和权限类
 */
class AcpRoleAction extends AcpAction 
{
	/**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo 
     */
	public function AcpRoleAction()
	{
		parent::_initialize();
	}

	/**
     * 查看管理员列表
     * @author 陆宇峰
     * @return void
     * @todo 从user表取出role_type=1的数据
     */
	public function list_admin()
	{
		$submit = $this->_request('submit');
		if($submit == 'search')
		{
			$linkman    = $this->_request('linkman');
			$group_id	 = $this->_request('group');
			$total       = $this->_request('total');     //分页时传递的总数
			$s = $this->_request('s');      //分页搜索时，相应参数传递进行了加密，有s表示是分页处理
		}
		$where .= ' role_type = 1 ';        //查询条件(是管理员的用户)
		if($linkman)
		{
			$linkman = $s?url_jiemi($linkman):$linkman;
			$where .= ' AND realname like "%'.$linkman.'%" ';
			$this->assign('s_linkman',$linkman);
		}
		if($group_id)
		{
			$agent_rank = $s?url_jiemi($agent_rank):$agent_rank;
			$where .= ' AND group_id = '.$group_id.' ';
			$this->assign('s_group_id',$group_id);
		}
		
		require_once('Lib/Model/UsersGroupModel.class.php');
		$UsersGroupModel = new UsersGroupModel();
		$group_list = $UsersGroupModel->getUserGroupList();  	//获取所有的角色信息
		$temp_group = array();
		foreach($group_list as $k=>$v)
		{
			$group_id = $v['group_id'];
			$temp_group[$group_id] = $v['group_name'];
		}
		
		require_once('Lib/Model/UserModel.class.php');
		$users = new UserModel();
		$total = $total?$total:$users->listUserNum($where);            //符合条件的总会员数
		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		//获取查询参数
		$map['realname']	= $realname?url_jiami($realname):'';
		$map['agent_rank']  = $agent_rank?url_jiami($agent_rank):'';
		$map['submit']      = 'search';
		$map['s']           = 1;
		$map['total'] = $total;
		$map['mod_id']      = 0;
		
		foreach($map as $k=>$v){
			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);
		
		$r = $users->getUserList('', $where,'reg_time DESC',($Page->firstRow.','.$Page->listRows));
		
		foreach($r as $k=>$v)       //取出用户其他相关的信息
		{
			$group = $v['group_id'];
			$r[$k]['group_name'] = $temp_group[$group];
			$r[$k]['reg_time']  = date('Y-m-d H:i:s',$v['reg_time']);
		}
		#echo $users->getLastSql();
		#echo "<pre>";
		#print_r($r);
		#die;
		$this->assign('user_list',$r);
		$this->assign('group_list',$group_list);
		$this->assign('head_title','管理员列表');
		$this->display();
	}
	
	/**
     * 添加管理员
     * @author 陆宇峰
     * @return void
     * @todo 插入一条数据到user表，role_type=1。先判断账号重名
     */
	public function add_admin()
	{
		$submit = $this->_post('submit');
		if($submit == 'submit')				//执行添加操作
		{
			$linkman  	 = $this->_post('linkman');
			$username 	 = $this->_post('name');
			$password 	 = $this->_post('password');
			$re_password = $this->_post('re_password');
			$mobile 	 = $this->_post('mobile');
			$tel 		 = $this->_post('tel');
			$email		 = $this->_post('email');
			$sex		 = $this->_post('sex');
			$group_id	 = $this->_post('group');
			
			if(!$linkman)
			{
				$this->error('对不起，请输入联系人姓名',$this->cur_url);
			}
			
			if(!$username)
			{
				$this->error('对不起，请指定一个登录名',$this->cur_url);
			}
			else
			{
				require_once('Lib/Model/UserModel.class.php');
				$UserModel = new UserModel();
				$num = $UserModel->listUserNum("username = '" . $username . "'");
				if($num)
				{
					$this->error('对不起，该登录名已经存在，请重新指定',$this->cur_url);
				}
			}
			if(!$password)		//如果未指定密码，则密码默认等同于用户名
			{
				$password = $username;
			}
			else
			{
				if(strlen($password) < 6)
				{
					$this->error('对不起，密码不能少于6位',$this->cur_url);
				}
				if($password != $re_password)
				{
					$this->error('对不起，两次密码输入不匹配',$this->cur_url);
				}
			}
			
			if(!$mobile && !$tel)
			{
				$this->error('对不起，手机和固话请至少填写一个',$this->cur_url);
			}
			else
			{
				if($mobile && !checkMobile($mobile))
				{
					$this->error('对不起，手机号格式不正确',$this->cur_url);
				}
				if($tel && !checkTel($tel))
				{
					$this->error('对不起，固话号格式不正确',$this->cur_url);
				}
			}
			
			if(!$email || !checkEmail($email))
			{
				$this->error('对不起，请输入正确的邮箱地址',$this->cur_url);
			}
			
			require_once('Lib/Model/UsersGroupModel.class.php');
			$UsersGroupModel = new UsersGroupModel();
			$group_info = $UsersGroupModel->getGroupInfoByGroupId($group_id);
			if(!$group_id)
			{
				$this->error('对不起，您设置的角色信息不存在！',$this->cur_url);
			}
			
			$data = array(
					'role_type'			=>	1,			//管理员
					'username'			=>	$username,
					'realname'			=>	$linkman,
					'password'			=>	md5($password),
					'mobile'			=>	$mobile,
					'tel'				=>	$tel,
					'email'				=>	$email,
					'sex'				=>	$sex,
					'group_id'			=>	$group_id,
					'reg_time'			=>	time(),
					'is_enable'			=>	1
			);
			$user_id = $UserModel->addUser($data);
			// echo $UserModel->getLastSql();
			if($user_id)
			{
				$this->success('恭喜您，您成功的添加了一个管理员！','/AcpRole/list_admin/mod_id/0');
			}
			else
			{
				$this->error('抱歉，添加失败！','/AcpRole/list_admin/mod_id/0');
			}
		}
		
		
		require_once('Lib/Model/UsersGroupModel.class.php');
		$UsersGroupModel = new UsersGroupModel();
		$group_list = $UsersGroupModel->getUserGroupList();  	//获取所有的角色信息
		
		$this->assign('action_title', '管理员列表');
		$this->assign('action_src', '/AcpRole/list_admin/mod_id/0');
		$this->assign('head_title','添加管理员');
		$this->assign('group_list',$group_list);
		$this->display();
	}
		
	/**
     * 修改管理员
     * @author 陆宇峰
     * @return void
     * @todo 修改管理员数据,users表。先判断账号重名
     */
	public function edit_admin()
	{
		$redirect = $this->_get('redirect');
		if($redirect)
		{
			$goback = url_jiemi($redirect);
		}
		
		$submit = $this->_post('submit');	
		if($submit == 'submit')					//当提交更改时
		{
			$uid = $this->_post('uid');
			if(!$uid)
			{
				$this->error('参数错误',$redirect);
			}
			$user_id  	= url_jiemi($uid);
			$linkman	= $this->_post('linkman');
			$password 	= $this->_post('newpassword');
			$email    	= $this->_post('email');
			$mobile		= $this->_post('mobile');
			$tel		= $this->_post('tel'); 
			$sex		= $this->_post('sex');
			$group_id	= $this->_post('group');
			
			if(!$linkman)
			{
				$this->error('对不起，联系人不能为空！',$this->cur_url);
			}
			if(!$email || !checkEmail($email))
			{
				$this->error('对不起，邮箱格式不正确！',$this->cur_url);
			}
			
			if(!$mobile && !$tel)
			{
				$this->error('对不起，手机和固话至少填写一项！',$this->cur_url);
			}
			else
			{
				if($mobile && !checkMobile($mobile))
				{
					$this->error('对不起，手机号格式不正确！',$this->cur_url);
				}
				if($tel && !checkTel($tel))
				{
					$this->error('对不起，固话号格式不正确！',$this->cur_url);
				}
			}
			if($sex != 0 && $sex != 1 && !$sex != 2)
			{
				$sex = 2;
			}
			
			require_once('Lib/Model/UsersGroupModel.class.php');
			$UsersGroupModel = new UsersGroupModel();
			$group_info = $UsersGroupModel->getGroupInfoByGroupId($group_id);
			if(!$group_id)
			{
				$this->error('对不起，您设置的角色信息不存在！',$this->cur_url);
			}
			
			require_once('Lib/Model/UserModel.class.php');
			$UserModel = new UserModel($user_id);
			$data = array(
					'realname'		=>	$linkman,
					'email'			=>	$email,
					'mobile'		=>	$mobile,
					'tel'			=>	$tel,
					'sex'			=>	$sex,
					'group_id'		=>	$group_id
			);
			$UserModel->setUserInfo($data);
			$UserModel->saveUserInfo();				//执行更新
			if($password)
			{
				if(strlen($password) < 6)
				{
					$this->error('对不起，新密码不能少于6位！',$this->cur_url);
				}
				$UserModel->setPassword($password);
			}
			$this->success('恭喜您，编辑成功！',$goback);
		}
		
		$u = $this->_get('u');
		$user_id = $u?url_jiemi($u):0;
		if(!$user_id)
		{
			$this->error('参数错误',$goback);
		}
		
		require_once('Lib/Model/UserModel.class.php');
		$UserModel = new UserModel($user_id);
		$r = $UserModel->getUserInfo('user_id,username,sex,realname,email,tel,mobile,group_id,is_enable');
		
		require_once('Lib/Model/UsersGroupModel.class.php');
		$UsersGroupModel = new UsersGroupModel();
		$group_list = $UsersGroupModel->getUserGroupList();  	//获取所有的角色信息
		
		
		$this->assign('user_info',$r);
		$this->assign('group_list',$group_list);
		$this->assign('action_title', '管理员列表');
		$this->assign('action_src', '/AcpRole/list_admin/mod_id/0');
		$this->assign('head_title','编辑管理员信息');
		$this->display();
	}
		
	/**
     * 删除管理员
     * @author 陆宇峰
     * @return void
     * @todo 删除一行users表数据
     */
	public function del_admin()
	{
		$this->display();
	}
	
	/**
     * 角色列表
     * @author 陆宇峰
     * @return void
     * @todo 从role表取出数据
     */
	public function list_role()
	{
		//调用角色模型的getRoleList方法获取角色列表
		require_once('Lib/Model/RoleModel.class.php');
		$role_obj = new RoleModel();
        //分页处理
        import('ORG.Util.Pagelist');
		//获取总数
        $count = $role_obj->getRoleNum();
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$role_obj->setStart($Page->firstRow);
        $role_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$role_list = $role_obj->getRoleList();
		$i = 1;
		foreach ($role_list AS $k => $v)
		{
			$role_list[$k]['i'] = $i;
			$i ++;
		}

		$this->assign('role_list', $role_list);
		$this->assign('show', $show);
		$this->assign('head_title', '角色列表');
		$this->display();
	}
	
	/**
     * 添加角色
     * @author 陆宇峰
     * @return void
     * @todo 插入一行数据到users_group表
     */
	public function add_role()
	{
		$act = $this->_post('act');
		if ($act == 'add')
		{
			$role_name = $this->_post('role_name');
			if (!$role_name)
			{
				$this->error('对不起，角色名不能为空！');
			}

			$priv_list = $this->_post('priv_list');
			if (!$priv_list)
			{
				$this->error('对不起，请至少选择一项权限！');
			}

			$priv_list = substr($priv_list, 0, -1);

			require_once('Lib/Model/RoleModel.class.php');
			$role_obj = new RoleModel();
			//组织数组
			$role_info = array(
				'group_name'	=> $role_name,
				'priv_str'		=> $priv_list
			);
			$role_obj->addRole($role_info);
			$this->success('恭喜您，角色添加成功！');
		}

		//获取权限列表
		require_once('Lib/Model/RoleModel.class.php');
		$role_obj = new RoleModel();
		$priv_list = $role_obj->getPrivList();

		$this->assign('action_title', '角色列表');
		$this->assign('action_src', '/AcpRole/list_role/mod_id/0');
		$this->assign('head_title','添加角色');
		$this->assign('priv_list', $priv_list);
		$this->display();
	}
	
	/**
     * 修改角色
     * @author 陆宇峰
     * @return void
     * @todo 修改role表的数据,先删除role_source表对应的数据后再插入
     */
	public function edit_role()
	{
		$act = $this->_post('act');
		require_once('Lib/Model/RoleModel.class.php');
		$role_obj = new RoleModel();
		//获取当前角色id
		$group_id = $this->_get('group_id');
		$group_id = intval($group_id);
		if (!$group_id)
		{
			$this->error('对不起，参数group_id无效！');
		}
		$role_obj->setGroupId($group_id);

		if ($act == 'edit')
		{
			$role_name = $this->_post('role_name');
			if (!$role_name)
			{
				$this->error('对不起，角色名不能为空！');
			}

			$priv_list = $this->_post('priv_list');
			if (!$priv_list)
			{
				$this->error('对不起，请至少选择一项权限！');
			}

			$priv_list = substr($priv_list, 0, -1);

			//组织数组
			$role_info = array(
				'group_name'	=> $role_name,
				'priv_str'		=> $priv_list
			);
			$role_obj->setRoleInfo($role_info);
			$role_obj->saveRoleInfo();
			$this->success('恭喜您，角色编辑成功！');
		}

		$role_info = $role_obj->getRoleInfo();
		if (!$role_info)
		{
			$this->error('对不起，该角色不存在！');
		}

		$role_info['priv_arr'] = explode(',', $role_info['priv_str']);

		//获取权限列表
		$priv_list = $role_obj->getPrivList();
		#echo "<pre>";
		#print_r($priv_list);
		#echo "</pre>";
		
		$this->assign('action_title', '角色列表');
		$this->assign('action_src', '/AcpRole/list_role/mod_id/0');
		$this->assign('head_title','编辑角色');
		$this->assign('role_info', $role_info);
		$this->assign('priv_list', $priv_list);
		$this->display();
	}
	
	/**
     * 删除角色
     * @author 陆宇峰
     * @return void
     * @todo 删除一行role表的数据，同时删除role_source表
     */
	public function del_role()
	{
		require_once('Lib/Model/RoleModel.class.php');
		$role_obj = new RoleModel();
		//获取当前角色id
		$group_id = $this->_get('group_id');
		$group_id = intval($group_id);
		if (!$group_id)
		{
			$this->error('对不起，参数group_id无效！');
		}
		$redirect = U('/AcpRole/list_role');
		$role_obj->setGroupId($group_id);	
		$returned_num = $role_obj->deleteRole();
		if ($returned_num == -1)
		{
			$this->success('对不起，无效的参数group_id！', $redirect);
		}
		elseif ($returned_num == -2)
		{
			$this->success('对不起，该角色下存在关联的管理员，无法删除！', $redirect);
		}

		$this->success('恭喜您，角色删除成功！', $redirect);
	}
}
?>

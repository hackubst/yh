<?php
/**
 * 用户的基础类
 * 注意全站要加上用户类别，即user表的role_type=3。否则会取出管理员数据造成问题
 *
 */
class AcpUserAction extends AcpAction
{
    public function AcpUserAction()
    {
        parent::_initialize();
        $this->assign('USER_NAME', C('USER_NAME'));
    }

    private function get_search_condition()
    {
        //初始化SQL查询的where子句
        $where = '';

        //查询相同密码
        $password = $this->_request('password');
        if($password)
        {
            $where .= " AND password = '$password' ";
        }
        //查询相同注册ip
        $reg_ip = $this->_request('reg_ip');
        if($reg_ip)
        {
            $login_log_obj = new LoginLogModel();
            $login_where = "ip = '$reg_ip' ";
            $uids = $login_log_obj->where($login_where)->group('user_id')->order('login_log_id asc')->getField('user_id',true);
            $uids = implode(',',$uids) ? : 0;
            $where .= " AND user_id in ($uids) ";
        }

        //查询相同登录ip
        $login_ip = $this->_request('login_ip');
        if($login_ip)
        {
            $login_log_obj = new LoginLogModel();
            $login_where = "ip = '$login_ip' ";
            $uids = $login_log_obj->where($login_where)->getField('user_id',true);
            $uids = array_unique($uids) ? : array();
            $uids = implode(',',$uids) ? : 0;
            $where .= " AND user_id in ($uids) ";
        }
        //user_id
        $id = $this->_request('id');
        if ($id)
        {
            $where .= ' AND id = ' . $id;
        }
        //parent_id
        $parent_id = $this->_request('parent_id');
        if ($parent_id)
        {
            $where .= ' AND parent_id = ' . $parent_id;
        }
        //mobile
        $mobile = $this->_request('mobile');
        if ($mobile)
        {
            $where .= ' AND mobile LIKE "%' . $mobile . '%"';
        }

        //username
        $nickname = $this->_request('nickname');
        if ($nickname)
        {
            $where .= ' AND nickname LIKE "%' . $nickname . '%"';
        }

        //真实姓名
        $alipay_account_name = $this->_request('alipay_account_name');
        if ($alipay_account_name)
        {
            $where .= ' AND alipay_account_name LIKE "%' . $alipay_account_name . '%"';
        }

        //加盟商名称
        $realname = $this->_request('realname');
        if ($realname)
        {
            $where .= ' AND realname LIKE "%' . $realname . '%"';
        }

        //QQ
        $qq = $this->_request('qq');
        if ($qq)
        {
            $where .= ' AND qq LIKE "%' . $qq . '%"';
        }

        //邮箱
        $email = $this->_request('email');
        if ($email)
        {
            $where .= ' AND email LIKE "%' . $email . '%"';
        }


        //用户等级
        $level_id = $this->_request('level_id');
        $level_id = ($level_id == '') ? 0 : $level_id;
        $level_id = intval($level_id);
        if ($level_id)
        {
            $where .= ' AND level_id = ' . intval($level_id);
        }

        /*注册时间begin*/
        //起始时间
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        #echo $start_time;
        if ($start_time)
        {
            $where .= ' AND reg_time >= ' . $start_time;
        }

        //结束时间
        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time)
        {
            $where .= ' AND reg_time <= ' . $end_time;
        }
        /*注册时间end*/
        #echo $where;
        //重新赋值到表单
        $this->assign('id', $id);
        $this->assign('mobile', $mobile);
        $this->assign('nickname', $nickname);
        $this->assign('alipay_account_name', $alipay_account_name);
        $this->assign('level_id', $level_id);
        $this->assign('start_time', $start_time ? $start_time : '');
        $this->assign('end_time', $end_time ? $end_time : '');

        /*重定向页面地址begin*/
        $redirect = $_SERVER['PATH_INFO'];
        $redirect .= $user_id ? '/user_id/' . $user_id : '';
        $redirect .= $nickname ? '/nickname/' . $nickname : '';
        $redirect .= $level_id ? '/level_id/' . $level_id : '';
        $redirect .= $start_time ? '/start_time/' . $start_time : '';
        $redirect .= $end_time ? '/end_time/' . $end_time : '';
        $redirect .= $alipay_account_name ? '/alipay_account_name/' . $alipay_account_name : '';

        $this->assign('redirect', url_jiami($redirect));
        /*重定向页面地址end*/

        return $where;
    }
    
    /**
     * 获取用户列表，公共方法
     * @author 姜伟
     * @param string $where
     * @param string $head_title
     * @param string $opt   引入的操作模板文件
     * @todo 获取用户列表，公共方法
     */
    function user_list($where, $head_title, $opt)
    {
        $where .= $this->get_search_condition();
        $user_obj = new UserModel(); 

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $user_obj->getUserNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $user_obj->setStart($Page->firstRow);
        $user_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $user_list = $user_obj->getUserList('', $where, ' reg_time DESC');
        $user_list = $user_obj->getListData($user_list);
        $this->assign('user_list', $user_list);
        #echo "<pre>";
        #print_r($user_list[0]);
        #echo "</pre>";
        #echo $user_obj->getLastSql();

        //用户等级列表
        $level_obj = new LevelModel();
        $level_list = $level_obj->getLevelList('level_id,level_name'); 
        $this->assign('level_list', $level_list);

        //获取大区列表
        $big_area_obj = M('big_area');
        $big_area_list = $big_area_obj->field('big_area_id, area_name')->order()->select();
        $this->assign('big_area_list', $big_area_list);

        //地址链接
        $url = 'http://' . $_SERVER['HTTP_HOST'];
        $this->assign('url', $url);

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display(APP_PATH . 'Tpl/AcpUser/get_user_list.html');
    }

    function new_user_list($where, $head_title, $opt)
    {
        $start_time = strtotime(date('Y-m-d',time()));
        $end_time = strtotime(date('Y-m-d',time())) + 24*3600;

        $where .= $this->get_search_condition();
        $user_obj = new UserModel();

        $bet_start_time = strtotime(I('bet_start_time'));
        $bet_end_time = strtotime(I('bet_end_time'));
        $bet_time = $bet_end_time-$bet_start_time;
        if ($bet_start_time && $bet_end_time) {
            if ((0<=$bet_time && $bet_time <=86400)) {
                $where .= ' and tp_bet_log.addtime > '.$bet_start_time;
                $where .= ' and tp_bet_log.addtime < '.$bet_end_time;
                $this->assign('bet_start_time',$bet_start_time);
                $this->assign('bet_end_time',$bet_end_time);
            } else {
                $this->error('游玩时间筛选需要小于等于24小时');
            }
        } else {
            $where .= ' AND tp_bet_log.is_open = 1 AND addtime >='.$start_time.' AND addtime <='.$end_time;
        }

//        import('ORG.Util.Pagelist');
//        $count = $user_obj->getUserNum($where);
//        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
//        $user_obj->setStart($Page->firstRow);
//        $user_obj->setLimit($Page->listRows);
//        $show = $Page->show();
//        $this->assign('show', $show);

        $field = 'SUM(total_after_money - total_bet_money) as bet_total,id,nickname,left_money,role_type,is_extend_user,is_enable,tp_users.user_id';
//        $user_list = $bet_obj->field($field)->join('right join tp_users on tp_users.user_id = tp_bet_log.user_id  AND tp_bet_log.is_open = 1 AND addtime >='.$start_time.' AND addtime <='.$end_time)->where($where)->group('tp_users.user_id')->order('bet_total desc')->select();
//        dump($bet_obj->getLastSql());die;
        $user_list = $user_obj->field($field)->join('tp_bet_log on tp_users.user_id = tp_bet_log.user_id')->where($where)->group('tp_users.user_id')->order('bet_total desc')->select();
//        echo $user_obj->getLastSql();
        foreach ($user_list as $k => $v)
        {
            $user_list[$k]['bet_total'] = feeHandle($user_list[$k]['bet_total']);
            $user_list[$k]['left_money'] = feeHandle($user_list[$k]['left_money']);
        }
        $this->assign('user_list', $user_list ?:array());
        $this->assign('count',count($user_list?:array()));
        //用户等级列表
        $level_obj = new LevelModel();
        $level_list = $level_obj->getLevelList('level_id,level_name');
        $this->assign('level_list', $level_list);

        //获取大区列表
        $big_area_obj = M('big_area');
        $big_area_list = $big_area_obj->field('big_area_id, area_name')->order()->select();
        $this->assign('big_area_list', $big_area_list);

        //地址链接
        $url = 'http://' . $_SERVER['HTTP_HOST'];
        $this->assign('url', $url);

        $this->assign('opt', $opt);
        $this->assign('new', 1);
        $this->assign('head_title', $head_title);
        $this->display(APP_PATH . 'Tpl/AcpUser/get_user_list.html');
    }
    public function get_today_user_list()
    {
        $this->new_user_list('role_type = 3', '当天游戏用户列表', 'user');
    }
    //全部用户列表
    public function get_all_user_list()
    {
        $this->user_list('role_type = 3', C('USER_NAME') . '列表', 'user');
    }
    //黑名单用户列表
    public function get_black_list()
    {
        $this->user_list('is_enable = 2','黑名单列表', 'user');
    }
    //代理商列表
    public function get_agent_user_list()
    {
        $this->user_list('role_type = 4', '代理商列表','agent');
    }

    //推广员推广列表
    public function extend_user()
    {
        $user_id = I('get.user_id', 0, 'int');
        $this->user_list('role_type = 3 AND parent_id = ' . $user_id, '推广员推广列表', 'user');
    }

    //门店管理员
    //@author wzg
    public function get_dept_user_list()
    {
        $this->user_list('role_type = 6', '门店管理员');
    }

    /**
     * 添加用户
     * @author cc
     * @param void
     * @return void
     * @todo 添加用户
     */
    // public function add_user()
    // {
    //     $action = $this->_post('action');
    //     $redirect = $this->_get('redirect');
    //     $redirect = ($redirect)?url_jiemi($redirect):'/AcpUser/get_all_user_list';
                        
    //     if($action == 'add')            //提交动作
    //     {
    //         /* post提交 begin */
    //         $data = array();
    //         $realname = $this->_post('realname');
    //         //$nickname           = $this->_post('nickname');
    //         //$password           = $this->_post('password');
    //         //$realname           = $this->_post('realname');
    //         $mobile             = $this->_post('mobile');
    //         // $address            = $this->_post('address');
    //         //$user_type          = $this->_post('user_type');
    //         //$email              = $this->_post('email');
    //         //$qq                 = $this->_post('qq');
    //         $store_sn           = $this->_post('store_sn');
    //         //$big_area_id        = $this->_post('big_area_id');
    //         //$area_id            = $this->_post('area_id');
    //         //$city_id            = $this->_post('city_id');
    //         //$province_id        = $this->_post('province_id');
    //         /* post提交 end */
            
    //         /* 数据验证 begin */
    //         if ($realname == '') 
    //         {
    //             $this->error('对不起，用户名不能为空');
    //         }
    //         /*if(!$password)
    //         {
    //             $this->error('对不起，密码不能为空！');           //参数错误
    //         }
    //         else
    //         {
    //             if(strlen($password) < 6)
    //             {
    //                 $this->error('对不起，密码不能少于6位');
    //             }
               
    //         }*/
    //         /*if($nickname == '')
    //         {
    //             $this->error('对不起，加盟商名称不能为空！');           //参数错误
    //         }
    //         if($realname == '')
    //         {
    //             $this->error('对不起，联系人不能为空！');           //参数错误
    //         }*/
    //         if(!$mobile || !checkMobile($mobile))
    //         {
    //             $this->error('对不起，手机号格式不正确');
    //         } else {
    //             //判断这个号码是否已用过
    //             //role_type = 5
    //             if(D('User')->where('is_extend_user = 1 AND mobile = ' . $mobile)->find()) {
    //                 $this->error('对不起，此手机号已用');
    //             }
    //         }
    //         /*if($qq == '')
    //         {
    //             $this->error('对不起，QQ号不能为空！');           //参数错误
    //         }*/
    //         if($store_sn == '')
    //         {
    //             $this->error('对不起，门店编号不能为空！');           //参数错误
    //         }
    //         /*if(!$big_area_id)
    //         {
    //             $this->error('对不起，大区不能为空！');           //参数错误
    //         }
    //         if(!$area_id)
    //         {
    //             $this->error('对不起，地区不能为空！');           //参数错误
    //         }*/
            
    //         $user_obj = D('User');
    //         //检查username是否存在
    //         //$username_info = $user_obj->field('user_id')->where('realname = "' . $realname.'"')->find();
    //         // dump($username_info);echo $user_obj->getLastSql();die;
    //         //if ($username_info) {
    //          //   $this->error('对不起，用户名已存在！');
    //         //}
    //         //检查store_sn是否存在
    //         //$store_sn_info = $user_obj->field('user_id')->where('store_sn = "' . $store_sn .'"')->find();
    //         //if ($store_sn_info) {
    //          //   $this->error('对不起，门店编号已存在！');
    //        // }
    //         /* 数据验证 end */

    //         /* 写入数据库 begin */
    //         $data = array(
    //             'realname'      =>  $realname,
    //             //'nickname'      =>  $nickname,
    //             'password'      =>  MD5($mobile),
    //             //'realname'      =>  $realname,
    //             'mobile'        =>  $mobile,
    //             // 'address'       =>  $address,
    //             //'user_type'     =>  $user_type,
    //             //'email'         =>  $email,
    //             //'qq'            =>  $qq,
    //             'store_sn'      =>  $store_sn,
    //             //'big_area_id'   =>  $big_area_id,
    //             //'area_id'       =>  $area_id,
    //             //'city_id'       =>  $city_id,
    //             //'province_id'   =>  $province_id,
    //             'role_type'     => 3,
    //             'is_extend_user'  => 1,
    //         );

    //         //如果此号码已在用户中绑定,则直接与其合并,成为推广员
    //         $user_info = $user_obj->getUserInfo('', 'role_type = 3 AND is_extend_user = 0 AND mobile = ' . $mobile);
    //         if ($user_info) {
    //             unset($user_info['mobile']);
    //             $r = $user_obj->where('user_id = ' . $user_info['user_id'])->save($data);
    //         } else {

    //             $r = $user_obj->addUser($data);
    //             #$info = json_decode($r,true);
    //         }

    //         if($r) {
    //             $this->success(C('AGENT_NAME') . '添加成功！');
    //         } else {
    //             $this->error(C('AGENT_NAME') . '添加失败！');
    //         }
    //         /* 写入数据库 end */
    //     }
    //     else
    //     {
    //         //获取大区列表
    //         $big_area_obj = M('big_area');
    //         $big_area_list = $big_area_obj->field('big_area_id, area_name')->order()->select();
    //         $this->assign('big_area_list', $big_area_list);

    //         // //获取省份列表
    //         // $province = M('address_province');
    //         // $province_list = $province->field('province_id, province_name')->select();
    //         // $this->assign('province_list', $province_list);

    //         //门店列表
    //         $dept_list = M('Dept')->where('isuse = 1')->select();
    //         $this->assign('dept_list', $dept_list);

    //         $this->assign('head_title','添加用户');
    //         $this->display();
    //     }
        
    // }

    //用户资金明细
    public function money_detail()
    {

         $user_id = I('get.user_id');

        if($user_id){
            $user_id = 62277;
        }

        $where = 'user_id = '.$user_id;
        $post_obj = new ChangeMoneyModel();
        $data = M('user_change_moneys')->where($where)->order('cahngetime desc')->select();

        import('ORG.Util.Pagelist');
        $count = M('user_change_moneys')->where($where)->count();
      
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));

        $post_obj->setStart($Page->firstRow);
        $post_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $post_list = $post_obj->getRewordList('', $where,'changetime desc');
        
        $post_list = $post_obj->getRewordData($post_list);


        $this->assign('post_list', $post_list);
        $this->assign('head_title','帖子列表');
        $this->display();

    

    }

    /**
     * @todo 验证数据有效性
     * @author 袁志鹏
     */
    public function edit_user_error()
    {
        $user_id = I('get.user_id');
        $mobile          = I('post.mobile');
        $password        = I('post.password');
        $re_password        = I('post.re_password');
        $nickname        = I('post.nickname');
        $bank_password        = I('post.bank_password');
        $safe_password        = I('post.safe_password');

        if(!$mobile)
        {
            $this->error('请填写手机号');
        }

        if(!checkMobile($mobile))
        {
            $this->error('请填写正确的手机号');
        }
        if($password)
        {
            if(strlen($password) < 6)
            {
                $this->error('登陆密码不能少于6位哦');
            }
            if($re_password != $password)
            {
                $this->error('两次输入登陆密码不同');
            }
        }
        if($bank_password)
        {
            if(strlen($bank_password) < 6)
            {
                $this->error('银行密码不能少于6位哦');
            }
        }
        if($safe_password)
        {
            if(strlen($safe_password) < 6)
            {
                $this->error('安全密码不能少于6位哦');
            }

        }
        if(!$nickname)
        {
            $this->error('请填写昵称');
        }

        // 验证手机号 
        $user_obj = new UserModel();
        $mobile_user_info = $user_obj->getParamUserInfo('user_id !='.$user_id.' AND mobile="'.$mobile.'"', 'user_id');
        if($mobile_user_info)
        {
            $this->error('手机号已存在，请重新填写');
        }

    }

    /**
     * 编辑用户
     * @author cc
     * @param void
     * @return void
     * @todo 编辑用户
     */
    public function edit_user()
    {
        $user_id = I('get.user_id');

        $user_obj = new UserModel($user_id);

        $user_info = $user_obj->getUserInfo('','user_id ='.$user_id);
        $data = I('post.');
        if($data['action'] == 'edit')
        {
            $this->edit_user_error();
            if(!$data['password'])
            {
                unset($data['password']);
                unset($data['re_password']);
            }else{
                $data['password'] = md5($data['password']);
                unset($data['re_password']);
            }

            if(!$data['safe_password'])
            {
                unset($data['safe_password']);
            }else{
                $data['safe_password'] = md5($data['safe_password']);
            }

            if(!$data['bank_password'])
            {
                unset($data['bank_password']);
            }else{
                $data['bank_password'] = md5($data['bank_password']);
            }
            if($data['level_id'] != $user_info['level_id'])  //修改等级后调整经验
            {
                $level_obj = new LevelModel();
                $exp = $level_obj->where('level_id ='.$data['level_id'])->getField('min_exp');

                $data['exp'] = $exp;

            }
            unset($data['action']);
            $user_id  = $user_obj->editUserInfo($data);
                // dump($user_obj->getLastSql());die;
            if($user_id)
            {
                $this->success('编辑成功', '/AcpUser/get_all_user_list');
            }
            else
            {
                $this->success('未修改');
            }
        }
        $level_obj = new LevelModel();
        $level_list = $level_obj->getLevelList('','','level_id');
        
        $this->assign('level_list',$level_list);
        $this->assign('user_info',$user_info);
        $this->assign('head_title','编辑用户');
        $this->display();        
    }

    /**
     * 添加门店管理员
     * @author wzg
     * @todo role_type = 6
     */
    public function add_dept_user()
    {
        $cur_url = '/AcpUser/add_dept_user.html';
        $act = I('act', '', 'strip_tags');
        if ('add' == $act) {
            $linkman  	 = $this->_post('linkman');
			$username 	 = $this->_post('name');
			$password 	 = $this->_post('password');
			$re_password = $this->_post('re_password');
			$mobile 	 = $this->_post('mobile');
			$tel 		 = $this->_post('tel');
			$email		 = $this->_post('email');
			$sex		 = $this->_post('sex');
            $priv_list   = $this->_post('priv_list');

			if (!$priv_list)
			{
				$this->error('对不起，请至少选择一个门店！');
			}
			$priv_list = substr($priv_list, 0, -1);

			if(!$linkman)
			{
				$this->error('对不起，请输入联系人姓名',$cur_url);
			}
			
			if(!$username)
			{
				$this->error('对不起，请指定一个登录名',$cur_url);
			}
			else
			{
				require_once('Lib/Model/UserModel.class.php');
				$UserModel = new UserModel();
				$num = $UserModel->listUserNum("username = '" . $username . "'");
				if($num)
				{
					$this->error('对不起，该登录名已经存在，请重新指定',$cur_url);
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
					$this->error('对不起，密码不能少于6位',$cur_url);
				}
				if($password != $re_password)
				{
					$this->error('对不起，两次密码输入不匹配',$cur_url);
				}
			}
			
			if(!$mobile && !$tel)
			{
				$this->error('对不起，手机和固话请至少填写一个',$cur_url);
			}
			else
			{
				if($mobile && !checkMobile($mobile))
				{
					$this->error('对不起，手机号格式不正确',$cur_url);
				}
				if($tel && !checkTel($tel))
				{
					$this->error('对不起，固话号格式不正确',$cur_url);
				}
			}
			
			if(!$email || !checkEmail($email))
			{
				$this->error('对不起，请输入正确的邮箱地址',$cur_url);
			}
			
			$data = array(
					'role_type'			=>	6,			//管理员
					'username'			=>	$username,
					'realname'			=>	$linkman,
					'password'			=>	md5($password),
					'mobile'			=>	$mobile,
					'tel'				=>	$tel,
					'email'				=>	$email,
					'sex'				=>	$sex,
					'reg_time'			=>	time(),
					'is_enable'			=>	1,
                    'dept_list'         => $priv_list,
			);
			$user_id = $UserModel->addUser($data);
			// echo $UserModel->getLastSql();
			if($user_id)
			{
				$this->success('恭喜您，您成功的添加了一个管理员！',$cur_url);
			}
			else
			{
				$this->error('抱歉，添加失败！', $cur_url);
			}

        }
        $dept_obj = new DeptModel();
        $dept_list = $dept_obj->where('isuse = 1')->select();

        $this->assign('dept_list', $dept_list);
        $this->assign('head_title', '添加门店管理员');
        $this->display();    
    }

    /**
     * 修改门店管理员
     * @author wzg
     */
    public function edit_dept_user()
    {
        $user_id = I('get.user_id', 0, 'int');
        if (!$user_id) $this->error('用户不存在');

        $UserModel = new UserModel($user_id);
        $user_info = $UserModel->getUserInfo('', 'user_id = ' . $user_id);
        $user_info['dept_list'] = explode(',', $user_info['dept_list']);
        $this->assign('user_info', $user_info);

        $act = I('act', '', 'strip_tags');
        if ('add' == $act) {
            $linkman  	 = $this->_post('linkman');
			$username 	 = $this->_post('name');
			$password 	 = $this->_post('password');
			$re_password = $this->_post('re_password');
			$mobile 	 = $this->_post('mobile');
			$tel 		 = $this->_post('tel');
			$email		 = $this->_post('email');
			$sex		 = $this->_post('sex');
            $priv_list   = $this->_post('priv_list');

			if (!$priv_list)
			{
				$this->error('对不起，请至少选择一个门店！');
			}
			$priv_list = substr($priv_list, 0, -1);

			if(!$linkman)
			{
				$this->error('对不起，请输入联系人姓名');
			}
			
			if(!$username)
			{
				$this->error('对不起，请指定一个登录名');
			}
			else
			{
				require_once('Lib/Model/UserModel.class.php');
				$num = $UserModel->listUserNum("user_id != " . $user_id . "AND username = '" . $username . "'");
				if($num)
				{
					$this->error('对不起，该登录名已经存在，请重新指定');
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
					$this->error('对不起，密码不能少于6位');
				}
				if($password != $re_password)
				{
					$this->error('对不起，两次密码输入不匹配');
				}
			}
			
			if(!$mobile && !$tel)
			{
				$this->error('对不起，手机和固话请至少填写一个');
			}
			else
			{
				if($mobile && !checkMobile($mobile))
				{
					$this->error('对不起，手机号格式不正确');
				}
				if($tel && !checkTel($tel))
				{
					$this->error('对不起，固话号格式不正确');
				}
			}
			
			if(!$email || !checkEmail($email))
			{
				$this->error('对不起，请输入正确的邮箱地址');
			}
			
			$data = array(
					//'role_type'			=>	6,			//管理员
					'username'			=>	$username,
					'realname'			=>	$linkman,
					'password'			=>	md5($password),
					'mobile'			=>	$mobile,
					'tel'				=>	$tel,
					'email'				=>	$email,
					'sex'				=>	$sex,
					//'reg_time'			=>	time(),
					//'is_enable'			=>	1,
                    'dept_list'         => $priv_list,
			);
			$user_id = $UserModel->editUserInfo($data);
			// echo $UserModel->getLastSql();
			if($user_id)
			{
				$this->success('恭喜您，您成功的修改了一个管理员！');
			}
			else
			{
				$this->error('抱歉，修改失败！');
			}

        }
        $dept_obj = new DeptModel();
        $dept_list = $dept_obj->where('isuse = 1')->select();

        $this->assign('dept_list', $dept_list);
        $this->assign('head_title', '添加门店管理员');
        $this->display(APP_PATH . 'Tpl/AcpUser/add_dept_user.html');
    }

    /**
     * type AJAX
     * 批量删除会员(实际是控制tp_users表中的is_enable字段)
     */
    public function del_users()
    {
        $users  = $this->_post('users');
        $submit = $this->_post('submit');
        if(!$users || $submit != 'del')
        {
            exit(json_encode(array('type'=>-1,'meesage'=>'请求被拒绝，请稍后再次尝试!')));
        }
        if($_SESSION['user_info']['role_type'] != 1)
        {
            exit(json_encode(array('type'=>-2,'meesage'=>'您没有执行该项操作的权限!')));
        }
        $user_arr = explode(',',$users);
        require_once('Lib/Model/UserModel.class.php');
        $UserModel = new UserModel();
        
        $sucess = 0;
        $false  = 0;
        foreach($user_arr as $v)
        {
            if(!$UserModel->getUserRelatedInfo($v))         //如果有重要的关联数据，则不能删除
            {
                $false++;
            }
            else
            {
                if($UserModel->setUsersEnable($v))          //执行删除（实际为更改字段值） 
                {
                    $sucess++;
                }
            }
        }
        if($false)
        {
            exit(json_encode(array('type'=>-2,'meesage'=>'您成功的删除了'.$sucess.'个用户,失败'.$false.'个。失败的可能原因是该用户下有移动端下好的订单。')));
        }
        else
        {
            exit(json_encode(array('type'=>1,'meesage'=>'恭喜您！成功删除'.$sucess.'个用户！')));
        }
    }
  
    /**
     * 用户等级列表
     * @author yzp
     * @return void
     * @todo 
     */
    public function get_level_list()
    {
            $LevelModel = new LevelModel();
            $total = $LevelModel->getLevelNum();            //总共有多少个级别
            //处理分页
            import('ORG.Util.Pagelist');                        // 导入分页类
            $per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
            $Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
            $page_str = $Page->show();                      //分页输出
            $this->assign('page_str',$page_str);
            
            $LevelModel->setStart($Page->firstRow);         //分页获取所有的级别信息
            $LevelModel->setLimit($Page->listRows);
            $level_list  = $LevelModel->getLevelList('','','sign_reward asc');      

            $this->assign('level_list',$level_list);
            $this->assign('head_title',C('USER_NAME') . '等级列表');
            $this->display();
    }

    /**
     * @access public
     * @todo 添加一个用户级别
     * @author yzp
     * 
     */
    public function add_level()
    {
        $submit = $this->_post('submit');
        $redirect = $this->_get('redirect');
        $redirect = ($redirect)?url_jiemi($redirect):'/AcpUser/get_level_list';
        if($submit == 'add')            //提交动作
        {
            $data = array();
            $level_name      = $this->_post('level_name');
            $min_exp = $this->_post('min_exp');
            $max_exp      = $this->_post('max_exp');
            $sign_reward          = $this->_post('sign_reward');
            $exchange_rate          = $this->_post('exchange_rate');
            if(!$level_name)
            {
                $this->error('对不起，等级名不能为空！', U());           //参数错误
            }
            if(is_null($min_exp))
            {
                $this->error('对不起，请输入该等级最小经验', U());           //参数错误
            }
            if(is_null($max_exp))
            {
                $this->error('对不起，请输入该等级最大经验', U());            //参数错误
            }
            if(is_null($sign_reward))
            {
                $this->error('对不起，请输入该等级每日可领救济豆', U());            //参数错误
            }
            if(is_null($exchange_rate))
            {
                $this->error('对不起，请输入该等级卡密兑换额外支付金豆比例', U());            //参数错误
            }
            $data = array(
                'level_name'     =>  $level_name,
                'min_exp' =>  $min_exp,
                'max_exp'      =>  $max_exp,
                'sign_reward' =>  $sign_reward,
                'exchange_rate' =>  $exchange_rate,
            );
            $LevelModel = new LevelModel();
            $r = $LevelModel->addLevel($data);
            if($r )
            {
                $this->success(C('USER_NAME') . '等级添加成功！', $redirect);           //错误信息
            }
            else
            {
                $this->error(C('USER_NAME') . '等级添加失败！', $redirect);
            }
        }
        else
        {
            $this->assign('head_title','添加等级');
            $this->display();
        }
    }
        
    /**
     * @access public
     * @todo 编辑一个用户级别
     * @author yzp
     * 
     */
    public function edit_level()
    {
        $level_id  = $this->_get('level_id');
        $redirect = $this->_get('redirect');
        $submit = $this->_post('submit');
        
        $redirect = ($redirect)?url_jiemi($redirect):U('/AcpUser/get_level_list');
        if(!$level_id)
        {
            $this->error('对不起，参数错误！', $redirect);           //参数错误
        }
        $LevelModel = new LevelModel($level_id);
        if($submit == 'edit')
        {
            $data = array();
            $level_name      = $this->_post('level_name');
            $min_exp = $this->_post('min_exp');
            $max_exp      = $this->_post('max_exp');
            $sign_reward          = $this->_post('sign_reward');
            $exchange_rate          = $this->_post('exchange_rate');

            $data = array(
                'level_name'     =>  $level_name,
                'min_exp' =>  $min_exp,
                'max_exp'      =>  $max_exp,
                'sign_reward' =>  $sign_reward,
                'exchange_rate' =>  $exchange_rate,
            );
            $r =  $LevelModel->editLevel('level_id = ' . $level_id,$data);
            $redirect = ($redirect)?url_jiemi($redirect):U('/AcpUser/edit_level/level_id/' . $level_id);
            if($r !== false)
            {
                $this->success('等级修改成功！', $redirect);
            }
            else
            {
                $this->error(C('USER_NAME') . '等级修改失败！');
            }
        }
        
        $r = $LevelModel->getLevelInfo('level_id = ' . $level_id);
        if(!$r)
        {
            $this->error('对不起，不存在的级别！', $redirect);           //参数错误
        }
        $this->assign('level_info',$r);
        $this->assign('head_title','编辑级别');
        $this->display();
    }
        
    /**
     * @access public
     * @todo 删除一个用户级别
     * type AJAX
     */
    public function del_rank()
    {
        $rank_id  = $this->_post('rank');
        $rank_id  = url_jiemi($rank_id);
        $redirect = $this->_get('redirect');
        
        $redirect = ($redirect)?url_jiemi($redirect):U('/AcpUser/get_user_rank_list');
        if(!$rank_id)
        {
            return json_encode(array('type'=>-10,'message'=>'对不起，参数错误！'));
        }
        
        require_once('Lib/Model/LevelModel.class.php');
        $LevelModel = new LevelModel();
        $json_info = $LevelModel->delLevelById($rank_id);
        exit($json_info);
    }


    /**
     * 用户登录记录列表
     * @author yzp
     * @return void
     * @todo 
     */
    public function get_login_log_list()
    {
            $user_id = I('get.user_id');
            $where = '';
            if($user_id)
            {
                $where = 'user_id ='.$user_id;
            }
            //起始时间
            $start_time = $this->_request('start_time');
            $start_time = str_replace('+', ' ', $start_time);
            $start_time = strtotime($start_time);
            #echo $start_time;
            if ($start_time) {
                $where .= ' AND login_time >= ' . $start_time;
            }

            //结束时间
            $end_time = $this->_request('end_time');
            $end_time = str_replace('+', ' ', $end_time);
            $end_time = strtotime($end_time);
            if ($end_time) {
                $where .= ' AND login_time <= ' . $end_time;
            }

            $this->assign('start_time', $start_time);
            $this->assign('end_time', $end_time);

            $login_log_obj = new LoginLogModel();
            $total = $login_log_obj->getLoginLogNum($where);            //总共有多少个级别
            //处理分页
            import('ORG.Util.Pagelist');                        // 导入分页类
            $per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
            $Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
            $page_str = $Page->show();                      //分页输出
            $this->assign('page_str',$page_str);
            
            $login_log_obj->setStart($Page->firstRow);         //分页获取所有的级别信息
            $login_log_obj->setLimit($Page->listRows);
            $login_log_list  = $login_log_obj->getLoginLogList('',$where,'login_time desc');      

            $login_log_list = $login_log_obj->getListData($login_log_list);

            $this->assign('login_log_list',$login_log_list);

            $this->assign('action_title','用户列表');
            $this->assign('action_src','/AcpUser/get_all_user_list/mod_id/0');

            $this->assign('head_title','用户登录记录');
            $this->display();
    }
    
    /**
     * 查看用户详情
     * @author 姜伟
     * @param void
     * @return void
     * @todo 从地址栏获取用户ID，调用获取用户模型的getUserInfo方法获取用户信息
     */
    public function user_detail()
    {
        //接收用户ID，验证用户ID有效性
        $redirect = $this->_get('redirect');
        $redirect = $redirect ? url_jiemi($redirect) : U('/AcpUser/get_all_user_list');
        $user_id = $this->_get('user_id');
        $user_id = intval($user_id);

        if (!$user_id)
        {
            $this->error('无效的用户号', $redirect);
        }
        
        //调用用户模型的getUserInfo获取用户信息
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('');
        // echo $user_obj->getLastSql();die;
        if (!$user_info)
        {
            $this->error('无效的用户号', $redirect);
        }

        //性别
        $user_info['sex'] = ($user_info['sex'] == 0) ? '女' : ($user_info['sex'] == 1 ? '男' : '未知');

        //禁用状态
        $user_info['is_enable'] = ($user_info['is_enable'] == 1) ? '正常' : '已禁用';

        //等级
        $user_rank_obj = new UserRankModel();
        $user_rank_info = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_info['user_rank_id'], 'rank_name');
        $user_info['rank_name'] = $user_rank_info ? $user_rank_info['rank_name'] : '';

        //地址
        $user_info['area_string'] = AreaModel::getAreaString($user_info['province_id'], $user_info['city_id'], $user_info['area_id']);

        //获取大区列表
        $big_area_obj = M('big_area');
        $user_info['big_area_name'] = $big_area_obj->where('big_area_id = ' . $user_info['big_area_id'])->getField('area_name');

        $login_log_obj = new LoginLogModel();
        $login_log_info = $login_log_obj->getLoginLogInfo('user_id ='.$user_id,'ip,login_time','login_time desc');

        $user_info['login_ip'] = $login_log_info['ip'];
        $user_info['login_time'] = $login_log_info['login_time'];
        $user_info['reg_ip'] = $login_log_obj->getLoginLogInfo('user_id ='.$user_id,'ip','login_time asc')['ip'];

        $account_obj = new AccountModel();

        $start_time = strtotime(date('Y-m-d',strtotime('-7 days')));
        $end_time = time();
        //7日内充值
        $change_type = AccountModel::RECHARGE;
        $where = 'operater <> 1 AND user_id ='.$user_id.' AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND change_type ='.$change_type.' AND state = 0';
        $recharge = $account_obj->getAccountInfo($where,'sum(amount_in) as recharge');



        $user_info['recharge'] = $recharge['recharge'] ? : 0;

        
        $level_obj = new LevelModel();
        $user_info['level_name'] = $level_obj->getLevelInfo('level_id ='.$user_info['level_id'],'level_name')['level_name'];

        $return_log_obj = new ReturnLogModel();
        $user_info['return_money'] = $return_log_obj->getReturnLogInfo('lower_id ='.$user_id,'SUM(money) AS return_money')['return_money'];

        $user_info['return_time'] = $return_log_obj->getReturnLogInfo('lower_id ='.$user_id,'addtime','addtime desc')['addtime'];
        $user_obj = new UserModel();
        $user_info['parent_name'] = $user_obj->getUserInfo('alipay_account_name','user_id ='.$user_info['parent_id'])['alipay_account_name'];

        $change_type = AccountModel::RECHARGE;
        $where = 'operater <> 1 AND user_id ='.$user_id.' AND change_type ='.$change_type.' AND state = 0';
        $user_info['total_recharge'] = $account_obj->getAccountInfo($where,'sum(amount_in) as total_recharge')['total_recharge'];

        $bet_log_obj = new BetLogModel();
        $user_info['game_total'] = $bet_log_obj->getBetLogInfo('user_id ='.$user_id,'SUM(total_after_money-total_bet_money) AS game_total')['game_total'];

        $user_gift_password_obj = new UserGiftPasswordModel();
        $cash = $user_gift_password_obj
        ->join('tp_user_gift on tp_user_gift.user_gift_id = tp_user_gift_password.user_gift_id')
        ->join('tp_gift_card on tp_gift_card.gift_card_id = tp_user_gift.gift_card_id')
        ->field('sum(tp_gift_card.cash) AS cash')
        ->where('tp_user_gift_password.user_id ='.$user_id.' AND tp_user_gift_password.isuse = 0')
        ->find();
        $user_info['cash'] = $cash['cash'];

        $return_log_obj = new ReturnLogModel();
        $user_info['my_return_money'] = $return_log_obj->getReturnLogInfo('return_type = 3 AND user_id ='.$user_id,'SUM(money) AS my_return_money')['my_return_money'];

        $change_type = AccountModel::RELIEF;
        $where = 'user_id ='.$user_id.' AND change_type ='.$change_type.' AND state = 0';
        $user_info['relief'] = $account_obj->getAccountInfo($where,'sum(amount_in) as relief')['relief'];

        //活动奖励
        $return_log_obj = new ReturnLogModel();
        $user_info['activity_reward'] = $return_log_obj->getReturnLogInfo('user_id ='.$user_id,'SUM(money) AS activity_reward')['activity_reward'];

        $account_obj = new AccountModel();
        $begin_time=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $end_time=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

        //今日充值
        $recharge = $account_obj->getAccountInfo('operater <> 1 AND user_id ='.$user_id.' AND change_type ='.AccountModel::RECHARGE.' AND addtime >='.$begin_time.' AND addtime <='.$end_time.' AND state = 0','sum(amount_in) as recharge');


        $bet_log_obj = new BetLogModel();

        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('valid_flow,game_type_id','isuse = 1 AND valid_flow > 0');

        $start_time = strtotime(date('Y-m-d',time()));
        $end_time = time();
        $new_where['user_id'] = $user_id;
        $new_where['addtime'] = array(array('EGT',$start_time),array('ELT',$end_time),'and');
        $new_where['is_open'] = 1;

        $total_bet_money = 0;
        foreach ($game_type_list as $key => $val)
        {
            $new_where['user_id'] = $user_id;
            $new_where['game_type_id'] = $val['game_type_id'];

            $bet_log_obj->setLimit(300);
            $bet_log_list = $bet_log_obj->getBetLogList('user_id,total_bet_money,game_type_id,bet_json',$new_where,'bet_log_id ASC');
            $valid = $user_obj->checkIsFull($bet_log_list,$val['valid_flow'],$val['game_type_id']) ? : 0;
            log_file($val['game_type_id'].'-------'.$valid,'valid_flow');
            $total_bet_money += $valid;
        }
        $user_info['daily_flow'] = feeHandle($total_bet_money);
        //7日内流水
//        $change_type = AccountModel::BETTING;
//        $where = 'user_id ='.$user_id.' AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND change_type ='.$change_type.' AND state = 0';
//        $flow = $account_obj->getAccountInfo($where,'sum(amount_out) as flow');
//        $user_info['flow'] = $flow['flow'] ? : 0;

        $start_time = strtotime(date('Y-m-d',strtotime('-7 days')));
        $end_time = time();
//        $new_where['user_id'] = $user_id;
//        $new_where['addtime'] = array(array('EGT',$start_time),array('ELT',$end_time),'and');
//        $new_where['is_open'] = 1;
//
//        $total_bet_money = 0;
//        foreach ($game_type_list as $key => $val)
//        {
//            $new_where['user_id'] = $user_id;
//            $new_where['game_type_id'] = $val['game_type_id'];
//
//            $bet_log_obj->setLimit(300);
//            $bet_log_list = $bet_log_obj->getBetLogList('user_id,total_bet_money,game_type_id,bet_json',$new_where,'bet_log_id ASC');
//            $valid = $user_obj->checkIsFull($bet_log_list,$val['valid_flow'],$val['game_type_id']) ? : 0;
//            log_file($val['game_type_id'].'-------'.$valid,'valid_flow');
//            $total_bet_money += $valid;
//        }

        //新的7日流水合计
        $daily_win_obj = new DailyWinModel();
        $where = 'daily_flow <> 0 AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$user_id;
        $daily_flow_sum = $daily_win_obj->getDailyWinInfo($where,'sum(daily_flow) AS total')['total'] ? : 0;

        $user_info['flow'] = $daily_flow_sum + $total_bet_money ? : 0;

        //格式化金豆
        $total_money = $user_info['left_money'] + $user_info['frozen_money'];
        $user_info['left_money'] = feeHandle($user_info['left_money']);
        $user_info['frozen_money'] = feeHandle($user_info['frozen_money']);
        $user_info['total_money'] = feeHandle($total_money);
        $user_info['recharge'] = feeHandle($user_info['recharge']);
        $user_info['return_money'] = feeHandle($user_info['return_money']);
        $user_info['flow'] = feeHandle($user_info['flow']);
        $user_info['game_total'] = feeHandle($user_info['game_total']);
        $user_info['total_recharge'] = feeHandle($user_info['total_recharge']);
        $user_info['relief'] = feeHandle($user_info['relief']);
        $user_info['my_return_money'] = feeHandle($user_info['my_return_money']);
        $user_info['activity_reward'] = feeHandle($user_info['activity_reward']);
        $user_info['cash'] = feeHandle($user_info['cash']);


        $user_info['daily_recharge'] = feeHandle($recharge['recharge']);

        //代理总充值
        $total_recharge = $account_obj->where('change_type ='.AccountModel::RECHARGE.' AND operater = '.$user_id)->getField('sum(amount_in)');
        $deposit_obj = new DepositApplyModel();
        $total_deposit = $deposit_obj->where('state = 1 AND user_id'.$user_id)->getField('sum(money)');
        $recharge_out = $account_obj->where('change_type =' . AccountModel::RECHARGEOUT . ' AND user_id = '.$user_id)->getField('sum(amount_out)');
        $total_profit = $total_recharge - $recharge_out;
        $total_recharge = feeHandle($total_recharge);
        $total_deposit = feeHandle($total_deposit);
        $total_profit = feeHandle($total_profit);
        $this->assign('total_recharge', $total_recharge?:0);
        $this->assign('total_deposit', $total_deposit?:0);
        $this->assign('total_profit', $total_profit?:0);

        $this->assign('user_info', $user_info);
        $this->assign('head_title', '查看用户详情');
        $this->display();
    }

    /**
     * 注册用户按日统计
     */
    public function user_reg_stat_by_day() 
    {
        $add_time = $this->_post('add_time');
        $start_time = 0;
        $end_time = 0;
        $date = '';

        if ($add_time)
        {
            $start_time = strtotime(date('Y-m-d', strtotime($add_time)));
            #$end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
            $end_time = strtotime(date('Y-m-d', strtotime($add_time))) + 115200;
            $date = date('Y-m-d', strtotime($add_time));
        }
        else
        {
            //今天0点的时间戳
            $end_time = strtotime(date('Y-m-d', time())) + 86400;

            //昨天0点的时间戳
            #$start_time = strtotime(date('Y-m-d', time())) - 86400;
            $start_time = strtotime(date('Y-m-d', time()));
            $date  = date('Y-m-d', $start_time);
        }

        //获取今日注册用户按日统计数
        $user_obj = new UserModel();
        $user_stat_list = $user_obj->field('DATE_FORMAT(FROM_UNIXTIME(reg_time), "%H") AS hour, COUNT(*) AS reg_num')->where('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time)->group('hour')->order('reg_time DESC')->select();

        $new_user_stat_list = array();
        for ($i = 0; $i <= 24; $i ++)
        {
            $new_user_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($user_stat_list AS $key => $val)
        {
            $new_user_stat_list[intval($val['hour'])] = $val['reg_num'];
        }

        $this->assign('uv_list', $new_user_stat_list);
        $this->assign('user_stat_list', $new_user_stat_list);
        $this->assign('date', $date);
        #echo "<pre>";
        #print_r($new_pv_list);
        #print_r($new_uv_list);

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '注册用户按日统计');
        $this->display();
    }

    /**
     * 注册用户按月统计
     */
    public function user_reg_stat_by_month() 
    {
        $year = $this->_post('year');
        $month = $this->_post('month');
        $year = $year ? $year : date('Y');
        $month = $month ? $month : date('m');
        $start_time = 0;
        $end_time = 0;
        $date = '';

        if ($year && $month)
        {
            $this->assign('year', $year);
            $this->assign('month', $month);
            $start_time = mktime(0, 0, 0, $month, 1, $year);
            if ($month == 12)
            {
                $year ++;
                $month = 1;
            }
            else
            {
                $month ++;
            }

            $end_time = mktime(0, 0, 0, $month, 1, $year) - 1;
            $date = $year . '-' . date('m');
        }

        //获取今日注册用户按月统计数
        $user_obj = new UserModel();
        $user_stat_list = $user_obj->field('DATE_FORMAT(FROM_UNIXTIME(reg_time), "%d") AS day, COUNT(*) AS reg_num')->where('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time)->group('day')->order('reg_time DESC')->select();

        $new_user_stat_list = array();
        for ($i = 0; $i <= 30; $i ++)
        {
            $new_user_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($user_stat_list AS $key => $val)
        {
            $new_user_stat_list[intval($val['day'])] = $val['reg_num'];
        }

        $this->assign('user_stat_list', $new_user_stat_list);
        $this->assign('user_stat_list', $new_user_stat_list);
        $this->assign('date', $date);
        $this->assign('day_num', date('d', mktime(0,0,0,$month + 1,0,$year)));

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '注册用户按月统计');
        $this->display();
    }

    /**
     * 注册用户按年统计
     */
    public function user_reg_stat_by_year() 
    {
        $year = $this->_post('year');
        $start_time = 0;
        $end_time = 0;
        $date = '';

        if ($year)
        {
            $start_time = mktime(0, 0, 0, 1, 1, $year);
            $end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
            $date = date('Y-m-d', strtotime($year));
        }
        else
        {
            $year = date('Y');
            $start_time = mktime(0, 0, 0, 1, 1, $year);
            $end_time = mktime(0, 0, 0, 1, 1, $year + 1) - 1;
        }
        $this->assign('year', $year);

        //获取今日注册用户按年统计数
        $user_obj = new UserModel();
        $user_stat_list = $user_obj->field('DATE_FORMAT(FROM_UNIXTIME(reg_time), "%m") AS month, COUNT(*) AS reg_num')->where('reg_time >= ' . $start_time . ' AND reg_time <= ' . $end_time)->group('month')->order('reg_time DESC')->select();

        $new_user_stat_list = array();
        for ($i = 0; $i <= 12; $i ++)
        {
            $new_user_stat_list[$i] = 0;
        }

        //组成数组
        foreach ($user_stat_list AS $key => $val)
        {
            $new_user_stat_list[intval($val['month'])] = $val['reg_num'];
        }

        $this->assign('user_stat_list', $new_user_stat_list);
        $this->assign('date', $date);

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '注册用户按年统计');
        $this->display();
    }

    /**
     * 用户消费统计
     */
    public function user_consume_stat() 
    {
        $field = 'user_id, COUNT(order_id) AS order_num, AVG(pay_amount) AS amount_avg, SUM(pay_amount) AS total';
        $where = 'order_status = ' . OrderModel::CONFIRMED;
        #$where = 'order_status = 0';
        $order = 'total DESC, amount_avg DESC';
        $group = 'user_id';

        $order_obj = new OrderModel();
        $total = $order_obj->where($where)->count('DISTINCT user_id');
        //处理分页
        import('ORG.Util.Pagelist');    // 导入分页类
        $per_page_num = C('PER_PAGE_NUM');  //分页 每页显示条数
        $Page = new Pagelist($total, $per_page_num);    // 实例化分页类 传入总记录数和每页显示的记录数
        $page_str = $Page->show();      //分页输出
        $this->assign('page_str',$page_str);
        
        $order_obj->setStart($Page->firstRow);  //分页获取所有的级别信息
        $order_obj->setLimit($Page->listRows);
        
        //获取用户消费统计
        $user_stat_list = $order_obj->field($field)->where($where)->group($group)->order($order)->limit()->select();

        foreach ($user_stat_list AS $k => $v)
        {
            //用户基本信息
            $user_obj = new UserModel($user_id);
            $user_info = $user_obj->getUserInfo('nickname, realname, mobile, province, city, province_id, city_id, area_id, user_rank_id, reg_time');

            //用户所在区域
            $area_string = AreaModel::getAreaString($user_info['province_id'], $user_info['city_id'], $user_info['area_id']);
             $area_string = $area_string ? $area_string : '【' . $user_info['provice'] . '】' . '【' . $user_info['city'] . '】';
            $user_info['area_string'] = $area_string;

            //用户等级名称
            $user_rank_obj = new UserRankModel();
            $user_rank_info = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_info['user_rank_id'], 'rank_name');
            $user_info['rank_name'] = $user_rank_info ? $user_rank_info['rank_name'] : '--';
            unset($user_info['province_id']);
            unset($user_info['city_id']);
            unset($user_info['area_id']);
            unset($user_info['province']);
            unset($user_info['city']);
            $user_info['total'] = $v['total'];
            $user_info['amount_avg'] = $v['amount_avg'];
            $user_info['order_num'] = $v['order_num'];
            $user_stat_list[$k] = $user_info;
        }
        #echo "<pre>";
        #print_r($user_stat_list);

        $this->assign('user_stat_list', $user_stat_list);

        //TITLE中的页面标题
        $this->assign('shop_name', $GLOBALS['install_info']['shop_name']);
        $this->assign('head_title', '用户消费统计');
        $this->display();
    }

    private function get_suggest_search_condition()
    {
        //初始化SQL查询的where子句
        $where = '';

        //消息内容
        $message = $this->_request('message');
        if ($message)
        {
            $where .= ' AND message LIKE "%' . $message . '%"';
        }

        //消息类型
        $message_type = $this->_request('message_type');
        if (is_numeric($message_type) && $message_type)
        {
            $where .= ' AND message_type = ' . $message_type;
        }

        //状态
        $state = $this->_request('state');
        if (is_numeric($state) && $state >= 0)
        {
            $where .= ' AND state = ' . intval($state);
        }

        /*提交时间begin*/
        //起始时间
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        #echo $start_time;
        if ($start_time)
        {
            $where .= ' AND addtime >= ' . $start_time;
        }

        //结束时间
        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time)
        {
            $where .= ' AND addtime <= ' . $end_time;
        }
        /*提交时间end*/
        #echo $where;
        //重新赋值到表单
        $this->assign('message', $message);
        $this->assign('state', $state);
        $this->assign('message_type', $message_type);
        $this->assign('start_time', $start_time ? $start_time : '');
        $this->assign('end_time', $end_time ? $end_time : '');

        /*重定向页面地址begin*/
        $redirect = $_SERVER['PATH_INFO'];
        $redirect .= $message ? '/message/' . $message : '';
        $redirect .= $state ? '/state/' . $state : '';
        $redirect .= $message_type ? '/message_type/' . $message_type : '';
        $redirect .= $start_time ? '/start_time/' . $start_time : '';
        $redirect .= $end_time ? '/end_time/' . $end_time : '';

        $this->assign('redirect', url_jiami($redirect));
        /*重定向页面地址end*/

        return $where;
    }
    
    /**
     * 获取用户意见反馈列表，公共方法
     * @author 姜伟
     * @param string $where
     * @param string $head_title
     * @param string $opt   引入的操作模板文件
     * @todo 获取意见反馈用户列表，公共方法
     */
    function user_suggest_list($where, $head_title, $opt)
    {
        $where .= $this->get_suggest_search_condition();
        $user_suggest_obj = new UserSuggestModel(); 

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $user_suggest_obj->getUserSuggestNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $user_suggest_obj->setStart($Page->firstRow);
        $user_suggest_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $user_suggest_list = $user_suggest_obj->getUserSuggestList('', $where, ' addtime DESC');
        $user_suggest_list = $user_suggest_obj->getListData($user_suggest_list);
        $this->assign('user_suggest_list', $user_suggest_list);
        #echo "<pre>";
        #print_r($user_suggest_list);
        #echo "</pre>";
        #echo $user_suggest_obj->getLastSql();

        //状态列表
        $this->assign('state_list', UserSuggestModel::getStateList());
        //类型列表
        $this->assign('type_list', UserSuggestModel::getTypeList());

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display(APP_PATH . 'Tpl/AcpUser/get_user_suggest_list.html');
    }

    public function get_user_suggest_list()
    {
        $this->user_suggest_list('1', C('USER_NAME') . '意见列表');
    }

    public function ajax_del_user()
    {
        $ids = I('id', NULL, 'strip_tags');

        if ($ids) {
            $handle_array = explode(',', $ids);
            $success_num = 0;
            foreach ($handle_array AS $id) {
                $success_num += D('User')->delRecord($id);
            }
            exit($success_num ? 'success' : 'failure');
        } else {
            exit('failure');
        }
    }

    //积分明细
    public function integral_detail(){
        $start_time = $this->_request('start_time', '');
        $end_time   = $this->_request('end_time', '');
        $user_id    = $this->_request('user_id', 0);
        $packet_id = $this->_request('packet_id', 0);
        $packet = $this->_request('packet');
         if($start_time)
        {
            $start_time = str_replace('+', ' ', $start_time);
        }
        if($end_time)
        {
            $end_time = str_replace('+', ' ', $end_time);
        }

        //通过时间区间筛选数据
        if($start_time && $end_time)
            $where['addtime'] = array( array('GT', strtotime($start_time)), array('LT', strtotime($end_time)), 'AND'); 
        //按用户id筛选
        if($user_id)
            $where['user_id'] = $user_id;
        
        
        //获取订单列表
        import('ORG.Util.Pagelist');// 导入分页类
        $integral_obj = new IntegralModel();
        $count  = $integral_obj->getIntegralNum($where);

        $Page         = new Pagelist($count,C('PER_PAGE_NUM')); 
        $show         = $Page->show();
        $fields       = 'user_id,addtime,change_type,integral,start_integral,end_integral,remark';
        $integral_obj->setStart($Page->firstRow);
        $integral_obj->setLimit(C('PER_PAGE_NUM'));
        $changed_list = $integral_obj->getIntegralList($fields, $where, 'addtime desc');
     
        $UserModel = new UserModel();
        foreach ($changed_list as $key => $value) 
        {
            $user_info                         = $UserModel->getParamUserInfo('user_id = ' . $value['user_id'], 'username,realname');
            $changed_list[$key]['username']    = $user_info['realname'];
            $changed_list[$key]['change_type'] = $integral_obj->integralChangeType($value['change_type']);
        }
       // dump($changed_list);  
        //p($changed_list);
        $this->assign('changed_list', $changed_list);
        $this->assign('page', $show);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);

        $this->assign('head_title', '积分明细');
        $this->display();
    }  




    //设置上级代理
    public function ajax_set_user_father(){
        $user_id = I('user_id','', 'intval');
        $father_id = I('father_id','', 'intval');
        if(!$user_id || $father_id === false){
            exit('failure');
        }
        $user_obj = new UserModel($user_id);
        $user_obj->setUserInfo(array('first_agent_id'=>$father_id));
        if($user_obj->saveUserInfo()){
            exit('success');
        }
        exit('failure');
    }  
 
    /**
     * 获取管理员登录日志列表，公共方法
     * @author 姜伟
     * @param string $where
     * @param string $head_title
     * @param string $opt   引入的操作模板文件
     * @todo 获取管理员登录日志列表，公共方法
     */
    function login_log_list($where, $head_title, $opt)
    {
        $login_log_obj = new LoginLogModel(); 

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $login_log_obj->getLoginLogNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $login_log_obj->setStart($Page->firstRow);
        $login_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $login_log_list = $login_log_obj->getLoginLogList('', $where, ' login_time DESC');
        $login_log_list = $login_log_obj->getListData($login_log_list);
        $this->assign('login_log_list', $login_log_list);
        #echo "<pre>";
        #print_r($login_log_list[0]);
        #echo "</pre>";
        #echo $login_log_obj->getLastSql();

        //地址链接
        $url = 'http://' . $_SERVER['HTTP_HOST'];
        $this->assign('url', $url);

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display('get_login_log_list');
    }

	function login_log()
	{
		$this->login_log_list('1', '管理员登录日志', 'all');
	}

    /**
     * @todo 验证数据有效性
     * @author 袁志鹏
     */
    public function add_user_error()
    {

        $mobile          = I('post.mobile');
        $password        = I('post.password');
        $re_password        = I('post.re_password');
        $nickname        = I('post.nickname');
        $garden_id        = I('post.garden_id');

        if(!$mobile)
        {
            $this->error('请填写手机号');
        }

        if(!checkMobile($mobile))
        {
            $this->error('请填写正确的手机号');
        }
        if($password)
        {
            if(strlen($password) < 6)
            {
                $this->error('密码不能少于6位哦');
            }
            if($re_password != $password)
            {
                $this->error('两次输入密码不同');
            }
        }
        if(!$nickname)
        {
            $this->error('请填写昵称');
        }

        // 验证手机号 
        $user_obj = new UserModel();
        $mobile_user_info = $user_obj->getParamUserInfo('mobile="'.$mobile.'"', 'user_id');
        if($mobile_user_info)
        {
            $this->error('手机号已存在，请重新填写');
        }

    }

    /**
     * @todo 添加用户
     * @author 袁志鹏
     */
    public function add_user()
    {
        if(IS_POST)
        {
            $this->add_user_error();
            $data = I('post.');
            $user_obj = new UserModel();

            $data['reg_time'] = time();
            $data['role_type'] = 3;
            $data['password'] = $data['safe_password'] = $data['bank_password'] = empty($data['password'])? md5($data['mobile']) : md5($data['password']);    
            unset($data['re_password']);
            unset($data['action']);
            $user_id  = $user_obj->addUser($data);
            // dump($user_obj->getLastSql());die;
            if($user_id)
            {
                $this->success('添加成功', '/AcpUser/get_all_user_list');
            }
            else
            {
                $this->error('系统繁忙，请重试');
            }
        }

        $this->assign('head_title', '添加用户');
        $this->display();
    }

    /**
     * @todo 验证数据有效性
     * @author 袁志鹏
     */
    public function add_agent_error()
    {

        $mobile          = I('post.mobile');
        $password        = I('post.password');
        $re_password        = I('post.re_password');
        $realname        = I('post.realname');
        $introduce        = I('post.introduce');
        $game_name        = I('post.game_name');

        if(!$mobile)
        {
            $this->error('请填写手机号');
        }

        if(!checkMobile($mobile))
        {
            $this->error('请填写正确的手机号');
        }
        if($password)
        {
            if(strlen($password) < 6)
            {
                $this->error('密码不能少于6位哦');
            }
            if($re_password != $password)
            {
                $this->error('两次输入密码不同');
            }
        }
        if(!$realname)
        {
            $this->error('请填写代理商姓名');
        }
        if(!$introduce)
        {
            $this->error('请填写代理商介绍');
        }
        if(!$game_name)
        {
            $this->error('请填写游戏名称');
        }

        // 验证手机号 
        $user_obj = new UserModel();
        $mobile_user_info = $user_obj->getParamUserInfo('mobile="'.$mobile.'"', 'user_id');
        if($mobile_user_info)
        {
            $this->error('手机号已存在，请重新填写');
        }

    }

    /**
     * @todo 添加代理商
     * @author 袁志鹏
     */
    public function add_agent()
    {
        if(IS_POST)
        {
            $this->add_agent_error();
            $data = I('post.');
            $user_obj = new UserModel();

            $data['reg_time'] = time();
            $data['role_type'] = 4;
            $data['password'] = empty($data['password'])? md5($data['mobile']) : md5($data['password']);    
            unset($data['re_password']);
            unset($data['action']);
            $data['username'] = $data['mobile'];
            $user_id  = $user_obj->addUser($data);
            // dump($user_obj->getLastSql());die;
            if($user_id)
            {
                $this->success('添加成功', '/AcpUser/get_all_user_list');
            }
            else
            {
                $this->error('系统繁忙，请重试');
            }
        }

        $this->assign('head_title', '添加代理商');
        $this->display();
    }


    /**
     * @todo 验证数据有效性
     * @author 袁志鹏
     */
    public function edit_agent_error()
    {

        $password        = I('post.password');
        $re_password        = I('post.re_password');
        $realname        = I('post.realname');
        $introduce        = I('post.introduce');
        $game_name        = I('post.game_name');

    
        if($password)
        {
            if(strlen($password) < 6)
            {
                $this->error('密码不能少于6位哦');
            }
            if($re_password != $password)
            {
                $this->error('两次输入密码不同');
            }
        }
        if(!$realname)
        {
            $this->error('请填写代理商姓名');
        }
        if(!$introduce)
        {
            $this->error('请填写代理商介绍');
        }
        if(!$game_name)
        {
            $this->error('请填写游戏名称');
        }

    }

    /**
     * 编辑代理商信息
     * @author cc
     * @param void
     * @return void
     * @todo 编辑用户
     */
    public function edit_agent_info()
    {
        $user_id = I('get.user_id');

        $user_obj = new UserModel($user_id);

        $user_info = $user_obj->getUserInfo('','user_id ='.$user_id);
        $data = I('post.');
        if($data['action'] == 'edit')
        {
            $this->edit_agent_error();
            if(!$data['password'])
            {
                unset($data['password']);
                unset($data['re_password']);
            }else{
                $data['password'] = md5($data['password']);
                unset($data['re_password']);
            }
            unset($data['action']);
            $data['username'] = $data['mobile'];
            $user_id  = $user_obj->editUserInfo($data);
                // dump($user_obj->getLastSql());die;
            if($user_id)
            {
                $this->success('编辑成功', '/AcpUser/get_agent_user_list/mod_id/1');
            }
            else
            {
                $this->error('系统繁忙，请重试');
            }
        }
        //role_type
        $admin_id = session('user_id');
        $role_type = $user_obj->where('user_id ='.$admin_id)->getField('role_type');
        $this->assign('role_type',$role_type);
        $this->assign('user_info',$user_info);
        $this->assign('head_title','编辑代理商信息');
        $this->display('../McpUser/edit_agent_info');        
    }


    public function get_verify_code_list(){
        $user_obj = new UserModel();
        $verify_code_obj = new VerifyCodeModel();

        $where_user = '1';
        $where = '1';
        //user_id
        $id = $this->_request('id');
        if ($id) {
            $where_user .= ' AND id = ' . $id;
            $this->assign('id', $id);
        }

        //mobile
        $mobile = $this->_request('mobile');
        if ($mobile) {
            $where .= ' AND mobile LIKE "%' . $mobile . '%"';
            $this->assign('mobile', $mobile);

        }

        //username
        $nickname = $this->_request('nickname');
        if ($nickname) {
            $where_user .= ' AND nickname LIKE "%' . $nickname . '%"';
            $this->assign(' nickname', $nickname);

        }
        if ($id || $nickname) {
            $user_ids = $user_obj->where($where_user)->getField('user_id', true);
            $user_ids = implode(',', $user_ids);
            $where .= ' AND user_id IN (' . $user_ids . ')';
        }

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $verify_code_obj->where($where)->count();
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $verify_code_obj->setStart($Page->firstRow);
        $verify_code_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $code_list = $verify_code_obj->where($where)->order('expire_time DESC')->limit()->select();
        $code_list = $verify_code_obj->getListData($code_list);
        $this->assign('code_list', $code_list);

        $this->assign('head_title', '验证码列表');
        $this->display(APP_PATH . 'Tpl/AcpUser/get_verify_code_list.html');
    }

    /**
     * 获取下线返利记录
     * @author yzp
     * @Date:  2019/11/4
     * @Time:  17:01
     */
    public function get_lower_return_log()
    {
        $parent_id = I('get.parent_id') ? : 0;
        $return_log_obj = new ReturnLogModel();
        $where = 'return_type ='.MarketingRuleModel::BETTING.' AND user_id='.$parent_id;
        $count = $return_log_obj->where($where)->count();
        //分页处理
        import('ORG.Util.Pagelist');
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $return_log_obj->setStart($Page->firstRow);
        $return_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);
        $return_log = $return_log_obj->where($where)->order('return_log_id DESC')->limit()->select();
        $return_log = $return_log_obj->getListDataResult($return_log);
        $this->assign('return_log', $return_log);

        $this->assign('head_title', '下线返利记录');
        $this->display();
    }
}
?>

<?php
/**
 * 用户的基础类
 * 注意全站要加上用户类别，即user表的role_type=3。否则会取出管理员数据造成问题
 *
 */
class AcpAgentAction extends AcpAction
{
    private function get_search_condition()
    {
        //初始化SQL查询的where子句
        $where = '';


        //user_id
        $user_id = intval($this->_request('user_id'));
        if ($user_id)
        {
            $where .= ' AND user_id = ' . $user_id;
        }

        //first_agent_id
        $first_agent_id = intval($this->_request('first_agent_id'));
        if ($first_agent_id)
        {
            $where .= ' AND first_agent_id = ' . $first_agent_id;
        }

        //mobile
        $mobile = $this->_request('mobile');
        if ($mobile)
        {
            $where .= ' AND mobile LIKE "%' . $mobile . '%"';
        }

        //username
        $username = $this->_request('username');
        if ($username)
        {
            $where .= ' AND username LIKE "%' . $username . '%"';
        }

        //真实姓名
        $realname = $this->_request('realname');
        if ($realname)
        {
            $where .= ' AND realname LIKE "%' . $realname . '%"';
        }

        //加盟商名称
        $nickname = $this->_request('nickname');
        if ($nickname)
        {
            $where .= ' AND nickname LIKE "%' . $nickname . '%"';
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

        //门店编号
        $store_sn = $this->_request('store_sn');
        if ($store_sn)
        {
            $where .= ' AND store_sn LIKE "%' . $store_sn . '%"';
        }

        //大区
        $big_area_id = $this->_request('big_area_id');
        $big_area_id = ($big_area_id == '') ? 0 : $big_area_id;
        $big_area_id = intval($big_area_id);
        if ($big_area_id)
        {
            $where .= ' AND big_area_id = ' . intval($big_area_id);
        }

        //用户等级
        $user_rank_id = $this->_request('user_rank_id');
        $user_rank_id = ($user_rank_id == '') ? 0 : $user_rank_id;
        $user_rank_id = intval($user_rank_id);
        if ($user_rank_id)
        {
            $where .= ' AND user_rank_id = ' . intval($user_rank_id);
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
        $this->assign('mobile', $mobile);
        $this->assign('first_agent_id', $first_agent_id);
        $this->assign('username', $username);
        $this->assign('realname', $realname);
        $this->assign('user_rank_id', $user_rank_id);
        $this->assign('start_time', $start_time ? $start_time : '');
        $this->assign('end_time', $end_time ? $end_time : '');

        /*重定向页面地址begin*/
        $redirect = $_SERVER['PATH_INFO'];
        $redirect .= $username ? '/username/' . $username : '';
        $redirect .= $first_agent_id ? '/first_agent_id/' . $first_agent_id : '';
        $redirect .= $user_rank_id ? '/user_rank_id/' . $user_rank_id : '';
        $redirect .= $start_time ? '/start_time/' . $start_time : '';
        $redirect .= $end_time ? '/end_time/' . $end_time : '';
        $redirect .= $realname ? '/realname/' . $realname : '';

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
        $user_list = $user_obj->getAgentListData($user_list);
        $this->assign('user_list', $user_list);
        #echo "<pre>";
        #print_r($user_list[0]);
        #echo "</pre>";
        #echo $user_obj->getLastSql();

        //用户等级列表
        $user_rank_obj = new UserRankModel();
        $rank_list = $user_rank_obj->getUserRankList(); 
        $this->assign('rank_list', $rank_list);

        //获取大区列表
        $big_area_obj = M('big_area');
        $big_area_list = $big_area_obj->field('big_area_id, area_name')->order()->select();
        $this->assign('big_area_list', $big_area_list);

        //地址链接
        $url = 'http://' . $_SERVER['HTTP_HOST'];
        $this->assign('url', $url);

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display(APP_PATH . 'Tpl/AcpAgent/get_user_list.html');
    }

    public function get_agent_list()
    {
        $this->user_list('role_type = 3', C('USER_NAME') . '列表', 'user');
    }

    //异步设置父级代理ID号
    //jw
    public function ajax_set_user_father()
    {
        $user_id   = I('post.user_id', 0 ,'int');
        $father_id = I('post.father_id', 0 ,'int');

		if ($user_id && $father_id)
		{
			$user_obj = new UserModel($user_id);
			$status = $user_obj->setParent($user_id, $father_id);
		}

        exit($status ? 'success':'failure');
    }
}

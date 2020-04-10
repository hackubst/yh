<?php
/**
 * 用户的基础类
 * 注意全站要加上用户类别，即user表的role_type=3。否则会取出管理员数据造成问题
 *
 */
class McpUserAction extends McpAction
{
    public function McpUserAction()
    {
        parent::_initialize();
        $this->assign('USER_NAME', C('USER_NAME'));
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

        $this->assign('opt', $opt);
        $this->assign('head_title', $head_title);
        $this->display('get_agent_info');
    }
    
    //代理商信息
    public function get_agent_info()
    {
        $this->user_list('role_type = 4 AND user_id ='.session('user_id'), '查看个人信息','agent');
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

        $admin_user_id = session('user_id');
        $admin_role_type = $user_obj->where('user_id ='.$admin_user_id)->getField('role_type');
        $this->assign('role_type',$admin_role_type);
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
                $this->success('编辑成功', '/McpUser/get_agent_info');
            }
            else
            {
                $this->error('系统繁忙，请重试');
            }
        }
        
        $this->assign('user_info',$user_info);
        $this->assign('head_title','编辑个人信息');
        $this->assign('action_title','管理个人信息');
        $this->assign('action_src','/McpUser/get_agent_info/mod_id/0');

        $this->display();        
    }

}
?>

<?php
/**
 * 即时通讯
 * Created by PhpStorm.
 * User: lkp
 * Date: 2017/8/25
 * Time: 13:18
 */
class AcpChatAction extends AcpAction{

    public $hx_obj = '';
    public function __construct()
    {
        parent::_initialize();
        Vendor('hxapi');
        $data['client_id']='YXA6Un7C8IilEeenu7e6IiU8bA';
        $data['client_secret']='YXA67CuvKraW8lOhXc-Mz0-EAipyt1E';
        $data['org_name']='1126170823178654';
        $data['app_name']='xaiobie';
        $hx_obj = new HxApi($data);
        $this->hx_obj = $hx_obj;
    }

    /**
     * 客服聊天
     * @creator 刘康平
     * @return void
     */
    public function system_im(){

        //注册用户方法
        /*Vendor('hxapi');
        $hx_obj = new HxApi();
        $hx_data['username'] = $user_id;
        $hx_data['password'] = md5($user_info['mobile']);
        $hx_data['nickname'] = $user_info['nickname'];
        //print_r($hx_data);
        //$hx_data['nickname'] = $realname;
        $ret = $hx_obj->signup($hx_data);
        $obj = json_decode($ret);
        //var_dump($obj);
        if($obj->error){
            return false;
        }
        $arr['hx_password'] = $hx_data['password'];
        $s = $user_obj->editUserInfo($arr);
        if ($s)
        {
            return true;
        }else
        {
            return false;
        }*/

        $user_id = session('user_id');
        $user_obj = new UserModel();
        $user_info = $user_obj->getParamUserInfo('user_id = '.$user_id,'');
        $config = array(
            'HX_ID'=> $user_id,
            'HX_PWD'=> $user_info['hx_password']
        );
        $this->assign('config',$config);
        $this->assign('head_title','客服聊天');
        $this->display();
    }

    /**
     * 根据用户名获取用户聊天列表
     * @creator 刘康平
     * @return void
     */
    public function getReplyListByUserId(){
        $from_user_id = $_POST['username'];
        if($from_user_id){
            //$user_obj = new UserModel();
            $user_info['user_id'] = $from_user_id;

            $reply_obj = new ReplyModel();
            $reply_list = $reply_obj->getReplyListByUsername('(from_user_id=1 AND to_user_id='
                .$user_info['user_id'].') OR (to_user_id=1 AND from_user_id='.$user_info['user_id'].')');
//            echo $reply_obj->getLastSql();
            $arr = array(
                "is_read" => 1
            );

            $reply_obj -> setRead('from_user_id='.$from_user_id.' AND to_user_id=0',$arr);
            log_file("setRead".$reply_obj->getLastSql(),"getReplyListByUserId");

            if($reply_list){
                exit(json_encode(array(
                    'code' => 0,
                    'data' => $reply_list
                )));
            }
        }
    }


    /**
     * 获取聊天记录
     * @creator 刘康平
     * @return void
     */
    function getReplyListByUsername(){
        $username = $_POST['username'];
        if($username){
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('user_id','username="'.$username.'"');

            $reply_obj = new ReplyModel();
            $reply_list = $reply_obj->getReplyListByUsername('(from_user_id=0 AND to_user_id='
                .$user_info['user_id'].') OR (to_user_id=0 AND from_user_id='.$user_info['user_id'].')');
            if($reply_list){
                exit(json_encode(array(
                    'code' => 0,
                    'data' => $reply_list
                )));
            }
        }
        exit(json_encode(array(
            'code' => 10001,
            'msg' => '获取资料失败'
        )));
    }

    /**
     * 插入客服的聊天记录
     * @creator 刘康平
     * @return void
     */
    public function ajax_add_reply()
    {
        //$user_id = session('user_id');
        $to_user_id = $_POST['to_user_name'];
        $user_type = $_POST['user_type'];
        $message = $_POST['message'];
        $message_id = $_POST['message_id'];

        //$user_obj = new UserModel();
        //$user_info = $user_obj->getUserInfo('user_id', 'username="' . $to_user_name . '"');

        $arr = array(
            'from_user_id' => session('user_id'),
            'to_user_id' => $to_user_id,
            'message' => $message,
            'user_type' => $user_type,
            'message_id' => $message_id,
        );

        $reply_obj = new ReplyModel();
        $ret = $reply_obj->addReply($arr);
        //var_dump($reply_obj->getLastSql());
        if ($ret) {
            exit('success');
        }
        exit(json_encode($arr));
    }








    /**
     * 添加群组
     * @creator 刘康平
     * @return void
     */
    public function add_group(){
        if(IS_POST){
            $group_name = I('group_name');
            $group_dec = I('group_desc');
            if(empty($group_name)){
                $this->error('群名称不能为空');
            }
            if(empty($group_dec)){
                $this->error('群描述不能为空');
            }
            $hx_obj = $this->hx_obj;
            $options ['groupname'] = $group_name;     //群名
            $options ['desc'] = $group_dec;  //群描述
            $options ['public'] = true;        //群是否公开
            $options ['owner'] = session('user_id');   //群组的管理员
            $result = $hx_obj->createGroup($options);
            //将群组的group_id存入数据库
            $hx_group_obj = new HxGroupModel();
            $data = array(
                'addtime' => time(),
                'hx_group_id'=>$result['data']['groupid'],
                'hx_group_name'=>$group_name,
                'hx_group_mem_num' => 1,
                'user_id' => session('user_id'),
                'hx_group_desc' => $group_dec
            );
            $r = $hx_group_obj->addGroupInfo($data);
            $r ? $this->success('添加群组成功','/AcpChat/group_list') : $this->error('抱歉，添加失败');
        }
        $this->assign('head_title','添加群组');
        $this->display();
    }

    /**
     * 删除群组
     * @creator 刘康平
     * @return void
     */
    public function del_group(){
        $id = I('id');
        $hx_obj = $this->hx_obj;
        //删除环信群组
        $hx_obj->deleteGroup($id);
        //删除数据库群组
        $hx_group_obj = new HxGroupModel();
        $r = $hx_group_obj->delGroup('hx_group_id = '.$id);
        $r ? exit('success') : exit('failure');
    }

    /**
     * 修改群组
     * @creator 刘康平
     * @return void
     */
    public function edit_group(){
        $group_id = I('group_id');
        $hx_group_obj = new HxGroupModel();
        $group_info = $hx_group_obj->getGroupInfo('group_id = '.$group_id);
        if(IS_POST){
            $group_id = I('group_id');
            $group_name = I('group_name');
            $group_desc = I('group_desc');
            //修改环信的群信息
            $hx_obj = $this->hx_obj;
            $hx_group_id=$group_info['hx_group_id'];
            $options['groupname'] = $group_name;
            $options['description'] = $group_desc;
            $hx_obj->modifyGroupInfo($hx_group_id,$options);
            //修改数据库的群信息
            $data = array(
                'hx_group_name' => $group_name,
                'hx_group_desc' => $group_desc
            );
            $r = $hx_group_obj->editGroupInfo('group_id = '.$group_id,$data);
            $r ? $this->success('修改成功','/AcpChat/group_list') : $this->error('修改失败');
        }
        $this->assign('group_id',$group_id);
        $this->assign('group_info',$group_info);
        $this->assign('head_title','修改群组信息');
        $this->display();
    }


    /**
     * 群组列表
     * @creator 刘康平
     * @return void
     */
    public function group_list(){
        $hx_group_obj = new HxGroupModel();
        $submit = I('submit');
        $user_id = session('user_id');
        $where = 'isuse = 1';
        $son_company_id = D('users')->where('user_id = '.$user_id)->getField('son_company_id');
        if(!$son_company_id == 1){
            $where .= ' AND user_id = '.session('user_id');
        }
        //分页处理
        import('ORG.Util.Pagelist');
        //获取总数
        if($submit == 'search'){
            $group_name = I('group_name');
            if(!empty($group_name)){
                $where .= ' AND hx_group_name LIKE '."'%$group_name%'";
                $this->assign('group_name',$group_name);
            }
        }
        $count = $hx_group_obj->getGroupNum($where);

        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $hx_group_obj->setStart($Page->firstRow);
        $hx_group_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $group_list = $hx_group_obj->getHxGroupList('',$where,'addtime DESC','');
//        echo $hx_group_obj->getLastSql();
        for($i=0;$i<count($group_list);$i++){
            $hx_obj = $this->hx_obj;
            $result = $hx_obj->getGroupUsers($group_list[$i]['hx_group_id']);
            $group_list[$i]['hx_group_mem_num'] = $result['count'];
        }

        $this->assign('show',$show);
        $this->assign('group_list',$group_list);
        $this->assign('head_title','群组列表');
        $this->display();
    }
    /**
     * 添加群组的成员
     * @creator 刘康平
     * @return void
     */
    public function add_group_member(){
        if(IS_AJAX){
            $ids = I('user_ids');
            $user_id_arr = explode(',',$ids);
            //群组id
            $hx_group_id = I('hx_group_id');
            $hx_obj = $this->hx_obj;
            $hx_group_obj = new HxGroupModel();
            $hx_group_info = $hx_group_obj->getGroupInfo('hx_group_id = '.$hx_group_id,'');
            //添加环信成员
            if(count($user_id_arr) > 1 ){
                //批量添加
                $usernames['usernames']=$user_id_arr;
                $result = $hx_obj->addGroupMembers($hx_group_id,$usernames);

            }elseif (count($user_id_arr) > 60){
                $this->error('很遗憾，最多只能同时添加60位群友');
            }
            else{
                //单个添加
                $result = $hx_obj->addGroupMember($hx_group_id,$ids);
            }
            //添加数据库群成员
            $hx_group_member_obj = new HxGroupMemberModel();
            $num = 0;
            for($i=0;$i<count($user_id_arr);$i++){
                $arr = array(
                    'addtime' => time(),
                    'hx_group_id' => $hx_group_id,
                    'hx_group_name' => $hx_group_info['hx_group_name'],
                    'user_id' => $user_id_arr[$i]
                );
                $r = $hx_group_member_obj->addGroupMember($arr);
                if($r){
                    $num++;
                }
            }
            if($num == count($user_id_arr)){
                /*
                $from=session('user_id');
                $target_type="chatgroups";
                //$target_type="chatgroups";
                $target=array("$hx_group_id");
                //$target=array("122633509780062768");
                $content="有新成员加入啦";
                $ext['a']="a";
                $ext['b']="b";
                $hx_obj->sendText($from,$target_type,$target,$content,$ext);
                */
                exit('success');
             }else{
                exit('failure');
            }
        }
    }

    /**
     * 删除群组的成员
     * @creator 刘康平
     * @return void
     */
    public function del_group_member(){
        $hx_group_id = I('hx_group_id');
        $username= I('user_id');
        $hx_obj = $this->hx_obj;
        //环信删除群成员
        $hx_obj->deleteGroupMember($hx_group_id,$username);

        //数据库删除群成员
        $hx_group_member_obj = new HxGroupMemberModel();
        $r = $hx_group_member_obj->delMember('user_id = '.$username.' AND hx_group_id = '.$hx_group_id);
        $r ? exit('success') : exit('failure');
    }

    /**
     * 未加该群的员工列表
     * @creator 刘康平
     * @return void
     */
    public function staff_list()
    {
        $user_id = session('user_id');
        $hx_group_id = I('hx_group_id');
        $where = '';
        $son_company_id = D('users')->where('user_id = '.$user_id)->getField('son_company_id');
        if($son_company_id == 1){
            $where .= ' AND role_type = 1 AND group_id IN (2,3,4) AND is_enable = 1';
            $son_company_obj = new SonCompanyModel();
            $son_company_list = $son_company_obj->getSonCompanyList('','isuse = 1');
            $this->assign('son_company_list',$son_company_list);
        }else{
            $where .= ' AND role_type = 1  AND group_id IN (2,3,4) AND is_enable = 1 AND son_company_id = '.$son_company_id;
        }
        $user_obj = new UserModel();

        //经销商列表
        $distributor_obj = new DistributorModel();
        $distributor_list = $distributor_obj->getDistributorList('distributor_name,distributor_id','isuse = 1');
        $this->assign('distributor_list',$distributor_list);

        if(!empty(I('submit'))){
            $son_company_id = I('son_company');
            $distributor_id = I('distributor_id');
            if($son_company_id){
                $where .= ' AND son_company_id = '.$son_company_id;
            }
            if($distributor_id){
                $where .= ' AND distributor_id = '.$distributor_id;
            }
            $this->assign('son_company_id',$son_company_id);
            $this->assign('distributor_id',$distributor_id);
        }

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $user_obj->getUserHxNum($hx_group_id,$where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $fetch = $Page->firstRow;
        $limit = $Page->listRows;
        $show = $Page->show();
        $this->assign('show', $show);
//        $user_list = $user_obj->getUserList('',$where);
        $user_list = $user_obj->getUserHxList($hx_group_id,$fetch,$limit,$where);
        $user_list = $user_obj->getListData($user_list);
        $this->assign('user_list', $user_list);
        $this->assign('hx_group_id',$hx_group_id);
        $this->display();
    }

    /**
     * 该群的成员列表
     * @creator 刘康平
     * @return void
     */
    public function group_member_list(){
        $user_id = session('user_id');
        $hx_group_id = I('hx_group_id');

        $son_company_id = D('users')->where('user_id = '.$user_id)->getField('son_company_id');
        if($son_company_id == 1){
            $son_company_obj = new SonCompanyModel();
            $son_company_list = $son_company_obj->getSonCompanyList('','isuse = 1');
            $this->assign('son_company_list',$son_company_list);
        }
        $hx_group_member_obj = new HxGroupMemberModel();
        $where = ' hx_group_id = '.$hx_group_id;
        if(!empty(I('submit'))){
            $son_company_id = I('son_company');
            if($son_company_id > 1){
                $where .= ' AND son_company_id = '.$son_company_id;
            }
            $this->assign('son_company_id',$son_company_id);
        }

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $hx_group_member_obj->getMemberNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $hx_group_member_obj->setStart($Page->firstRow);
        $hx_group_member_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $hx_group_member_list = $hx_group_member_obj->getGroupMemberList('',$where,'addtime DESC');
        $hx_group_member_list = $hx_group_member_obj->getMemberDetail($hx_group_member_list);
        $this->assign('show',$show);
        $this->assign('hx_group_id',$hx_group_id);
        $this->assign('hx_group_member_list',$hx_group_member_list);
        $this->display();
    }
}

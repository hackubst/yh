<?php
/**
 * acp后台商品类
 */
class AcpGiftAction extends AcpAction
{

    /**
     * 初始化
     * @author yzp
     * @return void
     * @todo 初始化方法
     */
    public function _initialize()
    {
        parent::_initialize();
        // 引入商品公共函数库
        require_cache('Common/func_item.php');
    }

    protected function get_search_condition()
    {
        $where = "";

        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        if ($start_time) {
            $where .= ' AND addtime >= ' . $start_time;
            $this->assign('start_time', $start_time);
        }

        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time) {
            $where .= ' AND addtime <= ' . $end_time;
            $this->assign('end_time', $end_time);
        }

        $name = $this->_request('name');
        if($name){
            $where .= ' AND name LIKE "%' . $name . '%"';
            $this->assign('name', $name);
        }

        $gift_name = $this->_request('gift_name');
        if ($gift_name) {
            $this->assign('gift_name', $gift_name);
            $gift_id_list = D('Gift')->where('gift_name LIKE "%' . $gift_name . '%"')
                ->field('gift_id')->select();

            $gift_ids = array();
            if ($gift_id_list) {
                foreach ($gift_id_list as $k => $v) {
                    array_push($gift_ids, $v['gift_id']);
                }

                $gift_ids = implode(',', $gift_ids);
                $where .= ' AND gift_id IN ( ' . $gift_ids . ' )';

            } else {
                $where .= ' AND false';
            }

        }

        $mobile = $this->_request('mobile');
        if ($mobile) {
            $user_list = D('User')->where('mobile LIKE "%' . $mobile . '%"')
                ->field('user_id')
                ->select();

            $user_ids = array();
            foreach ($user_list as $k => $v) {
                array_push($user_ids, $v['user_id']);
            }

            $user_ids = implode(',', $user_ids);
            $where .= ' AND user_id in ( ' . $user_ids . ' )';
            $this->assign('mobile', $mobile);
        }

        $nickname = $this->_request('nickname');
        if ($nickname) {
            $user_list = D('User')->where('nickname LIKE "%' . $nickname . '%"')
                ->field('user_id')
                ->select();

            $user_ids = array();
            foreach ($user_list as $k => $v) {
                array_push($user_ids, $v['user_id']);
            }

            $user_ids = implode(',', $user_ids);
            $where .= ' AND user_id in ( ' . $user_ids . ' )';
            $this->assign('nickname', $nickname);
        }

        $realname = $this->_request('realname');
        if ($realname) {
            $user_list = D('User')->where('realname LIKE "%' . $realname . '%"')
                ->field('user_id')
                ->select();

            $user_ids = array();
            foreach ($user_list as $k => $v) {
                array_push($user_ids, $v['user_id']);
            }

            $user_ids = implode(',', $user_ids);
            $where .= ' AND dcp_id in ( ' . $user_ids . ' )';
            $this->assign('realname', $realname);
        }

        //点卡状态
        $isuse = $this->_request('isuse');
        if($isuse)
        {
            $this->assign('isuse', $isuse);
        }
        if($isuse != 4)
        {
            switch ($isuse) {
                case '1':
                    $where .= ' AND isuse = 0';
                    break;

                case '2':
                    $where .= ' AND isuse = 1 AND end_time >='.time();
                    break;
                case '3':
                    $where .= ' AND isuse = 1 AND end_time <'.time();
                    break;
            }
        }

        return $where;
    }
    //获取卡密列表
    public function get_gift_password_list()
    {
        $user_gift_id = I('user_gift_id');
        $id = $this->_request('id', '');

        if($user_gift_id)
        {
            $where = 'user_gift_id = '.$user_gift_id;
        }else{
            $where = '1';
        }
        $user_obj = new UserModel();
        //ID筛选
        if ($id) {
            $user_ids = $user_obj->where('id LIKE "%' . $id . '%"')->getField('user_id', true);

            if ($user_ids) {
                $user_ids = implode(',',$user_ids);
                $where .= ' AND user_id in ('.$user_ids.')';
            } else {
                $where .= ' AND user_id = -1';
            }
        }

        $where .= $this->get_search_condition();
        $user_gift_password_obj = new UserGiftPasswordModel();

        import('ORG.Util.Pagelist');
        $count = $user_gift_password_obj->getUserGiftPasswordNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $user_gift_password_obj->setStart($Page->firstRow);
        $user_gift_password_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $user_gift_password_list = $user_gift_password_obj->getUserGiftPasswordList('',$where,'use_time desc');

        $user_gift_password_list = $user_gift_password_obj->getListDataTwo($user_gift_password_list);
        // dump($user_gift_password_list);die;

        $this->assign('user_gift_password_list', $user_gift_password_list ? : array());
        $this->assign('id', $id);
        $this->assign('head_title', '卡密列表');
        $this->display();
    }

    //获取所有兑换记录
    //@author yzp
    public function get_user_gift_list()
    {
        $user_gift_obj = new UserGiftModel();

        $where = 'isuse = 1' . $this->get_search_condition();

        $gift_card_id = I('get.gift_card_id');

        if($gift_card_id)
        {
            $where .= ' AND gift_card_id ='.$gift_card_id;
        }
        $id = $this->_request('id', '');
        //ID筛选
        $user_obj = new UserModel();
        if ($id) {
            $user_ids = $user_obj->where('id LIKE "%' . $id . '%"')->getField('user_id', true);

            if ($user_ids) {
                $user_ids = implode(',',$user_ids);
                $where .= ' AND user_id in ('.$user_ids.')';
            } else {
                $where .= ' AND user_id = -1';
            }
        }
        import('ORG.Util.Pagelist');
        $count = $user_gift_obj->getUserGiftNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $user_gift_obj->setStart($Page->firstRow);
        $user_gift_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $user_gift_list = $user_gift_obj->getUserGiftList('', $where, ' addtime DESC');
        $user_gift_list = $user_gift_obj->getListDataThree($user_gift_list);
        $this->assign('user_gift_list', $user_gift_list);
        $this->assign('id', $id);
        $this->assign('head_title', '兑换记录列表');
        $this->display();
    }


    //获取所有用户奖品券信息
    //@author yzp
    public function get_gift_card_list()
    {
        $gift_card_obj = new GiftCardModel();

        $where = 'isuse = 1' . $this->get_search_condition();
        import('ORG.Util.Pagelist');
        $count = $gift_card_obj->getGiftCardNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $gift_card_obj->setStart($Page->firstRow);
        $gift_card_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $gift_card_list = $gift_card_obj->getGiftCardList('', $where, ' addtime DESC');
        foreach($gift_card_list as $k => &$v){
            $v['money'] = feeHandle($v['money']);
        }
        // $gift_card_list = $gift_card_obj->getListData($gift_card_list);
        $this->assign('gift_card_list', $gift_card_list);

        $this->assign('head_title', '兑换卡列表');
        $this->display();
    }

    //@author
    //添加礼品
    public function add_gift_card()
    {
        $gift_card_obj = new GiftCardModel();

        $action = $this->_post('action');
        if($action == 'add')             //执行添加操作
        {
            $name = $this->_post('name');
            $money = $this->_post('money');
            $cash    = $this->_post('cash');
            $first_code    = $this->_post('first_code');
//            $img  = $this->_post('img');

            if(!$name)
            {
                $this->error('对不起，请填写兑换卡名称');
            }
            if(!$money)
            {
                $this->error('对不起，请填写所需金豆');
            }
//            if(!$img)
//            {
//                $this->error('对不起，请上传图片');
//            }
            if(!$cash)
            {
                $this->error('对不起，请填写价值现金');
            }
            if(!$first_code)
            {
                $this->error('对不起，请填写卡密首字符');
            }

            $data = array(
                    'name'=>  $name,
                    'money'=> $money,
                    'first_code'    =>  $first_code,
                    'cash'  =>  $cash,
            );
            $gift_card_id = $gift_card_obj->addGiftCard($data);

            if ($gift_card_id)
            {
                $this->success('恭喜您，兑换卡添加成功','/AcpGift/get_gift_card_list');
            }
            else
            {
                $this->success('抱歉，添加失败');
            }
        }

//        $this->assign('pic_data', array(
//            'name' => 'img',
//            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
//        ));

        $this->assign('head_title','添加兑换卡');
        $this->assign('action','add');
        $this->display();
    }

    //@author
    //修改礼品
    public function edit_gift_card()
    {
        $gift_card_id = intval($this->_get('gift_card_id'));
        $gift_card_obj = new GiftCardModel();
        $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id = ' . $gift_card_id, '');

        $action = $this->_post('action');
        if($action == 'edit')             //执行添加操作
        {
            $name = $this->_post('name');
            $money = $this->_post('money');
            $cash    = $this->_post('cash');
            $first_code   = $this->_post('first_code');

            if(!$name)
            {
                $this->error('对不起，请填写兑换卡名称');
            }
            if(!$money)
            {
                $this->error('对不起，请填写所需金豆');
            }

            if(!$cash)
            {
                $this->error('对不起，请填写价值现金');
            }
            if (!$first_code) {
                $this->error('对不起，请填写卡密首字符');
            }

            $data = array(
                    'name'=>  $name,
                    'money'=> $money,
                    'first_code'    =>  $first_code,
                    'cash'  =>  $cash,
            );

            $gift_card_obj = new GiftCardModel($gift_card_id);
            $success = $gift_card_obj->editGiftCard('gift_card_id ='.$gift_card_id,$data);

            if ($success)
            {
                $this->success('恭喜您，兑换卡编辑成功','/AcpGift/get_gift_card_list');
            }
            else
            {
                $this->success('抱歉，编辑失败');
            }
        }

        $this->assign('pic_data', array(
            'name' => 'img',
            'url'  => $gift_card_info['img'],
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));

        $this->assign('gift_card_info',$gift_card_info);
        $this->assign('head_title','编辑兑换卡');
        $this->assign('action','edit');
        $this->display('add_gift_card');
    }

    public function use_gift_password()
    {
        $user_gift_password_id = I('post.user_gift_password_id', 0, 'int');

        if ($user_gift_password_id) {
            $user_gift_password_obj = new UserGiftPasswordModel();
            $data = [
                'use_time' => time(),
                'isuse' => 0
            ];
            $status = $user_gift_password_obj->editUserGiftPassword('user_gift_password_id ='.$user_gift_password_id,$data);
            exit($status ? 'success' : 'failure');
        }

        exit('failure');
    }

    //@author yzp
    //删除礼品
    public function delete_gift_card()
    {
        $gift_card_id = I('post.gift_card_id', 0, 'int');

        if ($gift_card_id) {
            $gift_card_obj = new GiftCardModel();
            $status = $gift_card_obj->delGiftCard($gift_card_id);
            exit($status ? 'success' : 'failure');
        }

        exit('failure');
    }

    //@author yzp
    //设置上下架
    public function set_enable()
    {
        $gift_id = I('post.gift_id', 0, 'int');
        $opt     = I('post.opt');

        if ($gift_id && is_numeric($opt)) {
            $status = D('Gift')->where('gift_id =' . $gift_id)->setField('isuse', $opt);
            exit($status ? 'success' : 'failure');
        }

        exit('failure');
    }

    //批量上下架
    //@author yzp
    public function batch_set_enable()
    {
        $gift_ids = I('post.gift_ids');
        $opt      = I('post.opt');

        if ($gift_ids && is_numeric($opt)) {
            $gift_array  = explode(',', $gift_ids);
            $success_num = 0;
            $gift_obj    = D('Gift');

            foreach ($gift_array as $gift_id) {
                $status = $gift_obj->where('gift_id =' . $gift_id)->setField('isuse', $opt);
                $success_num += $status ? 1 : 0;

            }
            exit($success_num ? 'success' : 'failure');

        } else {
            exit('failure');

        }
    }

    //@author yzp
    //批量删除礼品
    public function batch_delete_gift_card()
    {
        $gift_ids = I('post.gift_ids');

        if ($gift_ids) {
            $gift_array  = explode(',', $gift_ids);
            $success_num = 0;
            $gift_obj    = D('Gift');

            foreach ($gift_array as $gift_id) {
                $status = $gift_obj->where('gift_id =' . $gift_id)->delete();
                $success_num += $status ? 1 : 0;

            }
            exit($success_num ? 'success' : 'failure');

        } else {
            exit('failure');

        }
    }

}

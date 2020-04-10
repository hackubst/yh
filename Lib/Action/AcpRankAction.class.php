<?php

/**
 * acp后台商品类
 */
class AcpRankAction extends AcpAction
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
            $end = $start_time + 24 * 3600;
            $where .= ' AND addtime >= ' . $start_time . ' AND addtime < ' . $end;
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
        if ($name) {
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

        return $where;
    }

    //获取所有兑换记录
    //@author yzp
    public function get_rank_list()
    {
        $rank_obj = new RankListModel();

        $where = '1' . $this->get_search_condition();
        if ($where == '1') {
            $start_time = strtotime(date('Y-m-d', time()));
            $end_time = strtotime(date('Y-m-d', time())) + 24 * 3600;

            $where = 'addtime >=' . $start_time . ' AND addtime <' . $end_time;

            $this->assign('start_time', $start_time);
            $this->assign('end_time', $end_time);
        }

        import('ORG.Util.Pagelist');
        $count = $rank_obj->getRankListNum($where);
        $Page = new Pagelist($count, 20);
        $rank_obj->setStart(0);
        $rank_obj->setLimit(20);
        $show = $Page->show();
        $this->assign('show', $show);

        $rank_list = $rank_obj->getRankListList('', $where, ' reward DESC');
        $rank_list = $rank_obj->getListData($rank_list);
        // dump($rank_list);die;
        // dump($rank_obj->getLastSql());die;
        $this->assign('rank_list', $rank_list);

        $this->assign('head_title', '排行榜列表');
        $this->display();
    }


    public function set_rank()
    {
        $action = $this->_request('action', '');
        $rank_list_id = $this->_request('rank_list_id', 0);
        $win = $this->_request('win', 0);
        $reward = $this->_request('reward', 0);

        $nickname = $this->_request('nickname', '');
        $index = $this->_request('index', 0);
        $rank_time = $this->_request('rank_time');

        $addtime = strtotime($rank_time);
        //缺少判断可操作的权限===============================================================================
        if (!$win)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,盈利金豆必须大于0!')));
        if ($win && !preg_match('/^[1-9][0-9]*$/', $win))
            exit(json_encode(array('code' => 400, 'msg' => '对不起,盈利金豆格式不正确!')));
        if (!$reward)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,排行奖励必须大于0!')));
        if ($reward && !preg_match('/^[1-9][0-9]*$/', $reward))
            exit(json_encode(array('code' => 400, 'msg' => '对不起,排行奖励格式不正确!')));

        if ($action == 'set') {
            if (!$rank_list_id) {
                $data = [
                    'win' => $win,
                    'reward' => $reward,
                    'nickname' => $nickname,
                    // 'sort' => $index,
                    'addtime' => $addtime
                ];
                $rank_obj = new RankListModel();
                $rank_obj->addRankList($data);
                //修改排行榜记录
                $admin_log_obj = new AdminLogModel();
                $admin_log_obj->addAdminLog(array('type' => AdminLogModel::RANK));

            } else {
                $data = [
                    'win' => $win,
                    'reward' => $reward,
                    'nickname' => $nickname,
                    // 'sort' => $index
                ];
                $rank_obj = new RankListModel($rank_list_id);
                $rank_obj->editRankList('rank_list_id = ' . $rank_list_id, $data);

                //修改排行榜记录
                $admin_log_obj = new AdminLogModel();
                $admin_log_obj->addAdminLog(array('type' => AdminLogModel::RANK));
            }

            exit(json_encode(array('code' => 200, 'msg' => '恭喜您,操作成功!')));
        }
    }


    //获取所有用户奖品券信息
    //@author yzp
    public function get_gift_card_list()
    {
        $gift_card_obj = new GiftCardModel();

        $where = $this->get_search_condition();
        import('ORG.Util.Pagelist');
        $count = $gift_card_obj->getGiftCardNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $gift_card_obj->setStart($Page->firstRow);
        $gift_card_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $gift_card_list = $gift_card_obj->getGiftCardList('', $where, ' addtime DESC');
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
        if ($action == 'add')             //执行添加操作
        {
            $name = $this->_post('name');
            $money = $this->_post('money');
            $cash = $this->_post('cash');
            $img = $this->_post('img');

            if (!$name) {
                $this->error('对不起，请填写兑换卡名称');
            }
            if (!$money) {
                $this->error('对不起，请填写所需金豆');
            }
            if (!$img) {
                $this->error('对不起，请上传图片');
            }
            if (!$cash) {
                $this->error('对不起，请填写价值现金');
            }

            $data = array(
                'name' => $name,
                'money' => $money,
                'img' => $img,
                'cash' => $cash,
            );
            $gift_card_id = $gift_card_obj->addGiftCard($data);

            if ($gift_card_id) {
                $this->success('恭喜您，兑换卡添加成功', '/AcpRank/get_gift_card_list');
            } else {
                $this->success('抱歉，添加失败');
            }
        }

        $this->assign('pic_data', array(
            'name' => 'img',
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));

        $this->assign('head_title', '添加兑换卡');
        $this->assign('action', 'add');
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
        if ($action == 'edit')             //执行添加操作
        {
            $name = $this->_post('name');
            $money = $this->_post('money');
            $cash = $this->_post('cash');
            $img = $this->_post('img');

            if (!$name) {
                $this->error('对不起，请填写兑换卡名称');
            }
            if (!$money) {
                $this->error('对不起，请填写所需金豆');
            }
            if (!$img) {
                $this->error('对不起，请上传图片');
            }
            if (!$cash) {
                $this->error('对不起，请填写价值现金');
            }

            $data = array(
                'name' => $name,
                'money' => $money,
                'img' => $img,
                'cash' => $cash,
            );

            $gift_card_obj = new GiftCardModel($gift_card_id);
            $success = $gift_card_obj->editGiftCard('gift_card_id =' . $gift_card_id, $data);

            if ($success) {
                $this->success('恭喜您，兑换卡编辑成功', '/AcpGift/get_gift_card_list');
            } else {
                $this->success('抱歉，编辑失败');
            }
        }

        $this->assign('pic_data', array(
            'name' => 'img',
            'url' => $gift_card_info['img'],
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));

        $this->assign('gift_card_info', $gift_card_info);
        $this->assign('head_title', '编辑兑换卡');
        $this->assign('action', 'edit');
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
            $status = $user_gift_password_obj->editUserGiftPassword('user_gift_password_id =' . $user_gift_password_id, $data);
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
        $opt = I('post.opt');

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
        $opt = I('post.opt');

        if ($gift_ids && is_numeric($opt)) {
            $gift_array = explode(',', $gift_ids);
            $success_num = 0;
            $gift_obj = D('Gift');

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
            $gift_array = explode(',', $gift_ids);
            $success_num = 0;
            $gift_obj = D('Gift');

            foreach ($gift_array as $gift_id) {
                $status = $gift_obj->where('gift_id =' . $gift_id)->delete();
                $success_num += $status ? 1 : 0;

            }
            exit($success_num ? 'success' : 'failure');

        } else {
            exit('failure');

        }
    }


    /**
     * 发放排行榜奖励
     * @date: 2019/4/18
     * @author: hui
     */
    public function send_rank_reward()
    {
        if (IS_AJAX && IS_POST) {
            $push_log_obj = new PushLogModel();
            $rank_list_obj = new RankListModel();
            $where = 'addtime = ' .(strtotime(date("Y-m-d", time())) + 1)  . ' AND is_send = 0';
            $rank_list_list = $rank_list_obj->getRankListList('', $where);
            foreach ($rank_list_list as $k => $v) {
                $arr = array(
                    'user_id' => $v['user_id'],
                    'opt' => PushLogModel::RANK_LIST_REWARD,
                    'content' => date("Y-m-d", strtotime("-1 day")) . '排行榜奖励已发放,恭喜你获得' . $v['reward'] . '金豆',
                );
                $push_log_obj->addPushLog($arr);
            }
            $res = $rank_list_obj->editRankList($where, ['is_send' => 1]);
            if ($res !== false) {
                exit ('success');
            } else {
                exit ('failure');
            }
        }
    }

    //获取所有兑换记录
    //@author yzp
    public function get_income_rank()
    {
        $agent_income_obj = new AgentIncomeModel();

        $where = 'isuse = 1';

        import('ORG.Util.Pagelist');
        $agent_income_list = $agent_income_obj->getAgentIncomeList('agent_id,SUM(gain_money) AS gain_money', $where, ' gain_money DESC','agent_id');
        $count = count($agent_income_list);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $agent_income_obj->setStart($Page->firstRow);
        $agent_income_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $agent_income_list = $agent_income_obj->getAgentIncomeList('agent_id,SUM(gain_money) AS gain_money', $where, ' gain_money DESC','agent_id');
        $agent_income_list = $agent_income_obj->getListData($agent_income_list);
        $this->assign('agent_income_list', $agent_income_list);

        $this->assign('head_title', '代理商利润排行榜');
        $this->display();
    }


    public function get_recharge_rank()
    {
        $account_obj = new AccountModel();

        $where = 'change_type ='.AccountModel::RECHARGEOUT;

        import('ORG.Util.Pagelist');
        $account_list = $account_obj->where($where)->group('user_id')->count();
        $count = count($account_list);

        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $account_obj->setStart($Page->firstRow);
        $account_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $account_list = $account_obj->getAccountList('user_id,SUM(amount_out) AS amount_out', $where, ' amount_out DESC','','user_id');
        $account_list = $account_obj->getListDataName($account_list);
        $this->assign('account_list', $account_list);

        $this->assign('head_title', '代理商充值排行榜');
        $this->display();
    }


    public function get_card_rank()
    {
        $user_gift_password_obj = new UserGiftPasswordModel();

        $where = 'dcp_id != 0';
         import('ORG.Util.Pagelist');
        $account_list = $user_gift_password_obj->where($where)->group('dcp_id')->count();
        $count = count($account_list);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $user_gift_password_obj->setStart($Page->firstRow);
        $user_gift_password_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $account_list = $user_gift_password_obj->getUserGiftPasswordList('dcp_id,COUNT(*) as num', $where, ' num DESC','dcp_id');

        $account_list = $user_gift_password_obj->getListDataName($account_list);
        $this->assign('account_list', $account_list);

        $this->assign('head_title', '代理商出卡排行榜');
        $this->display();
    }

    /**
     * 今日排行榜列表
     */
    public function get_robot_list(){
        $robot_obj = new RobotModel();
        $count = $robot_obj->getRobotNum();
        import('ORG.Util.Pagelist');

        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $robot_obj->setStart($Page->firstRow);
        $robot_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);
        $robot_list = $robot_obj->getRobotList('','','today_money DESC');
        foreach ($robot_list as $k => $v)
        {
            $robot_list[$k]['today_money'] = feeHandle($v['today_money']);
        }
        $this->assign('robot_list', $robot_list);

        $this->assign('head_title', '今日排行榜列表');
        $this->display();
    }


    public function set_robot_rank()
    {
        $action = $this->_request('action', '');
        $robot_id = $this->_request('robot_id', 0);
        $win = $this->_request('win', 0);
        $nickname = $this->_request('nickname', '');

        //缺少判断可操作的权限===============================================================================
        if (!$win)
            exit(json_encode(array('code' => 400, 'msg' => '对不起,盈利金豆必须大于0!')));
        if ($win && !preg_match('/^[1-9][0-9]*$/', $win))
            exit(json_encode(array('code' => 400, 'msg' => '对不起,盈利金豆格式不正确!')));
        if ($action == 'set') {
            $data = [
                'today_money' => floatval($win),
                'robot_name' => $nickname,
            ];
            $robot_obj = new RobotModel();
            $robot_obj->editRobot('robot_id = ' . $robot_id, $data);
            log_file('sql ='.$robot_obj->getLastSql(),'edit_robot');
        }

        exit(json_encode(array('code' => 200, 'msg' => '恭喜您,操作成功!')));

    }

}

<?php
/**
 * acp后台商品类
 */
class McpRedPacketAction extends McpAction
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

        $title = $this->_request('title');
        if($title){
            $where .= ' AND title LIKE "%' . $title . '%"';
            $this->assign('title', $title);
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

        //红包状态
        $status = $this->_request('status');
        if($status)
        {
            $this->assign('status', $status);
        }
        if($status != 4)
        {
            switch ($status) {
                case '1':
                    $where .= ' AND is_cancel = 0 AND expire_time >='.time();
                    break;
                
                case '2':
                    $where .= ' AND is_cancel = 1 AND expire_time >='.time();
                    break;
                case '3':
                    $where .= ' AND expire_time <'.time();
                    break;
            }   
        }
        return $where;
    }


    //获取所有领取红包记录
    //@author yzp
    public function get_red_packet_log_list()
    {
        $red_packet_log_obj = new RedPacketLogModel();

        $where = 'true' . $this->get_search_condition();

        $red_packet_id = I('get.red_packet_id');

        if($red_packet_id)
        {
            $where .= ' AND red_packet_id ='.$red_packet_id;
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
        $count = $red_packet_log_obj->getRedPacketLogNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $red_packet_log_obj->setStart($Page->firstRow);
        $red_packet_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $red_packet_log_list = $red_packet_log_obj->getRedPacketLogList('', $where, ' addtime DESC');

        $red_packet_log_list = $red_packet_log_obj->getDataList($red_packet_log_list);
        $this->assign('red_packet_log_list', $red_packet_log_list ? : array());
        $this->assign('id',$id);
        $this->assign('head_title', '红包领取记录列表');
        $this->display();
    }


    //获取红包信息
    //@author yzp
    public function base_red_packet_list($where)
    {
        $red_packet_obj = new RedPacketModel();

        $where .= $this->get_search_condition();

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
        $count = $red_packet_obj->getRedPacketNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $red_packet_obj->setStart($Page->firstRow);
        $red_packet_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('id', $id);
        $this->assign('show', $show);

        $red_packet_list = $red_packet_obj->getRedPacketList('', $where, ' addtime DESC');
        $red_packet_list = $red_packet_obj->getListData($red_packet_list);
        // dump($red_packet_list);die;
        $this->assign('red_packet_list', $red_packet_list ? : array());

        $this->display('get_red_packet_list');

    }


    public function admin_red_packet_list(){
        $admin_id = session('user_id');
        $this->assign('act','admin');
        $this->assign('head_title', '后台发放红包列表');
        $this->base_red_packet_list('isuse = 1 AND user_id ='.$admin_id);

    }

    public function get_red_packet_list(){
        $this->assign('act','user');
        $this->assign('head_title', '红包列表');
        $this->base_red_packet_list('isuse = 1 AND user_id != 1');

    }


    /**
     * 生成红包
     */
    public function add_red_package(){
        $user_obj = new UserModel();
        $admin_id = session('user_id');
        $left_money = $user_obj->where('user_id ='.$admin_id)->getField('left_money');
        $config_base_obj = new ConfigBaseModel();
        $red_expire_time = $config_base_obj->getConfig('red_expire_time') ?: 7;   //红包有效期天数

        $this->assign('left_money',$left_money);
        if(IS_POST){
            $type = I('type');
            $total_money = I('total_money');
            $num = I('num');
            $title = I('title');

            if(!$total_money){
                $this->error('对不起，请填写红包总金额');
            }
            if(!$num){
                $this->error('对不起，请填写红包总数量');
            }
            $red_package_obj = new RedPacketModel();
            if($type == 1){
                $each_money = $total_money / $num ;
            }
            $admin_id = session('user_id');
            $left_moeny = $user_obj->where('user_id ='.$admin_id)->getField('left_money');
            if($left_moeny < $total_money){
                $this->error('对不起，您的金豆余额不足');
            }
            $account_obj = new AccountModel();
            $acc_res = $account_obj->addAccount($admin_id,AccountModel::AGENT_RED_PACKET, $total_money * -1,'代理发红包');
            if($acc_res == -1){
                $this->error('对不起，您的金豆余额不足');
            }
            $data = array(
                'type' => $type,
                'total_money' => $total_money,
                'title' => $title?:'恭喜发财，大吉大利！',
                'num' => $num,
                'residue_money' => $total_money,
                'residue_num' => $num,
                'each_money' => $each_money?:0,
                'user_id' => session('user_id'),
                'expire_time' => time() + $red_expire_time * 24 * 3600
            );
            $res = $red_package_obj->addRedPacket($data);
            if ($res)
            {
                $redis_obj = new Redis();
                $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
                $red_str = 'red_'.$res;
                $redis_obj->set($red_str,$num);
//                $this->success('生成成功','get_admin_red_packet_list');
                $this->success('生成成功','/McpRedPacket/admin_red_packet_list');
            }
            else
            {
                $this->success('抱歉，生成失败');
            }

        }


        $this->assign('head_title','生成红包');
        $this->display();
    }

    public function cancel_red(){
        if(IS_AJAX){
            $id = I('id');
            $red_packet_obj = new RedPacketModel();
            $res = $red_packet_obj->editRedPacket('red_packet_id ='.$id,['is_cancel' => 1]);
            //撤销退钱
            $red_info = $red_packet_obj->getRedPacketInfo('red_packet_id ='.$id);
            $account_obj = new AccountModel();
            $account_obj->addAccount($red_info['user_id'],AccountModel::AGENT_RED_PACKET_RETURN,$red_info['residue_money'],'代理红包撤销退回');

            exit($res !== false ? 'success' : 'failure');
        }
        exit('failure');
    }
}

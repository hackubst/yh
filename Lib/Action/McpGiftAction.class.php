<?php
/**
 * Mcp后台商品类
 */
class McpGiftAction extends McpAction
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

    //核验卡密
    public function check_card_password()
    {

        $where = '';

        $card_password = $this->_request('card_password');
//        $this->assign('card_password', $card_password);

        if ($card_password) {
            $card_password = str_replace('，', ',', $card_password);
            $card_password = str_replace("\r\n", ',', $card_password);
            $card_password = str_replace("\n", ',', $card_password);
            $card_password = str_replace(' ', ',', $card_password);
            $card_list = explode(',', $card_password);
            $user_gift_password_obj = new UserGiftPasswordModel();
            $total_money = 0;
            $enable_card_password = '';
            foreach ($card_list as $k => $v) {
                $password_card = $user_gift_password_obj->getUserGiftPasswordField('card_password = "' . $v . '" AND isuse = 1', 'user_gift_password_id') ?: 0;
                if($password_card == 0)
                {
                    $enable_card_password .= $v.',';
                }
                $user_gift_password_ids[] = $password_card;
                $money = $user_gift_password_obj->getUserGiftPasswordField('card_password = "' . $v . '" AND isuse = 1', 'money') ?: 0;
                $total_money += $money;
            }
            $this->assign('card_password', trim($enable_card_password,','));
            $user_gift_password_ids = implode(',', $user_gift_password_ids);
           //卡密核销
            $data = [
                'use_time' => time(),
                'isuse' => 0,
                'dcp_id' => session('user_id')
            ];
            $status = $user_gift_password_obj->editUserGiftPassword('user_gift_password_id IN(' . $user_gift_password_ids . ')', $data);
            //金豆加入代理商余额中

            if($total_money > 0){
                $account_obj = new AccountModel();
                $account_obj->addAccount(session('user_id'),AccountModel::CARD_RETURN,$total_money,'卡密核销');
            }

            if ($user_gift_password_ids) {
                $where = 'user_gift_password_id IN(' . $user_gift_password_ids . ')';
            } else {
                return $where;
            }
        }

        return $where;
    }

    //获取卡密列表
    public function get_gift_password_list()
    {
        
        $where = 'user_gift_password_id = 0';

        $new_where = $this->check_card_password();
        if($new_where)
        {
            $where = $new_where;
        }
        $user_gift_password_obj = new UserGiftPasswordModel();


        import('ORG.Util.Pagelist');
        $count = $user_gift_password_obj->getUserGiftPasswordNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $user_gift_password_obj->setStart($Page->firstRow);
        $user_gift_password_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $user_gift_password_list = $user_gift_password_obj->getUserGiftPasswordList('',$where);
        $user_gift_password_list = $user_gift_password_obj->getListDataTwo($user_gift_password_list);
        // dump($user_gift_password_list);die;
//        总收卡额，应付金额（99折），各面值卡数
        $write_off_rate = $GLOBALS['config_info']['DCP_WRITE_OFF_RATE'];
        $total_cash = $user_gift_password_obj->where($where)->getField('sum(cash)');
        $total_pay_money = $total_cash * (100 - $write_off_rate) / 100;
        $card_list = $user_gift_password_obj->where($where)->group('cash')->field('cash,count(*) as count')->select();
        $this->assign('total_cash', $total_cash?:0);
        $this->assign('total_pay_money', $total_pay_money?:0);
        $this->assign('card_list', $card_list?:[]);
        $this->assign('user_gift_password_list', $user_gift_password_list);
        $this->assign('head_title', '核销卡密');
        $this->display();
    }

    //代理商已经核验的点卡
    public function get_checked_gift_list()
    {
        $where = 'isuse = 0 AND dcp_id ='.session('user_id');

        $nickname = $this->_request('nickname');
        if ($nickname) {

            $user_info = D('User')->where('nickname = "' . $nickname . '"')
                ->field('user_id')
                ->find();

            $this->assign('nickname', $nickname);
            if($user_info)
            {
                $where .= ' AND user_id ='.$user_info['user_id'];
            }else{
                $where .= ' AND user_id = -1';
            }
        }

        $id = $this->_request('id');
        if ($id) {

            $user_info = D('User')->where('id = "' . $id . '"')
                ->field('user_id')
                ->find();

            $this->assign('id', $id);
            if($user_info)
            {
                $where .= ' AND user_id ='.$user_info['user_id'];
            }else{
                $where .= ' AND user_id = -1';
            }
        }

        //起始时间
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        #echo $start_time;
        if ($start_time) {
            $where .= ' AND addtime >= ' . $start_time;
        }

        //结束时间
        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time) {
            $where .= ' AND addtime <= ' . $end_time;
        }



        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);

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

        $this->assign('user_gift_password_list', $user_gift_password_list);
        $this->assign('hidden', 1);

        $this->assign('head_title', '核销记录列表');
        $this->display('get_gift_password_list');
    }

    //核销使用点卡
    public function use_gift_password()
    {
        $user_gift_password_id = I('post.user_gift_password_id', 0, 'int');

        if ($user_gift_password_id) {
            $user_gift_password_obj = new UserGiftPasswordModel();
            $data = [
                'use_time' => time(),
                'isuse' => 0,
                'dcp_id' => session('user_id')
            ];
            $status = $user_gift_password_obj->editUserGiftPassword('user_gift_password_id ='.$user_gift_password_id,$data);
            //金豆加入代理商余额中

            exit($status ? 'success' : 'failure');
        }

        exit('failure');
    }
}

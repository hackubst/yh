<?php

class IndexApiModel extends ApiModel
{

    private $user_id;

    public function __construct()
    {
        $this->user_id = intval(session('user_id'));
        // $this->user_id = 62290;
    }

    CONST DOING = 1; //正在进行中

    CONST ENDING = 2; //已结束

    CONST WEEK = 1; //7天
    CONST MONTH = 2; //30天
    CONST HALFYEAR = 3; //半年
    CONST YEAR = 4; //一年

    CONST SAVE = 1;//存入银行

    const YESTERDAY = 1; //昨天
    const TODAY = 2;  //今天
    const SEVEN = 3;   //7 天

    const RAND = 2;   //随机红包


    /**
     *首页轮播图列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo首页轮播图列表
     */
    public function custFlashList($params)
    {
        $type = $params['type']?:0;

        $cust_flash_obj = new CustFlashModel();

        $where = 'isuse = 1';
        $where .= ' AND adv_type = '.$type;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $cust_flash_obj->getCustFlashNum($where);
        $cust_flash_obj->setStart($firstRow);
        $cust_flash_obj->setLimit($num_per_page);

        $fields = 'cust_flash_id,pic';

        $cust_flash_list = $cust_flash_obj->getCustFlashList($fields, $where, 'serial asc');

        ApiModel::returnResult(0, array(
            'cust_flash_list' => $cust_flash_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *活动列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo活动列表
     */
    public function activityList($params)
    {
        $type = $params['type'];

        $marketing_rule_obj = new MarketingRuleModel();

        $where = 'isuse = 1';
        $time = time();

        switch ($type) {
            case self::DOING :
                $where .= ' AND start_time <=' . $time . ' AND end_time >=' . $time;
                break;
            case self::ENDING :
                $where .= ' AND end_time <' . $time;
                break;
            default:
                # code...
                break;
        }

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $marketing_rule_obj->getMarketingRuleNum($where);
        $marketing_rule_obj->setStart($firstRow);
        $marketing_rule_obj->setLimit($num_per_page);

        $fields = 'marketing_rule_id,start_time,end_time,marketing_rule_name,imgurl';

        $marketing_rule_list = $marketing_rule_obj->getMarketingRuleList($fields, $where, 'addtime desc');

        ApiModel::returnResult(0, array(
            'marketing_rule_list' => $marketing_rule_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *新闻公告列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo新闻公告列表
     */
    public function noticeList($params)
    {

        $notice_obj = new NoticeModel();

        $where = 'isuse = 1';

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $notice_obj->getNoticeNum($where);
        $notice_obj->setStart($firstRow);
        $notice_obj->setLimit($num_per_page);

        $fields = 'notice_id,addtime,title,path_img';

        $notice_list = $notice_obj->getNoticeList($fields, $where, 'serial asc,addtime desc');

        ApiModel::returnResult(0, array(
            'notice_list' => $notice_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *新闻详情
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'领取成功'，失败退出返回错误码
     * @todo新闻详情
     */
    public function noticeDetail($params)
    {
        $notice_obj = new NoticeModel();

        $where = 'isuse = 1 AND notice_id =' . $params['notice_id'];

        $notice_info = $notice_obj->getNoticeInfo($where, '');

        if (!$notice_info) {
            ApiModel::returnResult(1000, null, '新闻不存在');
        }
        $notice_info['content'] = $notice_info['description'];
        ApiModel::returnResult(0, array(
            'info' => $notice_info ?: array()
        ));

    }


    /**
     *兑换中心列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo兑换中心列表
     */
    public function giftCardList($params)
    {
        $gift_card_obj = new GiftCardModel();

        $where = 'isuse = 1';

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $gift_card_obj->getGiftCardNum($where);
        $gift_card_obj->setStart($firstRow);
        $gift_card_obj->setLimit($num_per_page);

        $gift_card_list = $gift_card_obj->getGiftCardList('gift_card_id,cash,money,name,img', $where, 'addtime desc');
        // dump($gift_card_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'gift_card_list' => $gift_card_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *兑换卡详情页
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回数据
     * @todo兑换卡详情页
     */
    public function giftCardInfo($params)
    {
        $gift_card_id = $params['gift_card_id'];
        $user_id = session('user_id');
        $gift_card_obj = new GiftCardModel();

        $where = 'isuse = 1 AND gift_card_id =' . $gift_card_id;

        $gift_card_info = $gift_card_obj->getGiftCardInfo($where, 'gift_card_id,cash,money,name,img');
        // dump($gift_card_obj->getLastsql());die;
        if (!$gift_card_info) {
            ApiModel::returnResult(0, array(
                'gift_card_info' => $gift_card_info ?: array()
            ));
        }

        //判断是否到达1倍流水,没达到需要2%d的手续费
        $account_obj = new AccountModel();
        $daily_win_obj = new DailyWinModel();
        $user_obj = new UserModel();
        $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
        $end_time = time();
        //总计充值
        $where = 'operater <> 1 AND user_id ='.$user_id.' AND change_type ='.AccountModel::RECHARGE.' AND state = 0';
        $total_recharge = $account_obj->field('sum(amount_in) as recharge')->where($where)->find()['recharge'] ? : 0;

        //除今日外有效流水
        $where = 'daily_flow <> 0 AND user_id ='.$user_id;
        $total_daily_flow = $daily_win_obj->getDailyWinInfo($where,'sum(daily_flow) AS total')['total'] ? : 0;

        //7日内充值
        $where = 'operater <> 1 AND user_id ='.$user_id.' AND change_type ='.AccountModel::RECHARGE.' AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND state = 0';
        $recharge = $account_obj->field('sum(amount_in) as recharge')->where($where)->find()['recharge'] ? : 0;

        //前6日有效流水
        $where = 'daily_flow <> 0 AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$user_id;
        $daily_flow_sum = $daily_win_obj->getDailyWinInfo($where,'sum(daily_flow) AS total')['total'] ? : 0;

        //计算今日有效流水
        $bet_log_obj = new BetLogModel();
        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('valid_flow,game_type_id','isuse = 1 AND valid_flow > 0');

        $start_time = strtotime(date('Y-m-d',time()));
        $end_time = time();

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
        $daily_flow = $total_bet_money;   //当天实时有效流水
        $is_suit = 1;
        //判断是否需要2%的手续费
        if(($daily_flow + $daily_flow_sum)/3 <= $recharge)
        {
           $is_suit = 0;
        }

        $account_obj = new AccountModel();
        $user_id = $this->user_id;

        $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
        $end_time = time();

//        //7日内充值
//        $change_type = AccountModel::RECHARGE;
//        $where = 'user_id =' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <=' . $end_time . ' AND change_type =' . $change_type;
//        $recharge = $account_obj->getAccountInfo($where, 'sum(amount_in) as recharge');


        //7日内已提
        $change_type = AccountModel::DEPOSIT;
        $where = 'user_id =' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <=' . $end_time . ' AND change_type =' . $change_type;
        $deposit = $account_obj->getAccountInfo($where, 'sum(amount_out) as deposit');


        //7日内流水
//        $change_type = AccountModel::BETTING;
//        $where = 'user_id =' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <=' . $end_time . ' AND change_type =' . $change_type;
//        $flow = $account_obj->getAccountInfo($where, 'sum(amount_out) as flow');

        $gift_card_info['recharge'] = $recharge ?: 0;
        $gift_card_info['deposit'] = $deposit['deposit'] ?: 0;
        $gift_card_info['flow'] = $daily_flow + $daily_flow_sum ?: 0;
        $gift_card_info['is_suit'] = $is_suit;
        //用户等级
        $user_obj = new UserModel();
        $user_level = $user_obj->where('user_id ='.$user_id)->getField('level_id');

        //等级兑换金豆列表
        $level_obj = new LevelModel();
        $level_list = $level_obj->getLevelList('level_id,level_name,exchange_rate', '');
        foreach ($level_list as $k => &$v) {
            $v['exchange_money'] = ($v['exchange_rate'] + 100) / 100 * $gift_card_info['money'];
            if($user_level == $v['level_id']){
                $money = $v['exchange_money'];
            }
        }
        unset($v);
        if($is_suit == 0)
        {
            $config_base_obj = new ConfigBaseModel();
            $flow_poundage = $config_base_obj->getConfig('poundage') ?: 0.02;   //手续费
            $money = $money + $gift_card_info['money'] * $flow_poundage;
        }
        $gift_card_info['money'] = $money;
        ApiModel::returnResult(0, array(
            'gift_card_info' => $gift_card_info ?: array(),
            'level_list' => $level_list ?: [],
        ));

    }


    /**
     *卡密兑换
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'领取成功'，失败退出返回错误码
     * @todo卡密兑换
     */
    public function exChangeCard($params)
    {

        $user_id = $this->user_id;

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $card_str = 'card_'.$this->user_id;
        if ($redis_obj->setnx($card_str, 1)) {
            $redis_obj->expire($card_str, 3);
        } else {
            ApiModel::returnResult(2000,null,'该卡券兑换中');
        }

        $gift_card_obj = new GiftCardModel();

        $where = 'isuse = 1 AND gift_card_id =' . $params['gift_card_id'];

        $gift_card_info = $gift_card_obj->getGiftCardInfo($where, '');

        if($params['number'] <= 0)
        {
            ApiModel::returnResult(2000, null, '卡密数量必须大于0');
        }
        if (!$gift_card_info) {
            ApiModel::returnResult(2000, null, '兑换卡不存在');
        }

        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('safe_password,left_money,mobile,level_id', 'user_id = ' . $user_id);

        if ($params['safe_password'] != $user_info['safe_password']) {
            ApiModel::returnResult(2000, null, '安全密码错误');
        }

        $amount = $params['number'] * $gift_card_info['money'];

        if ($amount > $user_info['left_money']) {
            ApiModel::returnResult(2000, null, '金豆不足');
        }

        $verify_code_obj = new VerifyCodeModel();

        if (!$verify_code_obj->checkVerifyCodeValid($params['verify_code'], $user_info['mobile'])) {
            ApiModel::returnResult(40051, null, '无效的验证码');
        }
        //判断是否到达1倍流水,没达到需要2%d的手续费
        $account_obj = new AccountModel();
        $daily_win_obj = new DailyWinModel();
        $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
        $end_time = time();
        //总计充值
        $where = 'operater <> 1 AND user_id ='.$user_id.' AND change_type ='.AccountModel::RECHARGE.' AND state = 0';
        $total_recharge = $account_obj->field('sum(amount_in) as recharge')->where($where)->find()['recharge'] ? : 0;

        //除今日外有效流水
        $where = 'daily_flow <> 0 AND user_id ='.$user_id;
        $total_daily_flow = $daily_win_obj->getDailyWinInfo($where,'sum(daily_flow) AS total')['total'] ? : 0;

        //7日内充值
        $where = 'operater <> 1 AND user_id ='.$user_id.' AND change_type ='.AccountModel::RECHARGE.' AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND state = 0';
        $recharge = $account_obj->field('sum(amount_in) as recharge')->where($where)->find()['recharge'] ? : 0;

        //前6日有效流水
        $where = 'daily_flow <> 0 AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$user_id;
        $daily_flow_sum = $daily_win_obj->getDailyWinInfo($where,'sum(daily_flow) AS total')['total'] ? : 0;

        //计算今日有效流水
        $bet_log_obj = new BetLogModel();
        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('valid_flow,game_type_id','isuse = 1 AND valid_flow > 0');

        $start_time = strtotime(date('Y-m-d',time()));
        $end_time = time();

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
        $daily_flow = $total_bet_money;   //当天实时有效流水

        //判断是否需要2%的手续费
        $flow_poundage = 0;
        log_file('total_daily_flow='.$total_daily_flow.'----daily_flow='.$daily_flow.'-----total_recharge='.$total_recharge.'-----daily_flow_sum='.$daily_flow_sum.'-----recharge='.$recharge,'exChangeCard',true);
        if(($daily_flow + $daily_flow_sum)/3 <= $recharge)
        {
            $config_base_obj = new ConfigBaseModel();
            $flow_poundage = $config_base_obj->getConfig('poundage') ?: 0.02;   //手续费
            $flow_poundage = $flow_poundage * 100;
        }


        $level_obj = new LevelModel();
        $poundage = $level_obj->getLevelField('level_id =' . $user_info['level_id'], 'exchange_rate');
        $amount = (100 + $poundage + $flow_poundage) / 100 * $amount;

        $account_obj = new AccountModel();
        $res = $account_obj->addAccount($user_id, AccountModel::EXCHANGECARD, $amount * -1, '兑换点卡');

        if ($res != -1) {
            //生成卡密
            $user_gift_obj = new UserGiftModel();
            $user_gift_data = [
                'gift_card_id' => $params['gift_card_id'],
                'addtime' => time(),
                'user_id' => $user_id,
                'number' => $params['number']
            ];

            $user_gift_id = $user_gift_obj->addUserGift($user_gift_data);

            if (!$user_gift_id) {
                ApiModel::returnResult(1000, null, '兑换失败');
            }

            //生成卡密
            $user_gift_password_obj = new UserGiftPasswordModel();
            $user_gift_passwor_id = $user_gift_password_obj->CreatePassword($user_gift_id, $params['number'], $user_id,$params['gift_card_id']);

            if (!$user_gift_passwor_id) {
                ApiModel::returnResult(1000, null, '兑换失败');
            }
            return '兑换成功';
        } else {
            ApiModel::returnResult(1000, null, '金豆不足,兑换失败');
        }
    }

    /**
     *游戏排行榜
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo游戏排行榜
     */
    public function rankList()
    {

        $user_id = $this->user_id;

        $rank_list_obj = new RankListModel();

        $rank_list_obj->setLimit(20);

        $start_time = strtotime(date('Y-m-d', time()));
        $end_time = strtotime(date('Y-m-d', time())) + 24 * 3600;

        $where = 'addtime >=' . $start_time . ' AND addtime <' . $end_time;

        $yesterday_list = $rank_list_obj->getRankListList('nickname,win AS total,user_id,reward', $where, 'total desc');
        $yesterday_list = $rank_list_obj->getListData($yesterday_list);

        $start_time = strtotime(date('Y-m-d'));
        $end_time = time();

        $where = 'addtime >=' . $start_time . ' AND addtime <=' . $end_time;
//        $rank_list_obj->setLimit(20);
//        $today_list = $rank_list_obj->getRankListList('win AS total,nickname,reward,user_id', $where, 'reward desc');
//        $today_list = $rank_list_obj->getListData($today_list);
//        $where .= ' AND is_open = 1 AND total_after_money>total_bet_money';
        $where .= ' AND is_open = 1 ';
        $bet_log_obj = new BetLogModel();
        $bet_log_obj->setLimit(20);
        $today_list = $bet_log_obj->getBetLogList('user_id,SUM(total_after_money-total_bet_money)AS total', $where, 'total desc', 'user_id');
        //机器人列表
        $robot_obj = new RobotModel();
        $robot_list = $robot_obj->where('today_money > 0')->select();
        $today_list = $rank_list_obj->getTodayData($today_list,$robot_list);


        $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
        $end_time = time();

        $where = 'addtime >=' . $start_time . ' AND addtime <=' . $end_time ;
        $rank_list_obj->setLimit(20);
        $sevenday_list = $rank_list_obj->getRankListList('nickname,sum(reward) as reward,user_id,sum(win) AS total', $where, 'total desc','nickname');
//        dump($rank_list_obj->getLastSql());
        $sevenday_list = $rank_list_obj->getListData($sevenday_list);
        // dump($rank_list_obj->getLastsql());
        // dump($sevenday_list);
        ApiModel::returnResult(0, array(
            'yesterday_list' => $yesterday_list ?: array(),
            'today_list' => $today_list ?: array(),
            'sevenday_list' => $sevenday_list ?: array()
        ));

    }

    /**
     *获取推广邀请链接
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo获取推广邀请链接
     */
    public function getInviteUrl($params)
    {
        $user_id = $this->user_id;

        if (!$user_id) {
            ApiModel::returnResult(1000, null, '请先登陆');
        }

        $user_obj = new UserModel($user_id);
        $id = $user_obj->where('user_id = ' . $user_id)->getField('id');
        $url = $params['url'];

        $url .= '?id=' . $id;

        ApiModel::returnResult(0, array(
            'url' => $url ?: ''
        ));
    }

    /**
     *商务合作列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo商务合作列表
     */
    public function AgentList($params)
    {
        $user_obj = new UserModel();

        $where = 'is_enable = 1 AND role_type =' . UserModel::AGENT.' AND is_index = 1';

//        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

//        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
//        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $user_obj->getUserNum($where);
//        $user_obj->setStart($firstRow);
//        $user_obj->setLimit($num_per_page);

        $user_list = $user_obj->field('qq,introduce,game_name')->where($where)->order('reg_time desc')->select();
        shuffle($user_list);
        // dump($user_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'user_list' => $user_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *推广记录列表
     * @author yzp
     * @param array $params 参数列表SS
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo推广记录列表
     */
    public function recommendList($params)
    {

        $user_id = $this->user_id;
        $user_obj = new UserModel();

        $where = 'parent_id =' . $user_id;

        $invite_log_obj = new InviteLogModel();

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $invite_log_obj->getInviteLogNum($where);
        $invite_log_obj->setStart($firstRow);
        $invite_log_obj->setLimit($num_per_page);

        $invite_log_list = $invite_log_obj->getInviteLogList('', $where, 'addtime desc');
        $invite_log_list = $invite_log_obj->getListData($invite_log_list);
        // dump($invite_log_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'invite_log_list' => $invite_log_list ?: array(),
            'total' => $total ?: 0
        ));

    }


    /**
     *领取红包
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'发送成功'，失败退出返回错误码
     * @todo领取红包
     */
    public function getRedPacket($params)
    {

        $user_id = $this->user_id;

        if (!$user_id) {
            ApiModel::returnResult(1000, null, '请先登陆');
        }

        usleep(rand(1000,10000));
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));

//        $red_packet_id = 'getRedPacket_'.$params['red_packet_id'];
//        if ($redis_obj->setnx($red_packet_id, 1)) {
//            $redis_obj->expire($red_packet_id, 3);
//        } else {
//            ApiModel::returnResult(2000,null,'请稍后领取');
//        }

        $str = 'getRedPacket_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'领取中');
        }
        $old_red_packet_id = $params['red_packet_id'];
        $params['red_packet_id'] = rtrim($params['red_packet_id'],'%3D');
        $params['red_packet_id'] = rtrim($params['red_packet_id'],'=');
        // $red_packet_id = url_jiemi($params['red_packet_id']);
        $red_packet_id = url_jiemi($params['red_packet_id']);

        $red_str = 'red_'.$red_packet_id;
        if(!$redis_obj->exists($red_str)){
            ApiModel::returnResult(1000, null, '红包不存在');
        }else{
            if($redis_obj->get($red_str) <= 0){
                ApiModel::returnResult(1000, null, '红包已领完');
            }
        }

//        log_file('$red_packet_id ='.$red_packet_id,'red_pack',true);
//        log_file('$params ='.$params['red_packet_id'],'red_pack',true);
        $red_packet_obj = new RedPacketModel($red_packet_id);

        if (!intval($red_packet_id)) {
            ApiModel::returnResult(1000, null, '红包不存在');
        }

        $config_base_obj = new ConfigBaseModel();
        $red_limit_money = $config_base_obj->getConfig('red_limit_money') ?: 1;   //红包最小金额

        $where = 'isuse = 1 AND red_packet_id =' . $red_packet_id;

        $red_packet_info = $red_packet_obj->getRedPacketInfo($where, '');

        if (!$red_packet_info) {
            ApiModel::returnResult(1000, null, '红包不存在');
        }

        $red_packet_log_obj = new RedPacketLogModel();
        $red_packet_log_info = $red_packet_log_obj->getRedPacketLogInfo('user_id =' . $user_id . ' AND red_packet_id = ' . $red_packet_id, 'addtime');

        if ($red_packet_log_info) {
            ApiModel::returnResult(1000, null, '已经领取过该红包');
        }

        if ($red_packet_info['is_cancel'] == 1) {
            ApiModel::returnResult(1000, null, '红包发起人已撤销');
        }

        if ($red_packet_info['expire_time'] < time()) {
            ApiModel::returnResult(1000, null, '红包已过期');
        }

        if ($red_packet_info['residue_num'] <= 0) {
            ApiModel::returnResult(1000, null, '红包已领完');
        }

        $red_str = 'red_'.$red_packet_id;
        if($redis_obj->get($red_str) <= 0){
            ApiModel::returnResult(1000, null, '红包已领完');
        }else{
            if($redis_obj->Decr($red_str) < 0){
                ApiModel::returnResult(1000, null, '红包已领完');
            }
        }

        if ($red_packet_info['type'] == self::RAND) {
            //如果是最后一个领取红包，或者只有一个红包
            if ($red_packet_info['residue_num'] == 1) {
                $get_money = $red_packet_info['residue_money'];
            } else {

                $rand_money = $red_packet_info['residue_money'] - $red_limit_money * $red_packet_info['residue_num'];

                $get_money = $red_limit_money + mt_rand(0, $rand_money); //每个人至少有配置项配置的乐豆
            }

        } else {

            $get_money = $red_packet_info['each_money'];
        }
        if($get_money <= 0)
        {
            ApiModel::returnResult(1000, null, '红包已领完');
        }

        $red_packet_log_obj = new RedPacketLogModel();

        $log_data = [
            'red_packet_id' => $red_packet_id,
            'money' => $get_money,
            'user_id' => $user_id
        ];
        $red_packet_log_id = $red_packet_log_obj->addRedPacketLog($log_data);

        if (!$red_packet_log_id) {
            ApiModel::returnResult(1000, null, '红包领取失败');
        }

        $data['residue_money'] = $red_packet_info['residue_money'] - $get_money;
        $data['residue_num'] = $red_packet_info['residue_num'] - 1;

        $red_packet_id = $red_packet_obj->editRedPacket($where, $data);

        $account_obj = new AccountModel();

        $account_obj->addAccount($user_id, AccountModel::GETREDPACKET, $get_money, '领红包');

        ApiModel::returnResult(0, array(
            'money' => feeHandle($get_money)
        ));
    }

    /**
     *红包详情
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo红包详情
     */
    public function getRedPacketInfo($params)
    {
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $user_id = $this->user_id;
        $params['red_packet_id'] = rtrim($params['red_packet_id'],'=');
        $params['red_packet_id'] = rtrim($params['red_packet_id'],'%3D');
         $red_packet_id = url_jiemi($params['red_packet_id']);

        $red_str = 'red_'.$red_packet_id;
        log_file($red_str,'red_log');
        if($redis_obj->get($red_str) <= 0){
            ApiModel::returnResult(400, null, '红包已抢完');
        }

//        $red_packet_id = $params['red_packet_id'];
        log_file($red_packet_id,'red_log');
        $red_packet_obj = new RedPacketModel($red_packet_id);

        if (!intval($red_packet_id)) {
            ApiModel::returnResult(1000, null, '红包不存在');
        }

        $where = 'isuse = 1 AND red_packet_id =' . $red_packet_id;

        $red_packet_info = $red_packet_obj->getRedPacketInfo($where, '');

        if (!$red_packet_info) {
            ApiModel::returnResult(1000, null, '红包不存在');
        }

        if ($red_packet_info['residue_num'] <= 0) {
            ApiModel::returnResult(400, null, '红包已抢完');
        }

        $red_packet_info['each_money'] = feeHandle($red_packet_info['each_money']);
        if($red_packet_info['type'] == 2)
        {
//            $each_money = intval($red_packet_info['total_money']/$red_packet_info['num']) ;
//            $red_packet_info['each_money'] = feeHandle($each_money) ? : 0;
            $red_packet_info['each_money'] = '拼手气';
        }
        ApiModel::returnResult(0, array(
            'red_packet_info' => $red_packet_info ?: array()
        ));

    }

    /**
     *游戏系列列表
     * @author yzp
     * @param array $params 参数列表SS
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo游戏系列列表
     */
    public function gameSeriesList($params)
    {

        $user_id = $this->user_id;
        $game_series_obj = new GameSeriesModel();

        $where = 'isuse = 1';

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $game_series_obj->getGameSeriesNum($where);
        $game_series_obj->setStart($firstRow);
        $game_series_obj->setLimit($num_per_page);

        $game_series_list = $game_series_obj->getGameSeriesList('', $where, '');
        // dump($game_series_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'game_series_list' => $game_series_list ?: array(),
            'total' => $total ?: 0
        ));

    }


    /**
     *游戏类型列表
     * @author yzp
     * @param array $params 参数列表SS
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo游戏类型列表
     */
    public function gameTypeList($params)
    {

        $user_id = $this->user_id;

        $game_series_id = $params['game_series_id'];

        $game_type_obj = new GameTypeModel();

        $where = 'isuse = 1 AND game_series_id =' . $game_series_id;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page =  20;

        $total = $game_type_obj->getGameTypeNum($where);
        $game_type_obj->setStart($firstRow);
        $game_type_obj->setLimit($num_per_page);

        $game_type_list = $game_type_obj->getGameTypeList('game_type_id,game_type_name', $where, '');
        // dump($game_type_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'game_type_list' => $game_type_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    //活动测试使用
    public function test()
    {
        $test_obj = new MarketingRuleModel();
        $test_list = $test_obj->getBetting();

    }

    /**
     *特殊文章详情
     * @author yzp
     * @param array $params 参数列表SS
     * @return 成功返回array
     */
    public function getArticleInfo($params)
    {
        $article_obj = new ArticleModel();

        $article_tag = $params['article_tag'];

        $where = 'isuse = 1 AND article_tag = "' . $article_tag . '"';
        $article_info = $article_obj->getArticleInfo($where, 'description');
        $article_info['description'] = htmlspecialchars_decode($article_info['description']);

        ApiModel::returnResult(0, $article_info ?: array());
    }

    /**
     *活动详情
     * @author yzp
     * @param array $params 参数列表SS
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo活动详情
     */
    public function getActivityInfo($params)
    {
        $marketing_rule_obj = new MarketingRuleModel();

        $marketing_rule_id = $params['marketing_rule_id'];

        $where = 'isuse = 1 AND marketing_rule_id =' . $marketing_rule_id;

        $marketing_rule_info = $marketing_rule_obj->getMarketingRuleInfo($where, 'marketing_rule_name,contents');
        $marketing_rule_info['contents'] = htmlspecialchars_decode($marketing_rule_info['contents']);

        ApiModel::returnResult(0, $marketing_rule_info ?: array());
    }


    /**
     * 获取官方qq
     * @date: 2019/4/28
     * @author: hui
     * @return array
     */
    public function getSysqq()
    {
        return array(
            'sys_qq' => explode(',',$GLOBALS['config_info']['SYS_QQ']),
            'sys_qq_group' => explode(',',$GLOBALS['config_info']['SYS_QQ_GROUP']),
            'sys_qq_key' => $GLOBALS['config_info']['SYS_QQ_KEY'],
        );
    }

    /**
     * 获取七牛token
     * @date: 2019/4/28
     * @author: hui
     * @return array
     */
    public function getQiniuToken()
    {
        return array(
            'token' => get_qiniu_uploader_up_token(),
            'image_domain' => C('QINIU_UPLOAD_DRIVER_CONFIG.QINIU_IMAGES_DOMAIN'),
        );
    }

    public function getPushLogList($params)
    {
        $user_id = $this->user_id;
        $push_log_obj = new PushLogModel();

        $where = 'user_id =' . $user_id;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $push_log_obj->getPushLogNum($where);
        $push_log_obj->setStart($firstRow);
        $push_log_obj->setLimit($num_per_page);

        $push_log_list = $push_log_obj->getPushLogList('', $where, 'addtime DESC');
        // dump($game_type_obj->getLastsql());die;
        return array(
            'push_log_list' => $push_log_list ?: [],
            'total' => $total ?: 0,
        );
    }

    /**
     * 获取参数列表
     * @author 姜伟
     * @param
     * @return 参数列表
     * @todo 获取参数列表
     */
    function getParams($func_name)
    {
        $params = array(
            'getSysqq' => array(),
            'getQiniuToken' => array(),
            'getActivityInfo' => array(
                array(
                    'field' => 'marketing_rule_id',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                )
            ),
            'getArticleInfo' => array(
                array(
                    'field' => 'article_tag',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                )
            ),
            'gameTypeList' => array(
                array(
                    'field' => 'game_series_id',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'getPushLogList' => array(
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'custFlashList' => array(
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'type',
                    'required' => false,
                ),
            ),
            'gameSeriesList' => array(
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'AgentList' => array(
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'activityList' => array(
                array(
                    'field' => 'type',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'noticeList' => array(
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'giftCardList' => array(
                array(
                    'field' => 'firstRow',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'fetchNum',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'exChangeCard' => array(
                array(
                    'field' => 'safe_password',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'number',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'verify_code',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'gift_card_id',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'getInviteUrl' => array(
                array(
                    'field' => 'url',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                )
            ),
            'loginAreaSwitch' => array(
                array(
                    'field' => 'open_login_limit',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'login_limit_province_id',
                    'type' => 'string',
                    'required' => false,
                ),
                array(
                    'field' => 'login_limit_city_id',
                    'type' => 'string',
                    'required' => false,
                ),
                array(
                    'field' => 'another_limit_province_id',
                    'type' => 'string',
                    'required' => false,
                ),
                array(
                    'field' => 'another_limit_city_id',
                    'type' => 'string',
                    'required' => false,
                ),
            ),
            'noticeDetail' => array(
                array(
                    'field' => 'notice_id',
                    'type' => 'string',
                ),
            ),
            'getRedPacket' => array(
                array(
                    'field' => 'red_packet_id',
                    'type' => 'string',
                ),
            ),
            'getRedPacketInfo' => array(
                array(
                    'field' => 'red_packet_id',
                    'type' => 'string',
                ),
            ),
            'giftCardInfo' => array(
                array(
                    'field' => 'gift_card_id',
                    'type' => 'string',
                ),
            ),
            'login' => array(

                array(
                    'field' => 'mobile',
                    'min_len' => 6,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40004,
                    'miss_code' => 41004,
                    'empty_code' => 44004,
                    'type_code' => 45004,
                    //'func'		=> 'checkUserName',
                    'func_code' => 47004,
                ),
                array(
                    'field' => 'password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'code',
                    'type' => 'string',
                ),
            ),
            'signin' => array(
                array(
                    'field' => 'mobile',
                    'required' => true,
                    'miss_code' => 41006,
                    'func' => 'checkMobile',
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'verify_code',
                    'min_len' => 6,
                    'max_len' => 6,
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
                array(
                    'field' => 'password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
            ),
            'register' => array(
//				array(
//					'field'		=> 'username',
//					'min_len'	=> 6,
//					'max_len'	=> 32,
//					'type'		=> 'string',
//					'required'	=> true,
//					'len_code'	=> 40004,
//					'miss_code'	=> 41004,
//					'empty_code'=> 44004,
//					'type_code'	=> 45004,
//					//'func'		=> 'checkUserName',
//					'func_code'	=> 47004,
//				),
                array(
                    'field' => 'username',
                    'min_len' => 6,
                    'max_len' => 32,
                    'type' => 'string',
                    //'required'	=> true,
                    'len_code' => 40004,
                    'miss_code' => 41004,
                    'empty_code' => 44004,
                    'type_code' => 45004,
                    //'func'		=> 'checkUserName',
                    'func_code' => 47004,
                ),
                array(
                    'field' => 'password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'mobile',
                    'required' => true,
                    'miss_code' => 41006,
                    'func' => 'checkMobile',
                    'func_code' => 47006,
                ),
//				array(
//					'field'		=> 'nickname',
//					'min_len'	=> 1,
//					'max_len'	=> 16,
//					'type'		=> 'string',
//					'required'	=> true,
//					'len_code'	=> 40007,
//					'miss_code'	=> 41007,
//					'empty_code'=> 44007,
//					'type_code'	=> 45007,
//				),
                array(
                    'field' => 'verify_code',
                    'min_len' => 6,
                    'max_len' => 6,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
            ),
            'signup' => array(
                array(
                    'field' => 'mobile',
                    'required' => true,
                    'miss_code' => 41006,
                    'func' => 'checkMobile',
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'verify_code',
                    'min_len' => 6,
                    'max_len' => 6,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
                array(
                    'field' => 'password',
                    'required' => true,
                    'miss_code' => 41006,
                ),
                array(
                    'field' => 'parent_id',
                ),
            ),
            'editUserInfo' => array(
                array(
                    'field' => 'nickname',
                    'min_len' => 1,
                    'max_len' => 20,
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40007,
                    'miss_code' => 41007,
                    'type_code' => 45007,
                ),
                array(
                    'field' => 'qq',
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40003,
                    'miss_code' => 41003,
                    'type_code' => 45003,
                ),
                array(
                    'field' => 'wx_account',
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40013,
                    'miss_code' => 41013,
                    'type_code' => 45013,
                ),
                array(
                    'field' => 'headimgurl',
                    'type' => 'string',
                    'required' => false,
                    'miss_code' => 41014,
                    'empty_code' => 44014,
                    'type_code' => 45014,
                ),
                array(
                    'field' => 'alipay_account_name',
                    'type' => 'string',
                    'required' => false,
                    'miss_code' => 41014,
                    'empty_code' => 44014,
                    'type_code' => 45014,
                )

            ),
            'checkPassword' => array(
                array(
                    'field' => 'safe_password',
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40007,
                    'miss_code' => 41007,
                    'type_code' => 45007,
                ),

            ),
            'checkMobileRegistered' => array(
                array(
                    'field' => 'mobile',
                    'required' => true,
                    'miss_code' => 41006,
                    'func' => 'checkMobile',
                    'func_code' => 47006,
                ),
            ),
            'sendVerifyCode' => array(
                array(
                    'field' => 'mobile',
                    'required' => true,
                    'miss_code' => 41006,
                    'func' => 'checkMobile',
                    'func_code' => 47006,
                ),
            ),
            'editPassword' => array(
                array(
                    'field' => 'password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'new_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 're_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'type',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
            'setPassword' => array(
                array(
                    'field' => 'old_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'new_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
            ),
            'resetPassword' => array(
                array(
                    'field' => 'new_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 're_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'mobile',
                    'type' => 'string',
                    'required' => true,
                ),
            ),
            'findPassword' => array(
                array(
                    'field' => 'mobile',
                    'type' => 'string',
                ),
                array(
                    'field' => 'verify_code',
                    'type' => 'string',
                ),
                array(
                    'field' => 'code',
                    'type' => 'string',
                ),
            ),

            // findPasswordBySms
            'findPasswordBySms' => array(
                array(
                    'field' => 'mobile',
                    'required' => true,
                    'miss_code' => 41006,
                    'func' => 'checkMobile',
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'new_password',
                    'min_len' => 32,
                    'max_len' => 32,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40005,
                    'miss_code' => 41005,
                    'empty_code' => 44005,
                    'type_code' => 45005,
                ),
                array(
                    'field' => 'verify_code',
                    'min_len' => 6,
                    'max_len' => 6,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
            ),
            'checkVerifyCodeValid' => array(
                array(
                    'field' => 'verify_code',
                    'min_len' => 6,
                    'max_len' => 6,
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
            ),
            'getShareUrl' => array(
                array(
                    'field' => 'model',
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
                array(
                    'field' => 'action',
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
                array(
                    'field' => 'type',
                    'type' => 'string',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
                array(
                    'field' => 'type_id',
                    'type' => 'int',
                    'required' => true,
                    'len_code' => 40026,
                    'miss_code' => 41051,
                    'empty_code' => 44051,
                    'type_code' => 45051,
                ),
            ),
            'androidUpdate' => array(
                array(
                    'field' => 'version',
                ),
            ),
            'frontUpdate' => array(
                array(
                    'field' => 'version',
                ),
            ),
        );

        return $params[$func_name];
    }
}

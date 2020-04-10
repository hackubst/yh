<?php

class UserApiModel extends ApiModel
{

    private $user_id;

    public function __construct()
    {
        $this->user_id = intval(session('user_id'));
//         $this->user_id = 62290;
    }

    CONST PASSWORD = 1; //登陆密码
    CONST SAFE_PASSWORD = 2; //安全密码
    CONST BANK_PASSWORD = 3; //银行密码

    CONST WEEK = 1; //7天
    CONST MONTH = 2; //30天
    CONST HALFYEAR = 3; //半年
    CONST YEAR = 4; //一年

    CONST SAVE = 1;//存入银行

    /**
     * 获取用户信息主页资料
     * @author yzp
     * @param array $params
     * @return 成功返回$user_info，失败返回错误码
     */
    public function getUserHomeInfo()
    {
        //获取用户基本信息
        $where = 'user_id = ' . $this->user_id;
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo(
            'user_id, nickname, headimgurl, mobile, email,left_money,frozen_money,exp,level_id',
            $where
        );

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }
        //获取今日充值 今日盈亏 今日流水

        $level_obj = new LevelModel();
        $level_info = $level_obj->getLevelInfo('level_id =' . $user_info['level_id'], 'level_name');

        $user_info['level_name'] = $level_info['level_name'] ?: '';

        $account_info = $user_obj->getDayAccountInfo();
        //获取当前登陆信息
        $login_log_obj = new LoginLogModel();
        $login_log_info = $login_log_obj->field('login_time,ip')->where($where . ' AND status = 1')->order('login_time desc')->find();
        if($login_log_info && $login_log_info['login_time'])
        {
            $login_log_info['login_time_str'] = date('Y-m-d',$login_log_info['login_time']);
        }
        $user_info['login_log_info'] = $login_log_info ?: array();
        $user_info['account_info'] = $account_info ?: array();

        return $user_info ?: array();
    }

    /**
     * 获取用户信息
     * @author yzp
     * @param array $params
     * @return 成功返回$user_info，失败返回错误码
     * @todo 获取用户基本信息接口
     */
    public function getUserInfo()
    {
        //获取用户基本信息
        $where = 'user_id = ' . $this->user_id;
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo(
            'user_id, nickname, headimgurl, mobile, email, alipay_account_name,wx_account,qq,open_chenck_login,open_login_limit,open_chenck_personal,
            login_limit_province_id,login_limit_city_id,another_limit_province_id,another_limit_city_id,user_id,left_money,frozen_money,level_id,exp,more_exp,id',
            $where
        );

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        $login_log_obj = new LoginLogModel();
        $login_log_info = $login_log_obj->field('ip_address')->where($where)->order('login_time desc')->find();

        $level_obj = new LevelModel();
        $level_info = $level_obj->getLevelInfo('level_id =' . $user_info['level_id'], 'level_name');

        $max_exp = $level_obj->getLevelInfo('level_id = 8', 'min_exp')['min_exp'];
        $user_info['level_name'] = $level_info['level_name'] ?: '';

        //获取剩余工资
        $left_win_loss = $user_obj->getLeftWinLoss($this->user_id);
        $user_info['win_loss_left'] = $left_win_loss['left'] ? : 0;

        if ($user_info['exp'] - $max_exp > 0) {
            $user_info['more_exp'] = $user_info['exp'] - $max_exp;
        } else {
            $user_info['more_exp'] = 0;
        }

        $user_info['ip_address'] = $login_log_info['ip_address'] ?: '';

        return $user_info ?: array();
    }

    //验证安全密码
    //yzp
    public function checkPassword($params)
    {
        $user_id = $this->user_id;
        $user_obj = new UserModel();
        $user_obj->userId = $user_id;

        $user_info = $user_obj->getUserInfo('safe_password', 'user_id = ' . $user_id);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        if ($user_info['safe_password'] != $params['safe_password']) {
            ApiModel::returnResult(1000, null, '安全密码错误');
        }

        return '验证成功';
    }


    /**
     * 修改用户信息
     * @author yzp
     * @param array $params
     * @return 成功返回$user_info，失败返回错误码
     * @todo 修改用户基本信息接口
     */
    public function editUserInfo($params)
    {
        if (empty($params)) {
            return '没有任何修改';
        }

        $user_id = $this->user_id;
        $user_obj = new UserModel();
        $user_obj->userId = $user_id;
        $info = $user_obj->editUserInfo($params);
        if ($info) {
            ApiModel::returnResult(0, '修改成功');
        } else {
            ApiModel::returnResult(0, '修改失败');
        }
    }


    /**
     * 修改登陆密码 | 安全密码
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 修改密码
     */
    function editPassword($params)
    {
        //判断账号密码合法性
        $type = $params['type'];

        $password = $params['password'];

        $user_id = $this->user_id;
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('safe_password,password,bank_password', 'user_id =' . $user_id);
        if (!$user_info) {
            ApiModel::returnResult(42005, null, '用户不存在');
        }
        if ($type == self::PASSWORD) {
            $old_password = $user_info['password'];
        } elseif ($type == self::SAFE_PASSWORD) {
            $old_password = $user_info['safe_password'];
        } elseif ($type == self::BANK_PASSWORD) {
            $old_password = $user_info['bank_password'];
        }

        if ($old_password != $password) {
            ApiModel::returnResult(42005, null, '原密码错误');
        }

        if ($params['new_password'] != $params['re_password']) {
            ApiModel::returnResult(42005, null, '两次密码输入不一致');
        }

        if ($type == self::PASSWORD) {
            $arr = array(
                'password' => $params['new_password']
            );
        } elseif ($type == self::SAFE_PASSWORD) {
            $arr = array(
                'safe_password' => $params['new_password']
            );
        } elseif ($type == self::BANK_PASSWORD) {
            $arr = array(
                'bank_password' => $params['new_password']
            );
        }

        $success = $user_obj->where('user_id =' . $user_id)->save($arr);
        return '修改成功';
    }

    /**
     * 开启/关闭登录验证
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 开启/关闭登录验证
     */
    public function loginSwitch($params)
    {
        $user_id = $this->user_id;
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('open_chenck_login', 'user_id =' . $user_id);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        $switch = $params['switch'];

        if ($user_info['open_chenck_login'] == $switch) {
            ApiModel::returnResult(0, '未修改');
        }

        $info = $user_obj->editUserInfo(array('open_chenck_login' => $switch));

        if ($info) {
            ApiModel::returnResult(0, '修改成功');
        } else {
            ApiModel::returnResult(0, '修改失败');
        }
    }

    /**
     * 开启/关闭用户中心验证
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 开启/关闭用户中心验证
     */
    public function personSwitch($params)
    {
        $user_id = $this->user_id;
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('open_chenck_personal', 'user_id =' . $user_id);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        $switch = $params['switch'];

        if ($user_info['open_chenck_personal'] == $switch) {
            ApiModel::returnResult(0, '未修改');
        }

        $info = $user_obj->editUserInfo(array('open_chenck_personal' => $switch));

        if ($info) {
            ApiModel::returnResult(0, '修改成功');
        } else {
            ApiModel::returnResult(0, '修改失败');
        }

    }

    /**
     * 开启/关闭登录地区验证
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 开启/关闭登录地区验证
     */
    public function loginAreaSwitch($params)
    {
        $user_id = $this->user_id;
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('open_login_limit', 'user_id =' . $user_id);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        $info = $user_obj->editUserInfo($params);

        if ($info) {
            ApiModel::returnResult(0, '修改成功');
        } else {
            ApiModel::returnResult(0, '修改失败');
        }

    }

    /**
     * 登录记录列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 登录记录列表
     */
    public function loginLog($params)
    {
        $user_id = $this->user_id;
        $login_log_obj = new LoginLogModel();

        $type = $params['type'];

        switch ($type) {
            case self::WEEK :
                $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
                $end_time = time();
                break;
            case self::MONTH :
                $start_time = strtotime(date('Y-m-d', strtotime('-30 days')));
                $end_time = time();
                break;
            case self::HALFYEAR :
                $start_time = strtotime("-0 year -6 month -0 day");
                $end_time = time();
                break;
            case self::YEAR :
                $start_time = strtotime("-1 year -0 month -0 day");
                $end_time = time();
                break;
            default:
                # code...
                break;
        }

        $where = 'user_id = ' . $user_id . ' AND login_time >=' . $start_time . ' AND login_time <=' . $end_time;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $login_log_obj->getLoginLogNum($where);
        $login_log_obj->setStart($firstRow);
        $login_log_obj->setLimit($num_per_page);

        $login_log_list = $login_log_obj->getLoginLogList('', $where, 'login_time desc');

        ApiModel::returnResult(0, array(
            'login_log_list' => $login_log_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *等级列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo等级列表
     */
    public function levelList($params)
    {

        $level_obj = new LevelModel();

        $where = '';

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $level_obj->getLevelNum($where);
        $level_obj->setStart($firstRow);
        $level_obj->setLimit($num_per_page);

        $level_list = $level_obj->getLevelList('', $where, 'sign_reward asc');

        ApiModel::returnResult(0, array(
            'level_list' => $level_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *领取救济
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'领取成功'，失败退出返回错误码
     * @todo领取救济
     */
    public function getRelief()
    {
        $user_id = $this->user_id;

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'getRelief_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }

        $where = 'user_id = ' . $user_id;

        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('user_id, exp,left_money,frozen_money', $where);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        //判断是否有活动
        $marketing_rule_obj = new MarketingRuleModel();

        $res = $marketing_rule_obj->checkRule(MarketingRuleModel::RELIEF);

        if (!$res) {
            ApiModel::returnResult(2000, null, '暂无活动,或者活动已过期');
        }

        //判断今日是否已领取救济金
        $account_obj = new AccountModel();
        $account_info = $account_obj->getDayReliveInfo(' AND user_id =' . $user_id, AccountModel::RELIEF);

        if ($account_info) {
            ApiModel::returnResult(2000, null, '今日已领取');
        }

        $hour_time_age = strtotime('-1 hour');
        $bet_log_obj = new BetLogModel();
        $bet_where = 'is_open = 0 AND user_id ='.$user_id.' AND addtime >='.$hour_time_age;

        $has_opening = $bet_log_obj->getBetLogInfo($bet_where);

        if($has_opening)
        {
            ApiModel::returnResult(2003, null, '当前有待开奖投注，不可领取救济金');
        }
        if (($user_info['left_money'] + $user_info['frozen_money']) > 1000)
        {
            ApiModel::returnResult(2000, null, '金豆超过1000，不可领取救济金');
        }

        $exp = $user_info['exp'] ?: 0;

        $level_obj = new LevelModel();

        $level_info = $level_obj->field('sign_reward')->where('max_exp >=' . $exp)->order('sign_reward asc')->find();
        // dump($level_obj->getLastsql());die;
        if (!$level_info) {
            ApiModel::returnResult(1000, null, '等级救济金不存在');
        }
        //领取救济金金额
        $sign_reward = $level_info['sign_reward'] ?: 0;

        if ($sign_reward == 0) {
            ApiModel::returnResult(1000, null, '没有可领取的救济金');
        }
        $account_obj = new AccountModel();
        $left = $account_obj->addAccount($user_id, AccountModel::RELIEF, $sign_reward, '领取救济金');
        // dump($account_obj->getLastsql());die;
        if ($left) {
            return '领取成功';
        } else {
            ApiModel::returnResult(1000, null, '领取失败');
        }
    }

    /**
     *排行榜奖励列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo排行榜奖励列表
     */
    public function rewardList($params)
    {
        $user_id = $this->user_id;

        $rank_list_obj = new RankListModel();

        $where = 'user_id =' . $user_id . ' AND is_send = 1';

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $rank_list_obj->getRankListNum($where);
        $rank_list_obj->setStart($firstRow);
        $rank_list_obj->setLimit($num_per_page);

        $rank_list_list = $rank_list_obj->getRankListList('rank_list_id,addtime,reward,is_received', $where, 'addtime desc');
        $rank_list_list = $rank_list_obj->getDataList($rank_list_list);
        ApiModel::returnResult(0, array(
            'rank_list_list' => $rank_list_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *领取排行榜奖励
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'领取成功'，失败退出返回错误码
     * @todo领取排行榜奖励
     */
    public function getReward()
    {
        $user_id = $this->user_id;
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'getReward_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }

        $where = 'user_id = ' . $user_id;

        $start_time = strtotime(date('Y-m-d', time()));
        $end_time = strtotime(date('Y-m-d', time())) + 24 * 3600 -1;

        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('user_id', $where);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        //判断是否有活动
        $marketing_rule_obj = new MarketingRuleModel();

        $res = $marketing_rule_obj->checkRule(MarketingRuleModel::RANKREWARD);

        if (!$res) {
            ApiModel::returnResult(2000, null, '暂无活动,或者活动已过期');
        }

        //判断今日是否已领取排行榜奖励金
        $account_obj = new AccountModel();
        $account_info = $account_obj->getDayReliveInfo(' AND user_id =' . $user_id, AccountModel::REWARD);

        if ($account_info) {
            ApiModel::returnResult(2000, null, '已领取');
        }

        $rank_list_obj = new RankListModel();

        $where = 'user_id =' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <= ' . $end_time . ' AND is_send = 1';

        $rank_list_info = $rank_list_obj->getRankListInfo($where, 'reward,rank_list_id');

        if (!$rank_list_info) {
            ApiModel::returnResult(1000, null, '没有可以领取的排行榜奖励金');
        } elseif ($rank_list_info['is_received'] == 1) {

            ApiModel::returnResult(1000, null, '已领取');
        }
        //领取排行榜奖励金金额
        $reward = $rank_list_info['reward'] ?: 0;

        if ($reward == 0) {
            ApiModel::returnResult(1000, null, '没有可领取的排行榜奖励金');
        }
        //标记为已经领取
        $rank_list_obj->editRankList('rank_list_id =' . $rank_list_info['rank_list_id'], array('is_received' => 1));

        $account_obj = new AccountModel();
        $left = $account_obj->addAccount($user_id, AccountModel::REWARD, $reward, '领取排行榜奖励金');
        // dump($account_obj->getLastsql());die;
        if ($left) {
            return '领取成功';
        } else {
            ApiModel::returnResult(1000, null, '领取失败');
        }
    }

    /**
     *用户兑换记录列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo用户兑换记录列表
     */
    public function userGiftList($params)
    {

        $user_id = $this->user_id;
        $user_gift_obj = new UserGiftModel();

        $type = $params['type'];

        switch ($type) {
            case self::WEEK :
                $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
                $end_time = time();
                break;
            case self::MONTH :
                $start_time = strtotime(date('Y-m-d', strtotime('-30 days')));
                $end_time = time();
                break;
            case self::HALFYEAR :
                $start_time = strtotime("-0 year -6 month -0 day");
                $end_time = time();
                break;
            case self::YEAR :
                $start_time = strtotime("-1 year -0 month -0 day");
                $end_time = time();
                break;
            default:
                # code...
                break;
        }

        $where = 'user_id = ' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <=' . $end_time;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $user_gift_obj->getUserGiftNum($where);
        $user_gift_obj->setStart($firstRow);
        $user_gift_obj->setLimit($num_per_page);

        $user_gift_list = $user_gift_obj->getUserGiftList('user_gift_id,gift_card_id,number,addtime', $where, 'addtime desc');
        $user_gift_list = $user_gift_obj->getListData($user_gift_list);
        // dump($user_gift_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'user_gift_list' => $user_gift_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *查看卡密信息
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo查看卡密信息
     */
    public function giftPasswordInfo($params)
    {
        $user_id = $this->user_id;
        $user_gift_obj = new UserGiftModel();
        $where = 'user_id = ' . $user_id . ' AND user_gift_id =' . $params['user_gift_id'];

        $user_gift_info = $user_gift_obj->getUserGiftInfo($where, 'number,addtime,gift_card_id');

        $gift_card_obj = new GiftCardModel();
        $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id =' . $user_gift_info['gift_card_id'], 'name');

        $user_gift_info['card_name'] = $gift_card_info['name'] ?: '';

        $user_gift_password_obj = new UserGiftPasswordModel();
        $user_gift_password_list = $user_gift_password_obj->where($where)->getField('card_password', true);

        $user_gift_info['card_password'] = $user_gift_password_list ?: array();

        return $user_gift_info;
    }

    /**
     *我的点卡列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo我的点卡列表
     */
    public function myGiftList($params)
    {

        $user_id = $this->user_id;
        $user_gift_password_obj = new UserGiftPasswordModel();

        $type = $params['type'];

        switch ($type) {
            case self::WEEK :
                $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
                $end_time = time();
                break;
            case self::MONTH :
                $start_time = strtotime(date('Y-m-d', strtotime('-30 days')));
                $end_time = time();
                break;
            case self::HALFYEAR :
                $start_time = strtotime("-0 year -6 month -0 day");
                $end_time = time();
                break;
            case self::YEAR :
                $start_time = strtotime("-1 year -0 month -0 day");
                $end_time = time();
                break;
            default:
                # code...
                break;
        }

        $where = 'user_id = ' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <=' . $end_time;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $user_gift_password_obj->getUserGiftPasswordNum($where);
        $user_gift_password_obj->setStart($firstRow);
        $user_gift_password_obj->setLimit($num_per_page);

        $user_gift_password_list = $user_gift_password_obj->getUserGiftPasswordList('', $where);
        $user_gift_password_list = $user_gift_password_obj->getListData($user_gift_password_list);
        // dump($user_gift_password_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'user_gift_list' => $user_gift_password_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *乐豆明细列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo乐豆明细列表
     */
    public function accountList($params)
    {

        $user_id = $this->user_id;
        $account_obj = new AccountModel();
        //只查看最近30天的记录
        $start_time = strtotime(date('Y-m-d', strtotime('-30 days')));
        $end_time = time();

        $where = 'user_id = ' . $user_id . ' AND addtime >=' . $start_time . ' AND addtime <=' . $end_time . ' AND state = 0';

        //2019.6.21  在乐豆明细中不显示 游戏投注类型  赢取
        $where .= ' AND change_type not in ('.AccountModel::GAMEWIN.','.AccountModel::BETTING.')';

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $account_obj->getAccountNum($where);
        $account_obj->setStart($firstRow);
        $account_obj->setLimit($num_per_page);

        $account_list = $account_obj->getAccountList('amount_after_pay,addtime,change_type,amount_in,amount_out,bank_money_after', $where, 'account_id desc');
        $account_list = $account_obj->getListData($account_list);
        // dump($account_obj->getLastsql());die;

        ApiModel::returnResult(0, array(
            'account_list' => $account_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *存取乐豆
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo存取乐豆
     */
    public function accessBean($params)
    {

        $user_id = $this->user_id;

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'accessBean_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }

        $where = 'user_id = ' . $user_id;
        $user_obj = new UserModel();
        $account_obj = new AccountModel();

        $user_info = $user_obj->getUserInfo('left_money,frozen_money,bank_password', $where);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        $type = $params['type'];

        if (!$params['number'] || $params['number'] < 1) {
            ApiModel::returnResult(1000, null, '金豆应不少于1');
        }

        if ($type == self::SAVE) {
            if ($params['number'] > $user_info['left_money']) {
                ApiModel::returnResult(1000, null, '金豆余额不足');
            }
            $res = $account_obj->addAccount($user_id, AccountModel::SAVE, $params['number'] * -1, '存入银行');
        } else {
            if ($params['bank_password'] != $user_info['bank_password']) {
                ApiModel::returnResult(1000, null, '银行密码错误');
            }

            if ($params['number'] > $user_info['frozen_money']) {
                ApiModel::returnResult(1000, null, '银行余额不足');
            }
            $res = $account_obj->addAccount($user_id, AccountModel::TAKEOUT, $params['number'], '从银行取出');
        }

        if ($res > 0) {
            return '操作成功';
        } else {
            return '操作失败';
        }
    }

    /**
     *发送红包
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'发送成功'，失败退出返回错误码
     * @todo发送红包
     */
    public function sendRedPacket($params)
    {
        $config_base_obj = new ConfigBaseModel();
        $red_expire_time = $config_base_obj->getConfig('red_expire_time') ?: 7;   //红包有效期天数
        $red_limit_money = $config_base_obj->getConfig('red_limit_money') ?: 1;   //红包最小金额
        $red_point = $config_base_obj->getConfig('red_point') ?: 1;   //发红包税点

        $red_packet_obj = new RedPacketModel();

        $user_id = $this->user_id;

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'sendRedPacket_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }

        if($params['total_money'] <= 0)
        {
            ApiModel::returnResult(2000,null,'红包金额必须大于0');
        }
        if($params['num'] <= 0)
        {
            ApiModel::returnResult(2000,null,'红包数量必须大于0');
        }

        $where = 'user_id = ' . $user_id;
        $user_obj = new UserModel();
        $account_obj = new AccountModel();

        $user_info = $user_obj->getUserInfo('frozen_money,mobile', $where);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        $verify_code_obj = new VerifyCodeModel();
        $valid = $verify_code_obj->checkVerifyCodeValid($params['code'], $user_info['mobile']);

        if (!$valid) {
            ApiModel::returnResult(1000, null, '验证码无效');
        }

        if (!$params['total_money'] || $params['total_money'] < $params['num'] * $red_limit_money) {
            ApiModel::returnResult(1000, null, '每个红包至少有' . $red_limit_money . '金豆');
        }
        // dump( $user_info['frozen_money']/1000*(100+$red_point)/100);die;


        if ($params['total_money'] * (100 + $red_point) / 100 > $user_info['frozen_money']) {
            ApiModel::returnResult(1000, null, '银行余额不足');
        }

        if ($params['type'] == 1) {
            $params['each_money'] = $params['total_money'] / $params['num'];
        }
        $params['residue_money'] = $params['total_money'];
        $params['residue_num'] = $params['num'];
        $params['expire_time'] = time() + 3600 * 24 * $red_expire_time; //红包有效期
        $params['addtime'] = time();
        $params['user_id'] = $user_id;

        $res = $account_obj->addAccount($user_id, AccountModel::REDPACKET, intval($params['total_money'] * -1 * (100 + $red_point) / 100), '发红包');

        if ($res >0) {
//            $res = $account_obj->saveBank($params['total_money'] * -1 * 1000 * (100 + $red_point) / 100);
            $red_packet_id = $red_packet_obj->addRedPacket($params);

            $red_str = 'red_'.$red_packet_id;
            $redis_obj->set($red_str,$params['num']);

            log_file('$red_packet_id='.$red_packet_id,'red_paget');
            $jiami_id = url_jiami(strval($red_packet_id));
            log_file('$jiami_id='.$jiami_id,'red_paget');
            return $jiami_id;
        } else {
            return '生成失败';
        }

    }

    /**
     *我发送的红包列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo我的红包列表
     */
    public function redPacketList($params)
    {

        $user_id = $this->user_id;
        $red_packet_obj = new RedPacketModel();

        $where = 'isuse = 1 AND user_id = ' . $user_id;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $red_packet_obj->getRedPacketNum($where);
        $red_packet_obj->setStart($firstRow);
        $red_packet_obj->setLimit($num_per_page);

        $red_packet_list = $red_packet_obj->getRedPacketList('red_packet_id,title,total_money - residue_money as residue_money,num - residue_num as residue_num,total_money,num,type,addtime,expire_time,is_cancel', $where, 'addtime desc');
        $red_packet_list = $red_packet_obj->getJiaMiData($red_packet_list);
        // dump($red_packet_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'red_packet_list' => $red_packet_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *我领取的红包列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo我的红包列表
     */
    public function packetLogList($params)
    {

        $user_id = $this->user_id;
        $red_packet_log_obj = new RedPacketLogModel();

        $where = 'user_id = ' . $user_id;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $red_packet_log_obj->getRedPacketLogNum($where);
        $red_packet_log_obj->setStart($firstRow);
        $red_packet_log_obj->setLimit($num_per_page);

        $red_packet_log_list = $red_packet_log_obj->getRedPacketLogList('red_packet_id,addtime,money', $where, 'addtime desc');
        $red_packet_log_list = $red_packet_log_obj->getListData($red_packet_log_list);
        // dump($red_packet_log_obj->getLastsql());die;
        ApiModel::returnResult(0, array(
            'red_packet_log_list' => $red_packet_log_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     *撤销红包
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     */
    public function cancelRedPacket($params)
    {
        ApiModel::returnResult(2000,null,'操作失败');

        $user_id = $this->user_id;

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'cancelRedPacket_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }

        $red_packet_obj = new RedPacketModel($params['red_packet_id']);

        $where = 'isuse = 1 AND user_id = ' . $user_id . ' AND red_packet_id =' . $params['red_packet_id'];

        $red_packet_info = $red_packet_obj->getRedPacketInfo($where, 'is_cancel,residue_money');

        if (!$red_packet_info) {
            ApiModel::returnResult(1000, null, '红包不存在');
        }

        if ($red_packet_info['is_cancel'] == 1) {
            ApiModel::returnResult(1000, null, '红包已撤销');
        }

        $red_packet_id = $red_packet_obj->editRedPacket($where, array('is_cancel' => 1));

        if ($red_packet_id) {
            $account_obj = new AccountModel();
            $account_obj->addAccount($user_id, AccountModel::REDRETURN, $red_packet_info['residue_money'], '红包撤销');
            return '撤销成功';
        } else {
            return '撤销失败';
        }
    }

    /**
     *经验兑换乐豆
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'、兑换成功'，失败退出返回错误码
     */
    public function exChangeExp($params)
    {

        $user_id = $this->user_id;

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'exChangeExp_'.$this->user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }

        //获取用户基本信息
        $where = 'user_id = ' . $this->user_id;
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('exp', $where);

        if (!$user_info) {
            ApiModel::returnResult(1000, null, '用户不存在');
        }

        //判断是否有活动
        $marketing_rule_obj = new MarketingRuleModel();

        $res = $marketing_rule_obj->checkRule(MarketingRuleModel::EXP);

        if (!$res) {
            ApiModel::returnResult(2000, null, '暂无活动,或者活动已过期');
        }

        $level_obj = new LevelModel();
        $max_exp = $level_obj->order('level_id desc')->getField('min_exp');
        $user_info['more_exp'] = $user_info['exp'] - $max_exp;

        if ($user_info['more_exp']<=0) {
            ApiModel::returnResult(1000, null, '没有可以兑换的经验');
        }
        if($user_info['more_exp'] < $params['bean'])
        {
            ApiModel::returnResult(1000, null, '没有足够兑换的经验');
        }

        $more_exp =  $params['bean'] > 0 ? $params['bean'] : $user_info['more_exp'];

        $account_obj = new AccountModel();
        $res = $account_obj->addAccount($user_id, AccountModel::EXCHANGEEXP, $more_exp, '经验兑换乐豆');

        if ($res) {
            return '兑换成功';
        } else {
            return '兑换失败';
        }
    }

    /**
     *我领取的红包列表
     * @author yzp
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo我的红包列表
     */
    public function returnLogList($params)
    {

        $user_id = $this->user_id;
        $return_log_obj = new ReturnLogModel();

        $where = 'user_id = ' . $user_id;

        $firstRow = isset($params['firstRow']) ? $params['firstRow'] : 0;

        $num_per_page = isset($params['fetchNum']) ? $params['fetchNum'] : C('PER_PAGE_NUM');
        $num_per_page = isset($num_per_page) ? $num_per_page : 10;

        $total = $return_log_obj->getReturnLogNum($where);
        $return_log_obj->setStart($firstRow);
        $return_log_obj->setLimit($num_per_page);

        $return_log_list = $return_log_obj->getReturnLogList('money,addtime,return_type', $where, 'addtime desc');
        $return_log_list = $return_log_obj->getListData($return_log_list);
        ApiModel::returnResult(0, array(
            'return_log_list' => $return_log_list ?: array(),
            'total' => $total ?: 0
        ));

    }

    /**
     * 获取图形验证码
     * @author yzp
     * @param array $params 参数列表
     */
    public function getImgCode()
    {
        $session_id = session_id();
        log_file('app请求头'.json_encode($_REQUEST),'app_log');
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/Public/verify/?session_id=' . $session_id;
        ApiModel::returnResult(0, array(
            'url' => $url
        ));
    }

    /**
     * 登录接口
     * @author wsq
     * @param array $params 参数列表
     * @return 成功返回'登录成功'，失败退出返回错误码
     * @todo 查看用户名和密码是否合法，不合法退出，否则设置session
     */
    function login($params)
    {
//		$jpush_reg_id = $params['jpush_reg_id'];
        //判断账号密码合法性
        $user_obj = new UserModel();
        $login_log_obj = new LoginLogModel();

        $user_id = $user_obj->where('is_enable = 1 AND mobile = "' . $params['mobile'] . '" AND role_type = 3')->getField('user_id');

        if (!$user_id) {
            ApiModel::returnResult(42005, null, '用户不存在');
        }

        $user_info = $user_obj->getUserInfo(
            'user_id, role_type, mobile,open_login_limit,login_limit_city_id,another_limit_city_id',
            'mobile = "' . $params['mobile'] . '" AND password = "' . $params['password'] . '" AND role_type = 3'
        );
        //验证图形验证码
//         $verify_code_obj = new VerifyCodeModel();
//         if(!$verify_code_obj->checkImgCode($params['code'])){
//             $login_log_obj->addApiLoginLog($user_id,0);//登陆失败记录
//             ApiModel::returnResult(42005, null, '验证码错误');
//         };

        $code = strtoupper($params['code']);
        log_file('code = ' . session('verify'), 'code', true);
        //图形验证码验证
        if ($code != 1111) {
            if (md5($code) != session('verify')) {
                ApiModel::returnResult(40051, null, '图形验证码错误');
            }
        }
        session('verify', null);

        log_file('$params=' . json_encode($params), 'debug_login');
        log_file('$sql=' . $user_obj->getLastsql(), 'debug_login');
        log_file('$user_info=' . json_encode($user_info), 'debug_login');

        if (!$user_info) {
            $login_log_obj->addApiLoginLog($user_id, 0);//登陆失败记录
            ApiModel::returnResult(42001, null, '用户名或密码不正确');
        }
        if ($user_info['open_login_limit'] == 1) //打开了登陆地址限制
        {
            $ip = getIP();
            $city = getCityByIp($ip);
            if ($city['city_id'] != $user_info['login_limit_city_id'] && $city['city_id'] != $user_info['another_limit_city_id']) {
                $login_log_obj->addApiLoginLog($user_id, 0);//登陆失败记录
                ApiModel::returnResult(42004, null, '登陆地区受限');
            }
        }

        //登陆记录
        $login_log_obj->addApiLoginLog($user_id, 1);//登陆成功记录
        session('user_id', $user_info['user_id']);
        session('role_type', $user_info['role_type']);

        $session_id = session_id();
        //单点登录
        $push_obj = new PushModel();
        $push_obj->redisSet('push_'.$user_info['user_id'].'_session',$session_id);
        $user_obj = new UserModel($user_info['user_id']);
//		$arr = array(
//			'jpush_reg_id' => $jpush_reg_id,
//			);
//		$user_obj->setUserInfo($arr);
//		$r= $user_obj->saveUserInfo();
        //返回的数组
        return array(
            'user_id' => $user_info['user_id']
        );
    }

    /**
     * 新登录接口
     * @author 姜伟
     * @param array $params 参数列表
     * @return 成功返回'登录成功'，失败退出返回错误码
     * @todo 查看用户名和密码是否合法，不合法退出，否则设置session
     */
    function signin($params)
    {
        $verify_code = isset($params['verify_code']) && $params['verify_code'] ? $params['verify_code'] : '';
        $password = isset($params['password']) && $params['password'] ? $params['password'] : '';

        if (!$verify_code && !$password) {
            ApiModel::returnResult(42004, null, '缺少验证码或密码');
        }


        $user_obj = new UserModel();
        if ($verify_code) {
            //有验证码，以验证码为准
            //验证码验证
            $verify_code_obj = new VerifyCodeModel();
            if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $params['mobile'])) {
                ApiModel::returnResult(40051, null, '无效的验证码');
            }
            $result = $user_obj->getUserInfo('user_id', 'mobile = "' . $params['mobile'] . '"');
        }

        if (!$verify_code && $password) {
            //无验证码，以密码登录
            $result = $user_obj->getUserInfo('user_id', 'mobile = "' . $params['mobile'] . '" AND password = "' . $params['password'] . '" ');
        }

        if ($result) {
            session('user_id', $result['user_id']);
            $new_password = randLenString(6, 0);
            $new_password = md5($new_password);
            $arr = array(
                'password' => $new_password
            );
            $user_obj = new UserModel($result['user_id']);
            $user_obj->setUserInfo($arr);
            $user_obj->saveUserInfo();
            log_file($new_password);
            return array(
                'password' => $new_password
            );


        }

        #ApiModel::returnResult(-1, null, '系统错误');
        ApiModel::returnResult(42001, null, '密码不正确');


//		$mobile   = $params['mobile'];
//		$password = $params['password'];
//
//		$user_obj = new UserModel();
//		$user_num = $user_obj->getUserNum('role_type = 3 AND mobile = ' . $mobile);
//		if (!$user_num) {
//			ApiModel::returnResult(42001, null, '该手机号尚未注册，请先注册');
//			//todo 系统帮助用户注册
//		}
//
//		$fields = 'user_id, role_type';
//		$result = $user_obj->getUserInfo($fields, 'role_type = 3 AND mobile = "' . $params['mobile'] . '" AND password = "' . $params['password'] . '"');
//
//		if ($result) {
//
//			/**
//			 * 如果是安卓登录，则清理掉ios的device_token
//			 */
//			if (is_android_mobile()) {
//				$user_obj = new UserModel($result['user_id']);
//				$user_obj->setUserInfo(array(
//					'user_id'      => $result['user_id'],
//					'device_token' => ''
//				));
//				$user_obj->saveUserInfo();
//			}
//
//			session('user_id', $result['user_id']);
//			session('role_type', $result['role_type']);
//			return array(
//				'user_id'           => $result['user_id'],
//				'system_money_name' => $GLOBALS['config_info']['SYSTEM_MONEY_NAME'],
//				'delivery_amount_limit' => $GLOBALS['config_info']['DELIVERY_AMOUNT_LIMIT'],
//			);
//		}
//
//		log_file('params = ' . json_encode($params), 'signin', true);
//		ApiModel::returnResult(42001, null, '密码不正确');
    }

    /**
     * 新用户注册接口
     * @author 姜伟
     * @param array $params
     * @return 成功返回'注册成功'，失败返回错误码
     * @todo 新用户注册接口
     */
    function signup($params)
    {
        log_file(session_id(), 'session_id_2');
        log_file($_COOKIE['PHPSESSID'], 'session_id_3');
        $verify_code = $params['verify_code'];
        $mobile = $params['mobile'];
        $again_password = $params['again_password'];
        $password = $params['password'];
        $user_obj = new UserModel();

        $code = strtoupper($params['code']);
        log_file('verify ='.session('verify'), 'verify');
        log_file(md5($code), 'verify');

        //图形验证码验证
        if (md5($code) != session('verify')) {

            ApiModel::returnResult(40051, null, '图形验证码错误');
        }
        session('verify', null);

        //验证码验证
        $verify_code_obj = new VerifyCodeModel();
        if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile)) {
            ApiModel::returnResult(40051, null, '无效的验证码');
        }


        if ($again_password != $password) {
            ApiModel::returnResult(42004, null, '两次密码输入不同');
        }

        //手机号验证
        $user_info = $user_obj->getUserInfo('user_id, mobile_registered', 'mobile = "' . $mobile . '" AND role_type = 3');
        if ($user_info) {
            if ($user_info['user_id']) {
                ApiModel::returnResult(40020, null, '手机号已注册');
            }

//			$user_id = $user_info['user_id'];
//			$user_obj = new UserModel($user_id);
//			unset($params['verify_code']);
//
//			$user_obj->setUserInfo($params);
//			$user_obj->saveUserInfo();

            //$planter_id = $user_info['current_planter_id'];
        } else {
            //注册
            unset($params['again_password']);
            unset($params['verify_code']);
            unset($params['code']);
            $user_obj = new UserModel();
            $params['mobile_registered'] = 1;
            $user_id = $user_obj->addUser($params);
            log_file($user_obj->getLastSql());
        }

        if (!$user_id) {
            ApiModel::returnResult(-1, null, '系统错误');
        } else {
            session('user_id', $user_id);
            //去重
            do {
                $invite_id = randLenString(6, 1, '');
            } while ($user_obj->where('invite_id =' . $invite_id)->getField('user_id'));

            $arr = array(
                'safe_password' => $params['password'],
                'bank_password' => $params['password'],
                'invite_id' => $invite_id,
            );
            $user_obj = new UserModel($user_id);
            $user_obj->setUserInfo($arr);
            $user_obj->saveUserInfo();
            //如果被邀请的话
            if ($params['parent_id']) {
                $parent_id = $user_obj->where('id =' . $params['parent_id'])->getField('user_id');
                $config_base_obj = new ConfigBaseModel();
                $invite_award = $config_base_obj->getConfig('invite_award') ;   //推广奖励

                $invite_log_obj = new InviteLogModel();
                $invite_data = [
                    'user_id' => $user_id,
                    'parent_id' => $parent_id,
                    'addtime' => time(),
                    'reward' => $invite_award,
                ];
                //生成推广记录
                $invite_log_id = $invite_log_obj->addInviteLog($invite_data);
//                //推广金豆奖励
//                $account_obj = new AccountModel();
//                $account_obj->addAccount($parent_id, AccountModel::INVITEAWARD, $invite_award, '推荐新用户奖励');
            }
            return $user_id;
        }
    }

    /**
     * 注销接口
     * @author wsq
     * @param array $params 参数列表
     * @return 成功返回'退出成功'，失败退出返回错误码
     * @todo 注销接口
     */
    function logout($params)
    {
        $user_id = session('user_id');
        $user_obj = new UserModel(session('user_id'));
        $arr = array(
            'jpush_reg_id' => '',
        );
        $user_obj->setUserInfo($arr);
        $user_obj->saveUserInfo();
        session('user_id', null);
        session('role_type', null);

        //单点登录
        $push_obj = new PushModel();
        $push_obj->redisSet('push_' . $user_id . '_session', null);
        return '退出成功';
    }

    /**
     * 用户注册接口
     * @author 姜伟
     * @param array $params
     * @return 成功返回'注册成功'，失败返回错误码
     * @todo 为该用户注册
     */
    function register($params)
    {
        $verify_code = $params['verify_code'];
        $mobile = $params['mobile'];
        $user_obj = new UserModel();

        //验证码验证

        $verify_code_obj = new VerifyCodeModel();
        if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile)) {
            ApiModel::returnResult(40051, null, '无效的验证码');
        }

        //手机号验证
        $user_info = $user_obj->getUserInfo(
            'user_id, role_type, mobile_registered',
            'mobile = "' . $params['mobile'] . '"'
        );

        if ($user_info) {
            if ($user_info['mobile_registered']) {
                ApiModel::returnResult(40020, null, '手机号已注册');
            }

            $user_id = $user_info['user_id'];
            $user_obj = new UserModel($user_id);
            unset($params['verify_code']);

            $params['mobile_registered'] = 1;
            $user_obj->setUserInfo($params);
            $user_obj->saveUserInfo();
            session('user_id', $user_id);
            session('planter_id', $user_info['current_planter_id']);
            return $user_id;
        }

        //注册
        unset($params['verify_code']);
        $user_obj = new UserModel();
        $params['mobile_registered'] = 1;
        $user_id = $user_obj->addUser($params);
        if (!$user_id) {
            ApiModel::returnResult(-1, null, '系统错误');
        }

        // 注册商家
        /*
        $merchant_obj = new MerchantModel();
        $arr = array(
            user_id => $user_id
        );
        $merchant_id = $merchant_obj->addMerchant($arr);
        */

        session('user_id', $user_id);
        session('planter_id', 0);
        return $user_id;
    }

    /**
     * 第三方授权登录．
     * @param $params
     * @return array
     */
    function thirdLogin($params)
    {
        $third_tag = $params['third_tag'];

        if (!$third_tag) ApiModel::returnResult(1, null, '缺少third_tag参数');
        $user_obj = new UserModel();
        $user_info = '';
        if ($third_tag == 'wx') {
            $user_info = $user_obj->getUserInfo('user_id, role_type, mobile', 'wx_token ="' . $params['third_token'] . '" and role_type = 3');
        }

        if ($third_tag == 'qq') {
            $user_info = $user_obj->getUserInfo('user_id, role_type, mobile', 'qq_token ="' . $params['third_token'] . '" and role_type = 3');
        }

        if (!$user_info) {
            ApiModel::returnResult(2, null, '未找到该用户，请前往绑定手机');
        }

        session('user_id', $user_info['user_id']);
        session('role_type', $user_info['role_type']);
        $user_obj = new UserModel($user_info['user_id']);
        $arr = array(
            'jpush_reg_id' => $params['jpush_reg_id'],
        );
        $user_obj->setUserInfo($arr);
        $r = $user_obj->saveUserInfo();
        //返回的数组
        return array(
            'user_id' => $user_info['user_id']
        );
    }

    /**
     * 第三方授权注册
     * @param $params
     * @return bool|mixed
     */
    function thirdRegister($params)
    {
        $third_tag = $params['third_tag'];
        $mobile = $params['mobile'];
        $verify_code = $params['verify_code'];
        $third_token = $params['third_token'];

        //验证码验证
        $verify_code_obj = new VerifyCodeModel();
        if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile)) {
            ApiModel::returnResult(40051, null, '无效的验证码');
        }
        $user_obj = new UserModel();
        //手机号验证
        $user_info = $user_obj->getUserInfo(
            'user_id, role_type, wx_token , qq_token',
            'mobile = "' . $mobile . '" and role_type = 3'
        );

        if ($user_info) {
            if ($third_tag == 'wx') {
                if ($user_info['wx_token']) {
                    ApiModel::returnResult(40020, null, '手机号已注册');
                } else {
                    session('user_id', $user_info['user_id']);
                    $arr = array(
                        'jpush_reg_id' => $params['jpush_reg_id'],
                        'wx_token' => $third_token,
                    );
                    $user_obj = new UserModel($user_info['user_id']);
                    $user_obj->setUserInfo($arr);
                    $r = $user_obj->saveUserInfo();
                    log_file('third_wx =' . $user_obj->getLastSql(), 'third_ccy');
                    //返回的数组
                    return array(
                        'user_id' => $user_info['user_id']
                    );
                }
            }

            if ($third_tag == 'qq') {
                if ($user_info['qq_token']) {
                    ApiModel::returnResult(40020, null, '手机号已注册');
                } else {
                    session('user_id', $user_info['user_id']);
                    $arr = array(
                        'jpush_reg_id' => $params['jpush_reg_id'],
                        'qq_token' => $third_token,
                    );
                    $user_obj = new UserModel($user_info['user_id']);
                    $user_obj->setUserInfo($arr);
                    $r = $user_obj->saveUserInfo();
                    log_file('third_qq =' . $user_obj->getLastSql(), 'third_ccy');
                    //返回的数组
                    return array(
                        'user_id' => $user_info['user_id']
                    );
                }
            }
        }

        //注册
        $data = array(
            'role_type' => 3,
            'mobile' => $mobile,
            'username' => $mobile,
            'jpush_reg_id' => $params['jpush_reg_id'],
            'password' => $params['password'],
        );

        if ($third_tag == 'wx') $data['wx_token'] = $third_token;
        if ($third_tag == 'qq') $data['qq_token'] = $third_token;

        $user_obj = new UserModel();
        $user_id = $user_obj->addUser($data);
        log_file($user_obj->getLastSql(), 'a', true);
        log_file('user_id = ' . $user_id, 'a', true);
        if (!$user_id) ApiModel::returnResult(-1, null, '系统错误');

        session('user_id', $user_id);
        return $user_id;
    }

    /**
     * 验证手机号是否已注册
     * @author 姜伟
     * @param array $params
     * @return 成功返回1/0，失败返回错误码
     * @todo 验证手机号是否已注册
     */
    function checkMobileRegisteredThird($params)
    {
        $mobile = $params['mobile'];
        $third_tag = $params['third_tag'];
        $where = 'mobile = "' . $mobile . '" and role_type = 3';
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('user_id ,wx_token , qq_token', $where);

        if ($third_tag == 'wx') {
            if ($user_info && !$user_info['wx_token']) {
                return array(
                    'msg' => '手机号可被绑定!',
                    'state' => 1,
                );
            }
        }

        if ($third_tag == 'qq') {
            if ($user_info && !$user_info['qq_token']) {
                return array(
                    'msg' => '手机号可被绑定!',
                    'state' => 1,
                );
            }
        }

        if ($user_info) {
            ApiModel::returnResult(1, null, '抱歉，手机已被绑定!');
        }

        return array(
            'msg' => '全新手机号!',
            'state' => 0,
        );
    }

    /**
     * 找回密码
     * @author 姜伟
     * @param array $params
     * @return 成功返回'修改成功'，失败返回错误码
     * @todo 找回密码
     */
    function findPassword($params)
    {
        $code = strtoupper($params['code']);
        $verify_code = $params['verify_code'];
        $mobile = $params['mobile'];
        $verify_code_obj = new VerifyCodeModel();
        //图形验证码验证
        // if (md5($code) != session('verify'))
        if (!$verify_code_obj->checkImgCode($code)) {
            ApiModel::returnResult(40051, null, '图形验证码错误');
        }
        // session('verify',null);
        //短信验证码验证

        if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile)) {
            ApiModel::returnResult(40051, null, '无效的短信验证码');
        }

        // 判断账号合法性
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo(
            'user_id, role_type',
            'mobile = "' . $mobile . '" '
        );
        if (!$user_info) {
            ApiModel::returnResult(42001, null, '该用户未注册');
        }
        // //验证图形验证码
        // if(!$verify_code_obj->checkImgCode($params['code'])){
        //     ApiModel::returnResult(42005, null, '验证码错误');
        // };

        return '验证成功';
    }

    /**
     * 找回密码 根据手机号短信
     * @author clk
     * @param array $params
     * @return 成功返回'修改成功'，失败返回错误码
     * @todo 找回密码
     */
    function findPasswordBySms($params)
    {
        $mobile = $params['mobile'];
        $verify_code = $params['verify_code'];
        $new_password = $params['new_password'];

        // 验证码验证
        $verify_code_obj = new VerifyCodeModel();
        if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile)) {
            ApiModel::returnResult(40051, null, '无效的验证码');
        }

        // 判断账号合法性
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo(
            'user_id, role_type',
            'mobile = "' . $mobile . '" '
        );

        if (!$user_info) {
            ApiModel::returnResult(42001, null, '该用户未注册');
        }

        // 修改密码
        $user_id = $user_info['user_id'];
        $user_obj = new UserModel($user_id);
        $arr = array(
            'password' => $new_password
        );

        $user_obj->setUserInfo($arr);
        $success = $user_obj->saveUserInfo();

        log_file($params['new_password']);
        log_file($user_obj->getLastSql());

        return '修改成功';
    }


    /**
     * 获取用户基本信息接口
     * @author clk
     * @param array $params
     * @return 成功返回$user_info，失败返回错误码
     * @todo 获取用户基本信息接口
     */
    function getShareParam($params)
    {
        //获取用户基本信息
        $where = 'user_id = ' . session('user_id');
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo(
            'user_id, nickname , headimgurl, email',
            $where
        );

        //return $user_info;
        $share_info = array(
            title => $user_info['nickname'],
            desc => 'Welcome everyone join us',
            link => 'http://www.beyondin.com',
            img => 'http://img3.duitang.com/uploads/item/201410/13/20141013213303_YJwHP.jpeg'
        );

        return $share_info;
    }


    /**
     * 验证手机号是否已注册
     * @author 姜伟
     * @param array $params
     * @return 成功返回1/0，失败返回错误码
     * @todo 验证手机号是否已注册
     */
    function checkMobileRegistered($params)
    {
        $mobile = $params['mobile'];
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('user_id', 'mobile_registered = 1 AND mobile = "' . $mobile . '"');

        return $user_info ? 1 : 0;
    }

    /**
     * 发送验证码短信
     * @author 姜伟
     * @param array $params
     * @return 成功返回'发送成功'，失败返回错误码
     * @todo 发送验证码短信
     */
    function sendVerifyCode($params)
    {
        $mobile = $params['mobile'];
        //获取验证码
        $verify_code_obj = new VerifyCodeModel();
        $verify_code = $verify_code_obj->generateVerifyCode($mobile);
        log_file($verify_code_obj->getLastSql());
        log_file($verify_code);
        if ($verify_code) {
            $sms_obj = new SMSModel();
            $send_state = $sms_obj->sendVerifyCode($mobile, $verify_code, SMSModel::VERIFY_CODE);
            if ($send_state > 0) {
                return '发送成功';
            } else {
                ApiModel::returnResult(400, null, '发送失败');
            }
        } else {
            ApiModel::returnResult(-1, null, '请不要重复点击');
        }
    }

    /**
     * 登录验证短信
     * @date: 2019/4/23
     * @author: hui
     * @return string
     */
    public function sendLoginCode()
    {
        $user_id = session('user_id');
        $user_obj = new UserModel();
        $mobile = $user_obj->where('user_id = ' . $user_id)->getField('mobile');
        //获取验证码
        $verify_code_obj = new VerifyCodeModel();
        $verify_code = $verify_code_obj->generateVerifyCode($mobile);
        log_file($verify_code_obj->getLastSql());
        log_file($verify_code);
        if ($verify_code) {
            $sms_obj = new SMSModel();
            $send_state = $sms_obj->sendVerifyCode($mobile, $verify_code, SMSModel::LOGIN);
            if ($send_state > 0) {
                return '发送成功';
            } else {
                return '发送失败';
            }
        } else {
            ApiModel::returnResult(-1, null, '请不要重复点击');
        }
    }

    /**
     * 绑定微信
     * @param $params
     * @date: 2019/4/29
     * @author: hui
     * @return 用户信息数组，
     */
    public function bindWeixin($params)
    {
        $mobile = $params['mobile'];
        $verify_code = $params['verify_code'];

        //验证手机号和验证码合法性
        $verify_code_obj = new VerifyCodeModel();
        if (!$verify_code_obj->checkVerifyCodeValid($verify_code, $mobile)) {
            ApiModel::returnResult(42005, null, '对不起，验证码无效');
        }

        //获取用户信息
        $user_obj = new UserModel();
        $field = 'user_id, role_type, password, mobile, nickname, wx_token, headimgurl, sex,remark,is_card_check,realname';
        $user_info = $user_obj->getUserInfo('*', 'mobile = "' . $mobile . '" AND role_type = 3');

        if (!$user_info) {
            ApiModel::returnResult(42005, null, '无法获取该手机号绑定账号信息');
        }
        $wx_user_id = intval(session('user_id'));

        if (!$wx_user_id) {
            ApiModel::returnResult(42005, null, '无法获取微信信息');
        }

        //1 用户存在合并微信信息到原有用户信息；2 用户不存在保存手机/密码信息
        if ($user_info && ($wx_user_id != $user_info['user_id'])) {
            if ($user_info['openid'] || $user_info['wx_token']) {
                ApiModel::returnResult(42006, [], '该手机号已绑定其他微信');
            } else {
                //$wx_field = 'openid, refresh_token, access_token, token_expires_time';
                //nickname, sex, city, province, headimgurl, user_cookie, role_type, is_enable, reg_time, province_id, city_id
                $wx_user_obj = new UserModel();
                $wx_user_info = $wx_user_obj->getUserInfo('*', 'user_id = ' . $wx_user_id);

                $edit_user_data = array();
                foreach ($wx_user_info as $k => $v) {
                    $edit_user_data[$k] = $user_info[$k] ?: $v;
                }
                $result = $wx_user_obj->where('user_id=' . $user_info['user_id'])->save($edit_user_data);

                //合并用户信息成功，删除创建的微信用户信息
                if ($result !== false) {
                    $wx_user_obj->delRecord($wx_user_id);
                    $user_new_info = $user_obj->getUserInfo($field, 'user_id = ' . $user_info['user_id']);
                    session('user_id', $user_info['user_id']);
                    log_file('$sql=' . $user_obj->getLastsql(), 'debug_wx_login');
                    return $user_new_info;
                } elseif ($result) {
                    ApiModel::returnResult(42005, null, '绑定失败');
                }
            }

        }
    }


    /**
     * 修改密码
     * @author wsq
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 修改密码
     */
    function setPassword($params)
    {
        //判断账号密码合法性
        $user_id = session('user_id');
        $user_obj = new UserModel($user_id);

        $user_info = $user_obj->getUserInfo(
            'user_id',  // fields
            'user_id = ' . $user_id . ' AND password = "' . $params['old_password'] . '"' //where
        );

        if (!$user_info) {
            ApiModel::returnResult(42005, null, '旧密码不正确');
        }

        $user_obj->setUserInfo(
            array(
                'password' => $params['new_password']
            )
        );

        $success = $user_obj->saveUserInfo();

        return '修改成功';
    }


    /**
     * 修改密码
     * @author wsq
     * @param array $params 参数列表
     * @return 成功返回'修改成功'，失败退出返回错误码
     * @todo 修改密码
     */
    function resetPassword($params)
    {
        //判断账号密码合法性
        $mobile = $params['mobile'];
        $user_obj = new UserModel();
        $user_info = $user_obj->getUserInfo('user_id', 'mobile =' . $mobile);
        if (!$user_info) {
            ApiModel::returnResult(42005, null, '用户不存在');
        }

        if ($params['new_password'] != $params['re_password']) {
            ApiModel::returnResult(42005, null, '两次秘密输入不一致');
        }
        if ($params['type'] == 1) {
            $arr = array(
                'password' => $params['new_password'],
                'bank_password' => $params['new_password'],
            );
        } else {
            $arr = array(
                'safe_password' => $params['new_password']
            );
        }

        $success = $user_obj->where('user_id =' . $user_info['user_id'])->save($arr);
        return '修改成功';
    }


    /**
     * 绑定qq
     * @param $params
     * @date: 2019/4/29
     * @author: hui
     * @return string
     */
    public
    function bindQQ($params)
    {
        $verify_code = $params['verify_code'];
        $qq = $params['qq'];
        $user_id = $this->user_id;
        $user_obj = new UserModel();
        $user_info = $user_obj->where('qq = "' . $qq . '"')->find();
        if ($user_info) {
            ApiModel::returnResult(-1, '', '该QQ已被其他账号绑定');
        }
        $mobile = $user_obj->where('user_id =' . $user_id)->getField('mobile');
        //验证码验证
        $verify_code_obj = new VerifyCodeModel();
        $valid = $verify_code_obj->checkVerifyCodeValid($verify_code, $mobile);
        if (!$valid) {
            ApiModel::returnResult(-1, '', '验证码错误');
        }
        $res = $user_obj->where('user_id =' . $user_id)->save(['qq' => $qq]);
        if ($res !== false) {
            return '绑定成功';
        } else {
            ApiModel::returnResult(-1, '', '绑定失败');
        }

    }

    /**
     * 判断验证码是否有效
     * @author 姜伟
     * @param array $params 参数列表
     * @return 成功返回0，失败退出返回错误码
     * @todo 判断验证码是否有效
     */
    function checkVerifyCodeValid($params)
    {
        $verify_code = $params['verify_code'];
        //验证码验证
        $verify_code_obj = new VerifyCodeModel();
        $valid = $verify_code_obj->checkVerifyCodeValid($verify_code);

        return $valid ? 1 : 0;
    }

    /**
     * @todo 判断传递的安卓版本号是否需要更新
     * @author lye
     */
    public
    function androidUpdate($params)
    {
        $version = $params['version'];
        if (!$version) {
            ApiModel::returnResult(-2, [], '参数错误');
        }

        $android_version_obj = new AndroidVersionModel();
        $android_version_info = $android_version_obj->getInfoyVersion($version);
        if (!$android_version_info) {
            ApiModel::returnResult(-3, [], '参数错误');
        }
        if ($android_version_info['status'] == 1) {
            ApiModel::returnResult(0, '更新');
        } else {
            ApiModel::returnResult(-1, [], '不更新');
        }
    }

    public
    function frontUpdate($params)
    {
        $version = $params['version'];
        if (!$version) {
            ApiModel::returnResult(-2, [], '参数错误');
        }

        $front_version_obj = M('front_version');
        $front_version_info = $front_version_obj->where('version = "' . $version . '"')->find();
        if (!$front_version_info) {
            ApiModel::returnResult(-3, [], '参数错误');
        }
        if ($front_version_info['status'] == 1) {
            ApiModel::returnResult(0, '更新');
        } else {
            ApiModel::returnResult(-1, [], '不更新');
        }
    }

//邮箱验证
    public
    function sendEmail($params)
    {
        //发送邮件
        $email = $params['email'];//获取收件人邮箱
        //return $email;
        $sendmail = '1936438520@qq.com'; //发件人邮箱
        $sendmailpswd = "baipjdxhtbmxdcad"; //客户端授权密码,而不是邮箱的登录密码，就是手机发送短信之后弹出来的一长串的密码
        $send_name = 'lh';// 设置发件人信息，如邮件格式说明中的发件人，
        $toemail = $email;//定义收件人的邮箱
        $to_name = 'hl';//设置收件人信息，如邮件格式说明中的收件人
        vendor('Phpemail.PHPMailer');
        $mail = new PHPMailer();
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "utf8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = "smtp.qq.com";// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = $sendmail;//// 发送方的
        $mail->Password = $sendmailpswd;//客户端授权密码,而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port = 465;//  qq端口465或587）
        $mail->setFrom($sendmail, $send_name);// 设置发件人信息，如邮件格式说明中的发件人，
        $mail->addAddress($toemail, $to_name);// 设置收件人信息，如邮件格式说明中的收件人，
        $mail->addReplyTo($sendmail, $send_name);// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        $mail->Subject = "开开乐28";// 邮件标题

        $code = rand(100000, 999999);
        session("email_code", $code);
        //return $code."----".session("code");
        $mail->Body = "开开乐28：<b>您的验证码是：$code</b>，如果非本人操作无需理会！";// 邮件正文
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
        if (!$mail->send()) { // 发送邮件
            echo "Message could not be sent.";
            echo "Mailer Error: " . $mail->ErrorInfo;// 输出错误信息
        } else {
            return '发送成功';
        }
    }


    /**
     * 短信验证
     * @param $params
     * @date: 2019/4/23
     * @author: hui
     * @return string
     */
    public
    function checkVerifyCode($params)
    {
        $user_id = $this->user_id;
        $user_obj = new UserModel();
        $mobile = $user_obj->where('user_id =' . $user_id)->getField('mobile');
        $verify_code_obj = new VerifyCodeModel();
        $valid = $verify_code_obj->checkVerifyCodeValid($params['verify_code'], $mobile);
        if ($valid) {
            return '验证成功';
        } else {
            ApiModel::returnResult(-1, null, '验证失败,请重新登录');
        }
    }

    /**
     * 获取sessionid
     * @date: 2019/5/20
     * @author: hui
     * @return string
     */
    public function getSessionid()
    {
        return session_id();
    }


    /**
     * 获取排行榜奖励
     * @date: 2019/5/20
     * @author: hui
     * @return array
     */
    public function getRankReward(){
//        $addtime = strtotime(date('Ymd',strtotime('-1 day')));
//        $addtime = strtotime(date('Ymd',time()));
        $begin_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end_time = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;

        $user_id = session('user_id');
        $rank_list_obj = new RankListModel();
        $rank_list_info = $rank_list_obj->getRankListInfo('user_id ='.$user_id.' AND addtime >= '.$begin_time.' AND addtime <='.$end_time.' AND is_send = 1 AND is_received != 2',
            'reward,is_received');
        if($rank_list_info){
            return $rank_list_info;
        }else{
            return array('reward' => 0,
                'is_received' => 1);
        }


    }


    /**
     * 判断红包是否领取
     * @param $params
     * @date: 2019/5/21
     * @author: hui
     */
    public function isReceivedRedPacket($params){
//        $red_packet_id = $params['red_packet_id'];
        $params['red_packet_id'] = rtrim($params['red_packet_id'],'%3D');
        $params['red_packet_id'] = rtrim($params['red_packet_id'],'=');
        $red_packet_id = url_jiemi($params['red_packet_id']);
        $red_packet_log_obj = new RedPacketLogModel();
        $user_id = session('user_id');
        $is_received = $red_packet_log_obj->getRedPacketLogInfo('user_id ='.$user_id.' AND red_packet_id ='.$red_packet_id);
        if($is_received){
            return '红包已领取';
        }else{
            ApiModel::returnResult(-1,'','红包未领取');
        }
    }

    //设置手机访问和pc显示一样
    public function changeMobileWap($params)
    {
        $user_id = session('user_id');
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        if ($params['is_wap'] == 1) {
            $redis_obj->set('is_mobile_wap'.$user_id,$params['is_wap']);
            $redis_obj->persist('is_mobile_wap'.$user_id,$params['is_wap']);
        } else {
            $redis_obj->del('is_mobile_wap'.$user_id);
        }
        return '操作成功';
    }

    //获取体验版链接
    public function getTestUrl()
    {
        $user_obj = new UserModel();
        $user_info = [];

        $base_obj = new ConfigBaseModel();
        $server_name = $base_obj->getConfig('test_url');

        try {
            $user_info = $user_obj->getUserToken(session('user_id'));
        } catch (Exception $exception) {
            self::returnResult(-1,'',$exception->getMessage());
        }
        self::returnResult(0,[
            'url'   =>  $server_name.'?u_token='.$user_info['token']
        ]);


    }


    public function getRealInfo($params)
    {
        $user_obj = new UserModel();
        $res = $user_obj->getUserInfo('*',['token' => $params['user_token']]);

        self::returnResult(0,$res);
    }

    //六合彩检视账户
    public function getLiuhecaiUser($params)
    {
        $user_obj = new UserModel();
        $bet_log_obj = new BetLogModel();
        try {
            //获取用户信息
            $user_info = $user_obj->getUserInfo('nickname,mobile,left_money','user_id = '.$this->user_id);

            $game_result_obj = new GameResultModel();
            $game_result_info = $game_result_obj->getGameResultInfo('game_result_id = '.$params['game_result_id'],'issue,addtime');
            //当前这一期已用金额
            $use_money = $bet_log_obj
                ->where('user_id='.$this->user_id.' and game_result_id = '.$params['game_result_id'])
                ->sum('total_bet_money');

            self::returnResult(0,[
                'nickname'  =>  $user_info['nickname'],
                'mobile'  =>  $user_info['mobile'],
                'left_money'  =>  $user_info['left_money'],
                'use_money'  =>  $use_money?:0,
                'issue'  =>  $game_result_info['issue'],
                'addtime'  =>  date('Y-m-d H:i:s',$game_result_info['addtime']),

            ]);

        } catch (Exception $exception) {
            self::returnResult(-1,'','获取用户信息失败');
        }

    }

    //最新12笔下单信息
    public function lastLiuhecaiList($params)
    {
        $bet_log_obj = new BetLogModel();
        try{
            $bet_log_obj->setLimit(12);
            $bet_log_list = $bet_log_obj->getBetLogList('addtime,bet_json,total_bet_money','user_id = '.$this->user_id.' and game_result_id = '.$params['game_result_id'],'addtime desc ');
            if ($bet_log_list) {
                foreach ($bet_log_list as &$value) {
                    $value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
                }
            }
            self::returnResult(0,$bet_log_list?:[]);
        } catch (Exception $exception) {
            self::returnResult(-1,'','获取数据失败');
        }
    }

    //获取最近7天的下注时间
    public function getLastSevenList($params)
    {
        $weekarray=array("日","一","二","三","四","五","六");
        $bet_log_obj = new BetLogModel();
        $result_arr = [];
        //0点时间戳
        $zero_time = strtotime(date('Y-m-d'));
        for ($i=0;$i<7;$i++) {
            $result_arr[] = ['time' => $zero_time];
            $zero_time-=86400;
        }
        try{
            foreach ($result_arr as $key => &$value) {
                $info = $bet_log_obj
                    ->query('select SUM( tuishui_money ) AS tuishui_sum ,SUM( total_after_money ) AS win_sum ,SUM( total_bet_money ) AS bet_sum from tp_bet_log where addtime >='
                        .$value['time']
                        .' and addtime <' .($value['time']+86400)
                        .' and user_id = ' .$this->user_id
                        .' and game_type_id = ' .$params['game_type_id']);
                $info = $info[0];
//            echo json_encode($info);die;
            $order_num = $bet_log_obj->where('addtime >='.$value['time']
                .' and addtime <' .($value['time']+86400)
                .' and user_id = ' .$this->user_id
                .' and game_type_id = ' .$params['game_type_id'])->count();
            $value['win_sum'] = ($info['win_sum']-$info['bet_sum'])?:0;//输赢后结果

            $value['tuishui_money'] = $info['tuishui_sum']?:0;//退水金额
            $value['total_bet_money'] = $info['bet_sum']?:0;//总下注金额
            $value['order_num'] = $order_num?:0;//总下注次数
            $value['tuishui_result'] = $value['win_sum']+$value['tuishui_money'];//退税后结果
                $value['time'] = date("m-d",$value['time'])."星期".$weekarray[date("w",$value['time'])];
            }

            self::returnResult(0,$result_arr?:[]);
        } catch (Exception $exception) {
            self::returnResult(-1,'','获取数据失败');
        }
    }

    public function getLastSevenListResult($params)
    {
        $weekarray=array("日","一","二","三","四","五","六");
        $game_result_obj = new GameResultModel();
        $game_result_obj->setLimit(7);
        try{
            $json = '[{"issue":"912219771","result":"2,3,4,5,6,7,10"}]';
            $result_arr = $game_result_obj->getGameResultList('issue,result,addtime','type = 10 and is_open =1','addtime desc');
            foreach ($result_arr as $key => &$value) {
                $arr = explode(',',$value['result']);
                $sum = 0;
                foreach ($arr as $value1) {
                    $sum+=$value1;
                }
                $value['sum'] = $sum;
                $tema = array_pop($arr);
                switch (GameLogModel::check_danshuang($tema)){
                    case 1:
                        $value['danshuang'] = '单';
                        break;
                    case 2:
                        $value['danshuang'] = '双';
                        break;
                    case 3:
                        $value['danshuang'] = '和';
                        break;
                }
//                echo json_encode($value);die;
                switch (GameLogModel::check_daxiao($tema)){
                    case 1:
                        $value['te_daxiao'] = '小';
                        break;
                    case 2:
                        $value['te_daxiao'] = '大';
                        break;
                    case 3:
                        $value['te_daxiao'] = '和';
                        break;
                }

                switch (GameLogModel::check_zonghedaxiao($value['result'])){
                    case 1:
                        $value['zong_daxiao'] = '小';
                        break;
                    case 2:
                        $value['zong_daxiao'] = '大';
                        break;

                }
                switch (GameLogModel::check_zonghedanshuang($value['result'])){
                    case 1:
                        $value['zong_danshuang'] = '单';
                        break;
                    case 2:
                        $value['zong_danshuang'] = '双';
                        break;

                }
                $value['te_shengxiao'] = BetLogModel::getXiaoname($tema%13);
                switch (GameLogModel::check_heshudanshuang($value['result'])){
                    case 1:
                        $value['heshu_danshuang'] = '单';
                        break;
                    case 2:
                        $value['heshu_danshuang'] = '双';
                        break;
                    case 3:
                        $value['heshu_danshuang'] = '和';
                        break;

                }
                switch (GameLogModel::check_heshudaxiao($value['result'])){
                    case 1:
                        $value['heshu_daxiao'] = '大';
                        break;
                    case 2:
                        $value['heshu_daxiao'] = '小';
                        break;
                    case 3:
                        $value['heshu_daxiao'] = '和';
                        break;

                }
                for ($i=1;$i<7;$i++) {
                    $value['xiao_'.$i] =BetLogModel::getXiaoname($arr[$i-1]%13); ;
                }
                switch (GameLogModel::check_sebo($tema)){
                    case 1:
                        $value['heshu_sebo'] = '红波';
                        break;
                    case 2:
                        $value['heshu_sebo'] = '蓝波';
                        break;
                    case 3:
                        $value['heshu_sebo'] = '绿波';
                        break;
                    case 4:
                        $value['heshu_sebo'] = '和局';
                        break;

                }
                $value['week'] = $weekarray[date("w",$value['addtime'])];
                $value['time'] = date("Y-m-d",$value['addtime']);
//                echo json_encode($value);
            }

            self::returnResult(0,$result_arr?:[]);
        } catch (Exception $exception) {
            self::returnResult(-1,'','获取数据失败');
        }
    }

    //最新六合彩信息
    public function newLiuhecai($params)
    {
        $game_log_obj = new GameLogModel();
        $game_type_obj = new GameTypeModel();
        $game_result_obj = new GameResultModel();
        $game_log_info = $game_log_obj->where('game_type_id =' . $params['game_type_id'])
            ->field('game_result_id,part_one_result,part_two_result,part_three_result,part_four_result,part_five_result,part_six_result,result,ex_result')
            ->order('addtime DESC')->find();
        $game_result_info = $game_result_obj->getGameResultInfo('game_result_id =' . $game_log_info['game_result_id'], 'result,issue') ?: [];
        $start_time = strtotime('-3 day');  //todo 2019/10/18 客户反馈只展示三天内的开奖数据
        $game_result_obj->setLimit(1);
        $where = 'type =11 AND addtime >='.$start_time;
        $game_result_list = $game_result_obj->getGameResultList('game_result_id,issue,addtime', $where, 'game_result_id DESC');
        $game_result_info['netx_isuse'] = $game_result_list[0]['issue'];
        $game_result_info['game_result_id'] = $game_result_list[0]['game_result_id'];
        $game_result_info['netx_addtime'] = $game_result_list[0]['addtime'];
        self::returnResult(0,$game_result_info);
    }
    //最新六合彩会员信息
    public function liuhecaiVip()
    {

        $water_obj = new WaterModel();
        $water_list = $water_obj->field('name,a,b,c,d,limit,high_limit,low_limit')->select();
        self::returnResult(0,$water_list);
        $arr = [
            [
                'name'  =>  '特码A',
                'a'   =>  '0.1',
                'b'   =>  '0.2',
                'c'   =>  '0.3',
                'd'   =>  '0.4',
                'limit'   =>  '1000',
                'high_limit'   =>  '10000',
                'low_limit'   =>  '100',
            ],
            [
                'name'  =>  '特码B',
                'a'   =>  '0.1',
                'b'   =>  '0.2',
                'c'   =>  '0.3',
                'd'   =>  '0.4',
                'limit'   =>  '1000',
                'high_limit'   =>  '10000',
                'low_limit'   =>  '100',
            ],
            [
                'name'  =>  '色波',
                'a'   =>  '0.1',
                'b'   =>  '0.2',
                'c'   =>  '0.3',
                'd'   =>  '0.4',
                'limit'   =>  '1000',
                'high_limit'   =>  '10000',
                'low_limit'   =>  '100',
            ]
        ];
        self::returnResult(0,$arr);
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
            'bindQQ' => array(
                array(
                    'field' => 'qq',
                    'required' => false,
                    'type' => 'string',
                ),
                array(
                    'field' => 'verify_code',
                    'required' => false,
                    'type' => 'string',
                ),
            ),
            'getRealInfo' => array(
                array(
                    'field' => 'user_token',
                    'type' => 'string',
                ),
            ),
            'getLiuhecaiUser' => array(
                array(
                    'field' => 'game_result_id',
                    'type' => 'int',
                ),
            ),
            'lastLiuhecaiList' => array(
                array(
                    'field' => 'game_result_id',
                    'type' => 'int',
                ),
            ),
            'getLastSevenList' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
            ),
            'getLastSevenListResult' => array(
                array(
                    'field' => 'game_result_id',
                    'type' => 'int',
                ),
            ),
            'newLiuhecai' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
            ),
            'isReceivedRedPacket' => array(
                array(
                    'field' => 'red_packet_id',
                    'required' => false,
                    'type' => 'int',
                ),

            ),
            'accountList' => array(
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
            'sendEmail' => array(
                array(
                    'field' => 'email',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'checkVerifyCode' => array(
                array(
                    'field' => 'verify_code',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'giftPasswordInfo' => array(
                array(
                    'field' => 'user_gift_id',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'cancelRedPacket' => array(
                array(
                    'field' => 'red_packet_id',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'packetLogList' => array(
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
            'redPacketList' => array(
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
            'returnLogList' => array(
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
            'sendRedPacket' => array(
                array(
                    'field' => 'type',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'num',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'total_money',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'title',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
                array(
                    'field' => 'code',
                    'required' => true,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'accessBean' => array(
                array(
                    'field' => 'type',
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
                    'field' => 'bank_password',
                    'required' => false,
                    'miss_code' => 41006,
                    'func_code' => 47006,
                ),
            ),
            'myGiftList' => array(
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
            'userGiftList' => array(
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
            'rewardList' => array(
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
            'levelList' => array(
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
            'loginLog' => array(
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
            'personSwitch' => array(
                array(
                    'field' => 'switch',
                    'type' => 'string',
                ),
            ),
            'loginSwitch' => array(
                array(
                    'field' => 'switch',
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
                array(
                    'field' => 'again_password',
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
                    'field' => 'again_password',
                    'required' => true,
                    'miss_code' => 41006,
                ),
                array(
                    'field' => 'nickname',
                    'type' => 'string',
                    'required' => true,
                ),
                array(
                    'field' => 'code',
                    'type' => 'string',
                    'required' => true,
                ),
                array(
                    'field' => 'parent_id',
                    'type' => 'string',
                    'required' => false,
                )
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
                ),
                array(
                    'field' => 'email',
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40007,
                    'miss_code' => 41007,
                    'type_code' => 45007,
                ),
                array(
                    'field' => 'mobile',
                    'type' => 'string',
                    'required' => false,
                    'len_code' => 40003,
                    'miss_code' => 41003,
                    'type_code' => 45003,
                ),

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
            'sendLoginCode' => array(),
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
                array(
                    'field' => 'type',
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
            'changeMobileWap' => array(
                array(
                    'field' => 'is_wap',
                ),
            ),
            'exChangeExp' => array(
                array(
                    'field' => 'bean',
                ),
            ),
        );

        return $params[$func_name];
    }
}

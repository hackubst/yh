<?php
/**
 * 游戏类别模型类
 * table_name = tp_game_type
 * py_key = game_type_id
 */

class GameTypeModel extends Model
{

    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化游戏类别id
     */
    public function GameTypeModel()
    {
        parent::__construct('game_type');
    }

    const BAO = 1;  //豹
    const SHUN = 2; //顺
    const DUI = 3; //对
    const BAN = 4; //半
    const ZA = 5;  //杂

    const SMALL = 1;  //小
    const BIG = 2; //大

    const SIGNLE = 1; //单
    const DOUBLE = 2; //双

    const LONG = 1; //龙
    const HU = 2; //虎
    const HE = 3; //和

    const ZHUANG = 1; //庄
    const XIAN = 2; //闲
    const PING = 3; //和
    const DA = 4;//大
    const XIAO = 5;//小
    const ZHUANGDUI = 6;//庄对
    const XIANDUI = 7;//闲对
    const RONDDUI = 8;//任意对
    const DOUBLEDUI = 9;//完美对


    const JIN = 1;//金
    const MU = 2; //木
    const SHUI = 3; //水
    const HUO = 4; //火
    const TU = 5; //土

    const CHUN = 1;//春
    const XIA = 2; //夏
    const QIU = 3; //秋
    const DONG = 4; //冬

    const SHUIPING = 1;//水瓶
    const SHUANGYU = 2;//双鱼
    const BAIYANG = 3;//白羊
    const JINGNIU = 4;//金牛
    const SHUANGZI = 5;//双子
    const JUXIE = 6;//巨蟹
    const SHIZI = 7;//狮子
    const CHUNV = 8;//处女
    const TIANCHENG = 9;//天秤
    const TIANXIE = 10;//天蝎
    const SHESHOU = 11;//射手
    const MOJIE = 12;//摩羯
    //

    const SHU = 1;//鼠
    const NIU = 2;//牛
    const HU1 = 3;//虎
    const TU1 = 4;//兔
    const LONG1 = 5;//龙
    const SHE = 6;//蛇
    const MA = 7;//马
    const YANG = 8;//羊
    const HOU = 9;//猴
    const JI = 10;//鸡
    const GOU = 11;//狗
    const ZHU = 12;//猪


    const DANDAN28 = 1;//蛋蛋28
    const DANDAN36 = 2;//蛋蛋36
    const DANDANWAIWEI = 3;//蛋蛋外围
    const DANDANDINGWEI = 4;//蛋蛋定位
    const DANDAN28GUDING = 5;//蛋蛋28固定
    const DANDANBAIJIALE = 6;//蛋蛋百家乐
    const DANDANBAIJIALENEW = 7;//新蛋蛋百家乐
    const DANDANXINGZUO = 8;//蛋蛋星座
    const DANDAN16 = 9;//蛋蛋16
    const DANDAN11 = 10;//蛋蛋11
    const BEIJING28 = 11;//北京28
    const BEIJING11 = 12;//北京11
    const BEIJING16 = 13;//北京16
    const BEIJING36 = 14;//北京36
    const BEIJING28GUDING = 15;//北京28固定
    const PK10 = 16;//PK10
    const PK22 = 17;//PK22
    const PKGUANJUN = 18;//PK冠军
    const PKLONGHU = 19;//PK龙虎
    const PKGUANYAJUN = 20;//PK冠亚军
    const JIANADA11 = 21;//加拿大11
    const JIANADA16 = 22;//加拿大16
    const JIANADA28 = 23;//加拿大28
    const JIANADA36 = 24;//加拿大36
    const JIANADADINGWEI = 25;//加拿大定位
    const JIANADAWAIWEI = 26;//加拿大外围
    const JIANADA28GUDING = 27;//加拿大28固定
    const JIANADABAIJIALE = 28;//加拿大百家乐
    const JIANADABAIJIALENEW = 29;//新加拿大百家乐
    const JIANADAXINGZUO = 30;//加拿大星座
    const HANGUO28 = 31;//韩国28
    const HANGUO16 = 32;//韩国16
    const HANGUO36 = 33;//韩国36
    const HANGUO10 = 34;//韩国10
    const HANGUOWAIWEI = 35;//韩国外围
    const HANGUODINGWEI = 36;//韩国定位
    const TENGXUNFFC = 37;//腾讯分分彩
    const TENGXUN28 = 38;//腾讯28
    const TENGXUNBAIJIALE = 39;//腾讯百家乐
    const TENGXUNXINGZUO = 40;//腾讯星座
    const TENGXUN16 = 41;//腾讯16
    const TENGXUN11 = 42;//腾讯11
    const CQSSC = 43;//重庆时时彩
    const HANGUO28GUDING = 44;//韩国28固定
    const BTBONE28 = 45;//比特币1分28
    const BTBONESAICHE = 46;//比特币1分赛车
    const BTBTWO28 = 47;//比特币1.5分28
    const BTBTWOSAICHE = 48;//比特币1.5分赛车
    const BTBTHREE28 = 49;//比特币3分28
    const BTBTHREESAICHE = 50;//比特币3分赛车

    //new
    const BEIJINGSAICHE = 51;//北京赛车
    const FEITING10 = 52;//飞艇10
    const FEITING22 = 53;//飞艇22
    const FEITINGYAJUN = 56;//飞艇亚军
    const FEITINGGUANJUN = 54;//飞艇冠军
    const FEITINGFEIHU = 55;//飞艇龙虎
    const FEITINGSAICHE = 57;//飞艇赛车

    //群玩法2.8
    const JIANADAQUN = 58;//加拿大群
    const DANDANQUN = 59;//蛋蛋群
    const BEIJINGQUN = 60;//北京群
    const HANGUOQUN = 61;//韩国群

    //香港六合彩
    const TEMA = 62;//特码
    const LIANGMIAN = 64;//两面
    const SEBO = 65;//色波
    const TEXIAO = 66;//特肖
    const HEXIAO = 67;//合肖
    const TEMATOUWEISHU = 68;//特码头尾数
    const ZHENGMA = 69;//正码
    const ZHENGMATE = 70;//正码特
    const ZHENGMA16 = 71;//正码1-6
    const WUXING = 72;//五行
    const PINGTEYIXIAOWEI = 73;//平特一肖尾数
    const ZHENGXIAO = 74;//正肖
    const SEBO7 = 75;//7色波
    const ZONGXIAO = 76;//总肖
    const ZIXUANBUZHONG = 77;//自选不中
    const LIANXIAOLIANWEI = 78;//连肖连尾
    const LIANMA = 79;//连码





    /**
     * 获取游戏类别信息
     * @author 姜伟
     * @param int $game_type_id 游戏类别id
     * @param string $fields 要获取的字段名
     * @return array 游戏类别基本信息
     * @todo 根据where查询条件查找游戏类别表中的相关数据并返回
     */
    public function getGameTypeInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改游戏类别信息
     * @author 姜伟
     * @param array $arr 游戏类别信息数组
     * @return boolean 操作结果
     * @todo 修改游戏类别信息
     */
    public function editGameType($where = '', $arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');

        return $this->where($where)->save($arr);
    }

    /**
     * 添加游戏类别
     * @author 姜伟
     * @param array $arr 游戏类别信息数组
     * @return boolean 操作结果
     * @todo 添加游戏类别
     */
    public function addGameType($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除游戏类别
     * @author 姜伟
     * @param int $game_type_id 游戏类别ID
     * @param int $opt ,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delGameType($game_type_id, $opt = false)
    {
        if (!is_numeric($game_type_id)) return false;
        if ($opt) {
            return $this->where('game_type_id = ' . $game_type_id)->delete();
        } else {
            return $this->where('game_type_id = ' . $game_type_id)->save(array('isuse' => 2));
        }

    }

    /**
     * 根据where子句获取游戏类别数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的游戏类别数量
     * @todo 根据where子句获取游戏类别数量
     */
    public function getGameTypeNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询游戏类别信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 游戏类别基本信息
     * @todo 根据SQL查询字句查询游戏类别信息
     */
    public function getGameTypeList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }
    public function getGameTypeAll($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->select();
    }

    public function getGameTypeLimitList($fields = '', $where = '', $orderby = '', $group = '', $start = 0, $limit = 0)
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit($start, $limit)->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where
     * @param  string $field
     * @return void
     */
    public function getGameTypeField($where, $field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取游戏类别列表页数据信息列表
     * @author 姜伟
     * @param array $game_type_list
     * @return array $game_type_list
     * @todo 根据传入的$GameType_list获取更详细的游戏类别列表页数据信息列表
     */
    public function getListData($game_type_list)
    {
        $series_obj = new GameSeriesModel();
        foreach ($game_type_list as $k => &$v) {
            $v['game_series_name'] = $series_obj->getGameSeriesField('game_series_id =' . $v['game_series_id'], 'game_series_name');
        }
        return $game_type_list;
    }

    /**
     * 获取游戏类别列表页数据信息列表
     * @author 姜伟
     * @param array $game_type_list
     * @return array $game_type_list
     * @todo 根据传入的$GameType_list获取更详细的游戏类别列表页数据信息列表
     */
    public function getWinLossData($game_type_list,$user_id,$where1)
    {
        $user_obj = new UserModel();
//        $bet_log_obj = new BetLogModel();
        foreach ($game_type_list as $k => $v) {
            $game_type_list[$k]['last_money'] = feeHandle($v['last_money']);

//            $bet_log_info = $bet_log_obj
//            ->field('SUM(total_after_money - total_bet_money) AS last_money')
//            ->where('game_type_id = '.$v['game_type_id'].' AND user_id ='.$user_id.' AND is_open = 1'.$where1)
//            ->find();
            $user_info = $user_obj->getUserInfo('id','user_id ='.$v['user_id']);
//            if($bet_log_info['last_money'] == 0)
//            {
//                unset($game_type_list[$k]);
//            }else{
//                $game_type_list[$k]['last_money'] = feeHandle($bet_log_info['last_money']) ? : 0;
//                $game_type_list[$k]['nickname'] = $user_info['nickname'] ? : '';
//            }
            $game_type_list[$k]['id'] = $user_info['id'];
        }

//        $game_type_list = array_merge($game_type_list);
        return $game_type_list;
    }

    /**
     * 获取游戏类别列表页数据信息列表
     * @author 姜伟
     * @param array $game_type_list
     * @return array $game_type_list
     * @todo 根据传入的$GameType_list获取更详细的游戏类别列表页数据信息列表
     */
    public function getWinLossAllData($game_type_list,$count,$start_time)
    {
        $start = $start_time;
        $bet_log_obj = new BetLogModel();
        $user_obj = new UserModel();
        $account_obj = new AccountModel();
        $return_log_obj = new ReturnLogModel();
        $rank_list_obj = new RankListModel();            // RELIEF
        $user_gift_password_obj = new UserGiftPasswordModel();
        $deposit_apply_obj = new DepositApplyModel();
        for ($i=0; $i < $count; $i++) { 

            $start_time = $start + $i*24*3600;
            $end_time = $start_time + 24*3600;
            $where = 'addtime >='.$start_time.' AND addtime <'.$end_time;

            $reg_number = $user_obj->getUserNum('reg_time >='.$start_time.' AND reg_time <='.$end_time);
            $data['reg_number'] = feeHandle($reg_number)? : 0;
            //输赢
            $bet_log_info = $bet_log_obj->getBetLogInfo($where,'SUM(total_after_money - total_bet_money) AS gain_money');
            $gain_money = $bet_log_info['gain_money'];
            $data['gain_money'] = feeHandle($bet_log_info['gain_money']) ? : 0;

            //充值统计
            $account_info = $account_obj->getAccountInfo($where.' AND operater <> 1 AND change_type ='.AccountModel::RECHARGE,'SUM(amount_in) AS recharge');
            $data['recharge'] = feeHandle($account_info['recharge']) ? : 0;
            //救济分统计
            $account = $account_obj->getAccountInfo($where.' AND change_type ='.AccountModel::RELIEF,'SUM(amount_in) AS relief');
            $data['relief'] = feeHandle($account['relief']) ? : 0;

            //返点分(每周盈亏)
            $return_log_info = $return_log_obj->getReturnLogInfo($where.' AND return_type= 1','SUM(money) AS return_money');
            $data['return_money'] = feeHandle($return_log_info['return_money']) ? : 0;
            //排名奖
            $rank_list_info = $rank_list_obj->getRankListInfo($where.' AND user_id <> 0','SUM(reward) AS reward');
            $data['reward'] = feeHandle($rank_list_info['reward']) ? : 0;

            //首充奖励
            $return_log_info = $return_log_obj->getReturnLogInfo($where.' AND return_type= 2','SUM(money) AS recharge_money');
            $data['recharge_money'] = feeHandle($return_log_info['recharge_money']) ? : 0;
            //活动奖励（领取救济、排行榜奖励、首充返利、下线投注返利、每周盈亏返利总和）
//            $return_log_info = $return_log_obj->getReturnLogInfo($where,'SUM(money) AS activity_reward');
            $where1 = ' AND change_type in (1,2,6,26,27,28,29)';
            $activity_reward_info = $account_obj->getAccountInfo($where.$where1,'SUM(amount_in) AS activity_reward');
//            $data['activity_reward'] = $account['relief'] + $return_money + $rank_list_info['reward'] + $recharge_money ? : 0;
            $data['activity_reward'] = feeHandle($activity_reward_info['activity_reward']) ? : 0;
            //总分、银行资金
            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('SUM(left_money + frozen_money) AS left_money','role_type = 3 AND is_enable = 1');
            $user_obj = new UserModel();
            $agent_info = $user_obj->getUserInfo('SUM(left_money) AS frozen_money','role_type = 4 AND is_enable = 1');

            $data['left_money'] = feeHandle($user_info['left_money']) ? : 0;
            $data['frozen_money'] = feeHandle($agent_info['frozen_money']) ? : 0;

            foreach ($game_type_list as $k => $v) {

                $bet_log_info = $bet_log_obj->getBetLogInfo('is_open = 1 AND game_type_id = '.$v['game_type_id'].' AND '.$where,'SUM(total_after_money - total_bet_money) AS last_money');
                $game_type_list[$k]['last_money'] = feeHandle($bet_log_info['last_money']) ? : 0;
            }
            // $game_type_list['start_time'] = date('Y-m-d',$start_time);

            $data['gain_money_two'] = feeHandle($gain_money + $activity_reward_info['activity_reward']);
            $data['activity_reward'] = feeHandle($activity_reward_info['activity_reward']) ?:0;
            $data['start_time'] = date('Y-m-d',$start_time);

            //充卡
            $data['user_gift_num'] = feeHandle($user_gift_password_obj->getUserGiftPasswordInfo($where,'SUM(money) AS user_gift_num')['user_gift_num']) ? : 0;

            //兑奖
            $data['user_gift_password_num'] = feeHandle($user_gift_password_obj->getUserGiftPasswordInfo('use_time >='.$start_time.' AND use_time <='.$end_time,'sum(money) as user_gift_password_num')['user_gift_password_num']) ?:0;
            //代理充
            $data['daily_recharge'] = $account_obj->getAccountInfo($where.' AND user_id != 1 AND change_type ='.$account_obj::ADDMONEY,'SUM(amount_in) as amount_in')['amount_in']/1000 ?:0;
            //代理收
            $data['daily_recharge_out'] = $account_obj->getAccountInfo($where.' AND operater != 1 AND change_type ='.$account_obj::RECHARGE,'SUM(amount_in) as amount_in')['amount_in']/1000 ?:0;

            //代理提现
            $data['daily_deposit'] = $deposit_apply_obj->getDepositApplyInfo('state = 1 AND pass_time >='.$start_time.' AND pass_time <='.$end_time,'SUM(real_get_money) as money')['money'] ? : 0;

            //代理总充
            $data['total_recharge'] = $account_obj->getAccountInfo('user_id != 1 AND change_type ='.$account_obj::ADDMONEY,'SUM(amount_in) as amount_in')['amount_in']/1000 ?:0;
            //代理总提现
            $data['total_deposit'] = $deposit_apply_obj->getDepositApplyInfo('state = 1','SUM(real_get_money) as money')['money'] ? : 0;

            $last['data'] = $data;
            $last['game_type_list'] = $game_type_list;

            $last_list[] = $last;
        }
        krsort($last_list);
        return $last_list;
    }

    /**
     * 计算人数
     * @param $result_id
     * @param $game_type_id
     * @date: 2019/3/12
     * @author: hui
     * @return array
     */
    public function calculatePeople($result_id, $game_type_id)
    {
        $bet_log_obj = new BetLogModel();
        $real_bet_num = $bet_log_obj->where('game_result_id =' . $result_id .' AND game_type_id ='.$game_type_id)->count('DISTINCT user_id');
        $real_win_num = $bet_log_obj->where('game_result_id =' . $result_id . ' AND is_win = 1  AND game_type_id ='.$game_type_id)->count('DISTINCT user_id');
        $game_type_info = $this->getGameTypeInfo('game_type_id =' . $game_type_id,
            'max_bet_num,min_bet_num,max_win_num,min_win_num');
        $bet_num = rand($game_type_info['min_bet_num'], $game_type_info['max_bet_num']) + $real_bet_num;
        $win_num = rand($game_type_info['min_win_num'], $game_type_info['max_win_num']) + $real_win_num;


        return array(
            'real_bet_num' => $real_bet_num?:0,
            'real_win_num' => $real_win_num?:0,
            'bet_people' => $bet_num?:0,
            'win_people' => $win_num?:0,
        );
    }

    /**
     * 算计奖金总数
     * @param $result_id
     * @param $game_type_id
     * @date: 2019/3/14
     * @author: hui
     * @return mixed
     */
    public function calculateMoney($result_id, $game_type_id)
    {
        $bet_log_obj = new BetLogModel();
        $real_bet_money = $bet_log_obj->where('game_result_id =' . $result_id .' AND game_type_id ='.$game_type_id)->getField('sum(total_bet_money)');
        $real_win_money = $bet_log_obj->where('game_result_id =' . $result_id .' AND game_type_id ='.$game_type_id)->getField('sum(total_after_money)');
        $game_type_info = $this->getGameTypeInfo('game_type_id =' . $game_type_id,
            'base_bonus_pools,max_bet_reward,min_bet_reward,max_win_reward,min_win_reward');

        $base_bonus_pools = $game_type_info['base_bonus_pools'] * rand(100000000,110000000) / 100000000 ;
        $bet_money = $base_bonus_pools + $real_bet_money;
        $bet_reward = rand($game_type_info['min_bet_reward'], $game_type_info['max_bet_reward']) + $real_bet_money;
        $win_reward = rand($game_type_info['min_win_reward'], $game_type_info['max_win_reward']) + $real_win_money;
        return array(
            'bet_money' => $bet_money?:0,
            'bet_reward' => $bet_reward?:0,
            'win_reward' => $win_reward?:0,
            'real_bet_money' => $real_bet_money?:0,
            'real_win_money' => $real_win_money?:0,
        );
    }

    //转换格式
    public function exCahngeData($bet_json){

        $new_bet_json = array();
        foreach ($bet_json['rate'] as $k => $v) {
            if($v)
            {
                $new_bet_json[$k]['key'] =  intval($bet_json['key'][$k]);
                $new_bet_json[$k]['rate'] =  $v;
            } 
        }
        array_multisort(array_column($new_bet_json,'key'),SORT_ASC,$new_bet_json);

        return json_encode($new_bet_json);
    }

    //获取近7天各游戏类型盈利
    public function getDailyData($game_type_list,$user_id){

        foreach ($game_type_list as $k => $v) {
            $daily_time = 86400;//一天的时间戳长度
            $bet_log_obj = new BetLogModel();

            $start_time = strtotime('-6 day', strtotime(date('Y-m-d')));
            $end_time = time();
            $where = 'addtime > ' . $start_time . ' AND addtime < ' . $end_time . ' AND user_id =' . $user_id . ' AND is_open = 1';

            //获取七日参加总期数
            $bet_log_info = $bet_log_obj->getBetLogInfo($where, 'count(*) as all_issue');
            $all_issue = $bet_log_info['all_issue'] ?: 0;

            //分别查询出今日、一天前、两天前、三天前、四天前、五天前、六天前的游戏统计
            for ($i = 0; $i < 7; $i++) {

                $start_time = strtotime(date('Y-m-d')) - $daily_time * $i;
                $end_time = $start_time + $daily_time;

                $where = 'addtime > ' . $start_time . ' AND addtime < ' . $end_time . ' AND game_type_id = ' . $v['game_type_id'] . ' AND user_id =' . $user_id . ' AND is_open = 1';
                //获取盈利
                $bet_log_info = $bet_log_obj->getBetLogInfo($where, 'count(*) as total_issue,sum(total_after_money) - sum(total_bet_money) as win_loss');
// if($i == 3) {dump($bet_log_obj->getLastSql());die;}
                $game_type_list[$k]['daily_data'][] = $bet_log_info['win_loss'] ?: 0;

                $game_type_list[$k]['issue_data'][] = $bet_log_info['total_issue'] ?: 0;
            }

            //当天该游戏类型的总盈利
            $game_type_list[$k]['win_loss_count'] = array_sum($game_type_list[$k]['daily_data']) ?: 0;
            //当天该游戏类型的总期数
            $game_type_list[$k]['issue_count'] = array_sum($game_type_list[$k]['issue_data']) ?: 0;
            //当天该游戏类型的期数占总期数的比例
            $game_type_list[$k]['issue_rate'] = round($game_type_list[$k]['issue_count'] / $all_issue * 100, 2);

        }

        //计算总计
        $daily_data = array_column($game_type_list, 'daily_data');

        for ($i = 0; $i < 7; $i++) {
            $now = array_column($daily_data, $i);
            $last[] = array_sum($now);
        }
        //计算总期数
        $issue_data = array_column($game_type_list, 'issue_data');

        for ($i = 0; $i < 7; $i++) {
            $now = array_column($issue_data, $i);
            $issue_last[] = array_sum($now);
        }
        $now_array['game_type_id'] = -1;
        $now_array['game_type_name'] = '总数';
        $now_array['daily_data'] = $last ?: array();
        $now_array['issue_data'] = $issue_last ?: array();
        $now_array['win_loss_count'] = array_sum($now_array['daily_data']);
        $now_array['issue_count'] = array_sum($now_array['issue_data']);
        $now_array['issue_rate'] = 100;
        array_push($game_type_list, $now_array);
        return $game_type_list;
    }

    /**
     * 新的获取盈利数据
     * @param $game_type_list
     * @param $user_id
     * @return array
     * @author yzp
     * @Date:  2019/7/25
     * @Time:  15:46
     */
    public function getDailyDataNew($game_type_list,$user_id){

        $daily_bet_obj = new DailyBetModel();
        $bet_log_obj = new BetLogModel();
        $start_time = strtotime(date('Y-m-d',strtotime("today")));
        $end_time = time();
        $where['addtime'] = array(array('EGT',$start_time),array('ELT',$end_time),'and');
        $where['is_open'] = 1;
        $where['user_id'] = $user_id;
        $last = array();
        $sum_data = array();
        //查询今日的
        foreach ($game_type_list as $k => &$v) {
            $where['game_type_id'] = $v['game_type_id'];
            $bet_log_info = $bet_log_obj->getBetLogInfo($where,'COUNT(bet_log_id) AS issue_num,SUM(total_after_money - total_bet_money) AS win_loss');
            $v['win_loss'] = $bet_log_info['win_loss'] ? : 0;
            $v['issue_num'] = $bet_log_info['issue_num'] ? : 0;
        }unset($v);
        if($game_type_list){
            $sum_data['game_type_id'] = 0;
            $sum_data['win_loss'] = array_sum(array_column($game_type_list,'win_loss'));
            $sum_data['issue_num'] = array_sum(array_column($game_type_list,'issue_num'));
            array_push($game_type_list,$sum_data);
        }
        $count = count($game_type_list);
        $base_data = $game_type_list;
        $last[] = $game_type_list ?: array();

        foreach ($base_data as $ba => &$se)
        {
            $se['win_loss'] = 0;
            $se['issue_num'] = 0;
        }unset($se);
        $base_data = $base_data ? : array();
        //查询历史的
        for ($i =0 ;$i<7;$i++)
        {
            $time  = time() - 24*3600*$i;
            $now_time = strtotime(date("Y-m-d",strtotime("-1 day",$time)));
            $where = 'user_id = '.$user_id.' AND addtime ='.$now_time;
            $bet_type_json = $daily_bet_obj->getDailyBetInfo($where,'bet_type_json')['bet_type_json'] ? : '';

            $new_data = $base_data;
            $now_data = json_decode($bet_type_json,true) ? : array();
            if($now_data)
            {
                $now_type_id = array_column($now_data,'game_type_id');
                $now_type_id = array_flip($now_type_id);
                foreach ($new_data as $k => $v)
                {
                    if($v['game_type_id'] == 0)
                    {
                        $new_data[$k]['win_loss'] = array_sum(array_column($new_data,'win_loss'));
                        $new_data[$k]['issue_num'] = array_sum(array_column($new_data,'issue_num'));
                    }else{
                        $kkk = $now_type_id[$v['game_type_id']];
                        $new_data[$k]['win_loss'] = $now_data[$kkk]['win_loss'] ? : 0;
                        $new_data[$k]['issue_num'] = $now_data[$kkk]['issue_num'] ? : 0;
                    }
                }
                $last[] = $new_data;
            }else{
                $last[] = $base_data;
            }
        }
        //算出总计
        $last_data = array();
        for ($i=0; $i < $count;$i++)
        {
            $last_data[$i]['game_type_id'] = $last[0][$i]['game_type_id'];
            for ($j = 0 ;$j < 8;$j++)
            {
                $last_data[$i]['win_loss'] += $last[$j][$i]['win_loss'];
                $last_data[$i]['issue_num'] += $last[$j][$i]['issue_num'];
            }
        }
        $last[] = $last_data;

        $game_type_obj = new GameTypeModel();
        $type_name = $game_type_obj->getGameTypeAll('game_type_name','isuse = 1','game_type_id asc');
        $type_name[] = array('game_type_name'=>'总计');

        return array(
            'type_name' => $type_name,
            'list' => $last
        );
    }


    /**
     * 整合人数 金额
     * @param $money
     * @param $people
     * @param $part
     * @return mixed
     */
    public function mixPeopleAndMoney($people, $money, $part)
    {
        $part['total_money'] = $money['bet_money'];
        $part['bet_reward'] = $money['bet_reward'];
        $part['win_reward'] = $money['win_reward'];
        $part['win_people'] = $people['win_people'];
        $part['bet_people'] = $people['bet_people'];
        //add 真实人数
        $part['real_bet_money'] = $money['real_bet_money'];
        $part['real_win_money'] = $money['real_win_money'];
        $part['real_bet_num'] = $people['real_bet_num'];
        $part['real_win_num'] = $people['real_win_num'];

        return $part;
    }
}

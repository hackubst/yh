<?php
/**
 * 活动规则模型类
 * table_name = tp_marketing_rule
 * py_key = marketing_rule_id
 */

class MarketingRuleModel extends Model
{
    // 1.每周亏损返利 2.每日首充返利 3.下线投注返利（投注流水限制）4.每日救济 5.经验换豆 6.每日排行上榜有礼
    const WEEKLOSS = 1;
    const DAILYCHARGE = 2;
    const BETTING = 3;
    const RELIEF = 4;
    const EXP = 5;
    const RANKREWARD = 6;
    const SELF_BETTING = 7;
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化活动规则id
     */
    public function MarketingRuleModel()
    {
        parent::__construct('marketing_rule');
    }

    /**
     * 获取活动规则信息
     * @author 姜伟
     * @param int $marketing_rule_id 活动规则id
     * @param string $fields 要获取的字段名
     * @return array 活动规则基本信息
     * @todo 根据where查询条件查找活动规则表中的相关数据并返回
     */
    public function getMarketingRuleInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改活动规则信息
     * @author 姜伟
     * @param array $arr 活动规则信息数组
     * @return boolean 操作结果
     * @todo 修改活动规则信息
     */
    public function editMarketingRule($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加活动规则
     * @author 姜伟
     * @param array $arr 活动规则信息数组
     * @return boolean 操作结果
     * @todo 添加活动规则
     */
    public function addMarketingRule($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除活动规则
     * @author 姜伟
     * @param int $marketing_rule_id 活动规则ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delMarketingRule($marketing_rule_id,$opt = false)
    {
        if (!is_numeric($marketing_rule_id)) return false;
        if($opt)
        {
            return $this->where('marketing_rule_id = ' . $marketing_rule_id)->delete();
        }else{
           return $this->where('marketing_rule_id = ' . $marketing_rule_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取活动规则数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的活动规则数量
     * @todo 根据where子句获取活动规则数量
     */
    public function getMarketingRuleNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询活动规则信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 活动规则基本信息
     * @todo 根据SQL查询字句查询活动规则信息
     */
    public function getMarketingRuleList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getMarketingRuleField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取活动规则列表页数据信息列表
     * @author 姜伟
     * @param array $MarketingRule_list
     * @return array $MarketingRule_list
     * @todo 根据传入的$MarketingRule_list获取更详细的活动规则列表页数据信息列表
     */
    public function getListData($marketing_rule_list)
    {
        // 每周亏损返利 2.每日首充返利 3.下线投注返利（投注流水限制）4.每日救济 5.经验换豆 6.每日排行上榜有礼
        foreach ($marketing_rule_list as $k => $v) {
            switch ($v['marketing_type']) {
                case '1':
                    $marketing_type = '每周亏损返利';
                    break;
                case '2':
                    $marketing_type = '每日首充返利';
                    break;
                case '3':
                    $marketing_type = '下线投注返利';
                    break;
                case '4':
                    $marketing_type = '每日救济';
                    break;
                case '5':
                    $marketing_type = '经验换豆';
                    break;
                case '6':
                    $marketing_type = '每日排行上榜有礼';
                    break;
                default:
                    $marketing_type = '';
                    break;
            }
            $marketing_rule_list[$k]['marketing_type'] = $marketing_type;
        }
        return $marketing_rule_list;
    }

    /**
     * 获取活动规则结果
     * @author yzp
     * @todo 根据传入的$MarketingRule_list获取活动规则结果
     */
    public function getResult($marketing_type)
    {
         // 1.每周亏损返利 2.每日首充返利 3.下线投注返利（投注流水限制）4.每日救济 5.经验换豆 6.每日排行上榜有礼
        switch ($marketing_type) {

            case MarketingRuleModel::WEEKLOSS :
                return $this->getWeekLoss();  //领取每周亏损返利
                break;
            case MarketingRuleModel::DAILYCHARGE :
                return $this->getDailyCharge();   //领取每日首充返利
                break;
            case MarketingRuleModel::BETTING :
                return $this->getBetting();   //领取下线投注返利（投注流水限制）
                break;
            case MarketingRuleModel::RELIEF :
                return $this->getRelief();   //领取每日救济
                break;
            case MarketingRuleModel::EXP :
                return $this->getExp();   //领取经验换豆
                break;
            case MarketingRuleModel::RANKREWARD :
                return $this->getRankReward();   //领取每日排行上榜有礼
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * 领取每周亏损返利
     * @author yzp
     * @todo 领取每周亏损返利，需要添加至定时脚本，每日一次
     */    
    public function getWeekLoss()
    {

        //查询活动是否存在，并且在活动时间内
        $marketing_obj = new MarketingRuleModel();
        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::WEEKLOSS,'start_time,end_time');
        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
        {
            return false;
        }

        $daily_win_obj = new DailyWinModel();
        if (date('l',time()) == 'Sunday'){
//            $start_time = strtotime('last monday');
            $start_time = strtotime('-1 week last monday');
        }else{
            $start_time=mktime(0,0,0,date('m'),date('d')-date('w')+1-7,date('Y'));
        }

        $black_user_id = $this->getBlackUser();

        $end_time = strtotime('last sunday') + 24*3600;
//        $where = 'addtime >='.$start_time.' AND addtime <'.$end_time;

        $where = ' AND addtime >='.$start_time.' AND addtime <'.$end_time.' AND user_id not in ('.$black_user_id.')';
        $bet_log_obj = new BetLogModel();
        $bet_log_list = $bet_log_obj->getBetLogListAll('user_id,SUM(total_after_money - total_bet_money) AS total','is_open = 1'.$where,'total desc','user_id');

        //取出上周亏损总计列表
//        $daily_win_list = $daily_win_obj->getDailyWinList('sum(loss) AS loss_total,sum(win) AS win_total,user_id',$where,'loss_total desc','user_id','');
        //今日时间戳
        $start_time = strtotime(date('Y-m-d',strtotime(date('Y-m-d'))));
        $end_time = time();

        $config_base_obj = new ConfigBaseModel();
        $min_loss = $config_base_obj->getConfig('min_loss');   //亏损返利下限
        $return_rate = $config_base_obj->getConfig('return_rate') ? : 0.05;   //亏损返利比例
        if(time() < 1570982400)
        {
            $return_rate = 0.07;
        }
        foreach ($bet_log_list as $k => $v) {
//            $v['total'] = 0;
//            if($v['loss_total'] - $v['win_total'] <= 0)
//            {
//                continue;
//            }
            if($v['total'] >= 0)
            {
                continue;
            }
            $v['total'] = -1 * $v['total'];
//            $v['total'] = $v['loss_total'] - $v['win_total'];  //上周亏损总计
            $return_log_obj = new ReturnLogModel();
            $return_log_info = $return_log_obj->getReturnLogInfo('addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$v['user_id'].' AND return_type ='.MarketingRuleModel::WEEKLOSS,'return_log_id');

            $money = intval($v['total'] * $return_rate/7);//亏损金额 * 亏损返利比例/7，分七天发送 

            if(!$return_log_info && $v['total'] >= $min_loss && $money >= 1)
            {
                $data = [
                    'account_id' => 0,
                    'user_id' => $v['user_id'],
                    'money' => $money, //亏损金额 * 亏损返利比例/7，分七天发送
                    'return_type' => MarketingRuleModel::WEEKLOSS,
                    'addtime' => time()
                ];
                $return_log_obj->addReturnLog($data);  //生成返利记录
                //自动存入银行
                $account_obj = new AccountModel();
//                $account_obj->AutoSaveBank($v['user_id'],$money);
                $account_obj->addAccount($v['user_id'], AccountModel::WEEKLOSS, $money, '领取上周亏损返利');

            }
        }
        // dump($daily_win_list);die;
    }

    /**
     * 有效投注流水达首充（当日第一次充值）金额的10倍返6% 达20倍返12%，需要添加至定时脚本，每日一次
     * @author yzp
     * @todo 领取每日首充返利
     */
    public function getDailyCharge()
    {
        //查询活动是否存在，并且在活动时间内
        $marketing_obj = new MarketingRuleModel();
        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::DAILYCHARGE,'start_time,end_time');

        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
        {
            return false;
        }

        $account_obj = new AccountModel();

        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;

        //查询前一天首充的数量以及用户
        $where = 'change_type = ' .AccountModel::RECHARGE.' AND addtime >='.$start_time.' AND addtime <='.$end_time;
        $account_list = $account_obj->getAccountList('account_id,user_id,amount_in', $where, 'addtime asc','','user_id');

        $config_base_obj = new ConfigBaseModel();
//        $recharge_rebate = $config_base_obj->getConfig('recharge_rebate') ? : 0.005;   //每日首充返利比例

//        $double_flow = $config_base_obj->getConfig('double_flow') ? : 1;   //流水倍数
        foreach ($account_list as $k => $v) {
            $where1 = 'daily_flow <> 0 AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id = '.$v['user_id'];
            //每日有效流水
            $daily_win_obj = new DailyWinModel();
            $daily_flow = $daily_win_obj->getDailyWinInfo($where1,'daily_flow')['daily_flow'] ? : 0;
            $return_log_obj = new ReturnLogModel();
            $return_log_info = $return_log_obj->getReturnLogInfo('account_id ='.$v['account_id'].' AND return_type ='.MarketingRuleModel::DAILYCHARGE,'return_log_id');

            if(!$return_log_info && $daily_flow >= $v['amount_in'] * 20)
            {//有效投注流水达首充（当日第一次充值）金额的10倍返6% 达20倍返12%
                $recharge_rebate = 0.12;
                $result = ceil($v['amount_in'] * $recharge_rebate) ?: 0;
                if($result)
                {
                    $data = [
                        'account_id' => $v['account_id'],
                        'user_id' => $v['user_id'],
                        'money' => $result, //充值金额 * 每日首充返利比例
                        'return_type' => MarketingRuleModel::DAILYCHARGE,
                        'addtime' => time()
                    ];
                    $return_log_obj->addReturnLog($data);  //生成返利记录
                    //自动存入银行
//                    $account_obj->AutoSaveBank($v['user_id'],$result);
                    $account_obj->addAccount($v['user_id'], AccountModel::DAILYCHARGE, $result, '领取每日首充返利');
                }

            }elseif (!$return_log_info && $daily_flow >= $v['amount_in'] * 10)
            {
                $recharge_rebate = 0.06;
                $result = ceil($v['amount_in'] * $recharge_rebate) ?: 0;
                if($result)
                {
                    $data = [
                        'account_id' => $v['account_id'],
                        'user_id' => $v['user_id'],
                        'money' => $result, //充值金额 * 每日首充返利比例
                        'return_type' => MarketingRuleModel::DAILYCHARGE,
                        'addtime' => time()
                    ];
                    $return_log_obj->addReturnLog($data);  //生成返利记录
                    //自动存入银行
                    $account_obj->AutoSaveBank($v['user_id'],$result);
                    $account_obj->addAccount($v['user_id'], AccountModel::DAILYCHARGE, $result, '领取每日首充返利');
                }
            }
        }

//        foreach ($account_list as $k => $v) {
//            $return_log_obj = new ReturnLogModel();
//            $return_log_info = $return_log_obj->getReturnLogInfo('account_id ='.$v['account_id'].' AND return_type ='.MarketingRuleModel::DAILYCHARGE,'return_log_id');
//            if(!$return_log_info && $v['amount_in'] * $recharge_rebate > 1)
//            {
//                $data = [
//                    'account_id' => $v['account_id'],
//                    'user_id' => $v['user_id'],
//                    'money' => $v['amount_in'] * $recharge_rebate, //充值金额 * 每日首充返利比例
//                    'return_type' => MarketingRuleModel::DAILYCHARGE,
//                    'addtime' => time()
//                ];
//                $return_log_obj->addReturnLog($data);  //生成返利记录
//                //自动存入银行
//                $account_obj->AutoSaveBank($v['user_id'],$v['amount_in'] * $recharge_rebate);
//
//            }
//        }
    }
    /**
     * 领取每日首充返利，需要添加至定时脚本，每日一次
     * @author yzp
     * @todo 领取每日首充返利
     */
    public function getDailyChargeOld()
    {
        //查询活动是否存在，并且在活动时间内
        $marketing_obj = new MarketingRuleModel();
        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::DAILYCHARGE,'start_time,end_time');

        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
        {
            return false;
        }

        $account_obj = new AccountModel();

        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;

        //查询前一天首充的数量以及用户
        $where = 'change_type = ' .AccountModel::RECHARGE.' AND addtime >='.$start_time.' AND addtime <='.$end_time;
        $account_list = $account_obj->getAccountList('account_id,user_id,amount_in', $where, 'addtime asc','','user_id');

        $config_base_obj = new ConfigBaseModel();
        $recharge_rebate = $config_base_obj->getConfig('recharge_rebate') ? : 0.005;   //每日首充返利比例

        $where1 = 'addtime >='.$start_time.' AND addtime <='.$end_time;
        $double_flow = $config_base_obj->getConfig('double_flow') ? : 1;   //流水倍数


        foreach ($account_list as $k => $v) {
            $where1 .= ' AND user_id = '.$v['user_id'];
            //每日有效流水
            $daily_win_obj = new DailyWinModel();
            $daily_flow = $daily_win_obj->getDailyWinInfo($where1,'daily_flow')['daily_flow'] ? : 0;

            $return_log_obj = new ReturnLogModel();
            $return_log_info = $return_log_obj->getReturnLogInfo('account_id ='.$v['account_id'].' AND return_type ='.MarketingRuleModel::DAILYCHARGE,'return_log_id');
            if(!$return_log_info && $daily_flow >= $v['amount_in'] * $double_flow)
            {
                $data = [
                    'account_id' => $v['account_id'],
                    'user_id' => $v['user_id'],
                    'money' => $v['amount_in'] * $recharge_rebate, //充值金额 * 每日首充返利比例
                    'return_type' => MarketingRuleModel::DAILYCHARGE,
                    'addtime' => time()
                ];
                $return_log_obj->addReturnLog($data);  //生成返利记录
                //自动存入银行
                $account_obj->AutoSaveBank($v['user_id'],$v['amount_in'] * $recharge_rebate);

            }
        }

//        foreach ($account_list as $k => $v) {
//            $return_log_obj = new ReturnLogModel();
//            $return_log_info = $return_log_obj->getReturnLogInfo('account_id ='.$v['account_id'].' AND return_type ='.MarketingRuleModel::DAILYCHARGE,'return_log_id');
//            if(!$return_log_info && $v['amount_in'] * $recharge_rebate > 1)
//            {
//                $data = [
//                    'account_id' => $v['account_id'],
//                    'user_id' => $v['user_id'],
//                    'money' => $v['amount_in'] * $recharge_rebate, //充值金额 * 每日首充返利比例
//                    'return_type' => MarketingRuleModel::DAILYCHARGE,
//                    'addtime' => time()
//                ];
//                $return_log_obj->addReturnLog($data);  //生成返利记录
//                //自动存入银行
//                $account_obj->AutoSaveBank($v['user_id'],$v['amount_in'] * $recharge_rebate);
//
//            }
//        }
    }

//    /**
//     * 领取下线投注返利（投注流水限制）需要添加至定时脚本，每日一次
//     * @author yzp
//     * @todo 领取下线投注返利（投注流水限制）
//     */
//    public function getBetting()
//    {
//         //查询活动是否存在，并且在活动时间内
//        $marketing_obj = new MarketingRuleModel();
//        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::BETTING,'start_time,end_time');
//
//        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
//        {
//            return false;
//        }
//
//         $account_obj = new AccountModel();
//
//        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
//        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;
//
//        //查询前一天游戏投注
//        $where = 'change_type = ' .AccountModel::BETTING.' AND addtime >='.$start_time.' AND addtime <='.$end_time;
//        $account_list = $account_obj->getAccountList('user_id,sum(amount_out) AS total', $where, 'addtime asc','','user_id');
//
//        $config_base_obj = new ConfigBaseModel();
//        $min_flow = $config_base_obj->getConfig('min_flow') ? : 10000;   //投注下限
//        $flow_rate = $config_base_obj->getConfig('flow_rate') ? : 0.0015;   //投注返利 比例
//
//        foreach ($account_list as $k => $v) {
//            //查询是否有邀请人
//            $user_obj = new UserModel($v['user_id']);
//
//            $parent_id = $user_obj->where('user_id = '.$v['user_id'])->getField('parent_id');
//
//            if(!$parent_id)
//            {
//                return false;
//            }
//
//            $return_log_obj = new ReturnLogModel();
//            $start_time = strtotime(date('Y-m-d',strtotime(date('Y-m-d'))));
//            $end_time = time();
//
//            $return_log_info = $return_log_obj->getReturnLogInfo('addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$parent_id.' AND return_type ='.MarketingRuleModel::BETTING.' AND lower_id ='.$v['user_id'],'return_log_id');
//            // dump($return_log_obj->getLastsql());die;
//            if(!$return_log_info && $v['total'] >= $min_flow)
//            {
//                $data = [
//                    'account_id' => 0,
//                    'user_id' => $parent_id,
//                    'lower_id' => $v['user_id'],
//                    'money' => $v['total'] * $flow_rate, //下级投注金额 * 投注返利比例
//                    'return_type' => MarketingRuleModel::BETTING,
//                    'addtime' => time()
//                ];
//                $return_log_obj->addReturnLog($data);  //生成返利记录
//                //自动存入银行
//                $account_obj->AutoSaveBank($parent_id,$v['total'] * $flow_rate);
//
//            }
//        }
//    }

    /**
     * 领取每日救济
     * @author yzp
     * @todo 领取每日救济
     */
    public function getRelief()
    {
        
        $user_id = session('user_id');
        $where     = 'user_id = ' .$user_id;

        $user_obj  = new UserModel();
        $user_info = $user_obj->getUserInfo('user_id, exp',$where);

        //判断今日是否已领取救济金
        $account_obj = new AccountModel();
        $account_info = $account_obj->getDayReliveInfo(' AND user_id ='.$user_id,AccountModel::RELIEF);

        if($account_info)
        {
            return '今日已领取';
        }

        $exp = $user_info['exp'] ? : 0;

        $level_obj = new LevelModel();

        $level_info = $level_obj->field('sign_reward')->where('max_exp >='.$exp)->order('sign_reward asc')->find();
        // dump($level_obj->getLastsql());die;
        if(!$level_info)
        {
            return '等级救济金不存在';
        }
        //领取救济金金额
        $sign_reward = $level_info['sign_reward'] ? : 0 ;

        if($sign_reward == 0)
        {
            return '没有可领取的救济金';
        }
        $account_obj = new AccountModel();
        $left = $account_obj->addAccount($user_id, AccountModel::RELIEF, $sign_reward, '领取救济金');
        // dump($account_obj->getLastsql());die;
        if($left)
        {
            return '领取成功';
        }
        else{
            return '领取失败';
        }
    }

    /**
     * 领取经验换豆
     * @author yzp
     * @todo 领取经验换豆
     */
    public function getExp()
    {
        $user_id = session('user_id');
        //获取用户基本信息
        $where     = 'user_id = ' .$this->user_id;
        $user_obj  = new UserModel();
        $user_info = $user_obj->getUserInfo('more_exp',$where);

        if(!$user_info)
        {
            return '用户不存在';
        }

        if(!$user_info['more_exp'])
        {
            return '没有可以兑换的经验';
        }

        $account_obj = new AccountModel();
        $res = $account_obj->addAccount($user_id, AccountModel::EXCHANGEEXP, $user_info['more_exp'] , '经验兑换乐豆');

        if($res)
        {
            return '兑换成功';
        }
        else{
            return '兑换失败';
        }
    }

    /**
     * 领取每日排行上榜有礼，。定时脚本，每天零点跑一次
     * @author yzp
     * @todo 领取每日排行上榜有礼
     */
    public function getRankReward()
    {
        $type = $_GET['type'];
        if($type != 1)
        {
            return false;
        }
        //查询活动是否存在，并且在活动时间内
        $marketing_obj = new MarketingRuleModel();
        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::RANKREWARD,'start_time,end_time');

        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
        {
            return false;
        }
        
        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;
        $black_user_id = $this->getBlackUser();

        $where = 'daily_flow = 0 AND addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id not in ('.$black_user_id.')';

        $daily_win_obj = new DailyWinModel();
        $daily_win_obj->setLimit(20);
        $daily_win_list = $daily_win_obj->getDailyWinList('',$where,'win desc,loss asc')?:[];

        $rank_list_obj = new RankListModel();

        //判断是否已经生成记录、不重复生成
        $count = $rank_list_obj->where('addtime ='.$start_time)->count();
        if($count >0)
        {
            return false;
        }
        $config_base_obj = new ConfigBaseModel();

        //机器人
        $robot_obj = new RobotModel();
        $robot_list = $robot_obj->where('today_money > 0')->select();

        foreach ($robot_list as $ki => &$vi) {
            $vi['nickname'] = $vi['robot_name'];
            $vi['win'] = $vi['today_money'];
        }
        unset($vi);

        //重置机器人盈利金豆
        $robot_obj->where('true')->save(['today_money' => 0]);


        $daily_win_list = array_merge($daily_win_list, $robot_list);

        $key = array_column($daily_win_list, 'win');
        array_multisort($key, SORT_DESC, $daily_win_list);
        $daily_win_list = array_slice($daily_win_list,0,20);
        foreach ($daily_win_list as $k => $v) {
            if($k < 5) //1~5名奖励不同
            {
                $a = $k +1;
                $reward = $config_base_obj->getConfig("rank$a");   //获取指定奖励
            }
            elseif($k >= 5 && $k <10) //6~10奖励相同
            {
                $reward = $config_base_obj->getConfig('rank6');   //获取指定奖励
            }else{           //10~20奖励相同
                $reward = $config_base_obj->getConfig('rank7');   //获取指定奖励
            }
            $rank_data['user_id'] = $v['user_id']?:0;
            $rank_data['win'] = $v['win'];
            $rank_data['reward'] = $reward;
            $rank_data['addtime'] = NOW_TIME;
            $rank_data['nickname'] = $v['nickname']?:'';
            //生成排行榜，并且生成奖励
            $rank_list_obj->addRankList($rank_data);
            //把之前的记录过期
            $rank_list_obj->editRankList('addtime <'.NOW_TIME .' AND is_received = 0',['is_received' => 2]);

            // $return_log_obj = new ReturnLogModel();
            // $data = [
            //         'account_id' => 0,
            //         'user_id' => $v['user_id'],
            //         'money' => $reward, //获取指定奖励
            //         'return_type' => MarketingRuleModel::RANKREWARD,
            //         'addtime' => time()
            //     ];

            // $return_log_obj->addReturnLog($data);  //生成返利记录
        }
    }


    /**
     * 检查是否在活动时间内
     * @author yzp
     * @todo 检查是否在活动时间内
     */    
    public function checkRule($marketing_type)
    {

        //查询活动是否存在，并且在活动时间内
        $marketing_obj = new MarketingRuleModel();
        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.$marketing_type,'start_time,end_time');

        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
        {
            return false;
        }
        return true;
    }

    //统计每日盈亏
    public function addDailyWin()
    {
        $type = $_GET['type'];
        if($type != 1)
        {
            return false;
        }
        $bet_log_obj = new BetLogModel();

        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;

        $where = ' AND addtime >='.$start_time.' AND addtime <'.$end_time;

        $bet_log_list = $bet_log_obj->getBetLogListAll('user_id,SUM(total_after_money - total_bet_money) AS daily_win','is_open = 1'.$where,'daily_win desc','user_id');
        $daily_win_obj = new DailyWinModel();
        //判断是否已经生成记录、不重复生成
        $count = $daily_win_obj->where('addtime ='.strtotime(date("Y-m-d",strtotime("-1 day"))))->count();
        if($count >0)
        {
            return false;
        }
        foreach ($bet_log_list as $k => $v) {
            
            $data['user_id'] = $v['user_id'];
            if($v['daily_win'] >0)
            {
                $data['win'] = $v['daily_win'];
            }else{
                $data['loss'] = -1*$v['daily_win'];
            }
            $data['addtime'] = strtotime(date("Y-m-d",strtotime("-1 day")));

            $daily_win_obj->addDailyWin($data);
            unset($data);
        }
    }

    /**
     * 领取下线投注返利（投注流水限制）需要添加至定时脚本，每日一次
     * @author yzp
     * @todo 领取下线投注返利（投注流水限制）
     */
    public function getBetting()
    {
        //查询活动是否存在，并且在活动时间内
        $marketing_obj = new MarketingRuleModel();
        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::BETTING,'start_time,end_time');

        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
        {
            return false;
        }

        $account_obj = new AccountModel();

        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;

//        //查询前一天游戏投注
//        $where = 'change_type = ' .AccountModel::BETTING.' AND addtime >='.$start_time.' AND addtime <='.$end_time;
//        $account_list = $account_obj->getAccountList('user_id,sum(amount_out) AS total', $where, 'addtime asc','','user_id');

        $where = 'daily_flow <> 0 AND addtime >='.$start_time.' AND addtime <='.$end_time;
        $config_base_obj = new ConfigBaseModel();
//        $min_flow = $config_base_obj->getConfig('min_flow') ? : 10000;   //投注下限
        $flow_rate = $config_base_obj->getConfig('flow_rate') ? : 0.0015;   //投注返利 比例
        $return_double_flow = $config_base_obj->getConfig('return_double_flow') ? : 1;   //流水倍数
        $valid_flow = $config_base_obj->getConfig('valid_flow') ? : 1;   //有效流水标准

        $min_flow = $valid_flow * $return_double_flow;   //最终流水
        $black_user_id = $this->getBlackUser();
        $where .= ' AND daily_flow >='.$min_flow.' AND user_id not in ('.$black_user_id.')';
        //每日有效流水
        $daily_win_obj = new DailyWinModel();
        $account_list = $daily_win_obj->getDailyWinAllList('user_id,daily_flow AS total',$where,'daily_win_id asc');

        foreach ($account_list as $k => $v) {
            //查询是否有邀请人
            $user_obj = new UserModel($v['user_id']);

            $parent_id = $user_obj->where('user_id = '.$v['user_id'])->getField('parent_id');

            if(!$parent_id)
            {
                continue;
            }
            $user_obj = new UserModel();
            $user_info = $user_obj->getParamUserInfo('id = '.$parent_id,'user_id');

            $parent_id = $user_info['user_id'];

            $return_log_obj = new ReturnLogModel();
            $start_time = strtotime(date('Y-m-d',strtotime(date('Y-m-d'))));
            $end_time = time();

            $return_log_info = $return_log_obj->getReturnLogInfo('addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$parent_id.' AND return_type ='.MarketingRuleModel::BETTING.' AND lower_id ='.$v['user_id'],'return_log_id');
            // dump($return_log_obj->getLastsql());die;
            if(!$return_log_info && $v['total'] >= $min_flow)
            {
                $money = ceil($v['total'] * $flow_rate);
                if($money >= 1)
                {
                    $data = [
                        'account_id' => 0,
                        'user_id' => $parent_id,
                        'lower_id' => $v['user_id'],
                        'money' => $money, //下级投注金额 * 投注返利比例
                        'return_type' => MarketingRuleModel::BETTING,
                        'addtime' => time()
                    ];
                    $return_log_obj->addReturnLog($data);  //生成返利记录
                    //自动存入银行
//                    $account_obj->AutoSaveBank($parent_id,$money);
                    $account_obj->addAccount($parent_id, AccountModel::SBETTING, $money, '领取下线投注返利');
                }

            }
        }
    }

//    public function addValidFlow()
//    {
//        //根据客户提供的规则
//        $type_array = array('28','16','11','10','22','36','PK');
//        $type_index_array = array(21,10,8,7,16,0,11);
//        $bet_log_obj = new BetLogModel();
//
//        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
//        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;
//
//        $where['addtime'] = array('EGT',$start_time);
////        $where['addtime'] = array('ELT',$end_time);
//        $where['is_open'] = 1;
//        $bet_log_list = $bet_log_obj->getBetLogListAll('user_id,SUM(total_bet_money) AS total_bet_money',$where,'total_bet_money desc','user_id');
//        $config_base_obj = new ConfigBaseModel();
//        $game_type_obj = new GameTypeModel();
//        $daily_win_obj = new DailyWinModel();
//        foreach ($bet_log_list as $k => $v) {
//            $total_bet_money = 0;
//            foreach ($type_array as $key => $val)
//            {
//                $where['user_id'] = $v['user_id'];
//                //28系列
//                $times = $config_base_obj->getConfig($val) ? : $type_index_array[$key];   //获取指定奖励
//                if($times > 0){
//                    $type_where['game_type_name'] = array('LIKE', '%'.$val.'%');
//                    $type_id = $game_type_obj->where($type_where)->getField('game_type_id', true);
//                    $where['game_type_id'] = array('in', $type_id);
//                    $bet_log_obj->setLimit($times);
//                    $bet_log_list = $bet_log_obj->getBetLogList('user_id,SUM(total_bet_money) AS total_bet_money',$where,'bet_log_id ASC');
//                    $total_bet_money += $bet_log_list[0]['total_bet_money'];
//                }
//            }
//            $data['daily_flow'] = $total_bet_money;   //一天有效流水
//            $data['user_id'] = $v['user_id'];
//            $data['addtime'] = strtotime(date("Y-m-d",strtotime("-1 day")));
//            $daily_win_obj->addDailyWin($data);
//            unset($data);
//        }
//    }

    /**
     * 记录每日有效流水
     * @author yzp
     * @Date:  2019/6/21
     * @Time:  17:34
     */
    public function addValidFlow()
    {
        $type = $_GET['type'];
        if($type != 1)
        {
            return false;
        }
        $bet_log_obj = new BetLogModel();
        $user_obj = new UserModel();
        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;

        $where['addtime'] = array(array('EGT',$start_time),array('ELT',$end_time),'and');
        $where['is_open'] = 1;
        $bet_log_list = $bet_log_obj->getBetLogListAll('user_id,SUM(total_bet_money) AS total_bet_money',$where,'total_bet_money desc','user_id');
        $game_type_obj = new GameTypeModel();
        $daily_win_obj = new DailyWinModel();
        //判断是否已经生成记录、不重复生成
        $count = $daily_win_obj->where('daily_flow <> 0 AND addtime ='.strtotime(date("Y-m-d",strtotime("-1 day"))))->count();
        if($count >0)
        {
            return false;
        }
        $game_type_list = $game_type_obj->getGameTypeAll('valid_flow,game_type_id','isuse = 1 AND valid_flow > 0');
        foreach ($bet_log_list as $k => $v) {
            $total_bet_money = 0;
            foreach ($game_type_list as $key => $val)
            {
                $where['user_id'] = $v['user_id'];
                $where['game_type_id'] = $val['game_type_id'];

                $bet_log_obj->setLimit(300);
                $bet_log_list = $bet_log_obj->getBetLogList('user_id,total_bet_money,game_type_id,bet_json',$where,'bet_log_id ASC');
                $total_bet_money += $user_obj->checkIsFull($bet_log_list,$val['valid_flow']);

            }
            $data['daily_flow'] = $total_bet_money;   //一天有效流水
            $data['user_id'] = $v['user_id'];
            $data['addtime'] = strtotime(date("Y-m-d",strtotime("-1 day")));
            $daily_win_obj->addDailyWin($data);
            unset($data);
        }
    }

    /**
     * 获取自己的有效流水返利
     * @author yzp
     * @Date:  2019/9/6
     * @Time:  13:53
     */
    public function getSelfBetting()
    {
        //查询活动是否存在，并且在活动时间内
//        $marketing_obj = new MarketingRuleModel();
//        $marketing_info = $marketing_obj->getMarketingRuleInfo('isuse = 1 AND marketing_type ='.MarketingRuleModel::BETTING,'start_time,end_time');
//
//        if(!$marketing_info || $marketing_info['start_time'] > time() || $marketing_info['end_time'] < time())
//        {
//            return false;
//        }
        //2019/11/11 客户要求取消返利
        return false;
        $account_obj = new AccountModel();

        $start_time = strtotime(date('Y-m-d',strtotime("-1 day")));
        $end_time = strtotime(date('Y-m-d',strtotime("-1 day"))) + 24*3600;

        $where = 'daily_flow <> 0 AND addtime >='.$start_time.' AND addtime <='.$end_time;
        $config_base_obj = new ConfigBaseModel();
        $self_flow_rate = $config_base_obj->getConfig('self_flow_rate') ? : 1;   //有效流水返利比例
        $self_flow = $config_base_obj->getConfig('self_flow') ? : 1;   //有效流水标准

        $black_user_id = $this->getBlackUser();
        $where .= ' AND daily_flow >='.$self_flow.' AND user_id not in ('.$black_user_id.')';
        //每日有效流水
        $daily_win_obj = new DailyWinModel();
        $account_list = $daily_win_obj->getDailyWinAllList('user_id,daily_flow AS total',$where,'daily_win_id asc');

        foreach ($account_list as $k => $v) {

            if(!$v['total'])
            {
                continue;
            }
            $return_log_obj = new ReturnLogModel();
            $start_time = strtotime(date('Y-m-d',strtotime(date('Y-m-d'))));
            $end_time = time();

            $return_log_info = $return_log_obj->getReturnLogInfo('addtime >='.$start_time.' AND addtime <='.$end_time.' AND user_id ='.$v['user_id'].' AND return_type ='.MarketingRuleModel::SELF_BETTING,'return_log_id');
            if(!$return_log_info && $v['total'] >= $self_flow)
            {
                $money = ceil($v['total'] * $self_flow_rate);
                if($money >= 1)
                {
                    $data = [
                        'account_id' => 0,
                        'user_id' => $v['user_id'],
                        'money' => $money, //自己有效流水投注金额 * 投注返利比例
                        'return_type' => MarketingRuleModel::SELF_BETTING,
                        'addtime' => time()
                    ];
                    $return_log_obj->addReturnLog($data);  //生成返利记录
                    //自动存入银行
//                    $account_obj->AutoSaveBank($v['user_id'],$money);
                    $account_obj->addAccount($v['user_id'], AccountModel::SELF_BETTING, $money, '领取有效流水投注返利');
                }

            }
        }
    }

    /**
     * 不参加活动返利的用户
     * @return array|string
     * @author yzp
     * @Date:  2019/11/6
     * @Time:  17:24
     */
    public function getBlackUser()
    {
        $user_obj = new UserModel();
        $black_user_id = $user_obj->where('is_no_activity = 0')->getField('user_id',true) ? : array();
        $black_user_id = $black_user_id ? implode(',',$black_user_id) : '0';
        return $black_user_id;
    }
}

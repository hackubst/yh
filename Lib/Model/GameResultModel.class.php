<?php
/**
 * 开奖结果模型类
 * table_name = tp_game_result
 * py_key = game_result_id
 */

class GameResultModel extends Model
{

    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化开奖结果id
     */
    public function GameResultModel()
    {
        parent::__construct('game_result');
    }

//    result_type
    const BJKLB = 1;//北京快乐8
    const BJPKS = 2;//北京PK10
    const JNDKLB = 3;//加拿大快乐八
    const HGXL = 4;//韩国系列
    const CQSSC = 5;//重庆时时彩系列
    const TXFFC = 6;//腾讯分分彩系列
    const BTBONE = 7;//比特币1分钟系列
    const BTBTWO = 8;//比特币1.5分钟系列
    const BTBTHREE = 9;//比特币3分钟系列
    const FEITING = 10;//飞艇
    const XIANGGANGLIUHECAI = 11;//香港六合彩
//part_type
//0：同一种颜色
//1： (1,2,3,4,5,6 | 7,8,9,10,11,12 | 13,14,15,16,17,18)
//2：(1,2,3,19,20 | 4,5,6 | 7,8,9 | 10,11,12 | 13,14,15 | 16,17,18)
//3：(2,5,8,11,14,17 | 3,6,9,12,15,18 | 4,7,10,13,16,19)
//4：(1,4,7,10,13,16 | 2,5,8,11,14,17 | 3,6,9,12,15,18)
//5：(1,2,3 | 4,5,6,7,8,9,10)
//6:   (1 | 2-10)
//7:   (1 | 2-9  | 10)
//8:   (1 |  2  | 3- 10)

//    const TXFFC = 3;//腾讯分分彩
//    const CQSSC = 4;//重庆时时彩


    /**
     * 获取开奖结果信息
     * @param int $game_result_id 开奖结果id
     * @param string $fields 要获取的字段名
     * @return array 开奖结果基本信息
     * @author 姜伟
     * @todo 根据where查询条件查找开奖结果表中的相关数据并返回
     */
    public function getGameResultInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改开奖结果信息
     * @param array $arr 开奖结果信息数组
     * @return boolean 操作结果
     * @author 姜伟
     * @todo 修改开奖结果信息
     */
    public function editGameResult($where = '', $arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = NOW_TIME;
        $arr['last_edit_user_id'] = session('user_id');

        return $this->where($where)->save($arr);
    }

    /**
     * 添加开奖结果
     * @param array $arr 开奖结果信息数组
     * @return boolean 操作结果
     * @author 姜伟
     * @todo 添加开奖结果
     */
    public function addGameResult($arr)
    {
        if (!is_array($arr)) return false;

        return $this->add($arr);
    }

    /**
     * 删除开奖结果
     * @param int $game_result_id 开奖结果ID
     * @param int $opt ,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @author 姜伟
     * @todo isuse设为1 || 真删除
     */
    public function delGameResult($game_result_id, $opt = false)
    {
        if (!is_numeric($game_result_id)) return false;
        if ($opt) {
            return $this->where('game_result_id = ' . $game_result_id)->delete();
        } else {
            return $this->where('game_result_id = ' . $game_result_id)->save(array('isuse' => 2));
        }

    }

    /**
     * 根据where子句获取开奖结果数量
     * @param string|array $where where子句
     * @return int 满足条件的开奖结果数量
     * @author 姜伟
     * @todo 根据where子句获取开奖结果数量
     */
    public function getGameResultNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询开奖结果信息
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 开奖结果基本信息
     * @author 姜伟
     * @todo 根据SQL查询字句查询开奖结果信息
     */
    public function getGameResultList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param string $where
     * @param string $field
     * @return void
     */
    public function getGameResultField($where, $field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取开奖结果列表页数据信息列表
     * @param array $game_result_list
     * @param array $game_type_id
     * @return array $game_result_list
     * @author 姜伟
     * @todo 根据传入的$GameResult_list获取更详细的开奖结果列表页数据信息列表
     */
    public function getListData($game_result_list, $game_type_id, $user_id)
    {
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $game_log_obj = new GameLogModel();
        $bet_log_obj = new BetLogModel();
        $game_type_obj = new GameTypeModel();
        //检查是否有自动投注
        //判断是否有自动投注
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_info = $bet_auto_obj->getBetAutoInfo('user_id =' . $user_id . ' AND game_type_id =' . $game_type_id . ' AND is_open = 1');
        $is_auto = 0;
        if ($bet_auto_info) {
            $is_auto = 1;
        }
        $result_type = $game_type_obj->getGameTypeField('game_type_id =' . $game_type_id, 'result_type');
        //最新没开奖的期数
        $game_result_id = $this->where('is_open = 0 AND type =' .$result_type)->order('game_result_id ASC')->getField('game_result_id');

        foreach ($game_result_list as $k => &$v) {
            //get game's result from redis
            //如果后台重开号码则需要读取数据库
            $is_update = 0;
            $key = 'get_true_result_'.$v['game_result_id'];
            if($redis_obj->get($key))
            {
                $user_game_type_key = 'get_true_result_'.$user_id.'_'.$v['game_result_id'].'_'.$game_type_id;
                if(!$redis_obj->get($user_game_type_key) || $redis_obj->get($user_game_type_key) != $redis_obj->get($key))
                {
                    log_file('game_result_id='.$v['game_result_id'],'game_result_id',true);
                    $redis_obj->set($user_game_type_key,$redis_obj->get($key));
                    $is_update = 1;
                }
            }
            $res = $redis_obj->get($user_id.'_'.$v['game_result_id'].'_'.$game_type_id);
            if ($res && $is_update == 0) {
                $v = json_decode($res,true);
                continue;
            }
            $v['addtime_str'] = date('m-d H:i:s',$v['addtime']);
            $game_log_info = $game_log_obj->getGameLogInfo('game_type_id =' . $game_type_id . ' AND game_result_id = ' . $v['game_result_id']) ?: '';
            if (!$game_log_info) {
                $v['is_open']=0;
                //未开奖计算投注信息
                $real_bet_num = $bet_log_obj->where('game_result_id =' . $v['game_result_id'])->count('DISTINCT user_id') ?: 0;
//                $real_win_num = $bet_log_obj->where('game_result_id =' . $v['game_result_id'] . ' AND is_win = 1')->count('DISTINCT user_id');
                $real_bet_money = $bet_log_obj->where('game_result_id =' . $v['game_result_id'])->getField('sum(total_bet_money)') ?: 0;
//                $real_win_money = $bet_log_obj->where('game_result_id =' . $v['game_result_id'])->getField('sum(total_after_money)');

                //最近未开奖的 提前机器人下注
                if($v['game_result_id'] == $game_result_id){
                    $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_type_id,
                        'base_bonus_pools,min_bet_num');
                    $real_bet_num += $game_type_info['min_bet_num'];
                    $real_bet_money += $game_type_info['base_bonus_pools'];
                }
                $game_log_info['bet_people'] = $real_bet_num;
                $game_log_info['total_money'] = $real_bet_money ;
//                $game_log_info['bet_reward'] = $real_bet_money;
            }

            //查到用户自己的投注
            $user_id = session('user_id');
            $bet_log_info = $bet_log_obj->getBetLogInfo('user_id =' . $user_id . ' AND game_result_id =' . $v['game_result_id'] . ' AND game_type_id =' . $game_type_id, 'total_bet_money,total_after_money');
            $game_log_info['bet_reward'] = floatval($bet_log_info ['total_bet_money']) ?: 0;
            $game_log_info['win_reward'] = floatval($bet_log_info['total_after_money']) ?: 0;
            $game_log_info['is_bet'] = 0;
            if ($bet_log_info) {
                $game_log_info['is_bet'] = 1;
            }


            $game_log_info['xian_card'] = json_decode($game_log_info['xian_card']) ?: '';
            $game_log_info['zhuang_card'] = json_decode($game_log_info['zhuang_card']) ?: '';
            $v['game_log_info'] = $game_log_info;
            $v['is_auto'] = $is_auto;
            if($game_type_id == GameTypeModel::BTBONE28 || $game_type_id == GameTypeModel::BTBTWO28 || $game_type_id == GameTypeModel::BTBTHREE28){
                $v['result'] = $game_log_info['result']?:'';
            }

            //杀模式
//            $is_kill = $game_type_obj->getGameTypeField('game_type_id =' . $game_type_id, 'is_kill');
            if ($game_log_info['ex_result']) {
                $v['result'] = $game_log_info['ex_result'];
            }

            if (!$res && $v['result'] != '' && $v['open_time'] < (NOW_TIME -100)) {
                $redis_obj->set($user_id.'_'.$v['game_result_id'].'_'.$game_type_id,json_encode($v),259200);
            }
        }
        unset($v);
        return $game_result_list;
    }


    /**
     * 添加预计开奖记录
     * @param $type
     * @date: 2019/3/12
     * @author: hui
     */
    public function addResult($type)
    {
        $three_days_ago = strtotime("-3 day");
        $issue = $this->where('type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id DESC')->getField('issue')?:0;
        //上一次开奖的时间
        $last_issue_time = $this->where('type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id DESC')->getField('addtime');

        if (!$issue) {
            if ($type != self::BTBONE && $type != self::BTBTWO && $type != self::BTBTHREE) {
                return;
            }
        }
//        $open_issue = $this->where('type =' . $type . ' AND is_open = 1')->order('game_result_id DESC')->getField('issue')?:0;
        $open_game_result_id = $this->where('type =' . $type . ' AND is_open = 1 AND addtime >'.$three_days_ago)->order('game_result_id DESC')->getField('game_result_id')?:0;

        //未开奖数
        $count_issue = $this->where('game_result_id >' . $open_game_result_id . ' AND is_open = 0 AND type =' . $type)->count()?:0;
//        echo $count_issue;
        //明天0点时间戳
        $next_day = strtotime(date('Ymd', strtotime('+1 day', NOW_TIME)));
        $day = strtotime(date('Ymd 20:03:30', NOW_TIME));
//        $day = strtotime(date('Ymd 19:0:0', NOW_TIME));
        $day_two = strtotime(date('Ymd 3:30:0', NOW_TIME));

        $day_three = strtotime(date('Ymd 4:04:00', NOW_TIME));//飞艇
        $today = strtotime('Ymd', NOW_TIME);
        if ($type == GameResultModel::BJKLB) {
            //0点 -9点 不开奖（不包括9点）
            if (NOW_TIME >= $today && NOW_TIME < strtotime(date('Ymd 9:05:0', NOW_TIME))) {
                return;
            }
            $time = 4 * 60 * 5;
            if ($time + NOW_TIME >= $next_day + 1) {
                return;
//                $addtime = strtotime(date('Ymd 9:0:0', strtotime('+1 day')));
//                $addtime = $addtime + 60 * 5 + $time + NOW_TIME - $next_day;
            } else {
                $addtime = $time + NOW_TIME;
            }
//
//            if ($addtime >= strtotime(date('Ymd 0:20:0', NOW_TIME)) && $addtime < strtotime(date('Ymd 0:25:0', NOW_TIME))) {
//                $addtime = strtotime(date('Ymd 9:25:01', NOW_TIME));
//            }
//            if($addtime + 60 * 5 >= $next_day){
//                $addtime = strtotime(date('Ymd 9:00:01'),$next_day);
//            }

        } elseif ($type == GameResultModel::BJPKS) {
            //0点 -9.30不开奖（不包括 9:30）
            if (NOW_TIME >= $today && NOW_TIME < strtotime(date('Ymd 9:30:0', NOW_TIME))) {
                return;
            }
            $time = 4 * 60 * 20;
            if ($time + NOW_TIME >= $next_day + 1) {
                return;
//                $addtime = strtotime(date('Ymd 9:20:0', strtotime('+1 day')));
//                $addtime = $addtime + $time + NOW_TIME - $next_day;
            } else {
                $addtime = $time + NOW_TIME;
            }
//            if ($addtime >= strtotime(date('Ymd 1:30:0', NOW_TIME)) && $addtime < strtotime(date('Ymd 1:50:0', NOW_TIME))) {
//                $addtime = strtotime(date('Ymd 10:50:01', NOW_TIME));
//            }


        } elseif ($type == GameResultModel::JNDKLB) {
            //读取当前是夏令时还是冬令时
            $base_config = new ConfigBaseModel();
            $jnd_time = $base_config->getConfig('jnd_time'); //0代表夏令时，1代表冬令时
            if($jnd_time)
            {
                $day = strtotime(date('Ymd 20:03:30', NOW_TIME));
                $first_addtime = strtotime(date('Ymd 21:00:0', NOW_TIME));
            }else{
                $day = strtotime(date('Ymd 19:03:30', NOW_TIME));
                $first_addtime = strtotime(date('Ymd 20:00:0', NOW_TIME));
            }
            //让19：0.：30的比赛置顶
            $jnd_game_info = $this->getGameResultInfo('addtime ='.$day,'*');
            if($jnd_game_info && $jnd_game_info['is_open'] == 0)
            {
                return;
            }
            //取上一条添加的记录
            $time = 4 * 60 * 3.5;
//            $first_addtime = strtotime(date('Ymd 19:57:30', NOW_TIME));
            //19点 -20点 不开奖
            if (NOW_TIME < $first_addtime && NOW_TIME > $day) {
                return;
            }
            $addtime = $last_issue_time + 60 * 3.5;
            if($addtime > $day && $addtime < $first_addtime){
                $addtime = $day;
            }
        } elseif ($type == GameResultModel::HGXL) {
            $time = 4 * 60 * 1.5;
            $seven_clock = strtotime(date('Ymd 7:0:0'), NOW_TIME);
            $five_clock = strtotime(date('Ymd 5:0:0'), NOW_TIME);

            //5点 -7点 不开奖（不包括7点）
            if (NOW_TIME >= $five_clock && NOW_TIME < $seven_clock) {
                return;
            }
            if ($time + NOW_TIME >= $five_clock  && $time + NOW_TIME < $seven_clock) {
                $addtime = $seven_clock + $time + NOW_TIME - $five_clock;
            } else {
                $addtime = $time + NOW_TIME;
            }
        } elseif ($type == GameResultModel::CQSSC) {
            $is_next_day = 1;
            $time = 4 * 60 * 20;
            $addtime = strtotime(date('Ymd 7:10:0', NOW_TIME));
            if (NOW_TIME < $addtime && NOW_TIME > $day_two ) {
                return;
            }
            if ($time + NOW_TIME > $day_two && NOW_TIME + $time < $addtime) {
                return;
//                $addtime = $addtime + $time + NOW_TIME - $day_two ;
            } elseif($count_issue == 0 && NOW_TIME < strtotime(date('Ymd 7:30:0', NOW_TIME))) {
                $addtime = $time + strtotime(date('Ymd 7:30:01', NOW_TIME));
            } else {
                $addtime = $time + NOW_TIME;
            }
        } elseif ($type == GameResultModel::TXFFC) {
            $time = 4 * 60;
            $addtime = $time + NOW_TIME;
        } elseif ($type == GameResultModel::BTBONE) {
            $time = 4 * 60 * 1;
            $addtime = $time + NOW_TIME;
        } elseif ($type == GameResultModel::BTBTWO) {
            $is_next_day = 1;
            $time = 4 * 60 * 1.5;
            $addtime = $time + NOW_TIME;
        } elseif ($type == GameResultModel::BTBTHREE) {
            $is_next_day = 1;
            $time = 4 * 60 * 3;
            $addtime = $time + NOW_TIME;
        }
        elseif ($type == GameResultModel::FEITING) {
            //让19：0.：30的比赛置顶
//            $jnd_game_info = $this->getGameResultInfo('addtime ='.$day_three,'*');
//            if($jnd_game_info && $jnd_game_info['is_open'] == 0)
//            {
//                return;
//            }
            //取上一条添加的记录
            $time = 4 * 60 * 5;
            //4点4分生成之后 -13点 不开奖
            $first_addtime = strtotime(date('Ymd 13:00:0', NOW_TIME));
            if($last_issue_time == strtotime(date('Ymd 04:04:0', NOW_TIME)) && NOW_TIME < $first_addtime)
            {
                return;
            }
//            $first_addtime = strtotime(date('Ymd 19:57:30', NOW_TIME));
            if (NOW_TIME < $first_addtime && NOW_TIME > $day_three) {
                return;
            }
            $addtime = $last_issue_time + 60 * 5;
            if($addtime > $day && $addtime < $first_addtime){
                $addtime = $first_addtime;
            }
        }
//        $addtime = floor($addtime/10)*10;
        $num = 0;
        while ($count_issue <= 4) {
//            $is_new_day = 0;
//            $issue_num = $issue - $open_issue;
            $issue_num = $count_issue;
            $next_issue = $issue + 1;
            //重庆时时彩/比特币下一天 期号变化
            if ($is_next_day) {
                $other_time = date('Ymd', $addtime);
                $first_issue = substr($issue, 0, 8);
                if ($other_time != $first_issue) {
                    $next_issue = $other_time . '001';
//                    $is_new_day = 1;
                }
            }
//            if($is_new_day == 1 && $type == GameResultModel::CQSSC)
//            {
//                $open_time = $addtime - 1;
//                if($open_time < strtotime(date('Ymd 0:30:00', (NOW_TIME+24*3600))))
//                {
//                    return;
//                }
//                if($num != 0)
//                {
//                    $addtime = $addtime + 1200;
//                }
//            }else{
                //加拿大系列
                if($type == GameResultModel::JNDKLB){
                    $open_time = $addtime + $time / 4 * $num ;
                }else{
                        $open_time = $addtime - 1 - $time / 4 * ( 4 - $issue_num);
                        if($type == GameResultModel::CQSSC && $open_time < strtotime(date('Ymd 1:50:0',$open_time)) &&  $open_time >= strtotime(date('Ymd 0:10:00', $open_time)))
                        {
                            $open_time = $open_time + 1200;
                        }
                    }
            if($type == GameResultModel::FEITING){
                $open_time = $addtime + $time / 4 * $num ;
                if (substr($issue,-3) === '180') {
//                    $next_issue = strval((intval(substr($issue,0,-3))+1)).'001';
                    $next_issue = strval(date('Ymd')).'001';
                    $open_time = strtotime(date('Ymd 13:09:00',NOW_TIME));
                    $addtime = $open_time;
                }
            }
//            }
            if($type == GameResultModel::CQSSC)
            {
                $open_time = floor($open_time/10)*10;
            }

            //比特币1分钟
            if($type == GameResultModel::BTBONE && $type == self::HGXL){
                $next_issue = date('YmdHi',$open_time);
                $next_issue = substr($next_issue,3);
//                $next_issue += 4 - $issue_num;
            }
            $open_time = floor($open_time/10)*10;
            $arr = array(
                'type' => $type,
                'is_open' => 0,
                'addtime' => $open_time,
                'issue' => $next_issue,
            );
            $game_result_id = $this->add($arr);
//            echo $this->getLastSql();
            $game_bet_money_obj = new GameBetMoneyModel();
            $game_type_obj = new GameTypeModel();
            $game_list = $game_type_obj->where('game_series_id = 5')->select();
            foreach ($game_list as $value) {
                $game_bet_money_obj->add([
                    'game_type_id'  =>  $value['game_type_id'],
                    'game_result_id'    =>  $game_result_id
                ]);
            }

//            echo $game_bet_money_obj->getLastSql();
//            echo 1111;die;
            $issue = $next_issue;
            $count_issue ++;
            $num ++;

            log_file('sql =' . $this->getLastSql(), 'addResult', true);
        }
    }


    //http://api.caipiaokong.cn/lottery/?name=xglhc&format=json&uid=1232627&token=2e25138fd1283774fe8b6cdae58e02cd24ce22df
    /**
     * 调用彩票控接口 获取开奖结果
     * @param $type
     * @date: 2019/3/12
     * @return string
     * @author: hui
     */
    public function getCaipiaokongApi($type)
    {
        set_time_limit(0);
        if ($type == self::BJKLB) {
            $url = 'http://api.caipiaokong.cn/lottery/?name=bjklb&format=json&uid=1490818&token=4de37c8e6d198c400fd6c6a375a0e7d541cb1c8a';
            $issue_time = 5 * 60;
        } elseif ($type == self::BJPKS) {
            return;
            $url = 'http://api.caipiaokong.cn/lottery/?name=bjpks&format=json&uid=1489037&token=7bf5719c316991944e9949161afda9ea3ba8b9f1';
            $issue_time = 20 * 60;
        } elseif ($type == self::JNDKLB) {
            $url = 'http://api.caipiaokong.cn/lottery/?name=jndklb&format=json&uid=1490818&token=4de37c8e6d198c400fd6c6a375a0e7d541cb1c8a';
            $issue_time = 3.5 * 60;
            $three_days_ago = strtotime("-3 day");
            $addtime = $this->where('is_open = 0 AND type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id ASC')->getField('addtime');
        } elseif ($type == self::CQSSC) {
            return;
            $url = 'http://api.caipiaokong.cn/lottery/?name=cqssc&format=json&uid=1489037&token=7bf5719c316991944e9949161afda9ea3ba8b9f1';
            $issue_time = 20 * 60;
        }
        elseif ($type == self::FEITING) {
            $url = 'http://api.caipiaokong.cn/lottery/?name=xyft&format=json&uid=1490818&token=4de37c8e6d198c400fd6c6a375a0e7d541cb1c8a';
            return;
            $issue_time = 5 * 60;
        }
        //获取开奖结果
        $result = $this->request_get($url);
        log_file('result_status='.$result,'cpk_result_log',true);
//        echo $result;
        $result = json_decode($result, true);
        if (!$result) {
            return;
        }

        //查找对应开奖记录 添加结果
        $game_log_obj = new GameLogModel();
        $three_days_ago = strtotime("-3 day");
        $issue = $this->where('is_open = 0 AND type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id ASC')->getField('issue');
        //结果倒序输出
        $array_key = array_keys($result);
        $array_value = array_values($result);
        $array_return = array();
        for ($i = 1, $size_of_array = sizeof($array_key); $i <= $size_of_array; $i++) {
            $array_return[$array_key[$size_of_array - $i]] = $array_value[$size_of_array - $i];
        }
        log_file('result_json='.json_encode($array_return),'cpk_result_log',true);
        foreach ($array_return as $k => &$v) {
            if ($k == 'status') {
                continue;
            }
            if ($type == self::BJKLB ) {
                $v['number'] = substr($v['number'], 0, strlen($v['number']) - 3);
            }
            if (!$issue) {
                $info = $this->where('issue = ' . $k . ' AND type = ' . $type)->find();
                if ($info) {
                    continue;
                }
                //防止插入重复的开奖数据
                $max_issue = $this->where('type =' . $type)->order('game_result_id DESC')->getField('issue');
                if($k <= $max_issue)
                {
                    continue;
                }
                $open_time = strtotime($v['dateline']);

//                $first_addtime = 0;
//                if ($type == self::JNDKLB) {
                    $first_addtime = strtotime(date('Ymd 19:57:30', strtotime('-1 day', $open_time)));
//                }
                if ($type == self::FEITING) {
                    $first_addtime = strtotime(date('Ymd 12:59:00', strtotime('-1 day', $open_time)));
                }
                $time = $open_time - $first_addtime;
                $yu = $time % $issue_time; //单次差
                $mabel_time = $open_time - $yu; //预计开奖时间

                $arr = array(
                    'type' => $type,
//                    'addtime' => NOW_TIME,
                    'addtime' => $mabel_time,
                    'issue' => $k,
                    'open_time' => $open_time,
                    'result' => $v['number'],
                    'is_open' => 1,
                );
                $game_result_id = $this->addGameResult($arr);
                $game_log_obj->calculateResult($game_result_id, $type);
            }
            if ($k == $issue) {
                $game_result_id = $this->where('is_open = 0 AND type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id ASC')->getField('game_result_id');
                $arr = array(
                    'issue' => $k,
                    'open_time' => strtotime($v['dateline']),
                    'result' => $v['number'],
                    'is_open' => 1,
                );
                if($type == self::JNDKLB)  //如果是加拿大游戏重开的第一期  冬令时20:03:30 夏令时19:03:30
                {
                    if($addtime == strtotime(date('Ymd 20:03:30', NOW_TIME)) || $addtime == strtotime(date('Ymd 19:03:30', NOW_TIME)))
                    {
                        //读取当前是夏令时还是冬令时
                        $base_config = new ConfigBaseModel();
                        $jnd_time = $base_config->getConfig('jnd_time'); //0代表夏令时，1代表冬令时
                        if(($addtime == strtotime(date('Ymd 20:03:30', NOW_TIME)) && $jnd_time) || ($addtime == strtotime(date('Ymd 19:03:30', NOW_TIME)) && !$jnd_time))
                        {
                            $new_issue = $k;
                            if($jnd_time)
                            {
                                $first_jnd_time = strtotime(date('Ymd 20:57:30', NOW_TIME));
                            }else{
                                $first_jnd_time = strtotime(date('Ymd 19:57:30', NOW_TIME));
                            }
                            $diff_type = 0;
                            $last_open_time = strtotime($v['dateline']);
                            $diff = $last_open_time - $first_jnd_time;
                            if ($diff < 0)//实际开奖时间偏差  等于0准时开，大于0表示晚开，反之提前开
                            {
                                $diff_type = 1;
                                $diff = -1 * $diff;
                                $may_times = floor($diff / 210) * 210;
                            }else{
                                $may_times = ceil($diff / 210) * 210;
                            }
                            if($diff_type == 0)
                            {
                                $next_open_time = $first_jnd_time + $may_times;
                            }else{
                                $next_open_time = $first_jnd_time - $may_times;
                            }
                            for ($ii = 0;$ii < 5 ;$ii ++)
                            {
                                $new_issue = $new_issue + 1;
                                $newarr = array(
                                    'type' => $type,
                                    'is_open' => 0,
                                    'addtime' => $next_open_time + $issue_time * $ii,
                                    'issue' => $new_issue,
                                );
                                $this->addGameResult($newarr);
                            }
                        }
                    }
                }

                if($type == self::FEITING && $addtime == strtotime(date('Ymd 13:00:00', NOW_TIME)))  //如果是飞艇游戏重开的第一期
                {
                    //飞艇只有180期，180期的时候下一期从001开始
                    $new_issue = $k;
                    if (substr($new_issue,-3) == 180) $new_issue+=820;
                    $first_jnd_time = strtotime(date('Ymd 13:00:00', NOW_TIME));
                    $diff_type = 0;
                    $last_open_time = strtotime($v['dateline']);
                    $diff = $last_open_time - $first_jnd_time;
                    if ($diff < 0)//实际开奖时间偏差  等于0准时开，大于0表示晚开，反之提前开
                    {
                        $diff_type = 1;
                        $diff = -1 * $diff;
                        $may_times = floor($diff / 300) * 300;
                    }else{
                        $may_times = ceil($diff / 300) * 300;
                    }
                    if($diff_type == 0)
                    {
                        $next_open_time = $first_jnd_time + $may_times;
                    }else{
                        $next_open_time = $first_jnd_time - $may_times;
                    }
                    for ($ii = 0;$ii < 5 ;$ii ++)
                    {
                        $new_issue = $new_issue + 1;
                        $newarr = array(
                            'type' => $type,
                            'is_open' => 0,
                            'addtime' => $next_open_time + $issue_time * $ii,
                            'issue' => $new_issue,
                        );
                        $this->addGameResult($newarr);
                    }
                }
                $this->editGameResult('game_result_id =' . $game_result_id, $arr);
                //计算每个类型结果
                $game_log_obj->calculateResult($game_result_id, $type);
            }

        }
        unset($v);
        return '开奖成功';
    }

    public function getCaipiaokongApiWithLiuhecai($type)
    {
        set_time_limit(0);
        $type == self::XIANGGANGLIUHECAI;
        $url = 'http://api.caipiaokong.cn/lottery/?name=xglhc&format=json&uid=1232627&token=2e25138fd1283774fe8b6cdae58e02cd24ce22df';
        $issue_time = 5 * 60;
        //获取开奖结果
        $result = $this->request_get($url);
        log_file('result_status='.$result,'cpk_result_log',true);
//        echo $result;die;
        $result = json_decode($result, true);
        if (!$result) {
            return;
        }

        //查找对应开奖记录 添加结果
        $game_log_obj = new GameLogModel();
        $three_days_ago = strtotime("-3 day");
        $issue = $this->where('is_open = 0 AND type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id ASC')->getField('issue');
        //结果倒序输出
        $array_key = array_keys($result);
        $array_value = array_values($result);
        $array_return = array();
        for ($i = 1, $size_of_array = sizeof($array_key); $i <= $size_of_array; $i++) {
            $array_return[$array_key[$size_of_array - $i]] = $array_value[$size_of_array - $i];
        }
//        echo json_encode($array_return);die;
        log_file('result_json='.json_encode($array_return),'cpk_result_log',true);
        foreach ($array_return as $k => &$v) {
            if ($k == 'status') {
                continue;
            }
            if (!$issue) {
                $info = $this->where('issue = ' . $k . ' AND type = ' . $type)->find();
                if ($info) {
                    continue;
                }
                //防止插入重复的开奖数据
                $max_issue = $this->where('type =' . $type)->order('game_result_id DESC')->getField('issue');
                if($k <= $max_issue)
                {
                    continue;
                }
                $open_time = strtotime($v['dateline']);
//                $time = $open_time - $first_addtime;
//                $yu = $time % $issue_time; //单次差
//                $mabel_time = $open_time - $yu; //预计开奖时间

                $arr = array(
                    'type' => $type,
//                    'addtime' => NOW_TIME,
                    'addtime' => $open_time,
                    'issue' => $k,
                    'open_time' => $open_time,
                    'result' => $v['number'],
                    'is_open' => 1,
                );
                $game_result_id = $this->addGameResult($arr);
                $game_log_obj->calculateResult($game_result_id, $type);
            }
            if ($k == $issue) {
                $game_result_id = $this->where('is_open = 0 AND type =' . $type.' AND addtime >'.$three_days_ago)->order('game_result_id ASC')->getField('game_result_id');
                $arr = array(
                    'issue' => $k,
                    'open_time' => strtotime($v['dateline']),
                    'result' => $v['number'],
                    'is_open' => 1,
                );
                $this->editGameResult('game_result_id =' . $game_result_id, $arr);
                //计算每个类型结果
                $game_log_obj->calculateResult($game_result_id, $type);
            }

        }
        unset($v);
        return '开奖成功';
    }




    public function getTengxunApi()
    {
        $result = '';
        while (!$result) {
            $url = 'https://mma.qq.com/cgi-bin/im/online';
            $result = $this->request_get($url);
            $result = substr($result, 12, strlen($result) - 13);
            $result = json_decode($result, true);
            $result = implode(',', str_split($result['c']));
        }


        $issue = $this->where('is_open = 0 AND type =' . self::TXFFC)->order('game_result_id ASC')->getField('issue');
        $open_issue = $this->where('is_open = 1 AND type =' . self::TXFFC)->order('game_result_id DESC')->getField('issue') ?: 0;
        $game_log_obj = new GameLogModel();
        if (!$issue) {
            $arr = array(
                'type' => self::TXFFC,
                'addtime' => NOW_TIME,
                'issue' => $open_issue + 1,
                'open_time' => NOW_TIME,
                'result' => $result,
                'is_open' => 1,
            );
            $game_result_id = $this->addGameResult($arr);
            $game_log_obj->calculateResult($game_result_id, self::TXFFC);
        } else {
            $game_result_id = $this->where('is_open = 0 AND type =' . self::TXFFC)->order('game_result_id ASC')->getField('game_result_id');
            $arr = array(
                'issue' => $issue,
                'open_time' => NOW_TIME,
                'result' => $result,
                'is_open' => 1,
            );
            $this->editGameResult('game_result_id =' . $game_result_id, $arr);
            //计算每个类型结果
            $game_log_obj->calculateResult($game_result_id, self::TXFFC);

        }
        return '开奖成功';
    }


    /**
     * 比特币系列开奖
     * @param $type
     * @return string|void
     */
    public function getBtbApi($type){

        $result = '';
        while (!$result) {
            $url = 'https://blockchain.info/unconfirmed-transactions?format=json';
            $result = $this->request_get($url);
            $result = json_decode($result, true);
        }
        $hash_one = $result['txs']['7']['hash'];
        $hash_two = $result['txs']['8']['hash'];
        $hash_three = $result['txs']['9']['hash'];
        $hash_total = $hash_one.$hash_two.$hash_three;
        $hash_new = hash('sha256',$hash_total);

        $issue = $this->where('is_open = 0 AND type =' . $type)->order('game_result_id ASC')->getField('issue');
        $open_issue = $this->where('is_open = 1 AND type =' . $type)->order('game_result_id DESC')->getField('issue') ?: 0;
        $game_log_obj = new GameLogModel();
        $game_result_id = $this->where('is_open = 0 AND type =' .$type)->order('game_result_id ASC')->getField('game_result_id');
        if (!$issue) {
            return ;//只开奖 不插记录
//            $open_issue = $open_issue + 1;
//            $first_issue = date('Ymd',NOW_TIME);
//            $minute = date('i',NOW_TIME);
//            $hour = date('H',NOW_TIME);
//            if($type == GameResultModel::BTBTHREE){
//                $last_issue = ( $hour * 60 + $minute ) / 3;
//                $last_issue = ceil($last_issue);
//                $open_issue = $first_issue.$last_issue;
//            }
//            if($type == GameResultModel::BTBTWO){
//                $last_issue = ( $hour * 60 + $minute ) / 1.5;
//                $last_issue = ceil($last_issue);
//                $open_issue = $first_issue.$last_issue;
//            }
//            $arr = array(
//                'type' => $type,
//                'addtime' => NOW_TIME,
//                'issue' => $open_issue,
//                'open_time' => NOW_TIME,
//                'is_open' => 1,
//                'hash_one' => $hash_one,
//                'hash_two' => $hash_two,
//                'hash_three' => $hash_three,
//                'hash_total' => $hash_total,
//                'hash_new' => $hash_new,
//            );
//            $game_result_id = $this->addGameResult($arr);
//            $game_log_obj->calculateResult($game_result_id, $type);
        } else {
            $arr = array(
               'open_time' => NOW_TIME,
//            'result' => $result,
                'is_open' => 1,
                'hash_one' => $hash_one,
                'hash_two' => $hash_two,
                'hash_three' => $hash_three,
                'hash_total' => $hash_total,
                'hash_new' => $hash_new,
            );
            $this->editGameResult('game_result_id =' . $game_result_id, $arr);
            //计算每个类型结果
            $game_log_obj->calculateResult($game_result_id, $type);

        }

        return '开奖成功';
    }


    /**
     * 获取https get请求数据
     * @param string $url
     * @return mixed
     */
    function request_get($url = '')
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        echo curl_error($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;

    }


    /**
     * 韩国系统开奖结果
     * @date: 2019/3/20
     * @return mixed|string
     * @author: hui
     */
    public function randHanguoResult()
    {
        $arr = '';
        for ($i = 1; $i <= 80; $i++) {
            $arr[$i] = $i;
        }
        $result = array_rand($arr, 20);




//        echo json_encode($result);die;
        //查找对应开奖记录 添加结果
        $game_log_obj = new GameLogModel();
        $issue = $this->where('is_open = 0 AND type =' . GameResultModel::HGXL)->order('issue ASC')->getField('issue') ?: 0;

        $now_issue = $this->where('is_open = 1 AND type =' . GameResultModel::HGXL)->order('issue DESC')->getField('issue') ?: 0;

        $game_result_id = $this->where('is_open = 0 AND type =' . GameResultModel::HGXL)->order('issue ASC')->getField('game_result_id');
        $game_log_obj = new GameLogModel();
        $kill_res = $game_log_obj->hanguo28Kill($game_result_id);
//        echo json_encode($kill_res);
        if ($kill_res) {

            $result = $kill_res;
//            echo print_r($kill_res);die;
        } else {
            $result = implode(',', $result);

        }
//        echo 'kill:'.json_encode($kill_res);
//        echo $result.'----';
        $seven_clock = strtotime(date('Ymd 7:0:0'), NOW_TIME);
        $five_clock = strtotime(date('Ymd 5:0:0'), NOW_TIME);
        //5点 -7点 不开奖（不包括7点）
        if (NOW_TIME >= $five_clock && NOW_TIME < $seven_clock) {
            return;
        }

        if (!$issue) {
            $next_issue = date('YmdHi',NOW_TIME);
            $next_issue = substr($next_issue,3);
            $arr = array(
                'type' => GameResultModel::HGXL,
                'is_open' => 0,
                'addtime' => NOW_TIME,
                'issue' => $next_issue,
            );
            $game_result_id = $this->addGameResult($arr);
//            $issue = $now_issue + 1;
        }
        $arr = array(
//            'issue' => $issue,
            'open_time' => NOW_TIME,
            'result' => $result,
            'is_open' => 1,
        );
        echo json_encode($arr);
        $this->editGameResult('game_result_id =' . $game_result_id, $arr);
        //计算每个类型结果
        $game_log_obj->calculateResult($game_result_id, GameResultModel::HGXL);


        return '开奖成功';

    }



}

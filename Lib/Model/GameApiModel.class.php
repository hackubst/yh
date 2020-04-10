<?php

/**
 * Class GameApiModel
 */
class GameApiModel extends ApiModel
{
    CONST FIRST = 1; //走势图最新100条
    CONST SECOND = 2; //走势图最新200条 
    CONST THIRD = 3; //走势图最新300条
    CONST FOURTH = 4; //走势图最新400条
    CONST FIFTH = 5; //走势图最新500条
    CONST AUTOCHANGE = 1; //输赢变换模式

    /**
     * 获取首页
     * @date: 2019/3/11
     * @author: hui
     */
    public function homePage($params)
    {
        $type = $params['type']?:0;
        //热门游戏
        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeLimitList('game_type_id,game_series_id,base_img,game_type_name',
            'is_index = 1 AND isuse = 1', '', '', 0, 8);

        //新闻公告
        $notice_obj = new NoticeModel();
        $notice_list = $notice_obj->getNoticeLimitList('notice_id,title,description,path_img,addtime', 'isuse = 1', 'serial', 0, 4);

        //轮播图
        $cust_flash_obj = new CustFlashModel();
        $cust_flash_list = $cust_flash_obj->getCustFlashList('pic,link', 'isuse = 1 AND adv_type ='.$type, 'serial');

        //实物礼品列表
        $material_obj =  new MaterialModel();
        $material_obj->setStart(0);
        $material_obj->setLimit(8);
        $material_list = $material_obj->getMaterialList('material_id,name,img_url,money','isuse = 1','serial DESC');

        return array(
            'game_type_list' => $game_type_list,
            'notice_list' => $notice_list,
            'cust_flash_list' => $cust_flash_list?:[],
            'material_list' => $material_list?:[],
        );
    }


    /**
     * 获取图形验证码
     * @date: 2019/3/11
     * @return string
     * @author: hui
     */
    public function imgcode()
    {
        $host = 'http://' . $_SERVER['HTTP_HOST'];
        $url = $host . '/Public/verify/';
        return $url;
    }


    /**
     * 游戏系列列表
     * @date: 2019/3/19
     * @return array
     * @author: hui
     */
    public function getGameTypeList($params)
    {
        $is_app = isset($params['is_app'])?$params['is_app']:0;
        $game_series_obj = new GameSeriesModel();
        $game_series_list = $game_series_obj->getGameSeriesAll('', 'isuse = 1 and game_series_id !=10');
        $game_series_list = $game_series_obj->getListData($game_series_list,$is_app);
        return $game_series_list;
    }

    public function getGameTypeListLIUHECAI($params)
    {
        $is_app = isset($params['is_app'])?$params['is_app']:0;
        $game_series_obj = new GameSeriesModel();
        $game_series_list = $game_series_obj->getGameSeriesAll('', 'isuse = 1 and game_series_id = 10');
        $game_series_list = $game_series_obj->getListData($game_series_list,$is_app);
        return $game_series_list;
    }


    /**
     * 开奖结果
     * @param $params
     * @date: 2019/3/19
     * @return array
     * @author: hui
     */
    public function nowResult($params)
    {
        $game_log_obj = new GameLogModel();
        $game_type_obj = new GameTypeModel();
        $game_result_obj = new GameResultModel();
        $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $params['game_type_id'],
            'game_type_id,game_type_name,min_bet_money,max_bet_money,max_win_money,bet_json,game_rule,part_type,start_time,end_time,issue_num,each_issue_time,is_kill');
        $game_log_info = $game_log_obj->where('game_type_id =' . $params['game_type_id'])
            ->field('game_result_id,part_one_result,part_two_result,part_three_result,part_four_result,part_five_result,part_six_result,result,ex_result')
            ->order('addtime DESC')->find();
        $game_result_info = $game_result_obj->getGameResultInfo('game_result_id =' . $game_log_info['game_result_id'], 'result,issue') ?: [];
        $game_type_id = $params['game_type_id'];
        if ($game_type_id == GameTypeModel::BTBONE28 || $game_type_id == GameTypeModel::BTBTWO28 || $game_type_id == GameTypeModel::BTBTHREE28) {
            $game_result_info['result'] = $game_log_info['part_one_result'] . ',' . $game_log_info['part_two_result'] . ',' . $game_log_info['part_three_result'];
        }
        if ($game_type_info['is_kill']) {
            $game_result_info['result'] = $game_log_info['ex_result'];
        }
        //今日盈亏
        $bet_log_obj = new BetLogModel();
        $begin_time = strtotime(date('Ymd'));
        $end_time = strtotime(date('Ymd 23:59:59'));
        $user_id = session('user_id') ?: 0;
        $where = 'addtime > ' . $begin_time . ' AND addtime < ' . $end_time . ' AND game_type_id = ' . $params['game_type_id'] . ' AND user_id =' . $user_id . ' AND is_open = 1';
        $bet_log_info = $bet_log_obj->getBetLogInfo($where,
            'count(*) as total_issue,sum(total_bet_money) as total_bet_money,sum(total_after_money) as total_after_money');
        $bet_win = $bet_log_obj->getBetLogNum($where . ' AND is_win = 1');
        $rate = round($bet_win / $bet_log_info['total_issue'] * 100, 2);
        $bet_log_info['rate'] = $rate;
        $bet_log_info['bet_win'] = $bet_win;
        $bet_log_info['total_bet_money'] = $bet_log_info['total_bet_money'] ?: 0;
        $bet_log_info['total_after_money'] = $bet_log_info['total_after_money'] ?: 0;
        $bet_log_info['win_loss'] = $bet_log_info['total_after_money'] - $bet_log_info['total_bet_money'];


        return array(
            'game_type_info' => $game_type_info,
            'game_log_info' => $game_log_info,
            'game_result_info' => $game_result_info,
            'bet_log_info' => $bet_log_info,
        );
    }


    /**
     * 开奖列表
     * @param $params
     * @date: 2019/3/19
     * @return array
     * @author: hui
     */
    public function getResultLogList($params)
    {
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $user_id = session('user_id');
        $firstRow = $params['firstRow'] ? $params['firstRow'] : 0;
        $pageSize = $params['pageSize'] ? $params['pageSize'] : 10;

        $data = $redis_obj->get($user_id.'_'.$params['game_type_id'].$firstRow.'_getResultLogList');
        if ($data) {
            return json_decode($data,true);
        }

        $game_result_obj = new GameResultModel();
        $game_type_obj = new GameTypeModel();
        $result_type = $game_type_obj->getGameTypeField('game_type_id =' . $params['game_type_id'], 'result_type');
        $start_time = strtotime('-3 day');  //todo 2019/10/18 客户反馈只展示三天内的开奖数据
        $where = 'type =' . $result_type.' AND addtime >='.$start_time;
        //总数ce
        $total = $game_result_obj->getGameResultNum($where);
        $game_result_obj->setStart($firstRow);
        $game_result_obj->setLimit($pageSize);
        $game_result_list = $game_result_obj->getGameResultList('', $where, 'game_result_id DESC');
//        echo $game_result_obj->getLastSql();
        $game_result_list = $game_result_obj->getListData($game_result_list, $params['game_type_id'], $user_id);

        $data = array(
            'game_result_list' => $game_result_list ?: [],
            'total' => $total,
        );
        $redis_obj->set($user_id.'_'.$params['game_type_id'].'_getResultLogList',json_encode($data),15);
        return $data;
    }

    /**
     * 投注
     * @param $params
     * @date: 2019/3/28
     * @return bool
     * @author: hui
     */
    public function gameBet($params)
    {
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->field('is_bet,isuse,game_type_id')->where('game_type_id =' . $params['game_type_id'])->find();
        if (($params['game_type_id']>=GameTypeModel::HANGUO28 && $params['game_type_id']<=GameTypeModel::HANGUODINGWEI)
            ||$params['game_type_id'] == GameTypeModel::HANGUO28GUDING)
        {
            $kill = 1;
        }else {
            $kill = 0;
        }
        if($game_type_info['is_bet'] != 1 || $game_type_info['isuse'] != 1)
        {
            ApiModel::returnResult(2000,null,'当前不可投注');
        }

        $bet_log_obj = new BetLogModel();
        $user_id = session('user_id');
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'gameBet_'.$user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }
        $bet_json = htmlspecialchars_decode($params['bet_json']);
        $game_result_obj = new GameResultModel();
        $is_open = $game_result_obj->getGameResultField('game_result_id =' . $params['game_result_id'], 'is_open');
        $addtime = $game_result_obj->getGameResultField('game_result_id =' . $params['game_result_id'], 'addtime');


        $total_money = 0;
        $bet_arr = json_decode($bet_json,true);
        foreach ($bet_arr as $k => $v)
        {
            if(!is_array($v['bet_json']))
            {
                continue;
            }
            foreach ($v['bet_json'] as $key => $val)
            {
                if($val['money'] <= 0)
                {
                    ApiModel::returnResult(-1, '', '投注不能小于0');
                }
                $total_money += $val['money'];
            }
        }

        //小于1000不能投注
        if ($total_money < 1000) {
            ApiModel::returnResult(-1, '', '投注不能小于1000');
        }

        $bet_log_info = $bet_log_obj->getBetLogInfo('game_result_id = ' . $params['game_result_id'] . ' AND game_type_id = ' . $params['game_type_id'] . ' AND user_id = ' . $user_id);
        //获取之前投注的
        $has_bet = $bet_log_info['total_bet_money']?:0;
        $game_type_obj = new GameTypeModel();
        $max_bet_money = $game_type_obj->getGameTypeField('game_type_id =' . $params['game_type_id'], 'max_bet_money');
        if ($max_bet_money < $total_money + $has_bet) {
            ApiModel::returnResult(-1, '', '投注不能大于最高投注额:' . $max_bet_money);
        }
        //判断是否有自动投注
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_info = $bet_auto_obj->getBetAutoInfo('user_id =' . $user_id . ' AND game_type_id =' . $params['game_type_id'] . ' AND is_open = 1');
        if ($bet_auto_info) {
            ApiModel::returnResult(-1, '', '请先取消自动投注');
        }

        if (!$bet_json) {
            ApiModel::returnResult(-1, '', '投注不能为空');
        }
        if ($is_open) {
            ApiModel::returnResult(-1, '', '已开奖，不可投注');
        }

        //系列id
        $game_series_id = $game_type_obj->getGameTypeField('game_type_id =' . $params['game_type_id'], 'game_series_id');
        if($game_series_id == GameResultModel::BJKLB){
            $ex_time = 80;
//            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::BJPKS){
            $ex_time = 80;
//            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::JNDKLB){
            $ex_time = 30;
//            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::HGXL){
            $ex_time = 30;
//            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::CQSSC){
//            $ex_time = 65;
            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::BTBONE){
            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::BTBTWO){
            $ex_time = 10;
        }elseif($game_series_id == GameResultModel::BTBTHREE){
            $ex_time = 10;
        }
        elseif($game_series_id == GameResultModel::FEITING){
            $ex_time = 10;
        }

        elseif($game_series_id == GameResultModel::XIANGGANGLIUHECAI){
            $ex_time = 30;
        }

        if ($addtime < time() + $ex_time) {
            ApiModel::returnResult(-1, '', '开奖中，不可投注');
        }

//        return json_decode(htmlspecialchars_decode($params['bet_json']),true);
        $account_obj = new AccountModel();
        $account = $account_obj->addAccount($user_id, AccountModel::BETTING, $total_money * -1, '游戏投注', '');
        if ($account < 0) {
            ApiModel::returnResult(-1, '', '余额不足');
        }
        $game_bet_money_obj = new GameBetMoneyModel();
        $bet_log_id = 0;
        if (!$bet_log_info) {
            //没有投注记录  添加
            $arr = array(
                'user_id' => $user_id,
                'game_result_id' => $params['game_result_id'],
                'total_bet_money' => $total_money,
                'bet_json' => $bet_json,
                'game_type_id' => $params['game_type_id'],
                'pan_type' => $params['pankou']?:1,
            );
            $res = $bet_log_obj->addBetLog($arr);
            if (!$res) {
                ApiModel::returnResult(-1, '', '投注失败');
            }
            $bet_log_id = $res;
            $bet_arr = json_decode($bet_json, true);
//            echo $bet_json;die;
            foreach ($bet_arr as $k => &$v) {
                if ($kill == 1){
                    foreach ($v['bet_json'] as $value) {
                        $game_bet_money_obj->where(['game_result_id'=>$params['game_result_id'],'game_type_id' => $game_type_info['game_type_id']])->setInc($value['key'],$value['money']);
                    }
                }

            }
        } else {
            $bet_log_id = $bet_log_info['bet_log_id'];
            //有投注记录
            //投注信息更新 ，投注json
//            echo $bet_json;die;
            $bet_arr = json_decode($bet_json, true);
            $old_bet_arr = json_decode($bet_log_info['bet_json'], true);
            foreach ($bet_arr as $k => &$v) {
                foreach ($old_bet_arr as $ki => &$vi) {
//                    if ($vi['key'] == $v['key']) {
//                        $v['money'] += $vi['money'];
//                        unset($old_bet_arr[$ki]);
//
                    if ($vi['part'] == $v['part']) {
//                        echo 123;
                        foreach ($v['bet_json'] as $kk => &$vv) {
                            foreach ($vi['bet_json'] as $kki => &$vvi) {
                                if ($vv['key'] == $vvi['key']) {
                                    $vv['money'] += $vvi['money'];
                                    if ($kill == 1){
                                        $game_bet_money_obj->where(['game_result_id'=>$params['game_result_id'],'game_type_id' => $game_type_info['game_type_id']])->setInc($vvi['key'],$vvi['money']);

                                    }
//                                    echo json_encode($vvi);die;
//                                    echo $game_bet_money_obj->getLastSql();
//                                    echo 222;
                                    unset($vi['bet_json'][$kki]);
                                }
                            }
                            unset($vvi);
                        }
                        unset($vv);
                        unset($old_bet_arr[$ki]);

                        $v['bet_json'] = array_merge_recursive($v['bet_json'], $vi['bet_json']);
                    }
                }
                unset($vi);
            }
            unset($v);
            $bet_arr = array_merge_recursive($bet_arr, $old_bet_arr);
            $bet_arr = json_encode($bet_arr);
//            //根据投注key值排序
//            foreach ($bet_arr as $kkk => $vvv) {
//                $keysvalue[$kkk] = $vvv['part'];
//                foreach ($vvv['bet_json'] as $kkki => $vvvi){
//                    $keysvalue2[$kkki] = $vvvi['key'];
//                    asort($keysvalue2);
//                    reset($keysvalue2);
//                    foreach ($keysvalue2 as $keyi => $valuei) {
//                        $new_array2[$keyi] = $bet_arr[$kkki][$keyi];
//                    }
//                    $new_array2 = array_values($new_array2);
//                    $vvv['bet_json'] = $new_array2;
//                }
//            }
//            asort($keysvalue);
//            reset($keysvalue);
//
//            foreach ($keysvalue as $key => $value) {
//                $new_array[$key] = $bet_arr[$key];
//            }
//            $new_array = array_values($new_array);
//            $new_array = json_encode($new_array);
            $arr = array(
                'total_bet_money' => $total_money + $bet_log_info['total_bet_money'],
                'bet_json' => $bet_arr,
            );
            $res = $bet_log_obj->editBetLog('bet_log_id =' . $bet_log_info['bet_log_id'], $arr);
            if ($res === false) {
                ApiModel::returnResult(-1, '', '投注失败');
            }
        }

        //六合彩返水
        if ($params['game_type_id'] >= GameTypeModel::TEMA && $params['game_type_id'] <= GameTypeModel::LIANMA) {
            $bet_json_arr = json_decode($bet_json,true);
            $game_log_obj = new GameLogModel();
            foreach ($bet_json_arr as $kliu => $vliu) {
                        $money = 0;
                    foreach ($vliu['bet_json'] as $valueliu) {
                        $money+=$valueliu['money'];
                    }
                    $game_log_obj->tuishui(
                        $params['game_type_id'].'-'.$vliu['part'],
                        $money,
                        $params['pankou'],
                        $user_id,
                        $bet_log_id
                        );


            }

        }

        return '投注成功';
    }

    public function gameBetLiuhecai($params)
    {
        $bet_json_all = htmlspecialchars_decode($params['bet_json']);
        $bet_json_all = json_decode($bet_json_all,true);
        foreach ($bet_json_all as $bet_json) {
            $game_type_obj = new GameTypeModel();
            $game_type_info = $game_type_obj->field('is_bet,isuse,game_type_id')->where('game_type_id =' . $bet_json['game_type_id'])->find();

            if($game_type_info['is_bet'] != 1 || $game_type_info['isuse'] != 1)
            {
                ApiModel::returnResult(2000,null,'当前不可投注');
            }
        }

        $bet_log_obj = new BetLogModel();
        $user_id = session('user_id');
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'gameBet_'.$user_id;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
            ApiModel::returnResult(2000,null,'操作中');
        }
        foreach ($bet_json_all as $data) {
            $game_type_obj = new GameTypeModel();
            $game_type_info = $game_type_obj->field('is_bet,isuse,game_type_id')->where('game_type_id =' . $data['game_type_id'])->find();
            $game_result_obj = new GameResultModel();
            $is_open = $game_result_obj->getGameResultField('game_result_id =' . $data['game_result_id'], 'is_open');
            $addtime = $game_result_obj->getGameResultField('game_result_id =' . $data['game_result_id'], 'addtime');
            $total_money = 0;
            $bet_arr = $data['bet_json'];
//            print_r($bet_arr);die;
            foreach ($bet_arr as $k => $v)
            {
//                print_r($v);die;
                if(!is_array($v['bet_json']))
                {
                    continue;
                }
                foreach ($v['bet_json'] as $key => $val)
                {
                    if($val['money'] <= 0)
                    {
                        ApiModel::returnResult(-1, '', '投注不能小于0');
                    }
                    $total_money += $val['money'];
                }
            }
//            echo $total_money;die;
//小于1000不能投注
            if ($total_money < 1000) {
//                ApiModel::returnResult(-1, '', '投注不能小于1000');
            }

            $bet_log_info = $bet_log_obj->getBetLogInfo('game_result_id = ' . $data['game_result_id'] . ' AND game_type_id = ' . $data['game_type_id'] . ' AND user_id = ' . $user_id);
            //获取之前投注的
            $has_bet = $bet_log_info['total_bet_money']?:0;
            $game_type_obj = new GameTypeModel();
            $max_bet_money = $game_type_obj->getGameTypeField('game_type_id =' . $data['game_type_id'], 'max_bet_money');
            if ($max_bet_money < $total_money + $has_bet) {
                ApiModel::returnResult(-1, '', '投注不能大于最高投注额:' . $max_bet_money);
            }
            //判断是否有自动投注
            $bet_auto_obj = new BetAutoModel();
            $bet_auto_info = $bet_auto_obj->getBetAutoInfo('user_id =' . $user_id . ' AND game_type_id =' . $data['game_type_id'] . ' AND is_open = 1');
            if ($bet_auto_info) {
                ApiModel::returnResult(-1, '', '请先取消自动投注');
            }

            if (!$data['bet_json']) {
                ApiModel::returnResult(-1, '', '投注不能为空');
            }
            if ($is_open) {
                ApiModel::returnResult(-1, '', '已开奖，不可投注');
            }

            //系列id
            $game_series_id = $game_type_obj->getGameTypeField('game_type_id =' . $data['game_type_id'], 'game_series_id');
            $ex_time = 30;


            if ($addtime < time() + $ex_time) {
                ApiModel::returnResult(-1, '', '开奖中，不可投注');
            }

            $account_obj = new AccountModel();
            $account = $account_obj->addAccount($user_id, AccountModel::BETTING, $total_money * -1, '游戏投注', '');
            if ($account < 0) {
                ApiModel::returnResult(-1, '', '余额不足');
            }
            $game_bet_money_obj = new GameBetMoneyModel();
            $bet_log_id = 0;
//            echo json_encode($data);die;
//            echo $data['bet_json'];die;
            if (!$bet_log_info) {
                //没有投注记录  添加
                $arr = array(
                    'user_id' => $user_id,
                    'game_result_id' => $data['game_result_id'],
                    'total_bet_money' => $total_money,
                    'bet_json' => json_encode($data['bet_json']),
                    'game_type_id' => $data['game_type_id'],
                    'pan_type' => $data['pankou']?:1,
                );
                $res = $bet_log_obj->addBetLog($arr);
                if (!$res) {
                    ApiModel::returnResult(-1, '', '投注失败');
                }
                $bet_log_id = $res;

            } else {
                $bet_log_id = $bet_log_info['bet_log_id'];
                //有投注记录
                //投注信息更新 ，投注json
//            echo $bet_json;die;
                $bet_arr = $data['bet_json'];
                $old_bet_arr = json_decode($bet_log_info['bet_json'], true);
                foreach ($bet_arr as $k => &$v) {
                    foreach ($old_bet_arr as $ki => &$vi) {

                        if ($vi['part'] == $v['part']) {
//                        echo 123;
                            foreach ($v['bet_json'] as $kk => &$vv) {
                                foreach ($vi['bet_json'] as $kki => &$vvi) {
                                    if ($vv['key'] == $vvi['key']) {
                                        $vv['money'] += $vvi['money'];
                                        unset($vi['bet_json'][$kki]);
                                    }
                                }
                                unset($vvi);
                            }
                            unset($vv);
                            unset($old_bet_arr[$ki]);

                            $v['bet_json'] = array_merge_recursive($v['bet_json'], $vi['bet_json']);
                        }
                    }
                    unset($vi);
                }
                unset($v);
                $bet_arr = array_merge_recursive($bet_arr, $old_bet_arr);
                $bet_arr = json_encode($bet_arr);
                $arr = array(
                    'total_bet_money' => $total_money + $bet_log_info['total_bet_money'],
                    'bet_json' => $bet_arr,
                );
                $res = $bet_log_obj->editBetLog('bet_log_id =' . $bet_log_info['bet_log_id'], $arr);
                if ($res === false) {
                    ApiModel::returnResult(-1, '', '投注失败');
                }
            }
            //六合彩返水
            if ($data['game_type_id'] >= GameTypeModel::TEMA && $data['game_type_id'] <= GameTypeModel::LIANMA) {
                $bet_json_arr = json_decode($data['bet_json'],true);
                $game_log_obj = new GameLogModel();
                foreach ($bet_json_arr as $kliu => $vliu) {
                    $money = 0;
                    foreach ($vliu['bet_json'] as $valueliu) {
                        $money+=$valueliu['money'];
                    }
                    $game_log_obj->tuishui(
                        $data['game_type_id'].'-'.$vliu['part'],
                        $money,
                        $data['pankou'],
                        $user_id,
                        $bet_log_id
                    );
                }
            }
        }
        return '投注成功';
    }


    /**
     * 获取上次投注
     * @param $params
     * @date: 2019/3/29
     * @return mixed
     * @author: hui
     */
    public function getLastBetLog($params)
    {
        $bet_log_obj = new BetLogModel();
        $user_id = session('user_id');
        $bet_log_info = $bet_log_obj->where('game_type_id =' . $params['game_type_id'] . '  AND user_id = ' . $user_id)
            ->order('addtime DESC')->field('bet_json')->find();
        return $bet_log_info ?: [];
    }


    /**
     * 新建/修改投注模式
     * @param $params
     * @date: 2019/3/29
     * @return mixed
     * @author: hui
     */
    public function newBetMode($params)
    {
        $bet_mode = new BetModeModel();
        $user_id = session('user_id');
        $params['bet_mode_id'] = $params['bet_mode_id'] ?: 0;
        //判断名称是否存在
        $bet_mode_info = $bet_mode->getBetModeInfo('mode_name = "' . $params['mode_name'] . '" AND user_id =' . $user_id . ' AND game_type_id = ' . $params['game_type_id'] . ' AND bet_mode_id !=' . $params['bet_mode_id']);
        if ($bet_mode_info) {
            ApiModel::returnResult(-1, '', '模式名称已经存在');
        }
        $bet_json = htmlspecialchars_decode($params['bet_json']);

        $bet_arr = json_decode($bet_json,true);
        $total_money = 0;
        foreach ($bet_arr as $k => $v)
        {
            if(!is_array($v['bet_json']))
            {
                continue;
            }
            foreach ($v['bet_json'] as $key => $val)
            {
                if($val['money'] <= 0)
                {
                    ApiModel::returnResult(-2, '', '数据有误');
                }
                $total_money += $val['money'];
            }
        }
        if(!$total_money)
        {
            ApiModel::returnResult(-2, '', '数据有误');
        }
        $arr = array(
            'user_id' => $user_id,
            'game_type_id' => $params['game_type_id'],
            'mode_name' => $params['mode_name'],
            'total_money' => $total_money,
            'bet_json' => $bet_json,
        );
        if ($params['bet_mode_id']) {
            $sueecss = $bet_mode->editBetMode('bet_mode_id =' . $params['bet_mode_id'], $arr);
            if ($sueecss === false) {
                ApiModel::returnResult(-1, '', '修改失败');
            }
        } else {
            $sueecss = $bet_mode->addBetMode($arr);
            if (!$sueecss) {
                ApiModel::returnResult(-1, '', '添加失败');
            }
        }
        return $sueecss;
    }


    public function getLastBetInfo($params)
    {
        $bet_log_obj = new BetLogModel();
        $game_log_obj = new GameLogModel();
        $game_type_obj = new GameTypeModel();
        $user_id = session('user_id');
        $have_bet_json = $bet_log_obj->getBetLogField('game_result_id =' . $params['game_result_id'] . ' AND game_type_id =' . $params['game_type_id'] . ' AND user_id =' . $user_id, 'bet_json');
        $last_bet_json = $game_log_obj->where('game_type_id =' . $params['game_type_id'])
            ->order('addtime DESC')->getField('bet_json');
        $new_bet_json = $game_type_obj->getGameTypeField('game_type_id =' . $params['game_type_id'], 'bet_json');
        return array(
            'have_bet' => $have_bet_json ?: '',
            'last_bet_rate' => $last_bet_json ?: '',
            'new_bet_rate' => $new_bet_json ?: '',
        );

    }

    public function getResult($params)
    {
        $obj = new GameResultModel();
        $obj->addResult($params['type']);
        return $obj->getCaipiaokongApi($params['type']);
    }

    public function test($params)
    {
        $game_log_obj = new GameLogModel();
        return $game_log_obj->hanguo28Kill($params['id']);
//        return $game_log_obj->calculateResult(2294, 2);
//        $obj = new BetAutoModel();
//        return $obj->gameAutoBet(1);
//        $obj = new UserModel();
////       return $obj->addUser();
//        $user_list = $obj->field('user_id')->where('role_type != 1')->select();
//        foreach ($user_list as $k => $v){
//
//            $id_have_exist = 1;
//            $k = 0;
//            while($id_have_exist && $k < 10){
//                $id = '';
//                for ($i = 0; $i < 6; $i++) {
//                    $rand = rand(0, 9);
//                    $id .= $rand;
//                }
//                $k++;
//                $id_have_exist = $this->where('id ='.$id)->field('user_id')->find();
//            }
//
//            $obj->where('user_id ='.$v['user_id'])->save(['id' => $id]);
//        }

//        $obj = new GameResultModel();
//        $obj->addResult(GameResultModel::BTBONE);
//        return $obj->getBtbApi(GameResultModel::BTBONE);
//        $json = '[{"part":1,"name":"前三","bet_json":[{"key":1,"name":"豹","rate":96},{"key":2,"name":"顺","rate":16.0032},{"key":3,"name":"对","rate":3.552},{"key":4,"name":"半","rate":2.6688},{"key":5,"name":"杂","rate":3.1968}]},{"part":2,"name":"中三","bet_json":[{"key":1,"name":"豹","rate":96},{"key":2,"name":"顺","rate":16.0032},{"key":3,"name":"对","rate":3.552},{"key":4,"name":"半","rate":2.6688},{"key":5,"name":"杂","rate":3.1968}]},{"part":3,"name":"后三","bet_json":[{"key":1,"name":"豹","rate":96},{"key":2,"name":"顺","rate":16.0032},{"key":3,"name":"对","rate":3.552},{"key":4,"name":"半","rate":2.6688},{"key":5,"name":"杂","rate":3.1968}]},{"part":4,"name":"第一球","bet_json":[{"key":1,"name":"大","rate":1.96},{"key":2,"name":"小","rate":1.96},{"key":3,"name":"单","rate":1.96},{"key":4,"name":"双","rate":1.96},{"key":5,"name":0,"rate":9.8},{"key":6,"name":1,"rate":9.8},{"key":7,"name":2,"rate":9.8},{"key":8,"name":3,"rate":9.8},{"key":9,"name":4,"rate":9.8},{"key":10,"name":5,"rate":9.8},{"key":11,"name":6,"rate":9.8},{"key":12,"name":7,"rate":9.8},{"key":13,"name":8,"rate":9.8},{"key":14,"name":9,"rate":9.8}]},{"part":5,"name":"第二球","bet_json":[{"key":1,"name":"大","rate":1.96},{"key":2,"name":"小","rate":1.96},{"key":3,"name":"单","rate":1.96},{"key":4,"name":"双","rate":1.96},{"key":5,"name":0,"rate":9.8},{"key":6,"name":1,"rate":9.8},{"key":7,"name":2,"rate":9.8},{"key":8,"name":3,"rate":9.8},{"key":9,"name":4,"rate":9.8},{"key":10,"name":5,"rate":9.8},{"key":11,"name":6,"rate":9.8},{"key":12,"name":7,"rate":9.8},{"key":13,"name":8,"rate":9.8},{"key":14,"name":9,"rate":9.8}]},{"part":6,"name":"第三球","bet_json":[{"key":1,"name":"大","rate":1.96},{"key":2,"name":"小","rate":1.96},{"key":3,"name":"单","rate":1.96},{"key":4,"name":"双","rate":1.96},{"key":5,"name":0,"rate":9.8},{"key":6,"name":1,"rate":9.8},{"key":7,"name":2,"rate":9.8},{"key":8,"name":3,"rate":9.8},{"key":9,"name":4,"rate":9.8},{"key":10,"name":5,"rate":9.8},{"key":11,"name":6,"rate":9.8},{"key":12,"name":7,"rate":9.8},{"key":13,"name":8,"rate":9.8},{"key":14,"name":9,"rate":9.8}]},{"part":6,"name":"第四球","bet_json":[{"key":1,"name":"大","rate":1.96},{"key":2,"name":"小","rate":1.96},{"key":3,"name":"单","rate":1.96},{"key":4,"name":"双","rate":1.96},{"key":5,"name":0,"rate":9.8},{"key":6,"name":1,"rate":9.8},{"key":7,"name":2,"rate":9.8},{"key":8,"name":3,"rate":9.8},{"key":9,"name":4,"rate":9.8},{"key":10,"name":5,"rate":9.8},{"key":11,"name":6,"rate":9.8},{"key":12,"name":7,"rate":9.8},{"key":13,"name":8,"rate":9.8},{"key":14,"name":9,"rate":9.8}]},{"part":6,"name":"第五球","bet_json":[{"key":1,"name":"大","rate":1.96},{"key":2,"name":"小","rate":1.96},{"key":3,"name":"单","rate":1.96},{"key":4,"name":"双","rate":1.96},{"key":5,"name":0,"rate":9.8},{"key":6,"name":1,"rate":9.8},{"key":7,"name":2,"rate":9.8},{"key":8,"name":3,"rate":9.8},{"key":9,"name":4,"rate":9.8},{"key":10,"name":5,"rate":9.8},{"key":11,"name":6,"rate":9.8},{"key":12,"name":7,"rate":9.8},{"key":13,"name":8,"rate":9.8},{"key":14,"name":9,"rate":9.8}]}]';
//        return json_encode(json_decode($json));
//        $obj = new GameLogModel();
//        return $obj->calculateResult(7919,6);



    }

    public function randHanguoResult()
    {
        $game_result_obj = new GameResultModel();
        return $game_result_obj->randHanguoResult();
    }

    /**
     * 投注记录
     * @param $params
     * @date: 2019/3/29
     * @return array
     * @author: yzp
     */
    public function getBetLogList($params)
    {
        $bet_log_obj = new BetLogModel();

        $user_id = session('user_id') ?: 0;

        $where = 'game_type_id = ' . $params['game_type_id'] . ' AND user_id =' . $user_id;

        //只展示7天
        $start_time = strtotime(date('Y-m-d', strtotime('-7 days')));
        $where .= ' AND addtime > ' . $start_time;

        $firstRow = $params['firstRow'] ? $params['firstRow'] : 0;
        $fetchNum = $params['fetchNum'] ? $params['fetchNum'] : 10;
        //总数
        $total = $bet_log_obj->getBetLogNum($where);
        $bet_log_obj->setStart($firstRow);
        $bet_log_obj->setLimit($fetchNum);
        $bet_log_list = $bet_log_obj->getBetLogList('', $where, 'game_result_id desc');
        $bet_log_list = $bet_log_obj->getListData($bet_log_list);
        // dump($bet_log_obj->getLastSql());die;
        return array(
            'bet_log_list' => $bet_log_list ?: [],
            'total' => $total,
        );
    }


    /**
     * 盈利统计
     * @param $params
     * @date: 2019/3/29
     * @return array
     * @author: yzp
     */
    public function getWinList($params)
    {

        $game_type_obj = new GameTypeModel();

        $user_id = session('user_id') ?: 0;

        $where = 'game_type_id = ' . $params['game_type_id'] . ' AND user_id =' . $user_id;

        $game_type_list = $game_type_obj->getGameTypeAll('game_type_id,game_type_name', 'isuse = 1');
        //统计
        $game_type_list = $game_type_obj->getDailyData($game_type_list, $user_id);

        return array(
            'game_type_list' => $game_type_list
        );
    }

    /**
     * 获取新的盈利统计
     * @return array
     * @author yzp
     * @Date:  2019/8/16
     * @Time:  10:09
     */
    public function getNewWinList()
    {

        $game_type_obj = new GameTypeModel();

        $user_id = session('user_id') ?: 0;

        $game_type_list = $game_type_obj->getGameTypeAll('game_type_id', 'isuse = 1','game_type_id asc');
        //统计
        $game_type_list = $game_type_obj->getDailyDataNew($game_type_list, $user_id);

        return array(
            'game_type_list' => $game_type_list
        );
    }


    /**
     * 走势图
     * @param $params
     * @date: 2019/3/29
     * @return array
     * @author: yzp
     */
    public function getRunChart($params)
    {
        $arr = [];
        $game_log_obj = new GameLogModel();
        //查询游戏记录条数
        switch ($params['type']) {
            case self::FIRST:
                $fetchNum = 100;
                break;
            case self::SECOND:
                $fetchNum = 200;
                break;
            case self::THIRD:
                $fetchNum = 300;
                break;
            case self::FOURTH:
                $fetchNum = 400;
                break;
            case self::FIFTH:
                $fetchNum = 500;
                break;
            default:
                $fetchNum = 100;
                break;
        }
        //走势图头
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $params['game_type_id'], 'chart_json,is_use_result');
        $arr['is_use_result'] = $game_type_info['is_use_result'];
        $chart_json = json_decode($game_type_info['chart_json'], true);
        foreach ($chart_json as $key => &$value) {
            $value['real_num'] = 0;
        }
        unset($value);
        $last_arr = [];
        $one_sum = ['1' => 0, '2' => 0, '3' => 0];
        $two_sum = ['1' => 0, '2' => 0, '3' => 0];
        if ($game_type_info['is_use_result'] == 2) {
            $fetchNum = 300;
        }

        //结果数据
        $game_log_obj->setStart(0);
        $game_log_obj->setLimit($fetchNum);
        $game_log_list = $game_log_obj->getGameLogList('game_result_id,result,last_result,part_one_result,
        part_two_result,part_three_result,part_four_result,part_five_result,part_six_result,sixteen_hash,ten_hash,sub_result,sixteen_data,ten_data,result_data',
            'game_type_id =' . $params['game_type_id'], 'addtime DESC');
        $game_log_list = $game_log_obj->getResultList($game_log_list, $params['game_type_id']);

        switch ($game_type_info['is_use_result']) {
//        0数字加大小  1结果记录  2庄闲  3只有结果没有大小单双
            case 0:
                foreach ($game_log_list as $k => $v) {
                    foreach ($chart_json as $ki => &$vi) {
                        if ($v['result'] == $vi['key']) {
                            $vi['real_num']++;
                        }
                    }
                    unset($vi);
                    foreach ($chart_json as $kii => &$vii) {
                        $vii['rate'] = round($vii['real_num'] / $vii['num'], 2);
                    }
                    unset($vii);
                    $last_result = explode(',', $v['last_result']);
                    foreach ($last_result as $j => $i) {
                        if ($i == 1 && $j < 6) {
                            $last_arr[$j]++;
                        }
                    }

                }
                $arr['chart_json'] = $chart_json;
                $arr['last_arr'] = implode(',', $last_arr);
                $arr['game_log_list'] = $game_log_list;
                break;
            case 3:
                foreach ($game_log_list as $k => $v) {
                    foreach ($chart_json as $ki => &$vi) {
                        if ($v['result'] == $vi['key']) {
                            $vi['real_num']++;
                        }
                    }
                    unset($vi);
                    foreach ($chart_json as $kii => &$vii) {
                        $vii['rate'] = round($vii['real_num'] / $vii['num'], 2);
                    }
                    unset($vii);
                }
                $arr['chart_json'] = $chart_json;
                $arr['game_log_list'] = $game_log_list ?: [];
                break;
            case 2:
                foreach ($game_log_list as $k => $v) {
                    $result = explode(',', $v['result']);
                    $one_result_list[] = $result[0];
                    $one_sum[$result[0]]++;
                    if ($result[1] <= GameTypeModel::PING) {
                        $two_result_list[] = $result[1];
                        $two_sum[$result[1]]++;
                    } else {
                        unset($two_sum);
                    }
                }
                $arr['one_result_list'] = array_reverse($one_result_list) ?: [];
                $arr['two_result_list'] = array_reverse($two_result_list) ?: [];
                $arr['one_sum'] = $one_sum;
                $arr['two_sum'] = $two_sum ?: [];
                break;
            case 1:
//                $arr['chart_json'] = $chart_json;
                $arr['game_log_list'] = $game_log_list ?: [];
                break;
        }
        return $arr;
    }

    /**
     * 游戏规则
     * @param $params
     * @date: 2019/3/29
     * @return array
     * @author: yzp
     */
    public function getGameRule($params)
    {
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id = ' . $params['game_type_id'], 'game_rule');
        $game_rule = htmlspecialchars_decode($game_type_info['game_rule']);
        return array(
            'game_rule' => $game_rule
        );
    }


    /**
     * 投注模式列表
     * @param $params
     * @date: 2019/3/30
     * @return array
     * @author: yzp
     */
    public function BetModeList($params)
    {
        $bet_mode_obj = new BetModeModel();
        $user_id = session('user_id');

        $where = 'user_id = ' . $user_id . ' AND game_type_id = ' . $params['game_type_id'];

//        $firstRow = $params['firstRow'] ? $params['firstRow'] : 0;
//        $fetchNum = $params['fetchNum'] ? $params['fetchNum'] : 10;
        //总数
        $total = $bet_mode_obj->getBetModeNum($where);
//        $bet_mode_obj->setStart($firstRow);
//        $bet_mode_obj->setLimit($fetchNum);
        $bet_mode_list = $bet_mode_obj->getBetModeListALL('bet_mode_id,mode_name,bet_json,total_money,loss_change,win_change', $where);
        $bet_mode_list = $bet_mode_obj->getListData($bet_mode_list);

        return array(
            'bet_mode_list' => $bet_mode_list ?: [],
            'total' => $total,
        );

    }

    /**
     * 删除模式
     * @param $params
     * @date: 2019/4/8
     * @return string
     */
    public function delBetMode($params)
    {
        $bet_mode_obj = new BetModeModel();
        $where = 'bet_mode_id = ' . $params['bet_mode_id'];
        $bet_mode_obj->where($where)->delete();

        return '删除成功';

    }

    /**
     * 自动投注
     * @param $params
     * @date: 2019/4/1
     * @return text
     * @author: yzp
     */
    public function setAutoBet($params)
    {
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->field('is_bet,isuse')->where('game_type_id =' . $params['game_type_id'])->find();
        if($game_type_info['is_bet'] != 1 || $game_type_info['isuse'] != 1)
        {
            ApiModel::returnResult(2000,null,'当前不可投注');
        }

        $bet_auto_obj = new BetAutoModel();
        $bet_mode_obj = new BetModeModel();
        $user_id = session('user_id');

        $where = 'user_id =' . $user_id . ' AND game_type_id =' . $params['game_type_id'];

        $bet_auto_info = $bet_auto_obj->getBetAutoInfo($where, 'bet_auto_id');
        $user_obj = new UserModel();
        $left_money = $user_obj->where('user_id =' . $user_id)->getField('left_money');
        if ($left_money >= $params['max_money']) {
            ApiModel::returnResult(1000, null, '金豆余额已超出设置上限');
        }
        if ($left_money <= $params['min_money']) {
            ApiModel::returnResult(1000, null, '金豆余额已低于设置下限');

        }
        //编辑模式
        if ($bet_auto_info) {
            $params['is_open'] = 1;
            $params['new_mode_id'] = $params['start_mode_id'];
            $params['bet_issue_num'] = 0;

            $res = $bet_auto_obj->editBetAuto('bet_auto_id = ' . $bet_auto_info['bet_auto_id'], $params);
            if ($res === false) {
                ApiModel::returnResult(1000, null, '设置失败');
            }
        } else {
            $params['user_id'] = $user_id;
            $params['new_mode_id'] = $params['start_mode_id'];
            $params['is_open'] = 1;
            $params['bet_issue_num'] = 0;

            $res = $bet_auto_obj->addBetAuto($params);
            if (!$res) {
                ApiModel::returnResult(1000, null, '设置失败');
            }
        }

        //输赢变换
        $change_arr = json_decode(htmlspecialchars_decode($params['change_json']), true);
        foreach ($change_arr as $k => $v) {
            $arr = array(
                'win_change' => $v['win_change'],
                'loss_change' => $v['loss_change'],
            );
            $bet_mode_obj->editBetMode('bet_mode_id =' . $v['bet_mode_id'], $arr);
        }

        return '设置成功';
    }

    /**
     * 获取当前自动投注详情
     * @param $params
     * @date: 2019/4/1
     * @return array
     * @author: yzp
     */
    public function getAutoBetInfo($params)
    {
        $bet_auto_obj = new BetAutoModel();
        $user_id = session('user_id');

        $where = 'user_id =' . $user_id . ' AND game_type_id =' . $params['game_type_id'];
//
//        if ($params['type']) {
//            $where .= ' AND type =' . $params['type'];
//        }

        $bet_auto_info = $bet_auto_obj->getBetAutoInfo($where, '');

        if (!$bet_auto_info) {
            return array(
                'bet_auto_info' => array()
            );
        }

        if ($bet_auto_info['type'] == self::AUTOCHANGE)  //输赢变换
        {
            $bet_mode_obj = new BetModeModel();
            $bet_mode_info = $bet_mode_obj->getBetModeInfo('bet_mode_id =' . $bet_auto_info['start_mode_id'], 'win_change,loss_change,total_money,mode_name');

            $bet_auto_info['bet_mode_info'] = $bet_mode_info ?: array();

        } elseif ($bet_auto_info['bet_mode_json']) {
            $bet_auto_info['bet_mode_json'] = json_decode($bet_auto_info['bet_mode_json'], true);
        }

        return array(
            'bet_auto_info' => $bet_auto_info ?: array()
        );
    }

    /**
     * 停止自动投注
     * @param $params
     * @date: 2019/4/1
     * @return array
     * @author: yzp
     */
    public function stopAutoBet($params)
    {
        $bet_auto_obj = new BetAutoModel();
        $user_id = session('user_id');

        $where = 'user_id =' . $user_id . ' AND game_type_id =' . $params['game_type_id'];

        $bet_auto_info = $bet_auto_obj->getBetAutoInfo($where, 'bet_auto_id,is_open');

        if (!$bet_auto_info) {
            return '没有正在进行的投注';
        }
        if ($bet_auto_info['is_open'] == 0) {
            return '已停止投注';
        }

        $res = $bet_auto_obj->editBetAuto('bet_auto_id =' . $bet_auto_info['bet_auto_id'], array('is_open' => 0)); //停止自动投注

        if (!$res) {
            return '操作失败';
        }

        return '操作成功';
    }


    /**
     * 保存模式
     * @param $params
     * @date: 2019/4/9
     * @return bool
     * @author: hui
     */
    public function saveBetMode($params)
    {

        $bet_log_obj = new BetLogModel();
        $bet_log_info = $bet_log_obj->getBetLoginfo('bet_log_id = ' . $params['bet_log_id'], '');
        $bet_mode = new BetModeModel();
        $user_id = session('user_id');
        $mode_name = '模式' . time();
        //判断名称是否存在
        $bet_mode_info = $bet_mode->getBetModeInfo('mode_name = "' . $mode_name . '" AND user_id =' . $user_id . ' AND game_type_id = ' . $params['game_type_id']);
        if ($bet_mode_info) {
            ApiModel::returnResult(-1, '', '模式名称已经存在');
        }
        //去除win字段
        $bet_json = json_decode($bet_log_info['bet_json'], true);

        foreach ($bet_json as $k => &$v) {
            foreach ($v['bet_json'] as $ki => &$vi) {
                unset($vi['win']);
            }
            unset($vi);
        }
        unset($v);
        $bet_json = json_encode($bet_json);
        $arr = array(
            'user_id' => $user_id,
            'game_type_id' => $bet_log_info['game_type_id'],
            'mode_name' => $mode_name,
            'total_money' => $bet_log_info['total_bet_money'],
            'bet_json' => $bet_json,
        );
        $sueecss = $bet_mode->addBetMode($arr);
        if (!$sueecss) {
            ApiModel::returnResult(-1, '', '添加失败');
        }

        return $sueecss;
    }


    public function tttt()
    {
        $data[0]['part'] = 1;
        $data[0]['name'] = '冠亚军和';
        $bet_json[0]['key'] = 1;
        $bet_json[0]['name'] = '冠亚大';
        $bet_json[0]['rate'] = '2.205';
        $bet_json[1]['key'] = 2;
        $bet_json[1]['name'] = '冠亚小';
        $bet_json[1]['rate'] = '1.764';
        $bet_json[2]['key'] = 3;
        $bet_json[2]['name'] = '冠亚单';
        $bet_json[2]['rate'] = '1.764';
        $bet_json[3]['key'] = 4;
        $bet_json[3]['name'] = '冠亚双';
        $bet_json[3]['rate'] = '2.205';
        $data[0]['bet_json'] = $bet_json;

        for ($j = 1; $j <= 5; $j++) {
            $bet_json = [];
            $data[$j]['part'] = $j + 1;
            $data[$j]['name'] = '赛车一';
            $bet_json[0]['key'] = 1;
            $bet_json[0]['name'] = '大';
            $bet_json[0]['rate'] = '1.96';
            $bet_json[1]['key'] = 2;
            $bet_json[1]['name'] = '小';
            $bet_json[1]['rate'] = '1.96';
            $bet_json[2]['key'] = 3;
            $bet_json[2]['name'] = '单';
            $bet_json[2]['rate'] = '1.96';
            $bet_json[3]['key'] = 4;
            $bet_json[3]['name'] = '双';
            $bet_json[3]['rate'] = '1.96';
            $bet_json[4]['key'] = 5;
            $bet_json[4]['name'] = '龙';
            $bet_json[4]['rate'] = '1.96';
            $bet_json[5]['key'] = 6;
            $bet_json[5]['name'] = '虎';
            $bet_json[5]['rate'] = '1.96';
            for ($i = 0; $i < 10; $i++) {
                $bet_json[$i + 6]['key'] = $i + 7;
                $bet_json[$i + 6]['name'] = $i + 1;
                $bet_json[$i + 6]['rate'] = '9.8';
            }
            $data[$j]['bet_json'] = $bet_json;
        }
        for ($j = 6; $j <= 10; $j++) {
            $bet_json = [];
            $data[$j]['part'] = $j + 1;
            $data[$j]['name'] = '赛车一';
            $bet_json[0]['key'] = 1;
            $bet_json[0]['name'] = '大';
            $bet_json[0]['rate'] = '1.96';
            $bet_json[1]['key'] = 2;
            $bet_json[1]['name'] = '小';
            $bet_json[1]['rate'] = '1.96';
            $bet_json[2]['key'] = 3;
            $bet_json[2]['name'] = '单';
            $bet_json[2]['rate'] = '1.96';
            $bet_json[3]['key'] = 4;
            $bet_json[3]['name'] = '双';
            $bet_json[3]['rate'] = '1.96';
            for ($i = 0; $i < 10; $i++) {
                $bet_json[$i + 4]['key'] = $i + 5;
                $bet_json[$i + 4]['name'] = $i + 1;
                $bet_json[$i + 4]['rate'] = '9.8';
            }
            $data[$j]['bet_json'] = $bet_json;
        }
        return $data;

    }


    public function gameBetLiuhecaiOmit()
    {
        return [
            [
                'type'  =>  '正特',
                'data'  =>  [
                    [
                        'type_name' =>  '特码',
                        'number' =>  '2',
                        'omit_number'   =>  2
                    ],[
                        'type_name' =>  '特码',
                        'number' =>  '1',
                        'omit_number'   =>  2
                    ]
                ]

            ],
        ];
    }


    /**
     * 获取参数列表
     * @param
     * @return 参数列表
     * @author 姜伟
     * @todo 获取参数列表
     */
    function getParams($func_name)
    {
        $params = array(
            'stopAutoBet' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                    'required' => true,
                ),
            ),
            'getAutoBetInfo' => array(
                array(
                    'field' => 'type',
                    'type' => 'int',
                    'required' => false,
                ),
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                    'required' => true,
                ),
            ),
            'saveBetMode' => array(
                array(
                    'field' => 'bet_log_id',
                    'type' => 'int',
                    'required' => false,
                ),
//                array(
//                    'field' => 'mode_name',
//                    'type' => 'string',
//                    'required' => true,
//                ),
            ),
            'setAutoBet' => array(
//                array(
//                    'field' => 'type',
//                    'type' => 'int',
//                    'required' => true,
//                ),
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                    'required' => true,
                ),
                array(
                    'field' => 'start_issue',
                    'type' => 'string',
                    'required' => true,
                ),
                array(
                    'field' => 'start_mode_id',
                    'type' => 'int',
                    'required' => true,
                ),
                array(
                    'field' => 'issue_number',
                    'type' => 'string',
                    'required' => true,
                ),
                array(
                    'field' => 'max_money',
                    'type' => 'string',
                    'required' => true,
                ),
                array(
                    'field' => 'min_money',
                    'type' => 'string',
                    'required' => true,
                ),
                array(
                    'field' => 'change_json',
                    'type' => 'string',
                    'required' => true,
                ),


            ),
            'BetModeList' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
            ),
            'delBetMode' => array(
                array(
                    'field' => 'bet_mode_id',
                    'type' => 'int',
                ),
            ),
            'getGameRule' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
            ),
            'getRunChart' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
                array(
                    'field' => 'type',
                    'type' => 'int',
                ),
            ),
            'getBetLogList' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),

                array(
                    'field' => 'firstRow',
                    'type' => 'int',
                ),
                array(
                    'field' => 'pageSize',
                    'type' => 'int',
                ),

            ),
            'getLastBetInfo' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),

                array(
                    'field' => 'game_result_id',
                    'type' => 'int',
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
                    'field' => 'jpush_reg_id',
                    'type' => 'string',
                ),
            ),
            'homePage' => array(
                array(
                'field' => 'type',
                'type' => 'int',
                ),
            ),
            'imgcode' => array(),
            'getGameTypeList' => array(
                array(
                    'field' => 'is_app',
                    'type' => 'int',
                ),
            ),

            'nowResult' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
            ),
            'getResultLogList' => array(
                array(
                    'field' => 'firstRow',
                    'type' => 'int',
                ),
                array(
                    'field' => 'pageSize',
                    'type' => 'int',
                ),
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
                array(
                    'field' => 'type',
                    'type' => 'int',
                ),
            ),
            'gameBet' => array(
                array(
                    'field' => 'game_result_id',
                    'type' => 'int',
                ),
                array(
                    'field' => 'total_bet_money',
                    'type' => 'int',
                ),
                array(
                    'field' => 'bet_json',
                    'type' => 'text',
                ),
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
            ),
            'gameBetLiuhecai' => array(
                array(
                    'field' => 'bet_json',
                    'type' => 'text',
                ),
            ),

            'newBetMode' => array(
                array(
                    'field' => 'bet_mode_id',
                    'type' => 'int',
                ),
                array(
                    'field' => 'total_money',
                    'type' => 'int',
                ),
                array(
                    'field' => 'bet_json',
                    'type' => 'text',
                ),
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
                array(
                    'field' => 'mode_name',
                    'type' => 'string',
                ),
            ),
            'getLastBetLog' => array(
                array(
                    'field' => 'game_type_id',
                    'type' => 'int',
                ),
                array(
                    'field' => 'issue',
                    'type' => 'string',
                ),
            ),


            'getResult' => array(
                array(
                    'field' => 'type',
                    'type' => 'int',
                ),
            ),
            'test' => array(
                array(
                    'field' => 'type',
                    'type' => 'int',
                ),
                array(
                    'field' => 'num',
                    'type' => 'int',
                ),
            ),
        );

        return $params[$func_name];
    }
}

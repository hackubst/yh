<?php

class AcpGameAction extends AcpAction
{
    /**
     * 游戏系列列表
     * @date: 2019/3/8
     * @author: hui
     */
    public function game_series_list()
    {
        //获取订单列表
        $game_series_obj = new GameSeriesModel();
        $game_series_list = $game_series_obj->select();
        $this->assign('game_series_list', $game_series_list);
        $this->assign('head_title', '游戏系列列表');
        $this->display();
    }

    /**
     * 改变游戏系列状态
     * @date: 2019/3/8
     * @author: hui
     */
    public function series_set_enable()
    {
        if (IS_AJAX && IS_POST) {
            $series_id = I('series_id');
            $opt = I('opt');
            $game_series_obj = new GameSeriesModel();
            $success = $game_series_obj->editGameSeries('game_series_id =' . $series_id, ['isuse' => $opt]);
            if ($success !== false) {
                exit('success');
            } else {
                exit('failure');
            }
        }
    }


    /**
     * 游戏类型列表
     * @date: 2019/3/11
     * @author: hui
     */
    public function game_type_list()
    {
        //获取订单列表
        $game_type_obj = new GameTypeModel();
        $where = 'true';

        $type_name = I('type_name');
        if ($type_name) {
            $where .= ' AND game_type_name LIKE "%' . $type_name . '%"';
            $this->assign('type_name', $type_name);
        }

        $series_id = I('series_id');
        if ($series_id) {
            $where .= ' AND game_series_id =' . $series_id;
            $this->assign('series_id', $series_id);
        }

        import('ORG.Util.Pagelist');

        $count = $game_type_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $game_type_obj->setStart($Page->firstRow);
        $game_type_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $game_type_list = $game_type_obj->getGameTypeList('', $where);
        $game_type_list = $game_type_obj->getListData($game_type_list);
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);


        $this->assign('game_type_list', $game_type_list);
        $this->assign('show', $show);
        $this->assign('head_title', '游戏系列列表');
        $this->display();
    }

    /**
     * 游戏记录列表
     * @date: 2019/3/28
     * @author: yzp
     */
    public function get_game_log()
    {
        $game_log_obj = new GameLogModel();
        $where = 'true';

        $type_name = I('type_name');
        if ($type_name) {
            $where .= ' AND game_log_name LIKE "%' . $type_name . '%"';
            $this->assign('type_name', $type_name);
        }

        $series_id = I('series_id');
        if ($series_id) {
            $where .= ' AND game_series_id =' . $series_id;
            $this->assign('series_id', $series_id);
        }

        import('ORG.Util.Pagelist');

        $count = $game_log_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $game_log_obj->setStart($Page->firstRow);
        $game_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $game_log_list = $game_log_obj->getGameLogList('', $where);
        $game_log_list = $game_log_obj->getListData($game_log_list);
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);

        // dump($game_log_list);die;
        $this->assign('game_log_list', $game_log_list);
        $this->assign('show', $show);
        $this->assign('head_title', '游戏记录列表');
        $this->display();
    }

    /**
     * 改变游戏系列状态
     * @date: 2019/3/11
     * @author: hui
     */
    public function type_set_enable()
    {
        if (IS_AJAX && IS_POST) {
            $type_id = I('type_id');
            $opt = I('opt');
            $type = I('type');
            $game_type_obj = new GameTypeModel();
            if ($type == 1) {
                //是否显示在首页
                $arr = array(
                    'is_show_app' => $opt,
                );
            } elseif ($type == 2) {
                //是否启用
                $arr = array(
                    'isuse' => $opt,
                );
            } elseif ($type == 3) {
                //是否开启投注
                $arr = array(
                    'is_bet' => $opt,
                );
            }
            $success = $game_type_obj->editGameType('game_type_id =' . $type_id, $arr);
            if ($success !== false) {
                exit('success');
            } else {
                exit('failure');
            }
        }
    }


    /**
     * 开启杀模式/随机模式
     */
    public function edit_kill_model()
    {
        if (IS_AJAX && IS_POST) {
            $id = I('id');
            $state = I('state');
            $kill_rate = I('kill_rate');
            $game_type_obj = new GameTypeModel();
            $arr = array(
                'is_kill' => $state,
            );
            if ($kill_rate) {
                $arr['kill_rate'] = $kill_rate;
            }
            $success = $game_type_obj->editGameType('game_type_id =' . $id, $arr);
//            echo $game_type_obj->getLastSql();
            if ($success !== false) {
                exit('success');
            } else {
                exit('failure');
            }
        }
    }

    public function edit_water()
    {
        $water_obj = new WaterModel();
        $water_id = I('water_id');
        $water_info = $water_obj->getWaterInfo('water_id = ' . $water_id);
        if (IS_POST) {
            $a = I('pan_a');
            $b = I('pan_b');

            $c = I('pan_c');
            $d = I('pan_d');


            $water_id = I('get.water_id');
            $where = 'water_id = ' . $water_id;
            $water_obj->editWater($where, array(
                'a' => $a,
                'b' => $b,
                'c' => $c,
                'd' => $d,
            ));

            $this->success('修改成功', '/AcpGame/get_water_list');
        }

        $this->assign('water_info', $water_info);
//        echo json_encode($water_info);
        $this->assign('head_title', '修改游戏类型');
        $this->display();

    }

    /**
     * 编辑游戏类型
     * @date: 2019/3/22
     * @author: yzp
     */
    public function edit_game_type()
    {
        $game_type_obj = new GameTypeModel();
        $game_type_id = I('game_type_id');
        $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id = ' . $game_type_id);
        $game_type_info['bet_json'] = json_decode($game_type_info['bet_json'], true);
        $game_type_info['game_rule'] = htmlspecialchars_decode($game_type_info['game_rule']);
        if (I('act') == 'submit') {
//            $game_series_id = I('game_series_id');
//            $game_type_name = I('game_type_name');
            $base_bonus_pools = I('base_bonus_pools');
            $max_bet_num = I('max_bet_num');

            $min_bet_num = I('min_bet_num');
            $max_win_num = I('max_win_num');
            $min_win_num = I('min_win_num');
            $max_bet_money = I('max_bet_money');
            $max_win_money = I('max_win_money');

//            $max_win_reward = I('max_win_reward');
//            $min_win_reward = I('min_win_reward');
//            $max_bet_reward = I('max_bet_reward');
//            $min_bet_reward = I('min_bet_reward');

            $max_deduct = I('max_deduct');
            $min_deduct = I('min_deduct');

            $base_img = I('base_img');
            $game_rule = I('game_rule');
            $isuse = I('isuse');
            $is_index = I('is_index');


//            $table_type          = I('table_type');
//            $result_type          = I('result_type');

//            $bet_json = I('bet_json');
//            $bet_json = $game_type_obj->exCahngeData($bet_json);

            $game_type_id = I('get.game_type_id');
            $where = 'game_type_id = ' . $game_type_id;
            $game_type_obj->editGameType($where, array(
//                'game_series_id' => $game_series_id,
//                'game_type_name' => $game_type_name,
                'base_bonus_pools' => $base_bonus_pools,

                'max_bet_num' => $max_bet_num,
                'min_bet_num' => $min_bet_num,
                'max_win_num' => $max_win_num,
                'min_win_num' => $min_win_num,
                'max_bet_money' => $max_bet_money,
                'max_win_money' => $max_win_money,
                'max_win_reward' => $max_win_reward,
                'min_win_reward' => $min_win_reward,
                'max_bet_reward' => $max_bet_reward,
                'min_bet_reward' => $min_bet_reward,
                'max_deduct' => $max_deduct,
                'min_deduct' => $min_deduct,
                'base_img' => $base_img,
                'game_rule' => $game_rule,
                'isuse' => $isuse,
                'is_index' => $is_index,
//                'bet_json' => $bet_json,
//                'table_type' => $table_type,
//                'result_type' => $result_type
            ));

            $this->success('修改成功', '/AcpGame/game_type_list');
        }

        $this->assign('pic_data', array(
            'name' => 'base_img',
            'title' => '类型图片',
            'url' => $game_type_info['base_img'],
            'help' => '<span style="color:red;">图片尺寸：600*320像素；暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));

        $game_series_obj = new GameSeriesModel();
        $where = 'isuse = 1';
        $game_series_list = $game_series_obj->getGameSeriesAll('*', $where);
        $this->assign('game_series_list', $game_series_list);

        $this->assign('game_type_info', $game_type_info);
        $this->assign('head_title', '修改游戏类型');
        $this->display();

    }


    /**
     * 投注记录列表
     * @date: 2019/4/1
     * @author: yzp
     */
    public function get_bet_log()
    {

        $bet_log_obj = new BetLogModel();
        $where = 'true';

        $user_id = I('get.user_id');

        if ($user_id) {
            $where .= ' AND user_id =' . $user_id;
        }

        $where .= $this->game_search();

        import('ORG.Util.Pagelist');

        $count = $bet_log_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $bet_log_obj->setStart($Page->firstRow);
        $bet_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $bet_log_list = $bet_log_obj->getBetLogList('', $where, 'addtime DESC');
        $bet_log_list = $bet_log_obj->getListData($bet_log_list);
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);

        //类型列表
        $type_obj = new GameTypeModel();
        $game_type_list = $type_obj->select();
        $this->assign('game_type_list', $game_type_list);

        // dump($bet_log_list);die;
        $this->assign('bet_log_list', $bet_log_list);
        $this->assign('show', $show);
        $this->assign('head_title', '投注记录列表');

        $this->assign('action_title', '用户列表');
        $this->assign('action_src', '/AcpUser/get_all_user_list/mod_id/0');

        $this->display('get_game_bet_log');
    }

    public function get_game_count()
    {

        $bet_log_obj = new BetLogModel();
        $where = 'is_open = 1';

        $game_type_id = I('game_type_id');
        $id = $this->_request('id', '');

        if ($game_type_id) {
            $where .= ' AND game_type_id =' . $game_type_id;
            $this->assign('game_type_id', $game_type_id);
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

        $where .= $this->game_search1();
        $where .= $this->game_time_search1();

        import('ORG.Util.Pagelist');


        $bet_log_list = $bet_log_obj->getBetLogList('user_id,game_type_id,addtime,SUM(total_after_money - total_bet_money) AS win_loss', $where, '', 'user_id');
        $count = count($bet_log_list);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $bet_log_obj->setStart($Page->firstRow);
        $bet_log_obj->setLimit($Page->listRows);
        $show = $Page->show();


        $bet_log_list = $bet_log_obj->getBetLogList('user_id,game_type_id,addtime,SUM(total_after_money - total_bet_money) AS win_loss', $where, 'win_loss desc', 'user_id');
        $bet_log_list = $bet_log_obj->getCountData($bet_log_list);

        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);

        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('game_type_name,game_type_id', 'isuse = 1');

        $this->assign('game_type_list', $game_type_list);
        $this->assign('id', $id);
        $this->assign('bet_log_list', $bet_log_list);
        $this->assign('show', $show);
        $this->assign('head_title', '每日游戏统计');
        $this->display();
    }


    public function game_time_search1()
    {
        //通过每日时间查询
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        if ($start_time) {
            $end = $start_time + 24 * 3600;
            $this->assign('start_time', $start_time);
        } else {
            $start_time = strtotime(date('Y-m-d', time()));
            $end = $start_time + 24 * 3600;
            $this->assign('start_time', $start_time);
        }
        $where = ' AND tp_bet_log.addtime >=' . $start_time . ' AND tp_bet_log.addtime <=' . $end;
        return $where;
    }

    public function game_time_search()
    {
        //通过每日时间查询
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        if ($start_time) {
            $end = $start_time + 24 * 3600;
            $this->assign('start_time', $start_time);
        } else {
            $start_time = strtotime(date('Y-m-d', time()));
            $end = $start_time + 24 * 3600;
            $this->assign('start_time', $start_time);
        }
        $where = ' AND addtime >=' . $start_time . ' AND addtime <=' . $end;
        return $where;
    }

    public function user_game_search()
    {
        $where = '';
        $game_type_obj = new GameTypeModel();
        //通过游戏类型名查询
        $type_name = I('type_name');
        if ($type_name) {
            $game_type_ids = $game_type_obj->where('game_type_name LIKE "%' . $type_name . '%"')->getField('game_type_id', true);

            if ($game_type_ids) {
                $where .= ' AND tp_bet_log.game_type_id in (' . join(',', $game_type_ids) . ')';
            } else {
                $where .= ' AND tp_bet_log.game_type_id = -1';
            }

            $this->assign('type_name', $type_name);
        }

        //通过游戏系列查询
        $series_id = I('series_id');
        if ($series_id) {
            $game_type_ids = $game_type_obj->where('game_series_id = ' . $series_id)->getField('game_type_id', true);

            if ($game_type_ids) {
                $where .= ' AND tp_bet_log.game_type_id in (' . join(',', $game_type_ids) . ')';
            } else {
                $where .= ' AND tp_bet_log.game_type_id = -1';
            }

            $this->assign('series_id', $series_id);
        }
        return $where;
    }

    //游戏相关搜索
    public function game_search()
    {
        $where = '';
        $game_type_obj = new GameTypeModel();
        $game_result_obj = new GameResultModel();
        $user_obj = new UserModel();
        //通过每日时间查询
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        if ($start_time) {
            $end = $start_time + 24 * 3600;
            $this->assign('start_time', $start_time);

            $where .= ' AND addtime >=' . $start_time . ' AND addtime <=' . $end;
        }

        //通过游戏类型名查询
        $type_name = I('type_name');
        if ($type_name) {
            $game_type_ids = $game_type_obj->where('game_type_name LIKE "%' . $type_name . '%"')->getField('game_type_id', true);

            if ($game_type_ids) {
                $where .= ' AND game_type_id in (' . join(',', $game_type_ids) . ')';
            } else {
                $where .= ' AND game_type_id = -1';
            }

            $this->assign('type_name', $type_name);
        }

        $game_type_id = I('game_type_id');
        if ($game_type_id) {
            $where .= ' AND game_type_id =' . $game_type_id;
            $this->assign('game_type_id', $game_type_id);
        }

        //通过游戏系列查询
        $series_id = I('series_id');
        if ($series_id) {
            $game_type_ids = $game_type_obj->where('game_series_id = ' . $series_id)->getField('game_type_id', true);

            if ($game_type_ids) {
                $where .= ' AND game_type_id in (' . join(',', $game_type_ids) . ')';
            } else {
                $where .= ' AND game_type_id = -1';
            }

            $this->assign('series_id', $series_id);
        }

        //通过昵称查询
        $nickname = I('nickname');
        if ($nickname) {
            $user_ids = $user_obj->where('nickname LIKE "%' . $nickname . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where .= ' AND user_id in (' . join(',', $user_ids) . ')';
            } else {
                $where .= ' AND user_id = -1';
            }

            $this->assign('nickname', $nickname);
        }

        //通过投注期号查询
        $issue = I('issue');
        if ($issue) {
            $game_result_ids = $game_result_obj->where('issue LIKE "%' . $issue . '%"')->getField('game_result_id', true);

            if ($game_result_ids) {
                $where .= ' AND game_result_id in (' . join(',', $game_result_ids) . ')';
            } else {
                $where .= ' AND game_result_id = -1';
            }
            $this->assign('issue', $issue);
        }

        //通过用户手机号查询
        $mobile = I('mobile');
        if ($mobile) {
            $user_ids = $user_obj->where('mobile LIKE "%' . $mobile . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where .= ' AND user_id in (' . join(',', $user_ids) . ')';
            } else {
                $where .= ' AND user_id = -1';
            }

            $this->assign('mobile', $mobile);
        }

        $is_win = I('is_win');
        if ($is_win) {
            $this->assign('is_win', $is_win);

            $is_win = $is_win == 2 ? 0 : $is_win;

            $where .= ' AND is_win =' . $is_win;
        }

        //ID筛选
        $id = I('id');
        if ($id) {
            $user_ids = $user_obj->where('id LIKE "%' . $id . '%"')->getField('user_id', true);

            if ($user_ids) {
                $user_ids = implode(',',$user_ids);
            } else {
                $user_ids = -1;
            }
            $this->assign('id', $id);
            $where .= ' AND user_id in('.$user_ids.')';
        }
        return $where;
    }

    /**
     * 开奖记录列表
     * @date: 2019/4/3
     * @author: yzp
     */
    public function get_game_result()
    {

        $game_log_obj = new GameLogModel();
        $where = 'true';

        $game_type_id = I('get.game_type_id');

        if ($game_type_id) {
            $where .= ' AND game_type_id =' . $game_type_id;
        }
        $where .= $this->game_search();

        import('ORG.Util.Pagelist');

        $count = $game_log_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $game_log_obj->setStart($Page->firstRow);
        $game_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $game_log_list = $game_log_obj->getGameLogList('', $where, 'addtime desc');
        $game_log_list = $game_log_obj->getResultLog($game_log_list);
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();

        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('game_type_name,game_type_id', 'isuse = 1');

        $this->assign('game_type_list', $game_type_list);
        $this->assign('series_list', $series_list);

        // dump($game_log_list);die;
        $this->assign('game_log_list', $game_log_list ?: array());
        $this->assign('show', $show);
        $this->assign('head_title', '开奖记录列表');
        $this->display();
    }

    /**
     * 投注记录明细列表
     * @date: 2019/4/3
     * @author: yzp
     */
    public function get_game_bet_log()
    {
        $bet_log_obj = new BetLogModel();
        $where = 'true';

        $game_result_id = I('get.game_result_id');

        if ($game_result_id) {
            $where .= ' AND game_result_id =' . $game_result_id;
        }

        $where .= $this->game_search();

        import('ORG.Util.Pagelist');

        $count = $bet_log_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $bet_log_obj->setStart($Page->firstRow);
        $bet_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $bet_log_list = $bet_log_obj->getBetLogList('', $where,'addtime DESC');
        $bet_log_list = $bet_log_obj->getAllData($bet_log_list);
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);

        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->getGameTypeAll('game_type_name,game_type_id', 'isuse = 1');

        $this->assign('game_type_list', $game_type_list);

        // dump($bet_log_list);die;
        $this->assign('bet_log_list', $bet_log_list ?: array());
        $this->assign('show', $show);
        $this->assign('head_title', '投注记录明细列表');
        $this->display();
    }


    //用户游戏输赢统计
    public function get_user_game_count()
    {
        $game_type_obj = new GameTypeModel();
        $where = 'true';
        $where1 = '';
        $user_id = I('get.user_id');

        if (!intval($user_id)) {
            $this->error('参数错误');
        }

        //起始时间
        $start_time = $this->_post('start_time') ? : $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        #echo $start_time;
        if ($start_time) {
            $where1 .= ' AND tp_bet_log.addtime >= ' . $start_time;
        }

        //结束时间
        $end_time = $this->_post('end_time') ? : $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);
        $end_time = strtotime($end_time);
        if ($end_time) {
            $where1 .= ' AND tp_bet_log.addtime <= ' . ($end_time + 24*3600 -1);
        }

        $where .= $this->user_game_search();

        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);

        import('ORG.Util.Pagelist');

        $all = $game_type_obj
            ->field('user_id,game_type_name,SUM(total_after_money - total_bet_money) AS last_money')
            ->join('tp_bet_log on tp_bet_log.game_type_id = tp_game_type.game_type_id')
            ->where($where.' AND tp_bet_log.is_open = 1 AND user_id ='.$user_id.$where1)
            ->group('tp_bet_log.game_type_id')
            ->select();

        $count = count($all);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $game_type_obj->setStart($Page->firstRow);
        $game_type_obj->setLimit($Page->listRows);
        $show = $Page->show();
//        $game_type_list = $game_type_obj->getGameTypeList('game_type_id,game_type_name', $where);

        $game_type_list = $game_type_obj
            ->field('user_id,game_type_name,SUM(total_after_money - total_bet_money) AS last_money')
            ->join('tp_bet_log on tp_bet_log.game_type_id = tp_game_type.game_type_id')
            ->where($where.' AND tp_bet_log.is_open = 1 AND user_id ='.$user_id.$where1)
            ->group('tp_bet_log.game_type_id')
            ->limit()
            ->select();
//        dump($game_type_list);
        $game_type_list = $game_type_obj->getWinLossData($game_type_list);

        $bet_log_obj = new BetLogModel();
        $bet_log_info = $bet_log_obj->getBetLogInfo($where.' AND is_open = 1 AND user_id ='.$user_id.$where1,'SUM(total_after_money - total_bet_money) AS total');


        $total = feeHandle($bet_log_info['total']) ? : 0;

        // dump($game_type_list);die;
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);

        $this->assign('game_type_list', $game_type_list);
        $this->assign('show', $show);
        $this->assign('total', $total);
        $this->assign('head_title', '游戏输赢统计');

        $this->assign('action_title', '用户列表');
        $this->assign('action_src', '/AcpUser/get_all_user_list/mod_id/0');

        $this->display();
    }

    // 中央银行
    public function get_game_total()
    {
        set_time_limit(0);
        $game_type_obj = new GameTypeModel();
        $where = 'true';

        $time_data = $this->game_time_length();
        //统计查询
        $count = ($time_data['end_time'] - $time_data['start_time']) / 3600 / 24;

        $time_where = 'addtime >=' . $time_data['start_time'] . ' AND addtime <=' . $time_data['end_time'];

        $game_type_list = $game_type_obj->getGameTypeAll('game_type_id,game_type_name', $where);

        $this->assign('game_type_list', $game_type_list);

        $game_type_list = $game_type_obj->getGameTypeAll('game_type_id,game_type_name', $where);
        $list = $game_type_obj->getWinLossAllData($game_type_list, $count, $time_data['start_time']);

        import('ORG.Util.Page');
        $page = $this->_request('p');
        if (empty($page)) {
            $page = 1;
        }
        $p = new Page($count, 3, '', $page);//3为每页显示的条数,$page为传过来的页码
        $pa = ($page - 1) * 3;//计算每页的数据从第几条开始
        $list = array_slice($list, $pa, 3);//重组数组
        $page = $p->show();//传递页码到前端显示
        $this->assign('page', $page);   //传递页码
//         dump($list);die;
        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        $this->assign('series_list', $series_list);

        $this->assign('list', $list);
        $this->assign('head_title', '中央银行');

        $this->display();
    }

    public function game_time_length()
    {
        //通过每日时间查询
        $start_time = $this->_request('start_time');
        $start_time = str_replace('+', ' ', $start_time);
        $start_time = strtotime($start_time);
        if (!$start_time) {
            $start_time = strtotime(date('Y-m-d'));
        }
        $this->assign('start_time', $start_time);

        $end_time = $this->_request('end_time');
        $end_time = str_replace('+', ' ', $end_time);

        if (!$end_time) {
            $end_time = strtotime(date('Y-m-d')) + 3600 * 24;
            $this->assign('end_time', $end_time - 1);

        } else {
            $this->assign('end_time', strtotime($end_time));
            $end_time = strtotime($end_time) + 3600 * 24;
        }

        return array('start_time' => $start_time, 'end_time' => $end_time);

        // $where = 'addtime >='.$start_time.' AND addtime <='.$end_time;
        // return $where;
    }

    public function get_games_total()
    {

        $bet_log_obj = new BetLogModel();
        $game_type_obj = new GameTypeModel();
        $where = 'true';

        $game_type_id = I('game_type_id');

        if ($game_type_id) {
            $where .= ' AND game_type_id =' . $game_type_id;
        }

        $where .= $this->game_search();
        $where .= $this->game_time_search();

        import('ORG.Util.Pagelist');

        $count = $bet_log_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $bet_log_obj->setStart($Page->firstRow);
        $bet_log_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $bet_log_list = $bet_log_obj->getBetLogList('user_id,game_type_id,addtime,SUM(total_after_money - total_bet_money) AS win_loss', $where, '', 'user_id');
        $bet_log_list = $bet_log_obj->getCountData($bet_log_list);

        //系列列表
        $series_obj = new GameSeriesModel();
        $series_list = $series_obj->select();
        dump($series_list);die;
        $this->assign('series_list', $series_list);

        $this->assign('bet_log_list', $bet_log_list);
        $this->assign('show', $show);
        $this->assign('head_title', '每日游戏统计');
        $this->display();
    }

    //游戏相关搜索
    public function game_search1()
    {
        $where = '';
        $game_type_obj = new GameTypeModel();
        $game_result_obj = new GameResultModel();
        $user_obj = new UserModel();


        //通过游戏类型名查询
        $type_name = I('type_name');
        if ($type_name) {
            $game_type_ids = $game_type_obj->where('game_type_name LIKE "%' . $type_name . '%"')->getField('game_type_id', true);

            if ($game_type_ids) {
                $where .= ' AND tp_bet_log.game_type_id in (' . join(',', $game_type_ids) . ')';
            } else {
                $where .= ' AND tp_bet_log.game_type_id = -1';
            }

            $this->assign('type_name', $type_name);
        }

        //通过游戏系列查询
        $series_id = I('series_id');
        if ($series_id) {
            $game_type_ids = $game_type_obj->where('game_series_id = ' . $series_id)->getField('game_type_id', true);

            if ($game_type_ids) {
                $where .= ' AND tp_bet_log.game_type_id in (' . join(',', $game_type_ids) . ')';
            } else {
                $where .= ' AND tp_bet_log.game_type_id = -1';
            }

            $this->assign('series_id', $series_id);
        }

        //通过昵称查询
        $nickname = I('nickname');
        if ($nickname) {
            $user_ids = $user_obj->where('nickname LIKE "%' . $nickname . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where .= ' AND user_id in (' . join(',', $user_ids) . ')';
            } else {
                $where .= ' AND user_id = -1';
            }

            $this->assign('nickname', $nickname);
        }


        //通过用户手机号查询
        $mobile = I('mobile');
        if ($mobile) {
            $user_ids = $user_obj->where('mobile LIKE "%' . $mobile . '%"')->getField('user_id', true);

            if ($user_ids) {
                $where .= ' AND user_id in (' . join(',', $user_ids) . ')';
            } else {
                $where .= ' AND user_id = -1';
            }

            $this->assign('mobile', $mobile);
        }

        return $where;
    }


    /**
     * 投注记录详情
     * @date: 2019/5/7
     * @author: hui
     */
    public function bet_log_detail(){
        $bet_log_id = I('bet_log_id');
        $bet_log_obj = new BetLogModel();
        $bet_log_info = $bet_log_obj->getBetLogInfo('bet_log_id = '.$bet_log_id,'bet_json,game_type_id');
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id ='.$bet_log_info['game_type_id'],'bet_json');

        $bet_json = json_decode($bet_log_info['bet_json'],true);
        $old_bet_json = json_decode($game_type_info['bet_json'],true);
        foreach ($old_bet_json as $k => &$v){
            foreach ($bet_json as $bet_k => $bet_v){
                if ($bet_v['part'] == $v['part']) {
                    foreach ($v['bet_json'] as $ki => &$vi) {
                        foreach ($bet_v['bet_json'] as $bet_ki => $bet_vi) {
                            if ($vi['key'] == $bet_vi['key']) {
                                $vi['money'] = feeHandle($bet_vi['money']);
                                $vi['win'] = feeHandle($bet_vi['win']);
                            }
                        }
                    }
                    unset($vi);
                }
            }
        }unset($v);
        $this->assign('old_bet_json',$old_bet_json);
//        $this->assign('bet_json',$bet_json);
        $this->assign('head_title','投注记录详情');
        $this->display();


    }




    /**
     * 卡奖重启
     * @date: 2019/3/11
     * @author: hui
     */
    public function restart_game()
    {
        if (IS_AJAX && IS_POST) {
            $series_id = I('series_id');
            $game_series_obj = new GameSeriesModel();
            $game_result_obj = new GameResultModel();
            $bet_log_obj = new BetLogModel();
            $account_obj = new AccountModel();
            $type = $game_series_obj->where('game_series_id ='.$series_id)->getField('result_type');
            $where = 'type IN (' . $type . ' )AND (result = "" OR result = 0)';
            //投注返回金额
            $list = $game_result_obj->getGameResultList('', $where);
            foreach ($list as $k => $v) {
                $bet_list = $bet_log_obj->getBetLogList('', 'game_result_id =' . $v['game_result_id']);
                foreach ($bet_list as $kk => $vv) {
                    $account_obj->addAccount($vv['user_id'], AccountModel::BETTING_RETURN, $vv['total_bet_money'], '开奖失败,投注金额退回');
                    $bet_log_obj->where('bet_log_id =' . $vv['bet_log_id'])->save(['is_open' => 1]);
                }
            }

            if ($game_result_obj->where($where)->delete() !== false) {
                exit('success');
            } else {
                exit('failure');
            }
        }
    }

    /**
     * 更改有效流水场次
     * @author yzp
     * @Date:  2019/6/24
     * @Time:  14:50
     */
    public function edit_valid_flow()
    {
        $id = I('request.id');
        $valid_flow = I('request.valid_flow');
        $game_type_obj = new GameTypeModel();
        if (!ctype_digit($id)) {
            $this->_ajaxFeedback(0, null, '参数无效！');
        }
        if (!ctype_digit($valid_flow)) {
            $this->_ajaxFeedback(0, null, '参数无效！');
        }
        $data['valid_flow'] = $valid_flow;
        $res = $game_type_obj->editGameType('game_type_id='.$id,$data);
        if ($res !== false)
        {
            $this->_ajaxFeedback(1, [], '操作成功');
        }
        else
        {
            $this->_ajaxFeedback(0, [], '操作失败! 请重试');
        }
    }

    /**
     * 修改游戏赔率
     */
    public function edit_bet_json(){
        $game_type_obj = new GameTypeModel();
        if (I('act') == 'submit') {
            $game_type_id = I('game_type_id');
            $new_bet_json = I('bet_json');
            $new_bet_json = json_encode($new_bet_json);
            $res = $game_type_obj->editGameType('game_type_id ='.$game_type_id,['bet_json' => $new_bet_json]);
            if($res !== false){
                $this->success('修改成功', '/AcpGame/game_type_list');
            }else{
                $this->success('修改失败');
            }
        }
        $game_type_id = I('game_type_id');
        $bet_json = $game_type_obj->getGameTypeField('game_type_id ='.$game_type_id,'bet_json');
        $bet_json = json_decode($bet_json,true);

//        dump($bet_json);
        $this->assign('game_type_id',$game_type_id);
        $this->assign('bet_json',$bet_json);
        $this->assign('head_title','');
        $this->display();
    }


    public function get_water_list()
    {
        //获取订单列表
        $game_water_obj = new WaterModel();
        $where = 'true';

        import('ORG.Util.Pagelist');

        $count = $game_water_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $game_water_obj->setStart($Page->firstRow);
        $game_water_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $water_list = $game_water_obj->getWaterList('', $where);



        $this->assign('water_list', $water_list);
        $this->assign('show', $show);
        $this->assign('head_title', '退水列表');
        $this->display();
    }
    /**
     * 有效流水统计
     * @author yzp
     * @Date:  2019/9/6
     * @Time:  14:14
     */
    public function get_flow_list()
    {
        $daily_win_obj = new DailyWinModel();

        $where = 'daily_flow <> 0';
        $start_time = I('start_time');
        $end_time = I('end_time');
        if ($start_time) {
            $start_time = str_replace('+', ' ', $start_time);
        }else{
            $start_time = date('Y-m-d',strtotime("-1 day"));
        }
        if ($end_time) {
            $end_time = str_replace('+', ' ', $end_time);
        }
        else{
            $end_time = date('Y-m-d',strtotime("-1 day"));
        }
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);

        if($start_time)
        {
            $where .= ' AND addtime >='.strtotime($start_time);
        }
        if($end_time)
        {
            $where .= ' AND addtime <='.strtotime($end_time);
        }

        if(strtotime($start_time) >= strtotime(date("Y-m-d")) || strtotime($end_time) >= strtotime(date("Y-m-d")))
        {
            $this->error('请选择当天之前的时间段');
        }
        import('ORG.Util.Pagelist');

        $count = $daily_win_obj->where($where)->count();
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $daily_win_obj->setStart($Page->firstRow);
        $daily_win_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $daily_win_list = $daily_win_obj->getDailyWinList('user_id,sum(daily_flow) as daily_flow', $where, 'daily_flow DESC','user_id');
        $daily_win_list = $daily_win_obj->getListData($daily_win_list);

        $this->assign('daily_win_list', $daily_win_list);
        $this->assign('show', $show);
        $this->assign('head_title', '有效流水统计');

        $this->display();
    }

    /**
     * 手动开奖
     * @author yzp
     * @Date:  2019/11/14
     * @Time:  15:03
     */
    public function do_open_result()
    {

        $game_result_obj = new GameResultModel();

        $data = I('post.');
        if(IS_POST)
        {
            log_file('open_data='.json_encode($data),'do_open_result',true);
            $type = $data['type'];
            $issue = $data['issue'];
            $result = $data['result'];
            if(!$type)
            {
                $this->error('请选择开奖游戏系列');
            }
            if(!$issue)
            {
                $this->error('请输入开奖游戏期号');
            }
            if(!intval($issue))
            {
                $this->error('请输入正确的开奖游戏期号');
            }
            if(!$result)
            {
                $this->error('请输入开奖结果');
            }
            //过滤中文逗号，转换为英文
            $result = str_replace('，',',',$result);

            $where['type'] = $type;
            $where['issue'] = $issue;
            $game_result_info = $game_result_obj->getGameResultInfo($where,'');
            if(!$game_result_info)
            {
                $this->error('待开奖期号不存在');
            }else{
                if($game_result_info['is_open'] == 1)
                {
                    $this->error('当前期号已开奖，不可重复开奖');
                }
            }
            $arr = array(
                'result' => $result,
                'open_time' => time(),
                'is_open' => 1
            );
            //保存开奖结果，设置为已开奖
            $res = $game_result_obj->editGameResult('game_result_id =' . $game_result_info['game_result_id'], $arr);
            log_file('sql='.$game_result_obj->getLastSql(),'do_open_result',true);
            if($res)
            {
                log_file('game_result_id='.$game_result_info['game_result_id'].'---type='.$type,'do_open_result',true);
                //开奖计算投注结果
                $game_log_obj = new GameLogModel();
                $return = $game_log_obj->calculateResult($game_result_info['game_result_id'], $type);
                if($return === false)
                {
                    log_file('自动开奖冲突，已经自动开奖---game_result_id='.$game_result_info['game_result_id'].'---type='.$type,'do_open_result',true);
                    $this->error('自动开奖已执行');
                }
                log_file('开奖成功---game_result_id='.$game_result_info['game_result_id'].'---type='.$type,'do_open_result',true);
                $this->success('开奖成功');
            }
            log_file('开奖失败---game_result_id='.$game_result_info['game_result_id'].'---type='.$type,'do_open_result',true);
            $this->error('开奖失败');
        }
        $type_list = array(
            $game_result_obj::BJKLB => '蛋蛋|北京系列',
            $game_result_obj::BJPKS => 'PK系列',
            $game_result_obj::JNDKLB => '加拿大系列',
            $game_result_obj::HGXL => '韩国系列',
            $game_result_obj::CQSSC => '重庆时时彩',
            $game_result_obj::TXFFC => '腾讯分分彩系列',
            $game_result_obj::BTBONE => '比特币1分钟系列',
            $game_result_obj::BTBTWO => '比特币1.5分钟系列',
            $game_result_obj::BTBTHREE => '比特币3分钟系列',
            $game_result_obj::FEITING => '飞艇系列'
        );
        $this->assign('type_list',$type_list);
        $this->display();
    }


    public function get_true_result()
    {
        define('OPEN_TYPE', 1);
        $game_result_obj = new GameResultModel();

        $data = I('post.');
        if(IS_POST)
        {
            log_file('open_data='.json_encode($data),'do_open_result',true);
            $type = $data['type'];
            $issue = $data['issue'];
            if(!$type)
            {
                $this->error('请选择开奖游戏系列');
            }
            if(!$issue)
            {
                $this->error('请输入开奖游戏期号');
            }
            if(!intval($issue))
            {
                $this->error('请输入正确的开奖游戏期号');
            }

            $where['type'] = $type;
            $where['issue'] = $issue;
            $game_result_info = $game_result_obj->getGameResultInfo($where,'');
            if(!$game_result_info)
            {
                $this->error('开奖期号不存在');
            }
            if($game_result_info['is_open'] != 1)
            {
                $this->error('当前期号还未开奖');
            }
            log_file('sql='.$game_result_obj->getLastSql(),'get_true_result',true);
            //开奖计算投注结果
            $game_log_obj = new GameLogModel();
//            $game_log_obj->where('game_result_id ='.$game_result_info['game_result_id'])->delete();
            $return = $game_log_obj->calculateResult($game_result_info['game_result_id'], $type);
            if($return === false)
            {
                $this->error('操作失败');
            }
            $redis_obj = new Redis();
            $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
            //获取开奖结果需要重新获取数据库的标记
            $key = 'get_true_result_'.$game_result_info['game_result_id'];
            $redis_obj->set($key,NOW_TIME);

            $this->success('操作成功');
        }
        $type_list = array(
            $game_result_obj::BJKLB => '蛋蛋|北京系列',
            $game_result_obj::BJPKS => 'PK系列',
            $game_result_obj::JNDKLB => '加拿大系列',
            $game_result_obj::HGXL => '韩国系列',
            $game_result_obj::CQSSC => '重庆时时彩',
            $game_result_obj::TXFFC => '腾讯分分彩系列',
            $game_result_obj::BTBONE => '比特币1分钟系列',
            $game_result_obj::BTBTWO => '比特币1.5分钟系列',
            $game_result_obj::BTBTHREE => '比特币3分钟系列',
            $game_result_obj::FEITING => '飞艇系列'
        );
        $this->assign('type_list',$type_list);
        $this->display();
    }
}

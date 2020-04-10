<?php
/**
 * 自动投注模型类
 * table_name = tp_bet_auto
 * py_key = bet_auto_id
 */

class BetAutoModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化自动投注id
     */
    public function BetAutoModel()
    {
        parent::__construct('bet_auto');
    }

    /**
     * 获取自动投注信息
     * @author 姜伟
     * @param int $bet_auto_id 自动投注id
     * @param string $fields 要获取的字段名
     * @return array 自动投注基本信息
     * @todo 根据where查询条件查找自动投注表中的相关数据并返回
     */
    public function getBetAutoInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改自动投注信息
     * @author 姜伟
     * @param array $arr 自动投注信息数组
     * @return boolean 操作结果
     * @todo 修改自动投注信息
     */
    public function editBetAuto($where='',$arr)
    {
        if (!is_array($arr)) return false;

//        $arr['last_edit_time'] = time();
//        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加自动投注
     * @author 姜伟
     * @param array $arr 自动投注信息数组
     * @return boolean 操作结果
     * @todo 添加自动投注
     */
    public function addBetAuto($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除自动投注
     * @author 姜伟
     * @param int $bet_auto_id 自动投注ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delBetAuto($bet_auto_id,$opt = false)
    {
        if (!is_numeric($bet_auto_id)) return false;
        if($opt)
        {
            return $this->where('bet_auto_id = ' . $bet_auto_id)->delete();
        }else{
           return $this->where('bet_auto_id = ' . $bet_auto_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取自动投注数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的自动投注数量
     * @todo 根据where子句获取自动投注数量
     */
    public function getBetAutoNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询自动投注信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 自动投注基本信息
     * @todo 根据SQL查询字句查询自动投注信息
     */
    public function getBetAutoList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getBetAutoField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取自动投注列表页数据信息列表
     * @author 姜伟
     * @param array $BetAuto_list
     * @return array $BetAuto_list
     * @todo 根据传入的$BetAuto_list获取更详细的自动投注列表页数据信息列表
     */
    public function getListData($BetAuto_list)
    {
        
    }


    /**
     * 游戏自动投注
     * @param $game_type_id
     * @date: 2019/4/12
     * @author: hui
     */
    public function gameAutoBet($game_type_id)
    {
        $bet_mode_obj = new BetModeModel();
        $game_type_obj = new GameTypeModel();

        $game_type_info = $game_type_obj->field('is_bet,isuse')->where('game_type_id =' . $game_type_id)->find();
        if($game_type_info['is_bet'] != 1 || $game_type_info['isuse'] != 1)
        {
            return;
        }

        $type = $game_type_obj->getGameTypeField('game_type_id =' . $game_type_id, 'result_type');
        $game_result_obj = new GameResultModel();
        $issue = $game_result_obj->where('type =' . $type . ' AND is_open = 0')->order('issue ASC')->getField('issue');
        $game_result_id = $game_result_obj->where('type =' . $type . ' AND is_open = 0')->order('issue ASC')->getField('game_result_id');
        $bet_log_obj = new BetLogModel();
        $user_obj = new UserModel();
        $bet_auto_list = $this->getBetAutoList('', 'start_issue <= ' . $issue . ' AND is_open = 1 AND game_type_id =' . $game_type_id);
        foreach ($bet_auto_list as $key => $value) {
            $bet_mode_info = $bet_mode_obj->getBetModeInfo('bet_mode_id =' . $value['new_mode_id'], '');

            $total_money = 0;
            $bet_json = htmlspecialchars_decode($bet_mode_info['bet_json']);
            $bet_arr = json_decode($bet_json,true);
            foreach ($bet_arr as $k => $v)
            {
                if(!is_array($v['bet_json']))
                {
                    continue;
                }
                foreach ($v['bet_json'] as $i => $val)
                {
                    $total_money += $val['money'];
                }
            }
            //投注实际金额是否相同
            if($total_money != $bet_mode_info['total_money'] || $total_money <1000)
            {
                continue;
            }

            $left_money = $user_obj->where('user_id =' . $value['user_id'])->getField('left_money');
            if($left_money >= $value['max_money'] || $left_money <= $value['min_money']){
                $this->editBetAuto('bet_auto_id ='.$value['bet_auto_id'],['is_open' => 0]);
                log_file($this->getlastsql(),'gameAutoBet');
                continue;
            }
            //自动投注
            $bet_log_info = $bet_log_obj->getBetLogInfo('game_result_id = ' . $game_result_id . ' AND game_type_id = ' . $game_type_id . ' AND user_id = ' . $value['user_id']);
            $account_obj = new AccountModel();
            $account = $account_obj->addAccount($value['user_id'], AccountModel::BETTING, $bet_mode_info['total_money'] * -1, '游戏自动投注', $value['new_mode_id']);
            if ($account < 0) {
                //自动投注没有金豆中断后，关闭自动投注
                $this->editBetAuto('bet_auto_id ='.$value['bet_auto_id'],['is_open' => 0]);
                log_file($this->getlastsql(),'gameAutoBet');
                continue;
            }
            if (!$bet_log_info) {
                //没有投注记录  添加
                $arr = array(
                    'user_id' => $value['user_id'],
                    'game_result_id' => $game_result_id,
                    'total_bet_money' => $bet_mode_info['total_money'],
                    'bet_json' => $bet_mode_info['bet_json'],
                    'game_type_id' => $game_type_id,
                    'is_auto_bet' => 1,
                    'bet_auto_id' => $value['bet_auto_id']
                );
                $res = $bet_log_obj->addBetLog($arr);
            } else {
                //有投注记录
                //投注信息更新 ，投注json
                $bet_arr = json_decode($bet_mode_info['bet_json'], true);
                $old_bet_arr = json_decode($bet_log_info['bet_json'], true);
                foreach ($bet_arr as $k => &$v) {
                    foreach ($old_bet_arr as $ki => &$vi) {

                        if ($vi['part'] == $v['part']) {
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
                    'total_bet_money' => $bet_mode_info['total_money'] + $bet_log_info['total_bet_money'],
                    'bet_json' => $bet_arr,
                    'is_auto_bet' => 1,
                    'bet_auto_id' => $value['bet_auto_id']
                );
                $res = $bet_log_obj->editBetLog('bet_log_id =' . $bet_log_info['bet_log_id'], $arr);
            }
            log_file($bet_log_obj->getlastsql(),'gameAutoBet');

//            dump($bet_log_obj->getLastSql());
            //增加
            $this->where('bet_auto_id ='.$value['bet_auto_id'])->setInc('bet_issue_num');
        }
    }


    /**
     * 自动投注输赢变换
     * @param $bet_auto_id
     * @param $is_win
     * @date: 2019/4/12
     * @author: hui
     */
    public function changeMode($bet_auto_id,$is_win){
        log_file($bet_auto_id,'changeMode');
        $bet_auto_info = $this->getBetAutoInfo('bet_auto_id ='.$bet_auto_id,'');
        if($bet_auto_info['bet_issue_num'] >= $bet_auto_info['issue_number']){
            //已投注期数达到要投注期数，关闭
            $arr['is_open'] = 0;
        }
        $bet_mode_obj = new BetModeModel();
        $bet_mode_info = $bet_mode_obj->getBetModeInfo('bet_mode_id ='.$bet_auto_info['new_mode_id'],'');
        //输赢变换
        if($is_win){
            $arr['new_mode_id'] = $bet_mode_info['win_change']?:$bet_auto_info['new_mode_id'];
        }else{
            $arr['new_mode_id'] = $bet_mode_info['loss_change']?:$bet_auto_info['new_mode_id'];
        }
        $this->editBetAuto('bet_auto_id ='.$bet_auto_id,$arr);
        log_file($this->getlastsql(),'changeMode');
    }
}

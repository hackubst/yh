<?php
/**
 * 投注记录模型类
 * table_name = tp_bet_log
 * py_key = bet_log_id
 */

class BetLogModel extends Model
{


    const A = 1;
    const B = 2;
    const C = 3;
    const D = 4;

    const SHU = 1;
    const NIU = 2;
    const HU = 3;
    const TU = 4;
    const LONG = 5;
    const SHE = 6;
    const MA = 7;
    const YANG = 8;
    const HOU = 9;
    const JI = 10;
    const GOU = 11;
    const ZHU = 12;

    static function getXiaoname($xiao)
    {
        $xiao_name_arr = [
            self::SHU=>'鼠',
            self::NIU=>'牛',
            self::HU=>'虎',
            self::TU=>'兔',
            self::LONG=>'龙',
            self::SHE=>'蛇',
            self::MA=>'马',
            self::YANG=>'羊',
            self::HOU=>'猴',
            self::JI=>'鸡',
            self::GOU=>'狗',
            self::ZHU=>'猪',
        ];
        return $xiao_name_arr[$xiao];
    }

    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化投注记录id
     */
    public function BetLogModel()
    {
        parent::__construct('bet_log');
    }

    /**
     * note:根据type获取字段名称
     * @param $pan_type
     * @return string
     */
    static function getPanStr($pan_type)
    {
        switch ($pan_type) {
            case self::A:
                $str = 'bet_json';
                break;
            case self::B:
                $str = 'bet_json_b';
                break;
            case self::C:
                $str = 'bet_json_c';
                break;
            case self::D:
                $str = 'bet_json_d';
                break;
            default:
                $str = 'bet_json';
                break;
        }

        return $str;
    }

    /**
     * 获取投注记录信息
     * @author 姜伟
     * @param int $bet_log_id 投注记录id
     * @param string $fields 要获取的字段名
     * @return array 投注记录基本信息
     * @todo 根据where查询条件查找投注记录表中的相关数据并返回
     */
    public function getBetLogInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改投注记录信息
     * @author 姜伟
     * @param array $arr 投注记录信息数组
     * @return boolean 操作结果
     * @todo 修改投注记录信息
     */
    public function editBetLog($where = '', $arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');

        return $this->where($where)->save($arr);
    }

    /**
     * 添加投注记录
     * @author 姜伟
     * @param array $arr 投注记录信息数组
     * @return boolean 操作结果
     * @todo 添加投注记录
     */
    public function addBetLog($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();
//        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除投注记录
     * @author 姜伟
     * @param int $bet_log_id 投注记录ID
     * @param int $opt ,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delBetLog($bet_log_id, $opt = false)
    {
        if (!is_numeric($bet_log_id)) return false;
        if ($opt) {
            return $this->where('bet_log_id = ' . $bet_log_id)->delete();
        } else {
            return $this->where('bet_log_id = ' . $bet_log_id)->save(array('isuse' => 2));
        }

    }

    /**
     * 根据where子句获取投注记录数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的投注记录数量
     * @todo 根据where子句获取投注记录数量
     */
    public function getBetLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询投注记录信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 投注记录基本信息
     * @todo 根据SQL查询字句查询投注记录信息
     */
    public function getBetLogList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }


    public function getBetLogListAll($fields = '', $where = '', $orderby = '', $group = '')
    {
        $this->field($fields)->where($where)->order($orderby)->group($group)->select();
        $sql = $this->getLastSql();
        log_file('sql11:'.$this->getLastSql(),'last_ssssq');
        $res = $this->query('/*FORCE_SLAVE*/ '.$sql);
        log_file('sql:'.$this->getLastSql(),'last_ssssq');
        return $res;
    }

    /**
     * 获取某一字段的值
     * @param  string $where
     * @param  string $field
     * @return void
     */
    public function getBetLogField($where, $field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取投注记录列表页数据信息列表
     * @author 姜伟
     * @param array $BetLog_list
     * @return array $BetLog_list
     * @todo 根据传入的$BetLog_list获取更详细的投注记录列表页数据信息列表
     */
    public function getListData($bet_log_list)
    {
        $game_result_obj = new GameResultModel();
        $game_log_obj = new GameLogModel();
        $game_type_obj = new GameTypeModel();
        $user_obj = new UserModel();
        foreach ($bet_log_list as $k => $v) {
            $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $v['game_type_id'], 'game_type_name,bet_json,is_fixation_rate');

            $user_info = $user_obj->getUserInfo('id', 'user_id = ' . $v['user_id']);
            $bet_log_list[$k]['id'] = $user_info['id'] ?: '';
//            $bet_log_list[$k]['old_bet_rate'] = $game_type_info['bet_json'] ? : '';
            $bet_log_list[$k]['is_fixation_rate'] = $game_type_info['is_fixation_rate'];

            $bet_log_list[$k]['game_type_name'] = $game_type_info['game_type_name'] ?: '';

            $game_result_info = $game_result_obj->getGameResultInfo('game_result_id =' . $v['game_result_id'],
                'result,issue,is_open,addtime,hash_one,hash_two,hash_three,hash_total,hash_new') ?: [];

            $game_log_info = $game_log_obj->getGameLogInfo('game_result_id =' . $v['game_result_id'] . ' AND game_type_id =' . $v['game_type_id'],
                '') ?: [];
            $bet_log_list[$k]['result'] = $game_log_info['result'] == null ? '' : $game_log_info['result'];
//            $bet_log_list[$k]['part_one_result'] = $game_log_info['part_one_result'] == null ? '' : $game_log_info['part_one_result'];
//            $bet_log_list[$k]['part_two_result'] = $game_log_info['part_two_result'] == null ? '' : $game_log_info['part_two_result'];
//            $bet_log_list[$k]['part_three_result'] = $game_log_info['part_three_result'] == null ? '' : $game_log_info['part_three_result'];
//            $bet_log_list[$k]['part_four_result'] = $game_log_info['part_four_result'] == null ? '' : $game_log_info['part_four_result'];
//            $bet_log_list[$k]['part_five_result'] = $game_log_info['part_five_result'] == null ? '' : $game_log_info['part_five_result'];
//            $bet_log_list[$k]['part_six_result'] = $game_log_info['part_six_result'] == null ? '' : $game_log_info['part_six_result'];
//            $bet_log_list[$k]['real_bet_rate'] = $game_log_info['bet_json'] ? : '';

            $bet_log_list[$k]['game_log_info'] = $game_log_info;
            $bet_log_list[$k]['game_result_info'] = $game_result_info;
            $bet_log_list[$k]['issue'] = $game_result_info['issue'] ?: '';
            $bet_log_list[$k]['is_open'] = $game_result_info['is_open'];
            $bet_log_list[$k]['opentime'] = $game_result_info['addtime'];
            $bet_log_list[$k]['hash_one'] = $game_result_info['hash_one'];
            $bet_log_list[$k]['hash_two'] = $game_result_info['hash_two'];
            $bet_log_list[$k]['hash_three'] = $game_result_info['hash_three'];
            $bet_log_list[$k]['hash_total'] = $game_result_info['hash_total'];
            $bet_log_list[$k]['hash_new'] = $game_result_info['hash_new'];
            //标准赔率中 增加 当前赔率
            $bet_log_list[$k]['old_bet_rate'] = $this->merge_rate($game_type_info['bet_json'], $game_log_info['bet_json']);

            if ($game_result_info['is_open']) {
                $bet_log_list[$k]['win_loss'] = $v['total_after_money'] - $v['total_bet_money'];
                $bet_log_list[$k]['win_loss_fee'] = feeHandle($v['total_after_money'] - $v['total_bet_money']);
                $bet_log_list[$k]['win_loss'] = feeHandle($v['total_after_money'] - $v['total_bet_money']);
            } else {
                $bet_log_list[$k]['win_loss'] = 0;
            }
            $bet_log_list[$k]['total_bet_money_fee'] = feeHandle($v['total_bet_money']);
            $bet_log_list[$k]['total_bet_money'] = feeHandle($v['total_bet_money']);
            $bet_log_list[$k]['total_after_money_fee'] = feeHandle($v['total_after_money']);



        }
        return $bet_log_list;
    }

    //每日统计数据处理
    public function getCountData($bet_log_list)
    {
        foreach ($bet_log_list as $k => $v) {

            $user_obj = new     UserModel();
            $user_info = $user_obj->getUserInfo('nickname,left_money,id', 'user_id = ' . $v['user_id']);

            $game_type_obj = new GameTypeModel();

            $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $v['game_type_id'], 'game_type_name');

            $bet_log_list[$k]['game_type_name'] = $game_type_info['game_type_name'] ?: '';
            $bet_log_list[$k]['nickname'] = $user_info['nickname'] ?: '';
            $bet_log_list[$k]['id'] = $user_info['id'] ?: '';
            $bet_log_list[$k]['left_money'] = feeHandle($user_info['left_money']) ?: '';
            $bet_log_list[$k]['win_loss'] = feeHandle($v['win_loss']) ?: '';
        }
        return $bet_log_list;
    }

    //开奖投注明细数据处理
    public function getAllData($bet_log_list)
    {
        $game_type_obj = new GameTypeModel();
        $game_result_obj = new GameResultModel();
        foreach ($bet_log_list as $k => $v) {
            $user_obj = new UserModel();
            $user_info = $user_obj->field('id,nickname')->where('user_id = ' . $v['user_id'])->find() ? : array();
            $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $v['game_type_id'], 'game_type_name');

            $game_result_info = $game_result_obj->getGameResultInfo('game_result_id =' . $v['game_result_id'], 'issue,is_open') ?: [];

            $bet_log_list[$k]['issue'] = $game_result_info['issue'] ?: '';
            $bet_log_list[$k]['is_open'] = $game_result_info['is_open'] ?: '';
            $bet_log_list[$k]['game_type_name'] = $game_type_info['game_type_name'] ?: '';
            $bet_log_list[$k]['nickname'] = $user_info['nickname'] ?: '';
            $bet_log_list[$k]['id'] = $user_info['id'] ?: 0;
            $bet_log_list[$k]['win_loss'] = feeHandle($v['total_after_money'] - $v['total_bet_money']);
            $bet_log_list[$k]['total_bet_money'] = feeHandle($v['total_bet_money']) ;
            unset($user_info);
        }
        return $bet_log_list;
    }

    //合并当前赔率 和标准赔率
    public function merge_rate($old_rate, $now_rate)
    {
        $old_rate = json_decode($old_rate, true);
        $now_rate = json_decode($now_rate, true);
        foreach ($old_rate as $k => &$v) {
            foreach ($v['bet_json'] as $ki => &$vi) {
                $vi['now_rate'] = '';

            }
            unset($vi);
        }
        unset($v);

        foreach ($old_rate as $k => &$v) {
            foreach ($now_rate as $now => $now_v) {
                if ($v['part'] == $now_v['part']) {
                    foreach ($v['bet_json'] as $ki => &$vi) {
                        foreach ($now_v['bet_json'] as $now_ki => $now_vi) {
                            if ($vi['key'] == $now_vi['key']) {
                                $vi['now_rate'] = $now_vi['rate'];
                            }
                        }
                    }
                    unset($vi);
                }
            }
        }
        unset($v);
        return json_encode($old_rate);

    }
}

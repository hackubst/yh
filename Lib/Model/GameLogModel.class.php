<?php
/**
 * 游戏开奖记录模型类
 * table_name = tp_game_log
 * py_key = game_log_id
 */

class GameLogModel extends Model
{

    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化游戏开奖记录id
     */
    public function GameLogModel()
    {
        parent::__construct('game_log');
    }

    /**
     * 获取游戏开奖记录信息
     * @param int $game_log_id 游戏开奖记录id
     * @param string $fields 要获取的字段名
     * @return array 游戏开奖记录基本信息
     * @author 姜伟
     * @todo 根据where查询条件查找游戏开奖记录表中的相关数据并返回
     */
    public function getGameLogInfo($where, $fields = '')
    {
        return $this->field($fields)->where($where)->order('game_log_id desc')->find();
    }

    /**
     * 修改游戏开奖记录信息
     * @param array $arr 游戏开奖记录信息数组
     * @return boolean 操作结果
     * @author 姜伟
     * @todo 修改游戏开奖记录信息
     */
    public function editGameLog($where = '', $arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');

        return $this->where($where)->save($arr);
    }

    /**
     * 添加游戏开奖记录
     * @param array $arr 游戏开奖记录信息数组
     * @return boolean 操作结果
     * @author 姜伟
     * @todo 添加游戏开奖记录
     */
    public function addGameLog($arr)
    {
        if (!is_array($arr)) return false;

        $arr['addtime'] = time();
//        $arr['add_user_id'] = session('user_id');

        $res = $this->add($arr);
        log_file('sql=' . $this->getLastSql(), 'addGameLog', true);
        log_file('$res =' . $res, 'addGameLog', true);
        return $res;
    }

    /**
     * 删除游戏开奖记录
     * @param int $game_log_id 游戏开奖记录ID
     * @param int $opt ,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @author 姜伟
     * @todo isuse设为1 || 真删除
     */
    public function delGameLog($game_log_id, $opt = false)
    {
        if (!is_numeric($game_log_id)) return false;
        if ($opt) {
            return $this->where('game_log_id = ' . $game_log_id)->delete();
        } else {
            return $this->where('game_log_id = ' . $game_log_id)->save(array('isuse' => 2));
        }

    }

    /**
     * 根据where子句获取游戏开奖记录数量
     * @param string|array $where where子句
     * @return int 满足条件的游戏开奖记录数量
     * @author 姜伟
     * @todo 根据where子句获取游戏开奖记录数量
     */
    public function getGameLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询游戏开奖记录信息
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 游戏开奖记录基本信息
     * @author 姜伟
     * @todo 根据SQL查询字句查询游戏开奖记录信息
     */
    public function getGameLogList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param string $where
     * @param string $field
     * @return void
     */
    public function getGameLogField($where, $field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取游戏开奖记录列表页数据信息列表
     * @param array $GameLog_list
     * @return array $GameLog_list
     * @author 姜伟
     * @todo 根据传入的$GameLog_list获取更详细的游戏开奖记录列表页数据信息列表
     */
    public function getListData($game_log_list)
    {
        foreach ($game_log_list as $k => $v) {
            $game_type_obj = new GameTypeModel();
            $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $v['game_type_id'], 'game_type_name');

            $game_log_list[$k]['game_type_name'] = $game_type_info['game_type_name'] ?: '';
        }
        return $game_log_list;
    }


    public function getResultList($game_log_list, $game_type_id)
    {
        $game_type_obj = new GameTypeModel();
        $game_result_obj = new GameResultModel();
        $result_type = $game_type_obj->getGameTypeField('game_type_id =' . $game_type_id, 'result_type');

        foreach ($game_log_list as $k => $v) {
            $where = 'is_open = 1 AND type =' . $result_type . ' AND game_result_id =' . $v['game_result_id'];  //只查询已开奖的

            $game_result_info = $game_result_obj->getGameResultInfo($where, 'issue,result,addtime,hash_one,hash_two,hash_three,hash_total,hash_new');

            $game_log_list[$k]['issue'] = $game_result_info['issue'];
            $game_log_list[$k]['addtime'] = $game_result_info['addtime'];
            $game_log_list[$k]['game_result_info'] = $game_result_info;
        }
        return $game_log_list;
    }

    //后台开奖记录
    public function getResultLog($game_log_list)
    {
        $game_result_obj = new GameResultModel();
        $game_type_obj = new GameTypeModel();

        foreach ($game_log_list as $k => $v) {

            $game_type_info = $game_type_obj->getGameTypeInfo('game_type_id =' . $v['game_type_id'], 'game_type_name');

            $where = 'game_result_id =' . $v['game_result_id'];  //只查询已开奖的

            $game_result_info = $game_result_obj->getGameResultInfo($where, '');
            $game_log_list[$k]['game_type_name'] = $game_type_info['game_type_name'] ?: '';
            $game_log_list[$k]['is_open'] = $game_result_info['is_open'] ?: '';
            $game_log_list[$k]['type'] = $game_result_info['type'] ?: '';
            $game_log_list[$k]['issue'] = $game_result_info['issue'] ?: '';
            $game_log_list[$k]['result'] = $game_result_info['result'] ?: '';
            $game_log_list[$k]['open_time'] = $game_result_info['open_time'] ?: '';
            $game_log_list[$k]['total_money'] = feeHandle($v['total_money']) ?: '';
        }
        return $game_log_list;
    }

    /**
     * 计算开奖结果
     * @param $result_id
     * @param $type
     * @date: 2019/3/12
     * @author: hui
     */
    public function calculateResult($result_id, $type)
    {

        //防止后台手动开奖和自动开奖时间同步，造成重复处理数据
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $str = 'calculateResult_'.$result_id.'_'.$type;
        if ($redis_obj->setnx($str, 1)) {
            $redis_obj->expire($str, 3);
        } else {
           return false;
        }
        log_file('start_open_game:'.$result_id.'--'.$type,'game_open',true);
//        log_file('result_id =' . $result_id, 'calculateResult', true);
//        log_file('type =' . $type, 'calculateResult', true);
        try{
            $game_result_obj = new GameResultModel();
            if ($type == GameResultModel::BJKLB) {
                //北京快乐8 开奖计算
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                $this->dandan28($result_id, $result, GameTypeModel::DANDAN28);//蛋蛋28
                $this->dandan36($result_id, $result, GameTypeModel::DANDAN36);//蛋蛋36
                $this->dandan28($result_id, $result, GameTypeModel::DANDANWAIWEI);//蛋蛋外围
                $this->dandanDingwei($result_id, $result, GameTypeModel::DANDANDINGWEI);//蛋蛋定位
                $this->dandan28($result_id, $result, GameTypeModel::DANDAN28GUDING);//蛋蛋28固定
                $this->dandanBaijiale($result_id, $result, GameTypeModel::DANDANBAIJIALE);//蛋蛋百家乐
                $this->dandanBaijialeNew($result_id, $result, GameTypeModel::DANDANBAIJIALENEW);//蛋蛋百家乐
                $this->dandanXingzuo($result_id, $result, GameTypeModel::DANDANXINGZUO);//蛋蛋星座
                $this->dandan16($result_id, $result, GameTypeModel::DANDAN16);//蛋蛋16
                $this->dandan11($result_id, $result, GameTypeModel::DANDAN11);//蛋蛋11

                $this->beijing28($result_id, $result, GameTypeModel::BEIJING28);//北京28
                $this->beijing11($result_id, $result, GameTypeModel::BEIJING11);//北京11
                $this->beijing16($result_id, $result, GameTypeModel::BEIJING16);//北京16
                $this->beijing36($result_id, $result, GameTypeModel::BEIJING36);//北京36
                $this->beijing28($result_id, $result, GameTypeModel::BEIJING28GUDING);//北京28固定
                $this->beijing28($result_id, $result, GameTypeModel::BEIJINGQUN);//北京群
                $this->beijing28($result_id, $result, GameTypeModel::DANDANQUN);//蛋蛋群

            } elseif ($type == GameResultModel::BJPKS) {
                //北京PK10 开奖计算
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                $this->pk10($result_id, $result, GameTypeModel::PK10);//PK10
                $this->pk22($result_id, $result, GameTypeModel::PK22);//PK22
                $this->pkGuanJun($result_id, $result, GameTypeModel::PKGUANJUN);//PK冠军
                $this->pkLongHu($result_id, $result, GameTypeModel::PKLONGHU);//PK龙虎
                $this->pkGuanYaJun($result_id, $result, GameTypeModel::PKGUANYAJUN);//PK冠亚军
                $this->pkSaiche($result_id,$result,GameTypeModel::BEIJINGSAICHE);

            } elseif ($type == GameResultModel::JNDKLB) {
                //加拿大快乐8 开奖计算
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                //开奖后 处理加拿大系列时间问题
                $this->jndFixTime($result_id);

                $this->beijing11($result_id, $result, GameTypeModel::JIANADA11);//加拿大11
                $this->beijing16($result_id, $result, GameTypeModel::JIANADA16);//加拿大16
                $this->beijing28($result_id, $result, GameTypeModel::JIANADA28);//加拿大28
                $this->beijing36($result_id, $result, GameTypeModel::JIANADA36);//加拿大36
                $this->beijingDingwei($result_id, $result, GameTypeModel::JIANADADINGWEI);//加拿大定位
                $this->beijing28($result_id, $result, GameTypeModel::JIANADAWAIWEI);//加拿大外围
                $this->beijing28($result_id, $result, GameTypeModel::JIANADA28GUDING);//加拿大28固定
                $this->jndBaijiale($result_id, $result, GameTypeModel::JIANADABAIJIALE);//加拿大百家乐
                $this->dandanBaijialeNew($result_id, $result, GameTypeModel::JIANADABAIJIALENEW);//新加拿大百家乐
                $this->jndXingzuo($result_id, $result, GameTypeModel::JIANADAXINGZUO);//加拿大星座
                $this->beijing28($result_id, $result, GameTypeModel::JIANADAQUN);//加拿大群
            } elseif ($type == GameResultModel::HGXL) {
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
//            echo json_encode($result);
                $result = explode(',', $result);
                echo json_encode($result);

//            die;
//            echo json_encode($result).'--';
                $this->beijing28($result_id, $result, GameTypeModel::HANGUO28);//韩国28
                $this->beijing16($result_id, $result, GameTypeModel::HANGUO16);//韩国16
                $this->beijing36($result_id, $result, GameTypeModel::HANGUO36);//韩国36
                $this->hanguo10($result_id, $result, GameTypeModel::HANGUO10);//韩国10
                $this->beijing28($result_id, $result, GameTypeModel::HANGUOWAIWEI);//韩国外围
                $this->beijingDingwei($result_id, $result, GameTypeModel::HANGUODINGWEI);//韩国定位
                $this->beijing28($result_id, $result, GameTypeModel::HANGUO28GUDING);//韩国28固定
                $this->beijing28($result_id, $result, GameTypeModel::HANGUOQUN);//韩国群
            } elseif ($type == GameResultModel::CQSSC) {
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                $this->cqssc($result_id, $result, GameTypeModel::CQSSC);//重庆时时彩
            } elseif ($type == GameResultModel::TXFFC) {
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                $this->fenfencai($result_id, $result, GameTypeModel::TENGXUNFFC);//腾讯分分彩
                $this->tengxun28($result_id, $result, GameTypeModel::TENGXUN28);//腾讯28
                $this->tengxunBaijiale($result_id, $result, GameTypeModel::TENGXUNBAIJIALE);//腾讯百家乐
                $this->tengxunXingzuo($result_id, $result, GameTypeModel::TENGXUNXINGZUO);//腾讯星座
                $this->tengxun16($result_id, $result, GameTypeModel::TENGXUN16);//腾讯16
                $this->tengxun11($result_id, $result, GameTypeModel::TENGXUN11);//腾讯11
            } elseif ($type == GameResultModel::BTBONE) {
                $hash_new = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'hash_new');
                $this->btb28($result_id,$hash_new,GameTypeModel::BTBONE28);
                $this->btbSaiche($result_id,$hash_new,GameTypeModel::BTBONESAICHE);
            } elseif ($type == GameResultModel::BTBTWO) {
                $hash_new = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'hash_new');
                $this->btb28($result_id,$hash_new,GameTypeModel::BTBTWO28);
                $this->btbSaiche($result_id,$hash_new,GameTypeModel::BTBTWOSAICHE);
            } elseif ($type == GameResultModel::BTBTHREE) {
                $hash_new = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'hash_new');
                $this->btb28($result_id,$hash_new,GameTypeModel::BTBTHREE28);
                $this->btbSaiche($result_id,$hash_new,GameTypeModel::BTBTHREESAICHE);
            }
            elseif ($type == GameResultModel::FEITING) {
                //北京PK10 开奖计算
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                $this->pk10($result_id, $result, GameTypeModel::FEITING10);//PK10
                $this->pk22($result_id, $result, GameTypeModel::FEITING22);//PK22
                $this->pkGuanJun($result_id, $result, GameTypeModel::FEITINGGUANJUN);//PK冠军
                $this->pkLongHu($result_id, $result, GameTypeModel::FEITINGFEIHU);//PK龙虎
                $this->pkGuanYaJun($result_id, $result, GameTypeModel::FEITINGYAJUN);//PK冠亚军
                $this->pkSaiche($result_id,$result,GameTypeModel::FEITINGSAICHE);

            }
            elseif ($type == GameResultModel::XIANGGANGLIUHECAI) {
                //北京PK10 开奖计算
                $result = $game_result_obj->getGameResultField('game_result_id =' . $result_id, 'result');
                $result = explode(',', $result);
                $this->liuhecai($result_id, $result, GameTypeModel::TEMA);
                $this->liuhecai($result_id, $result, GameTypeModel::LIANGMIAN);
                $this->liuhecai($result_id, $result, GameTypeModel::SEBO);
                $this->liuhecai($result_id, $result, GameTypeModel::TEXIAO);
                $this->liuhecai($result_id, $result, GameTypeModel::HEXIAO);
                $this->liuhecai($result_id,$result,GameTypeModel::TEMATOUWEISHU);
                $this->liuhecai($result_id,$result,GameTypeModel::ZHENGMA);
                $this->liuhecai($result_id,$result,GameTypeModel::ZHENGMATE);
                $this->liuhecai($result_id,$result,GameTypeModel::ZHENGMA16);
                $this->liuhecai($result_id,$result,GameTypeModel::WUXING);
                $this->liuhecai($result_id,$result,GameTypeModel::PINGTEYIXIAOWEI);
                $this->liuhecai($result_id,$result,GameTypeModel::ZHENGXIAO);
                $this->liuhecai($result_id,$result,GameTypeModel::SEBO7);
                $this->liuhecai($result_id,$result,GameTypeModel::ZONGXIAO);
                $this->liuhecai($result_id,$result,GameTypeModel::ZIXUANBUZHONG);
                $this->liuhecai($result_id,$result,GameTypeModel::LIANXIAOLIANWEI);
                $this->liuhecai($result_id,$result,GameTypeModel::LIANMA);

            }
            log_file('end_open_game:'.$result_id.'--'.$type,'game_open',true);
        } catch (Exception $exception) {
            log_file('open_failed:'.$exception->getMessage(),'game_open',true);
        }


    }


    /**
     * 处理8点后第一期的时间
     * @param $result_id
     */
    public function jndFixTime($result_id)
    {
        $game_result_obj = new  GameResultModel();
        $result_info = $game_result_obj->getGameResultInfo('game_result_id =' . $result_id, '');
        $first_addtime = strtotime(date('Ymd 19:57:30', time()));
        if ($result_info['addtime'] == $first_addtime) {
            //取标准第一次开奖时间，拿此次实际开奖时间获得单次差   实际开奖-单次差 = 预测开奖时间
            $time = $result_info['open_time'] - $first_addtime;
            $yu = $time % (3.5 * 60); //单次差
            $mabel_time = $result_info['open_time'] - $yu; //预计开奖时间
            $sub_time = $mabel_time - $result_info['addtime']; //缺了几期的时间，后面所有的期数 加上这个时间
            //没开奖的
            $list = $game_result_obj->getGameResultList('', 'type =' . GameResultModel::JNDKLB . ' AND game_result_id >= ' . $result_id);
            foreach ($list as $k => $v) {
                $addtime = $v['addtime'] + $sub_time;
                $game_result_obj->where('game_result_id =' . $v['game_result_id'])->save(['addtime' => $addtime]);
            }
        }
    }

//-------------蛋蛋系列--------------//

    /**
     * 蛋蛋系列 3区计算 28  (1,2,3,4,5,6 | 7,8,9,10,11,12 | 13,14,15,16,17,18)
     * @param $result_arr
     * @date: 2019/3/14
     * @return array
     * @author: hui
     */
    public function dandan_three_part_28($result_arr)
    {
        //计算每个分区的结果
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        foreach ($result_arr as $v) {
            if ($i <= 6) {
                $part1 += $v;
            } elseif ($i > 6 && $i <= 12) {
                $part2 += $v;
            } elseif ($i > 12 && $i <= 18) {
                $part3 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 10;
        $last_part2 = $part2 % 10;
        $last_part3 = $part3 % 10;

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
        );
    }

    /**
     * 蛋蛋系列 3区计算 16  (1,2,3,4,5,6 | 7,8,9,10,11,12 | 13,14,15,16,17,18)
     * @param $result_arr
     * @date: 2019/3/18
     * @return array
     * @author: hui
     */
    public function dandan_three_part_16($result_arr)
    {
        //计算每个分区的结果
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        foreach ($result_arr as $v) {
            if ($i <= 6) {
                $part1 += $v;
            } elseif ($i > 6 && $i < 12) {
                $part2 += $v;
            } elseif ($i > 12 && $i <= 18) {
                $part3 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 6 + 1;
        $last_part2 = $part2 % 6 + 1;
        $last_part3 = $part3 % 6 + 1;

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
        );
    }

    /**
     * 蛋蛋系列 6区计算 (1,2,3,19,20 | 4,5,6 | 7,8,9 | 10,11,12 | 13,14,15 | 16,17,18)
     * @param $result_arr
     * @date: 2019/3/14
     * @return array
     * @author: hui
     */
    public function dandan_six_part($result_arr)
    {
        //计算每个分区的结果
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        $part4 = 0;
        $part5 = 0;
        $part6 = 0;
        foreach ($result_arr as $v) {
            if ($i <= 3 || $i > 18) {
                $part1 += $v;
            } elseif ($i > 3 && $i <= 6) {
                $part2 += $v;
            } elseif ($i > 6 && $i <= 9) {
                $part3 += $v;
            } elseif ($i > 9 && $i <= 12) {
                $part4 += $v;
            } elseif ($i > 12 && $i <= 15) {
                $part5 += $v;
            } elseif ($i > 15 && $i <= 18) {
                $part6 += $v;
            }
            $i++;
        }
        //每张牌的数字
        $last_part1 = $part1 % 13 + 1;
        $last_part2 = $part2 % 13 + 1;
        $last_part3 = $part3 % 13 + 1;
        $last_part4 = $part4 % 13 + 1;
        $last_part5 = $part5 % 13 + 1;
        $last_part6 = $part6 % 13 + 1;

        $data[1] = array('num' => $last_part1, 'part' => 1);
        $data[2] = array('num' => $last_part2, 'part' => 2);
        $data[3] = array('num' => $last_part3, 'part' => 3);
        $data[4] = array('num' => $last_part4, 'part' => 4);
        $data[5] = array('num' => $last_part5, 'part' => 5);
        $data[6] = array('num' => $last_part6, 'part' => 6);

//        foreach ($data as $k => $v) {
//            $num[] = $v['num'];
//            $part[] = $v['part'];
//        }
        $keys = array_keys($data);

        //根据牌面大小排序
        array_multisort(array_column($data, 'num'), SORT_ASC, $data, $keys);

        //随机颜色
        foreach ($data as $ki => &$vi) {
            if ($data[$ki]['num'] != $data[$ki - 1]['num']) {
                $vi['type'] = rand(1, 4);
            } else {
                $vi['type'] = (($data[$ki - 1]['type'] + 1) % 4) ?: 4;
            }
        }
        //根据分区重新排序
        array_multisort(array_column($data, 'part'), SORT_ASC, $data, $keys);

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_four_sum' => $part4,
            'part_five_sum' => $part5,
            'part_six_sum' => $part6,
            'part_one_result' => $data[0],
            'part_two_result' => $data[1],
            'part_three_result' => $data[2],
            'part_four_result' => $data[3],
            'part_five_result' => $data[4],
            'part_six_result' => $data[5],
        );
    }

    /**
     * 蛋蛋28/蛋蛋外围/蛋蛋28固定
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/12
     * @author: hui
     */
    public function dandan28($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_three_part_28($result_arr);
        //结果计算
        $result = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        $last_result = '';
        //单双
        if ($result % 2) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //中边
        if ($result >= 10 && $result <= 17) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //大小
        if ($result >= 14) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //尾数大小
        if ($result % 10 >= 5) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //余数
        $last_result .= $result % 3 . ',';
        $last_result .= $result % 4 . ',';
        $last_result .= $result % 5;


        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);


        $part['last_result'] = $last_result;
        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 蛋蛋36
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/14
     * @author: hui
     */
    public function dandan36($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_three_part_28($result_arr);
        //结果计算
        $type = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $type;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);

    }

    /**
     * 蛋蛋定位
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/14
     * @author: hui
     */
    public function dandanDingwei($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_three_part_28($result_arr);
        //结果计算
        $first = $part['part_one_result'] . $part['part_two_result'];
        $second = $part['part_two_result'] . $part['part_three_result'];
        //大小
        if ($first > 49) {
            $first_size = GameTypeModel::BIG;
        } else {
            $first_size = GameTypeModel::SMALL;
        }
        if ($second > 49) {
            $second_size = GameTypeModel::BIG;
        } else {
            $second_size = GameTypeModel::SMALL;
        }
        //单双
        if ($first % 2 == 0) {
            $first_type = GameTypeModel::DOUBLE;
        } else {
            $first_type = GameTypeModel::SIGNLE;
        }
        if ($second % 2 == 0) {
            $second_type = GameTypeModel::DOUBLE;
        } else {
            $second_type = GameTypeModel::SIGNLE;
        }
        //龙虎和
        $type = $this->longHuHe($part['part_one_result'], $part['part_three_result']);
        $arr = [$first, $first_size, $first_type, $second, $second_size, $second_type, $type];
        $result = implode(',', $arr);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;//蛋蛋外围
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 蛋蛋百家乐
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/3
     * @author: hui
     */
    public function dandanBaijiale($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_three_part_28($result_arr);
        //结果计算
        //球一
        if ($part['part_one_result'] > $part['part_three_result']) {
            $first_type = GameTypeModel::XIAN;
        } elseif ($part['part_one_result'] < $part['part_three_result']) {
            $first_type = GameTypeModel::ZHUANG;
        } else {
            $first_type = GameTypeModel::PING;
        }
        //球二
        if ($part['part_two_result'] > $part['part_three_result']) {
            $second_type = GameTypeModel::XIAN;
        } elseif ($part['part_two_result'] < $part['part_three_result']) {
            $second_type = GameTypeModel::ZHUANG;
        } else {
            $second_type = GameTypeModel::PING;
        }
        $type = $first_type . ',' . $second_type;
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $type;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 新蛋蛋百家乐
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function dandanBaijialeNew($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_six_part($result_arr);
        //计算结果
        $one = $part['part_one_result']['num'] < 10 ? $part['part_one_result']['num'] : 10;
        $two = $part['part_two_result']['num'] < 10 ? $part['part_two_result']['num'] : 10;
        $three = $part['part_three_result']['num'] < 10 ? $part['part_three_result']['num'] : 10;
        $four = $part['part_four_result']['num'] < 10 ? $part['part_four_result']['num'] : 10;
        $five = $part['part_five_result']['num'] < 10 ? $part['part_five_result']['num'] : 10;
        $six = $part['part_six_result']['num'] < 10 ? $part['part_six_result']['num'] : 10;
        //发牌
        $xian_card = array($part['part_one_result'], $part['part_three_result']);
        $zhuang_card = array($part['part_two_result'], $part['part_four_result']);
        $xian = ($one + $three) % 10;
        $zhuang = ($two + $four) % 10;
        $card_num = 4;
        $remark = '起手牌-闲：' . $xian . '，庄：' . $zhuang . '。';
        if ($xian > 7 || $zhuang > 7) {
            $remark .= '不需要补牌';
        } elseif ($xian == 6 || $xian == 7) {
            $remark .= '闲不需要补牌,';
            if ($zhuang < 7) {
                $card_num++;
                $remark .= '满足庄补第一张条件';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_five_result']);
                $zhuang = ($two + $four + $five) % 10;
            } else {
                $remark .= '庄不需要补牌';
            }
        } elseif ($xian < 6) {
            $remark .= '满足闲补第一张条件，庄为：' . $zhuang . '，且补牌为：' . $five . '，';
            $card_num++;
            $xian_card = array($part['part_one_result'], $part['part_three_result'], $part['part_five_result']);
            $xian = ($one + $three + $five) % 10;
            if ($zhuang < 3) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 3 && $five != 8) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 4 && $five != 10 && $five != 1 && $five != 8 && $five != 9) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 5 && $five != 10 && $five != 1 && $five != 2 && $five != 3 && $five != 8 && $five != 9) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 6 && ($five == 6 || $five == 7)) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } else {
                $remark .= '不满足补第二张牌要求';
            }
        }
        //庄闲
        if ($zhuang > $xian) {
            $result = GameTypeModel::ZHUANG;
        } elseif ($zhuang < $xian) {
            $result = GameTypeModel::XIAN;
        } else {
            $result = GameTypeModel::PING;
        }
        //对子
        $tag1 = 0; //庄对标记
        $tag2 = 0; //闲对标记
        if ($zhuang_card[0]['num'] == $zhuang_card[1]['num']) {
            $tag1 = 1;
        }

        if ($xian_card[0]['num'] == $xian_card[1]['num']) {
            $tag2 = 1;
        }

        if ($tag1 == 1) {
            $result .= ',' . GameTypeModel::ZHUANGDUI;
        }
        if ($tag2 == 1) {
            $result .= ',' . GameTypeModel::XIANDUI;
        }
        if ($tag2 == 1 || $tag1 == 1) {
            $result .= ',' . GameTypeModel::RONDDUI;
        }
        if ($tag2 == 1 && $tag1 == 1) {
            $result .= ',' . GameTypeModel::DOUBLEDUI;
        }
        if ($card_num > 4) {
            $result .= ',' . GameTypeModel::DA;
        } else {
            $result .= ',' . GameTypeModel::XIAO;
        }

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['part_one_result'] = json_encode($part['part_one_result']);
        $part['part_two_result'] = json_encode($part['part_two_result']);
        $part['part_three_result'] = json_encode($part['part_three_result']);
        $part['part_four_result'] = json_encode($part['part_four_result']);
        $part['part_five_result'] = json_encode($part['part_five_result']);
        $part['part_six_result'] = json_encode($part['part_six_result']);
        $part['result'] = $result;
        $part['remark'] = $remark;
        $part['zhuang_card'] = json_encode($zhuang_card);
        $part['xian_card'] = json_encode($xian_card);
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 蛋蛋星座
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function dandanXingzuo($result_id, $result_arr, $game_type_id)
    {
        $part = $this->beijing_three_part_28($result_arr);
        $sum = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //结果计算
        //1:3
        $one_type = $this->longHuHe($part['part_one_result'], $part['part_three_result']);
        //2:3
        $two_type = $this->longHuHe($part['part_two_result'], $part['part_three_result']);
        //豹对
        $bao_dui = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //五行
        $wu_xing = $this->wuXing($sum);
        //四季
        $si_ji = $this->siJi($sum);
        //星座
        $xing_zuo = $this->xingZuo($sum);
        //生肖
        $sheng_xiao = $this->shengXiao($sum);
        //前二
        $first = $part['part_one_result'] . $part['part_two_result'];
        $second = $part['part_two_result'] . $part['part_three_result'];
        //大小
        if ($first > 49) {
            $first_size = GameTypeModel::BIG;
        } else {
            $first_size = GameTypeModel::SMALL;
        }
        if ($second > 49) {
            $second_size = GameTypeModel::BIG;
        } else {
            $second_size = GameTypeModel::SMALL;
        }
        //单双
        if ($first % 2 == 0) {
            $first_type = GameTypeModel::DOUBLE;
        } else {
            $first_type = GameTypeModel::SIGNLE;
        }
        if ($second % 2 == 0) {
            $second_type = GameTypeModel::DOUBLE;
        } else {
            $second_type = GameTypeModel::SIGNLE;
        }
        $type = array($sum, $one_type, $two_type, $bao_dui, $wu_xing, $si_ji, $xing_zuo, $sheng_xiao, $first_size, $first_type, $second_size, $second_type);
        $result = implode(',', $type);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 蛋蛋16
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function dandan16($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_three_part_16($result_arr);
        //结果计算
        $result = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 蛋蛋11
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function dandan11($result_id, $result_arr, $game_type_id)
    {
        $part = $this->dandan_three_part_16($result_arr);
        //结果计算
        $result = $part['part_one_result'] + $part['part_three_result'];
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

//-------------北京系列--------------//

    /**
     * 北京系列 3区计算 28 (2,5,8,11,14,17 | 3,6,9,12,15,18 | 4,7,10,13,16,19)
     * @param $result_arr
     * @date: 2019/3/18
     * @return array
     * @author: hui
     */
    public function beijing_three_part_28($result_arr)
    {
        //计算每个分区的结果
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        foreach ($result_arr as $v) {
            if ($i == 2 || $i == 5 || $i == 8 || $i == 11 || $i == 14 || $i == 17) {
                $part1 += $v;
            } elseif ($i == 3 || $i == 6 || $i == 9 || $i == 12 || $i == 15 || $i == 18) {
                $part2 += $v;
            } elseif ($i == 4 || $i == 7 || $i == 10 || $i == 13 || $i == 16 || $i == 19) {
                $part3 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 10;
        $last_part2 = $part2 % 10;
        $last_part3 = $part3 % 10;
        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
        );
    }

    /**
     * 北京系列 3区计算 16 (1,4,7,10,13,16 | 2,5,8,11,14,17 | 3,6,9,12,15,18)
     * @param $result_arr
     * @date: 2019/3/18
     * @return array
     * @author: hui
     */
    public function beijing_three_part_16($result_arr)
    {
        //计算每个分区的结果
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        foreach ($result_arr as $v) {
            if ($i == 1 || $i == 4 || $i == 7 || $i == 10 || $i == 13 || $i == 16) {
                $part1 += $v;
            } elseif ($i == 2 || $i == 5 || $i == 8 || $i == 11 || $i == 14 || $i == 17) {
                $part2 += $v;
            } elseif ($i == 3 || $i == 6 || $i == 9 || $i == 12 || $i == 15 || $i == 18) {
                $part3 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 6 + 1;
        $last_part2 = $part2 % 6 + 1;
        $last_part3 = $part3 % 6 + 1;

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
        );
    }

    /**
     * 北京28 北京28固定
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function beijing28($result_id, $result_arr, $game_type_id)
    {
//        echo json_encode($result_arr).'hahaha';
        $game_type_obj = new GameTypeModel();
        $is_kill = 0;
        $kill_res = false;

        $part = $this->beijing_three_part_28($result_arr);

        //结果计算
        $result = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //计算中奖人数 投注金额
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;

        if($is_kill&&$kill_res){
            $part['ex_result'] = implode(',',$result_arr);
        }
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);

        $this->calculateReward($game_log_id);
    }

    /**
     * 北京28 北京28固定
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function beijingDingwei($result_id, $result_arr, $game_type_id)
    {
//        if($game_type_id == GameTypeModel::HANGUO28GUDING){
//            $game_type_obj = new GameTypeModel();
//            $is_kill = $game_type_obj->getGameTypeField('game_type_id ='.$game_type_id,'is_kill');
//            if($is_kill){
//                $game_log_obj = new GameLogModel();
//                $kill_res = $game_log_obj->hanguo16Kill($result_id,$game_type_id);
//                if ($kill_res) {//kill calculate success
//                    $result_arr = $kill_res;
//                }
//
//            }
//        }
        $part = $this->beijing_three_part_28($result_arr);
        //结果计算
        //结果计算
        $first = $part['part_one_result'] . $part['part_two_result'];
        $second = $part['part_two_result'] . $part['part_three_result'];
        //大小
        if ($first > 49) {
            $first_size = GameTypeModel::BIG;
        } else {
            $first_size = GameTypeModel::SMALL;
        }
        if ($second > 49) {
            $second_size = GameTypeModel::BIG;
        } else {
            $second_size = GameTypeModel::SMALL;
        }
        //单双
        if ($first % 2 == 0) {
            $first_type = GameTypeModel::DOUBLE;
        } else {
            $first_type = GameTypeModel::SIGNLE;
        }
        if ($second % 2 == 0) {
            $second_type = GameTypeModel::DOUBLE;
        } else {
            $second_type = GameTypeModel::SIGNLE;
        }
        //龙虎和
        $type = $this->longHuHe($part['part_one_result'], $part['part_three_result']);
        $arr = [$first, $first_size, $first_type, $second, $second_size, $second_type, $type];
        $result = implode(',', $arr);        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 北京11
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function beijing11($result_id, $result_arr, $game_type_id)
    {
        $part = $this->beijing_three_part_16($result_arr);
        //结果计算
        $result = $part['part_one_result'] + $part['part_three_result'];
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);

    }


    /**
     * 北京16
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function beijing16($result_id, $result_arr, $game_type_id)
    {
//        if($game_type_id == GameTypeModel::HANGUO16){
//            $game_type_obj = new GameTypeModel();
//            $is_kill = $game_type_obj->getGameTypeField('game_type_id ='.$game_type_id,'is_kill');
//            if($is_kill){
//                $game_log_obj = new GameLogModel();
//                $kill_res = $game_log_obj->hanguo16Kill($result_id,$game_type_id);
//                if ($kill_res) {//kill calculate success
//                    $result_arr = $kill_res;
//                }
//
//            }
//        }
        $part = $this->beijing_three_part_16($result_arr);
        //结果计算
        $result = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 北京36
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/14
     * @author: hui
     */
    public function beijing36($result_id, $result_arr, $game_type_id)
    {
//        if($game_type_id == GameTypeModel::HANGUO36){
//            $game_type_obj = new GameTypeModel();
//            $is_kill = $game_type_obj->getGameTypeField('game_type_id ='.$game_type_id,'is_kill');
//            if($is_kill){
//                $game_log_obj = new GameLogModel();
//                $kill_res = $game_log_obj->hanguo36Kill($result_id,$game_type_id);
//                if ($kill_res) {//kill calculate success
//                    $result_arr = $kill_res;
//                }
//
//            }
//        }
        $part = $this->beijing_three_part_28($result_arr);
        //结果计算
        $type = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $type;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


//-------------PK系列--------------//

    /**
     * PK系列 3区计算22
     * @param $result_arr
     * @date: 2019/3/18
     * @return array
     * @author: hui
     */
    public function pk_three_part_22($result_arr)
    {
        //计算每个分区的结果
        $i = 0;
        foreach ($result_arr as $v) {
            if ($i == 0) {
                $part1 = $v;
            } elseif ($i == 1) {
                $part2 = $v;
            } elseif ($i == 2) {
                $part3 = $v;
                break;
            }
            $i++;
        }
        $last_part1 = $part1;
        $last_part2 = $part2;
        $last_part3 = $part3;

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
        );
    }

    /**
     * PK10
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/19
     * @author: hui
     */
    public function pk10($result_id, $result_arr, $game_type_id)
    {
        $game_result_obj = new GameResultModel();
        $issue = $game_result_obj->getGameResultField('game_result_id = ' . $result_id, 'issue');
        $issue = $issue % 10 - 1;
        if ($issue < 0) {
            $issue += 10;
        }
        $i = 0;
        foreach ($result_arr as $v) {
            if ($i == $issue) {
                $res = $v;
                $result = $v . ',' . $v;
            }
            $i++;
        }
        $part['part_one_sum'] = $res;
        $part['part_one_result'] = $res;

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * PK22
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/19
     * @author: hui
     */
    public function pk22($result_id, $result_arr, $game_type_id)
    {
        $part = $this->pk_three_part_22($result_arr);
        $res = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        $result = array($res, $part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        $result = implode(',', $result);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * PK冠军
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/19
     * @author: hui
     */
    public function pkGuanJun($result_id, $result_arr, $game_type_id)
    {
        $i = 0;
        foreach ($result_arr as $v) {
            if ($i == 0) {
                $res = $v;
                $result = $v . ',' . $v;
                break;
            }
            $i++;
        }
        $part['part_one_sum'] = $res;
        $part['part_one_result'] = $res;
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * PK龙虎
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/19
     * @author: hui
     */
    public function pkLongHu($result_id, $result_arr, $game_type_id)
    {
        $i = 0;
        foreach ($result_arr as $v) {
            if ($i == 0) {
                $part1 = $v;
            } elseif ($i == 9) {
                $part2 = $v;
            }
            $i++;
        }
        $part['part_one_sum'] = $part1;
        $part['part_one_result'] = $part1;
        $part['part_three_sum'] = $part2;
        $part['part_three_result'] = $part2;
        $res = $this->longHuHe($part1, $part2);
        $result = array($res, $part1, $part2);
        $result = implode(',', $result);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    //六合彩开奖
    public function liuhecai($result_id, $result_arr, $game_type_id)
    {
        $result = implode(',', $result_arr);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = array_pop($result_arr);
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * PK冠亚军
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/19
     * @author: hui
     */
    public function pkGuanYaJun($result_id, $result_arr, $game_type_id)
    {
        $i = 0;
        foreach ($result_arr as $v) {
            if ($i == 0) {
                $part1 = $v;
            } elseif ($i == 1) {
                $part2 = $v;
                break;
            }
            $i++;
        }
        $part['part_one_sum'] = $part1;
        $part['part_one_result'] = $part1;
        $part['part_three_sum'] = $part2;
        $part['part_three_result'] = $part2;
        $res = $part1 + $part2;
        $result = array($res, $part1, $part2);
        $result = implode(',', $result);

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    public function pkSaiche($result_id, $result, $game_type_id)
    {


        $part['result'] = $this->saiChe($result);

        $result = implode(',',$result);
        $game_result_obj = new GameResultModel();
        $game_result_obj->editGameResult('game_result_id =' . $result_id, ['result' => $result]);

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
//        $part['sixteen_data'] = json_encode($data);
//        $part['result_data'] = json_encode($ten_data);

        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;

        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);

        $this->calculateReward($game_log_id);
    }

    //-------------加拿大系列--------------//

    /**
     * 加拿大百家乐
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/14
     * @author: hui
     */
    public function jndBaijiale($result_id, $result_arr, $game_type_id)
    {
        $part = $this->beijing_three_part_28($result_arr);
        //结果计算
        //球一
        if ($part['part_one_result'] > $part['part_three_result']) {
            $first_type = GameTypeModel::XIAN;
        } elseif ($part['part_one_result'] < $part['part_three_result']) {
            $first_type = GameTypeModel::ZHUANG;
        } else {
            $first_type = GameTypeModel::PING;
        }
        //球二
        if ($part['part_two_result'] > $part['part_three_result']) {
            $second_type = GameTypeModel::XIAN;
        } elseif ($part['part_two_result'] < $part['part_three_result']) {
            $second_type = GameTypeModel::ZHUANG;
        } else {
            $second_type = GameTypeModel::PING;
        }
        $type = $first_type . ',' . $second_type;
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $type;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    /**
     * 加拿大星座
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/18
     * @author: hui
     */
    public function jndXingzuo($result_id, $result_arr, $game_type_id)
    {
        $part = $this->beijing_three_part_28($result_arr);
        $sum = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //结果计算
        //1:3
        $one_type = $this->longHuHe($part['part_one_result'], $part['part_three_result']);
        //2:3
        $two_type = $this->longHuHe($part['part_two_result'], $part['part_three_result']);
        //豹对
        $bao_dui = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //五行
        $wu_xing = $this->wuXing($sum);
        //四季
        $si_ji = $this->siJi($sum);
        //星座
        $xing_zuo = $this->xingZuo($sum);
        //生肖
        $sheng_xiao = $this->shengXiao($sum);
        //前二
        $first = $part['part_one_result'] . $part['part_two_result'];
        $second = $part['part_two_result'] . $part['part_three_result'];
        //大小
        if ($first > 49) {
            $first_size = GameTypeModel::BIG;
        } else {
            $first_size = GameTypeModel::SMALL;
        }
        if ($second > 49) {
            $second_size = GameTypeModel::BIG;
        } else {
            $second_size = GameTypeModel::SMALL;
        }
        //单双
        if ($first % 2 == 0) {
            $first_type = GameTypeModel::DOUBLE;
        } else {
            $first_type = GameTypeModel::SIGNLE;
        }
        if ($second % 2 == 0) {
            $second_type = GameTypeModel::DOUBLE;
        } else {
            $second_type = GameTypeModel::SIGNLE;
        }
        $type = array($sum, $one_type, $two_type, $bao_dui, $wu_xing, $si_ji, $xing_zuo, $sheng_xiao, $first_size, $first_type, $second_size, $second_type);
        $result = implode(',', $type);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    //-------------韩国系列--------------//

    /**
     * 韩国10
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/3/21
     * @author: hui
     */
    public function hanguo10($result_id, $result_arr, $game_type_id)
    {
//        if($game_type_id == GameTypeModel::HANGUO10){
//            $game_type_obj = new GameTypeModel();
//            $is_kill = $game_type_obj->getGameTypeField('game_type_id ='.$game_type_id,'is_kill');
//            if($is_kill){
//                $game_log_obj = new GameLogModel();
//                $kill_res = $game_log_obj->hanguo10Kill($result_id,$game_type_id);
//                if ($kill_res) {//kill calculate success
//                    $result_arr = $kill_res;
//                }
//            }
//        }
        $part = $this->dandan_three_part_28($result_arr);
        $part['part_one_sum'] += $result_arr[6];
        $part['part_one_result'] = $part['part_one_sum'] % 10;
        $result = $part['part_one_result'] + 1;
        unset($part['part_two_result']);
        unset($part['part_two_sum']);
        unset($part['part_three_result']);
        unset($part['part_three_sum']);
        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    //-------------重庆时时彩--------------///

    /**
     * 重庆时时彩
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/3
     * @author: hui
     */
    public function cqssc($result_id, $result_arr, $game_type_id)
    {
        $part = [];
        $part['part_one_result'] = $result_arr[0];
        $part['part_two_result'] = $result_arr[1];
        $part['part_three_result'] = $result_arr[2];
        $part['part_four_result'] = $result_arr[3];
        $part['part_five_result'] = $result_arr[4];
        //和
        $sum = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'] + $part['part_four_result'] + $part['part_five_result'];
        //和大小
        if ($sum >= 23) {
            $sum_daxiao = GameTypeModel::BIG;
        } elseif ($sum < 23) {
            $sum_daxiao = GameTypeModel::SMALL;
        }
        //和单双
        $sum_danshuang = $sum % 2 ? GameTypeModel::SIGNLE : GameTypeModel::DOUBLE;
        //龙虎
        $longhu = $this->longHuHe($part['part_one_result'], $part['part_five_result']);
        //前三
        $first = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //中三
        $second = $this->baoDui($part['part_two_result'], $part['part_three_result'], $part['part_four_result']);
        //后三
        $last = $this->baoDui($part['part_three_result'], $part['part_four_result'], $part['part_five_result']);

        $result = array($sum, $sum_daxiao, $sum_danshuang, $longhu, $first, $second, $last);
        $result = implode(',', $result);

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    //-------------腾讯分分彩--------------///

    /**
     * 腾讯分分彩  5区计算  1,4,7| 2,5,8|1,2,3,4,5,6,7,8|1,3,8|2,4,6
     * @param $result_arr
     * @date: 2019/4/18
     * @return array
     * @author: hui
     */
    public function tengxun_five_part($result_arr)
    {
        //计算每个分区的结果
        $result_arr = array_reverse($result_arr);
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        $part4 = 0;
        $part5 = 0;
        foreach ($result_arr as $v) {
            if ($i == 1 || $i == 4 || $i == 7) {
                $part1 += $v;
            }
            if ($i == 2 || $i == 5 || $i == 8) {
                $part2 += $v;
            }
            if ($i <= 8) {
                $part3 += $v;
            }
            if ($i == 1 || $i == 3 || $i == 8) {
                $part4 += $v;
            }
            if ($i == 2 || $i == 4 || $i == 6) {
                $part5 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 10;
        $last_part2 = $part2 % 10;
        $last_part3 = $part3 % 10;
        $last_part4 = $part4 % 10;
        $last_part5 = $part5 % 10;

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_four_sum' => $part4,
            'part_five_sum' => $part5,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
            'part_four_result' => $last_part4,
            'part_five_result' => $last_part5,
        );
    }

    /**
     * 腾讯分分彩  6区计算 1,4,7| 2,5,8|3,6,9|1,2,3|4,5,6|7,8,9
     * @param $result_arr
     * @date: 2019/4/18
     * @return array
     * @author: hui
     */
    public function tengxun_six_part($result_arr)
    {
        //计算每个分区的结果
        $result_arr = array_reverse($result_arr);
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        $part4 = 0;
        $part5 = 0;
        $part6 = 0;
        foreach ($result_arr as $v) {
            if ($i == 1 || $i == 4 || $i == 7) {
                $part1 += $v;
            }
            if ($i == 2 || $i == 5 || $i == 8) {
                $part2 += $v;
            }
            if ($i == 3 || $i == 6 || $i == 9) {
                $part3 += $v;
            }
            if ($i == 1 || $i == 2 || $i == 3) {
                $part4 += $v;
            }
            if ($i == 4 || $i == 5 || $i == 6) {
                $part5 += $v;
            }
            if ($i == 7 || $i == 8 || $i == 9) {
                $part6 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 13 + 1;
        $last_part2 = $part2 % 13 + 1;
        $last_part3 = $part3 % 13 + 1;
        $last_part4 = $part4 % 13 + 1;
        $last_part5 = $part5 % 13 + 1;
        $last_part6 = $part6 % 13 + 1;


        $data[1] = array('num' => $last_part1, 'part' => 1);
        $data[2] = array('num' => $last_part2, 'part' => 2);
        $data[3] = array('num' => $last_part3, 'part' => 3);
        $data[4] = array('num' => $last_part4, 'part' => 4);
        $data[5] = array('num' => $last_part5, 'part' => 5);
        $data[6] = array('num' => $last_part6, 'part' => 6);

//        foreach ($data as $k => $v) {
//            $num[] = $v['num'];
//            $part[] = $v['part'];
//        }
        $keys = array_keys($data);

        //根据牌面大小排序
        array_multisort(array_column($data, 'num'), SORT_ASC, $data, $keys);

        //随机颜色
        foreach ($data as $ki => &$vi) {
            if ($data[$ki]['num'] != $data[$ki - 1]['num']) {
                $vi['type'] = rand(1, 4);
            } else {
                $vi['type'] = (($data[$ki - 1]['type'] + 1) % 4) ?: 4;
            }
        }
        //根据分区重新排序
        array_multisort(array_column($data, 'part'), SORT_ASC, $data, $keys);

        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_four_sum' => $part4,
            'part_five_sum' => $part5,
            'part_six_sum' => $part6,
            'part_one_result' => $data[0],
            'part_two_result' => $data[1],
            'part_three_result' => $data[2],
            'part_four_result' => $data[3],
            'part_five_result' => $data[4],
            'part_six_result' => $data[5],
        );
    }


    public function tengxun_three_part_16($result_arr)
    {
        //计算每个分区的结果
        $result_arr = array_reverse($result_arr);
        $i = 1;
        $part1 = 0;
        $part2 = 0;
        $part3 = 0;
        foreach ($result_arr as $v) {
            if ($i == 1 || $i == 4 || $i == 7) {
                $part1 += $v;
            }
            if ($i == 2 || $i == 5 || $i == 8) {
                $part2 += $v;
            }
            if ($i == 3 || $i == 6 || $i == 9) {
                $part3 += $v;
            }
            $i++;
        }
        $last_part1 = $part1 % 6 + 1;
        $last_part2 = $part2 % 6 + 1;
        $last_part3 = $part3 % 6 + 1;


        return array(
            'part_one_sum' => $part1,
            'part_two_sum' => $part2,
            'part_three_sum' => $part3,
            'part_one_result' => $last_part1,
            'part_two_result' => $last_part2,
            'part_three_result' => $last_part3,
        );
    }


    /**
     * 腾讯分分彩
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/18
     * @author: hui
     */
    public function fenfencai($result_id, $result_arr, $game_type_id)
    {
        $part = $this->tengxun_five_part($result_arr);
        //前三
        $first = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //中三
        $second = $this->baoDui($part['part_two_result'], $part['part_three_result'], $part['part_four_result']);
        //后三
        $last = $this->baoDui($part['part_three_result'], $part['part_four_result'], $part['part_five_result']);

        $result = array($first, $second, $last);
        $result = implode(',', $result);

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    /**
     * 腾讯28
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/18
     * @author: hui
     */
    public function tengxun28($result_id, $result_arr, $game_type_id)
    {
        $part = $this->tengxun_five_part($result_arr);

        $result = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 腾讯百家乐
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/18
     * @author: hui
     */
    public function tengxunBaijiale($result_id, $result_arr, $game_type_id)
    {
        $part = $this->tengxun_six_part($result_arr);

        //计算结果
        $one = $part['part_one_result']['num'] < 10 ? $part['part_one_result']['num'] : 10;
        $two = $part['part_two_result']['num'] < 10 ? $part['part_two_result']['num'] : 10;
        $three = $part['part_three_result']['num'] < 10 ? $part['part_three_result']['num'] : 10;
        $four = $part['part_four_result']['num'] < 10 ? $part['part_four_result']['num'] : 10;
        $five = $part['part_five_result']['num'] < 10 ? $part['part_five_result']['num'] : 10;
        $six = $part['part_six_result']['num'] < 10 ? $part['part_six_result']['num'] : 10;
        //发牌
        $xian_card = array($part['part_one_result'], $part['part_three_result']);
        $zhuang_card = array($part['part_two_result'], $part['part_four_result']);
        $xian = ($one + $three) % 10;
        $zhuang = ($two + $four) % 10;
        $card_num = 4;
        $remark = '起手牌-闲：' . $xian . '，庄：' . $zhuang . '。';
        if ($xian > 7 || $zhuang > 7) {
            $remark .= '不需要补牌';
        } elseif ($xian == 6 || $xian == 7) {
            $remark .= '闲不需要补牌,';
            if ($zhuang < 7) {
                $card_num++;
                $remark .= '满足庄补第一张条件';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_five_result']);
                $zhuang = ($two + $four + $five) % 10;
            } else {
                $remark .= '庄不需要补牌';
            }
        } elseif ($xian < 6) {
            $remark .= '满足闲补第一张条件，庄为：' . $zhuang . '，且补牌为：' . $five . '，';
            $card_num++;
            $xian_card = array($part['part_one_result'], $part['part_three_result'], $part['part_five_result']);
            $xian = ($one + $three + $five) % 10;
            if ($zhuang < 3) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 3 && $five != 8) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 4 && $five != 10 && $five != 1 && $five != 8 && $five != 9) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 5 && $five != 10 && $five != 1 && $five != 2 && $five != 3 && $five != 8 && $five != 9) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } elseif ($zhuang == 6 && ($five == 6 || $five == 7)) {
                $card_num++;
                $remark .= '满足补第二张牌要求';
                $zhuang_card = array($part['part_two_result'], $part['part_four_result'], $part['part_six_result']);
                $zhuang = ($two + $four + $six) % 10;
            } else {
                $remark .= '不满足补第二张牌要求';
            }
        }
        //庄闲
        if ($zhuang > $xian) {
            $result = GameTypeModel::ZHUANG;
        } elseif ($zhuang < $xian) {
            $result = GameTypeModel::XIAN;
        } else {
            $result = GameTypeModel::PING;
        }
        //对子
        $tag1 = 0; //庄对标记
        $tag2 = 0; //闲对标记
        if ($zhuang_card[0]['num'] == $zhuang_card[1]['num']) {
            $tag1 = 1;
        }

        if ($xian_card[0]['num'] == $xian_card[1]['num']) {
            $tag2 = 1;
        }

        if ($tag1 == 1) {
            $result .= ',' . GameTypeModel::ZHUANGDUI;
        }
        if ($tag2 == 1) {
            $result .= ',' . GameTypeModel::XIANDUI;
        }
        if ($tag2 == 1 || $tag1 == 1) {
            $result .= ',' . GameTypeModel::RONDDUI;
        }
        if ($tag2 == 1 && $tag1 == 1) {
            $result .= ',' . GameTypeModel::DOUBLEDUI;
        }
        if ($card_num > 4) {
            $result .= ',' . GameTypeModel::DA;
        } else {
            $result .= ',' . GameTypeModel::XIAO;
        }

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['part_one_result'] = json_encode($part['part_one_result']);
        $part['part_two_result'] = json_encode($part['part_two_result']);
        $part['part_three_result'] = json_encode($part['part_three_result']);
        $part['part_four_result'] = json_encode($part['part_four_result']);
        $part['part_five_result'] = json_encode($part['part_five_result']);
        $part['part_six_result'] = json_encode($part['part_six_result']);
        $part['result'] = $result;
        $part['remark'] = $remark;
        $part['zhuang_card'] = json_encode($zhuang_card);
        $part['xian_card'] = json_encode($xian_card);
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    /**
     * 腾讯星座
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/18
     * @author: hui
     */
    public function tengxunXingzuo($result_id, $result_arr, $game_type_id)
    {
        $part = $this->tengxun_five_part($result_arr);

        $sum = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //结果计算
        //1:3
        $one_type = $this->longHuHe($part['part_one_result'], $part['part_three_result']);
        //2:3
        $two_type = $this->longHuHe($part['part_two_result'], $part['part_three_result']);
        //豹对
        $bao_dui = $this->baoDui($part['part_one_result'], $part['part_two_result'], $part['part_three_result']);
        //五行
        $wu_xing = $this->wuXing($sum);
        //四季
        $si_ji = $this->siJi($sum);
        //星座
        $xing_zuo = $this->xingZuo($sum);
        //生肖
        $sheng_xiao = $this->shengXiao($sum);
        //前二
        $first = $part['part_one_result'] . $part['part_two_result'];
        $second = $part['part_two_result'] . $part['part_three_result'];
        //大小
        if ($first > 49) {
            $first_size = GameTypeModel::BIG;
        } else {
            $first_size = GameTypeModel::SMALL;
        }
        if ($second > 49) {
            $second_size = GameTypeModel::BIG;
        } else {
            $second_size = GameTypeModel::SMALL;
        }
        //单双
        if ($first % 2 == 0) {
            $first_type = GameTypeModel::DOUBLE;
        } else {
            $first_type = GameTypeModel::SIGNLE;
        }
        if ($second % 2 == 0) {
            $second_type = GameTypeModel::DOUBLE;
        } else {
            $second_type = GameTypeModel::SIGNLE;
        }
        $type = array($sum, $one_type, $two_type, $bao_dui, $wu_xing, $si_ji, $xing_zuo, $sheng_xiao, $first_size, $first_type, $second_size, $second_type);
        $result = implode(',', $type);

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    /**
     * 腾讯16
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/18
     * @author: hui
     */
    public function tengxun16($result_id, $result_arr, $game_type_id)
    {
        $part = $this->tengxun_three_part_16($result_arr);

        $result = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];
        //结果计算

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;
        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    /**
     * 腾讯11
     * @param $result_id
     * @param $result_arr
     * @param $game_type_id
     * @date: 2019/4/18
     * @author: hui
     */
    public function tengxun11($result_id, $result_arr, $game_type_id)
    {
        $part = $this->tengxun_three_part_16($result_arr);

        $result = $part['part_one_result'] + $part['part_two_result'];
        //结果计算

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);

        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);
        //添加结果
        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $result;

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }


    //-------------比特币系列------------//

    public function btb28($result_id, $hash, $game_type_id)
    {
        //计算结果
        $sixteen_hash = substr($hash, 0, 16);
        $ten_hash = hexdec($sixteen_hash);
        $sub_result = $ten_hash / pow(2, 64);
        $sub_result = round($sub_result,12);
        $part['part_one_result'] = substr($sub_result, 2, 1);
        $part['part_two_result'] = substr($sub_result, 3, 1);
        $part['part_three_result'] = substr($sub_result, 4, 1);
        $result = $part['part_one_result'] . ',' . $part['part_two_result'] . ',' . $part['part_three_result'];

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);

        $last_result = '';
        //单双
        if ($result % 2) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //中边
        if ($result >= 10 && $result <= 17) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //大小
        if ($result >= 14) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //尾数大小
        if ($result % 10 >= 5) {
            $last_result .= '1,0,';
        } else {
            $last_result .= '0,1,';
        }
        //余数
        $last_result .= $result % 3 . ',';
        $last_result .= $result % 4 . ',';
        $last_result .= $result % 5;

        //添加结果
        $part['sixteen_hash'] = $sixteen_hash;
        $part['ten_hash'] = $ten_hash;
        $part['sub_result'] = $sub_result;

        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;
        $part['result'] = $part['part_one_result'] + $part['part_two_result'] + $part['part_three_result'];

        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $part['last_result'] = $last_result;

        $game_log_id = $this->addGameLog($part);
        $this->calculateReward($game_log_id);
    }

    public function btbSaiche($result_id, $hash, $game_type_id)
    {
        //计算结果
        $data = $ten_data = [];
        //十六进制
        for ($i = 1; $i <= 10; $i++) {
            $data[$i-1]['key'] = $i;
            $data[$i-1]['result'] = substr($hash, ($i - 1) * 6, 6);
        }
        //十进制
        foreach($data as $k => $v){
            $ten_data[$k]['key'] = $v['key'];
            $ten_data[$k]['result'] = hexdec($v['result']);
        }
        $part['ten_data'] = json_encode($ten_data);

        $data_result = array_column($ten_data,'result');
        array_multisort($data_result,SORT_DESC,$ten_data);

        $result = array_column($ten_data,'key');
        $part['result'] = $this->saiChe($result);

        $result = implode(',',$result);
        $game_result_obj = new GameResultModel();
        $game_result_obj->editGameResult('game_result_id =' . $result_id, ['result' => $result]);

        //计算中奖人数 投注金额
        $game_type_obj = new GameTypeModel();
        $people = $game_type_obj->calculatePeople($result_id, $game_type_id);
        $money = $game_type_obj->calculateMoney($result_id, $game_type_id);
        //添加结果
        $part['sixteen_data'] = json_encode($data);
        $part['result_data'] = json_encode($ten_data);

        $part['game_result_id'] = $result_id;
        $part['game_type_id'] = $game_type_id;

        $part = $game_type_obj->mixPeopleAndMoney($people,$money,$part);

        $game_log_id = $this->addGameLog($part);

        $this->calculateReward($game_log_id);
    }



    //-------------开奖算法------------//



    //赛车算法
    public function saiChe($result)
    {
        //冠亚和
        $gy_sum = $result[0] + $result[1];

        if ($gy_sum > 11) {
            $gy_daxiao = GameTypeModel::BIG;
        } else {
            $gy_daxiao = GameTypeModel::SMALL;
        }
        if ($gy_sum % 2) {
            $gy_danshaung = GameTypeModel::SIGNLE;
        } else {
            $gy_danshaung = GameTypeModel::DOUBLE;
        }
        $longhu_one = $this->longHuHe($result[0], $result[5]);
        $longhu_two = $this->longHuHe($result[1], $result[6]);
        $longhu_three = $this->longHuHe($result[2], $result[7]);
        $longhu_four = $this->longHuHe($result[3], $result[8]);
        $longhu_five = $this->longHuHe($result[4], $result[9]);
        $data = [$gy_sum, $gy_daxiao, $gy_danshaung, $longhu_one, $longhu_two, $longhu_three, $longhu_four, $longhu_five];
        $data = implode(',', $data);
        return $data;
    }

    //龙虎和
    public function longHuHe($one, $two)
    {
        if ($one > $two) {
            $type = GameTypeModel::LONG;
        } elseif ($one < $two) {
            $type = GameTypeModel::HU;
        } else {
            $type = GameTypeModel::HE;
        }
        return $type;
    }

    //豹对
    public function baoDui($one, $two, $three)
    {
        $sort_array = array($one, $two, $three);
        sort($sort_array, SORT_STRING);
        if ($sort_array[1] == $sort_array[0] && $sort_array[1] == $sort_array[2]) {
            //豹
            $type = GameTypeModel::BAO;
        } elseif (($sort_array[1] - $sort_array[0] == 1 && $sort_array[2] - $sort_array[1] == 1)
            || $sort_array == [0, 1, 9] || $sort_array == [0, 8, 9]) {
            //顺
            $type = GameTypeModel::SHUN;
        } elseif ($sort_array[1] == $sort_array[0] || $sort_array[1] == $sort_array[2]) {
            //对
            $type = GameTypeModel::DUI;
        } elseif (($sort_array[1] - $sort_array[0] == 1 || $sort_array[2] - $sort_array[1] == 1)
            || ($sort_array[0] == 0 && $sort_array[2] == 9)) {
            //半
            $type = GameTypeModel::BAN;
        } else {
            //杂
            $type = GameTypeModel::ZA;
        }
        return $type;
    }

    //五行
    public function wuXing($sum)
    {
        if ($sum == 0 || $sum == 5 || $sum == 10 || $sum == 15 || $sum == 20 || $sum == 25) {
            $type = GameTypeModel::JIN;
        } elseif ($sum == 1 || $sum == 6 || $sum == 11 || $sum == 16 || $sum == 21 || $sum == 26) {
            $type = GameTypeModel::MU;
        } elseif ($sum == 2 || $sum == 7 || $sum == 12 || $sum == 17 || $sum == 22 || $sum == 27) {
            $type = GameTypeModel::SHUI;
        } elseif ($sum == 3 || $sum == 8 || $sum == 13 || $sum == 18 || $sum == 23) {
            $type = GameTypeModel::HUO;
        } elseif ($sum == 4 || $sum == 9 || $sum == 14 || $sum == 19 || $sum == 24) {
            $type = GameTypeModel::TU;
        }
        return $type;
    }

    //四季
    public function siJi($sum)
    {
        if ($sum == 0 || $sum == 4 || $sum == 8 || $sum == 12 || $sum == 16 || $sum == 20 || $sum == 24) {
            $type = GameTypeModel::CHUN;
        } elseif ($sum == 1 || $sum == 5 || $sum == 9 || $sum == 13 || $sum == 17 || $sum == 21 || $sum == 25) {
            $type = GameTypeModel::XIA;
        } elseif ($sum == 2 || $sum == 6 || $sum == 10 || $sum == 14 || $sum == 18 || $sum == 22 || $sum == 26) {
            $type = GameTypeModel::QIU;
        } elseif ($sum == 3 || $sum == 7 || $sum == 11 || $sum == 15 || $sum == 19 || $sum == 23 || $sum == 27) {
            $type = GameTypeModel::DONG;
        }
        return $type;
    }

    //星座
    public function xingZuo($sum)
    {
        if ($sum == 0 || $sum == 12 || $sum == 24) {
            $type = GameTypeModel::SHUIPING;
        } elseif ($sum == 1 || $sum == 13 || $sum == 25) {
            $type = GameTypeModel::SHUANGYU;
        } elseif ($sum == 2 || $sum == 14 || $sum == 26) {
            $type = GameTypeModel::BAIYANG;
        } elseif ($sum == 3 || $sum == 15 || $sum == 27) {
            $type = GameTypeModel::JINGNIU;
        } elseif ($sum == 4 || $sum == 16) {
            $type = GameTypeModel::SHUANGZI;
        } elseif ($sum == 5 || $sum == 17) {
            $type = GameTypeModel::JUXIE;
        } elseif ($sum == 6 || $sum == 18) {
            $type = GameTypeModel::SHIZI;
        } elseif ($sum == 7 || $sum == 19) {
            $type = GameTypeModel::CHUNV;
        } elseif ($sum == 8 || $sum == 20) {
            $type = GameTypeModel::TIANCHENG;
        } elseif ($sum == 9 || $sum == 21) {
            $type = GameTypeModel::TIANXIE;
        } elseif ($sum == 10 || $sum == 22) {
            $type = GameTypeModel::SHESHOU;
        } elseif ($sum == 11 || $sum == 23) {
            $type = GameTypeModel::MOJIE;
        }
        return $type;
    }

    //生肖
    public function shengXiao($sum)
    {
        if ($sum == 0 || $sum == 1 || $sum == 26 || $sum == 27) {
            $type = GameTypeModel::SHU;
        } elseif ($sum == 2 || $sum == 3 || $sum == 25) {
            $type = GameTypeModel::NIU;
        } elseif ($sum == 4 || $sum == 23 || $sum == 24) {
            $type = GameTypeModel::HU1;
        } elseif ($sum == 5 || $sum == 22) {
            $type = GameTypeModel::TU1;
        } elseif ($sum == 6 || $sum == 21) {
            $type = GameTypeModel::LONG1;
        } elseif ($sum == 7 || $sum == 20) {
            $type = GameTypeModel::SHE;
        } elseif ($sum == 8 || $sum == 19) {
            $type = GameTypeModel::MA;
        } elseif ($sum == 9 || $sum == 18) {
            $type = GameTypeModel::YANG;
        } elseif ($sum == 10 || $sum == 17) {
            $type = GameTypeModel::HOU;
        } elseif ($sum == 11 || $sum == 16) {
            $type = GameTypeModel::JI;
        } elseif ($sum == 12 || $sum == 15) {
            $type = GameTypeModel::GOU;
        } elseif ($sum == 13 || $sum == 14) {
            $type = GameTypeModel::ZHU;
        }
        return $type;
    }




    /**
     * 获取抽水比例后重新计算赔率
     * @param $game_log_id
     * @param $bet_json
     * @date: 2019/4/3
     * @return mixed
     * @author: hui
     */
    public function randDeduct($game_log_id, $bet_json)
    {
        $max_deduct_rate = $GLOBALS['config_info']['MAX_DEDUCT_RATE'];
        $min_deduct_rate = $GLOBALS['config_info']['MIN_DEDUCT_RATE'];
        //每一个类型的抽水比例可单独设置
        $game_type_obj = new GameTypeModel();
        $game_type_id = $this->getGameLogField('game_log_id =' . $game_log_id, 'game_type_id');
        $type_max_deduct = $game_type_obj->getGameTypeField('game_type_id =' . $game_type_id, 'max_deduct');
        $type_min_deduct = $game_type_obj->getGameTypeField('game_type_id =' . $game_type_id, 'min_deduct');
        if (floatval($type_max_deduct) && floatval($type_min_deduct)) {
            $max_deduct_rate = $type_max_deduct;
            $min_deduct_rate = $type_min_deduct;
        }
        foreach ($bet_json as $k => &$v) {
            foreach ($v['bet_json'] as $ki => &$vi) {
                $deduct = rand($min_deduct_rate * 100, $max_deduct_rate * 100);
                $deduct_rate = $deduct / 100;
                $vi['rate'] = $vi['rate'] * (1 - $deduct_rate / 100);
            }
            unset($vi);
        }
        unset($v);

        $this->editGameLog('game_log_id =' . $game_log_id, ['bet_json' => json_encode($bet_json)]);
        return $bet_json;
    }

    /**
     * 计算发放奖励基础方法
     * @param $game_log_id
     * @date: 2019/4/3
     * @author: hui
     */
    public function calculateReward($game_log_id)
    {
        $OPEN_TYPE = intval(OPEN_TYPE) ? OPEN_TYPE : 0;
        if($OPEN_TYPE == 1)//为1时不重复结算奖励，只修改开奖结果
        {
            return false;
        }
        $game_log_info = $this->getGameLogInfo('game_log_id = ' . $game_log_id);
        $game_type_obj = new GameTypeModel();
        $bet_json = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'bet_json');
        $bet_json = json_decode($bet_json, true);

        $is_fixation_rate = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'is_fixation_rate');
        switch ($game_log_info['game_type_id']) {
            case GameTypeModel::DANDAN28:
            case GameTypeModel::DANDAN36:
            case GameTypeModel::DANDAN28GUDING:
            case GameTypeModel::DANDAN16:
            case GameTypeModel::DANDAN11:
            case GameTypeModel::BEIJING28:
            case GameTypeModel::BEIJING11:
            case GameTypeModel::BEIJING16:
            case GameTypeModel::BEIJING36:
            case GameTypeModel::BEIJING28GUDING:
            case GameTypeModel::PK10:
            case GameTypeModel::PK22:
            case GameTypeModel::PKGUANJUN:
            case GameTypeModel::PKLONGHU:
            case GameTypeModel::PKGUANYAJUN:
            case GameTypeModel::JIANADA11:
            case GameTypeModel::JIANADA16:
            case GameTypeModel::JIANADA28:
            case GameTypeModel::JIANADA36:
            case GameTypeModel::JIANADA28GUDING:
            case GameTypeModel::HANGUO28:
            case GameTypeModel::HANGUO16:
            case GameTypeModel::HANGUO36:
            case GameTypeModel::HANGUO10:
            case GameTypeModel::TENGXUN28:
            case GameTypeModel::TENGXUN16:
            case GameTypeModel::TENGXUN11:
            case GameTypeModel::HANGUO28GUDING:
            case GameTypeModel::BTBONE28:
            case GameTypeModel::BTBTWO28:
            case GameTypeModel::BTBTHREE28:
            case GameTypeModel::FEITING10:
            case GameTypeModel::FEITING22:
            case GameTypeModel::FEITINGGUANJUN:
            case GameTypeModel::FEITINGFEIHU:
            case GameTypeModel::FEITINGYAJUN:
                $this->dandan28Reward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::DANDANWAIWEI:
            case GameTypeModel::JIANADAWAIWEI:
            case GameTypeModel::HANGUOWAIWEI:
                $this->dandanwaiweiReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::DANDANDINGWEI:
            case GameTypeModel::JIANADADINGWEI:
            case GameTypeModel::HANGUODINGWEI:
                $this->dandandingweiReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::DANDANBAIJIALE:
            case GameTypeModel::JIANADABAIJIALE:
                $this->dandanbaijialeReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::DANDANBAIJIALENEW:
            case GameTypeModel::JIANADABAIJIALENEW:
            case GameTypeModel::TENGXUNBAIJIALE:
                $this->dandanBaijialeNewReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::DANDANXINGZUO:
            case GameTypeModel::JIANADAXINGZUO:
            case GameTypeModel::TENGXUNXINGZUO:
                $this->dandanXinzuoReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::CQSSC:
                $this->cqsscReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::TENGXUNFFC:
                $this->txffcReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::BTBONESAICHE:
            case GameTypeModel::BTBTWOSAICHE:
            case GameTypeModel::BTBTHREESAICHE:
            case GameTypeModel::FEITINGSAICHE:
            case GameTypeModel::BEIJINGSAICHE:
                $this->btbSaicheReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::JIANADAQUN:
            case GameTypeModel::DANDANQUN:
            case GameTypeModel::BEIJINGQUN:
            case GameTypeModel::HANGUOQUN:
                $this->qunweiReward($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::TEMA:
                $this->tema($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::LIANGMIAN:
                $this->liangmian($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::SEBO:
                $this->sebo($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::TEXIAO:
                $this->texiao($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::HEXIAO:
                $this->hexiao($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::TEMATOUWEISHU:
                $this->temaweishu($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::ZHENGMA:
                $this->zhengma($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::ZHENGMATE:
                $this->zhengmate($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::ZHENGMA16:
                $this->zhengma16($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::WUXING:
                $this->liuhewuxing($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::PINGTEYIXIAOWEI:
                $this->pingteyixiaowei($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::ZHENGXIAO:
                $this->zhengxiao($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::SEBO7:
                $this->sebo7($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::ZONGXIAO:
                $this->zongxiao($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::ZIXUANBUZHONG:
                $this->zixuanbuzhong($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::LIANXIAOLIANWEI:
                $this->lianxiaolianwei($game_log_info, $bet_json, $is_fixation_rate);
                break;
            case GameTypeModel::LIANMA:
                $this->lianma($game_log_info, $bet_json, $is_fixation_rate);
                break;
        }
        $bet_auto_obj = new BetAutoModel();
        $bet_auto_obj->gameAutoBet($game_log_info['game_type_id']);
    }


    /**
     * 蛋蛋28奖励
     * @param $game_log_info
     * @param $bet_json
     * @param $is_fixation_rate
     * @date: 2019/4/3
     * @author: hui
     */
    public function dandan28Reward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);
        if ($game_log_info['game_type_id'] == GameTypeModel::JIANADA28) {
            log_file('SQL:'.$bet_log_obj->getLastSql(),'jianada28',true);
            log_file('data:'.json_encode($bet_log_list),'jianada28',true);
        }

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');
        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];
            //循环投注
            foreach ($new_json as $ki => &$vi) {//投注json   vi

                foreach ($bet_json as $kk => $vv) { //赔率json vv

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {
                                if ($vii['key'] == $game_log_info['result'] && $vvi['key'] == $game_log_info['result']) {
                                    $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                    $after_money += $vii['win'];
                                }
                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);


            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                //发放奖励
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }

            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }


        }
        unset($v);
    }


    /**
     * 蛋蛋外围奖励
     * @param $game_log_info
     * @param $bet_json
     * @param $is_fixation_rate
     * @date: 2019/4/8
     * @author: yzp
     */
    public function dandanwaiweiReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');
        if ($game_log_info['game_type_id'] == GameTypeModel::JIANADAWAIWEI) {
            log_file('SQL:'.$bet_log_obj->getLastSql(),'jianadawaiwei',true);
            log_file('data:'.json_encode($bet_log_list),'jianadawaiwei',true);
        }
        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];
            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {

                                if ($vi['part'] == 1)//大小单双
                                {
//                                    if (in_array($vii['key'], array(1, 2, 3, 4)) && $vvi['key'] == $vii['key'] && in_array($game_log_info['result'], array(13, 14)))//13,14时为回本
//                                    {
//                                        $vii['win'] = intval($vii['money']);
//                                        $after_money += $vii['win'];
//                                    } else
                                    if ($vii['key'] == 1 && $vvi['key'] == 1)//小
                                    {
                                        if ($game_log_info['result'] < 13 && $game_log_info['result'] >= 0) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 13) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] == 2 && $vvi['key'] == 2) //大
                                    {
                                        if ($game_log_info['result'] > 14 && $game_log_info['result'] < 28) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 14) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] == 3 && $vvi['key'] == 3) //单
                                    {
//                                        log_file('11111','waiwei',true);
//                                        log_file($game_log_info['result'] % 2,'waiwei',true);
//                                        log_file($game_log_info['result'],'waiwei',true);
                                        if ($game_log_info['result'] % 2 && $game_log_info['result'] != 13) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                            log_file($vii['win'],'waiwei',true);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 13) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }


                                    } elseif ($vii['key'] == 4 && $vvi['key'] == 4) //双
                                    {
                                        if (!($game_log_info['result'] % 2) && $game_log_info['result'] != 14) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 14) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }


                                    } elseif ($vii['key'] == 5 && $vvi['key'] == 5) //极大
                                    {
                                        if ($game_log_info['result'] > 21 && $game_log_info['result'] < 28) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 6 && $vvi['key'] == 6) //极小
                                    {
                                        if ($game_log_info['result'] >= 0 && $game_log_info['result'] <= 5) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 7 && $vvi['key'] == 7) //小单
                                    {
                                        if (in_array($game_log_info['result'], array(1, 3, 5, 7, 9, 11))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 13) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 8 && $vvi['key'] == 8) //小双
                                    {
                                        if (in_array($game_log_info['result'], array(0, 2, 4, 6, 8, 10, 12))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 9 && $vvi['key'] == 9) //大单
                                    {
                                        if (in_array($game_log_info['result'], array(15, 17, 19, 21, 23, 25, 27))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 10 && $vvi['key'] == 10) //大双
                                    {
                                        if (in_array($game_log_info['result'], array(16, 18, 20, 22, 24, 26))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 14) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                } elseif ($vi['part'] == 2) {
                                    if ($vii['key'] == 1 && $vvi['key'] == 1) //龙
                                    {
                                        if (in_array($game_log_info['result'], array(0, 3, 6, 9, 12, 15, 18, 21, 24, 27))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 2 && $vvi['key'] == 2) //虎
                                    {
                                        if (in_array($game_log_info['result'], array(1, 4, 7, 10, 13, 16, 19, 22, 25))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 3 && $vvi['key'] == 3) //豹
                                    {
                                        if (in_array($game_log_info['result'], array(2, 5, 8, 11, 14, 17, 20, 23, 26))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                }
                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    /**
     * 群玩法外围奖励
     * @param $game_log_info
     * @param $bet_json
     * @param $is_fixation_rate
     * @date: 2019/4/8
     * @author: yzp
     */
    public function qunweiReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');
        //判断是不是包子顺或者对
        $sort_array = [$game_log_info['part_one_result'],$game_log_info['part_two_result'],$game_log_info['part_three_result']];
        sort($sort_array, SORT_STRING);
        $type = 0;
        if ($sort_array[1] == $sort_array[0] && $sort_array[1] == $sort_array[2]) {
            //豹
            $type = GameTypeModel::BAO;
        } elseif (($sort_array[1] - $sort_array[0] == 1 && $sort_array[2] - $sort_array[1] == 1)
            || $sort_array == [0, 1, 9] || $sort_array == [0, 8, 9]) {
            //顺
            $type = GameTypeModel::SHUN;
        } elseif ($sort_array[1] == $sort_array[0] || $sort_array[1] == $sort_array[2]) {
            //对
            $type = GameTypeModel::DUI;
        }


        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {
                                    if ($vii['key'] == 1 && $vvi['key'] == 1)//小
                                    {
                                        if ($game_log_info['result'] < 13 && $game_log_info['result'] >= 0) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 13) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] == 2 && $vvi['key'] == 2) //大
                                    {
                                        if ($game_log_info['result'] > 14 && $game_log_info['result'] < 28) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 14) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] == 3 && $vvi['key'] == 3) //单
                                    {
//                                        log_file('11111','waiwei',true);
//                                        log_file($game_log_info['result'] % 2,'waiwei',true);
//                                        log_file($game_log_info['result'],'waiwei',true);
                                        if ($game_log_info['result'] % 2 && $game_log_info['result'] != 13) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                            log_file($vii['win'],'waiwei',true);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 13) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }


                                    } elseif ($vii['key'] == 4 && $vvi['key'] == 4) //双
                                    {
                                        if (!($game_log_info['result'] % 2) && $game_log_info['result'] != 14) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 14) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }


                                    }  elseif ($vii['key'] == 5 && $vvi['key'] == 5) //小单
                                    {
                                        if (in_array($game_log_info['result'], array(1, 3, 5, 7, 9, 11))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 13) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 6 && $vvi['key'] == 6) //小双
                                    {
                                        if (in_array($game_log_info['result'], array(0, 2, 4, 6, 8, 10, 12))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 7 && $vvi['key'] == 7) //大单
                                    {
                                        if (in_array($game_log_info['result'], array(15, 17, 19, 21, 23, 25, 27))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == 8 && $vvi['key'] == 8) //大双
                                    {
                                        if (in_array($game_log_info['result'], array(16, 18, 20, 22, 24, 26))) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 14) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }

            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }


    /**
     * 蛋蛋定位奖励
     * @param $game_log_info
     * @param $bet_json
     * @param $is_fixation_rate
     * @date: 2019/4/8
     * @author: yzp
     */
    public function dandandingweiReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将18,1,2,81,2,1,3格式转换为数组

        //获取开奖的三个数字
        $first = $game_log_info['part_one_result'];
        $second = $game_log_info['part_two_result'];
        $third = $game_log_info['part_three_result'];

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];
            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {

                                if ($vi['part'] == 1) //龙虎和
                                {
                                    if ($vii['key'] == $result['6'] && $vvi['key'] == $result['6']) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                } elseif ($vi['part'] == 2)//前两和
                                {
                                    if ($vii['key'] == 1 && $vvi['key'] == 1)  //大
                                    {
                                        if ($result[1] == GameTypeModel::BIG) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == 2 && $vvi['key'] == 2)  //小
                                    {
                                        if ($result[1] == GameTypeModel::SMALL) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == 3 && $vvi['key'] == 3)  //单
                                    {
                                        if ($result[2] == GameTypeModel::SIGNLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == 4 && $vvi['key'] == 4)  //双
                                    {
                                        if ($result[2] == GameTypeModel::DOUBLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                } elseif ($vi['part'] == 3)//后两和
                                {
                                    if ($vii['key'] == 1 && $vvi['key'] == 1)  //大
                                    {
                                        if ($result[4] == GameTypeModel::BIG ) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == 2 && $vvi['key'] == 2)  //小
                                    {
                                        if ($result[4] == GameTypeModel::SMALL) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == 3 && $vvi['key'] == 3)  //单
                                    {
                                        if ($result[5] == GameTypeModel::SIGNLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == 4 && $vvi['key'] == 4)  //双
                                    {
                                        if ($result[5] == GameTypeModel::DOUBLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                } elseif (in_array($vi['part'], array(4, 5, 6))) {
                                    if ($vi['part'] == 4) {
                                        $index = $first;
                                    } elseif ($vi['part'] == 5) {
                                        $index = $second;
                                    } elseif ($vi['part'] == 6) {
                                        $index = $third;
                                    }

                                    if ($vii['key'] == 5 + $index && $vvi['key'] == 5 + $index) //号码一二三
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 1 && $vvi['key'] == 1 && $index > 4) //号码一二三 ,大
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 2 && $vvi['key'] == 2 && $index <= 4) //号码一二三 ,小
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 3 && $vvi['key'] == 3 && $index % 2) //号码一二三 ,单
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 4 && $vvi['key'] == 4 && !($index % 2)) //号码一二三 ,双
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }

                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }

    /**
     * 蛋蛋百家乐奖励
     * @param $game_log_info
     * @param $bet_json
     * @param $is_fixation_rate
     * @date: 2019/4/8
     * @author: yzp
     */
    public function dandanbaijialeReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将1,2格式转换为数组

        //获取一号、二号路子
        $first = $result['0'];
        $second = $result['1'];

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];
            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {
                                if ($vi['part'] == 1) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) //球一闲
                                    {
                                        if ($first == GameTypeModel::XIAN) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) //球一庄
                                    {
                                        if ($first == GameTypeModel::ZHUANG) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) //球一和
                                    {
                                        if ($first == GameTypeModel::PING) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) //球二闲
                                    {
                                        if ($second == GameTypeModel::XIAN) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] == $vvi['key'] && $vvi['key'] == 5) //球二庄
                                    {
                                        if ($second == GameTypeModel::ZHUANG) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($vii['key'] == $vvi['key'] && $vvi['key'] == 6) //球二和
                                    {
                                        if ($second == GameTypeModel::PING) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }

                                    }
                                }

                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }

            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }


    /**
     * 蛋蛋百家乐算法
     * @param $game_log_info
     * @param $bet_json
     * @param int $is_fixation_rate
     * @date: 2019/4/9
     * @author: hui
     */
    public function dandanBaijialeNewReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将1,2格式转换为数组

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {
                                foreach ($result as $key => $value) {
                                    if ($vii['key'] == $value && $vvi['key'] == $value) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }


    /**
     * 蛋蛋星座算法
     * @param $game_log_info
     * @param $bet_json
     * @param int $is_fixation_rate
     * @date: 2019/4/9
     * @author: hui
     */
    public function dandanXinzuoReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将1,2格式转换为数组

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {
                                //大小
                                if ($vi['part'] == 1) {
                                    //大小
                                    if ($result[0] > 13) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($result[0] <= 13) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    //单双
                                    if ($result[0] % 2) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (!($result[0] % 2)) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                }
                                //大小极值
                                if ($vi['part'] == 2) {

                                    if ($result[0] > 21) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($result[0] < 6) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }


                                    if (in_array($result[0], array(1, 3, 5, 7, 9, 11, 13))) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (in_array($result[0], array(0, 2, 4, 6, 8, 10, 12))) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (in_array($result[0], array(15, 17, 19, 21, 23, 25, 27))) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 5) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (in_array($result[0], array(14, 16, 18, 20, 22, 24, 26))) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 6) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }


                                }
                                //豹对
                                if ($vi['part'] == 3) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[3]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //五行
                                if ($vi['part'] == 4) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[4]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //四季
                                if ($vi['part'] == 5) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[5]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //星座
                                if ($vi['part'] == 6) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[6]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //生肖
                                if ($vi['part'] == 7) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[7]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //1:3
                                if ($vi['part'] == 8) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[1]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //2:3
                                if ($vi['part'] == 9) {
                                    //2:3
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[2]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                //前二
                                if ($vi['part'] == 10) {
                                    //大小
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                        if ($result[8] == GameTypeModel::BIG) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                        if ($result[8] == GameTypeModel::SMALL) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    //单双
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                        if ($result[9] == GameTypeModel::SIGNLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                        if ($result[9] == GameTypeModel::DOUBLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                }
                                //后二
                                if ($vi['part'] == 11) {
                                    //大小
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                        if ($result[10] == GameTypeModel::BIG) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                        if ($result[10] == GameTypeModel::SMALL) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    //单双
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                        if ($result[11] == GameTypeModel::SIGNLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                        if ($result[11] == GameTypeModel::DOUBLE) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                }
                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }


    /**
     * 重庆时时彩
     * @param $game_log_info
     * @param $bet_json
     * @param int $is_fixation_rate
     * @date: 2019/4/9
     * @author: hui
     */
    public function cqsscReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将1,2格式转换为数组

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {
                                //大小
                                if ($vi['part'] == 1) {
                                    //大小
                                    if ($result[1] == GameTypeModel::BIG) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($result[1] == GameTypeModel::SMALL) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    //单双
                                    if ($result[2] == GameTypeModel::SIGNLE) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($result[2] == GameTypeModel::DOUBLE) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    //龙虎和
                                    if ($result[3] == GameTypeModel::LONG) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 5) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($result[3] == GameTypeModel::HU) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 6) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($result[3] == GameTypeModel::HE) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 7) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                }
                                //特码球
                                if ($vi['part'] == 2 || $vi['part'] == 3 || $vi['part'] == 4 || $vi['part'] == 5 || $vi['part'] == 6) {
                                    if ($vi['part'] == 2) {
                                        $ball_num = $game_log_info['part_one_result'];
                                    } elseif ($vi['part'] == 3) {
                                        $ball_num = $game_log_info['part_two_result'];
                                    } elseif ($vi['part'] == 4) {
                                        $ball_num = $game_log_info['part_three_result'];
                                    } elseif ($vi['part'] == 5) {
                                        $ball_num = $game_log_info['part_four_result'];
                                    } elseif ($vi['part'] == 6) {
                                        $ball_num = $game_log_info['part_five_result'];
                                    }
                                    //大小
                                    if ($ball_num >= 5) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($ball_num <= 4) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    //单双
                                    if ($ball_num % 2) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (!($ball_num % 2)) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $ball_num + 5) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }

                                }
                                //前三、中三、后三
                                if ($vi['part'] == 7) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[4]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                if ($vi['part'] == 8) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[5]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                                if ($vi['part'] == 9) {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[6]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }
                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }

    /**
     * 腾讯分分彩奖励
     * @param $game_log_info
     * @param $bet_json
     * @param int $is_fixation_rate
     * @date: 2019/4/19
     * @author: hui
     */
    public function txffcReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将18,1,2,81,2,1,3格式转换为数组

        //获取开奖的三个数字
        $first = $game_log_info['part_one_result'];
        $second = $game_log_info['part_two_result'];
        $third = $game_log_info['part_three_result'];
        $fourth = $game_log_info['part_four_result'];
        $fifth = $game_log_info['part_three_result'];

        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {

                                if (in_array($vi['part'], array(1, 2, 3))) //前中后三  豹对
                                {
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $result[$vi['part'] - 1]) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                } elseif (in_array($vi['part'], array(4, 5, 6, 7, 8))) {
                                    if ($vi['part'] == 4) {
                                        $index = $first;
                                    } elseif ($vi['part'] == 5) {
                                        $index = $second;
                                    } elseif ($vi['part'] == 6) {
                                        $index = $third;
                                    } elseif ($vi['part'] == 7) {
                                        $index = $fourth;
                                    } elseif ($vi['part'] == 8) {
                                        $index = $fifth;
                                    }

                                    if ($vii['key'] == 5 + $index && $vvi['key'] == 5 + $index) //号码一二三
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 1 && $vvi['key'] == 1 && $index > 4) //号码一二三 ,大
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 2 && $vvi['key'] == 2 && $index <= 4) //号码一二三 ,小
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 3 && $vvi['key'] == 3 && $index % 2) //号码一二三 ,单
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    } elseif ($vii['key'] == 4 && $vvi['key'] == 4 && !($index % 2)) //号码一二三 ,双
                                    {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }

                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }


    /**
     * 赛车开奖算法
     * @param $game_log_info
     * @param $bet_json
     * @param int $is_fixation_rate
     */
    public function btbSaicheReward($game_log_info, $bet_json, $is_fixation_rate = 0)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        if (!$is_fixation_rate) {
//            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }
        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);

        $result = explode(',', $game_log_info['result']) ?: array(); //将12,1,2,2,1,2,1,2格式转换为数组
        //开奖号
        $game_result = $game_result_obj->getGameResultField('game_result_id ='.$game_log_info['game_result_id'],'result');
        $game_result = explode(',', $game_result);
        $game_type_obj = new GameTypeModel();
        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //循环投注
            foreach ($new_json as $ki => &$vi) {
                foreach ($bet_json as $kk => $vv) {

                    if ($vv['part'] == $vi['part']) {
                        foreach ($vi['bet_json'] as $kii => &$vii) {
                            $vii['win'] = 0;
                            foreach ($vv['bet_json'] as $kki => $vvi) {
                                if (in_array($vi['part'], array(1))) //冠亚军
                                {
                                    if($result[1] == GameTypeModel::SMALL){
                                        if($vii['key'] == $vvi['key'] && $vvi['key'] == 2){
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }elseif($result[1] == GameTypeModel::BIG){
                                        if($vii['key'] == $vvi['key'] && $vvi['key'] == 1){
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    if($result[2] == GameTypeModel::SIGNLE){
                                        if($vii['key'] == $vvi['key'] && $vvi['key'] == 3){
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }elseif($result[2] == GameTypeModel::DOUBLE){
                                        if($vii['key'] == $vvi['key'] && $vvi['key'] == 4){
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                } elseif (in_array($vi['part'], array(2, 3, 4, 5, 6))) {
                                    $ball_num = $game_result[$vi['part'] - 2];
                                    //大小
                                    if ($ball_num > 5) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($ball_num <= 5) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    //单双
                                    if ($ball_num % 2) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (!($ball_num % 2)) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    //龙虎
                                    if($result[$vi['part'] + 1] == GameTypeModel::LONG){
                                        if($vii['key'] == $vvi['key'] && $vvi['key'] == 5){
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        }
                                    }elseif($result[$vi['part'] + 1 ] == GameTypeModel::HU){
                                        if($vii['key'] == $vvi['key'] && $vvi['key'] == 6){
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        }
                                    }

                                    //球号
                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $ball_num + 6) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }elseif (in_array($vi['part'], array(7, 8, 9, 10, 11))) {

                                    $ball_num = $game_result[$vi['part'] - 2];

                                    //大小
                                    if ($ball_num > 5) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 1) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif ($ball_num <= 5) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }
                                    //单双
                                    if ($ball_num % 2) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 3) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    } elseif (!($ball_num % 2)) {
                                        if ($vii['key'] == $vvi['key'] && $vvi['key'] == 4) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }
                                    }

                                    if ($vii['key'] == $vvi['key'] && $vvi['key'] == $ball_num + 4) {
                                        $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                        $after_money += $vii['win'];
                                    }
                                }

                            }
                        }
                        unset($vii);
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) //赢
            {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }
        }
        unset($v);
    }



//-----------------------杀模式-----------------------//
    //韩国28杀模式
    public function hanguo28Kill($game_result_id)
    {
        //获取赔率
        $game_result_obj = new GameResultModel();
//        $game_result_info = $game_result_obj->getGameResultInfo(['game_result_id'=>$game_result_id]);
//        $game_type_id = $game_result_info['game_type_id'];
        $game_type_obj = new GameTypeModel();
        $game_type_list = $game_type_obj->where(['game_series_id'=>5])->select();
        $arr = [];
        foreach ($game_type_list as $value) {
            if ($value['is_kill'] == 0) continue;
            $arr = $this->hanguo36Kill($game_result_id,$value['game_type_id'],$arr);
        }
        echo json_encode($arr);
//        echo json_encode($arr[array_rand($arr,1)]);
        return $arr?$arr[array_rand($arr,1)]:[];
//        $game_type_info = $game_type_obj->getGameTypeInfo(['game_type_id'=>$game_type_id]);
//        $game_bet_money_obj = new GameBetMoneyModel();
//        $game_bet_money_list = $game_bet_money_obj->getList(['game_result_id'=>$game_result_id],'*');
//        foreach ($game_bet_money_list as $value) {
//
////        echo $game_type_info['bet_json'];
//            $rate_info = array_column(json_decode($game_type_info['bet_json'],true)[0]['bet_json'],'rate','key');
////        echo json_encode($rate_info);
//            unset($game_bet_money_info['id']);
//            unset($game_bet_money_info['game_type_id']);
//            unset($game_bet_money_info['game_result_id']);
//            $all_money = 0;
//            $game['key'] = 0;
//            $game['rate'] = $game_type_info['rate'];
//            foreach ($game_bet_money_info as $value) {
//                $all_money += $value;
//            }
////        log_file('kill_money:'.$all_money,'kill');
//            foreach ($game_bet_money_info as $key => $value) {
//                echo $rate_info[$key].'---';
//                $real_rate =  abs($value*$rate_info[$key]/$all_money*100 - $game_type_info['rate']);
//                if ($real_rate<$game['rate']) {
//                    $game['key'] = $key;//实际数值
//                    $game['rate'] = $real_rate;//实际kill
////                log_file('$key:'.$key,'kill');
////                log_file('$real_rate:'.$real_rate,'kill');
//                }
//            }
//            echo $game['key'].'---'.$game['rate'];
//            if ($game['key']==0 && $game['rate'] == 0) {
//                return false;
//            }
//            $hg_result_obj = new HgResultModel();
////        log_file('result:'.$game['key'],'kill');
//            $hg_result_list = $hg_result_obj->where(['all_number'=>$game['key']])->select();
////        log_file('array_key:'.array_rand($hg_result_list,1),'kill');
////        log_file('array_key:'.$hg_result_obj->getLastSql(),'kill');
//            $arr = explode(',',$hg_result_list[array_rand($hg_result_list,1)]['result']);
//        }

    }

    public function hanguo36Kill($game_result_id,$game_type_id,$arr)
    {
        //获取赔率
        $field = '';
        //获取之前有没有杀过，有的话就是第二次杀
        switch ($game_type_id) {
            case GameTypeModel::HANGUO28 :
                $field = '*';
                break;
            case GameTypeModel::HANGUO16 :
                $field = 'id,game_type_id,game_result_id,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18';
                break;
            case GameTypeModel::HANGUO36 :
                $field = 'id,game_type_id,game_result_id,1,2,3,4,5';
                break;
            case GameTypeModel::HANGUO10 :
                $field = 'id,game_type_id,game_result_id,1,2,3,4,5,6,7,8,9,10';
                break;
            case GameTypeModel::HANGUO28GUDING :
                $field = '*';
                break;

        }

        $game_bet_money_obj = new GameBetMoneyModel();
        $game_bet_money_info = $game_bet_money_obj->getInfo(['game_result_id'=>$game_result_id,'game_type_id'=> $game_type_id],$field);
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo(['game_type_id'=>$game_type_id]);
        $rate_info = array_column(json_decode($game_type_info['bet_json'],true)[0]['bet_json'],'rate','key');
        unset($game_bet_money_info['id']);
        unset($game_bet_money_info['game_type_id']);
        unset($game_bet_money_info['game_result_id']);
        $all_money = 0;
        $game['key'] = 0;
        $game['rate'] = $game_type_info['kill_rate'];
        //获取为0的数据
        $zero_arr=[];
        foreach ($game_bet_money_info as $key => $value) {
            $all_money += $value*$rate_info[$key];
            if ($value == 0) {
                $zero_arr[] = $key;
            }
        }
        $int = 0;
//        log_file('kill_money:'.$all_money,'kill');
        foreach ($game_bet_money_info as $key => $value) {
            $int+=$value*$rate_info[$key];
            if ($zero_arr) {
                $game['key'] = $zero_arr[array_rand($zero_arr,1)];//实际数值
                $game['rate'] = 100;//实际kill
                break;
            }
//            echo $key.'===';
//            echo $rate_info[$key].'---';
            $real_rate =  abs($value*$rate_info[$key]/$all_money*100 - $game_type_info['kill_rate']);
//            if ()
//            echo $value.'----'.$rate_info[$key].'-----'.$real_rate.'</br>';
            if ($real_rate<$game['rate']) {
                $game['key'] = $key;//实际数值
                $game['rate'] = $real_rate;//实际kill
            }
        }
//        echo $game['key'].'---'.$game['rate'].'---'.$game_type_info['kill_rate'];
        if ($game['key']==0 && $game['rate'] == $game_type_info['kill_rate']) {
//            echo 232323;
            return [];
        }
//        print_r(array_intersect(['2'=>1],['2'=>1]));
//        echo 'jajaja';
//        echo json_encode($arr);
        $hg_result_obj = new HgResultModel();

        if (!$arr) {
            $hg_result_list = $hg_result_obj->where(['all_number'=>$game['key'],'game_type_id'=>$game_type_id])->select();
            echo $hg_result_obj->getLastSql();
            return array_column($hg_result_list,'result');
//            $arr = explode(',',$hg_result_list[array_rand($hg_result_list,1)]['result']);
        } else {
            $hg_result_list = $hg_result_obj->where(['all_number'=>$game['key'],'game_type_id'=>$game_type_id])->select();
            echo $hg_result_obj->getLastSql();
            if ($hg_result_list) {
                $in_arr = array_intersect(array_column($hg_result_list,'result'),$arr);
                if ($in_arr) {
                    return $in_arr;
                }
                return $arr;
            } else {
                return $arr;
            }
        }
    }
    public function hanguo10Kill($game_result_id,$game_type_id)
    {
        //获取赔率
        //获取之前有没有杀过，有的话就是第二次杀

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $hg_key = $redis_obj->get('hg_key');

        $game_bet_money_obj = new GameBetMoneyModel();
        $filed = 'id,game_type_id,game_result_id,1,2,3,4,5,6,7,8,9,10';
        $game_bet_money_info = $game_bet_money_obj->getInfo(['game_result_id'=>$game_result_id],$filed);
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo(['game_type_id'=>$game_type_id]);
        $rate_info = array_column(json_decode($game_type_info['bet_json'],true)[0]['bet_json'],'rate','key');
        unset($game_bet_money_info['id']);
        unset($game_bet_money_info['game_type_id']);
        unset($game_bet_money_info['game_result_id']);
        $all_money = 0;
        $game['key'] = 0;
        $game['rate'] = $game_type_info['rate'];
        foreach ($game_bet_money_info as $value) {
            $all_money += $value;
        }
//        log_file('kill_money:'.$all_money,'kill');
        foreach ($game_bet_money_info as $key => $value) {
            echo $rate_info[$key].'---';
            $real_rate =  abs($value*$rate_info[$key]/$all_money*100 - $game_type_info['rate']);
            if ($real_rate<$game['rate']) {
                $game['key'] = $key;//实际数值
                $game['rate'] = $real_rate;//实际kill
//                log_file('$key:'.$key,'kill');
//                log_file('$real_rate:'.$real_rate,'kill');
            }
        }
        echo $game['key'].'---'.$game['rate'];
        if ($game['key']==0 && $game['rate'] == 0) {
            return false;
        }
        $hg_result_obj = new HgResultModel();
        $hg_result_list = $hg_result_obj->where(['all_number'=>$game['key']])->select();
        if (!$hg_key) {
            $arr = explode(',',$hg_result_list[array_rand($hg_result_list,1)]['result']);
        } else {
            $arr = array_intersect(json_decode($hg_result_list,true),$hg_result_list);
            if ($arr) {
                $arr = array_rand($arr);
            } else {
                return false;
            }
        }
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $redis_obj->set('hg_key',json_encode($arr));
        $redis_obj->expire('hg_key','20');
        $redis_obj->set('hg_key_result_id',$game_result_id);
        $redis_obj->expire('hg_key_result_id',20);
        return $arr;
    }

    public function hanguo16Kill($game_result_id,$game_type_id)
    {
        //获取赔率
        //获取之前有没有杀过，有的话就是第二次杀

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $hg_key = $redis_obj->get('hg_key');

        $game_bet_money_obj = new GameBetMoneyModel();
        $filed = 'id,game_type_id,game_result_id,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18';
        $game_bet_money_info = $game_bet_money_obj->getInfo(['game_result_id'=>$game_result_id],$filed);
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo(['game_type_id'=>$game_type_id]);
        $rate_info = array_column(json_decode($game_type_info['bet_json'],true)[0]['bet_json'],'rate','key');
        unset($game_bet_money_info['id']);
        unset($game_bet_money_info['game_type_id']);
        unset($game_bet_money_info['game_result_id']);
        $all_money = 0;
        $game['key'] = 0;
        $game['rate'] = $game_type_info['rate'];
        foreach ($game_bet_money_info as $value) {
            $all_money += $value;
        }
        log_file('kill_money16--:'.$game_result_id,'kill');
        log_file('kill_money16:'.$all_money,'kill');
        foreach ($game_bet_money_info as $key => $value) {
            echo $rate_info[$key].'---';
            $real_rate =  abs($value*$rate_info[$key]/$all_money*100 - $game_type_info['rate']);
            if ($real_rate<$game['rate']) {
                $game['key'] = $key;//实际数值
                $game['rate'] = $real_rate;//实际kill
                log_file('$key16:'.$key,'kill');
                log_file('$real_rate16:'.$real_rate,'kill');
            }
        }
        echo $game['key'].'---'.$game['rate'];
        if ($game['key']==0 && $game['rate'] == 0) {
            return false;
        }
        $hg_result_obj = new HgResultModel();
        $hg_result_list = $hg_result_obj->where(['all_number'=>$game['key']])->select();
        if (!$hg_key) {
            $arr = explode(',',$hg_result_list[array_rand($hg_result_list,1)]['result']);
        } else {
            $arr = array_intersect(json_decode($hg_result_list,true),$hg_result_list);
            if ($arr) {
                $arr = array_rand($arr);
            } else {
                return false;
            }
        }
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $redis_obj->set('hg_key',json_encode($arr));
        $redis_obj->expire('hg_key','20');
        $redis_obj->set('hg_key_result_id',$game_result_id);
        $redis_obj->expire('hg_key_result_id',20);
        return $arr;
    }
    public function hanguo28GDKill($game_result_id,$game_type_id)
    {
        //获取赔率
        //获取之前有没有杀过，有的话就是第二次杀

        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $hg_key = $redis_obj->get('hg_key');

        $game_bet_money_obj = new GameBetMoneyModel();
        $filed = '*';
        $game_bet_money_info = $game_bet_money_obj->getInfo(['game_result_id'=>$game_result_id],$filed);
        $game_type_obj = new GameTypeModel();
        $game_type_info = $game_type_obj->getGameTypeInfo(['game_type_id'=>$game_type_id]);
        $rate_info = array_column(json_decode($game_type_info['bet_json'],true)[0]['bet_json'],'rate','key');
        unset($game_bet_money_info['id']);
        unset($game_bet_money_info['game_type_id']);
        unset($game_bet_money_info['game_result_id']);
        $all_money = 0;
        $game['key'] = 0;
        $game['rate'] = $game_type_info['rate'];
        foreach ($game_bet_money_info as $value) {
            $all_money += $value;
        }
//        log_file('kill_money:'.$all_money,'kill');
        foreach ($game_bet_money_info as $key => $value) {
            echo $rate_info[$key].'---';
            $real_rate =  abs($value*$rate_info[$key]/$all_money*100 - $game_type_info['rate']);
            if ($real_rate<$game['rate']) {
                $game['key'] = $key;//实际数值
                $game['rate'] = $real_rate;//实际kill
//                log_file('$key:'.$key,'kill');
//                log_file('$real_rate:'.$real_rate,'kill');
            }
        }
        echo $game['key'].'---'.$game['rate'];
        if ($game['key']==0 && $game['rate'] == 0) {
            return false;
        }
        $hg_result_obj = new HgResultModel();
        $hg_result_list = $hg_result_obj->where(['all_number'=>$game['key']])->select();
        if (!$hg_key) {
            $arr = explode(',',$hg_result_list[array_rand($hg_result_list,1)]['result']);
        } else {
            $arr = array_intersect(json_decode($hg_result_list,true),$hg_result_list);
            $game_result_obj = new GameResultModel();
            $game_result_obj->where(['id' => $redis_obj->get('hg_key_result_id')])->save(['']);
            if ($arr) {
                $arr = array_rand($arr);
            } else {
                return false;
            }
        }
        $redis_obj = new Redis();
        $redis_obj->connect(C('REDIS_HOST'),C('REDIS_PORT'));
        $redis_obj->set('hg_key',json_encode($arr));
        $redis_obj->expire('hg_key','20');
        $redis_obj->set('hg_key_result_id',$game_result_id);
        $redis_obj->expire('hg_key_result_id',20);
        return $arr;
    }

    //查出投注数最少的值
    protected function getMinBetKey($game_result_id){
        //查出投注数最少的值
        $bet_log_obj = new BetLogModel();
        $bet_log_list = $bet_log_obj->getBetLogListAll('bet_json','game_type_id ='.GameTypeModel::HANGUO28.' AND game_result_id ='.$game_result_id)?:[];
        $game_type_obj = new GameTypeModel();
        $bet_rate = $game_type_obj->getGameTypeField('game_type_id ='.GameTypeModel::HANGUO28,'bet_json');
        $bet_rate = json_decode($bet_rate,true);
        //初始化new_json
        $new_json = $bet_rate;
        foreach ($new_json as $new_key => &$new_value) {
            foreach ($new_value['bet_json'] as $new_key2 => &$new_value2) {
                $new_value2['money'] = 0;
            }
            unset($new_value2);
        }
        unset($new_value);
        //循环获取所有投注额
        foreach($bet_log_list as $k => $v){
            $bet_json = json_decode($v['bet_json'],true);
            foreach($bet_json as $kk => $vv){
                $new_json[$kk]['part'] = $vv['part'];
                foreach($vv['bet_json'] as $key => $value){
                    $new_json[$kk]['bet_json'][$key]['key'] = $value['key'];
                    $new_json[$kk]['bet_json'][$key]['money'] += $value['money'];
                }
            }
        }
        //循环获取可盈利的金额
        foreach($new_json as $new_k => &$new_v) {
            foreach ($bet_rate as $rate_k => $rate_v) {
                if ($new_v['part'] == $rate_v['part']) {
                    foreach ($new_v['bet_json'] as $new_kk => &$new_vv) {
                        $new_vv['win'] = 0;
                        foreach ($rate_v['bet_json'] as $rate_kk => $rate_vv) {
                            if ($new_vv['key'] == $rate_vv['key']) {
                                $vii['win'] = intval($new_vv['money'] * $rate_vv['rate']);
                            }
                        }
                    }
                    unset($new_vv);
                }
            }

        }
        unset($new_v);
        //循环取盈利最少的值
//        $min_part = $new_json[0]['part'];
//        $min_key = $new_json[0]['bet_json'][0]['key'];
        $min_win = $new_json[0]['bet_json'][0]['win'];
        $min_arr = [];
        foreach ($new_json as $new_ki => $new_vi) {
            foreach ($new_vi['bet_json'] as $new_kki => $new_vvi) {
                if ($new_vvi['win'] < $min_win) {
                    $min_win = $new_vvi['win'];
                    $min_arr = [];
                    $min_arr[] = array(
                        'min_key' => $new_vvi['key'],
                        'min_part' => $new_vi['part'],
                    );
                } elseif ($new_vvi['win'] == $min_win) {
                    $min_arr[] = array(
                        'min_key' => $new_vvi['key'],
                        'min_part' => $new_vi['part'],
                    );
                }
            }
        }

        $randKey = array_rand($min_arr);
        $result = $min_arr[$randKey];
        $result['min_win'] = $min_win;
        return $result;
    }

    //循环取个6值
    protected function randSixNum($sum, $max_num, $min_num)
    {
        $one_arr = [];
        $sum = $sum - 6 * $min_num;
        $max_num = $max_num - $min_num;
        for ($i = 6; $i > 0; $i--) {
            $rand = ceil($sum / $i);
            $max = $sum > $max_num ? $max_num : $sum;
            $number = rand($rand, $max);
            while (in_array($number + $min_num, $one_arr)) {
                $number = rand($rand, $max_num);
            }
            $one_arr[] = $number + $min_num;
            $sum -= $number;
        }
        sort($one_arr);
        return $one_arr;
    }

    //特码开奖
    public function tema($game_log_info, $bet_json, $is_fixation_rate)
    {
        log_file('tema:'.json_encode($game_log_info),'tema');
        log_file('tema-bet:'.json_encode($bet_json),'tema');
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }
            log_file('bet_json:'.json_encode($bet_json));
            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {//循环下注的part
                    foreach ($bet_json as $kk => $vv) {//循环下注比例

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {//循环下注key
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {//循环比例json
                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1)//特码A
                                    {
                                        if ($game_log_info['result'] == $vii['key'] && $game_log_info['result'] == $vvi['key'] && $game_log_info['result']!= 49) {//49为和

                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        } elseif ($game_log_info['result'] == 49) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] && $vvi['key'] && $vi['part'] == 2) //特码B
                                    {

                                        if ($game_log_info['result'] == $vvi['key'] && $game_log_info['result'] == $vvi['key']  && $game_log_info['result']!= 49) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }elseif ($game_log_info['result'] == 49) {
                                            $vii['win'] = intval($vii['money']);
                                            $after_money += $vii['win'];
                                        }

                                    }
                                    elseif ($vii['key'] && $vvi['key'] && $vi['part'] == 3) //号码
                                    {

                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//特大
                                            if ($game_log_info['result']>=25&&$game_log_info['result']<=48) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//特小
                                            if ($game_log_info['result']<=24&&$game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//特尾大
                                            if (self::check_weushudaxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//特尾大小
                                            if (self::check_weushudaxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                        elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//特单
                                            if (self::check_danshuang($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//特双
                                            if (self::check_danshuang($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//特大单
                                            if (self::check_danshuang($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//特大双
                                            if (self::check_danshuang($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//特合大
                                            if (self::check_heshudaxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                        elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//特合小
                                            if (self::check_heshudaxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//特小单
                                            if (self::check_danshuang($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//特小双
                                            if (self::check_danshuang($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }


                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }

            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }
    //特码开奖
    public function liangmian($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');



        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {
                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1) //两面
                                    {
                                        if ($vii['key'] == 0 && $vvi['key'] == 0) {//特大
                                            if ($game_log_info['result']>=25&&$game_log_info['result']<=48) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 1 && $vvi['key'] == 1) {//特小
                                            if ($game_log_info['result']<=24&&$game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//特单
                                            if (self::check_danshuang($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 3&& $vvi['key'] == 3) {//特双
                                            if (self::check_danshuang($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//特合大
                                            if (self::check_heshudaxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                        elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//特合小
                                            if (self::check_heshudaxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//特合单
                                            if (self::check_heshudanshuang($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//特合双
                                            if (self::check_heshudanshuang($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//特天肖
                                            if (self::check_tetiandi($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//特地肖
                                            if (self::check_tetiandi($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//特前肖
                                            if (self::check_teqianhou($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//特后肖
                                            if (self::check_teqianhou($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//特家肖
                                            if (self::check_tejiaye($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 13 && $vvi['key'] == 13) {//特野肖
                                            if (self::check_tejiaye($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                        elseif ($vii['key'] == 14 && $vvi['key'] == 14) {//特大尾
                                            if (self::check_weushudaxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 15 && $vvi['key'] == 15) {//特小尾
                                            if (self::check_weushudaxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 16 && $vvi['key'] == 16) {//总和单
                                            if (self::check_zonghedanshuang($result_str) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 17 && $vvi['key'] == 17) {//总和双
                                            if (self::check_zonghedanshuang($result_str) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 18 && $vvi['key'] == 18) {//总和大
                                            if (self::check_zonghedaxiao($result_str) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 19 && $vvi['key'] == 19) {//总和小
                                            if (self::check_zonghedaxiao($result_str) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 20 && $vvi['key'] == 20) {//特大单
                                            if (self::check_danshuang($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 21 && $vvi['key'] == 21) {//特小单
                                            if (self::check_danshuang($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 22 && $vvi['key'] == 22) {//特大双
                                            if (self::check_danshuang($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 2 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 23 && $vvi['key'] == 23) {//特小双
                                            if (self::check_danshuang($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 1 && $game_log_info['result']>=1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }

            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //色波开奖
    public function sebo($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {
                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1)//色波
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//红色波
                                            if (self::check_sebo($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//蓝色波
                                            if (self::check_sebo($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//绿色波
                                            if (self::check_sebo($game_log_info['result']) == 3) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                    } elseif ($vii['key'] && $vvi['key'] && $vi['part'] == 2) //半波
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//红单
                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//蓝单
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//绿单
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_danshuang($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//红双

                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//蓝双
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//绿双
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_danshuang($game_log_info['result']) == 2) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//红大
                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//蓝大
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//绿大
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_daxiao($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//红小
                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//蓝小
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//绿小
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_daxiao($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                    }
                                    elseif ($vii['key'] && $vvi['key'] && $vi['part'] == 3) //半半波
                                    {

                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//红大单
                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//蓝大单
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//绿大单
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_daxiao($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//红大双

                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//蓝大双
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//绿大双
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_daxiao($game_log_info['result']) == 2 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//红小单
                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//蓝小单
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//绿小单
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_daxiao($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//红小双
                                            if (self::check_sebo($game_log_info['result']) == 1 && self::check_daxiao($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//蓝小双
                                            if (self::check_sebo($game_log_info['result']) == 2 && self::check_daxiao($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//绿小双
                                            if (self::check_sebo($game_log_info['result']) == 3 && self::check_daxiao($game_log_info['result']) == 1 && self::check_danshuang($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }

            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //特肖开奖
    public function texiao($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {
                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1)//特肖
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//鼠
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::SHU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//马
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::MA ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//牛
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::NIU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//羊

                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::YANG ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//虎
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::HU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//猴
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::HOU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//兔
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::TU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//鸡
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::JI ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//龙
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::LONG ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//狗
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::GOU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//蛇
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::SHE ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//猪
                                            if (self::check_shengxiao($game_log_info['result']) == BetLogModel::ZHU ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }

            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //合肖
    public function hexiao($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] ) //肖
                                    {
                                        if ($vii['key'] && $vvi['key'] ) {//
                                                if (self::check_hexiao($result_str,$vii['value'])==1) {
                                                    $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                    $after_money += $vii['win'];
                                                }
                                        }

                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //特码头尾数开奖
    public function temaweishu($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {
                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1)//特码头尾数
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//0头
                                            if (self::check_tou($game_log_info['result']) == 0) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//1头
                                            if (self::check_tou($game_log_info['result']) == 1 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//2头
                                            if (self::check_tou($game_log_info['result']) == 2 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//3头

                                            if (self::check_tou($game_log_info['result']) == 3 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//4头
                                            if (self::check_tou($game_log_info['result']) == 4 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//0尾
                                            if (self::check_weishu($game_log_info['result']) == 0 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//1尾
                                            if (self::check_weishu($game_log_info['result']) == 1 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//2尾
                                            if (self::check_weishu($game_log_info['result']) == 2 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//3尾
                                            if (self::check_weishu($game_log_info['result']) == 3 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//4尾
                                            if (self::check_weishu($game_log_info['result']) == 4) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//5尾
                                            if (self::check_weishu($game_log_info['result']) == 5 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//6尾
                                            if (self::check_weishu($game_log_info['result']) == 6) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 13 && $vvi['key'] == 13) {//7尾
                                            if (self::check_weishu($game_log_info['result']) == 7 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 14 && $vvi['key'] == 14) {//8尾
                                            if (self::check_weishu($game_log_info['result']) == 8 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 15 && $vvi['key'] == 15) {//9尾
                                            if (self::check_weishu($game_log_info['result']) == 9 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //正码开奖
    public function zhengma($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1)//号码
                                    {
                                        if ($game_log_info['result'] == $vvi['key'] && self::check_haoma($result_str,$vvi['key'])) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] && $vvi['key'] && $vi['part'] == 2) //总
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1 ) {//总大
                                            if (self::check_zonghedaxiao($result_str) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif($vii['key'] == 2 && $vvi['key'] == 2 ){//总小
                                            if (self::check_zonghedaxiao($result_str) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 3 && $vvi['key'] == 3 ){//总单
                                            if (self::check_zonghedanshuang($result_str) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//总双
                                            if (self::check_zonghedanshuang($result_str) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //正码开奖
    public function zhengmate($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part']%2 == 1)//正码
                                    {
                                        if ($result_str_arr[$vi['part']%2] == $vvi['key'] ) {
                                            $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                            $after_money += $vii['win'];
                                        }

                                    } elseif ($vii['key'] && $vvi['key'] && $vi['part']%2 == 0) //总
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1 ) {//单
                                            if (self::check_danshuang($result_str_arr[$vi['part']%2])==1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif($vii['key'] == 2 && $vvi['key'] == 2 ){//双
                                            if (self::check_danshuang($result_str_arr[$vi['part']%2]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 3 && $vvi['key'] == 3 ){//红
                                            if (self::check_sebo($result_str_arr[$vi['part']%2]) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//大
                                            if (self::check_daxiao($result_str_arr[$vi['part']%2]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//小
                                            if (self::check_daxiao($result_str_arr[$vi['part']%2]) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//蓝
                                            if (self::check_sebo($result_str_arr[$vi['part']%2]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//合单
                                            if (self::check_zonghedanshuang($result_str) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//合双
                                            if (self::check_zonghedanshuang($result_str) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//绿
                                            if (self::check_sebo($result_str_arr[$vi['part']%2]) == 3) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //正码1-6
    public function zhengma16($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part']) //总
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1 ) {//单
                                            if (self::check_danshuang($result_str_arr[$vi['part']])==1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif($vii['key'] == 2 && $vvi['key'] == 2 ){//双
                                            if (self::check_danshuang($result_str_arr[$vi['part']]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 3 && $vvi['key'] == 3 ){//大
                                            if (self::check_daxiao($result_str_arr[$vi['part']]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//小
                                            if (self::check_daxiao($result_str_arr[$vi['part']]) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 5 && $vvi['key'] == 5 ){//合单
                                            if (self::check_zonghedanshuang($result_str) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 6 && $vvi['key'] == 6 ){//合双
                                            if (self::check_zonghedanshuang($result_str) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 7 && $vvi['key'] == 7 ){//合大
                                            if (self::check_zonghedaxiao($result_str) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 8 && $vvi['key'] == 8 ){//合小
                                            if (self::check_zonghedaxiao($result_str) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 9 && $vvi['key'] == 9 ){//红
                                            if (self::check_sebo($result_str_arr[$vi['part']]) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }

                                        elseif($vii['key'] == 10 && $vvi['key'] == 10 ){//蓝
                                            if (self::check_sebo($result_str_arr[$vi['part']]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 11 && $vvi['key'] == 11 ){//绿
                                            if (self::check_sebo($result_str_arr[$vi['part']]) == 3) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 12 && $vvi['key'] == 12 ){//尾大
                                            if (self::check_weushudaxiao($result_str_arr[$vi['part']]) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 13 && $vvi['key'] == 13 ){//尾小
                                            if (self::check_weushudaxiao($result_str_arr[$vi['part']]) == 1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }


                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //五行开奖
    public function pingteyixiaowei($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1) //一肖
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//鼠
                                            if (self::check_findshengxiao($result_str,BetLogModel::SHU)) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//马
                                            if (self::check_findshengxiao($result_str,BetLogModel::MA )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//牛
                                            if (self::check_findshengxiao($result_str,BetLogModel::NIU )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//羊

                                            if (self::check_findshengxiao($result_str,BetLogModel::YANG ) ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//虎
                                            if (self::check_findshengxiao($result_str,BetLogModel::HU)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//猴
                                            if (self::check_findshengxiao($result_str,BetLogModel::HOU)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//兔
                                            if (self::check_findshengxiao($result_str,BetLogModel::TU)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//鸡
                                            if (self::check_findshengxiao($result_str,BetLogModel::JI )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//龙
                                            if (self::check_findshengxiao($result_str,BetLogModel::LONG)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//狗
                                            if (self::check_findshengxiao($result_str,BetLogModel::GOU )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//蛇
                                            if (self::check_findshengxiao($result_str,BetLogModel::SHE)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//猪
                                            if (self::check_findshengxiao($result_str,BetLogModel::ZHU )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    } elseif ($vii['key'] && $vvi['key'] && $vi['part'] == 2)
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//鼠
                                            if (self::check_findweishu($result_str,0)) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//马
                                            if (self::check_findweishu($result_str,6 )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//牛
                                            if (self::check_findweishu($result_str,1 )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//羊

                                            if (self::check_findweishu($result_str,6 ) ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//虎
                                            if (self::check_findweishu($result_str,2)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//猴
                                            if (self::check_findweishu($result_str,7)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//兔
                                            if (self::check_findweishu($result_str,3)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//鸡
                                            if (self::check_findweishu($result_str,8)  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//龙
                                            if (self::check_findweishu($result_str,4)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//狗
                                            if (self::check_findweishu($result_str,9)  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    public function zhengxiao($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1) //一肖
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//鼠
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::SHU)) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::SHU);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//马
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::MA )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::MA);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//牛
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::NIU )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::NIU);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//羊

                                            if (self::check_zhengshengxiao($result_str,BetLogModel::YANG ) ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::YANG);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//虎
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::HU)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::HU);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//猴
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::HOU)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::HOU);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//兔
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::TU)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::TU);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//鸡
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::JI )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::JI);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//龙
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::LONG)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::LONG);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//狗
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::GOU )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::GOU);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//蛇
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::SHE)   ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::SHE);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//猪
                                            if (self::check_zhengshengxiao($result_str,BetLogModel::ZHU )  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate'])*self::check_zhengshengxiao($result_str,BetLogModel::ZHU);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //色波7开奖
    public function sebo7($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1) //一肖
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//红
                                            if (self::check_sebo7($result_str)==1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//绿
                                            if (self::check_sebo7($result_str)==3  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//蓝
                                            if (self::check_sebo7($result_str)==2  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//和

                                            if (self::check_sebo7($result_str)==4 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    public function zongxiao($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1) //一肖
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1) {//2肖
                                            if (self::check_zongxiao($result_str)==2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 2 && $vvi['key'] == 2) {//3肖
                                            if (self::check_zongxiao($result_str)==3  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 3 && $vvi['key'] == 3) {//4肖
                                            if (self::check_zongxiao($result_str)==4  ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 4 && $vvi['key'] == 4) {//5肖

                                            if (self::check_zongxiao($result_str)==5 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 5 && $vvi['key'] == 5) {//6肖

                                            if (self::check_zongxiao($result_str)==6 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        } elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//7肖

                                            if (self::check_zongxiao($result_str)==7 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//总肖单

                                            if (self::check_zongxiao($result_str)==1 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//总肖双

                                            if (self::check_zongxiao($result_str)==0 ) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //自选不中
    public function zixuanbuzhong($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] == 1) //一肖
                                    {
                                        if ($vii['key'] && $vvi['key'] ) {//
                                            if (self::check_zixuanbuzhong($result_str,$vii['value'])==1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
// elseif ($vii['key'] == 6 && $vvi['key'] == 6) {//选5
//                                            if (self::check_zongxiao($result_str,$vii['value'])==3  ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        }elseif ($vii['key'] == 7 && $vvi['key'] == 7) {//选5
//                                            if (self::check_zongxiao($result_str,$vii['value'])==4  ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        }elseif ($vii['key'] == 8 && $vvi['key'] == 8) {//选5
//
//                                            if (self::check_zongxiao($result_str,$vii['value'])==5 ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        } elseif ($vii['key'] == 9 && $vvi['key'] == 9) {//选5
//
//                                            if (self::check_zongxiao($result_str,$vii['value'])==6 ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        } elseif ($vii['key'] == 10 && $vvi['key'] == 10) {//选5
//
//                                            if (self::check_zongxiao($result_str,$vii['value'])==7 ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        }elseif ($vii['key'] == 11 && $vvi['key'] == 11) {//选5
//
//                                            if (self::check_zongxiao($result_str,$vii['value'])==1 ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        }elseif ($vii['key'] == 12 && $vvi['key'] == 12) {//选5
//
//                                            if (self::check_zongxiao($result_str,$vii['value'])==0 ) {
//                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
//                                                $after_money += $vii['win'];
//                                            }
//                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //连肖连伟
    public function lianxiaolianwei($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] <= 4) //肖
                                    {
                                        if ($vii['key'] && $vvi['key'] ) {//
                                            $arr = explode('-',$vii['value']);//字符串转换成数组
                                            foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果
                                                if (self::check_lianxiao($result_str,$v_arr)==1) {
                                                    $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                    $after_money += $vii['win'];
                                                }
                                            }
                                        }

                                    }elseif ($vii['key'] && $vvi['key'] && $vi['part'] >= 5) //连尾
                                    {
                                        if ($vii['key'] && $vvi['key'] ) {//
                                            $arr = explode('-',$vii['value']);//字符串转换成数组
                                            foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果
                                                if (self::check_lianwei($result_str,$v_arr)==1) {
                                                    $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                    $after_money += $vii['win'];
                                                }
                                            }
                                        }

                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //连码开奖
    public function lianma($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part'] ==1) //lianma
                                    {
                                        if ($vii['key'] && $vvi['key'] &&$vvi['key'] == 1) {//
                                            $bet_arr = explode(',',$vvi['rate']);//遍历三中二
                                            $num = 1;
                                            foreach ($bet_arr as $key_bet => $bet_value) {
                                                $arr = explode('-',$vii['value']);//字符串转换成数组
                                                foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果
                                                    if (self::check_lianma($result_str,$v_arr,$num)==1) {
                                                        $vii['win'] = intval($vii['money'] * $bet_value);
                                                        $after_money += $vii['win'];
                                                    }
                                                    $num--;
                                                }
                                            }


                                        }
                                        elseif ($vii['key'] && $vvi['key'] &&$vvi['key'] == 4) {//二中特
                                            $arr = explode('-',$vii['value']);//字符串转换成数组
                                            foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果-全中
                                                if (self::check_lianma($result_str,$v_arr)==1) {
                                                    $vii['win'] = intval($vii['money'] * $arr[0]);
                                                    $after_money += $vii['win'];
                                                }

                                            }
                                            foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果--特中
                                                if (self::check_tezhong($result_str,$v_arr)==1) {
                                                    $vii['win'] = intval($vii['money'] * $arr[1]);
                                                    $after_money += $vii['win'];
                                                }

                                            }

                                        }
                                        elseif ($vii['key'] && $vvi['key'] &&$vvi['key'] == 5) {//特串
                                            $arr = explode('-', $vii['value']);//字符串转换成数组
                                            foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果--特中
                                                if (self::check_tezhong($result_str,$v_arr)==1) {
                                                    $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                    $after_money += $vii['win'];
                                                }
                                            }
                                        }
                                            elseif ($vii['key'] && $vvi['key'] ) {//
                                            $arr = explode('-',$vii['value']);//字符串转换成数组
                                            foreach ($arr as $k_arr =>$v_arr) {//遍历数据组合计算结果
                                                if (self::check_lianma($result_str,$v_arr)==1) {
                                                    $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                    $after_money += $vii['win'];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }

    //开奖
    public function liuhewuxing($game_log_info, $bet_json, $is_fixation_rate)
    {
        $account_obj = new AccountModel();
        $bet_log_obj = new BetLogModel();
        $bet_auto_obj = new BetAutoModel();
        $game_result_obj = new GameResultModel();
        $result_str = $game_result_obj->getGameResultField('game_result_id = '.$game_log_info['game_result_id'],'result');
        $result_str_arr = explode(',',$result_str);
        if (!$is_fixation_rate) {
            $bet_json = $this->randDeduct($game_log_info['game_log_id'], $bet_json);
        }

        //六合彩需要获取不容盘口的赔率
        $game_type_obj = new GameTypeModel();

        $pan_bet_json = $game_type_obj->getGameTypeInfo('game_type_id =' . $game_log_info['game_type_id'],'bet_json_b,bet_json_c,bet_json_d');

        $bet_log_list = $bet_log_obj->getBetLogListAll('', 'game_result_id =' . $game_log_info['game_result_id'] . ' AND game_type_id = ' . $game_log_info['game_type_id']);


        $max_win_money = $game_type_obj->getGameTypeField('game_type_id =' . $game_log_info['game_type_id'], 'max_win_money');

        $type = 0;

        //循环投注记录列表
        foreach ($bet_log_list as $k => &$v) {
            $new_json = json_decode($v['bet_json'], true);
            $is_win = 0;
            $after_money = 0;
            $bet_money = $v['total_bet_money'];

            //获取相关盘口数据
            if ($v['pan_type'] != BetLogModel::A) {
                $bet_json = $pan_bet_json[BetLogModel::getPanStr($v['pan_type'])];
            }

            //如果是群玩法，排除直接返本金
            if ($type != 0 ) {
                $after_money = $v['total_bet_money'];
            }
            else {
                //循环投注
                foreach ($new_json as $ki => &$vi) {
                    foreach ($bet_json as $kk => $vv) {

                        if ($vv['part'] == $vi['part']) {
                            foreach ($vi['bet_json'] as $kii => &$vii) {
                                $vii['win'] = 0;
                                foreach ($vv['bet_json'] as $kki => $vvi) {

                                    if ($vii['key'] && $vvi['key'] && $vi['part']) //五行
                                    {
                                        if ($vii['key'] == 1 && $vvi['key'] == 1 ) {//金
                                            if (self::check_wuxing($game_log_info['result'])==1) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }elseif($vii['key'] == 2 && $vvi['key'] == 2 ){//木
                                            if (self::check_wuxing($game_log_info['result']) == 2) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 3 && $vvi['key'] == 3 ){//水
                                            if (self::check_wuxing($game_log_info['result']) == 3) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 4 && $vvi['key'] == 4 ){//火
                                            if (self::check_wuxing($game_log_info['result']) == 4) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }
                                        elseif($vii['key'] == 5 && $vvi['key'] == 5 ){//土
                                            if (self::check_wuxing($game_log_info['result']) == 5) {
                                                $vii['win'] = intval($vii['money'] * $vvi['rate']);
                                                $after_money += $vii['win'];
                                            }
                                        }


                                    }
                                }
                            }
                            unset($vii);
                        }
                    }
                }
            }
            unset($vi);
            $new_json = json_encode($new_json);

            if ($after_money > 0) {
                if ($after_money > $max_win_money) {
                    $after_money = $max_win_money;
                }
                $account_obj->addAccount($v['user_id'], AccountModel::GAMEWIN, $after_money, '竞猜奖励', $v['bet_log_id']);
            }
            if ($after_money > $bet_money) {
                $is_win = 1;
            }
            $bet_log_obj->editBetLog('bet_log_id =' . $v['bet_log_id'], ['bet_json' => $new_json, 'is_win' => $is_win, 'total_after_money' => $after_money, 'is_open' => 1]);

            //自动投注
            if ($v['is_auto_bet']) {
                $bet_auto_obj->changeMode($v['bet_auto_id'], $is_win);
            }

        }
        unset($v);
    }



    /**
     * note:判断单双
     * @param $result
     * @return int @1单，2双，3和局
     */
    static function check_danshuang($result)
    {
        if ($result == 49) {
            return 3;
        }
        if ($result%2 == 0) {
            return 2;
        }
        if ($result%2 == 1) {
            return 1;
        }
    }

    /**
     * note:判断大小
     * @param $result
     * @return int  1代表小，2代表大，3和局
     */
    static function check_daxiao($result)
    {
        if ($result == 49) {
            return 3;
        }
        if ($result>=1 && $result<=24) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:判断合数大小
     * @param $result
     * @return int  1代表大，2代表小，3和局
     */
    static function check_heshudaxiao($result)
    {
        if ($result == 49) {
            return 3;
        }
        $ge = $result%10;
        $shi = intval($result/10);
        if ($ge+$shi>=7) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:判断合数单双
     * @param $result
     * @return int  1代表单，2代表双，3和局
     */
    static function check_heshudanshuang($result)
    {
        if ($result == 49) {
            return 3;
        }
        $ge = $result%10;
        $shi = intval($result/10);
        if (($ge+$shi)%2 == 0) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * note:判断特天地肖
     * @param $result
     * @return int  1，特天肖，2特地肖，3和局
     */
    static function check_tetiandi($result)
    {
        if ($result == 49) {
            return 3;
        }
        $tian_arr = [BetLogModel::NIU,BetLogModel::TU,BetLogModel::LONG,BetLogModel::MA,BetLogModel::HOU,BetLogModel::ZHU];
        if (in_array(self::check_shengxiao($result),$tian_arr)){
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:判断特前后
     * @param $result
     * @return int 1特前，2特后，3和局
     */
    static function check_teqianhou($result)
    {
        if ($result == 49) {
            return 3;
        }
        $tian_arr = [BetLogModel::SHU,BetLogModel::NIU,BetLogModel::HU,BetLogModel::TU,BetLogModel::LONG,BetLogModel::SHE];
        if (in_array(self::check_shengxiao($result),$tian_arr)){
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:判断特家野
     * @param $result
     * @return int 1特家，2特野，3和局
     */
    static function check_tejiaye($result)
    {
        if ($result == 49) {
            return 3;
        }
        $tian_arr = [BetLogModel::NIU,BetLogModel::MA,BetLogModel::YANG,BetLogModel::JI,BetLogModel::GOU,BetLogModel::ZHU];
        if (in_array(self::check_shengxiao($result),$tian_arr)){
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:判断尾数大小
     * @param $result
     * @return int 1小，2大，3和局
     */
    static function check_weushudaxiao($result)
    {
        if ($result == 49) {
            return 3;
        }
        if ($result%10 >= 5 && $result%10 <= 9) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * note:判断总和单双
     * @param $result
     * @return int 1单，2双
     */
    static function check_zonghedanshuang($result)
    {
        $result_arr = explode(',',$result);
        $sum = 0;
        foreach ($result_arr as $value) {
            $sum += $value;
        }
        if ($sum%2 == 0) {
            return 2;
        } else {
            return 1;
        }

    }

    /**
     * note:判断总和大小
     * @param $result
     * @return int 1小，2大
     */
    static function check_zonghedaxiao($result)
    {
        $result_arr = explode(',',$result);
        $sum = 0;
        foreach ($result_arr as $value) {
            $sum += $value;
        }
        if ($sum >= 175) {
            return 2;
        } else {
            return 1;
        }

    }

    /**
     * note:判断属于什么波
     * @param $result
     * @return int 1红波，2蓝波，3绿波，4和
     */
    static function check_sebo($result)
    {
        $return_num = 4;
        switch ($result%6) {
            case 1:
            case 2:
                $return_num = 1;
                break;
            case 3:
            case 4:
                $return_num = 2;
                break;
            case 5:
            case 0:
                $return_num = 3;
                break;
        }
        return $return_num;
    }

    /**
     * note:判断头部数字
     * @param $result
     * @return int
     */
    static function check_tou($result)
    {
        $tou = intval($result/10);
        return $tou;
    }

    /**
     * note:返回尾数
     * @param $result
     * @return int
     */
    static function check_weishu($result)
    {
        $wei = $result%10;
        return $wei;
    }

    //判断生肖
    static function check_shengxiao($result)
    {
        return $result%13;
    }

    //一肖
    static function check_findshengxiao($result,$type)
    {
        $result_arr = explode(',',$result);
        foreach ($result_arr as $value) {
            if ($value%13 == $type){
                return true;
            }
        }
        return false;
    }
    static function check_findweishu($result,$type)
    {
        $result_arr = explode(',',$result);
        foreach ($result_arr as $value) {
            if ($value%10 == $type){
                return true;
            }
        }
        return false;
    }


    static function check_zhengshengxiao($result,$type)
    {
        $num = 0;
        $result_arr = explode(',',$result);
        foreach ($result_arr as $value) {
            if ($value%13 == $type){
                $num++;
            }
        }
        return $num;
    }


    static function check_haoma($result,$key)
    {
        $result_arr = explode(',',$result);
        $result_arr = array_pop($result_arr);
        if (in_array($key,$result_arr)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * note:判断五行
     * @param $result
     * @return int 1金,2木,3水,4火,5土
     */
    static function check_wuxing($result)
    {
        $wuxing_num = 0;
        $jin = [6,7,20,21,28,29,36,37];
        $mu = [2,3,10,11,18,19,32,33,40,41,48,49];
        $shui = [8,9,16,17,24,25,38,39,46,47];
        $huo = [4,5,12,13,26,27,34,35,42,43];
        $tu = [1,14,15,22,23,30,31,44,45];
        if (in_array($result,$jin)) {
            $wuxing_num = 1;
        }
        if (in_array($result,$mu)) {
            $wuxing_num = 2;
        }
        if (in_array($result,$shui)) {
            $wuxing_num = 3;
        }
        if (in_array($result,$huo)) {
            $wuxing_num = 4;
        }
        if (in_array($result,$tu)) {
            $wuxing_num = 5;
        }
        return $wuxing_num;
    }


    static function check_sebo7($result)
    {
        $result_arr = explode(',',$result);
        $hong = 0;
        $lan = 0;
        $lv = 0;
        $add = 1;
        for ($i=0;$i<7;$i++) {
            if ($i==6) $add = 1.5;
            if (self::check_sebo($result_arr[$i]) == 1) {
                $hong+=$add;
            }
            if (self::check_sebo($result_arr[$i]) == 2) {
                $lan+=$add;
            }
            if (self::check_sebo($result_arr[$i]) == 3) {
                $lv+=$add;
            }
        }

        if ($hong == 3 && $lan == 3 && $lv == 1.5) {
            return 4;
        }
        if ($hong == 3 && $lv == 3 && $lan == 1.5) {
            return 4;
        }
        if ($lan == 3 && $lv == 3 && $hong == 1.5) {
            return 4;
        }

        if ($hong>$lan && $hong>$lv) {
            return 1;
        }
        if ($lan>$hong && $lan>$lv) {
            return 2;
        }
        if ($lv>$hong && $lv>$lan) {
            return 3;
        }
    }

    /**
     * note:总肖数量
     * @param $result
     * @return int 2-7
     */
    static function check_zongxiao($result)
    {
        $shengxiaoarr = [];
        $result_arr = explode(',',$result);
        foreach ($result_arr as $value){
            $shengxiaoarr[$value%13]=1;
        }
        $shengxiao_num = count($shengxiaoarr);
        return $shengxiao_num;
    }

    /**
     * note:自选不中
     * @param $result
     * @param $value
     * @return int 1中，2不中
     */
    static function check_zixuanbuzhong($result,$value)
    {
        $result_arr = explode(',',$result);
        $value_arr = explode(',',$value);

        if (count(array_diff($result_arr,$value_arr)) == (count($result_arr)+count($value_arr))) {
            return 1;
        } else {
            return 2;
        }

    }

    /**
     * note:连肖
     * @param $result
     * @param $value
     * @return int 1中，2不中
     */
    static function check_lianxiao($result,$str)
    {
        $result_shengxiao = [];//结果生肖
        $str_shengxiao = [];//投注生肖
        $result_arr = explode(',',$result);
        $value_arr = explode(',',$str);
        foreach ($result_arr as $value) {
            if (!in_array(self::check_shengxiao($value),$result_shengxiao)) {
                array_push($result_shengxiao,self::check_shengxiao($value));
            }
        }
        foreach ($value_arr as $value) {
            if (!in_array(self::check_shengxiao($value),$str_shengxiao)) {
                array_push($str_shengxiao,self::check_shengxiao($value));
            }
        }
        if (count(array_diff($result_shengxiao,$str_shengxiao)) == (count($result_shengxiao)+count($str_shengxiao))) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:合肖
     * @param $result
     * @param $value
     * @return int 1中，2不中
     */
    static function check_hexiao($result,$str)
    {
        $str_shengxiao = [];//投注
        $result_arr = explode(',',$result);
        $result_arr = array_pop($result_arr);
        $result_shengxiao = array_pop($result_arr);
        $value_arr = explode(',',$str);
        foreach ($value_arr as $value) {
            if (!in_array(self::check_shengxiao($value),$str_shengxiao)) {
                array_push($str_shengxiao,self::check_shengxiao($value));
            }
        }
        if (in_array(self::check_shengxiao($result_shengxiao),$str_shengxiao) && $result_shengxiao !=49) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:连尾
     * @param $result
     * @param $value
     * @return int 1中，2不中
     */
    static function check_lianwei($result,$str)
    {
        $result_shengxiao = [];//结果尾
        $str_shengxiao = [];//投注尾
        $result_arr = explode(',',$result);
        $value_arr = explode(',',$str);
        foreach ($result_arr as $value) {
            if (!in_array(self::check_weishu($value),$result_shengxiao)) {
                array_push($result_shengxiao,self::check_shengxiao($value));
            }
        }
        foreach ($value_arr as $value) {
            if (!in_array(self::check_weishu($value),$result_shengxiao)) {
                array_push($str_shengxiao,self::check_shengxiao($value));
            }
        }
        if (count(array_diff($result_shengxiao,$str_shengxiao)) == (count($result_shengxiao)+count($str_shengxiao))) {
            return 1;
        } else {
            return 2;
        }
    }


    /**
     * note:连码
     * @param $result
     * @param $value
     * @return int 1中，2不中
     */
    static function check_lianma($result,$str,$num=0)
    {
        $result_arr = explode(',',$result);
        $value_arr = explode(',',$str);

        if (count(array_diff($result_arr,$value_arr)) == $num) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:特中
     * @param $result
     * @param $value
     * @return int 1中，2不中
     */
    static function check_tezhong($result,$str,$num=0)
    {
        $result_arr = explode(',',$result);
        $value_arr = explode(',',$str);

        if (count(array_diff($result_arr,$value_arr)) == $num && in_array(array_pop($result_arr),$value_arr)) {
            return 1;
        } else {
            return 2;
        }
    }

    /**
     * note:总肖单双
     * @param $result
     * @return int  1单，0双
     */
    static function check_zongxiaodanshuang($result)
    {
        return self::check_zongxiao($result)%2;
    }


    /**
     * note:
     * @param $key *game_type_id加上bet_json中的part组成
     * @param $money *下注的钱
     * @param $pan *盘口
     * @param $user_id
     * @param $bet_log_id
     */
    function tuishui($key,$money,$pan,$user_id,$bet_log_id)
    {
        switch ($pan) {
            case 1:
                $field = 'a';
                break;
            case 2:
                $field = 'b';
                break;
            case 3:
                $field = 'c';
                break;
            case 4:
                $field = 'd';
                break;
            default:
                $field = 'a';
                break;
        }
//        echo $money;
//        echo $key;
        $water_obj = new WaterModel();
        $tuishui = $water_obj->getWaterInfo('part_key = "'.$key.'"',$field);
//        echo $water_obj->getLastSql();die;
//        dump($tuishui) ;die;
        $account_obj = new AccountModel();
        $account_obj->addAccount($user_id, AccountModel::GAMEWIN, $money*$tuishui[$field], '退水', $bet_log_id);
    }


}
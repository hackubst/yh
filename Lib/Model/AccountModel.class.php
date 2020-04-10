<?php

/**
 * @author 姜伟
 * @deprecated 记录用户账户变动明细的类库
 * @
 * */
class AccountModel extends Model
{
    /*
     *
			'1'	=> '在线充值',
			'2'	=> '银行汇入',
			'3'	=> '手动录入',
			'4'	=> '支付宝付款',
			'5'	=> '订单消费',
			'6'	=> '手动扣款',
			'7'	=> '订单退款',
			'8'=> '订单确认收货',
     *
     */
    const RELIEF = 1; //领取救济金
    const REWARD = 2; //领取排行榜奖励
    const SAVE = 3; //存入银行
    const TAKEOUT = 4; //银行取出
    const REDPACKET = 5; //发红包
    const EXCHANGEEXP = 6; //经验换豆
    const EXCHANGECARD = 7; //兑换点卡
    const GETREDPACKET = 8; //领红包
    const RECHARGE = 9; //乐豆充值
    const DEPOSIT = 10; //提现
    const REDRETURN = 11; //红包退回
    const BETTING = 12; //游戏投注
    // const ONLINE_VOUCHER = 1;
    // const BANK_VOUCHER = 2;
    // const MANUAL_OPERATOR = 3;
    // const ONLINE_PAY = 4;
    // const ORDER_COST = 5;
    // const MANUAL_DECRESE = 6;
    // const ORDER_REFOUND = 7;
    // const ORDER_CONFIRMD = 8;
    // const FIRST_LEVEL_AGENT_GAIN = 9;
    // const SECOND_LEVEL_AGENT_GAIN = 10;
    // const THIRD_LEVEL_AGENT_GAIN = 11;
    const ADD = 13; //手动加金豆
    const REDUCE = 14; //手动扣金豆
    const INTEGRAL_MONEY_COST = 14; //积分商城消费
    const DEPOSIT_APPLY = 15;//提现申请
    const REFUSE_DEPOSIT = 16;//拒绝提现申请
    const GAMEWIN = 17; //游戏竞猜赢取
    const INVITEAWARD = 18; //推广新用户奖励
    const ADDMONEY = 19; //手动加余额
    const REDUCEMONEY = 20; //手动扣余额
    const RECHARGEOUT = 21; // 代理充值支出
    const BETTING_RETURN = 22; //游戏投注返还
    const CARD_RETURN = 23; //卡密回收

    const AGENT_RED_PACKET = 24; //代理红包
    const AGENT_RED_PACKET_RETURN = 25; //代理红包

    const WEEKLOSS = 26; //每周亏损返利
    const DAILYCHARGE = 27; //每日首充返利
    const SBETTING =28;//下线投注返利
    const SELF_BETTING = 29;// 领取自己的有效流水返利
    const CANCEL_RECHARGE = 30;//撤销用户充值金豆
    const CANCEL_RECHARGE_ACT = 31;//撤销用户1%活动赠送金豆
    const RETURN_RECHARGE = 32;//返回代理充值金豆
    const ADD_BANK = 33;//增加银行分
    const REDUCE_BANK = 34;//减少银行分

    const TUISHUI = 35;//退水
    /**
     * 构造函数
     * @param void
     * @return void
     * @author 姜伟
     * @todo 初始化数据库，数据表
     * */
    public function AccountModel()
    {
        $this->db(0);
        $this->tableName = C('DB_PREFIX') . 'account';
    }

    /**
     * 修改用户账户余额，并写入账户变动日志
     * @param string $user_id 用户ID
     * @param int $change_type 变动类型
     * @param float $amount 变动金额(正负数）
     * @param string $remark 管理员备注，线上充值时，该参数为第三方支付平台返回的交易码
     * @param int $order_id 订单ID
     * @param string $proof 线下操作的凭证，非必填
     * @return float $amount_after_pay 余额不足时返回-1
     * @author 姜伟
     * @todo 1、调用分销商模型的getLeftMoney方法获取变动前余额$amount_before_pay；2、计算变动后$amount_after_pay = $amount_before_pay + $amount; 3、若$amount_after_pay小于0，返回-1退出，否则调用分销商模型的setLeftMoeny方法修改分销商余额；4、将账户变动信息写入到账户变动日志表account中；5、返回变动后的余额 $amount_after_pay
     * */
    public function addAccount($user_id, $change_type, $amount, $remark = '', $order_id = 0, $proof = '',$recharge_id = 0)
    {
        /*判断余额是否足够*/
        //调用UserModel的getLeftMoney方法获取预存款余额
        $user_obj = new UserModel($user_id);
        //变动前的余额
        $amount_before_pay = $user_obj->getLeftMoney();
        trace($amount_before_pay, 'amount_before_pay');

        //变动后的余额
        $amount_after_pay = $amount_before_pay + $amount;
        trace($amount_after_pay, 'amount_after_pay');

        $change_arr_ss = array(self::TAKEOUT,self::AGENT_RED_PACKET,self::REDPACKET,self::EXCHANGEEXP,self::RELIEF,self::WEEKLOSS,self::DAILYCHARGE,self::SBETTING,self::SELF_BETTING,self::REWARD,self::GETREDPACKET,self::REDRETURN,self::AGENT_RED_PACKET_RETURN,self::CANCEL_RECHARGE_ACT,self::REDUCE_BANK);
        //若余额不足，返回-1
        if(!in_array($change_type,$change_arr_ss))
        {
            if ($amount_after_pay < 0.00) {
                return -1;
            }
        }

        //变动后的银行余额
        $user_obj = new UserModel($user_id);
        $bank_money_before = $user_obj->getLeftBankMoney();
        $bank_money_after = $bank_money_before;

        if($change_type == self::TAKEOUT)
        {
            if ($bank_money_before - $amount < 0.00) {
                return -1;
            }
        }elseif($change_type == self::REDPACKET || $change_type == self::REDUCE_BANK)
        {
            if ($bank_money_before + $amount < 0.00) {
                return -1;
            }
        }

        $change_arr = array(self::CANCEL_RECHARGE_ACT,self::BETTING_RETURN,self::AGENT_RED_PACKET,self::REDPACKET,self::EXCHANGEEXP,self::RELIEF,self::WEEKLOSS,self::DAILYCHARGE,self::SBETTING,self::SELF_BETTING,self::REWARD,self::GETREDPACKET,self::REDRETURN,self::AGENT_RED_PACKET_RETURN,self::ADD_BANK,self::REDUCE_BANK);
        if(in_array($change_type,$change_arr))
        {
            $bank_money_after = $bank_money_before + $amount;
        }


        $change_arr_take = array(self::TAKEOUT,self::SAVE);
        if(in_array($change_type,$change_arr_take))
        {
            $bank_money_after = $bank_money_before + $amount * -1;
        }
        /*写账户变动日志begin*/
        //判断入账 or 出账
        $amount_in = $amount_out = 0.00;
        if ($amount > 0) {
            $amount_in = $amount;
        } else {
            $amount_out = $amount * -1;
        }


        //组成数组
        $this->data['user_id'] = $user_id;
        $this->data['change_type'] = $change_type;
        $this->data['amount_in'] = $amount_in;
        $this->data['amount_out'] = $amount_out;
        $this->data['amount_before_pay'] = $amount_before_pay;
        $this->data['amount_after_pay'] = $amount_after_pay;
        $this->data['order_id'] = $order_id;
        $this->data['operater'] = session('user_id') ? intval(session('user_id')) : 0;
        $this->data['addtime'] = time();
        $this->data['remark'] = $remark;
        $this->data['proof'] = $proof;
        $this->data['ip'] = get_client_ip();
        $this->data['bank_money_after'] = $bank_money_after;
        $this->data['bank_money_before'] = $bank_money_before;
        $this->data['recharge_id'] = $recharge_id ? : 0;


        //执行驱动事件
        switch ($change_type) {
            case self::SELF_BETTING: //领取有效流水投注返利
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::WEEKLOSS: //领取上周亏损返利
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::DAILYCHARGE: //领取每日首充返利
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::SBETTING: //领取下线投注返利
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::RELIEF: //领取救济金
                $this->saveBank($amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
//                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::AGENT_RED_PACKET: //代理发红包
            case self::AGENT_RED_PACKET_RETURN: //代理红包退回
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::REWARD: //领取排行榜奖励金
                $this->saveBank($amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
//                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::SAVE: //存入银行
                $this->saveBank($amount * -1);
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::TAKEOUT: //银行取出
                $this->saveBank($amount * -1);
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::REDPACKET: //发红包
                $this->saveBank($amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::ADD_BANK:
            case self::REDUCE_BANK:
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::EXCHANGEEXP: //经验换豆
                $this->saveExp($amount * -1);
                $this->saveBank($amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
//                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::EXCHANGECARD: //兑换点卡
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::GETREDPACKET: //领红包
                $this->saveBank($amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::RECHARGE:   //充值
            case self::GAMEWIN:   //游戏赢取
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::TUISHUI:   //游戏赢取
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::DEPOSIT:   //提现
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::REDRETURN:
                $this->saveBank($amount); //红包退回
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::BETTING:   //投注
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::ADD:   //后台加金豆
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::REDUCE:   //后台扣金豆
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::ADDMONEY:   //后台加余额
            case self::CARD_RETURN:   //核销
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::REDUCEMONEY:   //后台扣余额
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::RECHARGEOUT:   //代理充值后支出
                $user_obj->setLeftMoney($amount_after_pay);
                break;

            // case self::MANUAL_OPERATOR://jx1954
            //              $user_obj->setLeftMoney($amount_after_pay);//设置金额,解决充值的问题
            // 	$this->manualVoucher();//手动录入
            // 	break;
            // case self::ONLINE_PAY:
            // 	$this->onlinePayOrder();//线上支付
            // 	break;
            // case self::ORDER_COST:
            // 	$this->payOrder($order_id, $proof);//订单消费
            //              $user_obj->setLeftMoney($amount_after_pay);
            //              $user_obj->where('user_id = %d', intval($user_id))->setInc('consumed_money', $amount_out);

            // 	break;
            // case self::MANUAL_DECRESE:
            //              $user_obj->setLeftMoney($amount_after_pay);
            // 	$this->manualDeduct();//手动扣款
            // 	break;
            case self::INVITEAWARD:
                $user_obj->setLeftMoney($amount_after_pay);  //推荐新用户奖励
                break;
            //          case self::ORDER_REFOUND:
            //              //获取支付方式
            //               $payway_info = $this->getPayInfo($order_id);
            //               if (!$payway_info) {
            //                   return false;
            //               }

            //               //$this->data['payway'] = $payway_info['payway_id'];
            //               $pay_tag = $payway_info['pay_tag'];
            //               if ($pay_tag != 'wallet') {
            //                   //unset($this->data['amount_after_pay']);
            //                   //unset($this->data['amount_before_pay']);

            //                   //如果是第三方支付，则退回第三方，用户余额不变
            //                   $amount_after_pay = $amount_before_pay;

            //                   //执行第三方退款操作
            //                   if ($pay_tag == 'wxpay') {
            //                       $item_refund_change_id = M('ItemRefundChange')->where('order_id = ' . $order_id)->getField('item_refund_change_id');
            //                       trace($item_refund_change_id, 'wuzeguo');
            //                       $pay_code = M('Order')->where('order_id = ' . $order_id)->getField('pay_code');

            //                       //微信支付
            //                       $wxpay_obj = new WXPayModel();
            //                       $status = $wxpay_obj->refund($order_id, $item_refund_change_id, 1, $amount, $pay_code);
            //                       if (!$status) return false;
            //                   }

            //                   if ($pay_tag == 'cardpay') {
            //                       //会员卡支付
            //                       $member_car_obj = new MemberCardModel();
            //                       $status = $member_car_obj->refund($order_id, $user_id, $amount);
            //                       if (!$status) return false;

            //                   }

            //               } else {
            //                   //调用UserModel的setLeftMoeny方法修改分销商余额
            //                   $user_obj->setLeftMoney($amount_after_pay);
            //               }

            //              break;
            // case self::GROUP_BUY_COST:
            // 	//如果是团购则记录信息
            // 	//group_buy_user表
            // 	if ($change_type == self::GROUP_BUY_COST) {
            // 		$group_buy_user_obj = new GroupBuyUserModel();
            // 		$group_buy_id = M('Order')->where('order_id  = ' . $order_id)->getField('group_buy_id');
            // 		$arr = array(
            // 			'user_id' => $user_id,
            // 			'order_id' => $order_id,
            // 			'group_buy_id' => $group_buy_id,
            // 			'addtime'   => time(),
            // 		);
            // 		$group_buy_user_obj->addGroupBuyUser($arr);

            // 		//修改group_buy表状态
            // 		$group_buy_obj = new GroupBuyModel($group_buy_id);
            // 		$group_buy_obj->setGroupBuyStatus();
            // 	}

            // 	//订单状态
            // 	$this->payOrder($order_id, $proof);//订单消费
            //              $user_obj->setLeftMoney($amount_after_pay);
            //              $user_obj->where('user_id = %d', intval($user_id))->setInc('consumed_money', $amount_out);

            // 	break;
            // case self::GROUP_REFUND:
            //              //获取支付方式
            //               $payway_info = $this->getPayInfo($order_id);
            //               if (!$payway_info) {
            //                   return false;
            //               }

            //               //$this->data['payway'] = $payway_info['payway_id'];
            //               $pay_tag = $payway_info['pay_tag'];
            //               if ($pay_tag != 'wallet') {
            //                   //unset($this->data['amount_after_pay']);
            //                   //unset($this->data['amount_before_pay']);

            //                   //如果是第三方支付，则退回第三方，用户余额不变
            //                   $amount_after_pay = $amount_before_pay;

            //                   //执行第三方退款操作
            //                   if ($pay_tag == 'wxpay') {
            //                       $info = M('Order')->field('pay_code, group_buy_id')->where('order_id = ' . $order_id)->find();
            //                       $pay_code = $info['pay_code'];
            //                       $item_refund_change_id = NOW_TIME . $order_id;

            //                       //微信支付
            //                       $wxpay_obj = new WXPayModel();
            //                       $status = $wxpay_obj->refund($order_id, $item_refund_change_id, 1, $amount, $pay_code);
            //                       if (!$status) return false;
            //                   }

            //                   if ($pay_tag == 'cardpay') {
            //                       //会员卡支付
            //                       $member_car_obj = new MemberCardModel();
            //                       $status = $member_car_obj->refund($order_id, $user_id, $amount);
            //                       if (!$status) return false;

            //                   }

            //               } else {
            //                   //调用UserModel的setLeftMoeny方法修改分销商余额
            //                   $user_obj->setLeftMoney($amount_after_pay);
            //               }
            //              break;

            case self::INTEGRAL_MONEY_COST :
                $this->payOrder($order_id, $proof);//订单消费
                $user_obj->setLeftMoney($amount_after_pay);
                $user_obj->where('user_id = %d', intval($user_id))->setInc('consumed_money', $amount_out);
                break;
            case self::DEPOSIT_APPLY:
                $user_obj->setLeftMoney($amount_after_pay);
                $this->depositApply($user_id, $amount * -1);
                break;
            case self::REFUSE_DEPOSIT:
                $user_obj->setLeftMoney($amount_after_pay);
                $this->depositApply($user_id, $amount * -1);
                break;
            case self::BETTING_RETURN:
//                $this->saveBank($amount);
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::CANCEL_RECHARGE:
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            case self::CANCEL_RECHARGE_ACT:
                $this->AutoSaveBank($user_id,$amount);
                $this->data['amount_after_pay'] = $amount_before_pay;
                break;
            case self::RETURN_RECHARGE:
                $user_obj->setLeftMoney($amount_after_pay);
                break;
            default:
                trigger_error(__CLASS__ . ', ' . __FUNCTION__ . ', 无效的参数change_type');
        }

        $app_list = C('APP_LIST');
        foreach ($app_list AS $k => $v) {
            foreach ($v['account'] AS $key => $val) {
                if ($change_type == $val) {
                    //调用相关组件的相关方法
                    $class = $k . 'Model';
                    $func = strtolower($k) . $change_type;
                    $obj = new $class();
                    $obj->$func($order_id, $user_id, $amount);
                    break;
                }
            }
        }

        if($change_type == self::RECHARGEOUT)
        {
            return $this->add($this->data);
        }
        //保存到数据库
        $this->add($this->data);
        log_file('account: ' . $this->getLastSql());
        /*写账户变动日志end*/

        if($change_type == self::TAKEOUT || $change_type == self::REDPACKET)
        {
            return $bank_money_after;
        }
        return $amount_after_pay;
    }

    /**
     * @access public
     * @param string $pay_code 支付平台返回的交易码，唯一。 必须
     * @param int $pay_state 充值状态。1表示成功，0表示失败
     * @param float $pay_money 支付的金额。默认为0.00
     * @param int $sms_total 充值的短信总条数。默认为0
     * @return void
     * @author zhoutao
     * @todo   添加短信支付日志(表tp_sms_pay)
     */
    public function addSMSPayLog($pay_code, $pay_state, $pay_money = 0.00, $sms_total = 0)
    {
        if (!$pay_code) {
            return false;
        }
        $this->tableName = 'sms_pay';
        $data = array(
            'pay_code' => $pay_code,
            'pay_money' => $pay_money,
            'sms_total' => $sms_total,
            'pay_time' => time(),
            'pay_state' => $pay_state
        );
        $this->add($data);
    }

    /**
     * @param string $fields 返回的数据库字段列表，英文逗号隔开，为空则取全部字段
     * @param string $where 查询条件，where字句，为空则取全部
     * @return array $account_list 账户明细列表
     * @deprecated 根据查询条件获得账户明细列表总条数
     * @author 姜伟
     * @todo 从账户日志表中取一定数量的账户明细，并以数组形式返回
     * */
    public function getAccountNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 获取活动规则信息
     * @param int $marketing_rule_id 活动规则id
     * @param string $fields 要获取的字段名
     * @return array 活动规则基本信息
     * @author 姜伟
     * @todo 根据where查询条件查找活动规则表中的相关数据并返回
     */
    public function getAccountInfo($where, $fields = '', $order = '')
    {
        return $this->field($fields)->where($where)->order($order)->find();
    }

    /**
     * @param string $fields 返回的数据库字段列表，英文逗号隔开，为空则取全部字段
     * @param string $where 查询条件，where字句，为空则取全部
     * @return array $account_list 账户明细列表
     * @deprecated 根据查询条件获得账户明细列表
     * @author 姜伟
     * @todo 从账户日志表中取一定数量的账户明细，并以数组形式返回
     * */
    public function getAccountList($fields = '', $where = '', $order = '', $limit = '', $group = '')
    {
        return $this->field($fields)->where($where)->limit()->order($order)->group($group)->select();
    }

    /**
     * @param int $user_id 用户ID
     * @return array $account_list
     * @author 姜伟
     * @deprecated 获得单个用户的账户明细列表
     * @todo 从账户日志表中取一定数量的账户明细，并以数组形式返回
     * */
    public function getAccountDetailByUserId($user_id)
    {
        $user_id = intval($user_id);
        if (!$user_id) {
            trigger_error(__CLASS__ . ', ' . __FUNCTION__ . '，无效的参数user_id');
        }

        //返回的字段列表
        $fields = 'change_type, amount_in, amount_out, amount_after_pay, amount_before_pay, order_id, operater, addtime, remark, proof, ip';

        //查询条件
        $where = 'user_id = ' . $user_id;

        return $this->getAccountList($fields, $where);
    }

    /**
     * @param void
     * @return array $account_list
     * @author 姜伟
     * @deprecated 获得所有用户的账户明细列表
     * @todo 从账户日志表中取一定数量的账户明细，并以数组形式返回
     * */
    public function getAccountDetailByUsers()
    {
        //返回的字段列表
        $fields = 'user_id, change_type, amount_in, amount_out, amount_after_pay, amount_before_pay, order_id, operater, addtime, remark, proof, ip';

        return $this->getAccountList($fields);
    }

    /**
     * @param void
     * @return void
     * @author 姜伟
     * @deprecated 用户在线充值
     * @todo 调用分销商模型的方法改变累计充值字段，发送邮件、短信等
     * */
    public function voucher()
    {
        require_once('Lib/Model/UserModel.class.php');
        $user_id = intval(session('user_id'));
        $user_obj = new UserModel($user_id);

        //充值
        trace($this->data['amount_after_pay'], 'wuzeguo_amount');
        $user_obj->setLeftMoney($this->data['amount_after_pay']);
    }

    /**
     * @param int $order_id 订单ID
     * @param string $proof 消费码
     * @return void
     * @deprecated 用预存款余额支付订单
     * @author 姜伟
     * @todo 调用订单模型的payOrder方法即可
     * @version 1.0
     * */
    public function payOrder($order_id, $proof)
    {
        if (!$order_id) return;
        //调用订单模型的payOrder方法
        $order_obj = new OrderModel($order_id);
        $payway = $order_obj->where('order_id=%d', intval($order_id))->getField('payway');
        $order_obj->payOrder($proof, $payway);
    }

    /**
     * @param void
     * @return void
     * @author yzp
     * @deprecated 个人操作存入银行
     * @version 1.0
     * */
    public function saveBank($amount)
    {
        $user_id = session('user_id');
        $user_obj = new UserModel($user_id);

        $user_info = $user_obj->getUserInfo('frozen_money');

        $amount = $user_info['frozen_money'] + $amount ?: 0;
        $user_obj->editUserInfo(array('frozen_money' => $amount));
    }

    /**
     * @param void
     * @return void
     * @author yzp
     * @deprecated 自动存入银行
     * @version 1.0
     * */
    public function AutoSaveBank($user_id, $amount)
    {
        $user_obj = new UserModel($user_id);

        $user_info = $user_obj->getUserInfo('frozen_money');

        $amount = $user_info['frozen_money'] + $amount ?: 0;
        $user_obj = new UserModel($user_id);
        $user_obj->editUserInfo(array('frozen_money' => $amount));
    }


    /**
     * @param void
     * @return void
     * @author yzp
     * @deprecated 经验换豆
     * @version 1.0
     * */
    public function saveExp($amount)
    {
        $user_id = session('user_id');
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('exp');

        $amount = $user_info['exp'] + $amount ?: 0;
        $user_obj = new UserModel($user_id);
        $user_obj->editUserInfo(array('exp' => $amount));
    }


    /**
     * @param void
     * @return void
     * @author 姜伟
     * @deprecated 管理员手工扣款
     * @todo 发送邮件、短信等
     * @version 1.0
     * */
    public function manualDeduct()
    {
    }

    /**
     * @param void
     * @return void
     * @author 姜伟
     * @deprecated 管理员手工录入
     * @todo 发送邮件、短信等
     * @version 1.0
     * */
    public function manualVoucher()
    {
    }

    /**
     * @param void
     * @return void
     * @author 姜伟
     * @deprecated 线上支付订单
     * @todo 线上支付订单，发送邮件、短信等
     * @version 1.0
     * */
    public function onlinePayOrder()
    {
        $this->payOrder();
    }

    /**
     * @param void
     * @return void
     * @author 姜伟
     * @deprecated 线下汇款充值
     * @todo 线下汇款充值，发送邮件、短信等
     * @version 1.0
     * */
    public function offlinePay()
    {
    }

    /**
     * @param string $trade_no 第三方支付平台返回的交易码
     * @return boolean 存在返回true，不存在返回false
     * @author 姜伟
     * @deprecated 查询第三方支付平台返回的交易码是否已存在于account表中
     * @todo 查询第三方支付平台返回的交易码是否已存在于account表中
     * @version 1.0
     * */
    public function checkPayCodeExists($trade_no)
    {
        $account_info = $this->field('account_id')->where('pay_code = "' . $trade_no . '"')->find();

        return $account_info ? true : false;
    }

    /**
     * 获取财务明细列表页数据信息列表
     * @param array $account_list
     * @return array $account_list
     * @author 姜伟
     * @todo 根据传入的$account_list获取更详细的财务明细列表页数据信息列表
     */
    public function getListData($account_list)
    {
        foreach ($account_list AS $k => $v) {
            //操作类型
            $change_type_list = self::getChangeTypeList();
            $change_type_name = $change_type_list[$v['change_type']];
            $account_list[$k]['change_type'] = $change_type_name;

            $change = $v['amount_in'];
            $type = 1;
            if ($v['amount_out'] > 0) {
                $change = $v['amount_out'];
                $type = 2;
            }

            $account_list[$k]['change'] = $change ?: 0;
            $account_list[$k]['type'] = $type;
            $account_list[$k]['addtime_str'] = date('Y-m-d H:i:s',$v['addtime']);
            unset($account_list[$k]['amount_in']);
            unset($account_list[$k]['amount_out']);

        }

        return $account_list;
    }

    /**
     * 获取财务变动类型列表
     * @param void
     * @return array $change_type_list
     * @author 姜伟
     * @todo 获取财务变动类型列表
     */
    public static function getNewChangeTypeList()
    {
        $arr = array(
            '1' => '领取救济',
            '2' => '领取排行榜奖励',
            '3' => '存入银行',
            '4' => '银行取出',
            '6' => '经验换豆',
            '7' => '兑换点卡',
            '9' => '充值',
            '10' => '提现',
            '12' => '游戏投注',
            '13' => '手动加金豆',
            '14' => '手动扣金豆',
            '15' => '提现申请',
            '16' => '拒绝提现申请',
            '17' => '游戏竞猜赢取',
            '18' => '推广新用户奖励',
            '35' => '退水',
        );

        $app_list = C('APP_LIST');
        foreach ($app_list AS $k => $v) {
            foreach ($v['account'] AS $key => $val) {
                $arr[$key] = $val;
            }
        }

        return $arr;
    }


    /**
     * 获取财务变动类型列表
     * @param void
     * @return array $change_type_list
     * @author 姜伟
     * @todo 获取财务变动类型列表
     */
    public static function getChangeTypeList()
    {
        $arr = array(
            '1' => '领取救济',
            '2' => '领取排行榜奖励',
            '3' => '存入银行',
            '4' => '银行取出',
            '5' => '发红包',
            '6' => '经验换豆',
            '7' => '兑换点卡',
            '8' => '领红包',
            '9' => '充值',
            '10' => '提现',
            '11' => '红包退回',
            '12' => '游戏投注',
            '13' => '手动加金豆',
            '14' => '手动扣金豆',
            '15' => '提现申请',
            '16' => '拒绝提现申请',
            '17' => '游戏竞猜赢取',
            '18' => '推广新用户奖励',
            '19' => '手动加余额',
            '20' => '手动扣余额',
            '21' => '代理充值支出',
            '22' => '金豆返还',
            '23' => '卡密回收',
            '26' => '领取每周亏损返利',
            '27' => '领取充值返利',
            '28' => '领取下线投注返利',
            '29' => '领取有效流水返利',
            '35' => '退水'
        );

        $app_list = C('APP_LIST');
        foreach ($app_list AS $k => $v) {
            foreach ($v['account'] AS $key => $val) {
                $arr[$key] = $val;
            }
        }

        return $arr;
    }

    /**
     * 根据订单ID获取支付信息
     * @param int $order_id
     * @return float
     * @author 姜伟
     * @todo 根据订单ID获取支付信息
     */
    public function getPayInfo($order_id)
    {
        //获取支付方式
        $order_obj = new OrderModel($order_id);
        $order_info = $order_obj->getOrderInfo('payway');

        //获取支付方式信息
        $payway_obj = new PaywayModel();
        $payway_info = $payway_obj->getPaywayInfo('payway_id = ' . $order_info['payway']);
        if (!$payway_info) {
            return false;
        }

        return $payway_info;
    }

    /**
     * @param string $where 查询条件，where字句，为空则取全部
     * @return float $amount
     * @author 姜伟
     * @deprecated 根据查询条件获得账户总和
     * @todo
     * */
    public function sumAccount($where = '')
    {
        return $this->where($where)->sum('amount_in');
    }

    //提现申请
    public function depositApply($user_id, $amount)
    {
        $user_obj = new UserModel($user_id);
        $user_info = $user_obj->getUserInfo('frozen_money');
        $user_obj->setUserInfo(array(
            'frozen_money' => $user_info['frozen_money'] + $amount
        ));
        $user_obj->saveUserInfo();
    }

    /**
     * 获取今日是否领取救济信息
     * @param int $login_log_id 今日是否领取救济id
     * @param string $fields 要获取的字段名
     * @return array 今日是否领取救济基本信息
     * @todo 根据where查询条件查找今日是否领取救济表中的相关数据并返回
     */
    public function getDayReliveInfo($andwhere, $change_type)
    {

        $begin_time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $end_time = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;

        $where = 'change_type =' . $change_type . ' AND addtime >=' . $begin_time . ' AND addtime <=' . $end_time;

        $where = $where . $andwhere;

        return $this->field('account_id')->where($where)->find();
    }

    public function getListDataName($account_list)
    {
        foreach ($account_list as $k => $v) {

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('realname,id', 'user_id =' . $v['user_id']);
            if ($user_info) {
                $account_list[$k]['nickname'] = $user_info['realname'];
                $account_list[$k]['id'] = $user_info['id'];
            }
            $account_list[$k]['amount_out'] = feeHandle($v['amount_out']);

        }
        return $account_list;
    }
}

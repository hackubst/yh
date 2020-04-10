<?php
class TicketModel extends Model
{

    // 常量定义
    const TABLE_NAME  = 'ticket';
    const PRIMARY_KEY = 'ticket_id';
    
    /**
     * 构造函数
     * @author wsq
     * @param $id 
     * @return void
     * @todo 初始化主键
     */
    public function TicketModel()
    {
        parent::__construct(self::TABLE_NAME);
    }

    // auto complete
    protected $_auto = array(
        array('isuse','1', Model::MODEL_BOTH),
        array('period_of_validity','strtotime', Model::MODEL_BOTH, 'function'),
    );

    // validate
    protected $_validate = array(
        //array('password','checkPwd','密码格式不正确',0,'function'),
    );


    // map
    protected $_map = array(
        'VoucherCode' => 'voucher_code',
        'ParValue' =>  'value',
        'PeriodOfValidity' => 'period_of_validity',
        'VoucherState' => 'status',
        'IsReUse' => 'is_reuse',
        'UsedMoney' => 'used_money',
        'OnlyShow' => 'is_only_show',
    );

    /**
     * 修改
     * @author wsq
     * @param array $arr 信息数组
     * @return boolean 操作结果
     * @todo 修改信息
     */
    public function editRecord($arr, $id=false)
    {
        $opt = $id ? Model::MODEL_UPDATE : Model::MODEL_INSERT;

        if (!is_array($arr)) {return false;} //参数不合法
        if ($this->create($arr, $opt)) return ($id ? $this->where(self::PRIMARY_KEY . ' = %d ', $id)->save() : $this->add()); 
        else return false; //数据获取出错
    }


    /**
     * 删除
     * @author wsq
     * @param int $id 
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delRecord($id, $delete=true)
    {
        if (!is_numeric($id)) return false;
        if ($delete) return $this->where(self::PRIMARY_KEY . ' = %d', $id)->delete();  //真删除
        else return $this->where(self::PRIMARY_KEY . ' = %d', $id)->save(array('isuse' => 2)); //假删除
    }

    /**
     * 获取列表页数据信息列表
     * @author wsq
     * @param array $list
     * @return array $list
     * @todo 根据传入的$friend_list获取更详细的列表页数据信息列表
     */
    public function getListData($list)
    {
        foreach ($list AS $k => $v) {
            //数据同步
            //$info = $this->getERPMemberInfoByMemberCardIDOrMobile($v['card_code']); 
            //if ($info['Sucess']) {
            //    $data = $info['Data'][0];
            //    $this->syncMemberCardInfo($data);
            //} 
        }
        return $list;
    }
    
    // 同步卡片信息到数据库
    // @author wsq
    public function syncMemberCardInfo($data)
    {
        $id = $this->where('card_code ="%s"', $data['CardCode'])->getField(self::PRIMARY_KEY); 
        $status =  $this->editRecord($data, $id);
        return $status;
    }

    // 获取用户可以支付的卡
    // @author wsq
    public function getVoucherList($user_id) 
    {
        return $this->where('status = 2 AND user_id = %d', $user_id)->select();
    }

    // 获取ERP 商品信息
    // @author wsq
    public function getERPTicketInfo($user_id)
    {
        $card_id_list = D('MemberCard')->where('user_id=%d', intval($user_id))->getField('card_code', true);
        $data = array();
        foreach ($card_id_list AS $k => $v) {
            $stats = array();
            $status = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_GET_TICKET_LIST,
                    'msg' => json_encode(
                        array(
                            'BindUserCode'=> $user_id,
                            'CardCode' => $v,
                        )

                    ),
                )
            );
            if ($status['Sucess']) {
                // 同步卡片信息
                $status['Data'][0]['card_code'] = $v;
                foreach ($status['Data'] AS $key => $value) {
                    $value['card_code'] = $v;
                    array_push($data, $value);
                }
            }
        }

        if (count($data)) return $data;

        return NULL;
    }

    // 获取券列表
    // @author wsq
    public function getTicketList($fields = '', $where = '', $order = '', $limit = '', $groupby = '')
    {
		return $this->field($fields)->where($where)->group($groupby)->order($order)->limit()->select();
    }

    /**
     * 处理数据
     * @author wzg
     */
    public function getDataList($ticket_list)
    {
        foreach ($ticket_list AS $k => $v) {
            $ticket_list[$k]['left_money'] = $v['value'] - $v['used_money'];
        }
        return $ticket_list;
    }

    // 同步券信息
    // @author wsq
    public function syncTicketInfo($user_id)
    {
        $data = $this->getERPTicketInfo($user_id);
        if ($data) {
            $this->where('user_id=%d', intval($user_id))->delete();
            foreach ($data AS $k => $v) {
                $v['user_id'] = $user_id;
                $this->editRecord($v);
            }
            return true;
        }

        return false;
    }

    /**
     * 获取券信息
     * todo调用券接口
     * @param voucher_code string 券号码
     * @author wzg
     */
    public function getTicketInfo($voucher_code)
    {
        if (!$voucher_code)
        {
            return false;
        }
        $data = array();

        $status = D('Connection')->getResult(
            array(
                'type' => ConnectionModel::OP_GET_TICKET_INFO,
                'msg' => json_encode(
                    array(
                        'VoucherCode'=> $voucher_code,
                    )

                ),
            )
        );       

        if ($status['Sucess']) {
            // 券信息
            //foreach ($status['Data'] AS $k => $v) array_push($data, $v);

            return $status['Data'][0];
        }
        return false;
    }


    /**
     * 验证券信息， 返回可用金额
     * @todo:验证有效期、可用面值、状态、是否展示
     * @param $ticket_info array 券信息
     * @param $data_from  默认false来源于数据库
     * @return true_money
     * @author wzg
     */
    public function validTicketInfo($ticket_info, $data_from=false)
    {
        trace($ticket_info);
        return $ticket_info['ParValue'];
        if (!$ticket_info) {
            return false;
        }
        if ($data_from) {
            $ticket_info = $this->create($ticket_info);
        }
        //是否过期
        $valid_data = strtotime($ticket_info['period_of_validity']);
        if ($valid_data < time()) {
            return false;
        }
        //是否只是展示
        if ($ticket_info['is_only_show'] == 1) {
            return false;
        }

        //券状态
        if ($ticket_info['status'] != 2 ) {
            return false;
        }
        //是否多次使用，返回其剩余金额
        if ($ticket_info['is_reuse'] == 0) 
        {
            $true_money = $ticket_info['value'];
        } else {
            $true_money = $ticket_info['value'] - $ticket_info['used_money'];
        }

        return $true_money;
    }

    /**
     * 券支付
     * todo:调用接口进行付款
     * @author wzg
     */
    public function ticketPay($card_code, $voucher_code, $money, $order_id, $user_id)
    {
        if (!$card_code || !$voucher_code || !$money || !$order_id) {
            return false;
        }
        $order_sn = D('Order')->where('order_id = %d', intval($order_id))->getField('order_sn');

        $status = D('Connection')->getResult(
            array(
                'type' => ConnectionModel::OP_TICKET_PAY,
                'msg' => json_encode(
                    array(
                        'CardCode'=> $card_code,
                        'VoucherCode' => $voucher_code,
                        'Money' => $money, 
                        'BillNumber' => $order_sn,
                    )

                ),
            )
        );       

        if ($status['Sucess']) {
            return true;
        } else {
            return false;
        }
    }
}

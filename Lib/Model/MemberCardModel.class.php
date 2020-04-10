<?php
class MemberCardModel extends Model
{

    // 常量定义
    const TABLE_NAME  = 'member_card';
    const PRIMARY_KEY = 'member_card_id';
    
    /**
     * 构造函数
     * @author wsq
     * @param $id 
     * @return void
     * @todo 初始化主键
     */
    public function MemberCardModel()
    {
        parent::__construct(self::TABLE_NAME);
    }

    // auto complete
    protected $_auto = array(
        array('isuse','1', Model::MODEL_BOTH),
        array('addtime','time', Model::MODEL_BOTH, 'function'),
    );

    // validate
    protected $_validate = array(
        //array('password','checkPwd','密码格式不正确',0,'function'),
    );


    // map
    /*
     CardCode 卡号
     MemberName 会员名字
     CardLevel 卡级别
     IsIntegral 是否有积分功能，1是 0否
     IsDeposited 是否有储值功能，1是 0否，为1时能做为支付对象
     CurrentIntegral 当前积分
     PeriodOfValidity 卡有效期，格式YYYYMMDD
     CurrentBalance 当前余额
Provice
省
City
市
County
区/县
Comm
街道/乡镇
HomeAddress
详细地址
MemRecruiter
会员招募人名字
     
     */
    //
    protected $_map = array(
        'CardCode' => 'card_code',
        'CardLevel' =>  'card_level',
        'IsIntegral' => 'is_integral',
        'IsDeposited' => 'is_deposited',
        'CurrentBalance' => 'current_balance',
        'CurrentIntegral' => 'current_integral',
        'PeriodOfValidity' => 'period_of_validity',
        'BindUserCode' => 'user_id',
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
            $info = $this->getERPMemberInfoByMemberCardIDOrMobile($v['card_code']); 
            if ($info['Sucess']) {
                $data = $info['Data'][0];
                $this->syncMemberCardInfo($data);
            } 
        }
        return $list;
    }
    

	// 会员信息查询（类型编码00）
    // 根据卡号查询余额
    // @author wsq
    // 0005649404D80F351403939047361B5E9C8C800{"KeyWord":"66370001"}
    public function getERPMemberInfoByMemberCardIDOrMobile($id)
    {
        $info = D('Connection')->getResult(
            array(
                'type' => ConnectionModel::OP_CARD_QUERY,
                'msg' => json_encode(array('KeyWord' => $id)),
            )
        );
        return $info;
        if ($info['Sucess']) return $info;
        return NULL;
    }

    // 根据userID 获取ERP内的会员信息
    // @author wsq
    public function getERPMemberInfoByUserID($user_id)
    {
        $id = D('Users')->where('user_id=%d', $user_id)->getField('member_card_id');
        if ($id) {
            $info = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_CARD_QUERY,
                    'msg' => json_encode(array('KeyWord' => $id)),
                )
            );
            if ($info['Sucess']) return $info;
        }
        return NULL;
    }

    // 为ERP系统创建会员账户, 同步创建
    // {"MemberName":"TFBOYZ","Sex":0,"BirthDay":"","CertificateCode":null,"HomeAddress":null,"MobilePhone":"1333333","Mail":null}
    // @author wsq
    public function createNewMemberForERPSystemByUserID($user_id=0)
    {
        $user_info = D('Users')->where('user_id=%d', $user_id)->find();
        if ($user_info) {
            $Provice = D('AddressProvince')->where('province_id=%d', intval($user_info['province_id']))->getField('province_name');
            $City = D('AddressCity')->where('city_id=%d', intval($user_info['city_id']))->getField('city_name');
            $County = D('AddressArea')->where('area_id=%d', intval($user_info['area_id']))->getField('area_name');
            $Comm = D('Street')->where('street_id=%d', intval($user_info['street']))->getField('street_name');
            $Comm = $Comm ? $Comm : "";
            $MemRecruiter = D('User')->where('user_id=%d', intval($user_info['parent_id']))->getField('realname');
            $MemRecruiter = $MemRecruiter ? $MemRecruiter : "";
            $BirthDay = $user_info['birthday']?date('Ymd', $user_info['birthday']):"";
            $info = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_CREATE_MEMBER,
                    'msg' => json_encode(
                        array(
                            'MemberName' => $user_info['realname']?$user_info['realname']:$user_info['nickname'],
                            'Sex' => $user_info['sex']?1:0,
                            'BirthDay' => $BirthDay,
                            'CertificateCode' => "",
                            'HomeAddress' => "",
                            'MobilePhone' => $user_info['mobile'],
                            //'MobilePhone' => "",
                            'Mail' => $user_info['email'] ? $user_info['email'] : "",
                            'BindUserCode' => $user_id,
                            'Provice' => $Provice,
                            'City' => $City,
                            'County' => $County,
                            'Comm' => $Comm,
                            'HomeAddress' => $user_info['address'],
                            'MemRecruiter' => $MemRecruiter, //todo: 会员招募
                            //'BindUserCode' => "",
                        )
                    ),
                )
            );
            // 卡号与用户绑定
            // todo: 卡号独立抽象出来
            if ($info['Data'][0]['CardCode']) {
                $data = $info['Data'][0];
                $this->syncMemberCardInfo($data);
                return true;
            } else {
                return false;
            }
        }
    }

    // 使用会员卡消费
    //CardCode 会员编码
    //Money 金额，必须大于0
    //BindUserCode 绑定的用户唯一ID（接口调用方的用户ID），卡支付前须先绑定。
    // @author wsq
    public function payByCard($user_id, $card_id, $order_id, $pay_amount)
    {

        trace($user_id.';'.$card_id.';'.$order_id, '-------- pay card------');
        if ($user_id && $order_id && $card_id) { 
            //$card_id = D('Users')->where('user_id=%d', $user_id)->getField('member_card_id');
            $member_card_primary_key = D('MemberCard')->where('card_code="%s" AND user_id = %d', $card_id, $user_id)->getField(MemberCardModel::PRIMARY_KEY);
            if (!$card_id || !$member_card_primary_key) return false;

            $order_info = D('Order')->where('order_id = %d', intval($order_id))->field('order_sn, pay_amount')->find();

            //如果只是付一部分钱，那应用$pay_amount,否则用$order_info['pay_amount']
            $pay_amount = $pay_amount ? $pay_amount : $order_info['pay_amount'];

            $status = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_PAY,
                    'msg' => json_encode(
                        array(
                            'CardCode'=> $card_id,
                            'Money' => $pay_amount,
                            'BindUserCode' => $user_id,
                            'BillNumber' => $order_info['order_sn'],
                        )

                    ),
                )
            );

            if ($status['Sucess']) {
                // todo: 写日志
                D('CardPayLog')->editRecord(
                    array(
                        'user_id' => $user_id,
                        'order_id' => $order_id,
                        'sales_date' => $status['Data'][0]['SalesDate'],
                        'pos_no' => $status['Data'][0]['PosNo'],
                        'center_water_no' => $status['Data'][0]['CenterWaterNo'],
                        'sales_bill_no' => $status['Data'][0]['SalesBillNo'],
                        'trading_money' => $status['Data'][0]['TradingMoney'],
                        'card_code'=> $card_id,
                        'addtime' => NOW_TIME,
                    )
                );

                // 同步卡余额信息
                $info = $this->getERPMemberInfoByMemberCardIDOrMobile($card_id); 
                if ($info['Sucess']) {
                    $bind_user_id = $info['Data'][0]['BindUserCode'];
                    if ($bind_user_id == $user_id) {
                        $data = $info['Data'][0];
                        $this->syncMemberCardInfo($data);
                    }
                } 

                return $status['Data'][0]['CenterWaterNo'];

            } else {
                // todo: 支付不成功的处理流程
                // 查询ERP系统订单是否已经支付
                $water_no = D('CardPayLog')->where('order_id=%d', $order_id)->getField('center_water_no');
                return $water_no ? $water_no : false;
            }
        }

        return false;
    }

    // 绑定会员卡功能
    // 卡绑定（类型编码13
    // CardCode 会员编码
    // MobilePhone 绑定的手机号码，与ERP登记的手机号一致才能绑定
    // BindUserCode 绑定的用户唯一ID（接口调用方的用户ID），卡支付前须先绑定。
    // @author wsq
    public function bindMemberCard($user_id, $card_id)
    {
        if ($user_id && $card_id) { 
            //if (!$card_id) return false;
            $mobile = D('Users')->where('user_id=%d', $user_id)->getField('mobile');
            $status = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_BIND_CARD,
                    'msg' => json_encode(
                        array(
                            'CardCode'=> $card_id,
                            'MobilePhone' => $mobile,
                            'BindUserCode' => $user_id,
                        )

                    ),
                )
            );
            if ($status['Sucess']) {
                // 同步卡片信息
                $data = $status['Data'][0];
                $this->syncMemberCardInfo($data);
                return true;
            } else {
                // 再次查询卡片是否已经绑定
                // 如果绑定用户和当前用户一致，则更新当前用户
                $info = $this->getERPMemberInfoByMemberCardIDOrMobile($card_id); 
                if ($info['Sucess']) {
                    $bind_user_id = $info['Data'][0]['BindUserCode'];
                    if ($bind_user_id == $user_id) {
                        $data = $info['Data'][0];
                        $this->syncMemberCardInfo($data);
                        return "绑定成功";
                    }
                } 
            }
        }

        return "绑定失败,请稍后再试!";
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
    public function getPayCardList($user_id) 
    {
        return $this->where('is_deposited = 1 AND user_id = %d AND isuse = 1', $user_id)->select();
    }


    /**
     * 退款时原路退回
     * @todo 调用撤消卡支付接口
     * @param string $car_code
     * @param int $order_id
     * @param int $user_id
     * @param float $amount 退回金额
     * @param string $center_water_no 交易码
     * @param string $type 06
     * @author wzg
     */
    public function refund($order_id, $user_id, $amount =0.00)
    {
        if (!$order_id || !$user_id) {
            return false;
        }
        //根据order_id取出参数
        try{
            $card_pay_info = D('CardPayLog')->where('order_id = ' . $order_id . ' AND user_id = ' . $user_id)->find();   
        }
		catch (Exception $e)
        {
            return false;
        }

        //判断user_id 是否为绑定卡id 
        $member_car_obj = D('MemberCard');
        $is_mem_user = $member_car_obj->where('card_code = ' . $card_pay_info['card_code'] . ' AND user_id = ' . $user_id)->count();
        if (!$is_mem_user) {
            return false;
        }

        //金额不得大于交易时金额
        //if ($amount > $car_pay_info['trading_money']) {
        //    return false;
        //}

        $status = D('Connection')->getResult(
                array(
                    'type' => ConnectionModel::OP_REFUND,
                    'msg' => json_encode(
                        array(
                            'CardCode'           => $card_pay_info['card_code'],
                            'Money'              => -1*$card_pay_info['trading_money'],
                            'OriginalTradDate'   => $card_pay_info['sales_date'],
                            'OriginalTradBillNo' => $card_pay_info['sales_bill_no'],
                            'OriginalTradPosNo'  => $card_pay_info['pos_no'],
                            'CenterWaterNo'      => $card_pay_info['center_water_no'],
                            'BindUserCode'       => $card_pay_info['user_id'],
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

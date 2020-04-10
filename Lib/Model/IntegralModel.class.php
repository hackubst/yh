<?php
/**
 * 积分模型类
 */

class IntegralModel extends Model
{
    // 积分id
    public $integral_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $integral_id 积分ID
     * @return void
     * @todo 初始化积分id
     */
    public function IntegralModel($integral_id)
    {
        parent::__construct('integral');

        if ($integral_id = intval($integral_id))
		{
            $this->integral_id = $integral_id;
		}
    }


    const INTEGRAL_MONEY_COST = 1;//积分商城消费
    const INTEGRAL_RETURN =2; //现金消费积分返还
    const ORDER_REFOUND =3; //积分退款
    /**
     * 获取积分信息
     * @author 姜伟
     * @param int $integral_id 积分id
     * @param string $fields 要获取的字段名
     * @return array 积分基本信息
     * @todo 根据where查询条件查找积分表中的相关数据并返回
     */
    public function getIntegralInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改积分信息
     * @author 姜伟
     * @param array $arr 积分信息数组
     * @return boolean 操作结果
     * @todo 修改积分信息
     */
    public function editIntegral($arr)
    {
        return $this->where('integral_id = ' . $this->integral_id)->save($arr);
    }
 
    /** 
	 * 添加积分变动信息
     * @author 姜伟
     * @param string $user_id 用户ID
     * @param int $change_type 变动类型（变动类型，1充值购买，2订单赠送，3积分兑换，4系统赠送，5活动获得，6订单支付抵扣，7订单退款）
     * @param float $integral 变动金额(正负数）
     * @param string $remark 管理员备注，线上充值时，该参数为第三方支付平台返回的交易码
     * @param int $id 若是活动获得，则为活动ID，若是订单相关，则为订单ID
     * @return float $integral_after_pay 余额不足时返回-1
     * @todo 1、调用用户模型的getLeftIntegral方法获取变动前余额integral_before_pay；2、计算变动后$integral_after_pay = $integral_before_pay + $integral; 3、若$integral_after_pay小于0，返回-1退出，否则调用用户模型的setLeftIntegral方法修改用户积分余额；4、将积分变动信息写入到积分变动日志表integral中；5、返回变动后的积分余额 $integral_after_pay
     * */
    public function addIntegral($user_id, $change_type, $integral, $remark = '', $id = 0, $proof)
    {
		/*判断积分余额是否足够*/
		//调用IntegralModel的getLeftIntegral方法获取积分余额
		$user_obj = new UserModel($user_id);
		//变动前的余额
		$integral_before_pay = 0;
		$total_integral_before_pay = 0;
		$user_info = $user_obj->getUserInfo('');
		#$role_type = $user_info['role_type'];
		// if ($role_type == 2)
		// {
		// 	$merchant_obj = new MerchantModel($user_id);
		// 	$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $user_id, 'integral, total_integral');
		// 	$integral_before_pay = $merchant_info['integral'];
		// 	$total_integral_before_pay = $merchant_info['total_integral'];
		// }
		#if ($role_type == 3)
		#{
			// $user_obj = new UserModel($user_id);
			$user_info = $user_obj->getUserInfo();
            // dump($user_info);die;
			$integral_before_pay = $user_info['left_integral'];
			$total_integral_before_pay = $user_info['total_integral'];
		#}
		//变动后的余额
		$integral_after_pay = $integral_before_pay + $integral;  

		//若余额不足，返回-1
		if ($integral_after_pay < 0)
		{
			return -1;
		}

		//保存余额
		$arr = array(
			'left_integral'	=> $integral_after_pay,
		);
		if ($integral > 0)
		{
			$arr['total_integral'] = $user_info['total_integral'] + $integral;
		}
        if($integral < 0){
            $integral = $integral * -1;
        }
		// if ($role_type == 2)
		// {
		// 	$merchant_obj->editMerchant($arr);
		// }
		#if ($role_type == 3)
		#{
           // $user_obj->setUserInfo($arr);
			//$user_obj->saveUserInfo();
            // echo $user_obj->getLastSql();die;
		#}
		
		/*写账户变动日志begin*/
		//组成数组
    	$arr['user_id'] = $user_id;
    	#$arr['user_type'] = $role_type;
    	$arr['change_type'] = $change_type;
    	$arr['integral'] = intval($integral);
    	$arr['start_integral'] = $integral_before_pay;
    	$arr['end_integral'] = $integral_after_pay;
    	$arr['id'] = $id;
    	$arr['operater'] = intval(session('user_id'));
    	$arr['addtime'] = time();
    	$arr['remark'] = $remark;
    	$arr['ip'] = get_client_ip();
	if ($proof)
	{
		$arr['pay_code'] = $proof;
	}

		//执行驱动事件
         switch ($change_type)
         {
            case self::INTEGRAL_MONEY_COST :
                $data = array(
                    'left_integral' => $integral_after_pay
                    );
                $user_obj->editUserInfo($data);    
                break;
            case self::INTEGRAL_RETURN :
                $data = array(
                    'total_integral' => $arr['total_integral'],
                    'left_integral' => $integral_after_pay
                    );
                $user_obj->editUserInfo($data); 
                break;
            case self::ORDER_REFOUND :
                $data = array(
                    'left_integral' => $integral_after_pay
                    );
                $user_obj->editUserInfo($data); 
                break;
               
            default :
                return 0;
                break;
         }


		//保存到数据库
		$this->add($arr);
            // echo $this->getLastSql();die;
		/*写账户变动日志end*/

    	return $integral_after_pay;
    }

    /**
     * 删除积分
     * @author 姜伟
     * @param int $integral_id 积分ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delIntegral($integral_id)
    {
        if (!is_numeric($integral_id)) return false;
		return $this->where('integral_id = ' . $integral_id)->delete();
    }

    /**
     * 根据where子句获取积分数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的积分数量
     * @todo 根据where子句获取积分数量
     */
    public function getIntegralNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询积分信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 积分基本信息
     * @todo 根据SQL查询字句查询积分信息
     */
    public function getIntegralList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取用户积分变动类型列表
     * @author 姜伟
     * @param void
     * @return array $change_type_list
     * @todo 获取用户积分变动类型列表
     */
    public static function getChangeTypeList()
    {
        // $change_sort_list = D('integralChangeSort')->getSortList('isuse = 1');
        $change_sort_list = D('IntegralChangeSort')->getSortList();
        foreach ($change_sort_list as $key => $value) {
            //获取一级分类名称
            $class_name = D('integralChangeClass')->where('integral_change_class_id = ' . $value['class_id'])->getField('class_name');
            // echo D('integralChangeClass')->getLastSql();
            if ($class_name) {
                $change_type_list[$value['integral_change_sort_id']] =  $class_name . '/' . $value['sort_name'];
            }
            else {
                $change_type_list[$value['integral_change_sort_id']] =  $value['sort_name'];
            }
        }

        return $change_type_list;
		// return array(
		// 	'1'	=> '充值购买',
		// 	'2'	=> '订单赠送',
		// 	'3'	=> '积分兑换',
		// 	'4'	=> '系统赠送',
		// 	'5'	=> '活动获得',
		// 	'6'	=> '订单支付抵扣',
		// 	'7'	=> '订单退款',
		// );
    }

    /**
     * 获取积分列表页数据信息列表
     * @author 姜伟
     * @param array $integral_list
     * @return array $integral_list
     * @todo 根据传入的$integral_list获取更详细的积分列表页数据信息列表
     */
    public function getListData($integral_list)
    {
		foreach ($integral_list AS $k => $v)
		{
			//操作类型
            $change_type_list = self::getChangeTypeList();
			// $change_type_list = D('IntegralChangeClass')->getClassList();
            // dump($change_type_list);die;
			$change_type_name = $change_type_list[$v['change_type']];
			$integral_list[$k]['change_type_name'] = $change_type_name;

			//用户名称
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname, realname');
            // $username = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
			$username = $user_info['nickname'];
			$integral_list[$k]['username'] = $username;
            //订单信息
            $order_obj = new OrderModel($v['id']);
            $order_sn  = '--';
            try {
                $order_info = $order_obj->getOrderInfo('order_sn', '');
                $order_sn  = $order_info['order_sn'] ? $order_info['order_sn'] : "--";
            } catch (Exception $e) {}

			$integral_list[$k]['order_sn'] = $order_sn;
		}

		return $integral_list;
    }
 
     /** 
     * @author 姜伟
     * @deprecated 查询第三方支付平台返回的交易码是否已存在于account表中
     * @param string $trade_no 第三方支付平台返回的交易码
     * @return boolean 存在返回true，不存在返回false
     * @todo 查询第三方支付平台返回的交易码是否已存在于account表中
     * @version 1.0
     * */
    public function checkPayCodeExists($trade_no)
    {
		$integral_info = $this->field('integral_id')->where('pay_code = "' . $trade_no . '"')->find();

		return $integral_info ? true : false;
    }

     /**
     * 积分变动数量积分转文字
     * @param  [type] $change_type [description]
     * @return [type]              [description]
     */
    public function integralChangeType($change_type){
        switch ($change_type) {
            case self::INTEGRAL_MONEY_COST :
                $change_type = '积分商城消费';
                break;
            
            case self::INTEGRAL_RETURN :
                $change_type = '现金消费积分返还';
                break;
            case self::ORDER_REFOUND :
                $change_type = '用户退款';
                break;
             
            default:
               $change_type = '';
                break;
        }
        return $change_type;
    }
}

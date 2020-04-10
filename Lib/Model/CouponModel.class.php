<?php
/**
 * 优惠券模型类
 */

class CouponModel extends BaseModel
{
    // 优惠券id
    public $coupon_id;
   
    /**
     * 构造函数
     * @author zlf
     * @param $coupon_id 优惠券ID
     * @return void
     * @todo 初始化优惠券id
     */
    public function CouponModel($coupon_id)
    {
        parent::__construct('coupon');

        if ($coupon_id = intval($coupon_id))
		{
            $this->coupon_id = $coupon_id;
		}
    }

    /**
     * 获取优惠券信息
     * @author zlf
     * @param int $coupon_id 优惠券id
     * @param string $fields 要获取的字段名
     * @return array 优惠券基本信息
     * @todo 根据where查询条件查找优惠券表中的相关数据并返回
     */
    public function getCouponInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改优惠券信息
     * @author zlf
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 修改优惠券信息
     */
    public function editCoupon($arr)
    {
        return $this->where('coupon_id = ' . $this->coupon_id)->save($arr);
    }

    /**
     * 添加优惠券
     * @author zlf
     * @param array $arr 优惠券信息数组
     * @return boolean 操作结果
     * @todo 添加优惠券
     */
    public function addCoupon($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $coupon_id = $this->add($arr);

		if ($arr['activity_type'] != 2)
		{
			//非新用户关注类型的红包，须推送微信用户
			$msg = array(
				'first'				=> '您获得一张优惠券',
				'orderTicketStore'	=> '购买非虚拟商品商铺，可抵用镖金' . $arr['num'] . '元',
				'orderTicketRule'	=> '有效期：即刻起至' . date('Y-m-d H:i:s', $arr['deadline']),
				'url'				=> C('DOMAIN') . '/FrontShare/get_coupon_list/coupon_id/'. $coupon_id,
			);

			//$success = PushModel::wxPush($arr['user_id'], 'coupon', $msg);
		}
		return $coupon_id;
    }

    /**
     * 删除优惠券
     * @author zlf
     * @param int $coupon_id 优惠券ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delCoupon($coupon_id)
    {
        if (!is_numeric($coupon_id)) return false;
		return $this->where('coupon_id = ' . $coupon_id)->delete();
    }

    /**
     * 根据where子句获取优惠券数量
     * @author zlf
     * @param string|array $where where子句
     * @return int 满足条件的优惠券数量
     * @todo 根据where子句获取优惠券数量
     */
    public function getCouponNum($where = '')
    {
        return $this->where($where)->count();
    }
   
    /**
     * 根据where子句查询优惠券信息
     * @author zlf
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 优惠券基本信息
     * @todo 根据SQL查询字句查询优惠券信息
     */
    public function getCouponList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取优惠券状态列表
     * @author zlf
     * @param void
     * @return array $state_list
     * @todo 获取优惠券状态列表
     */
    public static function getStateList()
    {
		return array(
			'0'	=> '未使用',
            '1' => '已使用',
			'2'	=> '已过期',
		);
    }

    /**
     * 获取优惠券列表页数据信息列表
     * @author zlf
     * @param array $coupon_list
     * @return array $coupon_list
     * @todo 根据传入的$coupon_list获取更详细的优惠券列表页数据信息列表
     */
    public function getListData($coupon_list)
    {
		foreach ($coupon_list AS $k => $v)
		{
			//状态名称
			$state_list = self::getStateList();
			$coupon_list[$k]['state_name'] = $state_list[$v['state']];
            //是否可用
            if (isset($v['deadline']))
            {
                $r_time   = $v['deadline'] - strtotime('now');              
                $day_left = (int)($r_time / (24*60*60));
                $coupon_list[$k]['day_left'] = $day_left > 0 ? $day_left : 0;

                if ($r_time > 0) {
                    $coupon_list[$k]['usable'] = '可用';
                }else{
                    $coupon_list[$k]['usable'] = '已过期';
                }                
            }
            if($v['state'] != 0){
                $coupon_list[$k]['usable'] = '不可用';
            }
            //获取订单编号
            $order_obj = new OrderModel();
            if($v['order_id']){
                $order_info = $order_obj->getOrderByInfo('order_sn', 'order_id = ' . $v['order_id']);
                $coupon_list[$k]['order_sn'] = $order_info['order_sn'] ? $order_info['order_sn'] : '--';
            } else {
                $coupon_list[$k]['order_sn'] = '--';
            }
            
			//领取者姓名
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('realname, nickname');
			$coupon_list[$k]['realname'] = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];

			//分享者姓名
			/*$freight_activity_obj = new FreightActivityModel();
			$freight_activity_info = $freight_activity_obj->getFreightActivityInfo('freight_activity_id = ' . $v['freight_activity_id']);
			$user_obj = new UserModel($freight_activity_info['user_id']);
			$user_info = $user_obj->getUserInfo('realname, nickname');
			$coupon_list[$k]['share_user_realname'] = $user_info['realname'] ? $user_info['realname'] : $user_info['nickname'];
			//缩略图
			$freight_activity_rule_obj = new FreightActivityRuleModel();
			$freight_activity_rule_info = $freight_activity_rule_obj->getFreightActivityRuleInfo('freight_activity_rule_id = ' . $freight_activity_info['freight_activity_rule_id'], 'activity_small_pic');
			$coupon_list[$k]['activity_small_pic'] = $freight_activity_rule_info['activity_small_pic'];*/
		}
		return $coupon_list;
    }

    /**
     * 打开好友分享的红包，获取一张优惠券，并保存
     * @author zlf
     * @param int $freight_activity_id
     * @return int $num 优惠券面额，0表示已被抢完，-1表示已抢过，-2镖师活动已过期
     * @todo 打开好友分享的红包，获取一张优惠券，并保存
     */
    public function getCoupon($freight_activity_id)
    {
		//获取分享活动信息
		$freight_activity_obj = new FreightActivityModel();
		$freight_activity_info = $freight_activity_obj->getFreightActivityInfo('freight_activity_id = ' . $freight_activity_id, 'valid_day_num, deadline');
		if (!$freight_activity_info || $freight_activity_info['deadline'] < time())
		{
			//活动不存在或活动已过期
			return -2;
		}

		$user_id = intval(session('user_id'));

		//是否已领取
		$coupon_obj = new CouponModel();
		$coupon_num = $coupon_obj->getCouponNum('freight_activity_id = ' . $freight_activity_id . ' AND user_id = ' . $user_id);
		if ($coupon_num)
		{
			return -1;
		}

		//增加一张优惠券
		$num = $freight_activity_obj->getCouponNum($freight_activity_id);
		#var_dump($num);
		if ($num == 0)
		{
			//已被抢光
			return 0;
		}

		$arr = array(
			'user_id'		=> $user_id,
			'activity_type'	=> 1,
			'freight_activity_id'	=> $freight_activity_id,
			'addtime'		=> time(),
			'deadline'		=> time() + $freight_activity_info['valid_day_num'] * 86400,
			'num'			=> $num,
		);
		$coupon_id = $this->addCoupon($arr);

		return $coupon_id;
	}

    /**
     * 根据镖金数量获取最优优惠券
     * @author zlf
     * @param int $freight
     * @param array $coupon_list 优惠券列表，面额从大到小，且按到期时间升序排列
     * @return int $coupon_id 0镖师无优惠券
     * @todo 根据镖金数量获取最优优惠券
     */
    public function getBestCoupon($freight, $coupon_list)
    {
		$coupon_id = 0;
		foreach ($coupon_list AS $k => $v)
		{
			if ($v['num'] <= $freight)
			{
				$coupon_id = $v['coupon_id'];
				break;
			}
			elseif ($v['num'] > $freight)
			{
				$coupon_id = $v['coupon_id'];
			}
		}
		return $coupon_id;
	}

    /**
     * 使用优惠券
     * @author zlf
     * @param int $coupon_id
     * @param int $merge_pay_id
     * @param int $freight_discount
     * @return int $result 0失败，1成功
     * @todo 使用优惠券
     */
    public function useCoupon($coupon_id, $merge_pay_id, $freight_discount)
	{
		$coupon_info = $this->getCouponInfo('coupon_id = ' . $coupon_id, 'state, deadline');
		if (!$coupon_info || $coupon_info['state'] == 1 || $coupon_info['deadline'] < time())
		{
			//不存在或已使用或已过期
			return 0;
		}

		//使用之
		$arr = array(
			'state'				=> 1,
			'order_id'		=> $merge_pay_id,
            'discount'  => $freight_discount,
			'use_time'	=> time(),
		);
		$success = $this->where('coupon_id = ' . $coupon_id)->save($arr); 
		return $success ? 1 : 0;
	}

    /**
     * 获取订单镖金领取页信息列表
     * @author zlf
     * @param array $coupon_list
     * @return array $coupon_list
     * @todo 获取订单镖金领取页信息列表
     */
    public function getCouponShareList($coupon_list)
    {
		foreach ($coupon_list AS $k => $v)
		{
			//领取者姓名、头像
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname, headimgurl');
			$coupon_list[$k]['nickname'] = $user_info['nickname'];
			$coupon_list[$k]['headimgurl'] = $user_info['headimgurl'];
		}

		return $coupon_list;
    }
}

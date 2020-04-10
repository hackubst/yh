<?php
/**
 * 提现申请模型类
 */

class DepositApplyModel extends BaseModel
{
    // 提现申请id
    public $deposit_apply_id;

    /**
     * 构造函数
     * @author 姜伟
     * @param $deposit_apply_id 提现申请ID
     * @return void
     * @todo 初始化提现申请id
     */
    public function DepositApplyModel($deposit_apply_id)
    {
        parent::__construct('deposit_apply');

        if ($deposit_apply_id = intval($deposit_apply_id))
		{
            $this->deposit_apply_id = $deposit_apply_id;
		}
    }

    /**
     * 获取提现申请信息
     * @author 姜伟
     * @param int $deposit_apply_id 提现申请id
     * @param string $fields 要获取的字段名
     * @return array 提现申请基本信息
     * @todo 根据where查询条件查找提现申请表中的相关数据并返回
     */
    public function getDepositApplyInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改提现申请信息
     * @author 姜伟
     * @param array $arr 提现申请信息数组
     * @return boolean 操作结果
     * @todo 修改提现申请信息
     */
    public function editDepositApply($arr)
    {
        return $this->where('deposit_apply_id = ' . $this->deposit_apply_id)->save($arr);
    }

    /**
     * 添加提现申请
     * @author 姜伟
     * @param array $arr 提现申请信息数组
     * @return boolean 操作结果
     * @todo 添加提现申请
     */
    public function addDepositApply($arr)
    {
        if (!is_array($arr)) return false;

		$user_id = intval(session('user_id'));
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('left_money, frozen_money, role_type');
		$arr['role_type'] = $user_info['role_type'];

		//判断余额是否足够
		if ($user_info['left_money'] < $arr['money'])
		{
			//余额不足
			return -2;
		}

		//调用account模型的addAccount方法
		$account_obj = new AccountModel();
		$left = $account_obj->addAccount($user_id, AccountModel::DEPOSIT_APPLY, $arr['money'] * -1, '提现申请');

		$arr['addtime'] = time();

		return $this->add($arr);
    }

    /**
     * 删除提现申请
     * @author 姜伟
     * @param int $deposit_apply_id 提现申请ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delDepositApply($deposit_apply_id)
    {
        if (!is_numeric($deposit_apply_id)) return false;
		return $this->where('deposit_apply_id = ' . $deposit_apply_id)->delete();
    }

    /**
     * 根据where子句获取提现申请数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的提现申请数量
     * @todo 根据where子句获取提现申请数量
     */
    public function getDepositApplyNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询提现申请信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 提现申请基本信息
     * @todo 根据SQL查询字句查询提现申请信息
     */
    public function getDepositApplyList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取提现申请状态列表
     * @author 姜伟
     * @param void
     * @return array $state_list
     * @todo 获取提现申请状态列表
     */
    public static function getStateList()
    {
		return array(
			'0'	=> '待处理',
			'1'	=> '已通过',
			'2'	=> '已拒绝',
		);
    }

    /**
     * 获取提现申请类型列表
     * @author 姜伟
     * @param void
     * @return array $deposit_type_list
     * @todo 获取提现申请类型列表
     */
    public static function getDepositTypeList()
    {
		return array(
			'1'	=> '余额提现',
			'2'	=> '配送费奖励提现',
		);
    }

    /**
     * 获取提现申请类型筛选列表
     * @author 姜伟
     * @param void
     * @return array $deposit_type_list
     * @todo 获取提现申请类型筛选列表
     */
    public static function getDepositTypeSelectList()
    {
		return array(
			'1'	=> '云仓提现',
			'2'	=> '配送员余额提现',
			'3'	=> '配送费奖励提现',
		);
    }

    /**
     * 获取提现申请列表页数据信息列表
     * @author 姜伟
     * @param array $deposit_apply_list
     * @return array $deposit_apply_list
     * @todo 根据传入的$deposit_apply_list获取更详细的提现申请列表页数据信息列表
     */
    public function getListData($deposit_apply_list, $export = false)
    {
		foreach ($deposit_apply_list AS $k => $v)
		{
			//状态
			$state_list = self::getStateList();
			$state_name = $state_list[$v['state']];
			$deposit_apply_list[$k]['state_name'] = $state_name;

			//用户名称
			$username = '';
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('role_type, realname, mobile,id');
			$username = $user_info['realname'];
			$deposit_apply_list[$k]['username'] = $username;
			$deposit_apply_list[$k]['mobile'] = $user_info['mobile'];
			$deposit_apply_list[$k]['id'] = $user_info['id'];

			// 所在地区
			$deposit_apply_list[$k]['address'] = AreaModel::getAreaString($v['province_id'],$v['city_id'],$v['area_id']);

			//提现账号
			$user_obj = new UserModel($v['user_id']);
            $account_info = $user_obj->getUserInfo('openid');
            $deposit_apply_list[$k]['openid'] = $account_info['openid'];
			/*$account_info = $user_obj->getUserInfo('alipay_account, alipay_account_name, openid');
			$end_line = $export ? "\n" : "<br>";
			$deposit_apply_list[$k]['account_info'] = '支付宝账户：' . $account_info['alipay_account'] . $end_line . "户名：" . $account_info['alipay_account_name'];
			if ($export)
			{
				$bank_card_info['bank_name'] = '支付宝';
				$deposit_apply_list[$k] = array_merge($deposit_apply_list[$k], $bank_card_info);
			}*/
		}
		$deposit_apply_list = $this->getQiyePayInfo($deposit_apply_list);

		return $deposit_apply_list;
    }

    /**
     * 获取企业支付信息
     * @author 姜伟
     * @param apply_list
     * @return bool
     * @todo 
     */
function getQiyePayInfo($deposit_apply_list)
{
		foreach ($deposit_apply_list AS $k => $v)
		{
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('nickname, openid');
			//微信openid
			$deposit_apply_list[$k]['openid'] = $user_info['openid'];
			//nickname
			$deposit_apply_list[$k]['nickname'] = $user_info['nickname'];
			//apply_time
			$deposit_apply_list[$k]['apply_time'] = date('Y-m-d H:i:s', $v['addtime']);
			//实际提现金额
			$deposit_apply_list[$k]['real_money'] = self::calRealMoney($v['money']);
			//流水号
			$deposit_apply_list[$k]['no'] = 'D' . $v['deposit_apply_id'];
		}
		return $deposit_apply_list;
}

    /**
     * 通过提现申请
     * @author 姜伟
     * @param void
     * @return bool
     * @todo 判断有效性，判断冻结池余额是否足够，扣款&通过提现申请&变更状态，推送消息
     */
    public function passDepositApply()
    {
		$success = false;
		//查看是否有效申请
		$deposit_apply_info = $this->getDepositApplyInfo('deposit_apply_id = ' . $this->deposit_apply_id, 'money, state, user_id');
		if (!$deposit_apply_info || $deposit_apply_info['state'] != 0)
		{
			return false;
		}

		//提现是否成功
		$user_obj = new UserModel($deposit_apply_info['user_id']);
		$user_info = $user_obj->getUserInfo('frozen_money');
		$msg = '';
		$state = 1;
		if ($user_info['frozen_money'] >= $deposit_apply_info['money'])
		{
			$arr = array(
				'state'		=> 1,
				'pass_time'	=> time(),
			);
			$msg = '恭喜您，您的提现申请已通过';

			//扣除冻结池中的相应金额除
			$user_obj->setUserInfo(array(
				'frozen_money'	=> $user_info['frozen_money'] - $deposit_apply_info['money']
			));
			$user_obj->saveUserInfo();
		}
		else
		{
			$msg = '您的余额不足，无法提现';
			$arr = array(
				'state'			=> 2,
				'admin_remark'	=> $msg,
			);
			$state = 2;
		}
		$success = $this->editDepositApply($arr);

		return $success;
    }

    /**
     * 拒绝提现申请
     * @author 姜伟
     * @param void
     * @return bool
     * @todo 判断有效性，判断冻结池余额是否足够，扣冻结池款&加用户余额OR奖励&拒绝提现申请&变更状态，推送消息
     */
    public function refuseDepositApply()
    {
		$success = false;
		//查看是否有效申请
		$deposit_apply_info = $this->getDepositApplyInfo('deposit_apply_id = ' . $this->deposit_apply_id, 'deposit_type, money, state, user_id');
		if (!$deposit_apply_info || $deposit_apply_info['state'] != 0)
		{
			return false;
		}

		//冻结池余额是否足够
		$user_id = $deposit_apply_info['user_id'];
		$user_obj = new UserModel($user_id);
		$user_info = $user_obj->getUserInfo('frozen_money');
		$msg = '';
		$state = 1;
		if ($user_info['frozen_money'] >= $deposit_apply_info['money'])
		{
			//足够
			$arr = array(
				'state'	=> 2,
			);

			//调用account模型的addAccount方法
			$account_obj = new AccountModel();
			$left = $account_obj->addAccount($user_id, AccountModel::REFUSE_DEPOSIT, $deposit_apply_info['money'], '拒绝提现申请：' . $msg);
		}
		else
		{
			$msg = '您的冻结池余额不足，无法提现';
			$arr = array(
				'state'			=> 2,
				'admin_remark'	=> $msg,
			);
			$state = 2;
            //调用account模型的addAccount方法
            $account_obj = new AccountModel();
            $account_obj->addAccount($user_id, AccountModel::REFUSE_DEPOSIT, $user_info['frozen_money'], '拒绝提现申请：' . $msg);
		}
		$success = $this->editDepositApply($arr);

		return $success;
    }

    /**
     * 统计提现总数
     * @author 姜伟
     * @param int $role_type
     * @param string $where
     * @return float
     * @todo 统计提现总数
     */
    public function sumDepositTotal($role_type, $where = '1')
    {
		$where .= ' AND state = 1';
		if ($role_type)
		{
			$where .= ' AND role_type = ' . $role_type;
		}

		return $this->where($where)->sum('money');
    }

public static function calRealMoney($money)
{
	return round($money * (100 - $GLOBALS['config_info']['DEPOSIT_FEE']) / 100, 2);
}
}

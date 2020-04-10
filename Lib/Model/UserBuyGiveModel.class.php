<?php
/**
 * 用户买赠活动模型类
 */

class UserBuyGiveModel extends BaseModel
{
    // 用户买赠活动id
    public $user_buy_give_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $user_buy_give_id 用户买赠活动ID
     * @return void
     * @todo 初始化用户买赠活动id
     */
    public function UserBuyGiveModel($user_buy_give_id)
    {
        parent::__construct('user_buy_give');

        if ($user_buy_give_id = intval($user_buy_give_id))
		{
            $this->user_buy_give_id = $user_buy_give_id;
		}
    }

    /**
     * 获取用户买赠活动信息
     * @author 姜伟
     * @param int $user_buy_give_id 用户买赠活动id
     * @param string $fields 要获取的字段名
     * @return array 用户买赠活动基本信息
     * @todo 根据where查询条件查找用户买赠活动表中的相关数据并返回
     */
    public function getUserBuyGiveInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改用户买赠活动信息
     * @author 姜伟
     * @param array $arr 用户买赠活动信息数组
     * @return boolean 操作结果
     * @todo 修改用户买赠活动信息
     */
    public function editUserBuyGive($arr)
    {
        return $this->where('user_buy_give_id = ' . $this->user_buy_give_id)->save($arr);
    }

    /**
     * 添加用户买赠活动
     * @author 姜伟
     * @param array $arr 用户买赠活动信息数组
     * @return boolean 操作结果
     * @todo 添加用户买赠活动
     */
    public function addUserBuyGive($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除用户买赠活动
     * @author 姜伟
     * @param int $user_buy_give_id 用户买赠活动ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delUserBuyGive($user_buy_give_id)
    {
        if (!is_numeric($user_buy_give_id)) return false;
		return $this->where('user_buy_give_id = ' . $user_buy_give_id)->delete();
    }

    /**
     * 根据where子句获取用户买赠活动数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的用户买赠活动数量
     * @todo 根据where子句获取用户买赠活动数量
     */
    public function getUserBuyGiveNum($where = '')
    {
        return $this->where($where)->count();
    }
    
    /**
     * 根据merchant_id查询是否已用户买赠活动
     * @author zlf
     * @param int $merchant_id 商户ID
     * @return boolean 结果 
     * @todo 查询是否已用户买赠活动
     */
    public function getMerchantIsUserBuyGive($merchant_id)
    {
        return $this->where('merchant_id = ' . $merchant_id)->select();
    }

    /**
     * 根据where子句查询用户买赠活动信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 用户买赠活动基本信息
     * @todo 根据SQL查询字句查询用户买赠活动信息
     */
    public function getUserBuyGiveList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取用户买赠活动列表页数据信息列表
     * @author 姜伟
     * @param array $user_buy_give_list
     * @return array $user_buy_give_list
     * @todo 根据传入的$user_buy_give_list获取更详细的用户买赠活动列表页数据信息列表
     */
    public function getListData($user_buy_give_list)
    {
		foreach ($user_buy_give_list AS $k => $v)
		{
			//获取买赠描述
			$buy_give_obj = new BuyGiveModel();
			$buy_give_info = $buy_give_obj->getBuyGiveInfo('buy_give_id = ' . $v['buy_give_id'], '');
			$user_buy_give_list[$k]['send_desc'] = $buy_give_obj->getSendDesc($buy_give_info);

			//商户信息
			$merchant_obj = new MerchantModel($v['merchant_id']);
			$merchant_info = $merchant_obj->getMerchantInfo('merchant_id = ' . $v['merchant_id'], 'shop_name');
			$user_buy_give_list[$k]['shop_name'] = $merchant_info['shop_name'];

			//用户姓名、手机号
			$user_obj = new UserModel($v['user_id']);
			$user_info = $user_obj->getUserInfo('realname, nickname, mobile');
			$user_desc = '姓名：' . ($user_info['realname'] ? $user_info['realname'] : $user_info['nickname']) . "<br>手机号：" . $user_info['mobile'];
			$user_buy_give_list[$k]['user_desc'] = $user_desc;
		}

		return $user_buy_give_list;
    }
}

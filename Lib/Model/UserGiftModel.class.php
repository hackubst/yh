<?php
/**
 * 兑换卡模型类
 * table_name = tp_user_gift
 * py_key = user_gift_id
 */

class UserGiftModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化兑换卡id
     */
    public function UserGiftModel()
    {
        parent::__construct('user_gift');
    }

    /**
     * 获取兑换卡信息
     * @author 姜伟
     * @param int $user_gift_id 兑换卡id
     * @param string $fields 要获取的字段名
     * @return array 兑换卡基本信息
     * @todo 根据where查询条件查找兑换卡表中的相关数据并返回
     */
    public function getUserGiftInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改兑换卡信息
     * @author 姜伟
     * @param array $arr 兑换卡信息数组
     * @return boolean 操作结果
     * @todo 修改兑换卡信息
     */
    public function editUserGift($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加兑换卡
     * @author 姜伟
     * @param array $arr 兑换卡信息数组
     * @return boolean 操作结果
     * @todo 添加兑换卡
     */
    public function addUserGift($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除兑换卡
     * @author 姜伟
     * @param int $user_gift_id 兑换卡ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delUserGift($user_gift_id,$opt = false)
    {
        if (!is_numeric($user_gift_id)) return false;
        if($opt)
        {
            return $this->where('user_gift_id = ' . $user_gift_id)->delete();
        }else{
           return $this->where('user_gift_id = ' . $user_gift_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取兑换卡数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的兑换卡数量
     * @todo 根据where子句获取兑换卡数量
     */
    public function getUserGiftNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询兑换卡信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 兑换卡基本信息
     * @todo 根据SQL查询字句查询兑换卡信息
     */
    public function getUserGiftList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getUserGiftField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取兑换卡列表页数据信息列表
     * @author 姜伟
     * @param array $UserGift_list
     * @return array $UserGift_list
     * @todo 根据传入的$UserGift_list获取更详细的兑换卡列表页数据信息列表
     */
    public function getListData($user_gift_list)
    {
        foreach ($user_gift_list as $k => $v) {

            $gift_card_obj = new GiftCardModel();
            $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id ='.$v['gift_card_id'],'name,money,cash');

            $user_gift_list[$k]['card_name'] = $gift_card_info['name'] ? : '';
            $user_gift_list[$k]['num'] = $v['number'] ? : '';
            $user_gift_list[$k]['money'] = $gift_card_info['cash'] ? : '';

        }
        return $user_gift_list;
    }
    /**
     * 获取兑换卡列表页数据信息列表
     * @author 姜伟
     * @param array $UserGift_list
     * @return array $UserGift_list
     * @todo 根据传入的$UserGift_list获取更详细的兑换卡列表页数据信息列表
     */
    public function getListDataTwo($user_gift_list)
    {
        foreach ($user_gift_list as $k => $v) {

            $gift_card_obj = new GiftCardModel();
            $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id ='.$v['gift_card_id'],'name,money,cash');

            $user_gift_list[$k]['card_name'] = $gift_card_info['name'] ? : '';
            $user_gift_list[$k]['num'] = $gift_card_info['cash'] ? : '';

            $user_gift_password_obj = new UserGiftPasswordModel();
            $user_gift_password_info = $user_gift_password_obj->getUserGiftPasswordInfo('user_gift_id ='.$v['user_gift_id'],'isuse,card_password');
            $user_gift_list[$k]['isuse'] = $user_gift_password_info['isuse'] ? : '';
            $user_gift_list[$k]['card_password'] = $user_gift_password_info['card_password'] ? : '';

            unset($user_gift_list[$k]['user_gift_id']);
        }
        return $user_gift_list;
    }

    public function getListDataThree($user_gift_list)
    {
        foreach ($user_gift_list as $k => $v) {

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname,id','user_id = '.$v['user_id']);

            $gift_card_obj = new GiftCardModel();
            $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id ='.$v['gift_card_id'],'name,money,cash');

            $user_gift_list[$k]['name'] = $gift_card_info['name'] ? : '';
            $user_gift_list[$k]['number'] = $v['number'] ? : '';
            $user_gift_list[$k]['money'] = feeHandle($gift_card_info['money']) ? : '';
            $user_gift_list[$k]['cash'] = $gift_card_info['cash'] ? : '';

            $user_gift_list[$k]['nickname'] = $user_info['nickname'] ? : '';
            $user_gift_list[$k]['id'] = $user_info['id'] ? : '';

        }
        return $user_gift_list;
    }

}
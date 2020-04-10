<?php
/**
 * 点卡卡密模型类
 * table_name = tp_user_gift_password
 * py_key = user_gift_password_id
 */

class UserGiftPasswordModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化点卡卡密id
     */
    public function UserGiftPasswordModel()
    {
        parent::__construct('user_gift_password');
    }

    /**
     * 获取点卡卡密信息
     * @author 姜伟
     * @param int $user_gift_password_id 点卡卡密id
     * @param string $fields 要获取的字段名
     * @return array 点卡卡密基本信息
     * @todo 根据where查询条件查找点卡卡密表中的相关数据并返回
     */
    public function getUserGiftPasswordInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改点卡卡密信息
     * @author 姜伟
     * @param array $arr 点卡卡密信息数组
     * @return boolean 操作结果
     * @todo 修改点卡卡密信息
     */
    public function editUserGiftPassword($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加点卡卡密
     * @author 姜伟
     * @param array $arr 点卡卡密信息数组
     * @return boolean 操作结果
     * @todo 添加点卡卡密
     */
    public function addUserGiftPassword($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除点卡卡密
     * @author 姜伟
     * @param int $user_gift_password_id 点卡卡密ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delUserGiftPassword($user_gift_password_id,$opt = false)
    {
        if (!is_numeric($user_gift_password_id)) return false;
        if($opt)
        {
            return $this->where('user_gift_password_id = ' . $user_gift_password_id)->delete();
        }else{
           return $this->where('user_gift_password_id = ' . $user_gift_password_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取点卡卡密数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的点卡卡密数量
     * @todo 根据where子句获取点卡卡密数量
     */
    public function getUserGiftPasswordNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询点卡卡密信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 点卡卡密基本信息
     * @todo 根据SQL查询字句查询点卡卡密信息
     */
    public function getUserGiftPasswordList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getUserGiftPasswordField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取点卡卡密列表页数据信息列表
     * @author 姜伟
     * @param array $UserGiftPassword_list
     * @return array $UserGiftPassword_list
     * @todo 根据传入的$UserGiftPassword_list获取更详细的点卡卡密列表页数据信息列表
     */
    public function getListData($user_gift_password_list)
    {
        foreach ($user_gift_password_list as $k => $v) {

            $user_gift_obj = new UserGiftModel();
            $where = 'user_gift_id ='.$v['user_gift_id'];

            $user_gift_info = $user_gift_obj->getUserGiftInfo($where,'gift_card_id');

            $gift_card_obj = new GiftCardModel();
            $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id ='.$user_gift_info['gift_card_id'],'name,cash');

            $user_gift_password_list[$k]['name'] = $gift_card_info['name'] ? : '';
            $user_gift_password_list[$k]['cash'] = $gift_card_info['cash'] ? : '';

        }
        return $user_gift_password_list;
    }

    //创建卡密
    public function CreatePassword($user_gift_id,$number,$user_id,$gift_card_id)
    {
        // dump($user_course_id);dump($course_id);dump($portion_num);die;
        if(!$user_gift_id || !$number)
        {   
            return false;
        }
        for ($i=0; $i < $number; $i++) { 
            //生成随机卡密
            $data['user_gift_id'] = $user_gift_id;
            $data['addtime'] = time();
            $data['end_time'] = 7*3600*24 + time();
            $data['user_id'] = $user_id;
            $card_password= randLenString(20,1);
            $new_card_password = self::veriyIviteCode($card_password);
            // dump($new_card_password);die;
            $gift_card_obj = new GiftCardModel();
            //add 8.23 添加首字符串
            $first_code = $gift_card_obj->getGiftCardField('gift_card_id ='.$gift_card_id,'first_code');
            $money = $gift_card_obj->getGiftCardField('gift_card_id ='.$gift_card_id,'money');
            $cash = $gift_card_obj->getGiftCardField('gift_card_id ='.$gift_card_id,'cash');
            $data['money'] = $money;
            $data['cash'] = $cash;
            $new_card_password = $first_code.$new_card_password;
            if($new_card_password)
            {
                $data['card_password'] = $new_card_password;
                $user_gift_passwor_id = self::addUserGiftPassword($data);
            }
            else{
                return false;
            }
        }
        return $user_gift_passwor_id;
        
    }

    public function veriyIviteCode($card_password)
    {

        $count = self::getUserGiftPasswordNum('card_password = "'.$card_password.'"');
        if($count == 0)
        {
            return $card_password;
        }else{
            self::veriyIviteCode(randLenString(20,1));
        }
    }

    public function getListDataTwo($user_gift_password_list)
    {
        $write_off_rate = $GLOBALS['config_info']['DCP_WRITE_OFF_RATE'];
        foreach ($user_gift_password_list as $k => $v) {
            
            $user_gift_obj = new UserGiftModel();
            $user_gift_info = $user_gift_obj->getUserGiftInfo('user_gift_id ='.$v['user_gift_id'],'gift_card_id,user_id');

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('nickname,id','user_id = '.$user_gift_info['user_id']);

            $gift_card_obj = new GiftCardModel();
            $gift_card_info = $gift_card_obj->getGiftCardInfo('gift_card_id ='.$user_gift_info['gift_card_id'],'name,money,cash');

            $user_gift_password_list[$k]['name'] = $gift_card_info['name'] ? : '';
//            $user_gift_password_list[$k]['money'] = $gift_card_info['money'] ? : '';
//            $user_gift_password_list[$k]['cash'] = $gift_card_info['cash'] ? : '';
            $user_gift_password_list[$k]['write_off_money'] = $v['cash'] * (100 - $write_off_rate) / 100 ?: '';
            $user_gift_password_list[$k]['nickname'] = $user_info['nickname'] ? : '';
            $user_gift_password_list[$k]['id'] = $user_info['id'] ? : '';

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('realname','user_id = '.$v['dcp_id']);
            $user_gift_password_list[$k]['dcp_name'] = $user_info['realname'] ? : '';

        }
        return $user_gift_password_list;
    }

    public function getListDataName($account_list)
    {
        foreach ($account_list as $k => $v) {

            $user_obj = new UserModel();
            $user_info = $user_obj->getUserInfo('realname,id','user_id ='.$v['dcp_id']);
            if($user_info)
            {
                $account_list[$k]['nickname'] = $user_info['realname'];
                $account_list[$k]['id'] = $user_info['id'];
            }

        }
        return $account_list;
    }

}

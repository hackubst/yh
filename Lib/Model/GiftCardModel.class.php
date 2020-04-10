<?php
/**
 * 兑换卡券模型类
 * table_name = tp_gift_card
 * py_key = gift_card_id
 */

class GiftCardModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化兑换卡券id
     */
    public function GiftCardModel()
    {
        parent::__construct('gift_card');
    }

    /**
     * 获取兑换卡券信息
     * @author 姜伟
     * @param int $gift_card_id 兑换卡券id
     * @param string $fields 要获取的字段名
     * @return array 兑换卡券基本信息
     * @todo 根据where查询条件查找兑换卡券表中的相关数据并返回
     */
    public function getGiftCardInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改兑换卡券信息
     * @author 姜伟
     * @param array $arr 兑换卡券信息数组
     * @return boolean 操作结果
     * @todo 修改兑换卡券信息
     */
    public function editGiftCard($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加兑换卡券
     * @author 姜伟
     * @param array $arr 兑换卡券信息数组
     * @return boolean 操作结果
     * @todo 添加兑换卡券
     */
    public function addGiftCard($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除兑换卡券
     * @author 姜伟
     * @param int $gift_card_id 兑换卡券ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delGiftCard($gift_card_id,$opt = false)
    {
        if (!is_numeric($gift_card_id)) return false;
        if($opt)
        {
            return $this->where('gift_card_id = ' . $gift_card_id)->delete();
        }else{
           return $this->where('gift_card_id = ' . $gift_card_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取兑换卡券数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的兑换卡券数量
     * @todo 根据where子句获取兑换卡券数量
     */
    public function getGiftCardNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询兑换卡券信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 兑换卡券基本信息
     * @todo 根据SQL查询字句查询兑换卡券信息
     */
    public function getGiftCardList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getGiftCardField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取兑换卡券列表页数据信息列表
     * @author 姜伟
     * @param array $GiftCard_list
     * @return array $GiftCard_list
     * @todo 根据传入的$GiftCard_list获取更详细的兑换卡券列表页数据信息列表
     */
    public function getListData($GiftCard_list)
    {
        
    }

}

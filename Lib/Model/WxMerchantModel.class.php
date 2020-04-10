<?php
/**
 * 微信商户模型类
 * table_name = tp_wx_merchant
 * py_key = wx_merchant_id
 */

class WxMerchantModel extends Model
{
   
    /**
     * 构造函数
     * @author 姜伟
     * @todo 初始化微信商户id
     */
    public function WxMerchantModel()
    {
        parent::__construct('wx_merchant');
    }

    /**
     * 获取微信商户信息
     * @author 姜伟
     * @param int $wx_merchant_id 微信商户id
     * @param string $fields 要获取的字段名
     * @return array 微信商户基本信息
     * @todo 根据where查询条件查找微信商户表中的相关数据并返回
     */
    public function getWxMerchantInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改微信商户信息
     * @author 姜伟
     * @param array $arr 微信商户信息数组
     * @return boolean 操作结果
     * @todo 修改微信商户信息
     */
    public function editWxMerchant($where='',$arr)
    {
        if (!is_array($arr)) return false;

        $arr['last_edit_time'] = time();
        $arr['last_edit_user_id'] = session('user_id');
        
        return $this->where($where)->save($arr);
    }

    /**
     * 添加微信商户
     * @author 姜伟
     * @param array $arr 微信商户信息数组
     * @return boolean 操作结果
     * @todo 添加微信商户
     */
    public function addWxMerchant($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
        $arr['add_user_id'] = session('user_id');

        return $this->add($arr);
    }

    /**
     * 删除微信商户
     * @author 姜伟
     * @param int $wx_merchant_id 微信商户ID
     * @param int $opt,默认为假删除，true为真删除
     * @return boolean 操作结果
     * @todo isuse设为1 || 真删除
     */
    public function delWxMerchant($wx_merchant_id,$opt = false)
    {
        if (!is_numeric($wx_merchant_id)) return false;
        if($opt)
        {
            return $this->where('wx_merchant_id = ' . $wx_merchant_id)->delete();
        }else{
           return $this->where('wx_merchant_id = ' . $wx_merchant_id)->save(array('isuse' => 2)); 
        }
        
    }

    /**
     * 根据where子句获取微信商户数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的微信商户数量
     * @todo 根据where子句获取微信商户数量
     */
    public function getWxMerchantNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询微信商户信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $group
     * @return array 微信商户基本信息
     * @todo 根据SQL查询字句查询微信商户信息
     */
    public function getWxMerchantList($fields = '', $where = '', $orderby = '', $group = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->group($group)->limit()->select();
    }

    /**
     * 获取某一字段的值
     * @param  string $where 
     * @param  string $field
     * @return void
     */
    public function getWxMerchantField($where,$field)
    {
        return $this->where($where)->getField($field);
    }


    /**
     * 获取微信商户列表页数据信息列表
     * @author 姜伟
     * @param array $WxMerchant_list
     * @return array $WxMerchant_list
     * @todo 根据传入的$WxMerchant_list获取更详细的微信商户列表页数据信息列表
     */
    public function getListData($WxMerchant_list)
    {
        
    }

}

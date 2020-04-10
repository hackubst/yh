<?php
/**
 * 街道模型类
 */

class StreetModel extends Model
{
    // 街道id
    public $street_id;
   
    /**
     * 构造函数
     * @author wzg
     * @param $street_id 街道ID
     * @return void
     * @todo 初始化街道id
     */
    public function StreetModel($street_id)
    {
        parent::__construct('street');

        if ($street_id = intval($street_id))
		{
            $this->street_id = $street_id;
		}
    }

    /**
     * 获取街道信息
     * @author wzg
     * @param int $street_id 街道id
     * @param string $fields 要获取的字段名
     * @return array 街道基本信息
     * @todo 根据where查询条件查找街道表中的相关数据并返回
     */
    public function getStreetInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改街道信息
     * @author wzg
     * @param array $arr 街道信息数组
     * @return boolean 操作结果
     * @todo 修改街道信息
     */
    public function editStreet($arr)
    {
        return $this->where('street_id = ' . $this->street_id)->save($arr);
    }

    /**
     * 添加街道
     * @author wzg
     * @param array $arr 街道信息数组
     * @return boolean 操作结果
     * @todo 添加街道
     */
    public function addStreet($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除街道
     * @author wzg
     * @param int $street_id 街道ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delStreet($street_id)
    {
        if (!is_numeric($street_id)) return false;
		return $this->where('street_id = ' . $street_id)->delete();
    }

    /**
     * 根据where子句获取街道数量
     * @author wzg
     * @param string|array $where where子句
     * @return int 满足条件的街道数量
     * @todo 根据where子句获取街道数量
     */
    public function getStreetNum($where = '')
    {
        return $this->where($where)->count();
    }
    
    /**
     * 根据where子句查询街道信息
     * @author wzg
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 街道基本信息
     * @todo 根据SQL查询字句查询街道信息
     */
    public function getStreetList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取街道列表页数据信息列表
     * @author wzg
     * @param array $street_list
     * @return array $street_list
     * @todo 根据传入的$street_list获取更详细的街道列表页数据信息列表
     */
    public function getListData($street_list)
    {
		foreach ($street_list AS $k => $v)
		{
            //省市区
            $street_list[$k]['province_name'] = M('AddressProvince')->where('province_id = ' . $v['province_id'])->getField('province_name');
            $street_list[$k]['city_name'] = M('AddressCity')->where('city_id = ' . $v['city_id'])->getField('city_name');
            $street_list[$k]['area_name'] = M('AddressArea')->where('area_id = ' . $v['area_id'])->getField('area_name');
		}

		return $street_list;
    }

    /**
     * 判断街道下是否有商户（删除除外）
     * @author wzg
     * @return array
     * @todo merchant,users
     * */
    public function is_have_merchant($street_id)
    {
        $merchant_obj = D('Merchant');
        $merchant_list = $merchant_obj->getMerchantList('merchant_id', 'street_id = ' . $street_id);
        $count = 0;
        if($merchant_list) {
            foreach ($merchant_list AS $k => $v)
            {
                $user_obj = D('Users');
                $is_enable = $user_obj->where('user_id = ' . $v['merchant_id'])->getField('is_enable');
                if(7 == $is_enable) {
                    continue; 
                } else {
                    $count += 1;
                }
               
            }
        }

        return $count;
    }
}

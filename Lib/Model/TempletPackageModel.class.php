<?php
/**
 * 模板模型类
 */

class TempletPackageModel extends Model
{
    // 模板id
    public $templet_package_id;
   
    /**
     * 构造函数
     * @author wzg
     * @param $templet_package_id 模板ID
     * @return void
     * @todo 初始化模板id
     */
    public function TempletPackageModel($templet_package_id)
    {
        parent::__construct('templet_package');

        if ($templet_package_id = intval($templet_package_id))
		{
            $this->templet_package_id = $templet_package_id;
		}
    }

    /**
     * 获取模板信息
     * @author wzg
     * @param int $templet_package_id 模板id
     * @param string $fields 要获取的字段名
     * @return array 模板基本信息
     * @todo 根据where查询条件查找模板表中的相关数据并返回
     */
    public function getTempletPackageInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改模板信息
     * @author wzg
     * @param array $arr 模板信息数组
     * @return boolean 操作结果
     * @todo 修改模板信息
     */
    public function editTempletPackage($arr)
    {
        return $this->where('templet_package_id = ' . $this->templet_package_id)->save($arr);
    }

    /**
     * 添加模板
     * @author wzg
     * @param array $arr 模板信息数组
     * @return boolean 操作结果
     * @todo 添加模板
     */
    public function addTempletPackage($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除模板
     * @author wzg
     * @param int $templet_package_id 模板ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delTempletPackage($templet_package_id)
    {
        if (!is_numeric($templet_package_id)) return false;
		return $this->where('templet_package_id = ' . $templet_package_id)->delete();
    }

    /**
     * 根据where子句获取模板数量
     * @author wzg
     * @param string|array $where where子句
     * @return int 满足条件的模板数量
     * @todo 根据where子句获取模板数量
     */
    public function getTempletPackageNum($where = '')
    {
        return $this->where($where)->count();
    }
    
    /**
     * 根据where子句查询模板信息
     * @author wzg
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 模板基本信息
     * @todo 根据SQL查询字句查询模板信息
     */
    public function getTempletPackageList($fields = '', $where = '', $orderby = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取模板列表页数据信息列表
     * @author wzg
     * @param array $templet_packaget_list
     * @return array $templet_packaget_list
     * @todo 根据传入的$templet_packaget_list获取更详细的模板列表页数据信息列表
     */
    public function getListData($templet_packaget_list)
    {
		foreach ($templet_packaget_list AS $k => $v)
		{
            //省市区
            $templet_packaget_list[$k]['province_name'] = M('AddressProvince')->where('province_id = ' . $v['province_id'])->getField('province_name');
            $templet_packaget_list[$k]['city_name'] = M('AddressCity')->where('city_id = ' . $v['city_id'])->getField('city_name');
            $templet_packaget_list[$k]['area_name'] = M('AddressArea')->where('area_id = ' . $v['area_id'])->getField('area_name');
		}

		return $templet_packaget_list;
    }

    /**
     * 判断模板下是否有商户（删除除外）
     * @author wzg
     * @return array
     * @todo merchant,users
     * */
    public function is_have_merchant($templet_package_id)
    {
        $merchant_obj = D('Merchant');
        $merchant_list = $merchant_obj->getMerchantList('merchant_id', 'templet_package_id = ' . $templet_package_id);
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

    /**
     * 添加模板到套餐中
     * @author wzg
     */
    public function addPageListToPackage($templet_package_id, $list)
    {
        foreach ($list AS $k => $v) 
        {
            $list[$k]['templet_package_id'] = $templet_package_id;
        }
        return M('Templet')->addAll($list);
    }
}

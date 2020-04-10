<?php
/**
 * 客服账号模型类
 */

class CustomerServiceOnlineModel extends Model
{
    // 客服账号id
    public $customer_service_online_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $customer_service_online_id 客服账号ID
     * @return void
     * @todo 初始化客服账号id
     */
    public function CustomerServiceOnlineModel($customer_service_online_id)
    {
        parent::__construct('customer_service_online');

        if ($customer_service_online_id = intval($customer_service_online_id))
		{
            $this->customer_service_online_id = $customer_service_online_id;
		}
    }

    /**
     * 获取奖品信息
     * @author 姜伟
     * @param int $customer_service_online_id 客服账号id
     * @param string $fields 要获取的字段名
     * @return array 客服账号基本信息
     * @todo 根据where查询条件查找客服账号表中的相关数据并返回
     */
    public function getCustomerServiceOnlineInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改客服账号信息
     * @author 姜伟
     * @param array $arr 客服账号信息数组
     * @return boolean 操作结果
     * @todo 修改客服账号信息
     */
    public function editCustomerServiceOnline($arr)
    {
        return $this->where('customer_service_online_id = ' . $this->customer_service_online_id)->save($arr);
    }

    /**
     * 添加客服账号
     * @author 姜伟
     * @param array $arr 客服账号信息数组
     * @return boolean 操作结果
     * @todo 添加客服账号
     */
    public function addCustomerServiceOnline($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除客服账号
     * @author 姜伟
     * @param int $customer_service_online_id 客服账号ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delCustomerServiceOnline()
    {
        if (!is_numeric($customer_service_online_id)) return false;
		return $this->where('customer_service_online_id = ' . $this->customer_service_online_id)->delete();
    }

    /**
     * 根据where子句获取客服账号数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的客服账号数量
     * @todo 根据where子句获取客服账号数量
     */
    public function getCustomerServiceOnlineNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询客服账号信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 客服账号基本信息
     * @todo 根据SQL查询字句查询客服账号信息
     */
    public function getCustomerServiceOnlineList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取客服账号列表页数据信息列表
     * @author 姜伟
     * @param array $customer_service_online_list
     * @return array $customer_service_online_list
     * @todo 根据传入的$customer_service_online_list获取更详细的客服账号列表页数据信息列表
     */
    public function getListData($customer_service_online_list)
    {
		foreach ($customer_service_online_list AS $k => $v)
		{
			//客服类型
			$service_type_list = self::get_service_type_list();
			$customer_service_online_list[$k]['service_type'] = $service_type_list[$v['service_type']];

			//售前/售后
			$is_after_service_list = self::get_is_after_service_list();
			$customer_service_online_list[$k]['is_after_service'] = $is_after_service_list[$v['is_after_service']];

			//产品名称
			if ($v['item_id'] == 0)
			{
				$customer_service_online_list[$k]['item_name'] = '合作加盟';
			}
			else
			{
				$item_obj = new ItemModel($v['item_id']);
				$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name');
				$customer_service_online_list[$k]['item_name'] = $item_info['item_name'];
			}
		}

		return $customer_service_online_list;
    }

    /**
     * 获取客服类型列表
     * @author 姜伟
     * @param void
     * @return array $service_type_list
     * @todo 获取客服类型列表
     */
    public static function get_service_type_list()
    {
		$service_type_list = array(
			'1'		=> 'QQ',
			#'2'		=> '旺旺',
		);
		return $service_type_list;
    }

    /**
     * 获取客服对应的产品列表
     * @author 姜伟
     * @param void
     * @return array $item_list
     * @todo 获取客服对应的产品列表
     */
    public static function get_item_list()
    {
		$item_obj = new ItemModel();
		$item_list = $item_obj->getItemList('item_id, item_name', 'isuse = 1');
		$item_list[] = array(
			'item_id'	=> 0,
			'item_name'	=> '加盟合作',
		);
		return $item_list;
    }

    /**
     * 获取售前售后列表信息
     * @author 姜伟
     * @param void
     * @return array $is_after_service_list
     * @todo 获取售前售后列表信息
     */
    public static function get_is_after_service_list()
    {
		$is_after_service_list = array(
			'0'		=> '售前',
			'1'		=> '售后',
		);
		return $is_after_service_list;
    }
}

<?php
/**
 * 轮播图片模型类
 */

class CustFlashModel extends Model
{
    // 轮播图片id
    public $cust_flash_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $cust_flash_id 轮播图片ID
     * @return void
     * @todo 初始化轮播图片id
     */
    public function CustFlashModel($cust_flash_id)
    {
        parent::__construct('cust_flash');

        if ($cust_flash_id = intval($cust_flash_id))
		{
            $this->cust_flash_id = $cust_flash_id;
		}
    }

    /**
     * 获取轮播图片信息
     * @author 姜伟
     * @param int $cust_flash_id 轮播图片id
     * @param string $fields 要获取的字段名
     * @return array 轮播图片基本信息
     * @todo 根据where查询条件查找轮播图片表中的相关数据并返回
     */
    public function getCustFlashInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改轮播图片信息
     * @author 姜伟
     * @param array $arr 轮播图片信息数组
     * @return boolean 操作结果
     * @todo 修改轮播图片信息
     */
    public function editCustFlash($arr)
    {
        return $this->where('cust_flash_id = ' . $this->cust_flash_id)->save($arr);
    }

    /**
     * 添加轮播图片
     * @author 姜伟
     * @param array $arr 轮播图片信息数组
     * @return boolean 操作结果
     * @todo 添加轮播图片
     */
    public function addCustFlash($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除轮播图片
     * @author 姜伟
     * @param int $cust_flash_id 轮播图片ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delCustFlash($cust_flash_id)
    {
        if (!is_numeric($cust_flash_id)) return false;
		return $this->where('cust_flash_id = ' . $cust_flash_id)->delete();
    }

    /**
     * 根据where子句获取轮播图片数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的轮播图片数量
     * @todo 根据where子句获取轮播图片数量
     */
    public function getCustFlashNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询轮播图片信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 轮播图片基本信息
     * @todo 根据SQL查询字句查询轮播图片信息
     */
    public function getCustFlashList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取轮播图片列表页数据信息列表
     * @author 姜伟
     * @param array $cust_flash_list
     * @return array $cust_flash_list
     * @todo 根据传入的$cust_flash_list获取更详细的轮播图片列表页数据信息列表
     */
    public function getListData($cust_flash_list)
    {
		foreach ($cust_flash_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic');
			$cust_flash_list[$k]['item_name'] = $item_info['item_name'];
			$cust_flash_list[$k]['mall_price'] = $item_info['mall_price'];
			$cust_flash_list[$k]['small_pic'] = $item_info['base_pic'];

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$cust_flash_list[$k]['status'] = $status;
		}

		return $cust_flash_list;
    }
}

<?php
/**
 * 首页广告图片模型类
 */

class IndexAdsModel extends Model
{
    // 首页广告图片id
    public $index_ads_id;
   
    /**
     * 构造函数
     * @author zlf
     * @param $index_ads_id 首页广告图片ID
     * @return void
     * @todo 初始化首页广告图片id
     */
    public function IndexAdsModel($index_ads_id)
    {
        parent::__construct('index_ads');

        if ($index_ads_id = intval($index_ads_id))
		{
            $this->index_ads_id = $index_ads_id;
		}
    }

    /**
     * 获取首页广告图片信息
     * @author zlf
     * @param int $index_ads_id 首页广告图片id
     * @param string $fields 要获取的字段名
     * @return array 首页广告图片基本信息
     * @todo 根据where查询条件查找首页广告图片表中的相关数据并返回
     */
    public function getIndexAdsInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改首页广告图片信息
     * @author zlf
     * @param array $arr 首页广告图片信息数组
     * @return boolean 操作结果
     * @todo 修改首页广告图片信息
     */
    public function editIndexAds($arr)
    {
        return $this->where('index_ads_id = ' . $this->index_ads_id)->save($arr);
    }

    /**
     * 添加首页广告图片
     * @author zlf
     * @param array $arr 首页广告图片信息数组
     * @return boolean 操作结果
     * @todo 添加首页广告图片
     */
    public function addIndexAds($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除首页广告图片
     * @author zlf
     * @param int $index_ads_id 首页广告图片ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delIndexAds($index_ads_id)
    {
        if (!is_numeric($index_ads_id)) return false;
		return $this->where('index_ads_id = ' . $index_ads_id)->delete();
    }

    /**
     * 根据where子句获取首页广告图片数量
     * @author zlf
     * @param string|array $where where子句
     * @return int 满足条件的首页广告图片数量
     * @todo 根据where子句获取首页广告图片数量
     */
    public function getIndexAdsNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询首页广告图片信息
     * @author zlf
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 首页广告图片基本信息
     * @todo 根据SQL查询字句查询首页广告图片信息
     */
    public function getIndexAdsList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取首页广告图片列表页数据信息列表
     * @author zlf
     * @param array $index_ads_list
     * @return array $index_ads_list
     * @todo 根据传入的$index_ads_list获取更详细的首页广告图片列表页数据信息列表
     */
    public function getListData($index_ads_list)
    {
		foreach ($index_ads_list AS $k => $v)
		{
            $index_ads_info = $this->getIndexAdsInfo('index_ads_id = ' . $v['index_ads_id'],'size_style');
            $style_name = '';
            if ($index_ads_info['size_style'] == 0)
            {
                $style_name = '全幅大图';
            }
            elseif ($index_ads_info['size_style'] == 1)
            {
                $style_name = '半幅中图';
            }
            elseif ($index_ads_info['size_style'] == 2)
            {
                $style_name = '1/3幅小图';
            }
            $index_ads_list[$k]['style_name'] = $style_name;
			//产品名称
			/*$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic');
			$index_ads_list[$k]['item_name'] = $item_info['item_name'];
			$index_ads_list[$k]['mall_price'] = $item_info['mall_price'];
			$index_ads_list[$k]['small_pic'] = $item_info['base_pic'];

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$index_ads_list[$k]['status'] = $status;*/
		}

		return $index_ads_list;
    }
}

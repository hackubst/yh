<?php
/**
 * 收藏模型类
 */

class CollectModel extends Model
{
    // 收藏id
    public $collect_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $collect_id 收藏ID
     * @return void
     * @todo 初始化收藏id
     */
    public function CollectModel($collect_id)
    {
        parent::__construct('collect');

        if ($collect_id = intval($collect_id))
		{
            $this->collect_id = $collect_id;
		}
    }

    /**
     * 获取收藏信息
     * @author 姜伟
     * @param int $collect_id 收藏id
     * @param string $fields 要获取的字段名
     * @return array 收藏基本信息
     * @todo 根据where查询条件查找收藏表中的相关数据并返回
     */
    public function getCollectInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改收藏信息
     * @author 姜伟
     * @param array $arr 收藏信息数组
     * @return boolean 操作结果
     * @todo 修改收藏信息
     */
    public function editCollect($arr)
    {
        return $this->where('collect_id = ' . $this->collect_id)->save($arr);
    }

    /**
     * 添加收藏
     * @author 姜伟
     * @param array $arr 收藏信息数组
     * @return boolean 操作结果
     * @todo 添加收藏
     */
    public function addCollect($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除收藏
     * @author 姜伟
     * @param int $collect_id 收藏ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delCollect($collect_id)
    {
        if (!is_numeric($collect_id)) return false;
        return $this->where('collect_id = ' . $collect_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取收藏数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的收藏数量
     * @todo 根据where子句获取收藏数量
     */
    public function getCollectNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询收藏信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 收藏基本信息
     * @todo 根据SQL查询字句查询收藏信息
     */
    public function getCollectList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取收藏列表页数据信息列表
     * @author 姜伟
     * @param array $collect_list
     * @return array $collect_list
     * @todo 根据传入的$collect_list获取更详细的收藏列表页数据信息列表
     */
    public function getListData($collect_list)
    {
        require_cache('Common/func_item.php');

		foreach ($collect_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic, is_gift');
			$collect_list[$k]['item_name']  = $item_info['item_name'];
            $collect_list[$k]['mall_price'] = $item_info['mall_price'];
			$collect_list[$k]['is_gift']    = $item_info['is_gift'];
			$collect_list[$k]['small_pic']  = small_img($item_info['base_pic']);

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$collect_list[$k]['status'] = $status;
		}

		return $collect_list;
    }

    // 猜你喜欢列表内容
    // 暂时先根据收藏列表获取同类型的商品，若没有同类型的商品，则获取热门推荐里面同类型的商品
    //@author wangsongqing
    public function getGuessMyLikeList($user_id)
    {

        $item_obj = D('Item');
        $where    = 'isuse = 1 AND is_gift = 0';

        $collect_obj  = new CollectModel();
        $collect_list = $collect_obj->getCollectList('item_id', 'user_id = ' . $user_id, 'addtime DESC');

        $class_ids_array = array();
        if ($collect_list) {
            foreach ($collect_list AS $k => $v) {
                $class_id = $item_obj->where('item_id = %d', $v['item_id'])->getField('class_id'); 
                $class_id ? array_push($class_ids_array, $class_id) : 0;
            }
        } 

        if (count($class_ids_array)) {
            $where .= ' AND class_id IN (' . implode(',', $class_ids_array) . ')';
        }

        $item_obj->setLimit(20);
        $hot_item_list = $item_obj->getItemList(
            'item_name, item_id, base_pic, mall_price, market_price', 
            $where,
            'sales_num DESC'
        );

        shuffle($hot_item_list);
        $result = array_slice($hot_item_list, 0, 4);

        if (count($result) < 4) {
            $item_obj->setLimit(20);
            $extra_item_list = $item_obj->getItemList(
                'item_name, item_id, base_pic, mall_price, market_price', 
                'isuse = 1 AND is_gift = 0',
                'sales_num DESC'
            );
            $left_item = array_slice($extra_item_list, 0, 4 - count($result));
            $result = array_merge($result, $left_item);
        }

        return $result;
    }
}

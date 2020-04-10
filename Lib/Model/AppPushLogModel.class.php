<?php
/**
 * APP消息推送模型类
 */

class AppPushLogModel extends Model
{
    // APP消息推送id
    public $app_push_log_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $app_push_log_id APP消息推送ID
     * @return void
     * @todo 初始化APP消息推送id
     */
    public function AppPushLogModel($app_push_log_id)
    {
        parent::__construct('app_push_log');

        if ($app_push_log_id = intval($app_push_log_id))
		{
            $this->app_push_log_id = $app_push_log_id;
		}
    }

    /**
     * 获取APP消息推送信息
     * @author 姜伟
     * @param int $app_push_log_id APP消息推送id
     * @param string $fields 要获取的字段名
     * @return array APP消息推送基本信息
     * @todo 根据where查询条件查找APP消息推送表中的相关数据并返回
     */
    public function getAppPushLogInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改APP消息推送信息
     * @author 姜伟
     * @param array $arr APP消息推送信息数组
     * @return boolean 操作结果
     * @todo 修改APP消息推送信息
     */
    public function editAppPushLog($arr)
    {
        return $this->where('app_push_log_id = ' . $this->app_push_log_id)->save($arr);
    }

    /**
     * 添加APP消息推送
     * @author 姜伟
     * @param array $arr APP消息推送信息数组
     * @return boolean 操作结果
     * @todo 添加APP消息推送
     */
    public function addAppPushLog($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除APP消息推送
     * @author 姜伟
     * @param int $app_push_log_id APP消息推送ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delAppPushLog($app_push_log_id)
    {
        if (!is_numeric($app_push_log_id)) return false;
        return $this->where('app_push_log_id = ' . $app_push_log_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取APP消息推送数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的APP消息推送数量
     * @todo 根据where子句获取APP消息推送数量
     */
    public function getAppPushLogNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询APP消息推送信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array APP消息推送基本信息
     * @todo 根据SQL查询字句查询APP消息推送信息
     */
    public function getAppPushLogList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取APP消息推送列表页数据信息列表
     * @author 姜伟
     * @param array $app_push_log_list
     * @return array $app_push_log_list
     * @todo 根据传入的$app_push_log_list获取更详细的APP消息推送列表页数据信息列表
     */
    public function getListData($app_push_log_list)
    {
        require_cache('Common/func_item.php');

		foreach ($app_push_log_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic, is_gift');
			$app_push_log_list[$k]['item_name']  = $item_info['item_name'];
            $app_push_log_list[$k]['mall_price'] = $item_info['mall_price'];
			$app_push_log_list[$k]['is_gift']    = $item_info['is_gift'];
			$app_push_log_list[$k]['small_pic']  = small_img($item_info['base_pic']);

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$app_push_log_list[$k]['status'] = $status;
		}

		return $app_push_log_list;
    }

    // 猜你喜欢列表内容
    // 暂时先根据APP消息推送列表获取同类型的商品，若没有同类型的商品，则获取热门推荐里面同类型的商品
    //@author wangsongqing
    public function getGuessMyLikeList($user_id)
    {

        $item_obj = D('Item');
        $where    = 'isuse = 1 AND is_gift = 0';

        $app_push_log_obj  = new AppPushLogModel();
        $app_push_log_list = $app_push_log_obj->getAppPushLogList('item_id', 'user_id = ' . $user_id, 'addtime DESC');

        $class_ids_array = array();
        if ($app_push_log_list) {
            foreach ($app_push_log_list AS $k => $v) {
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

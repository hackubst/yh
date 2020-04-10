<?php
/**
 * 关键词自动回复模型类
 */

class WxKwReplyModel extends Model
{
    // 关键词自动回复id
    public $wx_kw_reply_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $wx_kw_reply_id 关键词自动回复ID
     * @return void
     * @todo 初始化关键词自动回复id
     */
    public function WxKwReplyModel($wx_kw_reply_id)
    {
        parent::__construct('wx_kw_reply');

        if ($wx_kw_reply_id = intval($wx_kw_reply_id))
		{
            $this->wx_kw_reply_id = $wx_kw_reply_id;
		}
    }

    /**
     * 获取关键词自动回复信息
     * @author 姜伟
     * @param int $wx_kw_reply_id 关键词自动回复id
     * @param string $fields 要获取的字段名
     * @return array 关键词自动回复基本信息
     * @todo 根据where查询条件查找关键词自动回复表中的相关数据并返回
     */
    public function getWxKwReplyInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改关键词自动回复信息
     * @author 姜伟
     * @param array $arr 关键词自动回复信息数组
     * @return boolean 操作结果
     * @todo 修改关键词自动回复信息
     */
    public function editWxKwReply($arr)
    {
        return $this->where('wx_kw_reply_id = ' . $this->wx_kw_reply_id)->save($arr);
    }

    /**
     * 添加关键词自动回复
     * @author 姜伟
     * @param array $arr 关键词自动回复信息数组
     * @return boolean 操作结果
     * @todo 添加关键词自动回复
     */
    public function addWxKwReply($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();

        return $this->add($arr);
    }

    /**
     * 删除关键词自动回复
     * @author 姜伟
     * @param int $wx_kw_reply_id 关键词自动回复ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delWxKwReply($wx_kw_reply_id)
    {
        if (!is_numeric($wx_kw_reply_id)) return false;
        return $this->where('wx_kw_reply_id = ' . $wx_kw_reply_id)->save(array('isuse' => 2));
    }

    /**
     * 根据where子句获取关键词自动回复数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的关键词自动回复数量
     * @todo 根据where子句获取关键词自动回复数量
     */
    public function getWxKwReplyNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询关键词自动回复信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 关键词自动回复基本信息
     * @todo 根据SQL查询字句查询关键词自动回复信息
     */
    public function getWxKwReplyList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit()->select();
    }

    /**
     * 获取关键词自动回复列表页数据信息列表
     * @author 姜伟
     * @param array $wx_kw_reply_list
     * @return array $wx_kw_reply_list
     * @todo 根据传入的$wx_kw_reply_list获取更详细的关键词自动回复列表页数据信息列表
     */
    public function getListData($wx_kw_reply_list)
    {
        require_cache('Common/func_item.php');

		foreach ($wx_kw_reply_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic, is_gift');
			$wx_kw_reply_list[$k]['item_name']  = $item_info['item_name'];
            $wx_kw_reply_list[$k]['mall_price'] = $item_info['mall_price'];
			$wx_kw_reply_list[$k]['is_gift']    = $item_info['is_gift'];
			$wx_kw_reply_list[$k]['small_pic']  = small_img($item_info['base_pic']);

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$wx_kw_reply_list[$k]['status'] = $status;
		}

		return $wx_kw_reply_list;
    }
}

<?php
/**
 * 用户评价商家模型类
 */

class UserCommentMerchantModel extends BaseModel
{
    // 用户评价商家id
    public $user_comment_merchant_id;
   
    /**
     * 构造函数
     * @author 姜伟
     * @param $user_comment_merchant_id 用户评价商家ID
     * @return void
     * @todo 初始化用户评价商家id
     */
    public function UserCommentMerchantModel($user_comment_merchant_id)
    {
        parent::__construct('user_comment_merchant');

        if ($user_comment_merchant_id = intval($user_comment_merchant_id))
		{
            $this->user_comment_merchant_id = $user_comment_merchant_id;
		}
    }

    /**
     * 获取用户评价商家信息
     * @author 姜伟
     * @param int $user_comment_merchant_id 用户评价商家id
     * @param string $fields 要获取的字段名
     * @return array 用户评价商家基本信息
     * @todo 根据where查询条件查找用户评价商家表中的相关数据并返回
     */
    public function getUserCommentMerchantInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
    }

    /**
     * 修改用户评价商家信息
     * @author 姜伟
     * @param array $arr 用户评价商家信息数组
     * @return boolean 操作结果
     * @todo 修改用户评价商家信息
     */
    public function editUserCommentMerchant($arr)
    {
        return $this->where('user_comment_merchant_id = ' . $this->user_comment_merchant_id)->save($arr);
    }

    /**
     * 添加用户评价商家
     * @author 姜伟
     * @param array $arr 用户评价商家信息数组
     * @return boolean 操作结果
     * @todo 添加用户评价商家
     */
    public function addUserCommentMerchant($arr)
    {
        if (!is_array($arr)) return false;

		$arr['addtime'] = time();
		$arr['isuse'] = 1;
		$score = $arr['score'] ? $arr['score'] * 5 : 0;
		$arr['score'] = $score;
		$user_comment_merchant_id = $this->add($arr);

		$merchant_obj = new MerchantModel();
		$merchant_obj->recalculateSerialItem($arr['merchant_id'], 'score_avg', $score, true);
        return $user_comment_merchant_id;
    }

    /**
     * 删除用户评价商家
     * @author 姜伟
     * @param int $user_comment_merchant_id 用户评价商家ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delUserCommentMerchant($user_comment_merchant_id)
    {
        if (!is_numeric($user_comment_merchant_id)) return false;
		return $this->where('user_comment_merchant_id = ' . $user_comment_merchant_id)->delete();
    }

    /**
     * 根据where子句获取用户评价商家数量
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的用户评价商家数量
     * @todo 根据where子句获取用户评价商家数量
     */
    public function getUserCommentMerchantNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询用户评价商家信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return array 用户评价商家基本信息
     * @todo 根据SQL查询字句查询用户评价商家信息
     */
    public function getUserCommentMerchantList($fields = '', $where = '', $orderby = '', $limit = '')
    {
        return $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
    }

    /**
     * 获取用户评价商家列表页数据信息列表
     * @author 姜伟
     * @param array $user_comment_merchant_list
     * @return array $user_comment_merchant_list
     * @todo 根据传入的$user_comment_merchant_list获取更详细的用户评价商家列表页数据信息列表
     */
    public function getListData($user_comment_merchant_list)
    {
		foreach ($user_comment_merchant_list AS $k => $v)
		{
			//产品名称
			$item_obj = new ItemModel($v['item_id']);
			$item_info = $item_obj->getItemInfo('item_id = ' . $v['item_id'], 'item_name, isuse, stock, mall_price, base_pic');
			$user_comment_merchant_list[$k]['item_name'] = $item_info['item_name'];
			$user_comment_merchant_list[$k]['mall_price'] = $item_info['mall_price'];
			$user_comment_merchant_list[$k]['small_pic'] = $item_info['base_pic'];

			$status = '';
			if ($item_info['isuse'] == 0)
			{
				$status = '已下架';
			}
			elseif ($item_info['isuse'] == 1)
			{
				$status = $item_info['stock'] ? '上架中' : '缺货';
			}
			$user_comment_merchant_list[$k]['status'] = $status;
		}

		return $user_comment_merchant_list;
    }

    //商品是否齐全”、“配送及时”、“镖师态度良好
    //获取评论勾选列表
    //@author wsq
    public static function getCommentItemList()
    {
    
        return array(
            1 => '商品是否齐全',
            2 => '配送及时',
            3 => '镖师态度良好',
        );
    }
}

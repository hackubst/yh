<?php
class CollectApiModel extends ApiModel
{
	/**
	 * 获取收藏的商品列表(cheqishi.collect.getCollectList)
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回$collect_list，失败退出返回错误码
	 * @todo 获取收藏的商品列表(cheqishi.collect.getCollectList)
	 */
	function getCollectList($params)
	{
        //获取基本信息
		$firstRow    = isset($params['firstRow']) ? $params['firstRow'] : 0;
		$user_id     = intval(session('user_id'));

        //获取分页信息
		$collect_num_per_page = C('PER_PAGE_NUM');
		$collect_num_per_page = isset($params['fetch_num']) ? intval($params['fetch_num']) : $collect_num_per_page;

		//总数
		$collect_obj = new CollectModel();
		$where       = 'user_id = ' . $user_id;
		$total       = $collect_obj->getCollectNum($where);

		if ($user_id && ($total != 0 && $firstRow <= ($total - 1))) {
			$collect_obj->setStart($firstRow);
			$collect_obj->setLimit($collect_num_per_page);
			//获取订单列表
			$collect_list = $collect_obj->getCollectList('', $where, 'addtime DESC');
			$collect_list = $collect_obj->getListData($collect_list);

		} else {
			ApiModel::returnResult(40018, null, '没有更多记录了');

		}

		return array(
			'total_num'			=> $total,
			'nextFirstRow'		=> $collect_num_per_page + $firstRow,
			'collect_list'		=> $collect_list
		);

	}

	/**
	 * 添加收藏
	 * @author wsq
	 * @param array $params
	 * @return 成功返回'收藏成功'，失败返回错误码
	 * @todo 添加收藏
	 */
	function addCollect($params)
	{
		$item_id = $params['item_id'];
		$user_id = intval(session('user_id'));

        // 查询商品是否已经在收藏夹
		$where        = 'item_id = ' . $item_id . ' AND user_id = ' . $user_id;
		$collect_obj  = new CollectModel();
		$collect_info = $collect_obj->getCollectInfo($where, 'item_id');

		if ($collect_info) {
			ApiModel::returnResult(40024, null, '已收藏过该商品');

		}

		//添加
        $success = $collect_obj->addCollect(
            array(
                'item_id'	=> $item_id,
                'user_id'	=> $user_id,
            )
        );

		return '收藏成功';
	}

	/**
	 * 取消收藏(cheqishi.collect.cancelCollect)
	 * @author 姜伟
	 * @param array $params
	 * @return 成功返回'取消收藏成功'，失败返回错误码
	 * @todo 取消收藏(cheqishi.collect.cancelCollect)
	 */
	function cancelCollect($params)
	{
        //采集基本信息
		$item_id = $params['item_id'];
		$user_id = intval(session('user_id'));

        //查询商品是否在收藏夹
		$where        = 'item_id = ' . $item_id . ' AND user_id = ' . $user_id;
		$collect_obj  = new CollectModel();
		$collect_info = $collect_obj->getCollectInfo($where, 'item_id');

		if (!$collect_info)
		{
			ApiModel::returnResult(40025, null, '未收藏过该商品');
		}

		//收藏夹移除商品
		$success = $collect_obj->where('item_id = ' . $item_id . ' AND user_id = ' . $user_id)->delete();

		return '取消收藏成功';
	}

	/**
	 * 获取参数列表
	 * @author 姜伟
	 * @param 
	 * @return 参数列表 
	 * @todo 获取参数列表
	 */
	function getParams($func_name)
	{
		$params = array(
			'getCollectList'	=> array(
				array(
					'field'		=> 'firstRow', 
				),
				array(
					'field'		=> 'fetch_num', 
				),
			),
			'addCollect'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41025, 
					'empty_code'=> 44025, 
					'type_code'	=> 45025, 
				)
			),
			'cancelCollect'	=> array(
				array(
					'field'		=> 'item_id', 
					'type'		=> 'int', 
					'required'	=> true, 
					'miss_code'	=> 41025, 
					'empty_code'=> 44025, 
					'type_code'	=> 45025, 
				),
			),
		);

		return $params[$func_name];
	}
}

<?php
class AccountApiModel extends ApiModel
{
	/**
	 * 获取财务明细(cheqishi.account.getAccountList)
	 * @author 姜伟
	 * @param array $params 参数列表
	 * @return 成功返回$account_list，失败退出返回错误码
	 * @todo 获取财务明细(cheqishi.account.getAccountList)
	 */
	function getAccountList($params)
	{
		$num_per_page = C('PER_PAGE_NUM');
		$num_per_page = isset($params['fetch_num']) ? intval($params['fetch_num']) : $num_per_page;

		$user_id  = intval(session('user_id'));
		$firstRow = isset($params['firstRow']) ? intval($params['firstRow']) : 0;
		$where    = ' user_id = ' . $user_id;

		$account_obj = new AccountModel();
		//订单总数
		$total = $account_obj->getAccountNum($where);

		if ( $user_id && ($total > 0 && $firstRow <= ($total - 1))) {

			$account_obj->setStart($firstRow);
			$account_obj->setLimit($num_per_page);

            $account_list = $account_obj->getAccountList(
                'remark, addtime, amount_before_pay, amount_after_pay, amount_in, amount_out',
                $where,
                'addtime DESC'
            );

			foreach ($account_list AS $k => $v) {
				$account_list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);

			}
		}

		return array(
			'total_num'			=> $total,
			'nextFirstRow'		=> $firstRow + $num_per_page,
			'account_list'		=> $account_list,
		);

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
			'getAccountList'	=> array(
				array(
					'field'		=> 'firstRow', 
				),
				array(
					'field'		=> 'fetch_num', 
				),
			),
		);

		return $params[$func_name];
	}
}

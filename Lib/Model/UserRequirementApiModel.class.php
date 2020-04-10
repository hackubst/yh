<?php
class UserRequirementApiModel extends ApiModel
{
    /**
     * 获取需求列表
     * @author wsq
     */

    public function getRequireList($params){
        //获取基本信息
		$firstRow    = isset($params['firstRow']) ? $params['firstRow'] : 0;
		$user_id     = intval(session('user_id'));

        //获取分页信息
		$num_per_page = C('PER_PAGE_NUM');
		$num_per_page = isset($params['fetch_num']) ? intval($params['fetch_num']) : $num_per_page;

		//总数
        $where        = 'user_id = ' . $user_id;
		$ur_obj       = new UserRequirementModel();
		$total        = $ur_obj->getUserRequirementNum($where);   

		if ($user_id && ($total != 0 && $firstRow <= ($total - 1))) {
            $ur_obj->setStart($firstRow);
            $ur_obj->setLimit($num_per_page);

            //获取需求列表
            $req_list = $ur_obj->getUserRequirementList('', $where, 'addtime DESC');

		} else {
			ApiModel::returnResult(40018, null, '没有更多记录了');

		}

		return array(
			'total_num'			=> $total,
			'nextFirstRow'		=> $num_per_page + $firstRow,
			'require_list'		=> $req_list,
		);

    }

    /**
     * 获取需求详情
     * @author wsq
     */
    public function getRequireInfo($params) {

        $ur_id = intval($params['user_requirement_id']);
        $u_id  = intval(session('user_id'));

		$where    = 'user_id = ' . $u_id;
		$rl_obj   = M('UserRequirementLog');	
		$rp_info  = $rl_obj->where($where . ' AND user_requirement_id = ' . $ur_id)->find();
        log_file($rl_obj->getLastSql());

		$ur_info  = D('UserRequirement')->getUserRequirementById($ur_id);

        if (!$ur_info) ApiModel::returnResult(40018, null, '没有查到该记录了');

        return array(
            "require_info" => $ur_info,
            "reply_info"   => $rp_info ? $rp_info : "",
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
            'getRequireList' => array(
                array(
                    'field'		=> 'firstRow', 
                ),
                array(
                    'field'		=> 'fetch_num', 
                ),
            ),
            'getRequireInfo' => array(
                array(
                    'field'		=> 'user_requirement_id', 
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

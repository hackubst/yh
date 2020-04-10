<?php
/** 
 * @author 姜伟
 * @deprecated 入账申请
 * @
 * */
class AccountApplyModel extends Model
{
	/**
	 * 数据表中的主键值
	 */
	protected $AccountApplyId = 0;

    /** 
	 * 构造函数
     * @author 姜伟
     * @param void
     * @return void
     * @todo 初始化数据库，数据表
     * */
    public function AccountApplyModel($AccountApplyId)
    {
			$this->db(0);
			$this->tableName = C('DB_PREFIX') . 'account_apply';
                        $this->AccountApplyId = $AccountApplyId;
	}
   
    /** 
     * @author 姜伟
     * @deprecated 入账申请
     * @param array $apply_info 申请信息
     * @return void
     * @todo 验证参数有效性，过滤不必要参数，添加到数据库
     * */
    public function accountApply($apply_info)
    {
        return $this->add($apply_info);
    }
   
    /** 
     * @author 姜伟
     * @deprecated 通过入账申请
     * @param array $apply_info 申请信息
     * @return void
     * @todo 验证参数有效性，过滤不必要参数，保存到数据库
     * */
    public function passAccountApply($apply_info = array())
    {
         return $this->where( 'account_apply_id = ' .  $this->AccountApplyId)->save($apply_info);
    }
   
    /** 
     * @author 姜伟
     * @deprecated 拒绝入账申请
     * @param array $apply_info 申请信息
     * @return void
     * @todo 验证参数有效性，过滤不必要参数，保存到数据库
     * */
    public function refuseAccountApply($apply_info)
    {
    }
  
     /** 
     * @author 姜伟
     * @deprecated 根据$where字句条件获取入账申请数量
     * @param string $fields 所需字段列表，英文逗号隔开，不填则为全部
     * @param string $where 查询条件，where字句
     * @return int $num 入账申请数量
     * @todo 查询数据库，申请数量
     * @version 1.0
     * */
    public function getAccountApplyNumByQueryString($where)
    {
		$num = $this->where($where)->count();
		return $num;
    }

     /** 
     * @author 姜伟
     * @deprecated 获取未处理的入账申请数量
     * @param void
     * @return void
     * @todo 查询数据库，查询apply_state=0的订单数量
     * @version 1.0
     * */
    public function getUnhandledAccountApplyNum($where)
    {
		//$where = 'apply_state = 0';
		return $this->getAccountApplyNumByQueryString($where);
    }
    
    
     /** 
     * @author 姜伟
     * @deprecated 根据获取入账申请表id取出该信息
     * @return int  入账申请表id
     * @todo 查询数据库，入账申请id信息
     * @version 1.0
     * */
    public function getAccountApplyById($account_apply_id)
    {
		$account_apply_id = intval($account_apply_id);
		if ($account_apply_id)
		{
			return $this->where('account_apply_id = ' . $account_apply_id)->find();
		}
    }
    
    
    /** 
     * @author 姜伟
     * @deprecated 根据条件获取入账申请列表信息
     * @return array  入账申请列表信息
     * @todo 查询数据库，入账申请列表
     * @version 1.0
     * */
    public function getAccountApplyList($where, $field, $order, $limit)
    {
	return $this->where($where)->field($field)->order($order)->limit($limit)->select();
		 
    }
}

<?php
/**
 * 用户等级模型类
 * @ access public
 * @ Author : zhoutao0928@sina.com 、zhoutao@360shop.cc
 * @ Date 2014-03-10
 */

class UserRankModel extends Model
{
    private $user_rank_id;
	/**
	 * 构造函数
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 初始化数据库，数据表
	 */
	public function __construct($user_rank_id = 0)
	{
		parent::__construct();
		$this->user_rank_id = $user_rank_id;
	}

	/**
     * 获取用户等级总数
     * @author 姜伟
     * @param string $where
     * @return int $count
     * @todo 根据where查询条件查找用户等级表中的记录总数
     */
    public function getUserRankNum($where = '')
    {
		return $this->where($where)->count();
	}

    /**
     * 根据等级id获取折扣
     * @author zlf
     * @param string $rank_id
     * @return string 折扣
     * @todo 根据等级id获取折扣
     */
    public function getDiscountById($rank_id)
    {
        if (!is_numeric($rank_id))  return false;
        return $this->where('agent_rank_id = ' . $rank_id)->getField('discount');
    }

	/**
     * 获取用户等级信息列表
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $limit
     * @return null等级信息列表
     * @todo 根据where查询条件查找用户等级表中的相关数据并返回
     */
    public function getUserRankList($fields = '', $where = '', $orderby = '', $limit = '')
    {
		$user_rank_list = $this->field($fields)->where($where)->order($orderby)->limit($limit)->select();
		return $user_rank_list;
	}

	/**
     * 获取用户等级信息
     * @author 姜伟
     * @param string $where
     * @param string $fields
     * @return null或用户等级信息数组
     * @todo 根据where查询条件查找用户等级表中的相关数据并返回
     */
    public function getUserRankInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
	}

    /**
     * @access public
     * @todo 根据级别ID获取级别详情
     * 
     */
    public function getAgentRankInfoById($agent_rank_id)
    {
        $r = $this->where('agent_rank_id = '.$agent_rank_id)->find();
        return $r;
    }

	/**
     * 添加一条用户等级
     * @author 姜伟
     * @param array $arr
     * @return 成功返回插入的主键ID，失败返回false
     * @todo 将用户等级信息添加到用户等级表
     */
    public function addUserRank($arr)
    {
		if (!isset($arr['rank_name']) || !isset($arr['upgrade_money']) || !isset($arr['discount']))
		{
			return false;
		}

		//添加到数据库
		$user_rank_info = array(
			'rank_name'		=> $arr['rank_name'],
			'upgrade_money'	=> $arr['upgrade_money'],
			'discount'		=> $arr['discount'],
			'desc'			=> $arr['desc'] ? $arr['desc'] : '',
			'logo'			=> $arr['logo'] ? $arr['logo'] : '',
		);
		return $this->add($user_rank_info);
	}

	/**
     * 修改用户等级信息
     * @author 姜伟
     * @param array $arr
     * @return 成功返回1，失败返回0/false
     * @todo 修改用户等级信息
     */
    public function editUserRank($arr)
    {
		if (!$this->user_rank_id)
		{
			return false;
		}

		return $this->where('user_rank_id = ' . $this->user_rank_id)->save($arr);
	}

	/**
     * 删除用户等级
     * @author 姜伟
     * @param void
     * @return 成功返回1，失败返回0/false
     * @todo 删除用户等级
     */
    public function deleteUserRank()
    {
		if (!$this->user_rank_id)
		{
			return false;
		}

		//查看用户表中是否存在该用户等级关联的数据，若存在，不予删除
		$user_obj = new UserModel();
		$where = 'user_rank_id = ' . $this->user_rank_id;
		$user_num = $user_obj->getUserNum($where);
		if ($user_num)
		{
			return -1;
		}

		return $this->where('user_rank_id = ' . $this->user_rank_id)->delete();
	}
    /**
     * @access public
     * @todo 自动设置用户等级
     * 
     */
    public function autoSetUserRankByUserId($user_id)
    {
        $UserModel = new UserModel($user_id);
        $userinfo = $UserModel->getUserInfo();       //获取用户信息
        if(!$userinfo)
        {
           return FALSE;
        }
        
        $total_price = $userinfo['consumed_money'];            //用户当前消费的总金额（包含预存款以及已经消费的金额）
		$this->setLimit(1);
        $all_rank_info = $this->getUserRankList('', 'upgrade_money <= '.$total_price, 'upgrade_money DESC');          //所有当前用户还可以升级的级别，升序排序(额度大于用户当前总消费)
		$this->setLimit(null);
        $user_rank_info = $this->getUserRankInfo('user_rank_id = ' . $userinfo['user_rank_id']);       //用户当前级别的级别信息
        
        if($userinfo['is_rank_manual'])             //如果用户上一次的等级提升是管理员手动的
        {
            //获取该级别的消费额度
            if($total_price <= $user_rank_info['upgrade_money'])      //当前消费总额还不到管理员已经主动提升到的等级的额度，则不执行任何操作
            {
                return TRUE;
            }
            else if($total_price > $all_rank_info['upgrade_money'])  //额度已经足够再次提升了
            {
                $rank = $all_rank_info[0]['user_rank_id'];             //要升级到的级别
                $UserModel->setUserRank($rank);                         //执行操作
            }
            else
            {
                return FALSE;
            }
        }
        else
        {
            $rank = $all_rank_info[0]['user_rank_id'];         //要升级到的级别
            $UserModel->setUserRank($rank);                     //执行操作
        }
        return FALSE;
    }
}

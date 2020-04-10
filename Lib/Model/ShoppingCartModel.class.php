<?php
/**
 * 购物车模型基类
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
class ShoppingCartModel extends Model
{
	/**
	 * 购物车表主键ID
	 */
    public $shopping_cart_id = 0;
    
	/**
	 * where条件
	 */
    public $where = '';
     
    /**
     * 构造函数
     * @author 姜伟
     * @param $shopping_cart_id 购物车ID
     * @return void
     * @todo 初始化购物车id
     */
    public function __construct($shopping_cart_id)
    {
        parent::__construct('shopping_cart');

        if ($shopping_cart_id = intval($shopping_cart_id))
		{
            $this->shopping_cart_id = $shopping_cart_id;
		}

        $this->updateWhereCondition();
    }

    public function updateWhereCondition()
    {
		$where = 'user_id = ' . intval(session('user_id'));
        $item_ids = $this->where($where)->getField('item_id', true);

        if ($item_ids) {
            $filter_item_ids = D('Item')->where('isuse =1 AND stock > 0 AND item_id IN ('.join(',',$item_ids).')')->getField('item_id', true);
            if ($filter_item_ids) {
                $this->where = $where . ' AND item_id IN (' . join(',', $filter_item_ids).') ';
            } else {
                $this->where = 'false';
            }
        } else {
            $this->where = $where;
            return;
        }
    }

	/**
	 * 加入商品到购物车
	 * @author 姜伟
	 * @param int $item_id 商品ID
	 * @param int $item_sku_price_id 商品SKU ID
	 * @param array $item_info 包括代理商等级价$real_price，促销优惠后价格$total_price，商品数量$number, 商品名称item_name，商品小图small_pic，商品规格属性property
	 * @return 数据表中的影响行数 参数有误返回-1，失败返回0，成功返回1
	 * @todo 若$item_sku_price_id不为0，以之为准，否则以$item_id为准，若都不为大于0的整数，报错退出，验证参数有效性，组成数组后保存到数据库
	 */
    public function addShoppingCart($item_id, $item_sku_price_id, $item_info)
    {
		//验证参数有效性
		$valid_field = array('real_price', 'total_price', 'number', 'item_name', 'small_pic');
		foreach ($valid_field AS $v)
		{
			if (!isset($item_info[$v]) || !$item_info[$v])
			{
				//若缺少参数或参数值为空或0，直接返回-1
				return -1;
			}
		}
		if ($item_sku_price_id && (!isset($item_info['property']) || !$item_info['property']))
		{
			//若缺少参数或参数值为空或0，直接返回-1
			return -1;
		}

		$number = intval($item_info['number']);
		$number = $number ? $number : 0;
		$real_price = floatval($item_info['real_price']);
		$real_price = $real_price ? $real_price : 0.00;
		$total_price = floatval($item_info['total_price']);
		$total_price = $total_price ? $total_price : 0.00;
		$user_id = session('user_id');
		$user_id = $user_id ? $user_id : 0;
		$integral_num = floatval($item_info['integral_num']);
		$integral_num = $integral_num ? $integral_num : 0.00;
		$is_integral = $item_info['is_integral'] ? $item_info['is_integral'] : 0;

		//商品库存检测
		$item_obj = new ItemModel();
		if(!$item_info['package_tag']){
			$stock_enough = $item_obj->checkStockEnough($item_id, $item_sku_price_id, $number);
			//若库存不足，返回0
			if (!$stock_enough)
			{
				echo 'nostock';
				exit;
				#return 0;
			}
		}else{
			$stock_enough = $item_obj->checkPackageStockEnough($item_id, $number);
			#echo $item_obj->getLastSql();var_dump($stock_enough);die;
			//若库存不足，返回0
			if (!$stock_enough)
			{
				#$this->alert('库存不足，无法添加购物车');
				echo 'nostock';
				exit;
				#return 0;
			}
		}
		

		//查看购物车中是否已存在该商品
		$where = $this->where;

		if ($item_sku_price_id)
		{
			$where .= ' AND item_sku_price_id = ' . $item_sku_price_id;

		}
		else
		{
			if(!$item_info['package_tag']){
				$where .= ' AND item_id = ' . $item_id;
			}else{
				$where .= ' AND item_package_id = ' . $item_id;
			}

		}

		$shopping_cart_info = $this->field('shopping_cart_id, number, integral_num')->where($where)->find();

		if (!$shopping_cart_info)
		{
			//若购物车中不存在当前商品，直接插入
			//组织数组（普通商品）
			if(!$item_info['package_tag']){
				$item_arr = array(
					'user_id'			=> $user_id,
					'cookie_value'		=> $GLOBALS['shopping_cart_cookie'],
					'item_id'			=> $item_id,
					'item_sku_price_id'	=> $item_sku_price_id,
					'real_price'		=> $real_price,
					'mall_price'		=> $real_price,
					'total_price'		=> $total_price,
					'number'			=> $number,
					'integral_num'			=> $integral_num * $number,
					'item_name'			=> $item_info['item_name'],
					'small_pic'			=> $item_info['small_pic'],
					'property'			=> $item_info['property'],
					'is_integral'			=> $is_integral,
					'addtime'			=> time()
				);
			}else{
				//套餐商品
				$item_arr = array(
					'user_id'			=> $user_id,
					'cookie_value'		=> $GLOBALS['shopping_cart_cookie'],
					'item_package_id'			=> $item_id,
					'item_sku_price_id'	=> $item_sku_price_id,
					'real_price'		=> $real_price,
					'mall_price'		=> $real_price,
					'total_price'		=> $total_price,
					'number'			=> $number,
					'integral_num'			=> $integral_num * $number,
					'item_name'			=> $item_info['item_name'],
					'small_pic'			=> $item_info['small_pic'],
					'property'			=> $item_info['property'],
					'addtime'			=> time()
				);
			}
			

			return $this->add($item_arr);
				#print_r($this->add($item_arr))die;			

		}

		$number = $number + intval($shopping_cart_info['number']);
		$total_price = $number * floatval($real_price);
		//积分可抵扣金额
		$integral_num = $integral_num * $number;


		//更新数据库
		$arr = array(
			'number'		=> $number,
			'real_price'	=> $real_price,
			'mall_price'	=> $real_price,
			'total_price'	=> $total_price,
			'integral_num'		=> $integral_num,
			'is_integral'		=> $is_integral
		);
		#return $this->where('shopping_cart_id = ' . $shopping_cart_info['shopping_cart_id'])->save($arr);
		$this->where('shopping_cart_id = ' . $shopping_cart_info['shopping_cart_id'])->save($arr);
		/*若购物车中已有商品，重新计算优惠价后，更新已有商品的商品数量和价格end*/
		return $shopping_cart_info['shopping_cart_id'];
    }

	/**
	 * 批量删除购物车中的商品
	 * @author 姜伟
	 * @param array $item_info 商品信息列表，包括商品id，sku id
	 * @return 成功删除的数量
	 * @todo 若sku id不为0，where条件为sku id，否则为商品id
	 */
    public function batchDeleteShoppingCart($item_info)
    {
		//成功删除的个数
		$count_delete = 0;

		//遍历每一个商品，删除购物车表中的该商品
		foreach ($item_info AS $k => $v)
		{
			$where = '';
			$item_sku_price_id = intval($v['item_sku_price_id']);
			$item_id = intval($v['item_id']);
			$item_package_id = intval($v['item_package_id']);
			if ($item_sku_price_id)
			{
				$where = ' AND item_sku_price_id = ' . $item_sku_price_id;
			}
			elseif ($item_id)
			{
				$where = ' AND item_id = ' . $item_id;
			}
			elseif ($item_package_id)
			{
				$where = ' AND item_package_id = ' . $item_package_id;
			}
			else
			{
				continue;
			}

			$state = $this->where($this->where . $where)->delete();
			if ($state)
			{
				$count_delete ++;
			}
		}

		return $count_delete;
    }

	/**
	 * 获取当前对象有效数据字段
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 返回数据表中允许被编辑修改的字段列表
	 */
    public function getValidField()
    {
		return array('user_id', 'cookie_value', 'item_id', 'real_price', 'total_price', 'item_sku_price_id', 'number', 'addtime', 'integral_num');
    }
 
	/**
	 * 删除购物车中的单件商品
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 检验参数有效性，无效退出，有效则删除购物车表中的该商品
	 */
    public function deleteShoppingCart()
    {
		//检验$shopping_cart_id有效性
		if (!$this->checkShoppingCartIdValid($this->shopping_cart_id))
		{
			return false;
		}

		//删除购物车表中的该商品
		return $this->where($this->where . ' AND shopping_cart_id = ' . $this->shopping_cart_id)->delete();
    }
  
	/**
	 * 修改购物车中的单品信息
	 * @author 姜伟
	 * @param array $item_info 商品信息，包括商品价格，商品SKU ID，商品数量
	 * @return void
	 * @todo 检验参数有效性，调用saveShoppingCart方法保存到数据库
	 */
    public function editShoppingCart($item_info)
    {
		//过滤无效参数
		$valid_field = array('real_price', 'total_price', 'item_sku_price_id', 'number', 'integral_num');

		//验证参数有效性，过滤非法参数
		foreach ($item_info AS $key => $value)
		{
			if (!in_array($key, $valid_field))
			{
				unset($item_info[$key]);
			}
		}

		if ($item_info['number'] == 0)
		{
			return $this->deleteShoppingCart();
		}
		else
		{
			return $this->saveShoppingCart($item_info);
		}
    }

	/**
	 * 保存当前购物车中的单品信息到数据库
	 * @author 姜伟
	 * @param void
	 * @return void   
	 * @todo 检验参数有效性，去除多余参数，保存到数据库
	 */
    public function saveShoppingCart($item_info)
    {
		//验证当前对象购物车表主键ID有效性
		if (!$this->checkShoppingCartIdValid($this->shopping_cart_id))
		{
			return false;
		}

		$valid_field = $this->getValidField();

		//验证参数有效性，过滤非法参数
		foreach ($item_info AS $key => $value)
		{
			if (!in_array($key, $valid_field))
			{
				unset($item_info[$key]);
			}
		}

		//保存到数据库
		return $this->where($this->where . ' AND shopping_cart_id = ' . $this->shopping_cart_id)->save($item_info);
    }
  
	/**
	 * 检验购物车主键ID有效性
	 * @author 姜伟
	 * @param int $shopping_cart_id 
	 * @return void
	 * @todo 若为大于0的整数，有效，否则报错退出
	 */
    public function checkShoppingCartIdValid($shopping_cart_id)
    {
		$shopping_cart_id = intval($shopping_cart_id);
		if (!is_int($shopping_cart_id) || $shopping_cart_id < 1)
		{
			return false;
		}

		return true;
	}

	/**
	 * 清空购物车
	 * @author 姜伟
	 * @param void
	 * @return boolean
	 * @todo 根据user_id或cookie_value清空购物车
	 */
    public function clearShoppingCart()
    {
		return $this->where($this->where)->delete();
	}

	/**
     * 获得购物车中的商品总件数
     * @author 姜伟
     * @todo 从cart表取出当前用户商品的总件数
     */
	public function getShoppingCartNum($where)
	{
		$number = $this->where($this->where . $where)->sum('number');
		return $number ? $number : 0;
	}
 
	/**
	 * 统计当前用户的购物车总价
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从cart表统计当前用户的购物车总价
	 */
    public function sumCartAmount($where)
    {
        $this->updateWhereCondition();
		$total_price = $this->where($this->where . $where)->sum('total_price');
		return $total_price ? $total_price : 0.00;
    }
 
	/**
	 * 统计当前用户的购物车总优惠价
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 从cart表统计当前用户的购物车总优惠价
	 */
    public function sumCartCoupon()
    {
        $this->updateWhereCondition();
		$discount_amount = $this->where($this->where)->sum('real_price * number - total_price');
		return $discount_amount ? $discount_amount : 0.00;
    }

	/**
	 * 获取购物车内所有商品列表
	 * @author 姜伟
	 * @param string $fields 字段列表，英文逗号隔开
	 * @return void
	 * @todo 根据当前对象的where字段查询
	 */
    public function getShoppingCartList($fields = '', $where = '', $order = 'addtime DESC', $limit = '')
    {
		//获取购物车内所有商品
		$shopping_cart_list = $this->field($fields)->where($this->where . $where)->order($order)->select();

		return $shopping_cart_list;
    }

    /**
     * 获取购物车内所有商品列表
     * @author zlf
     * @param array $shopping_cart_list
     * @return array $shopping_cart_list
     * @todo 根据传入的$shopping_cart_list获取更详细的商品列表页数据信息列表
     */
    public function getListData($shopping_cart_list)
    {
        $user_id = intval(session('user_id'));
        $user_rank_info = D('User')->getUserInfo('user_rank_id', 'user_id = ' . $user_id);
        $user_rank_obj = new UserRankModel;
        
		foreach ($shopping_cart_list AS $k => $v)
		{			
            //vip价
            $user_rank = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_rank_info['user_rank_id'], 'discount');
            $shopping_cart_list[$k]['vip_price'] = round($user_rank['discount'] * $v['mall_price'] / 100, 2);
            //积分可抵扣金额
            #$shopping_cart_list[$k]['integral_num'] = round($v['mall_price'] * $v['integral_exchange_rate'] / 100, 2);    
            //计算单个商品总价
            $shopping_cart_list[$k]['total_price'] = $shopping_cart_list[$k]['vip_price'] * $shopping_cart_list[$k]['number']['number'];

		}

		return $shopping_cart_list;
    }

	/**
	 * 根据id或指定where条件获取购物车商品信息
	 * @author 姜伟
	 * @param string $fields 字段列表，英文逗号隔开
	 * @param string $where where查询子句
	 * @return array 查询结果，若找不到结果返回NULL
	 * @todo 根据当前对象的shopping_cart_id或指定where条件获取购物车商品信息
	 */
	public function getShoppingCartInfo($where = '', $fields = '')
	{
		$where = $this->where . ($where ? $where : ' AND shopping_cart_id = ' . $this->shopping_cart_id);

		return $this->field($fields)->where($where)->find();
	}

	/**
	 * 批量删除购物车中的商品
	 * @author 姜伟
	 * @param string $shopping_cart_ids
	 * @return array 查询结果，若找不到结果返回NULL
	 * @todo 批量删除购物车中的商品
	 */
	/*public function batchDeleteShoppingCart($shopping_cart_ids = '')
	{
		return $this->where('shopping_cart_id IN(' . $shopping_cart_ids . ')' . $this->where)->delete();
	}*/
}

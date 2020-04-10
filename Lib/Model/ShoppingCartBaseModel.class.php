<?php
/**
 * 购物车模型基类
 * @access public
 * @author 姜伟
 * @Date 2014-02-24
 */
class ShoppingCartBaseModel extends Model
{
	/**
	 * 购物车表主键ID
	 */
    public $shoppingCartId = 0;
    
	/**
	 * 购物车类型
	 */
    public $shoppingCartType = '';

	/**
	 * where条件
	 */
    public $where = '';
    
	/**
	 * 加入商品到购物车
	 * @author 姜伟
	 * @param int $item_id 商品ID
	 * @param int $item_sku_price_id 商品SKU ID
	 * @param int $number
	 * @return void
	 * @todo 若$item_sku_price_id不为0，以之为准，否则以$item_id为准，若都不为大于0的整数，报错退出
	 */
    public function addShoppingCart($item_id, $item_sku_price_id, $number)
    {
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
		//检验$shoppingCartId有效性
		$this->checkShoppingCartIdValid($this->shoppingCartId);

		//删除购物车表中的该商品
		$shoppingCartIdField = $this->getShoppingCartIdField();
		return $this->where($this->where . ' AND ' . $shoppingCartIdField . ' = ' . $this->shoppingCartId)->delete();
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
		$valid_field = array('real_price', 'total_price', 'item_sku_price_id', 'number');

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
		$this->checkShoppingCartIdValid($this->shoppingCartId);

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
		$shoppingCartIdField = $this->getShoppingCartIdField();
		return $this->where($this->where . ' AND ' . $shoppingCartIdField . ' = ' . $this->shoppingCartId)->save($item_info);
    }
   
	/**
	 * 计算某用户购物车中的选中商品总价格
	 * @author 姜伟
	 * @param int $user_id 用户ID
	 * @param string $item_ids 商品ID列表，英文逗号隔开，若为空则计算所有商品总价格
	 * @return float $total_amount 购物车商品总价格
	 * @todo 若$user_id大于0，则按user_id统计，否则报错退出
	 */
    public function calculateAmountByIds($user_id, $item_ids = '')
    {
		//验证参数有效性
		$user_id = intval($user_id);
		if (!$user_id)
		{
			trigger_error(__CLASS__ . ', ' . __FUNCTION__ . ', 非法的参数user_id');
		}

		//根据参数$item_ids获取查询条件
		$where = $item_ids ? 'item_id in ("' . $item_ids . ')"' : '';

		//查询数据库并计算商品总价
		$item_info = array(
			'total_amount' => 0.00
		);

		if ($this->shoppingCartType == 'Agent')
		{
			$item_info = $this->field('SUM(total_price) AS total_amount')->where($where)->find();
		}
		else
		{
			$item_info = $this->field('SUM(real_price * number) AS total_amount')->where($where)->find();
		}

		return $item_info['total_amount'];
    }

	/**
	 * 检验购物车主键ID有效性
	 * @author 姜伟
	 * @param int $shoppingCartId 
	 * @return void
	 * @todo 若为大于0的整数，有效，否则报错退出
	 */
    public function checkShoppingCartIdValid($shoppingCartId)
    {
		$shoppingCartId = intval($shoppingCartId);
		if (!is_int($shoppingCartId) || $shoppingCartId < 1)
		{
			trigger_error(__CLASS__ . ', ' . __FUNCTION__ . ', 无效的参数shoppingCartId');
		}
	}

	/**
	 * 根据购物车类型获取购物车主键字段名
	 * @author 姜伟
	 * @param void
	 * @return string $shoppingCartIdField 购物车表主键字段名
	 * @todo 根据购物车类型获取购物车主键字段名
	 */
    public function getShoppingCartIdField()
    {
		$shoppingCartIdField = ($this->shoppingCartType == 'Agent') ? 'shopping_cart_id' : 'buyer_shopping_cart_id';
		return $shoppingCartIdField;
	}
 
	/**
	 * 获取当前对象有效数据字段，空方法，子类覆盖实现
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 返回数据表中允许被编辑修改的字段列表
	 */
    public function getValidField()
    {
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
     * @author 陆宇峰
     * @todo 从cart表取出当前用户商品的总件数
     */
	public function countCartItems()
	{
		$number = $this->where($this->where)->sum('number');
		return $number ? $number : 0;
	}
 
	/**
	 * 统计当前用户的购物车总价
	 * @author 姜伟
	 * @param int $contain_virtual_stock_items 是否统计虚拟仓商品价格
	 * @return void
	 * @todo 从cart表统计当前用户的购物车总价
	 */
    public function sumCartAmount($contain_virtual_stock_items = 0)
    {
		$where = $contain_virtual_stock_items ? ' AND from_virtual_stock = 0' : '';
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
    public function getCartItemList($fields = '')
    {
		//获取购物车内所有商品
		$item_list = $this->field($fields)->where($this->where)->order('addtime DESC')->select();

		return $item_list;
    }

	/**
	 * 根据id或指定where条件获取购物车商品信息
	 * @author 姜伟
	 * @param string $fields 字段列表，英文逗号隔开
	 * @param string $where where查询子句
	 * @return array 查询结果，若找不到结果返回NULL
	 * @todo 根据当前对象的shoppingCartId或指定where条件获取购物车商品信息
	 */
	public function getShoppingCartInfo($fields = '', $where = '')
	{
		$shoppingCartIdField = $this->getShoppingCartIdField();
		$where = $this->where . ($where ? $where : ' AND ' . $shoppingCartIdField . ' = ' . $this->shoppingCartId);

		return $this->field($fields)->where($where)->find();
	}
}

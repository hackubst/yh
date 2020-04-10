<?php
/**
 * 商品模型类
 */

class ItemBaseModel extends Model
{

    // 商品id
    public $item_id;
    
    // 商品信息数组
    public $item_info = array();

    /**
     * 构造函数
     * @author 张勇
     * @param $item_id 商品ID
     * @return void
     * @todo 初始化商品id
     */
    public function ItemBaseModel($item_id)
    {
        parent::__construct('item');

        if ($item_id = intval($item_id))
            $this->item_id = $item_id;
    }

    /**
     * 上架指定ID商品
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo 将商品表“isuse”的值设为1
     */
    public function displayItem($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->save(array('isuse' => 1));
    }

    /**
     * 批量上架指定商品ID列表商品
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @return boolean 操作结果
     * @todo 将商品表“isuse”的值设为1
     */
    public function batchDisplayItem($arr_id)
    {
        if (!is_array($arr_id))  return false;
        $data = implode(',', $arr_id);
        return $this->where('item_id IN (' . $data . ')')->save(array('isuse' => 1));
    }

    /**
     * 下架指定ID商品
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo 将商品表“isuse”的值设为0
     */
    public function moveStorage($item_id)
    {
        if ($item_id = intval($item_id))
            $this->item_id = $item_id;

        // 判断商品id是否有效
        if (!$this->item_id) return false;

        // 执行更新操作
        return $this->where('item_id = ' . $this->item_id)->save(array('isuse' => 0));
    }

    /**
     * 批量下架指定商品ID列表商品
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @return boolean 操作结果
     * @todo 将商品表“isuse”的值设为0
     */
    public function batchMoveStorage($arr_id)
    {
        if (!is_array($arr_id))  return false;
        $data = implode(',', $arr_id);
        return $this->where('item_id IN (' . $data . ')')->save(array('isuse' => 0));
    }

    /**
     * 通过商品ID获取商品库存
     * @author 张勇
     * @param int $item_id 商品id
     * @return int 商品库存
     * @todo 通过商品ID获取商品库存
     */
    public function getStockById($item_id)
    {
        if (!is_numeric($item_id))  return false;
        return $this->where('item_id = ' . $item_id)->getField('stock');
    }

    /**
     * 通过商品货号获取商品库存
     * @author 张勇
     * @param int $item_sn 商品货号
     * @return int 商品库存
     * @todo 通过商品货号获取商品库存
     */
    public function getStcokBySn($item_sn)
    {
        return $this->where("item_sn = '" . $item_sn . "'")->getField('stock');
    }

    /**
     * 设置指定商品库存
     * @author 张勇
     * @param int $item_id 商品id
     * @param int $stock 库存
     * @return boolean 操作结果
     * @todo 设置指定商品库存
     */
    public function setStock($item_id, $stock)
    {
        if (!is_numeric($item_id) || !is_numeric($stock))   return false;
        return $this->where('item_id = ' . $item_id)->save(array('stock' => $stock));
    }

    /**
     * 通过商品ID获取商品市场价
     * @author 张勇
     * @param int $item_id 商品id
     * @return float 商品市场价
     * @todo 通过商品ID获取商品市场价
     */
    public function getMarketPrice($item_id)
    {
        if (!is_numeric($item_id))  return false;
        return $this->where('item_id = ' . $item_id)->getField('market_price');
    }

    /**
     * 通过商品ID获取商品批发价
     * @author 张勇
     * @param int $item_id 商品id
     * @return float 商品批发价
     * @todo 通过商品ID获取商品批发价
     */
    public function getWholesalePrice($item_id)
    {
        if (!is_numeric($item_id))  return false;
        return $this->where('item_id = ' . $item_id)->getField('wholesale_price');
    }

    /**
     * 设置指定商品的批发价
     * @author 张勇
     * @param int $item_id 商品id
     * @param float $price 批发价
     * @return boolean 操作结果
     * @todo 设置指定商品的批发价
     */
    public function setItemWholesalePrice($item_id, $price)
    {
        if (!is_numeric($item_id) || !is_numeric($price))   return false;
        return $this->where('item_id = ' . $item_id)->save(array('wholesale_price' => $price));
    }
    
    
    /**
     * 获取商品表最新一条记录
     * @author 张勇
     * @param int $item_id 商品id
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 通过商品ID获取商品信息
     */
    public function getItemInfoByMax()
    {
        return $this->max('item_id');
    }
    

    /**
     * 通过商品ID获取商品信息
     * @author 张勇
     * @param int $item_id 商品id
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 通过商品ID获取商品信息
     */
    public function getItemInfoById($item_id, $fields = null)
    {
        if (!is_numeric($item_id))  return false;
        return $this->field($fields)->where('item_id = ' . $item_id)->find();
    }
    
    /**
     * 通过商品ID获取当前可用商品的信息
     * @author zhoutao
     * @param int $item_id 商品id
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 通过商品ID获取商品信息
     */
    public function getUsefulItemInfoById($item_id, $fields = null)
    {
    	if (!is_numeric($item_id))  return false;
    	return $this->field($fields)->where('item_id = ' . $item_id.' AND isuse = 1 AND is_del = 0')->find();
    }

    /**
     * 通过商品货号获取商品信息
     * @author 张勇
     * @param int $item_sn 商品货号
     * @return array 商品基本信息
     * @todo 通过商品货号获取商品信息
     */
    public function getItemInfoBySn($item_sn)
    {
        return $this->where('is_del = 0 AND item_sn = "' . $item_sn .'"')->find();
    }

    /**
     * 修改指定商品的信息
     * @author 张勇
     * @param int $item_id 商品id
     * @param array $arr_item_info 商品信息数组
     * @return boolean 操作结果
     * @todo 修改指定商品的信息
     */
    public function setItem($item_id, $arr_item_info)
    {
        if (!is_numeric($item_id) || !is_array($arr_item_info)) return false;
        return $this->where('item_id = ' . $item_id)->save($arr_item_info);
    }

    /**
     * 批量修改商品信息
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @param array $arr_item_info 商品信息数组
     * @return boolean 操作结果
     * @todo 批量修改商品信息
     */
    public function batchSetItem($arr_id, $arr_item_info)
    {
        if (!is_array($arr_id)) return false;
        $data = implode(',', $arr_id);
        return $this->where('item_id IN (' . $data . ')')->save($arr_item_info);
    }

    /**
     * 添加商品
     * @author 张勇
     * @param array $arr_item_info 商品信息数组
     * @return boolean 操作结果
     * @todo 添加商品
     */
    public function addItem($arr_item_info)
    {
        if (!is_array($arr_item_info)) return false;

        $data = $arr_item_info;
        return $this->add($data);
    }

    /**
     * 删除商品
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function delItem($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->save(array('is_del' => 1));
    }

    /**
     * 批量删除商品
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @return boolean 操作结果
     * @todo is_del设为1
     */
    public function batchDelItem($arr_id)
    {
        if (!is_array($arr_id)) return false;
        $data = implode(',', $arr_id);
        return $this->where('item_id IN (' . $data . ')')->save(array('is_del' => 1));
    }

    /**
     * 还原商品到出售中
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo is_del设为0，isuse设为1
     */
    public function restoreToSale($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->save(array('is_del' => 0, 'isuse' => 1));
    }

    /**
     * 还原商品到仓库中
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo is_del设为0，isuse设为0
     */
    public function restoreToStore($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->save(array('is_del' => 0, 'isuse' => 0));
    }

    /**
     * 彻底删除商品
     * @author 张勇
     * @param int $item_id 商品ID
     * @return boolean 操作结果
     * @todo is_del设为2
     */
    public function deepDelItem($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->save(array('is_del' => 2));
    }

    /**
     * 批量还原删除商品到出售中
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @return boolean 操作结果
     * @todo is_del设为0, isuse设为1
     */
    public function batchToSale($arr_id)
    {
        if (!is_array($arr_id)) return false;
        $data = implode(',', $arr_id);
        return $this->where('item_id IN (' . $data . ')')->save(array('is_del' => 0, 'isuse' => 1));
    }

    /**
     * 批量还原删除商品到仓库里
     * @author 张勇
     * @param array $arr_id 商品ID数组
     * @return boolean 操作结果
     * @todo is_del设为0，isuse设为0
     */
    public function batchToStore($arr_id)
    {
        if (!is_array($arr_id)) return false;
        $data = implode(',', $arr_id);
        return $this->where('item_id IN (' . $data . ')')->save(array('is_del' => 0, 'isuse' => 0));
    }

    /**
     * 根据条件获取商品列表
     * @author 张勇
     * @param string $field 需要的字段
     * @param string $where where子句
     * @param string $order order子句
     * @param array 商品列表
     * @todo 根据条件获取商品列表
     */
    public function listItem($where = null, $order = null, $field = null)
    {
        return $this->field($field)->where($where)->order($order)->limit()->select();
    }

    /**
     * 根据where子句获取商品数量
     * @author 张勇
     * @param string|array $where where子句
     * @return int 满足条件的商品数量
     * @todo 根据where子句获取商品数量
     */
    public function getCountByWhere($where = null)
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询商品信息
     * @author 张勇
     * @param string $where where子句条件字符串
     * @return array 商品基本信息
     * @todo 根据SQL查询字句查询商品信息
     */
    public function getItemListByQueryString($where, $limit = null)
    {
        return $this->where($where)->limit($limit)->select();
    }

    /**
     * 通过一级分类ID获取关联的商品列表
     * @author 张勇
     * @param int $class_id 一级分类ID
     * @param string $field 需要的字段，多个字段用‘,’隔开
     * @return array 商品列表
     * @todo 通过一级分类ID获取关联的商品列表
     */
    public function getItemListByClassId($class_id, $field = null)
    {
        if (!is_numeric($class_id))   return false;
        return $this->where('class_id = ' . $class_id)->field($field)->select();
    }

    /**
     * 通过二级分类ID获取关联的商品列表
     * @author 张勇
     * @param int $sort_id 二级分类ID
     * @param string $field 需要的字段，多个字段用‘,’隔开
     * @return array 商品列表
     * @todo 通过二级分类ID获取关联的商品列表
     */
    public function getItemListBySortId($sort_id, $field = null)
    {
        if (!is_numeric($sort_id))   return false;
        return $this->where('class_sort_id = ' . $sort_id)->field($field)->select();
    }

    /**
     * 通过三级分类ID获取关联的商品列表
     * @author 张勇
     * @param int $genre_id 三级分类ID
     * @param string $field 需要的字段，多个字段用‘,’隔开
     * @return array 商品列表
     * @todo 通过三级分类ID获取关联的商品列表
     */
    public function getItemListByGenreId($genre_id, $field = null)
    {
        if (!is_numeric($genre_id))   return false;
        return $this->where('class_genre_id = ' . $genre_id)->field($field)->select();
    }


    /**
     * 通过一级分类ID获取关联的商品总数
     * @author 张勇
     * @param int $class_id 一级分类ID
     * @return array 商品列表
     * @todo 通过一级分类ID获取关联的商品列表
     */
    public function getItemCountByClassId($class_id)
    {
        if (!is_numeric($class_id))   return false;
        return $this->where('isuse = 1 AND is_del = 0 AND class_id = ' . $class_id)->count();
    }

    /**
     * 通过二级分类ID获取关联的商品列表
     * @author 张勇
     * @param int $sort_id 二级分类ID
     * @return array 商品列表
     * @todo 通过二级分类ID获取关联的商品列表
     */
    public function getItemCountBySortId($sort_id)
    {
        if (!is_numeric($sort_id))   return false;
        return $this->where('isuse = 1 AND is_del = 0 AND class_sort_id = ' . $sort_id)->count();
    }

    /**
     * 通过三级分类ID获取关联的商品列表
     * @author 张勇
     * @param int $genre_id 三级分类ID
     * @return array 商品列表
     * @todo 通过三级分类ID获取关联的商品列表
     */
    public function getItemCountByGenreId($genre_id)
    {
        if (!is_numeric($genre_id))   return false;
        return $this->where('isuse = 1 AND is_del = 0 AND class_genre_id = ' . $genre_id)->count();
    }

    /**
     * 获取某商品等级价，分销商等级折扣，商品混批优惠价
     * @author 姜伟
     * @param int $item_id 商品ID
     * @param int $agent_rank_id 分销商等级ID
     * @return array 
	 * @todo 获取某商品等级价，分销商等级折扣，商品混批优惠价
     */
    public function getItemPriceInfo($item_id, $agent_rank_id = 0)
    {
		$rank_price = 0.00;
		$discount_rate = 100;

		if ($agent_rank_id)
		{
			//取等级价
			$item_rank_price_obj = new ItemPriceRankModel();
			$rank_price = $item_rank_price_obj->getItemRankPrice($agent_rank_id, $item_id);

			if (!$rank_price)
			{
				$agent_rank_obj = new AgentRankModel($agent_rank_id);
				$discount_rate = $agent_rank_obj->getDiscountById($agent_rank_id);
				$discount_rate = ($discount_rate && $discount_rate > 0 && $discount_rate < 100) ? $discount_rate : 100;
			}
		}

		$item_price_arr = array(
			'rank_price'		=> $rank_price,
			'discount_rate'		=> $discount_rate,
		);
	
        return $item_price_arr;
    }

    /**
     * 计算商品实际支付价格
     * @author 张勇
     * @param int $item_id 商品ID
     * @param int $sku_id 规格属性ID
     * @param int $number 商品数量
     * @param int $agent_rank_id 分销商等级ID
     * @return float 实际支付价格
	 * @todo 姜伟改于2014-04-24. 计算商品实际支付价格：
	 * $pay_price = 0，$discount_rate = 100;
	 * 0、未登录，跳4；
	 * 1、若$sku_id不为0，按$sku_id和分销商等级ID $agent_rank_id查找等级价格表中该sku该等级的价格$sku_rank_price，若有记录，$pay_price = $sku_rank_price，跳7；若无记录，跳2；
	 * 2、按$item_id和agent_rank_id查找等级价格表中该商品的价格$item_rank_price，若有记录，$pay_price = $item_rank_price，跳7；否则跳3；
	 * 3、取分销商等级表中该分销商等级折扣$rank_discount_rate，若存在，$discount_rate = $rank_discount_rate，跳4；
	 * 4、若$sku_id不为0，按$sku_id取sku价格表中的sku价格$sku_price，$pay_price = $sku_price，跳6；否则跳5；
	 * 5、按$item_id取商品批发价$wholesale_price，$pay_price = $wholesale_price，跳6；
	 * 6、$pay_price *= $pay_price，跳7；
	 * 7、按$item_id取混批规则表中该商品当前数量优惠价$discount_price，$pay_price -= $discount_price，返回$pay_price；
     */
    public function calculateItemRealPrice($item_id, $sku_id, $number, $agent_rank_id = 0, $calculate_wholesale_rule = true)
    {
        if (!is_numeric($item_id) || !is_numeric($sku_id) || !is_numeric($number) || !is_numeric($agent_rank_id))   return false;
		
		//初始化
		// 分销商等级
		if (!$agent_rank_id)
		{
			$user_id = session('user_id');
			$Users   = M('Users');
			$agent_rank_id = $Users->where('user_id = ' . $user_id)->getField('agent_rank_id');
		}
		$pay_price = 0.00;
		$rank_price = 0.00;
		$discount_rate = 100;

		if ($agent_rank_id)
		{
			//1 & 2
			$item_rank_price_obj = new ItemPriceRankModel();
			$rank_price = $item_rank_price_obj->getItemRankPrice($agent_rank_id, $item_id, $sku_id);
			if (!$rank_price)
			{
				//3
				$agent_rank_obj = new AgentRankModel($agent_rank_id);
				$discount_rate = $agent_rank_obj->getDiscountById($agent_rank_id);
				$discount_rate = ($discount_rate && $discount_rate > 0 && $discount_rate < 100) ? $discount_rate : 100;
			}
			else
			{
				$pay_price = $rank_price;
			}
		}

		if (!$pay_price)
		{
			if ($sku_id)
			{
				// SKU价格
				$Item_sku  = D('ItemSku');
				$sku_info = $Item_sku->getSkuInfo($sku_id, 'sku_price');
				$pay_price = $sku_info ? $sku_info['sku_price'] : $pay_price;
			}
			else
			{
				//5
				$wholesale_price = $this->getWholesalePrice($item_id);
				$pay_price = $wholesale_price ? $wholesale_price : $pay_price;
			}
		}

		//6
		if (!$rank_price)
		{
			$pay_price = $pay_price * floatval($discount_rate / 100);
		}
		
		//7
		if ($calculate_wholesale_rule)
		{
			$itemWholesaleRule = M('item_wholesale_rule');
			$discount_price = $itemWholesaleRule->where('item_id=' . $item_id . ' AND start_num<=' . $number . ' AND end_num>=' . $number)->getField('price');
			$discount_price = $discount_price ? $discount_price : 0.00;
			$pay_price -= $discount_price;
		}
		$pay_price = $pay_price < 0 ? 0.00 : $pay_price;
	
        return $pay_price;
    }

	/**
     * 判断商品库存是否足够
     * @author 姜伟
     * @param int $item_id 商品ID
     * @param int $item_sku_price_id 商品sku id
     * @param int $number 商品数量
     * @return boolean 足够返回true，不足返回false
     * @todo 计算商品实际支付价格
     */
	public function checkStockEnough($item_id, $item_sku_price_id = 0, $number = 1)
	{
		$stock = 0;

		//判断虚拟仓中是否有货
		$user_id = intval(session('user_id'));
		$virtual_stock_obj = new VirtualStockModel();
		$stock = $virtual_stock_obj->getItemStock($user_id, $item_id, $item_sku_price_id);
		if ($stock && ($stock - intval($number)) >= 0)
		{
			//有货并且数量足够
			return true;
		}

		if ($item_sku_price_id)
		{
			$ItemSkuModel = new ItemSkuModel();
			$item_info = $ItemSkuModel->getSkuInfo($item_sku_price_id, 'sku_stock');
			#echo $ItemSkuModel->getLastSql();die;
			if (!$item_info)
			{
				return false;
			}
			$stock = $item_info['sku_stock'] - $number;
		}
		else
		{
			$item_obj = new ItemBaseModel();
			$stock = intval($item_obj->getStockById($item_id));
			$stock = $stock - $number;
		}

		return $stock < 0 ? false : true;
	}

	/**
     * 加库存
     * @author 姜伟
     * @param int $item_id 商品ID
     * @param int $item_sku_price_id 商品sku id
     * @param int $number 商品数量
     * @return 成功返回true，失败返回false
     * @todo 若item_sku_price_id不为0，加SKU表中商品的库存，否则减加item表的库存
     */
	public function addItemStock($item_id, $item_sku_price_id = 0, $number = 1)
	{
		//成功修改记录
		$num = 0;

		//先加上商品总库存
		$num = $this->where('isuse = 1 AND item_id = ' . $item_id)->setInc('stock', $number);

		//若存在sku，再加上sku的库存
		if ($item_sku_price_id)
		{
			$ItemSkuModel = new ItemSkuModel();
			$num = $ItemSkuModel->addItemStock($item_sku_price_id, $number);
		}

		//减少销量
		$this->where('isuse = 1 AND item_id = ' . $item_id)->setDec('sales_num', $number);

		return $num;
	}

	/**
     * 减库存
     * @author 姜伟
     * @param int $item_id 商品ID
     * @param int $item_sku_price_id 商品sku id
     * @param int $number 商品数量
     * @return 成功返回true，失败返回false
     * @todo 若item_sku_price_id不为0，减SKU表中商品的库存，否则减item表的库存
     */
	public function deductItemStock($item_id, $item_sku_price_id = 0, $number = 1)
	{
		//成功修改记录
		$num = 0;

		//先减去商品总库存
		$num = $this->where('isuse = 1 AND item_id = ' . $item_id)->setDec('stock', $number);

		//若存在sku，再减去sku的库存
		if ($item_sku_price_id)
		{
			$ItemSkuModel = new ItemSkuModel();
			$num = $ItemSkuModel->deductItemStock($item_sku_price_id, $number);
		}

		//增加销量
		$this->where('isuse = 1 AND item_id = ' . $item_id)->setInc('sales_num', $number);

		return $num;
	}

	/**
	 * 减少商品销量
	 * @author 姜伟
	 * @param int $item_id
	 * @param int $number
	 * @return boolean
	 * @todo 将item_id为$item_id的商品销量减少$number
	 */
	public function deductItemSalesNum($item_id, $number)
	{
		$this->where('item_id = ' . $item_id)->setDec('sales_num', $number);
	}
	
	/**
	 * @access public
	 * @todo 根据商品ID获取商品的起批范围信息
	 * 
	 * @author zhoutao@360shop.cc zhoutao0928@sina.com
	 * @data 2014-04-21
	 */
	public function getWholeSaleRuleByItemId($item_id)
	{
		if(!$item_id)
		{
			return FALSE;
		}
		$item_wholesale_rule = M('item_wholesale_rule');
		$result = array();
		$result = $item_wholesale_rule->where('item_id = '.$item_id)->order('start_num ASC')->select();
		return $result;
	}
	
	
}

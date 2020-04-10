<?php
/**
 * 商品模型类
 */

class ItemModel extends Model
{

    // 商品id
    public $item_id;
    
    // 商品信息数组
    public $item_info = array();

    const ITEM_ONLIE = 1;
    const ITEM_OFFLINE = 0;
    const ITEM_DELETE = 2;

    const TABLE_NAME  = 'item';
    const PRIMARY_KEY = 'item_id';

    // auto complete
    protected $_auto = array(
        array('isuse','1', Model::MODEL_BOTH),
        array('addtime','time', Model::MODEL_BOTH, 'function'),
        //array('mall_price', 'market_price',Model::MODEL_BOTH, 'field'),
        array('market_price', 'mall_price',Model::MODEL_BOTH, 'field'),
    );

    // validate
    protected $_validate = array(
        //array('password','checkPwd','密码格式不正确',0,'function'),
    );

    protected $_map = array(
        'GoodsName' => 'item_name',
        'GoodsCode' => 'goods_code',
        //'PurchPrice' => 'market_price',
        'SalePrice' => 'mall_price',
        'BaseBarCode' => 'item_sn',
        'StockAmount' => 'stock',
    );

    /**
     * 修改
     * @author wsq
     * @param array $arr 信息数组
     * @return boolean 操作结果
     * @todo 修改信息
     */
    public function editRecord($arr, $id=false)
    {
        $opt = $id ? Model::MODEL_UPDATE : Model::MODEL_INSERT;

        if (!is_array($arr)) {return false;} //参数不合法
        if ($this->create($arr, $opt)) return ($id ? $this->where(self::PRIMARY_KEY . ' = %d ', $id)->save() : $this->add()); 
        else return false; //数据获取出错
    }

    /**
     * 构造函数
     * @author 张勇
     * @param $item_id 商品ID
     * @return void
     * @todo 初始化商品id
     */
    public function ItemModel($item_id)
    {
        parent::__construct('item');
        require_cache('Common/func_item.php');

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
        return $this->where('item_id = ' . $item_id)->getField('mall_price');
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
        return $this->where('item_id = ' . $item_id)->save(array('mall_price' => $price));
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
     * 获取奖品信息
     * @author 姜伟
     * @param int $item_id 商品id
     * @param string $fields 要获取的字段名
     * @return array 商品基本信息
     * @todo 根据where查询条件查找商品表中的相关数据并返回
     */
    public function getItemInfo($where, $fields = '')
    {
		return $this->field($fields)->where($where)->find();
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
     * 修改商品信息
     * @author 姜伟
     * @param array $arr 商品信息数组
     * @return boolean 操作结果
     * @todo 修改商品信息
     */
    public function editItem($arr)
    {
        return $this->where('item_id = ' . $this->item_id)->save($arr);
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
        return $this->where('item_id = ' . $item_id)->save(array('isuse' => 2));
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
        return $this->where('item_id IN (' . $data . ')')->save(array('isuse' => 2));
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
        return $this->where('item_id = ' . $item_id)->save(array('isuse' => 1));
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
        return $this->where('item_id = ' . $item_id)->save(array('isuse' => 0));
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
     * @author 姜伟
     * @param string|array $where where子句
     * @return int 满足条件的商品数量
     * @todo 根据where子句获取商品数量
     */
    public function getItemNum($where = '')
    {
        return $this->where($where)->count();
    }

    /**
     * 根据where子句查询商品信息
     * @author 姜伟
     * @param string $fields
     * @param string $where
     * @param string $orderby
     * @param string $groupby
     * @return array 商品基本信息
     * @todo 根据SQL查询字句查询商品信息
     */
    public function getItemList($fields = '', $where = '', $orderby = '', $groupby = '')
    {
        return $this->field($fields)->where($where)->group($groupby)->order($orderby)->limit()->select();
    }

    /**
     * 获取商品列表页数据信息列表
     * @author 姜伟
     * @param array $item_list
     * @return array $item_list
     * @todo 根据传入的$item_list获取更详细的商品列表页数据信息列表
     */
    public function getListData($item_list)
    {
        $user_id = intval(session('user_id'));
        $user_rank_info = D('User')->getUserInfo('user_rank_id', 'user_id = ' . $user_id);
        $user_rank_obj = new UserRankModel();
        
		foreach ($item_list AS $k => $v)
		{
			$item_list[$k]['link_item'] = U('/FrontMall/item_detail/item_id/' . $v['item_id']);
            //$item_list[$k]['small_img'] = $v['base_pic'] ? small_img($v['base_pic']) : '';
            //由于后台上传商品图时删除了小图，故前台出现很多没法显示图片的bug，即先按原图赋值给小图的方式来处理
			$item_list[$k]['small_img'] = $v['base_pic'] ? $v['base_pic'] : '';
			$item_list[$k]['middle_img'] = $v['base_pic'] ? middle_img($v['base_pic']) : '';
			$status = '';
			if ($v['isuse'] == 0)
			{
				$status = '仓库中';
			}
			else
			{
				$status = $v['stock'] > $v['stock_alarm'] ? '出售中' : ($v['stock'] < 1 ? '缺货中' : '库存报警');
			}
			$item_list[$k]['status'] = $status;
            //vip价
            if ($user_rank_info) {
                $user_rank = $user_rank_obj->getUserRankInfo(
                    'user_rank_id = ' . intval($user_rank_info['user_rank_id']), 
                    'discount'
                );
                $item_list[$k]['vip_price'] = round($user_rank['discount'] * $v['mall_price'] / 100, 2);
            } else {
                $item_list[$k]['vip_price'] = $v['mall_price'];
            }
            //积分可抵扣金额
            $item_list[$k]['integral_num'] = round($v['mall_price'] * $v['integral_exchange_rate'] / 100, 2);
            //获取首批商品的数量
            #$item_list[$k]['first_number'] = M('FirstBuyItem')->field('number')->where('item_id = ' . $v['item_id'])->find();
            #$item_list = array_merge($item_list, $item_list['number']);
            //计算单个商品总价
            #$item_list[$k]['total_price'] = $item_list[$k]['vip_price'] * $item_list[$k]['first_number']['number'];

		}

		return $item_list;
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
        return $this->where('isuse = 1 AND sort_id = ' . $sort_id)->field($field)->limit()->select();
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
     * 获取某商品等级价，用户等级折扣，商品混批优惠价
     * @author 姜伟
     * @param int $item_id 商品ID
     * @param int $user_rank_id 用户等级ID
     * @return array 
	 * @todo 获取某商品等级价，用户等级折扣，商品混批优惠价
     */
    public function getItemPriceInfo($item_id, $user_rank_id = 0)
    {
		$rank_price = 0.00;
		$discount_rate = 100;

		if ($user_rank_id)
		{
			//取等级价
			$item_rank_price_obj = new ItemPriceRankModel();
			$rank_price = $item_rank_price_obj->getItemRankPrice($user_rank_id, $item_id);

			if (!$rank_price)
			{
				$user_rank_obj = new UserRankModel($user_rank_id);
				$discount_rate = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_rank_id);
				$discount_rate = $discount_rate ? $discount_rate['discount_rate'] : 100;
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
     * @param int $user_rank_id 用户等级ID
     * @return float 实际支付价格
	 * @todo 姜伟改于2014-04-24. 计算商品实际支付价格：
	 * $pay_price = 0，$discount_rate = 100;
	 * 0、未登录，跳4；
	 * 1、若$sku_id不为0，按$sku_id和用户等级ID $user_rank_id查找等级价格表中该sku该等级的价格$sku_rank_price，若有记录，$pay_price = $sku_rank_price，跳7；若无记录，跳2；
	 * 2、按$item_id和user_rank_id查找等级价格表中该商品的价格$item_rank_price，若有记录，$pay_price = $item_rank_price，跳7；否则跳3；
	 * 3、取用户等级表中该用户等级折扣$rank_discount_rate，若存在，$discount_rate = $rank_discount_rate，跳4；
	 * 4、若$sku_id不为0，按$sku_id取sku价格表中的sku价格$sku_price，$pay_price = $sku_price，跳6；否则跳5；
	 * 5、按$item_id取商品批发价$mall_price，$pay_price = $mall_price，跳6；
	 * 6、$pay_price *= $pay_price，跳7；
	 * 7、按$item_id取混批规则表中该商品当前数量优惠价$discount_price，$pay_price -= $discount_price，返回$pay_price；
     */
    public function calculateItemRealPrice($item_id, $sku_id, $number, $user_rank_id = 0, $calculate_wholesale_rule = true, $total_num = 0)
    {
		// 用户等级
		if (!$user_rank_id)
		{
			$user_id = session('user_id');
			$Users   = M('Users');
			$user_rank_id = $Users->where('user_id = ' . $user_id)->getField('user_rank_id', true);
		}
		$pay_price = 0.00;
		$rank_price = 0.00;
		$discount_rate = 100;

		if ($user_rank_id)
		{
			//3
			$user_rank_obj = new UserRankModel($user_rank_id);
			$discount_rate = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_rank_id);
			$discount_rate = $discount_rate ? $discount_rate['discount_rate'] : 100;
			$discount_rate = ($discount_rate && $discount_rate > 0 && $discount_rate < 100) ? $discount_rate : 100;
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
				$item_info = $this->getItemInfo('item_id = ' . $item_id);
				$mall_price = $item_info ? $item_info['mall_price'] : 0.00;
				$pay_price = $mall_price ? $mall_price : $pay_price;
			}
		}

        if($calculate_wholesale_rule){
            //计算批发价
            $wholesale_obj = new ItemWholesalePriceModel();
            $price_list = $wholesale_obj->getItemPrice($item_id);
            $wholrsale_price = 0.00;
            if($price_list){
                foreach ($price_list as $k => $v) {
                   if($total_num >= $v['min_num']){
                        $wholrsale_price = $v['price'];
                   }
                }
                $pay_price -= $wholrsale_price;
            }
        }
        

		//6
		if (!$rank_price)
		{
			$pay_price = $pay_price * floatval($discount_rate / 100);
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
			$stock = intval($this->getStockById($item_id));
			$stock = $stock - $number;
		}

		return $stock < 0 ? false : true;
	}

    /**
     * 判断套餐商品库存是否足够
     * @author zlf
     * @param int $item_id 商品ID
     * @param int $number 商品数量
     * @return boolean 足够返回true，不足返回false
     * @todo 判断套餐商品库存是否足够
     */
    public function checkPackageStockEnough($item_id, $number = 1)
    {
        if (!is_numeric($item_id))  return false;
        $item_list = D('ItemPackageDetail')->field('item_id, number')->where('item_package_id = ' . $item_id)->select();
        foreach ($item_list as $key => $value) {
            $item_info = D('Item')->field('stock')->where('item_id = ' . $value['item_id'])->find();
            $item_list[$key] = array_merge($item_list[$key], $item_info);
            
        }
        foreach ($item_list as $key => $value) {
            if(($item_list[$key]['number'] * $number - $item_list[$key]['stock']) > 0){
                return false;
            }
        }
        return true;
        #echo "<pre>"; print_r($item_info);print_r($item_list);die;
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
	
	/**
	 * 根据分类获取商品列表
	 * @author 姜伟
	 * @param int $class_id
	 * @param boolean $limit_num 
	 * @return $item_list
	 * @todo 根据分类获取商品列表
	 */
	public function getItemListGroupBySort($class_id, $limit_num = 4, $is_integral = 0)
	{
		require_cache('Common/func_item.php');
		//获取二级分类列表
		$sort_obj = new SortModel();
		$sort_list = $sort_obj->getClassSortList($class_id);
		$fields = '*';
		$limit = '0,' . $limit_num;
		$order = 'addtime DESC';
		$item_list = array();
		$i = 0;
		foreach ($sort_list AS $k => $v)
		{
			$arr = $this->field($fields)->where('isuse = 1 AND stock > 0 AND sort_id = ' . $v['sort_id']. ' and is_integral = 0')->order($order)->limit($limit)->select();
			foreach ($arr AS $key => $val)
			{    
                //$path = strstr($val['base_pic'], '/Uploads');
                //$arr[$key]['small_img'] = small_img($val['base_pic']);
                //由于后台上传商品图时删除了小图，故前台出现很多没法显示图片的bug，即先按原图赋值给小图的方式来处理
				$arr[$key]['small_img'] = $val['base_pic'];
			}
			if ($arr)
			{
				$item_list[$i]['sort_id'] = $v['sort_id'];
				$item_list[$i]['sort_name'] = $v['sort_name'];
				$item_list[$i]['sort_tag'] = $v['sort_tag'];
				$item_list[$i]['item_list'] = $arr;
				$i ++;
			}
			else
			{
				#echo $this->getLastSql() . "<br>";
			}
		}

		$new_item_list = array(); 
		foreach ($item_list AS $k => $v)
		{
			if (count($v['item_list']))
			{
				$new_item_list[$v['sort_id']] = $v;
			}
		}

		return $new_item_list;
	}

        /**
     * 根据推荐的一级分类获取商品列表
     * @author 姜伟
     * @param int $class_id
     * @param boolean $limit_num 
     * @return $item_list
     * @todo 根据分类获取商品列表
     */
    public function getItemListGroupByClass($limit_num = 15, $is_integral = 0)
    {
        require_cache('Common/func_item.php');
        //获取二级分类列表
        #$sort_obj = new SortModel();
        #$sort_list = $sort_obj->getClassSortList($class_id);
        //获取首页推荐的一级分类列表
        $class_obj = new ClassModel();
        $where = 'is_index = 1';
        $class_list = $class_obj->getClassList($where);
        $fields = 'item_id,base_pic';
        $limit = '0,' . $limit_num;
        $order = 'serial ASC,addtime DESC';
        $item_list = array();
        $i = 0;
        foreach ($class_list AS $k => $v)
        {
            $arr = $this->field($fields)->where('isuse = 1 AND stock > 0 AND class_id = ' . $v['class_id']. ' and is_integral = 0')->order($order)->limit($limit)->select();
            /*foreach ($arr AS $key => $val)
            {    
                //$path = strstr($val['base_pic'], '/Uploads');
                //$arr[$key]['small_img'] = small_img($val['base_pic']);
                //由于后台上传商品图时删除了小图，故前台出现很多没法显示图片的bug，即先按原图赋值给小图的方式来处理
                $arr[$key]['small_img'] = $val['base_pic'];
            }*/
            if ($arr)
            {
                $item_list[$i]['class_id'] = $v['class_id'];
                $item_list[$i]['class_name'] = $v['class_name'];
                $item_list[$i]['class_tag'] = $v['class_tag'];
                $item_list[$i]['item_list'] = $arr;
                $i ++;
            }
            else
            {
                #echo $this->getLastSql() . "<br>";
            }
        }

        $new_item_list = array(); 
        foreach ($item_list AS $k => $v)
        {
            if (count($v['item_list']))
            {
                $new_item_list[$v['class_id']] = $v;
            }
        }

        return $new_item_list;
        #return $item_list;
    }

    // 获取ERP 商品信息
    // @author wsq
    protected function getERPItemInfo($GoodsCode="")
    {
        $mobile = D('Users')->where('user_id=%d', $user_id)->getField('mobile');
        $status = D('Connection')->getResult(
            array(
                'type' => ConnectionModel::OP_GET_ITEM_INFO,
                'msg' => json_encode(
                    array(
                        'GoodsCode'=> $GoodsCode,
                    )

                ),
            )
        );
        if ($status['Sucess']) {
            // 同步卡片信息
            //$data = $status['Data'][0];
            $data = ConnectionModel::decodeCompressData($status['CompressData']);
            trace($data, '-----data--------');
            return $data;
        }

        return NULL;
    }

    // 获取ERP 商品分类信息
    // @author wsq
    protected function getERPCATInfo($CategoryCode="", $CategoryLevel=0)
    {
        $status = D('Connection')->getResult(
            array(
                'type' => ConnectionModel::OP_GET_CAT_INFO,
                'msg' => json_encode(
                    array(
                        'CategoryCode' => $CategoryCode,
                        'CategoryLevel' => $CategoryLevel,
                    )

                ),
            )
        );
        if ($status['Sucess']) {
            // 同步卡片信息
            //$data = $status['Data'][0];
            $data = ConnectionModel::decodeCompressData($status['CompressData']);
            return $data;
        }

        return NULL;
    }

    // 分析分类和商品关系
    // @author wsq
    protected function analyseItemCat()
    {
        return true;
        //$cat_data = $this->getERPCATInfo('',1);
        $cat_data = $this->getERPCATInfo('052', 1);
        dump($cat_data);
    }

    // 商品信息处理
    // @author wsq
    protected function itemHandleProcess()
    {
        $data = $this->getERPItemInfo();
        if ($data) {
            // 先将所有商品设置为下线
            $this->where('')->setField('isuse', self::ITEM_OFFLINE);
            foreach ($data AS $k => $v) {
                // 已经有的商品设置为更新
                // 同时设置为 上架状态，
                // 这样可以保证同步上来的商品都是上架状态
                // 不在同步列表内的商品设置为下架状态
                $item_id = $this->where('goods_code ="%s"', $v['GoodsCode'])
                    ->getField('item_id');
                $this->editRecord($v, $item_id);
            }
            // 分析分类信息，
            return true;
        } else {
            return false;
        }
    }

    // 同步商品资料
    // @author wsq
    public function syncItemInfo()
    {
        // 商品资料处理
        $item_status =  $this->itemHandleProcess();
        // todo: 商品分类暂时没需求
        // 分类信息处理
        //$this->analyseItemCat();
        return $item_status;
    }
}

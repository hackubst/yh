<?php
/**
 * 物流公司Model
 *
 * @author zhengzhen
 * @date 2014/2/26
 *
 */
class ShippingCompanyModel extends Model
{
    /**
	 * 添加一个物流公司
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_shipping_company插入一条记录
	 *
	 */
    public function addShippingCompany(array $data)
    {
		$this->create($data);
		return $this->add();
    }
    
    /**
	 * 修改物流公司信息
	 *
	 * @param int $id 物流公司ID
	 * @param array $data 更新数据
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_shipping_company中shipping_company_id为$id的数据为$data
	 *
	 */
    public function editShippingCompany($id, array $data)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('shipping_company_id=' . $id)->setField($data);
    }
	
	/**
	 * 添加一个配送区域
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_shipping_company_region_price插入一条记录
	 *
	 */
	public function addShippingRegion(array $data)
	{
		$_this = M('shipping_company_region_price');
		$_this->create($data);
		return $_this->add();
	}
	
	/**
	 * 修改配送区域
	 *
	 * @param int $id 配送区域ID
	 * @param array $data 更新数据
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_shipping_company_region_price中shipping_company_region_price_id为$id的数据为$data
	 *
	 */
	public function editShippingRegion($id, array $data)
	{
		if($id < 0)
		{
			return false;
		}
		$_this = M('shipping_company_region_price');
		$_this->where('shipping_company_region_price_id=' . $id)->setField($data);
	}
	
	/**
	 * 获取配送区域信息
	 *
	 * @param int $id 配送区域ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回配送区域信息，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company_region_price中shipping_company_region_price_id为$id的$fields字段的值
	 *
	 */
	public function getShippingRegionById($id, $fields = '')
	{
		if($id < 0)
		{
			return false;
		}
		$_this = M('shipping_company_region_price');
		return $_this->where('shipping_company_region_price_id=' . $id)->find();
	}
	
	/**
	 * 获取配送区域列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @return mixed 成功返回配送区域列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company_region_price中$limit条数记录
	 *
	 */
	public function getShippingRegionList($limit = '')
	{
		$_this = M('shipping_company_region_price');
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		return $_this->select();
	}
	
	/**
	 * 获取指定物流公司的配送区域列表
	 *
	 * @param int $id 物流公司ID
	 * @return mixed 成功返回配送区域列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company_region_price中shipping_company_id为$id的配送区域信息
	 *
	 */
	public function getShippingRegionListById($id)
	{
		if($id < 0)
		{
			return false;
		}
		$_this = M('shipping_company_region_price');
		return $_this->where('shipping_company_id=' . $id)->select();
	}
	
	/**
	 * 删除一个配送区域
	 *
	 * @param int $id 配送区域ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_shipping_company_region_price中shipping_company_region_price_id为$id的记录
	 *
	 */
    public function deleteShippingRegionById($id)
    {
		if($id < 0)
		{
			return false;
		}
		$_this = M('shipping_company_region_price');
		return $_this->where('shipping_company_region_price_id=' . $id)->delete();
    }
	
	/**
	 * 删除一个配送区域
	 *
	 * @param int $id 物流公司ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_shipping_company_region_price中shipping_company_id为$id的记录
	 *
	 */
    public function deleteShippingRegionByCompanyId($id)
    {
		if($id < 0)
		{
			return false;
		}
		$_this = M('shipping_company_region_price');
		return $_this->where('shipping_company_id=' . $id)->delete();
    }
    
    /**
	 * 获取物流公司信息
	 *
	 * @param int $id 物流公司ID
	 * @param string $fields 获取字段列表，多个以半角逗号分隔，默认''，即所有字段
	 * @return mixed 成功返回物流公司信息，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company中shipping_company_id为$id的$fields字段的值
	 *
	 */
    public function getShippingCompanyInfoById($id, $fields = '')
    {
		if($id < 0)
		{
			return false;
		}
		$_this = $this;
		if($fields)
		{
			$_this = $_this->field($fields);
		}
		return $_this->where('shipping_company_id=' . $id)->find();
    }
    
    /**
	 * 获取开启的物流公司列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @return mixed 成功返回物流公司列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company中$limit条数记录，以serial排序
	 *
	 */
    public function getShippingCompanyList($limit = '')
    {
		$_this = $this;
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		return $_this->where('isuse=1')->order('serial')->select();
    }
	
	/**
	 * 获取物流公司列表
	 *
	 * @param string $limit 限定获取记录起始及条数，默认''
	 * @return mixed 成功返回物流公司列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company中$limit条数记录，以serial排序
	 *
	 */
    public function getAllShippingCompanyList($limit = '')
    {
		$_this = $this;
		if($limit)
		{
			$_this = $_this->limit($limit);
		}
		return $_this->order('serial')->select();
    }
    
    /**
	 * 删除一个物流公司，同时删除快递单打印表中的关联数据
	 *
	 * @param int $id 物流公司ID
	 * @return mixed 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_shipping_company中shipping_company_id为$id的记录，同时删除表tp_shipping_print_set中相应记录数
	 *
	 */
    public function deleteShippingCompanyById($id)
    {
		$result = $this->where('shipping_company_id=' . $id)->delete();
		if($result)
		{
			$_this = M('shipping_print_set');
			$_this->where('shipping_company_id=' . $id)->delete();
		}
		return $result;
    }
	
	/**
	 * 设置指定物流公司使用次数
	 *
	 * @param int $id 物流公司ID
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 设置表tp_shipping_company中shipping_company_id为$id的used_time递增1
	 *
	 */
    public function setShippingCompanyUsedTime($id)
    {
		if($id < 0)
		{
			return false;
		}
		return $this->where('shipping_company_id=' . $id)->setInc('used_time');
    }
    
    /**
	 * 计算物流费用，若包邮则返回0
	 *
	 * @param int $id 物流公司ID
	 * @param int $province_id 省份ID
	 * @param float $weight 商品重量
	 * @param float $total_price 订单总价
	 * @return int $shippingFee 成功返回物流费用，否则返回false
	 * @author zhengzhen
	 * @todo 根据订单的配送方式及省份ID，订单商品总重量$item_total_weight，查找tp_shipping_company_region表中的相应数据，首先得到该配送方式下到达该省份所需的首重$start_weight、首重费用$start_weight_price，若$item_total_weight < $start_weight，则物流费用为$start_weight_price；否则物流费用为$start_weight_price + ($item_total_weight - $start_weight) * 超出首重后的每千克物流费用
	 *
	 */
    public function calculateShippingFee($province_id, $weight, $total_price, $id = 1)
    {
		$config = new ConfigBaseModel();
		//是否满多少包邮
		$freeShippingStatus = $config->getConfig('free_shipping_status');
		$freeShippingTotal = $config->getConfig('free_shipping_total');
		if($freeShippingStatus && $total_price >= $freeShippingTotal)
		{
			return 0;
		}

		//是否统一运费
		$uniformShippingFee = $config->getConfig('uniform_shipping_fee');
		if($uniformShippingFee > 0)
		{
			return $uniformShippingFee;
		}

		$_this = M('shipping_company_region_price');
		$where = 'shipping_company_id=' . $id . ' AND FIND_IN_SET(' . $province_id . ', province_id)';
		$shippingRegionPrice = $_this->where($where)->find();
		if(!$shippingRegionPrice)
		{
			$fields = 'start_weight,start_weight_price,added_weight,added_weight_price';
			$shippingCompanyInfo = $this->getShippingCompanyInfoById($id, $fields);
			if(!$shippingCompanyInfo)
			{
				return false;
			}
			$shippingRegionPrice = $shippingCompanyInfo;
		}

		$weight /= 1000;
		$startWeight = $shippingRegionPrice['start_weight'];
		$startWeightPrice = $shippingRegionPrice['start_weight_price'];
		$addedWeight = $shippingRegionPrice['added_weight'];
		$addedWeightPrice = $shippingRegionPrice['added_weight_price'];
		if($weight <= $startWeight)
		{
			$weight = $startWeight;
		}
		$shippingFee = $startWeightPrice + ceil(($weight - $startWeight) / $addedWeight) * $addedWeightPrice;
		return $shippingFee;
    }

    /**
     * 获取快递公司列表
     * @author tale
     * @return array
     */
    public static function getCompanyList()
    {
        return array(
            "tt"  => "天天",
            "ems" => "EMS",
            "sf"  => "顺丰",
            "yd"  => "韵达",
            "yt"  => "圆通",
            "zto" => "中通",
            "sto" => "申通",
            "ht"  => "汇通",
            "qf"  => "全峰",
            "db"  => "德邦",
        );
    }
}
?>

<?php
/**
 * 商品SKU模型类
 */

class ItemSkuModel extends Model
{
    public $itemSkuId;
    
    public $item_id;

    /**
     * 构造函数
     * @author 张勇
     * @todo 构造函数
     */
    public function __construct()
    {
        parent::__construct('item_sku_price');
    }

    /**
     * 获取商品单个sku信息
     * @author 张勇
     * @param int $sku_id 规格属性ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array SKU信息
     * @todo 获取商品单个sku信息
     */
    public function getSkuInfo($sku_id, $fields = null)
    {
        if (!is_numeric($sku_id))   return false;
        return $this->field($fields)->where('item_sku_price_id = ' . $sku_id)->find();
    }

    /**
     * 获取商品的sku信息
     * @author 张勇
     * @param int $item_id 商品ID
     * @param string $fields 查询的字段名，默认为空，取全部
     * @return array SKU信息
     * @todo 获取商品的sku信息
     */
    public function itemSkuInfo($item_id, $fields = null)
    {
        if (!is_numeric($item_id))   return false;
        return $this->field($fields)->where('item_id = ' . $item_id)->order('serial, item_sku_price_id')->select();
    }

    /**
     * 统计商品目前的sku规格总数
     * @author 张勇
     * @param int $item_id 商品ID
     * @param string $group 统计的字段
     * @return array SKU信息
     * @todo 获取商品的sku信息
     */
    public function itemSkuGroup($item_id, $group = 'property_value1')
    {
        if (!is_numeric($item_id))   return false;
         $this->field('item_sku_price_id')->where('item_id = '. $item_id)->group($group)->select();
         echo $this->_sql();
    }

    /**
     * 获取商品sku详细信息(包括规格属性的名称)
     * @author 张勇
     * @param string $item_id 商品ID
     * @return array $arr_sku sku信息
     * @todo  获取商品sku详细信息,相关表：item_sku_price、property、property_value
     */
    public function itemSkus($item_id)
    {
        if (!is_numeric($item_id))   return false;
        //获取用户等级
        $user_id = intval(session('user_id'));
        $user_rank_info = D('User')->getUserInfo('user_rank_id', 'user_id = ' . $user_id);
        $user_rank_obj = new UserRankModel;
        $user_rank = $user_rank_obj->getUserRankInfo('user_rank_id = ' . $user_rank_info['user_rank_id'], 'discount');
        #echo "<pre>";print_r($user_rank);die;
        
        #$item_list[$k]['vip_price'] = round($user_rank['discount'] * $v['mall_price'] / 100, 2);

        $arr_sku = $arr_property_value = array();

        $Item_sku_price = M('Item_sku_price');
        $res = $Item_sku_price->where('item_id = ' . $item_id)->order('serial')->select();

        $Property       = M('Property');
        $Property_value = M('Property_value');

        foreach ($res as $k => $v) {
            $arr_sku['sku_info'][$k] = $v;

            // 判断是否有第一种规格属性的名称
            if (!$arr_property_value[1][$v['property_value1']]) {
                $res_prop1 = $Property_value->where('property_value_id = ' . $v['property_value1'] . ' AND isuse = 1')->find();
                $arr_property_value[1][$v['property_value1']] = $res_prop1['property_value'];

                // 第一种规格名称
                if (!$arr_sku['sku_name1']) {
                    $arr_sku['sku_name1'] = $Property->where('property_id = ' . $res_prop1['property_id'] . ' AND isuse = 1')->getField('property_name');
                }
            }

            // 判断是否有第二种规格属性的名称
            if (!$arr_property_value[2][$v['property_value2']]) {
                $res_prop2 = $Property_value->where('property_value_id = ' . $v['property_value2'] . ' AND isuse = 1')->find();
                $arr_property_value[2][$v['property_value2']] = $res_prop2['property_value'];

                // 第二种规格名称
                if (!$arr_sku['sku_name2']) {
                    $arr_sku['sku_name2'] = $Property->where('property_id = ' . $res_prop2['property_id'] . ' AND isuse = 1')->getField('property_name');
                }
            }
        }

        foreach ($arr_sku['sku_info'] as $k => $v) {
            // 单条sku信息第一种规格属性的名称
            if (isset($arr_property_value[1][$v['property_value1']])) {
                $arr_sku['sku_info'][$k]['property_name1'] = $arr_property_value[1][$v['property_value1']];
                #$arr_sku['sku_info'][$k]['sku_price'] = floatval($user_rank['discount'] * $arr_property_value[1][$v['sku_price']] / 100);
                
            } else {
                $arr_sku['sku_info'][$k]['property_name1'] = '';
            }

            // 单条sku信息第二种规格属性的名称
            if (isset($arr_property_value[2][$v['property_value2']])) {
                $arr_sku['sku_info'][$k]['property_name2'] = $arr_property_value[2][$v['property_value2']];
                #$arr_sku['sku_info'][$k]['sku_price'] = floatval($user_rank['discount'] * $arr_property_value[2][$v['sku_price']] / 100);

            } else {
                $arr_sku['sku_info'][$k]['property_name2'] = '';
            }


        }
        $property1 = $property2 = $name1 = $name2 = array();
        foreach ($arr_sku['sku_info'] as $k => $v) {
            if($v['property_name1'] && !in_array($v['property_name1'], $name1)){
                $name1[] = $v['property_name1'];
                $property1[] = array('property_name'=>$v['property_name1'],'property_value'=>$v['property_value1']);
            }
            if($v['property_name2'] && !in_array($v['property_name2'], $name2)){
                $name2[] = $v['property_name2'];
                $property2[] = array('property_name'=>$v['property_name2'],'property_value'=>$v['property_value2']);
            }
        }
        $arr_sku['property_list']['property1'] = $property1;
        $arr_sku['property_list']['property2'] = $property2;
        #echo "<pre>";print_r($arr_sku);die;
        return $arr_sku;
    }

    /**
     * 获取单个SKU详细信息（包括规格属性的名称）
     * @author 张勇
     * @param int $sku_id 规格id
     * @return array
     * @todo 获取单个SKU详细信息（包括规格属性的名称）
     */
    public function getSku($sku_id)
    {
        if (!is_numeric($sku_id))   return false;

        $Property       = M('Property');
        $sku_info = $Property->where('property_id = ' . $sku_id . ' AND is_extended_property = 2')->find();

        if (!$sku_info) return false;

        $Property_value = M('Property_value');
        $arr_prop = $Property_value->where('property_id = ' . $sku_id . ' AND isuse = 1')->order('serial')->select();

        $sku_info['property_values'] = $arr_prop;
        return $sku_info;
    }

    
    /**
     * @todo 根据相应的自定义条件获取sku_price的详细信息 
     * @author zhoutao
     * @date 2014-04-14
     */
    public function getSkuPriceInfo($where = '')
    {
    	if(!$where || $where == '')
    	{
    		return false;
    	}
    	return $this->where($where)->select();
    }
    
    
    /**
     * @todo 获取sku信息用来输出在模板供JS使用
     * @param array $item_info 商品的基本信息。必须
     * @param array $arr_sku 该商品类型下的所有的sku信息数组， 等于get_type_sku($item_info['item_type_id'])。默认为空数组
     * @param array $item_sku_info 该商品的所有的sku信息 ，等于$this->getSkuPriceInfo('item_id = '.$item_id.' AND isuse = 1')。默认为空
     * @author zhoutao 
     * @date 2014-04-15
     */
    public function getSkuMap($item_info, $arr_sku=array(), $item_sku_info=array())
    {
    	if(!$item_info || empty($item_info))
    	{
    		return FALSE;
    	}
    	if(!$arr_sku || empty($arr_sku))		//如果没有传递该商品类型所拥有的所有的sku信息数组
    	{
    		$arr_sku = get_type_sku($item_info['item_type_id']);	//根据类型获取该商品类型下的规格属性
    	}
    	
    	if(!$item_sku_info || empty($item_sku_info))		//如果没有传递 商品本身的sku信息数组
    	{
    		$item_sku_info = $this->getSkuPriceInfo('item_id = '.$item_info['item_id'].' AND isuse = 1');
    	}
    	#echo '<pre>';print_r($arr_sku);exit;
		#myprint($item_sku_info);
    	$temp_arr = array();	
    	if($arr_sku)
    	{
    		foreach($arr_sku as $k0=>$v0)
    		{
    			$porperty = $v0['property_id'];		//规格ID
    			foreach($v0['prop_value'] as $k1=>$v1)
    			{
    				$property_value_id = $v1['property_value_id'];	//规格值ID
    				$temp_arr[$property_value_id] = $porperty;
    			}
    		}
    	}
//     	myprint($temp_arr);
    	$skumap_arr = array();		//按照页面JS使用数据的格式 组装
    	foreach($item_sku_info as $key=>$value)
    	{
    		if($value['sku_stock'] <= 0)
    		{
    			continue;
    		}
    		
    		$temp_k = '';			//数组$skumap_arr的键名
    		$arr	= array();		//数组$skumap_arr的键值
    		$property_value1 = $value['property_value1'];	//第一个规格属性的ID
    		$property_value2 = $value['property_value2'];	//第二个规格属性的ID
    		$temp_k .= ';'.$property_value1.':'.$temp_arr[$property_value1];
    		$temp_k .= ';'.$property_value2.':'.$temp_arr[$property_value2].';';
    		
//     		$temp_k .= ';'.$temp_arr[$property_value1].':'.$property_value1;
//     		$temp_k .= ';'.$temp_arr[$property_value2].':'.$property_value2.';';
    		
    		$arr['sku_id'] 	   	 = $value['item_sku_price_id']; 
     		$arr['wholsesale'] 	 = $item_info['wholesale_price'];
    		$arr['sku_price']  	 = $value['sku_price'];
    		$arr['stock']		 = $value['sku_stock'];
    		$skumap_arr[$temp_k] = $arr;
    	}
    	return json_encode($skumap_arr);
    }
    
    /**
     * 删除某件商品的SKU信息
     * @author 张勇
     * @param string $item_id 商品id
     * @return boolean 操作结果
     * @todo 删除某件商品的SKU信息
     */
    public function delItemSku($item_id)
    {
        if (!is_numeric($item_id)) return false;
        return $this->where('item_id = ' . $item_id)->delete();
    }

    /**
     * 删除单条SKU信息
     * @author 张勇
     * @param int $sku_id
     * @return boolean 操作结果
     * @todo 删除单条SKU信息
     */
    public function delSku($sku_id)
    {
        if (!is_numeric($sku_id)) return false;
        return $this->where('item_sku_price_id = ' . $sku_id)->delete();
    }

    /**
     * 批量设置商品SKU信息
     * @author 张勇
     * @param string $item_id 商品id
     * @param array $arr_sku sku信息数组
     * @return boolean 操作结果
     * @todo 批量设置商品SKU信息
     */
    public function batchSetSku($item_id, $arr_sku)
    {
        if (!is_numeric($item_id) || !is_array($arr_sku)) return false;

        foreach ($arr_sku as $sku) {
            if (isset($sku['sku_id']) && $sku['sku_id']) {
                $this->setSku($sku['sku_id'], $arr_sku);
            } else {
                $this->addSku($item_id, $arr_sku);
            }
        }

        return true;
    }

    /**
     * 添加单个商品SKU信息
     * @author 张勇
     * @param string $item_id
     * @param array $arr_sku sku信息数组
     * @return boolean 操作结果
     * @todo 添加单个商品SKU信息
     */
    public function addSku($item_id, $arr_sku)
    {
        if (!is_numeric($item_id) || !is_array($arr_sku)) return false;

        $data = $arr_sku;
        $data['item_id'] = $item_id;
        $r = $this->add($data);
        return $r;
    }

    
    /**
     * 更改单个商品SKU信息
     * @author 张勇
     * @param int $sku_id
     * @param array $arr_sku sku信息数组
     * @return boolean 操作结果
     * @todo 更改单个商品SKU信息
     */
    public function setSku($sku_id, $arr_sku)
    {
        if (!is_numeric($sku_id) || !is_array($arr_sku)) return false;

        $data = $arr_sku;
        return $this->where('item_sku_price_id = ' . $sku_id)->save($data);
    }

    /**
     * 根据其中一个规格值id获取某商品支持另一个规格值id列表
     * @author 姜伟
     * @param int $item_id
     * @param string $property_name
     * @param int $property_value
     * @return array 规格列表
     * @todo 
     */
    public function getItemSkuByProperyValue($item_id, $property_name, $property_value)
    {
		$fields = 'item_sku_price_id, sku_stock, sku_price, ';
		if ($property_name == 'property_value1')
		{
			$fields .= 'property_value2 AS property_value';
		}
		else
		{
			$fields .= 'property_value1 AS property_value';
		}

		$item_sku_list = $this->field($fields)->where('item_id = ' . $item_id . ' AND ' . $property_name . ' = ' . $property_value)->select();
		return $item_sku_list;
    }

    /**
     * 根据商品ID和两种规格值获取当前商品SKU信息，主要是库存
     * @author 姜伟
     * @param int $item_id
     * @param string $property_value1
     * @param int $property_value2
     * @return array 规格列表
     * @todo 
     */
    public function getItemSkuInfoByPropertyValues($item_id, $property_value1, $property_value2)
    {
		$fields = 'sku_stock';
		$where = 'item_id = ' . $item_id . ' AND property_value1 = ' . $property_value1;
		if ($property_value2)
		{
			$where .= ' AND property_value2 = ' . $property_value2;
		}
		$item_sku_info = $this->field($fields)->where($where)->find();
		return $item_sku_info;
    }

	/**
     * 减库存
     * @author 姜伟
     * @param int $item_sku_price_id 商品sku id
     * @param int $number 商品数量
     * @return 返回成功修改记录的条数
     * @todo 减SKU表中商品的库存
     */
	public function deductItemStock($item_sku_price_id, $number = 1)
	{
		$item_sku_price_id = intval($item_sku_price_id);
		if ($item_sku_price_id)
		{
			return $this->where('item_sku_price_id = ' . $item_sku_price_id)->setDec('sku_stock', $number);
		}

		return 0;
	}

	/**
     * 加库存
     * @author 姜伟
     * @param int $item_sku_price_id 商品sku id
     * @param int $number 商品数量
     * @return 返回成功修改记录的条数
     * @todo 减SKU表中商品的库存
     */
	public function addItemStock($item_sku_price_id, $number = 1)
	{
		$item_sku_price_id = intval($item_sku_price_id);
		if ($item_sku_price_id)
		{
			return $this->where('item_sku_price_id = ' . $item_sku_price_id)->setInc('sku_stock', $number);
		}

		return 0;
	}
	
	/**
	 * @todo 删除指定商品指定sku的级别价格信息
	 * @param int $item_id 商品ID
	 * @param int $sku_id 商品的sku ID
	 * @return bool
	 */
	public function delItemSkuRankPrice($item_id,$sku_id)
	{
		$item_price_rank = M('item_price_rank');
		return $item_price_rank->where('item_id = '.$item_id.' AND item_sku_price_id = '.$ksu_id)->delete();
	}
	
	/**
	 * @todo 设置商品sku的会员级别价格
	 * @author zhoutao@360shop.cc zhoutao0928@sina.com
	 * @param 要设置的信息数组  $data
	 * @return bool 成功返回TRUE 否则返回FALSE
	 */
	public function setItemPriceRank($data)
	{
		
	}
	

    /**
     * 根据商品类型已商品id获取当前商品的所有sku信息
     * @author 姜伟
     * @param 
     * @param item_id                           商品id
     * @param item_type_id                      商品类型id
     * @return property1_name                   规格名称1
     * @return property2_name                   规格名称2
     * @return sku_info['item_sku_price_id']    skuid
     * @return sku_info['item_id']              商品id
     * @return sku_info['item_sn']              sku商品货号
     * @return sku_info['sku_price']            sku价格
     * @return sku_info['sku_stock']            sku库存 
     * @return sku_info['property_value1_name'] sku规格1  (颜色)
     * @return sku_info['property_value2_name'] sku规格2  (尺码)
     * @return sku_info['sku_rank_price']       sku会员等级折扣价格
     */
    public function getItemSkuByTypeIdAndItemId($item_id, $item_type_id, $user_id, $agent_rank_id)
    {
    
        if (!$item_id && !$item_type_id) 
        {
            return false;
        }

        //本类型下的sku信息(颜色尺码)
        $arr_sku  = get_type_sku($item_type_id);    
        if(!$arr_sku)
        {
            return false;
        }
        
        //本商品勾选的sku信息(颜色尺码)
        $Model    = new Model(); 
        $sql      = "SELECT GROUP_CONCAT(item_sku_price_id,':',sku_price,':',sku_stock,':',property_value1,':',property_value2) AS sku_row 
                     FROM tp_item_sku_price WHERE item_id = ". $item_id ." GROUP BY property_value1 ORDER BY COUNT('property_value1') desc";
        $sku_info = $Model->query($sql);
        if(!$sku_info)
        {
            return false;
        }

      /*  $user_id        = $_SESSION['user_info']['user_id'];
        $agent_rank_id  = $_SESSION['user_info']['agent_rank_id'];
*/
        $AgentRankModel = new AgentRankModel();
        $property_value = M('property_value');
        $temp_arr       = array();
        foreach ($sku_info as $key => $value) 
        {
            //504:59.00:2400:404:408,505:59.00:2400:404:409,506:59.00:2400:404:410
            $sku_row   = explode(',', $value['sku_row']);

            $temp_arr2 = array();
            foreach ($sku_row as $key2 => $value2) 
            {
                list($item_sku_price_id,$sku_price,$sku_stock,$property_value1,$property_value2) = explode(':', $value2);
                
                //颜色
                foreach ($arr_sku[0]['prop_value'] as $key3 => $value3) 
                {
                    if($property_value1 == $value3['property_value_id'])
                    {
                        $property_value1 = $value3['property_value'];
                    }
                }
                //尺码
                foreach ($arr_sku[1]['prop_value'] as $key4 => $value4) 
                {
                    if($property_value2 == $value4['property_value_id'])
                    {
                        $property_value2 = $value4['property_value'];
                    }
                }

                $temp_arr2[$key2]['item_sku_price_id'] = $item_sku_price_id;
                $temp_arr2[$key2]['sku_price']         = $sku_price;
                $temp_arr2[$key2]['sku_stock']         = $sku_stock;
                $temp_arr2[$key2]['property_value1']   = $property_value1;
                $temp_arr2[$key2]['property_value2']   = $property_value2;

                $temp_arr2[$key2]['sku_rank_price'] = '';
                if($user_id)
                {
                    //获取当前sku的会员价格
                    $temp_arr2[$key2]['sku_rank_price'] = $AgentRankModel->getAgentRankByTypeId($agent_rank_id, ' AND item_sku_price_id = '. $item_sku_price_id);
                    //print_r($temp_arr2[$key2]['sku_rank_price']) ;
                }
            }

            $sku_info[$key] = $temp_arr2;
        }
       //print_r($temp_arr);
       //die;
      
        $arr['property1_name'] = $arr_sku[0]['property_name'];
        $arr['property2_name'] = $arr_sku[1]['property_name'];
        $arr['sku_info']       = $sku_info; 
        return $arr; 
    }
}

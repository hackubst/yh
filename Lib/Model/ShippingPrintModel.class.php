<?php
/**
 * 快递单打印Model
 *
 * @author zhengzhen
 * @date 2014/2/27
 *
 */
class ShippingPrintModel extends Model
{
	/**
	 * 订单Model对象
	 * @access protected
	 */
	protected $_orderModel = null;
	
	/**
	 * 订单列表
	 * @access protected
	 */
	protected $_orderList = array();
	
	protected $tableName = 'shipping_print_set';
	
	
	/**
	 * 构造器
	 *
	 * @param string $orderType 订单类型，即agent---实际订单,virtual---虚拟库存订单
	 * @access public
	 * @return mixed
	 * @author zhengzhen
	 *
	 */
	public function __construct($orderType)
	{
		parent::__construct();
		if($orderType == 'real')
		{
			$this->_orderModel = new OrderModel(0);
		}
		elseif($orderType == 'virtual')
		{
			$this->_orderModel = new VirtualStockModel();
		}
	}
    
	/**
	 * 添加快递单打印配置
	 *
	 * @param array $data 新增数据，数组的键名为数据表字段名
	 * @return mixed 成功返回当前插入记录主键值，否则返回false
	 * @author zhengzhen
	 * @todo 向表tp_shipping_print_set插入一条记录
	 *
	 */
    public function addShippingPrint(array $data)
    {
		$this->create($data);
		return $this->add();
    }
	
    /**
	 * 修改快递单打印配置
	 *
	 * @param int $id 物流公司ID
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool 成功返回true，否则返回false
	 * @author zhengzhen
	 * @todo 更新表tp_shipping_print_set中shipping_company_id为$id的数据为$data
	 *
	 */
    public function editShippingPrint($id, array $data)
    {
		if($id <= 0)
		{
			return false;
		}
		return $this->where('shipping_company_id=' . $id)->setField($data);
    }
	
	/**
	 * 保存快递单打印配置
	 *
	 * @param array $data 更新数据，数组的键名为数据表字段名
	 * @return bool 成功返回受影响记录数，否则返回false
	 * @author zhengzhen
	 * @todo 新增或更新表tp_shipping_print_set中数据
	 *
	 */
	public function saveShippingPrint(array $data)
	{
		if($data['background_img'])
		{
			$imgDomain = '';
			if(false !== strpos($data['background_img'], C('IMG_DOMAIN')))
			{
				$imgDomain = C('IMG_DOMAIN');
			}
			$data['background_img'] = str_replace($imgDomain . '/Uploads', '##img_domain##', $data['background_img']);
		}
		$this->create($data);
		return $this->add('', '', true);
	}
	
	/**
	 * 删除快递单打印配置
	 *
	 * @param int $id 物流公司ID
	 * @return bool 成功返回删除记录数，否则返回false
	 * @author zhengzhen
	 * @todo 删除表tp_shipping_print_set中shipping_company_id为$id的数据，同时删除快递单底图
	 *
	 */
	public function deleteShippingPrint($id)
	{
		if($id <= 0)
		{
			return false;
		}
		$backgroundImg = $this->where('shipping_company_id=' . $id)->getField('background_img');
		$result = $this->where('shipping_company_id=' . $id)->delete();
		if($result)
		{
			$backgroundImg = str_replace('##img_domain##', APP_PATH . 'Uploads', $backgroundImg);
			@unlink($backgroundImg);
		}
		return $result;
	}
    
    /**
	 * 通过物流公司ID获取快递单打印配置信息
	 *
	 * @param int $id 物流公司ID
	 * @return mixed 成功返回快递单打印配置信息，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_print_set中shipping_company_id为$id的记录
	 *
	 */
    public function getShippingPrintInfoById($id)
    {
		if($id < 0)
		{
			return false;
		}
		$result = $this->where('shipping_company_id=' . $id)->find();
		if($result)
		{
			if(false !== strpos($result['background_img'], '##img_domain##'))
			{
				$result['background_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $result['background_img']);
			}
		}
		return $result;
    }
	
	/**
	 * 快递单打印配置列表
	 *
	 * @return mixed 成功返回快递单打印配置列表，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_print_set中记录
	 *
	 */
	public function getShippingPrintList()
	{
		return $this->select();
	}
	
	/**
	 * 快递单打印项列表
	 *
	 * @return mixed 成功返回快递单打印项列表，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_print_item中记录
	 *
	 */
	public function getShippingPrintItem()
	{
		$shippingPrintItem = M('shipping_print_item');
		return $shippingPrintItem->field('shipping_print_item_en,shipping_print_item_value')->select();
	}
	
	/**
	 * 批量快递单打印
	 *
	 * @param int $start 起始订单ID，默认第一条记录
	 * @param int $rows 每次获取订单数，默认15
	 * @param string $where 订单查询条件，默认''
	 * @return mixed 成功返回快递单打印数据数组，否则返回错误信息，
	 * 'ERROR_NOT_SAME_EXPRESS_COMPANY'表示选中订单不是同一家快递公司的，
	 * 'ERROR_NO_TEMPLATE'表示未添加指定快递公司的快递单打印模板
	 * @author zhengzhen
	 * @todo 验证通过条件$where查询获得订单是否为同一家快递公司，如果是则调用方法_buildPrintData()获取快递单打印数据
	 *
	 */
    public function batchShippingPrint($start = 1, $rows = 15, $where = '')
	{
		//订单列表
		$result = $this->_getOrderList($start, $rows, $where);
		if(!$result)
		{
			return false;
		}
		$this->_orderList = $result;
		//验证是否同一家快递公司
		$companyCur = 0;
		foreach($this->_orderList as $key => $val)
		{
			if($companyCur === 0)
			{
				$companyCur = $val['express_company_id'];
			}
			elseif($companyCur != $val['express_company_id'])
			{
				return 'ERROR_NOT_SAME_EXPRESS_COMPANY';
			}
		}
		
		$data = $this->_buildPrintData($companyCur);
	//	echo "<pre>";
	//	print_r($data);die;
		return $data;
	}
    
    /**
	 * 生成快递单打印数据
	 *
	 * @param int $id 物流公司ID
	 * @return mixed 成功返回快递单打印数据，否则返回false
	 * @author zhengzhen
	 * @todo 返回数据格式如：
	 * array(
	 * 		'page' => array(
	 * 			0 => array(
	 * 				'item' => array(
	 * 						'W' => ,//打印项宽度
	 *						'H' => ,//打印项高度
	 *						'X' => ,//打印项水平偏移
	 *						'Y' => ,//打印项垂直偏移
	 *						'S' => ,//打印项字体大小，需转换单位像素px为磅pt
	 *						'B' => ,//打印项字体粗体
	 *						'I' => ,//打印项字体斜体
	 *						'L' => ,//打印项字符间距
	 * 						'VALUE' => //打印项值
	 * 				)
	 * 			),
	 * 			...
	 * 		),
	 * 		'other' => array(
	 * 			'MB_NAME' => ,
	 * 			'MB_BG' => ,
	 * 			'MB_BG_WIDTH' => ,
	 * 			'MB_BG_HEIGHT' => ,
	 * 			'MB_WIDTH' => ,
	 * 			'MB_HEIGHT' => 
	 * 		)
	 * )
	 *
	 */
    protected function _buildPrintData($id)
    {
		//打印配置
		$shippingPrintSet = $this->getShippingPrintInfoById($id);
		if(!$shippingPrintSet)
		{
			if($shippingPrintSet !== false)
			{
				return 'ERROR_NO_TEMPLATE';
			}
			return false;
		}
		//打印项默认值
		$printItemDefaultValueList = $this->_getPrintItemDefaultValueList();
		//打印项值
		$shippingPrintSetDetail = unserialize($shippingPrintSet['set_detail']);
		//快递单图片路径
		$shippingBG = str_replace('##img_domain##', APP_PATH . 'Uploads', $shippingPrintSet['background_img']);
		//快递单图片尺寸
		list($shippingPageSize['width'], $shippingPageSize['height']) = getimagesize($shippingBG);
		
		$i = 1;
		$data = $printItems = array();
		foreach($this->_orderList as $key => $val)
		{
			$printItems['ordersn']	 = $val['order_sn'];
			$printItems['realname2'] = $val['consignee'];
			$printItems['province2'] = $val['province_name'];
			$printItems['city2'] 	 = $val['city_name'];
			$printItems['district2'] = $val['area_name'];
			$printItems['address2']	 = $val['address'];
			$printItems['zipcode2']	 = $val['post_code'];
			$printItems['mobile2']	 = $val['mobile'];
			$printItems['year']		 = date('Y', $val['addtime']);
			$printItems['month']	 = date('m', $val['addtime']);
			$printItems['day'] 		 = date('d', $val['addtime']);
			$printItems['sitename']	 = $GLOBALS['config_info']['SHOP_NAME'];
			$printItems['address']	 = $GLOBALS['config_info']['CUSTOMER_SERVICE_ADDRESS'];
			$printItems['tel']		 = $GLOBALS['config_info']['CUSTOMER_SERVICE_TELEPHONE'];
			
			if(is_array($printItemDefaultValueList))
			{
				$printItems = array_merge($printItems, $printItemDefaultValueList);
			}
			
			$offset1 = ($i - 1) * 1 / 25.4 * 96;//打印机偏移量，单位px
			$j = 1;
			foreach ($printItems as $k => $v)
			{
				if(!isset($shippingPrintSetDetail[$k . '_w']))
				{
					continue;
				}
			//	$offset2 = $shippingPrintSetDetail[$k . '_h'] / 3;//调整文字垂直方向居中
				$data['page'][$i]['item'][$j] = array(
					'W' 	=> $shippingPrintSetDetail[$k . '_w'],
					'H' 	=> $shippingPrintSetDetail[$k . '_h'],
					'X'		=> $shippingPrintSetDetail[$k . '_x'],
					'Y'		=> round($shippingPrintSetDetail[$k . '_y'] + $offset1/** + $offset2*/),
					'S' 	=> $shippingPrintSetDetail[$k . '_s'] * 72 / 96,
					'B' 	=> $shippingPrintSetDetail[$k . '_b'],
					'I' 	=> $shippingPrintSetDetail[$k . '_i'],
					'L' 	=> $shippingPrintSetDetail[$k . '_l'],
					'VALUE'	=> ($shippingPrintSetDetail[$k . '_v']) ? $shippingPrintSetDetail[$k . '_v'] : $v
				);
				$j++;
			}
			$i++;
		}
		$shippingCompany = new ShippingCompanyModel();
		$shippingCompanyInfo = $shippingCompany->getShippingCompanyInfoById($id, 'shipping_company_name');
		$data['other'] = array(
			'MB_NAME' 	   => $shippingCompanyInfo['shipping_company_name'],
			'MB_BG' 	   => $shippingBG,
			'MB_BG_WIDTH'  => $shippingPageSize['width'],
			'MB_BG_HEIGHT' => $shippingPageSize['height'],
			'MB_WIDTH' 	   => $shippingPrintSet['printing_paper_width'],
			'MB_HEIGHT'    => $shippingPrintSet['printing_paper_height']
		);
		return $data;
    }
	
	/**
	 * 获得指定物流公司的打印项配置详情
	 *
	 * @param int $id 物流公司ID
	 * @return mixed 成功返回打印项配置详情，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_company_set中set_detail字段，并对其执行unserialize()操作
	 *
	 */
	protected function _getPrintItemConfig($id)
	{
		$setDetail = $this->where('shipping_company_id=' . $id)->getField('set_detail');
		if(!$setDetail)
		{
			return false;
		}
		return unserialize($setDetail);
	}
	
	/**
	 * 获取快递单打印项列表
	 *
	 * @return mixed 成功返回打印项组成的数组，否则返回false
	 * @author zhengzhen
	 * @todo 获取表tp_shipping_print_item中shipping_print_item_en,shipping_print_item_default_value字段值
	 *
	 */
	protected function _getPrintItemDefaultValueList()
	{
		$shippingPrintItem = M('shipping_print_item');
		$printItemList = $shippingPrintItem->field('shipping_print_item_en,shipping_print_item_default_value')->select();
		if(!$printItemList)
		{
			return false;
		}
		$data = array();
		foreach($printItemList as $key => $val)
		{
			if($val['shipping_print_item_default_value'])
			{
				$data[$val['shipping_print_item_en']] = $val['shipping_print_item_default_value'];
			}
		}
		return $data;
	}
	
	/**
	 * 获取订单列表
	 *
	 * @param int $start 起始订单记录，默认第一条
	 * @param int $rows 每次获取订单数，默认15
	 * @param string $where 查询条件
	 * @return mixed 成功返回订单列表数组，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，查询tp_order中指定字段，从$start开始，获取$rows条记录
	 *
	 */
	protected function _getOrderList($start = null, $rows = null, $where)
	{
		if(isset($start))
		{
			$this->_orderModel->setStart($start);
		}
		if(isset($rows))
		{
			$this->_orderModel->setLimit($rows);
		}
		$this->_orderModel->setListFields('order_sn,consignee,addtime,province_id,city_id,area_id,address,post_code,mobile,express_company_id');
		$this->_orderModel->setWhere($where);
		
		$result = $this->_orderModel->getAllOrderList();
		if($result && is_array($result))
		{
			foreach($result as $key => $val)
			{
				$data = array(
					'province_id' => $val['province_id'],
					'city_id' => $val['city_id'],
					'area_id' => $val['area_id']
				);
				$data = self::convertAddress($data);
				$result[$key] = array_merge($result[$key], $data);
			}
		}
		return $result;
	}
	
	/**
	 * 获取订单总数
	 *
	 * @param int $start 起始订单记录，默认第一条
	 * @param int $rows 获取订单数，默认1
	 * @return int 成功返回$rows，否则返回0
	 * @author zhengzhen
	 * @todo 查询tp_order中所有字段，从$start开始，获取$rows条记录
	 *
	 */
	public function testOrderNum($start, $rows = 1)
	{
		$this->_orderModel->setStart($start);
		$this->_orderModel->setLimit($rows);
		$this->_orderModel->setListFields('order_id');
		if($this->_orderModel->getAllOrderList())
		{
			return true;
		}
		return false;
	}
	
	/**
	 * 检测订单数量
	 *
	 * @param string $where 查询条件
	 * @return mixed 成功返回订单总数，否则返回false
	 * @author zhengzhen
	 * @todo 通过条件$where，以及isuse=1，查询表tp_order中记录总数
	 *
	 */
	public function getOrderTotal($where)
	{
		$condition = 'isuse=1';
		if($where)
		{
			$condition .= ' AND ' . $where;
		}
		
		return $this->_orderModel->where($where)->count();
	}
	
	/**
	 * 获取用户信息
	 *
	 * @return mixed 成功返回用户信息，否则返回false
	 * @author zhengzhen
	 * @todo 调用UserModel中的getUserInfo()方法获取user_id为$id的用户信息
	 *
	 */
	protected function _getUserInfo($id)
	{
		$user = new UserModel($id);
		$result = $user->getUserInfo('province_id,city_id,area_id,address,tel,mobile');
		if($result)
		{
			$data = array(
				'province_id' => $result['province_id'],
				'city_id' => $result['city_id'],
				'area_id' => $result['area_id']
			);
			$data = self::convertAddress($data);
			$result = array_merge($result, $data);
		}
		return $result;
	}
	
	public static function convertAddress(array $data)
	{
		$province = M('address_province');
		$city = M('address_city');
		$area = M('address_area');
		$data['province_name'] = $province->where('province_id=' . $data['province_id'])->getField('province_name');
		$data['city_name'] = $city->where('city_id=' . $data['city_id'])->getField('city_name');
		$data['area_name'] = $area->where('area_id=' . $data['area_id'])->getField('area_name');
		return $data;
	}
	
	/**
	 * 组装打印项数组
	 *
	 * @param array $data 打印项数据
	 * array(
	 * 		0 => 'json0'
	 * 		1 => 'json1'
	 * 		...
	 * )
	 * @return string serialize的打印项信息
	 * @author zhengzhen
	 * @todo 将入参URL解码，JSON解码，重新组装数组，并按数组的键排序后serialize
	 *
	 */
	public static function buildPrintItem(array $data)
	{
		$printItem = array();
		foreach($data as $key => $val)
		{
			$json = json_decode(urldecode($val));
			$printItem[$json->id . '_x'] = $json->left;
			$printItem[$json->id . '_y'] = $json->top;
			$printItem[$json->id . '_w'] = $json->width;
			$printItem[$json->id . '_h'] = $json->height;
			$printItem[$json->id . '_s'] = str_replace('px', '', $json->fontSize);
			$printItem[$json->id . '_b'] = ($json->italic == 'italic') ? 1 : 0;
			$printItem[$json->id . '_i'] = ($json->bold == 'bold') ? 1: 0;
			$printItem[$json->id . '_l'] = $json->letterSpacing;
			$printItem[$json->id . '_v'] = $json->value;
		}
		ksort($printItem);
		return serialize($printItem);
	}
	
	/**
	 * 反转buildPrintItem组装的字符串
	 *
	 * @param string $data 打印项数据
	 * @return array 
	 * @author zhengzhen
	 * @todo 将入参unserialize，将数组按键排序，重组
	 *
	 */
	public static function reversePrintItem($data)
	{
		$printItem = array();
		$data = unserialize($data);
		ksort($data);
		$data = array_chunk($data, 9, true);
		foreach($data as $key => $val)
		{
			$printItemCell = array();
			foreach($val as $k => $v)
			{
				$tmp = explode('_', $k);
				$printItemCell['id'] = $tmp[0];
				switch($tmp[1])
				{
					case 'x':
						$printItemCell['left'] = $v;
						break;
					case 'y':
						$printItemCell['top'] = $v;
						break;
					case 'w':
						$printItemCell['width'] = $v;
						break;
					case 'h':
						$printItemCell['height'] = $v;
						break;
					case 's':
						$printItemCell['fontSize'] = $v . 'px';
						break;
					case 'b':
						$printItemCell['italic'] = ($v) ? 'italic' : 'normal';
						break;
					case 'i':
						$printItemCell['bold'] = ($v) ? 'bold' : 400;
						break;
					case 'l':
						$printItemCell['letterSpacing'] = $v;
						break;
					case 'v':
						$printItemCell['value'] = $v;
						break;
				}
			}
			$printItem[$tmp[0]] = urlencode(json_encode($printItemCell));
		}
		return $printItem;
	}
}
?>

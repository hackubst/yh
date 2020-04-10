<?php
/**
 * 配送类
 * 
 *
 */
class AcpShippingAction extends AcpAction {
	
	
	 /**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo
     */
	public function AcpShippingAction()
	{
		parent::_initialize();
	}
	
	/**
     * 配送方式列表
     * @author 陆宇峰
     * @return void
     * @todo 显示所有快递公司，从shipping_company表取出来
     */
	public function list_company()
	{
		$shippingCompany = new ShippingCompanyModel();
		$shippingCompanyList = $shippingCompany->getAllShippingCompanyList();
	//	echo "<pre>";
	//	print_r($shippingCompanyList);die;
		
		if($shippingCompanyList && is_array($shippingCompanyList))
		{
			foreach($shippingCompanyList as $key => $val)
			{
				$shippingRegionList = $shippingCompany->getShippingRegionListById($val['shipping_company_id']);
				$shippingCompanyList[$key]['shipping_region_list'] = $shippingRegionList;
			}
			$this->assign('shipping_company_list', $shippingCompanyList);
		}
		
		$config = new ConfigBaseModel();
		$shippingFee = $config->getConfig('uniform_shipping_fee');
		$this->assign('shipping_fee', $shippingFee);

		//默认物流
		$this->assign('default_express_company', $GLOBALS['config_info']['DEFAULT_EXPRESS_COMPANY']);
		
		$this->assign('head_title', '配送方式');
		$this->display();
	}
	
	/**
     * 添加配送方式
     * @author 陆宇峰
     * @return void
     * @todo 插入一条数据到shipping_company表，如顺丰快递
     */
	public function add_company()
	{
		$act = $this->_post('act');
		if($act == 'submit')
		{
			$_post = $this->_post();
			$shippingCompanyName = $_post['shipping_company_name'];
			$shippingCompanySite = $_post['shipping_company_site'];
			$startWeight 		 = $_post['start_weight'];
			$addedWeight 		 = $_post['added_weight'];
			$startWeightPrice 	 = $_post['start_weight_price'];
			$addedWeightPrice 	 = $_post['added_weight_price'];
			$shippingDesc 		 = $_post['shipping_desc'];
			$serial 			 = $_post['serial'];
			$isUse 				 = $_post['isuse'];
			
			//表单验证
			if(!$shippingCompanyName)
			{
				$this->error('请输入公司名称！');
			}
			elseif(mb_strlen($shippingCompanyName) > 32)
			{
				$this->error('请输入32个字符以内的公司名称！');
			}
			if($shippingCompanySite)
			{
				if(!preg_match('/^http/i', $shippingCompanySite))
				{
					$this->error('请输入带协议的完整URL地址！');
				}
				elseif(!preg_match(C('URL_PREG'), $shippingCompanySite))
				{
					$this->error('请输入有效的URL地址！');
				}
			}
			if(!$startWeight)
			{
				$this->error('请输入首重重量！');
			}
			elseif(!is_numeric($startWeight))
			{
				$this->error('请输入纯数值的首重重量！');
			}
			if(!$addedWeight)
			{
				$this->error('请输入续重重量！');
			}
			elseif(!is_numeric($addedWeight))
			{
				$this->error('请输入纯数值的续重重量！');
			}
			if(!$startWeightPrice)
			{
				$this->error('请输入首重费用！');
			}
			if(!is_numeric($startWeightPrice))
			{
				$this->error('请输入纯数值的首重费用！');
			}
			if(!$addedWeightPrice)
			{
				$this->error('请输入续重费用！');
			}
			if(!is_numeric($addedWeightPrice))
			{
				$this->error('请输入纯数值的续重费用！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请输入数字排序号！');
			}
			if(intval($isUse) !== 0 && intval($isUse) !== 1)
			{
				$this->error('非法参数！');
			}
			
			$data = array(
				'shipping_company_name' => $shippingCompanyName,
				'shipping_company_site' => $shippingCompanySite,
				'start_weight' 			=> $startWeight,
				'start_weight_price' 	=> $startWeightPrice,
				'added_weight' 			=> $addedWeight,
				'added_weight_price' 	=> $addedWeightPrice,
				'shipping_desc' 		=> $shippingDesc,
				'serial' 				=> $serial,
				'isuse' 				=> $isUse
			);
			
			$shippingCompany = new ShippingCompanyModel();
			if($shippingCompany->addShippingCompany($data))
			{
				$this->success('恭喜您，配送方式添加成功！', U('/AcpShipping/list_company'));
			}
			else
			{
				$this->error('对不起，配送方式添加失败，请稍后重试！');
			}
		}
		
		$this->assign('action_title', '配送方式列表');
		$this->assign('action_src', U('/AcpShipping/list_company'));
		$this->assign('head_title', '添加配送方式');
		$this->display();
	}
	
	/**
     * 修改配送方式
     * @author 陆宇峰
     * @return void
     * @todo 修改shipping_company表数据
     */
	public function edit_company()
	{
		$id = $this->_get('id');
		$act = $this->_post('act');
		$shippingCompany = new ShippingCompanyModel();
		
		if($act == 'submit')
		{
			$_post = $this->_post();
			$shippingCompanyName = $_post['shipping_company_name'];
			$shippingCompanySite = $_post['shipping_company_site'];
			$startWeight 		 = $_post['start_weight'];
			$addedWeight 		 = $_post['added_weight'];
			$startWeightPrice 	 = $_post['start_weight_price'];
			$addedWeightPrice 	 = $_post['added_weight_price'];
			$shippingDesc 		 = $_post['shipping_desc'];
			$serial 			 = $_post['serial'];
			$isUse 				 = $_post['isuse'];
			
			//表单验证
			if(!$shippingCompanyName)
			{
				$this->error('请输入公司名称！');
			}
			elseif(mb_strlen($shippingCompanyName) > 32)
			{
				$this->error('请输入32个字符以内的公司名称！');
			}
			if($shippingCompanySite)
			{
				if(!preg_match('/^http/i', $shippingCompanySite))
				{
					$this->error('请输入带协议的完整URL地址！');
				}
				elseif(!preg_match(C('URL_PREG'), $shippingCompanySite))
				{
					$this->error('请输入有效的URL地址！');
				}
			}
			if(!$startWeight)
			{
				$this->error('请输入首重重量！');
			}
			elseif(!is_numeric($startWeight))
			{
				$this->error('请输入纯数值的首重重量！');
			}
			if(!$addedWeight)
			{
				$this->error('请输入续重重量！');
			}
			elseif(!is_numeric($addedWeight))
			{
				$this->error('请输入纯数值的续重重量！');
			}
			if(!$startWeightPrice)
			{
				$this->error('请输入首重费用！');
			}
			if(!is_numeric($startWeightPrice))
			{
				$this->error('请输入纯数值的首重费用！');
			}
			if(!$addedWeightPrice)
			{
				$this->error('请输入续重费用！');
			}
			if(!is_numeric($addedWeightPrice))
			{
				$this->error('请输入纯数值的续重费用！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请输入数字排序号！');
			}
			if(intval($isUse) !== 0 && intval($isUse) !== 1)
			{
				$this->error('非法参数！');
			}
			
			$data = array(
				'shipping_company_name' => $shippingCompanyName,
				'shipping_company_site' => $shippingCompanySite,
				'start_weight' 			=> $startWeight,
				'start_weight_price' 	=> $startWeightPrice,
				'added_weight' 			=> $addedWeight,
				'added_weight_price' 	=> $addedWeightPrice,
				'shipping_desc' 		=> $shippingDesc,
				'serial' 				=> $serial,
				'isuse' 				=> $isUse
			);
			
			if(false !== $shippingCompany->editShippingCompany($id, $data))
			{
				$this->success('恭喜您，配送方式修改成功！', U('/AcpShipping/list_company'));
			}
			else
			{
				$this->error('对不起，配送方式修改失败，请稍后重试！');
			}
		}
		
		if(!$id || !ctype_digit($id))
		{
			$this->error('非法参数！', U('/AcpShipping/list_company'));
		}
		$shippingCompanyInfo = $shippingCompany->getShippingCompanyInfoById($id);
		if(!$shippingCompanyInfo)
		{
			$this->error('无效ID！', U('/AcpShipping/list_company'));
		}
		
		$this->assign('shipping_company_info', $shippingCompanyInfo);
		
		$this->assign('action_title', '配送方式列表');
		$this->assign('action_src', U('/AcpShipping/list_company'));
		$this->assign('head_title', '修改配送方式');
		$this->display();
	}

	/**
     * 添加配送区域
     * @author zhengzhen
     * @return void
     * @todo 插入一条数据到tp_shipping_company_region_price表
     */
	public function add_region()
	{
		$cId = $this->_get('cid');
		$act = $this->_post('act');
		$shippingCompany = new ShippingCompanyModel();
		
		if($act == 'submit')
		{
			$_post = $this->_post();
			$startWeight 	  = $_post['start_weight'];
			$addedWeight 	  = $_post['added_weight'];
			$startWeightPrice = $_post['start_weight_price'];
			$addedWeightPrice = $_post['added_weight_price'];
			$provinceId 	  = $_post['province_id'];
			$provinceText 	  = $_post['province_text'];
			
			$data = array(
				'shipping_company_id' => $cId,
				'province_id' 		  => $provinceId,
				'province_text' 	  => $provinceText,
				'start_weight' 		  => $startWeight,
				'start_weight_price'  => $startWeightPrice,
				'added_weight' 		  => $addedWeight,
				'added_weight_price'  => $addedWeightPrice
			);
			
			if($shippingCompany->addShippingRegion($data))
			{
				$this->success('恭喜您，配送区域添加成功！', U('/AcpShipping/list_company'));
			}
			else
			{
				$this->error('对不起，配送区域添加失败！');
			}
		}
		
		if(!$cId || !ctype_digit($cId))
		{
			$this->error('非法参数！', U('/AcpShipping/list_company'));
		}
		$fields = 'shipping_company_name,start_weight,start_weight_price,added_weight,added_weight_price';
		$shippingCompanyInfo = $shippingCompany->getShippingCompanyInfoById($cId, $fields);
		if(!$shippingCompanyInfo)
		{
			$this->error('无效ID！', U('/AcpShipping/list_company'));
		}
		
		$addressProvince = M('address_province');
		$addressProvinceList = $addressProvince->select();
		
		$this->assign('shipping_company_info', $shippingCompanyInfo);
		$this->assign('address_province_list', $addressProvinceList);
		
		$this->assign('action_title', '配送方式列表');
		$this->assign('action_src', U('/AcpShipping/list_company'));
		$this->assign('head_title', '添加配送区域');
		$this->display();
	}
	
	/**
     * 修改配送方式
     * @author zhengzhen
     * @return void
     * @todo 更新表tp_shipping_company_region_price中数据
     */
	public function edit_region()
	{
		$cId = $this->_get('cid');
		$rId = $this->_get('rid');
		$act = $this->_post('act');
		$shippingCompany = new ShippingCompanyModel();
		
		if($act == 'submit')
		{
			$_post = $this->_post();
			$startWeight 	  = $_post['start_weight'];
			$addedWeight 	  = $_post['added_weight'];
			$startWeightPrice = $_post['start_weight_price'];
			$addedWeightPrice = $_post['added_weight_price'];
			$provinceId 	  = $_post['province_id'];
			$provinceText 	  = $_post['province_text'];
			
			$data = array(
				'shipping_company_id' => $cId,
				'province_id' 		  => $provinceId,
				'province_text' 	  => $provinceText,
				'start_weight' 		  => $startWeight,
				'start_weight_price'  => $startWeightPrice,
				'added_weight' 		  => $addedWeight,
				'added_weight_price'  => $addedWeightPrice
			);
			
			if(false !== $shippingCompany->editShippingRegion($rId, $data))
			{
				$this->success('恭喜您，配送区域修改成功！', U('/AcpShipping/list_company'));
			}
			else
			{
				$this->error('对不起，配送区域修改失败！');
			}
		}
		
		if(!$cId || !ctype_digit($cId))
		{
			$this->error('非法参数！', U('/AcpShipping/list_company'));
		}
		$fields = 'shipping_company_name,start_weight,start_weight_price,added_weight,added_weight_price';
		$shippingCompanyInfo = $shippingCompany->getShippingCompanyInfoById($cId, $fields);
		if(!$shippingCompanyInfo)
		{
			$this->error('无效ID！', U('/AcpShipping/list_company'));
		}
		
		if(!$rId || !ctype_digit($rId))
		{
			$this->error('非法参数！', U('/AcpShipping/list_company'));
		}
		$shippingRegionInfo = $shippingCompany->getShippingRegionById($rId);
		if(!$shippingRegionInfo)
		{
			$this->error('无效ID！', U('/AcpShipping/list_company'));
		}
		
		$addressProvince = M('address_province');
		$addressProvinceList = $addressProvince->select();
		
		$this->assign('shipping_company_info', $shippingCompanyInfo);
		$this->assign('shipping_region_info', $shippingRegionInfo);
		$this->assign('address_province_list', $addressProvinceList);
		
		$this->assign('action_title', '配送方式列表');
		$this->assign('action_src', U('/AcpShipping/list_company'));
		$this->assign('head_title', '修改配送区域');
		$this->display();
	}
	
	/**
     * 删除配送方式
     * @author 陆宇峰
     * @return void
     * @todo 删除shipping_company表数据，同时删除tp_shipping_company_region_price对应数据
     */
	public function del_company()
	{
		
	}
	
	/**
     * 快递单打印模板列表
	 *
     * @author zhengzhen
     * @return void
     * @todo 从tp_shipping_print_item表取出数据
     */
	public function list_express_template()
	{
		$shippingPrint = new ShippingPrintModel('real');//实际订单
		$shippingPrintList = $shippingPrint->getShippingPrintList();
		if($shippingPrintList && is_array($shippingPrintList))
		{
			$shippingCompany = new ShippingCompanyModel();
			foreach($shippingPrintList as $key => $val)
			{
				$shippingCompanyInfo = $shippingCompany->getShippingCompanyInfoById($val['shipping_company_id'], 'shipping_company_name');
				$shippingPrintList[$key]['shipping_company_name'] = $shippingCompanyInfo['shipping_company_name'];
			}
		}
		
		$this->assign('shipping_print_list', $shippingPrintList);
		
		$this->assign('head_title', '快递单模板');
		$this->display();
	}
	
	/**
     * 添加快递单打印模板
	 *
     * @author zhengzhen
     * @return void
     * @todo 向tp_shipping_print_item表插入数据
     */
	public function add_express_template()
	{
		$act = $this->_post('act');
		$shippingPrint = new ShippingPrintModel('');
		
		if($act == 'submit')
		{
			$shippingCompanyId = $this->_post('shipping_company_id');
			$printTempName = $this->_post('print_temp_name');
			$printingPaperWidth = $this->_post('printing_paper_width');
			$printingPaperHeight = $this->_post('printing_paper_height');
			$printItemsParams = $this->_post('print_items_params');
			$imgUrl = $this->_post('img_url');
			
			$data = array(
				'shipping_company_id' => $shippingCompanyId,
				'print_temp_name' => $printTempName,
				'background_img' => $imgUrl,
				'printing_paper_width' => $printingPaperWidth,
				'printing_paper_height' => $printingPaperHeight,
				'set_detail' => ShippingPrintModel::buildPrintItem($printItemsParams)
			);
			if(false !== $shippingPrint->saveShippingPrint($data))
			{
				$this->success('恭喜您，快递单打印模板添加成功！', U('/AcpShipping/list_express_template'));
			}
			else
			{
				$this->error('对不起，快递单打印模板添加失败，请稍后重试！');
			}
		}
		$shippingCompany = new ShippingCompanyModel();
		$shippingCompanyList = $shippingCompany->getShippingCompanyList();
		if($shippingCompanyList && is_array($shippingCompanyList))
		{
			$shippingCompanyOptions = array();
			foreach($shippingCompanyList as $key => $val)
			{
				$shippingCompanyOptions[$val['shipping_company_id']] = $val['shipping_company_name'];
			}
		}
		$printItemList = $shippingPrint->getShippingPrintItem();
		
		$this->assign('shipping_company_options', $shippingCompanyOptions);
		$this->assign('print_item_list', $printItemList);
		
		$this->assign('action_title', '快递单打印模板列表');
		$this->assign('action_src', U('/AcpShipping/list_express_template'));
		$this->assign('head_title', '添加快递单打印模板');
		$this->display();
	}
	
	/**
     * 修改快递单打印模板
	 *
     * @author zhengzhen
     * @return void
     * @todo 修改tp_shipping_print_item表中指定物流公司数据
     */
	public function edit_express_template()
	{
		$act = $this->_post('act');
		$id = $this->_get('id');
		$shippingPrint = new ShippingPrintModel('');
		
		if($act == 'submit')
		{
			$shippingCompanyId = $this->_post('shipping_company_id');
			$printTempName = $this->_post('print_temp_name');
			$printingPaperWidth = $this->_post('printing_paper_width');
			$printingPaperHeight = $this->_post('printing_paper_height');
			$printItemsParams = $this->_post('print_items_params');
			$imgUrl = $this->_post('img_url');
			
			$data = array(
				'shipping_company_id' => $shippingCompanyId,
				'print_temp_name' => $printTempName,
				'background_img' => $imgUrl,
				'printing_paper_width' => $printingPaperWidth,
				'printing_paper_height' => $printingPaperHeight,
				'set_detail' => ShippingPrintModel::buildPrintItem($printItemsParams)
			);
			if(false !== $shippingPrint->saveShippingPrint($data))
			{
				$this->success('恭喜您，快递单打印模板修改成功！', U('/AcpShipping/list_express_template'));
			}
			else
			{
				$this->error('对不起，快递单打印模板修改失败，请稍后重试！');
			}
		}
		$shippingCompany = new ShippingCompanyModel();
		$shippingCompanyList = $shippingCompany->getShippingCompanyList();
		if($shippingCompanyList && is_array($shippingCompanyList))
		{
			$shippingCompanyOptions = array();
			foreach($shippingCompanyList as $key => $val)
			{
				$shippingCompanyOptions[$val['shipping_company_id']] = $val['shipping_company_name'];
			}
		}
		
		$printItemList = $shippingPrint->getShippingPrintItem();
		$shippingPrintInfo = $shippingPrint->getShippingPrintInfoById($id);
		if(!$shippingPrintInfo)
		{
			$this->error('无效物流公司ID！');
		}
		$shippingPrintInfo['set_detail'] = ShippingPrintModel::reversePrintItem($shippingPrintInfo['set_detail']);
		
		$this->assign('shipping_company_options', $shippingCompanyOptions);
		$this->assign('print_item_list', $printItemList);
		$this->assign('shipping_print_info', $shippingPrintInfo);
		
		$this->assign('action_title', '快递单打印模板列表');
		$this->assign('action_src', U('/AcpShipping/list_express_template'));
		$this->assign('head_title', '修改快递单打印模板');
		$this->display();
	}
}
?>

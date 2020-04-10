<?php
/**
 * 配送类
 * 
 *
 */
class AcpShippingAjaxAction extends AcpAction {
	
	/**
     * 删除配送方式
     * @author zhengzhen
     * @return void
     * @todo 删除tp_shipping_company表数据，同时删除tp_shipping_company_region_price对应数据
     */
	public function del_company()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$shippingCompany = new ShippingCompanyModel();
			if($shippingCompany->deleteShippingCompanyById($id))
			{
				$shippingCompany->deleteShippingRegionByCompanyId($id);
				$this->_ajaxFeedback(1, null, '恭喜您，配送方式删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，配送方式删除失败，请稍后重试！');
			}
		}
	}
	
	/**
     * 删除配送区域
     * @author zhengzhen
     * @return void
     * @todo 删除tp_shipping_company_region_price表数据，同时删除tp_shipping_company_region_price对应数据
     */
	public function del_region()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$shippingCompany = new ShippingCompanyModel();
			if($shippingCompany->deleteShippingRegionById($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，配送区域删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，配送区域删除失败，请稍后重试！');
			}
		}
	}
	
	/**
     * 开启并设置统一邮费
     * @author zhengzhen
     * @return void
     * @todo 设置表tp_config中config_name为uniform_shipping_fee的值
     */
	public function set_shipping_fee()
	{
		if($this->isAjax())
		{
			$isSet = $this->_get('is_set');
			$shippingFee = $this->_get('shipping_fee');
			if(!$isSet)
			{
				$shippingFee = 0;
			}
			else
			{
				if(!$shippingFee)
				{
					$this->_ajaxFeedback(0, null, '请输入配送费！');
				}
				if(!is_numeric($shippingFee))
				{
					$this->_ajaxFeedback(0, null, '请输入纯数值的配送费！');
				}
			}
			$config = new ConfigBaseModel();
			if(false !== $config->setConfig('uniform_shipping_fee', $shippingFee))
			{
				if($isSet)
				{
					$msg = '恭喜您，统一配送费设置成功！';
				}
				else
				{
					$msg = '恭喜您，统一费用禁用成功！';
				}
				$this->_ajaxFeedback(1, null, $msg);
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，统一配送费设置失败，请稍后重试！');
			}
		}
	}
		
	/**
     * 选择默认物流
     * @author 姜伟
     * @return void
     * @todo 选择默认物流
     */
	public function set_default_express_company()
	{
		$shipping_company_id = $this->_post('shipping_company_id');

		if (ctype_digit($shipping_company_id))
		{
			$config_obj = new ConfigBaseModel();
			$success = $config_obj->setConfig('default_express_company', $shipping_company_id);
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}
	
	/**
	 * 快递单底图上传处理
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 首先判断$_FILES['imgFile']['tmp_name']图片大小是否超出预设值，然后判断图片格式是否支持
	 * @todo 创建相应路径并保存图片，转换图片路径为图片URL通过JSON返回
	 *
	 */
	public function uploadHandler()
	{
		$legacyImg = $this->_post('legacy_img');
		$tmpFile = $_FILES['imgFile']['tmp_name'];
		$tmpFileSize = $_FILES['imgFile']['size'];
		$maxSize = 2 * pow(1024, 2);//2MB
		if($tmpFileSize > $maxSize)
		{
			$this->_ajaxFeedback(0, null, "图片过大，请上传2MB以内大小图片！");
		}
		
		switch($_FILES['imgFile']['type'])
		{
			case 'image/gif':
				$imgExt = '.gif';
				break;
			case 'image/jpeg':
			case 'image/pjpeg'://IE
				$imgExt = '.jpg';
				break;
			case 'image/png':
			case 'image/x-png'://IE
				$imgExt = '.png';
				break;
			default:
				break;
		}
		if(!isset($imgExt))
		{
			$this->_ajaxFeedback(0, null, "暂不支持该图片格式！");
		}
		if($legacyImg)
		{
			//删除旧凭证图片
			$legacyImg = str_replace(C('IMG_DOMAIN') . '/Uploads', APP_PATH . 'Uploads', $legacyImg);
			@unlink($legacyImg);
		}
		
		$savePath = APP_PATH . 'Uploads/image/other/shipping/' . date('Y-m');
		$saveFile = $savePath . '/' . date("YmdHis") . '_' . rand(10000, 99999) . $imgExt;
		
		//确认保存路径，没有则创建
		if(!is_dir($savePath))
		{
			if(!@mkdir($savePath, 0700, true))
			{
				$this->_ajaxFeedback(0, null, "上传目录创建失败！");
			}
		}
		//移动文件
		if(move_uploaded_file($tmpFile, $saveFile) === false)
		{
			$this->_ajaxFeedback(0, null, "图片上传失败！");
		}
		
		$imgUrl = str_replace(APP_PATH . 'Uploads', C('IMG_DOMAIN') . '/Uploads', $saveFile);
		$this->_ajaxFeedback(1, array('img_url' => $imgUrl));
	}
	
	/**
     * 删除快递单打印模板
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除tp_shipping_print_set表数据
     */
	public function del_express_template()
	{
		if($this->isAjax())
		{
			$id = $this->_get('id');
			$shippingPrint = new ShippingPrintModel();
			if($shippingPrint->deleteShippingPrint($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，快递单打印模板删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，快递单打印模板删除失败，请稍后重试！');
			}
		}
	}
}
?>

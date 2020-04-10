<?php
/**
 * 系统设置类
 * 
 *
 */
class AcpConfigAjaxAction extends AcpAction
{

	public function _initialize()
	{
		parent::_initialize();
	}

	/**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo
     */
	public function AcpConfigAjaxAction()
	{

	}

	/**
     * 上传商品水印图片
     * @author 陆宇峰
     * @return void
     * @todo 上传水印图片
     */
	public function upload_water_img()
	{

	}

	/**
	 * 修改顶部菜单的排序
	 * @author 陆宇峰
	 * @return void
	 * @todo 设置tp_menu表的series字段
	 *
	 */
	public function edit_menu_series()
	{
		$id = $this->_get('id');
		$serial = $this->_get('serial');
		if($this->isAjax() && $id)
		{
			if(!ctype_digit($serial))
			{
				$this->_ajaxFeedback(0, null, '对不起，排序号必须为数字！');
			}
			//$generalArticle = new GeneralArticleModel();
			$config_model = new ConfigBaseModel();
			if($config_model->setMenuSerial($id, $serial))
			{
				$data = array('serial' => $serial);
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功!');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
			}
		}
	}


	/**
     * 删除顶部菜单
	 *
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 删除表tp_menu中id为$id的记录，
     */
	public function del_menu()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证该分类ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '对不起，发生参数错误，请稍后再试！');
			}

			$config_model = new ConfigBaseModel();
			if($config_model->delMenu($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，头部菜单删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，头部菜单删除失败！');
			}
		}
	}

	/**
     * 设置菜单状态为关闭
	 *
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 设置tp_menu中id为$id的记录的isuse为0
     */
	public function edit_menu_isuse()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$config_model = new ConfigBaseModel();
			$isUse = $config_model->getMenuIsuse($id);
			$isUse = abs($isUse - 1);
			
			if($config_model->setMenuIsuse($id, $isUse))
			{
				$data = array('isuse'=> $isUse);
				if($isUse === 0)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，分类禁用成功！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，分类启用成功！');
				}
			}
			else
			{
				if($isUse === 0)
				{
					$this->_ajaxFeedback(0, null, '对不起，分类禁用失败，请稍后再试！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(0, null, '对不起，分类启用失败，请稍后再试！');
				}
			}
		}
	}
	
	
	/**
     * 删除菜单的图片
	 *
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 先取出tp_menu的记录，再设置tp_menu中id为$id的记录path_img为空，同时删除物理图片
     */
	public function del_menu_img()
	{
		$id = $this->_get('id');
		if(!$this->isAjax() || !$id)
		{
			$this->_ajaxFeedback(0, null, '对不起，参数传入失败，请稍后再试！');
		}
		
		$config_model = new ConfigBaseModel();
		$menu_ary = $config_model->getMenu($id);
	
		//删除物理图片
		//@ulink($menu_ary['path_img']);
		
		//删除数据
		if ($config_model->editMenu($id, array('path_img' => '')))
		{
			$this->_ajaxFeedback(1, null, '恭喜您，图片删除成功！');
		}
		else
		{
			$this->_ajaxFeedback(0, null, '对不起，图片删除失败！');
		}
	}
	
	/**
	 * 删除客服
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 删除表tp_customer_server_online中customer_service_online_id为$id的客服记录
	 *
	 */
	public function del_customer_service_online()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$customerServiceOnline = new CustomerServiceOnlineModel();
			if($customerServiceOnline->deleteCustomerServiceOnlice($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，客服删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，客服删除失败！');
			}
		}
	}
	
	/**
	 * 设置在线客服启用状态
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 更新表tp_config中config_name为online_display的config_value值为$onlineDisplay
	 *
	 */
	public function set_customer_service_online()
	{
		$onlineDisplay = $this->_get('online_display');
		if($this->isAjax() && $onlineDisplay)
		{
			$config = new ConfigBaseModel();
			if(false !== $config->setConfig('online_display', $onlineDisplay))
			{
				if($onlineDisplay == 'block')
				{
					$this->_ajaxFeedback(1, null, '恭喜您，在线客服启用成功！');
				}
				elseif($onlineDisplay == 'none')
				{
					$this->_ajaxFeedback(1, null, '恭喜您，在线客服禁用成功！');
				}
			}
			else
			{
				if($onlineDisplay == 'block')
				{
					$this->_ajaxFeedback(0, null, '对不起，在线客服启用失败，请稍后重试！');
				}
				elseif($onlineDisplay == 'none')
				{
					$this->_ajaxFeedback(0, null, '对不起，在线客服禁用失败，请稍后重试！');
				}
			}
		}
	}
	
	/**
     * 快速修改友链链接序号
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改文章表中serial的序号，改完后不刷新当前页
     */
	public function edit_link_serial()
	{
		$id = $this->_get('id');
		$serial = $this->_get('serial');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			if(!ctype_digit($serial))
			{
				$this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
			}
			
			$link = new LinkModel();
			if(false !== $link->setSerial($id, $serial))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
		}
	}
	
	/**
     * 快速修改友情链接启用状态
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改友情链接表中isuse的值，改完后不刷新当前页
     */
	public function edit_link_isuse()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$link = new LinkModel();
			$linkInfo = $link->getLink($id, 'isuse');
			$isUse = abs($linkInfo['isuse'] - 1);
			if(false !== $link->setIsuse($id, $isUse))
			{
				$data = array('isuse' => $isUse);
				if($isUse === 0)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，友情链接禁用成功！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，友情链接启用成功！');
				}
			}
			else
			{
				if($isUse === 0)
				{
					$this->_ajaxFeedback(0, null, '对不起，友情链接禁用失败，请稍后再试！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(0, null, '对不起，友情链接启用失败，请稍后再试！');
				}
			}
		}
	}
	
	/**
	 * 删除友情链接
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 *
	 *
	 */
	public function del_link()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$link = new LinkModel();
			if($link->deleteLink($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，友情链接删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，友情链接删除失败，请稍后重试！');
			}
		}
	}
	
	/**
	 * 友情链接logo上传处理
	 *
	 * @author zhengzhen
	 * @return void
	 * @todo 调用upImageHandler函数处理上传图片
	 *
	 */
	public function uploadHandler()
	{
		upImageHandler($_FILES['imgFile'], '/other/link');
	}
	
	/**
	 * 删除友情链接logo
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 分两种情况：一，删除未入库图片，此时传入参数为img_url，将此图片URL替换为图片路径直接删除即可；
	 * @todo 二，删除已入库图片，此时传入参数为id，查询表tp_link中link_id为$id的link_logo值，替换其图片域名占位符为图片路径，然后删除图片，同时将此link_logo值置空。
	 *
	 */
	public function delLinkLogo()
	{
		$id = $this->_post('id');
		$imgUrl = $this->_post('img_url');
		
		if($this->isAjax())
		{
			if($id)
			{
				$link = new LinkModel();
				$linkLogo = $link->getLink($id, 'link_logo');
				$linkLogoFile = str_replace('##img_domain##', APP_PATH . 'Uploads', $linkLogo['link_logo']);
				if(false !== $link->editLink($id, array('link_logo' => '')))
				{
					@unlink($linkLogoFile);
					$this->_ajaxFeedback(1);
				}
			}
			elseif($imgUrl)
			{
				$imgDomain = C('IMG_DOMAIN');
				$imgDomain = (false !== strpos($imgUrl, $imgDomain)) ? $imgDomain : '';
				$imgFile = str_replace($imgDomain . '/Uploads', APP_PATH . 'Uploads', $imgUrl);
				@unlink($imgFile);
				$this->_ajaxFeedback(1);
			}
			else
			{
				$this->_ajaxFeedback(0);
			}
		}
	}
}
?>
<?php
/**
 * 公告管理ajax类
 * 
 *
 */
class AcpNoticeAjaxAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}
	
	/**
     * 添加公告栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 向表tp_notice_sort中插入一条记录
     */
	public function add_sort()
	{
		if($this->isAjax())
		{
			$sortName = $this->_get('sort_name');
			$description = $this->_get('description');
			
			if(!$sortName)
			{
				$this->_ajaxFeedback(0, null, '请输入栏目名称！');
			}
			$data['notice_sort_name'] = $sortName;
			$data['description'] = $description;
			$data['isuse'] = 1;
			$noticeCategory = new NoticeCategoryModel();
			if($noticeCategory->addNoticeCategory($data))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，栏目添加成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，栏目添加失败，请稍后重试！');
		}
	}
	
	/**
     * 修改公告栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 更新表tp_notice_sort中notice_sort_id为$id的公告栏目信息
     */
	public function edit_sort()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			//验证该栏目ID是否10以内
			if($id <= 10)
			{
				$this->_ajaxFeedback(0, null, '对不起，该栏目不能修改！');
			}
			$sortName = $this->_get('sort_name');
			$description = $this->_get('description');
			
			if(!$sortName)
			{
				$this->_ajaxFeedback(0, null, '请输入栏目名称！');
			}
			$data['notice_sort_name'] = $sortName;
			$data['description'] = $description;
			$noticeCategory = new NoticeCategoryModel();
			if(false !== $noticeCategory->setNoticeCategory($id, $data))
			{
				$sortName = $noticeCategory->getNoticeCategory($id, 'notice_sort_name');
				$data['notice_sort_id'] = $id;
				$data['notice_sort_name'] = $sortName['notice_sort_name'];
				$this->_ajaxFeedback(1, $data, '恭喜您，栏目修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，栏目修改失败，请稍后重试！');
		}
	}
	
	/**
     * 删除公告栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除表tp_notice_sort中notice_sort_id为$id的公告栏目记录，
	 * 删除前确定是否有该栏目下的公告，提示无法删除，请先调整公告栏目。表内ID20以内的数据不能删除
     */
	public function del_sort()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			//验证该栏目ID是否10以内
			if($id <= 10)
			{
				$this->_ajaxFeedback(0, null, '对不起，该栏目不能删除！');
			}
			//验证该栏目下是否有公告
			$where = 'notice_sort_id=' . $id;
			$generalNotice = new GeneralNoticeModel();
			if($generalNotice->getGeneralNoticeList('', '', $where))
			{
				$this->_ajaxFeedback(0, null, '对不起，该栏目下有公告，无法删除，请先调整！');
			}
			
			$noticeCategory = new NoticeCategoryModel();
			if($noticeCategory->deleteNoticeCategory($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，栏目删除成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，栏目删除失败，请稍后重试！');
		}
	}
	
	/**
     * 获取指定ID公告栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 获取表tp_notice_sort中notice_sort_id为$id的公告栏目信息
     */
	public function fetch_sort()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$fields = 'notice_sort_id,notice_sort_name,description';
			$noticeCategory = new NoticeCategoryModel();
			$noticeCategoryData = $noticeCategory->getNoticeCategory($id, $fields);
			if($noticeCategoryData)
			{
				$this->_ajaxFeedback(1, $noticeCategoryData);
			}
			$this->_ajaxFeedback(0, null, '对不起，服务器忙，稍后请重试！');
		}
	}
	
	/**
     * 快速修改公告栏目序号
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改公告栏目表中serial的序号，改完后不刷新当前页
     */
	public function edit_cat_serial()
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
			
			$noticeCategory = new NoticeCategoryModel();
			if(false !== $noticeCategory->setNoticeCategory($id, array('serial' => $serial)))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
		}
	}
	
	/**
     * 快速修改公告栏目启用状态
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改公告栏目表中isuse的值，改完后不刷新当前页
     */
	public function edit_cat_isuse()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$noticeCategory = new NoticeCategoryModel();
			$isUse = $noticeCategory->getNoticeCategoryState($id);
			$isUse = abs($isUse - 1);
			if(false !== $noticeCategory->setNoticeCategory($id, array('isuse' => $isUse)))
			{
				$data = array('isuse' => $isUse);
				if($isUse === 0)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，栏目禁用成功！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，栏目启用成功！');
				}
			}
			else
			{
				if($isUse === 0)
				{
					$this->_ajaxFeedback(0, null, '对不起，栏目禁用失败，请稍后再试！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(0, null, '对不起，栏目启用失败，请稍后再试！');
				}
			}
		}
	}
	
	/**
     * 快速修改公告序号
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 修改公告表中serial的序号，改完后不刷新当前页
     */
	public function edit_serial()
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
			
			$generalNotice = new GeneralNoticeModel();
			if(false !== $generalNotice->setSerial($id, $serial))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
		}
	}
	
	/**
     * 删除公告
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除公告相关表中notice_id为$id的记录，改完后不刷新当前页
     */
	public function del_notice()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			$success = 0;//删除成功数
			$failure = 0;//删除失败数
			$generalNotice = new GeneralNoticeModel();
			if(is_array($id))
			{
				foreach($id as $key => $val)
				{
					//验证ID是否为数字
					if(!ctype_digit($val))
					{
						continue;
					}
					if($generalNotice->deleteNotice($val))
					{
						$data[] = array('id' => $val, 'error' => 0);
						$success++;
					}
					else
					{
						$data[] = array('id' => $val, 'error' => 1);
						$failure++;
					}
				}
			}
			else
			{
				//验证ID是否为数字
				if(!ctype_digit($id))
				{
					$this->_ajaxFeedback(0, null, '参数无效！');
				}
				if($generalNotice->deleteNotice($id))
				{
					$data[] = array('id' => $val, 'error' => 0);
					$success++;
				}
				else
				{
					$data[] = array('id' => $val, 'error' => 1);
					$failure++;
				}
			}
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '篇公告删除成功！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '篇公告删除失败，请稍后重试！';
			}
			$msg = implode("<br />", $msg);
			if($success > 0)
			{
				$this->_ajaxFeedback(1, $data, $msg);
			}
			else
			{
				$this->_ajaxFeedback(0, $data, $msg);
			}
		}
	}
	
	
	/**
     * 公告从AZ下载到本地
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 从AZ下载公告到本地公告表，notice、notice_txt、notice_txt_photo表的数据全部下载到本地；图片也保存到本地；本地保存后的sort_id=传入的sort_id
     */
	public function down_notice_cloud()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			//下载资源
			$success = 0;//下载成功数
			$exist = 0;//已下载数（重复下载）
			$failure = 0;//下载失败数
			$info = new InfomationModel();
			$isDownload = $info->downloadInfomationSource($id);
			if($isDownload === true)
			{
				$status = 1;
				$data[] = $id;
				$msg =  '恭喜您，1篇公告下载成功！';
			}
			elseif($isDownload === 'isDownloaded')
			{
				$status = 0;
				$msg = '对不起，1篇公告已经下载，无需重复下载！';
			}
			else
			{
				$status = 0;
				$msg = '对不起，1篇公告下载失败，请稍后重试！';
			}
			$this->_ajaxFeedback($status, $data, $msg);
		}
	}
	
	/**
     * 从AZ批量下载公告到本地
     * @author 陆宇峰
     * @return string 返回JSON字符串
     * @todo 从AZ下载公告到本地公告表，notice、notice_txt、notice_txt_photo表的数据全部下载到本地；图片也保存到本地；本地保存后的sort_id=传入的sort_id
     */
	public function down_notice_cloud_batch()
	{
		$ids = $this->_get('id');
		if($this->isAjax() && $ids)
		{
			$success = 0;//下载成功数
			$exist = 0;//已下载数（重复下载）
			$failure = 0;//下载失败数
			$info = new InfomationModel();
			foreach($ids as $key => $val)
			{
				//验证ID是否为数字
				if(!ctype_digit($val))
				{
					continue;
				}
				//下载资源
				$isDownload = $info->downloadInfomationSource($val);
				if($isDownload === true)
				{
					$data[] = array('id' => $val, 'error' => 0);
					$success++;
				}
				elseif($isDownload === 'isDownloaded')
				{
					$data[] = array('id' => $val, 'error' => 1);
					$exist++;
				}
				else
				{
					$data[] = array('id' => $val, 'error' => 1);
					$failure++;
				}
			}
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '篇公告下载成功！';
			}
			if($exist > 0)
			{
				$msg[] = '对不起，' . $exist . '篇公告已经下载，无需重复下载！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '篇公告下载失败，请稍后重试！';
			}
			$msg = implode("<br />", $msg);
			if($success > 0)
			{
				$this->_ajaxFeedback(1, $data, $msg);
			}
			else
			{
				$this->_ajaxFeedback(0, $data, $msg);
			}
		}
	}
	
	/**
     * 获取公告内容
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 获取表tp_notice_txt中notice_id为$id的contents字段值
     */
	public function get_notice_contents()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$azNotice = new AzNoticeModel();
			if($noticeContents = $azNotice->get_notice_contents($id))
			{
				$noticeInfo = $azNotice->get_notice_info($id, 'title');
				$data = array(
					'title' => $noticeInfo['title'],
					'contents' => $noticeContents
				);
				$this->_ajaxFeedback(1, $data);
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，数据初始化失败，请稍后再试！');
			}
		}
	}
	
	/**
     * 添加公告关键词
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 向表tp_notice_keywords中插入一条记录
     */
	public function add_notice_keywords()
	{
		if($this->isAjax())
		{
			$keywords = $this->_get('keywords');
			
			if(!$keywords)
			{
				$this->_ajaxFeedback(0, null, '请输入关键词！');
			}
			$data['keyword'] = $keywords;
			$generalNotice = new GeneralNoticeModel();
			if($generalNotice->addNoticeKeyword($data))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，关键词添加成功！');
			}
		}
	}
	
	/**
     * 修改公告关键词
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 更新表tp_notice_keywords中notice_keywords_id为$id的公告关键词
     */
	public function edit_notice_keywords()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			$keywords = $this->_get('keywords');
			
			if(!$keywords)
			{
				$this->_ajaxFeedback(0, null, '请输入关键词！');
			}
			
			$generalNotice = new GeneralNoticeModel();
			if(false !== $generalNotice->setNoticeKeyword($id, $keywords))
			{
				$data = array('keywords' => $keywords);
				$this->_ajaxFeedback(1, $data, '恭喜您，关键词修改成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，关键词修改失败，请稍后重试！');
		}
	}
	
	/**
     * 删除公告关键词
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除表tp_notice_keywords中notice_keywords_id为$id的记录，改完后不刷新当前页
     */
	public function del_notice_keywords()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$generalNotice = new GeneralNoticeModel();
			if($generalNotice->deleteNoticeKeyword($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，关键词删除成功！');
			}
			$this->_ajaxFeedback(0, null, '对不起，关键词删除失败，请稍后重试！');
		}
	}
	
	/**
     * 获取指定ID公告关键词
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 获取表tp_notice_keywords中notice_keywords_id为$id的公告关键词
     */
	public function fetch_notice_keyword()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$generalNotice = new GeneralNoticeModel();
			$noticeKeyword = $generalNotice->getNoticeKeyword($id);
		}
		if($noticeKeyword)
		{
			$data['keyword'] = $noticeKeyword;
			$this->_ajaxFeedback(1, $data);
		}
		$this->_ajaxFeedback(0, null, '对不起，服务器忙，稍后请重试！');
	}
	
	/**
	 * 公告预览图上传处理
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 调用upImageHandler函数处理上传图片
	 *
	 */
	public function uploadHandler()
	{
		upImageHandler($_FILES['imgFile'], '/notice/thumb');
	}
	
	/**
	 * 删除公告缩略图
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 分两种情况：一，删除未入库图片，此时传入参数为img_url，将此图片URL替换为图片路径直接删除即可；
	 * @todo 二，删除已入库图片，此时传入参数为id，查询表tp_notice中notice_id为$id的path_img值，替换其图片域名占位符为图片路径，然后删除图片，同时将此path_img值置空。
	 *
	 */
	public function delImage()
	{
		$id = $this->_post('id');
		$imgUrl = $this->_post('img_url');
		
		if($this->isAjax())
		{
			if($id)
			{
				$generalNotice = new GeneralNoticeModel();
				$noticeThumbPath = $generalNotice->getNoticeInfo($id, 'path_img');
				$noticeThumbFile = str_replace('##img_domain##', APP_PATH . 'Uploads', $noticeThumbPath['path_img']);
				if($generalNotice->setNoticeInfo($id, array('path_img' => '')))
				{
					@unlink($noticeThumbFile);
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

<?php
/**
 * 新闻管理类
 * 
 *
 */
class AcpNoticeAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}
	
	/**
     * 新闻栏目列表
     * @author 陆宇峰
     * @return void
     * @todo 从notice_sort表中列出数据，注意按排序号排序。表中ID是10以内的参数不能删除。数据库从10起自增
     */
	public function list_sort()
	{
		$act = $this->_get('act');
		$p = $this->_get('p') ? $this->_get('p') : 1;
		
		if($act == 'submit')
		{
			$keyword = $this->_get('keyword');
			
			if($keyword)
			{
				$where = 'notice_sort_name LIKE "%' . $keyword . '%"';
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
		}
		$rows = 15;
		$noticeCategory = new NoticeCategoryModel();
		$noticeCategoryList = $noticeCategory->getNoticeCategoryListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($noticeCategoryList);die;
	
		if($noticeCategoryList && is_array($noticeCategoryList))
		{
			$pagination = array_pop($noticeCategoryList);
			foreach($noticeCategoryList as $key => $val)
			{
				$noticeCategoryList[$key]['no'] = ($p - 1) * $rows + $key + 1;
				$noticeCategoryList[$key]['notice_sort_name'] = mbSubStr($val['notice_sort_name'], 32);
			}
			$this->assign('pagination', $pagination);
			$this->assign('notice_category_list', $noticeCategoryList);
		}
		
		$this->assign('head_title', '新闻栏目列表');
		$this->display();
	}
	
	/**
     * 添加新闻分类
     * @author 陆宇峰
     * @return void
     * @todo 写数据进notice_sort表
     */
	public function add_sort()
	{
		
	}
	
	/**
     * 修改新闻分类
     * @author 陆宇峰
     * @return void
     * @todo 修改notice_sort表的数据
     */
	public function edit_sort()
	{
		
	}
	
	/**
     * 删除新闻分类
     * @author 陆宇峰
     * @return void
     * @todo 删除notice_sort表的数据，删除前确定是否有该分类下的新闻，提示无法删除，请先调整新闻分类。表内ID20以内的数据不能删除
     */
	public function del_sort()
	{
		
	}
	
	/**
     * 新闻列表
     * @author 陆宇峰
     * @return void
     * @todo 从notice表列出数据，默认从左边菜单带出sort_id，可用标题、栏目、发布时间搜索
     */
	public function list_notice()
	{
		$act = $this->_get('act');
		$notice_obj = new NoticeModel();

        $where = 'isuse = 1';

		if($act == 'submit')
		{
			$title = $this->_get('title');

			if($title)
			{
				$where .= ' AND title LIKE "%'.$title.'%"';
			}
			$this->assign('title', $title);
		}


		import('ORG.Util.Pagelist');
        $count = $notice_obj->getNoticeNum($where);
        $Page = new Pagelist($count, C('PER_PAGE_NUM'));
        $notice_obj->setStart($Page->firstRow);
        $notice_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

		$notice_list = $notice_obj->getNoticeList('',$where,'serial asc ');
		$notice_list = $notice_obj->getListData($notice_list);
		$this->assign('notice_list', $notice_list);		
		$this->assign('head_title', '新闻列表');
		$this->display();
	}
	
	/**
     * 添加新闻
     * @author 陆宇峰
     * @return void
     * @todo 添加新闻，tp_notice表，isuse默认1，az_notice_id=0
     * @todo 注意同步插入notice_text，tp_notice_txt_photo表
     */
	public function add_notice()
	{
		$act = $this->_post('act');
		if($act == 'submit')
		{
			$_post = $this->_post();
			$title 			  = $_post['title'];
			$isUse 			  = $_post['isuse'];
			$serial 		  = $_post['serial'];
			$path_img 		  = $_post['path_img'];
			$description 		  = $_post['description'];
			
			//表单验证
			if(!$title)
			{
				$this->error('请输入标题！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请输入纯数字的排序号！');
			}
			$data['title'] 			 = $title;
			$data['path_img'] 		 = $path_img;
			$data['serial'] 		 = $serial;
			$data['addtime'] 		 = time();
			$data['isuse'] 			 = $isUse;
			$data['description'] 		 = $description;
			
			$notice_obj= new NoticeModel();
			if($id = $notice_obj->addNotice($data))
			{
				// dump($notice_obj->getLastSql());die;
				$this->success('恭喜您，新闻添加成功！', '/AcpNotice/list_notice');
			}
			else
			{
				$this->error('对不起，新闻添加失败！');
			}
		}
		$this->assign('pic_data', array(
            'name' => 'path_img',
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));

		$this->assign('action_title', '新闻列表');
		$this->assign('action_src', '/AcpNotice/list_notice');
		$this->assign('head_title', '添加新闻');
		$this->display();
	}
	
	/**
     * 修改新闻
     * @author 陆宇峰
     * @return void
     * @todo 修改tp_notice表.注意同步修改notice_text，tp_notice_txt_photo表
     */
	public function edit_notice()
	{
		$id = $this->_get('id');
		$act = $this->_post('act');
		
		if(!$id || !ctype_digit($id))
		{
			$this->error('非法参数！');
		}
		$notice_obj= new NoticeModel($id);
		
		if($act == 'submit')
		{
			$_post = $this->_post();
			$title 			  = $_post['title'];
			$isUse 			  = $_post['isuse'];
			$serial 		  = $_post['serial'];
			$path_img 		  = $_post['path_img'];
			$description 		  = $_post['description'];
			
			//表单验证
			if(!$title)
			{
				$this->error('请输入标题！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请输入纯数字的排序号！');
			}
			$data['title'] 			 = $title;
			$data['path_img'] 		 = $path_img;
			$data['serial'] 		 = $serial;
			$data['isuse'] 			 = $isUse;
			$data['description'] 		 = $description;
			
			if($notice_obj->editNotice($data))
			{
				$this->success('恭喜您，新闻修改成功！', '/AcpNotice/list_notice');
			}
			else
			{
				$this->error('对不起，新闻修改失败！');
			}
		}
		
		$notice_info = $notice_obj->getNoticeInfo('notice_id ='.$id,'');
	
		$this->assign('pic_data', array(
            'name' => 'path_img',
            'url'  => $notice_info['path_img'],
            'help' => '<span style="color:red;">图片尺寸：600*320像素；</span><span style="color:green;">当选择模板8时，图片尺寸：600*600像素</span>,暂时只支持上传单张2M以内JPEG,PNG,GIF格式图片'
        ));
		$this->assign('notice_info', $notice_info);
		
		$this->assign('action_title', '新闻列表');
		$this->assign('action_src', '/AcpNotice/list_notice');
		$this->assign('head_title', '修改新闻');
		$this->display();
	}
	
	/**
     * 删除新闻
     * @author 陆宇峰
     * @return void
     * @todo 从tp_notice表删除，注意同步删除notice_text，tp_notice_txt_photo表，还有磁盘上的图片文件。
     */
	public function del_notice()
	{
		
	}
	
	/**
     * 批量删除新闻
     * @author 陆宇峰
     * @return void
     * @todo 从tp_notice表删除，注意同步删除notice_text，tp_notice_txt_photo表，还有磁盘上的图片文件。
     */
	public function del_notices()
	{
		
	}
	
	/**
     * 关键词列表
     * @author 陆宇峰
     * @return void
     * @todo 从notice_keywords表取出数据，列出来
     */
	public function list_notice_keywords()
	{
		$act = $this->_get('act');
		$p = $this->_get('p') ? $this->_get('p') : 1;
		
		if($act == 'submit')
		{
			$keyword = $this->_get('keyword');
			
			if($keyword)
			{
				$where = 'keyword LIKE "%' . $keyword . '%"';
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
		}
		$rows = 15;
		$generalNotice = new GeneralNoticeModel();
		$noticeKeywordsList = $generalNotice->getNoticeKeywordsListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($noticeKeywordsList);die;
		
		if($noticeKeywordsList)
		{
			$pagination = array_pop($noticeKeywordsList);
			foreach($noticeKeywordsList as $key => $val)
			{
				$noticeKeywordsList[$key]['no'] = ($p - 1) * $rows + $key + 1;
			}
			$this->assign('pagination', $pagination);
			$this->assign('notice_keywords_list', $noticeKeywordsList);
		}
		
		$this->assign('head_title', '关键词列表');
		$this->display();
	}
	
	/**
     * 添加新闻替换用的关键词
     * @author 陆宇峰
     * @return void
     * @todo 插入数据到notice_keywords表。需判断有无重复
     */
	public function add_notice_keywords()
	{
		
	}
	
	/**
     * 修改新闻替换用的关键词
     * @author 陆宇峰
     * @return void
     * @todo 修改数据到notice_keywords表。需判断有无重复
     */
	public function edit_notice_keywords()
	{
		
	}
	
	/**
     * 删除新闻替换用的关键词
     * @author 陆宇峰
     * @return void
     * @todo 删除notice_keywords表的数据
     */
	public function del_notice_keywords()
	{
		
	}
	
	/**
     * 开网店教程
     * @author 陆宇峰
     * @return void
     * @todo 从AZ列出开网店教程的新闻，sort_id=1，可以点击下载或者批量下载。保存到本地后，sort_id=1
     */
	public function list_taobao_tech()
	{
		$act = $this->_get('act');
		
		if($act == 'submit')
		{
			$keyword 	   = $this->_get('keyword');
			$beginTime 	   = $this->_get('begin_time');
			$endTime 	   = $this->_get('end_time');
			$beginTimeReal = $beginTime ? strtotime($beginTime) : 0;
			$endTimeReal   = $endTime ? strtotime($endTime) : time();
			
			$conditions = array();
			$where = '';
			if($keyword)
			{
				$conditions[] = 'title LIKE "%' . $keyword . '%"';
			}
			if($beginTimeReal > $endTimeReal)
			{
				$this->error('起始时间需小于截止时间！');
			}
			else
			{
				$conditions[] = 'addtime>=' . $beginTimeReal;
				$conditions[] = 'addtime<=' . $endTimeReal;
			}
			if(!empty($conditions))
			{
				$where = implode(' AND ', $conditions);
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
			$this->assign('begin_time', $beginTime);
			$this->assign('end_time', $endTime);
		}
		$rows = 15;
		$info = new InfomationModel();
		$azShopTutorialSourceList = $info->getAzShopTutorialSourceListPage($where, $rows);
		if($azShopTutorialSourceList && is_array($azShopTutorialSourceList))
		{
			$pagination = array_pop($azShopTutorialSourceList);
			
			//是否已下载
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_TECH, 'az_notice_id');
			foreach($azShopTutorialSourceList as $key => $val)
			{
				if(array_key_exists($val['notice_id'], array_flip($downloadedInfomationSourceList)))
				{
					$azShopTutorialSourceList[$key]['is_downloaded'] = 1;
				}
				$azShopTutorialSourceList[$key]['title'] = mbSubStr($val['title'], 30);
				$azShopTutorialSourceList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			
			$this->assign('pagination', $pagination);
			$this->assign('az_shop_tutorial_source_list', $azShopTutorialSourceList);
		}
		
		$this->assign('head_title', '开网店教程列表');
		$this->display();
	}
	
	/**
     * 网店通用素材
     * @author 陆宇峰
     * @return void
     * @todo 从AZ列出网店通用素材的新闻，sort_id=2，可以点击下载或者批量下载。保存到本地后，sort_id=2
     */
	public function list_taobao_course()
	{
		$act = $this->_get('act');
		
		if($act == 'submit')
		{
			$keyword 	   = $this->_get('keyword');
			$beginTime 	   = $this->_get('begin_time');
			$endTime 	   = $this->_get('end_time');
			$beginTimeReal = $beginTime ? strtotime($beginTime) : 0;
			$endTimeReal   = $endTime ? strtotime($endTime) : time();
			
			$conditions = array();
			$where = '';
			if($keyword)
			{
				$conditions[] = 'title LIKE "%' . $keyword . '%"';
			}
			if($beginTimeReal > $endTimeReal)
			{
				$this->error('起始时间需小于截止时间！');
			}
			else
			{
				$conditions[] = 'addtime>=' . $beginTimeReal;
				$conditions[] = 'addtime<=' . $endTimeReal;
			}
			if(!empty($conditions))
			{
				$where = implode(' AND ', $conditions);
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
			$this->assign('begin_time', $beginTime);
			$this->assign('end_time', $endTime);
		}
		$rows = 15;
		$info = new InfomationModel();
		$azTaobaoCourseSourceList = $info->getAzTaobaoCourseSourceListPage($where, $rows);
		if($azTaobaoCourseSourceList && is_array($azTaobaoCourseSourceList))
		{
			$pagination = array_pop($azTaobaoCourseSourceList);
			
			//是否已下载
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_SOURCE, 'az_notice_id');
			foreach($azTaobaoCourseSourceList as $key => $val)
			{
				if(array_key_exists($val['notice_id'], array_flip($downloadedInfomationSourceList)))
				{
					$azTaobaoCourseSourceList[$key]['is_downloaded'] = 1;
				}
				$azTaobaoCourseSourceList[$key]['title'] = mbSubStr($val['title'], 30);
				$azTaobaoCourseSourceList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			
			$this->assign('pagination', $pagination);
			$this->assign('az_taobao_course_source_list', $azTaobaoCourseSourceList);
		}
		
		$this->assign('head_title', '网店通用素材列表');
		$this->display();
	}
	
	/**
     * 常用软件下载
     * @author 陆宇峰
     * @return void
     * @todo 从AZ列出常用软件的新闻，sort_id=3，可以点击下载或者批量下载。保存到本地后，sort_id=3
     */
	public function list_software()
	{
		$act = $this->_get('act');
		
		if($act == 'submit')
		{
			$keyword 	   = $this->_get('keyword');
			$beginTime 	   = $this->_get('begin_time');
			$endTime 	   = $this->_get('end_time');
			$beginTimeReal = $beginTime ? strtotime($beginTime) : 0;
			$endTimeReal   = $endTime ? strtotime($endTime) : time();
			
			$conditions = array();
			$where = '';
			if($keyword)
			{
				$conditions[] = 'title LIKE "%' . $keyword . '%"';
			}
			if($beginTimeReal > $endTimeReal)
			{
				$this->error('起始时间需小于截止时间！');
			}
			else
			{
				$conditions[] = 'addtime>=' . $beginTimeReal;
				$conditions[] = 'addtime<=' . $endTimeReal;
			}
			if(!empty($conditions))
			{
				$where = implode(' AND ', $conditions);
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
			$this->assign('begin_time', $beginTime);
			$this->assign('end_time', $endTime);
		}
		$rows = 15;
		$info = new InfomationModel();
		$azSoftwareSourceList = $info->getAzSoftwareSourceListPage($where, $rows);
		if($azSoftwareSourceList && is_array($azSoftwareSourceList))
		{
			$pagination = array_pop($azSoftwareSourceList);
			
			//是否已下载
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_TOOLS, 'az_notice_id');
			foreach($azSoftwareSourceList as $key => $val)
			{
				if(array_key_exists($val['notice_id'], array_flip($downloadedInfomationSourceList)))
				{
					$azSoftwareSourceList[$key]['is_downloaded'] = 1;
				}
				$azSoftwareSourceList[$key]['title'] = mbSubStr($val['title'], 30);
				$azSoftwareSourceList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			
			$this->assign('pagination', $pagination);
			$this->assign('az_software_source_list', $azSoftwareSourceList);
		}
		
		$this->assign('head_title', '常用工具下载列表');
		$this->display();
	}
	
	/**
     * 电商资讯列表
     * @author 陆宇峰
     * @return void
     * @todo 使用az的数据库连接类，列出az上的notice表。支持新闻名搜索，az的sort_id=4
     */
	public function list_notice_cloud()
	{
		$act = $this->_get('act');
		
		if($act == 'submit')
		{
			$keyword 	   = $this->_get('keyword');
			$beginTime 	   = $this->_get('begin_time');
			$endTime 	   = $this->_get('end_time');
			$beginTimeReal = $beginTime ? strtotime($beginTime) : 0;
			$endTimeReal   = $endTime ? strtotime($endTime) : time();
			
			$conditions = array();
			$where = '';
			if($keyword)
			{
				$conditions[] = 'title LIKE "%' . $keyword . '%"';
			}
			if($beginTimeReal > $endTimeReal)
			{
				$this->error('起始时间需小于截止时间！');
			}
			else
			{
				$conditions[] = 'addtime>=' . $beginTimeReal;
				$conditions[] = 'addtime<=' . $endTimeReal;
			}
			if(!empty($conditions))
			{
				$where = implode(' AND ', $conditions);
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
			$this->assign('begin_time', $beginTime);
			$this->assign('end_time', $endTime);
		}
		$rows = 15;
		$info = new InfomationModel();
		$azInfomationSourceList = $info->getAzInfomationSourceListPage($where, $rows);
		if($azInfomationSourceList && is_array($azInfomationSourceList))
		{
			$pagination = array_pop($azInfomationSourceList);
			
			//是否已下载
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_KNOWLEDGE, 'az_notice_id');
			foreach($azInfomationSourceList as $key => $val)
			{
				if(array_key_exists($val['notice_id'], array_flip($downloadedInfomationSourceList)))
				{
					$azInfomationSourceList[$key]['is_downloaded'] = 1;
				}
				$azInfomationSourceList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			
			$this->assign('pagination', $pagination);
			$this->assign('az_infomation_source_list', $azInfomationSourceList);
		}
		
		$this->assign('head_title', '电商资讯列表');
		$this->display();
	}
	
	/**
     * 移动端收录站资源
     * @author 陆宇峰
     * @return void
     * @todo 从AZ拉取 移动端相关的资源站，不用保存本地，直接读。AZ上tp_link表，link_type = 1
     */
	public function list_weixin_source()
	{
		$act = $this->_get('act');
		$p = $this->_get('p') ? $this->_get('p') : 1;
		
		if($act == 'submit')
		{
			$keyword = $this->_get('keyword');
			
			if($keyword)
			{
				$where = 'link_name LIKE "%' . $keyword . '%"';
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
		}
		$rows = 15;
		$operatingSource = new OperatingSourceModel();
		$weixinSourceList = $operatingSource->getWeixinSourceListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($weixinSourceList);die;
		
		if($weixinSourceList && is_array($weixinSourceList))
		{
			$pagination = array_pop($weixinSourceList);
			foreach($weixinSourceList as $key => $val)
			{
				$weixinSourceList[$key]['no'] = ($p - 1) * $rows + $key + 1;
				$weixinSourceList[$key]['link_name'] = mbSubStr($val['link_name'], 10);
				$weixinSourceList[$key]['link_url'] = mbSubStr($val['link_url'], 30);
				$weixinSourceList[$key]['description'] = mbSubStr($val['description'], 50);
			}
			$this->assign('pagination', $pagination);
			$this->assign('weixin_source_list', $weixinSourceList);
		}
		
		$this->assign('head_title', '移动端站收录列表');
		$this->display();
	}
	
	/**
     * 友情链接购买站资源
     * @author 陆宇峰
     * @return void
     * @todo 从AZ拉取 友链购买站关的资源站，不用保存本地，直接读。AZ上tp_link表，link_type = 2
     */
	public function list_link_source()
	{
		$act = $this->_get('act');
		$p = $this->_get('p') ? $this->_get('p') : 1;
		
		if($act == 'submit')
		{
			$keyword = $this->_get('keyword');
			
			if($keyword)
			{
				$where = 'link_name LIKE "%' . $keyword . '%"';
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
		}
		$rows = 15;
		$operatingSource = new OperatingSourceModel();
		$linkPurchaseSiteSourceList = $operatingSource->getLinkPurchaseSiteSourceListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($linkPurchaseSiteSourceList);die;
		
		if($linkPurchaseSiteSourceList && is_array($linkPurchaseSiteSourceList))
		{
			$pagination = array_pop($linkPurchaseSiteSourceList);
			foreach($linkPurchaseSiteSourceList as $key => $val)
			{
				$linkPurchaseSiteSourceList[$key]['no'] = ($p - 1) * $rows + $key + 1;
				$linkPurchaseSiteSourceList[$key]['link_name'] = mbSubStr($val['link_name'], 10);
				$linkPurchaseSiteSourceList[$key]['link_url'] = mbSubStr($val['link_url'], 30);
				$linkPurchaseSiteSourceList[$key]['description'] = mbSubStr($val['description'], 50);
			}
			$this->assign('pagination', $pagination);
			$this->assign('link_purchase_site_source_list', $linkPurchaseSiteSourceList);
		}
		
		$this->assign('head_title', '友链购买网列表');
		$this->display();
	}
	
	/**
     * 网站收录站资源
     * @author 陆宇峰
     * @return void
     * @todo 从AZ拉取 收录站关的资源站，不用保存本地，直接读。AZ上tp_link表，link_type = 3
     */
	public function list_employ_source()
	{
		$act = $this->_get('act');
		$p = $this->_get('p') ? $this->_get('p') : 1;
		
		if($act == 'submit')
		{
			$keyword = $this->_get('keyword');
			
			if($keyword)
			{
				$where = 'link_name LIKE "%' . $keyword . '%"';
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
		}
		$rows = 15;
		$operatingSource = new OperatingSourceModel();
		$employSourceList = $operatingSource->getEmploySourceListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($employSourceList);die;
		
		if($employSourceList && is_array($employSourceList))
		{
			$pagination = array_pop($employSourceList);
			foreach($employSourceList as $key => $val)
			{
				$employSourceList[$key]['no'] = ($p - 1) * $rows + $key + 1;
				$employSourceList[$key]['link_name'] = mbSubStr($val['link_name'], 6);
				$employSourceList[$key]['link_url'] = mbSubStr($val['link_url'], 50);
				$employSourceList[$key]['description'] = mbSubStr($val['description'], 30);
			}
			$this->assign('pagination', $pagination);
			$this->assign('employ_source_list', $employSourceList);
		}
		
		$this->assign('head_title', '网站收录列表');
		$this->display();
	}
	
	/**
     * SEO查询
     * @author 陆宇峰
     * @return void
     * @todo 如果网站没有绑定顶级域名，提示请先绑定域名（AZ的domain字段）；如果没有备案成功，提示请先备案；
     * @todo 网站上放4个按钮：SEO查询（http://seo.chinaz.com/?host=360shop.com.cn）、友情链接查询（http://link.chinaz.com/?txtSiteUrl=360shop.com.cn）
     * @todo 百度收录查询（http://tool.chinaz.com/baidu/?wd=360shop.com.cn），关键词排名查询（http://tool.chinaz.com/KeyWords/?host=360shop.com.cn）
     */
	public function list_seo_query()
	{
		$domain = $_SERVER['HTTP_HOST'];
		if($domain)
		{
			$this->assign('is_domain', 1);
			if($this->system_config['LICENSE_NO'])
			{
				$queryUrl = array(
					'q_seo'    => 'http://seo.chinaz.com/?host=' . $domain,
					'q_link'   => 'http://link.chinaz.com/?txtSiteUrl=' . $domain,
					'q_record' => 'http://tool.chinaz.com/baidu/?wd=' . $domain,
					'q_rank'   => 'http://tool.chinaz.com/KeyWords/?host=' . $domain
				);
				$this->assign('is_license', 1);
				$this->assign('query_url', $queryUrl);
			}
		}
		
		$this->assign('head_title', 'SEO查询');
		$this->display();
	}
	
	/**
     * SEO知识库
     * @author 陆宇峰
     * @return void
     * @todo 从AZ拉取 SEO知识站关的新闻，不用保存本地，直接读。AZnotice表，sort_id = 5的新闻，
     */
	public function list_seo_source()
	{
		$act = $this->_get('act');
		$p = $this->_get('p') ? $this->_get('p') : 1;
		
		if($act == 'submit')
		{
			$keyword 	   = $this->_get('keyword');
			$beginTime 	   = $this->_get('begin_time');
			$endTime 	   = $this->_get('end_time');
			$beginTimeReal = $beginTime ? strtotime($beginTime) : 0;
			$endTimeReal   = $endTime ? strtotime($endTime) : time();
			
			$conditions = array();
			$where = '';
			if($keyword)
			{
				$conditions[] = 'title LIKE "%' . $keyword . '%"';
			}
			if($beginTimeReal > $endTimeReal)
			{
				$this->error('起始时间需小于截止时间！');
			}
			else
			{
				$conditions[] = 'addtime>=' . $beginTimeReal;
				$conditions[] = 'addtime<=' . $endTimeReal;
			}
			if(!empty($conditions))
			{
				$where = implode(' AND ', $conditions);
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
			$this->assign('begin_time', $beginTime);
			$this->assign('end_time', $endTime);
		}
		$rows = 15;
		$operatingSource = new OperatingSourceModel();
		$azSEOSourceList = $operatingSource->getAzSEOSourceListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($azSEOSourceList);die;
		
		if($azSEOSourceList && is_array($azSEOSourceList))
		{
			$pagination = array_pop($azSEOSourceList);
			foreach($azSEOSourceList as $key => $val)
			{
				$azSEOSourceList[$key]['no'] = ($p - 1) * $rows + $key + 1;
				$azSEOSourceList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			
			$this->assign('pagination', $pagination);
			$this->assign('az_seo_source_list', $azSEOSourceList);
		}
		
		$this->assign('head_title', 'SEO知识库列表');
		$this->display();
	}
	
	/**
     * 编辑关于潘朵拉
     * @author 姜伟
     * @return void
     * @todo 编辑关于潘朵拉
     */
	public function edit_about()
	{
		$this->edit_tag_notice('about', '关于潘朵拉');
	}

	/**
     * 编辑有tag的新闻
     * @author 姜伟
	 * @param $tag 新闻标签
	 * @param $page_name 页面标题
     * @return void
     * @todo 编辑有tag的新闻
     */
	public function edit_tag_notice($tag, $page_name)
	{
		$act = $this->_post('act');
		$generalNotice = new GeneralNoticeModel();
		$noticeData = $generalNotice->getNoticeIdByTag($tag);
		$id = $noticeData['notice_id'];
		if($act == 'submit')
		{
			$_post = $this->_post();
			$sortId 		  = 4;
			$title 			  = $_post['title'];
			$isUse 			  = $_post['isuse'];
			$author 		  = $_post['author'];
			$noticeSource 	  = $_post['notice_source'];
			$clickDot 		  = $_post['clickdot'];
			$serial 		  = $_post['serial'];
			$imgUrl 		  = $_post['img_url'];
			$keywords 		  = $_post['keywords'];
			$description 	  = $_post['description'];
			$contents 		  = $_post['contents'];
			$noticeTxtImages = $_post['notice_txt_images'];
			
			//表单验证
			if(!$title)
			{
				$this->error('请输入标题！');
			}
			if($clickDot && !ctype_digit($clickDot))
			{
				$this->error('请输入纯数字的点击率！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请输入纯数字的排序号！');
			}
			$data['notice_sort_id'] = $sortId;
			$data['title'] 			 = $title;
			$data['author'] 		 = $author;
			$data['notice_source']  = $noticeSource;
			$data['path_img'] 		 = $imgUrl;
			$data['keywords'] 		 = $keywords;
			$data['description'] 	 = $description;
			$data['clickdot'] 		 = $clickDot;
			$data['serial'] 		 = $serial;
			$data['isuse'] 			 = $isUse;
			$data['contents'] 		 = $contents;
			$data['notice_tag'] 	 = $tag;
			
			if($generalNotice->saveNotice($id, $data))
			{
				if($noticeTxtImages && is_array($noticeTxtImages))
				{
					$data = array();
					$data['notice_id'] = $id;
					foreach($noticeTxtImages as $key => $val)
					{
						$data['path_img'] = $val;
						$generalNotice->saveNoticePhoto($data);
					}
				}
				$this->success('恭喜您，新闻修改成功！');
			}
			else
			{
				$this->error('对不起，新闻修改失败！');
			}
		}
		
		$notice_txt_obj = D('notice_txt');
		$notice_txt = $notice_txt_obj->where('notice_id = ' . $noticeData['notice_id'])->find();
		$noticeData['contents'] = $notice_txt['contents'];
		#echo "<pre>";
		#print_r($noticeData);die;
	
		$noticeData['path_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $noticeData['path_img']);
		$this->assign('notice_data', $noticeData);
		
		$this->assign('action_title', '新闻列表');
		$this->assign('action_src', '/AcpNotice/list_notice');
		$this->assign('head_title', '修改' . $page_name);
		$this->display(APP_PATH . 'Tpl/AcpNotice/edit_tag_notice.html');
	}
}
?>

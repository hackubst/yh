<?php
/**
 * 文章管理类
 *
 *
 */
class AcpArticleAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}

//	/**
//     * 文章列表
//     * @author 陆宇峰
//     * @return void
//     * @todo 从article表列出数据，默认从左边菜单带出sort_id，可用标题、栏目、发布时间搜索
//     */
//	public function list_article()
//	{
//		$act = $this->_get('act');
//		$generalArticle = new GeneralArticleModel();
//		$_table = $generalArticle->getTableName();
//		if($act == 'submit')
//		{
//			$keyword 	   = $this->_get('keyword');
//			$sortId 	   = $this->_get('sort_id');
//			$beginTime 	   = $this->_get('begin_time');
//			$endTime 	   = $this->_get('end_time');
//			$beginTimeReal = $beginTime ? strtotime($beginTime) : 0;
//			$endTimeReal   = $endTime ? strtotime($endTime) : time();
//
//			$conditions = array();
//			$where = '';
//			if($keyword)
//			{
//				$conditions[] = $_table . '.title LIKE "%' . $keyword . '%"';
//			}
//			if($sortId)
//			{
//				$conditions[] = $_table . '.article_sort_id=' . $sortId;
//			}
//			if($beginTimeReal > $endTimeReal)
//			{
//				$this->error('起始时间需小于截止时间！');
//			}
//			else
//			{
//				$conditions[] = $_table . '.addtime>=' . $beginTimeReal;
//				$conditions[] = $_table . '.addtime<=' . $endTimeReal;
//			}
//			if(!empty($conditions))
//			{
//				$where = implode(' AND ', $conditions);
//			}
//
//			$this->assign('is_search', 1);
//			$this->assign('keyword', $keyword);
//			$this->assign('begin_time', $beginTime);
//			$this->assign('end_time', $endTime);
//			$this->assign('article_category_option_selected', $sortId);
//		}
//		$rows = 15;
//		$generalArticleList = $generalArticle->getGeneralArticleListPage($where, $rows);
//	//	echo "<pre>";
//	//	print_r($generalArticleList);die;
//
//		if($generalArticleList && is_array($generalArticleList))
//		{
//			$pagination = array_pop($generalArticleList);
//			foreach($generalArticleList as $key => $val)
//			{
//				$generalArticleList[$key]['title'] = mbSubStr($val['title'], 25);
//				$generalArticleList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
//			}
//
//			$this->assign('pagination', $pagination);
//			$this->assign('general_article_list', $generalArticleList);
//		}
//
//		//分类列表
//		$articleCategory = new ArticleCategoryModel();
//		$fields = 'article_sort_id,article_sort_name';
//		$articleCategoryList = $articleCategory->getArticleCategoryList('', '', $fields);
//		if($articleCategoryList && is_array($articleCategoryList))
//		{
//			foreach($articleCategoryList as $key => $val)
//			{
//				$articleCategoryOptions[$val['article_sort_id']] = $val['article_sort_name'];
//			}
//			$this->assign('article_category_options', $articleCategoryOptions);
//		}
//
//		$this->assign('head_title', '文章列表');
//		$this->display();
//	}

//	/**
//     * 添加文章
//     * @author 陆宇峰
//     * @return void
//     * @todo 添加文章，tp_article表，isuse默认1，az_article_id=0
//     * @todo 注意同步插入article_text，tp_article_txt_photo表
//     */
//	public function add_article()
//	{
//		$sortId = $this->_get('sort_id');
//		$act = $this->_post('act');
//		if($act == 'submit')
//		{
//			$_post = $this->_post();
//			$sortId 		  = $_post['sort_id'];
//			$title 			  = $_post['title'];
//			$isUse 			  = $_post['isuse'];
//			$author 		  = $_post['author'];
//			$articleSource 	  = $_post['article_source'];
//			$clickDot 		  = $_post['clickdot'];
//			$serial 		  = $_post['serial'];
//			$imgUrl 		  = $_post['img_url'];
//			$keywords 		  = $_post['keywords'];
//			$description 	  = $_post['description'];
//			$contents 		  = $_post['contents'];
//			$articleTxtImages = $_post['article_txt_images'];
//
//			//表单验证
//			if(!$title)
//			{
//				$this->error('请输入标题！');
//			}
//			if(!$sortId || !ctype_digit($sortId))
//			{
//				$this->error('请选择栏目！');
//			}
//			if($clickDot && !ctype_digit($clickDot))
//			{
//				$this->error('请输入纯数字的点击率！');
//			}
//			if($serial && !ctype_digit($serial))
//			{
//				$this->error('请输入纯数字的排序号！');
//			}
//			$data['article_sort_id'] = $sortId;
//			$data['title'] 			 = $title;
//			$data['author'] 		 = $author;
//			$data['article_source']  = $articleSource;
//			$data['path_img'] 		 = $imgUrl;
//			$data['keywords'] 		 = $keywords;
//			$data['description'] 	 = $description;
//			$data['clickdot'] 		 = $clickDot;
//			$data['serial'] 		 = $serial;
//			$data['addtime'] 		 = time();
//			$data['isuse'] 			 = $isUse;
//			$data['download_link'] 	 = $downloadLink;
//			$data['contents'] 		 = $contents;
//
//			$generalArticle = new GeneralArticleModel();
//			if($id = $generalArticle->addArticle($data))
//			{
//				if($articleTxtImages && is_array($articleTxtImages))
//				{
//					$data = array();
//					$data['article_id'] = $id;
//					foreach($articleTxtImages as $key => $val)
//					{
//						$data['path_img'] = $val;
//						$generalArticle->saveArticlePhoto($data);
//					}
//				}
//				$this->success('恭喜您，文章添加成功！', '/AcpArticle/list_article');
//			}
//			else
//			{
//				$this->error('对不起，文章添加失败！');
//			}
//		}
//		//分类列表
//		$articleCategory = new ArticleCategoryModel();
//		$fields = 'article_sort_id,article_sort_name';
//		$where = 'isuse=1';
//		$articleCategoryList = $articleCategory->getArticleCategoryList('', '', $fields, $where);
//		if($articleCategoryList && is_array($articleCategoryList))
//		{
//			foreach($articleCategoryList as $key => $val)
//			{
//				$articleCategoryOptions[$val['article_sort_id']] = $val['article_sort_name'];
//			}
//			$this->assign('article_category_options', $articleCategoryOptions);
//		}
//
//		if($sortId)
//		{
//			$this->assign('sort_id', $sortId);
//		}
//
//		$this->assign('action_title', '文章列表');
//		$this->assign('action_src', '/AcpArticle/list_article');
//		$this->assign('head_title', '添加文章');
//		$this->display();
//	}

	/**
     * 修改文章
     * @author 陆宇峰
     * @return void
     * @todo 修改tp_article表.注意同步修改article_text，tp_article_txt_photo表
     */
	public function edit_article()
	{
		$id = $this->_get('id');
		$article_obj = new ArticleModel($id);
		$article = $article_obj->getArticleInfo('article_id='.$id);
		$this->assign('article_data',$article);

		if(IS_POST){
			$data = $this->_post();
			$result = $article_obj->editArticle('article_id ='.$id,$data);
			if($result){
				$this->success('编辑文章成功','/AcpArticle/list_article');
			}else{
				$this->error('系统繁忙，请稍后再试');
			}
		}


		$this->assign('head_title', '修改文章');
		$this->display();
	}

	/**
     * 删除文章
     * @author 陆宇峰
     * @return void
     * @todo 从tp_article表删除，注意同步删除article_text，tp_article_txt_photo表，还有磁盘上的图片文件。
     */
	public function del_article()
	{

	}

	/**
     * 批量删除文章
     * @author 陆宇峰
     * @return void
     * @todo 从tp_article表删除，注意同步删除article_text，tp_article_txt_photo表，还有磁盘上的图片文件。
     */
	public function del_articles()
	{

	}

	/**
     * 关键词列表
     * @author 陆宇峰
     * @return void
     * @todo 从article_keywords表取出数据，列出来
     */
	public function list_article_keywords()
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
		$generalArticle = new GeneralArticleModel();
		$articleKeywordsList = $generalArticle->getArticleKeywordsListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($articleKeywordsList);die;

		if($articleKeywordsList)
		{
			$pagination = array_pop($articleKeywordsList);
			foreach($articleKeywordsList as $key => $val)
			{
				$articleKeywordsList[$key]['no'] = ($p - 1) * $rows + $key + 1;
			}
			$this->assign('pagination', $pagination);
			$this->assign('article_keywords_list', $articleKeywordsList);
		}

		$this->assign('head_title', '关键词列表');
		$this->display();
	}

	/**
     * 添加文章替换用的关键词
     * @author 陆宇峰
     * @return void
     * @todo 插入数据到article_keywords表。需判断有无重复
     */
	public function add_article_keywords()
	{

	}

	/**
     * 修改文章替换用的关键词
     * @author 陆宇峰
     * @return void
     * @todo 修改数据到article_keywords表。需判断有无重复
     */
	public function edit_article_keywords()
	{

	}

	/**
     * 删除文章替换用的关键词
     * @author 陆宇峰
     * @return void
     * @todo 删除article_keywords表的数据
     */
	public function del_article_keywords()
	{

	}

	/**
     * 开网店教程
     * @author 陆宇峰
     * @return void
     * @todo 从AZ列出开网店教程的文章，sort_id=1，可以点击下载或者批量下载。保存到本地后，sort_id=1
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
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_TECH, 'az_article_id');
			foreach($azShopTutorialSourceList as $key => $val)
			{
				if(array_key_exists($val['article_id'], array_flip($downloadedInfomationSourceList)))
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
     * @todo 从AZ列出网店通用素材的文章，sort_id=2，可以点击下载或者批量下载。保存到本地后，sort_id=2
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
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_SOURCE, 'az_article_id');
			foreach($azTaobaoCourseSourceList as $key => $val)
			{
				if(array_key_exists($val['article_id'], array_flip($downloadedInfomationSourceList)))
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
     * @todo 从AZ列出常用软件的文章，sort_id=3，可以点击下载或者批量下载。保存到本地后，sort_id=3
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
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_TOOLS, 'az_article_id');
			foreach($azSoftwareSourceList as $key => $val)
			{
				if(array_key_exists($val['article_id'], array_flip($downloadedInfomationSourceList)))
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
     * @todo 使用az的数据库连接类，列出az上的article表。支持文章名搜索，az的sort_id=4
     */
	public function list_article_cloud()
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
			$downloadedInfomationSourceList = $info->getDownloadedInfomationSourceList(ARTICLE_SORT_KNOWLEDGE, 'az_article_id');
			foreach($azInfomationSourceList as $key => $val)
			{
				if(array_key_exists($val['article_id'], array_flip($downloadedInfomationSourceList)))
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
     * @todo 从AZ拉取 SEO知识站关的文章，不用保存本地，直接读。AZarticle表，sort_id = 5的文章，
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
		$this->edit_tag_article('about', '关于潘朵拉');
	}

	/**
     * 编辑积分来源
     * @author wzlf
     * @return void
     * @todo 编辑积分来源
     */
	public function edit_integral_source()
	{
		$this->edit_tag_article('integral_source', '积分来源');
	}

	/**
     * 编辑积分消费
     * @author zlf
     * @return void
     * @todo 编辑积分消费
     */
	public function edit_integral_consume()
	{
		$this->edit_tag_article('integral_consume', '积分消费');
	}

	/**
     * 编辑积分方案
     * @author zlf
     * @return void
     * @todo 编辑积分方案
     */
	public function edit_integral_rule()
	{
		$this->edit_tag_article('integral_rule', '积分方案');
	}

	/**
     * 公共文章
     * @author zlf
     * @return void
     * @todo 公共文章
     */
	public function common_article()
	{
        $tag = $this->_get('tag');
		if(!$tag)
		{
			header('Location: /Common/page404');
		}
		else
		{
			$generalArticle = new GeneralArticleModel();
			$articleData = $generalArticle->getArticleIdByTag($tag);
			$id = $articleData['article_id'];
		}

		$generalArticle = new GeneralArticleModel();
		$generalArticleInfo = $generalArticle->getArticleInfo($id);

		$generalArticleContents = $generalArticle->getArticleContents($id);
		$this->assign('general_article_info', $generalArticleInfo);
		$this->assign('general_article_contents', $generalArticleContents);

		$this->assign('head_title', $generalArticleInfo['title']);
		$this->display();
	}


	/**
     * 编辑有tag的文章
     * @author 姜伟
	 * @param $tag 文章标签
	 * @param $page_name 页面标题
     * @return void
     * @todo 编辑有tag的文章
     */
	public function edit_tag_article($tag, $page_name)
	{
		$act = $this->_post('act');
		$generalArticle = new GeneralArticleModel();
		$articleData = $generalArticle->getArticleIdByTag($tag);
		if($act == 'submit')
		{
			$_post = $this->_post();
			$sortId 		  = 4;
			$title 			  = $_post['title'];
			$isUse 			  = $_post['isuse'];
			$author 		  = $_post['author'];
			$articleSource 	  = $_post['article_source'];
			$clickDot 		  = $_post['clickdot'];
			$serial 		  = $_post['serial'];
			$imgUrl 		  = $_post['img_url'];
			$keywords 		  = $_post['keywords'];
			$description 	  = $_post['description'];
			$contents 		  = $_post['contents'];
			$articleTxtImages = $_post['article_txt_images'];

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
			$data['article_sort_id'] = $sortId;
			$data['title'] 			 = $title;
			$data['author'] 		 = $author;
			$data['article_source']  = $articleSource;
			$data['path_img'] 		 = $imgUrl;
			$data['keywords'] 		 = $keywords;
			$data['description'] 	 = $description;
			$data['clickdot'] 		 = $clickDot;
			$data['serial'] 		 = $serial;
			$data['isuse'] 			 = $isUse;
			$data['contents'] 		 = $contents;
			$data['article_tag'] 	 = $tag;
			if($articleData)
			{
				$id = $articleData['article_id'];

				if($generalArticle->saveArticle($id, $data))
				{
					if($articleTxtImages && is_array($articleTxtImages))
					{
						$data = array();
						$data['article_id'] = $id;
						foreach($articleTxtImages as $key => $val)
						{
							$data['path_img'] = $val;
							$generalArticle->saveArticlePhoto($data);
						}
					}
					$this->success('恭喜您，文章修改成功！');
				}
				else
				{
					$this->error('对不起，文章修改失败！');
				}
			}else{
				if($id = $generalArticle->addArticle($data))
				{
					if($articleTxtImages && is_array($articleTxtImages))
					{
						$data = array();
						$data['article_id'] = $id;
						foreach($articleTxtImages as $key => $val)
						{
							$data['path_img'] = $val;
							$generalArticle->saveArticlePhoto($data);
						}
					}
					$this->success('恭喜您，文章修改成功！');
				}
				else
				{
					$this->error('对不起，文章修改失败！');
				}
			}
		}

		$article_txt_obj = D('article_txt');
		$article_txt = $article_txt_obj->where('article_id = ' . $articleData['article_id'])->find();
		$articleData['contents'] = $article_txt['contents'];
		#echo "<pre>";
		#print_r($articleData);die;

		$articleData['path_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $articleData['path_img']);
		$this->assign('article_data', $articleData);

		$this->assign('action_title', '文章列表');
		$this->assign('action_src', '/AcpArticle/list_article');
		$this->assign('head_title', '修改' . $page_name);
		$this->display(APP_PATH . 'Tpl/AcpArticle/edit_tag_article.html');
	}

    /**
     * @todo 搜索条件
     * @author lye
     * Date: 2018-06-19
     * Time: 16:54
     */
    private function get_search_condition()
    {
        $where = '';
        $act = $this->_request('act');
        if($act == 'search')
        {
            $article_sort_name = $this->_request('article_sort_name');
            $title             = $this->_request('title');
            $sort_id           = $this->_request('sort_id');
            $begin_time        = $this->_request('begin_time');
            $end_time          = $this->_request('end_time');

            if($article_sort_name)
            {
                $where .= ' AND article_sort_name LIKE "%' . $article_sort_name . '%"';
                $this->assign('article_sort_name', $article_sort_name);
            }

            if($title)
			{
				$where .= ' AND title LIKE "%'. $title. '%"';
                $this->assign('title', $title);
            }

            if($sort_id)
			{
                $where .= ' AND article_sort_id='. $sort_id;
                $this->assign('sort_id', $sort_id);
            }

            if($begin_time)
            {
                $where .= ' AND addtime >='. strtotime($begin_time);
                $this->assign('begin_time', $begin_time);
            }

            if($end_time)
            {
                $where .= ' AND addtime <='. strtotime($end_time);
                $this->assign('end_time', $end_time);
            }

            $this->assign('is_search', 1);
        }
        return $where;
    }

    /**
     * 文章栏目列表
     * @author lye
     * @return void
     * @todo 从article_sort表中列出数据，注意按排序号排序。表中ID是10以内的参数不能删除。数据库从10起自增
     */
    public function list_sort()
    {
        $article_sort_obj = new ArticleSortModel();
        $where = 'isuse=1'.$this->get_search_condition();

        //分页处理
        import('ORG.Util.Pagelist');
        $count = $article_sort_obj->getArticleSortNum($where);
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $article_sort_obj->setStart($Page->firstRow);
        $article_sort_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $article_sort_list = $article_sort_obj->getArticleSortList('', $where, 'serial ASC');
        $article_sort_list = $article_sort_obj->strSubArticleSortName($article_sort_list);

        $this->assign('article_sort_list', $article_sort_list);
        $this->assign('head_title', '文章栏目列表');
        $this->display();
    }


    /**
     * @todo 添加 | 修改文章
     * @author lye
     * Date: 2018-06-19
     * Time: 17:17
     */
    public function add_article()
    {
        $article_obj = new ArticleModel();
        $article_id  = I('get.article_id');
        if($article_id)
        {
        	$article_info = $article_obj->getArticleInfo('isuse !=2 AND article_id='.$article_id);
        	if(!$article_info) $this->error('文章飞走拉！');
        	$this->assign('article_info', $article_info);
        }

        // 接收参数
        if(IS_POST)
        {
            $_post = $this->_post();
            $title 			  = $_post['title'];
            $sortId 		  = $_post['sort_id'];
            $isUse 			  = $_post['isuse'];
            $author 		  = $_post['author'];
            $articleSource 	  = $_post['article_source'];
            $serial 		  = $_post['serial'];
            $imgUrl 		  = $_post['img_url'];
            $keywords 		  = $_post['keywords'];
            $description 	  = $_post['description'];
            $contents 		  = $_post['contents'];
            #$articleTxtImages = $_post['article_txt_images'];

            //表单验证
            if(!$title)
            {
                $this->error('请输入标题！');
            }
            if(!$sortId || !ctype_digit($sortId))
            {
                $this->error('请选择栏目！');
            }
            if($serial && !ctype_digit($serial))
            {
                $this->error('请输入纯数字的排序号！');
            }

            $data['title'] 			 = $title;
            $data['article_sort_id'] = $sortId;
            $data['author'] 		 = $author;
            $data['article_source']  = $articleSource;
            $data['path_img'] 		 = $imgUrl;
            $data['keywords'] 		 = $keywords;
            $data['description'] 	 = $description;
            $data['serial'] 		 = $serial;
            $data['addtime'] 		 = time();
            $data['isuse'] 			 = $isUse;
            $data['contents'] 		 = $contents;

            if($article_id)
			{
				$res = $article_obj->newEditArticle('article_id='.$article_id, $data);
			}
			else
			{
                $res = $article_obj->addArticle($data);
            }

            if($res !== false)
			{
				$this->success('操作成功', '/AcpArticle/list_article');
			}
			else
			{
                $this->error('系统繁忙，请重试！');
            }
        }

        $this->assign('pic_data', array(
            'name'  => 'img_url',
			'title' => '缩略图',
			'url'   => $article_info ? $article_info['path_img'] : ''
        ));

        // 返回文章分类列表
        $where  = 'isuse=1';
        $fields = 'article_sort_id,article_sort_name';
        $order  = 'serial ASC';
        $article_sort_list = (new ArticleSortModel())->getArticleSortList($fields, $where, $order);
        $this->assign('article_sort_list', $article_sort_list);

        $this->assign('action_title', '文章列表');
        $this->assign('action_src', '/AcpArticle/list_article');
        $this->assign('head_title', $article_id ? '编辑文章' :  '添加文章');
        $this->display();
    }

    /**
     * 文章列表
     * @author 陆宇峰
     * @return void
     * @todo 从article表列出数据，默认从左边菜单带出sort_id，可用标题、栏目、发布时间搜索
     */
    // public function list_article()
    // {
    //     $article_obj = new ArticleModel();
    //     $where = "isuse != 2 AND article_tag=''".$this->get_search_condition();
    //     //分页处理
    //     import('ORG.Util.Pagelist');
    //     $count = $article_obj->getArticleNum($where);
    //     $Page = new Pagelist($count,C('PER_PAGE_NUM'));
    //     $article_obj->setStart($Page->firstRow);
    //     $article_obj->setLimit($Page->listRows);
    //     $show = $Page->show();
    //     $this->assign('show', $show);

    //     $article_list = $article_obj->getArticleList('', $where, 'serial ASC');
    //     $article_list = $article_obj->getListData($article_list);
    //     $this->assign('article_list', $article_list);

    //     // 返回文章分类列表
    //     $where  = 'isuse=1';
    //     $fields = 'article_sort_id,article_sort_name';
    //     $order  = 'serial ASC';
    //     $article_sort_list = (new ArticleSortModel())->getArticleSortList($fields, $where, $order);
    //     $this->assign('article_sort_list', $article_sort_list);

    //     $this->assign('head_title', '文章列表');
    //     $this->display();
    // }
    public function list_article()
    {
        $article_obj = new ArticleModel();
        $where = "isuse != 2";

        $article_list = $article_obj->getArticleList('',$where, 'serial ASC');
        $this->assign('list', $article_list);

        $this->assign('head_title', '文章列表');
        $this->display();
    }
}
?>

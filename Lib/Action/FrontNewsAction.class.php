<?php 
class FrontNewsAction extends FrontAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->news_num_per_page = C('NEWS_NUM_PER_PAGE');

	}

	//新闻列表页
	public function news_list()
	{	
		//获取分类名称
		/*$sort_id = intval($this->_get('sort_id'));
		$sort_id = $sort_id ? $sort_id : 0;
		$article_sort_obj = new ArticleCategoryModel();
		$article_sort_list = $article_sort_obj->getArticleCategoryList('','serial ASC','article_sort_id, article_sort_name', 'isuse=1');
		$this->assign('article_sort_list', $article_sort_list);*/
		//获取文章列表
        $where = 'isuse = 1 AND (user_type = 3 OR user_type = 0)';
		$generalArticle = new GeneralArticleModel();
		$total = $generalArticle->getTotal($where);
		$generalArticle->setStart(0);
        $generalArticle->setLimit($this->news_num_per_page);
		#$news_list = $generalArticle->getLimitGeneralArticleList('','addtime DESC');
		$news_list = $generalArticle->field('article_id, title')->where($where)->order('addtime DESC')->limit()->select();
		#echo "<pre>";
		#print_r($this->news_num_per_page);
		#echo $generalArticle->getLastSql();
		#die;
		$this->assign('total', $total);
		$this->assign('firstRow', $this->news_num_per_page);
		$this->assign('news_list', $news_list);

		$this->assign('head_title', '新闻列表页');
		
		$this->assign('sort_id', $sort_id);
		$this->display();
	}

	//新闻详情页
	public function news_detail()
	{
		$sort_id = intval($this->_get('sort_id'));
		$id = intval($this->_get('id'));
		$tag = $this->_get('tag');
		if(!$id && !$tag)
		{
			header('Location: /Common/page404');
		}

		if (!$id && $tag)
		{
			$generalArticle = new GeneralArticleModel();
			$articleData = $generalArticle->getArticleIdByTag($tag);
			$id = $articleData['article_id'];
		}

		//获取文章列表
		$generalArticle = new GeneralArticleModel();
		$generalArticleList = $generalArticle->getGeneralArticleListFrontPage('article_sort_id = ' . $sort_id, 10);
		$pagination = array_pop($generalArticleList);
		$this->assign('general_article_list', $generalArticleList);

		$generalArticle = new GeneralArticleModel();
		$generalArticle->addClickdot($id);
		$generalArticleInfo = $generalArticle->getArticleInfo($id);
		#echo "<pre>";
		#print_r($generalArticleInfo);
		#die;
		$generalArticleContents = $generalArticle->getArticleContents($id, true);
		$generalArticleInfo['addtime'] = date('Y-m-d H:i:s', $generalArticleInfo['addtime']);
		#$summary = filterAndSubstr($generalArticleContents, 200, '<p><a><br>');
		#$this->assign('summary', $summary);
		$this->assign('general_article_info', $generalArticleInfo);
		$this->assign('general_article_contents', $generalArticleContents);

		
		$this->assign('sort_id', $sort_id);
		$this->assign('title', $generalArticleInfo['title']);
		$this->assign('description', $generalArticleInfo['description']);
		$this->assign('keywords', $generalArticleInfo['keywords']);
		$this->assign('author', $generalArticleInfo['author'] ? $generalArticleInfo['author'] : $GLOBALS['config_info']['COMPANY_NAME']);
		$this->assign('Copyright', $generalArticleInfo['article_source'] ? $generalArticleInfo['article_source'] : $GLOBALS['config_info']['COMPANY_NAME']);
		//判断操作类型
		$opt = '';
		$acticle_title = '';
		if ($tag == 'about_us')
		{
			$opt = 'about_us';
			$acticle_title = '关于我们';
		}else{
			$acticle_title = $generalArticleInfo['title'];
		}
		$this->assign('head_title', $acticle_title);

		$this->assign('detail_id', $id);
		$this->assign('opt', $opt);
		$this->assign('page_name', $page_name);
		$this->display();
	}
	
	//系统公告列表页
	public function notice_list()
	{
		$user_id = intval(session('user_id'));
		
		$notice_obj = new NoticeModel();
        $where = '(user_type = 3 OR user_type = 0)';
		//总数
		$total = $notice_obj->getNoticeNum($where);
		$notice_list = $notice_obj->getNoticeList('notice_id, title',$where,'addtime DESC');
		$notice_list = $notice_obj->getListData($notice_list);
		$this->assign('notice_list',$notice_list);
		#echo $notice_obj->getLastSql();
		#die;

		$this->assign('total', $total);
		$this->assign('firstRow', $this->news_num_per_page);
		$this->assign('head_title', '系统公告列表页');
		$this->display();
	}
	
	//系统公告详情页
	public function notice_detail()
	{
		$id = intval($this->_get('id'));

		$notice_obj = new NoticeModel();
		$noticeInfo = $notice_obj->getNoticeInfo('notice_id = ' .$id, 'title, addtime, description');
		$this->assign('noticeInfo',$noticeInfo);

		$this->assign('detail_id', $id);
		$this->assign('opt', $opt);
		$this->assign('page_name', $page_name);
		$this->assign('head_title', '系统公告详情页');
		$this->display();
	}

	//异步获取新闻列表
	public function get_news_list()
	{
		$firstRow = I('post.firstRow');
		$user_id = intval(session('user_id'));
		$generalArticle = new GeneralArticleModel();

		$where = 'isuse = 1';
		//总数
		$total = $generalArticle->getTotal($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$generalArticle->setStart($firstRow);
			$generalArticle->setLimit($this->news_num_per_page);
			$news_list = $generalArticle->field('article_id, title')->where($where)->order('addtime DESC')->limit()->select();
			
			echo json_encode($news_list);
			exit;
		}

		exit('failure');
	}

	//异步获取公告列表
	public function get_notice_list()
	{
		$firstRow = I('post.firstRow');
		$user_id = intval(session('user_id'));
		$notice_obj = new NoticeModel();
		$where = 'user_type = 3 AND user_id = ' .$user_id;

		//总数
		$total = $notice_obj->getNoticeNum($where);

		if ($firstRow <= ($total - 1) && $user_id)
		{
			$notice_obj->setStart($firstRow);
			$notice_obj->setLimit($this->news_num_per_page);
			$notice_list = $notice_obj->getNoticeList('notice_id, title','user_type = 3','addtime DESC');
			$notice_list = $notice_obj->getListData($notice_list);
			echo json_encode($notice_list);
			exit;
		}

		exit('failure');
	}
}

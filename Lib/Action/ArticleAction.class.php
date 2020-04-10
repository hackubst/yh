<?php

// 文章资讯
class ArticleAction extends FrontAction {

    function _initialize() {
        parent::_initialize();
    }

    
    /**
    * 文章列表
    * @return void
    * @author <zgq@360shop.cc>
    * @todo 根据条件把文章表tp_article数据取出来
    */
    public function article_list()
    {
		$id = $this->_get('id');
		$articleCategory = new ArticleCategoryModel();
		if(!$id)
		{
			$where = '';
		}
		elseif(!ctype_digit($id))
		{
			header('Location: /Common/page404');
		}
		else
		{
			$where = 'article_sort_id=' . $id;
			$articleCategoryInfo = $articleCategory->getArticleCategory($id, 'article_sort_name');
			$this->assign('article_sort_name', $articleCategoryInfo['article_sort_name']);
			$this->assign('article_sort_id', $id);
		}
    	$generalArticle = new GeneralArticleModel();
		$generalArticleList = $generalArticle->getGeneralArticleListFrontPage($where);
	//	echo "<pre>";
	//	print_r($generalArticleList);die;
		
		$pagination = array_pop($generalArticleList);
		foreach($generalArticleList as $key => $val)
		{
			$generalArticleList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
		}
		$fields = 'article_sort_id,article_sort_name';
		$where = 'isuse=1';
		$articleCategoryList = $articleCategory->getArticleCategoryList('', '', $fields, $where);
	//	echo "<pre>";
	//	print_r($articleCategoryList);die;
		
		$this->assign('pagination', $pagination);
		$this->assign('article_category_list', $articleCategoryList);
		$this->assign('general_article_list', $generalArticleList);
		
		$this->assign('head_title', '全部资讯');
		$this->display();
    }
    
   /**
    * 文章详情
    * @return void
    * @author <zgq@360shop.cc>
    * @todo 根据条件把文章表tp_article与文章内容tp_article_txt,文章图片tp_article_txt_photo数据取出来
    */
    public function article_display()
	{
		$sortId = $this->_get('sort_id');
        $id = $this->_get('id');
		if(!$id || !ctype_digit($id))
		{
			header('Location: /Common/page404');
		}
		$generalArticle = new GeneralArticleModel();
		$generalArticle->addClickdot($id);
		
		$fields = 'title,article_source,clickdot,addtime';
		$generalArticleInfo = $generalArticle->getArticleInfo($id, $fields);
		$generalArticleContents = $generalArticle->getArticleContents($id, true);
		
		$generalArticleInfo['addtime'] = date('Y-m-d H:i:s', $generalArticleInfo['addtime']);
		$summary = filterAndSubstr($generalArticleContents, 200, '<p><a><br>');
		
		$fields = 'article_sort_id,article_sort_name';
		$where = 'isuse=1';
		$articleCategory = new ArticleCategoryModel();
		$articleCategoryList = $articleCategory->getArticleCategoryList('', '', $fields, $where);
		
		$this->assign('article_category_list', $articleCategoryList);
		$this->assign('general_article_info', $generalArticleInfo);
		$this->assign('general_article_contents', $generalArticleContents);
		$this->assign('summary', $summary);
		$this->assign('article_sort_id', $sortId);
		
		$this->assign('list_src', '/Article/article_list');
		$this->assign('list_title', '全部资讯');
		$this->assign('head_title', '资讯详情');
		$this->display();
    }
    
    
     /**
    * 帮助中心
    * @return void
    * @author <zgq@360shop.cc>
    * @todo 根据条件把文章分类表tp_article_sort中是帮助中心的数据取出来
    */
    public function helpcenter()
	{
        $id = $this->_get('id');
		$helpCenter = new HelpCenterModel();
		if($id)
		{
			if(!ctype_digit($id))
			{
				header('Location: /Common/page404');
			}
			if(!$helpCenter->getTotal('help_id=' . $id))
			{
				header('Location: /Common/page404');
			}
			$helpInfo = $helpCenter->getHelpInfo($id, 'title');
			$this->assign('help_title', $helpInfo['title']);
			$this->assign('help_contents', $helpCenter->getHelpContents($id));
		}
		
		$fields = 'help_sort_id,help_sort_name';
		$where = 'isuse=1';
		$helpCenterCategory = new HelpCenterCategoryModel();
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields, $where);
		
		foreach($helpCenterCategoryList as $key => $val)
		{
			$fields = 'help_id,title';
			$where = 'isuse=1 AND help_sort_id=' . $val['help_sort_id'];
			$helpCenterCategoryList[$key]['left_help_center_menu'] = $helpCenter->getHelpList('', '', $fields, $where);
		}
		
		$this->assign('help_center_category_list', $helpCenterCategoryList);
		$this->assign('head_title', '帮助中心');
		$this->display();
    }
    
    
   /**
    * 帮助中心详情
    * @param string 
    * @return void
    * @author <zgq@360shop.cc>
    * @todo 根据条件把文章表tp_article中article_tag字段是帮助中心的数据取出来
    */
    public function helpcenter_display() {
        
        
        $head_title = $this->get_header_title('帮助中心详情页');
        $this->assign('head_title', $head_title);
        $this->display();
    }
    
    
}


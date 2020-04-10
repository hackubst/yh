<?php 
class FrontHelpAction extends FrontAction{
	function _initialize() 
	{
		if (ACTION_NAME != 'area_not_open')
		{
			parent::_initialize();
		}
	}

	//帮助中心列表页
	public function help_list()
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
        $where1 = 'isuse = 1 AND (user_type = 3 OR user_type = 0)';
		$helpCenterCategory = new HelpCenterCategoryModel();
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields, $where1);
		
		foreach($helpCenterCategoryList as $key => $val)
		{
			$fields = 'help_id,title';
			$where = 'isuse=1 AND (user_type = 3 OR user_type = 0) AND help_sort_id=' . $val['help_sort_id'];
			$helpCenterCategoryList[$key]['left_help_center_menu'] = $helpCenter->getHelpList('', '', $fields, $where);
		}
		
		$this->assign('help_center_category_list', $helpCenterCategoryList);
		#echo "<pre>";
		#print_r($helpCenterCategoryList);
		#die;
		$this->assign('head_title', '帮助中心');
		$this->display();
	}

	//帮助中心详情页
	public function help_detail()
	{
		$id = intval($this->_get('id'));
		$helpCenter = new HelpCenterModel();
		if(!$helpCenter->getTotal('help_id=' . $id))
		{
			$this->alert('对不起，文章不存在！', U('/FrontHelp/help_list'));
		}

		$helpInfo = $helpCenter->getHelpInfo($id, 'title, addtime');
		#echo $helpCenter->getLastSql();
		#die;
		$this->assign('help_info', $helpInfo);
		$this->assign('help_contents', $helpCenter->getHelpContents($id));
		
		$this->assign('head_title', $helpInfo['title']);
		$this->display();
	}

	//关于潘朵拉
	public function about()
	{
		/*$generalArticle = new GeneralArticleModel();
		$help_info = $generalArticle->getArticleIdByTag('about', 'article_id, title, addtime');
		$article_txt_obj = D('article_txt');
		$article_txt = $article_txt_obj->where('article_id = ' . $help_info['article_id'])->find();
		$help_info['help_title'] = $help_info['title'];
		unset($help_info['title']);
		#echo "<pre>";
		#print_r($help_info);die;
	
		$help_info['path_img'] = str_replace('##img_domain##', C('IMG_DOMAIN') . '/Uploads', $help_info['path_img']);
		$this->assign('article_data', $help_info);
		$this->assign('help_contents', $article_txt['contents']);*/
		$helpCenter = new HelpCenterModel();
		$helpInfo = $helpCenter->getHelpIdByTag('about_us');
		$contents = $helpCenter->getHelpContents($helpInfo['help_id']);
		$this->assign('help_info', $helpInfo);
		$this->assign('help_contents', $contents);
		$this->assign('head_title', '关于我们');
		$this->display(APP_PATH . 'Tpl/FrontHelp/help_detail.html');
	}

	//检查更新
	public function update()
	{
		$this->assign('head_title', '检查更新');
		$this->display();
	}
	
	//欢迎向导页
	public function welcome()
	{
		$this->assign('head_title', '潘朵拉欢迎介绍');
		$this->display();
	}
	
	//欢迎视频页
	public function welcome_video()
	{
		$this->assign('head_title', '潘朵拉欢迎视频页');
		$this->display();
	}

	//该城市尚未开通
	function area_not_open()
	{
        header("Content-Type:text/html; charset=utf-8");
		die('抱歉，该城市尚未开通');
	}
}

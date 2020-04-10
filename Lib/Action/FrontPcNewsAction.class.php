<?php 
class FrontPcNewsAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
		$this->news_num_per_page = C('NEWS_NUM_PER_PAGE');

	}

	//系统公告列表页
	public function notice_list()
	{
		$user_id = intval(session('user_id'));
		
		$notice_obj = new NoticeModel();
        #$where = '(user_type = 3 OR user_type = 0)';
        //分页处理
        import('ORG.Util.Pagelist');
        $count = $notice_obj->getNoticeNum();
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
		$notice_obj->setStart($Page->firstRow);
        $notice_obj->setLimit($Page->listRows);
        $show = $Page->show();
		$this->assign('show', $show);
		
		$notice_list = $notice_obj->getNoticeList('','','serial ASC,addtime DESC');
		$this->assign('notice_list',$notice_list);

		$this->assign('head_title', '公告列表');
		$this->display();
	}
	
	//系统公告详情页
	public function notice_detail()
	{
		$id = intval($this->_get('id'));
		if(!$id)
		{
			header('Location: /Common/page404');
		}

		$notice_obj = new NoticeModel();
		$noticeInfo = $notice_obj->getNoticeInfo('notice_id = ' .$id, 'title, addtime');
		$this->assign('noticeInfo',$noticeInfo);
		$generalArticleContents = $notice_obj->getArticleContents($id);
		$this->assign('general_article_contents', $generalArticleContents);
		


		$this->assign('detail_id', $id);
		$this->assign('head_title', $noticeInfo['title']);
		$this->display();
	}

}

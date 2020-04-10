<?php 
class FrontPcHelpAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
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
			$this->alert('对不起，文章不存在！', U('/'));
		}

		$helpInfo = $helpCenter->getHelpInfo($id, 'title, addtime');
		#echo $helpCenter->getLastSql();
		#die;
		$this->assign('help_info', $helpInfo);
		$this->assign('help_contents', $helpCenter->getHelpContents($id));
		
		$this->assign('head_title', $helpInfo['title']);
		$this->display();
	}

}

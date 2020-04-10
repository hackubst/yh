<?php
/**
 * 帮助中心管理类
 * 
 *
 */
class AcpHelpAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}
	
	/**
     * 帮助栏目列表
     * @author 陆宇峰
     * @return void
     * @todo 从tp_help_sort表中列出数据，注意按排序号排序。
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
				$where = 'help_sort_name LIKE "%' . $keyword . '%"';
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
		}
		$rows = 15;
		$helpCenterCategory = new HelpCenterCategoryModel();
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryListPage($where, $rows);
	//	echo "<pre>";
	//	print_r($helpCenterCategoryList);die;
		
		if($helpCenterCategoryList && is_array($helpCenterCategoryList))
		{
			$pagination = array_pop($helpCenterCategoryList);
			foreach($helpCenterCategoryList as $key => $val)
			{
				$helpCenterCategoryList[$key]['no'] = ($p - 1) * $rows + $key + 1;
				$helpCenterCategoryList[$key]['help_sort_name'] = mbSubStr($val['help_sort_name'], 32);
			}
			$this->assign('pagination', $pagination);
			$this->assign('help_center_category_list', $helpCenterCategoryList);
		}
		
		$this->assign('head_title', '帮助栏目列表');
		$this->display();
	}
	
	/**
     * 帮助列表
     * @author 陆宇峰
     * @return void
     * @todo 从tp_help表列出数据，默认从左边菜单带出sort_id，可用标题、栏目搜索
     */
	public function list_help()
	{
		$act = $this->_get('act');
		$helpCenter = new HelpCenterModel();
		$_table = $helpCenter->getTableName();
		if($act == 'submit')
		{
			$keyword = $this->_get('keyword');
			$sortId  = $this->_get('sort_id');
			
			$conditions = array();
			$where = '';
			if($keyword)
			{
				$conditions[] = $_table . '.title LIKE "%' . $keyword . '%"';
			}
			if($sortId)
			{
				$conditions[] = $_table . '.help_sort_id=' . $sortId;
			}
			if(!empty($conditions))
			{
				$where = implode(' AND ', $conditions);
			}
			
			$this->assign('is_search', 1);
			$this->assign('keyword', $keyword);
			$this->assign('help_center_category_option_selected', $sortId);
		}
		
		$helpList = $helpCenter->getHelpListPage($where);
	//	echo "<pre>";
	//	print_r($helpCenterList);die;
		
		if($helpList && is_array($helpList))
		{
			$pagination = array_pop($helpList);
			foreach($helpList as $key => $val)
			{
				$helpList[$key]['title'] = mbSubStr($val['title'], 50);
			}
			$this->assign('pagination', $pagination);
			$this->assign('help_list', $helpList);
		}
		
		//分类列表
		$helpCenterCategory = new HelpCenterCategoryModel();
		$fields = 'help_sort_id,help_sort_name';
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields);
		if($helpCenterCategoryList && is_array($helpCenterCategoryList))
		{
			foreach($helpCenterCategoryList as $key => $val)
			{
				$helpCenterCategoryOptions[$val['help_sort_id']] = $val['help_sort_name'];
			}
			$this->assign('help_center_category_options', $helpCenterCategoryOptions);
		}
		
		$this->assign('head_title', '帮助列表');
		$this->display();
	}
	
	/**
     * 添加帮助
     * @author 陆宇峰
     * @return void
     * @todo 添加文章，tp_help表，isuse默认1
     * @todo 注意同步插入help_txt，tp_help_txt_photo表
     */
	public function add_help()
	{
		$sortId = $this->_get('sort_id');
		$act = $this->_post('act');
		if($act == 'submit')
		{
			$title 		     = $this->_post('title');
			$sortId 	     = $this->_post('sort_id');
			$serial 	     = $this->_post('serial');
			$isNavigatorShow = $this->_post('is_navigator_show');
			$isBottomShow 	 = $this->_post('is_bottom_show');
			$isUse 		     = $this->_post('isuse');
			$contents 	     = $this->_post('contents');
			$helpTxtImages   = $this->_post('help_txt_images');
			
			//表单验证
			if(!$title)
			{
				$this->error('请填写标题！');
			}
			if(!$sortId || !ctype_digit($sortId))
			{
				$this->error('请选择分类！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请填写数字格式排序！');
			}
			$data['title'] 			   = $title;
			$data['help_sort_id'] 	   = $sortId;
			$data['serial'] 		   = $serial;
			$data['is_navigator_show'] = $isNavigatorShow;
			$data['is_bottom_show']    = $isBottomShow;
			$data['isuse'] 			   = $isUse;
			$data['contents'] 		   = $contents;
			
			$helpCenter = new HelpCenterModel();
			if($id = $helpCenter->addHelp($data))
			{
				if($helpTxtImages && is_array($helpTxtImages))
				{
					$data = array();
					$data['help_id'] = $id;
					foreach($helpTxtImages as $key => $val)
					{
						$data['path_img'] = $val;
						$helpCenter->saveHelpPhoto($data);
					}
				}
				$this->success('恭喜您，帮助添加成功!', '/AcpHelp/list_help');
			}
			else
			{
				$this->error('对不起，帮助添加失败，请稍后重试!');
			}
		}
		//分类列表
		$helpCenterCategory = new HelpCenterCategoryModel();
		$fields = 'help_sort_id,help_sort_name';
		$where = 'isuse=1';
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields, $where);
		if($helpCenterCategoryList && is_array($helpCenterCategoryList))
		{
			foreach($helpCenterCategoryList as $key => $val)
			{
				$helpCenterCategoryOptions[$val['help_sort_id']] = $val['help_sort_name'];
			}
			$this->assign('help_center_category_options', $helpCenterCategoryOptions);
		}
		
		if($sortId)
		{
			$this->assign('sort_id', $sortId);
		}
		
		$this->assign('action_title', '帮助列表');
		$this->assign('action_src', '/AcpHelp/list_help');
		$this->assign('head_title', '添加帮助');
		$this->display();
	}
	
	/**
     * 修改帮助
     * @author 陆宇峰
     * @return void
     * @todo 修改tp_help表.注意同步修改help_text，tp_help_txt_photo表
     */
	public function edit_help()
	{
		$id = $this->_get('id');
		if(!$id || !ctype_digit($id))
		{
			$this->error('非法参数！');
		}
		$helpCenter = new HelpCenterModel();
		if(!$helpCenter->getHelpInfo($id))
		{
			$this->error('无效ID！');
		}
		$act = $this->_post('act');
		if($act == 'submit')
		{
			$title 		     = $this->_post('title');
			$sortId 	     = $this->_post('sort_id');
			$serial 	     = $this->_post('serial');
			$isNavigatorShow = $this->_post('is_navigator_show');
			$isBottomShow 	 = $this->_post('is_bottom_show');
			$isUse 		     = $this->_post('isuse');
			$contents 	     = $this->_post('contents');
			$helpTxtImages   = $this->_post('help_txt_images');
			
			//表单验证
			if(!$title)
			{
				$this->error('请填写标题！');
			}
			if(!$sortId || !ctype_digit($sortId))
			{
				$this->error('请选择分类！');
			}
			if($serial && !ctype_digit($serial))
			{
				$this->error('请填写数字格式排序！');
			}
			$data['title'] 		  	   = $title;
			$data['help_sort_id'] 	   = $sortId;
			$data['serial'] 	  	   = $serial;
			$data['is_navigator_show'] = $isNavigatorShow;
			$data['is_bottom_show']    = $isBottomShow;
			$data['isuse'] 			   = $isUse;
			$data['contents'] 	  	   = $contents;
		//	echo "<pre>";
		//	print_r($data);die;
			
			if(false !== $helpCenter->saveHelp($id, $data))
			{
				if($helpTxtImages && is_array($helpTxtImages))
				{
					$data = array();
					$data['help_id'] = $id;
					foreach($helpTxtImages as $key => $val)
					{
						$data['path_img'] = $val;
						$helpCenter->saveHelpPhoto($data);
					}
				}
				$this->success('恭喜您，帮助修改成功！', '/AcpHelp/list_help');
			}
			else
			{
				$this->error('对不起，帮助修改失败，请稍后重试！');
			}
		}
		
		$helpData = $helpCenter->getHelpById($id);
	//	echo "<pre>";
	//	print_r($helpData);die;
		
		//分类列表
		$helpCenterCategory = new HelpCenterCategoryModel();
		$fields = 'help_sort_id,help_sort_name';
		$where = 'isuse=1';
		$helpCenterCategoryList = $helpCenterCategory->getHelpCenterCategoryList('', '', $fields, $where);
		if($helpCenterCategoryList && is_array($helpCenterCategoryList))
		{
			foreach($helpCenterCategoryList as $key => $val)
			{
				$helpCenterCategoryOptions[$val['help_sort_id']] = $val['help_sort_name'];
			}
			$this->assign('help_center_category_options', $helpCenterCategoryOptions);
		}
		
		$this->assign('help_data', $helpData);
		
		$this->assign('action_title', '帮助列表');
		$this->assign('action_src', '/AcpHelp/list_help');
		$this->assign('head_title', '修改帮助');
		$this->display();
	}
}
?>
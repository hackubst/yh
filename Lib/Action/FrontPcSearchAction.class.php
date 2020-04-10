<?php 
class FrontPcSearchAction extends FrontPcAction{
	function _initialize() 
	{
		parent::_initialize();
	}

	//搜索页
	public function search()
	{
		$this->assign('head_title', '搜索页');
		$this->display();
	}

	//搜索结果页
	public function search_result()
	{
		$where = '';
		$item_name = $this->_request('item_name');
		$class_id = $this->_request('class_id');
		$sort_id = $this->_request('sort_id');
		$class_tag = $this->_request('class_tag');
		if ($class_tag)
		{
			//获取种子分类ID
			$class_obj = new ClassModel();
			$class_info = $class_obj->getClassInfo('class_tag = "' . $class_tag . '"', 'class_id');
			$class_id = $class_info ? $class_info['class_id'] : 0;
		}
		$where .= $item_name ? ' AND item_name LIKE "' . $item_name . '"' : '';
		$where .= ctype_digit($class_id) ? ' AND class_id = ' . $class_id : '';
		$where .= ctype_digit($sort_id) ? ' AND sort_id = ' . $sort_id : '';
		
		$this->assign('class_id', $class_id);
		$this->assign('sort_id', $sort_id);
		$this->assign('item_name', $item_name);

		$this->assign('head_title', '搜索结果页');
		$this->display();
	}
}

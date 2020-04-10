<?php
/**
 * 问题库
 * @author 姜伟
 *
 */
class AcpQuestionAction extends AcpAction {
	
	 /**
     * 构造函数
     * @author 姜伟
     * @return void
     * @todo
     */
	public function AcpQuestionAction()
	{
            parent::_initialize();
	}
	
	/**
	 * 获取问题列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取问题列表
	 */
	public function get_question_list()
	{
		$question_obj = new QuestionModel();
		$where = '';
		//数据总量
		$total = $question_obj->getQuestionNum($where);

		//处理分页
		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$question_obj->setStart($Page->firstRow);
        $question_obj->setLimit($Page->listRows);
		$page_str = $Page->show();
		$this->assign('page_str',$page_str);
		
		$question_list = $question_obj->getQuestionList('', $where, ' insert_time DESC');
		$question_class_obj = new QuestionClassModel();
		foreach ($question_list AS $k => $v)
		{
			$question_list[$k]['question_class'] = $question_class_obj->convertQuestionClass($v['question_class_id'], $v['question_sort_id']);
		}
		$this->assign('question_list', $question_list);
		
		$this->display();
	}

	//添加问题
	function add_question()
	{
		$act = $this->_post('act');
		if($act == 'add')
		{
			$_post = $this->_post();
			$question_title		= $_post['question_title'];
			$question_keywords	= $_post['question_keywords'];
			$question_class_id	= $_post['question_class_id'];
			$question_sort_id	= $_post['question_sort_id'];
			$insert_user_id		= intval(session('user_id'));
			$answer				= $_post['contents'];
			$isuse				= $_post['isuse'];
			
			//表单验证
			if(!$question_title)
			{
				$this->error('请填写问题标题！');
			}
			if(!$question_keywords)
			{
				$this->error('请填写问题关键词！');
			}
			if(!$question_class_id)
			{
				$this->error('请选择一级分类！');
			}
			if(!$question_sort_id)
			{
				$this->error('请选择二级分类！');
			}
			if(!$answer)
			{
				$this->error('请填写问题答案！');
			}
			if(!ctype_digit($isuse))
			{
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'question_title'		=> $question_title,
				'question_keywords'		=> $question_keywords,
				'question_class_id'		=> $question_class_id,
				'question_sort_id'		=> $question_sort_id,
				'insert_user_id'		=> $insert_user_id,
				'answer'				=> $answer,
				'isuse'					=> $isuse,
			);
			$question_obj = new QuestionModel();
			$success = $question_obj->addQuestion($arr);

			if ($success)
			{
				$this->success('恭喜您，问题添加成功！', '/AcpQuestion/get_question_list');
			}
			else
			{
				$this->error('抱歉，问题添加失败！', '/AcpQuestion/get_question_list');
			}
		}

		//获取问题一级分类列表
		$question_class_obj = new QuestionClassModel();
		$question_class_list = $question_class_obj->getQuestionClassList();
		$this->assign('question_class_list', $question_class_list);

		$this->assign('head_title', '添加问题');
		$this->display();
	}

	//修改问题
	function edit_question()
	{
		$redirect = U('/AcpQuestion/get_question_list');
		$question_id = intval($this->_get('question_id'));
		if (!$question_id)
		{
			$this->error('对不起，非法访问！', $redirect);
		}
		$question_obj = new QuestionModel();
		$question_info = $question_obj->getQuestionInfo('question_id = ' . $question_id);
		if (!$question_info)
		{
			$this->error('对不起，不存在相关问题！', $redirect);
		}

		$act = $this->_post('act');
		if($act == 'edit')
		{
			$_post = $this->_post();
			$question_title		= $_post['question_title'];
			$question_keywords	= $_post['question_keywords'];
			$question_class_id	= $_post['question_class_id'];
			$question_sort_id	= $_post['question_sort_id'];
			$insert_user_id		= intval(session('user_id'));
			$answer				= $_post['contents'];
			$isuse				= $_post['isuse'];
			
			//表单验证
			if(!$question_title)
			{
				$this->error('请填写问题标题！');
			}
			if(!$question_keywords)
			{
				$this->error('请填写问题关键词！');
			}
			if(!$question_class_id)
			{
				$this->error('请选择一级分类！');
			}
			if(!$question_sort_id)
			{
				$this->error('请选择二级分类！');
			}
			if(!$answer)
			{
				$this->error('请填写问题答案！');
			}
			if(!ctype_digit($isuse))
			{
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'question_title'		=> $question_title,
				'question_keywords'		=> $question_keywords,
				'question_class_id'		=> $question_class_id,
				'question_sort_id'		=> $question_sort_id,
				'insert_user_id'		=> $insert_user_id,
				'answer'				=> $answer,
				'isuse'					=> $isuse,
			);
			$question_obj = new QuestionModel($question_id);
			$success = $question_obj->editQuestion($arr);

			if ($success)
			{
				$this->success('恭喜您，问题修改成功！', '/AcpQuestion/edit_question/question_id/' . $question_id);
			}
			else
			{
				$this->error('抱歉，问题修改失败！', '/AcpQuestion/edit_question/question_id/' . $question_id);
			}
		}

		//获取问题一级分类列表
		$question_class_obj = new QuestionClassModel();
		$question_class_list = $question_class_obj->getQuestionClassList();
		$this->assign('question_class_list', $question_class_list);

		$this->assign('question_info', $question_info);
		$this->assign('head_title', '修改问题');
		$this->display();
	}
	
	/**
	 * 获取问题一级分类列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取问题一级分类列表
	 */
	public function get_question_class_list()
	{
		$question_class_obj = new QuestionClassModel();
		$where = '';
		//数据总量
		$total = $question_class_obj->getQuestionClassNum($where);

		//处理分页
		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$question_class_obj->setStart($Page->firstRow);
        $question_class_obj->setLimit($Page->listRows);
		$page_str = $Page->show();
		$this->assign('page_str',$page_str);
		
		$question_class_list = $question_class_obj->getQuestionClassList('', $where, ' serial ASC');
		$this->assign('question_class_list', $question_class_list);
		
		$this->display();
	}


	/**
	 * 删除问题
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 异步方法，根据问题ID删除问题
	 */
	public function delete_question()
	{
		$question_id = intval($this->_post('question_id'));

		if ($question_id)
		{
			$question_obj = new QuestionModel($question_id);
			$success = $question_obj->deleteQuestion();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 批量删除问题
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 异步方法，根据问题ID集合删除问题
	 */
	public function batch_delete_question()
	{
		$question_ids = $this->_post('question_ids');

		if ($question_ids)
		{
			$question_id_ary = explode(',', $question_ids);
			$success_num = 0;
			foreach ($question_id_ary AS $question_id)
			{
				$question_obj = new QuestionModel($question_id);
				$success_num += $question_obj->deleteQuestion();
			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 根据一级分类ID获取二级分类列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 根据一级分类ID获取二级分类列表
	 */
	public function get_question_sort_list()
	{
		$question_class_id = intval($this->_post('question_class_id'));

		if ($question_class_id)
		{
			$question_sort_obj = new QuestionSortModel();
			$question_sort_list = $question_sort_obj->getQuestionSortList('question_sort_id, sort_name', 'question_class_id = ' . $question_class_id);
			echo $question_sort_list ? json_encode($question_sort_list) : 'failure';
			exit;
		}

		exit('failure');
	}

	//添加一级分类
	function add_question_class()
	{
		$act = $this->_post('act');
		if($act == 'add')
		{
			$_post = $this->_post();
			$class_name	= $_post['class_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
			
			//表单验证
			if(!$class_name)
			{
				$this->error('请填写分类名称！');
			}
			if(!ctype_digit($serial))
			{
				$this->error('请填写排序号！');
			}
			if(!ctype_digit($isuse))
			{
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'class_name'	=> $class_name,
				'serial'		=> $serial,
				'isuse'			=> $isuse,
			);
			$question_class_obj = new QuestionClassModel();
			$success = $question_class_obj->addQuestionClass($arr);

			if ($success)
			{
				$this->success('恭喜您，分类添加成功！', '/AcpQuestion/get_question_class_list');
			}
			else
			{
				$this->error('抱歉，问题添加失败！', '/AcpQuestion/get_question_class_list');
			}
		}

		$this->assign('head_title', '添加一级分类');
		$this->display();
	}

	//修改问题
	function edit_question_class()
	{
		$redirect = U('/AcpQuestion/get_question_class_list');
		$question_class_id = intval($this->_get('question_class_id'));
		if (!$question_class_id)
		{
			$this->error('对不起，非法访问！', $redirect);
		}
		$question_class_obj = new QuestionClassModel();
		$question_class_info = $question_class_obj->getQuestionClassInfo('question_class_id = ' . $question_class_id);
		if (!$question_class_info)
		{
			$this->error('对不起，不存在相关问题！', $redirect);
		}

		$act = $this->_post('act');
		if($act == 'edit')
		{
			$_post = $this->_post();
			$class_name	= $_post['class_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
			
			//表单验证
			if(!$class_name)
			{
				$this->error('请填写分类名称！');
			}
			if(!ctype_digit($serial))
			{
				$this->error('请填写排序号！');
			}
			if(!ctype_digit($isuse))
			{
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'class_name'	=> $class_name,
				'serial'		=> $serial,
				'isuse'			=> $isuse,
			);
			$question_class_obj = new QuestionClassModel($question_class_id);
			$success = $question_class_obj->editQuestionClass($arr);

			if ($success)
			{
				$this->success('恭喜您，分类修改成功！', '/AcpQuestion/edit_question_class/question_class_id/' . $question_class_id);
			}
			else
			{
				$this->error('抱歉，分类修改失败！', '/AcpQuestion/edit_question_class/question_class_id/' . $question_class_id);
			}
		}

		$this->assign('question_class_info', $question_class_info);
		$this->assign('head_title', '修改一级分类');
		$this->display();
	}
	
	/**
	 * 删除一级分类
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 异步方法，根据问题ID删除一级分类
	 */
	public function delete_question_class()
	{
		$question_class_id = intval($this->_post('question_class_id'));

		if ($question_class_id)
		{
			$question_class_obj = new QuestionClassModel($question_class_id);
			$success = $question_class_obj->deleteQuestionClass();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 批量删除一级分类
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 异步方法，根据问题ID集合删除一级分类
	 */
	public function batch_delete_question_class()
	{
		$question_class_ids = $this->_post('question_class_ids');

		if ($question_class_ids)
		{
			$question_class_id_ary = explode(',', $question_class_ids);
			$success_num = 0;
			foreach ($question_class_id_ary AS $question_class_id)
			{
				$question_class_obj = new QuestionClassModel($question_class_id);
				$success_num += $question_class_obj->deleteQuestionClass();
			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}
	
	/**
	 * 获取问题二级分类列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取问题二级分类列表
	 */
	public function get_question_sort()
	{
		$question_sort_obj = new QuestionSortModel();
		$where = '';
		//数据总量
		$total = $question_sort_obj->getQuestionSortNum($where);

		//处理分页
		import('ORG.Util.Pagelist');
		$per_page_num = C('PER_PAGE_NUM');
		$Page = new Pagelist($total, $per_page_num);
		$question_sort_obj->setStart($Page->firstRow);
        $question_sort_obj->setLimit($Page->listRows);
		$page_str = $Page->show();
		$this->assign('page_str',$page_str);
		
		$question_sort = $question_sort_obj->getQuestionSortList('', $where, ' serial ASC');
		$this->assign('question_sort_list', $question_sort);
		
		$this->display();
	}

	//添加二级分类
	function add_question_sort()
	{
		$act = $this->_post('act');
		if($act == 'add')
		{
			$_post = $this->_post();
			$sort_name	= $_post['sort_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
			
			//表单验证
			if(!$sort_name)
			{
				$this->error('请填写分类名称！');
			}
			if(!ctype_digit($serial))
			{
				$this->error('请填写排序号！');
			}
			if(!ctype_digit($isuse))
			{
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'sort_name'		=> $sort_name,
				'serial'		=> $serial,
				'isuse'			=> $isuse,
			);
			$question_sort_obj = new QuestionSortModel();
			$success = $question_sort_obj->addQuestionSort($arr);

			if ($success)
			{
				$this->success('恭喜您，分类添加成功！', '/AcpQuestion/get_question_sort');
			}
			else
			{
				$this->error('抱歉，问题添加失败！', '/AcpQuestion/get_question_sort');
			}
		}

		$this->assign('head_title', '添加二级分类');
		$this->display();
	}

	//修改问题
	function edit_question_sort()
	{
		$redirect = U('/AcpQuestion/get_question_sort');
		$question_sort_id = intval($this->_get('question_sort_id'));
		if (!$question_sort_id)
		{
			$this->error('对不起，非法访问！', $redirect);
		}
		$question_sort_obj = new QuestionSortModel();
		$question_sort_info = $question_sort_obj->getQuestionSortInfo('question_sort_id = ' . $question_sort_id);
		if (!$question_sort_info)
		{
			$this->error('对不起，不存在相关问题！', $redirect);
		}

		$act = $this->_post('act');
		if($act == 'edit')
		{
			$_post = $this->_post();
			$sort_name	= $_post['sort_name'];
			$serial		= $_post['serial'];
			$isuse		= $_post['isuse'];
			
			//表单验证
			if(!$sort_name)
			{
				$this->error('请填写分类名称！');
			}
			if(!ctype_digit($serial))
			{
				$this->error('请填写排序号！');
			}
			if(!ctype_digit($isuse))
			{
				$this->error('请选择是否有效！');
			}

			$arr = array(
				'sort_name'		=> $sort_name,
				'serial'		=> $serial,
				'isuse'			=> $isuse,
			);
			$question_sort_obj = new QuestionSortModel($question_sort_id);
			$success = $question_sort_obj->editQuestionSort($arr);

			if ($success)
			{
				$this->success('恭喜您，分类修改成功！', '/AcpQuestion/edit_question_sort/question_sort_id/' . $question_sort_id);
			}
			else
			{
				$this->error('抱歉，分类修改失败！', '/AcpQuestion/edit_question_sort/question_sort_id/' . $question_sort_id);
			}
		}

		$this->assign('question_sort_info', $question_sort_info);
		$this->assign('head_title', '修改二级分类');
		$this->display();
	}
		
	/**
	 * 删除二级分类
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 异步方法，根据问题ID删除二级分类
	 */
	public function delete_question_sort()
	{
		$question_sort_id = intval($this->_post('question_sort_id'));

		if ($question_sort_id)
		{
			$question_sort_obj = new QuestionSortModel($question_sort_id);
			$success = $question_sort_obj->deleteQuestionSort();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 批量删除二级分类
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 异步方法，根据问题ID集合删除二级分类
	 */
	public function batch_delete_question_sort()
	{
		$question_sort_ids = $this->_post('question_sort_ids');

		if ($question_sort_ids)
		{
			$question_sort_id_ary = explode(',', $question_sort_ids);
			$success_num = 0;
			foreach ($question_sort_id_ary AS $question_sort_id)
			{
				$question_sort_obj = new QuestionSortModel($question_sort_id);
				$success_num += $question_sort_obj->deleteQuestionSort();
			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}
	

}
?>

<?php
/**
 * 帮助中心管理ajax类
 * 
 *
 */
class AcpHelpAjaxAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}
	
	/**
     * 添加帮助中心栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 向表tp_help_sort中插入一条记录
     */
	public function add_sort()
	{
		if($this->isAjax())
		{
			$sortName = $this->_get('sort_name');
			if(!$sortName)
			{
				$this->_ajaxFeedback(0, null, '请输入栏目名称！');
			}
			
			$data['article_sort_id'] = 11;
			$data['help_sort_name'] = $sortName;
			$data['isuse'] = 1;
			$helpCenterCategory = new HelpCenterCategoryModel();
			if($helpCenterCategory->addHelpCenterCategory($data))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，栏目添加成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，栏目添加失败，请稍后重试！');
			}
		}
	}
	
	/**
     * 修改帮助中心栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 更新表tp_help_sort中help_sort_id为$id的栏目信息
     */
	public function edit_sort()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$sortName = $this->_get('sort_name');
			if(!$sortName)
			{
				$this->_ajaxFeedback(0, null, '请输入栏目名称！');
			}
			
			$data['help_sort_name'] = $sortName;
			$helpCenterCategory = new HelpCenterCategoryModel();
			if(false !== $helpCenterCategory->setHelpCenterCategory($id, $data))
			{
				$sortName = $helpCenterCategory->getHelpCenterCategory($id, 'help_sort_name');
				$data['help_sort_name'] = $sortName['help_sort_name'];
				$this->_ajaxFeedback(1, $data, '恭喜您，栏目修改成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，栏目修改失败，请稍后重试！');
			}
		}
	}
	
	/**
     * 删除帮助中心栏目
	 *
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除表tp_help_sort中help_sort_id为$id的文章栏目记录，
	 * 删除前确定是否有该栏目下的文章，提示无法删除，请先调整文章栏目。
     */
	public function del_sort()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			//验证该栏目下是否有文章
			$where = 'help_sort_id=' . $id;
			$helpCenter = new HelpCenterModel();
			if($helpCenter->getHelpList('', '', '', $where))
			{
				$this->_ajaxFeedback(0, null, '对不起，该栏目下有文章，无法删除，请先调整！');
			}
			
			$helpCenterCategory = new HelpCenterCategoryModel();
			if($helpCenterCategory->deleteHelpCenterCategory($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，栏目删除成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，栏目删除失败，请稍后重试！');
			}
		}
	}
	
	/**
     * 快速修改帮助中心文章栏目序号
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改帮助中心文章栏目表中serial的序号，改完后不刷新当前页
     */
	public function edit_cat_serial()
	{
		$id = $this->_get('id');
		$serial = $this->_get('serial');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			if(!ctype_digit($serial))
			{
				$this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
			}
			
			$helpCenterCategory = new HelpCenterCategoryModel();
			if(false !== $helpCenterCategory->setHelpCenterCategory($id, array('serial' => $serial)))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
			}
		}
	}
	
	/**
     * 快速修改帮助文章栏目启用状态
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改帮助文章栏目表中isuse的值，改完后不刷新当前页
     */
	public function edit_cat_isuse()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$helpCenterCategory = new HelpCenterCategoryModel();
			$isUse = $helpCenterCategory->getHelpCenterCategoryState($id);
			$isUse = abs($isUse - 1);
			if(false !== $helpCenterCategory->setHelpCenterCategory($id, array('isuse' => $isUse)))
			{
				$data = array('isuse' => $isUse);
				if($isUse === 0)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，栏目禁用成功！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(1, $data, '恭喜您，栏目启用成功！');
				}
			}
			else
			{
				if($isUse === 0)
				{
					$this->_ajaxFeedback(0, null, '对不起，栏目禁用失败，请稍后再试！');
				}
				elseif($isUse === 1)
				{
					$this->_ajaxFeedback(0, null, '对不起，栏目启用失败，请稍后再试！');
				}
			}
		}
	}
	
	/**
     * 快速修改帮助文章序号
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 修改帮助文章表中serial的序号，改完后不刷新当前页
     */
	public function edit_serial()
	{
		$id = $this->_get('id');
		$serial = $this->_get('serial');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			if(!ctype_digit($serial))
			{
				$this->_ajaxFeedback(0, null, '请输入纯数字的排序号！');
			}
			
			$helpCenter = new HelpCenterModel();
			if(false !== $helpCenter->setSerial($id, $serial))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，排序修改成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，排序修改失败，请稍后再试！');
			}
		}
	}
	
	/**
     * 删除帮助
     * @author zhengzhen
     * @return string 返回JSON字符串
     * @todo 删除帮助相关表中help_id为$id的记录，改完后不刷新当前页
     */
	public function del_help()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$success = 0;//删除成功数
			$failure = 0;//删除失败数
			$helpCenter = new HelpCenterModel();
			if(is_array($id))
			{
				foreach($id as $key => $val)
				{
					if($helpCenter->deleteHelp($val))
					{
						$data[] = array('id' => $val, 'error' => 0);
						$success++;
					}
					else
					{
						$data[] = array('id' => $val, 'error' => 1);
						$failure++;
					}
				}
			}
			else
			{
				if($helpCenter->deleteHelp($id))
				{
					$success++;
				}
				else
				{
					$failure++;
				}
			}
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '篇帮助删除成功！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '篇帮助删除失败，请稍后重试！';
			}
			$msg = implode("<br />", $msg);
			if($success > 0)
			{
				$this->_ajaxFeedback(1, $data, $msg);
			}
			else
			{
				$this->_ajaxFeedback(0, $data, $msg);
			}
		}
	}
}
?>
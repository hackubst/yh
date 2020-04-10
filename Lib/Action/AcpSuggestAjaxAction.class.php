<?php
/**
 * 用户建议、投诉AJAX控制器
 * @access public
 * @author zhengzhen
 * @Date 2014-04-02
 */
class AcpSuggestAjaxAction extends AcpAction {
	
	/**
	 * 删除投诉
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 删除表tp_complain_log中complain_log_id为$ids的投诉记录，同时删除凭证图片（如有）
	 *
	 */
	public function del_complain()
	{
		$ids = $this->_get('ids');
		if($this->isAjax() && $ids && is_array($ids))
		{
			$success = 0;//删除成功数
			$failure = 0;//删除失败数
			$complainApply = new ComplainApplyModel();
			foreach($ids as $key => $val)
			{
				//验证ID是否为数字
				if(!ctype_digit($val))
				{
					continue;
				}
				//获取凭证图片
				$complainInfo = $complainApply->getComplainById($val, 'proof');
				
				if($complainApply->deleteComplain($val))
				{
					if($complainInfo['proof'])
					{
						//删除凭证图片
						@unlink(str_replace('##img_domain##', APP_PATH . 'Uploads', $complainInfo['proof']));
					}
					$data[] = array('id' => $val, 'error' => 0);
					$success++;
				}
				else
				{
					$data[] = array('id' => $val, 'error' => 1);
					$failure++;
				}
			}
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '条客服投诉删除成功！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '条客服投诉删除失败，请稍后重试！';
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
	
	/**
	 * 设置投诉状态
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 更新表tp_complain_log中complain_log_id为$id的complain_state为1
	 *
	 */
	public function set_handled()
	{
		$id = $this->_get('id');
		if($this->isAjax() && $id)
		{
			//验证ID是否为数字
			if(!ctype_digit($id))
			{
				$this->_ajaxFeedback(0, null, '参数无效！');
			}
			
			$complainApply = new ComplainApplyModel();
			if(false !== $complainApply->setHandled($id))
			{
				$this->_ajaxFeedback(1, null, '恭喜您，客服投诉处理状态设置成功！');
			}
			else
			{
				$this->_ajaxFeedback(0, null, '对不起，客服投诉处理状态设置失败，请稍后再试！');
			}
		}
	}
	
	/**
	 * 删除用户建议
	 *
	 * @author zhengzhen
	 * @return string 返回JSON字符串
	 * @todo 删除表tp_user_suggest中user_suggest_id为$ids的投诉记录
	 *
	 */
	public function del_suggest()
	{
		$ids = $this->_get('ids');
		if($this->isAjax() && $ids && is_array($ids))
		{
			$success = 0;//删除成功数
			$failure = 0;//删除失败数
			$userSuggest = new UserSuggestModel();
			foreach($ids as $key => $val)
			{
				//验证ID是否为数字
				if(!ctype_digit($val))
				{
					continue;
				}
				
				if($userSuggest->deleteUserSuggest($val))
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
			
			if($success > 0)
			{
				$msg[] =  '恭喜您，' . $success . '条用户建议删除成功！';
			}
			if($failure > 0)
			{
				$msg[] = '对不起，' . $failure . '条用户建议删除失败，请稍后重试！';
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
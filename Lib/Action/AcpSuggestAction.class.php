<?php
/**
 * 给点建议表
 * 
 *
 */
class AcpSuggestAction extends AcpAction {
	public function _initialize()
	{
		parent::_initialize();
	}
	
	/**
     * 提交给点建议
     * @author 陆宇峰
     * @return void
     * @todo 提交资料到tp_user_suggest表，未来需要用接口写入AZ
     */
	public function add_suggest()
	{
		$act = $this->_post('act');
		
		if($act == 'submit')
		{
			$message = $this->_post('message');
			$vcode = $this->_post('vcode');
			
			if(!$message)
			{
				$this->error('请输入您的建议！');
			}
			if(!$vcode)
			{
				$this->error('请输入验证码！');
			}
			elseif(session('verify') != md5(strtoupper($vcode)))
			{
				$this->error('对不起，您输入的验证码错误！');
			}
			
			$data = array(
				'message' => $message,
				'addtime' => time()
			);
			$userSuggest = new UserSuggestModel();
			if($userSuggest->addAdvice($data))
			{
				$this->success('恭喜您，您的建议提交成功！', '/Acp');
			}
			else
			{
				$this->error('对不起，您的建议提交失败，请稍后重试！');
			}
		}
		
		$this->assign('head_title', '添加给点建议');
		$this->display();
	}
	
	/**
     * 提交售后问题
     * @author 陆宇峰
     * @return void
     * @todo 提交资料到tp_user_suggest表，未来需要用接口写入AZ
     */
	public function add_service()
	{
		$act = $this->_post('act');
		
		if($act == 'submit')
		{
			$message = $this->_post('message');
			$vcode = $this->_post('vcode');
			
			if(!$message)
			{
				$this->error('请输入您的问题！');
			}
			if(!$vcode)
			{
				$this->error('请输入验证码！');
			}
			elseif(session('verify') != md5(strtoupper($vcode)))
			{
				$this->error('对不起，您输入的验证码错误！');
			}
			
			$data = array(
				'message' => $message,
				'addtime' => time()
			);
			$userSuggest = new UserSuggestModel();
			if($userSuggest->addService($data))
			{
				//销毁当前验证码
				session('verify', null);
				$this->success('恭喜您，您的问题提交成功！', '/Acp');
			}
			else
			{
				$this->error('对不起，您的问题提交失败，请稍后重试！');
			}
		}
		
		$this->assign('head_title', '添加售后问题');
		$this->display();
	}
	
	/**
     * 给点建议的官方回复（暂时不做）
     * @author 陆宇峰
     * @return void
     * @todo 通过AZ的接口，把当前某条建议的官方回复取出来
     */
	public function get_suggest_reply()
	{
		$this->assign('head_title', '给点建议官方回复');
		$this->display();
	}
	
	/**
     * 售后服务的官方回复（暂时不做）
     * @author 陆宇峰
     * @return void
     * @todo 通过AZ的接口，把当前某条售后服务的官方回复取出来
     */
	public function get_service_reply()
	{
		$this->assign('head_title', '售后服务官方回复');
		$this->display();
	}
	
	/**
     * 客服投诉列表
     * @author 陆宇峰
     * @return void
     * @todo 从complain_log表取出数据
     */
	public function list_complain()
	{
		$complainApply = new ComplainApplyModel();
		$complainList = $complainApply->getAllComplainListPage();
	//	echo "<pre>";
	//	print_r($complainList);die;
		
		if($complainList && is_array($complainList))
		{
			$pagination = array_pop($complainList);
			$limit = 25;//限制在列表中显示25个字符
			foreach($complainList as $key => $val)
			{
				$complainList[$key]['contents'] = mbSubStr($val['contents'], $limit);
				$complainList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			$this->assign('pagination', $pagination);
			$this->assign('complain_list', $complainList);
		}
		
		$this->assign('head_title', '客服投诉列表');
		$this->display();
	}
	
	/**
     * 查看对客服的投诉
     * @author zhengzhen
     * @return void
     * @todo 从complain_log表取出数据
     */
	public function complain_detail()
	{
		$id = $this->_get('id');
		if(!ctype_digit($id))
		{
			$this->error('非法参数！');
		}
		
		$complainApply = new ComplainApplyModel();
		$customerServiceOnline = new CustomerServiceOnlineModel();
		$complainInfo = $complainApply->getComplainById($id);
		if($complainInfo)
		{
			$this->assign('complain_info', $complainInfo);
		}
		
		$this->assign('action_title', '客服投诉列表');
		$this->assign('action_src', '/AcpSuggest/list_complain');
		$this->assign('head_title', '客服投诉详情');
		$this->display();
	}
	
	/**
     * 用户建议列表
     * @author zhengzhen
     * @return void
     * @todo 从user_suggest表取出数据
     */
	public function list_suggest()
	{
		$userSuggest = new UserSuggestModel();
		$suggestList = $userSuggest->getAdviceListPage();
	//	echo "<pre>";
	//	print_r($suggestList);die;
		
		if($suggestList && is_array($suggestList))
		{
			$pagination = array_pop($suggestList);
			$limit = 50;//限制在列表中显示25个字符
			foreach($suggestList as $key => $val)
			{
				$suggestList[$key]['message'] = mbSubStr($val['message'], $limit);
				$suggestList[$key]['addtime'] = date('Y-m-d H:i:s', $val['addtime']);
			}
			$this->assign('pagination', $pagination);
			$this->assign('suggest_list', $suggestList);
		}
		
		$this->assign('head_title', '用户建议列表');
		$this->display();
	}
	
	/**
     * 查看用户建议
     * @author zhengzhen
     * @return void
     * @todo 从user_suggest表取出数据
     */
	public function suggest_detail()
	{
		$id = $this->_get('id');
		if(!ctype_digit($id))
		{
			$this->error('非法参数！');
		}
		
		$userSuggest = new UserSuggestModel();
		$suggestInfo = $userSuggest->getUserSuggestById($id);
	//	echo "<pre>";
	//	print_r($suggestInfo);die;
		
		if($suggestInfo)
		{
			$suggestInfo['addtime'] = date('Y-m-d H:i:s', $suggestInfo['addtime']);
			$this->assign('suggest_info', $suggestInfo);
		}
		
		$this->assign('action_title', '用户建议列表');
		$this->assign('action_src', '/AcpSuggest/list_suggest');
		$this->assign('head_title', '用户建议详情');
		$this->display();
	}
}
?>

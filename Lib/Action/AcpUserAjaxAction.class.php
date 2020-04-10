<?php
class AcpUserAjaxAction extends AcpAction {


	/**
         * type AJAX
         * @access public
         * @todo  ajax异步请求处理代销审核申请
         * 
         */
	public function doAgentApply()
	{
		$i    = $this->_post('i');          //代销申请的ID号
		$u    = $this->_post('u');          //用户ID
		$data = $this->_post('data');       //处理类型,该参数无值时表示是拒绝本次申请
		$reason = $this->_post('reason');       //拒绝的理由

		require_once('Lib/Model/AgentModel.class.php');
		$AgentModel = new AgentModel();
		if(!$data)          //没有该参数表示是拒绝本次申请
		{
			$AgentModel->doUserAgentApply($i,2);    //更新申请表的信息
			$AgentModel->setAuditPassed($u);
			$reason = rtrim(ltrim($reason));
			if($reason && $reason !='')
			{
				//发站内消息
				require_once('Lib/Model/MessageBaseModel.class.php');
				$MessageBaseModel = new MessageBaseModel();
				$MessageBaseModel->addMessage(0, $u, '代销申请被决绝', $reason);
			}
			exit(json_encode(array('type'=>1,'message'=>'恭喜您，操作成功！')));
		}
		else                //否则表示是同意本次申请
		{
			if($AgentModel->doUserAgentApply($i,1) && $AgentModel->setAuditPassed($u, 1))
			{
				exit(json_encode(array('type'=>1,'message'=>'恭喜您，操作成功！')));
			}
			else
			{
				exit(json_encode(array('type'=>2,'message'=>'对不起，操作失败！')));
			}
		}
	}


	/**
	 * type AJAX
	 * @access public
	 * @todo  ajax异步请求批量发送短信
	 *
	 */
	public function sendSms()
	{
		$submit = $this->_post('submit');
		$users = $this->_post('users');
		if(!$users)
		{
			die('没有用户');
		}
		require_once('Lib/Model/UserModel.class.php');
		$UserModel = new UserModel();
		if($submit == 'submit')         //提交发送操作
		{
			$sms_content = $this->_post('sms_content');         //短信内容
			$phonestr    = $this->_post('phonestr');            //确定要发送的用户
			if(!$sms_content)
			{
				exit(json_encode(array('type'=>2,'message'=>'对不起，短信内容不能为空')));
			}
			if(!$phonestr)
			{
				exit(json_encode(array('type'=>2,'message'=>'对不起，短信没有接受者！')));
			}
			//                 $phone_arr = explode(',',$phonestr);
			//                 //引入短信模型
			//                 require_once('Lib/Model/SMSModel.class.php');
			//                 $SMSModel = new SMSModel();
			$result = sendSMS($phonestr, $sms_content);     //发送短信
			
			if($result['status'])	       //是整数表示发送成功
			{
				exit(json_encode(array('type'=>2,'message'=>'恭喜您！成功的发送了'.$result['total_send'].'条短信！')));
			}
// 			else if($result == 'NEED_MORE_TOTAL')
// 			{
// 				exit(json_encode(array('type'=>2,'message'=>'对不起，短信余额不足，您需要付款购买短信服务！')));
// 			}
// 			else if($result == 'SMS_NEED_OPEN')
// 			{
// 				exit(json_encode(array('type'=>2,'message'=>'对不起，您当前关闭了sms服务！')));
// 			}
			else
			{
				exit(json_encode(array('type'=>2,'message'=>'对不起，短信发送失败!')));
			}
		}
		else                    //获取用户信息，并生成html页面
		{
			$users_phone = $UserModel->getUserFieldsByUsers($users,'mobile,linkman');
			$temp_arr = array();
			foreach($users_phone as $k=>$v){
				if(!$v['mobile'])
				{
					unset($users_phone[$k]);
				}
				else
				{
					$temp_arr[] = array('mobile'=>$v['mobile'],'linkman'=>$v['linkman']);
				}
			}
			$this->assign('phone_list',$temp_arr);
			$this->display('AcpUser:sendsms');
		}
	}
	
	/**
	 * 用户禁用/激活
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 
	 */
	function set_user_is_enable()
	{
		$user_id = intval($this->_post('user_id'));
		$is_enable = $this->_post('is_enable');
		if ($user_id && ctype_digit($is_enable))
		{
			$user_obj = new UserModel($user_id);
			$arr = array(
				'is_enable'	=> $is_enable
			);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 设置为代理商
	 * @author yzp
	 * @param void
	 * @return void
	 * @todo 
	 */
	function set_to_agent()
	{
		$user_id = intval($this->_post('user_id'));
		if ($user_id)
		{
			$user_obj = new UserModel($user_id);
			$mobile = $user_obj->where('user_id ='.$user_id)->getField('mobile');
			$arr = array(
				'role_type'	=> 4,
                'username' => $mobile,
			);
			$user_obj->setUserInfo($arr);
			$success = $user_obj->saveUserInfo();
			echo $success ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}

	/**
	 * 用户批量激活/禁用
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 
	 */
	function batch_set_user_is_enable()
	{
		$user_ids = $this->_post('user_ids');
		$is_enable = $this->_post('is_enable');
		if ($user_ids && ctype_digit($is_enable))
		{
			$user_id_ary = explode(',', $user_ids);
			$success_num = 0;
			foreach ($user_id_ary AS $user_id)
			{
				$user_obj = new UserModel($user_id);
				$arr = array(
					'is_enable'	=> $is_enable
				);
				$user_obj->setUserInfo($arr);
				$success = $user_obj->saveUserInfo();
				$success_num += $success ? 1 : 0;
			}
			echo $success_num ? 'success' : 'failure';
			exit;
		}

		exit('failure');
	}


	//设置用户意见状态
	public function set_user_suggest_state(){
		$user_suggest_id = I('user_suggest_id');
		$state = I('state');
		if(!is_numeric($user_suggest_id) || !is_numeric($state)){
			exit('failure');
		}
		$user_suggest_obj = new UserSuggestModel();
		if($user_suggest_obj->saveAdvice($user_suggest_id, array('state'=>$state))){
			exit('success');
		}
		exit('failure');
	}

	//批量设置用户意见状态
	public function batch_set_user_suggest_state(){
		$user_suggest_ids = I('user_suggest_ids');
		$id_arr = explode(',', $user_suggest_ids);
		$state = I('state');
		if(!is_array($id_arr) || !is_numeric($state)){
			exit('failure');
		}
		$user_suggest_obj = new UserSuggestModel();
		$success = 0;
		foreach ($id_arr as $key => $value) {
			if($user_suggest_obj->saveAdvice($value, array('state'=>$state))){
				$success++;
			}
		}
		if($success){
			exit('success');
		}
		exit('failure');
	}
}
?>

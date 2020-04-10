<?php
/**
 * 站内消息类
 * @author zhoutao@360shop.cc zhoutao0928@sina.com
 *
 */
class AcpMessageAction extends AcpAction {
	
	
	 /**
     * 构造函数
     * @author 陆宇峰
     * @return void
     * @todo
     */
	public function AcpMessageAction()
	{
            parent::_initialize();
	}
	
	/**
	 * 获取消息列表
	 * @author 姜伟
	 * @param void
	 * @return void
	 * @todo 获取消息列表
	 */
	public function get_message_list()
	{
		$eventlog = M('eventlog');
		$where = 'MsgType = "text"';
		//数据总量
		$total = $eventlog->where($where)->count();

		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$per_page_num = 3000;
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);
		
		$message_list = $eventlog->field('keyword, CreateTime, send_ids')->where($where)->order('CreateTime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
		$file = fopen('message.txt', 'a');
		foreach ($message_list AS $k => $v)
		{
			fwrite($file, $v['keyword'] . "\n");
		}
		fclose($file);
		$this->assign('message_list', $message_list);
		
		$this->display();
	}

	/**
     * 
     * @return void
     * @todo tp_message表中，message_type=2的消息
     */
	public function list_message()
        {
            require_once('Lib/Model/MessageBaseModel.class.php');
            $MessageBaseModel = new MessageBaseModel();
            
            $where = '';
            $total = $MessageBaseModel->countAllMessageList($where);            //符合条件的总消息数
            //处理分页
            import('ORG.Util.Pagelist');                        // 导入分页类
            $per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
            $Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
            //获取查询参数
            $map['mod_id'] = 2;
            
            foreach($map as $k=>$v){
    			$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
            }
            $page_str = $Page->show();                      //分页输出
            $this->assign('page_str',$page_str);
            
            $MessageBaseModel->setStart($Page->firstRow);
            $MessageBaseModel->setLimit($Page->listRows);
            $r = $MessageBaseModel->getAllMessageList($where);
            foreach($r as $k=>$v)
            {
                $r[$k]['addtime']  = date('Y-m-d H:i:s',$v['addtime']);
                $reply = $MessageBaseModel->getReplyInfoByMessageId($v['message_id']);
                $r[$k]['replynum'] = empty($reply)?0:count($reply);
            }
            $this->assign('messagelist',$r);
            $this->assign('head_title','查看所有站内信');
            $this->display();
        }
	
        
    /**
     * 回复消息
     * @return void
     * @todo 获取表单提交的消息信息，组成数组后，调用消息模型保存到数据库
     */
	public function reply_message()
	{
		//菜单光标处理
		$path_info = explode('/', $_SERVER['HTTP_REFERER']);
		if (isset($path_info[4]))
		{
			$menu_no = $this->get_in_menu(2, $path_info[4]);
			$this->assign("menu_no", $menu_no);
		}

            $redirect = $this->_request('redirect');
            $redirect = ($redirect)?url_jiemi($redirect):'/AcpUser/list_message/mod_id/2';
            $submit = $this->_post('submit');
            
            if($submit == 'submit')                 //提交回复时
            {
                $main_message_id = $this->_post('main_message');    //原始消息ID
                if($main_message_id)
                {
                    $main_message_id = url_jiemi($main_message_id);
                    if(!$main_message_id)
                    {
                        $this->error('对不起，参数有错误！', $redirect);           //参数错误
                    }
                    else
                    {
                        require_once('Lib/Model/MessageBaseModel.class.php');
                        $MessageBaseModel = new MessageBaseModel();
                        $r = $MessageBaseModel->getMessageInfoById($main_message_id,0,FALSE);   //查询消息
                        if(!$r)
                        {
                            $this->error('对不起，原消息不存在！', $redirect);           //参数错误
                        }
                        else
                        {
                            $top_send_user_id  = $r['send_user_id'];          //原始消息是谁发送的
                            $top_reply_user_id = $r['reply_user_id'];         //原始消息是发给谁的
                            if($top_send_user_id == 0)       //如果原始消息是管理员户发送的
                            {
                                $now_reply_user_id = $top_reply_user_id;    //那么本条消息的接受者与原消息一致
                            }
                            else if($top_reply_user_id == 0) //如果原始消息是发送给管理员的
                            {
                                $now_reply_user_id = $top_send_user_id;     //那么本条回复消息的接受者则是原消息的发送者
                            }
                            else
                            {
                                $this->error('对不起，您不能回复本消息！', $redirect);           //参数错误
                            }
                            $reply_contents = $this->_post('reply_contents');
                            if(!$reply_contents)
                            {
                                $this->error('对不起，您不能回复空消息！', $redirect);           //参数错误
                            }
                            
                            $r = $MessageBaseModel->addMessage(0, $now_reply_user_id, '', $reply_contents, 2, $main_message_id);
                            if($r == -1)
                            {
                                $this->error('对不起，回复失败！', $redirect);           //参数错误
                            }
                            else
                            {
                                $this->success('恭喜您，回复成功！', $redirect);           //参数错误
                            }
                        }
                            
                    }
                    
                }
            }
            else
            {
            
                $mid = $this->_request('message');      //消息id(加密的)
                if(!$mid)
                {
                    $this->error('参数错误', $redirect);
                }
                $message_id = url_jiemi($mid);
                
                require_once('Lib/Model/MessageBaseModel.class.php');
                $MessageBaseModel = new MessageBaseModel();
                $r = $MessageBaseModel->getMessageInfoById($message_id,0);
                if(!$r)
                {
                    $this->error('对不起,消息不存在！', $redirect);
                }
                
                $userinfo = array();        //存放本条消息相关的两个人的数据
                require_once('Lib/Model/UserModel.class.php');
                
                $top_send_user   = $r['message_info']['send_user_id'];            //原始消息的发送者ID
                $UserModel_send  = new UserModel($top_send_user);                       //原始消息的发送者信息
                $userinfo[$top_send_user]  = $UserModel_send->getUserInfo('user_id,username,realname,linkman');
                
                $top_reply_user  = $r['message_info']['reply_user_id'];           //原始消息的接受者ID
                $UserModel_reply = new UserModel($top_reply_user);                      //原始消息的接受者信息
                $userinfo[$top_reply_user] = $UserModel_reply->getUserinfo('user_id,username,realname,linkman');
                
                $r['message_info']['send_username']  = $userinfo[$top_send_user]['realname'];          //重新赋值，避免用户在更改某些信息后，消息显的不真实
                $r['message_info']['reply_username'] = $userinfo[$top_reply_user]['realname'];         //同上
                $r['message_info']['addtime'] = date('Y-m-d H:i:s',$r['message_info']['addtime']);
                if($r['reply_info'] && !empty($r['reply_info']))   //如果该消息有回复
                {
                    foreach($r['reply_info'] as $k=>$v)         //则循环处理这些回复信息
                    {
                        $r['reply_info'][$k]['r_send_username']  = $userinfo[$v['send_user_id']]['realname'];    //同样重新赋值
                        $r['reply_info'][$k]['r_reply_username'] = $userinfo[$v['reply_user_id']]['realname'];
                        $r['reply_info'][$k]['addtime']        = date('Y-m-d H:i:s',$v['addtime']);
                        if($v['reply_user_id'] == 0)      //该消息是发送给管理员的
                        {
                            if($r['reply_info'][$k]['is_read'] == 0 || !$r['reply_info'][$k]['is_read'])  //如果该条回复管理员尚未查看过
                            {
                                $MessageBaseModel->setMessageReaded(0,$r['reply_info'][$k]['message_id']);   //将消息状态设为已读
                            }
                        }
                    }
                }   
                if($r['message_info']['is_read'] == 0 || !$r['message_info']['is_read'])            //如果原消息还未读取过
                {
                    if($r['message_info']['reply_user_id'] == 0)       //如果该消息是发送给ACP管理员的
                    {
                        $MessageBaseModel->setMessageReaded(0,$r['message_info']['message_id']);   //将消息状态设为已读
                    }
                }
                
                $this->assign('messageinfo',$r);
            //   myprint($r);
                $this->assign('head_title','回复站内信');
                $this->display();
            }
      }
	
        
	/**
	 * type AJAX
	 * @access public
	 * @todo  ajax异步请求批量发送站内信
	 * 
	 */
	public function sendMessage()
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
			$message_title = $this->_post('messagetitle');      //消息标题
			$message_content = $this->_post('messagecontent');  //消息内容
			$usersinfo_str = $this->_post('usersinfo_str');     //最终确定要发送的用户
			if(!$message_title)
			{
				exit(json_encode(array('type'=>2,'message'=>'消息没有标题！')));
			}
			if(!$message_content)
			{
				exit(json_encode(array('type'=>2,'message'=>'消息内容不能为空！')));
			}
			if(!$usersinfo_str)
			{
				exit(json_encode(array('type'=>2,'message'=>'消息接受者为空！')));
			}
			
			require_once('Lib/Model/MessageBaseModel.class.php');
			$MessageBaseModel = new MessageBaseModel();
			$send_user_id = $_SESSION['user_info']['user_id'];
			$reply_user = explode(',', $usersinfo_str);
			if(count($reply_user) > 1)
			{
				$message_type = 3;
			}
			else
			{
				$message_type = 1;
			}
			$success = 0;       //记录发送成功的次数
			$false = 0;         //记录发送失败的次数
			foreach($reply_user as $k=>$v)
			{
				if($MessageBaseModel->addMessage(0, $v, $message_title, $message_content, $message_type) != -1)
				{
					$success++;
				}
				else
				{
					$false++;
				}
			}
			$message = '成功发送站内消息'.$success.'条';
			$message .= $false?'。失败'.$false.'条！':'';
			exit(json_encode(array('type'=>2,'message'=>$message)));
		}
		else                    //获取用户信息，并生成html页面
		{
			$users_info = $UserModel->getUserFieldsByUsers($users,'user_id,realname,linkman');
			$this->assign('users_info',$users_info);
			$this->display();
		}
	}
	

	
	/**
	 * @access public
	 * @todo  查看待回复的站内信(与管理员有关的)
	 */
	public function waiting_reply_message()
	{
		require_once('Lib/Model/MessageBaseModel.class.php');
		$MessageBaseModel = new MessageBaseModel();
		
		$message_r = $MessageBaseModel->countAllMessageNeedReply(false);
		$ids = '';
		$total = 0;
		foreach ($message_r as $key => $value) {
			if($value)
			{
				$ids .= $value . ',';
				$total++;
			}
		}
		//$message_ids = implode(',',$message_r);
		$ids = substr($ids, 0 ,-1);
		$where = 'message_id IN('.$ids.')';
		//$total = count($message_r);
		
		//处理分页
		import('ORG.Util.Pagelist');                        // 导入分页类
		$per_page_num = C('PER_PAGE_NUM');              //分页 每页显示条数
		$Page = new Pagelist($total, $per_page_num);        // 实例化分页类 传入总记录数和每页显示的记录数
		//获取查询参数
		$map['mod_id'] = 2;
		
		foreach($map as $k=>$v){
		$Page->parameter.= "$k=".$v.'&';	//为分页添加搜索条件
		}
		$page_str = $Page->show();                      //分页输出
		$this->assign('page_str',$page_str);
		
		$MessageBaseModel->setStart($Page->firstRow);
		$MessageBaseModel->setLimit($Page->listRows);
		$r = $MessageBaseModel->getAllMessageNeedReply($where);
		//dump($MessageBaseModel->_sql());
		foreach($r as $k=>$v)
		{
			$r[$k]['addtime']  = date('Y-m-d H:i:s',$v['addtime']);
			$reply = $MessageBaseModel->getReplyInfoByMessageId($v['message_id']);
			$r[$k]['replynum'] = empty($reply)?0:count($reply);
		}
		$this->assign('messagelist',$r);
		$this->assign('head_title','待回复的站内信');
		$this->display();
	}
	
	
	
	/**
	 * type AJAX
	 * @access public
	 * @todo ajax异步请求批量删除消息
	 * 
	 */
	public function delMessages()
	{
		$message_ids = $this->_post('mstr');
		$do = $this->_post('do');
		if(!$message_ids || !$do || $do != 'del')
		{
			exit(json_encode(array('type'=>2,'message'=>'对不起,权限不足！')));
		}
		require_once('Lib/Model/MessageBaseModel.class.php');
		$MessageBaseModel = new MessageBaseModel();
		$message_arr = explode(',',$message_ids);      //数组，要删除的消息ID
		if(empty($message_arr))
		{
			exit(json_encode(array('type'=>2,'message'=>'对不起！请选择要删除的消息！')));
		}
		
		if(count($message_arr) > 1)     //批量删除
		{
			if($MessageBaseModel->deleteMessage($message_arr))
			{
				exit(json_encode(array('type'=>1,'message'=>'恭喜您！删除成功！')));
			}
		}
		else        //删除单条
		{
			if($MessageBaseModel->deleteMessage($message_ids))
			{
				exit(json_encode(array('type'=>1,'message'=>'恭喜您！删除成功！')));
			}
		}
		exit(json_encode(array('type'=>2,'message'=>'对不起！删除失败！')));
		  
	}
}
?>

<?php
class AcpAppPushAction extends AcpAction{

	//APP通知推送列表
	public function push_list(){
		$push_obj = new AppPushLogModel();

		//分页处理
        import('ORG.Util.Pagelist');
        $count = $push_obj->getAppPushLogNum();
        $Page = new Pagelist($count,C('PER_PAGE_NUM'));
        $push_obj->setStart($Page->firstRow);
        $push_obj->setLimit($Page->listRows);
        $show = $Page->show();
        $this->assign('show', $show);

        $push_list = $push_obj->getAppPushLogList('title, addtime');
        $this->assign('push_list', $push_list);
		$this->assign('head_title', '通知列表');
		$this->display();
	}

	//app推送
	public function app_push(){
		if(IS_AJAX && IS_POST){
			//$title = I('title');
			$content = $this->_post('content');
			/*if(!$title){
				$this->ajaxReturn(array('msg'=>'请填写标题'));
			}*/
			if(!$content){
				$this->ajaxReturn(array('msg'=>'请填写推送内容'));
			}
			$content = htmlspecialchars_decode($content);
			$title = filterAndSubstr($content);
			$jpush_obj = new PushModel();
			//$receive = 'all'; 
			$result = $jpush_obj->jpush_all($title, 'html', $content);
			if($result){
		        $res_arr = json_decode($result, true);
		        if(isset($res_arr['error'])){                       //如果返回了error则证明失败
		            //echo $res_arr['error']['message'];          //错误信息
		            //echo $res_arr['error']['code'];             //错误码
		            //return false;  
		            $this->ajaxReturn(array('message'=> $res_arr['error']['message'].'，code：'.$res_arr['error']['code']));     
		        }else{

		            //处理成功的推送......
		           // echo '推送成功.....';
		            //return true;
		            $push_log_obj = new AppPushLogModel();
		            $arr = array(
		            	'title' => $title,
		            	'content' => $content,
		            	'addtime' => time(),
		            	'ip' => get_client_ip(),
		            	'operater' => session('user_id')
		            	);
		            $push_log_obj->addAppPushLog($arr);
		            $this->ajaxReturn(array('message'=>'推送成功'));
		        }
		    }else{      //接口调用失败或无响应
		        //echo '接口调用失败或无响应';
		        //return false;
		        $this->ajaxReturn(array('message'=>'接口调用失败或无响应'));
		    }
			
		}

		$this->assign('head_title', 'APP通知推送');
		$this->display();
	}
}


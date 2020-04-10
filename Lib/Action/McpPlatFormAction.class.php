<?php
// mcp平台管理相关操作
class McpPlatFormAction extends FrontAction {

	/**
     * 菜单和界面设置
     */
	public function mg_platform() 
	{
		$az_id          = $GLOBALS['install_info']['az_id'];
		$industry_id    = $GLOBALS['install_info']['industry_id'];

		$custom    		= M('custom');
		$industry_custom= M('industry_custom');
		$message   		= M('message');
		$cust_page 		= M('cust_page');
		$template_page 	= M('template_page');

		//获取商户的自定义菜单
		$tag         = 0;
		$custom_info = array();
		$custom_row  = $custom->where('az_id = ' . $az_id . ' AND p_id = 1')
							->field('id, p_id, name, custom_value as value, custom_type as type, obj_name, page_name, template_page_id')
							->order('serial')
							->limit(3)
							->select();
	//P($custom_row);die;								
		//判断用户是否设置过菜单,没有就获取默认的菜单
		if(!is_array($custom_row))
		{
			$tag        = 1;
			$custom_row = $industry_custom->where('industry_id = ' . $industry_id . ' AND p_id = 1')
								->field('id, p_id, name, custom_value as value, custom_type as type, obj_name, page_name, template_page_id')
								->order('serial')
								->limit(3)
								->select();
			
			if(!is_array($custom_row))
			{
				$custom_row = $industry_custom->where('industry_id = 0 AND p_id = 1')
								->field('id, p_id, name, custom_value as value, custom_type as type, obj_name, page_name, template_page_id')
								->order('serial')
								->select();
			}
		}

		$custom_info = $custom_row;
		$i = 0;
		foreach ($custom_info as $value)
		{
			$custom_info[$i]['data_name']          = '';
			$custom_info[$i]['template_page_name'] = '';
			switch ($value['type'])
			{
				case 'link': 		
					$custom_info[$i]['type'] = 1; 
					$custom_info[$i]['data_name'] = $value['value'];	
					///获取当前模板名称
					$custom_info[$i]['template_page_name'] = $template_page->where('id = ' . $value['template_page_id'])->getfield('page_name');		
					break;
				case 'tel': 		
					$custom_info[$i]['type'] = 2;
					
					//电话号码
					$custom_info[$i]['data_name'] = $value['value'];		
					break;
				case 'out': 		
					$custom_info[$i]['type'] = 3; 
				
					//获取消息id的名称
					$custom_info[$i]['data_name'] = $message->where('id = ' . $value['value'])->getfield('title');
					break;
				case 'cust_link': 	
					$custom_info[$i]['type'] = 4; 
						
					//获取自定义模版名称
					$custom_info[$i]['data_name'] = $cust_page->where('id = ' . $value['data_id'])->getfield('page_name');
					break;
				default: break;
			}
			
			if(!$tag)
			{
				$sort_row = $custom->where('p_id = ' . $value['id'])
									->field('id, p_id, name, custom_value as value, custom_type as type, obj_name, page_name, template_page_id')
									->order('serial')
									->select();
			}
			else 
			{
				$sort_row = $industry_custom->where('p_id = ' . $value['id'])
									->field('id, p_id, name, custom_value as value, custom_type as type, obj_name, page_name, template_page_id')
									->order('serial')
									->select();
			}
			
			$s = 0;
			foreach ($sort_row as  $value_b)
			{
				$sort_row[$s]['template_page_name'] = '';
				$sort_row[$s]['data_name']          = '';
				switch ($value_b['type'])
				{
					case 'link': 		
						$sort_row[$s]['type'] = 1; 
						$sort_row[$s]['data_name'] = $value_b['value'];	
						///获取当前模板名称
						$sort_row[$s]['template_page_name'] = $template_page->where('id = ' . $value_b['template_page_id'])->getfield('page_name');	
						break;
					case 'tel': 		
						$sort_row[$s]['type'] = 2;
						$sort_row[$s]['data_name'] = $value_b['value'];	
						break;
					case 'out': 		
						$sort_row[$s]['type'] = 3; 
						//获取消息id的名称
						$sort_row[$s]['data_name'] = $message->where('id = ' . $value_b['value'])->getfield('title');
						break;
					case 'cust_link': 	
						$sort_row[$s]['type'] = 4; 
						//获取自定义模版名称
						preg_match('/\d+/',$value_b['value'],$data_id);
						$sort_row[$s]['data_name'] = $cust_page->where('id = ' . $data_id[0])->getfield('page_name');
						break;
					default: break;
				}
				
				$s++;
			}
			$custom_info[$i]['sort'] =  $sort_row;
			$i++;
		}
		$this->assign('custom_info', $custom_info);
		$this->assign('menu_length', count($custom_info));
#echo'<pre>';
#P($custom_info);				
#die;
		$select_type = array(
			'1' => '跳转网址',
			'2' => '电话号码',
			'3' => '自动回复',
			'4' => '自定义页面',
		);
		$this->assign('select_type', $select_type);
		
		//TITLE中的页面标题
		$this->assign('head_title', '菜单和界面设置');
		$this->display();
	}

	/**
	* 保存菜单设置
	*/
	public function save_platform()
	{
		$az_id         = $GLOBALS['install_info']['az_id'];
		$platform_info = $this->_post('platform_info');

		//因为tp框架接收json会把数据过滤了所以要把json数据反过滤一下
		$platform_info = html_entity_decode($platform_info);
		$platform_info = str_replace('\\', '', $platform_info);
		$platform_arr  = json_decode($platform_info, true);

		$custom        = M('custom');
		//删除可与原有的菜单
		$custom->where('az_id = ' . $az_id)->delete();
//print_r($platform_arr);die();
		//存入新菜单数据
		$new_platform_arr_a = array();
		$new_platform_arr_b = array();
		
		if(is_array($platform_arr))
		{
			$i = 0;
			foreach ($platform_arr as $key => $value)
			{
				$new_platform_arr_a['az_id'] = $az_id;
			    $new_platform_arr_a['p_id']  = 1;
				$new_platform_arr_a['name']  = $value['name'];
				$new_platform_arr_a['custom_value'] = $value['value'];
				$new_platform_arr_a['custom_type'] 	= $value['type'];
				$new_platform_arr_a['obj_name'] 	= $value['obj_name'];
				$new_platform_arr_a['page_name']    =  $value['page_name'];
				$new_platform_arr_a['template_page_id'] =  $value['template_page_id'];
				$new_platform_arr_a['serial'] 	        = $i;
				//添加顶级
				$p_id = $custom->add($new_platform_arr_a);
//P($new_platform_arr_a);
				$j    = 0;
				foreach ($platform_arr[$i]['sorts'] as $key_b => $value_b)
				{
					$new_platform_arr_b['az_id'] = $az_id;
					$new_platform_arr_b['p_id']  = $p_id;
					$new_platform_arr_b['name']  = $value_b['name'];
					$new_platform_arr_b['custom_value'] = $value_b['value'];
					$new_platform_arr_b['custom_type'] 	= $value_b['type'];
					$new_platform_arr_b['obj_name'] 	= $value_b['obj_name'];
					$new_platform_arr_b['page_name']        =  $value_b['page_name'];
					$new_platform_arr_b['template_page_id'] =  $value_b['template_page_id'];
					$new_platform_arr_b['serial'] 	        = $j;
//print_r($new_platform_arr_b);
					//添加子级
					$custom->add($new_platform_arr_b);
					$j++;
				}
				$i++;
			}

			exit(json_encode(array('code' => '200', 'msg' => '恭喜您,菜单保存成功!')));
		}
		else
		{
			exit(json_encode(array('code' => '400', 'msg' => '对不起,菜单信息错误!')));
		}
	}

	/**
	* 调用支付宝接口创建菜单
	*/
	public function add_platform()
	{
		$is_create_alipay_custom = $GLOBALS['install_info']['is_create_alipay_custom'];
		$az_id                   = $GLOBALS['install_info']['az_id'];
		$act                     = $this->_request('act');

		if($act == 'add_platform')
		{
			$custom      = M('custom');

			//获取商户的自定义菜单
			$custom_info = array();
			$custom_row  = $custom->where('az_id = ' . $az_id . ' AND p_id = 1')
								  ->field('id, name, custom_value as actionParam, custom_type as actionType')
								  ->order('serial')
								  ->select();
								  
			//组织创建菜单的数据结构
			if(is_array($custom_row))
			{
				$menu = array();
				$i = 0;
				foreach ($custom_row as $key => $value)
				{
					$menu[$i]['name'] = $value['name'];
					if ($value['actionType'] == 'link' || $value['actionType'] == 'cust_link')
					{
						//只有一级菜单
						$menu[$i]['type'] = 'view';
						if (!substr_count($custom_row[$i]['actionParam'],'http')) 
						{
							$menu[$i]['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $value['actionParam'];
						}
						unset($custom_row[$i]['actionParam']);
						unset($custom_row[$i]['actionType']);
						$i ++;
						continue;
					}
					
					//有二级菜单
					$sort_row = $custom->where('p_id = ' . $value['id'])
										->field('name, custom_value as actionParam, custom_type as actionType')
										->order('serial')
										->select();

					$sub_menu = array();
					if (is_array($sort_row))
					{
						$j = 0;
						foreach ($sort_row as $key => $value_b)
						{
							if ($value_b['actionType'] == 'link' || $value_b['actionType'] == 'cust_link')
							{
								$sub_menu[$j]['type'] = 'view';
								$sub_menu[$j]['name'] = $value_b['name'];
								if (!substr_count($sort_row[$j]['actionParam'],'http')) 
								{
									$sub_menu[$j]['url'] = 'http://' . $_SERVER['HTTP_HOST'] . $sort_row[$j]['actionParam'];
								}
							}	

							$j++;
						}
						unset($custom_row[$i]['actionParam']);
						unset($custom_row[$i]['actionType']);
						$menu[$i]['sub_button'] = $sub_menu;

					}
					unset($custom_row[$i]['id']);
					$custom_info['button'] =  $custom_row;
					$i++;
				}
				$new_menu = array(
					'button' => $menu
				);
				unset($menu);

				$platform_model = M('platform');
				$platform_info = $platform_model->field('appid, appsecret, access_token, refresh_token, expires_in, create_time')->where('isuse = 1 AND platform_type = 2 AND az_id = ' . $az_id)->find();
				if (!$platform_info)
				{
					exit(json_encode(array('code' => '300', 'msg' => '对不起, 请先完成公众平台接口设置!')));
				}

				#echo "<pre>";
				#print_r($platform_info);
				#die;
				//获取商家接口信息
				$appid = $platform_info['appid'];
				$secret = $platform_info['appsecret'];
				Vendor('Wxin.WeiXin');
				$access_token = WxApi::getAccessToken($appid, $secret);
				$wxapi_obj = new WxApi($access_token);
				$result = $wxapi_obj->menu_create($new_menu);
				#$result = $wxapi_obj->menu_get();
				#echo "<pre>";
				#print_r($result);
				#echo "</pre>";
				#echo "menu<pre>";
				#print_r($new_menu);
				#echo "<pre>";
				#print_r($custom_row);
				#die;

				if (isset($result['errcode']) && $result['errcode'] == 0)
				{
					exit(json_encode(array('code' => '200', 'msg' => '恭喜您，菜单已同步到您的微信公众号！')));
				}
				else
				{
					exit(json_encode(array('code' => '301', 'msg' => '对不起，菜单创建失败，请稍后再试！')));
				}
			}
			else
			{
				exit(json_encode(array('code' => '400', 'msg' => '对不起,您还未设置菜单信息!')));
			}

		}
	}


	//模拟点击菜单,发送消息
	public function moni_click() {
		$url = "http://vent.tmallshop.cn/";

		$xml_string = '<XML> <AppId><![CDATA[2013091400029967]]></AppId> <FromUserId><![CDATA[2088102122554576]]></FromUserId> <CreateTime>1380108585332</CreateTime> <MsgType><![CDATA[event]]></MsgType> <EventType><![CDATA[click]]></EventType> <ActionParam><![CDATA[]]></ActionParam> <AgreementId><![CDATA[]]></AgreementId> <AccountNo><![CDATA[]]></AccountNo> <UserInfo><![CDATA[{ "logon_id": "135****1009", "user_name": "*iuxu527" }]]> </UserInfo> </XML>';

		$data= "sign=SKlbQBMz7ImtuU0dvTvYybMI+jRu2hvM9RXHcs4/OoDEQmYzr6vX7X8RH70YV4bcd8aHLF132GGZYteYGAdD+ntBajD4UdjaHXDJOOtz2Pt7vO6SST37NIyrlqDEzMdsY6yBH5SCTwg7bA3oj1kpAxYIs0iLqPk8h98PLssbpAs=&biz_content=" . $xml_string . "&sign_type=RSA&service=alipay.mobile.public.message.notify&charset=GBK";

		$ch = curl_init();    //初始化curl
		curl_setopt($ch, CURLOPT_URL, $url);                //设置链接
		curl_setopt($ch, CURLOPT_POST, 1);                  //设置为get方式
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);    //POST数据
		$response = curl_exec($ch); //接收返回信息

		if(curl_errno($ch)){//出错则显示错误信息
			//print curl_error($ch);
		}
		else
		{
			$data = json_decode($response,true);
			//print_r($response);die;
			if(!isset($data["errcode"]))
			$access_token=$data["access_token"];
		}

		curl_close($ch);          //关闭curl链接
	}

	
	/**
	* 获取该客户所拥有的模块接口
	*/
	public function Get_user_model()
	{
		$az_id         = $GLOBALS['install_info']['az_id'];
		$model         = M('model');
		
		//查询所拥有的模块   
		$dir_name_row  = $model->query('SELECT `dir_name` FROM (SELECT dir_name,serial  FROM `tp_model` WHERE ( az_id = 1 AND isuse = 1 ) UNION SELECT dir_name, serial FROM tp_model WHERE az_id = 0 AND isuse = 1) as t ORDER BY t.serial ASC'); 
		                                           
		//匹配并加载/Lib/Widget/模块挂件  
		$model_arr  = $model->where('isuse = 1')
							->field("GROUP_CONCAT(dir_name SEPARATOR ',') AS str")
		                    ->order('serial')
		                    ->find();
		 
         $model_arr = explode(',' , $model_arr['str']);  
// print_r($model_arr)  ;                                  
		foreach ($dir_name_row as $key => $value)
		{
			if(in_array($value['dir_name'], $model_arr))
				$Widget .= W($value['dir_name'], array('name'=> $value['dir_name'] . '_template'),true); 
		}  
//P($Widget);  
		exit(json_encode(array('code' => '200', 'data' => $Widget)));         
	}
	

	/**
	* 获取指定页面所有模版    的接口
	*/
	public function Get_page_template() {
		$page_name = $this->_post('page_name');

//$page_name = 'items_list';
		if($page_name)
		{
			$template_page = M('view_template_page');
			$row = $template_page->where('isuse = 1 AND model_page_dir_name = "' . $page_name . '"')
								 ->field('id, page_name as name, path_img, model_page_id')
								 ->order('serial')
								 ->select();
//P($row);die();
			exit(json_encode($row));
		}
		else 
		{
			exit(json_encode(array('code' => '400', 'msg' => '对不起,请求参数不对')));
		}
	}

	/**
	* 获取当前页面模版    的接口
	*/
	public function get_cur_page_template() {
		$az_id         = $GLOBALS['install_info']['az_id'];
		$template_id   = $GLOBALS['install_info']['template_id'];
		$page_name 	   = $this->_post('page_name');
		//$model_page_id = $this->_post('model_page_id');
		//$model_page_id = 3;
		
		//用page_name获取model_page_id
		$model_page        		= M('model_page');
		$model_page_id 			= $model_page->where('dir_name = "' . $page_name . '"')->getfield('id');
	
		//获取客户当前使用的模版名称
		$template_style         = M('template_style');
	 	$template_style_info    = $template_style->join('RIGHT JOIN tp_template_page ON tp_template_style.page_id = tp_template_page.id')
		        				 				 ->where('tp_template_style.az_id = ' . $az_id . ' AND tp_template_style.model_page_id = ' . $model_page_id)
		        			     				 ->field('tp_template_style.page_id, tp_template_page.page_name')
		        			    			     ->find();
	
		//若客户自定义了该页面的模板，直接返回
		if ($template_style_info)
			exit(json_encode(array('code' => '200', 'template_id' => $template_style_info['page_id'], 'template_name' => $template_style_info['page_name'] )));

		//若客户没有自定义该页面的模板，则返回该客户安装表中的template_id中该页面对应的模板
		$template_package      = M('template_package');
		$template_package_info = $template_package->join('RIGHT JOIN tp_template_page ON tp_template_package.page_id = tp_template_page.id')
		        				 				  ->where('tp_template_package.template_id = ' . $template_id . ' AND tp_template_package.model_page_id = ' . $model_page_id)
		        			     				  ->field('tp_template_package.page_id, tp_template_page.page_name')
		        			    			      ->find(); 
		
		if ($template_package_info)
			exit(json_encode(array('code' => '200', 'template_id' => $template_package_info['page_id'], 'template_name' => $template_package_info['page_name'] )));

		exit(json_encode(array('code' => '400', 'msg' => '对不起,请求参数不对')));
	}
	
	/**
	* 保存选择的页面模版    的接口
	*/
	public function save_page_template() {
		$az_id         = $GLOBALS['install_info']['az_id'];
		$id 		   = $this->_post('id');
		$model_page_id = $this->_post('model_page_id');

		//$id = 7;
		//$model_page_id = 7;
		if($id && $model_page_id)
		{
			$arr = array(
				'az_id' 		=> $az_id,
				'model_page_id' => $model_page_id,
				'page_id' 		=> $id,
				'iscust' 		=> 0,
				'isuse' 		=> 1,
				'addtime' 		=> time(),
			);
			
			$template_style      = M('template_style');
			$template_style_info = $template_style->where('az_id = ' . $az_id . ' AND model_page_id = ' . $model_page_id)->find();
			
			//若数据库中已有该模板的数据，更新之
			if ($template_style_info)
				$template_style->where('id = ' . $template_style_info['id'])->save($arr);
			else
				$template_style->add($arr);
		
			//若数据库中没有该模板的数据，添加一条数据
			exit(json_encode(array('code' => '200', 'msg' => '恭喜您,设置成功!')));
		}
		else 
		{
			exit(json_encode(array('code' => '400', 'msg' => '对不起,请求参数不对')));
		}
	}

	
	/**
     * 获取所有的消息素材 (用于消息选择弹窗)
     */
    public function mg_message() 
    {
    	//获取所有的消息素材
    	$az_id 	   = $GLOBALS['install_info']['az_id'];
    	$dir_name  = $GLOBALS['install_info']['dir_name'];

    	$message   = M('message');

    	import('ORG.Util.PageAcp');
    	$count  = $message->where('az_id = ' . $az_id . ' AND is_attention = 0 AND is_no_attention = 0')->count();
    	$Page   = new Page($count,C('MCP_PER_PAGE'));
    	$show   = $Page->show();
    	$row    = $message->where('az_id = ' . $az_id . ' AND is_attention = 0 AND is_no_attention = 0')
				    	  ->order('id desc')
				    	  ->limit($Page->firstRow . ',' . $Page->listRows)
				    	  ->select();

    	$i = 0;
    	foreach ($row as $key => $value)
    	{
    		$row[$key]['edit']       = "/McpMessageNotify/edit_message/id/".$value['id'];
    		$str                     = html_entity_decode($row[$i]['contents']);
            $row[$i]['contents']     = str_replace('\\"', '', $str);
            if($row[$i]['img_path'])
    			$row[$i]['img_path'] = '/Upload/' . $dir_name . $value['img_path'];
    			
    		$i++;
    	}
        
    	$this->assign('page',$show);
    	$this->assign('message_list', $row);

    	//TITLE中的页面标题
    	$this->assign('head_title', '消息素材管理');
    	$this->display();
    }
	
	/**
     * 自定义页列表
     */
	public function mg_cust_page() {
		$cust_page  = M('cust_page');
		$az_id      = $GLOBALS['install_info']['az_id'];

		$act        = $this->_post('act');
		$action     = $this->_post('action');

		//获取所有自定义页
		import('ORG.Util.PageAcp');
		$count  = $cust_page->where('az_id = ' . $az_id . ' AND (is_enable <> '. CL_IS_ENABLE_DEL .' and is_enable <> ' . CL_IS_ENABLE_VIEW.')')->count();
		$Page   = new Page($count,C('MCP_PER_PAGE'));
		$show   = $Page->show();
		$row    = $cust_page->where('az_id = ' . $az_id . ' AND (is_enable <> '. CL_IS_ENABLE_DEL .' and is_enable <> ' . CL_IS_ENABLE_VIEW.')')
							->order('id desc')
							->limit($Page->firstRow . ',' . $Page->listRows)
							->select();

		
		$AzId      = $GLOBALS['install_info']['az_id'];
		$DirName   = $GLOBALS['install_info']['dir_name'];
		
		$row_real  = array();
		$row_del   = array();
		foreach ($row as $k => $v){
			if($v['page_type'] == 1){
				if(file_exists(CLIENT_DIR . $DirName . '/CustPage/'.$v['page_dir'].'/'.$v['id'].'.html')){
					$row_real[] = $v;
				}else{
					$row_del[] = $v['id'];
				}
			}else{
				if(file_exists(CLIENT_DIR . $DirName . '/AdvPage/'.$v['page_dir'].'/'.$v['id'].'.html')){
					$row_real[] = $v;
				}else{
					$row_del[] = $v['id'];
				}
			}
		}

		if(sizeof($row_del)){
			$cust_page->where('id in ('.implode(',',$row_del).')')->save(array('is_enable'=>CL_IS_ENABLE_DEL));
			#echo $cust_page->_sql();
		}
		
		$this->assign('page',$show);
		$this->assign('cust_page', $row_real);

		$act = $this->_request('act');

		$time = time();
		$Ym = date("Ym",$time);

		$behaviour = $this->_request('behaviour');
		$AdvId = $this->_request('content_id', 0);

		if($action == 'add'){
			if($act == 'submit' && ($behaviour == 'preview' || $behaviour == 'save' || $behaviour == 'issue')){

				$contents = $this->_request('contents');
				$style_css = $this->_request('style_css');

				$Cust_page = M('Cust_page');

				$page_name = $this->_request('page_name');
				$contents = $this->_request('contents');
				$style_css = $this->_request('style_css');
				$isuse = 1;//$this->_request('isuse');

				if($behaviour == 'preview') $is_enable = 4;
				elseif ($behaviour == 'save') $is_enable = 2;
				elseif ($behaviour == 'issue') $is_enable = 1;

				$arr = array(
				'az_id'			=> $AzId,
				'page_dir'		=> $Ym,
				'page_time'		=> $time,
				'page_type'		=> 2,
				'is_enable'		=> $is_enable,		//1发布2保存不发表3删除4预览
				);
				
				if($behaviour != 'preview'){
					$arr['page_name']	= $page_name;
				}
				
				if ($AdvId){
					$arr['id'] = $AdvId;
				}

				$Cust_page->add($arr, array(), true);

				if ($AdvId){
					$AdvValId =  $AdvId;
				}else {
					$AdvValId = $Cust_page->getLastInsID();
				}

				//生成文件
				//判断客户目录是否存在
				if(!is_dir(CLIENT_DIR.$DirName)) mkdir(CLIENT_DIR.$DirName);

				//判断客户目录专家编辑文件是否存在
				if(!is_dir(CLIENT_DIR.$DirName.'/AdvPage/')) mkdir(CLIENT_DIR.$DirName.'/AdvPage/');

				$ClientDir = CLIENT_DIR.$DirName.'/AdvPage/';

				//判断当日客户目录是否存在
				if(!is_dir($ClientDir.$Ym)) mkdir($ClientDir.$Ym);

				//判断css目录
				if(!is_dir($ClientDir.$Ym.'/css/')) mkdir($ClientDir.$Ym.'/css/');

				@copy(CLIENT_DIR.'adv.html',$ClientDir.$Ym.'/'.$AdvValId.'.html');

				file_put_contents($ClientDir.$Ym.'/css/'.$AdvValId.'.css',$style_css);


				$CssLink = '<link href="http://'.$this->system_config['WEBSITE_DOMAIN'].'/client_dir/'.$DirName.'/AdvPage/'.$Ym.'/css/'.$AdvValId.'.css?v='.time().'" rel="stylesheet" type="text/css">';
				$TitleVal = '<title>'.$page_name.'</title>';
				$AdvVal = '<!--@start-body#-->'.htmlspecialchars_decode($contents).'<!--@end-body#-->';

				$AdvFront = array('{block name="title"}css{/block}','{block name="title"}标题{/block}', '{block name="body"}内容{/block}');
				$AdvLater = array($CssLink, $TitleVal, $AdvVal);

				$html = file_get_contents($ClientDir.$Ym.'/'.$AdvValId.'.html');
				$html = str_replace($AdvFront, $AdvLater, $html);
				file_put_contents($ClientDir.$Ym.'/'.$AdvValId.'.html',$html);



				$result = array();

				if($behaviour == 'preview') $result['success'] = 4;
				elseif ($behaviour == 'save') $result['success'] = 2;
				elseif ($behaviour == 'issue') $result['success'] = 1;

				$result['content_id'] = $AdvValId;
				$result['src'] = U('/FrontAdvPage/adv_page/id/'.$AdvValId);
				echo json_encode($result);
				exit;
			}elseif ($act == 'submit' && $behaviour == 'gaveup'){
				if($AdvId){
					$Cust_page = M('Cust_page');
					$Cust_page->where(' id= '.$AdvId.' and az_id = '. $AzId)->save(array('is_enable' => 3 ));
				}

				$result = array();
				$result['success'] = 3;
				$result['content_id'] = 0;
				echo json_encode($result);
				exit;

			}
		}elseif ($action == 'edit'){

			$Cust_page = M('Cust_page');
			$AdvInfo = $Cust_page->where('id = ' . $AdvId . ' and az_id = ' . $AzId . ' and is_enable <> ' . CL_IS_ENABLE_DEL)->find();
			if(!$AdvInfo){
				$result = array();
				$result['success'] = 2;
				$result['error'] = '对不起，您选择的文件不存在';
				echo json_encode($result);
				exit;
			}

			$ClientDir = CLIENT_DIR.$DirName.'/AdvPage/';
			$CssDir = $ClientDir.$AdvInfo['page_dir'].'/css/'.$AdvInfo['id'].'.css';
			$CssInfo = file_get_contents($CssDir);

			$HtmlDir = $ClientDir.$AdvInfo['page_dir'].'/'.$AdvInfo['id'].'.html';
			$HtmlInfo = file_get_contents($HtmlDir);
			preg_match('/<!--@start-body#-->(.*)<!--@end-body#-->/isU', $HtmlInfo, $match);

			$Ym = $AdvInfo['page_dir'];

			if($act == 'submit' && ($behaviour == 'preview' || $behaviour == 'save' || $behaviour == 'issue')){

				$contents = $this->_request('contents');
				$style_css = $this->_request('style_css');

				$Cust_page = M('Cust_page');

				$page_name = $this->_request('page_name');
				$contents = $this->_request('contents');
				$style_css = $this->_request('style_css');

				if($behaviour == 'preview') $is_enable = $AdvInfo['is_enable'];
				elseif ($behaviour == 'save') $is_enable = 2;
				elseif ($behaviour == 'issue') $is_enable = 1;

				$arr = array(
				'is_enable'		=> $is_enable,		//1发布2保存不发表3删除4预览
				);

				if($behaviour != 'preview'){
					$arr['page_name']	= $page_name;
				}

				$Cust_page->where('id='.$AdvId.' and az_id='. $AzId)->save($arr);

				$AdvValId = $AdvId;

				file_put_contents($ClientDir.$Ym.'/css/'.$AdvValId.'.css',$style_css);

				$CssLink = '<link href="http://'.$this->system_config['WEBSITE_DOMAIN'].'/client_dir/'.$DirName.'/AdvPage/'.$Ym.'/css/'.$AdvValId.'.css?v='.time().'" rel="stylesheet" type="text/css">';
				$TitleVal = '<title>'.$page_name.'</title>';
				$AdvVal = '<!--@start-body#-->'.htmlspecialchars_decode($contents).'<!--@end-body#-->';

				$AdvFront = array('{block name="title"}css{/block}','{block name="title"}标题{/block}', '{block name="body"}内容{/block}');
				$AdvLater = array($CssLink, $TitleVal, $AdvVal);

				@copy(CLIENT_DIR.'adv.html',$ClientDir.$Ym.'/'.$AdvValId.'.html');

				$html = file_get_contents($ClientDir.$Ym.'/'.$AdvValId.'.html');
				$html = str_replace($AdvFront, $AdvLater, $html);
				file_put_contents($ClientDir.$Ym.'/'.$AdvValId.'.html',$html);

				$result = array();

				if($behaviour == 'preview') $result['success'] = 4;
				elseif ($behaviour == 'save') $result['success'] = 2;
				elseif ($behaviour == 'issue') $result['success'] = 1;

				$result['content_id'] = $AdvValId;
				$result['src'] = U('/FrontAdvPage/adv_page/id/'.$AdvValId);
				echo json_encode($result);
				exit;
			}elseif ($act == 'submit' && $behaviour == 'gaveup'){
				if($AdvId){
					$Cust_page = M('Cust_page');
					$Cust_page->where(' id= '.$AdvId.' and az_id = '. $AzId)->save(array('is_enable' => $AdvInfo['is_enable'] ));
				}

				$result = array();
				$result['success'] = 3;
				$result['content_id'] = 0;
				echo json_encode($result);
				exit;

			}

			$result = array();
			$result['success'] = 1;
			$result['htmlinfo'] = (isset($match[1]) ? $match[1] : '');
			$result['cssinfo'] = $CssInfo;
			$result['title'] = $AdvInfo['page_name'];
			$result['advid'] = $AdvId;
			$result['src'] = U('/FrontAdvPage/adv_page/id/'.$AdvId);
			echo json_encode($result);
			exit;
		}

		//TITLE中的页面标题
		$this->assign('head_title', '自定义页列表');
		$this->display();
	}

    /**
     * 获取所有的自定义页面  (用于消息选择弹窗)
     */
	
	public function mg_cust_page_i() {
		$cust_page = M('cust_page');
		$az_id   = $GLOBALS['install_info']['az_id'];
		$act     = $this->_post('act');
		$action     = $this->_post('action');

		//获取所有自定义页
		import('ORG.Util.PageAcp');
		$count  = $cust_page->where('az_id = ' . $az_id . ' AND (is_enable <> '. CL_IS_ENABLE_DEL .' and is_enable <> ' . CL_IS_ENABLE_VIEW.')')->count();
		$Page   = new Page($count,C('MCP_PER_PAGE'));
		$show   = $Page->show();
		$row    = $cust_page->where('az_id = ' . $az_id . ' AND (is_enable <> '. CL_IS_ENABLE_DEL .' and is_enable <> ' . CL_IS_ENABLE_VIEW.')')
							->order('id desc')
							->limit($Page->firstRow . ',' . $Page->listRows)
							->select();
        
		foreach($row as $key=>$value)
		{
			if($value['page_type']==1)
			{
				$row[$key]['edit']="/McpPlatForm/edit_cust_page/id/".$value['id'];
			}
			else
			{
				$row[$key]['edit']="/McpPlatForm/mg_cust_page?hide_comm_page=1&id=".$value['id']."&status=3";
			}
		}
		
		$this->assign('page',$show);
		$this->assign('cust_page', $row);

		//TITLE中的页面标题
		$this->assign('head_title', '自定义页列表');
		$this->display();
	}
	
	/**
     * 添加自定义页
     */
	public function add_cust_page() {

		//获取编辑器
		$pablic = new PublicAction;
		$str    = $pablic->GetKin();
		$this->assign('GetKin', $str);

		$az_id          = $GLOBALS['install_info']['az_id'];

		$id         	= $this->_get('id');
		$e_id      	 	= $this->_post('id');
		$act        	= $this->_post('act');
		$page_name   	= $this->_post('page_name');
		$is_enable     	= $this->_post('is_enable');
		$contents   	= $this->_post('contents');

		//解密地址并输出
		$redirect = $this->_get('redirect');
		$redirect = url_jiemi($redirect);
		$redirect = ($redirect) ? $redirect : U('/McpPlatForm/mg_cust_page/');

		//修改数据
		if($act == 'submit')
		{
			//数据验证
			if (!$page_name)
			mcp_show_alert('对不起，请输入页面名称！','top.document.form2','page_name');//$this->ajaxReturn('page_name', '对不起,请输入页面名称!', 0);

			if (!$contents)
			mcp_show_alert('对不起,请输入内容！','top.document.form2','contents');//$this->ajaxReturn('contents', '对不起,请输入内容!', 0);
			$time_now = time();
			$page_dir = date("Ym",$time_now);

			$arr = array(
			'az_id'			=> $az_id,
			'page_dir'		=> $page_dir,
			'page_type'		=> 1,//1普通2高级
			'page_name'		=> $page_name,
			'page_time'		=> $time_now,
			'is_enable'		=> $is_enable,
			);

			$Cust_page = M('Cust_page');
			$Cust_page->add($arr);
			$CustId = $Cust_page->getLastInsID();

			$DirName   = $GLOBALS['install_info']['dir_name'];

			//判断客户目录是否存在
			if(!is_dir(CLIENT_DIR.$DirName)) mkdir(CLIENT_DIR.$DirName);

			//判断客户目录专家编辑文件是否存在
			if(!is_dir(CLIENT_DIR.$DirName.'/CustPage/')) mkdir(CLIENT_DIR.$DirName.'/CustPage/');

			$ClientDir = CLIENT_DIR.$DirName.'/CustPage/';

			//判断当日客户目录是否存在
			if(!is_dir($ClientDir.$page_dir)) mkdir($ClientDir.$page_dir);


			@copy(CLIENT_DIR.'cust.html',$ClientDir.$page_dir.'/'.$CustId.'.html');

			$TitleVal = '<title>'.$page_name.'</title>';	
			//此行用来控制iframe框体的高度  2013-11-20
			$TitleVal .= '<script>window.onload=function(){ var Bheight=document.getElementsByTagName("body")[0].offsetHeight;top.window.document.getElementById("cust'.$CustId.'").style.height=Bheight+33+"px";}</script>';
			
			$CustVal = '<!--@start-body#-->'.htmlspecialchars_decode($contents).'<!--@end-body#-->';

			$CustFront = array('{block name="title"}标题{/block}', '{block name="body"}内容{/block}');
			$CustLater = array($TitleVal, $CustVal);

			$html = file_get_contents($ClientDir.$page_dir.'/'.$CustId.'.html');
			$html = str_replace($CustFront, $CustLater, $html);
			file_put_contents($ClientDir.$page_dir.'/'.$CustId.'.html',$html);


			//日志记录
			$this->save_logs(CL_OP_TYPE_ADD, '添加自定义页《'.$page_name.'》', C('DB_PREFIX') . 'cust_page', '', $this->login_user['user_id']);
			mcp_show_alert('恭喜您,添加自定义页成功！',null,null,$redirect,null,true);//$this->ajaxReturn($redirect, '恭喜您,添加自定义页成功', 1);
		}
		//获取所有的自定义页分类
		$cust_page_sort = M('cust_page_sort');
		$sort  = $cust_page_sort->where('az_id = ' . $az_id)->order('serial desc')->select();
		$this->assign('sort', $sort);

		//TITLE中的页面标题
		$this->assign('head_title', '添加自定义页');
		$this->display();
	}


	/**
     * 编辑自定义页
     */
	public function edit_cust_page() {

		//获取编辑器
		$pablic = new PublicAction;
		$str = $pablic->GetKin();
		$this->assign('GetKin', $str);

		$az_id          = $GLOBALS['install_info']['az_id'];

		$id         	= $this->_get('id');
		$e_id      	 	= $this->_post('id');
		$act        	= $this->_post('act');
		$page_name   	= $this->_post('page_name');
		$is_enable 		= $this->_post('is_enable');
		$contents   	= $this->_post('contents');

		//解密地址并输出

		$redirect =  U('/McpPlatForm/mg_cust_page/');

		//获取需要修改的数据
		if (!$id)
		alert_mesg('请提供可编辑的ID！');

		$cust_page = M('cust_page');

		$row = $cust_page->where('id = ' . $id. ' and az_id = ' .$az_id . ' and page_type = 1' )->find();
		if(!$row)
		{
			alert_mesg('不存在该页面');
			exit;
		}
		$DirName   = $GLOBALS['install_info']['dir_name'];
		$ClientDir = CLIENT_DIR.$DirName.'/CustPage/';
		$HtmlDir = $ClientDir.$row['page_dir'].'/'.$row['id'].'.html';
		$HtmlInfo = file_get_contents($HtmlDir);
		preg_match('/<!--@start-body#-->(.*)<!--@end-body#-->/isU', $HtmlInfo, $match);


		//修改数据
		if($act == 'submit')
		{
			//数据验证
			if (!$page_name)
			mcp_show_alert('对不起，请输入页面名称！','top.document.form2','page_name');

			if (!$contents)
			mcp_show_alert('对不起,请输入内容！','top.document.form2','contents');
			$page_dir = $row['page_dir'];

			$arr = array(
			'az_id'			=> $az_id,
			'page_name'		=> $page_name,
			'is_enable'		=> $is_enable,
			);

			$DirName   = $GLOBALS['install_info']['dir_name'];

			//判断客户目录专家编辑文件是否存在
			if(!is_dir(CLIENT_DIR.$DirName.'/CustPage/')) mkdir(CLIENT_DIR.$DirName.'/CustPage/');

			$ClientDir = CLIENT_DIR.$DirName.'/CustPage/';

			//判断当日客户目录是否存在
			if(!is_dir($ClientDir.$page_dir)) mkdir($ClientDir.$page_dir);


			@copy(CLIENT_DIR.'cust.html',$ClientDir.$page_dir.'/'.$id.'.html');

			$TitleVal = '<title>'.$page_name.'</title>';
			//此行用来控制iframe框体的高度  2013-11-20
			$TitleVal .= '<script>window.onload=function(){ var Bheight=document.getElementsByTagName("body")[0].offsetHeight;top.window.document.getElementById("cust'.$id.'").style.height=Bheight+33+"px";}</script>';
			
			$CustVal = '<!--@start-body#-->'.htmlspecialchars_decode($contents).'<!--@end-body#-->';

			$CustFront = array('{block name="title"}标题{/block}', '{block name="body"}内容{/block}');
			$CustLater = array($TitleVal, $CustVal);

			$html = file_get_contents($ClientDir.$page_dir.'/'.$id.'.html');
			$html = str_replace($CustFront, $CustLater, $html);
			file_put_contents($ClientDir.$page_dir.'/'.$id.'.html',$html);


			//日志记录
			$this->save_logs(CL_OP_TYPE_EDIT, '修改自定义页《'.$page_name.'》', C('DB_PREFIX') . 'cust_page', '', $this->login_user['user_id']);
			mcp_show_alert('恭喜您,修改自定义页成功！',null,null,$redirect,null,true);
		}


		if(is_array($row))
		{
			$str = html_entity_decode(isset($match[1]) ? $match[1] : '');
			$row['contents'] = str_replace('\\"', '', $str);
			$row['path_img'] = '/Upload/' . $dir_name . '/' . $row['path_img'];
		}

		$this->assign('cust_page', $row);

		//TITLE中的页面标题
		$this->assign('head_title', '编辑自定义页');
		$this->display();
	}


	/**
     * 删除自定义页
     */
	public function del_cust_page() {
		$id = $this->_get('id');

		if (!$id)
		alert_mesg('请提供可删除的ID！');

		$az_id		= $GLOBALS['install_info']['az_id'];
		$Cust_page = M('Cust_page');
		$Cust_page->where('id='.$id.' and az_id = '.$az_id)->delete();

		//日志记录
		$this->save_logs(CL_OP_TYPE_DEL, '删除ID为'.$id.'的自定义页', C('DB_PREFIX') . 'cust_page', '', $this->login_user['user_id']);
		mcp_show_alert('恭喜您,删除成功!','','','/McpPlatForm/mg_cust_page/','',true);
	}


	public function ajaxUploadImage()
	{
		if(!empty($_POST)){
			$imageUp = (!empty($_FILES['Filedata']['name'])) ? $_FILES['Filedata'] : '';

			if($imageUp){
				$time = time();
				$Ym = date("Ym",$time);
				import("@.Common.UploadImage");
				$imgConf = array(
				'imgRootPath' => CLIENT_DIR . $GLOBALS['install_info']['dir_name'].'/AdvPage/'.$Ym,
				'imgSavePath' => 'images'
				);
				$imgUp = new UploadImage($imgConf);
				$ImgPath = $imgUp->uploadOne($imageUp, true);

				//取得图片地址
				$ImgUrl = 'http://'.$this->system_config['WEBSITE_DOMAIN'].'/client_dir/' .$GLOBALS['install_info']['dir_name'].'/AdvPage/'.$Ym.'/'. $ImgPath;

				$Adv_images = M('Adv_images');
				$arr = array(
				'az_id'		=> $GLOBALS['install_info']['az_id'],
				'img_src'	=> $ImgUrl,
				'add_time'	=> time(),
				'time_dir'	=> $Ym,
				'is_enable'	=> CL_IS_ENABLE_YES,
				);

				$Adv_images->add($arr);

				if($ImgUrl){
					echo json_encode(array('url' => $ImgUrl, 'status' => 1, 'path' => $ImgPath));
				}else{
					echo json_encode(array('url' => '', 'status' => 0, 'path' => ''));
				}
			}
		}
	}

	public function js_get_session_id() {
		//echo session_id();exit;
		$this->ajaxReturn(session_id(), '', 1);
	}
	
	
	//前端页面添加一级菜单的默认html代码输出
	public function js_get_first() {
		$id = $this->_post('id');

		if($id)
		{
$str = <<< ABC
<div data="$id" id="alipay-meun-$id" class="alipay-meun-c j-menudata" style="display:block;">
  <div class="alipay-meun-type j-meun-type">
    <a data="no" href="javascript:;">无子级菜单</a>
	<a class="hover" data="have" href="javascript:;">有子级菜单</a>
  </div>
  <div class="alipay-meun-have j-alimenutab" id="alimeunh$id">
    <div class="ali-meun-t"><span class="icon"></span><span class="name">子菜单名称</span><span class="type">子菜单类型</span><span class="xsnr">显示内容</span> <span class="tem">选择模板</span>
      <div style="clear:both;"></div>
    </div>
    <div class="ali-meun-c">
      <ul>
      </ul>
    </div>
    <div class="ali-navmeun-add" style="display:block;"><a href="#" class=""></a></div>
  </div>
  <div class="alipay-meun-no j-alimenutab" id="alimeunno$id">
    <div class="ali-meun-c-type-e j-nav-erji">
      <div class="ali-meun-c-type j-meunparent">
		<div class="ali-meun-c-type-text j-alltext" style="display:none;">跳转网址</div>
		<div class="ali-meun-c-type-edit j-alledit" style="display:block;">
			<i></i>
			<span>跳转网址</span>
			<select class="ali-m-se-type">
                <option value="1" selected="selected">跳转网址</option>
            </select>
        </div>
	</div>
      <div class="ali-meun-c-xsnr j-meunparent">
	        <div class="ali-meun-c-xsnr-text j-alltext" style="display: none;"></div>
	        <div class="ali-meun-c-xsnr-edit j-alledit" style="display: block;">
	            <div class="textp-wap j-xsnrspan clearfix" style="display:block;">
	                <span class="text"><input type="text" onchange="change_value(this);" class="text" name="val" value=""></span>
	            </div>
	        </div>
	    </div>
      <div style="clear:both;"></div>
      <input type="hidden" class="ali-meun-lihidden" ejtype="1" ejobjname="" ejclassid="" ejdataid="" ejedit="" ejpagename="" ejplatid="" name="" value=""/>
    </div>
  </div>
</div>
ABC;

			exit(json_encode(array('code' => '200', 'msg' => '恭喜您!请求成功!' , 'data' => $str)));         
		}
		else 
		{
			exit(json_encode(array('code' => '400', 'msg' => '对不起!参数不正确1')));         
		}
	}

	
	//前端页面添加2级菜单的默认html代码输出
	public function js_get_tow() {
		$id = $this->_post('id');

		if($id)
		{
$str = <<< ABC
<li class="j-nav-erji" id="$id">
	<div class="ali-meun-c-icon"><i class="move"></i><i class="del"></i></div>
	<div class="ali-meun-c-name j-meunparent">
		<div class="ali-meun-c-name-text j-alltext" style="display:none;"></div>
        <div class="ali-meun-c-name-edit j-alledit" style="display:block;">
            <input name="ali-meun-name-erji" type="text" value=""  onKeyUp="CheckLength(this,36)"/>
        </div>
	</div>
	<div class="ali-meun-c-type j-meunparent">
		<div class="ali-meun-c-type-text j-alltext" style="display:none;">跳转网址</div>
		<div class="ali-meun-c-type-edit j-alledit" style="display:block;">
			<i></i>
			<span>跳转网址</span>
			<select class="ali-m-se-type">
                <option value="1" selected="selected">跳转网址</option>
            </select>
        </div>
	</div>
<div class="ali-meun-c-xsnr j-meunparent">
	<div class="ali-meun-c-xsnr-text j-alltext" style="display: none;"></div>
	<div class="ali-meun-c-xsnr-edit j-alledit" style="display: block;">
	<div class="textp-wap j-xsnrspan clearfix" style="display:block;">
		<span class=""><input type="text" onchange="change_value(this);" class="text" name="val" value=""></span>
	</div>
	<div class="textp-tel" style="display:none;"><input name="ali-m-input-tel" type="text" value=""></div>
	<div class="textp-auto j-xsnrspan clearfix" style="display:none;">
		<span class="text">选择图文信息</span>
	    <span class="replace">选择</span>
	</div>
	<div class="textp-custom j-xsnrspan clearfix" style="display:none;">
		<span class="text">选择自定义页面</span>
	</div>
	</div>
	</div>
	<div style="clear:both;"></div>
	<input type="hidden" class="ali-meun-lihidden" ejid="" ejname="" ejtype="view" ejobjname="" ejclassid="" ejdataid="" ejedit="" ejpagename="" ejplatid="" pagename="" thisplanid="" name="" value=""/>
</li>
ABC;

			exit(json_encode(array('code' => '200', 'msg' => '恭喜您!请求成功!' , 'data' => $str)));         
		}
		else 
		{
			exit(json_encode(array('code' => '400', 'msg' => '对不起!参数不正确1')));         
		}
	}
}

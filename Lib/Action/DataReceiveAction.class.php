<?php
// 用户界面 
class DataReceiveAction extends StatAction {
	function receive_data()
	{
		//判断key是否合法
		$key = $this->_request('key');
		if ($this->valid_user($Key))
		{
		}
		#echo 'data: ';print_r($GLOBALS['HTTP_RAW_POST_DATA']);die;
		$data_arr = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
		//接收数据
		$az_id = htmlspecialchars($data_arr['az_id']);
		$user_id = htmlspecialchars($data_arr['user_id']);
		#$tb_seller_nick = $this->_request('tb_seller_nick', '');
		#$tb_buyer_nick = $this->_request('tb_buyer_nick', '');
		$ip = htmlspecialchars($data_arr['ip']);
		$province_id = htmlspecialchars($data_arr['province_id']);
		$city_id = htmlspecialchars($data_arr['city_id']);
		$browser = htmlspecialchars($data_arr['browser']);
		$user_agent = htmlspecialchars($data_arr['user_agent']);
		$screen_id = htmlspecialchars($data_arr['screen_id']);
		$domain = htmlspecialchars($data_arr['domain']);
		$visit_url = htmlspecialchars($data_arr['visit_url']);
		$os_id = htmlspecialchars($data_arr['os_id']);
		$add_time = htmlspecialchars($data_arr['add_time']);
		$hour = htmlspecialchars($data_arr['hour']);
		$visit_date = htmlspecialchars($data_arr['visit_date']);
		$language_id = htmlspecialchars($data_arr['language_id']);
		$terminal_id = htmlspecialchars($data_arr['terminal_id']);
		$from_domain = htmlspecialchars($data_arr['from_domain']);
		$from_url = htmlspecialchars($data_arr['from_url']);
		$is_entrance = htmlspecialchars($data_arr['is_entrance']);
		$page_title = htmlspecialchars($data_arr['page_title']);
		#$source_id = htmlspecialchars($data_arr['source_id']);
		#$engine_id = htmlspecialchars($data_arr['engine_id']);
		#$keywords = htmlspecialchars($data_arr['s_keywords']);
		$client_key = htmlspecialchars($data_arr['client_key']);
		$entrance_id = htmlspecialchars($data_arr['entrance_id']);
		$have_jump = htmlspecialchars($data_arr['have_jump']);
		$is_new_visitor = $client_key ? 0 : 1;

		$Pv_main = M('Pv_main');
		$Pv_respondent_data = M('Pv_respondent_data');
		#$Pv_source_data = M('Pv_source_data');
		$Pv_system_analysis = M('Pv_system_analysis');

		//分析
		$last_time = 30;
		if ($from_domain != '')
		{
			//更新页面停留时间
			$row = $Pv_respondent_data->where('visit_url = "' . $from_url . '"')->order('add_time DESC')->limit(0, 1)->select();
			if (count($row))
			{
				$last_time = $add_time - $row[0]['add_time'];
				$params = array(
					'last_time'	=> $last_time,
					'have_jump'	=> 0
				);
				$Pv_respondent_data->where('id = ' . $row[0]['id'])->save($params);
			}
		}

		//生成32位client_key并传到前台
		$md5_val = MD5($add_time . $ip);
		$new_client_key = $client_key ? '' : $md5_val;
		$client_key = $client_key ? $client_key : $md5_val;

		/*插入数据库*/
		//插入主表
		$params = array(
			'az_id'				=> $az_id,
			'user_id'			=> $user_id,
			#'tb_seller_nick'	=> $tb_seller_nick,
			#'tb_buyer_nick'		=> $tb_buyer_nick,
			'add_time'			=> $add_time,
			'visit_date'		=> $visit_date,
			'from_domain'		=> $from_domain == 'null' ? '' : $from_domain,
			'from_url'			=> $from_url,
			'ip'				=> $ip,
			'is_new_visitor'	=> $is_new_visitor,
			'client_key'		=> $client_key,
			'province_id'		=> $province_id,
			'city_id'			=> $city_id
		);
		$Pv_main->add($params);
		$last_insert_id = $Pv_main->getLastInsID($params);
		#echo $Pv_main->getLastSql();
		#print_r($params);

		//插入受访表
		$params = array(
			'id'				=> $last_insert_id,
			'user_id'			=> $user_id,
			'add_time'			=> $add_time,
			'domain'			=> $domain,
			'visit_url'			=> $visit_url,
			'page_title'		=> $page_title,
			'is_entrance'		=> $is_entrance,
			'have_jump'			=> $have_jump,
			'last_time'			=> $last_time,
		);
		$Pv_respondent_data->add($params);
		#print_r($params);

		//插入来源表
		#$params = array(
			#'id'				=> $last_insert_id,
			#'add_time'			=> $add_time,
		#);
		#$Pv_source_data->add($params);

		//插入系统分析表
		$params = array(
			'id'				=> $last_insert_id,
			'add_time'			=> $add_time,
			'browser'			=> $browser,
			'screen_id'			=> $screen_id,
			'os_id'				=> $os_id,
			'language_id'		=> $language_id,
			'terminal_id'		=> $terminal_id
		);
		$Pv_system_analysis->add($params);
		#print_r($params);die;

		#echo $this->_request('callback') . "({browser:" . json_encode($browser) . ", domain:" . json_encode($domain) . ", client_key:" . json_encode($new_client_key) . "})";
		echo $this->_request('callback') . "({browser:" . json_encode($browser) . ", domain:" . json_encode($domain) . ", client_key:" . json_encode($new_client_key) . "})";
	}

	function receive_contact_stat()
	{
		$arr = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
		if ($arr['user_id'] && $arr['ip'] && $arr['account_id'])
		{
			$arr['addtime'] = time();
			$area = getIPSource($arr['ip']);
			$arr['area'] = $area['province_id'] . ',' . $area['city_id'];
			$contact_log_obj = M('contact_log');
			$contact_log_obj->add($arr);
		}
	}

	function valid_user($Key)
	{
		return true;
	}

	private function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	
	private function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return (object) array_map(__FUNCTION__, $d);
		}
		else {
			// Return object
			return $d;
		}
	}
}

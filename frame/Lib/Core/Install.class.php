<?php
class Install{
	//安装信息数组
	private $install_info = null;
	private $mcp_config = null;

	function Install()
	{
		#$model = $this->db(1, 'AZ_DB_CONFIG');
		#$user = $model->table('tp_users');
		$user = M("User","tp_",'AZ_DB_CONFIG');
        $user_list = $user->select();
		echo "user list:<pre>";
		print_r($user_list);
		echo "</pre>";
		die;
		$conn = mysql_connect(C('DB_HOST'), C('DB_USER'), C('DB_PWD')); 
		if (!$conn)
			exit('数据库配置不正确');

		$state = mysql_select_db(C('DB_NAME'), $conn);
		if (!$state)
			exit('数据库连接不正确');

		mysql_query('SET NAMES UTF8');
		$result = mysql_list_tables(C('DB_NAME'));
		$i = 0;
		while ($row = mysql_fetch_assoc($result))
		{
			echo "<pre>";
			print_r($row);
			echo "</pre>";
		}

		$domain = strtolower($_SERVER['HTTP_HOST']);
		$domain = explode('.', $domain);
		$idsn 	= $domain[0];
		$sql 	= 'SELECT * FROM tp_shops WHERE idsn = "' . $idsn . '" LIMIT 1';
		$result = mysql_query($sql, $conn);
		$this->install_info = mysql_fetch_assoc($result);
		
		//判断一级域名是否合法
		$full_domain = $idsn . '.' . C('DOMAIN');
		if ($full_domain != strtolower($_SERVER['HTTP_HOST']))
			exit('一级域名不合法');

		//判断2级域名是否合法
		if (!$this->install_info)
			exit('二级域名不合法');
	}

	//获取安装信息
	function get_install_info()
	{
		return $this->install_info;
	}

}

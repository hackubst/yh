<?php

class SwaggerAction extends Action{



	public function index(){
		
		$http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
		$this->assign('api_url', $http_type . $_SERVER['HTTP_HOST']);
		$this->display(APP_PATH.'swagger/index.html');
	}
}
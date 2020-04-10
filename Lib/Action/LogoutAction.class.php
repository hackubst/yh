<?php
// 退出
class LogoutAction extends GlobalAction {
	function index() {
		session('user_info', null);
		session('user_id', null);
		redirect(U('/login/index/jinlong/kkl28'));
	}
}

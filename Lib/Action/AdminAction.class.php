<?php
// 管理员后台
class AdminAction extends GlobalAction {
	function index()
	{
		redirect(U('/acp'));
	}
}

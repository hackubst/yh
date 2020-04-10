<?php

class FrontLuntanUserAction extends FrontAction
{
	function _initialize() 
	{
		parent::_initialize();
	}

	//首页
	public function index()
	{	

		$l_u_obj = new LuntanUserModel();
		echo $l_u_obj->testLuntanUser();
		echo '<br>';
		echo '论坛个人中心';

	}

	public function test(){
		echo 1111;die;
	}
}

<?php
defined('THINK_PATH') or exit(); 
//测试组件
class TestBehavior extends AppBehavior
{
	public function action(&$params)
	{
		dump($params);
	}
}

?>